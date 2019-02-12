<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/10/19
 * Time: 9:19
 */
namespace app\api\controller;

use app\api\model\WxmpMaterial;
use think\Request;

class WxmpMaterials extends Base
{
    public function get_detail(Request $request, $id = 0)
    {
        return 'todo';
    }

    public function get_list(Request $request)
    {
        $input  = $request->get();
        $result = m('WxmpMaterial')->with(['items'])->getSearchResult($input, true);
        return $this->sendSuccess($result);
    }

    public function post(Request $request)
    {
        return 'todo';
    }

    public function put(Request $request)
    {
        return 'todo';
    }

    public function delete(Request $request)
    {
        $material_id = $request->param('id');
        $material = WxmpMaterial::get($material_id);
        if (!$material) {
            return $this->sendError(404, 'resource not found');
        }
        $result = $material->delete_material();
        if (!$result) {
            return $this->sendError(400, $material->getError());
        }
        return $this->sendSuccess();
    }

    public function sync(Request $request)
    {
        $input['appid'] = $request->param('appid');
        $input['type']  = $request->param('type');
        $rule = [
            'appid' => 'require',
            'type'  => 'require',
        ];
        $right = $this->validate($input, $rule);
        if ($right !== true) {
            return $this->sendError(400, $right);
        }
        $material = new WxmpMaterial();
        $result   = $material->sync_material($input['type'], $input['appid']);
        if (!$result) {
            return $this->sendError(400, $material->getError());
        }
        return $this->sendSuccess($result);
    }

}