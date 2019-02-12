<?php
namespace app\api\model;

class CenterClientApplySqb extends Center
{
	protected $table = 'pro_client_apply_sqb';
    protected $hidden = ['is_delete','update_time','create_uid','delete_time','delete_uid'];


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


    public function clientApplySqbCheck()
    {
	    return $this->hasMany('center_client_apply_sqb_check','cas_id','cas_id');
    }


    //  商户信息提交
    public function audit($data){

        $client = gvar('client');
        $w = [
            'contact_cellphone' => $data['contact_cellphone'],
            'cid' => $client['cid'],
            'og_id'=>$client['og_id']
        ];
        $m_sqb = $this->where($w)->find();
        if (!empty($m_sqb)){
            return $this->user_error('您的申请已存在');
        }

        $this->startTrans();
        try {
            $data['cid'] = $client['cid'];
            $rs = $this->isUpdate(false)->allowField(true)->save($data);

            if (false === $rs){
                return $this->sql_add_error('pro_client_apply_sqb');
            }

            $cas_id = $this->getAttr('cas_id');
            $mSqbCheck = new CenterClientApplySqbCheck();
            $result = $mSqbCheck->addSqbCheck($cas_id,$client['cid'],'提交申请');
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

            $data['is_check'] = 0;
            $rs = $m_sqb->allowField(true)->save($data,$w);
            if (false === $rs){
                return $this->sql_save_error('pro_client_apply_sqb');
            }

            $mSqbCheck = new CenterClientApplySqbCheck();
            $result = $mSqbCheck->addSqbCheck($m_sqb['cas_id'],$m_sqb['cid'],'信息修改');
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
}