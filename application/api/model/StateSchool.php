<?php
/**
 * Author: luo
 * Time: 2017-11-30 14:29
**/

namespace app\api\model;

class StateSchool extends Base
{

    protected $connection = 'center_database';
    protected $table = 'pro_state_school';

    protected $append = ['school_id_text'];

    protected function getSchoolIdTextAttr($value, $data)
    {
        return $data['name'];
    }

    //excel导入使用
    public static function findOrCreate($school_id)
    {
        $record = self::get(function ($query) use ($school_id) {
            $query->where('id|name', $school_id);
        });
        if ($record) {
            return $record->id;
        }
        $record = self::create(['name' => $school_id]);
        return $record->id;
    }

    public static function getSchoolIdText($school_id)
    {
        $text = redis()->get('public_school_id_'.$school_id);
        if($text) return $text;

        $school = PublicSchool::get(['id' => $school_id]);
        $text = $school ? $school->school_id_text : '';
        redis()->set('public_school_id_'.$school_id, $text, 3600);

        return $text;
    }

}