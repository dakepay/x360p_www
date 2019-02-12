<?php
namespace app\ftapi\model;

use app\common\Wechat;
use think\Db;
use think\Log;

class Wxmp extends Base
{
    protected $skip_og_id_condition = true;

    public $type = [
        'enable'        => 'boolean',
        'business_info' => 'json',
        'func_info'     => 'json',
        'is_default'    => 'boolean',
        'tags'          => 'array',
    ];

    public function setServiceTypeInfoAttr($value)
    {
        if (is_array($value)) {
            return $value['id'];
        }
    }

    public function setVerifyTypeInfoAttr($value)
    {
        if (is_array($value)) {
            return $value['id'];
        }
    }

    protected $append = ['payment'];

    public function getPaymentAttr($value, $data)
    {
        $payment = [];
        $payment['merchant_id'] = isset($data['merchant_id']) ? $data['merchant_id'] : '';
        $payment['key']         = isset($data['key']) ? $data['key'] : '';
        $payment['cert_path']   = isset($data['cert_path']) ? $data['cert_path'] : '';//todo 证书路径
        $payment['key_path']    = isset($data['key_path']) ? $data['key_path'] : '';//todo 证书路径
    }

    public function setCertPathAttr($value)
    {
        $prefix = request()->server('DOCUMENT_ROOT') . '/public/data/cert/';
        return $prefix . $value;
    }

    public function setKeyPathAttr($value)
    {
        $prefix = request()->server('DOCUMENT_ROOT') . '/public/data/cert/';
        return $prefix . $value;
    }

    public function getTemplateMessageConfigAttr($value)
    {
        $default_config = config('tplmsg');
        $wxmp_config = json_decode($value, true);
        return array_merge($default_config, $wxmp_config);
    }

    public function menus()
    {
        return $this->hasMany('WxmpMenu', 'wxmp_id', 'wxmp_id');
    }

    public function addWxmp($input)
    {
        $result = $this->allowField(true)->save($input);
        return $result;
    }

    public function editWxmp($input)
    {
        $result = $this->allowField(true)->save($input);
        if ($result == false) {
            return false;
        }
        return true;
    }

    public function addMenuGroup($input)
    {
        $result = $this->menus()->save($input);
        return $result;
    }

    public function updateTemplates($input)
    {
        $rs = $this->allowField(['template_message_config'])->save($input);
        return $rs;
    }

    // 处理客户数据库的授权微信
    public static function authorized(array $data)
    {
        $wxmp_count = self::count();
        $w = [];
        $w['authorizer_appid'] = $data['authorizer_appid'];
        $model = self::get($w);
        if (empty($model)) {
            $model = new self();
        }
        if (empty($data['bids'])) {
            // 如果没有选择校区，则是设置默认服务号
            $data['is_default'] = 1;
            self::update(['is_default' => 0], ['wxmp_id' => ['>', 0], 'og_id' => $data['og_id']]);
        } else {
            if (empty($wxmp_count) || ($wxmp_count === 1 && !empty($model->getData()))) {
                /*如果客户还没有设置任何自己的公众号，则第一个授权的公众号为客户默认的服务号*/
                $data['is_default'] = 1;
            } else {
                $data['is_default'] = 0;
            }
        }
        $model->allowField(true)->save($data);
        if (!empty($data['bids'])) {
            $bids = explode(',', $data['bids']);
            Branch::whereIn('bid', $bids)->update(['appid' => $data['authorizer_appid']]);
        }
    }

    public static function unauthorized($appid)
    {
        $w = [];
        $w['authorizer_appid'] = $appid;

        self::destroy($w, true);
        Branch::update(['appid' => ''], ['appid' => $appid]);
    }

