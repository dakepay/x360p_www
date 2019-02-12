<?php
/**
 * Author: luo
 * Time: 2018/4/18 10:03
 */

namespace app\sapi\controller;


use app\common\db\Query;
use app\sapi\model\Comment;
use app\sapi\model\CommentClick;
use think\Request;

class Comments extends Base
{

    public function get_list(Request $request)
    {
        $get = $request->get();

        /** @var Query $m_comment */
        $m_comment = new Comment();
        $sid = global_sid();
        $ret = $m_comment->where('sid', $sid)->getSearchResult($get);
        return $this->sendSuccess($ret);
    }

    public function tree_comments()
    {
        $app_name = input('app_name');
        $app_content_id = input('app_content_id');
        $page = input('page', 1);
        $pagesize = input('pagesize', 20);

        if (empty($app_name) || empty($app_content_id)) return $this->sendError(400, 'param error');
        if(($sid = global_sid()) <= 0) return $this->sendError(400, 'sid error');

        $m_comment = new Comment();
        $ret = $m_comment->getTreeComments($app_name, $app_content_id, $page, $pagesize);
        $ret['comment_num'] = $m_comment->where('app_name', $app_name)->where('app_content_id', $app_content_id)
            ->count();

        if (!empty(input('merge_second'))) {
            foreach ($ret['list'] as &$row) {

                if (!empty($row['child_comments'])) {
                    $second_child_comments = [];
                    $this->merge_second($row['child_comments'], $second_child_comments);
                    $row['child_comments'] = $second_child_comments;
                }
            }
        }

        $this->with_student($ret['list'], $sid);

        return $this->sendSuccess($ret);
    }

    private function with_student(&$list, $sid)
    {
        $m_cc = new CommentClick();
        foreach($list as &$row) {

            $click = $m_cc->where('cmt_id', $row['cmt_id'])->where('sid', $sid)->find();
            $row['comment_click'] = $click;

            if(!empty($row['child_comments'])) {
                $this->with_student($row['child_comments'], $sid);
            }

        }
    }

    private function merge_second($list, &$second_child_comments) {

        foreach($list as $row) {
            if(!empty($row['child_comments'])) {
                $this->merge_second($row['child_comments'], $second_child_comments);
            }

            unset($row['child_comments']);
            array_unshift($second_child_comments, $row);
        }
    }

    public function post(Request $request)
    {
        $post = $request->post();

        $m_comment = new Comment();
        $rs = $m_comment->addComment($post);
        if($rs === false) return $this->sendError(400, $m_comment->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  点赞
     * @author luo
     * @param Request $request
     * @url   /api/lessons/:id/
     * @method POST
     */
    public function post_up(Request $request)
    {
        $post = $request->post();
        $m_cc = new CommentClick();
        $post['cmt_id'] = input('id');
        $rs = $m_cc->click($post);
        if($rs === false) return $this->sendError(400, $m_cc->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * 添加学生对老师的评论
     * @param Request $request
     * @return bool|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function do_teacher_comment(Request $request){
        $post = $request->post();
        if (empty($post['sid'])) return $this->sendError(400,'sid not exists');
//        if (isset($post['eid'])) return $this->sendError(400,'eid not exists');
        if (empty($post['score'])) return $this->sendError(400,'score not exists');
        if (empty($post['content'])) return $this->sendError(400,'content not exists');
        if (empty($post['rvw_id'])) return $this->sendError(400,'rvw_id not exists');

        $m_comment = new Comment();
        $rs = $m_comment->addStudentToTeacherComment($post['sid'],$post['eid'],$post['score'],$post['content'],$post['rvw_id']);
        if($rs === false){
            return $this->sendError(400,$m_comment->getError());
        }
        return $this->sendSuccess();
    }

    /**
     * 添加学生对校区的评论
     * @param Request $request
     * @return bool|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function do_school_comment(Request $request){
        $post = $request->post();
        if (empty($post['sid'])) return $this->sendError(400,'sid not exists');
        if (empty($post['bid'])) return $this->sendError(400,'bid not exists');
        if (empty($post['score'])) return $this->sendError(400,'score not exists');
        if (empty($post['content'])) return $this->sendError(400,'content not exists');

        $m_comment = new Comment();
        $rs = $m_comment->addStudentToSchoolComment($post['sid'],$post['bid'],$post['score'],$post['content']);
        if($rs === false){
            return $this->sendError(400,$m_comment->getError());
        }
        return $this->sendSuccess();
    }

}