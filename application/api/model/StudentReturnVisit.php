<?php
/**
 * Author: luo
 * Time: 2018-01-10 17:53
**/

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;

class StudentReturnVisit extends Base
{

    protected $hidden = [
        'update_time', 
        'is_delete', 
        'delete_time', 
        'delete_uid'
    ];

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid');
    }

    public function studentReturnVisitAttachment()
    {
        return $this->hasMany('StudentReturnVisitAttachment', 'srv_id', 'srv_id');
    }

    //获取配置的条件次数，请假次数、缺勤次数
    public static function config_condition_times($name)
    {
        $config = Config::get_config('params');
        $times = 0;
        if(!empty($config)) {
            $times = isset($config['cfg_value']['return_visit'][$name]) ? $config['cfg_value']['return_visit'][$name] : $times;
        }
        return $times;
    }

    //添加一条回访
    public function addOneVisit($data)
    {
        try {
            $this->startTrans();
            if (!isset($data['sid'])) return $this->user_error('param error');
            $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
            if ($rs === false) return $this->user_error('添加失败');

            $srv_id = $this->getAttr('srv_id');

            if (isset($data['token'])){
                $mWbl = new WebcallCallLog();
                $rs = $mWbl->addRelateCmtId($data['token'],$srv_id);
                if (!$rs) throw new FailResult('token error');
            }

            add_service_record('return_visit', ['sid' => $data['sid'], 'st_did' => 235]);

            $last_attendance_time = isset($data['int_day']) ? strtotime($data['int_day']) : time();
            $rs = (new Student())->updateLastAttendanceTime($data['sid'], $last_attendance_time);
            if($rs === false) return $this->user_error('更新最后联络时间失败');

            $attachment_data = isset($data['student_return_visit_attachment']) ? $data['student_return_visit_attachment'] : [];
            if (!empty($attachment_data) && is_array($attachment_data)) {
                $m_srva = new StudentReturnVisitAttachment();
                $m_file = new File();
                foreach ($attachment_data as $row_data) {
                    if(!isset($row_data['file_id'])) continue;
                    $file = $m_file->find($row_data['file_id'])->toArray();
                    if(empty($file)) continue;
                    $file['srv_id'] = $this->srv_id;
                    $rs = $m_srva->data([])->allowField(true)->isUpdate(false)->save($file);
                    if ($rs === false) throw new FailResult('添加回访附件失败');
                }
            }

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    //删除一条学员回访
    public function delOne()
    {
        if(empty($this->getData())) return $this->user_error('学员回访数据错误');

        $rs = $this->studentReturnVisitAttachment()->delete();
        if($rs === false) return false;

        $rs = $this->delete();
        if($rs === false) return false;

        return true;
    }

}