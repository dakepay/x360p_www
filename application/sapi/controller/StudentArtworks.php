<?php
/**
 * Author: luo
 * Time: 2018-04-11 16:39
**/

namespace app\sapi\controller;

use app\common\db\Query;
use app\sapi\model\StudentArtwork;
use think\Request;

class StudentArtworks extends Base
{

    public function get_list(Request $request)
    {
        $sid = global_sid();
        $get = $request->get();
        /** @var Query $m_sa */
        $m_sa = new StudentArtwork();
        $ret = $m_sa->where('sid', $sid)->getSearchResult($get);
        
        return $this->sendSuccess($ret);
    }

    public function get_detail(Request $request, $id = 0)
    {
        $sart_id = $id;
        $m_sa = new StudentArtwork();
        $info = $m_sa->with(['studentArtworkAttachment','student', 'studentArtworkReview'])->find($sart_id);
        return $this->sendSuccess($info);
    }

    /**
     * @desc  备课
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function post(Request $request)
    {
        $sid = global_sid();
        if($sid <= 0) return $this->sendError(400, 'sid error');

        $post = $request->post();
        $post['sid'] = $sid;

        $m_sa = new StudentArtwork();
        $rs = $m_sa->addArtwork($post);
        if($rs === false) return $this->sendError(400, $m_sa->getErrorMsg());

        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $sart_id = input('id');
        $put = $request->put();
        $attachment_data = isset($put['student_artwork_attachment']) ? $put['student_artwork_attachment'] : [];

        /** @var StudentArtwork $preparation */
        $artwork = StudentArtwork::get($sart_id);
        if(empty($artwork)) return $this->sendError(400, '作品不存在');
        $rs = $artwork->edit($put, $attachment_data);
        if($rs === false) return $this->sendError(400, $artwork->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  删除备课
     * @author luo
     * @param Request $request
     * @method DELETE
     */
    public function delete(Request $request)
    {
        $sart_id = input('id');
        $m_sa = new StudentArtwork();
        $rs = $m_sa->delArtwork($sart_id);
        if($rs === false) return $this->sendError(400, $m_sa->getErrorMsg());
        
        return $this->sendSuccess();
    }

}