<?php
/**
 * Author: luo
 * Time: 2018/4/20 11:36
 */

namespace app\api\model;


use app\common\exception\FailResult;
use think\Exception;

class MarketClue extends Base
{

    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];
    protected $auto = ['birth_year', 'birth_month', 'birth_day'];
    protected $type = [
        'get_time'=>'timestamp'
    ];

    protected function setBirthTimeAttr($value)
    {
        if($value == '1970-01-01'){
            return 0;
        }
        $value = !is_numeric($value) || strlen($value) <= 8 ? strtotime($value) : $value;
        $value = $value >= 0 ? $value : 0;
        return (int)$value;
    }

    protected function getGetTimeAttr($value)
    {
        return date('Y-m-d',$value);
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

    protected function getBirthTimeAttr($value) {
        return $value > 0 ? date('Y-m-d', $value) : $value;
    }

    public function marketChannel()
    {
        return $this->hasOne('MarketChannel', 'mc_id', 'mc_id')->field('mc_id,channel_name');
    }

    //创建的员工信息
    public function employee()
    {
        return $this->hasOne('Employee', 'uid', 'create_uid')->field('uid,eid,ename');
    }

    public function recommendStudent()
    {
        return $this->hasOne('Student', 'sid', 'recommend_sid')->field('sid,student_name');
    }

    public function recommendUser()
    {
        return $this->hasOne('User', 'uid', 'recommend_uid')->field('uid,account,name');
    }

    public function addClue($data)
    {
        if(empty($data['name'])) return $this->user_error('姓名必填');

        //判断名单重复
        $w['tel'] = $data['tel'];
        $m_mcl = new self();
        $ex_mcl = $m_mcl->where($w)->find();

        if($ex_mcl){
            return $this->user_error('电话号码:'.$data['tel'].'已经在市场名单中存在!');
        }

        $w_customer['first_tel|second_tel'] = $data['tel'];
        $m_customer = new Customer();
        $ex_customer = $m_customer->where($w_customer)->find();
        if($ex_customer){
            return $this->user_error('电话号码:'.$data['tel'].'已经在客户名单中存在!');
        }

        $w_student['first_tel|second_tel'] = $data['tel'];
        $m_student = new Student();
        $ex_student = $m_student->where($w_student)->find();
        if($ex_student){
            return $this->user_error('电话号码:'.$data['tel'].'已经在学员档案中存在!');
        }
        $this->startTrans();
        try {
            $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
            if($rs === false) return false;

            if(isset($data['mc_id'])) {
                self::UpdateNumOfChannel($data['mc_id']);
            }

            // 添加一条市场名单 添加日志
            MarketClueLog::addMarketClueInsertLog($this->mcl_id);

        } catch(\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();

        return $this->mcl_id;
    }

    public function delOneClue($mcl_id = 0)
    {
        if($mcl_id > 0) {
            $clue = $this->find($mcl_id);
        }else{
            $clue = $this;
        }

        if(empty($clue)) return $this->user_error('市场名单错误');
        $this->startTrans();
        try{
            $rs = $clue->delete();
            if($rs === false){
                $this->rollback();
                return $this->sql_delete_error('market_clue');
            }
            
            // 更新市场渠道 市场名单数
            if(!empty($clue['mc_id'])) {
                self::UpdateNumOfChannel($clue['mc_id']);
            }

            // 市场名单删除日志
            MarketClueLog::addMarketClueDeleteLog($clue);

        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();

        return true;
    }

    //更新渠道相关名单数量
    public static function UpdateNumOfChannel($mc_id)
    {
        $m_mc = new MarketChannel();
        $m_market_clue = m('MarketClue');
        $total_num = $m_market_clue->where('mc_id', $mc_id)->count();
        $valid_num = $m_market_clue->where('mc_id', $mc_id)->where('is_valid = 1')->count();
        $visit_num = $m_market_clue->where('mc_id', $mc_id)->where('is_visit = 1')->count();
        $deal_num = $m_market_clue->where('mc_id', $mc_id)->where('is_deal = 1')->count();

        $update_data = [
            'total_num' => $total_num,
            'valid_num' => $valid_num,
            'visit_num' => $visit_num,
            'deal_num' => $deal_num,
        ];

        $m_mc->where('mc_id', $mc_id)->update($update_data);
        return true;
    }

    //多个名单分配员工
    public function multiAssignEmployee($mcl_ids, $eid,$assign_type = 2,$to_bid = 0)
    {
        if(!in_array($assign_type,[1,2])){
            return $this->input_param_error('assign_type');
        }

        $this->startTrans();
        try {
            if($assign_type == 1) {
                if($to_bid > 0){
                    $update_mcl['bid'] = $to_bid;
                }else{
                    $update_mcl['assigned_eid'] = $eid;
                    $update_mcl['assigned_time'] = time();
                }

                foreach ($mcl_ids as $mcl_id) {
                    $rs = $this->isUpdate(false)->where('mcl_id',$mcl_id)->update($update_mcl);
                    if($rs === false){
                        $this->rollback();
                        return $this->sql_save_error('market_clue');
                    }
                    //添加操作日志
                    if($to_bid){
                        // 分配给市场 只分配到校区 日志
                        MarketClueLog::addMarketClueToMarketToBidLog($mcl_id,$to_bid);
                    }else{
                        // 分配给市场 明确到跟进人
                        MarketClueLog::addMarketClueToMarketToEidLog($mcl_id,$eid);
                    }
                }
            }elseif($assign_type == 2){
                // 第二次 分配销售时 更新销售人员信息
                $update_mcl['cu_assigned_eid'] = $eid;
                $update_mcl['assigned_time'] = time();
                $w_mcl['mcl_id'] = ['in',$mcl_ids];
                $rs = $this->isUpdate(true)->where($w_mcl)->update($update_mcl);
                if ($rs === false){
                    $this->rollback();
                    return $this->sql_save_error('market_clue');
                }

                foreach($mcl_ids as $mcl_id){
                    $input = [];
                    $input['mcl_id'] = $mcl_id;
                    $m_mcl = new self();
                    $result = $m_mcl->changeToCustomer($input,$eid,$to_bid);
                    if(!$result){
                        $this->rollback();
                        return $this->user_error($m_mcl->getError());
                    }
                    /*// 市场名单转客户 同时更新 有效性( 市场名单转为客户 默认把有效性 变为有效 )
                    $market_clue = MarketClue::get($mcl_id);
                    if($market_clue['is_valid'] == 0){
                        $market_clue->is_valid = 1;
                        $result = $market_clue->save();
                        if(!$result){
                            $this->rollback();
                            return $this->sql_save_error('market_clue');
                        }
                    }
                    //更新市场渠道 有效人数
                    $mc_id = $market_clue['mc_id'];
                    self::UpdateNumOfChannel($mc_id);*/
                    // 添加操作日志
                    if($to_bid){
                        // 分配给市场 只分配到校区 日志
                        MarketClueLog::addMarketClueToCustomerToBidLog($mcl_id,$to_bid);
                    }else{
                        // 分配给市场 明确到跟进人
                        MarketClueLog::addMarketClueToCustomerToEidLog($mcl_id,$eid);
                    }
                }
            }
        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        if(input('is_push')) {
            $employee = (new Employee())->where('eid', $eid)->field('eid,mobile,ename')->find();
            if(empty($employee)) return true;

            $m_message = new Message();
            $to_employee_data = [
                'eid' => $eid,
                'mcl_ids' => $mcl_ids
            ];
            $rs = $m_message->sendTplMsg('clue_to_employee', $to_employee_data, [], 2);
            if($rs === false) return $this->user_error($m_message->getErrorMsg());

            foreach($mcl_ids as $mcl_id) {
                $market_clue = $this->where('mcl_id,name,tel')->find($mcl_id);
                if(empty($market_clue)) continue;

                $m_message->push_msg_to_mobile(['mcl_id' => $mcl_id, 'ename' => $employee['ename']], $market_clue['tel'], 'clue_to_student');
            }
        }

        return true;
    }

    //多个删除名单
    public function multiDel(array $mcl_ids)
    {
        $rs = $this->where('mcl_id', 'in', $mcl_ids)->delete();
        if($rs === false) return false;

        $mc_ids = $this->where('mcl_id', 'in', $mcl_ids)->column('mc_id');
        $mc_ids = array_unique($mc_ids);

        if(!empty($mc_ids)) {
            foreach($mc_ids as $mc_id) {
                self::UpdateNumOfChannel($mc_id);
            }
        }

        return true;
    }

    /**
     * 批量删除
     * @param $mc_id
     * @param $get_start_time
     * @param $get_end_time
     * @param $create_start_time
     * @param $create_end_time
     * @return bool
     */
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
        $mc_list = $model->select();

        if(!$mc_list){
            return $this->user_error('待删除的名单数为0，请重新确定删除条件!');
        }

        $total = count($mc_list);

        if($is_force_del == 0) {
            return $this->user_error('确定删除客户名单吗？共'. $total .'人 删除后不可恢复',self::CODE_HAVE_RELATED_DATA);
        }

        $this->startTrans();
        try {
                foreach ($mc_list as $mc){
                    $rs = $mc->delOneClue(0);
                    if(false === $rs){
                        $this->rollback();
                        return $this->user_error($mc->getError());
                    }
                }

        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    //转为客户

    /**
     * 转为客户
     * @param $post
     * @param int $is_valid 是否验证有效性
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function changeToCustomer($post,$to_eid = 0,$to_bid = 0,$is_valid = 0)
    {
        if(empty($post['mcl_id'])) return $this->user_error('mcl_id error');

        $data = $this->getData();
        if(!empty($data) && $this->mcl_id == $post['mcl_id']) {
            $clue = $this;
        } else {
            $clue = $this->find($post['mcl_id']);
        }

        if(empty($clue)) return $this->user_error('市场名单不存在');

        if($clue['cu_id'] > 0){
            $cu_info = get_customer_info($clue['cu_id']);
            if($cu_info) {
                if($to_bid > 0){
                    $update_cu['bid'] = $to_bid;
                    $update_cu['follow_eid'] = 0;
                }else{
                    $update_cu['follow_eid'] = $to_eid;
                }
                $update_cu['mcl_id']  = $clue['mcl_id'];
                // $update_cu['first_family_rel'] = $clue['family_rel'];
                $w_update_cu['cu_id'] = $clue['cu_id'];

                $m_customer = new Customer();
                $result = $m_customer->save($update_cu,$w_update_cu);

                return true;
            }
        }

        $from_did = 0;

        if($clue['from_did'] > 0){
            $from_did = $clue['from_did'];
        }else{
            if($clue['mc_id'] > 0) {
                $from_did = m('MarketChannel')->where('mc_id', $clue['mc_id'])->cache(5)->value('from_did', 0);
            }
        }

        $this->startTrans();

        try {
            $customer_data = array_merge($clue->toArray(), $post);
            $customer_data['first_tel'] = $clue['tel'];
            if(strlen($customer_data['first_tel'])>32){
                $error_msg = sprintf("%s的联系电话号码设置太长，需要重新编辑!",$customer_data['name']);
                exception($error_msg);
            }

            $customer_data['first_tel'] = trim($customer_data['first_tel']);

            if(!preg_match('/^\d+$/',$customer_data['first_tel'])){
                $error_msg = sprintf("%s的联系电话号码格式不对，需要重新编辑!",$customer_data['name']);
                exception($error_msg);
            }

            $customer_data['assign_time'] = time();
            $customer_data['referer_sid']  = $customer_data['recommend_sid'];
            $customer_data['first_tel']    = $customer_data['tel'];
            $customer_data['first_family_rel'] = $customer_data['family_rel'];

            if($to_eid != 0) {
                $customer_data['follow_eid'] = $to_eid;
                $clue->cu_assigned_eid = $to_eid;
                $employee = Employee::get($to_eid);
                if (empty($employee)) throw new FailResult('员工不存在');

                $employee_bids = $employee->bids;
                if(!in_array($clue['bid'],$employee_bids)){
                    $customer_data['bid'] = $employee_bids[0];
                    $clue->cu_assigned_bid = $employee_bids[0];
                }
            }else{
                if($to_bid > 0){
                    $customer_data['bid']    = $to_bid;
                    $clue->cu_assigned_bid   = $to_bid;
                }else{
                    if (!empty($clue['assigned_eid'])) {
                        $employee = get_employee_info($clue['assigned_eid']);
                        if (empty($employee)) throw new FailResult('员工不存在');
                        $customer_data['follow_eid'] = $employee['eid'];
                        $clue->cu_assigned_eid = $employee['eid'];
                    }
                }

            }
            $customer_data['from_did'] = $from_did;
            $customer_data['mcl_id'] = $clue['mcl_id'];
            $customer_data['mc_id']  = $clue['mc_id'];
            if(isset($clue['get_time'])){
                $customer_data['get_time'] = strtotime($clue['get_time']);
            }else{
                $customer_data['get_time'] = time();
            }

            if($customer_data['get_time'] <= 0 ){
                $customer_data['get_time'] = time();
            }

          
            $m_customer = new Customer();
            $rs = $m_customer->data([])->allowField(true)->isUpdate(false)->save($customer_data);
            if ($rs === false) throw new FailResult($m_customer->getErrorMsg());

            $clue->cu_id = $m_customer->getAttr('cu_id');
            if($is_valid){
                $clue->is_valid = $is_valid;
            }
            $rs = $clue->allowField('cu_id,is_valid,cu_assigned_eid,cu_assigned_bid,assigned_time')->isUpdate(true)->save();
            if ($rs === false) throw new FailResult($this->getErrorMsg());

            // 市场名单转为客户 日志
            MarketClueLog::addMarketClueChangeToCustomerLog($clue['mcl_id']);

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        return true;
    }

    //多个转为客户
    public function multiChangeToCustomer($post)
    {
        if(empty($post['mcl_ids'])) return $this->user_error('市场名单不能为空');

        try {
            $this->startTrans();
            $data = $post;
            unset($data['mcl_ids']);
            foreach ($post['mcl_ids'] as $mcl_id) {
                $data['mcl_id'] = $mcl_id;
                $rs = $this->changeToCustomer($data);
                if($rs === false) throw new FailResult($this->getErrorMsg());

                // 市场名单转为客户 日志
                MarketClueLog::addMarketClueChangeToCustomerLog($mcl_id);
            }

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    public function beValid(MarketClue $clue)
    {
        $clue->is_valid = 1;
        $rs = $clue->allowField('is_valid')->isUpdate(true)->save();
        if($rs === false) return false;

        if($clue->mc_id > 0) self::UpdateNumOfChannel($clue->mc_id);

        return true;
    }

    //多个名单生效
    public function multiChangeToValid($post)
    {
        try {
            $this->startTrans();

            foreach($post as $row) {
                if(empty($row['mcl_id'])) continue;

                /** @var MarketClue $clue */
                $clue = $this->find($row['mcl_id']);
                if(empty($clue)) continue;

                //是否有效
                if(!empty($row['is_valid']) && $row['is_valid'] == 1) {
                    $rs = $this->beValid($clue);
                    if($rs === false) throw new FailResult($this->getErrorMsg());
                }

                //是否转为客户
                if(!empty($row['is_change'])) {
                    $rs = $clue->changeToCustomer(['mcl_id' => $clue['mcl_id']]);
                    if($rs === false) throw new FailResult($clue->getErrorMsg());
                }

            }

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    /**
     * 渠道二维码名单提交处理
     * xusq
     * @param [type] $input [description]
     */
    public function addQrClue($input){

        unset($input['code']);
        if(isset($input['qr_eid'])){
            if(!is_numeric($input['qr_eid'])){
                unset($input['qr_eid']);
            }else{
                $input['qr_eid'] = intval($input['qr_eid']);
            }
        }
        if(isset($input['assigned_eid'])){
            if(!is_numeric($input['assigned_eid'])){
                unset($input['assigned_eid']);
            }else{
                $input['assigned_eid'] = intval($input['assigned_eid']);
            }
        }
        if(isset($input['grade'])){
            if(empty($input['grade'])){
                unset($input['grade']);
            }else{
                $input['grade'] = intval($input['grade']);
            }
        }
        $this->startTrans();
        try{

            $where['tel'] = $input['tel'];
            $where['name'] = trim($input['name']);
            $data_exist = $this->m_market_clue->where($where)->find();
            if(!empty($data_exist)){
                throw new FailResult($this->getErrorMsg());
            }

            if(isset($input['mc_id'])){
                $mc_info = get_mc_info($input['mc_id']);
                if($mc_info){
                    $input['from_did'] = $mc_info['from_did'];
                }
            }
            $input['get_time'] = time();



            $result = $this->validate(true)->isUpdate(false)->allowField(true)->save($input);


            if(false === $result){
                $this->rollback();
                return $this->sql_add_error('market_clue');
            }

            $mc_id = $this->getAttr('mc_id');

            $m_mc = $this->m_market_channel->where('mc_id',$mc_id)->find();
            $m_mc->total_num = $m_mc->total_num + 1;
            $m_mc->save();


        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
     
        return true;
    }

    /**
     *  更新市场渠道
     * @param $be_channel
     * @param $to_channel
     */
    public function updateMakeChannelID($be_channel,$to_channel)
    {
        $mMarketChannel = new MarketChannel();
        $to_mcl_model = $mMarketChannel->get($to_channel);
        if (empty($to_mcl_model)){
            return $this->user_error('合并到渠道不存在');
        }
        $w['mc_id'] = $be_channel;
        $data = $this->where($w)->select();
        if (!empty($data)){
            foreach ($data as $k => $v){
                $uodate_mc['mc_id'] = intval($to_channel);
                $update_w['mcl_id'] = $v['mcl_id'];
                $rs = $v->data([])->save($uodate_mc,$update_w);
                if (!$rs){
                    return $this->user_error('market channel');
                }
            }
        }
        return true;
    }


}