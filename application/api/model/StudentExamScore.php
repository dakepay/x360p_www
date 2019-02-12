<?php
/**
 * Author: luo
 * Time: 2018/4/10 10:27
 */

namespace app\api\model;


use app\common\exception\FailResult;
use think\Exception;

class StudentExamScore extends Base
{

    public function studentExamSubjectScore()
    {
        return $this->hasMany('StudentExamSubjectScore', 'ses_id', 'ses_id');
    }

    public function oneClass()
    {
        return $this->hasOne('Classes', 'cid', 'cid')->field('cid,class_name');
    }

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,student_name,photo_url,sex');
    }

    public function studentExam()
    {
        return $this->hasOne('StudentExam', 'se_id', 'se_id');
    }

    //添加多个学生的成绩
    public function addScores($post)
    {
        try {
            $this->startTrans();
            foreach ($post as $row) {
                if (empty($row['score_info'])) throw new FailResult('成绩信息不能为空');
                $rs = $this->addOneScore($row);
                if($rs === false) throw new FailResult($this->getErrorMsg());
            }

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    public function addOneScore($data)
    {
        $total_score = 0;
        foreach ($data['score_info'] as $info) {
            if(empty($info['exam_subject_did']) || empty_except_zero($info['score'])) throw new FailResult('分数或者科目错误');
           $total_score += $info['score'];
        }
        $data['total_score'] = $total_score;

        try {
            $this->startTrans();
            $cu_id = isset($data['cu_id']) ? $data['cu_id'] : 0;
            $sid = isset($data['sid']) ? $data['sid'] : 0;
            $old_score = $this->where(['sid' => $sid, 'cu_id' => $cu_id, 'se_id' => $data['se_id']])->find();
            if(!empty($old_score)) {
                $rs = $old_score->allowField('total_score,cid,remark')->save($data);
                if($rs === false) return $this->user_error($old_score->getError());
            } else {
                $rs = $this->validate()->data([])->allowField(true)->isUpdate(false)->save($data);
                if ($rs === false) return false;
            }

            $ses_id = $old_score ? $old_score->ses_id : $this->ses_id;

            $m_sess = new StudentExamSubjectScore();
            foreach ($data['score_info'] as $info) {
                $info['ses_id'] = $ses_id;
                $info['sid'] = $sid;
                $rs = $m_sess->addSubjectScore($info);
                if ($rs === false) throw new FailResult($m_sess->getErrorMsg());
            }

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    public function editScore($data)
    {
        if(empty($this->getData())) return $this->user_error('分数成绩数据为空');

        $m_sess = new StudentExamSubjectScore();

        try {
            $this->startTrans();
            foreach ($data['score_info'] as $info) {
                if (empty($info['exam_subject_did']) || empty($info['score'])) throw new FailResult('分数或者科目错误');
                $info['ses_id'] = $this->ses_id;
                $info['sid'] = $this->sid;
                $rs = $m_sess->addSubjectScore($info);
                if ($rs === false) throw new FailResult($m_sess->getErrorMsg());
            }

            $data['total_score'] = $m_sess->where('ses_id', $this->ses_id)->sum('score');
            $rs = $this->allowField('total_score,remark')->isUpdate(true)->save($data);
            if ($rs === false) throw new FailResult($this->getErrorMsg());

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    public function delScore()
    {
        if(empty($this->getData())) return $this->user_error('分数数据为空');

        try {
            $this->startTrans();

            StudentExamSubjectScore::destroy(['ses_id' => $this->ses_id], true);
            $this->delete(true);

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    public function delSubjectScore($ses_id, $sess_id)
    {
        try {
            $this->startTrans();
            $m_sess = new StudentExamSubjectScore();
            $m_sess->where('sess_id', $sess_id)->delete(true);

            $total_score = $m_sess->where('ses_id', $ses_id)->sum('score');
            $this->where('ses_id', $ses_id)->update(['total_score' => $total_score]);
            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }


}