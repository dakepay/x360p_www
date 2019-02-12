<?php
/**
 * Author: luo
 * Time: 2018/5/23 10:43
 */

namespace app\api\model;


use app\common\exception\FailResult;
use http\Exception;

class FilePackage extends Base
{
    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function filePackageFile()
    {
        return $this->hasMany('FilePackageFile', 'fp_id', 'fp_id');
    }

    public function employee()
    {
        return $this->hasOne('Employee', 'uid', 'create_uid')
            ->field('eid,ename,uid,mobile,photo_url');
    }

    public function addFilePackage($data)
    {
        try {
            if(isset($data['sid']) && $data['sid'] > 0){
                $sinfo = get_student_info($data['sid']);
                $data['bid'] = $sinfo['bid'];
            }elseif(isset($data['cid']) && $data['cid'] > 0){
                $cinfo = get_class_info($data['cid']);
                $data['bid'] = $cinfo['bid'];
            }
            $this->startTrans();
            $file_package_file = !empty($data['file_package_file']) && is_array($data['file_package_file']) ?
                $data['file_package_file'] : [];
            $file_ids = array_column($file_package_file, 'file_id');
            $files_package_id = $data['files_package_id'] = md5(implode(',', $file_ids));

            $old_file_package = $this->where('files_package_id', $files_package_id)->find();

            if(empty($old_file_package)) {
                $data['short_id'] = short_id();
                $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);
                if($rs === false) throw new FailResult($this->getErrorMsg());

                $fp_id = $this->fp_id;
                if(!empty($file_package_file)) {
                    $file_data = $data['file_package_file'];
                    $m_fpf = new FilePackageFile();
                    $m_file = new File();
                    foreach($file_data as $row_data) {
                        if(isset($row_data['file_id'])) {
                            $file = $m_file->find($row_data['file_id']);
                            if(empty($file)) continue;
                            $file = $file->toArray();
                        }

                        $file['fp_id'] = $fp_id;
                        $rs = $m_fpf->data([])->allowField(true)->isUpdate(false)->save($file);
                        if($rs === false) throw new FailResult('添加文件包文件失败');
                    }
                }
            } else {
                $this->data($old_file_package->getData());
            }

            if(!empty($data['is_push'])) {
                $m_spt = new ServicePushTask();
                $data['rel_id'] = $this->fp_id;
                $data['content_type'] = ServicePushTask::CONTENT_TYPE_FILE_PACKAGE;
                $data['remark'] = isset($this->title) ? $this->title : '文件包';
                $result = $m_spt->addTask($data, true);
                if(false === $result){
                    $this->rollback();
                    return $this->user_error($m_spt->getError());
                }

            }

        } catch(\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();

        return true;
    }

    //编辑
    public function updateFilePackage($data)
    {
        if(empty($this->getData())) return $this->user_error('文件包为空');

        try {
            $this->startTrans();
            $rs = $this->allowField(true)->isUpdate(true)->save($data);
            if ($rs === false) throw new FailResult('更新失败');

            $file_package_file = isset($data['file_package_file']) ? $data['file_package_file'] : [];
            if (!empty($file_package_file) && is_array($file_package_file)) {
                $old_file_ids = $this->filePackageFile()->column('file_id');
                $new_file_ids = array_column($file_package_file, 'file_id');
                $del_file_ids = array_diff($old_file_ids, $new_file_ids);
                $add_file_ids = array_diff($new_file_ids, $old_file_ids);

                $rs = $this->filePackageFile()->where('file_id', 'in', $del_file_ids)->delete();
                if($rs === false) throw new FailResult($this->getErrorMsg());

                $m_file = new File();
                $m_fpf = new FilePackageFile();
                foreach ($add_file_ids as $per_file_id) {
                    $file = $m_file->find($per_file_id);
                    if(empty($file)) continue;

                    $row_data['fp_id'] = $this->fp_id;
                    $row_data = array_merge($row_data, $file->toArray());
                    $rs = $m_fpf->data([])->allowField(true)->isUpdate(false)->save($row_data);
                    if ($rs === false) throw new FailResult($m_fpf->getErrorMsg());
                }
            }

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    public function deleteFilePackage()
    {
        if(empty($this->getData())) return $this->user_error('文件包数据错误');

        try {
            $this->startTrans();

            $rs = $this->filePackageFile()->delete();
            if($rs === false) throw new FailResult($this->getErrorMsg());


            $rs = $this->delete();
            if($rs === false) throw new FailResult($this->getErrorMsg());

            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }


}