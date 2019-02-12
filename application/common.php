<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use think\Log;
use think\Request;
use think\Loader;
use think\Queue;
use think\exception\ClassNotFoundException;
use PHPMailer\PHPMailer\PHPMailer;
use think\Db;

use app\api\model\Config;

/**
 * 快速写入日志函数
 * @param  [type] $msg   [description]
 * @param  [type] $level [description]
 * @return [type]        [description]
 */
function log_write($msg,$level){
    return Log::write($msg,$level,true);
}
/**
 * 是否手机号
 * @param  [type]  $str [description]
 * @return boolean      [description]
 */
function is_mobile($str){
	return preg_match('/^1[0-9]{10}$/',$str);
}
/**
 * 检测字符串是否Email格式
 * @param  [type]  $str [description]
 * @return boolean      [description]
 */
function is_email($str){
    return preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$str);
}
/**
 * 是否日期格式
 * @param  [type]  $str [description]
 * @return boolean      [description]
 */
function is_date_format($str){
    return preg_match('/^\d{4}[-\/]?\d{1,2}[-\/]?\d{1,2}$/',$str);
}
/**
 * 检测是否字符串日期格式
 * @param  [type]  $str [description]
 * @return boolean      [description]
 */
function is_str_date_format($str){
    return preg_match('/^\d{4}[-\/]\d{2}[-\/]\d{2}$/',$str);
}
/**
 * 检测是否整形日期格式
 * @param  [type]  $str [description]
 * @return boolean      [description]
 */
function is_int_day_format($str){
    return preg_match('/^\d{4}\d{2}\d{2}$/',$str);
}

/**
 * cache key获得
 * @param  [type] $token  [description]
 * @param  string $prefix [description]
 * @return [type]         [description]
 */
function cache_key($token,$prefix = 'SESS_'){
    return $prefix.$token;
}
/**
 * @desc  设置相应域名数据库配置, 主要登录时用到
 * @author luo
 * @param $sub_host
 */
function set_host_database_conf($sub_host = null, $cid = 0) {
    $center_database_config = \think\Config::get('center_database');

    if(!is_null($sub_host)) {
        $client = db('client',$center_database_config)->where('host',$sub_host)->find();
    } elseif(is_numeric($cid) && $cid > 0) {
        $client = db('client',$center_database_config)->where('cid',$cid)->find();
    } else {
        return false;
    }

    if(!$client){
        return false;
    }
    $host_database_config = db('database_config',$center_database_config)->where('cid',$client['cid'])->find();
    if(!$host_database_config){
        if($client['parent_cid'] > 0) {
            $host_database_config = db('database_config',$center_database_config)->where('cid',$client['parent_cid'])->find();
        }
        if(!$host_database_config) {
            return false;
        }
    }

    $host_database_config = array_merge($center_database_config, $host_database_config);
    \think\Config::set('database', $host_database_config);

    return true;
}

/**
 * 密码hash算法
 * @param  [type] $origin_password [description]
 * @param  string $salt            [description]
 * @return [type]                  [description]
 */
function passwd_hash($origin_password,$salt = ''){
    return md5(md5($origin_password).$salt);
}
/**
 * 注册全局变量
 * @param  [type] $name  [description]
 * @param  [type] $value [description]
 * @return [type]        [description]
 */
function gvar($name,$value = null){
	return config('g_'.$name,$value);
}
/**
 * 注册全局变量
 * @param  [type] $name  [description]
 * @param  [type] $value [description]
 * @return [type]        [description]
 */
function app_reg($name,$value = null){
    return config('g_'.$name,$value);
}

/**
 * 操作日志全局记录
 * @param  [type] $msg [description]
 * @return [type]      [description]
 */
function action_log($msg = null){
    if(is_null($msg)){
        return app_reg('action_log');
    }
    app_reg('action_log',$msg);
}



function format_error(Exception $e){
	return <<<EOF
		'message'=>{$e->getMessage()},
		'line'	 =>{$e->getLine()},
		'file'	 =>{$e->getFile()},
		'code'	 =>{$e->getCode()},
		'trace'	 =>{$e->getTraceAsString()}
EOF;
}


if(!function_exists('_')){
	/**
	 * 多语言
	 * @param  [type] $name [description]
	 * @param  array  $vars [description]
	 * @param  string $lang [description]
	 * @return [type]       [description]
	 */
	function _($name, $vars = [], $lang = ''){
	    return lang($name, $vars, $lang);
	}
}

/**
 * loop create directory
 * @param  string  $dirs 
 * @param  integer $mode 
 * @return boolean        
 */

function mkdirss($dirs, $mode = 0777) {
    if (!is_dir($dirs)) {
        mkdirss(dirname($dirs), $mode);
        return @mkdir($dirs, $mode);
    }
    return true;
}
/**
 * write content to a file
 * @param  string $l1 
 * @param  string $l2 
 * @return boolean     
 */
function write_file($l1, $l2 = '') {
    $dir = dirname($l1);
    if (!is_dir($dir)) {
        mkdirss($dir);
    }
    return @file_put_contents($l1, $l2);
}

/**
 * read content from a file
 * @param  string $l1 
 * @return boolean     
 */
function read_file($l1) {
    return @file_get_contents($l1);
}

/**
 * write content to a file by append mode
 * @param  string $l1 
 * @param  string $l2 
 * @return string     
 */
function append_file($l1,$l2 = ''){
	$dir = dirname($l1);
    if (!is_dir($dir)) {
        mkdirss($dir);
    }
	
	if(file_exists($l1)){
		$omsg = file_get_contents($l1);
		$l2 = $omsg."\n".$l2;
	}
	
	return @file_put_contents($l1,$l2);
}

/**
 * 判断目录是否为空
 * @param  [type]  $dir [description]
 * @return boolean      [description]
 */
function is_dir_empty($dir){
    $h = @ opendir($dir);
    $i=0;   
    while($_file=readdir($h)){   
        $i++;
        if($i>2){
            break;
        }   
    }   
    closedir($h);   
    if($i>2){
        return false;
    }
    return true;  
}

/**
 * 复制整个目录
 * @param  [type] $src [description]
 * @param  [type] $dst [description]
 * @return [type]      [description]
 */
