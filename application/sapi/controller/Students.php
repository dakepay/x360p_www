<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/27
 * Time: 11:44
 */

namespace app\sapi\controller;

use app\sapi\model\Order;
use app\sapi\model\OrderItem;
use app\sapi\model\OrderReceiptBill;
use app\sapi\model\OrderReceiptBillItem;
use app\sapi\model\OrderRefund;
use app\sapi\model\OrderRefundItem;
use app\sapi\model\OrderTransfer;
use app\sapi\model\OrderTransferItem;
use app\sapi\model\PublicSchool;
use app\sapi\model\Student;
use app\sapi\model\StudentCreditHistory;
use app\sapi\model\StudentMoneyHistory;
use app\sapi\model\User;
use app\sapi\model\UserStudent;
use think\Request;

class Students extends Base
{

    /**
     * @desc  帐号相关的学生
     * @author luo
     * @method GET
     */
    public function get_list(Request $request)
    {
        $uid = input('uid/d');
        $mUserStudent = new UserStudent();
        $mStudent = new Student();
        $w_s['first_uid|second_uid'] = $uid;
        $student_list = $mStudent->where($w_s)->select();
        $sids = $mUserStudent->where('uid', $uid)->column('sid');

        foreach($student_list as &$student){
            $student['school_id_text'] = PublicSchool::getSchoolIdText((string)$student['school_id']);
            if(!in_array($student['sid'],$sids)){
                $new_us = [];
                $new_us['sid'] = $student['sid'];
                $new_us['uid'] = $uid;
                $new_us['og_id'] = $student['og_id'];
                $mUserStudent->data([])->isUpdate(false)->save($new_us);
            }
        }


        return $this->sendSuccess(['list' => $student_list]);
    }

    public function get_detail(Request $request, $id = 0)
    {
        $student_obj = Student::get($id);

        return $this->sendSuccess($student_obj);
    }

    public function bind(Request $request)
    {
        $sids = $request->post('sids/a');
        if (empty($sids) || !is_array($sids)) {
            return $this->sendError(400, '参数sids不合法');
        }
        $student_list = Student::all($sids);
        if (count($student_list) !== count($sids)) {
            return $this->sendError(400, '档案不存在或已删除');
        }
        $mobile = $request->user->mobile;
        foreach ($student_list as $item) {
            if ($mobile !== $item->first_tel && $mobile !== $item->second_tel) {
                return $this->sendError(400, '手机号码不匹配');
            }
        }
        $m_user = (new StudentUser)->find($request->user->uid);
        $m_user->students()->saveAll($sids);
        return $this->sendSuccess();
    }

    public function post(Request $request)
    {
        $input = $request->post();
        $input = array_filter($input);
        $rule = [
            ['student_name|学生姓名', 'require|max:32'],
            ['nick_name|英文名', 'max:32'],
            ['sex|性别', 'in:0,1,2'],
            ['photo_url|头像地址', 'max:255'],
            ['birth_time|出生日期', 'date'],
            ['school_grade|学校年级', 'number'],
            ['school_class|学校班级', 'max:32'],
            ['school_id|学校ID', 'number'],
        ];
        /* 同一个user的student姓名不能重复 */
        $user = User::get(login_info('uid'));
        $student_names = $user->students()->alias('s')->column('s.student_name');
        if (isset($input['student_name']) && in_array($input['student_name'], $student_names)) {
            return $this->sendError(400, '同一个用户的学生姓名不能重复');
        }

        $result = $this->validate($input, $rule);
        if ($result !== true) {
            return $this->sendError(400, $result);
        }
        $m_student = new Student();
        $m_student->createOneStudent($input, $user);

        return $this->sendSuccess();

    }


