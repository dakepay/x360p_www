<?php
/**
 * Author: luo
 * Time: 2017-12-28 20:05
**/

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;

class ReviewTplSetting extends Base
{

    protected $type = [
        'setting' => 'json',
    ];


    //删除模板
    public function delOneTpl($rts_id, $tpl_setting = null, $is_force = 0)
    {
        if(is_null($tpl_setting)) {
            $tpl_setting = $this->findOrFail($rts_id);
        }

        $review = (new Review())->where('rts_id', $rts_id)->order('rvw_id desc')->limit(1)->find();
        if(!empty($review) && !$is_force) {
            return $this->user_error('有相关的点评，是否把相关点评全部转为默认模板', self::CODE_HAVE_RELATED_DATA);
        }

        $tpl_define_num = (new ReviewTplDefine())->where('rts_id', $rts_id)->count();
        if($tpl_define_num >0 && !$is_force) {
            return $this->user_error('有选择此模板的课程或者班级，是否把全部选择此模板的课程或者班级设为默认模板',
                self::CODE_HAVE_RELATED_DATA);
        }

        try {
            $this->startTrans();

            $first_review_setting = $this->order('rts_id desc')->limit(1)->field('rts_id')->find();
            $new_rts_id = !empty($first_review_setting) ? $first_review_setting['rts_id'] : 0;

            $rs = (new Review())->where('rts_id', $rts_id)->update(['rts_id' => $new_rts_id]);
            if ($rs === false) throw new FailResult('点评模板重置失败');

            $rs = (new ReviewTplDefine())->where('rts_id', $rts_id)->update(['rts_id' => $new_rts_id]);
            if ($rs === false) throw new FailResult('解除模板失败');

            $rs = $tpl_setting->delete();
            if ($rs === false) throw new FailResult('删除模板失败');

            $this->commit();
        } catch (FailResult $e) {
            $this->rollback();
            return $this->user_error($e->getMessage());
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }
    
}