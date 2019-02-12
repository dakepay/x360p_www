<?php
/**
 * 重置数据库连接配置
 * @param $dc
 */
function reset_db_config($dc){
    config('database',$dc);
    \app\api\model\Base::ResetLinks();
    \think\Db::clear();
}
/**
 * 根据CID获得数据库配置
 * @param $cid
 * @return array|false|null|PDOStatement|string|\think\Model
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function get_dbcfg_by_cid($cid){
    $w['cid'] = $cid;
    $client = db('client','db_center')->where($w)->find();
    if(!$client){
        return null;
    }
    $w_dc['cid'] = $cid;
    if($client['parent_cid'] > 0){
        $w_dc['cid'] = $client['parent_cid'];
    }
    $dbcfg = db('database_config','db_center')->where($w_dc)->find();
    if($dbcfg){
        $dbcfg['client'] = $client;
    }
    return $dbcfg;
}
// 增加一个新的table助手函数
function table($table, $config = [])
{
	$query = \think\Db::connect($config);
	$prefix = $query->getConnection()->getConfig('prefix');
	if(strpos($table,$prefix) !== 0){
		$table = $prefix.$table;
	}
	return $query->setTable($table);
}

/**
 * 判断表是否存在
 * @param $table
 * @return bool
 */
function table_exists($table){
    $result = false;
    $dbpre  = config('database.prefix');
    $sql = "SHOW TABLES LIKE '{$dbpre}$table';";
    $row = db()->query($sql);
    if($row){
        $result = true;
    }
    return $result;
}

/**
 * 获取多行数据
 * @param $table
 * @param $w
 * @param array $field
 * @param string $order
 * @param string $limit
 * @param bool $cache
 * @param bool $skip_deleted
 * @return mixed
 */
function get_table_list($table,$w,$field = [],$order = '',$limit = '',$cache = true,$skip_deleted = true,$config = []){
    static $cache = [];
    $key_arr = func_get_args();
    $cache_id = $table.'_'.md5(json_encode($key_arr));
    if($cache && isset($cache[$cache_id])){
        return $cache[$cache_id];
    }

    try {

        $table = table($table, $config);
        if (!empty($field)) {
            $table->field($field);
        }
        if (!empty($order)) {
            $table->order($order);
        }
        if (!empty($limit)) {
            $table->limit($limit);
        }
        if ($skip_deleted && !isset($w['delete_time'])) {
            $w['delete_time'] = null;
        }

        $list = $table->where($w)->select();

        if ($list) {
            $cache[$cache_id] = $list;
        }
    }catch(\Exception $e){
        return null;
    }

    return $list;
}

/**
 * 获得表数据所有行通过yield方式生成
 * @param $table
 * @param string $order
 * @param int $pagesize
 * @param bool $skip_deleted
 * @param array $config
 * @param bool $cache
 */
function get_all_rows($table,$w,$pagesize = 500,$order = '',$skip_deleted = true,$config = [],$cache = true){
    $db = table($table,$config);

    $total = $db->where($w)->count();

    if(!$total){
        return;
    }
    $max_page = ceil($total / $pagesize) ;

    for($page = 0;$page < $max_page;$page++){
        $limit = sprintf('%s,%s',$page*$pagesize,$pagesize);
        $data_list = get_table_list($table,$w,[],$order,$limit,$cache,$skip_deleted,$config);
        if(!$data_list){
            return;
        }
        foreach($data_list as $row){
            yield $row;
        }
    }
}

/**
 * 循环所有表数据
 * @param $table
 * @param $w
 * @param int $pagesize
 * @param string $order
 * @param bool $skip_deleted
 * @param array $config
 * @param boll $cache
 * @return Generator|void
 */
function loop_all_rows($table,$w,$pagesize = 20,$order = '',$skip_deleted = true,$config = [],$cache = false){

    $db = table($table,$config);

    $total = $db->where($w)->count();

    if(!$total){
        return;
    }

    while(true){
        $limit = sprintf('%s,%s',0,$pagesize);
        $data_list = get_table_list($table,$w,[],$order,$limit,$cache,$skip_deleted,$config);
        if(!$data_list){
            break;
        }
        foreach($data_list as $row){
            yield $row;
        }
        if(count($data_list) < $pagesize){
            break;
        }
    }

    return;
}

/**
 * 获取SQL语句产生的数据记录
 * @param $sql
 * @param int $pagesize
 * @param array $config
 * @return Generator|void
 * @throws \think\db\exception\BindParamException
 * @throws \think\exception\PDOException
 */
function get_sql_result($sql,$pagesize = 100,$config = []){
    $db = db('',$config,true);
    $page = 0;
    while(true){
        $limit = sprintf('limit %s,%s',$page*$pagesize,$pagesize);
        $query_sql = str_replace("%limit%",$limit,$sql);
        $data_list = $db->query($query_sql);
        if(!$data_list){
            break;
        }
        foreach($data_list as $row){
            yield $row;
        }
        if(count($data_list) < $pagesize){
            break;
        }
    }
    return ;
}

/**
 * 循环所有sql语句产生的数据
 * @param $sql
 * @param int $pagesize
 * @param array $config
 * @return Generator|void
 * @throws \think\db\exception\BindParamException
 * @throws \think\exception\PDOException
 */
function loop_sql_result($sql,$pagesize = 100,$config = []){
    $db     = db('',$config,true);

    while(true){
        $limit  = sprintf('limit %s,%s',0,$pagesize);
        $query_sql = str_replace("%limit%",$limit,$sql);
        $data_list = $db->query($query_sql);
        if(!$data_list){
            break;
        }
        foreach($data_list as $row){
            yield $row;
        }
        if(count($data_list) < $pagesize){
            break;
        }
    }
    return ;
}

/**
 * 插入表格数据
 * @param $table
 * @param $data
 */
function insert_table_data($table,$data,$config = [],$replace = false, $getLastInsID = true){
    $table = table($table,$config);
    return $table->insert($data,$replace,$getLastInsID);
}

/**
 * 获取数据库行
 * @param  [type]  $id           [id值]
 * @param  [type]  $table        [表名]
 * @param  [type]  $id_field     [id字段]
 * @param  boolean $cache        [是否缓存]
 * @param  boolean $skip_deleted [是否排除已删除]
 * @return [type]                [description]
 */
function get_row_info($id,$table,$id_field,$cache = true,$skip_deleted = true,$config = []){
	static $caches = [];

	$w = [];
	if(is_array($id)){
		$w = $id;
		$cache_id = $table.'_'.md5(json_encode($w));
	}else{
        $cache_id = $table.'_'.$id_field.'_'.$id;
    }
	if($cache && isset($caches[$cache_id])){
		return $caches[$cache_id];
	}
	if(empty($w)){
		$w[$id_field] = $id;
	}
	if($skip_deleted && !isset($w['delete_time'])){
		$w['delete_time'] = null;
	}

	$info = table($table,$config)->where($w)->find();
	if($info){
		$caches[$cache_id] = $info;
	}
	return $info;
}

