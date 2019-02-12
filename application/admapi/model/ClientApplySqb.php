<?php

namespace app\admapi\model;
use util\Sqb;

class ClientApplySqb extends Base
{
    protected $hidden = [];

    public function getBankAreaArrAttr($value, $data)
    {
        return explode(',',$value);
    }

    public function getAreaArrAttr($value, $data)
    {
        return explode(',',$value);
    }

    public function setBankAreaArrAttr($value, $data)
    {
        if (is_array($value)) {
            return join(',', $value);
        }
        return $value;
    }

    public function setAreaArrAttr($value, $data)
    {
        if (is_array($value)) {
            return join(',', $value);
        }
        return $value;
    }

    public function client()
    {
        return $this->hasOne('Client','cid','cid');
    }

    public function clientApplySqbCheck()
    {
        return $this->hasMany('client_apply_sqb_check','cas_id','cas_id');
    }

    /**
     *  后台审核
     * @param $input
     * @return bool
     */
    public function do_audit($cas_id,$is_audit = 0,$is_check = 1,$check_messages){
        $m_cas = $this->get($cas_id);
        if (empty($m_cas)){
            return $this->user_error('申请信息不存在');
        }
        $this->startTrans();
        try {
            $w['cas_id'] = $cas_id;
            $update = [
                'is_audit' => $is_audit,
                'is_check' => $is_check
                ];
            $rs = $this->save($update,$w);
            if (!$rs){
                return $this->sql_save_error('client_apply_sqb');
            }

            $mSqbCheck = new ClientApplySqbCheck();
            $result = $mSqbCheck->addSqbCheck($m_cas['cas_id'],$m_cas['cid'],$check_messages);
            if (false === $result){
                $this->rollback();
                return $this->user_error($mSqbCheck->getError());
            }
        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    public function updateInfo($data){

        $m_sqb = $this->where('cas_id',$data['cas_id'])->find();
        if (empty($m_sqb)){
            return $this->user_error('申请信息不存在');
        }
        $this->startTrans();
        try {
            $w['cas_id'] = $m_sqb['cas_id'];

            $rs = $m_sqb->allowField(true)->save($data,$w);
            if (false === $rs){
                return $this->sql_save_error('pro_client_apply_sqb');
            }

        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    /**
     *  商户信息提交
     * @param $sqb_id
     * @return bool|mixed
     */
    public function do_create($cas_id){
        $mSqb_info = $this->get($cas_id);
        if (empty($mSqb_info)){
            return $this->user_error('商户信息不存在!');
        }
        if ($mSqb_info['is_audit'] == 0){
            return $this->user_error('请先审核');
        }
        $sab_pay = config('shouqianba');
        $mSqb_info['vender_sn'] = $sab_pay['vender_sn'];
        $mSqb = new Sqb();
        $res = $mSqb->create($mSqb_info,$sab_pay['vender_sn'],$sab_pay['vender_key']);

        if ($res['result_code'] != 200){
            return $this->user_error($res['error_message']);
        }

        $update_w['sqb_id'] = $cas_id;
        $update['config'] = $res;
        $rs = $this->save($update,$update_w);
        if (!$rs){
            return $this->user_error('支付配置失败');
        }

        return true;
    }


}
