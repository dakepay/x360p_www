<?php
/**
 * luo 20171008
 */

namespace app\api\model;

use app\common\exception\FailResult;

class Lesson extends Base
{
    const LESSON_TYPE_CLASS = 0;
    const LESSON_TYPE_ONE_TO_ONE = 1;
    const LESSON_TYPE_ONE_TO_MULTI = 2;
    const LESSON_TYPE_TRAVEL = 3;
    
    const PRICE_TYPE_TIMES = 1; //按课次收费
    const PRICE_TYPE_HOUR = 2;  //按课时收费
    const PRICE_TYPE_MONTH = 3; //按时间（月）收费

    protected $readonly = ['lid'];
    protected $append = ['fit_age', 'fit_grade','define_price','define_promotion_rule'];
    protected $type = [
        'ability' => 'array',
        'public_content' => 'json',
    ];

    public function setBidsAttr($value)
    {
        return is_array($value) ? implode(',', $value) : $value;
    }

    public function getBidsAttr($value)
    {
        return split_int_array($value);
    }


    public function setSjIdAttr($value,$data){
        if(!$value){
            $sj_ids = $data['sj_ids'];
            if(is_string($sj_ids)){
                $sj_ids = split_int_array($sj_ids);
            }
            $sj_id = $sj_ids[0];
        }else{
            $sj_id = $value;
        }
        return $sj_id;
    }

    public function setSjIdsAttr($value,$data)
    {
        $sj_ids = is_array($value) ? implode(',', $value) : $value;
        if(empty($sj_ids) && !empty($data['sj_id'])){
            $sj_ids = $data['sj_id'];
        }
        return $sj_ids;
    }

    public function getSjIdsAttr($value,$data)
    {
       $sj_ids = split_int_array($value);
       if(empty($sj_ids)){
        if(!empty($data['sj_id'])){
            $sj_ids = [$data['sj_id']];
        }else{
            $sj_ids = [];
        }
       }
       return $sj_ids;
    }

    protected function scopeBids($query)
    {
        $bid = input('bids');
        $bid = !empty($bid) ? $bid : request()->header('x-bid');
        if($bid) {
            if(strpos($bid,',') !== false){
                $bids = array_filter(explode(',',$bid));
                if(!empty($bids)) {
                    $where = array_reduce($bids, function($where, $val){
                        $where[] = "find_in_set($val, bids)";
                        return $where;
                    });
                    $where_bids = implode(' or ', $where);
                }

            }else{
                $bid = intval($bid);
                if($bid !== -1){
                    $where_bids = "find_in_set($bid, bids)";
                }
            }

            if(isset($where_bids)) {
                $query->where(function($q) use ($where_bids){
                    $q->where($where_bids)->whereOr('is_public',1);
                });

            }

        }
    }

    /**
     * 获得定义价格
     * @param $value
     * @param $data
     */
    public function getDefinePriceAttr($value,$data){
        $price = 0.00;
        $bid = request()->header('x-bid');
        if(!$bid){
            return $price;
        }

        $price = get_lesson_define_price($data['lid'],$bid);
        return $price;
    }

    /**
     * 获得促销规则
     * @param $value
     * @return float|int
     */
    public function getDefinePromotionRuleAttr($value,$data)
    {
        $promotion_value = null;
        $bid = auto_bid();
        if(!$bid){
            return $promotion_value;
        }

        $promotion_value = get_lesson_define_promotion_rule($data['lid'],$bid);
        return $promotion_value;
    }

    public function setFitAgeAttr($value)
    {
        if(is_array($value) && count($value) == 2){
            $this->data['fit_age_start'] = $value[0];
            $this->data['fit_age_end'] = $value[1];
        }else{
            $this->data['fit_age_start'] = 0;
            $this->data['fit_age_end']   = 0;
        }
        return $value;
    }

    public function getFitAgeAttr($value, $data)
    {
        try {
            if(isset($data['fit_age_start']) && isset($data['fit_age_end'])) {
                $value = array($data['fit_age_start'], $data['fit_age_end']);
            } elseif (isset($data['fit_age']) && is_array($data['fit_age'])) {
                $value = $data['fit_age'];
            } else {
                $value = [0,0];
            }
                return $value;
        } catch (\Exception $exception) {
            return;
        }

    }

    public function setFitgradeAttr($value)
    {
        if(is_array($value) && count($value) == 2){
            $this->data['fit_grade_start'] = $value[0];
            $this->data['fit_grade_end'] = $value[1];
        }else{
            $this->data['fit_grade_start'] = 0;
            $this->data['fit_grade_end']   = 0;
        }
        
        return $value;
    }

    public function getFitGradeAttr($value, $data)
    {
        try {
            if(isset($data['fit_grade_start']) && isset($data['fit_grade_end'])){
                $value = array($data['fit_grade_start'], $data['fit_grade_end']);
            }elseif(isset($data['fit_grade']) && is_array($data['fit_grade'])){
                $value = $data['fit_grade'];
            }else{
                $value = [0,0];
            }
            return $value;
        } catch (\Exception $exception) {
            return;
        }

    }

    /**
     * 获得单课时分钟数
     * @param $value
     * @param $data
     * @return float|int
     */
    public function getPerLessonHourMinutesAttr($value,$data){
        if(!$value && empty($data)){
            return 0;
        }

        $unit_lesson_minutes = isset($data['unit_lesson_minutes']) ? $data['unit_lesson_minutes'] : 0;
        $unit_lesson_hours = isset($data['unit_lesson_hours']) ? $data['unit_lesson_hours'] : 1;

        if($value != 0 && $value != ($unit_lesson_minutes/$unit_lesson_hours)) {
            return $unit_lesson_minutes/$unit_lesson_hours;
        }
        //if($value === 0 && !empty($data)){
        //    $minutes = $data['unit_lesson_minutes'] / $data['unit_lesson_hours'];
        //    return $minutes;
        //}
        return $value;
    }

