<?php

namespace app\api\model;

class ReportServiceBySystem extends Base
{

	protected function getTimeCondition($start)
	{
        $condition = [];
        $condition[] = strtotime($start);
        $condition[] = strtotime("+1 day",strtotime($start)) - 1;
        return $condition;
	}
    /**
     * 统计排课次数
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function calc_arrange_times($w)
	{
		unset($w['create_time']);
		unset($w['eid']);
		return CourseArrange::where($w)->count();
	}
    /**
     * 统计考勤次数
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function calc_attendance_times($w)
	{
		unset($w['create_time']);
		unset($w['teach_eid']);
		return ClassAttendance::where($w)->count();
	}
    
    /**
     * 计算课前提醒次数
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function calc_s1_times($w){
        unset($w['create_time']);
        unset($w['eid']);
        $ca_ids = CourseArrange::where($w)->column('ca_id');
        $sum = 0;
        foreach ($ca_ids as $ca_id) {
        	$exist = CourseRemindLog::where('ca_id',$ca_id)->count();
        	if(!empty($exist)){
        		$sum += 1;
        	}
        }
        return $sum;
	}
    /**
     * 计算课前提醒人数
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function calc_s1_nums($w){
        unset($w['create_time']);
        unset($w['eid']);
        $ca_ids = CourseArrange::where($w)->column('ca_id');
        $sum = 0;
        foreach ($ca_ids as $ca_id) {
        	$num = CourseRemindLog::where('ca_id',$ca_id)->count();
        	$sum += $num;
        }
        return $sum;
	}
    /**
     * 计算备课服务次数
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function calc_s2_times($w){
        unset($w['create_time']);
        unset($w['eid']);
        return CoursePrepare::where($w)->count();
	}
    /**
     * 计算备课服务人数
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function calc_s2_nums($w){
		unset($w['create_time']);
		unset($w['eid']);
		$ca_ids = CoursePrepare::where($w)->column('ca_id');
		$sum = 0;
		foreach ($ca_ids as $ca_id) {
			$w_s['ca_id'] = $ca_id;
			$w_s['sid'] = ['gt',0];
			$num = CourseArrangeStudent::where($w_s)->count();
			$sum += $num;
		}
		return $sum;
	}
    /**
     * 计算课评服务次数
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function calc_s4_times($w){
		unset($w['create_time']);
		unset($w['teach_eid']);
		return Review::where($w)->count();
	}
    /**
     * 计算课评服务人数
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function calc_s4_nums($w){
		unset($w['create_time']);
		unset($w['teach_eid']);
		$rvw_ids = Review::where($w)->column('rvw_id');
		$sum = 0;
		foreach ($rvw_ids as $rvw_id) {
			$num = ReviewStudent::where('rvw_id',$rvw_id)->count();
			$sum += $num;
		}
		return $sum;
	}
    /**
     * 计算作业服务次数
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function calc_s5_times($w){
		unset($w['int_day']);
		unset($w['teach_eid']);
		return HomeworkTask::where($w)->count();
	}

	
    /**
     * 计算作业服务人数
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function calc_s5_nums($w){
        unset($w['int_day']);
		unset($w['teach_eid']);
		$ht_ids = HomeworkTask::where($w)->column('ht_id');
		$sum = 0;
		foreach ($ht_ids as $ht_id) {
			$info = get_homework_task_info($ht_id);
			if($info['cid']==0 && $info['lesson_type']==1){
				$sum +=1;
			}else if($info['cid']==0 && $info['lesson_type'] ==2){
                $sids = explode(',',$info['sids']);
                $num = count($sids);
                $sum += $num;
			}else{
				$cid = $info['cid'];
				$int_day = date('Ymd',$info['create_time']);
				$num = get_class_student_num($int_day,$cid);
				$sum += $num;
			}
		}
		return $sum;
	}
    /**
     * 计算作品服务次数
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function calc_s6_times($w){
		unset($w['int_day']);
		unset($w['teach_eid']);
		return StudentArtwork::where($w)->count();
	}
    /**
     * 计算作品服务人数
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function calc_s6_nums($w){
		unset($w['int_day']);
		unset($w['teach_eid']);
		return StudentArtwork::where($w)->count();
	}
    /**
     * 计算学员回访次数
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function calc_s7_times($w){
		unset($w['create_time']);
		unset($w['teach_eid']);
		return StudentReturnVisit::where($w)->count();
	}
	/**
     * 计算学员回访人数
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function calc_s7_nums($w){
		unset($w['create_time']);
		unset($w['teach_eid']);
		return StudentReturnVisit::where($w)->count();
	}


    /**
     * 获取统计字段
     * @return [type] [description]
     */
    public static function getSumFields()
    {
        $model = new self();
        $class = new \ReflectionClass($model);
        $methods = $class->getMethods();
        $fields = [];
        // 获取统计字段
        foreach ($methods as $method) {
        	if(strpos($method->name,'calc_') !== false){
        		$fields[] = substr($method->name,5);
        	}
        }
        return $fields;
    }

    protected function makeReportOfDay($start,$eid,$bid,$og_id = 0)
    {
        $date_info = getdate(strtotime($start));
        $data = [];
        $data['og_id'] = $og_id;
        $data['bid'] = $bid;
        $data['eid'] = $eid;
        $data['year'] = $date_info['year'];
        $data['month'] = $date_info['mon'];
        $data['week'] = $date_info['wday'];
        $data['day'] = $date_info['yday'];
        $data['int_day'] = $start;
        
        //根据$start $eid $bid 从其他表汇总 
        $base_w['int_day'] = $start;
        // $temp = ['1535454584','1539523566'];
        $temp = $this->getTimeCondition($start);
        $base_w['create_time'] = ['between',$temp];
        $base_w['bid'] = $bid;
        $base_w['teach_eid'] = $eid;
        $base_w['eid'] = $eid;
        $base_w['og_id'] = $og_id;
        // 当前表中所有字段
        $fields = $this->getTableFields();
        foreach ($fields as $field) {
        	$field_method = 'calc_'.$field;
        	if(method_exists($this,$field_method)){
        		$data[$field] = $this->$field_method($base_w);
        	}
        }

        // 新增或更新
        $w_exist['og_id'] = $og_id;
        $w_exist['bid'] = $bid;
        $w_exist['eid'] = $eid;
        $w_exist['int_day'] = $start;
        $model = new self();
        $exist_data = $model->where($w_exist)->find();
        if($exist_data){
        	$where['id'] = $exist_data['id'];
        	$model->allowField(true)->save($data,$where);
        }else{
            // 第二次开始必须使用下面的方式新增
        	$model->isUpdate(false)->save($data);
        }

        return $data;

    }

	public static function buildReport($eids,$input)
	{
		
		// print_r($input);exit;
		$og_id = gvar('og_id');
		$start = date('Ymd',strtotime($input['start_date']));
		$end = date('Ymd',strtotime($input['end_date']));
		$model = new self();
		try{
			for(; $start <= $end;){
				foreach ($eids as $eid) {
					$bids = Employee::where('eid',$eid)->value('bids');
					$bids = explode(',',$bids);
				    foreach ($bids as $bid) {
				    	if($bid==1) continue;
				    	$model->makeReportOfDay($start,$eid,$bid,$og_id);
				    }
				}
				$start = date('Ymd',strtotime('+1 day',strtotime($start)));
			}

		}catch(\Exception $exception){
			return $exception->getMessage();
		}

		return true;
	}
}