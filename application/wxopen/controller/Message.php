<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/11/24
 * Time: 11:14
 */

namespace app\wxopen\controller;

use app\api\model\Authorizer;
use app\api\model\CenterWechatFans;
use app\api\model\DatabaseConfig;
use app\api\model\Student;
use app\api\model\WxmpFansMessage;
use app\common\job\TransferMedia;
use think\App;
use think\Config;
use think\Db;
use think\Exception;
use think\Log;
use think\Request;
use app\api\model\User;
use app\api\model\WxmpFans;
use app\api\model\Wxmp;
use app\api\model\WxmpRule;
use app\api\model\WxmpRuleKeyword;
use EasyWeChat\Message\Material;
use EasyWeChat\Message\Text;
use think\Cache;

class Message extends OpenApp
{
    protected $authorization_info;
    protected $system_default;
    protected $wxapp;   # 微信对象实例
    protected $appid;
    protected $cid;
    protected $og_id;
    protected $db_config;
    protected $wxmp;
    protected $error_msg;
    protected $client;

    protected function _init()
    {
        parent::_init();
        $this->appid = input('appid');
        $auth_info = Authorizer::getByAppid($this->appid);
        if ($auth_info) {
            $this->authorization_info = $auth_info->toArray();
        } else {
            Log::record('没有在center数据库pro_wxopen_authorizer表查询到appid对应的授权记录', 'wechat');
            exit('');
        }
        //Log::record('公众号名称：' . $this->authorization_info['alias'] . '   cid:' . $this->authorization_info['cid'], 'wechat');
        $authorizerAppId = $this->authorization_info['authorizer_appid'];
        $authorizerRefreshToken = $this->authorization_info['authorizer_refresh_token'];

        $this->wxapp = $this->openPlatform->createAuthorizerApplication($authorizerAppId, $authorizerRefreshToken);

        /*兼容全网发布的测试公众号*/
        if ($this->appid !== self::WX_DISPLAY_CASE_APPID) {
            $this->cid = $this->authorization_info['cid'];
            if ($this->authorization_info['system_default']) {
                $this->system_default = true;
            } else {
                $this->system_default = false;
                if (empty($this->cid)) {
                    throw new Exception('非系统默认公众号cid为空');
                }
                $client = db('client','center_database')->where('cid',$this->cid)->find();
                if(!$client){
                    throw new Exception('cid不存在!cid:'.$this->cid);
                }

                $this->client = $client;
                gvar('client',$client);
                gvar('og_id',$client['og_id']);

                if($client['parent_cid'] > 0){
                    $w_dc['cid'] = $client['parent_cid'];
                }else{
                    $w_dc['cid'] = $this->cid;
                }
                $this->db_config = DatabaseConfig::withTrashed()->where($w_dc)->find()->toArray();

                Config::set('database', $this->db_config);
                $w = [];
                $w['authorizer_appid'] = $this->appid;
                $this->wxmp = Wxmp::get($w);
                if (empty($this->wxmp)) {
                    throw new Exception('授权公众号在对应的数据库中不存在！cid:' . $this->cid);
                }

                $this->og_id = $this->wxmp['og_id'];

            }
        }
    }

    protected function get_db_config($cid){
        $client = db('client','center_database')->where('cid',$cid)->find();
        $db_cid = $client['cid'];
        if($client['parent_cid'] > 0){
            $db_cid = $client['parent_cid'];
        }
        $db_config = DatabaseConfig::withTrashed()->where('cid', $db_cid)->find();
        return $db_config;
    }

    private function setDatabase($cid, $change_progress_database = true)
    {
        $client = db('client','center_database')->where('cid',$cid)->find();
        if(!$client){
            throw new Exception('cid不存在!cid:'.$cid);
        }

        $w_dc['cid'] = $cid;
        $db_config = DatabaseConfig::withTrashed()->where($w_dc)->find();

        if(empty($db_config) && $client['parent_cid'] > 0) {
            $w_dc['cid'] = $client['parent_cid'];
            $db_config = DatabaseConfig::withTrashed()->where($w_dc)->find();
        }

        if(empty($db_config)) {
            log_write('database config not exist. cid; ' . $w_dc['cid'], 'error');
            $this->error_msg = '系统数据库不存在，cid:' . $w_dc['cid'];
            return false;
        }

        $db_config = $db_config->toArray();

        if($change_progress_database) {
            Config::set('database', $db_config);
        }

        return $db_config;
    }

