<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/10/31
 * Time: 12:05
 */
namespace app\api\model;

use app\common\Wechat;
use EasyWeChat\Message\Image;
use EasyWeChat\Message\Text;
use EasyWeChat\Message\Video;
use EasyWeChat\Message\Voice;
use EasyWeChat\Message\Material;
use think\Exception;
use think\Log;
use think\Validate;

class WxmpFans extends Base
{
    protected $type = [
        'tagid_list' => 'array',
    ];

    const SUBSCRIBE   = 1;
    const UNSUBSCRIBE = 0;

    protected $skip_og_id_condition = true;

    public function user()
    {
        return $this->hasOne('User', 'uid', 'uid')->field('uid,account,mobile,user_type');
        //return $this->belongsTo('User', 'uid', 'uid')->field('uid,account');
    }

    public function employeeUser()
    {
        return $this->hasOne('User', 'uid', 'employee_uid')->field('uid,account,mobile,user_type');
        //return $this->belongsTo('User', 'employee_uid', 'uid')->field('uid,account');
    }

    /**
     * 获取粉丝信息后同步表：wxmp_fans_tag，该表用于两表联查,获取某个标签下的粉丝
     */
    public function syncFansTag()
    {
        //以下主要是find()的值是数组，所以直接getData(), 不用getAttr(),不然出错。
        $tagid_list = is_array($this->getData('tagid_list')) ? $this->getData('tagid_list')  : $this->getAttr('tagid_list');
        $fans_id = $this->getData('fans_id');
        $appid = $this->getData('appid');
        WxmpFansTag::destroy(['fans_id' => $fans_id], true);
        if (!empty($tagid_list)) {
            foreach ($tagid_list as $tag) {
                $data = [];
                $data['tag_id'] = $tag;
                $data['fans_id'] = $fans_id;
                $data['appid'] = $appid;
                WxmpFansTag::create($data);
            }
        }
    }

    /**
     * 修改粉丝备注
     * @param $remark
     */
    public function remark($remark)
    {
        try {
            $appid = $this->getData('appid');
            $openid = $this->getData('openid');
            $app = Wechat::getInstance($appid)->app;
            $user_service = $app->user;
            $api_result = $user_service->remark($openid, $remark);
            if ($api_result === false) {
                $this->error = '修改粉丝备注失败!';
                return false;
            }
            return true;
        } catch (\Exception $exception) {
            $this->error = $exception->getMessage();
            return false;
        }
    }

    public function unsubscribe()
    {
        $data[] = [];
        $data['og_id'] = 0;
        $data['bid'] = 0;
        $data['uid'] = 0;
        $data['employee_uid'] = 0;
        $data['subscribe'] = self::UNSUBSCRIBE;
        $data['unsubscribe_time'] = request()->time();
        try {
            $this->startTrans();
            Log::record($this->uid, 'wechat');
            if ($this->uid > 0) {
                $user_data = [];
                $user_data['openid'] = '';
                $user_data['is_weixin_bind'] = 0;
                User::update($user_data, ['uid' => $this->uid]);
            }
            if ($this->employee_uid > 0) {
                $user_data = [];
                $user_data['openid'] = '';
                $user_data['is_weixin_bind'] = 0;
                User::update($user_data, ['uid' => $this->employee_uid]);
            }

            //$rs = $this->allowField(true)->save($data);
            $rs = $this->delete(true);
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            Log::record($e->getMessage(), 'wechat');
        }
        return $this;
    }

    /**
     * 给单个粉丝编辑标签
     * @param $tag_ids
     */
    public function editTags(array $new_tag_ids)
    {
        try {
            $openid = $this->getData('openid');
            $appid = $this->getData('appid');
            $api_fans_tag_ids = $this->getFansTagIdList();
            $add_tag_ids = array_diff($new_tag_ids, $api_fans_tag_ids);
            $remove_tag_ids = array_diff($api_fans_tag_ids, $new_tag_ids);
            $app = Wechat::getInstance($appid)->app;
            $tag = $app->user_tag;
            foreach ($add_tag_ids as $add_tag_id) {
                $tag->batchTagUsers([$openid], $add_tag_id);
            }
            foreach ($remove_tag_ids as $remove_tag_id) {
                $tag->batchUntagUsers([$openid], $remove_tag_id);
            }
            /*修改微信服务器的数据后再更新本地数据库的数据*/
            $this->updateFansInfo();
            return true;
        } catch (\Exception $exception) {
            $this->error = $exception->getMessage();
            return false;
        }
    }

