<?php
/**
 * Author: luo
 * Time: 2017-11-21 19:28
**/

namespace app\api\controller;

use think\Request;
use app\api\model\TallyHelp as TallyHelpModel;
use app\api\model\Tally;

class TallyHelps extends Base
{

    public function post(Request $request)
    {
        $input = $request->post();
        if(!isset($input['type']) || empty($input['type'])) {
            return $this->sendError(400, '辅助核算类别参数不合法');
        }

        $model = new TallyHelpModel();
        $rs = $model->addOneCate($input);
        if(!$rs) return $this->sendError(400, $model->getErrorMsg());

        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $th_id = input('id/d');
        $model = new TallyHelpModel();
        $tally_help = $model->find($th_id);

        if($tally_help['type'] == TallyHelpModel::TYPE_CLIENT) {
            $is_exist = Tally::get(['client_th_id' => $tally_help['th_id']]);
            if(!empty($is_exist)) return $this->sendError(400, '存在相关往来流水，不能删除');
        }

        if($tally_help['type'] == TallyHelpModel::TYPE_ITEM) {
            $is_exist = Tally::get(['item_th_id' => $tally_help['th_id']]);
            if(!empty($is_exist)) return $this->sendError(400, '存在相关项目流水，不能删除');
        }

        if($tally_help['type'] == TallyHelpModel::TYPE_EMPLOYEE) {
            $is_exist = Tally::get(['employee_th_id' => $tally_help['th_id']]);
            if(!empty($is_exist)) return $this->sendError(400, '存在相关核算人员流水，不能删除');
        }

        $rs = $tally_help->delete();
        if(!$rs) return $this->sendError(400, '删除失败');

        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $th_id = input('id/d');
        $input = $request->only(['name','remark']);

        $tally_help = TallyHelpModel::get(['th_id' => $th_id]);
        $rs = $tally_help->save($input);
        if($rs === false) return $this->sendError(400, '修改失败');

        return $this->sendSuccess();
    }

}