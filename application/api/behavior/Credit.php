<?php


namespace app\api\behavior;

use app\api\model\CreditRule;
use app\api\model\StudentCreditHistory;

/**
 * Class Credit
 * @package app\api\behavior
 * @desc 学员积分变化钩子
 */
class Credit
{
    /**
     * @author luo
     * @param $data
     * @desc Hook::listen('handle_credit', ['hook_action'=>'attendance', 'sid' => 23]);
     */
    public function run($data)
    {
        if(!empty($data['hook_action'])) {
            $credit_rule = CreditRule::get(['hook_action' => $data['hook_action']]);
            if(empty($credit_rule)) {
                //log_write('钩子不存在，hook_action:'.$data['hook_action'], 'error');
                return true;
            }

            if(!$credit_rule['enable']) return true;
            if(!empty($credit_rule['rule'])) {
                $method_name = 'get_' . $data['hook_action'] . '_credit';

                $m_cr = new CreditRule();
                if(!method_exists($m_cr, $method_name)) {
                    log_write('钩子规则方法不存在，hook_action:'.$data['hook_action'], 'error');
                    return true;
                }
                $data['credit'] = $m_cr->$method_name($data, $credit_rule['rule']);
                if($data['credit'] === false) {
                    log_write('钩子执行错误，hook_action:'.$data['hook_action'] . ' error: ' . $m_cr->getErrorMsg(), 'error');
                    return true;
                }
            } else {
                $data['credit'] = $credit_rule['credit'];
            }

            $data['type'] = $credit_rule['type'];
            $data['cate'] = $credit_rule['cate'];
            $data['cru_id'] = $credit_rule['cru_id'];
        }

        $m_sch = new StudentCreditHistory();
        try {
            $rs = $m_sch->addOneHistory($data);
            if($rs === false) exception($m_sch->getErrorMsg());

        } catch(\Exception $e) {
            $msg = sprintf('credit_hook_error: %s, data: %s', $e->getMessage(), json_encode($data));
            log_write($msg, 'error');
        }

        return true;
    }


}