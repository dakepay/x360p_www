<?php
namespace app\api\model;

use think\Exception;
use think\Log;
use Curl\Curl;


class BookQtyHistory extends Base
{
    const BOOK_TYPE_INTO = 0;  //增加
    const BUSINESS_TYPE_RETURN = 1;     //减少



    /**
     * @desc  书籍库存变动表
     * @param array
     * @return bool
     */
    public function createBookQtyHistory($bk_id,$qty,$type,$remark = null,$sid=0){
        if (empty($bk_id)){
            return $this->input_param_error('bk_id');
        }
        if (empty($qty)){
            return $this->input_param_error('qty');
        }
        $data = [
            'bk_id' => $bk_id,
            'op_type' => $type,
            'qty' => $qty,
            'op_int_day' => int_day(time()),
            'remark' => $remark,
            'sid' => $sid,
        ];
        $rs = $this->allowField(true)->isUpdate(false)->save($data);
        if(!$rs) return $this->user_error('创建书籍库存变动失败');
        return $rs;
    }


}