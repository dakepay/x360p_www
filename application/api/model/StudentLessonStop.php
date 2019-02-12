<?php

namespace app\api\model;

use think\Exception;

class StudentLessonStop extends Base
{
	/**
	 * 更新今天的学生及课程状态
	 * @return [type] [description]
	 */
   public static function updateTodayStudentLessonStatus($og_id = 0)
   {
   		$now_int_day = intval(date('Ymd',time()));

   		$w_sls['og_id'] 		   = $og_id;
   		$w_sls['stop_int_day'] 	= ['ELT',$now_int_day];
   		$w_sls['stop_time']     = 0;


   		$sls_list = self::all($w_sls);

   		if($sls_list){
   			foreach($sls_list as $sls){
            
	   			$sls->updateStudentLessonStatus();
	   		}
   		}

   	
   		$w_sls = [];
   		$w_sls['og_id'] = $og_id;
   		$w_sls['recover_int_day'] = $now_int_day;
   		$w_sls['expired_time'] = 0;

   		$sls_list = self::all($w_sls);

   		if($sls_list){
   			foreach($sls_list as $sls){
   				$sls->recoverStudentLessonStatus();
   			}
   		}


   		return true;
   }

   public function updateStudentLessonStatus()
   {
   		if($this->stop_type == 1){
   			//休学
   			$w['sid'] = $this->sid;
   			$student = Student::get($w);
   			if($student->status != Student::STATUS_SUSPEND){
   				$student->status = Student::STATUS_SUSPEND;
   				$student->save();
   			}
   		}else{
   			if($this->cid > 0){
   				$w_cs['cid'] = $this->cid;
   				$w_cs['sid'] = $this->sid;
   				$w_cs['status'] = 1;

   				$cs = ClassStudent::get($w_cs);

   				if($cs){
   					$cs->status = 0;	//停课
   					$cs->save();
   				}
   			}

   			$w_sl['sid'] = $this->sid;
            $w_sl['lid'] = $this->lid;
            $w_sl['lesson_status'] = ['LT',2];
            $w_sl['is_stop'] = 1;
   			$sl = StudentLesson::get($w_sl);

   			if($sl){
   				$sl->is_stop = 1;
   				$sl->save();
   			}
   		}
   }

   /**
    * 恢复学员及课程状态
    * @return [type] [description]
    */
   public function recoverStudentLessonStatus()
   {
   		if($this->stop_type == 1){
   			//休学
   			$w['sid'] = $this->sid;
   			$student = Student::get($w);
   			if($student->status != Student::STATUS_NORMAL){
   				$student->status = Student::STATUS_NORMAL;
   				$student->save();
   			}
   		}else{
   			if($this->cid > 0){
   				$w_cs['cid'] = $this->cid;
   				$w_cs['sid'] = $this->sid;
   				$w_cs['status'] = 0;

   				$cs = ClassStudent::get($w_cs);

   				if($cs){
   					$cs->status = 1;	//停课
   					$cs->save();
   				}
   			}

   			$w_sl['lid'] = $this->lid;
   			$w_sl['sid'] = $this->sid;
   			$sl = StudentLesson::get($w_sl);

   			if($sl){
   				$sl->is_stop = 0;
   				$sl->save();
   			}
   		}
   }



}