/**
 * 客户是否超出许可数量
 * @param $field
 * @return bool
 */
function is_client_overflow($field){
    $result = false;

    $client_info = gvar('client.info');
    /*
    $parent_client_info = null;
    if($client_info['og_id'] > 0) {
        $parent_client_info = db('client', 'center_database')->where('cid', $client_info['parent_cid'])->find();
    }
    */
    $num_field = $field.'_num_limit';
    $sw_field  = 'is_'.$field.'_limit';
    $current_nums = 0;
    if($client_info[$sw_field] == 1) {
        $current_nums = get_client_current_nums($client_info,$field);
        if($current_nums >= $client_info[$num_field]) {
            $result = true;
        }
    }

    return $result;
}

/**
 * 获得客户当前数量
 * @param $field
 * @return int
 */
function get_client_current_nums($client_info,$field){
    $nums = 0;
    if($field == 'student'){
        $model = model('student');
        $w['status'] = ['LT',90];
        $w['vip_level'] = ['NEQ',0];
        $w['og_id'] = $client_info['og_id'];
        $nums = $model->where($w)->count();
    }elseif($field == 'account'){
        $model = model('user');
        $w['user_type'] = 1;
        $w['status'] = 1;
        $w['og_id'] = $client_info['og_id'];
        $nums = $model->where($w)->count();
    }elseif($field == 'branch'){
        $model = model('branch');
        $w['og_id'] = $client_info['og_id'];
        $nums = $model->where($w)->count();
    }

    if($client_info['client_type'] == 0 && $client_info['is_org_open'] == 1){//加盟商客户
        $num_field = $field.'_num_limit';
        $sw_field  = 'is_'.$field.'_limit';

        $w_org['is_frozen'] = 0;        //没有冻结
        $w_org[$sw_field] = 1;
        $org_model = model('org');
        $org_model->skip_og_id_condition = true;
        $sub_nums  = $org_model->where($w_org)->sum($num_field);
        if(!$sub_nums){
            $sub_nums = 0;
        }
        $nums += $sub_nums;
    }
    return $nums;
}

/**
 * 获得支付配置信息
 * @param  [type] $lid [description]
 * @return [type]      [description]
 */
function get_config_pay_info($id,$cache = true){
    return get_row_info($id,'config_pay','cp_id',$cache);
}

/**
 * 获得试听排课信息
 * @param  [type] $tla_id [description]
 * @return [type]      [description]
 */
function get_trial_listen_arrange_info($id,$cache = true){
    return get_row_info($id,'trial_listen_arrange','tla_id',$cache);
}

/**
 * 获得作业信息
 * @param  [type] $ht_id [description]
 * @return [type]      [description]
 */
function get_homework_task_info($id,$cache = true){
    return get_row_info($id,'homework_task','ht_id',$cache);
}
/**
 * 获得课程信息
 * @param  [type] $lid [description]
 * @return [type]      [description]
 */
function get_lesson_info($id,$cache = true){
	return get_row_info($id,'lesson','lid',$cache);
}
/**
 * 获得学生信息
 * @param  [type] $sid [description]
 * @return [type]      [description]
 */
function get_student_info($id,$cache = true){
	return get_row_info($id,'student','sid',$cache);
}

/**
 * 获得图书信息
 * @param  [type] $sid [description]
 * @return [type]      [description]
 */
function get_book_info($id,$cache = true){
    return get_row_info($id,'book','bk_id',$cache);
}

/**
 * 获取加盟商信息
 * @param  [type]  $id    [description]
 * @param  boolean $cache [description]
 * @return [type]         [description]
 */
function get_franchisee_info($id,$cache = true){
    return get_row_info($id,'franchisee','fc_id',$cache);
}


/**
 * 获得学员信息
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function get_class_info($id,$cache = true){
	return get_row_info($id,'class','cid',$cache);
}
/**
 * 获得教室信息
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function get_classroom_info($id,$cache = true){
	return get_row_info($id,'classroom','cr_id',$cache);
}
/**
 * 获得科目信息
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function get_subject_info($id,$cache = true){
	return get_row_info($id,'subject','sj_id',$cache);
}
/**
 * 获得科目级别信息
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function get_sg_info($id,$cache = true){
	return get_row_info($id,'subject_grade','sg_id',$cache);
}
/**
 * 获得校区信息
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function get_branch_info($id,$cache = true){
	return get_row_info($id,'branch','bid',$cache);
}

/**
 * 获得部门信息
 * @param $id
 * @param bool $cache
 * @return mixed
 */
function get_dept_info($id,$cache = true){
    return get_row_info($id,'department','dpt_id',$cache);
}

/**
 * 获得渠道信息
 * @param $id
 * @param bool $cache
 * @return mixed
 */
function get_mc_info($id,$cache = true){
  return get_row_info($id,'market_channel','mc_id',$cache);
}

/**
 * 获得渠道名称
 * @param $id
 * @return string
 */
function get_mc_name($id){
    $info = get_mc_info($id);
    if(!$info){
        return '-';
    }
    return $info['channel_name'];
}
/**获得市场名单信息
 * @param $id
 * @param bool $cache
 * @return mixed
 */
function get_mcl_info($id,$cache = true){
    return get_row_info($id,'market_clue','mcl_id',$cache);
}

/**
 * 获得市场名单名称
 * @param $sid
 * @return string
 */
function get_mcl_name($id){
    $info = get_mcl_info($id);
    if(!$info){
        return '-';
    }
    return $info['name'];
}

/**
 * 获得客户信息
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function get_customer_info($id,$cache = true){
	return get_row_info($id,'customer','cu_id',$cache);
}

/**
 * 获得客户名称
 * @param $sid
 * @return string
 */
function get_customer_name($id){
    $info = get_customer_info($id);
    if(!$info){
        return '-';
    }
    return $info['name'];
}

/**
 * 获取加盟商名称
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function get_franchisee_name($id)
{
    $info = get_franchisee_info($id);
    if(!empty($info)){
        return $info['org_name'];
    }
    return '-';
}

/**
 * 获取课程信息
 * @param $id
 * @param bool $cache
 * @return mixed
 */
function get_student_lesson_info($id,$cache = true)
{
    return get_row_info($id,'student_lesson','sl_id',$cache);
}

/**
 * 获取考勤信息
 * @param $id
 * @param bool $cache
 * @return mixed
 */
