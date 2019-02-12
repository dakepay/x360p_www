<?php
namespace app\api\controller;

use app\api\model\Comment;
use app\api\model\CommentClick;
use think\Request;

class Comments extends Base
{

    protected $withoutAuthAction = ['tree_comments'];

    public function get_list(Request $request){
        $input = input();
        $m_comments = new Comment();

        $result = $m_comments->getSearchResult($input);

        foreach ($result['list'] as $k => $v){
            if ($input['app_name'] = 'teacher'){
                $review = get_review_info($v['app_content_id']);
                $result['list'][$k]['employee'] = get_employee_info($review['eid']);
                $result['list'][$k]['class'] = get_lesson_info($review['lid']);
            }
        }

        return $this->sendSuccess($result);
    }

    public function post(Request $request)
    {
        $post = $request->post();

        $m_comment = new Comment();
        $rs = $m_comment->addComment($post);
        if($rs === false) return $this->sendError(400, $m_comment->getErrorMsg());

        return $this->sendSuccess();
    }

    public function tree_comments()
    {
        $app_name = input('app_name');
        $app_content_id = input('app_content_id');
        $page = input('page', 1);
        $pagesize = input('pagesize', 20);

        if (empty($app_name) || empty($app_content_id)) return $this->sendError(400, 'param error');

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

        $with = input('with');
        $with = is_string($with) ? explode(',', $with) : $with;
        if(!empty($with) && in_array('my_click', $with)) {
            $uid = gvar('uid');
            $eid = \app\api\model\User::getEidByUid($uid);
            $this->with_my_click($ret['list'], $eid);
        }

        return $this->sendSuccess($ret);
    }

    private function with_my_click(&$list, $eid)
    {
        $m_cc = new CommentClick();
        foreach($list as &$row) {

            $click = $m_cc->where('cmt_id', $row['cmt_id'])->where('eid', $eid)->find();
            $row['comment_click'] = $click;

            if(!empty($row['child_comments'])) {
                $this->with_my_click($row['child_comments'], $eid);
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

}