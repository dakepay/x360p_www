<?php
namespace app\api\model;

class StudentReferer extends Base
{
    protected $hidden = [];

    public function setRefrrerIntDayAtte($value ,$data)
    {
        return format_int_day($value);
    }

    public function getRefrrerIntDayAtte($value ,$data)
    {
        return int_day_to_date_str($value);
    }

    public function setRefererTeacherEidsAttr($value)
    {
        return is_array($value) ? implode(',', $value) : $value;
    }

    public function getRefererTeacherEidsAttr($value)
    {
        return split_int_array($value);
    }

    public function getSidAttr($value)
    {
        return get_student_info($value);
    }

    public function getRefererSidAttr($value)
    {
        return get_student_info($value);
    }

    /**
     * 创建关系
     * @param $sid
     * @param $referer_sid
     * @param int $charge_eid
     * @return bool
     */
    public function createStudentReferer($sid,$referer_sid,$charge_eid = 0)
    {
        $student = get_student_info($sid);
        $referer_student = get_student_info($referer_sid);
        $student_referer_info = $this->where('sid',$sid)->find();

        if (!empty($student_referer_info)){
            return true;
        }
        if (empty($student)) return $this->user_error('被介绍学员信息不存在');
        if (empty($referer_student)) return $this->user_error('介绍学员信息不存在');

        $referer_teacher_eids = [];
        $teacher_eids = [];
        $mStudentAttendance = new StudentAttendance();
        $StudentAttendance_info = $mStudentAttendance->where('sid',$referer_sid)->select();
        if (!empty($StudentAttendance_info)){
            foreach ($StudentAttendance_info as $row){
                array_push($teacher_eids,$row['eid']);
            }
            $teacher_eids = array_count_values($teacher_eids);
            arsort($teacher_eids);
            if (!empty($teacher_eids)){
                foreach ($teacher_eids as $k => $v){
                    array_push($referer_teacher_eids,$k);
                }
            }
        }

        $data = [
            'sid' => $sid,
            'referer_sid' => $referer_sid,
            'referer_int_day' => int_day(time()),
            'referer_cc_eid' => $charge_eid,
            'referer_teacher_eids' => $referer_teacher_eids,
            'referer_edu_eid' => $referer_student['eid']
        ];
        $result = $this->isUpdate(false)->allowField(true)->save($data);
        if (false === $result) return $this->sql_add_error('student_referer');

        $mStudent = new Student();
        $result = $mStudent->save(['referer_sid'=>$referer_sid],['sid'=>$sid]);
        if (false === $result) return $this->sql_save_error('student');

        return true;
    }

    /**
     * 删除
     * @param int $sr_id
     * @return bool
     */
    public function delStudentReferer($sr_id = 0)
    {
        $student_referer_info = $this->get($sr_id);
        if (empty($student_referer_info)) return $this->user_error('转学员介绍记录不存在');

        $rs = $student_referer_info->delete();
        if ($rs === false) return $this->sql_delete_error('student_referer');

        return true;
    }

    /**
     * 修改
     * @param $sr_id
     * @param $referer_teacher_eids
     * @param $referer_edu_eid
     * @param $referer_cc_eid
     */
    public function updateReferer($sr_id,$referer_teacher_eids,$referer_edu_eid,$referer_cc_eid){
        $student_referer = $this->get($sr_id);
        if (!$student_referer){
            return $this->user_error('转介绍学员信息不存在');
        }

        $w['sr_id'] = $sr_id;
        $update = [
            'referer_teacher_eids' => $referer_teacher_eids,
            'referer_edu_eid' => $referer_edu_eid,
            'referer_cc_eid' => $referer_cc_eid,
        ];
        $result = $this->allowField(true)->save($update,$w);
        if (false === $result) return $this->sql_save_error('student_referer');

        return true;
    }
}