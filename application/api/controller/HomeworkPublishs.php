<?php
/**
 * Author: luo
 * Time: 2018/7/18 11:10
 */

namespace app\api\controller;


use app\api\model\HomeworkPublish;
use think\Request;

class HomeworkPublishs extends Base
{

    public function post(Request $request)
    {
        $post = $request->post();
        $m_hp = new HomeworkPublish();

        $rs = $m_hp->addHomeworkPublish($post);
        if($rs === false) return $this->sendError(400, $m_hp->getErrorMsg());

        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        return $this->sendError(400, "not support");
    }

}