<?php
namespace app\admapi\model;

use app\admapi\model\VipUser;
use app\admapi\model\VipOrder;
use app\admapi\model\App;
use think\Exception;


class Client extends Base
{

    protected $type = [
        'params' => 'json',
    ];

    public function setExpireDayAttr($value)
    {
        return $value ? format_int_day($value) : $value;
    }

    public function databaseConfig()
    {
        return $this->hasOne('DatabaseConfig', 'cid', 'cid');
    }

    public function rewLog()
    {
        return $this->hasMany("RenewLog", "cid", "cid");
    }

    public function clientFollowUp()
    {
        return $this->hasMany('ClientFollowUP', 'cid', 'cid');
    }

    public function employee()
    {
        return $this->belongsToMany('Employee', 'employee_client', 'eid', 'cid');
    }

    public function addOneClient($data)
    {
        $rs = $this->validateData($data, 'Client');
        if($rs !== true) return false;

        $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
        if($rs === false) exception('增加失败');

        return true;
    }

    public function updateClient(Client $client, $data)
    {
        $database_config = DatabaseConfig::get(['cid' => $client->cid]);
        if(!empty($database_config)) unset($data['host']);

        $rs = $client->allowField(true)->isUpdate(true)->save($data);
        if($rs === false) return $this->user_error('更新失败');

        return true;
    }

    public function deleteClient(Client $client)
    {
        $dc = DatabaseConfig::get(['cid' => $client->cid]);
        if($dc){
            //删除数据库
            $dc->uninstallDb();
            $dc->delete(true);
        }
        $rs = $client->delete(true);
        if(!$rs) return false;

        return true;
    }

    public function renew($cid, $data)
    {
        $rs = $this->validateData($data, "Client.renew");
        if($rs !== true) $this->user_error($this->getErrorMsg());

        $client = $this->findOrFail($cid);
        $renew_log_data = [
            'cid' => $client->cid,
            'pre_day' => $client->expire_day,
            'new_day' => $data['expire_day'],
        ];

        $client->expire_day = $data['expire_day'];
        $rs = $client->save();
        if($rs === false) return $this->user_error("延期失败");

        $m_rl = new RenewLog();
        $rs = $m_rl->addOneLog($renew_log_data);
        if($rs === false) return $this->user_error($m_rl->getErrorMsg());

        return true;
    }


    public function exeSql($sql = ''){
       
        if(empty($sql)){
            return $this->user_error('sql语句不能为空!');
        }
        $m_dc = DatabaseConfig::get(['cid'=>$this->cid]);
        if(!$m_dc){
            return $this->user_error('用户还未安装数据库,不能运行SQL');
        }

        $result = $m_dc->executeSql($sql);

        if(false === $result){
            return $this->user_error($m_dc->getError());
        }

        return $result;
    }

    /**
     * 恢复出厂设置
     * @return [type] [description]
     */
    public function resetDb()
    {
        $og_id = $this->og_id;
        $w['cid'] = $this->cid;
        if($this->parent_cid > 0){
            $w['cid'] = $this->parnet_cid;
        }
        $dc = DatabaseConfig::get($w);

        if(!$dc){
            return $this->user_error('数据库连接配置信息不存在!');
        }
        
        $result = $dc->resetDb($this,$og_id);

        if(!$result){
            return $this->user_error($dc->getError());
        }

        return true;
    }

    /**
     * 冻结系统
     * @return bool
     */
    public function frozen($reason = '')
    {
        $client_info = $this->getData();
        if($client_info['is_frozen'] == 1){
            return $this->user_error('客户目前已经是冻结状态！');
        }
        $this->startTrans();
        try {
            $frozen_int_day = int_day(time());

            $msg = sprintf('系统冻结,从%s开始%s',$frozen_int_day,$reason);

            $cs_id = $this->m_client_service->addSystemService($msg,301,$this->cid);
            if(!$cs_id){
                $this->rollback();
                return $this->sql_add_error('client_service');
            }
            $this->is_frozen = 1;
            $this->frozen_int_day = $frozen_int_day;
            $result = $this->save();
            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('client');
            }
        }catch(Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }


