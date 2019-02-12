<?php
/**
 * Author: luo
 * Time: 2018/5/25 9:20
 */

namespace app\api\model;


use app\common\exception\FailResult;
use think\Exception;

class StudySituation extends Base
{

    protected $type = [
        'content' => 'json',
    ];


    protected $append = ['create_employee_name'];

    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function setIntDayAttr($value)
    {
        return $value ? format_int_day($value) : $value;
    }

    public function setIntHourAttr($value)
    {
        return $value ? format_int_hour($value) : $value;
    }

    public function getPushTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : $value;
    }

    public function getQueryTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : $value;
    }

    public function getViewTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : $value;
    }

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')
            ->field('sid,bid,student_name,sex,photo_url,birth_time,school_grade,school_class,school_id,first_tel,sno,card_no');
    }

    public function customer()
    {
        return $this->hasOne('Customer', 'cu_id', 'cu_id')
            ->field('cu_id,bid,name,sex,birth_time,school_grade,school_class,school_id,first_tel');
    }

    public function studySituationItem()
    {
        return $this->hasMany('StudySituationItem', 'ss_id', 'ss_id');
    }

    public function lessonBuySuit()
    {
        return $this->hasOne('LessonBuySuit','lbs_id', 'lbs_id');
    }

    public function questionnaire()
    {
        return $this->hasOne('Questionnaire', 'qid', 'qid');
    }

    //添加多个
    //public function addMultiStudySituation(array $data)
    //{
    //    try {
    //        $this->startTrans();
    //        foreach($data as $row) {
    //            $rs = $this->addStudySituation($row);
    //            if($rs === false) throw new FailResult($this->getErrorMsg());
    //        }
    //
    //        $this->commit();
    //    } catch(Exception $e) {
    //        $this->rollback();
    //        return $this->deal_exception($e->getMessage(), $e);
    //    }
    //
    //    return true;
    //}

    public function addStudySituation($data)
    {
        if(empty($data['sid']) && empty($data['cu_id'])) return $this->user_error('缺少sid或者cu_id');

        try {
            $this->startTrans();

            $study_situation_item_data = isset($data['study_situation_item']) ? $data['study_situation_item'] : [];
            unset($data['study_situation_item']);
            $data['short_id'] = short_id();
            $rs = $this->data($data)->isUpdate(false)->allowField(true)->save($data);
            if($rs === false) throw new FailResult($this->getErrorMsg());
            $ss_id = $this->ss_id;

            $service_record_data = [
                'sid' => $data['sid'] ?? 0,
                'cu_id' => $data['cu_id'] ?? 0,
                'st_did' => 236,
            ];
            add_service_record('study_situation', $service_record_data);

            $m_ssi = new StudySituationItem();
            foreach($study_situation_item_data as $item) {
                $item['ss_id'] = $ss_id;
                $rs = $m_ssi->addStudySituationItem($item);
                if($rs === false) throw new FailResult($m_ssi->getErrorMsg());
            }

            if(!empty($data['is_push'])) {
                $m_message = new Message();
                $m_message->sendTplMsg('study_situation', $this->getData(), 2);
            }

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }
        
        return true;
  }

    public function delStudySituation()
    {
        if(empty($this->getData())) return $this->user_error('学习调查数据错误');
        
        try {
            $this->startTrans();

            $rs = $this->studySituationItem()->delete();
            if($rs === false) throw new FailResult($this->getErrorMsg());

            $rs = $this->delete();
            if($rs === false) throw new FailResult($this->getErrorMsg());

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    public function updateStudySituation($update_data)
    {
        if(empty($update_data['ss_id'])) return $this->user_error('ss_id 错误');
        $rs = $this->allowField(true)->isUpdate(true)->save($update_data);
        if($rs === false) return false;

        return true;
    }

    //推送预览
    public function pushPreview($ss_id)
    {
        $study_situation = $this->find($ss_id);
        if(empty($study_situation)) return $this->user_error('学习调研不存在');

        $m_message = new Message();
        $msg = $m_message->geTplMsgPreviewData('study_situation', $study_situation->toArray());
        if($msg == false) return $this->user_error($m_message->getErrorMsg());

        $data = $msg;
        if($study_situation['sid'] > 0){
            $data['student'] = $study_situation->student;
        }else{
            $data['customer'] = $study_situation->customer;
        }

        return $data;
    }

    /**
     * 推送消息
     * @param $mobiles
     * @return bool
     */
    public function pushMessage($mobiles){
        $this->startTrans();
        try {
            $m_message = new Message();
            $rs = $m_message->sendTplMsg('study_situation', $this->getData(),$mobiles);
            if($rs === false){
                $this->rollback();
                return $this->user_error($m_message->getErrorMsg());
            }

            $this->is_push = 1;
            $this->push_time = time();
            $result = $this->save();
            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('study_situation');
            }
        } catch(\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

}