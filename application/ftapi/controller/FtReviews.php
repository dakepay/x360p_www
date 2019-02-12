<?php
namespace app\ftapi\controller;

use app\ftapi\model\FtReview;
use think\Exception;
use think\Log;
use think\Request;

class FtReviews extends Base
{

    /**
     * @desc  点评列表
     * @param Request $request
     * @method GET
     */
    public function get_list(Request $request)
    {
        $input = $request->get();
        $eid = global_eid();
        $employee_info = get_employee_info($eid);

        $w_fr['eid'] = $employee_info['eid'];
        $mFt_review = new FtReview();
        $ret = $mFt_review->where($w_fr)->with(['ft_review_student' => ['student']])->getSearchResult($input);
        return $this->sendSuccess($ret);
    }

    /**
     * @desc  点评详情
     * @param Request $request
     * @param int $id
     * @method GET
     */
    public function get_detail(Request $request, $id = 0)
    {
        $rvw_id = input('id/d');
        $mFt_review = new FtReview();
        $ret = $mFt_review->with(['ft_review_student' => ['student']])->find($rvw_id);

        return $this->sendSuccess($ret);
    }

    /**
     * @desc  添加点评
     * @param Request $request
     * @method POST
     */
    public function post(Request $request)
    {
        $ft_review_data = $request->post();
        $ft_review_file_data = isset($ft_review_data['ft_review_file']) ? $ft_review_data['ft_review_file'] : [];
        $ft_review_student_data = isset($ft_review_data['review_student']) ? $ft_review_data['review_student'] : [];

        $mFtReview = new FtReview();
        $rs = $mFtReview->addFtReview($ft_review_data, $ft_review_file_data,$ft_review_student_data);
        if($rs === false) return $this->sendError(400, $mFtReview->getError());

        return $this->sendSuccess();
    }

    /**
     * @desc  删除点评
     * @param Request $request
     * @method DELETE
     */
    public function delete(Request $request)
    {
        $rvw_id = input('id/d');
        $review = FtReview::get($rvw_id);
        if(empty($review)) return $this->sendError(400, '点评不存在');

        $rs = $review->delOneReview($rvw_id, $review);
        if($rs === false) return $this->sendError(400, $review->getError());

        return $this->sendSuccess();
    }

    /**
     * @desc  修改点评
     * @param Request $request
     * @method PUT
     */

    public function put(Request $request)
    {
        $ft_review_data = $request->post();
        $ft_review_file_data = isset($ft_review_data['ft_review_file']) ? $ft_review_data['ft_review_file'] : [];
        $ft_review_student_data = isset($ft_review_data['review_student']) ? $ft_review_data['review_student'] : [];

        $ft_review = FtReview::get($ft_review_data['frvw_id']);
        if(empty($ft_review)) return $this->sendError(400, 'review dont exist');
        if ($ft_review['sent_status'] != 0) return $this->sendError(400, 'review sent cannot be edited');

        $rs = $ft_review->updateFtReview($ft_review_data['frvw_id'],$ft_review_data,$ft_review_file_data,$ft_review_student_data);
        if($rs === false) return $this->sendError(400, $ft_review->getError());

        return $this->sendSuccess();
    }
}