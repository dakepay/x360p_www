<?php

namespace app\api\model;

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

    protected $skip_bid_condition = false;

    public $skip_insert_bid = false;


    protected static $auto_demo_field_tables = [
        'order_item',
        'order_receipt_bill',
        'course_arrange',
        'student_attendancex',
        'class_attendance',
        'student_lesson',
        'student_lesson_hour',
        'employee_lesson_hour',
        'order_payment_history',
        'tally'
    ];

    protected $hidden = [
        'create_time',
        'update_time', 
        'is_delete', 
        'delete_time', 
        'delete_uid'
    ];

    //model层操作错误码
    protected $error_code;
    const CODE_HAVE_RELATED_DATA = 600; //有关联数据的错误码
    const CODE_NORMAL_ERR = 400;        //普通错误码


    /**
     *  初始化模型
     * @access protected
     * @return void
     */
    protected function initialize()
    {
        parent::initialize();
        $table = \think\Loader::parseName($this->name);
        if(in_array($table,self::$auto_demo_field_tables)){
            $this->autoDemoField();
        }
    }

    /**
     * 自动处理is_demo字段
     */
    protected function autoDemoField(){
        if(!in_array('is_demo',$this->auto)){
            array_push($this->auto,'is_demo');
        }
    }


    public function skipInsertBid($skip = true){
        $this->skip_insert_bid = $skip;
        return $this;
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
        $this->autoOgIdWhere($query);
        $field = $this->getDeleteTimeField(true);
        $query->useSoftDelete($field);
    }

    public function skipOgId($skip = true){
        $this->skip_og_id_condition = $skip;
        $this->getQuery()->skipOgId($skip);
        return $this;
    }

    public function skipBid($skip = true){
        $this->skip_bid_condition = $skip;
        $this->getQuery()->skipBid($skip);
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
                $query->where('__TABLE__.og_id',0);
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

    protected function scopeBids($query)
    {
        $bid = input('bids');
        $bid = !empty($bid) ? $bid : request()->header('x-bid');
        if($bid) {
            if(strpos($bid,',') !== false){
                $bids = array_filter(explode(',',$bid));
                if(!empty($bids)) {
                    $where = array_reduce($bids, function($where, $val){
                        $where[] = "find_in_set($val, bids)";
                        return $where;
                    });
                    $where_bids = implode(' or ', $where);
                }

            }else{
                $bid = intval($bid);
                if($bid !== -1){
                    $where_bids = "find_in_set($bid, bids)";
                }
            }

            if(isset($where_bids)) {
                $query->where($where_bids);
            }

        }
    }

    /*老师或导师所在的班级*/
    protected function scopeAutoCid($query, $status = null)
    {
        $class_ids = Request::instance()->user->employee->getClassIds($status);
        if (empty($class_ids)) {
            $class_ids = [0];
        }
        $query->whereIn('cid', $class_ids);
    }

    /*当前用户默认学生所在的班级*/
    protected function scopeAutoStudentCid($query, $status = null)
    {
        $sid = Request::instance()->user->default_sid;
        $student = Student::get($sid);
        if ($student) {
            $class_ids = $student->getClassIds($status);
        }

        if (empty($class_ids)) {
            $class_ids = [0];
        }
        $query->whereIn('cid', $class_ids);
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

        if(in_array('og_id', $fields)) {
            if(!isset($model->data['og_id']) || $model->data['og_id'] <= 0) {
                $og_id = gvar('og_id');
                if(!$og_id){
                    $og_id = 0;
                }
                $model->data['og_id'] = $og_id;
            }
        }

        if(in_array('bid', $fields) && !$model->skip_insert_bid) {
            $data_bid = isset($model->bid) ? $model->data['bid'] : 0;
            if($data_bid <= 0) {
                $data_bid = request()->param('bid') ?: request()->bid;
                if(is_null($data_bid)){
                    $data_bid = 0;
                }
            }
    	    if($data_bid < 0){
    	    	$data_bid = 0;
    	    }

            //临时处理方案，避免出现校区ID为0 的情况
            if($data_bid == 0){
                $model->checkTableWhenBidEqualZero($model);
            }
    	    $model->data['bid'] = $data_bid;
        }

        if(isset($model->data['create_time']) && !is_int($model->data['create_time'])) {
            $model->data['create_time'] = time();
        }

        if(isset($model->data['update_time']) && !is_int($model->data['update_time'])) {
            $model->data['update_time'] = time();
        }

        return true;
    }

    private function checkTableWhenBidEqualZero($model)
    {

        $table = $model->getTable();
        $not_need_bid_table = [
            'sms_history',
            'print_tpl',
            'lesson_suit_define',   //bid=0表示适用所有校区
        ];
        $prefix = config('database')['prefix'];
        if(in_array(str_replace($prefix, '', $table), $not_need_bid_table)) {
            return true;
        }

        $need_bid_table = [
            'class',
            'classroom',
            'class_attendance',
            'class_student',
            'course_arrange',
            'customer',
            'homework_task',
            'order',
            'order_cut_amount',
            'order_item',
            'order_receipt_bill',
            'order_refund',
            'order_transfer',
            'review',
            'student',
            'student_absence',
            'student_attendance',
            'student_lesson',
            'student_lesson_hour',
            'tally',
        ];
        if(in_array(str_replace($prefix, '', $table), $need_bid_table)) {
            exception('您当前提交的数据有异常,可能与您的客户端环境有关系,无法区分所属校区,请与技术支持联系!');
        }
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
            $m = $this->find($id);
            if($m){
                $m->delete();
            }
          }  
        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
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

    public static function getOgIdOfLoginUser()
    {
        $user = gvar('user') ? gvar('user') : null;
        if(!$user) return 0;

        $og_id = isset($user['og_id']) ? $user['og_id'] : 0;
        return $og_id;
    }

    /**
     * 默认设置是否体验课订单
     * @param $value
     * @param $data
     * @return int
     */
    public function setIsDemoAttr($value, $data)
    {
        if($value == 1){
            return $value;
        }

        $is_demo = 0;
        if(!empty($data['cid'])) {
            $class = get_class_info($data['cid'], true);
            $is_demo = !empty($class) ? $class['is_demo'] : 0;
        }else{
            if(!empty($data['lid'])){
                $lesson = get_lesson_info($data['lid']);
                $is_demo = !empty($lesson)?$lesson['is_demo']:0;
            }
        }

        return $is_demo;
    }

    public function setSecondEidsAttr($value)
    {
        if(is_array($value)){
            return implode(',',$value);
        }
        return $value;
    }

    public function getSecondEidsAttr($value)
    {
        $arr = $value ? explode(',',$value) : [];
        $arr = array_intval($arr);
        return $arr;
    }

    public function getSecondEidAttr($value)
    {
        if(!$value){
            if(!empty($this->getAttr('second_eids'))){
                $value = $this->getAttr('second_eids')[0];
            }
        }
        return $value;
    }

    public function setSecondEidAttr($value)
    {
        if(!$value){
            if(!empty($this->getAttr('second_eids'))){
                $value = $this->getAttr('second_eids')[0];
            }
        }
        return $value;
    }


    public function getCreateEmployeeNameAttr($value,$data){
        if(empty($data['create_uid'])) return '';
        $employee = get_employee_info(User::getEidByUid($data['create_uid']));
        return empty($employee) ? '' : $employee['ename'];
    }

    /**
     * 重置链接
     */
    public static function ResetLinks(){
        $links = parent::$links;
        foreach($links as $query){
            $query->getConnection()->close();
        }
        parent::$links = [];
    }



}


