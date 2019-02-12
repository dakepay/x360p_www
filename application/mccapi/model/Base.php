<?php

namespace app\mccapi\model;

use app\common\exception\FailResult;
use think\Config;
use think\db\Query;
use think\Model;
use traits\model\SoftDelete;
use think\Request;
use think\Exception;
use think\Log;
use app\common\traits\ModelTrait;

class Base extends Model
{
	use SoftDelete;
    use ModelTrait;
    // 数据库查询对象
    protected $query = 'app\\common\\db\\Query';
	protected $autoWriteTimestamp = true;
    protected $deleteTime = 'delete_time';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    protected $hidden = ['create_time', 'create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    //model层操作错误码
    protected $error_code;
    const CODE_HAVE_RELATED_DATA = 600; //有关联数据的错误码
    const CODE_NORMAL_ERR = 400;        //普通错误码

    /**
     * 删除当前的记录
     * @access public
     * @param bool  $force 是否强制删除
     * @return integer
     */
    public function delete($force = false)
    {
        if (false === $this->trigger('before_delete', $this)) {
            return false;
        }
        $name = $this->getDeleteTimeField();
        if (!$force) {
            // 软删除
            $this->data[$name] = $this->autoWriteTimestamp($name);
            $this->isUpdate    = true;
            $result            = $this->save();
        } else {
            $result = $this->getQuery()->delete($this->data);
        }

        $this->trigger('after_delete', $this);
        return $result;
    }


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
        $common_tables = ['dictionary'];
        $name = strtolower($this->name);
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

        $field = $this->getDeleteTimeField(true);
        $query->useSoftDelete($field);
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
            $query->where('og_id', $og_id);
        }
    }

    protected static function before_insert(&$model)
    {
        $fields = $model->getTableFields();

		$uid = gvar('uid');
		if(!$uid){
			$uid = 0;
		}

		if(!isset($model->data['create_uid'])) {
            $model->data['create_uid'] = $uid;
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


    public function getAppend()
    {
        return $this->append;
    }

    /**
     * 用户定义错误
     * @param  [type] $msg [description]
     * @return [type]      [description]
     */
    protected function user_error($msg, $code = 400){
        $this->error = $msg;
        $this->error_code = $code;
        return false;
    }

    public function get_error_code()
    {
        return $this->error_code ? $this->error_code : 0;
    }

    /**
     * @desc  返回错误的描述信息
     */
    public function getErrorMsg()
    {
        return isset($this->error['msg']) ? $this->error['msg'] : (is_string($this->error) ? $this->error : '');
    }

    public function deal_exception($msg, $e) {
        if($e instanceof FailResult) {
            return $this->user_error($msg);
        }

        if(Config::get('app_debug')) {
            throw $e;
        } else {
            return $this->user_error($msg);
        }
    }

    /**
     * 数据库错误
     * @param  [type] $table  [description]
     * @param  [type] $action [description]
     * @return [type]         [description]
     */
    protected function sql_error($table,$action){
        $format = "%s数据库表出现错误,表名[%s],SQL:%s";
        $map = array(
            'insert'=>'写入',
            'update'=>'更新',
            'delete'=>'删除'
        );
        $action_txt = isset($map[$action])?$map[$action]:'操作';
        
        if($table == $this->$table){

            $sql = $this->getLastSql();
        }else{
            $m = 'm_'.$table;
            $sql = $this->$m->getLastSql();
        }
        $sql_error = sprintf($format,$action_txt,$table,$sql);
        if(APP_DEBUG){
            $this->error = $sql_error;
        }else{
            $this->error = '写入数据库时出现错误，请检查是否有未填写的字段！';
            Log::write($sql_error,Log::ERR);
        }
        
        return false;
    }
    /**
     * 数据写入错误
     * @param  [type] $table [description]
     * @return [type]        [description]
     */
    protected function sql_add_error($table){
        $this->sql_error($table,'insert');
        return false;
    }
    /**
     * 数据更新错误
     * @param  [type] $table [description]
     * @return [type]        [description]
     */
    protected function sql_save_error($table){
        $this->sql_error($table,'update');
        return false;
    }

    /**
     * 数据删除错误
     * @param  [type] $table [description]
     * @return [type]        [description]
     */
    protected function sql_delete_error($table){
        $this->sql_error($table,'delete');
        return false;
    }


      /**
     * 启动事务
     * @access public
     * @return void
     */
    public function startTrans()
    {
        $query = $this->db(true, false);
        $is_start_trans = app_reg('is_start_trans');
        if($is_start_trans){
            $is_start_trans++;
            app_reg('is_start_trans',$is_start_trans);
            return $this;
        }
        $query->startTrans();
        $is_start_trans = 1;
        app_reg('is_start_trans',$is_start_trans);
        return $this;
    }

    /**
     * 用于非自动提交状态下面的查询提交
     * @access public
     * @return void
     * @throws PDOException
     */
    public function commit()
    {
        $query = $this->db(true, false);
        $is_start_trans = app_reg('is_start_trans');
        if($is_start_trans && $is_start_trans == 1){
            return $query->commit ();
        }
        if($is_start_trans){
            $is_start_trans--;
            app_reg('is_start_trans',$is_start_trans);
        }
        return $this;
    }

    /**
     * 事务回滚
     * @access public
     * @return void
     * @throws PDOException
     */
    public function rollback()
    {
        $query = $this->db(true, false);
        $is_rollback = app_reg('is_rollback');
        if(!$is_rollback){
            app_reg('is_rollback',$is_rollback);
            return $query->rollback ();
        }
        return $this;
    }


    /**
     * 获得主键值
     * @return [type] [description]
     */
    public function getPkValue($sequence = null){
        $pk = $this->getPk();

        if(isset($this->data[$pk])){
            return $this->data[$pk];
        }

        $insertId = $this->getQuery()->getLastInsID($sequence);

        if($insertId){
            return $insertId;
        }

        return null;

    }

    /**
     * 输入参数错误
     * @param  [type]  $field   [description]
     * @param  integer $is_lost [description]
     * @param  string  $note    [description]
     * @return [type]           [description]
     */
    protected function input_param_error($field,$is_lost = 0,$note = ''){
        if($is_lost == 1){
            $error = '缺少输入参数:'.$field;
        }else{
            $error = '参数错误:'.$field;
        }

        if($note != ''){
            $error = $error.'['.$note.']';
        }
        
        $this->error = $error;
        return false;
    }

    /**
     * 检查输入参数
     * @param  [type] $input  [description]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    protected function checkInputParam(&$input,$params){
        $result = true;
        foreach($params as $param){
            if(!array_key_exists($param, $input) || is_null($input[$param])){
                $result = false;
                $this->input_param_error($param,1);
                break;
            }
        }
        return $result;
    }

}