function get_class_attendance_info($id,$cache = true)
{
    return get_row_info($id,'class_attendance','catt_id',$cache);
}

/**
 * 获得员工信息
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function get_employee_info($id,$cache = true){
	return get_row_info($id,'employee','eid',$cache);
}

/**
 * 获得员工名字
 * @param $id
 * @param bool $nickname
 * @return string
 */
function get_employee_name($id,$nickname = false){
    $info = get_employee_info($id);
    if(!$info){
        return '-';
    }
    $ret = $info['ename'];
    if($nickname && !empty($info['nick_name'])){
        $ret = $info['nick_name'];
    }
    return $ret;
}

/**
 * 获得外教员工信息
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function get_ft_employee_info($id,$cache = true){
    return get_row_info($id,'ft_employee','fe_id',$cache);
}

/**
 * 通过uid获取外教信息
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function get_ft_employee_by_uid($id){
    $employee_info = db('employee')->where('uid='.$id)->find();

    if (!$employee_info){
        return false;
    }

    $ft_mployee_info = db('ft_employee')->where('eid='.$employee_info['eid'])->find();

    if (!$ft_mployee_info){
        return false;
    }

    return $ft_mployee_info;
}


/**
 * 获得部门信息
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function get_department_info($id,$cache = true){
	return get_row_info($id,'department','dpt_id',$cache);
}

function get_department_name($id){
    $info = get_department_info($id);
    if(!$info){
        return '-';
    }
    return $info['dpt_name'];
}
/**
 * 获得字典信息
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function get_dict_info($id,$cache = true){
	return get_row_info($id,'dictionary','did',$cache);
}
/**
 * 获得机构信息
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function get_org_info($id,$cache = true){
	return get_row_info($id,'org','og_id',$cache);
}
/**
 * 获得字典title
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function get_org_name($id){
    $info = get_org_info($id);
    if(!$info){
        return '-';
    }
    return $info['org_name'];
}
/**
 * 获得订单信息
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function get_order_info($id,$cache = true){
	return get_row_info($id,'order','oid',$cache);
}
/**
 * 获得订单号信息
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function get_order_order_no($id){
    $info = get_order_info($id);
    if(!$info){
        return '-';
    }
    return $info['order_no'];
}
/**
 * 获得订单条目信息
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function get_order_item_info($id,$cache = true){
	return get_row_info($id,'order_item','oi_id',$cache);
}

/**
 * 获得订单收据信息
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function get_order_receipt_bill_info($id,$cache = true){
    return get_row_info($id,'order_receipt_bill','orb_id',$cache);
}

/**
 * 获得订单收据编号
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function get_order_receipt_bill_orb_no($id){
    $info = get_order_receipt_bill_info($id);
    if(!$info){
        return '-';
    }
    return $info['orb_no'];
}

/**
 * 获得订单支付记录
 * @param $oph_id
 * @param bool $cache
 * @return mixed
 */
function get_oph_info($oph_id,$cache = true){
    return get_row_info($oph_id,'order_payment_history','oph_id',$cache);
}
/**
 * 获得用户信息
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function get_user_info($id,$cache = true){
	return get_row_info($id,'user','uid',$cache);
}

function get_user_name($id,$cache = true){
    $info = get_user_info($id);
    if(!$info){
        return '-';
    }
    return $info['name'];
}
/**
 * 获得排课信息
 * @param  [type] $ca_id [description]
 * @return [type]        [description]
 */
function get_ca_info($id,$cache = true){
	return get_row_info($id,'course_arrange','ca_id',$cache);
}

/**
 * 获得教室信息
 * @param $id
 * @param bool $cache
 * @return mixed
 */
function get_cr_info($id,$cache = true){
    return get_row_info($id,'classroom','cr_id',$cache);
}

function get_cas_info($id,$cache = true,$skip_deleted = true){
    return get_row_info($id,'course_arrange_student','cas_id',$cache,$skip_deleted);
}
/**
 * 获得班级学员信息
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function get_cs_info($id,$cache = true){
	return get_row_info($id,'class_student','cs_id',$cache);
}
/**
 * 获得学员课程信息
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function get_sl_info($id,$cache = true){
	return get_row_info($id,'student_lesson','sl_id',$cache);
}

/**
 * 获得物品信息
 * @param $id
 * @param bool $cache
 * @return mixed
 */
function get_material_info($id,$cache = true){
	return get_row_info($id,'material','mt_id',$cache);
}

/**
 * 获得学员请假信息
 * @param $id
 * @param bool $cache
 * @return mixed
 */
function get_slv_info($id,$cache = true){
    return get_row_info($id,'student_leave','slv_id',$cache);
}

/**
 * 获得学员缺勤记录
 * @param $id
 * @param bool $cache
 * @return mixed
 */
function get_sa_info($id,$cache = true){
    return get_row_info($id,'student_absence','sa_id',$cache);
}

/**
 * 获得学员课时消耗信息
 * @param $id
 * @param bool $cache
 * @return mixed
 */
function get_slh_info($id,$cache = true){
    return get_row_info($id,'student_lesson_hour','slh_id',$cache);
}

/**
 * 获得学员考勤信息
 * @param $id
 * @param bool $cache
 * @return mixed
 */
function get_satt_info($id,$cache = true){
    return get_row_info($id,'student_attendance','satt_id',$cache);
}

/**
 * 获得补课安排信息
 * @param $id
 * @param bool $cache
 */
function get_ma_info($id,$cache = true){
    return get_row_info($id,'makeup_arrange','ma_id',$cache);
}

/**
 * 获得考勤记录信息
 * @param $id
 * @param bool $cache
 * @return mixed
 */
function get_catt_info($id,$cache = true){
    return get_row_info($id,'class_attendance','catt_id',$cache);
}

/**
 * 获得学员储值记录
 * @param $id
 * @param bool $cache
 * @return mixed
 */
function get_sdc_info($id,$cache = true){
    return get_row_info($id,'student_debit_card','sdc_id',$cache);
}

/**
 * 获得储值卡信息
 * @param $id
 * @param bool $cache
 * @return mixed
 */
function get_dc_info($id,$cache = true){
    return get_row_info($id,'debit_card','dc_id',$cache);
}

/**
 * 获取教材信息
 * @param $id
 * @param bool $cache
 * @return mixed
 */
function get_textbook_info($id,$cache = true){
    return get_row_info($id,'textbook','tb_id',$cache);
}

/**
 * 获取教材章节信息
 * @param $id
 * @param bool $cache
 * @return mixed
 */
function get_textbook_section_info($id,$cache = true){
    return get_row_info($id,'textbook_section','tbs_id',$cache);
}

/**
 * 获取教材和章节信息
 * @param $id
 * @return null
 */
