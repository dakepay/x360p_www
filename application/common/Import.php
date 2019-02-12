<?php
namespace app\common;

use think\Request;
use think\Response;
use think\File;
use think\Loader;
use util\excel;
use util\fileResponse;


/**
 * 导入基类
 */
abstract class Import{   
    protected $params = array();
    protected $res = '';
    protected $start_row_index = 3;
    protected $title_row_index = 1;     //标题行
    protected $pagesize = 200;
    protected $import_log = array();
    protected $error = '';
    protected $fields = array();
    protected $diy_field_name = '';         //自定义字段名称
    protected $title_row = [];              //标题行
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

    public function getTitleRow(){
        return $this->title_row;
    }

    /**
     * [pre_import description]
     * @param  File   $file [description]
     * @return [type]       [description]
    */
    public function pre_import(File $file){
    	$excel 		= new excel();
    	$real_file  = $file->getRealPath();
    	$token      = make_token(request()->user->token.$real_file);
        $ret = $excel->getExcelCount($real_file,$this->start_row_index);

        session('last_import_file',$real_file);
        $file_info['local_file'] = $file->getRealPath();
        $file_info['size'] 		 = $file->getSize();
        $file_info['ext']		 = $file->getExtension();

      	cache($token,$file_info,600);		//10分钟自动过期;

        $ret['fk']   = $token;

        if($ret['data_count'] < 20){
            $ret['size'] = $ret['data_count'];
        }else{
            $ret['size'] = intval($ret['data_count'] / 10);
            if($ret['size'] > 100){
                $ret['size'] = 100;
            }elseif($ret['size'] < 1){
                $ret['size'] = 1;
            }
        }
        return $ret;
    }


    protected function check_import($xcount){
        return true;
    }

    /**
     * 添加数据到数据库
     * @param [type] $data   [description]
     * @param [type] $row_no [description]
     * @return  0 成功
     * @return  2 失败
     * @return  1 重复
     */
    protected function add_data($data,$row_no){
        return 0;
    }


    protected function import_row(&$row,$row_no){

        $fields = $this->fields;
        $add = array();
        foreach($fields as $index=>$f){
            $field = $f['field'];
            if(!isset($row[$index])){
                continue;
            }
            $cell = $row[$index];
            if(!is_object($cell)){
                $value = $cell->getPlainText();
            }else{
                $value = $cell;
            }

            $func = 'convert_'.$field;
           
            if(empty($value)){
                if($f['must']){
                    $this->import_log[] = '第'.$row_no.'行的['.$f['name'].']没有填写!';
                    return 2;
                }
            }else{
                $add[$field] = $value;
                if(method_exists($this, $func)){
                    
                    $add[$field] = $this->$func($value,$add,$row);
                }
            }
        }

        return $this->add_data($add,$row_no);
    }


    
    public function getError(){
        return $this->error;
    }


    public function import(){
        $page       = isset($_GET['page'])?intval($_GET['page']):1;
        $pagesize   = isset($_GET['pagesize'])?intval($_GET['pagesize']):$this->pagesize;
        $sheet_index = isset($_GET['sheet'])?intval($_GET['sheet']):0;

        $xls_file = $this->params['local_file'];
        $excel = new excel();
        $xcount = $excel->getExcelCount($xls_file,$this->start_row_index,$sheet_index);


        $ret = [
            'next'=>0,
            'sheet'=>$sheet_index
        ];

        if($page == 1){
            if(!$this->check_import($xcount)){
                return false;
            };
        }

        if($this->title_row_index > 0 ){
            $rows = $excel->readAsRow($xls_file,$this->title_row_index,1);
            if(!empty($rows)){
                $this->title_row = $rows[0];
            }
        }

        $start_row = ($page - 1)*$pagesize + $this->start_row_index;

        $end_row = $start_row + $pagesize;



        $rows = $excel->readAsRow($xls_file,$start_row,$pagesize);


        $success = 0;
        $repeat  = 0;
        $failure = 0;
        if(count($rows) > 0){
            $row_no = $start_row;
            foreach($rows as $row){
                $result = $this->import_row($row,$row_no);
                if($result === 0){
                    $success++;
                }elseif($result === 1){
                    $repeat++;
                }else{
                    $failure++;
                }
                $row_no++;
            }
        }

        $ret['success'] = $success;
        $ret['repeat']  = $repeat;
        $ret['failure'] = $failure;
        $ret['log']     = $this->import_log;

        if($end_row > $xcount['rows']){
            $ret['deal'] = $xcount['rows'] - $this->start_row_index+1;
            $ret['next'] = 0;
        }else{
            $ret['next'] = $page + 1;
            $ret['deal'] = $end_row - $this->start_row_index+1;
        }

        return $ret;
    }

