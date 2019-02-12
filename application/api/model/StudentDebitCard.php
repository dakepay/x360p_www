<?php
/**
 * Author: luo
 * Time: 2018/8/15 10:59
 */

namespace app\api\model;


use app\common\exception\FailResult;
use think\Exception;

class StudentDebitCard extends Base
{

    protected $append = ['smh_id','create_employee_name'];

    protected $hidden = [
        'create_time',
        'update_time',
        'is_delete',
        'delete_time'
    ];

    protected function setBuyIntDayAttr($value)
    {
        return !empty($value) && !is_numeric($value) ? format_int_day($value) : $value;
    }

    protected function setExpireIntDayAttr($value)
    {
        return !empty($value) && !is_numeric($value) ? format_int_day($value) : $value;
    }

    /**
     * 获得学员金额变动历史记录ID
     * @param $value
     * @param $data
     */
    public function getSmhIdAttr($value,$data){
        if(!isset($data['oi_id']) || $data['oi_id'] == 0){
            return 0;
        }
        $w_smh['sdc_id'] = $data['sdc_id'];
        $smh_info = get_smh_info($w_smh);
        if(!$smh_info){
            return 0;
        }
        return $smh_info['smh_id'];
    }

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,student_name');
    }

    public function debitCard()
    {
        return $this->hasOne('DebitCard', 'dc_id', 'dc_id');
    }

    public function addCard($post)
    {
        $rule = [
            'sid' => 'require',
            'dc_id' => 'require'
        ];

        $validate = validate();
        $rs = $validate->rule($rule)->check($post);
        if($rs !== true) {
            return $this->user_error($validate->getError());
        }
        $ret = [
          'sdc_id'=>0,
          'smh_id'=>0
        ];
        $m_dc = new DebitCard();
        $debit_card = $m_dc->where('dc_id', $post['dc_id'])->find();
        if(empty($debit_card)) return $this->user_error('储蓄卡不存在');

        try {
            $this->startTrans();
            $post['remain_amount'] = $debit_card['amount'];
            $post['upgrade_vip_level'] = $debit_card['upgrade_vip_level'];
            $rs = $this->allowField(true)->save($post);
            if($rs === false) throw new FailResult($this->getErrorMsg());

            $sdc_id = $this->sdc_id;
            $ret['sdc_id'] = $sdc_id;
            $student = Student::get($post['sid']);
            $change_data = [
                'money'  => $debit_card['amount'],
                'sdc_id' => $sdc_id,
                'business_type' => StudentMoneyHistory::BUSINESS_TYPE_RECHARGE,
            ];
            $rs = $student->changeMoney($student, $change_data);
            if($rs === false) throw new FailResult($student->getErrorMsg());
            $ret['smh_id'] = $rs;

            if($debit_card['upgrade_vip_level'] > 0 && $debit_card['upgrade_vip_level'] > $student['vip_level']) {
                $student->vip_level = $debit_card['upgrade_vip_level'];
                $student->save();
            }

            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return $ret;
    }

    public function delCard()
    {
        if(empty($this->getData())) return $this->user_error('模型出错');

        $student_debit_card = $this->getData();

        $sid = $student_debit_card['sid'];

        $debit_card = DebitCard::get($student_debit_card['dc_id']);
        if(empty($debit_card)) return $this->user_error('储蓄卡已被删除');

        if($student_debit_card['buy_type'] == 0){
            if($student_debit_card['remain_amount'] < $debit_card['amount']){
                return $this->user_error('储值卡已被使用，不能删除!');
            }
        }else{
            if($student_debit_card['remain_amount'] < $student_debit_card['start_amount']){
                return $this->user_error('储值卡已被使用，不能删除!');
            }
        }
        try {
            $this->startTrans();

            if($student_debit_card['buy_type'] == 0) {      //如果是购买储值卡才删除金额
                $student = Student::get($student_debit_card['sid']);
                $change_data = [
                    'money' => -$debit_card['amount'],
                    'sdc_id' => $student_debit_card['sdc_id'],
                    'remark' => '取消购买储蓄卡'
                ];
                $rs = $student->changeMoney($student, $change_data);
                if ($rs === false) throw new FailResult($student->getErrorMsg());
                $student_info = get_student_info($sid);
                if($debit_card['upgrade_vip_level'] > 0 && $debit_card['upgrade_vip_level'] == $student_info['vip_level']){
                    $update_student['vip_level'] = $student_info['vip_level']-1;
                    $w_student_update['sid'] = $sid;
                    $m_student  = new Student();
                    $result = $m_student->save($update_student,$w_student_update);
                    if(false === $student){
                        $this->rollback();
                        return $this->sql_save_error('student');
                    }
                }
            }
            $rs = $this->delete();
            if($rs === false) throw new FailResult($this->getErrorMsg());

        } catch(\Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }
        $this->commit();

        return true;
    }

    /**
     * 更新储值卡过期日期
     * @param $input
     * @return bool
     */
    public function updateExpireDay($input){
        if(!$this->checkInputParam($input,['sdc_id'])){
            return false;
        }
        if(!isset($input['expire_int_day']) || $input['expire_int_day'] == 0){
            $input['expire_int_day'] = 0;
        }

        $w['sdc_id'] = $input['sdc_id'];
        $update['expire_int_day'] = format_int_day($input['expire_int_day']);
        $result = $this->save($update,$w);
        if (false === $result) {
            return $this->sql_save_error('student_debit_card');
        }

        return true;

    }


}