<?php
namespace app\ftapi\model;

class CenterWechatFans extends Base
{
    protected $connection = 'db_center';

    protected $skip_og_id_condition = true;
    const SUBSCRIBE   = 1;
    const UNSUBSCRIBE = 0;

    protected $type = [
        'tagid_list' => 'array',
    ];

    public static function accountBinding(array $data)
    {
        $w = [];
        $w['appid']  = $data['appid'];
        $w['openid'] = $data['openid'];
        $w['cid']    = $data['cid'];
        $w['uid']    = $data['uid'];
        $model = self::get($w);
        if (empty($model)) {
            $model = new self();
            $model->allowField(true)->save($data);
        }
        return $model;
    }

    public function unsubscribe()
    {
        $data = [];
        $data['cid'] = 0;
        $data['og_id'] = 0;
        $data['bid'] = 0;
        $data['uid'] = 0;
        $data['employee_uid'] = 0;
        $data['subscribe'] = self::UNSUBSCRIBE;
        $data['unsubscribe_time'] = request()->time();
        //$this->allowField(true)->save($data);
        $this->delete(true);
        return $this;
    }
}