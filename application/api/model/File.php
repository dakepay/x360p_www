<?php
namespace app\api\model;

use app\common\exception\FailResult;
use Curl\Curl;
use think\Exception;

class File extends Base
{

	/**
	 * 添加图片
	 * @param [type] &$input [description]
     * @return integer
	 */
	public function addFile(&$input){
		$this->data($input,true);

		$this->allowField(true)->save();

		return $this;

	}


    /**
     * @param $file_id 主键id
     * @desc  删除上传文件的数据库记录和实体文件
     * @return boolean
     */
    //public static function deleteFile($file_id)
    //{
    //    $file = self::get($file_id);
    //    if (!$file) {
    //        return false;
    //    }
    //    @unlink($file->local_file);
    //    $file->delete();
    //}

    public function delFile()
    {
        if(empty($this->getData())) {
            return $this->user_error('文件数据错误');
        }

        if(CoursePrepareAttachment::get(['file_id' => $this->file_id])) {
            return $this->user_error('有相关备课使用此文件，无法删除！');
        }

        if(HomeworkAttachment::get(['file_id' => $this->file_id])) {
            return $this->user_error('有相关作业使用此文件，无法删除！');
        }

        if(StudentArtworkAttachment::get(['file_id' => $this->file_id])) {
            return $this->user_error('有相关作品使用此文件，无法删除！');
        }

        if(StudentReturnVisitAttachment::get(['file_id' => $this->file_id])) {
            return $this->user_error('有相关回访使用此文件，无法删除！');
        }

        if(LessonStandardFileItem::get(['file_id' => $this->file_id])) {
            return $this->user_error('有相关的课标使用此文件，无法删除！');
        }

        if(ReviewFile::get(['file_id' => $this->file_id])) {
            return $this->user_error('有相关课评使用此文件，无法删除！');
        }

        $rs = $this->delete();
        if($rs === false) return false;

        return true;
    }

    public function delFiles(array $file_ids)
    {
        $msg = [
            'fail_num' => 0,
            'error' => [],
        ];

        foreach($file_ids as $file_id) {
            $file = $this->where('file_id', $file_id)->find();
            if(empty($file)) continue;
            $rs = $file->delFile();
            if($rs === false) {
                $msg['fail_num'] += 1;
                $tmp = [
                    'file_id' => $file_id,
                    'file_name' => $file['file_name'],
                    'msg' => $file->getErrorMsg(),
                ];
                $msg['error'][] = $tmp;
            }
        }

        return $msg;
    }

    public function getFileDuration($file)
    {
        if(empty($file['media_type']) || empty($file['file_url'])) return $file;

        if($file['media_type'] == 'voice' || $file['media_type'] == 'video') {
            $curl = new Curl();
            $curl->get($file['file_url'] . '?avinfo');
            if (!$curl->error) {
                $voice_info = json_decode($curl->response, true);
                $file['duration'] = $voice_info['streams'][0]['duration'];
            }
        }
        return $file;
    }

}