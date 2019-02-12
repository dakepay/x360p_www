<?php

namespace app\admapi\model;

class App extends Base
{
	public function addApp($input){
        $need_fields = ['app_ename','app_name','app_uri','app_icon_uri','app_desc','price_type'];
        if (!$this->checkInputParam($input,$need_fields)){
            return false;
        }
        $rs = $this->allowField(true)->isUpdate(false)->save($input);
        if(!$rs){
            return $this->user_error('添加失败:'.$rs->getError());
        }

        return true;
    }

    public function updateApp($input){
        if(empty($input['app_id'])){
            return $this->user_error('app_id error!');
        }
        $app = $this->get($input['app_id']);
        if(empty($app)){
            return $this->user_error('应用不存在!');
        }

        $update_app['app_id'] = $input['app_id'];
        $update = [
            'app_ename' => $input['app_ename'],
            'app_name' => $input['app_name'],
            'app_uri' => $input['app_uri'],
            'app_icon_uri' => $input['app_icon_uri'],
            'price_type' => $input['price_type'],
            'year_price' => $input['year_price'],
            'volume_price' => $input['volume_price'],
        ];
        $rs = $this->save($update,$update_app);

        if(false === $rs){
            return $this->sql_save_error('app');
        }
        return true;
    }

    public function delApp($app_id){
        if(empty($app_id)){
            return $this->user_error('app_id empty!');
        }
        $w['app_id'] = $app_id;
        $rs = $this->where($w)->delete(true);
        if(false === $rs){
            return $this->sql_save_error('app');
        }
        return true;
    }

}
