<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/10/17
 * Time: 17:25
 */
namespace app\api\controller;

use app\api\model\Wxmp;
use app\api\model\WxmpMenu;
use think\Request;

class WxmpMenus extends Base
{
    public function get_list(Request $request)
    {
        $input = $request->get();
        $result = m('WxmpMenu')->order('status', 'desc')->getSearchResult($input);
        return $this->sendSuccess($result);
    }

    /**
     * @desc  添加菜单
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function post(Request $request)
    {
        $input = $request->post();
        $rule = [
            'wxmp_id'             => 'require|integer',
            'group_name|菜单组名称' => 'require',
            'buttons|菜单内容'      => 'require|array',
        ];
        $right = $this->validate($input, $rule);
        if ($right !== true) {
            return $this->sendError(400, $right);
        }
        $wxmp = Wxmp::get($input['wxmp_id']);
        if (!$wxmp) {
            return $this->sendError(400, 'wxmp_id is invalid');
        }
        $input['appid'] = $wxmp['authorizer_appid'];

        if(isset($input['matchrule'])) {
            $input['matchrule'] = array_filter($input['matchrule']);
        }

        $menu = new WxmpMenu();
        $result = $menu->addMenu($input);
        if (!$result) {
            return $this->sendError(400, $menu->getError());
        }
        return $this->sendSuccess();
    }

    /**
     * @desc  修改菜单
     * @author luo
     * @param Request $request
     * @method PUT
     */
    public function put(Request $request)
    {
        $wm_id = $request->param('id');
        $menu  = WxmpMenu::get($wm_id);
        if (empty($menu)) {
            return $this->sendError(404, 'resource not found');
        }
        $put = $request->put();
        //$input['group_name'] = $request->put('group_name');
        $input['group_name'] = $put['group_name'];
        //$input['buttons'] = $request->put('buttons/a');
        $input['buttons'] = $put['buttons'];
        if (empty($input['group_name']) || empty($input['buttons']) || !is_array($input['buttons'])) {
            return $this->sendError(400, 'invalid parameter');
        }

        $input['matchrule'] = $put['matchrule'];
        if(isset($input['matchrule']) && is_array($input['matchrule'])) {
            $input['matchrule'] = array_filter($input['matchrule']);
        }

        $result = $menu->editMenu($input);
        if (!$result) {
            return $this->sendError(400, $menu->getError());
        }
        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $wm_id = $request->param('id');
        $menu  = WxmpMenu::get($wm_id);
        if (empty($menu)) {
            return $this->sendError(404, 'resource not found');
        }
        $result = $menu->deleteMenu();
        if (!$result) {
            return $this->sendError(400, $menu->getError());
        }
        return $this->sendSuccess();
    }

    public function do_active(Request $request)
    {
        $wm_id = $request->param('id');
        $menu  = WxmpMenu::get($wm_id);
        if (empty($menu)) {
            return $this->sendError(404, 'resource not found');
        }
        $result = $menu->active();
        if (!$result) {
            return $this->sendError(400, $menu->getError());
        }
        return $this->sendSuccess();
    }

    /**
     * @desc  同步微信菜单
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function sync(Request $request)
    {
        $appid = $request->param('appid');
        if (empty($appid)) {
            return $this->sendError('缺少appid!');
        }
        $menu = new WxmpMenu();
        $result = $menu->sync($appid);
        if (!$result) {
            if(strpos($menu->getError(), 'menu no exist hint') >= 0) {
                return $this->sendError(400, '公众号后台还没设置菜单');
            }
            return $this->sendError(400, $menu->getError());
        }
        return $this->sendSuccess($result);
    }
}