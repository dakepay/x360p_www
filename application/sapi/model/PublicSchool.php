<?php
/**
 * Author: luo
 * Time: 2017-10-11 11:09
**/

namespace app\sapi\model;

class PublicSchool extends Base
{

    protected $append = ['school_id_text'];

    protected function getSchoolIdTextAttr($value, $data)
    {
        return $data['school_name'];
    }

    //excel导入使用
    public static function findOrCreate($school_id)
    {
        $record = self::get(function ($query) use ($school_id) {
            $query->where('ps_id|school_name', $school_id);
        });
        if ($record) {
            return $record->ps_id;
        }
        $record = self::create(['school_name' => $school_id]);
        return $record->ps_id;
    }

    public static function getSchoolIdText($school_id)
    {
        $text = redis()->get('public_school_id_'.$school_id);
        if($text) return $text;

        $school = PublicSchool::get(['ps_id' => $school_id]);
        $text = $school ? $school->school_id_text : '';
        redis()->set('public_school_id_'.$school_id, $text, 3600);

        return $text;
    }

}