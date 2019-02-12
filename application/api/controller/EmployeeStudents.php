<?php
/**
 * Author: luo
 * Time: 2018/3/26 16:42
 */

namespace app\api\controller;


use app\api\model\ClassStudent;
use app\api\model\EmployeeStudent;
use app\api\model\Student;
use app\common\db\Query;
use think\Request;

class EmployeeStudents extends Base
{

    public function get_list(Request $request)
    {
        $get = $request->get();
        if(!isset($get['rid'])){
            $get['rid'] = EmployeeStudent::EMPLOYEE_SM;
        }
        if(!empty($get['cid'])) {
            $sids = (new ClassStudent())->where('cid', $get['cid'])->where('status', ClassStudent::STATUS_NORMAL)
                ->column('sid');
            $sids = $sids ?: [-1];
            $m_student = new Student();
            $ret = $m_student->where('sid', 'in', $sids)->getSearchResult();

        } else {
            /** @var Query $m_es */
            $sql = '';
            if(isset($get['eid'])){

                $sql = ' and es.eid = '.$get['eid'];
                unset($get['eid']);
            }
            if(isset($get['sid'])){
                $sql .= ' and es.sid = '.$get['sid'];
                unset($get['sid']);
            }
            $mEmployeeStudent = new EmployeeStudent();
            $ret = $mEmployeeStudent
                ->field('es.*')
                ->alias('es')
                ->join('student s','es.sid = s.sid','left')
                ->where('es.sid <> 0 and s.sid IS NOT NULL and s.is_delete = 0'.$sql)
                ->with('student')
                ->getSearchResult($get);
        }

        return $this->sendSuccess($ret);
    }

    /**
     * @desc  老师添加学生
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function post(Request $request)
    {
        $post = $request->post();
        $m_es = new EmployeeStudent();
        $rs = $m_es->assignMultiStudentsToEmployee($post);

        if($rs === false) return $this->sendError(400, $m_es->getErrorMsg());
        
        return $this->sendSuccess();
    }

    /**
     * @desc  学生更换学管师
     * @author luo
     * @method POST
     */
    public function change_employee(Request $request)
    {
        $post = $request->post();
        if(empty($post['sids'])){
            return $this->sendError(400, '请选择需要调整的学员');
        }
        if(empty($post['eid'])){
            return $this->sendError(400, '请选择学管师');
        }
        $m_es = new EmployeeStudent();
        $rs = $m_es->changeEmployee($post);
        
        if($rs === false) return $this->sendError(400, $m_es->getErrorMsg());

        return $this->sendSuccess();
    }

  
    
    /**
     * 批量取消学员分配的学管师
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function delete_students(Request $request)
    {
        $post = $request->post();
        if(empty($post['sids'])) return $this->sendError(400, '请选择要取消分配的学员');
        $mEmployeeStudent = new EmployeeStudent;
        $ret = $mEmployeeStudent->delStudents($post['sids']);
        if($ret === false){
            return $this->sendError(400,$mEmployeeStudent->getErrorMsg());
        }

        return $this->sendSuccess();
    }

}