    /**
     * //公众号消息与事件接收URL:pro.xiao360.com/wxopen/message/index/appid/$APPID$
     * @param Request $request
     */
    public function index(Request $request)
    {
        $this->wxapp->server->setMessageHandler(function ($message) {

            $this->saveMessage($message);
            switch ($message->MsgType) {
                case 'event':
                    if ($this->appid === self::WX_DISPLAY_CASE_APPID) {
                        return $message->Event . 'from_callback';
                    }
                    switch ($message->Event) {
                        case 'subscribe':
                            return $this->subscribe($message);
                            break;
                        case 'unsubscribe':
                            return $this->unsubscribe($message);
                            break;
                        case 'SCAN':
                            //todo 区分EventKey
                            //return $this->accountBinding($message);
                            return $this->dealScan($message);
                            break;
                        default:
                            return '';
                            break;
                    }
                    break;
                case 'text':
                    if ($this->appid === self::WX_DISPLAY_CASE_APPID) {
                        /*用于全网发布测试*/
                        if ($message->Content === 'TESTCOMPONENT_MSG_TYPE_TEXT') {
                            return 'TESTCOMPONENT_MSG_TYPE_TEXT_callback';
                        } elseif (preg_match('/^QUERY_AUTH_CODE/',trim($message->Content))) {
                            $auth_code = preg_replace('/^QUERY_AUTH_CODE:(.+)/', '$1', trim($message->Content));
                            $authorization_info = $this->openPlatform->getAuthorizationInfo($auth_code)['authorization_info'];
                            $authorizerAppId = $authorization_info['authorizer_appid'];
                            $authorizerRefreshToken = $authorization_info['authorizer_refresh_token'];
                            $temp_wxapp = $this->openPlatform->createAuthorizerApplication($authorizerAppId, $authorizerRefreshToken);
                            $text = new Text(['content' => $auth_code . '_from_api']);
                            $result = $temp_wxapp->staff->message($text)->to($message['FromUserName'])->send();
                            return '';
                        }
                    }
                    return $this->KeywordReply($message);
                    break;
                case 'image':
                case 'voice':
                case 'video':
                    return $this->processMedia($message);
                    break;
                case 'location':
                    break;
                case 'link':
                    break;
                default:
                    return '';
                    break;
            }
        });
        $response = $this->wxapp->server->serve();
        $response->send();
    }

    /*用户关注后添加一条粉丝记录*/
    private function addFans($message)
    {
        $w = [];
        $w['appid']  = $this->appid;
        $w['openid'] = $message['FromUserName'];
        $fans_info = $this->getFansInfo($message['FromUserName']);
        $data = array_merge($w, $fans_info);
        $data['unsubscribe_time'] = 0;/*重新关注*/
        $data['last_connect_time'] = time();
        $data['original_id'] = $message['ToUserName'];

        if ($this->system_default) {

            /*学习管家服务号粉丝*/
            $fans = CenterWechatFans::get($w);
            if ($fans) {
                CenterWechatFans::update($data, $w, true);
            } else {
                $fans = CenterWechatFans::create($data, true);
            }

            $keys = json_decode(substr($message['EventKey'], 8), true);

            if(!empty($keys) && isset($keys['cid'])) {
                $data['cid']   = intval($keys['cid']);

                $db_config = $this->get_db_config($data['cid']);
                Config::set('database', $db_config->toArray());
                /*客户公众号粉丝*/
                $fans = WxmpFans::get($w);
                if ($fans) {
                    WxmpFans::update($data, $w, true);
                } else {
                    $fans = WxmpFans::create($data, true);
                }
            }

        } else {
            /*客户公众号粉丝*/
            $fans = WxmpFans::get($w);
            if ($fans) {
                WxmpFans::update($data, $w, true);
            } else {
                $fans = WxmpFans::create($data, true);
            }
        }
        return $fans;
    }

    protected function getFansInfo($openid)
    {
        /*粉丝管理&关键字回复&账号绑定*/
        $userService = $this->wxapp->user;
        $fans_info = $userService->get($openid)->toarray();
        return $fans_info;
        /**
        array (
        'subscribe' => 1,
        'openid' => 'o7y5d0aBJiz7zw-TMwa5N80o_c30',
        'nickname' => 'karma',
        'sex' => 1,
        'language' => 'zh_CN',
        'city' => '',
        'province' => '上海',
        'country' => '中国',
        'headimgurl' => 'http://wx.qlogo.cn/mmopen/5JiaTvA1ibwEszTY92d3UNp8p9uz14jCpOjWtdtHUWnsk6e3AibcR5rdqIOxzmGYcIvCm5vM2m8Jt4fibWcYjQHV0loYdnvbqBeT/132',
        'subscribe_time' => 1516783034,
        'remark' => '',
        'groupid' => 0,
        'tagid_list' =>
            array (
            ),
        )
        */
    }

