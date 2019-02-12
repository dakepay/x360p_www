<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/15
 * Time: 18:43
 */
namespace app\api\export;

use app\common\Export;
use app\api\model\SwipingCardRecord;


class SwipingCardRecords extends Export
{
    protected $res_name = 'swiping_card_record';

    protected $columns = [
        ['field'=>'sid','title'=>'学员','width'=>20],
        ['field'=>'card_no','title'=>'卡号','width'=>20],
        ['field'=>'business_type','title'=>'业务类型','width'=>20],
        ['field'=>'create_time','title'=>'刷卡时间','width'=>20],
    ];

    protected function get_title(){
        $title = '刷卡记录表';
        return $title;
    }

    protected function convert_type($value)
    {
        $map = [0=>'-', 1=>'上课考勤', 2=>'离校通知',3=>'到校通知'];
        if (key_exists($value, $map)) {
            return $map[$value];
        }
        return '';
    }

    public function get_data()
    {
        $model = new SwipingCardRecord();
        $result = $model->getSearchResult($this->params,[],false);
        $list = $result['list'];

        foreach ($list as $k => $v) {

            $list[$k]['sid']    = get_student_name($v['sid']);
            $list[$k]['business_type'] = $this->convert_type($v['business_type']);

        }

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];

    }
}