function copy_dir($src,$dst) {  
    $dir = opendir($src);
    @mkdirss($dst);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                copy_dir($src . '/' . $file,$dst . '/' . $file);
                continue;
            }
            else {
                copy($src . '/' . $file,$dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}
/**
 * 删除整个目录
 * @param  [type] $dir [description]
 * @return [type]      [description]
 */
function remove_dir($dir) {
  $dh=opendir($dir);
  while ($file=readdir($dh)) {
    if($file!="." && $file!="..") {
      $fullpath=$dir."/".$file;
      if(!is_dir($fullpath)) {
          unlink($fullpath);
      } else {
          remove_dir($fullpath);
      }
    }
  }
  closedir($dh);
  if(rmdir($dir)) {
    return true;
  } 
  return false;
}

/**
 * 创建一个Token
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function make_token($id){
    return md5($id.time().random(5));
}

/**
* 产生随机字符串
*
* @param    int        $length  输出长度
* @param    string     $chars   可选的 ，默认为 0123456789
* @return   string     字符串
*/
function random($length, $chars = '0123456789') {
    $hash = '';
    $max = strlen($chars) - 1;
    for($i = 0; $i < $length; $i++) {
        $hash .= $chars[mt_rand(0, $max)];
    }
    return $hash;
}

/**
 * 生成随机字符串
 * @param string $lenth 长度
 * @return string 字符串
 */
function random_str($lenth = 5) {
    return random($lenth, '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ');
}

/**
 * 获得数据库连接的所有表list
 * @param  [type] $db_config [description]
 * @return [type]            [description]
 */
function get_tables($db_config = ''){
	static $db_tables = [];
	if(isset($db_tables[$db_config])){
		return $db_tables[$db_config];
	}
	if($db_config == ''){
		$config = config('database');
	}else{
		$config = config($db_config);
	}
	$prefix = $config['prefix'];
	$dbname = $config['database'];

	$row_key = 'Tables_in_'.$dbname;

	$rs  = db('',$db_config)->query('show tables');
	$tables = [];

	foreach($rs as $r){
		$table  = $r[$row_key];
		if(strpos($table,$prefix) !== false){
			$table = str_replace($prefix,'',$table);
			array_push($tables,$table);
		}
	}
	$db_tables[$db_config] = $tables;

	return $tables;
}

/**
 * 实例化Model
 * @param string    $name Model名称
 * @param string    $layer 业务层名称
 * @param bool      $appendSuffix 是否添加类名后缀
 * @return \think\Model
 */
function m($name = '', $layer = 'model', $appendSuffix = false)
{
	static $instance = [];
    $guid = $name . $layer;
    if (isset($instance[$guid])) {
        return $instance[$guid];
    }
    if (false !== strpos($name, '\\')) {
        $class  = $name;
        $module = Request::instance()->module();
    } else {
        if (strpos($name, '/')) {
            list($module, $name) = explode('/', $name, 2);
        } else {
            $module = Request::instance()->module();
        }
        $class = Loader::parseClass($module, $layer, $name, $appendSuffix);
    }
    if (class_exists($class)) {
        $model = new $class();
    } else {
    	$modules = ['api','common'];
    	foreach($modules as $mod){
    		$class = str_replace('\\' . $module . '\\', '\\' . $mod . '\\', $class);
	        if (class_exists($class)) {
	            $model = new $class();
	            break;
	        }
    	}
    	if(!class_exists($class)){
    		$tables = get_tables();
    		$table  = Loader::parseName($name);
            if(in_array($table,$tables)){
                $model = new app\api\model\Base;
            	$model->name($table);
            }else{
            	throw new ClassNotFoundException('class not exists:' . $class, $class);
            } 
    	} 
    }

    $instance[$guid] = $model;
    return $model;
}

function redis($name = '', $config = [], $force = false)
{
    return new \app\common\redis\Query($options = []);
}

/**
 * 将时间戳转换成int类型的日期
 * @param  [type] $time [description]
 * @return [type]       [description]
 */
function int_day($time){
	return intval(date('Ymd',$time));
}

/**
 * 将时间戳转换成 int 类型的时间
 * @param  [type] $time [description]
 * @return [type]       [description]
 */
function int_hour($time){
	return intval(date('Hi',$time));
}

/**
 * 将int_day和int_hour连接起来
 * @param $int_day
 * @param $int_hour
 */
function int_day_hour_concat($int_day,$int_hour){
    $str_int_hour = str_pad(strval($int_hour),4,'0',STR_PAD_LEFT);
    $str_int_day  = strval($int_day);
    return intval($str_int_day.$str_int_hour);
}

/**
 * 安全过滤函数
 * @param  [type] $input_var [description]
 * @return [type]            [description]
 */
function safe_str($input_var,$length = 255){
	return $input_var;
}

/**
 * 将int类型的日期和时间转换成 时间戳
 * @param  [type] $int_day  [description]
 * @param  [type] $int_hour [description]
 * @return [type]           [description]
 */
function int_to_time($int_day,$int_hour = 0){
    $str_day = strval($int_day);
    $year = intval(substr($str_day,0,4));
    $month = intval(substr($str_day,4,2));
    $day  = intval(substr($str_day,6,2));

    return mktime(intval($int_hour/100),intval($int_hour % 100),0,$month,$day,$year);

}

/**
 * 计算2个int_day之间的天数差
 * @param $from_int_day
 * @param $to_int_day
 * @return float|int
 */
function int_day_diff($from_int_day,$to_int_day){
    if($from_int_day == $to_int_day){
        return 0;
    }
    $from_int_time = int_to_time($from_int_day,0);
    $to_int_time   = int_to_time($to_int_day,0);

    $time_diff = $to_int_time - $from_int_time;

    $day = $time_diff / 86400;
    return $day;
}
/**
 * 计算2个int_day之间的月数差
 * @param  [type] $from_int_day [description]
 * @param  [type] $to_int_day   [description]
 * @return [type]               [description]
 */
function int_month_diff($from_int_day,$to_int_day){
    if($from_int_day == $to_int_day){
        return 0;
    }
    $from_year = intval(substr(strval($from_int_day),0,4));
    $from_month = intval(substr(strval($from_int_day),4,2));

    $to_year = intval(substr(strval($to_int_day),0,4));
    $to_month = intval(substr(strval($to_int_day),4,2));

    $add_month = 0;

    if($to_year > $from_year){
        $add_month = ($to_year - $from_year)*12;
    }

    $diff_month = $to_month - $from_month;

    return $add_month + $diff_month;
}

/**
 * int类型的日期增加天数
 * @param  [type] $int_day [description]
 * @param  [type] $days    [description]
 * @return [type]          [description]
 */
function int_day_add($int_day,$days){
    $old_date = int_to_time($int_day,0);
    $new_date = $old_date + intval($days) * 86400;
    $new_int_day = int_day($new_date);
    return $new_int_day;
}

/**
 * int类型的日期添加周数
 * @param  [type] $int_day [description]
 * @param  [type] $week    [description]
 * @return [type]          [description]
 */
function int_day_add_week($int_day,$week){
    return int_day_add($int_day,$week*7);
}

/**
 * int类型的小时数增加小时
 * @param  [type] $int_hour [description]
 * @param  [type] $hours    [description]
 * @return [type]           [description]
 */
function int_hour_add($int_hour,$hours){
    $hour_add = floor($hours);
    $min_add  = ($hours * 60)%60;
    $int_hour = strval($int_hour);

    if(strlen($int_hour) <= 2){
        $base_hour = 0;
        $base_min  = intval($int_hour);
    }else{
        if(strlen($int_hour) < 4){
            $int_hour = str_pad($int_hour,4,'0',STR_PAD_LEFT);
        }
        $base_hour = intval(substr($int_hour,0,2));
        $base_min  = intval(substr($int_hour,2,2));
    }

    $end_hour = $base_hour + $hour_add;

    $end_min  = $base_min + $min_add;

    if($end_min > 60){
        $end_hour = $end_hour + 1;
        $end_min  = $end_min % 60;
    }

    if($end_min < 10){
        $ret = $end_hour.'0'.$end_min;
    }else{
        $ret = $end_hour.$end_min;
    }

    return $ret;
}

/**
 * 字符串日期格式化为int类型的日期
 * @param  [type] $int_day [description]
 * @return [type]          [description]
 */
function format_int_day($int_day){
    return preg_replace('/[^\d]/','',$int_day);
}
/**
 * 字符串日期格式化为int类型的小时
 * @param  [type] $int_hour [description]
 * @return [type]           [description]
 */
function format_int_hour($int_hour){
    return preg_replace('/[^\d]/','',$int_hour);
}

function today_start_end_time(){
    $start = int_to_time(int_day(time()));
    $end = $start + 86400;
    return [$start,$end];
}


/**
 * int类型的日期转换成字符分隔符的日期
 * @param  [type] $int_day [description]
 * @return [type]          [description]
 */
function int_day_to_date_str($int_day){
    $int_day = format_int_day($int_day);
    $arr[] = substr($int_day,0,4);
    $arr[] = substr($int_day,4,2);
    $arr[] = substr($int_day,6,2);
    return implode('-',$arr);
}

/**
 * int类型的日期转换成时间戳
 * @param $int_day
 * @return false|int
 */
function int_day_to_timestamp($int_day){
    return strtotime(int_day_to_date_str($int_day).' 00:01');
}

/**
 * 当前年份的intday
 * @param  [type] $int_day [description]
 * @return [type]          [description]
 */
function current_year_int_day($int_day){
    $int_day = format_int_day($int_day);
    $cur_year = date('Y',time());
    if($cur_year != substr($int_day,0,4)){
        $int_day = $cur_year.substr($int_day,4);
    }
    return $int_day;
}

/**
 * 获得周时间段
 * @param int $base_time 基础时间 默认为当天
 * @param int $first_day 周开始是周一还是周日
 * @return array
 */
function week_ds($base_time = 0,$first_day = 1){
    $now  = time();
    $base = $now;

    if($base_time > 0){
        $base = $base_time;
    }
    $week_day_num = intval(date('N',$base));

    if($first_day == 1){
        $first_diff_nums = $week_day_num - 1;
        $last_diff_nums  = 7 - $week_day_num;
    }else{
        $first_diff_nums = ($week_day_num == 7)?0:$week_day_num;
        $last_diff_nums  = ($week_day_num == 7)?6:7-$week_day_num;
    }

    if($first_diff_nums > 0){
        $start_time = strtotime("-$first_diff_nums days",$base);
    }else{
        $start_time = $base;
    }

    if($last_diff_nums > 0){
        $end_time = strtotime("+$last_diff_nums days",$base);
    }else{
        $end_time = $base;
    }

    if($end_time > $now){
        $end_time = $now;
    }

    $start_date = date('Y-m-d',$start_time);
    $end_date   = date('Y-m-d',$end_time);

    return [$start_date,$end_date];
}

/**
 * 获得当前周的开始结束日期
 * @param int $first_day
 * @return array
 */
function current_week_ds($first_day = 1,$auto = true){
    $now = time();
    if($auto){
        $week_day_num = intval(date('N',$now));
        if($week_day_num < 4){
            $base_time = strtotime("-1 week",$now);
        }else{
            $base_time = $now;
        }
    }else{
        $base_time = 0;
    }
    return week_ds($base_time,$first_day);
}

/**
 * 指定日期月份日期段
 * @param int $base_time
 * @return array
 */
function month_ds($base_time = 0){
    $now = time();
    $base = $now;
    if($base_time > 0){
        $base = $base_time;
    }
    $month_max_day = intval(date('t',$base));
    $month_day_num = intval(date('j',$base));

    $first_diff_nums = $month_day_num - 1;
    $last_diff_nums  = $month_max_day - $month_day_num;

    if($first_diff_nums > 0){
        $start_time = strtotime("-$first_diff_nums days",$base);
    }else{
        $start_time = $base;
    }

    if($last_diff_nums > 0){
        $end_time = strtotime("+$last_diff_nums days",$base);
    }else{
        $end_time = $base;
    }

    if($end_time > $now){
        $end_time = $now;
    }

    $start_date = date('Y-m-d',$start_time);
    $end_date   = date('Y-m-d',$end_time);
    return [$start_date,$end_date];
}

/**
 * 当前月份日期段
 * @return array
 */
function current_month_ds(){
    return month_ds(0);
}

/**
 * int类型的日期 获得星期
 * @param  [type] $int_day [description]
 * @return [type]          [description]
 */
function int_day_to_week($int_day){
    $week_str = array(
        '日','一','二','三','四','五','六'
    );
    $date_str = int_day_to_date_str($int_day);

    $date = strtotime($date_str);

    $int_week = date('w',$date);

    return $week_str[$int_week];

}

/**
 * int类型的小时数转换成小时分钟的字符串表示
 * @param  [type] $int_hour [description]
 * @return [type]           [description]
 */
function int_hour_to_hour_str($int_hour){
    $int_hour = substr($int_hour,0,4);
    $len = strlen($int_hour);
    if($len == 4){
        $str = substr($int_hour,0,2).':'.substr($int_hour,2,2);
    }elseif($len == 3){
        $str = '0'.substr($int_hour,0,1).':'.substr($int_hour,1,2);
    }elseif($len == 2){
        $str = '00:'.$int_hour;
    }else{
        $str = '00:0'.$int_hour;
    }
    return $str;
}

/**
 * 计算2个int类型的时间段相隔的时间
 * @param  [type] $int_start_hour [description]
 * @param  [type] $int_end_hour   [description]
 * @return [type]                 [description]
 */
function cacu_hours($int_start_hour,$int_end_hour){
    $int_start_hour = format_int_hour($int_start_hour);
    $int_end_hour   = format_int_hour($int_end_hour);
    $len_start_hour = strlen($int_start_hour);
    $len_end_hour   = strlen($int_end_hour);
    if($len_start_hour == 3){
        $int_s_hour = (int)substr($int_start_hour,0,1);
        $int_s_min  = (int)substr($int_start_hour,1,2);
    }elseif($len_start_hour == 2){
        $int_s_hour = 0;
        $int_s_min  = (int)substr($int_start_hour,0,2);
    }elseif($len_start_hour == 1){
        $int_s_hour = 0;
        $int_s_min  = (int)substr($int_start_hour,0,1);
    }elseif($len_start_hour == 0){
        $int_s_hour = 0;
        $int_s_min  = 0;
    }else{
        $int_s_hour = (int)substr($int_start_hour,0,2);
        $int_s_min  = (int)substr($int_start_hour,2,2);
    }
    if($len_end_hour == 3){
        $int_e_hour = (int)substr($int_end_hour,0,1);
        $int_e_min  = (int)substr($int_end_hour,1,2);
    }elseif($len_end_hour == 2){
        $int_e_hour = 0;
        $int_e_min  = (int)substr($int_end_hour,0,2);
    }elseif($len_end_hour == 1){
        $int_e_hour = 0;
        $int_e_min  = (int)substr($int_end_hour,0,1);
    }elseif($len_end_hour == 0){
        $int_e_hour = 0;
        $int_e_min  = 0;
    }else{
        $int_e_hour = (int)substr($int_end_hour,0,2);
        $int_e_min  = (int)substr($int_end_hour,2,2);
    }

    $hours_diff = ($int_e_min - $int_s_min)/60 + $int_e_hour - $int_s_hour;

    return $hours_diff;
}

/**
 * 将int类型的日期时间转换成时间戳
 * @param  [type] $int_day  [description]
 * @param  [type] $int_hour [description]
 * @return [type]           [description]
 */
function int_day_hour_to_time($int_day,$int_hour){
    $day_part[] = substr($int_day,0,4);
    $day_part[] = substr($int_day,4,2);
    $day_part[] = substr($int_day,6,2);

    $hour_part = array();

    if(strlen($int_hour) == 3){
        $hour_part[] = '0'.substr($int_hour,0,1);
        $hour_part[] = substr($int_hour,1,2);
    }else{
        $hour_part[] = substr($int_hour,0,2);
        $hour_part[] = substr($int_hour,2,2);
    }

    return strtotime(implode('-',$day_part).' '.implode(':',$hour_part));

}

/**
 * 字符串转换成时间戳
 * @param  [type] $date_str [description]
 * @return [type]           [description]
 */
function str_to_time($date_str,$day_end = false){
    if(is_date_format($date_str)){
        if(is_int_day_format($date_str)){
            $date_str = int_day_to_date_str($date_str);
        }
        if($day_end){
            $date_str .= ' 23:59:59';
        }
    }
    return strtotime($date_str); 
}


/**
 * 给字符串打码
 * @param  [type] $str      [description]
 * @param  [type] $begin    [description]
 * @param  string $mask_str [description]
 * @return [type]           [description]
 */
function mask_text($str,$begin,$mask_str = '*'){
    $len = mstrlen($str);
    if($len <= $begin){
        return $str;
    }
    $new_str = msubstr($str,0,$begin,'utf-8',false);
    for($i=$begin;$i<$len;$i++){
        $new_str .= $mask_str;
    }
    return $new_str;
}

/**
 * 取最小值
 * @param  [type]  $val [description]
 * @param  integer $min [description]
 * @return [type]       [description]
 */
function min_val($val,$min = 0){
    if($val < $min){
        return $min;
    }
    return $val;
}
/**
 * 取最大值
 * @param  [type] $val [description]
 * @param  [type] $max [description]
 * @return [type]      [description]
 */
function max_val($val,$max){
    if($val > $max){
        return $val;
    }
    return $max;
}

/**
 * 获得当前季节
 * @return [type] [description]
 */
function get_current_season(){
    $base_time = time();
    $month_season_maps = [
        1=>'H',
        2=>'H',
        3=>'C',
        4=>'C',
        5=>'C',
        6=>'C',
        7=>'S',
        8=>'S',
        9=>'Q',
        10=>'Q',
        11=>'Q',
        12=>'Q'
    ];

    $month = date('n',$base_time);

    return $month_season_maps[$month];
}



/**
 * 模板替换函数
 * @param  [type] $format [description]
 * @param  [type] &$data  [description]
 * @return [type]         [description]
 */
function tpl_replace($format,&$data){
    $ret = $format;
    $reg = '/\$?\{([^\}]+)\}/';

    if(preg_match_all($reg,$format,$matches)){
        $index = 0;
        foreach($matches[0] as $pattern){
            if(isset($data[$matches[1][$index]])){
                $ret = str_replace($pattern,$data[$matches[1][$index]],$ret);
            }
            $index++;
        }
    }
    return $ret;
}

/**
 * 根据校区ID获得校区名
 * @param  [type] $bid [description]
 * @return [type]      [description]
 */
function get_branch_name($bid){
    static $bid_maps = [];

    if(isset($bid_maps[$bid])){
        return $bid_maps[$bid];
    }

    $branch = get_branch_info($bid);

    if(!$branch){
        $branch_name = '';
    }else{
        $branch_name = $branch['short_name']?$branch['short_name']:$branch['branch_name'];
    }

    $bid_maps[$bid] = $branch_name;

    return $branch_name;
}


/**
 * 根据部门ID获得部门名称
 * @param $dept_id
 * @return string
 */
function get_dept_name($dept_id){
    if(!$dept_id){
        return '-';
    }
    static $dept_id_maps = [];

    if(isset($dept_id_maps[$dept_id])){
        return $dept_id_maps[$dept_id];
    }

    $dept_info = get_dept_info($dept_id);

    if(!$dept_info){
        $dept_name = '';
    }else{
        $dept_name = $dept_info['dpt_name'];
    }

    $dept_id_maps[$dept_id] = $dept_name;

    return $dept_name;

}




/**
 * @param $mobile string 手机号码或邮箱地址
 * @param $vcode string 用户收到的验证码
 * @param $vtype string 验证码验证类型
 * @return bool|string
 */
function check_verify_code($mobile, $code, $type)
{
    $name = is_mobile($mobile) ? 'sms_vcode' : 'email_vcode';
    if (is_mobile($mobile)) {
        $w['mobile'] = $mobile;
    } else{
        $w['email'] = $mobile;
    }

    $w['type'] = $type;
    $w['expire_time'] = ['>', time()];
    $record = db($name)->where($w)->find();
    if (!$record) {
        return "验证码已过期，请重新请求";
    }
    if ($record['code'] !== $code) {
        return '验证码错误';
    }
    return true;
}

function send_email_vcode($email, $code)
{
    $mail = new PHPMailer();
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->Host = 'smtp.exmail.qq.com';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;
    //$mail->Helo = 'Hello smtp.qq.com Server';
    $mail->Hostname = 'localhost';
    $mail->CharSet = 'UTF-8';
    $mail->FromName = '校360';
    $mail->Username ='no-reply1@t910.com';
    $mail->Password = 'nopwd20151';
    $mail->From = 'no-reply1@t910.com';
    $mail->isHTML(true);
    //设置收件人邮箱地址 该方法有两个参数 第一个参数为收件人邮箱地址 第二参数为给该地址设置的昵称 不同的邮箱系统会自动进行处理变动 这里第二个参数的意义不大
    $mail->addAddress($email,'test');
    //    $mail->addAddress('xxx@163.com','晶晶在线用户');
    $mail->Subject = '您正在绑定邮箱';
    //添加邮件正文 上方将isHTML设置成了true，则可以是完整的html字符串 如：使用file_get_contents函数读取本地的html文件
    $mail->Body = "您的验证码是<b style=\"color:red;\">" . $code . "</b>,请妥善保管,5分钟有效";
    //为该邮件添加附件 该方法也有两个参数 第一个参数为附件存放的目录（相对目录、或绝对目录均可） 第二参数为在邮件附件中该附件的名称
    //$mail->addAttachment('./d.jpg','mm.jpg');

    $status = $mail->send();
    \think\Log::record($status);
    if($status) {
        \think\Log::record('邮件发送成功!');
        $param['email'] = $email;
        $param['code'] = $code;
        $param['type'] = 'bindEmail';
        $param['is_vcode'] = true;
        \think\Hook::listen('email_after_send', $param);
        return true;
    }else{
        \think\Log::record($mail->ErrorInfo);
        return '发送邮件失败，错误信息为：'.$mail->ErrorInfo;
    }
}

function makeOrderNo()
{
    mt_srand((double) microtime() * 1000000);
    return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
}

/**
 * 获得用户配置
 * @param  [type] $cfg_name [description]
 * @param  [type] $bid [校区ID,默认为0全局]
 * @return [type]           [description]
 */
function user_config($cfg_name = null,$bid = 0)
{
    $org_default_config = config('org_default_config');
    $extra_default_keys = ['sms_clound', 'tplmsg', 'recommend_rule', 'org_role', 'org_api','qrsign','print_vars','org_pc_ui','service_standard'];
    foreach($extra_default_keys as $k){
        $org_default_config[$k] = include(CONF_PATH .'extra'.DS.$k.'.php');
    }
    static $user_config = [];
    if(empty($user_config)){
        $user_config = Config::userConfig($bid);
    }
    $config = [];

    foreach($org_default_config as $key=>$def){
        $key = $key == 'tplmsg' && !isset($user_config['tplmsg']) && isset($user_config['wechat_template']) ? 'wechat_template' : $key;

        if(isset($user_config[$key])){
            if(is_string($user_config[$key])){
               $user_config[$key] = json_decode($user_config[$key],true);
            }
            $config[$key] = [];

            foreach($def as $kk=>$vv){
                if(isset($user_config[$key][$kk])){
                    if( is_array($vv) && !empty($vv)) {
                        $config[$key][$kk] = deep_array_merge($vv, $user_config[$key][$kk]);
                    }else{
                        $config[$key][$kk] = $user_config[$key][$kk];
                    }

                }else{
                    $config[$key][$kk] = $vv;
                }
            }

        }else{
            $config[$key] = $def;
            if($key == 'center_params') {
                $client_info = gvar('client');
                if(empty($client_info['info']['params'])) continue;
                $client_info_params = is_string($client_info['info']['params']) ?
                    json_decode($client_info['info']['params'],true) : $client_info['info']['params'];
                foreach($def as $k=>$c){
                    if(isset($client_info_params[$k])){
                        $client_info_params[$k] = deep_array_merge($c,$client_info_params[$k]);
                    } else {
                        $client_info_params[$k] = $c;
                    }
                }
                $config[$key] = $client_info_params;
            }
        }

        if($key == 'wechat_template') {
            $config['tplmsg'] = $config['wechat_template'];
        }
    }

    if(is_null($cfg_name)){
        return $config;
    }
    if(strpos($cfg_name,'.') === false){
        return isset($config[$cfg_name]) ? $config[$cfg_name] : null;
    }
    
    $arr_cfg = explode('.',$cfg_name);

    $val = $config;
    foreach($arr_cfg as $ac){
        if(is_null($val)){
            break;
        }
        $val = isset($val[$ac])?$val[$ac]:null;
    }
    return $val;
    
}

/**
 * 校区缺省配置
 * @param $config
 * @param string $cfg_name
 * @return mixed
 */
function branch_default_config($config,$cfg_name = 'params'){
    if($cfg_name != 'params'){
        return $config;
    }
    //暂时只处理系统参数的校区配置
    $patch_config= config('org_default_config_patch');
    $patch = $patch_config[$cfg_name];
    return deep_patch_config($config,$patch);
}

/**
 * 深度补丁配置
 * @param $config
 * @param $patch
 * @return array
 */
function deep_patch_config($config,$patch){
    $ret = [];
    foreach($patch as $k=>$v){
        if($k != '_bid_fields'){
            $ret[$k] = deep_patch_config($config[$k],$v);
        }else{
            foreach($v as $pf){
                $ret[$pf] = $config[$pf];
            }
        }
    }
    return $ret;
}

/**
 * 获得用户disabled_per_items
 * @param  [type] $cfg_name [description]
 * @return [type]           [description]
 */
function get_disabled_per_items(){
    $disabled_per_items = user_config('org_pc_ui.disabled_per_items');
    $items = [];
    if (!empty($disabled_per_items)){
        foreach ($disabled_per_items as $k => $v){
            if (strpos($v,'app') !== false){
                $items[$k] = substr($v,4);
            }
        }
    }

    return array_unique($items);
}

/**
 * 设置用户disabled_per_items
 * @param  [type] $cfg_name [description]
 * @return [type]           [description]
 */
function set_disabled_per_items($app_ename){
    $org_pc_ui = user_config('org_pc_ui');
    $apps = get_disabled_per_items();
    if (in_array($app_ename,$apps)){
        $key = array_search('app.'.$app_ename,$org_pc_ui['disabled_per_items']);
        unset($org_pc_ui['disabled_per_items'][$key]);
    }else{
        array_push($org_pc_ui['disabled_per_items'],'app.'.$app_ename);
    }
    $org_pc_ui['disabled_per_items'] = array_values($org_pc_ui['disabled_per_items']);
    return $org_pc_ui;
}

/**
 * 获得云存储配置
 * @param  [type] $storage [description]
 * @return [type]          [description]
 */
function storage_config($storage ='qiniu'){
    if($storage == 'qiniu'){
        $cfg = user_config('storage');
        if(empty($cfg)){
            $cfg = config('storage.'.$storage);
        }
    }else{
        $cfg = config('storage.'.$storage);
    }
    return $cfg;
}

/**
 * 模板消息配置读取
 * @param  [type] $scene [description]
 * @return [type]        [description]
 */
function tplmsg_config($scene)
{
    $tplmsg = user_config('tplmsg');
    if (empty($tplmsg)) {
        $tplmsg = config('tplmsg');
    }
    return isset($tplmsg[$scene]) ? $tplmsg[$scene] : null;
}


/**
 * 新增队列任务
 * @param  [type] $job_class_name [description]
 * @param  [type] $job_data       [description]
 * @param  [type] $queue_name     [description]
 * @param [type] $delay
 * @param [type] $task_id
 * @return [type]                 [description]
 */
function queue_push($job_class_name, $job_data, $queue_name = null, $delay = 0,$task_id = null)
{

    $database = config('database');
    $job_data['database'] = $database;
    $client = gvar('client');
    $og_id  = gvar('og_id');
    $job_data['client'] = $client;
    $job_data['og_id']  = $og_id;

    // 1.当前任务将由哪个类来负责处理。 
    if(substr($job_class_name,0,4) !== 'app\\'){
        $jobHandlerClassName = 'app\\common\\job\\'.$job_class_name;
    }else{
        $jobHandlerClassName = $job_class_name;
    }

    if(is_null($queue_name)){
        $queue_name = str_replace('app\\common\\job\\','', $jobHandlerClassName);
    }

    if($delay > 0) {
       return Queue::later($delay, $jobHandlerClassName, $job_data, $queue_name,$task_id);
    }
    return Queue::push($jobHandlerClassName, $job_data, $queue_name,$task_id);
}

/**
 * 取消队列
 * @param $task_id
 */
function queue_cancel($task_id){
    return Queue::deleteByTaskId($task_id);
}

/**
 * 队列任务ID
 * @return string
 */
function queue_task_id(){
    $arr = [
        gvar('client.cid'),
        gvar('og_id')
    ];
    $args = func_get_args();
    foreach($args as $arg){
        $arr[] = $arg;
    }
    $id = implode('-',$arr);

    if(strlen($id) > 32){
        $id = md5($id);
    }
    return $id;
}

//把回调加入队列
function callback_queue_push($url_key, $data)
{
    $api_config = user_config('org_api');
    if(empty($api_config) || empty($api_config['secret'])) return false;

    if(empty($api_config[$url_key])) return false;

    if(empty($data) || !is_array($data)) return false;

    $job_data = [
        'class' => 'Callback',
        'url' => $api_config[$url_key],
        'secret' => $api_config['secret'],
        'data' => $data,
    ];
    queue_push('Base', $job_data);
}

function str_replace_json($search, $replace, $subject)
{
    return json_decode(str_replace($search, $replace, json_encode($subject, JSON_UNESCAPED_UNICODE)), true);

}


/**etend function */
/**
 +----------------------------------------------------------
 * 字节格式化 把字节数格式为 B K M G T 描述的大小
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function byte_format($size, $dec=2) {
    $a = array("B", "KB", "MB", "GB", "TB", "PB");
    $pos = 0;
    while ($size >= 1024) {
         $size /= 1024;
           $pos++;
    }
    return round($size,$dec)." ".$a[$pos];
}

/**
 +----------------------------------------------------------
 * 检查字符串是否是UTF8编码
 +----------------------------------------------------------
 * @param string $string 字符串
 +----------------------------------------------------------
 * @return Boolean
 +----------------------------------------------------------
 */
function is_utf8($string) {
    return preg_match('%^(?:
         [\x09\x0A\x0D\x20-\x7E]            # ASCII
       | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
       |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
       |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
       |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
       | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
       |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
    )*$%xs', $string);
}
/**
 +----------------------------------------------------------
 * 代码加亮
 +----------------------------------------------------------
 * @param String  $str 要高亮显示的字符串 或者 文件名
 * @param Boolean $show 是否输出
 +----------------------------------------------------------
 * @return String
 +----------------------------------------------------------
 */
function highlight_code($str,$show=false) {
    if(file_exists($str)) {
        $str    =   file_get_contents($str);
    }
    $str  =  stripslashes(trim($str));
    // The highlight string function encodes and highlights
    // brackets so we need them to start raw
    $str = str_replace(array('&lt;', '&gt;'), array('<', '>'), $str);

    // Replace any existing PHP tags to temporary markers so they don't accidentally
    // break the string out of PHP, and thus, thwart the highlighting.

    $str = str_replace(array('&lt;?php', '?&gt;',  '\\'), array('phptagopen', 'phptagclose', 'backslashtmp'), $str);

    // The highlight_string function requires that the text be surrounded
    // by PHP tags.  Since we don't know if A) the submitted text has PHP tags,
    // or B) whether the PHP tags enclose the entire string, we will add our
    // own PHP tags around the string along with some markers to make replacement easier later

    $str = '<?php //tempstart'."\n".$str.'//tempend ?>'; // <?

    // All the magic happens here, baby!
    $str = highlight_string($str, TRUE);

    // Prior to PHP 5, the highlight function used icky font tags
    // so we'll replace them with span tags.
    if (abs(phpversion()) < 5) {
        $str = str_replace(array('<font ', '</font>'), array('<span ', '</span>'), $str);
        $str = preg_replace('#color="(.*?)"#', 'style="color: \\1"', $str);
    }

    // Remove our artificially added PHP
    $str = preg_replace("#\<code\>.+?//tempstart\<br />\</span\>#is", "<code>\n", $str);
    $str = preg_replace("#\<code\>.+?//tempstart\<br />#is", "<code>\n", $str);
    $str = preg_replace("#//tempend.+#is", "</span>\n</code>", $str);

    // Replace our markers back to PHP tags.
    $str = str_replace(array('phptagopen', 'phptagclose', 'backslashtmp'), array('&lt;?php', '?&gt;', '\\'), $str); //<?
    $line   =   explode("<br />", rtrim(ltrim($str,'<code>'),'</code>'));
    $result =   '<div class="code"><ol>';
    foreach($line as $key=>$val) {
        $result .=  '<li>'.$val.'</li>';
    }
    $result .=  '</ol></div>';
    $result = str_replace("\n", "", $result);
    if( $show!== false) {
        echo($result);
    }else {
        return $result;
    }
}

//输出安全的html
function h($text, $tags = null) {
    $text   =   trim($text);
    //完全过滤注释
    $text   =   preg_replace('/<!--?.*-->/','',$text);
    //完全过滤动态代码
    $text   =   preg_replace('/<\?|\?'.'>/','',$text);
    //完全过滤js
    $text   =   preg_replace('/<script?.*\/script>/','',$text);

    $text   =   str_replace('[','&#091;',$text);
    $text   =   str_replace(']','&#093;',$text);
    $text   =   str_replace('|','&#124;',$text);
    //过滤换行符
    $text   =   preg_replace('/\r?\n/','',$text);
    //br
    $text   =   preg_replace('/<br(\s\/)?'.'>/i','[br]',$text);
    $text   =   preg_replace('/(\[br\]\s*){10,}/i','[br]',$text);
    //过滤危险的属性，如：过滤on事件lang js
    while(preg_match('/(<[^><]+)( lang|on|action|background|codebase|dynsrc|lowsrc)[^><]+/i',$text,$mat)){
        $text=str_replace($mat[0],$mat[1],$text);
    }
    while(preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i',$text,$mat)){
        $text=str_replace($mat[0],$mat[1].$mat[3],$text);
    }
    if(empty($tags)) {
        $tags = 'table|td|th|tr|i|b|u|strong|img|p|br|div|strong|em|ul|ol|li|dl|dd|dt|a';
    }
    //允许的HTML标签
    $text   =   preg_replace('/<('.$tags.')( [^><\[\]]*)>/i','[\1\2]',$text);
    //过滤多余html
    $text   =   preg_replace('/<\/?(html|head|meta|link|base|basefont|body|bgsound|title|style|script|form|iframe|frame|frameset|applet|id|ilayer|layer|name|script|style|xml)[^><]*>/i','',$text);
    //过滤合法的html标签
    while(preg_match('/<([a-z]+)[^><\[\]]*>[^><]*<\/\1>/i',$text,$mat)){
        $text=str_replace($mat[0],str_replace('>',']',str_replace('<','[',$mat[0])),$text);
    }
    //转换引号
    while(preg_match('/(\[[^\[\]]*=\s*)(\"|\')([^\2=\[\]]+)\2([^\[\]]*\])/i',$text,$mat)){
        $text=str_replace($mat[0],$mat[1].'|'.$mat[3].'|'.$mat[4],$text);
    }
    //过滤错误的单个引号
    while(preg_match('/\[[^\[\]]*(\"|\')[^\[\]]*\]/i',$text,$mat)){
        $text=str_replace($mat[0],str_replace($mat[1],'',$mat[0]),$text);
    }
    //转换其它所有不合法的 < >
    $text   =   str_replace('<','&lt;',$text);
    $text   =   str_replace('>','&gt;',$text);
    $text   =   str_replace('"','&quot;',$text);
     //反转换
    $text   =   str_replace('[','<',$text);
    $text   =   str_replace(']','>',$text);
    $text   =   str_replace('|','"',$text);
    //过滤多余空格
    $text   =   str_replace('  ',' ',$text);
    return $text;
}

function ubb($Text) {
  $Text=trim($Text);
  //$Text=htmlspecialchars($Text);
  $Text=preg_replace("/\\t/is","  ",$Text);
  $Text=preg_replace("/\[h1\](.+?)\[\/h1\]/is","<h1>\\1</h1>",$Text);
  $Text=preg_replace("/\[h2\](.+?)\[\/h2\]/is","<h2>\\1</h2>",$Text);
  $Text=preg_replace("/\[h3\](.+?)\[\/h3\]/is","<h3>\\1</h3>",$Text);
  $Text=preg_replace("/\[h4\](.+?)\[\/h4\]/is","<h4>\\1</h4>",$Text);
  $Text=preg_replace("/\[h5\](.+?)\[\/h5\]/is","<h5>\\1</h5>",$Text);
  $Text=preg_replace("/\[h6\](.+?)\[\/h6\]/is","<h6>\\1</h6>",$Text);
  $Text=preg_replace("/\[separator\]/is","",$Text);
  $Text=preg_replace("/\[center\](.+?)\[\/center\]/is","<center>\\1</center>",$Text);
  $Text=preg_replace("/\[url=http:\/\/([^\[]*)\](.+?)\[\/url\]/is","<a href=\"http://\\1\" target=_blank>\\2</a>",$Text);
  $Text=preg_replace("/\[url=([^\[]*)\](.+?)\[\/url\]/is","<a href=\"http://\\1\" target=_blank>\\2</a>",$Text);
  $Text=preg_replace("/\[url\]http:\/\/([^\[]*)\[\/url\]/is","<a href=\"http://\\1\" target=_blank>\\1</a>",$Text);
  $Text=preg_replace("/\[url\]([^\[]*)\[\/url\]/is","<a href=\"\\1\" target=_blank>\\1</a>",$Text);
  $Text=preg_replace("/\[img\](.+?)\[\/img\]/is","<img src=\\1>",$Text);
  $Text=preg_replace("/\[color=(.+?)\](.+?)\[\/color\]/is","<font color=\\1>\\2</font>",$Text);
  $Text=preg_replace("/\[size=(.+?)\](.+?)\[\/size\]/is","<font size=\\1>\\2</font>",$Text);
  $Text=preg_replace("/\[sup\](.+?)\[\/sup\]/is","<sup>\\1</sup>",$Text);
  $Text=preg_replace("/\[sub\](.+?)\[\/sub\]/is","<sub>\\1</sub>",$Text);
  $Text=preg_replace("/\[pre\](.+?)\[\/pre\]/is","<pre>\\1</pre>",$Text);
  $Text=preg_replace("/\[email\](.+?)\[\/email\]/is","<a href='mailto:\\1'>\\1</a>",$Text);
  $Text=preg_replace("/\[colorTxt\](.+?)\[\/colorTxt\]/eis","color_txt('\\1')",$Text);
  $Text=preg_replace("/\[emot\](.+?)\[\/emot\]/eis","emot('\\1')",$Text);
  $Text=preg_replace("/\[i\](.+?)\[\/i\]/is","<i>\\1</i>",$Text);
  $Text=preg_replace("/\[u\](.+?)\[\/u\]/is","<u>\\1</u>",$Text);
  $Text=preg_replace("/\[b\](.+?)\[\/b\]/is","<b>\\1</b>",$Text);
  $Text=preg_replace("/\[quote\](.+?)\[\/quote\]/is"," <div class='quote'><h5>引用:</h5><blockquote>\\1</blockquote></div>", $Text);
  $Text=preg_replace("/\[code\](.+?)\[\/code\]/eis","highlight_code('\\1')", $Text);
  $Text=preg_replace("/\[php\](.+?)\[\/php\]/eis","highlight_code('\\1')", $Text);
  $Text=preg_replace("/\[sig\](.+?)\[\/sig\]/is","<div class='sign'>\\1</div>", $Text);
  $Text=preg_replace("/\\n/is","<br/>",$Text);
  return $Text;
}

// 随机生成一组字符串
function build_count_rand ($number,$length=4,$mode=1) {
    if($mode==1 && $length<strlen($number) ) {
        //不足以生成一定数量的不重复数字
        return false;
    }
    $rand   =  array();
    for($i=0; $i<$number; $i++) {
        $rand[] =   rand_string($length,$mode);
    }
    $unqiue = array_unique($rand);
    if(count($unqiue)==count($rand)) {
        return $rand;
    }
    $count   = count($rand)-count($unqiue);
    for($i=0; $i<$count*3; $i++) {
        $rand[] =   rand_string($length,$mode);
    }
    $rand = array_slice(array_unique ($rand),0,$number);
    return $rand;
}

function remove_xss($val) {
   // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
   // this prevents some character re-spacing such as <java\0script>
   // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
   $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);

   // straight replacements, the user should never need these since they're normal characters
   // this prevents like <IMG SRC=@avascript:alert('XSS')>
   $search = 'abcdefghijklmnopqrstuvwxyz';
   $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
   $search .= '1234567890!@#$%^&*()';
   $search .= '~`";:?+/={}[]-_|\'\\';
   for ($i = 0; $i < strlen($search); $i++) {
      // ;? matches the ;, which is optional
      // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

      // @ @ search for the hex values
      $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
      // @ @ 0{0,7} matches '0' zero to seven times
      $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
   }

   // now the only remaining whitespace attacks are \t, \n, and \r
   $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
   $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
   $ra = array_merge($ra1, $ra2);

   $found = true; // keep replacing as long as the previous round replaced something
   while ($found == true) {
      $val_before = $val;
      for ($i = 0; $i < sizeof($ra); $i++) {
         $pattern = '/';
         for ($j = 0; $j < strlen($ra[$i]); $j++) {
            if ($j > 0) {
               $pattern .= '(';
               $pattern .= '(&#[xX]0{0,8}([9ab]);)';
               $pattern .= '|';
               $pattern .= '|(&#0{0,8}([9|10|13]);)';
               $pattern .= ')*';
            }
            $pattern .= $ra[$i][$j];
         }
         $pattern .= '/i';
         $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
         $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
         if ($val_before == $val) {
            // no replacements were made, so exit the loop
            $found = false;
         }
      }
   }
   return $val;
}

