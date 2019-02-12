<?php

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;
use think\Request;
use think\Validate;

class StudentSuspend extends Base
{

	protected $hidden = [
        'update_time', 
        'is_delete', 
        'delete_time', 
        'delete_uid'
    ];


    /**
     * 添加一条停课记录
     * @param array   $info    [description]
     * @param Student $student [description]
     */
	public function addStudentSuspendLog(array $info,Student $student){

		$this->startTrans();
		try{

			$sl_id = $info['sl_id'];
			$begin_time = $info['stop_time'];
			$end_time = $info['recover_time'];
			$suspend_reason = trim($info['stop_remark']);
			$sl_info = StudentLesson::where('sl_id',$sl_id)->find();

			$data = [
                'sid'   =>  $student->sid,
                'bid'   =>  request()->bid,
                'lid'   =>  $sl_info->lid,
                'sl_id'  =>  $sl_id,
                'begin_time'  =>  strtotime($begin_time),
                'end_time'  =>  strtotime($end_time),
                'suspend_reason' => $suspend_reason,
			];

            $model = new self();
			$model->data([])->allowField(true)->isUpdate(false)->save($data);

            $this->commit();
		}catch(Exception $e){
            $this->rollback();
            return $this->exception_error($e);
		}

	}

}