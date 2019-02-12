<?php
/** 
 * Author: luo
 * Time: 2018-01-09 18:29
**/

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;

class StudentCreditHistory extends Base
{

    const TYPE_INC = 1; # 增加积分
    const TYPE_DEC = 2; # 减少积分

    const CATE_STUDY = 1; # 学习积分
    const CATE_CONSUME = 2; # 消费积分

    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];
    protected $append = ['create_employee_name'];


    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,student_name');
    }

    public function creditRule()
    {
        return $this->hasOne('CreditRule', 'cru_id', 'cru_id');
    }

    //添加学员积分
    public function addOneHistory($data)
    {
    	$rule = [
    	    'credit' => 'require|gt:0',
            'sid' => 'require',
            'type' => 'require',
            'cate' => 'require',
        ];

    	$validate = validate();
    	$rs = $validate->rule($rule)->check($data);
        if($rs !== true) return $this->user_error($validate->getError());

        $student = (new Student())->find($data['sid']);
        request()->bid = $student->bid;

        $data['before_credit'] = $data['cate'] == self::CATE_STUDY
            ? $student['credit'] : ($data['cate'] == self::CATE_CONSUME ? $student['credit2'] : 0);
        $data['after_credit'] = $data['type'] == self::TYPE_INC ? $data['before_credit'] + $data['credit'] : $data['before_credit'] - $data['credit'];
        if($data['after_credit'] <= 0)
        {
            $cate_name = $data['cate'] == self::CATE_STUDY
                ? '学习' : ($data['cate'] == self::CATE_CONSUME ? '消费' : '');
            return $this->user_error(sprintf('%s积分不能小于0', $cate_name));
        }

        try {
            $this->startTrans();
    	    $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
    	    if($rs === false) return $this->user_error('添加积分失败');
            self::UpdateStudentCredit($data['sid'], $data['cate']);
            $this->commit();
        } catch(Exception $e) {
            $this->startTrans();
            return $this->deal_exception($e->getMessage(), $e);
        }

    	return true;
    }

    public static function UpdateStudentCredit($sid, $cate)
    {
        $self = new self();
        if($cate == self::CATE_STUDY) {
            $positive_credit = $self->where('sid', $sid)->where('cate', self::CATE_STUDY)
                ->where('type', self::TYPE_INC)->sum('credit');
            $negative_credit = $self->where('sid', $sid)->where('cate', self::CATE_STUDY)
                ->where('type', self::TYPE_DEC)->sum('credit');
            $total_credit = $positive_credit - $negative_credit;
            $rs = (new Student())->where('sid', $sid)->update(['credit' => $total_credit]);
            if($rs === false) throw new FailResult('更新学生积分失败');
            
        }

        if($cate == self::CATE_CONSUME) {
            $positive_credit2 = $self->where('sid', $sid)->where('cate', self::CATE_CONSUME)
                ->where('type', self::TYPE_INC)->sum('credit');
            $negative_credit2 = $self->where('sid', $sid)->where('cate', self::CATE_CONSUME)
                ->where('type', self::TYPE_DEC)->sum('credit');
            $total_credit2 = $positive_credit2 - $negative_credit2;
            $rs = (new Student())->where('sid', $sid)->update(['credit2' => $total_credit2]);
            if($rs === false) throw new FailResult('更新学生消费积分失败');
        }

        return true;
    }

    public function addMultiCreditHistory($post)
    {
        try {
            $this->startTrans();
            foreach($post as $row) {
                $rs = $this->addOneHistory($row);
                if($rs === false) throw new FailResult($this->getErrorMsg());
            }

            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }


}