<?php
/**
 * Author: luo
 * Time: 2017-12-29 11:54
**/

namespace app\sapi\controller;

use app\api\model\Event;
use app\sapi\model\Employee;
use app\sapi\model\EventSignUp;
use app\sapi\model\Review;
use app\sapi\model\ReviewStudent;
use app\sapi\model\StudentArtwork;
use think\Log;
use think\Request;

/**
 * 分享出去的接口，不需要验证
 * @package app\sapi\controller
 */
class Share extends Base
{
    public $apiAuth = false;
    public $noRest  = true;

    protected function _init()
    {
        parent::_init();
        $client = gvar('client');
        //分享的是加盟商数据
        if(!empty($client) && !empty($client['og_id'])) {
            gvar('og_id', $client['og_id']);
        }
    }

    public function employee()
    {
        $eid = input('eid/d');
        $data = Employee::get($eid, ['profile', 'subjects']);
        return $this->sendSuccess($data);
    }

    /**
     * @desc  点评详情
     * @author luo
     * @param int $id
     * @method GET
     */
    public function my_review_detail()
    {
        $rs_id = input('rs_id/d');
        $m_rs = new ReviewStudent();
        $review = $m_rs->with(['student', 'review' => ['reviewFile.file', 'reviewTplSetting'], 'employee', 'lesson'])
            ->find($rs_id);
        if(empty($review)) return $this->sendError(400,'课评不存在');
        if(!empty($review)) {
            $review['org_name'] = org_name();
            $review['class_name'] = get_class_name($review['cid']);
            if(isset($review['review']) && isset($review['review']['review_tpl_setting'])
                && empty($review['review']['review_tpl_setting'])) {
                $review['review']['review_tpl_setting'] = [
                    'setting' =>  config('org_review_tpl'),
                    'rts_id' => 0,
                    'og_id' => 0,
                    'bid' => 0,
                    'name' => '默认配置模板'
                ];
            }
        }

        $rs = $review->setInc('view_times');
        if($rs === false) Log::record('增加个人课评查阅次数失败');

        if(!empty($review->review)) {
            $rs = (new Review())->where('rvw_id', $review->review->rvw_id)->setInc('view_times');
            if($rs === false) Log::record('分享：课评分享次数增加失败');
        }

        return $this->sendSuccess($review);
    }

    /**
     * @desc  点评分享成功
     * @author luo
     * @method GET
     */
    public function after_share_review()
    {
        $rs_id = input('rs_id/d');
        $review_student = ReviewStudent::get($rs_id);
        if(empty($review_student)) return $this->sendError(400, '不存在点评');

        $rs = $review_student->setInc('share_times');
        if($rs === false) Log::record('分享：课评分享次数增加失败');

        $rs = (new Review())->where('rvw_id', $review_student->rvw_id)->setInc('share_times');
        if($rs === false) Log::record('分享：课评分享次数增加失败');

        return $this->sendSuccess();
    }

    public function student_artwork_detail()
    {
        $sart_id = input('sart_id');
        if(empty($sart_id)) return $this->sendError(400, '作品id错误');
        $m_sa = new StudentArtwork();
        $info = $m_sa->with(['studentArtworkAttachment','student', 'studentArtworkReview'])->find($sart_id);
        return $this->sendSuccess($info);
    }

    //活动详情
    public function event_detail()
    {
        $event_id = input('event_id');

        $event = Event::get($event_id, ['event_attachment', 'one_class', 'event_sign_up.student']);
        if(empty($event)) return $this->sendError(400, '活动不存在');

        $event->view_nums += 1;
        $event->save();
        
        return $this->sendSuccess($event);
    }

    //活动报名
    public function event_sign_up(Request $request)
    {
        $post = $request->post();
        $m_esu = new EventSignUp();
        $rs = $m_esu->signUp($post);
        if($rs === false) return $this->sendError(400, $m_esu->getErrorMsg());
        
        return $this->sendSuccess();
    }



}
