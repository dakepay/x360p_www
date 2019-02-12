<?php
/**
 * Author: luo
 * Time: 2017-10-17 09:50
**/

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;

class Department extends Base
{

    public function branch()
    {
        return $this->hasOne('branch', 'bid', 'bid');
    }

    public function employee()
    {
        return $this->belongsToMany('Employee', 'employee_dept', 'eid', 'dpt_id');
    }

    //添加部门，并添加校区信息
    public function createDepartment($data)
    {
        $this->startTrans();
        try {

            //添加部门数据
            unset($data['dpt_id']);
            $rs = $this->data([])->validate(true)->allowField(true)->isUpdate(false)->save($data);
            if (!$rs) throw new FailResult($this->getErrorMsg());

            $department_id = $this->getLastInsID();

            //如果是校区，添加校区数据
            if ($this->dpt_type == 1) {
                $branch_data['branch_name'] = $this->dpt_name;
                $branch_data['short_name'] = $this->dpt_name;
                $branch_data['branch_type'] = 1;
                $branch_data['big_area_id'] = $this->pid;
                $branch_model = new Branch();
                $result = $branch_model->createBranch($branch_data);
                if (!$result) {
                    $this->rollback();
                    return $this->user_error($branch_model->getError());
                }

                $bid = $branch_model->bid;
                //修改部门中的校区id
                $data['bid'] = $bid;
                $data['dpt_id'] = $department_id;
                $result = $this->allowField(true)->save($data);
                if (false === $result) {
                    $this->rollback();
                    return $this->user_error('更新部门中的校区ID失败!');
                }
            }

            $this->commit();
        }catch (Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        return true;
    }

    //更新部门并更新校区信息
    public function editDepartment($data)
    {
        $this->startTrans();
        try {

            if(isset($data['pid']) && $data['pid'] > 0 && $data['dpt_type'] === 1) {
                $tmp_pid = $data['pid'];
                while(true) {
                    if($tmp_pid <= 0) break;
                    $parent_dep = $this->find($tmp_pid);
                    if($parent_dep['dpt_type'] === 1) {
                        return $this->user_error('父级不能是校区');
                    }
                    $tmp_pid = $parent_dep['pid'];
                }
            }

            //更新部门数据
            $rs = $this->allowField('pid,dpt_name')->isUpdate(true)->save($data);
            if($rs === false) exception($this->getError());

            //如果是校区，更新校区数据
            if(isset($this->dpt_name) && $this->dpt_type === 1) {
                $branch_data['branch_name'] = $this->dpt_name;
                $branch_data['bid'] = $this->bid;
                $branch_model = new Branch();
                $rs = $branch_model->editBranch($branch_data);
                if(!$rs) exception($branch_model->getError());

            }
            $this->commit();

        } catch (Exception $e) {
            $this->rollback();
            return $this->user_error($e->getMessage());
        }

        return true;
    }

    //删除部门并删除校区信息
    public function deleteDepartment($dpt_id)
    {
        $department = $this->find(['dpt_id' => $dpt_id]);
        if(empty($department)) return $this->user_error('不存在此部门');

        if(!$this->canDeleteDepartment($department)) return $this->user_error($this->getErrorMsg());

        $this->startTrans();
        try {

            if ($department->delete()) {

                if($department->bid) {
                    $branch = Branch::get(['bid' => $department->bid]);
                    if($branch) {
                        $rs = $branch->delOneBranch($branch);
                        if($rs === false) exception($branch->getErrorMsg());
                    }
                }

            }

            $this->commit();

        } catch(Exception $e) {
            $this->rollback();
            return $this->user_error($e->getMessage());
        }

        return true;
    }

    protected function canDeleteDepartment($department)
    {
        $sub_department = $this->where('pid', $department['dpt_id'])->find();
        if(!empty($sub_department)) return $this->user_error('有下级，不能删除');

        if($department['dpt_type'] === 1 && $department['bid'] > 0) {
            $had_studnet = Student::get(['bid' => $department['bid']]);
            if($had_studnet) return $this->user_error('该校区有学生，不能删除');

            $had_classroom = Classroom::get(['bid' => $department['bid']]);
            if($had_classroom) return $this->user_error('该校区有教室，不能删除');

            $had_payment = OrderPaymentHistory::get(['bid' => $department['bid']]);
            if($had_payment) return $this->user_error('该校区有付款记录，不能删除');

            $where[]= ['exp', "find_in_set({$department['bid']},bids)"];
            $had_teacher = (new Employee())->where($where)->limit(1)->find();
            if($had_teacher) return $this->user_error('有员工分配此校区权限，不能删除');
        };

        return true;
    }


}