    protected function subscribe($message)
    {
        $this->addFans($message);
        if (empty($message['EventKey'])) {
            if ($this->system_default) {
                return '校360学习管家欢迎您！';
            }

            $rule_id = $this->wxmp['welcome_message'];
            if (empty($rule_id)) {
                return '';
                /*
                if (App::$debug) {
                    return '系统还没有设置欢迎消息,请联系管理员！';
                } else {
                    return '欢迎关注!';
                }*/
            }
            $rule = WxmpRule::get($rule_id);
            if (!$rule || !$rule['status']) {
                if (App::$debug) {
                    return '系统设置的欢迎消息已失效，请联系管理员！';
                } else {
                    return '欢迎关注!';
                }
            }
            return $this->responseReply($message, $rule);
        } else {
            //扫描带参数二维码，如果没有关注，则扫描并且关注成功后推送的事件为subscribe而不是scan
            return $this->dealScan($message);
            //return $this->accountBinding($message);
        }
    }

    /**
     * 用户取消关注事件
     * @param $message
     */
    public function unsubscribe($message)
    {
        $w['openid'] = $message['FromUserName'];
        if ($this->system_default) {
            /*确定取消关注事件来源于哪个客户*/
            $fans = CenterWechatFans::get($w);
            if ($fans) {
                $center_database_config = \think\Config::get('center_database');
                if (!empty($fans['cid'])) {
                    $client = db('client',$center_database_config)->where('cid',$fans['cid'])->find();
                    $db_cid = $client['cid'];
                    if($client['parent_cid'] > 0){
                        $db_cid = $client['parent_cid'];
                    }
                    $db_config = DatabaseConfig::withTrashed()->where('cid', $db_cid)->find();
                    if ($db_config) {
                        Config::set('database', $db_config->toArray());
                        $local_fans = WxmpFans::get($w);
                        if ($local_fans) {
                            $local_fans->unsubscribe();
                        }
                    }
                }
                $fans->unsubscribe();
            }
        } else {
            $local_fans = WxmpFans::get($w);
            if ($local_fans) {

                $local_fans->unsubscribe();
            }
        }

    }

    protected function responseReply($message, WxmpRule $rule)
    {
        $containtype = $rule['containtype'];
        foreach ($containtype as $type) {
            $method_name = rtrim($type, 's') . 's';
            $records = $rule->$method_name;
//            $temp = $records[array_rand($records)];
            foreach ($records as $record) {
                if ($type == 'text') {
                    $resp_message = new Text(['content' => $record['content']]);
                } elseif ($type == 'image') {
                    $resp_message = new Material('image', $record['media_id']);;
                } elseif ($type == 'news') {
                    $resp_message = new Material('mpnews', $record['media_id']);;
                } elseif ($type == 'voice') {
                    $resp_message = new Material('voice', $record['media_id']);;
                } elseif ($type == 'video') {
                    $resp_message = new Material('mpvideo', $record['media_id']);;
                } else {
                    return '';
                }
                $this->wxapp->staff->message($resp_message)->to($message['FromUserName'])->send();
            }
        }
        return '';
    }

