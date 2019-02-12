<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/20
 * Time: 10:52
 */
namespace app\sapi\model;

class Config extends Base
{
    protected $type = [
        'cfg_value' => 'json'
    ];

    protected $hidden = ['format', 'create_time', 'create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    /**
     * 获取用户配置
     * @return [type] [description]
     */
    public static function userConfig(){
        $cfg_list = self::all();
        $config = [];
        foreach($cfg_list as $k=>$cfg){
            if($cfg['cfg_name'] == 'lesson'){
                $config['lesson'] = [];
                foreach($cfg['cfg_value'] as $_k=>$c){
                    $config['lesson'][$_k] = self::filter_enable_item($c);
                }
                continue;
            }
            $config[$cfg['cfg_name']] = $cfg_list[$k]['cfg_value'];
        }
        return $config;
    }

    protected static function filter_enable_item($list){
        $ret = [];
        foreach($list as $k=>$item){
            if(!isset($item['enable'])){
                $item['enable'] = true;
            }
            if($item['enable']){
                array_push($ret,$item);
            }
        }
        return $ret;
    }

}