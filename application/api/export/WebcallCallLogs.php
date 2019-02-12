<?php
namespace app\api\export;

use app\common\Export;
use app\api\model\WebcallCallLog;


class WebcallCallLogs extends Export
{

    protected $columns = [
        ['field'=>'bid','title'=>'所属校区简称','width'=>20],
        ['field'=>'eid','title'=>'通话员工','width'=>20],
        ['field'=>'caller_phone','title'=>'主叫电话','width'=>20],
        ['field'=>'callee_phone','title'=>'被叫电话','width'=>20],
        ['field'=>'abillsec','title'=>'呼叫时长','width'=>20],
        ['field'=>'billsec','title'=>'接通时长','width'=>20],
        ['field'=>'cacu_minutes','title'=>'计费分钟数','width'=>20],
        ['field'=>'caller_calltime','title'=>'呼叫时间','width'=>20],
        ['field'=>'callee_talkbegtime','title'=>'被叫接听时间','width'=>20],
        ['field'=>'callee_talkendtime','title'=>'被叫结束时间','width'=>20],
    ];

    protected function get_title(){
        $title = '呼叫记录';
        return $title;
    }

    public function get_data()
    {
        $model = new WebcallCallLog();
        unset($this->params['token']);
        $result = $model->getSearchResult($this->params,[],false);
        $list = $result['list'];

        foreach ($list as $k => $v) {
            $list[$k]['bid']       = get_branch_name($v['bid']);
            $list[$k]['eid'] = get_teacher_name($v['eid']);
            $list[$k]['caller_phone']  = $v['caller_phone'].' ';
            $list[$k]['callee_phone']  = $v['callee_phone'].' ';
            $list[$k]['abillsec']  = $v['abillsec'];
            $list[$k]['billsec']  = $v['billsec'];
            $list[$k]['cacu_minutes']  = $v['cacu_minutes'];
            $list[$k]['caller_calltime']  = $v['caller_calltime'];
            $list[$k]['callee_talkbegtime']  = $v['callee_talkbegtime'];
            $list[$k]['callee_talkendtime']  = $v['callee_talkendtime'];
        }

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];

    }
}