    protected function KeywordReply($message)
    {

        $content = $message->Content;
        //Log::record('KeywordReply:'.$content);
        //关键词前缀回复处理
        $key_prefix_list = config('wxopen.key_reply_prefix');

        foreach($key_prefix_list as $prefix){
            if(strpos($content,$prefix) === 0){

                $func = 'KeyPrefixReply'.$prefix;
                if(method_exists($this,$func)){
                    return $this->$func($message);
                }
            }
        }

        if ($this->system_default) {
            return '';//todo
        }
        $wxmp = $this->wxmp;
        $rule_ids = (new WxmpRule())->where(['wxmp_id' => $wxmp['wxmp_id'], 'status' => 1])
            ->order('displayorder', 'desc')->column('rule_id');
        if (!$rule_ids) {
            return $this->defaultReply($message, $wxmp);
        }
//        $material = new Material('mpnews', 'acU2wVIctQYf4So1dSbQ7G5XqLcJyR8_BxCX9jA2wH0');;
//        $this->wxapp->staff->message($material)->to($message['FromUserName'])->send();
//        $this->wxapp->staff->message($material)->to($message['FromUserName'])->send();
//        return 'aaa';


//        $keyword_list = WxmpRuleKeyword::whereIn('rule_id', $rule_ids)->select();
//        foreach ($keyword_list as $keyword) {
//            if ($keyword['type'] == 1 && $keyword['content'] == $content) {
//                return $this->responseReply($message, $keyword['rule']);
//            } elseif ($keyword['type'] == 2 && strstr($content, $keyword['content'])) {
//                return $this->responseReply($message, $keyword['rule']);
//            } elseif ($keyword['type'] == 3 && preg_match('/' . $keyword['content'] . '/', $content)) {
//                return $this->responseReply($message, $keyword['rule']);
//            }
//        }

        $hit_keywords = WxmpRuleKeyword::whereIn('rule_id', $rule_ids)
            ->where('type', 1)
            ->where('content', $content)
            ->select();
        if (!$hit_keywords) {
            $hit_keywords = WxmpRuleKeyword::whereIn('rule_id', $rule_ids)
                ->where('type', 2)
                ->where('content', 'like', '%' . $content . '%')
                ->select();
        }
        if (!$hit_keywords) {
            $hit_keywords = WxmpRuleKeyword::whereIn('rule_id', $rule_ids)
                ->where('type', 3)
                ->where('content', 'exp', 'regexp ' . "'" . $content . "'")
                ->select();
//            return $hit_keywords;
        }
        if ($hit_keywords) {
            foreach ($hit_keywords as $key => $item) {
                if ($key == 0) {
                    $max_displayorder_rule = $item['rule'];
                } else {
                    if ($item['rule']['displayorder'] > $max_displayorder_rule['displayorder']) {
                        $max_displayorder_rule = $item['rule'];
                    }
                }
            }

            return $this->responseReply($message, $max_displayorder_rule);
        }


        return $this->defaultReply($message, $wxmp);
    }


    protected function defaultReply($message, Wxmp $wxmp)
    {
        $rule_ids = WxmpRule::where(['wxmp_id' => $wxmp['wxmp_id'], 'status' => 1])->column('rule_id');
        if ($rule_ids) {
            $keywords = WxmpRuleKeyword::whereIn('rule_id', $rule_ids)->column('content');
            $keywords = join("\n", $keywords);
        } else {
            //return '该公众号还没有设置关键字回复!';
            return '';
        }

        $rule_id = $wxmp['default_message'];
        if (empty($rule_id)) {
            return "请选择以下关键字回复:\n" . $keywords;
        }
        $rule = WxmpRule::get($rule_id);
        if (!$rule || !$rule['status']) {
            return "请选择以下关键字回复:\n" . $keywords;
        }
        return $this->responseReply($message, $rule);
    }

    protected function processMedia($message)
    {
        $w = [];
        $w['appid']  = $this->appid;
        $w['openid'] = $message['FromUserName'];//todo openid需要与uid一对一关系

        if ($this->system_default) {
            $center_fans = CenterWechatFans::get($w);
            if (empty($center_fans) || empty($center_fans['cid'])) {
                if (App::$debug) {
                    return '由于您的微信号没有与系统绑定，您刚才上传的文件被忽略.';
                } else {
                    return '';
                }
            }

            // $db_config = DatabaseConfig::withTrashed()->where('cid', $center_fans['cid'])->find();
            $db_config = $this->get_db_config($center_fans['cid']);
            if ($db_config) {
                $this->db_config = $db_config->toArray();
            } else {
                $client = db('client','center_database')->where('cid', $center_fans['cid'])->find();
                if($client['parent_cid'] > 0) {
                    $db_config = DatabaseConfig::withTrashed()->where('cid', $client['parent_cid'])->find();
                    if(!empty($db_config)) {
                        $this->db_config = $db_config->toArray();
                    }
                }
            }
            if(empty($db_config)) {
                if (App::$debug) {
                    return '没有查询到数据库信息!';
                } else {
                    return '';
                }
            }
        }

        Config::set('database', $this->db_config);
        $fans = WxmpFans::get($w);
        if (empty($fans) || (empty($fans['uid']) && empty($fans['employee_uid']))) {
            if (App::$debug) {
                return '关注了公众号，但是没有绑定用户!';
            } else {
                return '';
            }
        }

        $queue_data = [
            'openid'        => $message['FromUserName'],
            'MediaId'       => $message['MediaId'],
            'MsgType'       => $message['MsgType'],
            'CreateTime'    => $message['CreateTime'],
            'MsgId'         => $message['MsgId'],
            'appid'         => $this->appid,
            'db_config'     => $this->db_config,
            'uid'           => $fans['employee_uid'] > 0 ? $fans['employee_uid'] : $fans['uid'],
            'og_id'         => $fans['og_id'],
        ];
        try {
            queue_push('TransferMedia', $queue_data);//队列默认是同步执行
            return '系统已经收到您上传的文件，等待处理中，请稍后...';
//            return (new TransferMedia())->doJob($queue_data);
        } catch (\Exception $exception) {
            Log::record($exception);
            return $exception->getMessage() . $exception->getLine();
        }
        return '';
    }

