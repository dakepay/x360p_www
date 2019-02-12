<?php
/**
 * Author: luo
 * Time: 2018/4/27 16:11
 */

namespace app\api\controller;


use app\api\model\ClassStudent;
use think\Request;

class ClassStudents extends Base
{
    /**
     * 学员出入班记录
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function assign_class_logs(Request $request)
    {
        $input = $request->param();
        $sid = $input['sid'];
        $student = get_student_info($sid);
        if(empty($student)){
            return $this->sendError(400,'学员不存在或已删除');
        }
        $mClassStudent = new ClassStudent;

        $inClass = $mClassStudent->where(array('sid'=>$sid,'in_time'=>array('gt',0)))->field('cid,create_uid,in_time')->order('cs_id desc')->select();
        $inClass = collection($inClass)->toArray();
        foreach ($inClass as &$item) {
            $item['op_type'] = 1;
            $item['op_time'] = date('Y-m-d',strtotime($item['in_time']));
        }

        $outClass = $mClassStudent->where(array('sid'=>$sid,'out_time'=>array('gt',0)))->field('cid,create_uid,out_time')->order('cs_id desc')->select();
        $outClass = collection($outClass)->toArray();
        foreach ($outClass as &$item) {
            $item['op_type'] = 2;
            $item['op_time'] = date('Y-m-d',$item['out_time']);
        }

        $ret['list'] = array_merge($inClass,$outClass);
        foreach ($ret['list'] as &$item) {
            $item['cid'] = get_class_name($item['cid']);
            $item['create_uid'] = get_user_name($item['create_uid']);
        }
        //$ret['total'] = count($ret['list']);

        return $this->sendSuccess($ret);
    }

    public function put(Request $request)
    {
        $put = $request->put();
        $cs_id = input('id');
        $class_student = ClassStudent::get($cs_id);
        $rs = $class_student->edit($put);
        if($rs === false) return $this->sendError(400, $class_student->getErrorMsg());
        
        return $this->sendSuccess();
    }

    /**
     * @desc  方法描述
     * @author luo
     * @param Request $request
     * @url   /api/lessons/:id/
     * @method POST
     */
    public function update_list(Request $request)
    {
        $post = $request->post();
        $m_cs = new ClassStudent();
        $rs = $m_cs->updateList($post);
        if($rs === false) return $this->sendError(400, $m_cs->getErrorMsg());
        
        return $this->sendSuccess();
    }

}