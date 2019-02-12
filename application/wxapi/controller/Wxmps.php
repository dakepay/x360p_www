<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/10/17
 * Time: 10:55
 */
namespace app\wxapi\controller;

use app\api\controller\Base;
use app\api\model\Wxmp;
use app\api\model\WxmpMenu;
use think\Request;

class Wxmps extends Base
{
    public function get_list(Request $request)
    {
        $input = input();
        $result = (new Wxmp())->getSearchResult($input);
        return $this->sendSuccess($result);
    }

    /*为服务号添加模板*/
    public function get_list_templates(Request $request)
    {
        $wxmp_id = $request->param('id');
        $wxmp = Wxmp::get($wxmp_id);
        if (empty($wxmp)) {
            return $this->sendError(400, 'resource not found!');
        }
        //todo
    }

    public function do_sync_menu(Request $request)
    {
        $wxmp_id = $request->param('id');
        $menu_model = new WxmpMenu();
        $result = $menu_model->sync($wxmp_id);
        if (!$result) {
            return $this->sendError(400, $menu_model->getError());
        }
        return $this->sendSuccess($result);
    }

    public function get_list_menus(Request $request, $subres)
    {
        $wxmp_id = $request->param('id');
        $input = $request->get();
        $input['wxmp_id'] = $wxmp_id;
        $ret = (new WxmpMenu())->order('status', 'desc')->getSearchResult($input, true);
        return $this->sendSuccess($ret);
    }

    public function post_templates(Request $request)
    {
        $wxmp_id = $request->param('id');
        $wxmp    = Wxmp::get($wxmp_id);
        if (empty($wxmp)) {
            return $this->sendError(400, 'resource not found!');
        }
        $input['template_message_config'] = $request->post('template_message_config/a');
        $result = $wxmp->updateTemplates($input);
        if ($result == false) {
            return $this->sendError(400, $wxmp->getError());
        }
        return $this->sendSuccess();
    }

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
         $wxmp->save();
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