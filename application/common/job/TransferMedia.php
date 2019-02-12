<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/10/5
 * Time: 14:47
 */
namespace app\common\job;

use app\api\model\File;
use app\api\model\WxmpFansMessage;
use app\common\Wechat;
use Curl\Curl;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Material\Temporary;
use think\Config;
use think\Db;
use think\Log;
use think\queue\Job;
use EasyWeChat\Message\Text;
use Qiniu;

/**
 * 处理用户在微信公众号上传的视频
 */
class TransferMedia
{
    /** @var Application */
    protected $wxapp;
    protected $storage_config = [];
    protected $error;


    /**
     * fire方法是消息队列默认调用的方法
     * @param Job            $job      当前的任务对象
     * @param array|mixed    $data     发布任务时自定义的数据
     */
    public function fire(Job $job, $data)
    {
        if ($job->attempts() > 3) {
            //通过这个方法可以检查这个任务已经重试了几次了
            $job->delete();
            Log::record("<warn>TransferMedia Job has been retried more than 3 times!"."</warn>\n");

            // 也可以重新发布这个任务
            //Log::record("<info>Hello Job will be availabe again after 2s."."</info>\n");
            //$job->release(2); //$delay为延迟时间，表示该任务延迟2秒后再执行
        } else {
            $isJobDone = $this->doJob($data);

            if ($isJobDone) {
                //如果任务执行成功， 记得删除任务
                $job->delete();
                Log::record("<info>TransferMedia Job has been done and deleted"."</info>\n");
            }
        }

    }

    /**
     * 根据消息中的数据进行实际的业务处理
     * @param array|mixed    $data     发布任务时自定义的数据
     * @return boolean                 任务执行的结果
     */
    public function doJob($data)
    {

        if(!isset($data['db_config'])){
            return false;
        }
        Config::set('database', $data['db_config']);
        try {
            $this->storage_config = storage_config('qiniu');
            $media_id = isset($data['MediaId']) && is_string($data['MediaId'])
                ? $data['MediaId'] : (isset($data['media_id']) ? $data['media_id'] : null);


            $file = File::get(['media_id' => $media_id]);
            if (!empty($file)) {
                return false;
            }
            $this->wxapp = Wechat::getApp($data['appid']);

            $temporary = $this->wxapp->material_temporary;
            $download_dir = RUNTIME_PATH . "media_files/";
            if (!is_dir($download_dir)) {
                mkdir($download_dir);
            }

            $message = new Text(['content' => '文件正在处理中，请稍后...']);
            $this->wxapp->staff->message($message)->to($data['openid'])->send();
            //下载文件到本地服务器
            $file_name = $temporary->download($data['MediaId'], $download_dir); //默认文件名为MediaId
            $media_path = $download_dir . $file_name;
            $file_size = filesize($media_path);

            //如果是voice语音文件，需要把格式转换为mp3
            if ($data['MsgType'] === 'voice') {
                $media_path = $this->voice_format_conversion($media_path);
            }

            //上传文件到七牛云
            $host = config('database.host');
            $file_url = $this->upload($media_path, $host);
            if (!$file_url) {
                Log::record('upload to qiniu failed:' . $file_url);
                return false;
            }

            $info = [
                'file_url' => $file_url,
                'file_type' => $data['MsgType'],
                'file_size' => $file_size
            ];

            //如果是voice语音文件和video视频文件，需要从七牛云获取时长
            if ($data['MsgType'] === 'voice' || $data['MsgType'] === 'video') {
                $curl = new Curl();
                $curl->get($file_url . '?avinfo');
                if ($curl->error) {
                    Log::record($curl->error_message);
                } else {
                    $voice_info = json_decode($curl->response, true);
                    $info['duration'] = $voice_info['streams'][0]['duration'];
                }
            }

            //删除服务器上的文件
            $result = unlink($media_path);
            if (!$result) {
                Log::record('unlink failed' . "\n");
            }

            $this->updateFansMessage($data, $info);

            //保存用户上传文件记录到数据库qms_file
            gvar('uid', $data['uid']);//这里是为了兼容Base model 的方法before_insert中的create_uid
            $this->add_wechat_file_record($data, $info);
            $message = new Text(['content' => '您刚才上传的文件已经处理完毕并保存到系统我的文件框里。']);
            $this->wxapp->staff->message($message)->to($data['openid'])->send();
        }catch(Exception $e){
            return false;
        }
        return true;

    }

    /**
     * 七牛云存储上传后处理
     * @param  [type] &$file [description]
     * @return [type]        [description]
     */
    protected function upload($media_path, $host){
        if(empty($this->storage_config)) {
            $center_db_cfg = Config::get('center_database');
            $db            = Db::connect($center_db_cfg);
            $client_db_config = $db->name('database_config')->where('host', $host)->find();
            Config::set('database', $client_db_config);
            $this->storage_config = storage_config('qiniu');
        }
        $config = $this->storage_config['qiniu'];
        // 构建鉴权对象
        $auth = new Qiniu\Auth($config['access_key'], $config['secret_key']);

        // 要上传的空间
        $bucket = $config['bucket'];

        // 生成上传 Token
        $token = $auth->uploadToken($bucket);

        // 上传到七牛后保存的文件名
        $key = $config['prefix'] . "/{$host}/wechat_media/" . pathinfo($media_path)['basename'];

        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new Qiniu\Storage\UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传。
        list($ret, $err) = $uploadMgr->putFile($token, $key, $media_path);
        if ($err) {
            print_r($err);
            return false;
        }
        $file_url = $config['domain'] . $ret['key'];
        return $file_url;
    }

