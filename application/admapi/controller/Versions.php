<?php
/**
 * Author: payhon
 * Time: 2017/12/15 10:33
 */
namespace app\admapi\controller;

use think\Request;
use app\admapi\model\Version;

class Versions extends Base
{
	public function get_list(Request $request)
    {
        $input = $request->request();
        $m_version = new Version();
        $ret = $m_version->getSearchResult($input);
        return $this->sendSuccess($ret);
    }


    public function get_list_batupgrade(Request $request)
    {
        $input = $request->request();

        $ver        = $input['version'];
        $page       = $input['page'];
        $pagesize   = $input['pagesize'];



        $m_version  = new Version();

        $do_nums = $m_version->batUpgradeVersion($ver,$page,$pagesize);

        if(false === $do_nums){
            return $this->sendError($m_version->getError());
        }

        $ret['count'] = $do_nums;
        $ret['logs']  = $m_version->getUpgradeLogs();

        return $this->sendSuccess($ret);
    }



    public function post(Request $request)
    {
        $input = $request->post();

        $m_version = new Version();

        $result = $m_version->addVersion($input);

        if(false === $result){
        	return $this->sendError(400,$m_version->getError());
        }

        return $this->sendSuccess('ok');
    }


    public function delete(Request $request)
    {
         $id = input('id/d');

         $m_version = new Version();

         $result = $m_version->delVersion($id);

         if(!$result){
            return $this->sendError($m_version->getError());
         }

         return $this->sendSuccess('ok');
    }



}