    public function dealScan($message)
    {
        if ($message->Event == 'subscribe') {
            /*用户没关注的情况下扫描二维码，只有点击关注才会触发事件*/
            $keys = json_decode(substr($message['EventKey'], 8), true);
        } else {
            $keys = json_decode($message['EventKey'], true);
        }

        if(empty($keys)) return true;

        $keys['code_type'] = isset($keys['code_type']) ? $keys['code_type'] : 'bind_account';
        switch ($keys['code_type']) {
            case 'bind_account':
                $this->accountBinding($message, $keys);
                break;
            case 'upload_file':
                $this->accountBinding($message, $keys);
                $this->tellUploadFile($message);
                break;
            default:
                break;
        }

        return '';
    }

    /**
     * 1.通过扫描二维码绑定账号.先关注，后通过EventKey绑定openid到uid
     * 2.家长账号只有一个校区，只会有一个openid，所以user['openid']是可用的,体现在wechat_bind表中表现为:uid和openid是一对一；
     * 3.员工可能属于多个校区，而每个校区可以有自己的公众号，所以一个员工账号可以有多个openid,体现在wechat_bind表中表现为：uid和openid是一对多;
     * array (
        'ToUserName' => 'gh_6335676f64cc',  公众号的原始id
        'FromUserName' => 'oD5_L1EuqNdvGwU9-RMRUdk-4338',   用户openid
        'CreateTime' => '1512043277',
        'MsgType' => 'event',
        'Event' => 'SCAN',
        'EventKey' => 'cid|og_id|bid|uid',
        'Ticket' => 'gQFH7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAybTNFandSZnZmYmsxY0pRdnhxMWIAAgQB8x9aAwQsAQAA',
     *)
     * @param $message
     */
    protected function accountBinding($message, $keys = [])
    {

        if(empty($keys)) {
            if ($message->Event == 'subscribe') {
                /*用户没关注的情况下扫描二维码，只有点击关注才会触发事件*/
                $keys = json_decode(substr($message['EventKey'], 8), true);
            } else {
                $keys = json_decode($message['EventKey'], true);
            }
        }

        return $this->accountBindWechat($message, intval($keys['cid']), $keys['og_id'], $keys['bid'], intval($keys['uid']));
    }

