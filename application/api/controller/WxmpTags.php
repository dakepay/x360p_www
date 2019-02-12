<?php
/**
 * Author: luo
 * Time: 2018/2/7 14:39
 */

namespace app\api\controller;

use app\api\model\WxmpFansTag;
use app\common\Wechat;
use think\Request;

class WxmpTags extends Base
{
    /**
     * @desc  所有标签列表
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function get_list(Request $request)
    {
        try {
            $app = Wechat::getInstance()->app;
            $list = $app->user_tag->lists();
            $m_tag = new WxmpFansTag();
            $data['tags'] = [];
            foreach($list['tags'] as $row) {
                $tmp = $row;
                $tmp['count'] = 0;
                $count = $m_tag->where('tag_id', $row['id'])->count();
                $tmp['count'] = $count;
                $data['tags'][] = $tmp;
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getCode(), $e->getMessage());
        }
        return $this->sendSuccess($data);
    }

    /**
     * @desc  添加标签
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function post(Request $request)
    {
        $name = $request->post('name');
        if(empty($name) || strlen($name) > 50) return $this->sendError(400, 'param error');
        try {
            $app = Wechat::getApp();
            $rs = $app->user_tag->create($name);
        } catch (\Exception $e) {
            return $this->sendError($e->getCode(), $e->getMessage());
        }

        return $this->sendSuccess();
    }

    /**
     * @desc  更改标签
     * @author luo
     * @param Request $request
     * @method PUT
     */
    public function put(Request $request)
    {
        $tag_id = input('id');
        $name = $request->put('name');
        try {
            $app = Wechat::getApp();
            $rs = $app->user_tag->update($tag_id, $name);
        } catch (\Exception $e) {
            return $this->sendError($e->getCode(), $e->getMessage());
        }

        return $this->sendSuccess();
    }

    /**
     * @desc  删除标签
     * @author luo
     * @param Request $request
     * @method DELETE
     */
    public function delete(Request $request)
    {
        $tag_id = input('id');
        try {
            $app = Wechat::getApp();
            $rs = $app->user_tag->delete($tag_id);
        } catch (\Exception $e) {
            return $this->sendError($e->getCode(), $e->getMessage());
        }

        return $this->sendSuccess();
    }

}