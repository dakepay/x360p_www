<?php
/**
 * Author: luo
 * Time: 2017-12-22 09:45
**/

namespace app\sapi\controller;

use app\sapi\model\StudentLesson;
use app\sapi\model\Lesson;
use think\Request;

class Lessons extends Base
{
    public function get_list(Request $request)
    {
        $sid = global_sid();
        $input = $request->get();
        $input['sid'] =  $sid;
        $m_sl = new StudentLesson();

        $where = [];
        if(isset($input['is_learning'])) {
            $learning_status = intval($input['is_learning']);
            if($learning_status == 2){
                $ret['list'] = [];
                return $this->sendSuccess($ret);
            }
        
            $lesson_status = $learning_status == 0 ? ['lt',2]:2;
            $where['lesson_status'] = $lesson_status;
         
        }
        $ret = $m_sl->with(['lesson', 'oneClass'])->where($where)->getSearchResult($input);

        return $this->sendSuccess($ret);
    }

    public function get_detail(Request $request, $id = 0){
        $Lesson = Lesson::get($id, ['attachments', 'goods']);
        if (!$Lesson) {
            return $this->sendError(400, '该课程不存在或已删除');
        }
        return $this->sendSuccess($Lesson);
    }
}