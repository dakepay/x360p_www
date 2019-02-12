<?php
namespace app\admapi\model;

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

		return $this->file_id;

	}


    /**
     * @param $file_id 主键id
     * @desc  删除上传文件的数据库记录和实体文件
     * @return boolean
     */
	public static function deleteFile($file_id)
    {
        $file = self::get($file_id);
        if (!$file) {
            return false;
        }
        @unlink($file->local_file);
        $file->delete();
    }


}