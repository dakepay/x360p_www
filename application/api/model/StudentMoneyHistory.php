<?php
/** 
 * Author: luo
 * Time: 2017-10-18 15:02
**/


namespace app\api\model;

class StudentMoneyHistory extends Base
{

    const BUSINESS_TYPE_TRANSFORM = 1;  //结转
    const BUSINESS_TYPE_UNTRANSFORM = 6;  //撤销结转
    const BUSINESS_TYPE_REFUND = 2;     //退款
    const BUSINESS_TYPE_RECHARGE = 3;   //充值
    const BUSINESS_TYPE_ORDER = 4;      //下单
    const BUSINESS_TYPE_SUPPLEMENT = 5; //订单续费
    const BUSINESS_TYPE_ADD = 11;       //手工增加
    const BUSINESS_TYPE_DEC = 12;       //手工减少
    const BUSINESS_TYPE_ROLLBACK = 13;  //撤销对冲

    const BUSINESS_TYPE_OUT = 14;   //转出
    const BUSINESS_TYPE_IN  = 15;   //转入
    const BUSINESS_TYPE_CONSUME = 16;   //违约课消
    const BUSINESS_TYPE_ATTENDANCE = 17;    //课时课消，从余额扣除
    const BUSINESS_TYPE_ATTENDACE_ROLLBACK = 18;    //课时课消撤销

    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function getAmountAttr($value,$data){
        return format_currency($value);
    }

    public function getAfterAmountAttr($value,$data){
        return format_currency($value);
    }