    /**
     * 获得单次课时的单价
     * @param  [type] $value [description]
     * @param  [type] $data  [description]
     * @return [type]        [description]
     */
    public function getPerLessonHourPriceAttr($value,$data){
        if($data['price_type'] == 2){
            return $data['unit_price'];
        }elseif($data['price_type'] == 3){
            return 0.00;
        }elseif($data['price_type'] == 1){  //按课次
            return round($data['unit_price'] / $data['unit_lesson_hours']);
        }

        return 0.00;

    }

    public function lessonMaterial()
    {
        return $this->belongsToMany('Material', 'lesson_material', 'mt_id', 'lid');
    }

    public function attachments()
    {
        return $this->hasMany('Attachment', 'lid', 'lid')
            ->field('create_time,create_uid,update_time,delete_time,delete_uid,is_delete', true)
            ->order('create_time', 'desc');
    }

    public function chapters()
    {
        return $this->hasMany('Chapter', 'lid', 'lid')->order('chapter_index', 'asc');
    }

    public function goods()
    {
        return $this->hasMany('Goods', 'lid', 'lid');
    }

    public function classes() {
        return $this->hasMany('Classes', 'lid', 'lid');
    }

    public function abilities()
    {
        return $this->hasMany('LessonAbility', 'lid', 'lid');
    }

    public function  deleteLesson()
    {
        $lid = $this->getData('lid');
        $order_item = OrderItem::get(['lid' => $lid]);
        if(!empty($order_item)) return $this->user_error('有相关订单，删除不了');

        $class = Classes::get(['lid' => $lid]);
        if(!empty($class)) return $this->user_error('有相关班级，删除不了');

        $course = CourseArrange::get(['lid' => $lid]);
        if(!empty($course)) return $this->user_error('有相关课程，删除不了');

        try {
            $this->startTrans();
            $rs = $this->attachments()->where('lid', $this->getData('lid'))->delete();
            if($rs === false) return $this->user_error('删除相关附件失败');

            $rs = LessonMaterial::destroy(['lid' => $lid]);
            if($rs === false) return $this->user_error('删除关联物品失败');

            $rs = $this->delete();
            if($rs === false) return $this->user_error('删除课程失败');
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            $this->error = $e->getMessage();
            return false;
        }

        return true;
    }

    public function addLesson($input)
    {
        foreach($input as $k=>$v){
            if(is_null($v)){
                unset($input[$k]);
            }
        }
        $this->startTrans();
        try {
            $this->data($input, true)->isUpdate(false)->allowField(true)->save();
            
        } catch (\Exception $e) {
            $this->rollback();
            $this->error = $e->getMessage();
            return false;
        }
        $this->commit();
        return true;
    }

    public function editLesson($input)
    {
        if((isset($input['price_type']) && $input['price_type'] != $this->price_type)
            || isset($input['lesson_type']) && $input['lesson_type'] != $this->lesson_type) {
            $order_item = OrderItem::get(['lid' => $this->lid]);
            if(!empty($order_item)) return $this->user_error('已经有相关的订单，无法修改计费方式和授课方式');
        }



        $this->startTrans();
        try {

            if(isset($input['sj_ids']) && (!empty(array_diff($this->sj_ids, $input['sj_ids']))
                    || !empty(array_diff($input['sj_ids'], $this->sj_ids)))){
                $update_sl['sj_ids'] = implode(',',$input['sj_ids']);
                $w_sl_update['lid']  = $this->lid;
                $m_sl = new StudentLesson();
                $result = $m_sl->save($update_sl,$w_sl_update);
                if(false === $result){
                    $this->rollback();
                    return $this->sql_save_error('student_lesson');
                }
            }

            $result = $this->isUpdate(true)->allowField(true)->save($input);
            if(false === $result){
                $this->rollback();
                return $this->sql_save_error('lesson');
            }
        } catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }
    /**
     * 获得单次课时数
     * @param  [type] $lid [description]
     * @return [type]      [description]
     */
    public static function getUnitLessonHoursByLid($lid){
        static $lessons = [];
        if(isset($lessons[$lid])){
            $lesson = $lessons[$lid];
        }else{
            $lesson = self::get($lid);
            $lessons[$lid] = $lesson;
        }
        return $lesson['unit_lesson_hours'];
    }

    public static function isSjIdsInLesson($sj_ids, $lid)
    {
        $lesson = self::get($lid);
        if(empty($lesson)) throw new FailResult('课程不存在');

        $sj_ids = !is_array($sj_ids) ? explode(',', $sj_ids) : $sj_ids;

        if(!empty(array_diff($sj_ids, $lesson->sj_ids))) throw new FailResult('科目与课程的科目不一致');

        return true;
    }

    /**
     * 获取指定课程的班级
     * @param $lid
     */
    public function getClass($lid)
    {
        if ($lid == 0) {
            $lid = $this->getData('lid');
        }

        $class_list = [];

        $lesson_info = get_lesson_info($lid);

        if (!$lesson_info) {
            return $class_list;
        }

        $w_le = [
            'lid' => $lid,
            'status' => Classes::STATUS_ING,
        ];
        $mClass = new Classes();

        $class_list = $mClass->where($w_le)->select();

        return $class_list;
    }

}