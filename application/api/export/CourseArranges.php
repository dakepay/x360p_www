<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/15
 * Time: 18:43
 */
namespace app\api\export;

use app\common\Export;
use app\api\model\CourseArrange;
use app\api\model\Classes as ClassesModel;

class CourseArranges extends Export
{
    protected $res_name = 'course_arrange';

    protected $columns = [
        ['field'=>'bid','title'=>'校区','width'=>20],
        ['field'=>'int_day','title'=>'日期','width'=>20],
        ['field'=>'section','title'=>'时段','width'=>20],
        ['field'=>'int_start_hour','title'=>'开始时间','width'=>20],
        ['field'=>'int_end_hour','title'=>'结束时间','width'=>20],
        ['field'=>'consume_lesson_hour','title'=>'扣课时数','width'=>20],
        ['field'=>'teach_eid','title'=>'老师','width'=>20],
        ['field'=>'second_eid','title'=>'助教','width'=>20],
        ['field'=>'obj','title'=>'授课对象','width'=>30],
        ['field'=>'cr_id','title'=>'教室','width'=>20],
        ['field'=>'is_attendance','title'=>'考勤','width'=>20],
    ];

    protected function get_title(){
        $title = '排课列表';
        return $title;
    }

    protected function convert_attendance($value)
    {
        $map = ['未考勤', '部分考勤', '已考勤'];
        if (key_exists($value, $map)) {
            return $map[$value];
        }
        return '';
    }

    protected function get_obj($is_trial,$lesson_type,$name,$cid,$ca_id){
        if($is_trial==1){
            return '试 '.$name;
        }else{
            if($lesson_type==0){
                return '班'.' '.get_class_name($cid);
            }else if($lesson_type==1){
                $sid = m('course_arrange_student')->where('ca_id',$ca_id)->find();
                $name = m('student')->where('sid',$sid['sid'])->find();
                return '一 '.$name->student_name;
            }else if($lesson_type==2){
                $sids = m('course_arrange_student')->where('ca_id',$ca_id)->column('sid');
                $names = m('student')->where('sid','in',$sids)->column('student_name');
                $students = collection($names)->toArray();
                return '多 '.implode(' ',$students);
            }
        }
    }

    protected function convert_hour($start,$end,$day){
        return '星期'.int_day_to_week($day).' '.int_hour_to_hour_str($start).'-'.int_hour_to_hour_str($end);
    }

    public function get_data()
    {
        $input = $this->params;
        $with = $input['with'] ?? [];
        $with = is_string($with) ? explode(',', $with) : $with;
        if(($key = array_search('students', $with)) !== false) {
            $with_students = true;
            unset($with[$key]);
            $input['with'] = implode(',', $with);
        }
        if(isset($input['teach_eid']) ){
            $login_user = gvar('user');
            $login_employee = $login_user['employee'];
            $login_eid = $login_employee['eid'];
            $rids = $login_employee['rids'];
            if($login_eid == $input['teach_eid'] && !in_array(1,$rids)){
                $m_classes = new ClassesModel();
                $my_cls_list = $m_classes->where('edu_eid',$login_eid)->whereOr('second_eid',$login_eid)->where('status','LT',2)->select();

                if($my_cls_list){
                    $cids = [];
                    foreach($my_cls_list as $c){
                        array_push($cids,$c['cid']);
                    }
                    $input['cid'] = '[IN,'.implode(',',$cids).']';
                    unset($input['teach_eid']);
                }
            }
        }
        /** @var Query $model */
        $model = new CourseArrange();
        if(isset($input['sid'])) {
            //$cids = (new ClassStudent())->where('sid', $input['sid'])->column('cid');
            //$cids = array_unique($cids);
            //$where = !empty($cids) ? sprintf('ca.cid in (%s) or ', implode(',', $cids)) : '';
            $model->refreshStudentArrange($input['sid']);
            $where = '';
            $where .= 'cas.sid = ' . $input['sid'];
            $where .= ' and cas.delete_time is NULL';
            $m_ca = new CourseArrange();
            $fields = $m_ca->getTableFields();
            $input_where = [];
            if(!empty($input)) {
                foreach($input as $key => $val) {
                    if(in_array($key, $fields) && $key != 'sid') {
                        $input['ca.'.$key] = $val;
                        unset($input[$key]);
                    }
                }
            }
            $sort = input('order_sort', 'asc');
            $ret = $model->alias('ca')->join('course_arrange_student cas', 'ca.ca_id = cas.ca_id', 'left')
                ->where($where)->where($input_where)->field('ca.*')->order('ca.int_day',$sort)
                ->getSearchResult($input,[],false);
        } else {
            $ret = $model->getSearchResult($input,[],false);
        }
        foreach($ret['list'] as &$course) {
            //是否返回排课的班级学员信息
            if(!empty($with_students) && !empty($course)) {
                $course['students'] = $model->getAttObjects($course['ca_id'],false,false);
            }
            if($course['is_attendance'] > 0){
                $w_catt['ca_id'] = $course['ca_id'];
                $catt_info = get_catt_info($w_catt);
                $course['consume_lesson_hour'] = $catt_info['consume_lesson_hour'];
            }
            $course['reason'] = empty($course['reason']) || is_null($course['reason']) ? '' : $course['reason'];
        }

        $list = collection($ret['list'])->toArray();

        // $model = new CourseArrange();
        // $result = $model->getSearchResult($this->params,[],false);
        // $list = collection($result['list'])->toArray();

        foreach ($list as $k => $v) {
            $list[$k]['bid'] = get_branch_name($v['bid']);
            $list[$k]['int_day'] = int_day_to_date_str($v['int_day']);
            $list[$k]['section'] = $this->convert_hour($v['int_start_hour'],$v['int_end_hour'],$v['int_day']);
            $list[$k]['int_start_hour'] = int_hour_to_hour_str($v['int_start_hour']);
            $list[$k]['int_end_hour'] = int_hour_to_hour_str($v['int_end_hour']);
            $list[$k]['teach_eid']       = get_teacher_name($v['teach_eid']);
            $list[$k]['second_eid']   = get_teacher_name($v['second_eid']);
            $list[$k]['cr_id']   = get_class_room($v['cr_id']);
            $list[$k]['is_attendance'] = $this->convert_attendance($v['is_attendance']);
            $list[$k]['obj'] = $this->get_obj($v['is_trial'],$v['lesson_type'],$v['name'],$v['cid'],$v['ca_id']);
        }

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];

    }
}