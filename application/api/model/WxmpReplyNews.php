<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/10/21
 * Time: 14:52
 */
namespace app\api\model;

class WxmpReplyNews extends Base
{
    public $append = ['material'];
    public function getMaterialAttr($value, $data)
    {
        return WxmpMaterial::get(['media_id' => $data['media_id']], ['items']);
    }

    protected $skip_og_id_condition = true;
}