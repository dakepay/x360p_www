<?php
namespace app\api\model;

class FtReviewFile extends Base
{

    public function file()
    {
        return $this->hasOne('File', 'file_id', 'file_id');
    }


}