function get_last_tbs_info($id){
    $last_tbs = null;
    $textbook_section_info = get_textbook_section_info($id);
    if (!empty($textbook_section_info)){
        $textbook_section = get_textbook_info($textbook_section_info['tb_id']);
        array_copy($last_tbs,$textbook_section_info,['section_title','sort']);
        array_copy($last_tbs,$textbook_section,['tb_name','tb_org_name']);
    }
    return $last_tbs;
}

/**
 * 获得字典值
 * @param $did
 * @return string
 */
function get_did_value($did,$field = 'title'){
    $did_info = get_row_info($did,'dictionary','did',true);
    if($did_info){
        return $did_info[$field];
    }
    return '-';
}

/**
 * 获得期段名称
 * @param $value
 * @return mixed
 */
function get_season_name($value){
    $w['pid'] = 12;
    $w['name'] = $value;
    $name = $value;
    $dict = get_row_info($w,'dictionary','did');
    if($dict){
        $name = $dict['title'];
    }

    return $name;
}

/**
 * 获得字典顶级ID
 * @param $cate
 * @return int
 */
function get_dict_topid($cate){
    $did = 0;
    $w['og_id'] = 0;
    $w['name']  = $cate;
    $w['pid']   = 0 ;

    $top_cate = get_row_info($w,'dictionary','did');
    if(!$top_cate){
        return $did;
    }
    return $top_cate['did'];
}

/**
 * 根据值获取字典ID
 * @param $value
 * @param $cate
 * @param string $field
 * @return int
 */
function get_dict_id($value,$cate,$field = 'title'){
    $did = 0;
    $top_did = get_dict_topid($cate);
    if(!$top_did){
        return $did;
    }
    $w['pid'] = $top_did;
    $w[$field] = $value;

    $dict = get_row_info($w,'dictionary','did');
    if($dict){
        $did = $dict['did'];
    }
    return $did;
}

/**
 * 添加字典值
 * @param $value
 * @param $cate
 * @param null $name
 */
function add_dict_value($value,$cate,$name = null){
    $ex_did = get_dict_id($value,$cate);
    if($ex_did){
        return $ex_did;
    }
    $uid = 0;
    $now_time = time();
    $top_did = get_dict_topid($cate);
    $user = gvar('user');
    if($user){
        $uid = $user['uid'];
    }
    $d['pid'] = $top_did;
    $d['title'] = $value;
    $d['desc'] = $value;
    $d['og_id'] = gvar('og_id');
    $d['name'] = $value;
    $d['create_time'] = $now_time;
    $d['update_time'] = $now_time;
    $d['create_uid']  = $uid;
    if(!is_null($name)){
        $d['name'] = $name;
    }
    $did = db('dictionary')->insert($d,true,true);
    if(!$did){
        $did = 0;
    }
    return $did;
}

/**
 * 获得年级标题
 * @param $grade
 */
function get_grade_title($grade){
    $w['pid'] = 11;
    $w['name'] = $grade;
    $did_info = get_row_info($w,'dictionary','did',true);
    if($did_info){
        return $did_info['title'];
    }
    return $grade;
}

/**
 * 根据一行记录获得课程名称
 * @param $row
 */
function get_course_name_by_row($row){
    $course_name = '';
    if(isset($row['name']) && !empty($row['name'])){
        $course_name = $row['name'];
    }elseif(isset($row['cid']) && $row['cid'] > 0) {
        $class_info = get_class_info($row['cid']);
        $course_name = $class_info['class_name'];
    }elseif(isset($row['lid']) && $row['lid'] > 0){
        $lesson_info = get_lesson_info($row['lid']);
        $course_name = $lesson_info['lesson_name'];
    }elseif(isset($row['sj_id']) && $row['sj_id']>0){
        $sj_info = get_subject_info($row['sj_id']);
        $course_name = $sj_info['subject_name'];
    }
    return $course_name;
}

/**
 * 获得微信公众号信息
 * @param $id
 * @param bool $cache
 * @return mixed
 */
function get_wxmp_info($id,$cache = true){
    return get_row_info($id,'wxmp','wxmp_id',$cache);
}
/**
 * 获得微信粉丝信息
 * @param $id
 * @param bool $cache
 * @return mixed
 */
function get_wxmp_fans_info($id,$cache = true){
    return get_row_info($id,'wxmp_fans','fans_id',$cache);
}

/**
 * 获得公立学校信息
 * @param $id
 * @param bool $cache
 * @return mixed
 */
function get_public_school_info($id,$cache = true){
    return get_row_info($id,'public_school','ps_id',$cache);
}

function get_accounting_info($id,$cache = true){
    return get_row_info($id,'accounting_account','aa_id',$cache);
}

/**
 * 获得科目信息
 * @param $id
 * @param bool $cache
 * @return mixed
 */
function get_sj_info($id,$cache = true){
    return get_row_info($id,'subject','sj_id',$cache);
}

/**
 * 获取渠道信息
 * @param  [type]  $id    [description]
 * @param  boolean $cache [description]
 * @return [type]         [description]
 */
function get_channel_info($id,$cache = true){
    return get_row_info($id,'market_channel','mc_id',$cache);
}

/**
 * 获得文件信息
 * @param $id
 * @param bool $cache
 * @return mixed
 */
function get_file_info($id,$cache = true){
    return get_row_info($id,'file','file_id',$cache);
}

/**
 * 获得杂费项目信息
 * @param $id
 * @param bool $cache
 * @return mixed
 */
function get_pi_info($id,$cache = true){
    return get_row_info($id,'pay_item','pi_id',$cache);
}

/**
 * 获得杂费条目
 * @param $id
 * @return string
 */
function get_pi_name($id){
    $info = get_pi_info($id);
    if(!$info){
        return '-';
    }
    return $info['name'];
}

/**
 * 获取课评主表
 * @param $id
 * @param bool $cache
 * @return mixed
 */
function get_review_info($id,$cache = true){
    return get_row_info($id,'review','rvw_id',$cache);
}

/**
 * 获得客户的UI配置信息
 * @param $cid
 */
function get_cuc_info($cid){
    $db_cfg = config('center_database');
    $w_cuc['cid'] = $cid;
    $info = get_row_info($w_cuc,'client_ui_config','cuc_id',true,true,$db_cfg);
    if(!$info){
        $new_info = ['cid'=>$cid];
        $t_key_map = get_cuc_tkey_map();
        foreach($t_key_map as $k=>$v){
            $new_info[$k.'_config'] = '';
        }
        if($cid) {
            $result = db('client_ui_config', $db_cfg)->insert($new_info);
            $new_info['cuc_id'] = $result;
        }else{
            $new_info['cuc_id'] = 0;
        }
        $info = $new_info;
    }
    return $info;
}

