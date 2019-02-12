<?php
namespace app\api\controller;

use app\api\model\StudentReferer;
use think\Request;

class StudentReferers extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->get();
        $model = new StudentReferer();
        $ret = $model->getSearchResult($input);

        if($ret['total'] > 0){
            foreach($ret['list'] as $k=>$r){
                $ret['list'][$k]['student_name'] = get_student_name($r['sid']);
                $ret['list'][$k]['referer_student_name'] = get_student_name($r['referer_sid']);
            }
        }
        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $input = input();

        $sid = isset($input['sid']) ? intval($input['sid']) : 0;
        $referer_sid = isset($input['referer_sid']) ? intval($input['referer_sid']) : 0;
        $charge_eid = isset($input['charge_eid']) ? intval($input['charge_eid']) : 0;

        $mStudentReferer = new StudentReferer();
        $rs = $mStudentReferer->createStudentReferer($sid,$referer_sid,$charge_eid);
        if (false === $rs) return $this->sendError(400,$mStudentReferer->getError());

        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $input = input();

        $sr_id = $input['sr_id'];
        $referer_teacher_eids = isset($input['referer_teacher_eids']) ? $input['referer_teacher_eids'] : '';
        $referer_edu_eid = isset($input['referer_edu_eid']) ? intval($input['referer_edu_eid']) : 0;
        $referer_cc_eid = isset($input['referer_cc_eid']) ? intval($input['referer_cc_eid']) : 0;

        $mStudentReferer = new StudentReferer();
        $rs = $mStudentReferer->updateReferer($sr_id,$referer_teacher_eids,$referer_edu_eid,$referer_cc_eid);
        if (false === $rs) return $this->sendError(400,$mStudentReferer->getError());

        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $id = input('id/d');

        $mStudentReferer = new StudentReferer();
        $rs = $mStudentReferer->delStudentReferer($id);
        if ($rs === false) return $this->sendError(400,'删除失败');

        return $this->sendSuccess();
    }



}