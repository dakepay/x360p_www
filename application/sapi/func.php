<?php


//获取sid
function global_sid() {
    //--1-- 请求参数的
    $sid = input('sid') ? input('sid/d') : 0;
    if($sid) return $sid;

    //--2-- 请求头的x-bid
    $sid = isset(request()->sid) ? request()->sid : 0;
    if($sid) return $sid;

    //--3-- 请求用户的默认sid
    $sid = gvar('user') && isset(gvar('user')->default_sid) ? gvar('user')->default_sid : 0;

    return $sid;
}

//通过token查找登录信息
function login_info($key = null)
{
    $token = request()->header('x-token');

    $cache_key = 'SESS_'.$token;
    $login_info = cache($cache_key);

    switch($key) {
        case 'uid':
            $login_info =  isset($login_info['uid']) ? $login_info['uid'] : 0;
            break;
        case 'og_id':
            $login_info =  isset($login_info['og_id']) ? $login_info['og_id'] : 0;
            break;
        case 'bid':
            $login_info =  isset($login_info['bid']) ? $login_info['bid'] : 0;
            break;
        default :
            $login_info = $login_info || [];
    }

    return $login_info;
}

//某月的开始时间， 结束时间
function where_month($year_month)
{
    $year_month = format_int_day($year_month);
    $year = substr($year_month, 0, 4);
    $month = substr($year_month, 4, 2);

    $start_day = $year.$month.'01';
    $start = strtotime($start_day);
    $end = strtotime(date('Ymd', strtotime('last day of '.$start_day)));

    return ['between', [$start, $end]];
}

//机构名称
function org_name($og_id = 0) {
    $params = (new \app\sapi\model\Config())->where('og_id', $og_id)->where('cfg_name = "params"')
        ->field('cfg_value')->find();

    if(empty($params) && $og_id > 0) {
        $params = (new \app\sapi\model\Config())->where('og_id = 0')->where('cfg_name = "params"')
            ->field('cfg_value')->find();
    }

    return !empty($params) && isset($params['cfg_value']['org_name']) ? $params['cfg_value']['org_name'] : '';
}


//取得三级客户域名
function get_sub_domain()
{
    $request = request();
    $config_domain = config('ui.domain');
    $pos = strrpos($request->host(), $config_domain);
    if(!$pos) return '';

    $pre_domain = substr($request->host(), 0, $pos - 1);
    $pre_domain_arr = explode('.', $pre_domain);
    return array_pop($pre_domain_arr);
}