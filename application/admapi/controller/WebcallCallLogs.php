<?php
namespace app\admapi\controller;

use app\admapi\model\WebcallCallLog;
use think\Request;

class WebcallCallLogs extends Base
{


    public function get_list(Request $request){
        $input = input();
        $m_wcl = new WebcallCallLog();
        $ret = $m_wcl->getSearchResult($input);

        foreach ($ret['list'] as $k => $v){
            if ($v['caller_calltime'] != 0){
                $ret['list'][$k]['caller_calltime'] = date('Y-m-d H:i:s',$v['caller_calltime']);
            }
            if ($v['caller_talkendtime'] != 0){
                $ret['list'][$k]['caller_talkendtime'] = date('Y-m-d H:i:s',$v['caller_talkendtime']);
            }

            if ($v['callee_talkbegtime'] != 0){
                $ret['list'][$k]['callee_talkbegtime'] = date('Y-m-d H:i:s',$v['callee_talkbegtime']);
            }
            if ($v['callee_talkendtime'] != 0){
                $ret['list'][$k]['callee_talkendtime'] = date('Y-m-d H:i:s',$v['callee_talkendtime']);
            }
        }

        return $this->sendSuccess($ret);
    }


}