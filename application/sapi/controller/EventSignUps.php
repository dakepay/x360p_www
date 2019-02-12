<?php
/**
 * Author: luo
 * Time: 2018/7/6 16:55
 */

namespace app\sapi\controller;


use app\sapi\model\EventSignUp;
use think\Request;

class EventSignUps extends Base
{

    public function post(Request $request)
    {
        $post = $request->post();
        $m_esu = new EventSignUp();
        $rs = $m_esu->signUp($post);
        if($rs === false) return $this->sendError(400, $m_esu->getErrorMsg());
        
        return $this->sendSuccess();
    }

}