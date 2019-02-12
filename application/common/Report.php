<?php
namespace app\common;

/**
 * 报表基类
 * Class Report
 * @package app\common
 */
class Report extends Model
{
    const FTYPE_INT          =   "int(11) unsigned NOT NULL DEFAULT '0'";
    const FTYPE_DECIMAL52    =   "decimal(5,2) unsigned NOT NULL DEFAULT '0.00'";
    const FTYPE_DECIMAL132   =   "decimal(13,2) unsigned NOT NULL DEFAULT '0.00'";
    const FTYPE_DECIMAL156   =   "decimal(15,6) unsigned NOT NULL DEFAULT '0.000000'";
    const FTYPE_SIGNED_DECIMAL156   =   "decimal(15,6) NOT NULL DEFAULT '0.000000'";
    const FTYPE_VARCHAR64 = "varchar(64) NOT NULL DEFAULT ''";

    protected $save_to_table = true;            //保存到数据库表
    protected $report_name = '';                //报名名
    protected $report_table_name = '';          //报表表名
    protected $report_fields = [];              //报表字段
    protected $extra_title = [];                //额外表头
    protected $extra_export_fields = [];         //额外导出字段

    /**
     * 初始化处理
     * @access protected
     * @return void
     */
    protected static function init()
    {
        $model = new static();
        $model->init_table();
        //更新增加
        self::beforeInsert(array($model,'before_insert'));
        //软删除
        /*
        默认情况下查询的数据不包含软删除数据，如果需要包含软删除的数据，可以使用下面的方式查询：
        User::withTrashed()->find();
        User::withTrashed()->select();
        如果仅仅需要查询软删除的数据，可以使用：

        User::onlyTrashed()->find();
        User::onlyTrashed()->select();
         */
        self::beforeUpdate(array($model,'before_update'));
        self::beforeDelete(array($model,'before_delete'));
    }

    protected $bid_row_field_value = [];                    //单个校区字段值
    /**
     * 初始化数据表格
     */
    protected function init_table(){
        if(!table_exists($this->report_table_name)){
            $this->create_table();
        }
    }

    /**
     *获得额外表头
     * @return mixed
     */
    public function getExtraTitle(){
        return $this->extra_title;
    }

    /**
     * 获得报表字段列表
     * @return array
     */
    public function getReportFields(){
        return $this->report_fields;
    }

    /**
     * 获得额外导出字段
     * @return array
     */
    public function getExtraExportFields(){
        return $this->extra_export_fields;
    }

    /**
     * 创建表格
     * @throws \Exception
     */
    protected function create_table(){
        if(empty($this->report_table_name)){
            exception('report_table_name empty!');
        }
        $table_pre = config('database.prefix');
        $field_sql = $this->build_create_table_field_sql();
        $sql = <<<EOF
CREATE TABLE `{$table_pre}{$this->report_table_name}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  `start_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始日期',
  `end_int_day` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结束日期',
  `bid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '校区ID',
  `dept_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分公司ID',
  {$field_sql}
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='{$this->report_name}';
EOF;
        db()->execute($sql);
    }

    /**
     * 构建创建表格字段SQL
     */
    protected function build_create_table_field_sql(){
        $sql = [];
        foreach($this->report_fields as $f=>$field){
            $sql[] = sprintf("`%s` %s %s",$f,$field['type'],"COMMENT '".$field['title']."'");
        }
        $sql = implode(",\n  ",$sql);
        $sql .= ",";
        return $sql;
    }

    /**
     * 获取查询报表
     * @param $input
     */
    public function getDaySectionReport($input,$pagenation = false){
        if(!isset($input['start_date'])){
            $ds = current_week_ds();
            $input['start_date'] = $ds[0];
            $input['end_date']   = $ds[1];
        }
        $w['start_int_day'] = format_int_day($input['start_date']);
        $w['end_int_day']   = format_int_day($input['end_date']);

        $day_diff = int_day_diff($w['start_int_day'],$w['end_int_day']);

        if($day_diff > 31){
            return $this->user_error('查询时间间隔不可超过1个月');
        }
        $og_id = gvar('og_id');
        $user  = gvar('user');

        $request_bids = isset($user['employee'])?$user['employee']['bids']:[];

        $query_bids = [];
        $build_bids = [];
        if(!$request_bids){
            $request_bids = [];
        }

        $query_bids = $request_bids;

        if(isset($input['bid'])){
            if($input['bid'] == -1){
                $query_bids = [];
            }else{
                $query_bids = explode(',',$input['bid']);
            }
        }

        if(empty($query_bids)){
            $w_branch['og_id'] = $og_id;
            $branch_list = get_table_list('branch',$w_branch);
            $query_bids  = array_column($branch_list,'bid');
        }

        $w['bid'] = ['in',$query_bids];

        $db = db($this->report_table_name);

        $input['order_field'] = isset($input['order_field']) ? $input['order_field'] : '';
        $input['order_sort'] = isset($input['order_sort']) ? $input['order_sort'] : '';

        $result = $db->where($w)->order($input['order_field'],$input['order_sort'])->select();

        $result_bids = [];
        if(!$result || isset($input['refresh']) && $input['refresh'] == 1){
            $result = [];
            $build_bids = $query_bids;
        }else{
            if(count($result) < count($query_bids)){
                $result_bids = array_column($result,'bid');
                $build_bids = array_values(array_diff($query_bids,$result_bids));
            }
        }

        if(!empty($build_bids)){
            foreach($build_bids as $bid){
               $result[] = $this->buildDaySectionReport($input['start_date'],$input['end_date'],$bid);
            }
        }

        $ret['list'] = $result;
        $ret['params'] = $input;
        $ret['total'] = count($result);
        /*
         *
        $ret['query_bids'] = $query_bids;
        $ret['build_bids'] = $build_bids;
        $ret['result_bids'] = $result_bids;
        */

        $enable_company = user_config('params.enable_company');
        if($enable_company){
            $ret['list1'] = $this->getCompanyList($result);
        }else{
            $ret['list1'] = [];
        }

        return $ret;

    }

