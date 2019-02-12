<?php
/**
 * Author: luo
 * Time: 2018/4/18 9:30
 */

namespace app\sapi\model;


class Comment extends Base
{

    const APP_NAME_TEACHER = 'teacher';         //学员对老师的点评
    const APP_NAME_SCHOOL  = 'school';          //学员对校区的点评

    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,student_name,photo_url');
    }

    public function employee()
    {
        return $this->hasOne('Employee', 'eid', 'eid')->field('eid,ename,photo_url');
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

    public function addComment($data)
    {
        $rs = $this->validate()->validateData($data);
        if($rs !== true) return false;

        $rs = $this->allowField(true)->data([])->save($data);
        if($rs === false) return false;

        return true;
    }

    /**
     * 添加学生对老师的评论
     * @param $sid
     * @param $eid
     * @param $score
     * @param $content
     * @param $rvw_id
     */
    public function addStudentToTeacherComment($sid,$eid,$score,$content,$rvw_id = 0){
        $cmt['app_name'] = self::APP_NAME_TEACHER;
        $cmt['app_content_id'] = $rvw_id;
        $cmt['content'] = $content;
        $cmt['score'] = $score;
        $cmt['sid'] = $sid;
        $cmt['eid'] = $eid;

        return $this->addComment($cmt);
    }

    /**
     * 添加学生对校区的评论
     * @param $sid
     * @param $bid
     * @param $scoe
     * @param $conent
     */
    public function addStudentToSchoolComment($sid,$bid,$score,$content){
        $cmt['app_name'] = self::APP_NAME_SCHOOL;
        $cmt['app_content_id'] = $bid;
        $cmt['content'] = $content;
        $cmt['score'] = $score;
        $cmt['sid'] = $sid;

        return $this->addComment($cmt);
    }

}