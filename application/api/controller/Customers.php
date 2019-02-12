<?php
/** 
 * Author: luo
 * Time: 2017-10-11 09:54
**/
namespace app\api\controller;

use app\api\model\Customer;
use app\api\model\CustomerEmployee;
use app\api\model\CustomerFollowUp;
use app\api\model\Employee;
use app\api\model\PublicSchool;
use app\api\model\Student;
use app\api\model\User;
use think\Request;

class Customers extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->get();
        $model = new Customer();
        $next_prev_fields = ['cu_id','sid','name','nick_name','sex','first_tel','last_follow_time'];

        if(!empty($input['lid']) || !empty($input['eid'])){
            $model = new \app\api\model\CustomerIntention();
            $model->alias('ci')->join('customer c','c.cu_id = ci.cu_id','left')->distinct(true);
        }else{
            $model->alias('c');
        }

        if (!empty($input['name'])) {
            $name = trim($input['name']);
            unset($input['name']);
            if(preg_match("/^[a-z]*$/i", $name)) {
                $where = sprintf('c.name like "%%%s%%" or c.pinyin like "%%%s%%" or c.pinyin_abbr = "%s"', $name, $name, $name);
            } elseif(preg_match("/^1\d{10}$/",$name)){
                $where = sprintf('c.first_tel = "%s" or c.second_tel = "%s"',$name,$name);
            } else {
                $where = sprintf("c.name like '%%%s%%'", $name);
            }

            $model->where($where);
        }

        if (!empty($input['un_follow_days'])) {
            $un_follow_days = intval($input['un_follow_days']);
            $w = [];
            if($un_follow_days > -2){
                if($un_follow_days == -1){
                    $w['follow_times'] = 0;
                }else{
                    $base_time = time() - $un_follow_days * 86400;
                    $w['last_follow_time'][] = ['GT',0];
                    $w['last_follow_time'][] = ['LT',$base_time];
                }
                $model->where($w);
            }
            unset($input['un_follow_days']);
        }


        //试听时间段
        if(isset($input['trial_time'])) {
            $trial_time = $input['trial_time'];
            unset($input['trial_time']);
            if(!empty($trial_time)) {
                $trial_time = explode(',', $trial_time);
                $trial_time_where = [];
                foreach($trial_time as $per_time) {
                    $trial_time_where[] = "find_in_set($per_time, c.trial_time)";
                }
                $model->where(implode(' or ', $trial_time_where));
            }
        }

        if(isset($input['age_start']) && isset($input['age_end']) && $input['age_start'] == $input['age_end']){
            $months = age_to_months($input['age_start']);

            $age_time_start = strtotime("-$months months");
            $age_time_end   = strtotime("+1 month",$age_time_start) - 1;

            $model->where('c.birth_time','BETWEEN',[$age_time_start,$age_time_end]);
            unset($input['age_start']);
        }else{
            if(isset($input['age_start'])) {
                $months = age_to_months($input['age_start']);
                $age_start_time = strtotime("-$months months");
                $model->where('c.birth_time', 'elt', $age_start_time);
                unset($input['age_start']);
            }
            if(isset($input['age_end'])) {
                $months = age_to_months($input['age_end']);
                $age_end_time = strtotime("-$months months");
                $model->where('c.birth_time', 'egt', $age_end_time);
                unset($input['age_end']);
            }

        }


        if(!empty($input['with'])) {
            $with = $tmp_with = explode(',', $input['with']);
            if(array_search('last_customer_follow_up', $tmp_with) >= 0) {
                unset($tmp_with[array_search('last_customer_follow_up', $tmp_with)]);
                $input['with'] = implode(',', $tmp_with);
            }
        }

        $first_eid = input('first_eid');
        $second_eid = input('second_eid');
        if(!empty($first_eid)) {   # 查找属于自己的客户，follow_eid, create_uid or deputy is myself
            $first_uid = Employee::getUidByEid($first_eid);
            $where = sprintf('c.follow_eid = %s or c.create_uid = %s or ce.eid=%s',
                $first_eid, $first_uid, $first_eid);
            //$input['bid'] = -1;
            $input['order_field'] = 'c.cu_id';
            $input['order_sort'] = 'desc';
            $ret = $model
                ->join('customer_employee ce', 'c.cu_id = ce.cu_id', 'left')
                ->where($where)->field('c.*')->setNextPrevFields($next_prev_fields)->getSearchResult($input);

        } elseif(!empty($second_eid)) { # 查找相关副责任人的员工
            //$input['bid'] = -1;
            $input['order_field'] = 'c.cu_id';
            $input['order_sort'] = 'desc';
            $ret = $model
                ->join('customer_employee ce', 'c.cu_id = ce.cu_id', 'left')
                ->where('ce.eid', $second_eid)->field('c.*')->setNextPrevFields($next_prev_fields)->getSearchResult($input);

        } else {
            $ret = $model->setNextPrevFields($next_prev_fields)->getSearchResult($input);
        }


        if(!empty($ret['list'])) {


            $db_cfu = db('customer_follow_up');
            foreach($ret['list'] as $k=>$customer) {
                if($customer['referer_sid'] > 0){
                    $customer['referer_student'] = get_student_info($customer['referer_sid']);
                }else{
                    $customer['referer_student'] = null;
                }
                if($customer['school_id'] > 0){
                    $customer['school_id_text'] = PublicSchool::getSchoolIdText($customer['school_id']);
                }else{
                    $customer['school_id_text'] = '';
                }

                $customer['user'] = $this->get_user_info($customer['create_uid']);
                if(!empty($with) && in_array('last_customer_follow_up', $with)) {
                    $customer['last_customer_follow_up'] = get_customer_last_followup($customer['cu_id']);
                }
                if(isset($customer['mc_id'])){
                    $mc_info = get_mc_info($customer['mc_id']);
                    $customer['channel_name'] = $mc_info['channel_name'];
                }else{
                    $customer['channel_name'] = '-';
                }
                if($customer['from_did']){
                    $customer['from_did_name'] = get_did_value($customer['from_did']);
                }

                $ret['list'][$k] = $customer;

            }
        }

        return $this->sendSuccess($ret);
    }

    protected function get_user_info($uid){
        static $users = [];
        if(isset($users[$uid])){
            return $users[$uid];
        }
        $user_info = get_user_info($uid);
        if($user_info){
            $u['uid'] = $user_info['uid'];
            $u['account'] = $user_info['account'];
            $u['name'] = $user_info['name'];
        }else{
            $u = null;
        }

        $users[$uid] = $u;

        return $u;
    }
 
    /**
     * 客户名单 获取 市场信息  
     * method get: api/customers/1/market_clue
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function get_list_market_clue(Request $request)
    {
        $cu_id = input('id/d');
        if(!$cu_id){
            return $this->sendError(400,'param error');
        }
        $model = new MarketClue;
        $ret = $model->where('cu_id',$cu_id)->find();

        if($ret){
            return $this->sendSuccess($ret);
        }
    }


    public function get_detail(Request $request, $id = 0)
    {
        $model = new Customer();
        $m_student = new Student();
        $customer = $model->with('student')->find($id);
        if(empty($customer)) return $this->sendError(400, '客户不存在');

        $ret['customer'] = $customer->toArray();
        $ret['customer']['school_id_text'] = PublicSchool::getSchoolIdText($ret['customer']['school_id']);
        $ret['customer']['referer_student'] = $m_student->where('sid', $customer['referer_sid'])
            ->field('sid,student_name,first_tel,sno')->find();
        $ret['deputy'] = $customer->employees()->field('pivot.eid, pivot.sale_role_did')->select();
        $ret['intention'] = db('customer_intention')->where('cu_id', $customer->cu_id)
            ->field('eid, lid')->select();

        return $this->sendSuccess($ret);
    }

    /**
     * @desc  新增客户
     * @author luo
     * @url   customers
     * @method POST
     * @param obj customer 客户基本信息
     * @param array deputy 副责任人:{eid, sales_role_did}
     * @param array intention 客户意向: {lid, eid}
     */
    public function post(Request $request) {

        $input = input();
        $customer_data = $input['customer'];
        if(isset($customer_data['sex'])){
            $customer_data['sex'] = intval($customer_data['sex']);
        }
        $deputy_data = isset($input['deputy']) && !empty($input['deputy']) ? $input['deputy'] : [];
        $intention_data = isset($input['intention']) && !empty($input['intention']) ? $input['intention'] : [];


        $validate_rs = $this->validate($customer_data, 'Customer');
        if($validate_rs !== true) return $this->sendError(400, $validate_rs);

        $customer_model = new Customer();
        $rs = $customer_model->createCustomer($customer_data, $deputy_data, $intention_data);

        if(!$rs) {
            return $this->sendError(400, $customer_model->getErrorMsg());
        }

        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $input = input('put.');

        $customer_data = $input['customer'];
        $customer_data['cu_id'] = $request->param('id');
        $deputy_data = $input['deputy'];
        $intention_data = $input['intention'];

        $model = new Customer();
        $rs = $model->updateCustomerAndDeputy($customer_data, $deputy_data, $intention_data);
        if(!$rs) return $this->sendError(400, $model->getError());

        return $this->sendSuccess();
    }

    /**
     * @desc  添加客户跟进情况
     * @author luo
     * @url   customers/1/followup
     * @method POST
     */
    public function post_followup(Request $request) {

        $customer_id = $request->param('id');
        $customer = Customer::get(['cu_id' => $customer_id]);
        if(empty($customer)) return $this->sendError(400, '不存在客户');

        $data = input();
        if($data['intention_level'] == 0){
            return $this->sendError(400,'请选择意向级别！');
        }
        $data['cu_id'] = $customer_id;
        $m_cfu = new CustomerFollowUp();
        $rs = $m_cfu->addOneFollowUp($data);
        if(!$rs) return $this->sendError(400, $m_cfu->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  批量分配主要负责人
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function assign_employee(Request $request)
    {
        $post = $request->post();
        if(empty($post['eid'])) return $this->sendError(400, 'eid error');
        if(empty($post['cu_ids'])) return $this->sendError(400, 'cu_ids error');

        $m_customer = new Customer();

        $result = $m_customer->assignToEmployee($post);

        if(!$result){
            return $this->sendError(400,$m_customer->getError());
        }

        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $id = input('id/d');
        $is_force_del = input('force/d', 0);

        $model = new Customer();

        $rs = $model->deleteOneCustomer($id, $is_force_del);
        if(!$rs) {
            if($model->get_error_code() == $model::CODE_HAVE_RELATED_DATA) {
                return $this->sendConfirm($model->getError());
            }
            return $this->sendError(400, $model->getError());
        }

        return $this->sendSuccess();

    }


    /**删除一个时间段客户名单
     * @param Request $request
     */
    public function delete_condition(Request $request)
    {
        $post = $request->post();

        $is_force_del = input('force/d', 0);

        $bid = isset($post['bid']) ? $post['bid'] : 0;
        $mc_id = isset($post['mc_id']) ? $post['mc_id'] : 0;
        $get_start_time = isset($post['get_start_time']) ? strtotime($post['get_start_time']) : 0;
        $get_end_time = isset($post['get_end_time']) ? str_to_time($post['get_end_time'],true) : 0;

        $create_start_time = isset($post['create_start_time']) ? strtotime($post['create_start_time']) : 0;
        $create_end_time = isset($post['create_end_time']) ? str_to_time($post['create_end_time'],true) : 0;

        $mCustomer = new Customer();
        $result = $mCustomer->deleteCondition($bid,$mc_id,$get_start_time,$get_end_time,$create_start_time,$create_end_time,$is_force_del);
        if($result === false)
        {
            if($mCustomer->get_error_code() == $mCustomer::CODE_HAVE_RELATED_DATA) {
                return $this->sendConfirm($mCustomer->getError());
            }
            return $this->sendError(400, $mCustomer->getError());
        }

        return $this->sendSuccess();
    }

    /**
     * @desc  搜索客户，用于学生报名
     * @author luo
     */
    public function search(Request $request)
    {
        $input = $request->param();
        $customer_model = new Customer();
        $customer_ret = $customer_model->getSearchResult($input);

        $ret = $customer_ret;

        isset($input['name']) && $input['student_name'] = $input['name'];
        $student_model = new Student();
        $student_ret = $student_model->getSearchResult($input);

        $ret['list'] = array_merge($customer_ret['list'], $student_ret['list']);
        $ret['total'] = $ret['total'] + $student_ret['total'];

        return $this->sendSuccess($ret);
    }

    /**
     * @desc  客户注册为学生
     * @author luo
     * @method POST
     */
    public function do_reg(Request $request)
    {
        $cu_id = $request->only('cu_id');
        if(empty($cu_id)) return $this->sendError(400, '参数错误');

        $model = new Customer();
        $sid = $model->changeToStudent($cu_id);
        if(!$sid) return $this->sendError(400, $model->getErrorMsg(), 400, $model->getError());

        return $this->sendSuccess($sid);
    }

    /**
     * @desc  客户转化统计
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function do_stats(Request $request)
    {
        $ret['page'] = $page = input('page', 1);
        $ret['pagesize'] = $pagesize = input('pagesize', config('default_pagesize'));

        $where = [];
        $where['og_id'] = gvar('og_id') ? gvar('og_id') : 0;


        $bids = input('bids');
        if(!empty($bids)){
            $where['bid'] = ['in', array_filter(explode(',', $bids), 'is_numeric')];
        }else{
            $bids = $request->header('x-bid');

            $arr_bids = explode(',',$bids);

            if(!empty($arr_bids)){
                $where['bid'] = ['in',$arr_bids];
            }
        }

        $create_time = input('create_time');
        if(!empty($create_time)) {
            $create_time = explode(',', trim($create_time, '[]'));
            if(strtolower($create_time[0]) == 'between' && isset($create_time[1]) && isset($create_time[2])) {
                $where['create_time'] = ['between', [strtotime($create_time[1]), strtotime($create_time[2])]];
            }
        }

        $group = input('group', 'bid');

        //根据校区统计
        if($group == 'bid') {
            $m_customer = new Customer();
            $customer_list = $m_customer->field('bid, count(cu_id) as customer_num')->where($where)
                ->group('bid')->page($page, $pagesize)->select();
            $total = $m_customer->field('bid, count(cu_id) as customer_num')->where($where)
                ->group('bid')->count();

            foreach($customer_list as &$customer_arr) {

                $signup_num = $m_customer->where('bid', $customer_arr['bid'])->where('sid > 0')->count();
                $signup_amount = $m_customer->where('bid', $customer_arr['bid'])->where('sid > 0')
                    ->sum('signup_amount');

                $customer_arr['signup_num'] = $signup_num ? $signup_num : 0;
                $customer_arr['signup_amount'] = $signup_amount ? $signup_amount : 0;

            }

            $ret['list'] = $customer_list;
            $ret['total'] = $total;
        }

        //根据跟进人统计
        if($group == 'follow_eid') {
            $m_customer = new Customer();
            $customer_list = $m_customer->field('follow_eid, count(cu_id) as customer_num')->where($where)
                ->group('follow_eid')->page($page, $pagesize)->select();
            $total = $m_customer->field('follow_eid, count(cu_id) as customer_num')->where($where)
                ->group('follow_eid')->count();

            foreach($customer_list as &$customer_arr) {
                $signup_num = $m_customer->where('follow_eid', $customer_arr['follow_eid'])->where('sid > 0')->count();
                $signup_amount = $m_customer->where('follow_eid', $customer_arr['follow_eid'])->where('sid > 0')
                    ->sum('signup_amount');

                $customer_arr['signup_num'] = $signup_num ? $signup_num : 0;
                $customer_arr['signup_amount'] = $signup_amount ? $signup_amount : 0;

                $follow_eid = $customer_arr['follow_eid'];
                $employee = Employee::get(function($query) use($follow_eid) {
                    $query->where('eid', $follow_eid)->field('eid,ename');
                });
                $customer_arr['employee'] = $employee ? $employee->toArray() : $employee;
            }

            $ret['list'] = $customer_list;
            $ret['total'] = $total;
        }

        return $this->sendSuccess($ret);
    }


    /**
     * 确定为无效客户
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function confirmUselessCustomer(Request $request)
    {
        $id = input('cu_id/d');
        $customer = Customer::get($id);
        if(empty($customer)){
            return $this->sendError(400,'客户不存在或已删除');
        }
        $m_customer = new Customer;
        $res = $m_customer->confirmUseless($customer);

        if($res === false){
            return $this->sendError(400,$customer->getErrorMsg());
        }

        return $this->sendSuccess('操作成功');
    }

    /**
     * 转入公海客户
     * @param  Request $request [description]
     * @return [type]           [description]
     * @method POST
     */
    public function intoPublicSea(Request $request){
        $input = input();
        $c_model = new Customer();
        $rs = $c_model->batIntoPublicSea($input['cu_ids']);

        if($rs === false) return $this->sendError(400, $c_model->getErrorMsg());
        return $this->sendSuccess('转入客户公海成功');
    }

    /**
     * 转出公海客户
     * @param  Request $request [description]
     * @return [type]           [description]
     * @method POST
     */
    public function outPublicSea(Request $request){
        $input = input();
        $c_model = new Customer();
        $rs = $c_model->batOutPublicSea($input['follow_eid'],$input['cu_ids']);

        if($rs === false) return $this->sendError(400, $c_model->getErrorMsg());

        return $this->sendSuccess('转出客户公海成功');

    }

    /**
     * 抢占公海客户
     * @param  Request $request [description]
     * @return [type]           [description]
     * @method POST
     */
    public function robPublicSea(Request $request){
        $input = input();
        $c_model = new Customer();
        $rs = $c_model->robPublicSea($input['cu_ids']);
        
        if($rs === false) return $this->sendError(400, $c_model->getErrorMsg());
        return $this->sendSuccess('抢占客户公海成功');

    }

}