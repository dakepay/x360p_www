<?php
/**
 * Author: luo
 * Time: 2018/4/17 15:34
 */

namespace app\api\model;


class Comment extends Base
{

    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,student_name,photo_url');
    }

    public function employee()
    {
        return $this->hasOne('Employee', 'eid', 'eid')->field('eid,ename,photo_url');
    }

    public function addComment($data)
    {
        $rs = $this->validate()->validateData($data);
        if($rs !== true) return false;

        $rs = $this->allowField(true)->data([])->save($data);
        if($rs === false) return false;

        return true;
    }

    public function getTreeComments($app_name, $app_content_id, $page, $pagesize)
    {
        $m_comment = new Comment();
        $list = $m_comment->where('app_name', $app_name)->where('app_content_id', $app_content_id)
            ->where('parent_cmt_id = 0')->order('cmt_id desc')->page($page, $pagesize)
            ->with(['student', 'employee'])->select();
        $total = $m_comment->where('app_name', $app_name)->where('app_content_id', $app_content_id)
            ->where('parent_cmt_id = 0')->count();

        $list = collection($list)->toArray();
        $this->getChildComment($list);

        $ret['page']= $page;
        $ret['pagesize'] = $pagesize;
        $ret['total'] = $total;
        $ret['list'] = $list;
        return $ret;
    }

    public function getChildComment(&$list)
    {
        foreach($list as &$row) {
            $child_comments = $this->where('parent_cmt_id', $row['cmt_id'])->with('student,employee')->limit(30)->select();
            $child_comments = collection($child_comments)->toArray();

            if(!empty($child_comments)) {
                $this->getChildComment($child_comments);
            }

            $row['child_comments'] = $child_comments;
        }

    }

}