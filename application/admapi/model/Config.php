<?php
namespace app\admapi\model;

class Config extends Base
{
    protected $type = [
        'cfg_value' => 'json'
    ];

    protected $soft_delete = false;     //不使用软删除

    protected $hidden = ['format', 'create_time', 'create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    /**
     * @param $input
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addConfig($input)
    {

        $w['cfg_name'] = $input['cfg_name'];
        $m_cfg = $this->where($w)->find();
        if($m_cfg){
            $w_cfg_update['cfg_id'] = $m_cfg->cfg_id;
            $result = $this->allowField(true)->save($input,$w_cfg_update);
        }else{
            $result = $this->allowField(true)->save($input);
        }

        if ($result === false) {
            return false;
        }

        return true;
    }

    /***
     * @param $input
     * @return bool
     */
    public function uodateConfig($input)
    {

        $result = $this->allowField(true)->isUpdate(true)->save($input);
        if ($result === false) {
            return false;
        }
        return true;
    }

    /***
     * @return bool
     */
    public function deleteConfig($id){
        $config = $this->get($id);
        if (!$config) {
            return $this->user_error(400, 'config not exists');
        }

        $result = $config->delete(true);
        if(false == $result){
            return $this->sql_delete_error('config');
        }

        return true;
    }


    //获取特定的配置
    public static function get_config($cfg_name)
    {
        $config = (new self())->where('cfg_name', $cfg_name)->find();
        return $config ? $config->toArray() : [];
    }


}