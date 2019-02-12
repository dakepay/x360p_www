<?php

namespace app\admapi\controller;

use think\Request;
use app\admapi\model\EmployeePerformance;

class Dashboard extends Base
{

    /**
     * @desc  业绩排行
     * @method POST
     *
     */
    public function ranking(Request $request){

        $input = $request->get('type');
        $mPerformance = new EmployeePerformance();
        $data['list'] = [];
        if ($input == 'month'){
            $create_time = time() - 30 * 86400;
            $sql = 'select eid,sum(amount) as total from pro_employee_performance where create_time > '.$create_time.' group by eid  order by total desc';
            $data['list'] = $mPerformance->query($sql);
        }elseif($input == 'year'){
            $create_time = time() - 30 * 12 * 86400;
            $sql = 'select eid,sum(amount) as total from pro_employee_performance where create_time > '.$create_time.' group by eid  order by total desc';
            $data['list'] = $mPerformance->query($sql);
        }
        if ($data['list']){
            foreach ($data['list'] as $k => $v){
                $data['list'][$k]['name'] = get_employee_name_center($v['eid']);
            }
        }else{
            return $this->sendSuccess($data);
        }
        return $this->sendSuccess($data);
    }



}