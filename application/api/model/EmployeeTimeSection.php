<?php
namespace app\api\model;


class EmployeeTimeSection extends Base
{

    public function setIntDayAttr($value)
    {
        return format_int_day($value);
    }

    public function setIntStartHourAttr($value)
    {
        return format_int_hour($value);
    }

    public function setIntEndHourAttr($value)
    {
        return format_int_hour($value);
    }

    public function getIntStartHourAttr($value)
    {
        return int_hour_to_hour_str($value);
    }


    public function getIntEndHourAttr($value)
    {
        return int_hour_to_hour_str($value);
    }


    /**
     * 批量添加
     * @param $data
     * @return bool
     */
    public function batchTimeSections(&$data)
    {
        $this->startTrans();
        try{
            foreach ($data as $time_section){
                $result = $this->addTimeSection($time_section);
                if (false === $result) return $this->user_error($this->getError());
            }
        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

    /**
     * 添加教师时间段
     * @param $bid
     * @param $eid
     * @param $time
     * @return bool
     */
    public function addTimeSection($data)
    {

        $employee_info = get_employee_info($data['eid']);
        if (empty($employee_info)) return $this->user_error('employee not exists');

        $w['int_day']       = intval($data['int_day']);
        $w['int_start_hour'] = format_int_hour($data['int_start_hour']);
        $w['int_end_hour']   = format_int_hour($data['int_end_hour']);
        $w['eid']       = intval($data['eid']);
        $w['bid']       = intval($data['bid']);

        $exists_ts = $this->where($w)->find();
        if($exists_ts){
            $this->user_error(sprintf('时间段%s ~ %s已经存在!',$data['int_start_hour'],$data['int_end_hour']));
            return false;
        }

        $this->startTrans();
        try{
            $rs = $this->isUpdate(false)->save($data);
            if ($rs === false) return $this->user_error('employee_time_section');
        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }


    public function updateTimeSections($ets_id,$data){
        $need_fields = ['eid','week_day','int_start_hour','int_end_hour'];
        if (!$this->checkInputParam($data,$need_fields)){
            return $this->input_param_error('params');
        }

        $m_Ets = $this->find($ets_id);
        if (!$m_Ets) return $this->input_param_error('ets_id');

        $w['int_start_hour'] = format_int_hour($data['int_start_hour']);
        $w['int_end_hour']   = format_int_hour($data['int_end_hour']);
        $w['week_day']       = intval($data['week_day']);
        $w['eid']       = intval($data['eid']);
        $w['bid']       = intval($data['bid']);

        $exists_ts = $this->where($w)->find();
        if($exists_ts){
            $this->user_error(sprintf('时间段%s ~ %s已经存在!',$data['int_start_hour'],$data['int_end_hour']));
            return false;
        }

        $w_update['ets_id'] = $ets_id;
        $this->startTrans();
        try{

            $rs = $this->isUpdate(false)->save($data,$w_update);
            if ($rs === false) return $this->sql_save_error('employee_time_section');
        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }


    /**
     * 批量删除
     * @param $ets_ids
     * @return bool
     */
    public function batchDelete($ets_ids){

        if (empty($ets_ids) || !is_array($ets_ids)) return $this->input_param_error('ets_ids');

        foreach ($ets_ids as $ets_id){

            $result = $this->delTimeSection($ets_id);
            if (false === $result) return $this->sql_delete_error('employee_time_section');
        }

        return true;
    }

    /**
     * 删除
     * @param EmployeeTimeSection $data
     * @return bool
     */
    public function delTimeSection($ets_id){
        $m_Ets = $this->find(['ets_id'=>$ets_id]);
        if (empty($m_Ets)) return $this->user_error('时段不存在');

        $this->startTrans();
        try{
            $rs = $m_Ets->delete();
            if (false === $rs) return $this->sql_delete_error('employee_time_section');
        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

}