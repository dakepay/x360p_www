<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/15
 * Time: 18:43
 */
namespace app\api\export;

use app\common\Export;
use app\api\model\Tally;


class Tallys extends Export
{
    protected $res_name = 'tally';

    protected $columns = [
        ['field'=>'int_day','title'=>'日期','width'=>20],
        ['field'=>'bid','title'=>'校区','width'=>20],
        ['field'=>'aa_id','title'=>'账户','width'=>20],
        ['field'=>'amount','title'=>'金额','width'=>20],
        ['field'=>'sid','title'=>'学员姓名','width'=>20],
        ['field'=>'remark','title'=>'备注','width'=>50],
        ['field'=>'tt_id','title'=>'收支类别','width'=>20],
    ];

    protected function get_title(){
        $title = '收支流水';
        return $title;
    }

    protected function convert_type($value)
    {
        $map = [1=>'收入', 2=>'支出'];
        if (key_exists($value, $map)) {
            return $map[$value];
        }
        return '';
    }

    protected function convert_amount($amount,$type){
        if($type==1){
            return ' +'.$amount;
        }else{
            return ' -'.$amount;
        }
    }

    protected function get_cate_name($tt_id){
        if($tt_id!=0){
            $name = m('tally_type')->where('tt_id',$tt_id)->find();
            return $name->name;
        }
    }

    public function get_data()
    {
        $model = new Tally();
        $result = $model->getSearchResult($this->params,[],false);
        $list = $result['list'];

        foreach ($list as $k => $v) {
            $list[$k]['bid']       = get_branch_name($v['bid']);
            $list[$k]['amount'] = $this->convert_amount($v['amount'],$v['type']);
            $list[$k]['aa_id']   = get_account_name($v['aa_id']);
            $list[$k]['sid']   = get_student_name($v['sid']);
            $list[$k]['int_day']   = int_day_to_date_str($v['int_day']);
            $list[$k]['tt_id'] = $this->get_cate_name($v['tt_id']);
        }

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];

    }
}