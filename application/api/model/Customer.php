<?php
/**
 * Author: luo
 * Time: 2017-10-11 10:00
**/

namespace app\api\model;

use app\common\exception\FailResult;
use Overtrue\Pinyin\Pinyin;
use think\Exception;

class Customer extends Base
{

    const STATUS_DID_SUCCESS = 113;
    const STATUS_DID_NOT_VISIT = 114;
    const STATUS_DID_LISTEN = 115;
    const STATUS_DID_VISITED = 159;

    const INPUT_FROM_HANDWORK = 0;
    const INPUT_FROM_SCAN_CODE = 1;

    protected $auto = ['birth_year', 'birth_month', 'birth_day','pinyin', 'pinyin_abbr'];
    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];
    protected $type = [
        'next_follow_time' => 'timestamp',
        'last_follow_time' => 'timestamp',
        'get_time'  => 'timestamp'
    ];

    public function getGetTimeAttr($value)
    {
        if($value == 0){
            return '';
        }
        return date('Y-m-d',$value);
    }

    public function setGetTimeAttr($value)
    {
        if($value == '1970-01-01'){
            return 0;
        }
        if(preg_match('/^\d{4}-\d{2}-\d{2}$/',$value)){
            return strtotime($value);
        }elseif(is_numeric($value)){
            return intval($value);
        }
        return 0;
    }

    protected $append = ['un_follow_days'];

    protected static function init()
    {
        parent::init();

        Customer::event('after_insert', function($model) {
            if (!$model instanceof Customer) {
                return false;
            }
            if (isset($model['customer_status_did'])) {
                $temp = [];
                $temp['og_id'] = $model['og_id'];
                $temp['bid']   = $model['bid'];
                $temp['cu_id']      = $model['cu_id'];
                $temp['follow_eid'] = isset($model['follow_eid']) ? $model['follow_eid'] : 0;
                $temp['old_value']  = $model['customer_status_did'];
                CustomerStatusConversion::create($temp);
            }
            return true;
        });

        Customer::event('after_update', function($model) {
            if (!$model instanceof Customer) {
                return false;
            }
            $changed_data = $model->getChangedData();
            if (isset($changed_data['customer_status_did'])) {
                $temp = [];
                $temp['cu_id']      = $model['cu_id'];
                $temp['follow_eid'] = isset($model['follow_eid']) ? $model['follow_eid'] : 0;
                $temp['old_value']  = $model->origin['customer_status_did'];
                $temp['new_value']  = $model['customer_status_did'];
                if (!CustomerStatusConversion::get($temp)) {
                    $temp['og_id'] = $model['og_id'];
                    $temp['bid']   = $model['bid'];
                    CustomerStatusConversion::create($temp);//todo 不能从高状态改变到低状态
                }

            }
            return true;
        });
    }

    /**
     * 自动转入公海
     */
    public static function AutoTransferPublicSea()
    {
        //todo;
        set_time_limit(0);
        //删选出所有应该进入公海的客户名单

        $cu_params = user_config('params.customer');
        $pc_un_follow_days = $cu_params['pc_un_follow_days'];
        if(!$pc_un_follow_days){
            return true;
        }



        $now_time = time();
        $base_time = $now_time - $pc_un_follow_days * 86400;
        $w_cu['follow_eid'] = ['NEQ',0];
        $w_cu['sid'] = 0;
        $w_cu['is_reg'] = 0;
        $w_cu['is_public'] = 0;
        $w_cu['last_follow_time'] =  ['GT',0];
        $w_cu['last_follow_time'] =  ['LT',$base_time];

        $m_cu = new self();
        $cu_list = $m_cu->where($w_cu)->select();
        if(!$cu_list){
            return true;
        }
        foreach($cu_list as $cu){
            $cu->autoInPublicSea();
        }
        return true;

    }

    protected function setPinyinAttr($value, $data)
    {
        if (!empty($data['name'])) {
            $temp = (new Pinyin())->name($data['name']);
            return join('', $temp);
        }
        return '';
    }

    protected function setNextFollowTimeAttr($value)
    {
        return !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setLastFollowTimeAttr($value)
    {
        return !is_numeric($value) ? strtotime($value) : $value;
    }

    public function getUnFollowDaysAttr($value,$data){
        if(!isset($data['create_time']) || !isset($data['last_follow_time'])){
            return 0;
        }
        $now_time = time();
        $base_time = $data['create_time'];
        if($data['last_follow_time'] > 0){
            $base_time = $data['last_follow_time'];
        }else{
            if($data['assign_time'] > 0){
                $base_time = $data['assign_time'];
            }
        }
        $days = ceil( ($now_time - $base_time ) / 86400);
        return $days;
    }

    protected function setPinyinAbbrAttr($value, $data)
    {
        if (!empty($data['name'])) {
            $temp = (new Pinyin())->abbr($data['name']);
            return $temp;
        }
        return '';
    }

    protected function setBirthTimeAttr($value)
    {
    	if($value == '1970-01-01'){
            return 0;
        }
        return !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setAssignTimeAttr($value)
    {
        return is_numeric($value) ? $value : strtotime($value);
    }

    protected function getAssignTimeAttr($value)
    {
        return $value > 0 ? date('Y-m-d H:i', $value) : $value;
    }

    protected function setTrialTimeAttr($value)
    {
        return is_array($value) ? implode(',', $value) : $value;
    }

    protected function getTrialTimeAttr($value)
    {
        return !empty($value) ? explode(',', $value) : [];
    }

    protected function getBirthTimeAttr($value)
    {
        return $value !== 0 ? date('Y-m-d', $value) : 0;
    }

    protected function setBirthYearAttr($value, $data) {
        if(isset($data['birth_time']) && $data['birth_time'] > 0) {
            if(preg_match('/^\d{4}-\d{2}-\d{2}$/',$data['birth_time'])){
                $da = explode('-',$data['birth_time']);
                $value = intval($da[1]);
                return $value?$value:0;
            }
            $data['birth_time'] = is_int($data['birth_time']) ? $data['birth_time'] : strtotime($data['birth_time']);
            $value = (int)date('Y', $data['birth_time']);
        }
        return $value ? $value : 0;
    }

    protected function setBirthMonthAttr($value, $data) {
        if(is_null($value)) {

            if (isset($data['birth_time']) && $data['birth_time'] > 0) {
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['birth_time'])) {
                    $da = explode('-', $data['birth_time']);
                    $value = intval($da[1]);
                    return $value ? $value : 0;
                }
                $data['birth_time'] = is_int($data['birth_time']) ? $data['birth_time'] : strtotime($data['birth_time']);
                $value = (int)date('m', $data['birth_time']);
            }

        }

        $value = is_null($value)?0:$value;

        return $value;
    }

    protected function setBirthDayAttr($value, $data) {
        if(is_null($value)) {
            if (isset($data['birth_time']) && $data['birth_time'] > 0) {
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['birth_time'])) {
                    $da = explode('-', $data['birth_time']);
                    $value = intval($da[1]);
                    return $value ? $value : 0;
                }
                $data['birth_time'] = is_int($data['birth_time']) ? $data['birth_time'] : strtotime($data['birth_time']);
                $value = (int)date('d', $data['birth_time']);
            }
        }
        $value = is_null($value)?0:$value;
        return $value;
    }


    protected function getLastFollowTimeAttr($value) {
        return $value ? date('Y-m-d', $value) : 0;
    }

    protected function getNextFollowTimeAttr($value) {
        return $value ? date('Y-m-d', $value) : 0;
    }

    public function employees()
    {
        return $this->belongsToMany('Employee', 'customer_employee', 'eid', 'cu_id');
    }

    public function intention()
    {
        return $this->belongsToMany('Employee', 'customer_intention', 'eid', 'cu_id')
            ->field('ename');
    }

    public function user()
    {
        return $this->hasOne('User', 'uid', 'create_uid');
    }

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid');
    }

    /*客户的介绍学员， 用于统计报表*/
    public function refererStudent()
    {
        return $this->hasOne('Student', 'sid', 'referer_sid')
            ->field(['sid', 'student_name', 'first_tel', 'sno']);
    }

    public function followup()
    {
        return $this->hasMany('CustomerFollowUp','cu_id', 'cu_id');
    }

    /**
     * @desc  创建客户信息，并创建副责任跟进人和客户意向信息
     * @author luo
     * @param $customer_data
     * @param $deputy_data
     * @param $intention_data
     * @return bool
     */
    public function createCustomer(array $customer_data, array $deputy_data = [], array $intention_data = []) {

        $w_cu['first_tel'] = $customer_data['first_tel'];
        $w_cu['og_id']     = gvar('og_id');
        $w_cu['bid']       = auto_bid();
        $cu_info = get_customer_info($w_cu);
        if((!isset($customer_data['family_cu_id']) || $customer_data['family_cu_id'] == 0) && $cu_info){
            return $this->user_error('首选号码在系统存在重复!');
        }

        $this->startTrans();
        try {
            //添加客户基本信息
            unset($customer_data['cu_id']);
            $add_rs = $this->allowField(true)->validate('Customer')->isUpdate(false)->save($customer_data);
            if (!$add_rs) throw new FailResult('添加客户信息失败');

            $customer_id = $this->getAttr('cu_id');

            //添加副责任人
            if (!empty($deputy_data)) {
                foreach ($deputy_data as $per_deputy) {
                    $per_deputy['cu_id'] = $customer_id;
                    $rs = $this->connectEmployee($per_deputy);
                    if (!$rs) throw new FailResult('添加副责任人信息失败');
                }
            }

            //添加客户意向信息
            if (!empty($intention_data)) {

                foreach ($intention_data as $per_intention) {
                    $per_intention['cu_id'] = $customer_id;
                    $per_intention['bid'] = request()->bid;
                    $rs = $this->createIntention($per_intention);
                    if (!$rs) throw new FailResult('添加客户意向信息失败');
                }

            }

            // 添加一条客户添加日志
            CustomerLog::addCustomerInsertLog($customer_id);

            $this->commit();

        } catch (FailResult $e) {
            $this->rollback();
            return $this->user_error($e->getMessage());
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return $customer_id;

    }

    //关联员工
    public function connectEmployee($data)
    {
        //$employee = Employee::get(['eid' => $data['eid']]);
        //$rs = $this->employees()->save($employee, ['sale_role_did' => $data['sale_role_did']]);
        $rs = (new CustomerEmployee())->isUpdate(false)->allowField(true)->save($data);
        if($rs === false) return $this->user_error('客户关联员工失败');

        return true;
    }

    public function createIntention($data)
    {
        if($data['eid'] !== 0) {
            $employee = Employee::get(['eid' => $data['eid']]);
            $pivot = ['lid' => $data['lid'],'bid'=>$data['bid']];

            $rs = $this->intention()->save($employee, $pivot);
        } else {

            $rs = db('customer_intention')->insert($data);
        }

        if(!$rs) exception('添加客户意向失败');

        return true;
    }



    //修改客户资料
    public function updateCustomerAndDeputy($customer_data, $deputy_data, $intention_data)
    {
        // 获取客户信息改变的值
        $old_data = get_customer_info($customer_data['cu_id']);
        $content = get_array_diff_value($old_data,$customer_data);

        $this->startTrans();
        try {
            $customer = $this->findOrFail(['cu_id' => $customer_data['cu_id']]);

            $old_cu_info = $customer->getData();

            //--2-- 处理副责任人
            $m_ce = new CustomerEmployee();
            $old_deputy_eids = $m_ce->where('cu_id', $customer->cu_id)->column('eid');
            $new_deputy_eids = array_column($deputy_data, 'eid');
            $add_deputy_eids = array_diff($new_deputy_eids, $old_deputy_eids);
            $detach_deputy_eids = array_diff($old_deputy_eids, $new_deputy_eids);
            $rs = $m_ce->where('cu_id', $customer_data['cu_id'])->where('eid', 'in', $detach_deputy_eids)->delete();
            if(false === $rs){
                $this->rollback();
                return $this->user_error('移除原副责任人失败');
            }


            if (!empty($add_deputy_eids)) {
                foreach ($deputy_data as $per_deputy) {
                    if (in_array($per_deputy['eid'], $add_deputy_eids)) {
                        $per_deputy['cu_id'] = $customer->cu_id;
                        $rs = $customer->connectEmployee($per_deputy);
                        if(!$rs){
                            $this->rollback();
                            return $this->user_error('添加副责任人信息失败!');
                        }
                    }
                }
            }

            //--3-- 处理客户意向
            $customer->intention()->detach();
            foreach ($intention_data as $per_intention) {
                $per_intention['cu_id'] = $customer->cu_id;
                $per_intention['bid'] = $customer->bid;
                $rs = $customer->createIntention($per_intention);
                if(!$rs){
                    $this->rollback();
                    return $this->user_error('添加意向客户失败!');
                }
            }

            $update_mcl = [];
            if(isset($customer_data['follow_eid']) && $old_cu_info['follow_eid'] != $customer_data['follow_eid'] && $old_cu_info['mcl_id'] > 0){
                $update_mcl['cu_assigned_eid'] = $customer_data['follow_eid'];
            }

            if(isset($customer_data['name']) && $customer_data['name'] != $old_cu_info['name']){
                $update_mcl['name'] = $customer_data['name'];
            }

            if(!empty($update_mcl)){
                $w_mcl['mcl_id'] = $old_cu_info['mcl_id'];
                $m_mcl = new MarketClue();
                $result = $m_mcl->save($update_mcl,$w_mcl);
                if(false === $result){
                    $this->rollback();
                    return $this->sql_save_error('market_clue');
                }
            }

            $result = $customer->allowField(true)->save($customer_data);
            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('customer');
            }

            // 添加一条客户编辑操作日志
            CustomerLog::addCustomerEditLog($content,$customer_data['cu_id']);

        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

    public function updateOneCustomer($data)
    {
        $rs = $this->isUpdate(true)->allowField(true)->save($data);
        if(!$rs) return $this->user_error('更新客户信息失败');

        return true;
    }

    //用户首次报名金额，用于客户转化统计
    public function addSignupAmount($sid, $amount)
    {
        $customer = $this->where('sid', $sid)->find();
        if(empty($customer) || $customer->signup_amount > 0) return true;

        if($amount <= 0) return true;
        $customer->signup_amount = $amount;
        $customer->save();

        return true;
    }

    //客户转为学员
    public function changeToStudent($cu_id)
    {

        $customer = $this->find($cu_id);
        if(empty($customer) || $customer->is_reg) return $this->user_error('客户不存在或者已经转正');

        try{
            $this->startTrans();
            if($customer->from_sid > 0) {
                $customer->sid = $customer->from_sid;
                $customer->from_sid = 0;
                //$customer->is_reg = 1; // 是否报读  转为正式学员 不一定报读
                $rs = $customer->save();
                if($rs === false) return $this->user_error('客户更新失败');
                
                $rs = Student::update(['status' => Student::STATUS_NORMAL], ['sid' => $customer->sid]);
                if($rs === false) return $this->user_error('转正失败');
                $this->commit();
                return true;
            }


            $data = $customer->toArray();
            $data['student_name'] = $data['name'];

            $student_model = new Student();

            $sid = $student_model->createOneStudent($data);
            if(!$sid) exception($student_model->getErrorMsg());

            $customer->sid = $sid;
            $customer->is_reg = 1;   // 有疑问，客户转学员就把状态改成已报读是否太草率？如果仅仅是转成学员，没有报读呢？建议：客户转成学员后如果有缴费行为（报读和充值）就把客户的状态改为已报读
            $customer->customer_status_did = self::STATUS_DID_SUCCESS;
            $customer->signup_int_day = date('Ymd', time());
            $customer->save();

            //市场名单更新sid
            $m_mc = new MarketClue();
            
            $result = $m_mc->save(['sid' => $sid], ['cu_id' => $customer->cu_id]);
            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('market_clue');
            }

            if ($customer['referer_sid'] > 0){
                $mStudentReferer = new StudentReferer();
                $result = $mStudentReferer->createStudentReferer($sid,$customer['referer_sid'],$customer['follow_eid']);
                if (false === $result) {
                    $this->rollback();
                    return $this->user_error($mStudentReferer->getError());
                }
            }

            // 添加一条客户转学员操作日志
            CustomerLog::addCustomerToStudentLog($cu_id);

            if($customer->follow_eid){
                $info = array(
                    'eid' => $customer->follow_eid,
                    'sid' => $customer->sid,
                    'rid' => EmployeeStudent::EMPLOYEE_CC
                );
                EmployeeStudent::addEmployeeStudentRelationship($info);
            }

            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            return $this->user_error(['msg' => $e->getMessage(), 'trace' => $e->getTrace()]);
        }

        return $customer->sid;
    }

    //删除客户
    public function deleteOneCustomer($id = 0, $is_force_del = 0)
    {
        if($id > 0) {
            $customer = $this->find($id);
        }else{
            $customer = $this;
        }
        if(empty($customer) || !$customer['cu_id']) return $this->user_error('客户不存在');

        $followup_list = $customer->getAttr('followup');
        if(!empty($followup_list) && !$is_force_del) {
            return $this->user_error('删除客户，同时会删除相关的跟进情况', self::CODE_HAVE_RELATED_DATA);
        }
        $m_mc = new MarketClue();

        $this->startTrans();
        try {
            $result = $customer->followup()->delete();
            if(false === $result){
                $this->rollback();
                return $this->sql_delete_error('customer_follow_up');
            }
            //更新market_clue表
            $update_mc['cu_id'] = 0;
            $update_mc['cu_assigned_eid'] = 0;
            $update_mc['cu_assigned_bid'] = 0;
            $w_mc_update['cu_id'] = $customer['cu_id'];

            $result = $m_mc->save($update_mc,$w_mc_update);
            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('market_clue');
            }

            $result = $customer->delete();
            if(false === $result){
                $this->rollback();
                return $this->sql_delete_error('customer');
            }

            // 添加一条客户删除日志
            CustomerLog::addCustomerDeleteLog($customer);

        } catch(\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

    public function deleteCondition($bid,$mc_id,$get_start_time,$get_end_time,$create_start_time,$create_end_time,$is_force_del = 0)
    {
        $model = new self;
        $model->where('is_delete','eq', 0);
        if (!empty($bid) && $bid > 0){
            $model->where('bid','eq', $bid);
        }
        if (!empty($mc_id) && $mc_id > 0){
            $model->where('mc_id','eq', $mc_id);
        }
        if (!empty($get_start_time) && !empty($get_end_time) && $get_start_time > 0 && $get_end_time > 0){
            $model->where('get_time','between', [$get_start_time,$get_end_time]);
        }
        if (!empty($create_start_time) && !empty($create_end_time) && $create_start_time > 0 && $create_end_time > 0){
            $model->where('create_time','between', [$create_start_time,$create_end_time]);
        }
        $cu_list = $model->select();

        if(!$cu_list){
            return $this->user_error('待删除的名单数为0，请重新确定删除条件!');
        }

        $total = count($cu_list);

        if($is_force_del == 0) {
            return $this->user_error('确定删除客户名单吗？共'. $total .'人 删除后不可恢复',self::CODE_HAVE_RELATED_DATA);
        }

        $this->startTrans();
        try {
            foreach ($cu_list as $cu){
                $result = $cu->deleteOneCustomer(0,1);
                if(false === $result){
                    $this->rollback();
                    return $this->user_error($cu->getError());
                }
            }
        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }


    
    /**
     * 确定客户无效
     * @param  Customer $customer [description]
     * @return [type]             [description]
     */
    public function confirmUseless(Customer $customer)
    {
        $m_mc = new MarketClue();

        $cu_id = $customer->cu_id;

        $followup_list = $customer->getAttr('followup');

        $this->startTrans();
        try {
            $result = $customer->followup()->delete();
            if(false === $result){
                $this->rollback();
                return $this->sql_delete_error('customer_follow_up');
            }
            //更新market_clue表
            $data = $m_mc->where('cu_id',$cu_id)->find();
            if(!empty($data)){
                $data->is_valid = 2;
                $data->cu_assigned_eid = 0;
                $result = $data->isUpdate(true)->save();
            }
            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('market_clue');
            }

            $result = $customer->delete(true);
            if(false === $result){
                $this->rollback();
                return $this->sql_delete_error('customer');
            }
        } catch(Exception $e) {
            $this->rollback();
            return $this->user_error($e->getTraceAsString());
        }
        $this->commit();
        return true;

    }


    public function assignToEmployee($input){
        $need_fields = ['cu_ids','eid'];

        if(!$this->checkInputParam($input,$need_fields)){
            return false;
        }

        $cu_ids     = array_unique($input['cu_ids']);
        $follow_eid = $input['eid'];

        if(!is_array($cu_ids)){
            $cu_ids = explode(',',$cu_ids);
        }
        $now_time = time();
        $w_cu = [];
        $update_cu['follow_eid']    = $follow_eid;
        $update_cu['assign_time'] = $now_time;

        $this->startTrans();
        try {
            foreach ($cu_ids as $cu_id) {
                $m_cu = new self();
                $w_cu['cu_id'] = $cu_id;
                $result = $m_cu->save($update_cu, $w_cu);
                if (false === $result) {
                    $this->rollback();
                    return $this->sql_save_error('customer');
                }

                $m_mcl = new MarketClue();
                $w_mcl['cu_id'] = $cu_id;
                $update_mcl['cu_assigned_eid'] = $follow_eid;

                $result = $m_mcl->save($update_mcl,$w_mcl);
                if(false === $result){
                    $this->rollback();
                    return $this->sql_save_error('market_clue');
                }

                // 添加一条客户分配日志
                CustomerLog::addCustomerAssignLog($cu_id,$follow_eid);
            }
        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

    /**
     * 客户实例自动转入公海
     * @return bool
     */
    public function autoInPublicSea(){
        return $this->intoPublicSea(0,1);
    }


    /**
     * @param int $cu_id
     * @param int $is_auto 是否自动转入
     * @return bool
     */
    public function intoPublicSea($cu_id = 0,$is_auto = 0){
        if($cu_id > 0){
            $cu_info = get_customer_info($cu_id);
        }else{
            $cu_info = $this->getData();
            $cu_id = $cu_info['cu_id'];
        }

        if(!$cu_info || !$cu_id){
            return $this->user_error('cu_id error!');
        }

        $ename = '-';
        $now_time = time();

        if($cu_info['follow_eid'] > 0){
            $ename = get_employee_name($cu_info['follow_eid']);
        }

        $note  = sprintf('由 %s => 转入公海',$ename);

        if($is_auto == 1){
            $note .= '[系统自动]';
        }else{
            $note = '[操作]'.$note;
        }

        $data = [
            'cu_id' => $cu_id,
            'eid'   => $cu_info['follow_eid'],
            'is_system' => 1,
            'system_op_type' => CustomerFollowUp::SYSTEM_OP_TYPE_IN_PS,
            'content' => $note,
        ];

        $this->startTrans();
        try {
            $m_cfu = new CustomerFollowUp();
            $result = $m_cfu->addOneFollowUp($data);

            if (!$result) {
                $this->rollback();
                return $this->user_error('跟进记录添加失败:' . $m_cfu->getError());
            }

            $update_cu = [
                'is_public' => 1,
                'in_public_time' => $now_time,
            ];

            $w_cu_update['cu_id'] = $cu_id;

            $m_cu = new self();

            $result = $m_cu->save($update_cu,$w_cu_update);

            if(false === $result){
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
     * 批量转入公海客户
     * @param  Customer $customer [description]
     * @return [type]             [description]
     */
    public function batIntoPublicSea($cu_ids = []){
        if (empty($cu_ids) || !is_array($cu_ids)) {
            $this->user_error('请选择转入公海客户ID');
        }
        $this->startTrans();
        try {
            foreach ($cu_ids as $cu_id) {
                $result = $this->intoPublicSea($cu_id);
                if (!$result) {
                    $this->rollback();
                    return false;
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
     * 转出公海客户
     * @param $eid
     * @param int $cu_id
     * @param int $is_rob 是否抢占
     * @return bool
     */
    public function outPublicSea($eid,$cu_id = 0,$is_rob = 1){
        if(!$eid){
            return $this->user_error('请选择要转入的员工');
        }

        if($cu_id > 0){
            $cu_info = get_customer_info($cu_id);
        }else{
            $cu_info = $this->getData();
            $cu_id = $cu_info['cu_id'];
        }

        if(!$cu_info || !$cu_id){
            return $this->user_error('cu_id error!');
        }


        $ename = '-';
        $now_time = time();

        if($cu_info['follow_eid'] > 0){
            $ename = get_employee_name($cu_info['follow_eid']);
        }

        $note  = sprintf('由 公海 转给 => %s',$ename);

        if($is_rob == 1){
            $note .= '[抢占]';
        }else{
            $note .= '[分配]';
        }

        $cfu_data = [
            'cu_id' => $cu_id,
            'eid' => $eid,
            'is_system' => 1,
            'system_op_type' => CustomerFollowUp::SYSTEM_OP_TYPE_OUT_PS,
            'content' => $note,
        ];

        $this->startTrans();
        try {

            $m_cfu = new CustomerFollowUp();
            $result = $m_cfu->addOneFollowUp($cfu_data);
            if (!$result) {
                return $this->user_error('跟进记录添加失败:'.$m_cfu->getError());
            }

            $update_cu = [
                'follow_eid'    => $eid,
                'is_public' => 0,
                'in_public_time' => 0,
                'get_time' => time()
            ];

            $w_cu_update['cu_id'] = $cu_id;

            $m_cu = new self();

            $result = $m_cu->save($update_cu,$w_cu_update);

            if(false === $result){
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
     * 批量转出公海客户
     * @param  Customer $customer [description]
     * @return [type]             [description]
     */
    public function batOutPublicSea($eid,$cu_ids = []){
        if(!$eid){
            return $this->input_param_error('eid');
        }
        if(empty($cu_ids)){
            return $this->input_param_error('cu_ids');
        }
        $this->startTrans();
        try {
            foreach ($cu_ids as $cu_id) {
                $rs = $this->outPublicSea($eid,$cu_id,0);
                if (!$rs) {
                    return false;
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
     * 抢占公海客户
     * @param  Customer $customer [description]
     * @return [type]             [description]
     */
    public function robPublicSea($cu_ids = []){
        if (empty($cu_ids) || !is_array($cu_ids)) {
            return $this->input_param_error('cu_ids');
        }
        $user = gvar('user');
        $my_eid = User::getEidByUid($user['uid']);
        $cu_params = user_config('params.customer');
        $pc_limit_customer_num = $cu_params['pc_limit_customer_num'];

        $w_cu['is_reg'] = 0;
        $w_cu['follow_eid'] = $my_eid;

        $my_unreg_cu_count = $this->where($w_cu)->count();

        $this_cu_count = count($cu_ids);

        if($my_unreg_cu_count + $this_cu_count > $pc_limit_customer_num){
            return $this->user_error('您当前未成交的客户数量超出限制，不允许再抢占这么多公海客户!');

        }
        $this->startTrans();
        try {
            foreach ($cu_ids as $cu_id) {
                $result = $this->outPublicSea($my_eid,$cu_id);
                if (!$result) {
                    $this->rollback();
                    return false;
                }
            }
        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;

    }

}

