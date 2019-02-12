<?php
 namespace app\api\model;


 class Evaluate extends Base
 {

     public function setIntDayAttr($value, $data)
     {
         return format_int_day($value);
     }

     public function setIntStartHourAttr($value, $data)
     {
        return format_int_hour($value);
     }

     public function setIntEndHourAttr($value, $data)
     {
         return format_int_hour($value);
     }

     public function employee()
     {
         return $this->hasOne('Employee','eid','eid');
     }

     public function student()
     {
         return $this->hasOne('Student','sid','sid');
     }

     public function customer()
     {
         return $this->hasOne('Customer','cu_id','cu_id');
     }


     public function createEvaluate($data)
     {
             $rs = $this->isUpdate(false)->allowField(true)->save($data);
             if (!$rs) return $this->sql_add_error('evaluate');

             return true;
     }

 }