    /**
     * @desc  修改学员资料
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function put(Request $request)
    {
        $sid = input('sid/d');
        $uid = login_info('uid');
        $input = $request->put();

        $student = Student::get($sid);
        if (!$student) {
            return $this->sendError(400, '该学生档案不存在或已删除');
        }

        $is_exist = UserStudent::get(['sid' => $sid, 'uid' => $uid]);
        if(empty($is_exist)) return $this->sendError(400, '不属于你名下的学生');

        if(isset($input['school_id'])) {
            $input['school_id'] = PublicSchool::findOrCreate($input['school_id']);
        }

        $rule = [
            ['student_name|学生姓名', 'require|max:32'],
            ['photo_url|头像地址', 'max:255'],
            ['birth_time|出生日期', 'date'],
            ['school_grade|学校年级', 'number'],
            ['school_class|学校班级', 'max:32'],
            ['school_id|学校ID', 'number'],
        ];

        $result = $this->validate($input, $rule);
        if ($result !== true) {
            return $this->sendError(400, $result);
        }

        $rs = $student->updateStudent($input, $sid, $student);
        if($rs === false) return $this->sendError(400, $student->getErrorMsg());
        
        return $this->sendSuccess();
    }

    /**
     * @desc  电子帐户记录
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function get_list_money_history(Request $request)
    {
        $sid = input('id/d');
        $input = $request->get();
        $where = [];
        if(isset($input['year_month'])) {
            $where['create_time'] = where_month($input['year_month']);
        }
        $m_smh = new StudentMoneyHistory();
        $ret = $m_smh->where($where)->where('sid', $sid)->getSearchResult($input);

        $list = [];
        foreach ($ret['list'] as $row) {
            //取出相关的order_item信息
            switch ($row['business_type']) {
                case $m_smh::BUSINESS_TYPE_TRANSFORM:   # 结转
                    $transfer = OrderTransfer::get($row['business_id']);
                    if(empty($transfer)) break;

                    $oi_ids = (new OrderTransferItem())->where('ot_id', $transfer['ot_id'])->column('oi_id');
                    if(empty($oi_ids)) break;

                    $order_items = OrderItem::all($oi_ids, ['material','lesson']);
                    break;

                case $m_smh::BUSINESS_TYPE_REFUND:  # 退款
                    $refund = OrderRefund::get($row['business_id']);
                    if(empty($refund)) break;

                    $oi_ids = (new OrderRefundItem())->where('or_id', $refund['or_id'])->column('oi_id');
                    if(empty($oi_ids)) break;

                    $order_items = OrderItem::all($oi_ids, ['material','lesson']);
                    break;

                case $m_smh::BUSINESS_TYPE_RECHARGE: # 充值
                    break;

                case $m_smh::BUSINESS_TYPE_ORDER: # 下单
                    $order = Order::get($row['business_id']);
                    if(empty($order)) break;

                    $oi_ids = (new OrderItem())->where('oid', $order['oid'])->column('oi_id');
                    if(empty($oi_ids)) break;

                    $order_items = OrderItem::all($oi_ids, ['material','lesson']);
                    break;
                case $m_smh::BUSINESS_TYPE_SUPPLEMENT:   # 补缴
                    $bill = OrderReceiptBill::get($row['business_id']);
                    if(empty($bill)) break;

                    $oi_ids = (new OrderReceiptBillItem())->where('orb_id', $bill['orb_id'])->column('oi_id');
                    if(empty($oi_ids)) break;

                    $order_items = OrderItem::all($oi_ids, ['material','lesson']);
                    break;
                default:
                    break;
            }

            $row['order_item'] = isset($order_items) ? $order_items : [];

            $day = date('Y-m', strtotime($row['create_time']));
            $list[$day][] = $row;
        }

        $ret['list'] = $list;

        return $this->sendSuccess($ret);
    }

    /**
     * @desc  学员积分记录
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function get_list_credit_history(Request $request)
    {
        $sid = global_sid();
        $input = $request->get();
        $m_sch = new StudentCreditHistory();
        $ret = $m_sch->where('sid', $sid)->getSearchResult($input);
        return $this->sendSuccess($ret);
    }


}