<?php
/** 
 * Author: luo
 * Time: 2017-10-14 12:04
**/

namespace app\sapi\model;

class OrderItem extends Base
{
    const GTYPE_LESSON = 0; //课程
    const GTYPE_GOODS = 1;  //物品

    const UNIT_LESSON_TIMES = 1;
    const UNIT_LESSON_HOURS = 2;
    const UNIT_TERM = 3;
    const UNIT_MONTHLY = 4;

    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    protected $insert = ['subtotal'];

    protected $append = ['item_name'];

    protected function setSubtotalAttr($value, $data) {

        $data['discount_amount'] = isset($data['discount_amount']) ? $data['discount_amount'] : 0;
        $data['reduced_amount'] = isset($data['reduced_amount']) ? $data['reduced_amount'] : 0;
        $value = isset($data['origin_amount']) ?
            $data['origin_amount'] - $data['discount_amount'] - $data['reduced_amount'] : 0;
        return $value;
    }

    /**
     * 获得订单条目名称
     * @param  [type] $value [description]
     * @param  [type] $data  [description]
     * @return [type]        [description]
     */
    public function getItemNameAttr($value,$data){
        $name = '';
        if(empty($data)){
            return $name;
        }

        if($data['gtype'] == 0) {
            //课程
            if (isset($data['lid']) && $data['lid'] > 0) {
                $lesson_info = get_lesson_info($data['lid']);
                if ($lesson_info) {
                    $name = $lesson_info['lesson_name'];
                }
            }
            if (isset($data['cid']) && $data['cid'] > 0) {
                $class_info = get_class_info($data['cid']);
                if ($class_info) {
                    $name = $class_info['class_name'];
                }
            }
        }elseif($data['gtype'] == 2){
            $prefix = '储值';
            if($data['dc_id'] > 0){
                $prefix = '购买储值卡';
            }
            $name = $prefix.':'.$data['subtotal'].'元';
        }else{
            //物品
            $goods_info = get_material_info($data['gid']);
            if($goods_info){
                $name = $goods_info['name'];
            }
        }
        return $name;
    }

    public function setExpireTimeAttr($value)
    {
        return $value ? strtotime($value) : 0;
    }

    public function getExpireTimeAttr($value)
    {
        return $value ? date('Y-m-d', $value) : 0;
    }

    public function joinOrder()
    {
        return $this->belongsTo('Order', 'oid', 'oid');
    }

    public function student()
    {
        return $this->belongsTo('Student', 'sid', 'sid')->field('sid,student_name');
    }

    public function studentLesson()
    {
        return $this->belongsTo('Student_Lesson', 'sl_id', 'sl_id');
    }

    public function lesson()
    {
        return $this->belongsTo('Lesson', 'lid', 'lid');
    }

    public function material()
    {
        return $this->hasOne('Material', 'mt_id', 'gid');
    }
    
    public function oneClass()
    {
        return $this->hasOne('Classes', 'cid', 'cid');
    }
    
    public function orderReceiptBillItem()
    {
        return $this->hasMany('OrderReceiptBillItem', 'oi_id', 'oi_id');
    }

    public function orderTransferItem()
    {
        return $this->hasMany('OrderTransferItem', 'oi_id', 'oi_id');
    }

    public function orderRefundItem()
    {
        return $this->hasMany('OrderRefundItem', 'oi_id', 'oi_id');
    }





}