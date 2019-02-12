<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2018/1/23
 * Time: 14:58
 */
namespace app\api\controller;

use app\api\model\WxmpFansMessage;
use app\api\model\WxmpFansTag;
use app\common\Wechat;
use think\Request;
use app\api\model\WxmpFans as Fans;

/**
 * 微信公众号粉丝管理
 * Class WxmpTags
 */
class WxmpFans extends Base
{
    public function get_list(Request $request)
    {
        $input = $request->get();
        $m_fans = new Fans();
        if (!empty($input['tag_id'])) {
            $fans_ids = (new WxmpFansTag())->whereIn('tag_id', $input['tag_id'])->cache(10)
                ->column('fans_id');
            if($fans_ids) {
                $m_fans->whereIn('fans_id', $fans_ids);
            }
            unset($input['tag_id']);
        }
        $app_id = Wechat::getAppid();
        $m_fans->where('appid', $app_id);
        $data = $m_fans->with(['user' => ['students'], 'employee_user'])->getSearchResult($input);
        return $this->sendSuccess($data);
    }

    public function post(Request $request)
    {
        return $this->sendError(400, 'not support!');
    }

    public function delete(Request $request)
    {
        return $this->sendError(400, 'not support!');
    }

    public function put(Request $request)
    {
        return $this->sendError(400, 'not support!');
    }

    public function get_detail(Request $request, $id = 0)
    {
        $fans_id = $id;
        $m_fans = new Fans();
        $data = $m_fans->with(['user' => ['students'], 'employee_user'])->find($fans_id);
        return $this->sendSuccess($data);
    }

    /**
     * 修改粉丝备注
     * @param Request $request
     */
    public function do_remark(Request  $request)
    {
        $fans_id = $request->param('id');
        $m_fans = Fans::get($fans_id);
        $remark = input('remark');
        $result = $m_fans->remark($remark);
        if ($result === false) {
            return $this->sendError(400, $m_fans->getError());
        }
        return $this->sendSuccess();
    }

    /**
     *编辑粉丝标签
     * @param Request $request
     */
    public function update_fans_tag(Request $request)
    {
        $fans_id = $request->post('fans_id');
        $fans = Fans::get($fans_id);
        if (empty($fans) || $fans['subscribe'] == 0) {
            return $this->sendError(400, '该粉丝的状态为未关注状态，无法编辑粉丝标签!');
        }
        $tag_ids = $request->post('tag_id/a');
        if (empty($tag_ids) || !is_array($tag_ids)) {
            return $this->sendError(400, 'tag_id参数不合法！');
        }
        $result = $fans->editTags($tag_ids);
        if ($result === false) {
            return $this->sendError(400, $fans->getError());
        }
        return $this->sendSuccess();
    }

    /**
     * 批量为用户打标签
     * @param Request $request
     */
    public function tag_users(Request $request)
    {
        if ($request->isPost()) {
            $input = $request->post();
            $rule = [
                'fans_id|粉丝id' => 'require|array',
                'tag_id|标签id'  => 'require|array',
            ];
            $right = $this->validate($input, $rule);
            if ($right !== true) {
                return $this->sendError(400, $right);
            }
            $m_fans = new Fans();
            $result = $m_fans->batchTagging($input['fans_id'], $input['tag_id']);
            if ($result !== true) {
                return $this->sendError(400, $m_fans->getError());
            }
            return $this->sendSuccess();
        } else {
            return $this->sendError(400, 'invalid request method!');
        }
    }

    /**
     * 批量为用户取消标签
     * @param Request $request
     */
    public function untag_users(Request $request)
    {
        if ($request->isPost()) {
            $input = $request->post();
            $rule = [
                'fans_id|粉丝id' => 'require|array',
                'tag_id|标签id'  => 'require|array',
            ];
            $right = $this->validate($input, $rule);
            if ($right !== true) {
                return $this->sendError(400, $right);
            }
            $m_fans = new Fans();
            $result = $m_fans->batchUnTagging($input['fans_id'], $input['tag_id']);
            if ($result !== true) {
                return $this->sendError(400, $m_fans->getError());
            }
            return $this->sendSuccess();
        } else {
            return $this->sendError(400, 'invalid request method!');
        }
    }

    /**
     * @desc  从粉丝中拉取所有粉丝
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function download_fans(Request $request) {
        $m_fans = new Fans();
        $rs = $m_fans->download_fans();
        if($rs === false) return $this->sendError(400, $m_fans->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * 同步粉丝信息
     * @param Request $request
     */
    public function sync_fans(Request $request)
    {
        if ($request->isPost()) {
            //--1-- 同步选中粉丝
            $fans_ids = $request->post('fans_id/a');
            if (empty($fans_ids) || !is_array($fans_ids)) {
                return $this->sendError(400, 'fans_id参数不合法!');
            }
            $m_fans = new Fans();
            $result = $m_fans->batchUpdateFansInfo($fans_ids);
            if ($result !== true) {
                return $this->sendError($m_fans->get_error_code(), $m_fans->getError());
            }
            return $this->sendSuccess();

        } elseif ($request->isGet()) {
            //--2-- 同步所有粉丝
            $m_fans = new Fans();
            $redis = redis();

            $total = $m_fans->count();
            $start = $redis->get('sync_fans_start') ? $redis->get('sync_fans_start') : 0;
            if($start > $total) return $this->sendSuccess(['status' => 1]);  # 1表示更新完成

            $size = 50;
            $fans_ids = $m_fans->limit($start, $size)->column('fans_id');

            $result = $m_fans->batchUpdateFansInfo($fans_ids);
            if ($result !== true) {
                return $this->sendError($m_fans->get_error_code(), $m_fans->getError());
            }

            $redis->set('sync_fans_start', $start + $size);

            return $this->sendSuccess(['status' => 0]);  # 0表示更新未完成
        } else {
            return $this->sendError(400, 'invalid request method!');
        }
    }

    /**
     * 获取粉丝与公众号的聊天记录
     * @param Request $request
     */
    public function get_list_messages(Request $request)
    {
        $fans_id = $request->param('id');
        $wx_message = new WxmpFansMessage();
        $data = $wx_message->where('fans_id', $fans_id)->order('create_time', 'desc')->getSearchResult();
        return $this->sendSuccess($data);
    }

    /**
     * 通过公众号给粉丝发送消息
     * @param Request $request
     */
    public function post_messages(Request $request)
    {
        $input = $request->post();
        $rule = [
            'type|消息类型' => 'require',
            'content|消息内容' => 'require|array'
        ];
        $right = $this->validate($input, $rule);
        if ($right !== true) {
            return $this->sendError(400, $right);
        }
        $fans = Fans::get(['fans_id' => input('id/d')]);
        if(empty($fans) || $fans->subscribe = 0) return $this->sendError(400, '粉丝不存在或者未关注');

        if($input['type'] == 'template') {
            //模板消息
            $result = $fans->sendTemplateMessage($input);
        } else {
            //客户消息
            $result = $fans->sendMessage($input);
        }

        if (!$result) {
            return $this->sendError(400, $fans->getError());
        }
        return $this->sendSuccess();
    }
}