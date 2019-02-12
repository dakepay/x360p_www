<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/15
 * Time: 18:43
 */
namespace app\api\export;

use app\common\Export;
use app\api\model\Customer;
use app\api\model\Student;
use app\api\model\User;
use app\api\model\CustomerFollowUp;


class Customers extends Export
{
    protected $res_name = 'customer';

    protected $columns = [
        ['field'=>'bid','title'=>'校区名称','width'=>20],
        ['field'=>'name','title'=>'姓名','width'=>20],
        ['field'=>'nick_name','title'=>'昵称','width'=>20],
        ['field'=>'sex','title'=>'性别','width'=>20],
        ['field'=>'birth_time','title'=>'出生日期/年龄','width'=>20],
        ['field'=>'first_tel','title'=>'手机号码','width'=>20],
        ['field'=>'second_tel','title'=>'第二手机号码','width'=>20],  //
        ['field'=>'home_address','title'=>'家庭住址','width'=>20],
        ['field'=>'follow_times','title'=>'跟进次数','width'=>20],
        ['field'=>'trial_listen_times','title'=>'试听次数','width'=>20],
        ['field'=>'visit_times','title'=>'到访次数','width'=>20],

        ['field'=>'last_follow_time','title'=>'最后跟进时间','width'=>20],
        ['field'=>'last_follow_content','title'=>'最后跟进内容','width'=>40],

        ['field'=>'no_follow_day','title'=>'未跟进天数','width'=>20],

        ['field'=>'next_follow_time','title'=>'下次跟进时间','width'=>20],
        ['field'=>'trial_time','title'=>'方便试听时间','width'=>30],
        ['field'=>'customer_status_did','title'=>'客户状态','width'=>20],
        ['field'=>'intention_level','title'=>'意向程度','width'=>15],

        ['field'=>'is_reg','title'=>'是否报读','width'=>20],
        ['field'=>'signup_int_day','title'=>'报名时间','width'=>20],
        ['field'=>'signup_amount','title'=>'报名金额','width'=>20],
        ['field'=>'create_uid','title'=>'录单人','width'=>20],
        ['field'=>'create_time','title'=>'录入时间','width'=>20],
        ['field'=>'remark','title'=>'备注','width'=>30],
        ['field'=>'mc_id','title'=>'市场渠道','width'=>20],
        ['field'=>'from_did','title'=>'招生来源','width'=>20],

        ['field'=>'school_id','title'=>'公立学校','width'=>20],
        ['field'=>'school_grade','title'=>'年级','width'=>20],
        ['field'=>'school_class','title'=>'学校班级','width'=>20],

        ['field'=>'referer_sid','title'=>'介绍人','width'=>20],
        ['field'=>'follow_eid','title'=>'责任人','width'=>20],
        ['field'=>'assign_time','title'=>'分配时间','width'=>20],
        ['field'=>'get_time','title'=>'获取时间','width'=>20],
    ];