    protected function accountBindWechat($message, $cid, $og_id, $bid, $uid)
    {
        $data = [];
        $data['appid'] = $this->appid;
        $data['original_id'] = $message['ToUserName'];
        $data['openid'] = $message['FromUserName'];
        $data['cid']   = $cid;
        $data['og_id'] = $og_id;
        $data['bid']   = $bid;
        if ($this->system_default) {
            $data['is_system'] = 1;
        }
        gvar('og_id',$og_id);

        $database_config = $this->setDatabase($data['cid']);
        if($database_config == false) {
            $this->wxapp->staff->message($this->error_msg)->to($message['FromUserName'])->send();
            return '';
        }
        //$db_config = DatabaseConfig::withTrashed()->where('cid', $data['cid'])->find();
        //Config::set('database', $db_config->toArray());

        $user = User::get($uid);
        if($user['user_type'] == User::EMPLOYEE_ACCOUNT) {
            $data['employee_uid']   = $user['user_type'] == User::EMPLOYEE_ACCOUNT ? $uid : 0;
        } else {
            $data['uid']   = $user['user_type'] == User::STUDENT_ACCOUNT ? $uid : 0;
        }
        if (App::$debug) {
            //$info[] = sprintf("二维码所属host：%s\n", $db_config['host']);
        }
        $info[] = sprintf("二维码所属账号：%s\n", $user['account']);
        $userService = $this->wxapp->user;  # 微信用户信息
        if (!empty($user['openid'])) {
            $wx_user = $userService->get($user['openid']);
            $wx_nick_name = $wx_user['nickname'];
            $info[] = sprintf("已绑定微信：%s\n", $wx_nick_name);
        }

        $w = [];
        $w['openid'] = $data['openid'];
        $bind_status_value = 0;
        if ($this->system_default) {
            /*关注系统默认公众号*/
            $bind = CenterWechatFans::get($w);
            if (!$bind) {
                $temp_data = $data;
                unset($temp_data['cid'], $temp_data['og_id'], $temp_data['bid'], $temp_data['uid'], $temp_data['employee_uid']);
                $bind = CenterWechatFans::create($temp_data, true);
            }

            if ($user['user_type'] == User::EMPLOYEE_ACCOUNT) {
                if (!empty($bind['cid']) && $bind['cid'] !== $data['cid']) {
                    $another_db_config = DatabaseConfig::withTrashed()->where('cid', $bind['cid'])->find();
                    $host = $another_db_config['host'];
                    $user = User::get($bind['employee_uid']);
                    $info[] = sprintf("您已经绑定了账号：%s\n如需关联新的账号请选择如下方式:\n     1.联系老师解除微信与账号的绑定.\n     2.取消关注公众号后再扫描二维码关注!", $user['account']);
                    $bind_status_value = '已经绑定到了其他机构的账号，无法绑定到当前账号!';
                } elseif (!empty($bind['employee_uid']) && $bind['employee_uid'] !== $uid) {
                    $user = User::get($bind['employee_uid']);
                    $info[] = sprintf("您已经绑定了账号：%s\n如需关联新的账号请选择如下方式:\n     1.联系老师解除微信与账号的绑定.\n     2.取消关注公众号后再扫描二维码关注!", $user['account']);
                    $bind_status_value = sprintf('该微信已经绑定到了账号%s！', $user['account']);
                } elseif ($bind['cid'] == $data['cid'] && $bind['employee_uid'] == $uid) {
                    $info[] = "您早已经绑定了该账号！";
                    $bind_status_value = 1;
                } else {
                    //是否被其他用户绑定过
                    $fans = CenterWechatFans::get(['cid' => $data['cid'], 'employee_uid' => $data['employee_uid']]);
                    if(!empty($fans) && !empty($user['openid'])) {
                        $this->error_msg = '帐号已经被其他人绑定，微信昵称为：'.$fans['nickname'];
                        $info = [$this->error_msg];
                        $bind_status_value = 1;
                    } else {
                        /*先关注后扫描二维码绑定微信的情况*/
                        $bind->allowField(true)->save($data);
                        $local_bind = WxmpFans::get($w);
                        if ($local_bind) {
                            $local_bind->allowField(true)->save($data);
                        } else {
                            WxmpFans::create($data, true);
                        }
                        $user->data('openid', $data['openid'])->data('is_weixin_bind', 1)->save();
                        $bind_status_value = 1;
                    }

                }
            } else {

                //绑定家长
                if (!empty($bind['cid']) && $bind['cid'] !== $data['cid']) {
                    $another_db_config = DatabaseConfig::withTrashed()->where('cid', $bind['cid'])->find();
                    $host = $another_db_config['host'];
                    $user = User::get($bind['uid']);
                    $info[] = sprintf("您已经绑定了账号：%s\n如需关联新的账号请选择如下方式:\n     1.联系老师解除微信与账号的绑定.\n     2.取消关注公众号后再扫描二维码关注!", $user['account']);
                    $bind_status_value = '已经绑定到了其他机构的账号，无法绑定到当前账号!';
                } elseif (!empty($bind['uid']) && $bind['uid'] !== $uid) {
                    $user = User::get($bind['uid']);
                    $info[] = sprintf("您已经绑定了账号：%s\n如需关联新的账号请选择如下方式:\n     1.联系老师解除微信与账号的绑定.\n     2.取消关注公众号后再扫描二维码关注!", $user['account']);
                    $bind_status_value = sprintf('该微信已经绑定到了账号%s！', $user['account']);
                } elseif ($bind['cid'] == $data['cid'] && $bind['uid'] == $uid) {
                    $info[] = "您早已经绑定了该账号！";
                    $bind_status_value = 1;
                } else {
                    //是否被其他用户绑定
                    $fans = CenterWechatFans::get(['cid' => $data['cid'],'uid' => $data['uid']]);
                    if(!empty($fans) && !empty($user['openid'])) {
                        $this->error_msg = '帐号已经被其他人绑定，微信昵称为：'.$fans['nickname'];
                        $info = [$this->error_msg];
                        $bind_status_value = 1;
                    } else {
                        /*先关注后扫描二维码绑定微信的情况*/
                        $bind->allowField(true)->save($data);
                        $local_bind = WxmpFans::get($w);
                        if ($local_bind) {
                            $local_bind->allowField(true)->save($data);
                        } else {
                            WxmpFans::create($data, true);
                        }
                        $user->data('openid', $data['openid'])->data('is_weixin_bind', 1)->save();

                    }

                }
            }

        } else {
            /*关注的是客户自己的公众号*/
            $bind = WxmpFans::get($w);
            if (!$bind) {
                $temp_data = [
                    'appid' => $this->appid,
                    'original_id' => $message['ToUserName'],
                    'openid' => $message['FromUserName'],
                ];
                unset($temp_data['cid'], $temp_data['og_id'], $temp_data['bid'], $temp_data['uid']);
                $bind = WxmpFans::create($temp_data, true);
            }
            if($user['user_type'] == User::STUDENT_ACCOUNT) {
                //如果是学生家长
                if (!empty($bind['uid']) && $bind['uid'] != $uid) {
                    $user = $bind['user'];
                    $info[] = sprintf("您已经绑定了账号：%s\n如需绑定新的账号请选择如下方式:\n     1.联系老师解除微信与账号的绑定.\n     2.取关公众号后再扫描二维码关注!", $user['account']);
                    $bind_status_value = '该微信已经绑定了其他的账号！';
                } elseif (!empty($bind['uid']) && $bind['uid'] == $uid) {
                    $info[] = "您早已经绑定了该账号！";
                    $bind_status_value = 1;
                } else {
                    $fans = WxmpFans::get(['uid' => $data['uid']]);
                    if(!empty($fans)) {
                        $this->error_msg = '帐号已经被其他人绑定，微信昵称为：'.$fans['nickname'];
                        $info = [$this->error_msg];
                        $bind_status_value = 1;
                    } else {
                        /*先关注为粉丝，然后扫描二维码绑定账号*/
                        $user->data('openid', $data['openid'])->data('is_weixin_bind', 1)->save();
                        $bind->allowField(true)->save($data);
                        $bind_status_value = 1;
                    }
                }
            } else {
                //如果是机构员工
                if (!empty($bind['employee_uid']) && $bind['employee_uid'] != $uid) {
                    $user = $bind['user'];
                    $info[] = sprintf("您已经绑定了账号：%s\n如需绑定新的账号请选择如下方式:\n     1.联系老师解除微信与账号的绑定.\n     2.取关公众号后再扫描二维码关注!", $user['account']);
                    $bind_status_value = '该微信已经绑定了其他的账号！';
                } elseif (!empty($bind['employee_uid']) && $bind['employee_uid'] == $uid) {
                    $info[] = "您早已经绑定了该账号！";
                    $bind_status_value = 1;
                } else {
                    //如果被uid被其他微信关注过
                    $fans = WxmpFans::get(['employee_uid' => $data['employee_uid']]);
                    if(!empty($fans)) {
                        $this->error_msg = '帐号已经被其他人绑定，微信昵称为：'.$fans['nickname'];
                        $info = [$this->error_msg];
                        $bind_status_value = 1;
                    } else {
                        /*先关注为粉丝，然后扫描二维码绑定账号*/
                        $user->data('openid', $data['openid'])->data('is_weixin_bind', 1)->save();
                        $bind->allowField(true)->save($data);
                        $bind_status_value = 1;
                    }

                }
            }
        }

        /*兼容前端轮询绑定状态*/
        $bind_status_cache_key = 'user_wechat_bind_status:' . $data['cid'] . ':' . $uid;
        Cache::set($bind_status_cache_key, $bind_status_value, 300);

        /*绑定账号后的回复信息*/
        $info[] = "关联账号信息:";
        if (App::$debug) {
            //$info[] = "     host: " . $host;
        }
        $info[] = "     登录账户: " . $user['account'];
        $info[] = "     手机号码: " . $user['mobile'];
        if ($user['user_type'] == User::STUDENT_ACCOUNT) {
            $mStudent = new \app\api\model\Student();
            $mUserStudent = new \app\api\model\UserStudent();
            $w_s['first_uid|second_uid'] = $user['uid'];
            $student_list = $mStudent->where($w_s)->select();
            $info[]   = '关联学生信息：';
            $sex_arr  = ['未知', '男', '女'];
            foreach($student_list as $ms){
                $info[] = "     姓名：" . $ms['student_name'] . '       性别：' . $sex_arr[$ms['sex']];

                $w_us['uid'] = $user['uid'];
                $w_us['sid'] = $ms['sid'];

                $ex_us = $mUserStudent->where($w_us)->find();

                if(!$ex_us){
                    $new_us = [];
                    $new_us['og_id'] = $user['og_id'];
                    $new_us['uid'] = $user['uid'];
                    $new_us['sid'] = $ms['sid'];
                    $mUserStudent->data([])->isUpdate(false)->save($new_us);
                }
            }

        }
        $reply = new Text(['content' => join("\n", $info)]);//todo
        $this->wxapp->staff->message($reply)->to($message['FromUserName'])->send();
        return '';
    }

