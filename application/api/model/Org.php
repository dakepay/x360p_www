<?php
/** 
 * Author: luo
 * Time: 2017-12-13 15:22
**/

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;
use think\Db;

class Org extends Base
{
    const IS_ORG = 1;
    const NOT_ORG = 0;

    const IS_INIT_YES = 1;
    const IS_INIT_NO  = 0;

    public $hidden = [ 'create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function setExpireDayAttr($value)
    {
        return $value ? format_int_day($value) : $value;
    }

    public function setOpenIntDayAttr($value)
    {
        return $value ? format_int_day($value) : $value;
    }

    public function setJoinIntDayAttr($value)
    {
        return $value ? format_int_day($value) : $value;
    }

    public function setIsStudentLimitAttr($value,$data){
        if(isset($data['student_num_limit']) && $data['student_num_limit'] > 0){
            $value = 1;
        }else{
            $value = 0;
        }
        return $value;
    }

    public function setIsAccountLimitAttr($value,$data){
        if(isset($data['account_num_limit']) && $data['account_num_limit'] > 0){
            $value = 1;
        }else{
            $value = 0;
        }
        return $value;
    }

    public function setIsBranchLimitAttr($value,$data){
        if(isset($data['branch_num_limit']) && $data['branch_num_limit'] > 0){
            $value = 1;
        }else{
            $value = 0;
        }
        return $value;
    }


    //添加机构和帐号
    public function addOrgAndAccount($org_data, $account_data = [])
    {
        $rs = $this->validateData($org_data, 'Org');
        if($rs !== true) return $this->user_error($this->getErrorMsg());

        $this->startTrans();
        try {

            $og_id = $this->addOneOrg($org_data, self::IS_ORG,$account_data);
            if($og_id === false) throw new FailResult($this->getErrorMsg());

            $org = $this->find($og_id);
            if($org['org_type'] == self::IS_ORG) {
                //在中心数据库添加域名
                $org_data['og_id'] = $og_id;
                $rs = $this->addCenterClientOfOrg($org_data);
                if ($rs === false) throw new FailResult($this->getErrorMsg());
            }

            $this->commit();
        } catch (FailResult $e) {
            $this->rollback();
            return $this->user_error($e->getMessage());
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    //添加加盟商主帐号
    public function addMainAccount($account_data, $og_id)
    {
        $this->startTrans();
        try {
            //添加超级管理员
            if (!empty($account_data)) {
                $account_data['is_admin'] = 1;
                $account_data['is_main'] = 1;
                $account_data['og_id'] = $og_id;
                $m_user = new User();
                $rs = $m_user->createEmployeeAccount($account_data);
                if ($rs === false) throw new FailResult($m_user->getErrorMsg());
            }

            $this->commit();
        } catch (FailResult $e) {
            $this->rollback();
            return $this->user_error($e->getMessage());
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }
    
    /**
     * 添加一个加盟商
     * @param [type]  $data         [description]
     * @param integer $org_type     [description]
     * @param [type]  $account_data [description]
     */
    public function addOneOrg($data, $org_type = 0,$account_data)
    {
        $this->startTrans();
        try {
            //--1-- 验证org_name
            $rs = $this->validateData($data, 'Org');
            if ($rs !== true) throw new FailResult($this->getErrorMsg());

            $forbid_host = [
                'test','demo','lantel','fuck','x360','xiao360','example'
            ];

            if(in_array($data['host'], $forbid_host)) throw new FailResult('域名已经被使用');

            $client = gvar('client');

            $add_student_nums = $data['student_num_limit'];
            $add_branch_nums = $data['branch_num_limit'];
            $add_account_nums = $data['account_num_limit'];

            /*
            if($this->checkStudentNumIsOverflow($add_student_nums)) {
                throw new FailResult('学生人数总和已经超过购买的学生人数了。');
            }
            */

            if(!$this->checkSubOrgOverFlow($add_student_nums,$add_branch_nums,$add_account_nums)){
               return false;
            }

            //--2-- 添加机构
            $data['org_type'] = $org_type;
            $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
            if ($rs === false) exception('添加机构失败');
            $og_id = $this->getAttr('og_id');

            //--3-- 如果是加盟商， 还要进行相关数据创建
            if($org_type === self::IS_ORG) {
                //设置全局og_id, 后续自动创建机构、校区、现金帐户、仓库都要用到
                gvar('og_id', $og_id);

                //复制一份系统默认的数据字典
                //$rs = $this->copySysDictionary($og_id);
                //if($rs === false) return $this->user_error($this->getErrorMsg());
                
                //创建一个默认部门(校区)
                $dept['og_id'] = $og_id;
                $dept['pid'] = 0;
                $dept['dpt_type'] = 1;
                $dept['dpt_name'] = $data['org_name'];

                $m_dept = new Department();
                $rs = $m_dept->createDepartment($dept);
                if ($rs === false) throw new FailResult($m_dept->getErrorMsg());

                //创建主账号
                 //创建一个默认员工
                if(!empty($account_data)){
                    $e['open_account'] = 1;
                    $e['user'] = [
                        'account'   => $account_data['account'],
                        'avatar'    => 'http://s1.xiao360.com/common_img/avatar.jpg',
                        'password'  => $account_data['password'],
                        'status'    => 1,
                        'is_admin'  => 1,
                        'is_main'   => 1,
                        'og_id'     => $og_id
                    ];

                    $bids = [$m_dept->bid];

                    $ename = isset($account_data['ename'])?$account_data['ename']:$data['org_name'];

                    $e['employee'] = [
                        'eid'   => 0,
                        'bids'   => $bids,
                        'ename' => $ename,
                        'is_on_job' => 1,
                        'is_part_job'   => 0,
                        'nick_name' => '管理员',
                        'rids'  => [10],
                        'sex'   => 1,
                        'og_id' => $og_id
                    ];

                    if(is_mobile($data['mobile'])){
                        $e['employee']['mobile'] = $data['mobile'];
                    }
                    if(isset($data['email']) && is_email($data['email'])){
                        $e['employee']['email'] = $data['email'];
                    }

                    $m_employee = new \app\api\model\Employee();

                    $result = $m_employee->createEmployee($e,true,false,false);

                    if(false === $result){
                        return $this->user_error('创建员工失败:'.$m_employee->getError());
                    }
                }

                //重新把og_id设为登录用户的
                gvar('og_id', self::getOgIdOfLoginUser());
            }

            $this->commit();
        } catch (FailResult $e) {
            $this->rollback();
            return $this->user_error($e->getMessage());
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return $og_id;
    }

    //更新机构
    public function updateOrg($data, $og_id, $org = null)
    {
        if(is_null($org)) {
            $org = $this->find($og_id);
        }

        $this->startTrans();

        try {

            if(!empty($data['student_num_limit']) && $data['student_num_limit'] > $org['student_num_limit']) {
                $add_nums = $data['student_num_limit'] - $org['student_num_limit'];
                if($this->checkStudentNumIsOverflow($add_nums)){
                    throw new FailResult('学生人数总和已经超过购买的学生人数了。');
                }
            }

            $rs = $this->addCenterClientOfOrg(array_merge($org->toArray(), $data));
            if($rs === false) throw new Exception($this->getErrorMsg());

            $rs = $org->allowField(true)->isUpdate(true)->save($data);
            if($rs === false) throw new Exception('更新机构信息失败');


        } catch (Exception $e) {
            $this->rollback();
            return $this->user_error($e->getMessage());
        }
        $this->commit();

        return true;
    }


    /**
     * 判断增加的学员数是否超过许可
     * @param $add_nums
     * @return bool
     */
    public function checkStudentNumIsOverflow($add_nums){
        $result = false;
        $client = gvar('client');
        $parent_cid = $client['cid'];
        $w_org['parent_cid'] = $parent_cid;
        $w_org['delete_time'] = NULL;
        $db = db('client','center_database');
        $sub_org_used_nums   = $db->name('client')->where($w_org)->sum('student_num_limit');
        //$org_num_count = $db->name('client')->where($w_org)->count();
        //$max_overflow_nums = 40;           //最大超额允许校区数
        $client_student_num_limit = $client['info']['student_num_limit'];
        $cacu_student_nums = $add_nums + $sub_org_used_nums;
        if($cacu_student_nums > $client_student_num_limit) {
            $result = true;
        }
        return $result;
    }

    /**
     * 检查加盟商额度是否超出限制
     * @param $add_student_nums
     * @param $add_branch_nums
     * @param $add_account_nums
     * @return bool
     */
    public function checkSubOrgOverFlow($add_student_nums,$add_branch_nums,$add_account_nums)
    {
        $result = true;
        $client = gvar('client');
        $client_info = $client['info'];
        $w_org['parent_cid'] = $client['cid'];
        $w_org['delete_time'] = NULL;
        $db = db('client','center_database');

        if($client_info['is_student_limit']){
            $w = array_merge($w_org,['is_student_limit'=>1]);

            $sub_org_used_nums = $db->name('client')->where($w)->sum('student_num_limit');
            $client_num_limit  = $client_info['student_num_limit'];
            $cacu_nums = $add_student_nums + $sub_org_used_nums;
            if($cacu_nums > $client_num_limit){
                $result = false;
                $this->user_error('学员数超出最大许可数:'.$client_num_limit.',已用:'.$sub_org_used_nums);
            }
        }

        if($client_info['is_branch_limit'] && $result){
            $w = [];
            $w = array_merge($w_org,['is_branch_limit'=>1]);

            $sub_org_used_nums = $db->name('client')->where($w)->sum('branch_num_limit');
            $client_num_limit = $client_info['branch_num_limit'];
            $cacu_nums = $add_branch_nums + $sub_org_used_nums;
            if($cacu_nums > $client_num_limit){
                $result = false;
                $this->user_error('校区数超出最大许可数:'.$client_num_limit.',已用:'.$sub_org_used_nums);
            }
        }

        if($client_info['is_account_limit'] && $result) {
            $w = [];
            $w = array_merge($w_org, ['is_account_limit' => 1]);

            $sub_org_used_nums = $db->name('client')->where($w)->sum('account_num_limit');
            $client_num_limit = $client_info['account_num_limit'];
            $cacu_nums = $add_account_nums + $sub_org_used_nums;
            if ($cacu_nums > $client_num_limit) {
                $result = false;
                $this->user_error('账号数超出最大许可数:' . $client_num_limit);
            }
        }
        return $result;
    }


    //根据加盟商创建中心数据库的客户信息
    public function addCenterClientOfOrg($org_data)
    {
        if(!isset($org_data['host'])) return true;
        preg_match('/^[a-z]{1}[\d|a-z]{3,9}$/', $org_data['host'], $match);
        if(empty($match)) return $this->user_error('域名参数错误');

        $data = [
            'parent_cid' => gvar('client.cid'),
            'og_id' => $org_data['og_id'],
            'client_name' => $org_data['org_name'],
            'contact' => $org_data['org_name'],
            'address' => isset($org_data['org_address']) ? $org_data['org_address'] : '',
            'tel' => isset($org_data['mobile']) ? $org_data['mobile'] : '',
            'host' => $org_data['host'],
            'expire_day' => isset($org_data['expire_day']) ? format_int_day($org_data['expire_day']) : '',
            'account_num_limit' => isset($org_data['account_num_limit']) ? $org_data['account_num_limit'] : 0,
            'student_num_limit' => isset($org_data['student_num_limit']) ? $org_data['student_num_limit'] : 0,
            'branch_num_limit' => isset($org_data['branch_num_limit']) ? $org_data['branch_num_limit'] : 0,
            'is_db_install' => 1,
        ];

        if($data['account_num_limit'] > 0){
            $data['is_account_limit'] = 1;
        }else{
            $data['is_account_limit'] = 0;
        }
        if($data['student_num_limit'] > 0){
            $data['is_student_limit'] = 1;
        }else{
            $data['is_student_limit'] = 0;
        }
        if($data['branch_num_limit'] > 0){
            $data['is_branch_limit'] = 1;
        }else{
            $data['is_branch_limit'] = 0;
        }

        $conn = Db::connect('center_database')->name('client');

        $w_c['parent_cid'] = $data['parent_cid'];
        $w_c['og_id'] = $data['og_id'];

        $ex_client = $conn->where($w_c)->find();

        if($ex_client){
            //更新
            if($data['host'] != $ex_client['host']){
                $w_c_host = [];
                $w_c_host['host'] = $data['host'];
                $w_c_host['cid']  = ['NEQ',$ex_client['cid']];

                $has_client = $conn->where($w_c_host)->find();
                if($has_client){
                    return $this->user_error('域名已经被使用，无法更改!');
                }
            }
            $w_c_update['cid'] = $ex_client['cid'];
            $result = $conn->where($w_c_update)->update($data);
            if(false === $result){
                return $this->user_error('更新加盟商信息失败！');
            }
	    $result = $ex_client['cid'];
        }else{
            //添加
            $w_c = [];
            $w_c['host'] = $data['host'];
            $has_client = $conn->where($w_c)->find();
            if($has_client){
                return $this->user_error('域名已经被使用！');
            }
                $result = $conn->insert($data,false,true);
            if(false === $result){
                return $this->user_error('创建加盟商失败!');
            }
        }
        return $result;

    }

    //删除机构相关的域名
    public function delCenterClientOfOrg($parent_cid, $og_id)
    {
        if($parent_cid < 0 || $og_id < 0) return true;

        $conn = Db::connect('center_database')->name('client');
        $rs = $conn->where('parent_cid', $parent_cid)->where('og_id', $og_id)->delete();
        if($rs === false) return $this->user_error('删除机构客户域名失败');

        return true;
    }


    public function delOneOrg($og_id, Org $org = null, $is_force = 0) {
        if(is_null($org)) {
            $org = $this->findOrFail($og_id);
        }
        $w['og_id'] = $og_id;
        $student = Student::get($w);
        if(!empty($student)) return $this->user_error('有相关学生不能删除');

        $employee_count = (new Employee())->where($w)->count();
        if($employee_count > 1) return $this->user_error('有相关老师不能删除');

        $tally = Tally::get(['og_id' => $og_id]);
        if(!empty($tally)) return $this->user_error('有相关记账不能删除');

        if(!$is_force) return $this->user_error('是否删除加盟机构，并删除相关帐号，相关校区，相关部门、仓库？', self::CODE_HAVE_RELATED_DATA);

        $this->startTrans();
        try {
            $mFranchisee = new Franchisee();
            $w_fc['fc_og_id'] = $og_id;
            $m_fc = $mFranchisee->where($w_fc)->find();
            if($m_fc){
                $m_fc->fc_og_id = 0;
                $m_fc->cid      = 0;
                $m_fc->system_status = 0;
                $result = $m_fc->save();
                if(false === $result){
                    $this->rollback();
                    return $this->sql_save_error('franchisee');
                }
            }
            $rs = User::destroy(['og_id' => $og_id]);
            if($rs === false) exception('帐号删除失败');

            $rs = Department::destroy(['og_id' => $og_id]);
            if($rs === false) exception('部门删除失败');

            $rs = Branch::destroy(['og_id' => $og_id]);
            if($rs === false) exception('校区删除失败');

            $rs = MaterialStore::destroy(['og_id' => $og_id]);
            if($rs === false) exception('仓库删除失败');

            $rs = AccountingAccount::destroy(['og_id' => $og_id]);
            if($rs === false) exception('收款帐户删除失败');

            $rs = $this->delCenterClientOfOrg(gvar('client.cid'), $og_id);
            if($rs === false) exception($this->getErrorMsg());

            $rs = $org->delete();
            if($rs === false) exception('机构删除失败');

        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception($e);
        }

        $this->commit();

        return true;
    }

    //冻结加盟商
    public function freeze($og_id, Org $org = null, $is_force = 0)
    {
        if(is_null($org)) {
            $org = $this->findOrFail($og_id);
        }

        //--1-- 如果不是强制冻结，则提醒有帐号
        /*
        $m_user = new User();
        $m_user->skipOgId();
        if(!$is_force) {
            $user_num = $m_user->where('og_id', $og_id)->count();
            if($user_num > 0) {
                return $this->user_error('是否冻结加盟商下面有相关帐号？', self::CODE_HAVE_RELATED_DATA);
            }
        }*/

        $this->startTrans();
        try {
            /*
            $rs = $m_user->where('og_id', $og_id)->update(['status' => 0]);
            if ($rs === false) return $this->user_error('冻结帐号失败');
            */
            $org->is_frozen = 1;
            $rs = $org->save();
            if ($rs === false) return $this->user_error('冻结加盟商失败');


        } catch (\Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }
        $this->commit();

        return true;
    }

    //加盟商续费
    public function renew($og_id,$data) {
        $org = $this->findOrFail($og_id);

        $this->startTrans();
        try {
            //--1-- 续费记录
            $log_data = [
                'og_id'   => $og_id,
                "pre_day" => $org['expire_day'],
                'new_day' => format_int_day($data['new_day']),
            ];
            if(strtotime($log_data['new_day']) <= $log_data['pre_day']) exception('新的到期日必须大于旧的');

            $rs = (new OrgRenewLog())->save($log_data);
            if ($rs === false) exception('续费记录失败');

            //--2-- 开放所有账号
            $rs = (new User())->where('og_id', $og_id)->update(['status' => 1]);
            if ($rs === false) exception('开放加盟商的帐号失败');

            $org->is_frozen = 0;
            $org->expire_day = $log_data['new_day'];
            $rs = $org->allowField('is_frozen,expire_day')->save();
            if ($rs === false) exception('加盟商解冻失败');

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    //加盟商解冻
    public function unfreeze($og_id) {
        $org = $this->findOrFail($og_id);

        $this->startTrans();
        try {
            $w_u['og_id'] = $og_id;
            $w_u['is_admin'] = 1;

            $org_main_user = db('user')->where($w_u)->find();

            if(!$org_main_user){
                //补充账号
                $org_info = $org->getData();
                $result = $this->fixOrgMainAccount($org_info);
                if(false === $result){
                    $this->rollback();
                    return false;
                }
            }
            //--2-- 开放所有账号
            $m_user = new User;
            $m_user->skipOgId();
            $rs = $m_user->where('og_id', $og_id)->where('is_admin = 1')->update(['status' => 1]);
            if ($rs === false) exception('开放加盟商的超级管理员失败');

            $org->is_frozen = 0;
            $rs = $org->save();
            if ($rs === false) exception('加盟商解冻失败');

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        return true;
    }

    //校区数量是否超过限制
    public static function isOverBranchLimit($og_id)
    {
        $total_branch_num = (new Branch())->count();
        $client = gvar('client');
        $branch_num_limit = !empty($client) ? $client['info']['branch_num_limit'] : 0;
        //数量为0是无限制
        if($branch_num_limit > 0 && $total_branch_num >= $branch_num_limit) return true;

        $org = Org::get($og_id);
        if(empty($org)) return false;

        if($org['branch_num_limit'] === 0) return false;

        $branch_num = (new Branch())->where('og_id', $og_id)->count();
        if($branch_num >= $org['branch_num_limit']) return true;

        return false;
    }

    //加盟商复制所有系统默认字典
    public function copySysDictionary($og_id)
    {
        $m_dictionary = new Dictionary();
        $data = $m_dictionary->where('og_id = 0')->where('pid != 0')->where('is_system = 1')->select();
        $list = collection($data)->toArray();

        $list = array_map(function($row) use($og_id){
            unset($row['did']);
            $row['og_id'] = $og_id;
            return $row;
        }, $list);

        $rs = $m_dictionary->saveAll($list, true);
        if($rs === false) return $this->user_error('增加加盟商系统字典失败');

        return true;
    }

    /**
     * 设置加盟商的配置
     * @param $set_og_id
     * @param $input
     * @return bool
     */
    public function setConfig($set_og_id,$input){
        $need_fields = ['og_id','fields'];

        if(!$this->checkInputParam($input,$need_fields)){
            return false;
        }

        $cfg_fields = $input['fields'];

        if(empty($cfg_fields)){
            return $this->user_error('请勾选要复制的配置项!');
        }

        $from_og_id = intval($input['og_id']);

        $this->startTrans();
        try {
            foreach ($cfg_fields as $f) {
                $result = $this->copyOrgConfig($set_og_id,$from_og_id,$f);
                if(!$result){
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
     * @param $to_og_id
     * @param $from_og_id
     * @param $param_name
     */
    public function copyOrgConfig($to_og_id,$from_og_id,$param_name){
        $allow_params_name = [
            'params'                =>  '系统参数',
            'org_pc_ui'             =>  'PC端界面',
            'student_mobile_ui'     =>  '学习管家界面',
            'org_mobile_ui'         =>  '教育助手界面',
            'student_option_fields' =>  '学员自定义字段',
            'org_role'              =>  '角色名称自定义'
        ];

        $ui_config_key = [
            'org_mobile_ui'      =>  'm',
            'student_mobile_ui'  => 'student',
            'school_mobile_ui'   => 'school',
            'org_pc_ui'          => 'pc'
        ];

        if(!in_array($param_name,array_keys($allow_params_name))){
            return $this->user_error('不允许的配置参数复制!:'.$param_name);
        }

        $mConfig = new Config();

        $w_cfg['og_id']     = $from_og_id;
        $w_cfg['cfg_name']  = $param_name;

        $cfg_row = $mConfig->skipOgId(true)->where($w_cfg)->find();

        if(!$cfg_row){
            return $this->user_error(sprintf('源机构的配置不存在:%s',$allow_params_name[$param_name]));
        }

        $w_cfg['og_id'] = $to_og_id;

        $ex_cfg_row = $mConfig->skipOgId(true)->where($w_cfg)->find();

        $this->startTrans();
        try {
            if (!$ex_cfg_row) {
                $new_cfg = [];
                $new_cfg['og_id'] = $to_og_id;
                $new_cfg['cfg_name'] = $param_name;
                $new_cfg['cfg_value'] = $cfg_row['cfg_value'];
                $new_cfg['format'] = $cfg_row['format'];

                if($param_name == 'params'){
                    $new_cfg['cfg_value'] = $this->default_param_value($cfg_row['cfg_value'],$to_og_id);
                }

                $result = $mConfig->data([])->isUpdate(false)->save($new_cfg);
                if (!$result) {
                    $this->rollback();
                    return $this->sql_add_error('config');
                }
            } else {
                if ($param_name == 'params') {
                    $ex_cfg_row['cfg_value'] = $this->copy_param_value($ex_cfg_row['cfg_value'], $cfg_row['cfg_value']);
                } else {
                    $ex_cfg_row['cfg_value'] = $cfg_row['cfg_value'];
                }

                $result = $ex_cfg_row->save();
                if (false === $result) {
                    $this->rollback();
                    return $this->sql_save_error('config');
                }

            }
        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();

        //centerui配置
        if(in_array($param_name,array_keys($ui_config_key))){
            $cuc_key = $ui_config_key[$param_name].'_config';
            if($from_og_id == 0){
                $from_client = gvar('client');
                $from_cid = $from_client['cid'];
            }else{
                $from_org_info = get_org_info($from_og_id);
                if(!$from_org_info){
                    return $this->user_error('源og_id错误!');
                }
                $w_from_oc['host'] = $from_org_info['host'];
                $from_client = db('client','center_database')->where($w_from_oc)->find();
                if(!$from_client){
                    return $this->user_error('未找到源客户信息:host=>'.$from_org_info['host']);
                }
                $from_cid = $from_client['cid'];
            }
            $to_org_info = get_org_info($to_og_id);
            if(!$to_org_info){
                return $this->user_error('目的og_id错误!');
            }
            $w_to_oc['host'] = $to_org_info['host'];
            $to_client = db('client','center_database')->where($w_to_oc)->find();
            if(!$to_client){
                return $this->user_error('未找到目的客户信息:host=>'.$to_org_info['host']);
            }
            $to_cid = $to_client['cid'];

            $w_from_cuc['cid'] = $from_cid;

            $from_cuc = db('client_ui_config','center_database')->where($w_from_cuc)->find();

            if(!$from_cuc){
                return $this->user_error('源客户在center数据库的配置信息不存在!cid:'.$from_cid);
            }
            $update_cuc[$cuc_key] = $from_cuc[$cuc_key];
            $w_to_cuc['cid'] = $to_cid;
            $to_cuc = db('client_ui_config','center_database')->where($w_to_cuc)->find();
            if(!$to_cuc){
                $new_cuc[$cuc_key] = $update_cuc[$cuc_key];
                $new_cuc['cid'] = $to_cid;
                db('client_ui_config','center_database')->insert($new_cuc);
            }else{
                db('client_ui_config','center_database')->where('cid',$to_cid)->update($update_cuc);
            }

            if($param_name == 'org_pc_ui'){
                $from_client = db('client','center_database')->where('cid',$from_cid)->find();
                $update_to_client['params'] = $from_client['params'];

                db('client','center_database')->where('cid',$to_cid)->update($update_to_client);
            }


        }

        return true;
    }

    /**
     * @param $dst
     * @param $src
     */
    private function copy_param_value($dst,$src){
        if(!is_array($dst)) {
            $dst_arr = json_decode($dst,true);
        }else{
            $dst_arr = $dst;
        }
        if(!is_array($src)){
            $src_arr = json_decode($src,true);
        }else{
            $src_arr = $src;
        }

        $skip_field = ['org_name','address','mobile'];

        foreach($dst_arr as $f=>$v){
            if(!in_array($f,$skip_field) && isset($src_arr[$f])){
                $dst_arr[$f] = $src_arr[$f];
            }
        }

        return $dst_arr;
    }

    private function default_param_value($cfg_value,$og_id){
        $org_info = get_org_info($og_id);
        if(is_string($cfg_value)) {
            $cfg = json_decode($cfg_value, true);
        }else{
            $cfg = $cfg_value;
        }
        $cfg['org_name'] = $org_info['org_name'];
        $cfg['address']  = $org_info['org_address'];
        $cfg['mobile'] = $org_info['mobile'];
        return $cfg;
    }


    /**
     * 开通校360系统 提交审核
     * @param  [type] $org_data [description]
     * @return [type]           [description]
     */
    public function createSystem($org_data,$account_data=[],$franchisee=[],$fc_id = 0)
    {
        $this->startTrans();
        try{

            $org_data['join_int_day'] = $franchisee['contract_start_int_day'];
            $org_data['fc_id'] = $fc_id;
            $org_data['org_short_name'] = $franchisee['org_name'];
            $org_data['init_account'] = $account_data['account'];
            $org_data['init_password'] = $account_data['password'];
            $org_data['init_status'] = $account_data['status'];
            $org_data['is_frozen'] = 0;
            $org_data['is_init'] = self::IS_INIT_NO;
            array_copy($org_data,$franchisee,['org_name','mobile','province_id','city_id','district_id','org_address','open_int_day']);
            $og_id = $this->addOrg($org_data,self::IS_ORG);

            if($og_id === false){
                $this->rollback();
                return false;
            }

            $franchisee['fc_og_id'] = $og_id;
            $franchisee['system_status'] = Franchisee::SYSTEM_STATUS_WAIT;
            $ret = $franchisee->save();
            if(!$ret){
                $this->rollback();
                return $this->sql_save_error('franchisee');
            }

        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();

        return true;

    }


    /**
     * 添加加盟商
     * @param [type]  $data         [description]
     * @param integer $org_type     [description]
     * @param [type]  $account_data [description]
     */
    public function addOrg($data, $org_type = 0)
    {
        $forbid_host = [
            'test','demo','lantel','fuck','x360','xiao360','example'
        ];

        if(in_array($data['host'], $forbid_host)){
            return $this->user_error('域名已经被使用');
        } 
	
	$w['host'] = $data['host'];
	
	$ex_host = db('client','center_database')->where($w)->find();
	if($ex_host){
		return $this->user_error('域名已经被使用');
	}

        $add_student_nums = $data['student_num_limit'];
        $add_branch_nums = $data['branch_num_limit'];
        $add_account_nums = $data['account_num_limit'];

        if(!$this->checkSubOrgOverFlow($add_student_nums,$add_branch_nums,$add_account_nums)){
           return false;
        }

        $this->startTrans();
        try {
            
            //--1-- 添加机构
            $data['org_type'] = $org_type;
            $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
            if ($rs === false){
                return $this->user_error('添加机构失败');
            } 

            $og_id = $this->getAttr('og_id');
            
        }catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();

        return $og_id;
    }


    public function updateOrgData($data,$og_id = 0,$check_host = true)
    {
        $forbid_host = [
            'test','demo','lantel','fuck','x360','xiao360','example'
        ];
        if(in_array($data['host'], $forbid_host)){
            return $this->user_error('域名已经被使用');
        } 
	
        if($check_host) {
            $w['host'] = $data['host'];

            $ex_host = db('client', 'center_database')->where($w)->find();
            if ($ex_host) {
                return $this->user_error('域名已经被使用');
            }
        }

        $data['is_frozen'] = $data['status'] ? 1 : 0;

        $add_student_nums = $data['student_num_limit'];
        $add_branch_nums = $data['branch_num_limit'];
        $add_account_nums = $data['account_num_limit'];

        if(!$this->checkSubOrgOverFlow($add_student_nums,$add_branch_nums,$add_account_nums)){
           return false;
        }

        $this->startTrans();
        try{

            $where['og_id'] = $og_id;
            $ret = $this->data([])->allowField(true)->isUpdate(true)->save($data,$where);
            if(false === $ret){
                $this->rollback();
                return $this->sql_save_error('org');
            }


        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();

        return true;
    }


    /**
     * 确定开通校360系统
     * @param  [type] $org_data [description]
     * @return [type]           [description]
     */
    public function confirmSystem($org_data,$franchisee = [],$data = [])
    {
        $this->startTrans();
        try{

            if($org_data['org_type'] == self::IS_ORG){
                $cid = $this->addCenterClientOfOrg($org_data);
                if(!$cid){
                    $this->rollback();
                    return false;
                }

                $org_data['is_init'] = self::IS_INIT_YES;
                $org_data['cid'] = $cid;
                $org_data['is_frozen'] = 0;
                $ret = $org_data->save();
                if(false === $ret){
                    $this->rollback();
                    return $this->sql_save_error('org');
                }

                $franchisee['cid'] = $cid;
                $franchisee['system_status'] = Franchisee::SYSTEM_STATUS_YES;
                $ret = $franchisee->save();
                if(false === $ret){
                    $this->rollback();
                    return $this->sql_save_error('franchisee');
                }

                $result = $this->create_init_data($org_data);
                if(!$result){
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
     * 创建初始化数据
     * @param $org_data
     * @return bool
     */
    protected function create_init_data($org_data){
        //设置全局og_id, 后续自动创建机构、校区、现金帐户、仓库都要用到
        $og_id = $org_data['og_id'];
        gvar('og_id', $og_id);
        $w_dept['og_id'] = $og_id;
        $w_dept['dpt_type'] = 1;
        $w_dept['is_delete'] = 0;
        $dept_info = db('department')->where($w_dept)->order('create_time ASC')->find();
        $this->startTrans();
        try {
            if (!$dept_info) {
                //创建一个默认部门(校区)
                $dept['og_id'] = $og_id;
                $dept['pid'] = 0;
                $dept['dpt_type'] = 1;
                $dept['dpt_name'] = $org_data['org_name'];
                $m_dept = new Department();
                $rs = $m_dept->createDepartment($dept);
                if ($rs === false) {
                    gvar('og_id', self::getOgIdOfLoginUser());
                    $this->rollback();
                    return false;
                }
            } else {
                $m_dept = new Department($dept_info);
            }

            //创建一个默认员工
            $bid = $m_dept->bid;
            $result = $this->create_main_account($org_data, $bid);
            if (!$result) {
                $this->rollback();
                return false;
            }
        }catch (\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();

        //重新把og_id设为登录用户的
        gvar('og_id', self::getOgIdOfLoginUser());
        return true;
    }

    /**
     * 创建主账号
     * @param $org_data
     * @param $bid
     * @return bool
     */
    protected function create_main_account($org_data,$bid){
        $og_id = $org_data['og_id'];
        gvar('og_id', $og_id);
        //创建一个默认员工
        $e['open_account'] = 1;
        $e['user'] = [
            'account'   => $org_data['init_account'],
            'avatar'    => 'http://s1.xiao360.com/common_img/avatar.jpg',
            'password'  => $org_data['init_password'],
            'status'    => 1,
            'is_admin'  => 1,
            'is_main'   => 1,
            'og_id'     => $og_id
        ];

        $bids = [$bid];
        $ename = $org_data['org_name'];
        $e['employee'] = [
            'eid'    => 0,
            'bids'   => $bids,
            'ename'  => $ename,
            'is_on_job' => 1,
            'is_part_job'   => 0,
            'nick_name' => '管理员',
            'rids'  => [10],
            'sex'   => 1,
            'og_id' => $og_id
        ];

        if(is_mobile($org_data['mobile'])){
            $e['employee']['mobile'] = $org_data['mobile'];
        }
        if(isset($org_data['email']) && is_email($org_data['email'])){
            $e['employee']['email'] = $org_data['email'];
        }
        $this->startTrans();
        try {
            $m_employee = new \app\api\model\Employee();
            $result = $m_employee->createEmployee($e, true, false, false);

            if (false === $result) {
                gvar('og_id', self::getOgIdOfLoginUser());
                $this->rollback();
                return $this->user_error('创建员工失败:' . $m_employee->getError());
            }
        }catch(\Exception $e){
            gvar('og_id', self::getOgIdOfLoginUser());
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        //重新把og_id设为登录用户的
        gvar('og_id', self::getOgIdOfLoginUser());
        return true;
    }


    /**
     * 修复机构主账号
     * @param $org_data
     * @return bool
     */
    public function fixOrgMainAccount($org_data){
        //设置全局og_id, 后续自动创建机构、校区、现金帐户、仓库都要用到
        return $this->create_init_data($org_data);
    }


}