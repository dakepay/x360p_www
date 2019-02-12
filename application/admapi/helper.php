<?php

function get_employee_name_center($id){
    $info = get_employee_info($id);
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
function get_client_info($id,$cache = true){
    return get_row_info($id,'client','cid',$cache);
}

function get_client_name($id){
    $info = get_client_info($id);
    if(!$info){
        return '-';
    }
    return $info['client_name'];
}

/**
 * 获得App信息
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function get_app_info($id,$cache = true){
    return get_row_info($id,'app','app_id',$cache);
}

function get_app_name($id){
    $info = get_app_info($id);
    if (!$info){
        return '-';
    }
    return $info['app_name'];
}

