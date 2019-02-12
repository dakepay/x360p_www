<?php
/**
 * Author: luo
 * Time: 2018/6/13 10:06
 */

namespace app\api\controller;
use app\api\model\StudentLessonImportLog;


use think\Request;

class StudentLessonImportLogs extends Base
{

    public function get_list(Request $request)
    {
        return parent::get_list($request);
    }

    public function delete(Request $request)
    {
        $slil_id = input('id/d');
        $mSlil = new StudentLessonImportLog();
        $result = $mSlil->delImportLog($slil_id);
        if(!$result){
            return $this->sendError(400,$mSlil->getError());
        }
        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        return $this->sendError(400, 'not support');
    }

}