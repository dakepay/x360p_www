<?php
/**
 * Author: luo
 * Time: 2018/7/7 11:22
 */

namespace app\api\model;


use app\common\exception\FailResult;
use think\Exception;

class EventSignUp extends Base
{

    protected $type = [
        'attend_time' => 'timestamp'
    ];

    public function setAttendTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,student_name,photo_url,first_tel');
    }

    public function oneEvent()
    {
        return $this->hasOne('Event', 'event_id', 'event_id');
    }

    public function changeToMarketClue()
    {
        if(empty($this->getData())) return $this->user_error('模型数据为空');

        try {
            $this->startTrans();
            array_copy($data, $this->getData(), ['bid','name','tel']);
            $m_mc = new MarketClue();
            $mcl_id = $m_mc->addClue($data);
            if($mcl_id === false) return $this->user_error($m_mc->getErrorMsg());

            $this->mcl_id = $mcl_id;
            $rs = $this->allowField('mcl_id')->save();
            if($rs === false) throw new FailResult($this->getErrorMsg());

            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }


}