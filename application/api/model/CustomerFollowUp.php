<?php
/** 
 * Author: luo
 * Time: 2017-10-11 11:09
**/

namespace app\api\model;

use think\Exception;

class CustomerFollowUp extends Base
{

    const SYSTEM_OP_TYPE_IN_PS = 1;
    const SYSTEM_OP_TYPE_OUT_PS = 2;



    protected $type = [
    ];

    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    protected $auto = ['eid'];

    protected function setEidAttr()
    {
        return gvar('uid') ? User::getEidByUid(gvar('uid')) : 0;
    }

    protected function setPromiseIntDayAttr($value, $data)
    {
        return $value ? date('Ymd', strtotime($value)) : '';
    }

    protected function setNextFollowTimeAttr($value, $data)
    {
        return $value ? strtotime($value) : $value;
    }

    protected function setVisitIntDayAttr($value, $data)
    {
        return $value ? date('Ymd', strtotime($value)) : '';
    }

    protected function getNextFollowTimeAttr($value)
    {
        if(strlen($value) == 8) $value = strtotime($value);
        return $value > 0 ? date('Y-m-d', $value) : $value;
    }

    public function customer()
    {
        return $this->belongsTo('Customer', 'cu_id', 'cu_id');
    }

    //添加跟进记录
    public function addOneFollowUp($data)
    {
        if(!isset($data['cu_id'])) return $this->user_error('param error');

        $this->startTrans();
        try {
            $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
            if ($rs === false){
                $this->rollback();
                return $this->user_error('添加跟进失败');
            }

            $cfu_id = $this->getAttr('cfu_id');

            if (isset($data['token'])){
                $mWbl = new WebcallCallLog();
                $rs = $mWbl->addRelateCmtId($data['token'],$cfu_id);
                if (!$rs) return $this->user_error('token error');
            }


            set_customer_last_followup($data['cu_id'],$this->getData());
            //增加跟进时，客户的跟进次数加一
            $customer = Customer::get($data['cu_id']);
            if (empty($customer)){
                $this->rollback();
                return $this->user_error('客户不存在');
            }

            $update_cu = [];

            $update_cu['follow_times'] = $customer['follow_times']+1;

            if(!isset($data['system_op_type']) || empty($data['system_op_type'])){
                $update_cu['last_follow_time'] = time();
            }

            if(isset($data['intention_level'])){
                $update_cu['intention_level'] = $data['intention_level'];

            }

            if(isset($data['customer_status_did'])){
                $update_cu['customer_status_did'] = $data['customer_status_did'];

            }

            if(isset($data['next_follow_time'])) {
                $update_cu['next_follow_time'] = $data['next_follow_time'];
            }

            if(!empty($update_cu)){
                foreach($update_cu as $f=>$v){
                    $customer[$f] = $v;
                }
                $result = $customer->isUpdate(true)->save();
                if(false === $result){
                    $this->rollback();
                    return $this->sql_save_error('customer');
                }
            }

            // 添加一条客户跟单操作日志
            CustomerLog::addCustomerFollowUpLog($data['cu_id']);

        } catch (Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

    public function updateFollowUp($data, $cfu_id, $follow_up = null)
    {
        if(is_null($follow_up)) {
            $follow_up = $this->find($cfu_id);
        }

        $customer = Customer::get($follow_up['cu_id']);
        if(empty($customer)) return $this->user_error('客户不存在');

        $rs = $follow_up->allowField(true)->save($data);
        if($rs === false) return $this->user_error('更新跟进记录失败');

        //到访
        if(isset($data['is_visit']) && $data['is_visit'] == 1) {
            $customer->visit_times = $customer->visit_times + 1;
            if ($customer->customer_status_did == Customer::STATUS_DID_NOT_VISIT || $customer->customer_status_did  == 0) {
                $customer->customer_status_did = Customer::STATUS_DID_VISITED;
            }
            $customer->save();

            $m_mc = new MarketClue();
            $rs = $m_mc->where('cu_id', $customer['cu_id'])->update(['is_visit' => 1]);
            if($rs === false) return $this->user_error($m_mc->getErrorMsg());
        }

        //未到访
        if(isset($data['is_visit']) && $data['is_visit'] == 0) {
            $customer->visit_times = $customer->visit_times > 0 ? $customer->visit_times - 1 : 0;
            if ($customer->customer_status_did == Customer::STATUS_DID_VISITED) {
                $customer->customer_status_did = Customer::STATUS_DID_NOT_VISIT;
            }
            $customer->save();
        }

        return true;
    }


    public function deleteFollowUp($cfu_id)
    {
        $follow_up = $this->find($cfu_id);

        $this->startTrans();
        try{

            $ret = $follow_up->delete();
            if(false === $ret){
                $this->rollback();
                return $this->sql_delete_error('customer_follow_up');
            }

            // 更新客户更新次数
            $cu_id = $follow_up->cu_id;
            $customer = Customer::get($cu_id);
            $ret = $customer->setDec('follow_times');
            if(false === $ret){
                $this->rollback();
                return $this->sql_save_error('customer');
            }

        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();

        return true;
    }

    /**
     *  今日沟通数量
     * @param $eid
     */
    public function getTodayFollowUp($eid){
        $create_time = today_start_end_time();
        $w['eid'] = $eid;
        return $this->where($w)->where('create_time','between',$create_time)->count();
    }

    public function getTodayPromise($eid){
        $w = [
            'eid' => $eid,
            'promise_int_day' => int_day(time()),
        ];
        return $this->where($w)->count();
    }


}