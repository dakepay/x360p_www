<?php
/**
 * Author: luo
 * Time: 2017-12-07 18:50
 **/

namespace app\api\controller;


use app\api\model\CourseArrange;
use app\api\model\MakeupArrange;
use think\Request;

class MakeupArranges extends Base
{
    /**
     * @desc  放假补课或者缺勤补课
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function post(Request $request)
    {
        $post = $request->post();
        $course = !empty($post['course']) ? $post['course'] : [];
        $sa_ids = !empty($post['sa_ids']) ? $post['sa_ids'] : [];
        $slv_ids = !empty($post['slv_ids']) ? $post['slv_ids'] : [];
        if(empty($course)) return $this->sendError(400, '课程参数错误');

        $mMakeupArrange = new MakeupArrange();
        $rs = $mMakeupArrange->addMakeUpStudents($course, $sa_ids, $slv_ids);
        if($rs === false) return $this->sendError(400, $mMakeupArrange->getError());

        return $this->sendSuccess();
    }

    /**
     * @desc  删除一条补课
     * @author luo
     * @param Request $request
     * @method DELETE
     */
    public function delete(Request $request)
    {
        $ma_id = input('id');
        $makeup_arrange = MakeupArrange::get($ma_id);
        if(empty($makeup_arrange)) return $this->sendSuccess();

        $rs = $makeup_arrange->delOne();
        if($rs === false) return $this->sendError(400, $makeup_arrange->getError());

        return $this->sendSuccess();
    }

}