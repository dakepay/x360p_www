<?php
namespace app\api\import;

use app\common\Import;
use think\Exception;
use util\excel;
use util\fileResponse;

class Init extends Import{
    protected $sheet_map = [
        'Params',
        'Departments',
        'Employees',
        'Subjects',
        'Lessons',
        'Classes',
        'StudentAndLessons'
    ];
	/**
     * [pre_import description]
     * @param  File   $file [description]
     * @return [type]       [description]
    */
    public function pre_import(File $file){
        $excel      = new excel();
        $real_file  = $file->getRealPath();
        $token      = make_token(request()->user->token.$real_file);
        $ret = $excel->getExcelSchema($real_file,0,1,100);

        session('last_import_file',$real_file);
        $file_info['local_file'] = $file->getRealPath();
        $file_info['size']       = $file->getSize();
        $file_info['ext']        = $file->getExtension();

        cache($token,$file_info,600);       //10分钟自动过期;

        $ret['fk']   = $token;

        return $ret;
    }

    /**
     * 导入处理
     * @return [type] [description]
     */
    public function import(){
        $sheet_index = isset($_GET['sheet'])?intval($_GET['sheet']):0;
        $sheet_count = count($this->sheet_map);
        if($sheet_index > $sheet_count-1){
            //所有都已经处理完毕
            return [
                'next'  => -1,
                'sheet' => -1
            ];
        }

        $import_cls_name = $this->sheet_map[$sheet_index];
        $class_name = 'app\\api\\import\\'.$import_cls_name;
        if(!class_exists($class_name)){
            throw new \Exception('class_not_exists:'.$class_name);   
        }

        $params = $this->params;

        $params['sheet_count'] = $sheet_count;
        $params['current_sheet_index'] = $sheet_index;

        $instance = new $class_name($params);

        return $instance->import();
    }



}