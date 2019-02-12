<?php
namespace app\common;

use think\Request;
use think\File;
use think\Loader;
use util\excel;


abstract class Export{

	protected $params = array();
	protected $pagenation = false;
    protected $diy_field_name = '';         //自定义字段名称
    protected $option_fields = [];
    protected $enable_option_fields = null;
    protected $option_fields_cfg_name = '';

    public function __construct($input) {
        foreach($input as $k=>$v){
            $this->params[$k] = $v;
        }
        if(method_exists($this,'__init'))
            $this->__init();
    }

    protected function get_columns(){
        $params = $this->params;
        $export_params = isset($params['columns']) ? $params['columns'] : '';
        $export_params = json_decode($export_params,true);
        $columns = $this->columns;

        $new_columns = [];
        foreach ($columns as $k => $v) {
            if($export_params[$v['field']]['show'] || !isset($export_params[$v['field']]['show'])){
                $new_columns[] = $v;
            }
        }
        return $new_columns;
    }

    protected function get_title(){
        return '导出文件'.date('Y-m-d H:i:s');
    }

    protected function get_data(){

    }


    public function getTitle(){
        return $this->get_title();
    }

    public function export($save_path = ''){
        $excel = new excel();
        $export_params['title']     = $this->get_title();
        $columns   = $this->get_columns();
        $data = $this->get_data();

        if($this->is_enable_diy_fields()){
            $diy_columns = $this->getDiyColumns();
            foreach($diy_columns as $column){
                $columns[] = $column;
            }
            $this->setDiyData($data);
        }

        $export_params['columns'] = $columns;
        $func = 'customExport';
        if(method_exists($this,$func)){
            return call_user_func(array($this,$func),$data,$excel,$export_params);
        }
        return $excel->export($data,$export_params,$save_path);
    }

    /**
     * 异步导出
     * @param $xls_file Excel文件
     * @param int $pagesize 每次处理数据行
     */
    public function asyncExport($xls_file,$pagesize = 1000){
        $excel = new excel();
        $this->pagenation = true;
        $this->params['page'] = 1;
        $this->params['pagesize'] = $pagesize;
        $export_params['title']     = $this->get_title();
        $columns   = $this->get_columns();
        if($this->is_enable_diy_fields()){
            $diy_columns = $this->getDiyColumns();
            foreach($diy_columns as $column) {
                $columns[] = $column;
            }
        }
        $export_params['columns'] = $columns;

        $total_page = 10000;
        for($page=1;$page<=$total_page;$page++){
            $this->params['page'] = $page;
            $result = $this->get_data();
            $data   = $result['list'];
            if(empty($data)){
                break;
            }
            if($this->is_enable_diy_fields()){
                $this->setDiyData($data);
            }
            if($page == 1){
                $excel->createFile($data,$export_params,$xls_file);
                $total_page = ceil($result['total'] / $pagesize);

            }else{

                $start_row = ($page-1)*$pagesize + 1;
                $excel->appendData($data,$export_params,$xls_file,$start_row);
            }
        }

        return true;
    }


    /**
     * 载入导出实例
     * @param [type] $res [description]
     */
    public static function Load($func,$params = []){
        $func_name = Loader::ParseName($func,1);
        $class_name = 'app\\api\\export\\'.$func_name;


        if(!class_exists($class_name)){
            throw new \Exception('class_not_exists:'.$class_name);
        }
        return new $class_name($params);
    }

    public function is_enable_diy_fields(){
        if(!is_null($this->enable_option_fields)){
            return $this->enable_option_fields;
        }

        if($this->option_fields_cfg_name == ''){
            $this->enable_option_fields = false;
            return false;
        }

        $this->option_fields = user_config($this->option_fields_cfg_name);

        $this->enable_option_fields = false;
        foreach($this->option_fields as $of){
            if($of['enable']){
                $this->enable_option_fields = true;
                break;
            }
        }

        return $this->enable_option_fields;
    }

    /**
     * 获得自定义字段头部
     * @return array
     */
    public function getDiyColumns(){
        $columns = [];

        foreach($this->option_fields as $of){
            if($of['enable']){
                $columns[] = [
                    'field' => $of['name'],
                    'title' => $of['label']
                ];
            }
        }

        return $columns;
    }

    /**
     * 设置自定义字段值
     * @param $data
     */
    public function setDiyData(&$data){
        $diy_field_name = $this->diy_field_name;
        foreach($data as $k=>$r){
            if(isset($r[$diy_field_name])){
                $exf = $r[$diy_field_name];
                foreach($this->option_fields as $of){
                    if($of['enable']){
                        $field = $of['name'];
                        $value = isset($exf[$field])?$exf[$field]:'';
                        $data[$k][$field] = $value;

                    }
                }
            }
        }
    }







}