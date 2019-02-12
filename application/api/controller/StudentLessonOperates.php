<?php
/**
 * Author: luo
 * Time: 2018/6/4 11:39
 */

namespace app\api\controller;


use app\api\model\StudentLessonOperate;
use think\Request;

class StudentLessonOperates extends Base
{

    public function get_list(Request $request)
    {
        $get = $request->get();
        $m_slo = new StudentLessonOperate();
        $ret = $m_slo->getSearchResult($get);
        
        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $post = $request->post();
        $mSlo = new StudentLessonOperate();
        if(!empty($post['op_type']) && $post['op_type'] == StudentLessonOperate::OP_TYPE_SEND) {
            $rs = $mSlo->addOperation($post, true);
            if($rs === false) return $this->sendError(400, $mSlo->getError());
        }

        if(!empty($post['op_type']) && $post['op_type'] == StudentLessonOperate::OP_TYPE_TRANSFER) {
            $rs = $mSlo->transferImportedLessonHours($post);
            if($rs === false) return $this->sendError(400, $mSlo->getError());
        }

        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        return $this->sendSuccess('not support');
    }
    
    public function delete(Request $request)
    {
        $slo_id = input('id/d');
        $mSlo = new StudentLessonOperate();

        $result = $mSlo->delOperation($slo_id);
        if (false === $result){
            return $this->sendError(400,$mSlo->getError());
        }

        return $this->sendSuccess();
    }

}