<?php

namespace app\admapi\model;

class Area extends Base
{
	public function addArea($input){
        $need_fields = ['name','parent_id','level'];
        if (!$this->checkInputParam($input,$need_fields)){
            return false;
        }
        $rs = $this->allowField(true)->isUpdate(false)->save($input);
        if(!$rs){
            return $this->user_error('添加失败:'.$rs->getError());
        }

        return true;
    }

    public function updateArea($input){
        if(empty($input['area_id'])){
            return $this->user_error('area_id error!');
        }
        $area = $this->get($input['area_id']);
        if(empty($area)){
            return $this->user_error('地区不存在!');
        }
        $update_area['area_id'] = $input['area_id'];
        unset($input['area_id']);
        $rs = $this->save($input,$update_area);

        if(false === $rs){
            return $this->sql_save_error('area');
        }
        return true;
    }

    public function delArea($area_id){
        if(empty($area_id)){
            return $this->user_error('area_id empty!');
        }
        $w['area_id'] = $area_id;
        $rs = $this->where($w)->delete(true);
        if(false === $rs){
            return $this->sql_save_error('area');
        }
        return true;
    }

}
