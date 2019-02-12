<?php
/** 
 * Author: luo
 * Time: 2017-10-14 12:04
**/

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;

class OrderItem extends Base
{
    const GTYPE_LESSON = 0; //课程
    const GTYPE_GOODS = 1;  //物品
    const GTYPE_DEBIT = 2;  //储值及储值卡
    const GTYPE_PAYITEM  = 3; //杂费

    const UNIT_LESSON_TIMES = 1;
    const UNIT_LESSON_HOURS = 2;
    const UNIT_TERM = 3;
    const UNIT_MONTHLY = 4;

    const LESSON_HOUR_START = 0;
    const LESSON_HOUR_END = 1;

    const CONSUME_TYPE_NEW    = 1;  #新报
    const CONSUME_TYPE_RENEW  = 2;  #续报
    const CONSUME_TYPE_EXTEND = 3;  #扩科

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
        }elseif($data['gtype'] == 2) {
            $prefix = '储值';
            if ($data['dc_id'] > 0) {
                $prefix = '购买储值卡';
            }
            $name = $prefix . ':' . $data['subtotal'] . '元';
        }elseif($data['gtype'] == 3){
            $name = get_pi_name($data['pi_id']);
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
        if(preg_match('/^\d{4}-\d{2}-\d{2}$/',$value)){
            return strtotime($value);
        }
        return $value ? intval($value) : 0;
    }

    public function getUnitLessonHourAmountAttr($value, $data)
    {
        if($value > 0 || $data['gtype'] == self::GTYPE_GOODS) {
            return $value;
        }

        $subtotal = $data['subtotal'];
        if($subtotal <= 0) return $value;

        $total_lesson_hours = $data['origin_lesson_hours'] + $data['present_lesson_hours'];
        if($total_lesson_hours <= 0) return $value;

        return round(($subtotal / $total_lesson_hours), 6);
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

    public function refererStudent()
    {
        return $this->hasOne('Student', 'sid', 'referer_sid')->field('sid,student_name');
    }

    public function refererTeacher()
    {
        return $this->hasOne('Employee', 'eid', 'referer_teacher_id')->field('ename,nick_name,eid');
    }

    public function refererEmployee()
    {
        return $this->hasOne('Employee', 'eid', 'referer_eid')->field('ename,nick_name,eid');
    }

    public function getNumsUnitTextAttr($value, $data)
    {
        $temp = [1 => '课次', 2 => '课时', 3 => '月'];
        if (isset($data['nums_unit'])) {
            return $temp[$data['nums_unit']];
        }
    }

    //获取项目的帐户付款情况
    public function getItemPaymentHis(OrderItem $item)
    {
        $bill_items = OrderReceiptBillItem::all(['oi_id' => $item->oi_id]);
        foreach($bill_items as &$per_bill) {
            $orb_id = $per_bill['orb_id'];
            $payment_list = OrderPaymentHistory::all(function($query) use($orb_id) {
                $query->where('orb_id', $orb_id)->field('orb_id,aa_id,amount,paid_time');
            });
            $per_bill['order_payment_history'] = $payment_list;
        }
        return $bill_items;
    }

    /**
     * 格式化输入字段
     * @param  [type] &$input [description]
     * @return [type]         [description]
     */
    protected function format_input_fields(&$input,&$order_info){
        if($input['gtype'] == 0){
            $this->cacuLessonHourFields($input,$order_info);
        }elseif($input['gtype'] == 1){
            //物品项目;
            if(!isset($input['gid']) || $input['gid'] == 0){
                return $this->user_error('缺少物品ID');
            }
        }
        return true;
    }

    /**
     * 计算课时字段
     * @return [type] [description]
     */
    public function cacuLessonHourFields(&$input,&$order_info){
        $lid = $input['lid'] = intval($input['lid']);
        $cid = $input['cid'] = intval($input['cid']);
        $nums_unit = $input['nums_unit'] = intval($input['nums_unit']);

        if($lid > 0){
            $lesson_info = get_lesson_info($lid);
            if(!$lesson_info){
                return $this->user_error('课程ID不存在:'.$lid);
            }
            $unit_lesson_hours = $lesson_info['unit_lesson_hours'];  //单次扣课时
            if($unit_lesson_hours <= 0) return $this->user_error('一次课扣0个课时数，出现错误,请检查课程信息');
            if($nums_unit == 1){
            //按课次
                $input['origin_lesson_times']  = intval($input['nums']);
                $input['present_lesson_times'] = intval($input['present_lesson_times']);

                $input['origin_lesson_hours'] = $input['origin_lesson_times'] * $unit_lesson_hours;
                $input['present_lesson_hours'] = $input['present_lesson_times'] * $unit_lesson_hours;

                $total_lesson_hours = $input['origin_lesson_hours'] + $input['present_lesson_hours'];

            }elseif($nums_unit == 2){
                $input['origin_lesson_hours'] = floatval($input['nums']);
                $input['present_lesson_hours'] = floatval($input['present_lesson_hours']);

                $input['origin_lesson_times'] = $input['origin_lesson_hours'] / $unit_lesson_hours;
                $input['present_lesson_times'] = $input['present_lesson_hours'] / $unit_lesson_hours;

                $total_lesson_hours = $input['origin_lesson_hours'] + $input['present_lesson_hours'];

                
            }else{
                //按时间计费
                $month = intval($input['nums']);
                if($order_info['paid_time'] == 0){
                    $paid_time = time();
                }else{
                    $paid_time = $order_info['paid_time'];
                }

                $input['start_int_day'] = int_day($paid_time);

                if(!$input['expire_time']) {
                    $input['expire_time'] = strtotime("+$month months", $paid_time);
                }else{
                    $input['expire_time'] = strtotime($input['expire_time']);
                }

                $input['origin_lesson_hours'] = 0.00;
                $input['present_lesson_hours'] = 0.00;
                $input['origin_lesson_times'] = 0.00;
                $input['present_lesson_times'] = 0.00;

                $total_lesson_hours = 0.00;  
            }
        }elseif($cid > 0){
            $nums_unit = $input['nums_unit'] = 2;       //如果是班级统一用课时
            $input['origin_lesson_hours']  = floatval($input['nums']);
            $input['present_lesson_hours'] = floatval($input['present_lesson_hours']);

            $consume_lesson_hour = $this->m_classes->getConsumeLessonHour($cid);

            $input['origin_lesson_times'] = $input['origin_lesson_hours'] / $consume_lesson_hour;
            $input['present_lesson_times'] = $input['present_lesson_hours'] / $consume_lesson_hour;

            $total_lesson_hours = $input['origin_lesson_hours'] + $input['present_lesson_hours'];
        }

        //单课时价格
        if($total_lesson_hours == 0){
            $input['unit_lesson_hour_amount'] = 0.00;
        }else{
            $input['unit_lesson_hour_amount'] = round($input['subtotal'] / $total_lesson_hours,6);
        }


        return $input;
    }

    /**
     * 创建单个项目
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function  createOrderItem(&$input)
    {

        if(!isset($input['oid'])){
            return $this->input_param_error('oid',1);
        }

        $order_info = get_order_info($input['oid']);

        $result = $this->format_input_fields($input,$order_info);

        if(!$result){
            return false;
        }

        $oi =[];
        array_copy($oi,$order_info,['og_id','bid','sid','oid']);
        array_copy($oi,$input,['gtype','gid','lid','cid','nums','nums_unit',
            'origin_price','price','origin_amount','subtotal','paid_amount',
            'discount_amount','reduced_amount','unit_lesson_hour_amount','expire_time',
            'consume_type','origin_lesson_hours','present_lesson_hours',
            'origin_lesson_times','present_lesson_times','sdc_id','dc_id','pi_id','c_start_int_day','from_lid','from_sl_id']);
        $oi['paid_amount'] = 0;


        $result = $this->data([])->isUpdate(false)->allowField(true)->save($oi);


        if(!$result){
            return $this->sql_add_error('order_item');
        }

        return true;
    }

    /**
     * 处理关联数据
     * @return [type] [description]
     */
    public function createBusinessData($oi_info = []){
        if(empty($oi_info)){
            $oi_info = $this->getData();
        }

        if($oi_info['gtype'] == 1){
            //处理商品
            return $this->dealOrderGoodsItem($oi_info);
        }elseif($oi_info['gtype'] == 2){
            //处理购买储值卡
            return $this->dealOrderDebitItem($oi_info);
        }elseif($oi_info['gtype'] == 3) {
            //处理杂费
            return true;
        }
        //处理课程
        return $this->dealOrderLessonHoursItem($oi_info);
    }

    /**
     * 处理订单商品条目
     * @param  [type] $oi_info [description]
     * @return [type]          [description]
     */
    public function dealOrderGoodsItem($oi_info){
        if(empty($oi_info)){
            $oi_info = $this->getData();
        }

        $update_oi = ['is_deliver' => 1]; # 是否已经发放，如果物品库存不足，则未发放

        $bid = $oi_info['bid'];         //校区ID

        $mt_id = $oi_info['gid'];       //物品ID

        $ms_id = Branch::getMsId($bid); //根据校区ID获取仓库ID

        if(!$ms_id){
            return $this->user_error('校区还没有设置相关仓库，无法处理物品库存!');
        }

        $mh_data = [
            'mt_id' => $mt_id,
            'ms_id' => $ms_id,
            'oi_id' => $oi_info['oi_id'],
            'num'   => $oi_info['nums'],
            'int_day'   => int_day(time()),
            'type'      => MaterialHistory::TYPE_OUT,
            'cate'      => MaterialHistory::CATE_ORDER,
            'remark'    => '自动发货,订单号:'.Order::getOrderNo($oi_info['oid'])
        ];

        $this->startTrans();
        try{
            $w_mh = [];

            $w_mh = array_copy($w_mh,$mh_data,['mt_id','ms_id','oi_id']);

            $ex_mh = $this->m_material_history->where($w_mh)->find();

            if(!$ex_mh){
                $result = $this->m_material_history->addOneHis($mh_data);
                if(!$result){
                    $this->rollback();
                    exception($this->m_material_history->getError());
                }
            }

            $w_oi['oi_id'] = $oi_info['oi_id'];

            $result = $this->m_order_item->save($update_oi,$w_oi);

            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('order_item');
            }
        }catch(Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();

        return true;

    }

    /**
     * 处理订单购买储值条目
     * @param  [type] $oi_info [description]
     * @return [type]          [description]
     */
    public function dealOrderDebitItem($oi_info){
        if(empty($oi_info)){
            $oi_info = $this->getData();
        }


        $update_oi = ['is_deliver' => 1]; # 是否已经储值成功
        $bid = $oi_info['bid'];         //校区ID

        $order_info = get_order_info($oi_info['oid']);

        $this->startTrans();
        try{
            $remark = $order_info['remark'];
            $buy_int_day = date('Ymd',$order_info['paid_time']);
            $expire_int_day = date('Ymd',$oi_info['expire_time']);
            $c_start_int_day = isset($oi_info['c_start_int_day'])?$oi_info['c_start_int_day']:0;
            if($c_start_int_day == 0){
                $c_start_int_day = $buy_int_day;
            }
            $m_smh = new StudentMoneyHistory();

            $result = $m_smh->addStudentMoney(
                $oi_info['sid'],
                $oi_info['paid_amount'],
                $oi_info['dc_id'],
                $expire_int_day,
                $remark,
                $buy_int_day,
                $oi_info['oi_id'],
                $c_start_int_day,
                $oi_info['consume_type']
            );

            if(!$result){
                $this->rollback();
                return $this->user_error($m_smh->getError());
            }

            $w_oi['oi_id'] = $oi_info['oi_id'];
            $result = $this->m_order_item->save($update_oi,$w_oi);
            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('order_item');
            }
        }catch(Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();

        return true;

    }

    /**
     * 处理订单课时条目
     * @param  [type] $oi_info [description]
     * @return [type]          [description]
     */
    public function dealOrderLessonHoursItem($oi_info){
        if(empty($oi_info)){
            $oi_info = $this->getData();
        }

        $mStudentLesson = new StudentLesson();
        if(isset($oi_info['from_lid']) && $oi_info['from_lid'] > 0){
            $result = $mStudentLesson->upgradeLessonHours($oi_info);
            if (false === $result){
                return $this->user_error($mStudentLesson->getError());
            }

            return true;
        }

        $result = $mStudentLesson->addByOrderItem($oi_info);

        if(!$result){
            return $this->user_error($mStudentLesson->getError());
        }

        return $result;
    }

    //创建单个项目
    public function  createItem($data)
    {
        $this->startTrans();
        try {

            if(isset($data['paid_amount']) && $data['paid_amount'] > 0) {
                //--1-- **只有订单付款超过0才能进行学生课程和物品减少操作**
                $data = $this->dealLessonAndMaterialIfPaid($data);
                if($data === false) throw new FailResult($this->getErrorMsg());

            } elseif (isset($data['order_from_assign_class']) && $data['order_from_assign_class']) {
                //--2-- 如果是分班自动创建的订单，仍然要增加student_lesson
                $data = $this->addStudentLessonFromAssignClass($data);
                if($data === false) throw new FailResult($this->getErrorMsg());
            }

            //换算课时、课次数量
            if(isset($data['gtype']) && $data['gtype'] === 0 && isset($data['lid'])) {
                $lesson = Lesson::get(['lid' => $data['lid']]);
                $data = (new StudentLesson())->calcLessonTimesAndHour($lesson, $data, $data['nums']);
            }
            $data = $this->calcUnitLessonHourAmount($data);
            $rs = (new self())->validate()->allowField(true)->save($data);
            if (!$rs) exception($this->getErrorMsg());

            $oi_id = $this->getLastInsID();
            $this->oi_id = $oi_id;

            ////记录课时变化
            //if(isset($data['sl_id']) && $data['sl_id'] > 0) {
            //    StudentLessonHourDetail::recordStudentLessonVariation($data['sl_id'], 1, 0, $oi_id);
            //}

            //缓存新增付款，作为后续收据的数据
            $data['paid_amount'] = isset($data['paid_amount']) ? $data['paid_amount'] : 0;
            redis()->hSet('update_order_item_info_' . $oi_id, 'add_paid_amount', $data['paid_amount']);
            $this->commit();

        } catch (FailResult $e) {
            $this->rollback();
            return $this->user_error(['msg' => $e->getMessage(), 'trace' => $e->getTrace()]);
        } catch(Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return $rs;
    }

    //计算order_item中的单位课时金额，用于考勤课耗
    public function calcUnitLessonHourAmount(&$data) {
        $data['origin_lesson_hours'] = isset($data['origin_lesson_hours']) ? $data['origin_lesson_hours'] : 0;
        $data['present_lesson_hours'] = isset($data['present_lesson_hours']) ? $data['present_lesson_hours'] : 0;
        $data['total_lesson_hours'] = $data['origin_lesson_hours'] + $data['present_lesson_hours'];
        $data['subtotal'] = isset($data['subtotal']) ? $data['subtotal'] : 0;

        $data['unit_lesson_hour_amount'] = $data['total_lesson_hours'] > 0
            ? round(($data['subtotal'] / $data['total_lesson_hours']), 6) : 0;

        return $data;
    }

    /**
     * @desc  分班时，学员没有付款仍然要创建一个student_lesson
     */
    public function addStudentLessonFromAssignClass($data)
    {
        $this->startTrans();
        try {
            if (!isset($data['gtype']) || !isset($data['nums']) || !isset($data['sid'])) {
                throw new FailResult('处理订单后续事务，参数错误');
            }

            //--1-- 如果是课程
            if ($data['gtype'] === 0) {
                $lesson = Lesson::get(['lid' => $data['lid']]);
                $student_lesson_model = new StudentLesson();

                $data['lesson_type'] = $lesson->lesson_type;
                $data = $student_lesson_model->calcLessonTimesAndHour($lesson, $data, $data['nums']);

                //$student_lesson = $student_lesson_model->getInfoBySidAndLid($data['sid'], $data['lid']);
                $student_lesson = $student_lesson_model
                    ->canAddStudentLesson($data['sid'], $data['lid'], isset($data['cid']) ? $data['cid'] : 0);
                //--1.1-- 如果不存在学生课程则新增，否则增加课时
                if (!($student_lesson instanceof StudentLesson)) {
                    $data = $student_lesson_model->getNeedAcNums($lesson, $data);
                    $sl_id = $student_lesson_model->createOneItem($data);
                    if (!$sl_id) throw new FailResult($student_lesson_model->getErrorMsg());
                } else {
                    //--1.2-- 如果之前没有选择班级，重新添加班级
                    if ($student_lesson->cid <= 0 && isset($data['cid']) && $data['cid'] > 0) {
                        $rs = $student_lesson->updateClass($student_lesson, $data['cid']);
                        if ($rs === false) throw new FailResult($student_lesson->getErrorMsg());
                    }
                    $sl_id = $student_lesson->sl_id;
                }

                $data['sl_id'] = $sl_id;
            }

            $this->commit();
        } catch (FailResult $e) {
            $this->rollback();
            return $this->user_error($e->getMessage());
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return $data;
    }

    /**
     * @desc  订单付款后，学员增加课时、物品进出库
     * @author luo
     * @param $data
     * @param int $is_add_payment 分班自动下班后会自动创建订单和student_lesson, 再付款就不增加student_lesson
     */
    public function dealLessonAndMaterialIfPaid($data, $is_add_payment = false)
    {
        $this->startTrans();
        try {
            if (!isset($data['gtype']) || !isset($data['nums']) || !isset($data['sid'])) {
                throw new FailResult('处理订单后续事务，参数错误');
            }

            //--1-- 如果是课程
            if ($data['gtype'] === 0) {
                $lesson = Lesson::get(['lid' => $data['lid']]);
                $student_lesson_model = new StudentLesson();

                $data['lesson_type'] = $lesson->lesson_type;
                $data = $student_lesson_model->calcLessonTimesAndHour($lesson, $data, $data['nums']);

                //$student_lesson = $student_lesson_model->getInfoBySidAndLid($data['sid'], $data['lid']);
                $student_lesson = $student_lesson_model
                    ->canAddStudentLesson($data['sid'], $data['lid'], isset($data['cid']) ? $data['cid'] : 0);

                //--1.1-- 如果不存在学生课程则新增，否则增加课时
                if (!($student_lesson instanceof StudentLesson)) {
                    $data = $student_lesson_model->getNeedAcNums($lesson, $data);
                    $sl_id = $student_lesson_model->createOneItem($data);
                    if (!$sl_id) throw new FailResult($student_lesson_model->getErrorMsg());
                } else {
                    if(!$is_add_payment) {
                        //--1.2-- 如果之前没有选择班级，重新添加班级
                        if ($student_lesson->cid <= 0 && isset($data['cid']) && $data['cid'] > 0) {
                            $rs = $student_lesson->updateClass($student_lesson, $data['cid']);
                            if ($rs === false) throw new FailResult($student_lesson->getErrorMsg());
                        }
                        $rs = $student_lesson->addTimes($student_lesson, $data);
                        if ($rs === false) exception($student_lesson->getErrorMsg());
                    }
                    $sl_id = $student_lesson->sl_id;

                }

                $data['sl_id'] = $sl_id;
            }

            //--2-- 处理物品
            if ($data['gtype'] === 1 && isset($data['gid']) && $data['gid'] > 0) {
                $rs = $this->orderMaterial($data);
                if ($rs === false) throw new FailResult($this->getErrorMsg());
                if (isset($rs['is_deliver'])) $data['is_deliver'] = $rs['is_deliver'];
            }

            $this->commit();
        } catch (FailResult $e) {
            $this->rollback();
            return $this->user_error($e->getMessage());
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return $data;
    }

    //物品下单
    public function orderMaterial($data)
    {
        $outcome = ['is_deliver' => 1]; # 是否已经发放，如果物品库存不足，则未发放

        $this->startTrans();
        try {
            if (!isset($data['gid']) || $data['gid'] <= 0) exception('没有物品ID');
            $mt_id = $data['gid'];
            $ms_id = Branch::getMsId(request()->bid);
            if($ms_id <= 0) throw new FailResult('该校区还没有相关仓库，无法处理物品');

            $is_enough = (new MaterialStoreQty())->isEnoughMaterial($ms_id, $mt_id, $data['nums']);
            if ($is_enough === false) $outcome['is_deliver'] = 0;

            $material_history_data = [
                'mt_id' => $mt_id,
                'ms_id' => $ms_id,
                'num'   => $data['nums'],
                'int_day' => date('Ymd', time()),
                'type'  => MaterialHistory::TYPE_OUT,
                'cate'  => MaterialHistory::CATE_ORDER,
                'remark' => '订单购买'.Order::getOrderNo(isset($data['oid']) ? $data['oid'] : 0)
            ];

            $m_mh = new MaterialHistory();
            $rs = $m_mh->addOneHis($material_history_data);
            if ($rs === false) throw new FailResult($m_mh->getErrorMsg());

            $this->commit();
        } catch (FailResult $e) {
            $this->rollback();
            return $this->user_error($e->getMessage());
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return $outcome;
    }



    /**
     * 重置order_item的分班状态
     * 在班级中删除一个学生后，让该学生的order_item ac_status 改为未分配班级状态
     * @param $oi_id order_item主键id
     */
    public static function resetAcStatus($oi_id)
    {
        $data['oi_id']     = $oi_id;
        $data['ac_status'] = 0;
        self::update($data);
    }

    public static function getOiId(array $where) {
        $item = self::get(function($query) use($where) {
            $query->field('oi_id')->where($where);
        });

        if(empty($item)) return (new static())->user_error('不存在此订单项目');

        $oi_id = $item->oi_id;

        return $oi_id;
    }

    //根据班级生成订单项目数据
    public static function makeItemDataFromClass(Classes $class)
    {
        $lesson = $class->lesson;
        if(empty($lesson)) exception('课程不存在');

        $data['lid'] = $class->lid;
        $data['gtype'] = 0;
        $data['nums'] = ($class->lesson_times - $class->lesson_index) >= 0 ? ($class->lesson_times - $class->lesson_index) : 0;
        $data['nums_unit'] = $lesson->price_type;
        $data['origin_price'] = $lesson->unit_price;
        $data['price'] = $lesson->unit_price;
        $data['origin_amount'] = $data['nums'] * $lesson->unit_price;
        $data['origin_lesson_times'] = $lesson->lesson_nums;
        $data['cid'] = $class->cid;
        $data['ac_status'] = StudentLesson::AC_STATUS_ALL;
        $data['need_ac_nums'] = $lesson->ac_class_nums;
        $data['ac_nums'] = 1;
        $data['lesson_status'] = $class->lesson_index > 0 ? StudentLesson::LESSON_STATUS_ING : StudentLesson::LESSON_STATUS_NO;
        $data['order_from_assign_class'] = true;

        $data = StudentLesson::calcLessonTimesAndHour($lesson, $data, $data['nums']);

        return $data;
    }

    //根据班级生成订单项目数据
    public static function makeItemMaterialDataFromClass(Classes $class)
    {
        $lesson = $class->lesson;
        if(empty($lesson)) exception('课程不存在');

        $outcome = [];

        $lesson_material_list = (new LessonMaterial())->where('lid', $lesson['lid'])->select();
        if(empty($lesson_material_list)) return [];

        foreach($lesson_material_list as $lesson_material) {
            //--1-- 是否存在这个物品
            $material = Material::get(['mt_id' => $lesson_material['mt_id']]);
            if(empty($material) || $lesson_material['default_num'] <= 0) continue;

            //--2-- 当前仓库数量是否充足
            $ms_id = Branch::getMsId(request()->bid);
            $is_enough = (new MaterialStoreQty())
                ->isEnoughMaterial($ms_id, $material['mt_id'], $lesson_material['default_num']);

            $nums = $lesson_material['default_num'] >= 0 ? $lesson_material['default_num'] : 0;
            $data = [
                'gtype' => OrderItem::GTYPE_GOODS,
                'gid' => $material['mt_id'],
                'is_deliver' => $is_enough ? 1 : 0,
                'nums' => $nums,
                'origin_price' => $material['sale_price'],
                'price' => $material['sale_price'],
                'origin_amount' => $nums * $material['sale_price'],
            ];

            array_push($outcome, $data);

        }

        return $outcome;
    }

    /*
     * 描述：计算订单项目剩余课次数，次数使用情况
     * 公式：购买的数量 - 考勤使用的次数 - 结转的次数
     */
    public static function getItemNumsCondition(OrderItem $item)
    {
        $sl_id = $item->sl_id;
        $model = new self();

        $total_nums = 0;
        $total_used_nums = 0;
        $total_back_nums = 0;
        if($sl_id > 0) {
            //--1-- 取得所有课程相同的item
            $item_list = OrderItem::all(function($query) use($sl_id){
                $query->where('sl_id', $sl_id)->order('create_time', 'asc');
            });
            $student_lesson = StudentLesson::get($sl_id);
            if($student_lesson){
                $total_used_nums = $student_lesson->use_lesson_hours;
                $total_back_nums =  $student_lesson->refund_lesson_hours + $student_lesson->transfer_lesson_hours;
                $total_nums = $total_used_nums + $total_back_nums;
            }
        } else {
            $oi_id = $item->oi_id;
            //--2-- 取物品的item
            $item_list = OrderItem::all(function($query) use($oi_id){
                $query->where('oi_id', $oi_id)->order('create_time', 'asc');
            });
        }


        foreach($item_list as $key => $per_item) {
            //--1.1-- 求结转次数
            $per_item['transfer_nums'] = OrderTransferItem::getTransferNumByOiId($per_item['oi_id']);
            //--1.2-- 求退款次数
            $per_item['refund_nums'] = OrderRefundItem::getRefundNumByOiId($per_item['oi_id']);
            //--1.3-- 求使用次数
            $refund_and_transfer_nums = $per_item['transfer_nums'] + $per_item['refund_nums'];

            //--1.4-- 求考勤次数
            $per_item['remain_nums'] = $per_item['nums'] + $per_item['present_lesson_hours'] - $refund_and_transfer_nums;

            $per_item['used_nums'] = 0;


            //--1.5-- 考勤次数先分配给先下的订单，直到分完为至
            if($per_item['remain_nums'] > 0 && $total_used_nums > 0) {
                if($total_used_nums > $per_item['remain_nums']){
                    $per_item['used_nums'] = $per_item['remain_nums'];
                    $per_item['remain_nums'] = 0;
                    $total_used_nums -= $per_item['used_nums'];
                }else{
                    $per_item['used_nums'] = $total_used_nums;
                    $per_item['remain_nums'] = $per_item['remain_nums'] - $total_used_nums;
                    $total_used_nums = 0;
                }
            }

            //前端剩余课时不包括赠送的课时数量
            $per_item['remain_nums'] = $per_item['remain_nums'] - $per_item['present_lesson_hours'];

            if($per_item['oi_id'] == $item['oi_id']) {
                return $per_item;
            }
        }



        return $item;
    }

    //计算总的课耗  20180507废弃，主要是student_lesson中加了transfer_lesson_hours,refund_lesson_hours
    public function getTotalUsedNum(OrderItem $item) {
        $sl_id = $item->sl_id;

        if($sl_id <= 0) return 0;

        $student_lesson = StudentLesson::get(['sl_id' => $sl_id]);

        $use_nums = $student_lesson->getTotalUseNums($student_lesson);  //总的课耗
        $total_transfer_num = $this->getTotalTransferNum($item);       //结转数量
        $total_refund_num = $this->getTotalRefundNum($item);
        $use_nums = $use_nums - $total_transfer_num - $total_refund_num;
        return $use_nums;
    }

    //获取item的结转次数
    public function getTotalTransferNum(OrderItem $item)
    {
        //如果是物品
        if($item->gtype == self::GTYPE_GOODS) {
            $oi_ids = [$item->oi_id];
        }

        //如果是课程
        if($item->gtype == self::GTYPE_LESSON) {
            $sl_id = $item->sl_id;
            $oi_ids = (new OrderItem())->where('sl_id', $sl_id)->column('oi_id');
        }

        if(!isset($oi_ids) || empty($oi_ids)) return 0;

        $total_num = (new OrderTransferItem())->where('oi_id','in', $oi_ids)->sum('nums');
        return $total_num ? $total_num : 0;
    }

    //获取总的退款课时
    public function getTotalRefundNum(OrderItem $item)
    {
        //如果是物品
        if($item->gtype == self::GTYPE_GOODS) {
            $oi_ids = [$item->oi_id];
        }

        //如果是课程
        if($item->gtype == self::GTYPE_LESSON) {
            $sl_id = $item->sl_id;
            $oi_ids = (new OrderItem())->where('sl_id', $sl_id)->column('oi_id');
        }

        if(!isset($oi_ids) || empty($oi_ids)) return 0;

        $total_num = (new OrderRefundItem())->where('oi_id', 'in', $oi_ids)->sum('nums');
        return $total_num ? $total_num : 0;
    }

    //获取项目未付款
    public function getUnpaidAmount(OrderItem $item)
    {
        $unpaid_amount = $item->subtotal - $item->paid_amount;
        return $unpaid_amount > 0 ? $unpaid_amount : 0;
    }

    /**
     * 删除订单条目
     * @param $oi_id
     * @param bool $del_sl  是否删除关联课时记录
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 1. 删除item
     * 2. 删除student_lesson
     * 3. 删除物品记录
     * 4. 学生分班取消
     * 5. 物品进出库记录
     * 6. 仓库数量减少
     * 7. 物品总数减少
     */
    public function delOneItem($oi_id,$del_sl = true)
    {
        $order_item = $this->find($oi_id);
        if(empty($order_item)) return true;
        $order_item = $order_item->toArray();

        $this->startTrans();
        try {
            //--1-- 如果购买的课程
            if ($order_item['gtype'] === self::GTYPE_LESSON && $del_sl) {
                $rs = $this->delStudentLessonOfItem($order_item);
                if ($rs === false) throw new FailResult($this->getErrorMsg());
            }

            //--2-- 如果购买的物品
            if ($order_item['gtype'] === self::GTYPE_GOODS) {
                if($order_item['paid_amount'] > 0) {
                    $m_or = new OrderRefund();
                    $rs = $m_or->refundMaterial($order_item, $order_item['nums']);
                    if ($rs === false) throw new FailResult($m_or->getErrorMsg());
                }
            }

            $rs = $this->where('oi_id', $order_item['oi_id'])->delete();
            if($rs === false) throw new FailResult("订单项目删除失败");

            $this->commit();
        } catch (FailResult $e) {
            $this->rollback();
            return $this->user_error($e->getMessage());
        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        return true;
    }

    //删除订单项目相关的课程
    public function delStudentLessonOfItem($order_item)
    {
        $sl_oi_nums = $this->where('sl_id', $order_item['sl_id'])->count();

        $m_sl = new StudentLesson();
        $student_lesson = StudentLesson::get(['sl_id' => $order_item['sl_id']]);

        if(empty($student_lesson)) return true;

        $this->startTrans();
        try {
            //--1-- 如果有多个项目，只能把student_lesson的相关课次数减少
            if ($sl_oi_nums > 1) {
                $lesson = Lesson::get(['lid' => $student_lesson['lid']]);

                if (!empty($lesson) || !empty($order_item['cid'])) {
                    //--1.1-- 处理课次数
                    $order_item = $m_sl::calcLessonTimesAndHour($lesson, $order_item, $order_item['nums'], null, $order_item['cid']);
                    $rs = $m_sl->handleStudentLessonHours($student_lesson, $order_item, StudentLesson::HANDLE_STUDENT_LESSON_HOURS_DELETE_BILL);
                    if ($rs === false) throw new FailResult($m_sl->getErrorMsg());
                }

                //--2-- 如果只有一个项目，则直接删除student_lesson
            } else {
                if($student_lesson['import_lesson_hours'] > 0){
                    $lesson = Lesson::get(['lid' => $student_lesson['lid']]);

                    if (!empty($lesson) || !empty($order_item['cid'])) {
                        //--1.1-- 处理课次数
                        $order_item = $m_sl::calcLessonTimesAndHour($lesson, $order_item, $order_item['nums'], null, $order_item['cid']);
                        $rs = $m_sl->handleStudentLessonHours($student_lesson, $order_item, StudentLesson::HANDLE_STUDENT_LESSON_HOURS_DELETE_BILL);
                        if ($rs === false) throw new FailResult($m_sl->getErrorMsg());
                    }
                }else {
                    if ($student_lesson['cid'] > 0) {
                        //移除分班
                        $class_student = ClassStudent::get(['cid' => $student_lesson['cid'], 'sid' => $student_lesson['sid']]);
                        if (!empty($class_student)) {
                            $rs = $class_student->removeStudentFromClass($class_student['cs_id']);
                            if ($rs === false) throw new FailResult($class_student->getErrorMsg());
                        }
                    }

                    $result = $student_lesson->remove();
                    if (false === $result) {
                        throw new FailResult($student_lesson->getError());
                    }
                }
            }

            $result = $this->m_student->updateLessonHours($order_item['sid']);

            if(!$result){
                throw new FailResult($this->m_student->getError());
            }
            $this->commit();
        } catch (FailResult $e) {
            $this->rollback();
            return $this->user_error($e->getMessage());
        } catch (Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        return true;
    }

    /**
     * 计算某一个order_item的课次单价
     * 原则：课耗是按课次算的，一个课次可以有多个课时
     * 补充：定义课程的时候 单价可以是按课次，也可以是按课时。
     *
     * @return float
     */
    public function getLessonTimesUnitPrice($index)
    {
        $data = $this->getData();
        $consume_type = user_config('params.present_lesson_consume_method');//后台配置
        $nums_unit    = $this->getData('nums_unit'); /*数量单位(0为物品的数量单位,1为课次,2为课时,3为月按时间*/
        $oi_price     = $this->getData('price');
        $reduced_amount = $this->getData('reduced_amount');
        $st_lesson    = $this->getAttr('student_lesson');

        $onetime_lesson_hours = $st_lesson['lesson']['unit_lesson_hours'];
        $unit_lesson_hour_amount = $this->getData('unit_lesson_hour_amount');

        if($unit_lesson_hour_amount == 0){  //如果是0，那么计算出来
            $data = $this->calcUnitLessonHourAmount($data);
            $unit_lesson_hour_amount = $data['unit_lesson_hour_amount'];
        }


        $oi_origin_lesson_times   = $this->getData('origin_lesson_times');
        $oi_present_lesson_times = $this->getData('present_lesson_times');

        
        $price = $unit_lesson_hour_amount * $onetime_lesson_hours;


        if($oi_present_lesson_times > 0){
            if($nums_unit == self::UNIT_LESSON_HOURS){
                $one_time_price        = $oi_price * $onetime_lesson_hours;
            }else{
                $one_time_price        = $oi_price;
            }
           

            if($reduced_amount > 0){
                $one_time_price = $one_time_price - round($reduced_amount / $oi_origin_lesson_times,6);
            }

            $price = $one_time_price;

            if($consume_type == 1){
                //先消耗正常课次
                if($index > $oi_origin_lesson_times){
                    $price = 0.000000;
                }
            }elseif($consume_type == 3){
                if($index <= $oi_present_lesson_times){
                    $price = 0.000000;
                }
            }
        }
        
        return $price;
    }

    /**
     * 获取order_item退费的课次
     */
    public function getRefundTimes()
    {
        $list = $this->getAttr('order_refund_item');
        $nums = 0;
        if (empty($list)) {
            return $nums;
        }
        foreach ($list as $item) {
            $nums += $item['nums'];
        }
        $nums_unit = $this->getAttr('nums_unit');
        if ($nums_unit == self::UNIT_LESSON_TIMES) {
            return $nums;
        } else {
            $unit_lesson_hours = Lesson::get($this->getData('lid'))['unit_lesson_hours'];
            return ceil($nums / $unit_lesson_hours);
        }
    }

    /**
     * 获取order_item结转的课次
     */
    public function getTransferTimes()
    {
        $list = $this->getAttr('order_transfer_item');
        $nums = 0;
        if (empty($list)) {
            return $nums;
        }
        foreach ($list as $item) {
            $nums += $item['nums'];
        }
        $nums_unit = $this->getAttr('nums_unit');
        if ($nums_unit == self::UNIT_LESSON_TIMES) {
            return $nums;
        } else {
            $unit_lesson_hours = Lesson::get($this->getData('lid'))['unit_lesson_hours'];
            return ceil($nums / $unit_lesson_hours);
        }
    }

    /**
     * 预充值返还
     */
    public function rollbackDebit(){
        $oi_info = $this->getData();
        $sid = $oi_info['sid'];
        $student_info = get_student_info($sid);

        if($oi_info['paid_amount'] > $student_info['money']){
            return $this->user_error('学员剩余余额不够扣除，无法撤销收费项,请确认储值以后是否有使用余额!');
        }

        $oi_id = $oi_info['oi_id'];
        $smh = StudentMoneyHistory::get(['oi_id'=>$oi_id]);
        if(!$smh){
            return $this->user_error('关联的预储值记录不存在,无法撤销收费项!');
        }

        $result = $smh->rollbackHistory();

        if(!$result){
            return $this->user_error($smh->getError());
        }

        return true;
    }

    public function undoDeductPresentLessonHours(OrderItem $order_item, $present_nums = 0)
    {
        $data = [
            'deduct_present_lesson_hours' => $order_item['deduct_present_lesson_hours'] - $present_nums,
        ];
        $w['oi_id'] = $order_item['oi_id'];
        $result = $this->save($data, $w);
        if (false === $result){
            return $this->sql_save_error('order_item');
        }

        return true;
    }

    public function update_transfer(OrderItem $oeder_item , $status = 0)
    {
        $update['is_lesson_hour_end'] = $status;
        $w['oi_id'] = $oeder_item['oi_id'];
        $result = $oeder_item->save($update,$w);
        if (false === $result){
            return $this->sql_save_error('order_item');
        }
    }

    /**
     * 撤销课程升级
     * @param $oi_id
     */
    public function undoUpgradeLessonHours($oi_id)
    {
        $mOrder_item = $this->find($oi_id);

        if ($mOrder_item['from_lid'] <= 0){
            return $this->user_error('该课程未升级');
        }

        $mStudentLesson = new StudentLesson();
        $result = $mStudentLesson->undoUpgradeLessonHours($oi_id,$mOrder_item['sl_id']);
        if (false === $result){
            return $this->user_error($mStudentLesson->getError());
        }

        return true;
    }

    /**
     * 批量转介绍设置
     * @param $oi_ids
     * @param $referer_sid
     * @param $referer_teacher_id
     * @param $referer_eid
     */
    public function patchDoReferer($oi_ids = [],$referer_sid = 0, $referer_teacher_id = 0, $referer_eid = 0)
    {
        if(empty($oi_ids)){
            return $this->input_param_error('oi_ids');
        }

        $this->startTrans();
        try {
            foreach ($oi_ids as $oi_id){
                $result = $this->doReferer($oi_id,$referer_sid,$referer_teacher_id,$referer_eid);
                if (false === $result){
                    $this->rollback();
                    return $this->user_error($this->getError());
                }
            }
        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    /**
     * 转介绍设置
     * @param $oi_id
     * @param $referer_sid
     * @param $referer_teacher_id
     * @param $referer_eid
     */
    public function doReferer($oi_id,$referer_sid,$referer_teacher_id,$referer_eid,$is_referer = 1)
    {
        if ($referer_sid == 0 && $referer_teacher_id == 0 && $referer_eid == 0){
            $is_referer = 0;
        }
        $w['oi_id'] = $oi_id;
        $update = [
            'is_referer' => $is_referer,
            'referer_sid' => $referer_sid,
            'referer_teacher_id' => $referer_teacher_id,
            'referer_eid' => $referer_eid
        ];

        $this->startTrans();
        try {
            $result = $this->save($update,$w);
            if (false === $result){
                $this->rollback();
                return $this->sql_save_error('order_item');
            }
        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    /**
     * 修改付款金额
     * @param $oi_id
     * @param $update_amount
     */
    public function updatePaidAmount($oi_id,$update_amount)
    {
        $order_item = $this->where('oi_id',$oi_id)->find();
        if (empty($order_item)){
            return $this->user_error('订单项目不存在！');
        }

        $update['paid_amount'] = $order_item['paid_amount'] + $update_amount;
        $w['oi_id'] = $oi_id;
        $result = $this->save($update,$w);
        if (false === $result){
            return $this->sql_save_error('order_item');
        }

        if ($order_item['sl_id'] > 0 && $order_item['gtype'] == OrderItem::GTYPE_LESSON){
            $student_lesson =StudentLesson::get($order_item['sl_id']);
            $result = $student_lesson->updateLessonAmount();
            if (false === $result){
                return $this->user_error($student_lesson->getError());
            }

            $Mslh = new StudentLessonHour();
            $lesson_ampunt =
            $result = $Mslh->updateLessonAmount($order_item['sl_id'],$order_item['oi_id']);
            if (false === $result){
                return $this->user_error($Mslh->getError());
            }
        }

        return true;
    }
}