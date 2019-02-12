<?php
/** 
 * Author: luo
 * Time: 2017-10-14 10:49
**/

namespace app\sapi\model;

use app\common\exception\FailResult;
use Overtrue\Pinyin\Pinyin;
use think\Exception;

class Student extends Base
{
    static public $ERR = '';

    public $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public $type = [
        'last_attendance_time' => 'timestamp',
    ];

    protected $append = ['school_id_text'];

    protected $auto = ['pinyin', 'pinyin_abbr'];

    protected function setPinyinAttr($value, $data)
    {
        if (!empty($data['student_name'])) {
            $temp = (new Pinyin())->name($data['student_name']);
            return join('', $temp);
        }
        return '';
    }

    protected function setPinyinAbbrAttr($value, $data)
    {
        if (!empty($data['student_name'])) {
            $temp = (new Pinyin())->abbr($data['student_name']);
            return $temp;
        }
        return '';
    }

    protected function setBirthTimeAttr($value)
    {
        $time = !is_numeric($value) ? strtotime($value) : $value;
        $time = $time > 0 ? $time : 0;
        return $time;
    }

    protected function getBirthTimeAttr($value)
    {
        return $value !== 0 ? date('Y-m-d', $value) : $value;
    }

    public function setBirthYearAttr($value, $data)
    {
        if(isset($data['birth_time']) && $data['birth_time']){
            return date('Y', strtotime($data['birth_time']));
        }
        return 0;
    }

    public function setBirthMonthAttr($value, $data)
    {
        if(isset($data['birth_time']) && $data['birth_time']){
            return date('n', strtotime($data['birth_time']));
        }
        return 0;
    }

    public function setBirthDayAttr($value, $data)
    {
        if(isset($data['birth_time']) && $data['birth_time']){
            return date('j', strtotime($data['birth_time']));
        }
        return 0;

    }

    public function setSchoolClassAttr($value)
    {
        return !empty($value) ? $value : 0;
    }

    public function setSchoolGradeAttr($value)
    {
        return !empty($value) ? $value : 0;
    }

    public function setSchoolIdAttr($value)
    {
        return !empty($value) ? $value : 0;
    }

    public function getFirstFamilyRelationTextAttr($value)
    {
        $map = ['未设置', '自己', '爸爸', '妈妈', '其他'];
        return $map[$value];
    }

    public function getSchoolIdTextAttr($value, $data)
    {
        if(!isset($data['school_id'])) return '';

        $school = (new PublicSchool())->where('ps_id', $data['school_id'])->field('school_name')->find();
        if(empty($school)) return '';

        return $school->school_name;
    }

    public function orders()
    {
        return $this->hasMany('Order', 'sid', 'sid');
    }

    public function classes()
    {
        return $this->belongsToMany('Classes', 'class_student', 'cid', 'sid');
    }

    public function user()
    {
        return $this->belongsToMany('User', 'user_student', 'uid', 'sid');
    }

    public function firstUser()
    {
        return $this->belongsTo('User', 'first_uid', 'uid')->field(['uid', 'account', 'mobile', 'email', 'name', 'sex', 'openid', 'avatar', 'status', 'login_times']);
    }

    public function secondUser()
    {
        return $this->belongsTo('User', 'second_uid', 'uid')->field(['uid', 'account', 'mobile', 'email', 'name', 'sex', 'openid', 'avatar', 'status', 'login_times']);
    }

    public function branch()
    {
        return $this->belongsTo('Branch', 'bid', 'bid');
    }

    /*一对一，一对多排课*/
    public function courseArrange()
    {
        return $this->belongsToMany('CourseArrange', 'course_arrange_student', 'ca_id', 'sid');
    }

    public function studentLesson()
    {
        return $this->hasMany('StudentLesson', 'sid', 'sid');
    }


    //家长添加一个学生
    public function createOneStudent($input)
    {
        $this->startTrans();
        try {
            $m_user = new User();
            $user = $m_user->where('mobile', $input['first_tel'])->where('user_type', User::STUDENT_ACCOUNT)->find();
            if (!empty($user)) {
                /* 同一个user的student姓名不能重复 */
                $student_names = $user->students()->alias('s')->column('s.student_name');
                if (isset($input['student_name']) && in_array($input['student_name'], $student_names)) {
                    throw new FailResult('同一个用户的学生姓名不能重复');
                }
            }

            if(isset($input['school_id'])) {
                $input['school_id'] = PublicSchool::findOrCreate($input['school_id']);
            }

            $m_student = new Student();
            $rs = $m_student->allowField(true)->save($input);
            if ($rs === false) throw new FailResult('添加学生失败');

            $rs = $m_user->createStudentUserAfterCreateStudent($m_student);
            if ($rs === false) throw new FailResult($m_user->getErrorMsg());
            $this->commit();
        } catch (FailResult $e) {
            $this->rollback();
            return $this->user_error($e->getMessage());
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    //编辑学员资料
    public function updateStudent($data, $sid, $student = null)
    {
        if(is_null($student)) {
            $student = $this->find($sid);
        }

        $allow_field = ['student_name','pinyin','pinyin_abbr','nick_name','sex','photo_url','birth_time','birth_year',
            'birth_month','birth_day','school_grade','school_class','school_id','first_family_name','first_family_rel',
            'second_family_name','second_family_rel'];

        $rs = $student->allowField($allow_field)->isUpdate(true)->save($data);
        if($rs === false) return $this->user_error('更新失败');

        $callback_user_data = $student->toArray();
        if(!empty($callback_user_data['first_uid'])) {
            $user = User::get($this->getData('first_uid'));
            if(!empty($user)) {
                $callback_user_data =  array_merge($callback_user_data, $user->getData());
            }
        }
        callback_queue_push('user_modify_callback_url', $callback_user_data);

        return true;
    }

    




}