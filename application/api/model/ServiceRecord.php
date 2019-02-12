<?php
/**
 * Author: luo
 * Time: 2018/5/22 16:50
 */

namespace app\api\model;


use app\common\exception\FailResult;
use think\Exception;

class ServiceRecord extends Base
{
    const OBJECT_TYPE_CUSTOMER = 0; # 客户
    const OBJECT_TYPE_STUDENT = 1; # 学生
    const OBJECT_TYPE_CLASS = 2; # 班级

    public function setIntDayAttr($value)
    {
        return $value ? format_int_day($value) : $value;
    }

    public function setIntHourAttr($value)
    {
        return $value ? format_int_hour($value) : $value;
    }

    public function serviceRecordFile()
    {
        return $this->hasMany('ServiceRecordFile', 'sr_id', 'sr_id');
    }

    /**
     * @desc  自动创建服务记录  ServiceRecord
     * @author luo
     * @param $data ['sid => 10, 'st_did' => 223, 'content' => '内容']
     * @return bool
     */
    public static function AutoAddServiceRecord($data)
    {
        $self = new self();

        $data['int_day'] = !empty($data['int_day']) ? $data['[int_day'] : date('Ymd', time());
        $data['int_hour'] = !empty($data['int_hour']) ? $data['int_hour'] : date('H', time());

        $rs = $self->addServiceRecord($data);
        if($rs === false) throw new FailResult($self->getErrorMsg());

        return true;
    }

    public function addServiceRecord($data)
    {
        try {
            $this->startTrans();

            if(!empty($data['sid'])) {
                $data['object_type'] = self::OBJECT_TYPE_STUDENT;
                // 添加一条服务 操作日志
                StudentLog::addServiceLog($data);
            } elseif(!empty($data['cid'])) {
                $data['object_type'] = self::OBJECT_TYPE_CLASS;
                // 添加一条班级 服务日志
                ClassLog::addClassServiceLog($data);
            } elseif(!empty($data['cu_id'])) {
                $data['object_type'] = self::OBJECT_TYPE_CUSTOMER;
            }

            $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
            if($rs === false) throw new FailResult($this->getErrorMsg());

            $sr_id = $this->sr_id;
            if(!empty($data['service_record_file']) && is_array($data['service_record_file'])) {
                $file_data = $data['service_record_file'];
                $m_srf = new ServiceRecordFile();
                $m_file = new File();
                foreach($file_data as $row_data) {
                    if(isset($row_data['file_id'])) {
                        $file = $m_file->find($row_data['file_id']);
                        if(empty($file)) continue;
                        $file = $file->toArray();
                    }

                    $file['sr_id'] = $sr_id;
                    $rs = $m_srf->data([])->allowField(true)->isUpdate(false)->save($file);
                    if($rs === false) throw new FailResult('添加服务记录文件失败');
                }
            }

            

            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    public function delServiceRecord()
    {
        if(empty($this->getData())) return $this->user_error('服务记录数据错误');

        try {
            $this->startTrans();
            $rs = $this->serviceRecordFile()->delete();
            if($rs === false) throw new FailResult($this->getErrorMsg());

            $rs = $this->delete();
            if($rs === false) throw new FailResult($this->getErrorMsg());

            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    public static function GetAttendanceData($satt_id)
    {
        $info = get_satt_info($satt_id);
        $content = '考勤相关信息，上课老师：%s，%s，出勤时间：%s，出勤方式：%s，备注：%s，%s';

        $ename = get_teacher_name($info['eid']);
        $in_time = !is_date_format($info['in_time']) && $info['in_time'] > 0 ? date('Y-m-d H:i:s', $info['in_time']) : $info['in_time'];
        $att_way_arr = [
            0 => '登记考勤',
            1 => '刷卡考勤',
            2 => '老师点名考勤',
            3 => '自由登记考勤',
        ];

        $att_way = !empty($att_way_arr[$info['att_way']]) ? $att_way_arr[$info['att_way']] : '';
        $remark = !empty($info['remark']) ? $info['remark'] : '-';

        $study = [
            $info['lid'] > 0 ? '课程-' . get_lesson_name($info['lid']) : '',
            $info['cid'] > 0 ? '班级-' . get_class_name($info['cid']) : ''
        ];
        $study = implode(',', array_filter($study));

        $extra = [
            $info['is_in'] == 0 ? '缺勤' : '',
            $info['is_late'] == 1 ? '迟到' : '',
            $info['is_leave'] == 1 ? '请假' : '',
            $info['consume_lesson_hour'] > 0 ? '扣除' . $info['consume_lesson_hour'] . '课时' : ''
        ];
        $extra = implode(',', array_filter($extra));

        $content = sprintf($content, $ename, $study, $in_time, $att_way, $remark, $extra);
        $data = [
            'sid' => $info['sid'],
            'rel_id' => $satt_id,
            'st_did' => 221,
            'content' => $content,
        ];
        return $data;
    }

}