    /*添加微信公众号上传文件到qms_wechat_file数据表*/
    protected function add_wechat_file_record($data, $info)
    {
        $insert_data = array();
        $insert_data['openid']     = isset($data['openid']) ? $data['openid'] : '';
        $insert_data['appid']     = isset($data['appid']) ? $data['appid'] : '';
        $insert_data['media_type'] = $data['MsgType'];
        $insert_data['uid']        = $data['uid'];
        $insert_data['og_id']      = isset($data['og_id']) ? $data['og_id'] : gvar('og_id');
        $insert_data['media_id']   = isset($data['MediaId']) && is_string($data['MediaId'])
            ? $data['MediaId'] : (isset($data['media_id']) ? $data['media_id'] : null);
        $insert_data['create_uid'] = isset($data['uid']) ? $data['uid'] : 0;

        $insert_data['file_url']  = $info['file_url'];
        $path_info                = pathinfo($insert_data['file_url']);
        $insert_data['file_type'] = $path_info['extension'];
        $insert_data['file_size'] = $info['file_size'];
        $insert_data['create_time'] = time();
        $insert_data['storage']   = 'qiniu';
        if (!empty($info['duration'])) {
            $insert_data['duration'] = $info['duration'];
        }
        $model = File::create($insert_data, true);
        return $model->getAttr('file_id');
    }

    /**
     * 把非MP3格式的语音文件转换为mp3格式
     * @param $media_path
     * @return string
     */
    protected function voice_format_conversion($media_path)
    {
        $path_info = pathinfo($media_path);
        if ($path_info['extension'] == 'mp3') {
            return $media_path;
        }
        $source_file = $media_path;
        $target_file = $path_info['dirname'] . DIRECTORY_SEPARATOR . $path_info['filename'] . '.mp3';
//        $command = "/usr/local/bin/ffmpeg -i {$source_file} {$target_file}";
//        exec($command,$error);
//        unlink($source_file);
        return $this->amr_to_mp3($source_file, $target_file);
    }

    protected function amr_to_mp3($amr_file,$mp3_file = null){
        if(is_null($mp3_file)){
            $mp3_file = str_replace('.amr','.mp3',$amr_file);
        }
        /*
        $ffmpeg = \FFMpeg\FFMpeg::create();
        $amr    = $ffmpeg->open($amr_file);
        $amr->save(new \FFMpeg\Format\Audio\Mp3(),$mp3_file);
        return $mp3_file;
        */
        $cmd = '/usr/local/bin/ffmpeg -i '.$amr_file.' '.$mp3_file;
        try {
            exec($cmd);
        } catch (\Exception $e) {
            Log::record($e->getMessage(), 'error');
            $this->error = 'arm转为MP3格式失败,' . $e->getMessage();
            return false;
    }
        @unlink($amr_file);
        return $mp3_file;
    }

    /*在微信h5页面调用jssdk上传图片到微信服务器后通过media_id保存用户的图片到七牛*/
    public function sync_transfer($data)
    {

        $appid = isset($data['appid']) ? $data['appid'] : Wechat::getAppid();
        $this->wxapp = Wechat::getApp($appid);
        $data['appid'] = $appid;

        /** @var Temporary $temporary */
        $temporary = $this->wxapp->material_temporary;
        $download_dir = RUNTIME_PATH . "media_files/";
        if (!is_dir($download_dir)) {
            mkdir($download_dir);
        }

        if (is_string($data['MediaId'])) {
            $data['MediaId'] = [$data['MediaId']];
        }
        $list = [];

        foreach ($data['MediaId'] as $media_id) {
            $download_name = strlen($media_id) > 120 ? md5($media_id) : $media_id;
            /*下载文件到本地服务器*/
            $file_name  = $temporary->download($media_id, $download_dir, $download_name); //默认文件名为MediaId
            $media_path = $download_dir . $file_name;
            $file_size  = filesize($media_path);

            /*如果是voice语音文件，需要把格式转换为mp3*/
            if ($data['MsgType'] === 'voice') {
                $media_path = $this->voice_format_conversion($media_path);
                if($media_path == false) return false;
            }

            /*上传文件到七牛云*/
            $host = config('database.host');
            $file_url = $this->upload($media_path, $host);
            if (!$file_url) {
                $this->error = 'upload to qiniu failed';
                Log::record('upload to qiniu failed');
                return false;
            }

            $info = [
                'file_url'  => $file_url,
                'file_type' => $data['MsgType'],
                'file_size' => $file_size
            ];

            /*如果是voice语音文件和video视频文件，需要从七牛云获取时长*/
            if ($data['MsgType'] === 'voice' || $data['MsgType'] === 'video') {
                $curl = new Curl();
                $curl->get($file_url . '?avinfo');
                if ($curl->error) {
                    Log::record($curl->error_message);
                } else {
                    $voice_info = json_decode($curl->response, true);
                    $info['duration'] = $voice_info['streams'][0]['duration'];
                }
            }

            /*删除服务器上的文件*/
            $result = unlink($media_path);
            if (!$result) {
                Log::record('unlink failed' . "\n");
            }

            /*保存用户上传文件记录到数据库*/
            gvar('uid', $data['uid']);//这里是为了兼容Base model 的方法before_insert中的create_uid
            $data['media_id'] = $media_id;
            $file_id = $this->add_wechat_file_record($data, $info);
            $info['file_id'] = $file_id;
            $list[] = $info;
        }

        return $list;
    }

    public function getError()
    {
        return $this->error;
    }

    private function updateFansMessage($data, $info)
    {
        $fans_message = WxmpFansMessage::get(['msg_id' => $data['MsgId']]);
        if ($fans_message) {
            $fans_message->save(['files_info' => $info]);
        }
    }
}