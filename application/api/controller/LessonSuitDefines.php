<?php
/**
 * Author: luo
 * Time: 2018/5/25 16:38
 */

namespace app\api\controller;


use app\api\model\LessonSuitDefine;
use think\Request;

class LessonSuitDefines extends Base
{

    public function get_list(Request $request)
    {
        $get = $request->get();
        $m_lsd = new LessonSuitDefine();
        $ret = $m_lsd->getSearchResult($get);
        
        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $post = $request->post();
        $m_lsd = new LessonSuitDefine();
        $rs = $m_lsd->addLessonSuitDefine($post);
        if($rs === false) return $this->sendError(400, $m_lsd->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $lsd_id = input('id');
        $put = $request->put();
        $put['lsd_id'] = $lsd_id;

        $m_lsd = new LessonSuitDefine();
        $rs = $m_lsd->allowField(true)->isUpdate(true)->save($put);
        if($rs === false) return $this->sendError(400, $m_lsd->getErrorMsg());

        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $lsd_id = input('id');

        $lesson_suit_define = LessonSuitDefine::get($lsd_id);
        if(empty($lesson_suit_define)) return $this->sendSuccess();

        $rs = $lesson_suit_define->delLessonSuitDefine();
        if($rs === false) return $this->sendError(400, $lesson_suit_define->getErrorMsg());
        
        return $this->sendSuccess();
    }

}