    public function unfrozen(){
        $client_info = $this->getData();
        if($client_info['is_frozen'] == 0){
            return $this->user_error('客户目前已经是解冻状态!');
        }

        try{
            $unfrozen_int_day = int_day(time());

            $frozen_int_day = $client_info['frozen_int_day'];

            $day_diff = int_day_diff($frozen_int_day,$unfrozen_int_day);

            if($day_diff > 0){
                $new_expire_day = int_day_add($this->expire_day,$day_diff);
                $this->expire_day = $new_expire_day;
            }

            $msg = sprintf('系统解冻,冻结日期:%s,到期日期增加%s天',$frozen_int_day,$day_diff);

            $cs_id = $this->m_client_service->addSystemService($msg,302,$this->cid);
            if(!$cs_id){
                $this->rollback();
                return $this->sql_add_error('client_service');
            }

            $this->is_frozen = 0;
            $this->frozen_int_day = 0;

            $result = $this->save();
            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('client');
            }


        }catch(Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

    /**
     * 续费天数
     * @return bool
     */
    public function expire($input)
    {
        $need_fields = ['expire_day','cid','eids','amount','consume_type','remark'];
        if (!$this->checkInputParam($input,$need_fields)){
            return false;
        }

        $m_client = $this->getData();
        $capacity = $input['expire_day'] - $m_client['expire_day'];
        if ($capacity < 1){
            return $this->user_error('请选择正确续费天数!');
        }

        $this->startTrans();
        try{
            $update['expire_day'] = $input['expire_day'];
            $w_update['cid'] = $m_client['cid'];
            $rs = $this->save($update,$w_update);
            if(!$rs){
                $this->rollback();
                return $this->sql_save_error('client');
            }

            $m_vcc = new VipClientConsume();
            $client_consume_data = [];
            $client_consume_data['cid'] = $m_client['cid'];
            $client_consume_data['consume_type'] = $input['consume_type'];
            $client_consume_data['amount'] = $input['amount'];
            $client_consume_data['extra_params'] = json_encode($input);
            $client_consume_data['remark'] = $input['remark'];

            $vcc_id = $m_vcc->addOneClientConsume($client_consume_data);
            if (!$vcc_id) {
                $this->rollback();
                return $this->user_error($m_vcc->getError());
            }

            $m_emp = new EmployeePerformance();
            $client_performance_data = [];
            $client_performance_data['cid'] = $m_client['cid'];
            $client_performance_data['consume_type'] = $input['consume_type'];
            $client_performance_data['vcc_id'] = $vcc_id;
            $client_performance_data['amount'] = $input['amount'];
            $result = $m_emp->batAddEmployeePerformance($input['eids'],$client_performance_data);
            if (!$result) {
                $this->rollback();
                return $this->user_error($m_emp->getError());
            }
        }catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }


    public function capacity($input)
    {
        $need_fields = ['type','num','cid','eids','amount','consume_type','remark'];
        if (!$this->checkInputParam($input,$need_fields)){
            return false;
        }

        $m_client = $this->getData();
        $update_limit = $this->updateLimit($m_client,$input['type'],$input['num']);
        $this->startTrans();
        try{
            $w_update['cid'] = $m_client['cid'];
            $rs = $this->save($update_limit,$w_update);
            if(!$rs){
                $this->rollback();
                return $this->sql_save_error('client');
            }

        $m_vcc = new VipClientConsume();
        $client_consume_data = [];
        $client_consume_data['cid'] = $m_client['cid'];
        $client_consume_data['consume_type'] = $input['consume_type'];
        $client_consume_data['amount'] = $input['amount'];
        $client_consume_data['extra_params'] = json_encode($input);
        $client_consume_data['remark'] = $input['remark'];

        $vcc_id = $m_vcc->addOneClientConsume($client_consume_data);
        if (!$vcc_id) {
            $this->rollback();
            return $this->user_error($m_vcc->getError());
        }

        $m_emp = new EmployeePerformance();
        $client_performance_data = [];
        $client_performance_data['cid'] = $m_client['cid'];
        $client_performance_data['consume_type'] = $input['consume_type'];
        $client_performance_data['vcc_id'] = $vcc_id;
        $client_performance_data['amount'] = $input['amount'];
        $result = $m_emp->batAddEmployeePerformance($input['eids'],$client_performance_data);
        if (!$result) {
            $this->rollback();
            return $this->user_error($m_emp->getError());
        }
        }catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }


    public function updateLimit($client,$type,$num)
    {
        switch ($type){
            case 'account':
                $account_num_limit = intval($num) + $client['account_num_limit'];
                $update_limit['account_num_limit'] = $account_num_limit;
                return $update_limit;
                break;
            case 'student':
                $student_num_limit = intval($num) + $client['student_num_limit'];
                $update_limit['student_num_limit'] = $student_num_limit;
                return $update_limit;
                break;
            case 'branch':
                $branch_num_limit = intval($num) + $client['branch_num_limit'];
                $update_limit['branch_num_limit'] = $branch_num_limit;
                return $update_limit;
                break;
            default:
                return $this->user_error('非法参数type!');
                break;
        }
    }

    /**
     * 后台添加应用
     * @return bool
     */
    public function clientAddApp($input){
        $m_client = $this::get($input['cid']);
        $m_app = App::get($input['app_id']);

        if(empty($m_client)) {
            return $this->user_error(400, "客户不存在");
        }
        if(empty($m_app)) {
            return $this->user_error(400, "App不存在");
        }

        $this->startTrans();
        try{
            $m_vca = new VipClientApp();
            $w = [
                'cid' => $m_client['cid'],
                'app_id' => $m_app['app_id'],
            ];
            $vca_info = $m_vca->where($w)->find();
            if (empty($vca_info)){
                $consume_type = 6;
                $client_app_data = [];
                $client_app_data['cid'] = $m_client['cid'];
                $client_app_data['og_id'] = $m_client['og_id'];
                $client_app_data['app_id'] = $m_app['app_id'];
                $client_app_data['app_ename'] = $m_app['app_ename'];
                $client_app_data['buy_time'] = int_day(time());
                $client_app_data['og_uid'] = $m_client['eid'];
                $client_app_data['status'] = VipClientApp::VIP_CLIENT_APP_ON;
                if ($input['price_type'] == 1){
                    $client_app_data['expire_int_day'] = format_int_day($input['expire_int_day']);
                }elseif($input['price_type'] == 2){
                    $client_app_data['volume_limit'] = intval($input['volume_limit']);
                }elseif($input['price_type'] == 3){
                    $client_app_data['expire_int_day'] = format_int_day($input['expire_int_day']);
                    $client_app_data['volume_limit'] = intval($input['volume_limit']);
                }
                $result = $m_vca->addOneClientApp($client_app_data);
            }else{
                $consume_type = 7;
                $client_app_uodate = [];
                if ($input['price_type'] == 1){
                    $client_app_uodate['expire_int_day'] = format_int_day($input['expire_int_day']);
                }
                if ($input['price_type'] == 2){
                    $client_app_uodate['volume_limit'] = intval($input['volume_limit']) + $vca_info['volume_limit'];
                }
                if ($input['price_type'] == 3){
                    $client_app_uodate['expire_int_day'] = format_int_day($input['expire_int_day']);
                    $client_app_uodate['volume_limit'] = intval($input['volume_limit']) + $vca_info['volume_limit'];
                }

                $result = $m_vca->updateClientApp($vca_info['vca_id'],$client_app_uodate);
            }

            if (!$result) {
                $this->rollback();
                return $this->user_error($m_vca->getError());
            }

            $m_vcc = new VipClientConsume();
            $client_consume_data = [];
            $client_consume_data['cid'] = $m_client['cid'];
            $client_consume_data['consume_type'] = $consume_type;
            $client_consume_data['amount'] = $input['amount'];
            $client_consume_data['extra_params'] = json_encode($input);
            $client_consume_data['remark'] = $input['remark'];
            $client_consume_data['app_id'] = $input['app_id'];

            $vcc_id = $m_vcc->addOneClientConsume($client_consume_data);
            if (!$vcc_id) {
                $this->rollback();
                return $this->user_error($m_vcc->getError());
            }

            $m_emp = new EmployeePerformance();
            $client_performance_data = [];
            $client_performance_data['cid'] = $m_client['cid'];
            $client_performance_data['consume_type'] = $consume_type;
            $client_performance_data['vcc_id'] = $vcc_id;
            $client_performance_data['amount'] = $input['amount'];
            $result = $m_emp->batAddEmployeePerformance($input['eids'],$client_performance_data);
            if (!$result) {
                $this->rollback();
                return $this->user_error($m_emp->getError());
            }

        }
        catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

    /**
     * 分配客户给员工
     * @param $cids
     * @param null $service_eid 客服员工ID
     * @param null $sale_eid 销售员工ID
     */
    public function assignToEmployee($cids,$service_eid = null,$sale_eid = null){
        $update_client = [];
        if(!is_null($service_eid) && $service_eid != 0){
            $update_client['service_eid'] = $service_eid;
        }
        if(!is_null($sale_eid) && $sale_eid != 0){
            $update_client['eid'] = $sale_eid;
        }
        $this->startTrans();
        try {
                foreach($cids as $cid){
                    $w_update = [];
                    $w_update['cid'] = $cid;
                    $result = (new Client())->data([])->isUpdate(true)->save($update_client,$w_update);
                    if(false === $result){
                        $this->rollback();
                        return $this->sql_save_error('client');
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