<?php

namespace app\common\traits;

use app\common\exception\FailResult;

trait ModelTrait{
	/**
	 * [__get description]
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	
	protected $m_val = [];

	public function __get($name){
        if(substr($name,0,2) == 'm_'){ 
        	if(isset($this->m_val[$name])){
        		return $this->m_val[$name];
        	}
        	$this->m_val[$name] = m(substr($name,2));
        	return $this->m_val[$name];
        }
        return parent::__get($name);
    }


    public function getAppend()
    {
        return $this->append;
    }

    /**
     * 抛出错误处理
     * @param  Exception $e [description]
     * @return [type]       [description]
     */
    protected function exception_error(\Exception $e){
        $this->error = $e->getMessage();
        log_write($this->format_exception($e),'error');
        //$this->error = $e->getMessage().print_r($e->getTrace()[0],true);
        $this->error_code = $e->getCode();
        return false;
    }

    private function format_exception(\Exception $e){
        $traces = $e->getTrace();
        $ret = [];
        foreach($traces as $row){
            if(isset($row['line']) && isset($row['file'])){
                array_push($ret,['file'=>$row['file'],'line'=>$row['line']]);
            }
        }
        return $ret;
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

        if(config('app_debug')) {
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
        //$format = "%s数据库表出现错误,表名[%s],SQL:%s";
        $format = "%s数据库表出现错误,表名[%s]";
        $map = array(
            'insert'=>'写入',
            'update'=>'更新',
            'delete'=>'删除'
        );
        $action_txt = isset($map[$action])?$map[$action]:'操作';

        /*
        if($table == $this->name){

            $sql = $this->getLastSql();
        }else{
            $m = 'm_'.$table;
            $sql = $this->$m->getLastSql();
        }

        $sql_error = sprintf($format,$action_txt,$table,$sql);
        */
        $sql_error = sprintf($format,$action_txt,$table);
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

}