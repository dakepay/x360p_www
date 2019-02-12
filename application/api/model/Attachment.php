<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/6/28
 * Time: 11:21
 */
namespace app\api\model;

class Attachment extends Base
{
    protected $name = 'lesson_attachment';

    public function cls()
    {
        return $this->belongsTo('Classes', 'cid', 'cid')->field(['cid', 'class_name', 'class_no', 'teach_eid']);
    }

    public function deleteAttachment()
    {
        $this->delete();
        $file_id = $this->getData('file_id');
        File::get($file_id)->delete();
        File::deleteFile($file_id);
    }


    /**
     * @param $input
     * @desc (标准件附件)添加课程附件&添加章节附件的公共方法，根据传递过来的参数判断
     * @return false|int
     */
    public function addStdAttach($input)
    {
        $input['is_lesson_std'] = 1;
        $where = [];
        $where['la_type'] = $input['la_type'];
        $rule = '';
        if ($input['lc_id'] && $input['lid']) {
            $where['lc_id'] = $input['lc_id'];
            $rule = 'LessonAttachment.chapter';
        }
        if ($input['lid'] && !$input['lc_id']) {
            $where['lid'] = $input['lid'];
            $rule = 'LessonAttachment.lesson';
        }
        $attach = $this->where($where)->find();
        if ($attach) {
            $attach->delete();
        }
        return $this->allowField(true)->validate($rule)->save($input);
    }

    public function addGeneralAttach($input)
    {
        $input['is_lesson_std'] = 0;
        $input['la_type'] = 0;
        if ($input['lc_id'] && $input['lid']) {
            $rule = 'LessonAttachment.chapter';
        }
        if ($input['lid'] && !$input['lc_id']) {
            $rule = 'LessonAttachment.lesson';
        }
        return $this->allowField(true)->validate($rule)->save($input);
    }

}
