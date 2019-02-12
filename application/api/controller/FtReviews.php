<?php
namespace app\api\controller;

use app\api\model\FtEmployee;
use think\Exception;
use think\Log;
use think\Request;
use app\api\model\FtReview;
use app\api\model\ClassAttendance;
use think\Db;

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
        $m_review = new FtReview();
        $ret = $m_review->with(['class_attendance','oneClass','ft_review_file','ft_review_student' => ['student']])->getSearchResult($input);
        return $this->sendSuccess($ret);
    }

    /**
     * 翻译情况汇总
     * @param Request $request
     */
    public function cc(Request $request){
        $input = input();

        $mFtEmployee = new FtEmployee();
        $ft_employee_info = $mFtEmployee->with('employee')->getSearchResult($input);

        $where = '';
        if (isset($input['int_day'])){
            $where = ' and fr.int_day = '.format_int_day($input['int_day']);
        }

        foreach ($ft_employee_info['list'] as $k => $v){
            $sql_a = 'select count(*) as num from x360p_class_attendance catt left join x360p_ft_review fr on catt.catt_id = fr.catt_id  where fr.eid = '.$v['eid'].$where;
            $sql_b = 'select count(*) as num from x360p_class_attendance as catt left join x360p_ft_review as fr on catt.catt_id = fr.catt_id  where fr.sent_status = 2 and fr.eid = '.$v['eid'].$where;
            $sql_c = 'select count(*) as num from x360p_class_attendance as catt left join x360p_ft_review as fr on catt.catt_id = fr.catt_id  where fr.catt_id is null and catt.eid = '.$v['eid'].$where;
            $sql_d = 'select count(*) as num from x360p_class_attendance as catt left join x360p_ft_review as fr on catt.catt_id = fr.catt_id  where fr.sent_status < 2 and fr.eid = '.$v['eid'].$where;

            $ft_employee_info['list'][$k]['has_written'] = DB::query($sql_a)[0]['num'];
            $ft_employee_info['list'][$k]['not_written'] =  DB::query($sql_c)[0]['num'];
            $ft_employee_info['list'][$k]['has_translate'] =  DB::query($sql_b)[0]['num'];
            $ft_employee_info['list'][$k]['not_translate'] =  DB::query($sql_d)[0]['num'];
        }

        return $this->sendSuccess($ft_employee_info);
    }


    /**
     * @desc  点评详情
     * @param Request $request
     * @param int $id
     * @method GET
     */
    public function get_detail(Request $request, $id = 0)
    {
        $rvw_id = $id;
        $m_review = new FtReview();
        $review = $m_review->find($rvw_id);

        return $this->sendSuccess($review);
    }

    /**
     * @desc  添加点评
     * @param Request $request
     * @method POST
     */
    public function post(Request $request)
    {
        $review_data = $request->post();
        $review_file_data = isset($review_data['review_file']) ? $review_data['review_file'] : [];
        $review_student_data = isset($review_data['review_student']) ? $review_data['review_student'] : [];

        $mFtReview = new FtReview();
        $result = $mFtReview->addOneReview($review_data, $review_file_data,$review_student_data);
        if(false === $result) return $this->sendError(400, $mFtReview->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  删除点评
     * @param Request $request
     * @method DELETE
     */
    public function delete(Request $request)
    {
        $frvw_id = input('id/d');
        $mFtreview = FtReview::get($frvw_id);
        if(empty($mFtreview)) return $this->sendError(400, '点评不存在');
	
        $result = $mFtreview->delOneReview($frvw_id, $mFtreview);
        if(false === $result) return $this->sendError(400, $mFtreview->getError());

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

    /**
     * 课评状态
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function sent_status(Request $request)
    {
        $input = input('post.');
        $frvw_id = $input['id'];
	
        $sent_status = $input['sent_status'];
        $trans_eid = $input['trans_eid'];

        $mFtReview = new FtReview();
        $result = $mFtReview->updateSentStatus($frvw_id,$trans_eid,$sent_status);

        if(!$result){
            return $this->sendError(400,$mFtReview->getError());
        }

        return $this->sendSuccess();
    }

}