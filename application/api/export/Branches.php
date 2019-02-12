<?php
namespace app\api\export;

use app\common\Export;
use app\api\model\Branch;

class Branches extends Export{
    protected $res_name = 'branch';

    protected $columns = [
        ['title'=>'校区名称','field'=>'branch_name','width'=>20],
        ['title'=>'校区简称','field'=>'short_name','width'=>20],
        ['title'=>'校区类型','field'=>'branch_type','width'=>20],
        ['title'=>'校区电话','field'=>'branch_tel','width'=>20],
        ['title'=>'省份','field'=>'province_id','width'=>20],
        ['title'=>'城市','field'=>'city_id','width'=>20],
        ['title'=>'区域','field'=>'district_id','width'=>20],
        ['title'=>'行政区','field'=>'area_id','width'=>20],
        ['title'=>'地址','field'=>'address','width'=>50],
    ];

    protected $area_map;

    protected function __init()
    {
        $this->init_map();
    }

    protected function init_map()
    {
        if (empty($this->area_map)) {
            $this->area_map = db('area')->column('name', 'area_id');
        }
    }

    protected function get_title(){
        $title = '校区统计信息';
        return $title;
    }

    protected function convert_branch_type($value)
    {
        $map = [1 => '直营', 2 => '加盟'];
        if (key_exists($value, $map)) {
            return $map[$value];
        }
        return '';
    }

    public function convert_map_id($id)
    {
        if (empty($id)) {
            return '';
        }
        if (key_exists($id, $this->area_map)) {
            return $this->area_map[$id];
        }
        return '';
    }


    public function get_data()
    {
        $list = Branch::all();
        if (!empty($list)) {
            $list = collection($list)->toArray();
        }
        foreach ($list as &$item) {
            $item['branch_type'] = $this->convert_branch_type($item['branch_type']);
            $item['province_id'] = $this->convert_map_id($item['province_id']);
            $item['city_id'] = $this->convert_map_id($item['city_id']);
            $item['district_id'] = $this->convert_map_id($item['district_id']);
            $item['area_id'] = $this->convert_map_id($item['area_id']);
        }
        return $list;
    }
}