    protected function tellUploadFile($message)
    {
        if(!empty($this->error_msg)) {
            $reply = new Text(['content' => '上传不了文件，原因：' . $this->error_msg]);
        } else {
            $reply = new Text(['content' => '上传文件，请直接选择发送至公众号即可']);
        }
        $this->wxapp->staff->message($reply)->to($message['FromUserName'])->send();
        return '';
    }

    /**
     * 保存粉丝的消息与事件到本地数据库
     * @param $message
     */
    protected function saveMessage($message)
    {
        $data['appid'] = $this->appid;
        $data['openid'] = $message['FromUserName'];
        $data['msg_type'] = $message['MsgType'];
        if (isset($message['Event'])) {
            $data['event'] = $message['Event'];
        }
        if (isset($message['MsgId'])) {
            $data['msg_id'] = $message['MsgId'];
        }
        if (isset($message['CreateTime'])) {
            $data['create_time'] = $message['CreateTime'];
        }
        $data['data_json'] = $message->toArray();
        if ($this->system_default) {
            //todo
        } else {
            $data['wxmp_id'] = $this->wxmp['wxmp_id'];
            $fans_info = WxmpFans::get(['openid' => $data['openid']]);
            if (!$fans_info) {
                $fans_info = $this->addFans($message);
            } else {
                $fans_info->last_connect_time = time();
                $rs = $fans_info->allowField('last_connect_time')->save();
            }
            $data['fans_id'] = $fans_info['fans_id'];
            if (isset($fans_info['uid'])) {
                $data['request_uid'] = $fans_info['uid'];
            }
            WxmpFansMessage::create($data, true);
        }
    }

