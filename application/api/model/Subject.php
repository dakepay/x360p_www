<?php
/**
 * Class: 科目
 * Author: luo
 * Time: 20171007
 */
namespace app\api\model;

class Subject extends Base
{

    /**
     * 创建新科目
     * @param $input
     * @return bool|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addSubject($input)
    {
        $need_fields = ['subject_name'];

        if(!$this->checkInputParam($input,$need_fields)){
            return false;
        }

        $w['subject_name'] = $input['subject_name'];

        $ex_subject = $this->where($w)->find();
        if($ex_subject){
            return $this->user_error(sprintf("科目:%s已经存在!",$input['subject_name']));
        }

        $result = $this->allowField(true)->save($input);

        if(!$result){
            return $this->sql_add_error('subject');
        }

        return $this->getData();
    }

    /**
     * 编辑科目
     * @param $sj_id
     * @param $input
     * @return bool|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editSubject($sj_id,$input)
    {
        $sj_info = $this->where('sj_id',$sj_id)->find();

        if(!$sj_info){
            return $this->input_param_error('sj_id');
        }

        if(isset($input['subject_name']) && $input['subject_name'] != $sj_info['subject_name']){
            //判断是否重复
            $w['subject_name'] = $input['subject_name'];
            $w['sj_id'] = ['NEQ',$sj_id];

            $ex_subject = $this->where($w)->find();

            if($ex_subject){
                return $this->user_error(sprintf("科目:%s已经存在!",$input['subject_name']));
            }
        }

        $w_update['sj_id'] = $sj_id;
        $result = $this->isUpdate(true)->allowField(true)->save($input,$w_update);

        if(false === $result){
            $this->rollback();
            return $this->sql_save_error('subject');
        }

        return $this->getData();
    }

    public function subjectGrade()
    {
        return $this->hasMany('SubjectGrade', 'sj_id', 'sj_id');
    }


    public function deleteSubject(){

        $this->startTrans();
    	try {
            $rs = $this->delete();
            if($rs === false) return $this->user_error('删除科目失败');
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            $this->error = $e->getMessage();
            return false;
        }
        return true;
    }

}