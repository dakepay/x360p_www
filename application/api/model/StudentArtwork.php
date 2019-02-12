<?php
/**
 * Author: luo
 * Time: 2018/4/9 9:35
 */

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;

class StudentArtwork extends Base
{

    protected $hidden = ['create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function student()
    {
        return $this->hasOne('Student', 'sid', 'sid')->field('sid,sex,student_name,photo_url');
    }

    public function studentArtworkAttachment()
    {
        return $this->hasMany('StudentArtworkAttachment', 'sart_id', 'sart_id');
    }

    public function studentArtworkReview()
    {
        return $this->hasOne('StudentArtworkReview', 'sart_id', 'sart_id');
    }

    //添加作品
    public function addArtwork($data)
    {
        try {
            $this->startTrans();

            if(empty($data['art_name'])) throw new FailResult('作品名称不能为空');


            $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
            if ($rs === false) throw new FailResult($this->getErrorMsg());

            $sart_id = $this->sart_id;

            if (!empty($data['student_artwork_attachment']) && is_array($data['student_artwork_attachment'])) {
                $attachment_data = $data['student_artwork_attachment'];
                $m_saa = new StudentArtworkAttachment();
                $m_file = new File();
                foreach ($attachment_data as $row_data) {
                    if (isset($row_data['file_id']) && !empty($row_data['file_id'])) {
                        $file = $m_file->find($row_data['file_id']);
                        if(empty($file)) throw new FailResult('文件不存在,file_id:'.$row_data['file_id']);
                        $row_data = array_merge($row_data, $file->toArray());
                    } else {
                        continue;
                    }
                    $row_data['sart_id'] = $sart_id;
                    $rs = $m_saa->data([])->allowField(true)->isUpdate(false)->save($row_data);
                    if ($rs === false) throw new FailResult('添加作品附件失败');
                }
            }

            if(!empty($data['hc_id'])) {
                $m_hc = new HomeworkComplete();
                $rs = $m_hc->where('hc_id', $data['hc_id'])->update(['sart_id' => $sart_id]);
                if($rs === false) throw new FailResult($m_hc->getErrorMsg());
            }

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    //编辑
    public function edit($data, $attachment_data = [])
    {

        if(empty($this->getData())) return $this->user_error('作品数据为空');

        try {
            $this->startTrans();

            $rs = $this->allowField(true)->isUpdate(true)->save($data);
            if ($rs === false) throw new FailResult('更新失败');

            $old_file_ids = $this->studentArtworkAttachment()->column('file_id');
            $new_file_ids = array_column($attachment_data, 'file_id');
            $del_file_ids = array_diff($old_file_ids, $new_file_ids);
            $add_file_ids = array_diff($new_file_ids, $old_file_ids);

            $rs = $this->studentArtworkAttachment()->where('file_id', 'in', $del_file_ids)->delete();
            if($rs === false) throw new FailResult('删除原附件失败');

            $m_file = new File();
            $m_saa = new StudentArtworkAttachment();
            foreach ($add_file_ids as $per_file_id) {
                $file = $m_file->find($per_file_id);
                if(empty($file)) throw new FailResult('文件不存在');

                $row_data['sart_id'] = $this->sart_id;
                $row_data = array_merge($row_data, $file->toArray());
                $rs = $m_saa->data([])->allowField(true)->isUpdate(false)->save($row_data);
                if ($rs === false) throw new FailResult('添加作品附件失败');

            }

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    ///删除备课
    public function delArtwork($sart_id)
    {
        /** @var StudentArtwork $artwork */
        $artwork = $this->find($sart_id);
        if(empty($artwork)) return $this->user_error('备课不存在');

        try {
            $this->startTrans();

            $artwork->studentArtworkAttachment()->delete();
            $artwork->delete();

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

}