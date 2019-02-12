<?php
/**
 * luo
 */
namespace app\api\model;

class Chapter extends Base
{
    protected $name = 'lesson_chapter';

    protected $hidden = ['create_time', 'create_uid', 'is_delete', 'delete_time', 'delete_uid'];

    public function chapterAttachments()
    {
        return $this->hasMany('Attachment', 'lc_id', 'lc_id')
            ->where('cid', 0)
            ->order('chapter_index', 'asc')
            ->order('la_type', 'asc');
    }

    public function attachments()
    {
        return $this->hasMany('Attachment', 'lc_id', 'lc_id')
            ->where('cid', 0);
    }
}

