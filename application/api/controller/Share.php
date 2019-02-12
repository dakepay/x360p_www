<?php
/**
 * Author: luo
 * Time: 2018/1/26 14:46
 */

namespace app\api\controller;

use app\api\model\EduGrowup;
use app\api\model\HomeworkComplete;
use app\api\model\Review;
use app\api\model\StudentArtwork;

/**
 * Class Share
 * @Desc 机构端相关分享接口，不需要权限
 */
class Share extends Base
{
    public $apiAuth = false;
    public $noRest  = true;

    /**
     * @desc  课评详情
     * @author luo
     * @method GET
     */
    public function review()
    {
        $rvw_id = input('rvw_id');
        $m_review = new Review();
        $review = $m_review->with(['reviewFile' => ['file'],'employee','oneClass','reviewTplSetting','reviewStudent' => ['student'],'lesson'])->where('rvw_id',$rvw_id)->find();
        if(!$review){
            return $this->sendError(400,'此课评不存在或已删除!');
        }
        $review = $review->toArray();
        if(empty($review['lesson'])){
            $review['lesson'] = ['lesson_name'=>''];
            $lesson_name = '';
            if($review['cid']>0){
                $lesson_name = $review['one_class']['class_name'];
            }elseif($review['sj_id'] > 0){
                $sj_info = get_sj_info($review['sj_id']);
                $lesson_name = $sj_info['subject_name'];
            }
            $review['lesson']['lesson_name'] = $lesson_name;
        }
        if(empty($review['review_tpl_setting'])) {
            $w['rts_id'] = 1;
            $rts_info = $this->m_review_tpl_setting->where($w)->find();
            if($rts_info){
                $review['review_tpl_setting'] = $rts_info['setting'];
            }else {
                $review['review_tpl_setting'] = config('org_review_tpl');
            }
        }

        return $this->sendSuccess($review);
    }

    /**
     * @desc  学员作品详情
     * @author luo
     * @method GET
     */
    public function student_artwork_detail()
    {
        $sart_id = input('sart_id');
        if(empty($sart_id)) return $this->sendError(400, '作品id错误');
        $m_sa = new StudentArtwork();
        $info = $m_sa->with(['studentArtworkAttachment','student', 'studentArtworkReview'])->where('sart_id',$sart_id)->find();
        return $this->sendSuccess($info);
    }

    /**
     * @desc  学员完成作业详情
     * @author luo
     * @method GET
     */
    public function homework_complete()
    {
        $hc_id = input('id');
        if(empty($hc_id)) $this->sendError(400, 'param error');

        $homework_complete = HomeworkComplete::get($hc_id);
        return $this->sendSuccess($homework_complete);
    }

    /**
     * @desc  成长记录
     * @author luo
     * @method GET
     */
    public function edu_growup()
    {
        $eg_id = input('eg_id');
        if(empty($hc_id)) $this->sendError(400, 'param error');

        $with = input('with') ?? [];
        $edu_growup = EduGrowup::get($eg_id, $with);

        return $this->sendSuccess($edu_growup);
    }

}