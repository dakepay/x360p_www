<?php
/**
 * luo
 */
namespace app\api\controller;

use app\api\model\SubjectGrade;
use think\Request;
use app\api\model\Subject;
use app\api\model\Lesson;

class Subjects extends Base
{

    /**
     * 获得科目列表
     * @param Request $request
     * @return Redirect|\app\api\model\Base|object|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function get_list(Request $request)
    {
        $input = $request->get();
        $mSubject = new Subject();
        $result = $mSubject->getSearchResult($input);

        return $this->sendSuccess($result);
    }

    /**
     * 创建科目
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function post(Request $request)
    {
        $input = $request->post();

        if(isset($input['subject_desc'])){
            $input['short_desc'] = $input['subject_desc'];
            unset($input['subject_desc']);
        }
        $mSubject = new Subject();

        $result = $mSubject->addSubject($input);

        if(!$result){
            return $this->sendError(400,$mSubject->getError());
        }

        return $this->sendSuccess($result);

    }

    public function put(Request $request)
    {
        $input = $request->put();

        $sj_id = input('id/d');

        if(isset($input['subject_desc'])){
            $input['short_desc'] = $input['subject_desc'];
            unset($input['subject_desc']);
        }
        $mSubject = new Subject();

        $result = $mSubject->editSubject($sj_id,$input);

        if(!$result){
            return $this->sendError(400,$mSubject->getError());
        }

        return $this->sendSuccess($result);
    }

    /**
     * @desc  科目的等级
     * @author luo
     * @method GET
     */
    public function get_list_grade(Request $request)
    {
        $sj_id = input('id');
        $get = $request->get();
        $m_sg = new SubjectGrade();
        $ret = $m_sg->where('sj_id', $sj_id)->getSearchResult($get);
        return $this->sendSuccess($ret);
    }


    /**
     * @title 删除科目
     * @desc  根据课程ID删除一个课程
     * @url subjects/:id
     * @method  DELETE
     * @return  返回字段描述
     * @return [type] [description]
     */
    public function delete(Request $request)
    {
        $id = input('id/d');
        
        $m_l = new Lesson;
        $sj_array = $m_l->column('sj_ids');
        $sj_string = implode(',',$sj_array);
        $sj_ids = explode(',',$sj_string);
        $sj_ids = array_unique($sj_ids);

        $subject = Subject::get($id);
        if(empty($subject)){
            return $this->sendError(400,'科目不存在或已经删除');
        }

        if(in_array($id,$sj_ids)){
            return $this->sendError(400,'该科目已绑定课程不能删除');
        }

        $ret = $subject->deleteSubject();

        if($ret !== true){
            return $this->sendError(400,$subject->getError());
        }

        return $this->sendSuccess();

    }


    /**
     * @desc  科目等级
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function post_grade(Request $request)
    {
        $post = $request->post();
        $m_sg = new SubjectGrade();
        $rs = $m_sg->addOneGrade($post);
        if($rs === false) return $this->sendError(400, $m_sg->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  删除科目等级
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function delete_grade(Request $request)
    {
        $sg_ids = input('sg_ids/a');

        $m_sg = new SubjectGrade();
        $rs = $m_sg->where('sg_id', 'in', $sg_ids)->delete();
        if($rs === false) return $this->sendError(400, $m_sg->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  方法描述
     * @author luo
     * @url   /api/lessons/:id/
     * @method PUT
     */
    public function put_grade(Request $request)
    {
        $put = $request->put();
        $sg_id = $put['sg_id'];
        $m_sg = new SubjectGrade();
        $grade = $m_sg->find($sg_id);
        $rs = $grade->allowField(true)->save($put);
        
        if($rs === false) return $this->sendError(400, '更新失败');

        return $this->sendSuccess();
    }



}