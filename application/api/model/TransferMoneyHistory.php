<?php 

namespace app\api\model;

class TransferMoneyHistory extends Base
{
	protected $hidden = [
        'update_time', 
        'is_delete', 
        'delete_time', 
        'delete_uid'
    ];


    /**
     * 撤销转让余额
     * @param $tmh_id
     */
	public function delTransferMoney($tmh_id)
    {
        $m_transfer_money = $this->where(['tmh_id'=>$tmh_id])->find();
        if (empty($m_transfer_money)){
            return $this->user_error('转让余额记录不存在');
        }
        $this->startTrans();
        try {
            $mStudent = new Student();
            $from_student = $mStudent->where(['sid' => $m_transfer_money['from_sid']])->find();
            $to_student = $mStudent->where(['sid' => $m_transfer_money['to_sid']])->find();

            $mSmh = new StudentMoneyHistory();
            $from_data['business_type'] = StudentMoneyHistory::BUSINESS_TYPE_IN;
            $from_data['amount'] = $m_transfer_money['amount'];
            $from_data['before_amount'] = $from_student->money;
            $from_data['after_amount'] = $from_student->money + $m_transfer_money['amount'];
            $from_data['remark'] = '转入：' . $m_transfer_money['remark'];
            array_copy($from_data, $from_student, ['og_id', 'bid', 'sid']);

            $to_data['business_type'] = StudentMoneyHistory::BUSINESS_TYPE_OUT;
            $to_data['amount'] = $m_transfer_money['amount'];
            $to_data['before_amount'] = $to_student->money;
            $to_data['after_amount'] = $to_student->money - $m_transfer_money['amount'];
            $to_data['remark'] = '转出：' . $m_transfer_money['remark'];
            array_copy($to_data, $to_student, ['og_id', 'bid', 'sid']);

            $from_res = $mSmh->data([])->isUpdate(false)->save($from_data);
            if (false === $from_res) {
                $this->rollback();
                return $this->sql_add_error('student_money_history');
            }

            $to_res = $mSmh->data([])->isUpdate(false)->save($to_data);
            if (false === $to_res) {
                $this->rollback();
                return $this->sql_add_error('student_money_history');
            }

            $from_student->money = $from_student->money + $m_transfer_money['amount'];
            $from_student->save();

            $to_student->money = $to_student->money - $m_transfer_money['amount'];
            $to_student->save();

            $m_transfer_money->delete();
        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

}