    protected function get_title(){
        $title = '客户名单';
        return $title;
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

    protected function convert_reg($key)
    {
        $map = [0=>'未报读',1=>'已报读'];
        if(key_exists($key,$map)){
            return $map[$key];
        }
        return '未确定';
    }

    protected function get_no_follow_day($get_time,$last_follow_time)
    {
        $get_time = $get_time ?: 0;
        $last_follow_time = $last_follow_time ?: $get_time;
        $last_follow_time = strtotime($last_follow_time);
        $no_follow_time = time() - $last_follow_time;
        $day = intval($no_follow_time/86400) + 1;
        return $day;
    }

    protected function convert_trial_time($trial_time)
    {
        $section = [];
        $week_map = [1=>'周一',2=>'周二',3=>'周三',4=>'周四',5=>'周五',6=>'周六',7=>'周日'];
        $day_map  = [1=>'上午',2=>'下午',3=>'晚上'];
        $times = $trial_time ?: [];
        if(!empty($times)){
            foreach ($times as $time) {
                $week = $week_map[substr($time,0,1)];
                $day = $day_map[substr($time,1,1)];
                $section[] = $week.$day;
            }
            return implode(',',$section);
        }
        return '-';
    }

    public function get_data()
    {

        $input = $this->params;
        $model = new Customer();

        if(!empty($input['lid']) || !empty($input['eid'])){
            $model = new \app\api\model\CustomerIntention();
            $model->alias('ci')->join('customer c','c.cu_id = ci.cu_id','left')->distinct(true);
        }else{
            $model->alias('c');
        }

        if (!empty($input['name'])) {
            $name = $input['name'];
            unset($input['name']);
            if(preg_match("/^[a-z]*$/i", $name)) {
                $where = sprintf('c.name like "%%%s%%" or c.pinyin like "%%%s%%" or c.pinyin_abbr = "%s"', $name, $name, $name);
            } else {
                $where = sprintf("c.name like '%%%s%%'", $name);
            }
            $model->where($where);
        }

        //试听时间段
        if(isset($input['trial_time'])) {
            $trial_time = $input['trial_time'];
            unset($input['trial_time']);
            if(!empty($trial_time)) {
                $trial_time = explode(',', $trial_time);
                $trial_time_where = [];
                foreach($trial_time as $per_time) {
                    $trial_time_where[] = "find_in_set($per_time, c.trial_time)";
                }
                $model->where(implode(' or ', $trial_time_where));
            }
        }

        if(isset($input['age_start'])) {
            $age_start = intval($input['age_start']);
            $age_start_time = strtotime($age_start . ' years ago');
            $model->where('c.birth_time', 'lt', $age_start_time);
        }
        if(isset($input['age_end'])) {
            $age_end = intval($input['age_end']);
            $age_end_time = strtotime($age_end . ' years ago');
            $model->where('c.birth_time', 'gt', $age_end_time);
        }

        if(!empty($input['with'])) {
            $with = $tmp_with = explode(',', $input['with']);
            if(array_search('last_customer_follow_up', $tmp_with) >= 0) {
                unset($tmp_with[array_search('last_customer_follow_up', $tmp_with)]);
                $input['with'] = implode(',', $tmp_with);
            }
        }

        $ret = $model->getSearchResult($input,$this->pagenation);

        if(!empty($ret['list'])) {
            $m_cfu = new CustomerFollowUp();
            foreach($ret['list'] as &$customer) {
                if($customer['follow_times']){
                    $customer['is_connect'] = $m_cfu->where('cu_id',$customer['cu_id'])->order('cfu_id desc')->value('is_connect');  // 是否为有效沟通
                }
                if(isset($customer['mc_id'])){
                    $mc_info = get_mc_info($customer['mc_id']);
                    $customer['channel_name'] = $mc_info['channel_name'];
                }else{
                    $customer['channel_name'] = '-';
                }

            }
        }

        $mCfu = new CustomerFollowUp();
        foreach ($ret['list'] as $k => $v) {
            $cu_follow_up_info = $mCfu->where(['cu_id'=>$v['cu_id'],'is_system'=>0])->order('create_time desc')->find();
            if($cu_follow_up_info){
                $last_content = $cu_follow_up_info['content'];
            }else{
                $last_content = '';
            }
            $ret['list'][$k]['bid']       = get_branch_name($v['bid']);
            $ret['list'][$k]['nick_name'] = $v['nick_name'] ?: '-';
            $ret['list'][$k]['school_id']  = get_school_name($v['school_id']);
            $ret['list'][$k]['school_grade'] = get_grade_name($v['school_grade']);
            $ret['list'][$k]['sex'] = get_sex($v['sex']);
            $ret['list'][$k]['first_family_rel'] = get_family_rel($v['first_family_rel']);
            $ret['list'][$k]['second_family_rel'] = get_family_rel($v['second_family_rel']);
            $ret['list'][$k]['birth_time'] = $this->convert_birth_time($v['birth_time']);
            $ret['list'][$k]['second_tel'] = $v['second_tel'] ?: '-';
            $ret['list'][$k]['home_address'] = $v['home_address'] ?: '-';
            $ret['list'][$k]['last_follow_time'] = date('Y-m-d',strtotime($cu_follow_up_info['create_time']));
            $ret['list'][$k]['last_follow_content'] = $last_content;
            $ret['list'][$k]['no_follow_day'] = $this->get_no_follow_day($v['get_time'],$v['last_follow_time']);
            $ret['list'][$k]['customer_status_did'] = get_did_value($v['customer_status_did']);
            $ret['list'][$k]['is_reg'] = $this->convert_reg($v['is_reg']);
            $ret['list'][$k]['signup_int_day'] = $v['signup_int_day'] ? date('Y-m-d',strtotime($v['signup_int_day'])) : '';
            $ret['list'][$k]['mc_id'] = get_mc_name($v['mc_id']);
            $ret['list'][$k]['from_did'] = get_did_value($v['from_did']);
            $ret['list'][$k]['referer_sid'] = get_student_name($v['referer_sid']);
            $ret['list'][$k]['follow_eid'] = get_teacher_name($v['follow_eid']);
            $ret['list'][$k]['remark'] = $v['remark'] ?: '-';
            $ret['list'][$k]['create_uid'] = get_user_name($v['create_uid']);
            $ret['list'][$k]['assign_time'] = $v['assign_time'] ? date('Y-m-d',strtotime($v['assign_time'])) : '-';
            $ret['list'][$k]['trial_time'] = $this->convert_trial_time($v['trial_time']);
        }

        if($this->pagenation){
            return $ret;
        }

        if (!empty($ret['list'])) {
            return collection($ret['list'])->toArray();
        }


        return [];
    }
}