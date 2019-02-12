<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/15
 * Time: 18:43
 */
namespace app\api\export;

use app\common\Export;
use app\api\model\MarketClue;


class MarketClues extends Export
{
    protected $res_name = 'market_clue';

    protected $columns = [
        ['field'=>'name','title'=>'姓名','width'=>20],
        ['field'=>'sex','title'=>'性别','width'=>20],
        ['field'=>'tel','title'=>'电话','width'=>20],
        ['field'=>'email','title'=>'邮箱','width'=>20],
        ['field'=>'birth_time','title'=>'出生日期/年龄','width'=>25],
        ['field'=>'bid','title'=>'校区','width'=>20],
        ['field'=>'cu_assigned_bid','title'=>'分配校区','width'=>20],
        ['field'=>'school_grade','title'=>'年级','width'=>20],
        ['field'=>'mc_id','title'=>'来源渠道','width'=>20],
        ['field'=>'from_did','title'=>'招生来源','width'=>20],
        ['field'=>'is_valid','title'=>'有效性','width'=>20],
        ['field'=>'is_deal','title'=>'是否转化','width'=>20],
        ['field'=>'is_visit','title'=>'是否到访','width'=>20],
        ['field'=>'is_deal','title'=>'是否成交','width'=>20],
        ['field'=>'cu_assigned_eid','title'=>'销售跟进人','width'=>20],
        ['field'=>'assigned_eid','title'=>'市场跟进人','width'=>20],
        ['field'=>'remark','title'=>'备注','width'=>40],
        ['field'=>'create_uid','title'=>'录入人','width'=>20],
        ['field'=>'create_time','title'=>'录入时间','width'=>20],
    ];

    protected function get_title(){
        $title = '市场名单';
        return $title;
    }

    protected function convert_valid($value)
    {
        $map = [0=>'待确认', 1=>'有效', 2=>'无效'];
        if (key_exists($value, $map)) {
            return $map[$value];
        }
        return '';
    }

    protected function convert_visit($key)
    {
        $map = [0=>'未上访',1=>'已上访'];
        if(key_exists($key,$map)){
            return $map[$key];
        }
        return '待确定';
    }

    protected function convert_grade($value)
    {
        $w['name'] = $value;
        $w['pid'] = 11;
        $w['og_id'] = gvar('og_id');
        $grade = m('dictionary')->where($w)->value('title');
        
        return $grade ? $grade : '-';
    }
    
    /**
     * 计算岁数
     * @param  [type] $birthday [description]
     * @return [type]           [description]
     */
    protected function getAge($birthday)
    {
        //格式化出生时间年月日
        $byear=date('Y',$birthday);
        $bmonth=date('m',$birthday);
        $bday=date('d',$birthday);
        //格式化当前时间年月日
        $tyear=date('Y');
        $tmonth=date('m');
        $tday=date('d');
        //开始计算年龄
        $age=$tyear-$byear;
        if($bmonth>$tmonth || $bmonth==$tmonth && $bday>$tday){
            $age--;
        }
        return $age;
    }
    
    /**
     * 计算月
     * @param  [type] $birthday [description]
     * @return [type]           [description]
     */
    protected function getMonth($birthday)
    {
        //格式化出生时间年月日
        $byear=date('Y',$birthday);
        $bmonth=date('m',$birthday);
        $bday=date('d',$birthday);
        //格式化当前时间年月日
        $tyear=date('Y');
        $tmonth=date('m');
        $tday=date('d');
        //开始计算月份
        if($tmonth>=$bmonth){
            $month = $tmonth-$bmonth;
        }else{
            $month = 12-($bmonth-$tmonth);
        }
        return $month;
    }

    protected function convert_birth_time($val)
    {
        if($val){
            $age = $this->getAge(strtotime($val));
            $month = $this->getMonth(strtotime($val));
            return $val.' / '.$age.'岁'.$month.'个月';
        }
        return '-';
    }


    public function get_data()
    {
        $model = new MarketClue();
        $data = $model->getSearchResult($this->params,$this->pagenation);

        foreach ($data['list'] as $k => $v) {
            $data['list'][$k]['mc_id'] = get_channel_name($v['mc_id']);
            $data['list'][$k]['sex'] = get_sex($v['sex']);
            $data['list'][$k]['bid'] = get_branch_name($v['bid']);
            $data['list'][$k]['cu_assigned_bid'] = get_branch_name($v['cu_assigned_bid']);
            $data['list'][$k]['school_grade'] = $this->convert_grade($v['school_grade']);
            $data['list'][$k]['from_did'] = get_did_value($v['from_did']);
            $data['list'][$k]['is_visit'] = $this->convert_visit($v['is_visit']);
            $data['list'][$k]['is_valid'] = $this->convert_valid($v['is_valid']);
            $data['list'][$k]['assigned_eid'] = get_teacher_name($v['assigned_eid']);
            $data['list'][$k]['cu_assigned_eid'] = get_teacher_name($v['cu_assigned_eid']);
            $data['list'][$k]['create_uid'] = get_user_name($v['create_uid']);
            $data['list'][$k]['birth_time'] = $this->convert_birth_time($v['birth_time']);
            $data['list'][$k]['tel'] = ' '.$v['tel'];
            $data['list'][$k]['is_deal'] = $v['cu_id']?'已转化':'-';
            $data['list'][$k]['remark'] = $data['list'][$k]['remark']?$data['list'][$k]['remark']:'-';
        }

        if($this->pagenation){
            return $data;
        }
        if (!empty($data['list'])) {
            return collection($data['list'])->toArray();
        }

        return [];

    }
}