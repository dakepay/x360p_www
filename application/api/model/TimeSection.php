<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/5
 * Time: 14:20
 */
namespace app\api\model;

class TimeSection extends Base
{
    protected $readonly = ['bid', 'season'];
    protected static function init()
    {
        parent::init();
    }

    public function setIntStartHourAttr($value)
    {
        return format_int_hour($value);
    }

    public function setIntEndHourAttr($value)
    {
        return format_int_hour($value);
    }

    public function getIntStartHourAttr($value)
    {
        return int_hour_to_hour_str($value);
    }


    public function getIntEndHourAttr($value)
    {
        return int_hour_to_hour_str($value);
    }

    /**
     * 获得校区的时间段设置
     * @param  [type] $bid    [description]
     * @param  [type] $season [description]
     * @return [type]         [description]
     */
    public function getBranchTimeSections($bid,$season){
        $w['bid']       = $bid;
        $w['season']    = $season;

        $list = $this->where($w)->select();

        if(!$list){
            $w['bid'] = 0;
            $list = $this->where($w)->select();
        }

        return $list;

    }

    public function delOneTimeSection($tsid)
    {
        $record = $this->find($tsid);
        if (!$record) {
            $this->error = '该条数据不存在或已删除';
        }
        $map = [];
        $map['bid'] = $record->bid;
        $map['season'] = $record->season;
        $map['time_index'] = ['>', $record->time_index];

        $this->startTrans();
        try {
            $res = $this->where($map)->update(['time_index' => ['exp', 'time_index-1']]);
            $record->delete();
        } catch (\Exception $e) {
            $this->rollback();
            $this->error = $e->getMessage();
            return false;
        }
        $this->commit();
        return true;
    }

    /**
     * @desc 添加时间间隔后根据int_start_hour字段排序，创建time_index字段后保存
     * @param $bid 校区id
     * @param $season  季节（C,S,Q,H）
     */
    public function sortTimeIndex($bid, $season,$week_day = -1)
    {
        $map['bid'] = $bid;
        $map['season'] = $season;
        $map['week_day'] = $week_day;
        $list = $this->where($map)->order('int_start_hour', 'asc')->select();
        $this->startTrans();

        $is_start_trans = app_reg('is_start_trans');

        foreach ($list as $key => $item) {
            $temp = $key + 1;
            if ($temp != $item->time_index) {
                $item->time_index = $temp;
                $item->save();
            }
        }
        $this->commit();
        return true;
    }

    /**
     * 添加时间段
     * 可批量或单个时间段添加
     * @param [type] &$input [description]
     */
    public function addTimeSections(&$input)
    {
        if(!isset($input['time_list'])){
            return $this->addTimeSection($input);
        }
        //mulit add
        $this->startTrans();
        $bid = intval($input['time_list'][0]['bid']);
        $season = safe_str($input['time_list'][0]['season']);
        $week_day = intval($input['time_list'][0]['week_day']);
        foreach($input['time_list'] as $_input){
            $result = $this->addTimeSection($_input,false);
            if(!$result){
                $this->rollback();
                return false;
            }
        }
        $this->sortTimeIndex($bid,$season,$week_day);
        $this->commit();
        return true;
    }

    /**
     * 添加时间段
     * 单个添加
     * @param [type]  &$input  [description]
     * @param boolean $do_sort [description]
     */
    public function addTimeSection(&$input,$do_sort = true)
    {
        //single add
        $w['int_start_hour'] = format_int_hour($input['int_start_hour']);
        $w['int_end_hour']   = format_int_hour($input['int_end_hour']);
        $w['bid']            = intval($input['bid']);
        $w['season']         = safe_str($input['season']);
        $w['week_day']       = intval($input['week_day']);

        $exists_ts = $this->where($w)->find();

        if($exists_ts){
            $this->user_error(sprintf('时间段%s ~ %s已经存在!',$input['int_start_hour'],$input['int_end_hour']));
            return false;
        }

        $this->startTrans();

        try{
            $tsid = $this->isUpdate(false)->save($input);
            if($do_sort){
                $this->sortTimeIndex($w['bid'],$w['season'],$w['week_day']);
            }
        }catch(Exception $e){
            $this->rollback();
            $this->user_error($this->getLastSql()."\n".$e->getMessage());
            return false;
        }

        $this->commit();

        return $tsid;
    }

    /**
     * 编辑时间段
     * @param  [type]  &$input [description]
     * @param  integer $tsid   [description]
     * @return [type]          [description]
     */
    public function editTimeSection(&$input,$tsid = 0){
        if($tsid == 0){
            $tsid = intval($input['tsid']);
        }

        $ts = $this->where('tsid',$tsid)->find();

        if(!$ts){
            $this->user_error('时间段不存在!');
            return false;
        }

        $this->startTrans();

        try{
            $ts->allowField('int_start_hour,int_end_hour')->save($input);
            $this->sortTimeIndex($ts->bid,$ts->season,$ts->week_day);
        }catch(Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();

        return true;
    }
    /**
     * 删除单条信息
     * @param  integer $tsid    [description]
     * @param  boolean $do_sort [description]
     * @return [type]           [description]
     */
    public function singleDelete($tsid = 0,$do_sort = true){
        if($tsid != 0){
            $rs = $this->find($tsid);
        }else{
            $rs = $this;
        }
        $this->startTrans();
        try{
           $rs->delete(true);
            if($do_sort){
                $this->sortTimeIndex($rs->bid,$rs->season,$rs->week_day);
            } 
        }catch(Exception $e){
            $this->rollback();
            $this->user_error($e->getMessage());
            return false;
        }
        $this->commit();
        return true;
    }

    /**
     * 批量删除信息
     * @param  [type] $ids [description]
     * @return [type]      [description]
     */
    public function batDelete($ids){
        $arr_ids = explode(',',$ids);

        if(empty($arr_ids)){
            $this->user_error('缺少参数');
            return false;
        }

        $first_id = $arr_ids[0];

        $rs = $this->find($first_id);

        $this->startTrans();

        $w['tsid'] = ['in',$arr_ids];

        try{
            $result = $this->where($w)->delete(true);
            $this->sortTimeIndex($rs->bid,$rs->season,$rs->week_day);
        }catch(Exception $e){
            $this->rollback();
            $this->user_error($e->getMessage());
            return false;
        }

        $this->commit();

        return true;


    }

    /**
     * 复制时间段
     * @param  [type] $tsid      [description]
     * @param  array  $week_days 复制的周天数组，比如[1,2,3,4,5]
     * @return [type]            [description]
     */
    public function copyTime($tsid,$week_days = []){
        $rs = $this->where('tsid',$tsid)->find();
        if(!$rs){
            $this->user_error('要复制的时间段不存在!');
            return false;
        }

        $copy_fields = ['bid','season','int_start_hour','int_end_hour'];

        $this->startTrans();
        try{
            foreach($week_days as $day){
                $r = [];
                foreach($copy_fields as $f){
                    $r[$f] = $rs[$f];
                }
                $r['int_start_hour'] = format_int_hour($r['int_start_hour']);
                $r['int_end_hour']   = format_int_hour($r['int_end_hour']);
                $r['week_day'] = $day;

                $ex_rs = $this->where($r)->find();
                if(!$ex_rs){
                    $r['time_index'] = 0;
                    $tsid = $this->data($r,true)->isUpdate(false)->save();
                    if(!$tsid){
                        $this->rollback();
                        return $this->sql_add_error('time_section');
                    }
                    $this->sortTimeIndex($r['bid'],$r['season'],$r['week_day']);
                }
            }
        }catch(Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }
}