<?php
/**
 * Author: luo
 * Time: 2018/3/1 14:45
 */

namespace app\api\controller;


use app\api\model\AccountingAccount;
use app\api\model\ConfigPay;
use think\Request;

class ConfigPays extends Base
{
    public function get_list(Request $request)
    {
        $m_cp = new ConfigPay();
        $input = $request->get();
        $ret = $m_cp->getSearchResult($input);

        return $this->sendSuccess($ret);
    }

    public function get_detail(Request $request, $id = 0)
    {
        $cp_id = input('id');
        $m_cp = new ConfigPay();
        $info = $m_cp->find($cp_id);

        return $this->sendSuccess($info);
    }

    /**
     * @desc  微信支付配置
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function post(Request $request)
    {
        $post = $request->post();

        $m_cp = new ConfigPay();
        $rs = $m_cp->addConfig($post);
        if($rs === false) return $this->sendError(400, $m_cp->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  收钱吧支付配置
     * @param Request $request
     * @method POST
     */
    public function post_sqb(Request $request){
        $post = $request->post();

        $m_cp = new ConfigPay();
        $rs = $m_cp->addSqbConfig($post);
        if($rs === false) return $this->sendError(400, $m_cp->getErrorMsg());

        return $this->sendSuccess();
    }


    public function put(Request $request)
    {
        $cp_id = input('id');
        $config = ConfigPay::get($cp_id);
        if(empty($config)) return $this->sendError(400, '配置不存在');

        $put = $request->put();
        $rs = $config->updateConfig($put);
        if($rs === false) return $this->sendError(400, $config->getErrorMsg());

        return $this->sendSuccess();
    }

    public function put_sqb(Request $request){
        $cp_id = input('id');
        $config = ConfigPay::get($cp_id);
        if(empty($config)) return $this->sendError(400, '配置不存在');

        $put = $request->put();
        $rs = $config->updateSqbConfig($put);
        if($rs === false) return $this->sendError(400, $config->getErrorMsg());

        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $cp_id = input('id');
        $config = ConfigPay::get($cp_id);
        if(empty($config)) return $this->sendError(400, '配置不存在');

        $rs = $config->delConfig($cp_id);
        if($rs === false) return $this->sendError(400, $config->getErrorMsg());

        return $this->sendSuccess();
    }
}