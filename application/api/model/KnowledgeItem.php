<?php
/**
 * Author: luo
 * Time: 2018/5/29 10:02
 */

namespace app\api\model;


use app\common\exception\FailResult;
use think\Exception;

class KnowledgeItem extends Base
{
    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];

    protected $insert = ['create_eid'];
    protected $append = ['create_employee_name'];

    public function setKeywordsAttr($value)
    {
        return is_array($value) ? implode(',', $value) : $value;
    }

    public function setCreateEidAttr($value)
    {
        $eid = User::getEidByUid(gvar('uid'));
        return $eid;
    }

    public function getKeywordsAttr($value)
    {
        return $value ? explode(',', $value) : [];
    }



    public static function UpdateStars($ki_id)
    {
        $star_num = (new KnowledgeItemLike())->where('ki_id', $ki_id)->count();
        (new self())->where('ki_id', $ki_id)->update(['stars' => $star_num]);

        return true;
    }
    
    public function delKnowledgeItem()
    {
        if(empty($this->getData())) return $this->user_error('知识库模型数据错误');

        try {
            $this->startTrans();
            $rs = (new KnowledgeItemLike())->where('ki_id', $this->ki_id)->delete();
            if($rs === false) throw new FailResult('相关点赞删除失败');
            
            $rs = $this->delete();
            if($rs === false) throw new FailResult($this->getErrorMsg());

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }


        return true;
    }

    /**
     * 添加知识库
     * @param [type] $knowledge_data [description]
     * @param [type] $knowledge_file [description]
     */
    public function addOneKnowledge($knowledge_data,$knowledge_file = [])
    {
        $this->startTrans();
        try{

            $ret = $this->data([])->isUpdate(false)->allowField(true)->save($knowledge_data);
            if($ret === false){
                return $this->user_error('添加知识失败');
            }

            $ki_id = $this->getAttr('ki_id');

            //  添加知识库附件
            if(!empty($knowledge_file)){
                $m_file = new File();
                $m_kif = new KnowledgeItemFile();
                foreach ($knowledge_file as $per_file) {
                    if(empty($per_file['file_id'])) {
                        log_write($per_file, 'error');
                        continue;
                    }
                    $file = $m_file->find($per_file['file_id']);
                    $file = $file ? $file->toArray() : [];
                    $per_file = array_merge($per_file,$file);
                    $per_file['ki_id'] = $ki_id;
                    $ret = $m_kif->data([])->isUpdate(false)->allowField(true)->save($per_file);
                    if($ret === false){
                        return $this->user_error('添加知识附件失败');
                    }
                }
            }

        }catch(Exception $e){
            $this->rollback();
            return $this->deal_exception($e->getMessage(),$e);
        }

        $this->commit();
        return true;

    }

    /**
     * 编辑知识库
     * @param  [type] $knowledge_data [description]
     * @param  [type] $knowledge_file [description]
     * @return [type]                 [description]
     */
    public function edit($knowledge_data,$knowledge_file = [])
    {
        $this->startTrans();
        try{

            $ret = $this->allowField(true)->isUpdate(true)->save($knowledge_data);
            if ($ret === false){
                return $this->user_error('更新失败');
            }
            // 更新 附件 (删除之前的附件记录，添加新的附件记录)
            $ki_id = $this->getAttr('ki_id');

            $m_kif = new KnowledgeItemFile();

            $delete = $m_kif->where('ki_id',$ki_id)->delete();
            if($delete === false){
                return $this->user_error('删除原附件失败');
            }

            if(!empty($knowledge_file)){
                $m_file = new File();
                foreach ($knowledge_file as $per_file) {
                    if(empty($per_file['file_id'])) {
                        log_write($per_file, 'error');
                        continue;
                    }
                    $file = $m_file->find($per_file['file_id']);
                    $file = $file ? $file->toArray() : [];
                    $per_file = array_merge($per_file,$file);
                    $per_file['ki_id'] = $ki_id;
                    $ret = $m_kif->data([])->isUpdate(false)->allowField(true)->save($per_file);
                    if($ret === false){
                        return $this->user_error('更新附件失败');
                    }
                }
            }
            

        }catch(Exception $e){
            $this->rollback();
            return $this->deal_exception($e->getMessage(),$e);
        }

        $this->commit();
        return true;
    }



    public function KnowledgeItemFile()
    {
        return $this->hasMany('knowledge_item_file','ki_id','ki_id');
    }
    

}