    /**
     * 导出模板
     */
    public function outputTpl(){
        $xls_file = $this->params['local_file'];
        $file_name = $this->params['filename'];
        $excel = new excel($xls_file);

        if($this->is_enable_diy_fields()){
            $option_fields = $this->option_fields;
            $col = count($this->fields)+1;
            $row = $this->title_row_index;
            foreach($option_fields as $of){
                if($of['enable']){
                    $excel->setCellValue($of['label'],$col,$row);
                    $col++;
                }
            }
        }
        $excel->output(null,$file_name);

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


    public function getDiyFieldValue($row){
        $ret = [];
        $regular_fields_count = count($this->fields);
        $title_row = $this->getTitleRow();
        $row_count = count($row);
        $title_field_count = count($title_row);
        if($title_field_count <= $regular_fields_count || $row_count <= $regular_fields_count){
            return $ret;
        }

        $start_column_index = $regular_fields_count;

        for($index=$start_column_index;$index<$title_field_count;$index++){

            $field = $this->getDiyFieldName($title_row[$index]);
            if($field){
                $value = $row[$index];
                $ret[$field] = $value;
            }
        }

        return $ret;

    }

    public function getDiyFieldName($title){
        static $cache = [];
        if(isset($cache[$title])){
            return $cache[$title];
        }

        $option_fields = $this->option_fields;

        $fname = false;

        foreach($option_fields as $of){
            if($of['label'] == $title && $of['enable']){
                $fname = $of['name'];
                $cache[$title] = $fname;
                break;
            }
        }
        return $fname;
    }

    /**
     * 获得Import实例
     * @param [type] $func [description]
     */
    public static function Load($func,$params = []){
    	$func_name = Loader::ParseName($func,1);
    	$class_name = 'app\\api\\import\\'.$func_name;
    	if(!class_exists($class_name)){
    		throw new \Exception('class_not_exists:'.$class_name);
    		
    	}
    	return new $class_name($params);
    }

    /**
     * 上传文件Handler
     * @param Request $request [description]
     */
    public static function Upload(Request $request){
    	$last_file = session('last_import_file');
		if(file_exists($last_file)){
			@unlink($last_file);
		}

		$import_func = $request->header('x-import-func');

		if(!$import_func){
			throw new \Exception('missing_import_func_in_header');
		}

		$file = $request->file('file');
		// 移动到框架应用根目录/public/uploads/ 目录下
		$info = $file->move(ROOT_PATH . 'public' . DS . 'data'.DS.'tmp_upload');
		if($info){
			$ext  = $info->getExtension();
			if(!in_array($ext,['xls','xlsx'])){
                @unlink($info->getRealPath());
				throw new \Exception('您上传的不是Excel文件!');
			}
			$instance = self::Load($import_func);
			return $instance->pre_import($info);

		}else{
			throw new \Exception($file->getEror());
		}
    }

    /**
     * 下载模板文件
     * @param [type] $tpl [description]
     */
    public static function DownloadImportTpl($tpl){
        $file_name = Loader::ParseName($tpl,0).'.xls';
        $tpl_file  = implode(DS,[ROOT_PATH.'application','api','import','tpl',$file_name]);
        if(file_exists($tpl_file)){
            $data['local_file'] = $tpl_file;
            $data['filename']   = $file_name;
            $instance = self::Load($tpl,$data);
            if($instance->is_enable_diy_fields()){
                return $instance->outputTpl();
            }else{
                return new fileResponse($data);
            }
        }else{
            return json(['error'=>400,'msg'=>'tpl does not exists!','tp'=>$tpl_file]);
        }
    }


    public static function CleanUploadedFile(){
        $last_file = session('last_import_file');
        if(file_exists($last_file)){
            @unlink($last_file);
        }
        return true;
    }
   


   
}