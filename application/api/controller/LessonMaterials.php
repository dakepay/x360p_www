<?php
/**
 * Author: luo
 * Time: 2017-12-07 09:54
**/

namespace app\api\controller;


use app\api\model\LessonMaterial;
use think\Request;

class LessonMaterials extends Base
{

    public function post(Request $request)
    {
        $input = $request->post();

        $action = input('action', 'post');

        $m_lm = new LessonMaterial();
        if($action == "post") {
            $rs = $m_lm->addBatchLessonMaterial($input['data']);
        } else {
            $rs = $m_lm->updateMaterialOfLesson($input['data']);
        }

        if($rs === false) return $this->sendError(400, $m_lm->getErrorMsg());

        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        return parent::put($request);
    }

    public function delete(Request $request)
    {
        return parent::delete($request);
    }


}