    /**
     * 学情服务回复
     * @param $message
     */
    protected function KeyPrefixReplyXQ($message)
    {

        $key = substr($message->Content,2);

        if($this->system_default){
            $last_at_pos = strrpos($key,'@');
            $host = substr($key,$last_at_pos+1);
            $key  = substr($key,0,$last_at_pos);
            $result = set_host_database_conf($host);
            if(!$result){
                Log::record('学情服务关键词查询未匹配到客户host,keywords:'.$message->Content, 'wechat');
                return '未找到学情报告，请确认关键词是否输入完毕';
            }
        }else{
            $host = $this->client['host'];
        }

        $w['short_id'] = $key;
        $ss_info = get_ss_info($w);
        if(!$ss_info){
            return '未找到学情报告，请确认关键词是否输入正确x'.$host.print_r($this->client,true);
        }

        if($ss_info['is_query'] == 0) {
            $update_ss['is_query'] = 1;
            $update_ss['query_time'] = time();
            $update_ss['query_openid'] = $message->FromUserName;

            db('study_situation')->where('ss_id',$ss_info['ss_id'])->update($update_ss);
        }

        $news = new \EasyWeChat\Message\News([
            'title'         => $ss_info['title'],
            'description'   => $ss_info['remark'],
            'url'           => get_student_url('/sq/'.$key,$host),
            'image'         => config('wxopen.xq_cover')
        ]);

        return $news;
    }

    /**
     * 发送消息帐号绑定微信
     * @param $message
     */
    protected function KeyPrefixReplyBD($message)
    {
        $key = substr($message->Content,3);
        if($this->system_default){
            $last_at_pos = strrpos($key,'@');
            $host = substr($key,$last_at_pos+1);
            $key  = substr($key,0,$last_at_pos);
            //$result = set_host_database_conf($host);

            $center_database_config = \think\Config::get('center_database');

            $client = db('client',$center_database_config)->where('host',$host)->find();
            if(!$client){
                Log::record('绑定帐号关键词查询未匹配到客户host,keywords:'.$message->Content, 'wechat');
                return '未找到帐号，请确认关键词是否输入正确1';
            }
            gvar('client',$client);
            gvar('og_id',$client['og_id']);
            $db_cid = $client['cid'];
            if($client['parent_cid'] > 0){
                $db_cid = $client['parent_cid'];
            }
            $host_database_config = db('database_config',$center_database_config)->where('cid',$db_cid)->find();
            if(!$host_database_config){
                Log::record('绑定帐号关键词查询未匹配到客户host,keywords:'.$message->Content, 'wechat');
                return '未找到帐号，请确认关键词是否输入正确2';
            }

            $host_database_config = array_merge($center_database_config, $host_database_config);
            \think\Config::set('database', $host_database_config);

        } else {
            $host = $this->client['host'];
            $client = $this->client;
        }

        $tel = $key;
        $user_info = get_user_info(['mobile' => $tel,'user_type'=>2]);
        if(!$user_info){
            return '未找到帐号信息，请确认关键词是否输入正确3'.$tel.':'.$host;
        }


        return $this->accountBindWechat($message, $client['cid'], $user_info['og_id'], 0, $user_info['uid']);
    }

}