    /**
     * 同步选中粉丝信息
     * @param array $fans_ids
     * @return bool
     */
    public function batchUpdateFansInfo(array $fans_ids)
    {
        $fans_list = self::all($fans_ids);
        try {
            foreach ($fans_list as $item) {
                $item->updateFansInfo();
            }
        } catch (\Exception $e) {
            return $this->user_error($e->getMessage(), $e->getCode());
        }
        return true;
    }

    /**
     * 更新粉丝的基本信息
     */
    public function updateFansInfo()
    {
        $fans_info = $this->getFansInfo()->toArray();
        $this->allowField(true)->save($fans_info);
        $this->syncFansTag();
    }

    /**
     * 获取一个粉丝的基本信息
     * @return mixed
     */
    public function getFansInfo($openid = null, $appid = null)
    {
        $appid = !is_null($appid) ? $appid : $appid = $this->getData('appid');
        $openid = !is_null($openid) ? $openid : $openid = $this->getData('openid');
        static $user_service;
        if (empty($user_service)) {
            $app = Wechat::getInstance($appid)->app;
            $user_service = $app->user;
        }
        return $user_service->get($openid);
    }

    /**
     * 给粉丝批量添加标签
     * @param array $fans_ids
     * @param array $tag_ids
     */
    public function batchTagging(array $fans_ids, array $tag_ids)
    {
        try {
            $fans_list = self::all($fans_ids);
            $openid_list = collection($fans_list)->column('openid');
            $appid = $fans_list[0]['appid'];
            $app = Wechat::getInstance($appid)->app;
            $tag = $app->user_tag;
            foreach ($tag_ids as $tag_id) {
                $tag->batchTagUsers($openid_list, $tag_id);
            }
            foreach ($fans_list as $item) {
                $item->updateFansInfo();
            }
            return true;
        } catch (\Exception $exception) {
            $this->error = $exception->getMessage();
            return false;
        }
    }

    /**
     * 给粉丝批量取消标签
     * @param array $openid_list
     * @param array $tag_ids
     */
    public function batchUnTagging(array $fans_ids, array $tag_ids)
    {
        try {
            $fans_list = self::all($fans_ids);
            $openid_list = collection($fans_list)->column('openid');
            $appid = $fans_list[0]['appid'];
            $app = Wechat::getInstance($appid)->app;
            $tag = $app->user_tag;
            foreach ($tag_ids as $tag_id) {
                $tag->batchUntagUsers($openid_list, $tag_id);
            }
            foreach ($fans_list as $item) {
                $item->updateFansInfo();
            }
            return true;
        } catch (\Exception $exception) {
            $this->error = $exception->getMessage();
            return false;
        }
    }

    /**
     * 获取指定 openid 用户身上的标签
     * @param null $openid
     */
    public function getFansTagIdList($openid = null)
    {
        $appid = $this->getData('appid');
        if (empty($openid)) {
            $openid = $this->getData('openid');
        }
        static $tag;
        if (empty($tag)) {
            $app = Wechat::getInstance($appid)->app;
            $tag = $app->user_tag;
        }
        $api_result = $tag->userTags($openid);
        return $api_result['tagid_list'];
    }

    /**
     * 当删除一个标签的时候（不是取消）更新本地数据库中粉丝的标签
     * @param $tag_id
     */
    public function removeDeletedTag($tag_id)
    {
        $tagid_list = $this->getAttr('tagid_list');
        $key = array_search($tag_id, $tagid_list);
        if ($key !== false) {
            unset($tagid_list[$key]);
            $this->save(['tagid_list' => array_values($tagid_list)]);
        }
    }

