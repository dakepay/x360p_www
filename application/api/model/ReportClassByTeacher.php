<?php

namespace app\api\model;

class ReportClassByTeacher extends Base
{
	public static function getSumFields()
	{    
		$model = new self();
		$class = new \ReflectionClass($model);
		$methods = $class->getMethods();
		$fields = [];
		foreach ($methods as $method) {
			if(strpos($method->name,'calc_') !== false){
				$fields[] = substr($method->name,5);
			}
		}
		foreach ($fields as $key => $field) {
			$fields["sum({$field})"] = "sum_{$field}";
			unset($fields[$key]);
		}
		return $fields;
	}

	protected function makeReportOfDay($eid,$cid,$int_start,$og_id = 0)
	{
		$date_info = getdate(strtotime($int_start));

		$cinfo = get_class_info($cid);

		$data = [];
		$data['og_id'] = $og_id;
		$data['teach_eid'] = $eid;
		$data['cid'] = $cid;
		$data['lid'] = $cinfo['lid'];
        
		$einfo = get_employee_info($eid);
		$data['bids'] = $einfo['bids'];

		$data['year'] = $date_info['year'];
		$data['month'] = $date_info['mon'];
		$data['week'] = $date_info['wday'];
		$data['day'] = $date_info['yday'];
		$data['int_day'] = $int_start;

        $b_w['int_day'] = $int_start;
        $b_w['is_cancel'] = 0;
        $b_w['teach_eid'] = $eid;
        $b_w['cid'] = $cid;
        $b_w['lesson_type'] = 0;
        
        $exit = CourseArrange::where($b_w)->select();

        if($exit){
        	$data['ca_ids'] = $this->get_ca_ids($b_w);
        	$data['on_ca_ids'] = $this->get_on_ca_ids($b_w);
        }
        
        $fields = $this->getTableFields();  
        foreach ($fields as $field) {
        	$field_method = 'calc_'.$field;
        	if(method_exists($this,$field_method)){
        		$data[$field] = $this->$field_method($b_w);
        	}
        }

		$model = new ReportClassByTeacher;

		$w_exist['og_id'] = $og_id;
		$w_exist['teach_eid'] = $eid;
		$w_exist['cid'] = $cid;
		$w_exist['int_day'] = $int_start;

		$exist_data = $model->where($w_exist)->find();

		if($exist_data){
			$w_ex = [];
            $w_ex['id'] = $exist_data['id'];
            $model->save($data,$w_ex);
		}else{
			$model->isUpdate(false)->save($data);
		}
		return $data;
	}

	public static function buildReport($input){

		$og_id = gvar('og_id');
		$int_start = date('Ymd',strtotime($input['start_date']));
		$int_end = date('Ymd',strtotime($input['end_date']));
		$eids = $input['eids'];
		$w['int_day'] = ['between',[$int_start,$int_end]];
		$model = new self();

		try{

			for(; $int_start <= $int_end;){
				foreach ($eids as $eid) {
					$w['teach_eid'] = $eid;
					$w['lesson_type'] = 0;
					$cids = CourseArrange::where($w)->column('cid');
					$cids = array_unique($cids);
					foreach ($cids as $cid) {
						if($cid==0)
						continue;
						$model->makeReportOfDay($eid,$cid,$int_start,$og_id);
					}
				}
				$int_start = date('Ymd',strtotime("+1 day",strtotime($int_start)));
			}

		}catch(\Exception $exception){
			return $exception->getMessage();
		}
	}
    
    /**
     * 总排课详情
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function get_ca_ids($w){
		$ca_ids = CourseArrange::where($w)->column('ca_id');
		if($ca_ids){
			return implode(',',$ca_ids);
		}
        return '';
	}
    
    /**
     * 已上课详情
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function get_on_ca_ids($w){
		$w['is_attendance'] = ['in',['1','2']];
		$on_ca_ids = CourseArrange::where($w)->column('ca_id');
		if($on_ca_ids){
			return implode(',',$on_ca_ids);
		}
        return '';
	}

    /**
     * 总排课数量
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
	protected function calc_total_arrange_nums($w)
    {
        return CourseArrange::where($w)->count();
    }

    /**
     * 已上课数量
     * @param  [type] $w [description]
     * @return [type]    [description]
     */
    protected function calc_on_arrange_nums($w)
    {
    	$w['is_attendance'] = ['in',['1','2']];
        return CourseArrange::where($w)->count();
    }





}