    public function getPublicAccounts()
    {
        $list = $this->field(['authorizer_access_token', 'authorizer_refresh_token', 'func_info', 'business_info', 'key', 'cert_path', 'key_path'], true)
            ->where('status', 0)
            ->select();
        if(!$list){
            $list = [];
        }
        $m_authorizer = new Authorizer();

        $system_default = $m_authorizer->field(['authorizer_access_token', 'authorizer_refresh_token', 'func_info', 'business_info'], true)
            ->where('system_default', 1)
            ->where('status', 0)
            ->skipOgId()
            ->select();

        return array_merge($list, $system_default);
    }

    /**
     * 从微信服务器同步最新的粉丝标签
     */
    public function syncLatestTags()
    {
        $appid = $this->getData('authorizer_appid');
        $app = Wechat::getInstance($appid)->app;
        $tag = $app->user_tag;
        $tags = $tag->lists();
        $this->setAttr('tags', $tags['tags'])->save();
    }

    /**
     * 同步公众号粉丝数据
     */
    public function syncFans()
    {
        $appid = $this->getData('authorizer_appid');
        $original_id = $this->getData('user_name');
        $og_id = gvar('og_id');
        $downloaded_openid_list = $this->getOpenIds();
        $exist_openid_list = (new WxmpFans())->where('appid', $appid)->column('openid');
        $openid_list = array_diff($downloaded_openid_list, $exist_openid_list);
        $wxmp_fans_table_name = WxmpFans::getTable();
        $sql_str = "replace into `{$wxmp_fans_table_name}`(og_id, appid, original_id, openid) VALUES ('{$og_id}', '{$appid}', '{$original_id}', '%s')";
        //todo 是否需要写在一条sql中
        foreach ($openid_list as $openid) {
            $sql = sprintf($sql_str, $openid);
            Db::query($sql);
        }
        $user_info_list = $this->getAllUserInfo($downloaded_openid_list);
        foreach ($user_info_list as $item) {
            $item['appid'] = $appid;
            $item['original_id'] = $original_id;
            $item['og_id'] = $og_id;
            $m_fans = WxmpFans::get(['openid' => $item['openid']]);
            if ($m_fans) {
                $m_fans->allowField(true)->save($item);
            } else {
                $m_fans = WxmpFans::create($item);
            }
            $m_fans->syncFansTag();
        }
        return true;
    }

    /**
     * 获取公众号的粉丝的openid列表
     * @param null $next_openid
     */
    public function getFollowers($next_openid = null)
    {
        $appid = $this->getData('authorizer_appid');
        static $user_service ;
        if (empty($user_service)) {
            $app = Wechat::getInstance($appid)->app;
            $user_service = $app->user;
        }
        $api_result = $user_service->lists($next_openid);
        return $api_result;
    }

    /**
     * 获取粉丝的openid列表
     * @param null $next_openid
     */
    public function getOpenIds($next_openid = null)
    {
        $openid_list = [];
        $api_result = $this->getFollowers($next_openid);
        $next_openid = $api_result['next_openid'];
        $count = $api_result['count'];
        if (!empty($api_result['data']['openid'])) {
            $openid_list = $openid_list + $api_result['data']['openid'];
        }
        if (!empty($next_openid)) {
            $openid_list = $openid_list + $this->getOpenIds($next_openid);
        }
        return $openid_list;
    }

    /**
     * 用openid获取粉丝的用户信息
     * @param array $openid_list
     */
    public function getAllUserInfo(array $openid_list)
    {
        $user_info_list = [];
        $total = count($openid_list);
        $temp = 100;/*开发者可通过该接口来批量获取用户基本信息。最多支持一次拉取100条。*/
        if ($total > 100) {
            $count = $total/100 + ($total%100>0?1:0);
            for ($page = 0; $page < $count; $page++) {
                $offset = $page * $temp;
                $batch_openids = array_slice($openid_list, $offset, $temp);
                $user_info_list += $this->batchGetUserInfo($batch_openids);
            }
        } else {
            $user_info_list += $this->batchGetUserInfo($openid_list)->toArray();
        }
        return $user_info_list['user_info_list'];
    }