/**
 +----------------------------------------------------------
 * 把返回的数据集转换成Tree
 +----------------------------------------------------------
 * @access public
 +----------------------------------------------------------
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 +----------------------------------------------------------
 * @return array
 +----------------------------------------------------------
 */
function list_to_tree($list, $pk='id',$pid = 'pid',$child = '_child',$root=0) {
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 +----------------------------------------------------------
 * 对查询结果集进行排序
 +----------------------------------------------------------
 * @access public
 +----------------------------------------------------------
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 +----------------------------------------------------------
 * @return array
 +----------------------------------------------------------
 */
function list_sort_by($list,$field, $sortby='asc') {
   if(is_array($list)){
       $refer = $resultSet = array();
       foreach ($list as $i => $data)
           $refer[$i] = &$data[$field];
       switch ($sortby) {
           case 'asc': // 正向排序
                asort($refer);
                break;
           case 'desc':// 逆向排序
                arsort($refer);
                break;
           case 'nat': // 自然排序
                natcasesort($refer);
                break;
       }
       foreach ( $refer as $key=> $val)
           $resultSet[] = &$list[$key];
       return $resultSet;
   }
   return false;
}

/**
 +----------------------------------------------------------
 * 在数据列表中搜索
 +----------------------------------------------------------
 * @access public
 +----------------------------------------------------------
 * @param array $list 数据列表
 * @param mixed $condition 查询条件
 * 支持 array('name'=>$value) 或者 name=$value
 +----------------------------------------------------------
 * @return array
 +----------------------------------------------------------
 */
function list_search($list,$condition) {
    if(is_string($condition))
        parse_str($condition,$condition);
    // 返回的结果集合
    $resultSet = array();
    foreach ($list as $key=>$data){
        $find   =   false;
        foreach ($condition as $field=>$value){
            if(isset($data[$field])) {
                if(0 === strpos($value,'/')) {
                    $find   =   preg_match($value,$data[$field]);
                }elseif($data[$field]==$value){
                    $find = true;
                }
            }
        }
        if($find)
            $resultSet[]     =   &$list[$key];
    }
    return $resultSet;
}

/**
 * 字符串转换成int数据类型
 * @param  [type] $value [description]
 * @return [type]        [description]
 */
function split_int_array($value,$split = ','){
    $arr = explode($split, $value);
    $ids = [];
    foreach($arr as $id){
        $id = intval($id);
        if($id){
            array_push($ids,$id);
        }
    }
    return $ids;
}

/**
 * 数字转中文,收据之类的中文大写
 */
if (!function_exists('number2chinese')) {
    function number2chinese($number, $isRmb = false) {
        // 判断正确数字
        list($integer, $decimal) = explode('.', $number . '.0');
        if (!preg_match('/^(\d+)?$/', $integer . $decimal)) {
            throw new Exception('number2chinese() wrong number', 1);
        }
        if (preg_match('/^\d+$/', $number)) {
            $decimal = null;
        }
        $integer = ltrim($integer, '0');
        // 准备参数
        $numArr  = ['', '一', '二', '三', '四', '五', '六', '七', '八', '九', '.' => '点'];
        $descArr = ['', '十', '百', '千', '万', '十', '百', '千', '亿', '十', '百', '千', '万亿', '十', '百', '千', '兆', '十', '百', '千'];
        if ($isRmb) {
            $number = substr(sprintf("%.5f", $number), 0, -1);
            $numArr  = ['', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖', '.' => '点'];
            $descArr = ['', '拾', '佰', '仟', '万', '拾', '佰', '仟', '亿', '拾', '佰', '仟', '万亿', '拾', '佰', '仟', '兆', '拾', '佰', '仟'];
            $rmbDescArr = ['角', '分', '厘', '毫'];
        }
        // 整数部分拼接
        $integerRes = '';
        $count = strlen($integer);
        if ($count > max(array_keys($descArr))) {
            throw new Exception('number2chinese() number too large.', 1);
        } else if ($count == 0) {
            $integerRes = '零';
        } else {
            for ($i = 0; $i < $count; $i++) {
                $n = $integer[$i];
                $j = $count - $i - 1;
                $cnZero = $i > 1 && $n !== '0' && $integer[$i - 1] === '0' ? '零' : '';
                $cnNum  = $numArr[$n];
                $cnDesc = ($n == '0' && $j % 4 != 0) || substr($integer, $i - 3, 4) === '0000' ? '' : $descArr[$j];
                if ($i == 0 && $cnNum == '一' && $cnDesc == '十') $cnNum = '';
                $integerRes .=  $cnZero . $cnNum . $cnDesc;
            }
        }
        // 小数部分拼接
        $decimalRes = '';
        $count = strlen($decimal);
        if ($decimal === null) {
            $decimalRes = $isRmb ? '整' : '';
        } else if ($decimal === '0') {
            $decimalRes = '零';
        } else if ($count > max(array_keys($descArr))) {
            throw new Exception('number2chinese() number too large.', 1);
        } else {
            for ($i = 0; $i < $count; $i++) {
                if ($isRmb && $i > count($rmbDescArr) - 1) break;
                $n = $decimal[$i];
                $cnZero = $n === '0' ? '零' : '';
                $cnNum  = $numArr[$n];
                $cnDesc = $isRmb ? $rmbDescArr[$i] : '';
                $decimalRes .=  $cnZero . $cnNum . $cnDesc;
            }
        }
        // 拼接结果
        $res = $isRmb ?
            $integerRes . ($decimalRes === '零' ? '元整' : "元$decimalRes"):
            $integerRes . ($decimalRes ==='' ? '' : "点$decimalRes");
        return $res;
    }
}


/** 
 * 获取某年第几周的开始日期和结束日期 
 * @param int $year 
 * @param int $week 第几周;
 * @param int $type 1时间戳， 2日期格式
 */ 
function weekday($year,$week=1, $type=1){
    $year_start = mktime(0,0,0,1,1,$year); 
    $year_end   = mktime(0,0,0,12,31,$year); 
     
    // 判断第一天是否为第一周的开始
    $first_day_week_day = intval(date('N',$year_start));
    if ($first_day_week_day === 1){
        $start = $year_start;//把第一天做为第一周的开始 
    }else{
        $n = $first_day_week_day - 1;
        $start = strtotime("-$n days",$year_start);
    } 
     
    if ($week===1){
        $weekday['start'] = $start;
    }else{
        $weekday['start'] = strtotime('+'.($week-1).' weeks',$start);
    }

    // 第几周的结束时间
    $weekday['end'] = strtotime('+6 days',$weekday['start'])+86399;
    /*
    if (date('Y',$weekday['end'])!=$year){
        $weekday['end'] = $year_end;
    }*/

    if($type === 2) {
        $weekday['start'] = date('Ymd', $weekday['start']);
        $weekday['end'] = date('Ymd', $weekday['end']);
    }

    return $weekday; 
} 
 
/** 
 * 计算一年有多少周，每周从星期一开始， 
 * 如果最后一天在周四后（包括周四）算完整的一周，否则不计入当年的最后一周 
 * 如果第一天在周四前（包括周四）算完整的一周，否则不计入当年的第一周 
 * @param int $year 
 * return int 
 */ 
function week($year){ 
    $year_start = mktime(0,0,0,1,1,$year); 
    $year_end = mktime(0,0,0,12,31,$year); 
    if (intval(date('W',$year_end))===1){ 
        return date('W',strtotime('last week',$year_end)); 
    }else{ 
        return date('W',$year_end); 
    } 
} 

//获取校区节假日
function getBranchHoliday($bid = -1, $year = null) {
    $og_id = gvar('og_id');
    if($bid === -1){
        $bid = auto_bid();
    }
    if(is_null($year)){
        $year = intval(date('Y',time()));
        $start_year = $year -1;
        $end_year   = $year +1;
    }else{
        $start_year = $year;
        $end_year   = $year;
    }

    $w['og_id'] = $og_id;
    $w['year']  = ['BETWEEN',[$start_year,$end_year]];
    $w['bid']   = $bid;
    $mHoliday = model('holiday');
    $holidays = $mHoliday->where($w)->column('int_day');

    if(empty($holidays) && $bid != 0){
        $w['bid'] = 0;
        $holidays = $mHoliday->where($w)->column('int_day');
    }
    return $holidays;
}

/**
 * amr to mp3
 * @param  [type] $amr_file [description]
 * @param  [type] $mp3_file [description]
 * @return [type]           [description]
 */
function amr_to_mp3($amr_file,$mp3_file = null){
    if(is_null($mp3_file)){
        $mp3_file = str_replace('.amr','.mp3',$amr_file);
    }
    /*
    $ffmpeg = \FFMpeg\FFMpeg::create();
    $amr    = $ffmpeg->open($amr_file);
    $amr->save(new \FFMpeg\Format\Audio\Mp3(),$mp3_file);
    return $mp3_file;
    */
    $cmd = '/usr/local/bin/ffmpeg -i '.$amr_file.' '.$mp3_file;
    @exec($cmd);
    unlink($amr_file);
    return $mp3_file;
}

/**
 * 版本整形化
 * @param  [type] $ver [description]
 * @return [type]      [description]
 */
function int_version($ver){
    if(empty($ver)){
        return 0;
    }

    $arr_ver = explode('.',$ver);

    $int_b = intval($arr_ver[0]);
    $int_m = intval($arr_ver[1]);
    $int_e = intval($arr_ver[2]);

    return intval($int_b.$int_m.$int_e);

}
/**
 * 获得今天任务cachekey
 * @param  [type] $cid [description]
 * @return [type]      [description]
 */
function get_today_task_key($cid,$og_id){
    return  sprintf('%s-%s-%s-ts',$cid,$og_id,date('Ymd',time()));
}

/**
 * 货币格式化
 * @param  [type] $number [description]
 * @param  [type] $abandon 是否四舍五入
 * @return [type]         [description]
 */
function format_currency($number,$abandon = true){
    if(!$number){
        $number = 0;
    }
    $ret = '';
    if($abandon){
        $ret = sprintf("%.2f",$number);
    }else{
        $ret = sprintf("%.2f",substr(sprintf("%.3f",$number),0,-2));
    }

    return floatval($ret);
}

/**
 * 格式化课时数
 * @param $number
 * @return float
 */
function format_lesson_hours($number){
    return format_currency($number);
}
/**
 * 模板消息URL替换
 * @param  [type] $url  [description]
 * @param  array  $data [description]
 * @param string $terminal 终端
 * @return [type]       [description]
 */
function tplmsg_url($url,$data = [],$terminal = 'student'){
    $client = gvar('client');
    $host   = '';
    if($client){
        $host = $client['domain'];
    }

    $base_url = 'https://';
    
    if($host != ''){
        $base_url = $base_url .  $host . '.';
    }

    $domain = config('ui.domain');

    $base_url = $base_url . $domain . '/'. $terminal .'/#';

    $data['base_url'] = $base_url;

    $url = tpl_replace($url,$data);

    return $url;
}

/**
 * 模板消息内容替换
 * @param  [type] $content [description]
 * @param  [type] $search  [description]
 * @param  [type] $replace [description]
 * @return [type]          [description]
 */
function tplmsg_content($content,$search,$replace){
    return str_replace($search, $replace, $content);
}

/**
 * 数组值拷贝
 * @param  [type] &$dst   [description]
 * @param  [type] $src    [description]
 * @param  array  $fields [description]
 * @return [type]         [description]
 */
function array_copy(&$dst,$src,$fields = []){
    if(is_object($src)){
        if(!method_exists($src, 'toArray')){
            return $dst;
        }
        $src = $src->toArray();
    }

    if(empty($fields)){
        $fields = array_keys($src);
    }

    foreach($fields as $index=>$f){
        $df = $f;
        $sf = $f;
        if(!is_numeric($index)){
            $df = $f;
            $sf = $index;
        }
        if(isset($src[$sf])){
            $dst[$df] = $src[$sf];
        }
    }
    return $dst;
}

/**
 * 根据int_hour计算分钟数
 * @param  [type] $int_start_hour [description]
 * @param  [type] $int_end_hour   [description]
 * @return [type]                 [description]
 */
function cacu_minutes($int_start_hour,$int_end_hour){
    $int_start_hour = format_int_hour($int_start_hour);
    $int_end_hour   = format_int_hour($int_end_hour);

    $len_start_hour = strlen($int_start_hour);
    $len_end_hour   = strlen($int_end_hour);
    if($len_start_hour == 3){
        $int_s_hour = (int)substr($int_start_hour,0,1);
        $int_s_min  = (int)substr($int_start_hour,1,2);
    }elseif($len_start_hour == 2){
        $int_s_hour = 0;
        $int_s_min  = (int)substr($int_start_hour,0,2);
    }elseif($len_start_hour == 1){
        $int_s_hour = 0;
        $int_s_min  = (int)substr($int_start_hour,0,1);
    }elseif($len_start_hour == 0){
        $int_s_hour = 0;
        $int_s_min  = 0;
    }else{
        $int_s_hour = (int)substr($int_start_hour,0,2);
        $int_s_min  = (int)substr($int_start_hour,2,2);
    }
    if($len_end_hour == 3){
        $int_e_hour = (int)substr($int_end_hour,0,1);
        $int_e_min  = (int)substr($int_end_hour,1,2);
    }elseif($len_end_hour == 2){
        $int_e_hour = 0;
        $int_e_min  = (int)substr($int_end_hour,0,2);
    }elseif($len_end_hour == 1){
        $int_e_hour = 0;
        $int_e_min  = (int)substr($int_end_hour,0,1);
    }elseif($len_end_hour == 0){
        $int_e_hour = 0;
        $int_e_min  = 0;
    }else{
        $int_e_hour = (int)substr($int_end_hour,0,2);
        $int_e_min  = (int)substr($int_end_hour,2,2);
    }

    $minutes = ($int_e_hour - $int_s_hour) * 60 + $int_e_min - $int_s_min;

    return $minutes;
}


/**
 * 计算应该扣除课时数
 * @param  [type]  $int_start_hour          [开始时间]
 * @param  [type]  $int_end_hour            [结束时间]
 * @param  integer $per_lesson_hour_minutes [每课时多少分钟]
 * @return [type]                           [description]
 */
function cacu_lesson_hours($int_start_hour,$int_end_hour,$per_lesson_hour_minutes = 60){
    //if(empty($per_lesson_hour_minutes)) exception('每课时分钟数据错误');
    $per_lesson_hour_minutes = empty($per_lesson_hour_minutes) ? 60 : $per_lesson_hour_minutes;
    $minutes = cacu_minutes($int_start_hour,$int_end_hour);
    
    $lesson_hour = round($minutes / $per_lesson_hour_minutes,2);

    return $lesson_hour;
}

//判断是否为空，但不包括0
function empty_except_zero($value) {
    $value = trim($value);
    return is_null($value) || $value === '' || $value === false;
}

/**
 * 短网址函数
 * @param $url
 * @return string
 */
function short_url($url){
    $url    = crc32($url);
    $result = sprintf("%u", $url);
    $s_url  = '';
    while($result>0){
        $s= $result%62;
        if($s>35){
            $s= chr($s+61);
        } elseif($s>9 && $s<=35){
            $s= chr($s+ 55);
        }
        $s_url.= $s;
        $result= floor($result/62);
    }
    return $s_url;
}

/**
 * 生成唯一的hash
 * @return string
 */
function get_hash(){
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()+-';
    $random = $chars[mt_rand(0,73)].$chars[mt_rand(0,73)].$chars[mt_rand(0,73)].$chars[mt_rand(0,73)].$chars[mt_rand(0,73)];//Random 5 times
    $content = uniqid().$random;
    return md5($content);
}

/**
 * 生成短ID
 * @return string
 */
function short_id(){
    $hash = get_hash();
    return short_url($hash);
}

/**
 * 配置合并
 * @param $default_config
 * @param $user_config
 */
function deep_array_merge($default_config,$user_config){
    foreach($default_config as $k=>$v){
        if(is_array($v) && !empty($v) && isset($user_config[$k]) && is_array($user_config[$k])){
            if(isset($v[0])){
                //后面那个判断主要是用于微信模板配置判断
                if(is_array($v[0]) || array_keys($v) === range(0, count($v) - 1)){
                    $default_config[$k] = $user_config[$k];
                }else{
                    $default_config[$k] = array_merge($v,$user_config[$k]);
                }

            }else {
                $default_config[$k] = deep_array_merge($v, $user_config[$k]);
            }
        }elseif(isset($user_config[$k])){
            $default_config[$k] = $user_config[$k];
        }
    }
    return $default_config;
}

/**
 * 获得客户的权限定义菜单项目
 * @param string $prefix
 * @param string $section
 * @return mixed
 */
function get_client_pers_item($prefix = 'org',$section = ''){
    $client = gvar('client');
    $ui_domain = $client['domain'];
    $per_item_key = $prefix.'_per_item';
    $client_per_file = PUBLIC_PATH . 'ui'.DS.$ui_domain.DS.$per_item_key.'.php';
    $all_pers = [];
    if(is_file($client_per_file)) {
        $all_pers = include($client_per_file);
    }else{
        if($client['parent_cid'] > 0){
            $parent_client = db('client','db_center')->where('cid',$client['parent_cid'])->find();
            if($parent_client){
                $ui_domain = $parent_client['host'];
                $client_per_file = PUBLIC_PATH . 'ui'.DS.$ui_domain.DS.$per_item_key.'.php';
                if(is_file($client_per_file)){
                    $all_pers = include($client_per_file);
                }
            }
        }
    }

    if(empty($all_pers)){
        $all_pers = config($per_item_key);
    }

    if($section != '' && isset($all_pers[$section])){
        return $all_pers[$section];
    }
    return $all_pers;
}

/**
 *
 */
function get_default_wxmp_name(){
    static $default_wxmp_name = null;
    if(!is_null($default_wxmp_name)){
        return $default_wxmp_name;
    }
    $w['is_default'] = 1;
    $wxmp_info = get_wxmp_info($w);
    if(!$wxmp_info){
        $default_wxmp_name = config('ui.default_wxmp_name');
    }else{
        $default_wxmp_name = $wxmp_info['nick_name'];
    }
    return $default_wxmp_name;
}


/**
 * 获得短信模板列表
 * @return array
 */
function get_sms_tpls(){
    $ret = [];
    $tplmsg = config('tplmsg');
    foreach($tplmsg as $bs=>$val){
        $apply_tpl = '';
        if(isset($val['sms']['apply_tpl'])){
            $wxmp_name = get_default_wxmp_name();
            $apply_tpl = str_replace('学习管家服务号',$wxmp_name,$val['sms']['apply_tpl']);
        }
        $vars = array_values($val['tpl_fields']);

        array_push($ret,[
            'name'         =>$val['name'],
            'desc'         =>$val['desc'],
            'business_type'=>$bs,
            'apply_tpl'     => $apply_tpl,
            'vars'          => $vars
        ]);
    }
    return $ret;
}

/**
 * 获得学习管家的业务URL
 * @param $bs_type
 * @param $extra
 * @param $host
 */
function get_student_url($url,$host = ''){
    $prefix = '';
    if($host != ''){
        $prefix = $host.'.';
    }
    $ret = 'https://'.$prefix.config('ui.domain').'/student#'.$url;

    return $ret;
}

/**
 * 一个时间是否在一个时间段
 * @param $int_hour
 * @param $ts
 * @return bool
 */
function is_time_in_timesection($int_hour,$ts){
    if($int_hour > $ts['int_start_hour'] && $int_hour < $ts['int_end_hour']){
        return true;
    }
    return false;
}

/**
 * 判断一个时间段是否在另一个时间段内
 * @param $input
 * @param $search
 * @return bool
 */
function is_timesection_in_timesection($input,$search){
    if($input['int_start_hour'] == $search['int_start_hour'] && $input['int_end_hour'] == $search['int_end_hour']){
        return true;
    }
    if(is_time_in_timesection($input['int_start_hour'],$search) || is_time_in_timesection($input['int_end_hour'],$search)){
        return true;
    }
    return false;
}

/**
 * 获得顶级字典分类DIDS
 * @return array
 */
function get_top_dict_dids(){
    $dicts_config = include(CONF_PATH .'dicts.php');
    $top_dids = $dicts_config['top_dids'];
    $client = gvar('client');
    if(isset($client['domain']) && !empty($client['domain'])){
        $client_ui_config_file = PUBLIC_PATH.$client['domain'].DS.'config.php';
        if(file_exists($client_ui_config_file)){
            $client_dicts_config = include($client_ui_config_file);
            if(isset($client_dicts_config['top_dids']) && !empty($client_dicts_config)){
                $top_dids = $dicts_config['top_dids'];
            }
        }
    }
    return $top_dids;
}

/**
 * 获取用户定义的角色名称
 * @param $rid
 * @param $default_name
 * @return mixed
 */
function user_role_name($rid,$default_name){
    if($rid > 10){
        return $default_name;
    }
    static $rid_name_map = [];
    if(empty($rid_name_map)){
        $user_org_role = user_config('org_role');
        foreach($user_org_role as $r){
            $rid_name_map[$r['rid']] = $r['role_name'];
        }
    }

    return isset($rid_name_map[$rid])?$rid_name_map[$rid]:$default_name;
}

/**
 * 获得用户定义词语
 * @return array
 */
function get_user_words(){
    $ret = [];
    $org_roles = config('org_role');
    foreach($org_roles as $r){
        $map = [];
        $map[0] = $r['role_name'];
        $map[1] = user_role_name($r['rid'],$r['role_name']);
        array_push($ret,$map);
    }
    return $ret;
}

/**
 * 导航翻译
 * @param $text
 * @return mixed
 */
function nav_translate($text){
    if(strpos($text,'%') === false){
        return $text;
    }
    $user_words = get_user_words();
    $user_org_role = user_config('org_role');
    foreach($user_words as $uw){
        $search = '%'.$uw[0].'%';
        if(strpos($text,$search) !== false){
            $text = str_replace($search,$uw[1],$text);
            break;
        }
    }
    return $text;
}
/**
 * 根据日期段获取周数组
 * @param  [type]  $start [description]
 * @param  [type]  $end   [description]
 * @param  integer $mode       0为从星期1开始，1为星期从星期天开始
 * @return [type]              [description]
 */
function get_week_section($start,$end,$mode = 0){
    $week_array = array();
    $first = 6 - date('w',$start);
    $first_end = date('Y-m-d',strtotime("+$first day",$start));
    $week_array[0]['start'] = date('Y-m-d',$start);
    $week_array[0]['end'] = $first_end;

    $start = strtotime("+1 day",strtotime($first_end));
    $i = 1;
    while($start <= $end){
        $week_array[$i]['start'] = date('Y-m-d',$start);
        $tmp = strtotime("+6 days",$start);
        if($end <= $tmp){
            $week_array[$i]['end'] = date('Y-m-d',$end);
        }
        else{
            $week_array[$i]['end'] = date('Y-m-d',$tmp);
        }
        $i++;
        $start = strtotime("+1 day",$tmp);
    }
    return $week_array;
}

/**
 * 日期年龄转化为日期字符串格式 xxxx-xx-xx
 * @param $str
 * @return string
 */
function dage_to_date($str){
    $date_str = '';
    $now_time = time();
    $now_year = date('Y',$now_time);
    $now_month = date('n',$now_time);
    $year = '0000';
    $month = '00';
    $day = '00';
    $num = 100;
    $float  = 0;

    if(is_numeric($str)) {

        $num = intval($str);
        $float = floatval($str);
    }else {
        $reg_age = '/^(\d+)(\.\d+)?[\x{5c81}]$/u';
        $reg_age_month = '/^(\d+)[\x{5c81}]([1-9][0-1]?)[\x{4e2a}]?[\x{6708}]$/u';
        $reg_date = '/^\d{4}-\d{2}-\d{2}$/';
        if(preg_match($reg_age,$str)){

            $num   = intval($str);
            $float = floatval($str);

        }elseif(preg_match($reg_age_month,$str,$matches)){
            $num   = intval($matches[1]);
            $month_rate = intval($matches[2]) / 12;
            $float = $num+$month_rate;


        }elseif(preg_match($reg_date,$str)){
            list($year,$month,$day) = explode('-',$str);


        }

    }
    $timestamp = 0;
    if($num > 25569){

        $timestamp = ($num - 25569) * 86400;

        $date_str  = date('Y-m-d',$timestamp);

    }elseif($num < 90){
        //年龄
        $year = strval($now_year - $num);
        if($float != $num){
            $month_suffix = $float - $num;
            $m_diff = ceil(12 * $month_suffix);
            $m = month_sub($now_month,$m_diff);
            if($m > $now_month){
                $year--;
            }
            $month = str_pad($m,2,'0',STR_PAD_LEFT);
        }
    }
    if($date_str == ''){
        $date_str = sprintf('%s-%s-%s',$year,$month,$day);
    }

    return $date_str;
}

/**
 * 月份减少
 * @param $base_month
 * @param $month_diff
 */
function  month_sub($base_month,$month_diff){

    $month = $base_month;
    for($i=0;$i<$month_diff;$i++){
        $month--;
        if($month == 0){
            $month = 12;
        }
    }

    return $month;
}

/**
 * 返回2个时间戳之间的天数
 * @param $ts1
 * @param $ts2
 */
function day_diff($ts1,$ts2){
    return round(($ts2-$ts1)/3600/24);
}

/**
 * 根据金额获取会员vip级别
 * @param $member_config
 * @param $amount
 */
function get_vip_level_by_amount($member_config,$amount){
    $vip_level = -1;
    if($member_config['enable'] == 0){
        return $vip_level;
    }
    $max = $member_config['max_level'];
    $levels = $member_config['level'];
    for($i=$max;$i>0;$i--){
        if($amount >= $levels[$i]['amount']){
            $vip_level = $i;
            if($levels[$i]['amount'] != 0){
                break;
            }
        }
    }

    return $vip_level;
}

/**
 * 获得性别
 * @param $sex
 * @return mixed
 */
function get_sex($sex){
    $map = [
        0=>'未确定',
        1=>'男',
        2=>'女'
    ];
    return $map[$sex];
}

/**
 * 获得家庭成员你关系map定义
 * @return array
 */
function get_family_rel_map(){
    return  [
        0=>'未设置',
        1=>'自己',
        2=>'爸爸',
        3=>'妈妈',
        4=>'其他',
        5=>'爷爷',
        6=>'奶奶',
        7=>'外公',
        8=>'外婆'
    ];
}
/**
 * 获得家庭成员关系
 * @param $rel
 * @return mixed|string
 */
function get_family_rel($rel){

    $map = get_family_rel_map();
    return isset($map[$rel])?$map[$rel]:'-';
}

/**
 * 获得家庭成员关系ID
 * @param $value
 * @return int|string
 */
function get_family_rel_id($value){
    $map = get_family_rel_map();
    $ret = 4;
    foreach($map as $id=>$v){
        if($v == $value){
            $ret = $id;
            break;
        }
    }
    return $ret;
}

/**
 * 获得实际的年级
 * @param $grade
 * @param $update_grade_int_ym
 * @return float|int
 */
function get_real_grade($grade,$update_grade_int_ym){
    if($update_grade_int_ym == 0){
        return $grade;
    }
    $now_ym = intval(date('Ym',time()));
    $now_y = floor($now_ym / 100);
    $now_m = $now_ym % 100;
    $base_y = floor($update_grade_int_ym / 100);
    $base_m = $update_grade_int_ym % 100;

    $add_grade = $now_y - $base_y;
    if($now_m >= 9 && $base_m < 9){
        $add_grade++;
    }

    $grade = $grade+$add_grade;

    if($grade == 0){
        $grade = 1;
    }

    return $grade;
}


function gmt_iso8601($time) {
    $dtStr = date("c", $time);
    $mydatetime = new DateTime($dtStr);
    $expiration = $mydatetime->format(DateTime::ISO8601);
    $pos = strpos($expiration, '+');
    $expiration = substr($expiration, 0, $pos);
    return $expiration."Z";
}

/**
 * 自动获取校区ID
 * @return int|string
 */
function auto_bid(){
    $bid = 0;
    $param_bid = request()->param('bid');
    if($param_bid){
        return $param_bid;
    }
    $header_bid = request()->header('x-bid');
    if($header_bid){
        return $header_bid;
    }
    return $bid;
}

/**
 * 将数组转化为整形
 * @param $arr
 * @return array
 */
function array_intval($arr){
    if(!is_array($arr)){
        return $arr;
    }
    foreach($arr as $k=>$v){
        if(is_scalar($v)){
            $arr[$k] = intval($v);
        }
    }
    return $arr;

}

/**
 * 获得URL文件名
 * @param $url
 * @return mixed
 */
function get_url_filename($url){
    $url_info = parse_url($url);
    $path_info = pathinfo($url_info['path']);
    return $path_info['basename'];
}

/**
 * 下载文件
 * @param $url
 * @param string $save_dir
 * @param string $filename
 * @param int $type
 * @return array|bool
 */
function download_file($url,$filename = '', $save_dir = '',$type = 1) {
    defined('DATA_ROOT_PATH') || define('DATA_ROOT_PATH',ROOT_PATH.'public/data/');
    if (trim($url) == '') {
        return false;
    }
    if (trim($save_dir) == '') {
        $save_dir = ROOT_PATH.'public'.DS.'data'.DS.'download';
    }
    if (0 !== strrpos($save_dir, '/')) {
        $save_dir .= DS;
    }
    //创建保存目录
    if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
        return false;
    }
    if($filename == ''){
        $filename = get_url_filename($url);
    }
    //获取远程文件所采用的方法
    if ($type) {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Chrome 42.0.2311.135');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $content = curl_exec($ch);
        curl_close($ch);
    } else {
        ob_start();
        readfile($url);
        $content = ob_get_contents();
        ob_end_clean();
    }
    //echo $content;
    $size = strlen($content);
    $real_file = $save_dir.$filename;
    if(file_exists($real_file)){
        @unlink($real_file);
    }
    //文件大小
    $fp = @fopen($save_dir . $filename, 'a');
    fwrite($fp, $content);
    fclose($fp);
    unset($content, $url);
    return [
        'file_name' => $filename,
        'save_path' => $save_dir . $filename,
        'file_size' => $size
    ];
}

/**
 * 本地文件上传
 * @param $url
 * @return array|bool
 */
function upload_file($local_file,$path = '',$storage = 'qiniu'){

    $config = user_config('storage.'.$storage);

    $path_info = pathinfo($local_file);

    $filename = short_url(md5($local_file)).'.'.$path_info['extension'];
    if($path == ''){
        $client = gvar('client');
        if($client) {
            $cid = $client['cid'];
            $og_id = gvar('og_id');

        }else{
            $cid = 0;
            $og_id = 0;
        }
        $path = $cid.'/'.$og_id.'/'.int_day(time()).'/'.$filename;
    }
    // 构建鉴权对象
    $auth = new \Qiniu\Auth($config['access_key'], $config['secret_key']);

    // 要上传的空间
    $bucket = $config['bucket'];

    // 生成上传 Token
    $token = $auth->uploadToken($bucket);

    // 要上传文件的本地路径
    $filePath = $local_file;

    // 上传到七牛后保存的文件名
    $key = $config['prefix'].$path;

    // 初始化 UploadManager 对象并进行文件的上传。
    $uploadMgr = new \Qiniu\Storage\UploadManager();

    // 调用 UploadManager 的 putFile 方法进行文件的上传。
    list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);

    if($err !== null){
        return false;
    }

    $file_url = $config['domain'].$key;

    $file['file_url']  = $file_url;
    $file['storage']   = $storage;

    return $file;

}

