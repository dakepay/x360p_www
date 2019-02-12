<?php
/** 
 * Author: luo
 * Time: 2017-10-17 09:47
**/

namespace app\api\controller;

use app\api\model\Department;
use app\api\model\Employee;
use app\api\model\EmployeeDept;
use think\Db;
use think\Request;
use app\api\model\Department as DepartmentModel;

class Departments extends Base
{

    public function post(Request $request)
    {
        $input = $request->post();
        $department_model = new DepartmentModel();
        $rs = $department_model->createDepartment($input);
        if(!$rs) return $this->sendError(400, $department_model->getError());

        return $this->sendSuccess();
    }

    public function get_list(Request $request) {
        $params = user_config('params');

        $dept_tree_root = [
            'dpt_id'=>0,
            'pid'=>0,
            'dpt_type'=>0,
            'dpt_name'=>$params['org_name'],
            'bid'=>0
        ];
        $w_og['og_id'] = gvar('og_id');
        $dept_list = collection(model('department')->where($w_og)->select())->toArray();
        $dept_tree_body = list_to_tree($dept_list,'dpt_id','pid','children',0);
        $dept_tree_root['children'] = $dept_tree_body;
        return $this->sendSuccess(['list'=>[$dept_tree_root]]);
    }

    public function put(Request $request)
    {
        $dpt_id = $request->param('id');
        $input = $request->put();

        $department_model = new DepartmentModel();

        $department_data = DepartmentModel::get(['dpt_id' => $dpt_id])->toArray();
        $department_data = array_merge($department_data, $input);

        $rs = $department_model->editDepartment($department_data);
        if(!$rs) return $this->sendError(400, $department_model->getError());

        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $dpt_id = $request->param('id');

        $department_model = new DepartmentModel();
        $rs = $department_model->deleteDepartment($dpt_id);

        if(!$rs) return $this->sendError(400, $department_model->getError());

        return $this->sendSuccess();
    }

    public function get_list_employees(Request $request)
    {
        $dpt_id = input('id/d');
        if($dpt_id <= 0) return $this->sendError(400, '部门错误');
        $dpt_info = get_department_info($dpt_id);
        if($dpt_info['dpt_type'] > 0 ){
            //如果是分公司
            if($dpt_info['dpt_type'] == 2) {
                $bids = get_bids_by_dpt_id($dpt_id);
                $where['bid'] = ['IN', $bids];
                $where['og_id'] = gvar('og_id');
            }else{
                $bid = $dpt_info['bid'];
                $where[]= ['exp', "find_in_set({$bid},bids)"];
                $where['og_id'] = gvar('og_id');
            }
            $mEmployee = new Employee();
            $with = ['departments','user'];
            $result = $mEmployee->where($where)->with($with)->getSearchResult([],true);

            return $this->sendSuccess($result);

        }

        $m_department = new Department();
        $sub_dpt_ids = $m_department->where('pid', $dpt_id)->column('dpt_id');
        $sub_dpt_ids[] = $dpt_id;
        $rid = input('rid');

        $ret = [];
        $ret['page'] = input('page', 1);
        $ret['pagesize'] = input('pagesize', config('default_pagesize'));

        if(!empty($rid)) {
            $input['dpt_id'] = ['in', $sub_dpt_ids];
            $input['er.rid'] = $rid;
            $m_ed = new EmployeeDept();
            $list = $m_ed->alias('ed')->join('employee_role er', 'ed.eid = er.eid')
                ->where($input)->order('ed.eid', 'desc')->group('ed.eid')
                ->page($ret['page'], $ret['pagesize'])->field('ed.*')->select();

            $ret['total'] = $m_ed->alias('ed')->join('employee_role er', 'ed.eid = er.eid')
                ->where($input)->order('ed.eid', 'desc')->group('ed.eid')
                ->count();

        } else {
            $input['dpt_id'] = ['in', $sub_dpt_ids];
            $input['og_id'] = gvar('og_id');
            $m_ed = new EmployeeDept();
            $list = Db::name('employee_dept')->where($input)->order('eid', 'desc')->field('distinct eid')
                ->page($ret['page'], $ret['pagesize'])->select();

            $ret['total'] = $m_ed->where($input)->order('eid', 'desc')->field('distinct eid')
                ->page($ret['page'], $ret['pagesize'])->count();
        }

        $ret['list'] = [];
        foreach($list  as $row) {
            $employee = Employee::get(['eid' => $row['eid'], 'og_id' => gvar('og_id')], ['departments', 'user']);
            if(empty($employee)) continue;
            $employee = $employee->toArray();

            if(!empty($employee['departments'])) {
                $employee['departments'] = array_map(function($dept){
                    return array_merge($dept, $dept['pivot']);
                }, $employee['departments']);
            }

            $ret['list'][] = $employee;
        }

        return $this->sendSuccess($ret);
    }

}