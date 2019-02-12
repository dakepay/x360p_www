<?php
namespace app\api\model;


use app\common\exception\FailResult;
use think\Exception;
use util\Webcall;
use Qiniu;

class WebcallCallLog extends Base
{

    protected $hidden = ['dialback_data', 'callback_data', 'create_time', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function employee()
    {
        return $this->hasOne('Employee','eid','eid');
    }

    public function student()
    {
        return $this->hasOne('Student','sid','sid');
    }

    public function customer()
    {
        return $this->hasOne('Customer','cu_id','cu_id');
    }

    public function marketClue()
    {
        return $this->hasOne('MarketClue','mcl_id','mcl_id');
    }

    public function getCallerCalltimeAttr($value)
    {
        if ($value > 0){
            return date('Y-m-d H:i:s',$value);
        }
        return $value;
    }

    public function getCallerTalkendtimeAttr($value)
    {
        if ($value > 0){
            return date('Y-m-d H:i:s',$value);
        }
        return $value;
    }

    public function getCalleeTalkbegtimeAttr($value)
    {
        if ($value > 0){
            return date('Y-m-d H:i:s',$value);
        }
        return $value;
    }

    public function getCalleeTalkendtimeAttr($value)
    {
        if ($value > 0){
            return date('Y-m-d H:i:s',$value);
        }
        return $value;
    }

    /**
     * 启动呼叫
     * @param $input
     * @param $cid
     * @param $uid
     */
    public function lanchCall($input,$callback_url,$cid = 0 ,$uid = 0){
        if($cid == 0){
            $cid = gvar('client.cid');
        }
        if($uid == 0){
            $uid = gvar('uid');
        }
        if(empty($input['phone']) || !is_numeric($input['phone'])){
            return $this->user_error('被叫电话号码错误');
        }

        $mEmployee = new Employee();
        $mobile = $mEmployee->where('uid', $uid)->value('mobile');
        if(empty($mobile)){
            return $this->user_error('您还没有绑定手机号，请先绑定手机号后再使用!');
        }

        $app = VipClientApp::GetClientAppInfo($cid,'webcall');
        if($app['volume_used'] > $app['volume_limit']) {
            return $this->user_error( '通话时长不足，请及时充值');
        }

        $webcall_config = config('webcall');
        $appkey     = $webcall_config['appkey'];
        $appsecret  = $webcall_config['appsecret'];

        $wc = new Webcall($appkey, $appsecret);

        $caller = '+86'.$mobile;
        $callee = '+86'.$input['phone'];
        $callback_url = $wc->urlsafe_b64encode($callback_url);
        $max_call_length = $app['volume_used'] > 3600 ? 3600 : $app['volume_used'];

        $result = $wc->lanchCall($callee,$caller,$callback_url,$max_call_length);

        if($result['system']['errcode'] != 0) {
            return $this->user_error($result['system']['errmessage']);
        }

        $result['business'] = array_merge($input, $result['business']);

        $rs = $this->addLog($result);

        if(!$rs){
            return false;
        }
        $ret['token'] = $result['business']['token'];

        return $ret;
    }

    /**
     * 添加日志
     * @param $response_data
     * @return bool
     */
    public function addLog($response_data)
    {
        $business_data = $response_data['business'];
        $call_log_data = [
            'cid' => gvar('client.cid'),
            'og_id' => gvar('og_id'),
            'eid' => User::getEidByUid(gvar('uid')),
            'bid' => auto_bid(),
            'caller_phone' => $business_data['caller'],
            'token' => $business_data['token'],
            'callee_phone' => $business_data['callee'],
            'callee_type' => empty($business_data['callee_type']) ? 0 : $business_data['callee_type'],
            'mcl_id' => empty($business_data['mcl_id']) ? 0 : $business_data['mcl_id'],
            'cu_id' => empty($business_data['cu_id']) ? 0 : $business_data['cu_id'],
            'sid' => empty($business_data['sid']) ? 0 : $business_data['sid'],
        ];
        $this->startTrans();
        try {
            $result = $this->allowField(true)->save($call_log_data);
            if(false === $result){
                $this->rollback();
                return $this->sql_add_error('webcall_call_log');
            }

            $result = db('webcall_call_log', 'db_center')->insert($call_log_data);
            if(false === $result){
                $this->rollback();
                return $this->user_error('写入呼叫日志失败!');
            }
        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

    /**
     * 更新
     * @param $post
     * @return bool
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function updateCallLog($post)
    {
        if(empty($post)) return $this->user_error('回调数据为空');
        $business_data = $post['business'];

        $center_call_log_conn = db('webcall_call_log', 'db_center');
        $center_old_log = $center_call_log_conn->where('token', $business_data['token'])->find();
        /*
        $update_center_wcl['callback_arrive_times'] = $center_old_log['callback_arrive_times']+1;
        $center_w['wcl_id'] = $center_old_log['wcl_id'];
        $rs = $center_call_log_conn->where($center_w)->update($update_center_wcl);
        if ($rs === false) exception('callback_arrive_times error');
        */
        $mWcl = new WebcallCallLog();
        $mWcl_data = $mWcl->where('token', $business_data['token'])->find();
        /*
        $update_x360p_wcl['callback_arrive_times'] = $mWcl_data['callback_arrive_times']+1;
        $update_x360p_w['wcl_id'] = $mWcl_data['wcl_id'];
        $rs = $mWcl->save($update_x360p_wcl,$update_x360p_w);
        if ($rs === false) exception('callback_arrive_times error');
        */

        $call_log_data = [
            'cid' => gvar('client.cid'),
            'og_id' => gvar('client.og_id'),
            'token' => $business_data['token'],
            'billsec' => $business_data['billsec'],
            'abillsec' => $business_data['abillsec'],
            'recordurl' => !empty($business_data['recordurl']) ? $business_data['recordurl'] : '',
            'callid' => $business_data['callid'],
            'reasoncode' => isset($business_data['reasoncode']) && !empty_except_zero($business_data['reasoncode']) ? $business_data['reasoncode'] : -1,
            'caller_phone' => !empty($business_data['caller']['phone']) ? $business_data['caller']['phone'] : 0,
            'caller_callcode' => !empty($business_data['caller']['callcode']) ? $business_data['caller']['callcode'] : 0,
            'caller_talkendtime' => !empty($business_data['caller']['talkendtime']) ? $business_data['caller']['talkendtime'] : 0,
            'caller_ringtime' => !empty($business_data['caller']['ringtime']) ? $business_data['caller']['ringtime'] : 0,
            'caller_talkbegtime' => !empty($business_data['caller']['talkbegtime']) ? $business_data['caller']['talkbegtime'] : 0,
            'caller_calltime' => !empty($business_data['caller']['calltime']) ? $business_data['caller']['calltime'] : 0,
            'callee_phone' => !empty($business_data['callee']['phone']) ? $business_data['callee']['phone'] : 0,
            'callee_callcode' => !empty($business_data['callee']['callcode']) ? $business_data['callee']['callcode'] : 0,
            'callee_talkendtime' => !empty($business_data['callee']['talkendtime']) ? $business_data['callee']['talkendtime'] : 0,
            'callee_ringtime' => !empty($business_data['callee']['ringtime']) ? $business_data['callee']['ringtime'] : 0,
            'callee_talkbegtime' => !empty($business_data['callee']['talkbegtime']) ? $business_data['callee']['talkbegtime'] : 0,
            'callee_calltime' => !empty($business_data['callee']['calltime']) ?$business_data['callee']['calltime'] : 0,
            'create_uid' => empty($center_old_log) ? 0 : Employee::getUidByEid($center_old_log['eid']),
        ];

        $this->startTrans();
        try {

            if(!empty($center_old_log)){
                if($center_old_log['callback_arrive_times'] <= 0){
                    $call_log_data['cacu_minutes'] = ceil($business_data['abillsec']/60);
                    if($business_data['billsec'] == 0){
                        $call_log_data['cacu_minutes'] = 1;         //未接通只计1分钟
                    }
                    if (!empty($call_log_data['recordurl'])) {
                        $job_data = [
                            'class' => 'WebcallAudio',
                            'token' => $business_data['token'],
                        ];
                        queue_push('WebcallAudio', $job_data, null, 50);
                    }
                    if($call_log_data['cacu_minutes'] > 0){
                        VipClientApp::UpdateWebCallSeconds($center_old_log['cid'], $call_log_data['cacu_minutes'], $business_data['token']);
                    }
                }elseif($center_old_log['callback_arrive_times'] > 2 && $center_old_log['billsec'] == 0 && $center_old_log['cacu_minutes'] == 0){
                    $call_log_data['cacu_minutes'] = 1;          //未接通计费1分钟
                    VipClientApp::UpdateWebCallSeconds($center_old_log['cid'], $call_log_data['cacu_minutes'], $business_data['token']);
                }

                $call_log_data['callback_arrive_times'] = $center_old_log['callback_arrive_times']+1;
                $rs = db('webcall_call_log', 'center_database')->where('token', $business_data['token'])->update($call_log_data);
                if($rs === false) throw new FailResult('更新通话数据失败');
            }else{
                $rs = $center_call_log_conn->insert($call_log_data);
                if($rs === false) throw new FailResult('添加回调数据失败');
            }

            if(!empty($mWcl_data)){
                $update_w['wcl_id'] = $mWcl_data['wcl_id'];
                $call_log_data['callback_arrive_times'] = $mWcl_data['callback_arrive_times']+1;

                $rs = $mWcl->allowField(true)->save($call_log_data,$update_w);
                if($rs === false) throw new FailResult($this->getErrorMsg());
            }else{
                $rs = $this->allowField(true)->save($call_log_data);
                if($rs === false) throw new FailResult($this->getErrorMsg());
            }
        } catch(\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

    public function uploadToQiniu($audio_path)
    {
        $host = gvar('client.domain');

        $storage_config = storage_config('qiniu');
        $config = $storage_config['qiniu'];
        // 构建鉴权对象
        $auth = new Qiniu\Auth($config['access_key'], $config['secret_key']);

        // 要上传的空间
        $bucket = $config['bucket'];

        // 生成上传 Token
        $token = $auth->uploadToken($bucket);

        // 上传到七牛后保存的文件名
        $key = $config['prefix'] . "/{$host}/telephone_record/" . pathinfo($audio_path)['basename'];

        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new Qiniu\Storage\UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传。
        list($ret, $err) = $uploadMgr->putFile($token, $key, $audio_path);
        if ($err) {
            print_r($err);
            return false;
        }
        $file_url = $config['domain'] . $ret['key'];
        return $file_url;
    }

    public function saveFile($file_data, $remove_origin_file = false)
    {
        if(empty($file_data['local_file']) || !is_string($file_data['local_file'])) return $this->user_error('本地文件路径错误');
        $local_file_path = $file_data['local_file'];

        $file_data['mod']   		= 'webcall';
        $file_data['file_type'] 	= 'mp3';
        $file_data['media_type'] 	= 'voice';
        $file_data['file_name']			= date('Y-m-d-H:i', time()) . '电话录音' . '.mp3';
        $file_data['file_size']			= filesize($local_file_path);
        $file_data['local_file']        = $local_file_path;

        $file_url = $this->uploadToQiniu($local_file_path);
        if($file_url != false) {
            $file_data['file_url'] = $file_url;
            $file_data['storage'] = 'qiniu';
            if($remove_origin_file) {
                @unlink($local_file_path);
            }
        }

        //写入数据库记录
        $m_file = new File();
        $file_data = $m_file->getFileDuration($file_data);
        return $m_file->addFile($file_data);
    }

    /**
     *  更改relate_cmt_id
     * @param $token
     * @param $relate_cmt_id
     */
    public function addRelateCmtId($token,$relate_cmt_id){

        if (empty($token) || empty($relate_cmt_id)){
            return $this->user_error('token exists');
        }
        $w['token'] =  $token;
        $mWcl = $this->where($w)->find();

        if (empty($mWcl)){
            return $this->user_error('webcall');
        }

        $update['relate_cmt_id'] = $relate_cmt_id;
        $update_w['wcl_id'] = $mWcl['wcl_id'];
        $rs = $this->save($update,$update_w);
        if ($rs === false){
            return $this->sql_save_error();
        }
        return true;
    }

}