<?php
/**
 * Author: luo
 * Time: 2018/4/10 10:32
 */

namespace app\api\controller;


use app\api\model\Student;
use app\api\model\StudentExam;
use app\api\model\StudentExamScore;
use app\api\model\StudentExamSubjectScore;
use app\common\db\Query;
use think\Request;
use util\excel;

class StudentExamScores extends Base
{

    public $withoutAuthAction = ['download_excel_template'];

    public function get_list(Request $request)
    {
        $get = $request->get();
        /** @var Query $m_ses */
        $m_ses = new StudentExamScore();
        $ret = $m_ses->getSearchResult($get);
        return $this->sendSuccess($ret);
    }

    //根据考试分类成绩
    public function query_exam(Request $request)
    {
        $get = $request->get();
        /** @var Query $m_se */
        $m_se = new StudentExam();
        /** @var Query $m_ses */
        $m_ses = new StudentExamScore();

        if(!empty($get['exam_subject_dids'])) {
            $subject_where = [];
            $subject_dids  = is_string($get['exam_subject_dids']) ? explode(',', $get['exam_subject_dids'])
                : $get['exam_subject_dids'];
            foreach($subject_dids as $did) {
                $subject_where[] = "find_in_set($did, exam_subject_dids)";
            }
            $subject_where = implode(' or ', $subject_where);
            $m_se->where($subject_where);
            unset($get['exam_subject_dids']);
        }

        $where = [];
        if(isset($get['cid'])) {
            $where['ses.cid'] = $get['cid'];
            unset($get['cid']);
        }

        if(isset($get['cu_id'])) {
            $where['ses.cid'] = $get['cu_id'];
            unset($get['cu_id']);
        }

        if(isset($get['sid'])) {
            $where['ses.sid'] = $get['sid'];
            unset($get['ses.sid']);
        }

        $ret = $m_se->alias('se')->join('student_exam_score ses', 'se.se_id = ses.se_id', 'left')
            ->order('se.se_id desc')->where($where)->distinct('se.se_id')->field('se.se_id')->getSearchResult($get);

        foreach($ret['list'] as &$item) {
            $exam = StudentExam::get($item['se_id']);
            $item = array_merge($item, $exam->toArray());
            $item['class_num'] = $m_ses->where('se_id', $item['se_id'])->group('cid')->count();
            $item['student_num'] = $m_ses->where('se_id', $item['se_id'])->group('sid')->count();
            $item['max_score'] = $m_ses->where('se_id', $item['se_id'])->max('total_score');
            $item['min_score'] = $m_ses->where('se_id', $item['se_id'])->min('total_score');
            $item['avg_score'] = $m_ses->where('se_id', $item['se_id'])->avg('total_score');
        }

        return $this->sendSuccess($ret);
    }

    //根据学员分类成绩
    public function query_student(Request $request)
    {
        $get = $request->get();
        /** @var Query $m_se */
        $m_se = new StudentExam();

        if(!empty($get['exam_subject_dids'])) {
            $subject_where = [];
            $subject_dids  = is_string($get['exam_subject_dids']) ? explode(',', $get['exam_subject_dids'])
                : $get['exam_subject_dids'];
            foreach($subject_dids as $did) {
                $subject_where[] = "find_in_set($did, exam_subject_dids)";
            }
            $subject_where = implode(' or ', $subject_where);
            $m_se->where($subject_where);
            unset($get['exam_subject_dids']);
        }

        $where = [];
        if(isset($get['cid'])) {
            $where['ses.cid'] = $get['cid'];
            unset($get['cid']);
        }

        if(isset($get['cu_id'])) {
            $where['ses.cid'] = $get['cu_id'];
            unset($get['cu_id']);
        }

        if(isset($get['sid'])) {
            $where['ses.sid'] = $get['sid'];
            unset($get['ses.sid']);
        }

        $ret = $m_se->alias('se')->join('student_exam_score ses', 'se.se_id = ses.se_id', 'left')
            ->order('se.se_id desc')->where($where)->where('ses.is_delete = 0')->distinct('ses.cid')
            ->field('ses.ses_id,se.se_id')->getSearchResult($get);

        $m_sess = new StudentExamSubjectScore();
        $m_class = new \app\api\model\Classes();
        foreach($ret['list'] as &$item) {
            $score = StudentExamScore::get($item['ses_id']);
            $exam = StudentExam::get($score['se_id']);
            $item = array_merge($item, $exam->toArray(), $score->toArray());
            $subject_score = $m_sess->where('ses_id', $item['ses_id'])->select();
            $item['student_exam_subject_score'] = $subject_score;
            $class = $m_class->where('cid', $score['cid'])->field('cid,class_name')->find();
            $item['one_class'] = $class;
        }

        return $this->sendSuccess($ret);
    }

