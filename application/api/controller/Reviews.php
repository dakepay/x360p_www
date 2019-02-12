<?php
/**
 * Author: luo
 * Time: 2017/12/29 10:39
 */

namespace app\api\controller;


use app\api\model\FtReview;
use app\api\model\Review;
use think\Exception;
use think\Log;
use think\Request;

class Reviews extends Base
{

    /**
     * @desc  点评列表
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function get_list(Request $request)
    {
        $input = $request->get();

        if(isset($input['rvw_id'])){
            $skip_bid = true;
        }else{
            $skip_bid = false;
        }
        $mReview = new Review();
        $ret = $mReview
            ->with(['reviewFile', 'reviewStudent.student', 'oneClass'])
            ->skipBid($skip_bid)
            ->getSearchResult($input);
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
        $rvw_id = $id;
        $m_review = new Review();
        $review = $m_review->with(['reviewFile, reviewStudent'])->find($rvw_id);

        return $this->sendSuccess($review);
    }

    /**
     * @desc  添加点评
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function post(Request $request)
    {
        $review_data = $request->post();
        $review_file_data = isset($review_data['review_file']) ? $review_data['review_file'] : [];
        $review_student_data = isset($review_data['review_student']) ? $review_data['review_student'] : [];

        $m_review = new Review();
        $rs = $m_review->addOneReview($review_data, $review_file_data, $review_student_data);
        if($rs === false) return $this->sendError(400, $m_review->getErrorMsg());

        if(isset($review_data['is_push_wechat']) && $review_data['is_push_wechat']) {
            try {
                $delay = 0;
                if(isset($review_data['send_time']) && $review_data['send_time']){
                    $delay = ($review_data['send_time'] - time()) > 0 ? $review_data['send_time'] - time() : '0';
                }
                foreach($review_student_data as $student) {
                    $rs = $m_review->pushReview($student['sid'],$student['rs_id'],$delay);
//                    $rs = $m_review->wechat_tpl_notify($student['sid'],$student['rs_id'],$delay);
                    if($rs === false) Log::info($m_review->getErrorMsg());
                }
            }  catch (Exception $e) {
                Log::info('课评消息发送失败:' . $e->getMessage());
            }
        }

        if (isset($review_data['frvw_id']) && $review_data['frvw_id']>0){
            $mFtReview = new FtReview();
            $rvw_id = $m_review->getAttr('rvw_id');
            $mFtReview->updateReview($review_data['frvw_id'],$rvw_id);
        }

        return $this->sendSuccess();
    }

    public function push()
    {
        $rvw_id = input('rvw_id');
        if(empty($rvw_id)) return $this->sendError(400, 'rvw_id 错误');

        $review = Review::get($rvw_id, ['review_student']);

        if(empty($review) || empty($review['review_student'])) {
            return $this->sendError(400, '课评不存在，或者没有相关课评学生,无法推送');
        }

        try {
            foreach($review['review_student'] as $student) {
//                $rs = $review->wechat_tpl_notify($student['sid'],$student['rs_id']);
                $rs = $review->pushReview($student['sid'],$student['rs_id']);

                if($rs === false) log_write($review->getErrorMsg(), 'error');
            }
        }  catch (\Exception $e) {
            return $this->sendError(400,$e->getMessage());
        }

        return $this->sendSuccess();
    }

    /**
     * @desc  删除点评
     * @author luo
     * @param Request $request
     * @method DELETE
     */
    public function delete(Request $request)
    {
        $rvw_id = input('id/d');
        $review = Review::get($rvw_id);
        if(empty($review)) return $this->sendError(400, '点评不存在');

        $rs = $review->delOneReview($rvw_id, $review);
        if($rs === false) return $this->sendError(400, $review->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  修改点评
     * @author luo
     * @param Request $request
     * @method PUT
     */
    public function put(Request $request)
    {
        return $this->sendError('不能编辑');
    }


}