    /**
     * 按分公司汇总数据
     * @param $list
     */
    public function getCompanyList($list){
        $dept_id_map_index = [];
        $datas = [];

        foreach($list as $k=>$r){
            if(!isset($dept_id_map_index[$r['dept_id']])){
                $index = $dept_id_map_index[$r['dept_id']] = count($datas);
                $datas[] = $this->init_company_row($r['dept_id']);
            }else{
                $index = $dept_id_map_index[$r['dept_id']];
            }
            foreach($this->report_fields as $f=>$fi){
                $datas[$index][$f] += $r[$f];
            }
        }
        return $datas;
    }

    private function init_company_row($dept_id){
        $row = [];
        $row['dept_id'] = $dept_id;
        foreach($this->report_fields as $f=>$fi){
            $row[$f] = 0;
        }
        return $row;
    }

    /**
     * 构建区间报表
     * @param $start_date
     * @param $end_date
     * @param $bid
     */
    public function buildDaySectionReport($start_date,$end_date,$bid){
        $start_ts = strtotime($start_date.' 00:00');
        $end_ts   = strtotime($end_date.' 23:59');

        $start_int_day = format_int_day($start_date);
        $end_int_day   = format_int_day($end_date);

        $params['between_ts'] = [$start_ts,$end_ts];
        $params['between_int_day'] = [$start_int_day,$end_int_day];

        $params['bid'] = $bid;
        $params['og_id'] = gvar('og_id');

        $this->init_bid_row_field($params);
        $this->build_day_section_report_before($params);
        $this->build_day_section_report_center($params);
        $this->build_day_section_report_after($params);

        return $this->save_day_section_report($params);


    }

    /**
     * 初始化一行校区统计数据
     * @param $params
     */
    protected function init_bid_row_field(&$params){
        $this->bid_row_field_value = [];
        $this->bid_row_field_value['og_id'] = $params['og_id'];
        $this->bid_row_field_value['bid'] = $params['bid'];
        $this->bid_row_field_value['dept_id'] = get_dept_id_by_bid($params['bid']);
        $this->bid_row_field_value['start_int_day'] = $params['between_int_day'][0];
        $this->bid_row_field_value['end_int_day']   = $params['between_int_day'][1];
        return $this;
    }

    /**
     * 生成报表前段
     * @param $params
     */
    protected function build_day_section_report_before(&$params){

    }

    /**
     * 生成报表中段
     * @param $params
     */
    protected function build_day_section_report_center(&$params){
        foreach($this->report_fields as $field=>$row){
            if(isset($this->bid_row_field_value[$field])){
                continue;
            }
            $func = 'get_'.$field.'_value';
            if(method_exists($this,$func)){
                $this->bid_row_field_value[$field] = $this->$func($params);
            }
        }
        return $this;
    }

    /**
     * 生成报表后段
     * @param $params
     */
    protected function build_day_section_report_after(&$params){

    }

    /**
     * 保存到数据库表
     * @param bool $bool
     */
    public function saveTable($bool = true){
        $this->save_to_table = $bool;
        return $this;
    }

    /**
     * @param $params
     * @return array|false|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function save_day_section_report(&$params){
        if(!$this->save_to_table){
            return array_merge($this->bid_row_field_value,['id'=>0]);
        }

        $model  = new static();
        $w_ex['bid'] = $params['bid'];
        $w_ex['start_int_day']  = $params['between_int_day'][0];
        $w_ex['end_int_day']    = $params['between_int_day'][1];
        $ex_model = $model->where($w_ex)->find();
        if($ex_model){
            foreach($this->report_fields as $field=>$r){
                if(isset($this->bid_row_field_value[$field])){
                    $ex_model[$field] = $this->bid_row_field_value[$field];
                }
            }
            $result = $ex_model->save();
            $result = $ex_model->toArray();
        }else{
            $result = $model->save($this->bid_row_field_value);
            if(!$result){
                return [];
            }
            $result = $model->toArray();
        }
        return $result;
    }

    protected static function before_insert(&$model)
    {
        $fields = $model->getTableFields();

        $uid = gvar('uid');
        if(!$uid){
            $uid = 0;
        }

        if(!isset($model->data['create_uid'])) {
            $model->data['create_uid'] = $uid;
        }


        if(isset($model->data['create_time']) && !is_int($model->data['create_time'])) {
            $model->data['create_time'] = time();
        }

        if(isset($model->data['update_time']) && !is_int($model->data['update_time'])) {
            $model->data['update_time'] = time();
        }

        return true;
    }

    protected static function before_update(&$model)
    {
        if(isset($model->data['create_time']) && !is_int($model->data['create_time'])) {
            $model->data['create_time'] = strtotime($model->data['create_time']);
        }

        return true;
    }

    protected static function before_delete(&$model)
    {
        $uid = gvar('uid');
        if(!$uid){
            $uid = 0;
        }
        $model->data['is_delete']  = 1;
        $model->data['delete_uid'] = $uid;
        return true;
    }

}