    //某个考试，班级分类成绩
    public function query_class(Request $request)
    {
        $get = $request->get();
        if(empty($get['se_id'])) return $this->sendError(400, '缺少考试id');
        $se_id = $get['se_id'];
        unset($get['se_id']);

        /** @var Query $m_se */
        $m_se = new StudentExam();
        $m_se->where('ses.se_id', $se_id);

        if(!empty($get['exam_subject_dids'])) {
            $subject_where = [];
            $subject_dids  = is_string($get['exam_subject_dids']) ? explode(',', $get['exam_subject_dids'])
                : $get['exam_subject_dids'];
            foreach($subject_dids as $did) {
                $subject_where[] = "find_in_set($did, exam_subject_dids)";
            }
            $subject_where = implode(' or ', $subject_where);
            $m_se->where($subject_where);
            unset($get['exam_subject_dids']);
        }

        $where = [];
        if(isset($get['cid'])) {
            $where['ses.cid'] = $get['cid'];
            unset($get['cid']);
        }

        if(isset($get['cu_id'])) {
            $where['ses.cid'] = $get['cu_id'];
            unset($get['cu_id']);
        }

        if(isset($get['sid'])) {
            $where['ses.sid'] = $get['sid'];
            unset($get['ses.sid']);
        }

        $ret = $m_se->alias('se')->join('student_exam_score ses', 'se.se_id = ses.se_id', 'left')
            ->order('se.se_id desc')->where($where)->distinct('ses.cid')->field('se.se_id,ses.cid')->getSearchResult($get);

        $m_class = new \app\api\model\Classes();
        $m_ses = new StudentExamScore();
        foreach($ret['list'] as &$item) {
            $class = $m_class->where('cid', $item['cid'])->field('cid,class_name')->find();
            $item['one_class'] = $class;
            $item['student_num'] = $m_ses->where('se_id', $se_id)->where('cid', $item['cid'])->group('sid')->count();
            $item['max_score'] = $m_ses->where('se_id', $se_id)->where('cid', $item['cid'])->max('total_score');
            $item['min_score'] = $m_ses->where('se_id', $se_id)->where('cid', $item['cid'])->min('total_score');
            $item['avg_score'] = $m_ses->where('se_id', $se_id)->where('cid', $item['cid'])->avg('total_score');
        }

        return $this->sendSuccess($ret);
    }

    //某个考试，某个班级下的学员成绩
    public function query_class_student(Request $request)
    {
        $get = $request->get();
        if(!isset($get['se_id']) || !isset($get['cid'])) return $this->sendError(400, '缺少考试id或者班级id');
        $se_id = $get['se_id'];
        unset($get['se_id']);
        $cid = $get['cid'];
        unset($get['cid']);

        /** @var Query $m_ses */
        $m_ses = new StudentExamScore();
        $m_ses->where('se_id', $se_id)->where('cid', $cid);
        $ret = $m_ses->with('studentExamSubjectScore')->getSearchResult($get);

        $m_class = new \app\api\model\Classes();
        $m_student = new Student();
        foreach($ret['list'] as &$item) {
            $class = $m_class->where('cid', $item['cid'])->field('cid,class_name')->find();
            $item['one_class'] = $class;
            $item['student'] = $m_student->where('sid', $item['sid'])->field('sid,student_name,first_tel,photo_url,sno')->find();
        }

        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $post = $request->post();
        $m_ses = new StudentExamScore();

        $rs = $m_ses->addScores($post);
        if($rs === false) return $this->sendError(400, $m_ses->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $put = $request->put();

        $ses_id = input('id');
        $score = StudentExamScore::get($ses_id);
        if(empty($score)) return $this->sendError(400, '成绩不存在');
        
        $rs = $score->editScore($put);
        if($rs === false) return $this->sendError(400, $score->getErrorMsg());

        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $ses_id = input('id');
        $score = StudentExamScore::get($ses_id);
        if(empty($score)) return $this->sendError(400, '成绩不存在');

        $rs = $score->delScore();
        if($rs === false) return $this->sendError(400, $score->getErrorMsg());
        
        return $this->sendSuccess();
    }

    /**
     * @desc  删除一个科目成绩
     * @author luo
     * @param Request $request
     * @method DELETE
     */
    public function delete_subject_score(Request $request)
    {
        $ses_id = input('id');
        $sess_id = input('subid');

        $m_ses = new StudentExamScore();
        $rs = $m_ses->delSubjectScore($ses_id, $sess_id);
        if($rs === false) return $this->sendError(400, $m_ses->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function download_excel_template(Request $request)
    {
        $se_id = input('se_id', 0);
        if($se_id <= 0) return $this->sendError(400, 'se_id error');

        $m_se = new StudentExam();
        $exam = $m_se->find($se_id);
        if(empty($exam['exam_subject_dids'])) return $this->sendError(400, '考试没有相关科目');

        $exam['exam_subject_dids'] = is_string($exam['exam_subject_dids']) ?
            explode(',', $exam['exam_subject_dids']) : $exam['exam_subject_dids'];
        $subjects = (new \app\api\model\Dictionary())->where('did', 'in', $exam['exam_subject_dids'])->select();


        $excel = new excel();
        $export_params['title']     = '学员成绩';
        $export_params['columns']   = [
            ['field'=>'student_name','title'=>'学生姓名','width'=>20, 'color' => 'FFFF0000'],
        ];
        foreach($subjects as $row) {
            $export_params['columns'][] = [
                'field' => $row['did'],
                'title' => $row['title'],
                'width' => 20,
            ];
        }
        $export_params['columns'][] = ['field' => 'remark', 'title' => '备注', 'width' => 50];
        $data = [];

        $excel->export($data,$export_params);
    }


}