    /**
     * 给粉丝发送消息
     * @param array $data
     */
    public function sendMessage(array $data)
    {
        $appid  = $this->getData('appid');
        $openid = $this->getData('openid');
        $type = $data['type'];
        $content = $data['content'];
        if(empty($content)) return $this->user_error('消息体不能为空');
        try {
            switch ($type) {
                case 'text' :
                    $message = new Text(['content' => $content['text']]);
                    break;
                case 'image' :
                    $message = new Image(['media_id' => $content['media_id']]);
                    break;
                case 'news' :
                    $message = new Material('mpnews', $content['media_id']);
                    break;
                case 'voice' :
                    $message = new Voice(['media_id' => $content['media_id']]);
                    break;
                case 'video' :
                    $message = new Video([
                        //'title' => $content['title'],
                        'media_id' => $content['media_id'],
                        //'description' => '',
                        //'thumb_media_id' => '',
                    ]);
                    break;
                case 'mpvideo' :
                    $message = new Material('mpnews', $content['media_id']);
                    break;
                case 'music' :
                    $message = new Video([
                        'title' => $content['title'],
                        'media_id' => $content['media_id'],
                        'description' => '',
                        'thumb_media_id' => '',
                    ]);
                    break;
                default :
                    throw new Exception('invalid type');
            }
            $app = Wechat::getInstance($appid)->app;
            $app->staff->message($message)->to($openid)->send();
            $this->saveResponseMessage($data);
            return true;
        } catch (\Exception $exception) {
            $this->error = $exception->getMessage();
            return false;
        }
    }

    /**
     * 给粉丝发送模板消息
     * @param array $data
     */
    public function sendTemplateMessage(array $input)
    {
        $appid  = $this->getData('appid');
        $content = $input['content'];
        $openid = $this->getData('openid');
        $rule = [
            //'scene|scene' => 'require',
            'data|消息内容' => 'require',
        ];
        $validate = new Validate($rule);
        $rs = $validate->check($content);
        if($rs !== true) {
            return $this->user_error($validate->getError());
        }

        $user = $this->getAttr('user');
        if(empty($user)) {
            $user = $this->getAttr('employeeUser');
        }

        $wechat = Wechat::getInstance($appid);
        $message['appid'] = $appid;

        if ($wechat->default) {
            return $this->user_error('请先设置自己的公众号，才能发送模板消息');
        } else {
            $w = [];
            $w['appid'] = $message['appid'];
            $w['scene'] = 'to_do';
            $target_tpl = WxmpTemplate::get($w);
            if (empty($target_tpl)) {
                return $this->user_error('该公众号还没有成功设置该模板');
            }
            $scene = $target_tpl['scene'];
            $message['template_id'] = $target_tpl['template_id'];
        }


        $user_template_setting = isset(Config::userConfig()['wechat_template'][$scene]) ? Config::userConfig()['wechat_template'][$scene] : null;
        if (empty($user_template_setting)) {
            //客户如果没有设置公众号的模板消息的first字段、remark字段和颜色的设置，则使用系统默认的公众号的设置
            $user_template_setting = config('tplmsg')[$scene];;
        }
        if(empty($user_template_setting)) return $this->user_error('未设置对应的模板');


        $message['data'] = $content['data'];
        $message['url'] = isset($content['url']) ? $content['url'] : '';
        $search  = array_values($user_template_setting['tpl_fields']);
        $replace = array_values($content['data']);

        //记录消息
        $inner_message = [];
        $inner_message['og_id'] = $this->getData('og_id');
        $inner_message['bid'] = $this->getData('bid');
        $inner_message['business_type'] = $scene;
        $inner_message['business_id'] = 0;
        $inner_message['title']   = $user_template_setting['message']['title'];
        $inner_message['content'] = str_replace($search, $replace, $user_template_setting['sms']['tpl']);
        $inner_message['uid'] = isset($user['uid']) ? $user['uid'] : 0;
        Message::create($inner_message);

        try {
            if ($openid && $user_template_setting['weixin_switch']) {
                $w = [];
                $w['openid'] = $openid;
                $w['subscribe'] = WxmpFans::SUBSCRIBE;
                if (WxmpFans::get($w)) {
                    $message['openid'] = $openid;
                    queue_push('SendWxTplMsg', $message);
                }
            }
        } catch (\Exception $e) {
            return $this->user_error($e->getMessage());
        }

        return true;
    }