    /**
     * 批量获取粉丝信息
     * @param array $batch_openids
     */
    public function batchGetUserInfo(array $batch_openids)
    {
        $appid = $this->getData('authorizer_appid');
        static $user_service ;
        if (empty($user_service)) {
            $app = Wechat::getInstance($appid)->app;
            $user_service = $app->user;
        }
        return $user_service->batchGet($batch_openids);
    }

    /**
     * 添加粉丝标签
     * @param $tag_name
     * @return bool
     */
    public function addTag($tag_name)
    {
        try {
            $appid = $this->getData('authorizer_appid');
            $app = Wechat::getInstance($appid)->app;
            $tag = $app->user_tag;
            $tag->create($tag_name);
            $this->syncLatestTags();
            return true;
        } catch (\Exception $exception) {
            $this->error = $exception->getMessage();
            return false;
        }
    }


    /**
     * 删除粉丝标签
     * @param $tag_id
     */
    public function deleteTag($tag_id)
    {
        try {
            $appid = $this->getData('authorizer_appid');
            $app = Wechat::getInstance($appid)->app;
            $tag = $app->user_tag;
            $tag->delete($tag_id);
            $affected_fans_ids = WxmpFansTag::where('appid', $appid)->where('tag_id', $tag_id)->fetchSql(false)->column('fans_id', 'id');
            WxmpFansTag::destroy(array_keys($affected_fans_ids), true);
            $affected_fans_list = WxmpFans::all(array_values($affected_fans_ids));
            foreach ($affected_fans_list as $fans_item) {
                $fans_item->removeDeletedTag($tag_id);
            }
//            $this->removeDeletedTag($tag_id);
            $this->syncLatestTags();
            return true;
        } catch (\Exception $exception) {
            $this->error = $exception->getMessage();
            return false;
        }
    }

    /**
     * 当删除一个标签的时候（不是取消）更新本地数据库中粉丝的标签
     * @param $tag_id
     */
    private function removeDeletedTag($tag_id)
    {
        $tags = $this->getAttr('tags');
        if ($tags) {
            $key = array_search($tag_id, array_column($tags, 'id'));
            if ($key !== false) {
                unset($tags[$key]);
                $this->save(['tags' => array_values($tags)]);
            }
        }
    }

    /**
     * 修改粉丝标签名称
     * @param $tag_name
     */
    public function editTag($tag_id, $tag_name)
    {
        try {
            $appid = $this->getData('authorizer_appid');
            $app = Wechat::getInstance($appid)->app;
            $tag = $app->user_tag;
            $tag->update($tag_id, $tag_name);
            $this->syncLatestTags();
            return true;
        } catch (\Exception $exception) {
            $this->error = $exception->getMessage();
            return false;
        }
    }

    public function updateWxmp()
    {
        if(empty($this->getData())) return $this->user_error('wxmp数据为空');

        try {
            $wechat = Wechat::getApp($this->getData('authorizer_appid'));
            $authorizer_info = $wechat->open_platform->getAuthorizerInfo($this->getData('authorizer_appid'));

        } catch(\Exception $e) {
            Log::record($e->getMessage(), 'error');
            return $this->user_error($e->getMessage());
        }

        if(empty($authorizer_info) || empty($authorizer_info['authorizer_info'])) return false;
        $rs = $this->allowField('nick_name,head_img,user_name,alias,qrcode_url,business_info,principal_name')
            ->isUpdate(true)->save($authorizer_info['authorizer_info']);
        if($rs === false) return false;

        return true;
    }


    /**
     * 删除公众号
     * @param $tag_name
     */
    public function delWxmp()
    {
        if(empty($this->getData())) return $this->user_error('公众号不存在或已删除');

        try {
            $rs = $this->delete();
            if($rs === false) throw new FailResult($this->getErrorMsg());

        } catch(\Exception $e) {
            Log::record($e->getMessage(), 'error');
            return $this->user_error($e->getMessage());
        }

        return true;
    }

}