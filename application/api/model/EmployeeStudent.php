<?php
/** 
 * Author: luo
 * Time: 2018-01-12 14:56
**/

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;

class EmployeeStudent extends Base
{
    const EMPLOYEE_TEACHER   = 1;    # 老师
    const EMPLOYEE_TA   = 2;    # 助教
    const EMPLOYEE_SM  = 4;    # 学管师/班主任
    const EMPLOYEE_CC = 7;    # 咨询师

    const TYPE_CLASS = 1; #班级学员与员工
    const TYPE_ONE   = 2; #一对一学员与员工
    const TYPE_MANY  = 3; #一对多学员与员工

    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')
            ->field('sid,student_name,sex,first_tel,photo_url,status,birth_time,birth_year,birth_month,birth_day');
    }


    /**
     * 自动创建一条学员与员工关系的记录
     * @param $data
     * @param int $type
     * @param int $lid
     * @param int $cid
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function addEmployeeStudentRelationship($data,$type = 0,$lid = 0,$cid = 0)
    {

        $data['type']  = $type;
        $data['lid'] = $lid;
        $data['cid'] = $cid;

        $where = array(
            'sid'   => $data['sid'],
            'eid'   => $data['eid'],
            'rid'   => $data['rid'],
            'type'  => $data['type'],
            'lid'   => $data['lid'],
            'cid'   => $data['cid']
        );
        $model = new self();
        $old = $model->where($where)->find();

        if(!empty($old)) {
            $rs = $model->data([])->isUpdate(true)->allowField(true)->save($data,$where);
            if($rs === false) return false;
        } else {
            $rs = $model->data([])->isUpdate(false)->allowField(true)->save($data);
            if($rs === false) return false;
        }

        return true;
    }

    /**
     * 解除学员与员工关系
     * @param $data
     * @param int $type
     * @param int $lid
     * @param int $cid
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function deleteEmployeeStudentRelationship($data,$type = 0,$lid = 0,$cid = 0)
    {
        $where = array(
            'sid'  => $data['sid'],
            'rid'  => $data['rid'],
            'eid'  => $data['eid'],
            'type' => $type,
            'lid'  => $lid,
            'cid'  => $cid
        );
        $model = new self();
        $employee_student = $model->where($where)->find();
        if(!empty($employee_student)){
            $ret = $employee_student->delete(true);
            if($ret === false){
                return false;
            }
        }
        
        return true;
    }


    /**
     * 创建一个学管师与学员之间的关系记录
     * @param $data
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addOneEmployeeStudent($data)
    {
        if(!isset($data['eid']) || !isset($data['sid'])) return $this->user_error('Param error');

        $employee_student = $this->where(['sid' => $data['sid'], 'rid' => $data['rid']])->find();
        if (!empty($employee_student)){
            $student_name = get_student_name($employee_student['sid']);
            $employee_name = get_employee_name($employee_student['eid']);
            return $this->user_error('学员 '. $student_name .' 已分配学管师 '.$employee_name);
        }

        $w['sid']  = $data['sid'];
        $w['eid']  = $data['eid'];
        $w['rid']  = $data['rid'];
        $old = $this->where($w)->find();

        if(!empty($old)) {
            $result = $this->data([])->isUpdate(true)->allowField(true)->save($data,$w);
            if(false === $result) {
                return $this->sql_save_error('employee_student');
            }

        } else {
            $result = $this->data([])->isUpdate(false)->allowField(true)->save($data);
            if(false === $result){
                return $this->sql_add_error('employee_student');
            }
        }

        $mStudent = new Student();
        $result =  $mStudent->where('sid', $data['sid'])->update(['eid' => $data['eid']]);
        if (false === $result){
            return $this->sql_save_error('student');
        }

        return true;
    }

    /**
     * 批量建立学员与学管师之间的关系
     * @param  [type] $post [description]
     * @return [type]       [description]
     */
    public function assignMultiStudentsToEmployee($post)
    {
        if(empty($post['eid'])) return $this->user_error('eid error');
        if(empty($post['sids']) || !is_array($post['sids'])) return $this->user_error('sids error');
        
        $this->startTrans();
        try {
            foreach($post['sids'] as $sid) {
                $data = [
                    'eid' => $post['eid'],
                    'sid' => $sid,
                    'rid' => EmployeeStudent::EMPLOYEE_SM
                ];
                $result = $this->addOneEmployeeStudent($data);
                if(false === $result){
                    $this->rollback();
                    return false;
                }
                // 添加一条学员分配班主任操作日志
                StudentLog::addAssignTeacherLog($data);
            }
        } catch(\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

    /**
     * 解除学员与学管师的关系
     * @param  array  $sids [description]
     * @return [type]       [description]
     */
    public function delStudents(array $sids)
    {
        $rid = EmployeeStudent::EMPLOYEE_SM;
        $this->startTrans();
        try {
            $model = new self();
            foreach ($sids as $sid) {
                $w['sid'] = $sid;
                $w['rid'] = $rid;
                $rs = $model->where($w)->delete();
                if($rs === false){
                    $this->rollback();
                    return $this->sql_delete_error('employee_student');
                }
            }
            (new Student())->where('sid', 'in', $sids)->update(['eid' => 0]);
            
        } catch(\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }
    
    /**
     * 批量调整学管师
     * @param  [type] $post [description]
     * @return [type]       [description]
     */
    public function changeEmployee($post)
    {
        $employee = Employee::get($post['eid']);
        if(empty($employee)) return $this->user_error('员工不存在');

        $rid = EmployeeStudent::EMPLOYEE_SM;
        $this->startTrans();
        try {

            $rs = $this->where('sid', 'in', $post['sids'])->where('rid', $rid)->update(['eid' => $post['eid']]);
            if($rs === false) return false;
            (new Student())->where('sid', 'in', $post['sids'])->update(['eid' => $post['eid']]);
        } catch(\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();

        return true;
    }

    /**
     * 删除学员所有关系
     * @param $sid
     */
    public function delAllStudentRelation($sid)
    {
        $this->startTrans();
        try {
            $w['sid'] = $sid;
            $rs = $this->where($w)->delete();
            if(false === $rs){
                $this->rollback();
                return $this->sql_delete_error('employee_student');
            }
        } catch(\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

}