/**
 * 获得客户UI配置终端映射表
 * @return array
 */
function get_cuc_tkey_map(){
    return [
        'm'         => 'org_mobile_ui',
        'student'   => 'student_mobile_ui',
        'school'    => 'school_mobile_ui',
        'pc'        => 'org_pc_ui',
        'ft'        => 'ft_ui'
    ];
}

/**
 * 根据配置文件KEY获得tkey(终端的键值)
 * @param $key
 * @return int|string
 */
function get_cuc_tkey($key){
    $t_key_map = get_cuc_tkey_map();

    if(isset($t_key_map[$key])){
        return $key;
    }
    $t_key = 'all';
    foreach($t_key_map as $k=>$v){
        if($v == $key){
            $t_key = $k;
            break;
        }
    }
    return $t_key;
}

function get_cuc_field($t_key){
    return $t_key.'_config';
}

/**
 * 设置客户的UI配置信息
 * @param $key
 * @param $value
 * @param int $cid
 */
function set_cuc_info($key,$value,$cid = 0){
    $db_cfg = config('center_database');
    $t_key = get_cuc_tkey($key);
   if($cid == 0){
       $client = gvar('client');
       $cid = $client['cid'];
   }
   $cuc_info = get_cuc_info($cid);

    $field = get_cuc_field($t_key);

    if(!isset($cuc_info[$field])){
        return false;
    }

   if(is_array($value)){
       $value = json_encode($value,JSON_UNESCAPED_UNICODE);
   }

   $update[$field] = $value;


   $result = db('client_ui_config',$db_cfg)->where('cid',$cid)->update($update);

   return $result;
}

/**
 * 获得UI配置
 * @param string $t
 * @param int $cid
 * @return array|mixed
 */
function get_ui_config($t='',$cid = 0){
    if($cid == 0){
        $client = gvar('client');
        $cid = $client['cid'];
    }
    if($t == ''){
        $t = 'all';
    }

    $t_key_map  = get_cuc_tkey_map();
    $w_cuc['cid'] = $cid;
    $cuc_info = get_cuc_info($cid);
    $ui_config = [];
    if(isset($t_key_map[$t])){
        $t_key = $t_key_map[$t];
        $default_ui_config = config($t_key);
        $tc_key = $t.'_config';
        if(!empty($cuc_info[$tc_key]) && gvar('user.is_admin') != 1){
            $ui_config = deep_array_merge($default_ui_config,json_decode($cuc_info[$tc_key],true));
        }else{
            $ui_config = $default_ui_config;
        }

    }else{
        foreach($t_key_map as $t=>$t_key){
            $tc_key = $t.'_config';
            $default_ui_config = config($t_key);
            if(!empty($cuc_info[$tc_key]) && gvar('user.is_admin') != 1){
                $ui_config[$t] = deep_array_merge($default_ui_config,json_decode($cuc_info[$tc_key],true));
            }else{
                $ui_config[$t] = $default_ui_config;
            }
        }
    }

    return $ui_config;
}


/**
 * 根据学校ID获取学校名称
 * @param  [type] $school_id [description]
 * @return [type]            [description]
 */
function get_school_name($ps_id){

    $school_info = get_public_school_info($ps_id);

    if(!$school_info){
        return '-';
    }
    return $school_info['school_name'];
}

/**
 * 根据科目ID获取科目名称
 * @param  [type] $school_id [description]
 * @return [type]            [description]
 */
function get_subject_name($sj_id){
    $sj_info  = get_sj_info($sj_id);

    if(!$sj_info){
        return '-';
    }
    return $sj_info['subject_name'];
}


function get_class_name($cid){
    $class_info = get_class_info($cid);
    if(!$class_info){
        return '-';
    }
    return $class_info['class_name'];
}

/**
 * 根据老师ID获取老师名称
 * @param  [type] $school_id [description]
 * @return [type]            [description]
 */
function get_teacher_name($eid,$nickname = false){
    return get_employee_name($eid,$nickname);
}


/**
 * 根据教室ID获取教室名称
 * @param  [type] $school_id [description]
 * @return [type]            [description]
 */
function get_class_room($cr_id){
    $cr_info = get_cr_info($cr_id);

    if(!$cr_info){
        return '-';
    }
    return $cr_info['room_name'];
}

/**
 * 获得课程名称
 * @param $lid
 * @return string
 */
function get_lesson_name($lid){
    $lesson = get_lesson_info($lid);
    if(!$lesson){
        return '-';
    }
    return $lesson['lesson_name'];
}

/**
 * 获得学员名称
 * @param $sid
 * @return string
 */
function get_student_name($sid){
    $student = get_student_info($sid);
    if(!$student){
        return '-';
    }
    return $student['student_name'];
}

/**
 * 获得渠道名
 * @param $mc_id
 * @return string
 */
function get_channel_name($mc_id){
    $channel = get_channel_info($mc_id);
    if(!$channel){
        return '-';
    }
    return $channel['channel_name'];
}

/**
 * 获得学员卡号
 * @param $sid
 * @return string
 */
function get_student_no($sid){
    $student = get_student_info($sid);
    if(!$student){
        return '-';
    }
    return $student['card_no'];
}

/**
 * 获得账户名
 * @param $aa_id
 * @return string
 */
function get_account_name($aa_id){
    $account = get_accounting_info($aa_id);
    if(!$account){
        return '-';
    }
    return $account['name'];
}

/**
 * 获得年级名
 * @param $school_grade
 * @return mixed|string
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function get_grade_name($school_grade){
    $w['pid'] = 11;
    $w['name'] = $school_grade;
    $grade = m('dictionary')->where($w)->find();
    if(!$grade){
        $title = '-';
    }else{
        $title = $grade->title;
    }
    return $title;
}

/**
 * 获得短信模板定义
 * @param $id
 * @param bool $cache
 * @return mixed
 */
function get_std_info($id,$cache = true){
    $std_info = get_row_info($id,'sms_tpl_define','std_id',$cache);
    if($std_info){
        $std_info['tpl_define'] = json_decode($std_info['tpl_define'],true);
    }
    return $std_info;
}

/**
 * 获得学员余额变动记录
 * @param $id
 * @param bool $cache
 */
function get_smh_info($id,$cache = true){
    return get_row_info($id,'student_money_history','smh_id',$cache);
}

/**
 * 获得学情服务
 * @param $id
 * @param bool $cache
 * @return mixed
 */
function get_ss_info($id,$cache = true){
    return get_row_info($id,'study_situation','ss_id',$cache);
}

/**
 * 获得试听安排记录
 * @param $id
 * @param bool $cache
 * @return mixed
 */