/**
 * Excel的日期格式转换
 * @param $date
 * @param bool $time
 * @return array|int|string
 */
function excel_datetime($date,$time = false){
    if(function_exists('GregorianToJD')){
        if (is_numeric( $date )) {
            $jd = GregorianToJD( 1, 1, 1970 );
            $gregorian = JDToGregorian( $jd + intval ( $date ) - 25569 );
            $date = explode( '/', $gregorian );
            $date_str = str_pad( $date [2], 4, '0', STR_PAD_LEFT )
                ."-". str_pad( $date [0], 2, '0', STR_PAD_LEFT )
                ."-". str_pad( $date [1], 2, '0', STR_PAD_LEFT )
                . ($time ? " 00:00:00" : '');
            return $date_str;
        }
    }else{
        $date = intval($date);
        $date=$date>25568?$date+1:25569;
        /*There was a bug if Converting date before 1-1-1970 (tstamp 0)*/
        $ofs=(70 * 365 + 17+2) * 86400;
        $date = date("Y-m-d",($date * 86400) - $ofs).($time ? " 00:00:00" : '');
    }
    return $date;
}

/**
 * 不为空
 * @param $val
 * @return string
 */
function not_null($val){
    if(is_null($val)){
        return '';
    }
    return $val;
}

/**
 * 确保https
 * @param $url
 * @return mixed
 */
