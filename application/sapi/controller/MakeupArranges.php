<?php
/**
 * Author: luo
 * Time: 2018/6/30 12:11
 */

namespace app\sapi\controller;


use app\sapi\model\MakeupArrange;
use app\sapi\model\CourseArrangeStudent;
use think\Request;

class MakeupArranges extends Base
{

    public function get_list(Request $request)
    {
        $m_ma = new MakeupArrange();
        $get = $request->get();
        if(isset($get['activity']) && $get['activity'] == 1){
        	$model = new CourseArrangeStudent;
        	$ret = $model->alias(['x360p_course_arrange_student'=>'cas','x360p_class'=>'c'])->join('x360p_class','cas.cid = c.cid and cas.cid > 0 and c.class_type = 2')->getSearchResult($get);
        	return $this->sendSuccess($ret);
        }
        $ret = $m_ma->getSearchResult($get);
        return $this->sendSuccess($ret);
    }

}