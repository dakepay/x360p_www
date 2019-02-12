<?php
/**
 * Author: luo
 * Time: 2018/6/15 12:18
 */

namespace app\api\model;


use app\common\exception\FailResult;
use think\Exception;

class EduGrowupItem extends Base
{
    public function eduGrowupPic()
    {
        return $this->hasMany('EduGrowupPic', 'egi_id', 'egi_id');
    }

    public function addEduGrowupItem($data)
    {
        if(empty($data['sid']) || empty($data['eg_id'])) return $this->user_error('sid or eg_id error');

        try {
            $this->startTrans();
            $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
            if($rs === false) return false;

            $egi_id = $this->egi_id;

            if(!empty($data['edu_growup_pic']) && is_array($data['edu_growup_pic'])) {
                $m_egp = new EduGrowupPic();
                foreach($data['edu_growup_pic'] as $row) {
                    $row['egi_id'] = $egi_id;
                    $row['eg_id'] = $data['eg_id'];
                    $rs = $m_egp->addEduGrowupPic($row);
                    if($rs === false) throw new FailResult($m_egp->getErrorMsg());
                }
            }

            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    public function updateEduGrowupItem($data)
    {
        if(empty($data['egi_id'])) return $this->user_error('egi_id error');

        $edu_growup_item = $this->find($data['egi_id']);
        if(empty($edu_growup_item)) return $this->user_error('edu_growup_item 不存在');

        try {
            $this->startTrans();
            $rs = $this->data([])->allowField(true)->isUpdate(true)->save($data);
            if($rs === false) return false;

            $egi_id = $this->egi_id;
            $m_egp = new EduGrowupPic();
            $edu_growup_pic = !empty($data['edu_growup_pic']) ? $data['edu_growup_pic'] : [];
            $old_egp_ids = $m_egp->where('egi_id', $this->egi_id)->column('egp_id');
            $new_egp_ids = !empty($edu_growup_pic) ? array_column($edu_growup_pic, 'egp_id') : [];
            $del_egp_ids = array_diff($old_egp_ids, $new_egp_ids);
            if(!empty($del_egp_ids)) {
                $rs = $m_egp->where('egp_id', 'in', $egi_id)->delete();
                if($rs === false) throw new FailResult($m_egp->getErrorMsg());
            }

            if(!empty($data['edu_growup_pic']) && is_array($data['edu_growup_pic'])) {
                $m_egp = new EduGrowupPic();
                foreach($data['edu_growup_pic'] as $row) {
                    $row['egi_id'] = $egi_id;
                    $row['eg_id'] = $data['eg_id'];
                    $rs = $m_egp->addEduGrowupPic($row);
                    if($rs === false) throw new FailResult($m_egp->getErrorMsg());
                }
            }

            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;


    }

}