function ensure_https($url){
    if(substr($url,0,5) == 'https'){
        return $url;
    }
    return str_replace('http:','https:',$url);
}


/**
 * 获取 两个数组 键名相同 键值不同的值
 * @param  [type] $old_array [description]
 * @param  [type] $new_array [description]
 * @return [type]            [description]
 */
function get_array_diff_value($old_array,$new_array)
{
    $need_convert_time_fields = ['birth_time','last_attendance_time','get_time'];
    $no_need_compare_fields = ['option_fields','arrange_times','end_lesson_time','first_tel','second_tel','assign_time','last_follow_time','trial_time','get_time','create_time'];
    $diff_array = [];
    $old_keys = array_keys($old_array);
    $new_keys = array_keys($new_array);
    $keys = array_intersect($old_keys,$new_keys);
    foreach ($keys as $key) {
     	if(in_array($key,$no_need_compare_fields)){
            continue;
        }
        if(in_array($key,$need_convert_time_fields)){
            $new_array[$key] = strtotime($new_array[$key]);
        }
       
        if($old_array[$key] != $new_array[$key]){
            $diff_array[] = [
                'field' => $key,
                'old_value'  => $old_array[$key],
                'new_value'  => $new_array[$key],
            ];
        }
    }
    return $diff_array;
}

