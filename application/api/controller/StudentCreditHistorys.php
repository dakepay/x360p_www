<?php
/**
 * Author: luo
 * Time: 2018/1/9 18:27
 */

namespace app\api\controller;


use app\api\model\CreditRule;
use app\api\model\Student;
use app\api\model\StudentCreditHistory;
use think\Request;

class StudentCreditHistorys extends Base
{

    /**
     * @desc
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function get_list(Request $request)
    {
        $input = $request->get();

        $m_sch = new StudentCreditHistory();
        $ret = $m_sch->with('student')->getSearchResult($input);
        return $this->sendSuccess($ret);
    }

    /**
     * @desc  按学生的积分历史
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function student_list(Request $request)
    {
        $get = $where_student = $request->get();

        $m_student = new Student();
        if(isset($get['sid'])) {
            $m_student->where('sid', $get['sid']);
            unset($get['sid']);
        }

        if(isset($get['credit'])) {
            $m_student->autoWhere(['credit' => $get['credit']]);
            unset($get['credit']);
        }

        if(isset($get['credit2'])) {
            $m_student->autoWhere(['credit2' => $get['credit2']]);
            unset($get['credit2']);
        }

        unset($where_student['create_time']);
        $ret = $m_student->field('sid,student_name,credit,credit2')->getSearchResult($where_student);
        $m_sch = new StudentCreditHistory();

        $create_time = !empty($get['create_time']) ? ['create_time' => $get['create_time']] : '';
        foreach($ret['list'] as &$student) {
            //学习积分
            $student['total_study_inc'] = $m_sch->where('sid', $student['sid'])->where('type', StudentCreditHistory::TYPE_INC)
                ->where('cate', StudentCreditHistory::CATE_STUDY)->sum('credit');
            $student['total_study_dec'] = $m_sch->where('sid', $student['sid'])->where('type', StudentCreditHistory::TYPE_DEC)
                ->where('cate', StudentCreditHistory::CATE_STUDY)->sum('credit');

            if(!empty($create_time)) {
                $student['period_study_inc'] = $m_sch->where('sid', $student['sid'])->where('type', StudentCreditHistory::TYPE_INC)
                    ->where('cate', StudentCreditHistory::CATE_STUDY)->autoWhere($create_time)->sum('credit');
                $student['period_study_dec'] = $m_sch->where('sid', $student['sid'])->where('type', StudentCreditHistory::TYPE_DEC)
                    ->where('cate', StudentCreditHistory::CATE_STUDY)->autoWhere($create_time)->sum('credit');
            }

            //消费积分
            $student['total_consume_inc'] = $m_sch->where('sid', $student['sid'])->where('type', StudentCreditHistory::TYPE_INC)
                ->where('cate', StudentCreditHistory::CATE_CONSUME)->sum('credit');
            $student['total_consume_dec'] = $m_sch->where('sid', $student['sid'])->where('type', StudentCreditHistory::TYPE_DEC)
                ->where('cate', StudentCreditHistory::CATE_CONSUME)->sum('credit');

            if(!empty($create_time)) {
                $student['period_consume_inc'] = $m_sch->where('sid', $student['sid'])->where('type', StudentCreditHistory::TYPE_INC)
                    ->where('cate', StudentCreditHistory::CATE_CONSUME)->autoWhere($create_time)->sum('credit');
                $student['period_consume_dec'] = $m_sch->where('sid', $student['sid'])->where('type', StudentCreditHistory::TYPE_DEC)
                    ->where('cate', StudentCreditHistory::CATE_CONSUME)->autoWhere($create_time)->sum('credit');
            }
        }
        
        return $this->sendSuccess($ret);
    }

    /**
     * @desc  积分变动
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function post(Request $request)
    {
        $post = $request->post();
        $m_sch = new StudentCreditHistory();
        if(!empty($post[0]['sid'])) {
            $rs = $m_sch->addMultiCreditHistory($post);
        } else {
            $rs = $m_sch->addOneHistory($post);
        }
        if($rs === false) return $this->sendError(400, $m_sch->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  相关统计积分统计
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function report(Request $request)
    {
        $get = $request->get();
        if(empty($get['group'])) return $this->sendError(400, 'error group param');

        $group = ['type', 'cate', $get['group']];
        $field = [$get['group'], 'type', 'cate', 'sum(credit) as credit'];
        $m_sch = new StudentCreditHistory();
        if(isset($get['create_time'])) {
           $m_sch->autoWhere(['create_time' => $get['create_time']]);
        }
        $list = $m_sch->group(implode(',', $group))->order($get['group'], 'asc')->field($field)->select();
        $data = [];
        $total_add_study_credit = 0;
        $total_reduce_study_credit = 0;
        $total_add_consume_credit = 0;
        $total_reduce_consume_credit = 0;
        $m_cr = new CreditRule();
        foreach($list as $per_row) {
            $per_row['add_study_credit'] = $per_row['type'] == StudentCreditHistory::TYPE_INC && $per_row['cate'] == StudentCreditHistory::CATE_STUDY
                ? $per_row['credit'] : 0;
            $per_row['reduce_study_credit'] = $per_row['type'] == StudentCreditHistory::TYPE_DEC && $per_row['cate'] == StudentCreditHistory::CATE_STUDY
                ? $per_row['credit'] : 0;
            $per_row['add_consume_credit'] = $per_row['type'] == StudentCreditHistory::TYPE_INC && $per_row['cate'] == StudentCreditHistory::CATE_CONSUME
                ? $per_row['credit'] : 0;
            $per_row['reduce_consume_credit'] = $per_row['type'] == StudentCreditHistory::TYPE_DEC && $per_row['cate'] == StudentCreditHistory::CATE_CONSUME
                ? $per_row['credit'] : 0;
            if($get['group'] == 'cr_id') {
                $per_row['credit_rule'] = $m_cr->find($per_row['cr_id']);
            }
            unset($per_row['credit']);
            $key = $per_row[$get['group']];
            if(isset($data[$key])) {
                $data[$key]['add_study_credit'] += $per_row['add_study_credit'];
                $data[$key]['reduce_study_credit'] += $per_row['reduce_study_credit'];
                $data[$key]['add_consume_credit'] += $per_row['add_consume_credit'];
                $data[$key]['reduce_consume_credit'] += $per_row['reduce_consume_credit'];
            } else {
                $data[$key] = $per_row;
            }
            $total_add_study_credit += $per_row['add_study_credit'];
            $total_reduce_study_credit += $per_row['reduce_study_credit'];
            $total_add_consume_credit += $per_row['add_consume_credit'];
            $total_reduce_consume_credit += $per_row['reduce_consume_credit'];
        }

        $ret['list'] = array_values($data);
        $ret['total_add_study_credit'] = $total_add_study_credit;
        $ret['total_reduce_study_credit'] = $total_reduce_study_credit;
        $ret['total_add_consume_credit'] = $total_add_consume_credit;
        $ret['total_reduce_consume_credit'] = $total_reduce_consume_credit;
        return $this->sendSuccess($ret);
    }


}