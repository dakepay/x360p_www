<?php
/**
 * Author: luo
 * Time: 2018/7/17 9:44
 */

namespace app\api\model;


use app\common\exception\FailResult;
use think\Exception;

class HomeworkPublish extends Base
{
    protected $type = [
        'publish_time' => 'timestamp'
    ];

    public function setPublishTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    public function homeworkComplete()
    {
        return $this->hasOne('HomeworkComplete', 'hc_id', 'hc_id');
    }

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,student_name,photo_url');
    }

    public function oneClass()
    {
        return $this->hasOne('Classes', 'cid', 'cid')->field('cid,class_name');
    }

    public function addHomeworkPublish($post)
    {
        try {
            $this->startTrans();
            $rs = $this->allowField(true)->save($post);
            if($rs === false) return false;

            if(!empty($post['hc_id'])) {
                $m_hc = new HomeworkComplete();
                $rs = $m_hc->where('hc_id', $post['hc_id'])->update(['is_publish' => 1]);
                if($rs === false) throw new FailResult($m_hc->getErrorMsg());
            }

            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

}