<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/10/17
 * Time: 17:31
 */
namespace app\api\model;

use app\common\Wechat;
use think\Log;

class WxmpMenu extends Base
{
    protected $type = [
        'buttons' => 'json',
        'matchrule' => 'json',
    ];

    protected $skip_og_id_condition = true;

    //protected function getMatchruleAttr($value) {
    //    return is_null($value) ? [] : $value;
    //}

    public function sync($appid)
    {
        $w = [];
        $w['status'] = 0;
        $w['authorizer_appid'] = $appid;
        $wxmp = Wxmp::get($w);
        if (!$wxmp) {
            return $this->user_error('微信公众号还未授权或已经被管理员取消授权!appid:'.$appid);
        }
        $wxmp_id = $wxmp['wxmp_id'];
        try {
            $menu   = Wechat::getApp($appid)->menu;
            $menus  = $menu->all();

            if($this->hasEmptyButtons($menus['menu']['button'])){
                return $this->user_error('微信公众号现有微信菜单包含了微信公众平台内置的图文消息回复，无法通过接口同步，请手动创建菜单!');
            }

            $buttons  = $this->filterInvalidMenu($menus['menu']['button']);
            $insert_data = [];
            //默认菜单
            if(isset($menus['menu'])) {
                $insert_data[0] = [
                    'menuid' => isset($menus['menu']['menuid']) ? $menus['menu']['menuid'] : 0,
                    'buttons' =>$buttons,
                    'group_name' => '微信同步:' . date('Y-m-d H:i:s', time()),
                    'status' => 1,
                    'wxmp_id' => $wxmp_id,
                    'appid' => $appid,
                ];
            }
            //有相应规则的菜单
            if(isset($menus['conditionalmenu'])) {
                foreach ($menus['conditionalmenu'] as $key => $per_menu) {
                    $tmp_key = $key + 1;
                    $buttons = $this->filterInvalidButtons($per_menu['button']);
                    $insert_data[$key + 1] = [
                        'menuid'    =>  $per_menu['menuid'],
                        'buttons'   =>  $buttons,
                        'matchrule' =>  $per_menu['matchrule'],
                        'group_name' => '微信同步:' . $tmp_key . date('Y-m-d H:i:s', time()),
                        'status' => 1,
                        'wxmp_id' => $wxmp_id,
                        'appid' => $appid,
                    ];
                }
            }

            self::update(['status' => 0], ['wxmp_id' => $wxmp_id]);
            $this->saveAll($insert_data);
        } catch (\Exception $e) {
            return $this->exception_error($e);
        }

        return true;
    }

    /*更新微信服务器保存的菜单配置*/
    private function updateMenu($wxmp_id, $wm_id = null)
    {
        $menu   = Wechat::getApp()->menu;
        $buttons = $this->getAttr('buttons');
        $matchrule = $this->getAttr('matchrule');
        $matchrule = !empty($matchrule) ? $matchrule : [];
        if(!empty($matchrule)) {
           $matchrule = array_filter($matchrule);
        }
        $data = $menu->add($buttons, $matchrule);
        if(!is_null($wm_id)) {
            $old_menuid = $this->getData('menuid');
            if($old_menuid) {   # 删除原来的菜单
                try {
                    $menu->destroy($old_menuid);
                } catch (\Exception $e) {
                    Log::record($e->getMessage(), 'wechat');
                }

            }
        }
        if(isset($data['menuid'])) {
            $this->isUpdate(true)->save(['menuid' => $data['menuid']]);
        }
        return true;
    }

    public function addMenu($input)
    {
        $this->startTrans();
        try {
            $where['wxmp_id'] = $input['wxmp_id'];
            if(!isset($input['matchrule']) || empty($input['matchrule'])) {
                $where['matchrule'] = null;
                self::update(['status' => 0], $where);
            }
            $input['status'] = 1;
            $this->allowField(true)->save($input);
            $this->updateMenu($input['wxmp_id']);
        } catch (\Exception $exception) {
            $this->rollback();
            //return $this->user_error($exception->getMessage());
            throw $exception;
        }
        $this->commit();
        return true;
    }

    public function editMenu($input)
    {
        $this->startTrans();
        try {
            $wxmp_id = $this->getData('wxmp_id');
            //$matchrule = $this->getData('matchrule');
            $matchrule = isset($input['matchrule']) ? $input['matchrule'] : [];
            $where['wxmp_id'] = $wxmp_id;
            /*if(empty($matchrule)) {
                $where['matchrule'] = null;
                self::update(['status' => 0], $where);
            }*/
            // $input['status'] = 1;
            $this->allowField(['group_name', 'buttons', 'matchrule'])->save($input);
            $this->updateMenu($wxmp_id, $this->getData('wm_id'));
        } catch (\Exception $exception) {
            $this->rollback();
            return $this->user_error($exception->getMessage());
        }
        $this->commit();
        return true;
    }

    //删除菜单
    public function deleteMenu()
    {
        //if ($this->getData('status')) {
        //    return $this->user_error('该菜单组正在使用中,无法删除！');
        //}
        $menuid = $this->getData('menuid');
        $rs = $this->delete();
        if($rs === false) return $this->user_error('删除失败');
        if($menuid > 0) {
            $menu = $this->where('menuid', $menuid)->where('status = 1')->find();
            if(empty($menu)) {
                try {
                    $menu   = Wechat::getApp()->menu;
                    $menu->destroy($menuid);
                } catch (\Exception $e) {
                    Log::record($e->getMessage(), 'wechat');
                }
            }
        }
        return true;
    }

    public function active()
    {
        $this->startTrans();
        try {
            $wxmp_id = $this->getData('wxmp_id');
            $where['wxmp_id'] = $wxmp_id;
            $matchrule = $this->getData('matchrule');
            if(empty($matchrule)) {
                $where['matchrule'] = null;
                self::update(['status' => 0], $where);
            }

            $input['status'] = 1;
            $this->allowField(true)->save($input);
            $this->updateMenu($wxmp_id);
        } catch (\Exception $exception) {
            $this->rollback();
            return $this->user_error($exception->getMessage());
        }

        $this->commit();
        return true;
    }

    public function hasEmptyButtons($buttons){
        $ret = false;
        foreach($buttons as $button){
            if(!isset($button['name'])){
                $ret = true;
                break;
            }
            foreach($button['sub_button'] as $sb){
                if(!isset($sb['name'])){
                    $ret = true;
                    break;
                }

            }
        }
        return $ret;
    }

    public function filterInvalidButtons(&$buttons){
        $ret = [];
        foreach($buttons as $button){

            if(!isset($button['name'])){
                continue;
            }
            $sub_buttons = [];
            foreach($button['sub_button'] as $sb){
                if(!isset($sb['name'])){
                    continue;
                }
                array_push($sub_buttons,$sb);
            }

            $button['sub_button'] = $sub_buttons;

            if(empty($button['sub_button'])){
                unset($button['sub_button']);
            }

            array_push($ret,$button);

        }
        return $ret;
    }
}