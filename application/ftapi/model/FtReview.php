<?php
namespace app\ftapi\model;

use app\common\exception\FailResult;
use app\common\Wechat;
use think\Exception;
use think\Hook;
use think\Log;

class FtReview extends Base
{
    protected $append = ['course_name'];

    protected $type = [
        'content' => 'json',
    ];

    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function ftReviewStudent()
    {
        return $this->hasMany('FtReviewStudent', 'frvw_id', 'frvw_id');
    }

    public function ftReviewFile()
    {
        return $this->hasMany('FtReviewFile','frvw_id','frvw_id');
    }

    public function getCourseNameAttr($value,$data){
        $course_name = get_course_name_by_row($data);

        return $course_name;
    }


    /**
     * 创建点评
     * @param $ft_review_data
     * @param array $ft_review_file
     * @param array $ft_review_student
     * @return bool
     */
    public function addFtReview($ft_review_data, $ft_review_file = [], &$ft_review_student = [])
    {
        $lesson = (new Lesson())->where('lid', $ft_review_data['lid'])->field('lesson_type')->find();
        $ft_review_data['lesson_type'] = $lesson ? $lesson['lesson_type'] : 0;

        if(isset($ft_review_data['catt_id']) && $ft_review_data['catt_id'] > 0) {
            $attendance = (new ClassAttendance())->field('ca_id')->find($ft_review_data['catt_id']);
            if($attendance) {
                $ft_review_data['ca_id'] = $attendance['ca_id'];
            }
        }

        $this->startTrans();
        try {
            //--1-- 添加总点评
            $rs = $this->data([])->isUpdate(false)->allowField(true)->save($ft_review_data);
            if ($rs === false) return $this->user_error('Failed to add review');

            $frvw_id = $this->getAttr('frvw_id');

            //--2-- 添加点评相关文件
            if (!empty($ft_review_file)) {
                $m_file = new File();
                $m_rf = new FtReviewFile();
                foreach ($ft_review_file as $per_file) {
                    if(empty($per_file['file_id'])) {
                        log_write($per_file, 'error');
                        continue;
                    }
                    $file = $m_file->find($per_file['file_id']);
                    $file = $file ? $file->toArray() : [];
                    $per_file = array_merge($per_file, $file);
                    $per_file['frvw_id'] = $frvw_id;
                    $rs = $m_rf->data([])->isUpdate(false)->allowField(true)->save($per_file);
                    if ($rs === false) throw new FailResult($m_rf->getErrorMsg());
                }
            }

            //--3-- 个人点评
            if (!empty($ft_review_student)) {
                $ft_review_student_data = [
                    'frvw_id'         => $frvw_id,
                    'lesson_type'    => $ft_review_data['lesson_type'],
                    'cid'            => $ft_review_data['cid'],
                    'lid'            => $ft_review_data['lid'],
                    'int_day'        => $ft_review_data['int_day'],
                    'int_start_hour' => $ft_review_data['int_start_hour'],
                    'int_end_hour'   => $ft_review_data['int_end_hour'],
                    'eid'            => $ft_review_data['eid'],
                ];

                foreach ($ft_review_student as $k=>$per_review_student) {
                    $per_review_student = array_merge($per_review_student, $ft_review_student_data);
                    $m_frs = new FtReviewStudent();
                    $rs = $m_frs->data([])->isUpdate(false)->allowField(true)->save($per_review_student);
                    $ft_review_student[$k]['frs_id'] = $m_frs->frs_id;
                    if ($rs === false) return $this->user_error('review on student failure');
                    if(isset($per_review_student['sid'])) {
                        add_service_record('ft_review', ['sid' => $per_review_student['sid'], 'st_did' => 232]);

                        if(isset($per_review_student['score'])) {
                            //点评积分
                            $hook_data = [
                                'hook_action' => 'ft_review',
                                'sid' => $per_review_student['sid'],
                                'star' => $per_review_student['score'],
                            ];
                            Hook::listen('handle_credit', $hook_data);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        $this->commit();
        return true;
    }

    /**
     * 修改点评
     * @param $frvw_id
     * @param $ft_review_data
     * @param array $ft_review_file
     * @param array $ft_review_student
     * @return bool
     */
    public function updateFtReview($frvw_id,$ft_review_data, $ft_review_file = [], &$ft_review_student = []){
        $this->startTrans();
        try {
            $update_ft_review['frvw_id'] = $frvw_id;
            //--1-- 修改总点评
            $rs = $this->allowField(true)->save($ft_review_data,$update_ft_review);
            if ($rs === false) return $this->user_error('Failed to change review');

            //--2-- 修改点评相关文件
            if (!empty($ft_review_file)) {
                $m_file = new File();
                $m_rf = new FtReviewFile();

                $rs = $m_rf->where(['frvw_id' => $frvw_id])->delete();
                if ($rs === false) return $this->user_error('old file deletion failed');

                foreach ($ft_review_file as $per_file) {
                    if(empty($per_file['file_id'])) {
                        log_write($per_file, 'error');
                        continue;
                    }
                    $file = $m_file->find($per_file['file_id']);
                    $file = $file ? $file->toArray() : [];


                    $per_file = array_merge($per_file, $file);
                    $per_file['frvw_id'] = $frvw_id;
                    $rs = $m_rf->data([])->isUpdate(false)->allowField(true)->save($per_file);
                    if ($rs === false) throw new FailResult($m_rf->getErrorMsg());
                }
            }

            //--3-- 个人点评
            if (!empty($ft_review_student)) {
                $m_frs = new FtReviewStudent();

                $rs = $m_frs->where(['frvw_id' => $frvw_id])->delete();
                if ($rs === false) return $this->user_error('old student review deletion failed');

                $ft_review_student_data = [
                    'frvw_id'         => $frvw_id,
                    'lesson_type'    => $ft_review_data['lesson_type'],
                    'cid'            => $ft_review_data['cid'],
                    'lid'            => $ft_review_data['lid'],
                    'int_day'        => $ft_review_data['int_day'],
                    'int_start_hour' => $ft_review_data['int_start_hour'],
                    'int_end_hour'   => $ft_review_data['int_end_hour'],
                    'eid'            => $ft_review_data['eid'],
                ];

                foreach ($ft_review_student as $k=>$per_review_student) {
                    $per_review_student = array_merge($per_review_student, $ft_review_student_data);
                    $rs = $m_frs->data([])->isUpdate(false)->allowField(true)->save($per_review_student);
                    if ($rs === false) return $this->user_error('Failed to change student review');
                    if(isset($per_review_student['sid'])) {
                        add_service_record('ft_review', ['sid' => $per_review_student['sid'], 'st_did' => 232]);

                        if(isset($per_review_student['score'])) {
                            //点评积分
                            $hook_data = [
                                'hook_action' => 'ft_review',
                                'sid' => $per_review_student['sid'],
                                'star' => $per_review_student['score'],
                            ];
                            Hook::listen('handle_credit', $hook_data);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        $this->commit();
        return true;
    }


    //删除一个点评
    public function delOneReview($rvw_id, $review = null)
    {
        if(is_null($review)) {
            $review = $this->findOrFail($rvw_id);
        }

        try {
            $this->startTrans();
            $rs = (new FtReviewStudent())->where('rvw_id', $rvw_id)->delete();
            if ($rs === false) throw new FailResult('删除学生个人点评失败');

            //$rs = ReviewFile::destroy(['rvw_id', $rvw_id]);
            $rs = (new ReviewFile())->where('rvw_id', $rvw_id)->delete();
            if ($rs === false) throw new FailResult('删除点评文件记录失败');

            $rs = $review->delete();
            if ($rs === false) throw new FailResult('删除点评失败');

            $this->commit();
        } catch (FailResult $e) {
            $this->rollback();
            return $this->user_error($e->getMessage());
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }


}