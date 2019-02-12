<?php
/**
 * Author: luo
 * Time: 2018/6/15 11:12
 */

namespace app\api\controller;


use app\api\model\EduGrowup;
use app\api\model\EduGrowupItem;
use think\Request;

class EduGrowups extends Base
{
    public function get_list(Request $request)
    {
        $m_eg = new EduGrowup();
        $get = $request->get();
        $ret = $m_eg->getSearchResult($get);
        
        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $post = $request->post();
        $m_eg = new EduGrowup();
        $rs = $m_eg->addGroupup($post);
        if($rs === false) return $this->sendError(400, $m_eg->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $eg_id = input('id');
        $edu_growup = EduGrowup::get($eg_id);
        if(empty($edu_growup)) return $this->sendError();

        $put = $request->put();
        $rs = $edu_growup->updateEduGrowup($put);
        if($rs === false) return $this->sendError(400, $edu_growup->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $eg_id = input('id');
        $edu_growup = EduGrowup::get($eg_id);
        if(empty($edu_growup)) return $this->sendSuccess();

        $rs = $edu_growup->delEduGrowup();
        if($rs === false) return $this->sendError(400, $edu_growup->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function post_items(Request $request)
    {
        $eg_id = input('id');
        $edu_growup = EduGrowup::get($eg_id);
        if(empty($edu_growup)) return $this->sendError(400, '成长记录不存在');

        $post = $request->post();
        $post['eg_id'] = $eg_id;
        $post['sid'] = $edu_growup['sid'];
        $post['cid'] = $edu_growup['cid'];
        $post['bid'] = $edu_growup['bid'];
        $m_egi = new EduGrowupItem();
        $rs = $m_egi->addEduGrowupItem($post);
        if($rs === false) return $this->sendError(400, $m_egi->getErrorMsg());

        return $this->sendSuccess();
    }

}