<?php

namespace app\sapi\model;

use think\Config;
use think\db\Query;
use think\Request;
use think\Exception;
use think\Log;
use app\common\Model;

class Base extends Model
{
    // 数据库查询对象
    protected $skip_og_id_condition = false;

    //model层操作错误码
    protected $error_code;
    const CODE_HAVE_RELATED_DATA = 600; //有关联数据的错误码
    const CODE_NORMAL_ERR = 400;        //普通错误码

    protected $hidden = [
        'create_time',
        'update_time',
        'is_delete',
        'delete_time',
        'delete_uid'
    ];

	/**
     * 初始化处理
     * @access protected
     * @return void
     */
    protected static function init()
    {
        $model = new static();
    	//更新增加
    	self::beforeInsert(array($model,'before_insert'));
    	//软删除
    	/*
    	默认情况下查询的数据不包含软删除数据，如果需要包含软删除的数据，可以使用下面的方式查询：
		User::withTrashed()->find();
		User::withTrashed()->select();
		如果仅仅需要查询软删除的数据，可以使用：

		User::onlyTrashed()->find();
		User::onlyTrashed()->select();
    	 */
    	self::beforeUpdate(array($model,'before_update'));
    	self::beforeDelete(array($model,'before_delete'));
    }

    /*当前用户uid查询范围*/
    protected function scopeAutoUid($query)
    {
        $uid = Request::instance()->user->uid;
        $query->where('uid', $uid);
    }

    protected function base($query)
    {
        $this->autoOgIdWhere($query);
        $field = $this->getDeleteTimeField(true);
        $query->useSoftDelete($field);
    }

    public function skipOgId($skip = true){
        $this->skip_og_id_condition = $skip;
        $this->getQuery()->skipOgId($skip);
        return $this;
    }


    protected function autoOgIdWhere($query){
        $name = strtolower($this->name);
        $skip_tables = ['org'];
        $common_tables = ['dictionary'];
        if(in_array($name,$skip_tables)){
            return $this;
        }
        if(!$this->skip_og_id_condition){
            $og_id = gvar('og_id');
            if(is_numeric($og_id) && $og_id > 0) {

                if(in_array($name,$common_tables)){
                    $query->where('__TABLE__.og_id','IN',[0,$og_id]);
                }else{
                    $query->where('__TABLE__.og_id', $og_id);
                }
            }elseif($og_id == 0){
                if(in_array($name,$common_tables)){
                    $query->where('__TABLE__.og_id',0);
                }
            }
        }
        return $this;
    }

    protected function scopeBid($query)
    {
        $bid = request()->bid;
        if($bid) {
            $query->where('bid', $bid);
        }
    }

    protected function scopeOgId($query)
    {
        $og_id = gvar('og_id');
        if(is_numeric($og_id) && $og_id > 0) {
            $query->where('__TABLE__.og_id', $og_id);
        }
    }

    protected static function before_insert(&$model)
    {
        $fields = $model->getTableFields();

		$uid = login_info('uid');
		if(!$uid){
			$uid = 0;
		}

		if(!isset($model->data['create_uid'])) {
            $model->data['create_uid'] = $uid;
        }

        if(in_array('og_id', $fields)) {
            $og_id = login_info('og_id');
            if(!$og_id){
                $og_id = 0;
            }
            $model->data['og_id'] = $og_id;
        }

        if(in_array('bid', $fields)) {
            $model->data['bid'] = isset($model->bid) ? $model->data['bid'] : login_info('bid');
        }
        if(isset($model->data['create_time']) && !is_int($model->data['create_time'])) {
            $model->data['create_time'] = time();
        }

        return true;
    }
    
     protected static function before_update(&$model)
    {
        if(isset($model->data['create_time']) && !is_int($model->data['create_time'])) {
            $model->data['create_time'] = strtotime($model->data['create_time']);
        }

        return true;
    }

    protected static function before_delete(&$model)
    {
    	$uid = gvar('uid');
		if(!$uid){
			$uid = 0;
		}
		$model->data['is_delete']  = 1;
		$model->data['delete_uid'] = $uid;
		return true;
    }

    /**
     * 批量删除
     * @param  [type] $ids [description]
     * @return [type]      [description]
     */
    public function batDelete($ids){
        $arr_ids = explode(',',$ids);

        if(empty($arr_ids)){
            return false;
        }
        $this->startTrans();
        try{
          foreach($arr_ids as $id){
            $this->delete($id);
          }  
        }catch(Exception $e){
            $this->rollback();
            $this->user_error($e->getMessage());
            return false;
        }
        
        $this->commit();
        return true;
    }

    /**
     * 恢复被软删除的记录
     * @access public
     * @param array $where 更新条件
     * @return integer
     */
    public function recover($where = [])
    {
        $name = $this->getDeleteTimeField();
        if (empty($where)) {
            $pk         = $this->getPk();
            $where[$pk] = $this->getData($pk);
        }
        $this->data['is_delete']  = 0;
        $this->data['delete_uid'] = 0;
        // 恢复删除
        return $this->getQuery()
            ->useSoftDelete($name, ['not null', ''])
            ->where($where)
            ->update([$name => null]);
    }

    /**
     * 获得资源名称
     * @return [type] [description]
     */
    public function getResName()
    {
        return $this->name;
    }

    /**
     * 获得校区姓名
     * @param  [type] $value [description]
     * @param  [type] &$data [description]
     * @return [type]        [description]
     */
    public function getBranchNameAttr($value, &$data)
    {
        if($value && isset($data['branch_name'])){
            return $value;
        }
        if(!isset($data['bid'])){
            return '';
        }

        $branchs_map = gvar('branchs_map');
        $bid = $data['bid'];
        $branch_name = '';
        if(empty($branchs_map)){
            $branchs_map = [];
            $branchs = m('branch')->select();
            $branchs_map = [];
            foreach($branchs as $row){
                $branchs_map[$row['bid']] = $row;
            }
            gvar('branchs_map',$branchs_map);
        }
        if(isset($branchs_map[$bid])){
            $branch_name = $branchs_map[$bid]['short_name']?$branchs_map[$bid]['short_name']:$branchs_map[$bid]['branch_name'];
        }
        return $branch_name;
    }



}


