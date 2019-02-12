<?php
namespace app\ftapi\model;

use app\common\Wechat;
use EasyWeChat\Message\Image;
use EasyWeChat\Message\Text;
use EasyWeChat\Message\Video;
use EasyWeChat\Message\Voice;
use EasyWeChat\Message\Material;
use think\Exception;
use think\Log;
use think\Validate;

class WxmpFans extends Base
{
    protected $type = [
        'tagid_list' => 'array',
    ];

    const SUBSCRIBE   = 1;
    const UNSUBSCRIBE = 0;

    protected $skip_og_id_condition = true;

    public function user()
    {
        return $this->hasOne('User', 'uid', 'uid')->field('uid,account,mobile,user_type');
    }

    public function employeeUser()
    {
        return $this->hasOne('User', 'uid', 'employee_uid')->field('uid,account,mobile,user_type');
    }

}