    private function saveResponseMessage(array $data)
    {
        $insert_data['appid'] = $this->getData('appid');
        $insert_data['openid'] = $this->getData('openid');
        $insert_data['fans_id'] = $this->getData('fans_id');
        $insert_data['request_uid'] = $this->getData('uid');
        $insert_data['msg_type'] = $data['type'];
        $insert_data['msg_id'] = isset($data['msg_id']) ? $data['msg_id'] : 0;
        $insert_data['data_json'] = $data;
        $insert_data['response_uid'] = request()->user['uid'];
        $wxmp_id = Wxmp::get(['authorizer_appid' => $insert_data['appid']])['wxmp_id'];
        $insert_data['wxmp_id'] = $wxmp_id ? $wxmp_id : 0;
        WxmpFansMessage::create($insert_data, true);
    }

    //从微信中拉取粉丝
    public function download_fans()
    {
        $app = Wechat::getApp();
        $app_id = Wechat::getAppid();

        $next_openid = null;
        try {
            while (true) {
                $data = $app->user->lists($next_openid);
                $list = $data->data;
                if (isset($list['openid']) && !empty($list['openid'])) {
                    foreach ($list['openid'] as $per_openid) {
                        $this->saveFans($per_openid, $app_id);
                    }
                    $next_openid = $data->next_openid;
                } else {
                    break;
                }
            }
        } catch (\Exception $e) {
            return $this->user_error($e->getMessage());
        }

        return true;
    }

    /**
     * 更新粉丝的基本信息
     */
    public function saveFans($open_id, $appid)
    {
        $fans_info = $this->getFansInfo($open_id, $appid)->toArray();
        if(empty($fans_info)) return true;
        $fans_info['appid'] = $appid;

        $fans = $this->where('openid', $open_id)->find();
        if(!empty($fans)) {
            $fans->allowField(true)->isUpdate(true)->save($fans_info);
            $fans->syncFansTag();
        } else {
            $this->data($fans_info)->allowField(true)->isUpdate(false)->save();
            $this->syncFansTag();
        }
    }

    /**
     * 判断一个openid是否关注公众号
     * @param $appid
     * @param $openid
     * @return bool
     */
    public function isOpenidSubscribe($appid,$openid){
        $w['appid'] = $appid;
        $w['openid'] = $openid;
        $fans_info = get_wxmp_fans_info($w);
        if(!$fans_info){
            return false;
        }
        return $fans_info['subscribe'];
    }

//    public static function updateWechatBindInfo($openid)
//    {
//        /*通过微信客户端oauth授权绑定*/
//        $appid = Wechat::getInstance()->wxmp['authorizer_appid'];
//        $model = self::get(['openid' => $openid]);
//        if (empty($model)) {
//            $data = [];
//            $data['openid'] = $openid;
//            $data['appid']  = $appid;
//            $model = self::create($data);
//        }
//        return $model;
//    }

    /**
     * 用户扫描微信账号绑定二维码后，微信推送的scan Event事件需要
     * @param array $data
     * @return WxmpFans|null|static
     */
//    public static function accountBinding(array $data)
//    {
//        $w = [];
//        $w['uid']    = $data['uid'];
//        $w['appid']  = $data['appid'];
//        $w['openid'] = $data['openid'];
//        $model = self::get($w);
//        if (empty($model)) {
//            $model = new self();
//            $model->allowField(true)->save($data);
//        }
//        return $model;
//    }
}