<?php
/**
 * Author: luo
 * Time: 2018/5/24 15:39
 */

namespace app\api\controller;


use app\api\model\LessonBuySuit;
use app\api\model\Message;
use app\api\model\StudySituation;
use think\Request;

class StudySituations extends Base
{
    public function get_list(Request $request)
    {
        $get = $request->get();
        $m_ss = new StudySituation();
        $ret = $m_ss->getSearchResult($get);

        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $m_ss = new StudySituation();
        $post = $request->post();
        $rs = $m_ss->addStudySituation($post);
        if($rs === false) return $this->sendError(400, $m_ss->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $ss_id = input('id');
        if(empty($ss_id)) return $this->sendError(400, 'ss_id 错误');

        $put = $request->put();
        $put['ss_id'] = $ss_id;
        $m_ss = new StudySituation();
        $rs = $m_ss->updateStudySituation($put);
        if($rs === false) return $this->sendError(400, $m_ss->getErrorMsg());

        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $ss_id = input('id');
        $study_situation = StudySituation::get($ss_id);
        if(empty($study_situation)) return $this->sendSuccess();

        $rs = $study_situation->delStudySituation();
        if($rs === false) return $this->sendError(400, $study_situation->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  学习问卷推荐学习套餐
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function post_lesson_buy_suit(Request $request)
    {
        $ss_id = input('id');
        $post = $request->post();
        $post['ss_id'] = $ss_id;

        $m_lbs = new LessonBuySuit();
        $rs = $m_lbs->addLessonBuySuit($post);
        if($rs === false) return $this->sendError(400, $m_lbs->getErrorMsg());
        
        return $this->sendSuccess();
    }

    /**
     * @desc  推送分析报告给家长
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function push(Request $request)
    {
        $ss_id   = input('post.ss_id/d');
        $mobiles = input('post.mobiles/a');
        $study_situation = StudySituation::get($ss_id);
        if(empty($study_situation)) return $this->sendError(400, '不存在学习报告');

        $result = $study_situation->pushMessage($mobiles);

        if(!$result){
            return $this->sendError(400,$study_situation->getError());
        }

        return $this->sendSuccess();
    }

    /**
     * @desc  推送信息预览
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function post_push_preview(Request $request)
    {
        $ss_id = input('id');
        $m_ss = new StudySituation();
        $data = $m_ss->pushPreview($ss_id);
        if($data === false) return $this->sendError(400, $m_ss->getErrorMsg());
        return $this->sendSuccess($data);
    }

}