<?php

namespace app\api\model;

class ReportClassByRoom extends Base
{   
	protected function makeReportOfDay($int_day,$cr_id,$og_id = 0)
	{ 
		// 时间信息
 		$date_info = getdate(strtotime($int_day));
 		$data = [];
 		$data['og_id'] = $og_id;
        $crinfo = get_classroom_info($cr_id);
 		$data['bid'] = $crinfo['bid'];
 		$data['cr_id'] = $cr_id;
 		$data['year'] = $date_info['year'];
 		$data['month'] = $date_info['mon'];
 		$data['week'] = $date_info['wday'];
 		$data['day'] = $date_info['yday'];
 		$data['int_day'] = $int_day;
        
        $w_cid['int_day'] = $int_day;
        $w_cid['cr_id'] = $cr_id;
 		$cids = CourseArrange::where($w_cid)->column('cid');
 		$data['cids'] = implode(',',$cids);

 		$ca_ids  = CourseArrange::where($w_cid)->column('ca_id');
 		$data['ca_ids'] = implode(',',$ca_ids);
 		
 		$base_w['int_day'] = ['=', $int_day];
        $base_w['cr_id'] = $cr_id;
        $base_w['bid'] = $crinfo['bid'];
        $fields = $this->getTableFields();
        foreach ($fields as $field) {
            $field_method = 'calc_' . $field;
            if (method_exists($this, $field_method)) {
                $data[$field] = $this->$field_method($base_w);
            }
        }
 	
 		$model = new ReportClassByRoom;
 		$w['og_id'] = $og_id;
 		$w['bid'] = $crinfo['bid'];
 		$w['cr_id'] = $cr_id;
 		$w['int_day'] = $int_day;

 		$exist_data = $model->where($w)->find();
 		if($exist_data){
 			$w_ex = [];
 			$w_ex['id'] = $exist_data['id'];
 			$model->save($data,$w_ex);
 		}else{
 			$model->isUpdate(false)->save($data);
 		}
	}

	protected function calc_arrange_nums($w)
    {
        return CourseArrange::where($w)->count();
    }

	public static function getTimeCondition($int_start_day, $int_end_day = null)
    {
        $condition = [];
        $condition[] = strtotime($int_start_day);
        if (empty($int_end_day)) {
            $condition[] = strtotime("+1 day",strtotime($int_start_day)) - 1;
        } else {
            $temp = strtotime($int_end_day);
            $condition[] = mktime(23,59,59, date('m', $temp), date('d', $temp), date('Y', $temp));
        }
        return $condition;
    }

    public static function getSumFields()
    {
        $class = new \ReflectionClass(new self());
        $methods = $class->getMethods();
        $fields = [];
        foreach ($methods as $method) {
            if (strpos($method->name, 'calc_') !== false) {
                $fields[] = substr($method->name, 5);
            }
        }
        foreach ($fields as $key => $field) {
            $fields["sum({$field})"] = "sum_{$field}";
            unset($fields[$key]);
        }
        return $fields;
    }


	public static function buildReport($input)
	{
		// print_r($input);exit;

		$og_id  = gvar('og_id');
		$int_start = date('Ymd',strtotime($input['start_date']));
		$int_end = date('Ymd',strtotime($input['end_date']));
		$cr_ids = $input['cr_id'];
		$model = new self();
        

		try{
            for(; $int_start <= $int_end;){

            	foreach ($cr_ids as $cr_id) {
            		$model->makeReportOfDay($int_start,$cr_id,$og_id);
            	}

                $int_start = date("Ymd", strtotime("+1 day", strtotime($int_start)));
            }
		}catch(\Exception $exception){
			return $exception->getMessage();
		}

		return true;
	}





}