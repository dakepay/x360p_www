<?php
/**
 * Author: luo
 * Time: 2018/3/26 10:16
 */

namespace app\sapi\model;


class HomeworkAttachment extends Base
{
    const ATT_TYPE_HOMEWORK = 0;
    const ATT_TYPE_COMPLETE = 1;
    const ATT_TYPE_REPLY = 2;

    public function taskFile()
    {
        return $this->hasOne('File','file_id', 'file_id')
            ->field('file_id,file_url,file_type,file_name,file_size,duration');
    }

    public function completeFile()
    {
        return $this->hasOne('File','file_id', 'file_id')
            ->field('file_id,file_url,file_type,file_name,file_size,duration');
    }

}