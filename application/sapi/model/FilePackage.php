<?php
/**
 * Author: luo
 * Time: 2018/5/29 10:53
 */

namespace app\sapi\model;


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


}