    public function getBeforeAmountAttr($value,$data){
        return format_currency($value);
    }
    /**
     * 获得合同号虚拟字段
     * @param $value
     * @param $data
     */
    public function getContractNoAttr($value,$data){
        $bid_no = str_pad($data['bid'],3,'0',STR_PAD_LEFT);
        $business_no = str_pad($data['smh_id'],8,'0',STR_PAD_LEFT);
        return sprintf("%s-%s",$bid_no,$business_no);
    }

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid');
    }

    public function createMoneyHistory($data)
    {
        $rs = $this->allowField(true)->isUpdate(false)->save($data);
        if(!$rs) return $this->user_error('创建电子钱包变动记录失败');

        return $this->smh_id;
    }

    /**
     * 撤回缴费记录
     */
    public function rollbackHistory(){
        $smh_info = $this->getData();
        $sid = $smh_info['sid'];
        $this->startTrans();
        try {
            if ($smh_info['sdc_id'] > 0) {
                $sdc = StudentDebitCard::get($smh_info['sdc_id']);

                if(!$sdc){
                    exception('关联的学员储值卡记录不存在,无法完成撤销操作!');
                }

                $result = $sdc->delete();
                if(false === $result){
                    $this->rollback();
                    return $this->sql_delete_error('student_debit_card');
                }
            }

            $student_info = get_student_info($sid);
            $m_student = new Student();
            if($student_info['money'] == $smh_info['after_amount']){
                //直接更新学员余额
                $update_student['money'] = $smh_info['before_amount'];
                $w_student['sid'] = $sid;

                $result = $m_student->save($update_student,$w_student);
                if(false === $result){
                    $this->rollback();
                    return $this->sql_save_error('student');
                }

                //直接删除
                $result = $this->delete();
                if(false === $result){
                    $this->rollback();
                    return $this->sql_delete_error('student_money_history');
                }
            }else{
                //通过添加余额变动记录来填平学员余额
                if($smh_info['after_amount'] > $smh_info['before_amount']){
                    $update_student['money'] = $student_info['money'] - $smh_info['amount'];
                }else{
                    $update_student['money'] = $student_info['money'] + $smh_info['amount'];
                }

                $w_student['sid'] = $sid;
                //fix;
                if($update_student['money'] < 0){
                    $update_student['money'] = 0;
                }

                $result = $m_student->save($update_student,$w_student);
                if(false === $result){
                    $this->rollback();
                    return $this->sql_save_error('student');
                }

                $new_smh['sid'] = $sid;
                $new_smh['remark'] = '撤销余额变动记录产生的对冲记录';
                array_copy($new_smh,$smh_info,['og_id','bid']);
                $new_smh['business_type'] = self::BUSINESS_TYPE_ROLLBACK;
                $new_smh['business_id'] = $smh_info['smh_id'];
                $new_smh['amount'] = $smh_info['amount'];
                $new_smh['before_amount'] = $student_info['money'];
                $new_smh['after_amount'] = $update_student['money'];

                $m_smh = new self();
                $result = $m_smh->data($new_smh)->save();
                if(!$result){
                    $this->rollback();
                    return $this->sql_add_error('student_money_history');
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
     * 添加学员的余额
     * @param $sid
     * @param $amount
     * @param int $dc_id
     * @param int $expire_int_day
     * @param string $remark
     * @param int $buy_int_day
     * @param int $oi_id
     */
    public function addStudentMoney($sid,
                                    $amount,
                                    $dc_id = 0,
                                    $expire_int_day=0,
                                    $remark = '',
                                    $buy_int_day = 0,
                                    $oi_id = 0,       //订单条目ID
                                    $c_start_int_day = 0,  //储值协议开始日期
                                    $consume_type = 0
                                ){
        $student_info = get_student_info($sid);

        if(!$student_info){
            return $this->user_error('sid参数错误!');
        }

        if($dc_id > 0){

            $m_dc = DebitCard::get($dc_id);
            if(!$m_dc){
                return $this->user_error('dc_id参数错误!');
            }
        }


        if($buy_int_day == 0){
            $buy_int_day = int_day(time());
        }

        $ret = ['smh_id'=>0,'dc_id'=>$dc_id,'sdc_id'=>0];

        $smh = [];
        $smh['business_type'] = self::BUSINESS_TYPE_RECHARGE;
        $smh['business_id'] = $dc_id;

        array_copy($smh,$student_info,['og_id','bid','sid']);

        $smh['before_amount'] = $student_info['money'];
        $smh['amount']        = $amount;
        $smh['after_amount']  = $student_info['money'] + $amount;

        $smh['remark'] = $remark;
        $smh['oi_id']  = $oi_id;
        $smh['c_start_int_day'] = format_int_day($c_start_int_day);
        $smh['c_end_int_day']   = format_int_day($expire_int_day);
        $smh['consume_type']   = $consume_type;

        $this->startTrans();
        try{

            $update_student = [];
            $update_student['money'] = $smh['after_amount'];

            if($dc_id > 0){
                $upgrade_vip_level = $m_dc['upgrade_vip_level'];
                $sdc = [];
                $sdc['dc_id'] = $dc_id;
                array_copy($sdc,$smh,['og_id','bid','sid']);
                $sdc['remain_amount'] = $amount;
                $sdc['is_used'] = 0;
                $sdc['buy_int_day'] = $buy_int_day;
                $sdc['expire_int_day'] = $expire_int_day;
                $sdc['upgrade_vip_level'] = $upgrade_vip_level;
                $sdc['oi_id'] = $oi_id;
                $m_sdc = StudentDebitCard::create($sdc);

                if(!$m_sdc->sdc_id){
                    $this->rollback();
                    return $this->sql_add_error('student_debit_card');
                }

                $ret['sdc_id'] = $m_sdc->sdc_id;
                $smh['sdc_id'] = $m_sdc->sdc_id;


                if($upgrade_vip_level > 0 && $student_info['vip_level'] < $upgrade_vip_level){
                    $update_student['vip_level'] = $upgrade_vip_level;
                }
            }

            //更新学员余额
            $m_student = new Student();
            $w_student['sid'] = $sid;
            $result = $m_student->save($update_student,$w_student);
            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('student');
            }

            $result = $this->data([])->isUpdate(false)->save($smh);
            if(!$result){
                $this->rollback();
                return $this->sql_add_error('student_money_history');
            }

            $smh['smh_id'] = $this->smh_id;
            $ret['smh_id'] = $this->smh_id;

        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        gvar('ret',$ret);
        return $ret;
    }

    /**
     * 获取打印数据
     * @param $smh_id
     * @return array|bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function makePrintData($smh_id)
    {
        $smh = $this->find($smh_id);
        if(empty($smh)) return $this->user_error('储值记录不存在');

        $smh->append(['contract_no']);

        $student_data = get_student_info($smh['sid']);
        $sys_data = user_config('params');
        $sys_data = [
            'org_name' => $sys_data['org_name'],
            'sysname' => $sys_data['sysname'],
            'address' => $sys_data['address'],
            'mobile' => $sys_data['mobile'],
        ];

        $sdc_info = [];
        $dc_info  = [];
        $op_info  = [];
        $smh_info = $smh->toArray();



        $op_info['name'] = '';
        $op_info['create_time'] = $smh['create_time'];
        $op_info['paid_time']   = $smh['create_time'];

        $w_e['uid'] = $smh['create_uid'];
        $employee_info = get_employee_info($w_e);
        if($employee_info){
            $op_info['name'] = $employee_info['ename'];
        }else{
            $user_info = get_user_info($w_e);
            $op_info['name'] = !empty($user_info['name'])?$user_info['name']:$user_info['account'];
        }

        if($smh->sdc_id > 0 ){
            $sdc = StudentDebitCard::get($smh->sdc_id);
            $sdc_info = $sdc->toArray();
            $dc = DebitCard::get($sdc->dc_id);
            $dc_info = $dc->toArray();
            $date_str = int_day_to_date_str($sdc_info['buy_int_day']);
            $arr_date_str = explode('-',$date_str);
            $sdc_info['buy_year'] = $arr_date_str[0];
            $sdc_info['buy_month'] = $arr_date_str[1];
            $sdc_info['buy_day'] = $arr_date_str[2];

            $date_str = int_day_to_date_str($sdc_info['expire_int_day']);
            $arr_date_str = explode('-',$date_str);
            $sdc_info['expire_year'] = $arr_date_str[0];
            $sdc_info['expire_month'] = $arr_date_str[1];
            $sdc_info['expire_day'] = $arr_date_str[2];
        }else{
            $sdc_info = [];
            $params = user_config('params.student_signup');
            if(!isset($smh_info['c_start_int_day']) || $smh_info['c_start_int_day'] == 0){
                $create_time = strtotime($smh_info['create_time']);
                $end_time = strtotime("+".$params['precharge_contract_month']." months",$create_time);
                $date_str1 = date('Y-m-d',$create_time);
                $date_str2 = date('Y-m-d',$end_time);
            }else{
                $date_str1 = int_day_to_date_str($smh['c_start_int_day']);
                $date_str2 = int_day_to_date_str($smh['c_end_int_day']);
            }
            $arr_date_str = explode('-',$date_str1);
            $sdc_info['buy_year'] = $arr_date_str[0];
            $sdc_info['buy_month'] = $arr_date_str[1];
            $sdc_info['buy_day'] = $arr_date_str[2];

            $arr_date_str = explode('-',$date_str2);
            $sdc_info['expire_year'] = $arr_date_str[0];
            $sdc_info['expire_month'] = $arr_date_str[1];
            $sdc_info['expire_day'] = $arr_date_str[2];
        }

        if($smh_info['oi_id'] > 0){
            $oi_info = get_order_item_info($smh_info['oi_id']);
            if($oi_info) {
                $w_oph['oid'] = $oi_info['oid'];
                $oph_info = get_oph_info($w_oph);
                if($oph_info){
                    $op_info['paid_time'] = date('Y-m-d',$oph_info['paid_time']);
                }
            }
        }


        if(intval(date('Ymd',$student_data['create_time'])) == intval(int_day(time()))){
            $student_data['is_new'] = 1;
        }else{
            $student_data['is_new'] = 0;
        }

        $student_data['sex'] = get_sex($student_data['sex']);
        $student_data['school_name'] = '';
        if($student_data['school_id'] > 0){
            $ps_info = get_public_school_info($student_data['school_id']);
            $student_data['school_name'] = $ps_info['school_name'];
        }
        $student_data['first_family_rel'] = get_family_rel($student_data['first_family_rel']);

        $sys_data['branch_name'] = get_branch_name($smh_info['bid']);


        $diy_vars = get_print_vars($smh_info['bid']);

        $lesson_price = [];

        $w_lp['dtype'] = 1;
        $w_lp['is_delete'] = 0;
        $w_lp['og_id'] = gvar('og_id');

        $lp_list = db('lesson_price_define')->where($w_lp)->select();


        if($lp_list){
            $lesson_price = [];
            $bid = $smh_info['bid'];
            $com_id = get_dept_id_by_bid($bid);
            foreach($lp_list as $lp){
                $arr_bids = explode(',',$lp['bids']);
                $arr_dept_ids = explode(',',$lp['dept_ids']);
                if(in_array($com_id,$arr_dept_ids) || in_array($bid,$arr_bids)){
                    $lesson_price['d'.$lp['sj_id']] = $lp;
                }
            }

        }

        $w_lp['dtype'] = 2;

        $lp_list = db('lesson_price_define')->where($w_lp)->select();

        if($lp_list){
            if(is_object($lesson_price)){
                $lesson_price = [];
            }
            $bid = $smh_info['bid'];
            $com_id = get_dept_id_by_bid($bid);
            foreach($lp_list as $lp){
                $arr_bids = explode(',',$lp['bids']);
                $arr_dept_ids = explode(',',$lp['dept_ids']);
                if(in_array($com_id,$arr_dept_ids) || in_array($bid,$arr_bids)){
                    $lesson_price['l'.$lp['product_level_did']] = $lp;
                }
            }
        }

        $data = [
            'diy'       => $diy_vars,
            'sys'       => $sys_data,
            'student'   => $student_data,
            'smh'       => $smh_info,
            'sdc'       => $sdc_info,
            'dc'        => $dc_info,
            'op'        => $op_info,
            'lp'        => $lesson_price
        ];

        return $data;
    }


}