function get_tla_info($id,$cache = true){
    return get_row_info($id,'trial_listen_arrange','tla_id',$cache);
}

/**
 * 学员课时是否匹配
 * @param $sl
 * @param $input
 * @return bool
 */
function is_sl_matched($sl,$input){
    $matched = false;
    if(is_array($sl['sj_ids'])){
        $arr_sj_ids = $sl['sj_ids'];
    }else{
        $arr_sj_ids = explode(',',$sl['sj_ids']);
    }
    $fit_sj_ids_length = count($arr_sj_ids);
    if($sl['remain_lesson_hours']> 0 && in_array($input['sj_id'],$arr_sj_ids)){
        $matched = true;
        if($sl['lid'] > 0 && $fit_sj_ids_length > 1 && isset($input['lesson_type'])){
            $lesson_info = get_lesson_info($sl['lid']);
            if($lesson_info['lesson_type'] != $input['lesson_type'] && $lesson_info['is_package'] == 0){
                $matched = false;
            }
        }
        if($matched) {
            if (user_config('params.enable_grade')) {
                if (isset($input['grade']) && isset($sl['grade']) && $sl['grade'] != 0) {
                    if ($input['grade'] != $sl['grade']) {
                        $matched = false;
                    }
                }
            }
        }
    }
    return $matched;
}

/**
 * 科目ID是否匹配
 * @param $sl
 * @param $input
 * @return bool
 */
function is_sj_id_matched($sl,$input){
    $matched = false;
    if(is_array($sl['sj_ids'])){
        $arr_sj_ids = $sl['sj_ids'];
    }else{
        $arr_sj_ids = explode(',',$sl['sj_ids']);
    }
    if($sl['remain_lesson_hours']> 0 && in_array($input['sj_id'],$arr_sj_ids)){
        $matched = true;
    }
    return $matched;
}

/**
 * @desc  添加服务记录
 * @author luo
 */
function add_service_record($bs_type, $data) {
    $params = user_config('params.service');
    if(!$params['auto_create_record']){
        return true;
    }
    if(empty($data['sid']) && empty($data['cu_id']) && empty($data['cid'])) return '没有服务对象';
    $service_record_desc = config('service_record_desc');
    if(empty($service_record_desc[$bs_type])) throw new \app\common\exception\FailResult('服务类型不存在');

    if(!empty($data['sid'])) {
        $data['name'] = get_student_name($data['sid']);
    } elseif(!empty($data['cid'])) {
        $data['name'] = get_class_name($data['cid']);
    } else {
        $customer = get_customer_info($data['cu_id']);
        $data['name'] = empty($customer) ? '' : $customer['name'];
    }

    $data['eid'] = !empty($data['eid']) ? $data['eid'] : \app\api\model\User::getEidByUid(gvar('uid'));
    $employee = get_employee_info($data['eid']);
    $data['ename'] = empty($employee) ? '' : $employee['ename'];
    $data['time'] = !empty($data['time']) ? $data['time'] : date('Y-m-d H:i', time());
    $content = $service_record_desc[$bs_type];

    $content_arr = preg_split('/[\{.*+\}]+/', $content);
    foreach($content_arr as &$field) {
        if(array_key_exists($field, $data)) {
            $field = $data[$field];
        }
    }

    $content = implode('', $content_arr);
    $data['content'] = $content;

    return \app\api\model\ServiceRecord::AutoAddServiceRecord($data);
}

/**
 * 根据时间 班级获取班级人数
 * @param  [type] $int_day [description]
 * @param  [type] $cid   [description]
 * @return [type]        [description]
 */
function get_class_student_num($int_day,$cid)
{
    $w['cid'] = $cid;
    $int_day = strtotime($int_day);
    
    $w1['cid'] = $cid;
    $w1['out_time'] = 0;
    $w1['in_time'] = ['lt',$int_day];
    $num1 = m('class_student')->where($w1)->count();

    $w2['cid'] = $cid;
    $w2['in_time'] = ['lt',$int_day];
    $w2['out_time'] = ['gt',$int_day];
    $num2 = m('class_student')->where($w2)->count();
    $num = $num1 + $num2;
    return $num;
}

/**
 * 获得学员缺课记录的年级id
 * @param $sa_id
 * @return mixed
 */
function get_student_absence_grade($sa_id){
    $sa_info = get_sa_info($sa_id);
    if($sa_info['cid'] > 0){
        $class_info = get_class_info($sa_info['cid']);
        return $class_info['grade'];
    }elseif($sa_info['lid'] > 0){
        $lesson_info = get_lesson_info($sa_info['lid']);
        return $lesson_info['fit_grade_start'];
    }
    return $sa_info['grade'];
}

/**
 * 获得学员缺课记录的课程ID
 * @param $sa_id
 * @return mixed
 */
function get_student_absence_lid($sa_id){
    $sa_info = get_sa_info($sa_id);
    if($sa_info['cid'] > 0){
        $class_info = get_class_info($sa_info['cid']);
        return $class_info['lid'];
    }
    return $sa_info['lid'];
}

/**
 * 获得学员课时记录的级别(年级段)
 * @param $sl
 * @return array
 */
function get_student_lesson_grade($sl){
    $ret = ['fit_grade_start'=>0,'fit_grade_end'=>0];
    if($sl['lid'] > 0){
        $lesson_info = get_lesson_info($sl['lid']);
        if($lesson_info) {
            $ret['fit_grade_start'] = $lesson_info['fit_grade_start'];
            $ret['fit_grade_end'] = $lesson_info['fit_grade_end'];
        }
    }elseif($sl['cid'] > 0){
        $class_info = get_class_info($sl['cid']);
        if($class_info){
            $ret['fit_grade_start'] = $ret['fit_grade_end']  = $class_info['grade'];
        }
    }
    return $ret;
}

/**
 * 获得学员课时的适用课程名
 * @param $sl
 * @return string
 */
function get_student_lesson_lesson_name($sl){
    $lesson_name = '';
    if($sl['cid'] > 0){
        $class_info = get_class_info($sl['cid']);
        $lesson_name = $class_info['class_name'];
    }elseif($sl['lid'] > 0){
        $lesson_info = get_lesson_info($sl['lid']);
        $lesson_name = $lesson_info['lesson_name'];
    }else{
        $arr_sj_ids = explode(',',$sl['sj_ids']);
        $sj_names = [];
        foreach($arr_sj_ids as $sj_id){
            $sj_info = get_subject_info($sj_id);
            array_push($sj_names,$sj_info['subject_name']);
        }
        $lesson_name = implode(',',$sj_names);
    }
    return $lesson_name;
}

/**
 * 根据校区ID获得分公司ID
 * @param $bid
 */