/**
 *  http请求
 * @param $url
 * @return mixed
 */
function http_request($url){
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Chrome 42.0.2311.135');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

    $response = curl_exec($ch);

    curl_close($ch);

    return $response;
}

/**
 * 获得或设置用户校区配置
 * @param $key
 * @param $bid
 * @param $section
 * @param $val      值
 */
function user_branch_config($key,$bid,$section = 'report',$val = []){
    $cfg_key = sprintf("%s_%s_%s", $section, $key, $bid);
    $mConfig = new Config();
    if(is_null($val)){
        //删除配置，恢复默认配置
        $d['cfg_name'] = $cfg_key;
        $d['og_id']    = gvar('og_id');

        $result = $mConfig->where($d)->delete(true);

        if(false === $result){
            return false;
        }
        return true;
    }else{
        if(empty($val)) {
            $default_cfg_file = CONF_PATH . $section . '/' . $key . '.php';

            if (!is_file($default_cfg_file)) {
                return null;
            }

            $default_config = include($default_cfg_file);
            $config = $default_config;
            $row = Config::get_config($cfg_key);
            if($row) {
                $user_config = $row['cfg_value'];
                if (!empty($user_config)) {
                    foreach ($default_config as $kk => $vv) {
                        if (isset($user_config[$kk])) {
                            if (is_array($vv) && !empty($vv)) {
                                $config[$kk] = deep_array_merge($vv, $user_config[$kk]);
                            } else {
                                $config[$kk] = $user_config[$kk];
                            }
                        }
                    }
                }
            }

            return $config;
        }else{
            $d['cfg_name'] = $cfg_key;
            $d['og_id']    = gvar('og_id');


            $ex_config = $mConfig->where($d)->find();

            if($ex_config){
                $ex_config['cfg_value'] = $val;
                $result = $ex_config->save();
                if(false === $result){
                    return false;
                }
                return true;
            }else{
                $d['cfg_value']    = $val;
                $d['format']   = 'json';
                $result = $mConfig->save($d);
                if(!$result){
                    return false;
                }
                return true;
            }
        }
    }
}

/**
 * 年龄转化成月数
 * @param $age
 * @return float|int
 */
function age_to_months($age){
    $age_str = strval($age);
    if(strpos($age_str,'.') !== false){
        $arr = explode('.',$age_str);
        $year  = intval($arr[0]);
        $month = intval($arr[1]);
        if($month > 11){
            $month = 11;
        }
    }else{
        $year = intval($age_str);
        $month = 0;
    }


    $months = $year*12 + $month;
    return $months;

}

