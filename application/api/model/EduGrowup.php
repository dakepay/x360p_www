<?php
/**
 * Author: luo
 * Time: 2018/6/15 11:12
 */

namespace app\api\model;


use app\common\exception\FailResult;
use think\Exception;

class EduGrowup extends Base
{
    public function setAbilityIdsAttr($value)
    {
        return !empty($value) && is_array($value) ? implode(',', $value) : $value;
    }

    public function getAbilityIdsAttr($value)
    {
        return !empty($value) && is_string($value) ? explode(',', $value) : [];
    }

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,student_name,photo_url');
    }

    public function oneClass()
    {
        return $this->hasOne('Classes', 'cid', 'cid')->field('cid,class_name');
    }

    public function eduGrowupItem()
    {
        return $this->hasMany('EduGrowupItem', 'eg_id', 'eg_id');
    }

    public function eduGrowupPic()
    {
        return $this->hasMany('EduGrowupPic', 'eg_id', 'eg_id');
    }

    public function addGroupup($post)
    {
        if(empty($post['sid'])) return $this->user_error('缺少sid');

        try {
            $this->startTrans();
            $rs = $this->allowField(true)->isUpdate(false)->save($post);
            if($rs === false) throw new FailResult($this->getErrorMsg());

            $eg_id = $this->eg_id;

            if(!empty($post['edu_growup_item']) && is_array($post['edu_growup_item'])) {
                $m_egi = new EduGrowupItem();
                foreach($post['edu_growup_item'] as $row) {
                    $row['eg_id'] = $eg_id;
                    $row['sid'] = $post['sid'];
                    $row['cid'] = $post['cid'] ?? 0;
                    $rs = $m_egi->addEduGrowupItem($row);
                    if($rs === false) throw new FailResult($m_egi->getErrorMsg());
                }
            }
            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    public function updateEduGrowup($put)
    {
        if(empty($this->getData())) return $this->user_error('模型数据为空');

        $edu_growup_item = !empty($put['edu_growup_item']) ? $put['edu_growup_item'] : [];

        $m_egi = new EduGrowupItem();
        $old_egi_ids = $m_egi->where('eg_id', $this->eg_id)->column('egi_id');
        $new_egi_ids = !empty($edu_growup_item) ? array_column($edu_growup_item, 'egi_id') : [];
        $del_egi_ids = array_diff($old_egi_ids, $new_egi_ids);

        try {
            $this->startTrans();

            $rs = $this->allowField(true)->isUpdate(true)->save($put);
            if($rs === false) throw new FailResult($this->getErrorMsg());
            

            if(!empty($del_egi_ids)) {
                $rs = $m_egi->where('egi_id', 'in', $del_egi_ids)->delete();
                if($rs === false) throw new FailResult($m_egi->getErrorMsg());

                $m_egp = new EduGrowupPic();
                $rs = $m_egp->where('egi_id', 'in', $del_egi_ids)->delete();
                if($rs === false) throw new FailResult($m_egp->getErrorMsg());
            }

            foreach($edu_growup_item as $item) {
                if(!empty($item['egi_id'])) {
                    $rs = $m_egi->updateEduGrowupItem($item);
                    if($rs === false) throw new FailResult($m_egi->getErrorMsg());
                } else {
                    unset($item['egi_id']);
                    $item['sid'] = $this->sid;
                    $item['eg_id'] = $this->eg_id;
                    $rs = $m_egi->addEduGrowupItem($item);
                    if($rs === false) throw new FailResult($m_egi->getErrorMsg());
                }
            }

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

    }

    public function delEduGrowup()
    {
        if(empty($this->getData())) return $this->user_error('模型数据错误');

        try {
            $this->startTrans();
            $rs = $this->eduGrowupItem()->delete();
            if($rs === false) throw new FailResult($this->getErrorMsg());

            $rs = $this->eduGrowupPic()->delete();
            if($rs === false) throw new FailResult($this->getErrorMsg());

            $rs = $this->delete();
            if($rs === false) throw new FailResult($this->getErrorMsg());
            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }


}