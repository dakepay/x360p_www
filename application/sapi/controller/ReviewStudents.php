<?php
/**
 * Author: luo
 * Time: 2018-01-04 14:46
**/

namespace app\sapi\controller;

use app\sapi\model\Review;
use app\sapi\model\ReviewStudent;
use think\Request;

class ReviewStudents extends Base
{

    /**
     * @desc  方法描述
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function get_list(Request $request)
    {
        $input = $request->get();
        $sid = global_sid();
        $m_rs = new ReviewStudent();
        $ret = $m_rs->where('sid', $sid)->with(['employee', 'lesson'])->getSearchResult($input);
        return $this->sendSuccess($ret);
    }

    /**
     * @desc  点评详情
     * @author luo
     * @param Request $request
     * @param int $id
     * @method GET
     */
    public function get_detail(Request $request, $id = 0)
    {
        $rs_id = $id;
        $m_rs = new ReviewStudent();
        $review = $m_rs->with(['review.reviewFile', 'employee', 'lesson'])->find($rs_id);

        return $this->sendSuccess($review);
    }

}