function get_dept_id_by_bid($bid){
    static $dept_id_map = [];
    if(isset($dept_id_map[$bid])){
        return $dept_id_map[$bid];
    }
    $dpt_id = 0;
    $w['og_id'] = gvar('og_id');
    $dept_list = get_table_list('department',$w);
    $branch_dept = [];
    $dept_map = [];
    foreach($dept_list as $k=>$r){
        $dept_map[$r['dpt_id']] = $r;
        if($r['bid'] == $bid){
            $branch_dept = $r;
        }
    }
    if(empty($branch_dept)){
        return $dpt_id;
    }

    $parent_dept = isset($dept_map[$branch_dept['pid']])?$dept_map[$branch_dept['pid']]:[];

    if(!empty($parent_dept) && $parent_dept['dpt_type'] == 2){
        $dpt_id = $parent_dept['dpt_id'];
        return $dpt_id;
    }

    $level = 0;
    while($parent_dept && $level < 10){
        if($parent_dept['dpt_type'] == 2){
            $dpt_id = $parent_dept['dpt_id'];
            break;
        }
        $parent_dept = isset($dept_map[$parent_dept['pid']])?$dept_map[$parent_dept['pid']]:0;
        $level++;
    }

    return $dpt_id;

}

/**
 * 根据分公司ID查询所有的校区ID
 * @param int $dpt_id
 * @return array
 */
function get_bids_by_dpt_id($dpt_id = 0){
    static $dpt_id_bids = [];
    if(empty($dpt_id_bids)){
        $dpt_id_bids = get_dpt_id_bids_map();
    }
    $ret = [];
    if(isset($dpt_id_bids[$dpt_id])){
        $ret = $dpt_id_bids[$dpt_id];
    }
    return $ret;
}

/**
 * 或得分公司校区地图表
 * @return array
 */
function get_dpt_id_bids_map(){
    $dpt_id_bids = [];
    $w['og_id'] = gvar('og_id');
    $dpt_id_bids[0] = [];
    $dept_list = get_table_list('department',$w);

    foreach($dept_list as $dept){
        if($dept['dpt_type'] == 2) {
            $dpt_id_bids[$dept['dpt_id']] = [];
        }
    }

    if(!empty($dpt_id_bids)){
        foreach($dept_list as $dept){
            if($dept['dpt_type'] == 1 && $dept['bid'] > 0){
                $company_id = get_dept_id_by_bid($dept['bid']);
                if($company_id > 0){
                    $dpt_id_bids[$company_id][] = $dept['bid'];
                }else{
                    $dpt_id_bids[0][] = $dept['bid'];
                }
            }
        }
    }
    return $dpt_id_bids;
}

/**
 * 根据校区ID获得打印变量
 * @param $bid
 */
function get_print_vars($bid){
    $ret = [];
    $config_vars = user_config('print_vars');
    $cur_dept_id = 0;
    $enable_company = user_config('params.enable_company');
    if($enable_company){
        $cur_dept_id = get_dept_id_by_bid($bid);
    }
    foreach($config_vars as $k=>$row){
        $value = $row['default'];
        $find_value = false;
        if(!empty($row['define'])){
            if(!$find_value) {
                foreach ($row['define'] as $r) {
                    if ($r['field'] == 'bid' && in_array($bid, $r['bid'])) {
                        $value = $r['value'];
                        $find_value = true;
                        break;
                    }
                }
            }

            if(!$find_value && $cur_dept_id > 0){
                foreach($row['define'] as $r){
                    if($r['field'] == 'dept_id' && in_array($cur_dept_id,$r['dept_id'])){
                        $value = $r['value'];
                        $find_value = true;
                        break;
                    }
                }
            }
        }
        $ret[$row['name']] = $value;
    }

    return $ret;
}

/**
 * 获得课程校区定义价格
 * @param $lid
 * @param int $bid
 * @return int
 */
function get_lesson_define_price($lid,$bid = 0){
    $price = 0;
    $lesson_info = get_lesson_info($lid);
    if(!$lesson_info){
        return $price;
    }
    $level_did = $lesson_info['product_level_did'];
    $enable_company = user_config('params.enable_company');

    $dept_id = 0;

    if($enable_company){
        $dept_id = get_dept_id_by_bid($bid);
    }
    $w_lpd['dtype'] = 0;
    $w_lpd['lid'] = $lid;

    $lesson_pld_rule = get_table_list('lesson_price_define',$w_lpd);

    $rule = null;

    if($lesson_pld_rule) {
        foreach ($lesson_pld_rule as $r) {
            $arr_bids = explode(',', $r['bids']);
            $arr_dept_ids = explode(',', $r['dept_ids']);

            if (in_array($bid, $arr_bids)) {
                $rule = $r;
                break;
            }

            if ($dept_id > 0 && in_array($dept_id, $arr_dept_ids)) {
                $rule = $r;
                break;
            }
        }
    }
    //按科目
    if(!$rule && !empty($lesson_info['sj_id']) && (is_numeric($lesson_info['sj_ids'])||empty($lesson_info['sj_ids']))){
        $sj_id = intval($lesson_info['sj_id']);
        $w_lpd = [];
        $w_lpd['dtype'] = 1;
        $w_lpd['sj_id'] = $sj_id;

        $lesson_pld_rule = get_table_list('lesson_price_define',$w_lpd);

        if($lesson_pld_rule){
            foreach ($lesson_pld_rule as $r) {
                $arr_bids = explode(',', $r['bids']);
                $arr_dept_ids = explode(',', $r['dept_ids']);

                if (in_array($bid, $arr_bids)) {
                    $rule = $r;
                    break;
                }

                if ($dept_id > 0 && in_array($dept_id, $arr_dept_ids)) {
                    $rule = $r;
                    break;
                }
            }
        }
    }
    //按课程级别
    if(!$rule && $level_did > 0){
        $w_lpd = [];
        $w_lpd['dtype'] = 2;
        $w_lpd['product_level_did'] = $level_did;

        $lesson_pld_rule = get_table_list('lesson_price_define',$w_lpd);

        if($lesson_pld_rule){
            foreach ($lesson_pld_rule as $r) {
                $arr_bids = explode(',', $r['bids']);
                $arr_dept_ids = explode(',', $r['dept_ids']);

                if (in_array($bid, $arr_bids)) {
                    $rule = $r;
                    break;
                }

                if ($dept_id > 0 && in_array($dept_id, $arr_dept_ids)) {
                    $rule = $r;
                    break;
                }
            }
        }
    }

    if($rule){
        $price = $rule['sale_price'];
    }

    return $price;
}

/**
 * 获得课程校区促销规则
 * @param $lid
 * @param int $bid
 */
