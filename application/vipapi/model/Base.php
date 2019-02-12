<?php

namespace app\vipapi\model;

use think\Config;
use think\db\Query;
use think\Request;
use think\Exception;
use think\Log;
use app\common\Model;

class Base extends Model
{

 
    //model层操作错误码
    protected $error_code;
    const CODE_HAVE_RELATED_DATA = 600; //有关联数据的错误码
    const CODE_NORMAL_ERR = 400;        //普通错误码




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
    	self::beforeDelete(array($model,'before_delete'));
    }

    /*当前用户uid查询范围*/
    protected function scopeAutoUid($query)
    {
        $uid = Request::instance()->user->uid;
        $query->where('uid', $uid);
    }


    protected static function before_insert(&$model)
    {
		$uid = gvar('uid');
		if(!$uid){
			$uid = 0;
		}
		$model->data['create_uid'] = $uid;

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



}


