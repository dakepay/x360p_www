<?php
/**
 * luo 20171008
 */

namespace app\sapi\model;

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
    protected $append = ['fit_age', 'fit_grade'];
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

    public function setSjIdsAttr($value)
    {
        return is_array($value) ? implode(',', $value) : $value;
    }

    public function getSjIdsAttr($value)
    {
       return split_int_array($value);
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

    public function lessonMaterial()
    {
        return $this->belongsToMany('Material', 'lesson_material', 'mt_id', 'lid');
    }

    public function attachments()
    {
        return $this->hasMany('Attachment', 'lid', 'lid')
            ->field('create_time,create_uid,update_time,delete_time,delete_uid,is_delete', true)
            ->where('lc_id', 0)
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

}