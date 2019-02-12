<?php
namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;
use think\Hook;
use think\Db;

class FtReview extends Base
{
    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    protected $type = [
        'content' => 'json',
    ];

    public function ftReviewStudent()
    {
        return $this->hasMany('FtReviewStudent', 'frvw_id', 'frvw_id');
    }

    public function oneClass()
    {
        return $this->belongsTo('Classes', 'cid', 'cid');
    }

    public function ftReviewFile()
    {
        return $this->hasMany('FtReviewFile','frvw_id','frvw_id');
    }

    public function classAttendance()
    {
        return $this->hasOne('ClassAttendance','catt_id','catt_id');
    }

    /**
     * 创建点评
     * @param $review_data
     * @param array $review_file
     * @param array $review_student
     * @return bool
     */
    public function addOneReview($review_data, $review_file = [], &$review_student = [])
    {
        $lesson = (new Lesson())->where('lid', $review_data['lid'])->field('lesson_type')->find();
        $review_data['lesson_type'] = $lesson ? $lesson['lesson_type'] : 0;

        if(isset($review_data['catt_id']) && $review_data['catt_id'] > 0) {
            $attendance = (new ClassAttendance())->field('ca_id')->find($review_data['catt_id']);
            if($attendance) {
                $review_data['ca_id'] = $attendance['ca_id'];
            }
        }

        $this->startTrans();
        try {
            //--1-- 添加总点评
            $rs = $this->data([])->isUpdate(false)->allowField(true)->save($review_data);
            if ($rs === false) return $this->user_error('添加点评失败');

            $frvw_id = $this->getAttr('frvw_id');

            //--2-- 添加点评相关文件
            if (!empty($review_file)) {
                $m_file = new File();
                $mFrf = new FtReviewFile();
                foreach ($review_file as $per_file) {
                    if(empty($per_file['file_id'])) {
                        log_write($per_file, 'error');
                        continue;
                    }
                    $file = $m_file->find($per_file['file_id']);
                    $file = $file ? $file->toArray() : [];
                    $per_file = array_merge($per_file, $file);
                    $per_file['frvw_id'] = $frvw_id;
                    $rs = $mFrf->data([])->isUpdate(false)->allowField(true)->save($per_file);
                    if ($rs === false) throw new FailResult($mFrf->getErrorMsg());
                }
            }

            //--3-- 个人点评
            if (!empty($review_student)) {
                $review_student_data = [
                    'frvw_id'         => $frvw_id,
                    'lesson_type'    => $review_data['lesson_type'],
                    'cid'            => $review_data['cid'],
                    'lid'            => $review_data['lid'],
                    'int_day'        => $review_data['int_day'],
                    'int_start_hour' => $review_data['int_start_hour'],
                    'int_end_hour'   => $review_data['int_end_hour'],
                    'eid'            => $review_data['eid'],
                ];

                foreach ($review_student as $k=>$per_review_student) {
                    $per_review_student = array_merge($per_review_student, $review_student_data);
                    $m_frs = new FtReviewStudent();
                    $rs = $m_frs->data([])->isUpdate(false)->allowField(true)->save($per_review_student);
                    $review_student[$k]['frs_id'] = $m_frs->frs_id;
                    if ($rs === false) return $this->user_error('点评学生失败');
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
        } catch (Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    /**
     * 删除点评
     * @param $frvw_id
     * @param null $ft_review
     * @return bool
     */
    public function delOneReview($frvw_id, $ft_review = null)
    {
        if(is_null($ft_review)) {
            $ft_review = $this->findOrFail($frvw_id);
        }

        try {
            $this->startTrans();
            $rs = (new FtReviewStudent())->where('frvw_id', $frvw_id)->delete();
            if ($rs === false) throw new FailResult('删除学生个人点评失败');

            $rs = (new ReviewFile())->where('rvw_id', $frvw_id)->delete();
            if ($rs === false) throw new FailResult('删除点评文件记录失败');

            $rs = $ft_review->delete();
            if ($rs === false) throw new FailResult('删除点评失败');

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

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
    public function updateFtReview($frvw_id,$ft_review_data, $ft_review_file = [], &$ft_review_student = [])
    {
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
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }


    /**
     * 更改课评状态
     * @param $frvw_id
     * @param $trans_eid
     * @param $sent_status
     * @return bool
     */
    public function updateSentStatus($frvw_id,$trans_eid,$sent_status)
    {
    	$m_frvw = $this->find($frvw_id);
	
        if(!$m_frvw){
            return $this->input_param_error('id');
        }

        $status_map = [0,1,2];
        if(!in_array($sent_status,$status_map)){
            return $this->input_param_error('status');
        }

        $employee = get_employee_info($trans_eid);
        if (!$employee){
            return $this->input_param_error('trans_eid');
        }

        $m_frvw->sent_status = $sent_status;
        $m_frvw->trans_eid = $trans_eid;

        $result = $m_frvw->save();
        if(false === $result){
            return $this->sql_save_error('ft_review');
        }

        return true;
    }

    /**
     * 修改外教rvw_id
     * @param $frvw_id
     * @param $rvw_id
     * @return bool
     */
    public function updateReview($frvw_id,$rvw_id)
    {
        $m_frvw = $this->find($frvw_id);

        if(!$m_frvw){
            return $this->input_param_error('id');
        }

        $m_frvw->rvw_id = $rvw_id;
        $result = $m_frvw->save();
        if(false === $result){
            return $this->sql_save_error('ft_review');
        }

        return true;
    }


    /**
     * 获取指定日期条件的翻译情况
     * @param int $int_day
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getReportStats($int_day = 0){
        $statistical = [];

        $mFtEmployee = new FtEmployee();
        $ft_employee = $mFtEmployee->field('eid')->select();
        $employees = [];
        foreach ($ft_employee as $employee){
            array_push($employees,$employee['eid']);
        }
        $mClassAttendance = new ClassAttendance();
        $today_attendance = $mClassAttendance->where('eid','in',$employees)->where('int_day', int_day(time()))->count();

        if($int_day == 0) {
            $int_day = int_day(time());
        }

        $sql_a = 'select count(*) as num from x360p_class_attendance catt left join x360p_ft_review fr on catt.catt_id = fr.catt_id  where fr.int_day = '.$int_day;
        $sql_b = 'select count(*) as num from x360p_class_attendance as catt left join x360p_ft_review as fr on catt.catt_id = fr.catt_id  where fr.sent_status = 2 and fr.int_day = '.$int_day;
        $sql_c = 'select count(*) as num from x360p_class_attendance as catt left join x360p_ft_review as fr on catt.catt_id = fr.catt_id  where fr.catt_id is null and fr.int_day = '.$int_day;
        $sql_d = 'select count(*) as num from x360p_class_attendance as catt left join x360p_ft_review as fr on catt.catt_id = fr.catt_id  where fr.sent_status < 2 and fr.int_day = '.$int_day;

        $statistical['today_attendance'] =  $today_attendance;
        $statistical['has_written'] = DB::query($sql_a)[0]['num'];
        $statistical['not_written'] =  DB::query($sql_c)[0]['num'];
        $statistical['has_translate'] =  DB::query($sql_b)[0]['num'];
        $statistical['not_translate'] =  DB::query($sql_d)[0]['num'];

        return $statistical;
    }
    
}