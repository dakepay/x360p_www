<?php
/**
 * Author: luo
 * Time: 2017-11-11 16:42
**/

namespace app\api\model;

class OrderCutAmount extends Base
{
    const CUT_TYPE_TRANSFER = 1;
    const CUT_TYPE_REFUND = 2;

    protected $hidden = [
        'update_time', 
        'is_delete', 
        'delete_time', 
        'delete_uid'
    ];

    public function student()
    {
    	return $this->hasOne('student','sid','sid');
    }

    public function createOneCut($data)
    {
        $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
        if(!$rs) return $this->user_error('新增扣款失败');

        return $this;
    }

    /**
     * 撤销结转手续费
     * @param $ot_id
     */
    public function undoCutAmount($ot_id)
    {
        $cut_amount_list = $this->where(['ot_id' => $ot_id, 'type' => 1])->select();
        if (!empty($cut_amount_list)){
            $this->startTrans();
            try {
                foreach ($cut_amount_list as $cut_amount){
                    $cut_amount->delete();
                }
            } catch(\Exception $e) {
                $this->rollback();
                return $this->exception_error($e);
            }
	    $this->commit();
        }

        
        return true;
    }


}