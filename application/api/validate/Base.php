<?php
/**
 * luo
 */
namespace app\api\validate;

use think\Validate;

class Base extends Validate
{
    protected $validate_class_name;

    public function getError()
    {
        $this->validate_class_name = !empty($this->validate_class_name) ? $this->validate_class_name . ': ' : '';
        return $this->validate_class_name . $this->error;
    }

}