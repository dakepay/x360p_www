<?php
/**
 * Author: luo
 * Time: 2018/4/9 9:32
 */

namespace app\api\controller;


use app\api\model\StudentArtwork;
use app\api\model\StudentArtworkReview;
use app\common\db\Query;
use think\Request;

class StudentArtworks extends Base
{

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
        $post = $request->post();

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

    /**
     * @desc  作品点评
     * @author luo
     * @method POST
     */
    public function post_review(Request $request)
    {
        $sart_id = input('id');
        $artwork = StudentArtwork::get($sart_id);
        if(empty($artwork)) return $this->sendError(400, '作品不存在');

        $post = $request->post();
        $post['sart_id'] = $sart_id;

        $m_sar = new StudentArtworkReview();
        $rs = $m_sar->addReview($post);
        if($rs === false) return $this->sendError(400, $m_sar->getErrorMsg());

        return $this->sendSuccess();
    }

}