function get_lesson_define_promotion_rule($lid,$bid = 0)
{
    $promotion_value = [];
    $lesson_info = get_lesson_info($lid);
    if(!$lesson_info){
        return $promotion_value;
    }

    $int_day = int_day(time());

    $w_pr['status'] = 1;
    $w_pr['start_time'] = ['ELT',$int_day];
    $w_pr['end_time'] = ['EGT',$int_day];
    $lesson_promotion_rule = get_table_list('promotion_rule',$w_pr);
    $lesson_promotion_rule = array_reverse($lesson_promotion_rule);

    $promotion = null;
    if (!empty($lesson_promotion_rule)){
        foreach ($lesson_promotion_rule as $rule){
            $arr_sj_ids = explode(',', $rule['suit_sj_ids']);
            $arr_lids = explode(',', $rule['suit_lids']);
            $arr_bids = explode(',', $rule['suit_bids']);

            if ($rule['is_public'] == 1){
                foreach ($arr_sj_ids as $sk => $sj_id){
                    if ($lesson_info['is_package'] == 1){
                        $sj_ids = explode(',', $lesson_info['sj_ids']);
                        if (in_array($sj_id, $sj_ids)){
                            $promotion = $rule;
                            break 2;
                        }
                    }else{
                        if ($sj_id == $lesson_info['sj_id']){
                            $promotion = $rule;
                            break 2;
                        }
                    }
                }
                if (in_array($lid, $arr_lids)) {
                    $promotion = $rule;
                    break;
                }
            }

            if (in_array($bid, $arr_bids) && !empty($arr_bids)) {
                foreach ($arr_sj_ids as $sk => $sj_id){
                    if ($lesson_info['is_package'] == 1){
                        $sj_ids = explode(',', $lesson_info['sj_ids']);
                        if (in_array($sj_id, $sj_ids)){
                            $promotion = $rule;
                            break 2;
                        }
                    }else{
                        if ($sj_id == $lesson_info['sj_id']){
                            $promotion = $rule;
                            break 2;
                        }
                    }
                }
                if (in_array($lid, $arr_lids)) {
                    $promotion = $rule;
                    break;
                }
            }
        }
    }

    return $promotion;
}

/**
 * 获得促销规则信息
 * @param  [type] $lid [description]
 * @return [type]      [description]
 */
function get_promotion_rule_info($id,$cache = true)
{
    return get_row_info($id,'promotion_rule','pr_id',$cache);

}

function get_customer_last_followup($cu_id){
    $client = gvar('client');
    $key = 'cfu-'.$client['cid'].'-'.$cu_id;
    $json = redis()->get($key);
    if($json){
        if(is_string($json)){
            $row = json_decode($json,true);
        }else{
            $row = $json;
        }
    }else{
        $w_cfu['cu_id'] = $cu_id;
        $w_cfu['is_system'] = 0;
        $row = db('customer_follow_up')->where($w_cfu)->order('cfu_id DESC')->find();
        redis()->set($key,json_encode($row));
    }
    return $row;
}

function set_customer_last_followup($cu_id,$row){
    $client = gvar('client');
    $key = 'cfu-'.$client['cid'].'-'.$cu_id;
    redis()->set($key,json_encode($row));
}

/**
 * 获得客户应用
 * @return array
 * @throws \think\Exception
 * @throws \think\exception\DbException
 */
function get_client_apps(){
    static $apps = null;
    if(!is_null($apps)){
        return $apps;
    }
    $client = gvar('client');
    $cid = $client['cid'];
    $sql = <<<EOF
SELECT 
vca.vca_id,vca.cid,vca.app_id,vca.app_ename,vca.expire_int_day,vca.volume_limit,vca.volume_used,vca.buy_time,
app.app_name,app.app_uri,app.app_icon_uri,app.app_desc,app.price_type,app.year_price,app.volume_price
from `pro_vip_client_app` vca 
LEFT JOIN `pro_app` app
ON vca.app_id = app.app_id
WHERE vca.cid = {$cid} AND vca.is_delete = 0 AND vca.status = 1
ORDER BY vca.vca_id ASC
EOF;
    $list = db('vip_client_app','db_center')->query($sql);
    if(!$list){
        $list = [];
    }
    $apps = $list;
    return $list;
}

/**
 * 获得客户应用名称列表
 * @return array|null
 * @throws \think\Exception
 * @throws \think\exception\DbException
 */
function get_client_apps_enames(){
    static $enames = null;
    if(!is_null($enames)){
        return $enames;
    }
    $apps = get_client_apps();
    $arr_ename = [];
    foreach($apps as $app){
        array_push($arr_ename,$app['app_ename']);
    }

    $enames = $arr_ename;

    return $enames;

}
/**
 * [sql_between_intday description]
 * @param  [type]  $str [description]
 * @param  integer $int [description]
 * @return [type]       [description]
 */
function sql_between_intday($str,$int = 1){
    $ret = ['BETWEEN'];
    $str = substr($str,1,-1);
    $arr = explode(',',$str);
    $from = $arr[1];
    $to   = $arr[2];

    if($int == 1){
        $from = int_day_to_date_str($from);
        $to   = int_day_to_date_str($to);
    }

    $from = strtotime($from.' 00:00:00');
    $to = strtotime($to.' 23:59:59');

    $ret[] = [$from,$to];

    return $ret;
}

/**
 * 专业课评样式列表
 */
function review_styles(){
    $review_style_dir = CONF_PATH . 'review_style';

    $styles = [];
    $styles[0] = config('org_review_tpl');
    $dh = @opendir($review_style_dir);
    while(($file = readdir($dh)) !== false){
        if($file != '.' && $file != '..') {
            if(substr($file,-4) == '.php'){
                $style = include($review_style_dir.DS.$file);
                $key   = substr($file,0,-4);
                $styles[$key] = $style;
            }
        }
    }

    return $styles;

}

/**
 * 获取学习管家默认密码
 */
function get_default_sm_pwd($tel){
    $pwd = '';
    $config = user_config('params');
    $default_sm_pwd_type = intval($config['service']['default_sm_pwd_type']);

    if (!isset($default_sm_pwd_type) || $default_sm_pwd_type > 5 || $default_sm_pwd_type < 1){
        $default_sm_pwd_type = 1;
    }

    switch ($default_sm_pwd_type)
    {
        case 1:
            $pwd = substr($tel, -6, 6);
            break;
        case 2:
            $pwd = '123456';
            break;
        case 3:
            $pwd = '888888';
            break;
        case 4:
            $pwd = '666666';
            break;
        case 5:
            $default_sm_pwd = $config['service']['default_sm_pwd'];
            $pwd = $default_sm_pwd;
            break;
    }
    
    return $pwd;
}