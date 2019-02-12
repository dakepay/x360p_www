<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2018/1/23
 * Time: 14:58
 */
namespace app\api\controller;

use app\api\model\Wxmp;
use app\common\Wechat;
use think\Request;
use app\api\model\WxmpFansTag;
use app\api\model\WxmpFans as Fans;

/**
 * 微信公众号
 * Class WxmpTags
 */
class Wxmps extends Base
{
    public function get_list(Request $request)
    {
        $input = $request->get();
        $w['og_id'] = gvar('og_id');
        $m_wxmp = new Wxmp();
        $data = [];
        $data['total'] = $m_wxmp->where($w)->count();
        if ($data['total'] > 0) {
            $data['list']  = $m_wxmp->where($w)->field(['authorizer_access_token', 'authorizer_refresh_token', 'func_info', 'business_info', 'key', 'cert_path', 'key_path'], true)
                ->select();
        } else {
            $data['list'] = [];
        }
        if ($data['list']) {
            foreach ($data['list'] as $item) {
                try {
                    $item->syncLatestTags();
                } catch (\Exception $exception) {
                    $data['error'] = $exception->getMessage();
                }
            }
        }
        return $this->sendSuccess($data);
    }

    protected function get_detail(Request $request, $id = 0)
    {

    }

    public function post(Request $request)
    {
        return $this->sendError(400, 'not support!');
    }

    public function delete(Request $request)
    {
        $wxmp_id = input('id');
        $wxmp = Wxmp::get($wxmp_id);
        if(empty($wxmp)) $this->sendError(400, '公众号不存在或已删除');
        $rs = $wxmp->delWxmp();
        if($rs === false) return $this->sendError(400, $wxmp->getErrorMsg());
        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $wxmp_id = input('id');
        $wxmp = Wxmp::get($wxmp_id);
        if(empty($wxmp)) $this->sendError(400, 'wxmp_id 错误');

        $rs = $wxmp->updateWxmp();
        if($rs === false) return $this->sendError(400, $wxmp->getErrorMsg());
        
        return $this->sendSuccess();
    }

    /**
     * 同步粉丝
     * @param Request $request
     */
    public function sync(Request $request)
    {
//        set_time_limit(0);
        $wxmp_id = $request->param('wxmp_id');
        if (!empty($wxmp_id)) {
            $m_wxmp = Wxmp::get($wxmp_id);
            if(empty($m_wxmp)) return $this->sendError(400, '授权公众号信息不存在或已经取消授权');
        } else {
            $appid = Wechat::getAppid();
            $m_wxmp = Wxmp::get(['authorizer_appid' => $appid]);
            if(empty($m_wxmp)) return $this->sendError(400, '授权公众号信息不存在或已经取消授权');
        }
        $result = $m_wxmp->syncFans();
        if ($result === false) {
            return $this->sendError(400, $m_wxmp->getError());
        }
        return $this->sendSuccess();
    }

    /**
     * 添加标签
     * @param Request $request
     * @param $subres
     */
    public function post_tags(Request $request)
    {
        $wxmp_id = $request->param('id');
        $m_wxmp = Wxmp::get($wxmp_id);
        if (!$m_wxmp) {
            return $this->sendError(400, '授权公众号信息不存在或已经取消授权');
        }
        $tag_name = input('tag_name');/*不得超过6个汉字或12个字符*/
        $result = $m_wxmp->addTag($tag_name);
        if ($result === false) {
            return $this->sendError(400, $m_wxmp->getError());
        }
        return $this->sendSuccess();
    }

    /**
     * 删除标签
     * @param Request $request
     */
    public function delete_tags(Request $request)
    {
        $wxmp_id = $request->param('id');
        $m_wxmp = Wxmp::get($wxmp_id);
        if (!$m_wxmp) {
            return $this->sendError(400, '授权公众号信息不存在或已经取消授权');
        }
        $tag_id = $request->param('subid');
        $result = $m_wxmp->deleteTag($tag_id);
        if ($result === false) {
            return $this->sendError(400, $m_wxmp->getError());
        }
        return $this->sendSuccess();
    }

    /**
     * 修改标签
     * @param Request $request
     */
    public function put_tags(Request $request)
    {
        $wxmp_id = $request->param('id');
        $m_wxmp = Wxmp::get($wxmp_id);
        if (!$m_wxmp) {
            return $this->sendError(400, '授权公众号信息不存在或已经取消授权');
        }
        $tag_name = input('tag_name');
        $tag_id = $request->param('subid');
        $result = $m_wxmp->editTag($tag_id, $tag_name);
        if ($result === false) {
            return $this->sendError(400, $m_wxmp->getError());
        }
        return $this->sendSuccess();
    }

    /**
     * 获取公众号粉丝
     * @param Request $request
     */
    public function get_list_fans(Request $request)
    {
        $id = $request->param('id');
        $m_wxmp = Wxmp::get($id);
        if (!$m_wxmp) {
            return $this->sendError(400, 'invalid request!');
        }
        $input = $request->get();
        $m_fans = new Fans();
        $m_fans->where('appid', $m_wxmp['authorizer_appid']);
        if (!empty($input['tag_id'])) {
            $fans_ids = (new WxmpFansTag)->where('tag_id', $input['tag_id'])->column('fans_id');
            if ($fans_ids) {
                $m_fans->whereIn('fans_id', $fans_ids);
            }
            unset($input['tag_id']);
        }
        $data = $m_fans->getSearchResult($input);
        return $this->sendSuccess($data);
    }

    /**
     * @desc  默认回复消息
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function default_message(Request $request)
    {
        $wxmp_id = $request->param('wxmp_id', 1);
        $rule_id = $request->param('rule_id/d');
        if (empty($rule_id) || !is_numeric($rule_id)) {
            return $this->sendError(400, 'invalid parameter');
        }
        $wxmp = Wxmp::get($wxmp_id);
        if (!$wxmp) {
            return $this->sendError(404, 'resource not found');
        }
        $wxmp->default_message = $rule_id;
        $rs = $wxmp->save();
        if($rs === false) return $this->sendError(400, $wxmp->getErrorMsg());

        return $this->sendSuccess();
    }

    public function welcome_message(Request $request)
    {
        $wxmp_id = $request->param('wxmp_id');
        $rule_id = $request->param('rule_id/d');
        if (empty($rule_id) || !is_numeric($rule_id)) {
            return $this->sendError(400, 'invalid parameter');
        }
        $wxmp = Wxmp::get($wxmp_id);
        if (!$wxmp) {
            return $this->sendError(404, 'resource not found');
        }
        $wxmp->welcome_message = $rule_id;
        $wxmp->save();
        return $this->sendSuccess();
    }
}