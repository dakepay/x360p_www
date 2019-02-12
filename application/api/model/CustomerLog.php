<?php
namespace app\api\model;

class CustomerLog extends Base
{
	const OP_ASSIGN = 1; #客户分配
	const OP_TO_STUDENT = 2; #客户转学员
	const OP_FOLLOW_UP = 3; #跟单
	const OP_EDIT = 4; #编辑
	const OP_ADD = 5; #添加客户
	const OP_DELETE = 6; #删除
	const OP_IMPORT = 7; #导入客户

	protected $type = [
        'content' => 'json',
    ];

    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];


    /**
     * 添加一条客户分配日志
     * @param [type] $cu_id      [description]
     * @param [type] $follow_eid [description]
     */
    public static function addCustomerAssignLog($cu_id,$follow_eid)
    {
    	$customer = get_customer_info($cu_id);
    	$data = [];
    	array_copy($data,$customer,['og_id','bid','cu_id']);
    	$data['op_type'] = CustomerLog::OP_ASSIGN;
    	$desc = config('format_string.customer_assign');
    	$temp['name'] = request()->user['name'];
    	$temp['time'] = date('Y-m-d H:i:s',time());
    	$temp['customer'] = $customer['name'];
    	$temp['follow_eid'] = get_employee_name($follow_eid);
    	$data['desc'] = str_replace(array_keys($temp),$temp,$desc);

    	$data['content'] = [];
   
        return CustomerLog::create($data);
    }
    
    /**
     * 添加一条客户转学员操作日志
     * @param Customer $customer [description]
     */
    public static function addCustomerToStudentLog($cu_id)
    {
    	$customer = get_customer_info($cu_id);
    	$data = [];
    	array_copy($data,$customer,['og_id','bid','cu_id']);
    	$data['op_type'] = CustomerLog::OP_TO_STUDENT;
    	$desc = config('format_string.customer_to_student');
    	$temp['name'] = request()->user['name'];
    	$temp['customer'] = $customer['name'];
    	$data['desc'] = str_replace(array_keys($temp),$temp,$desc);
    	$data['content'] = [];

    	return CustomerLog::create($data);
    }
    
    /**
     * 添加一条客户跟单日志
     * @param [type] $cu_id [description]
     */
    public static function addCustomerFollowUpLog($cu_id)
    {
        $customer = get_customer_info($cu_id);
        $data = [];
        array_copy($data,$customer,['og_id','bid','cu_id']);
        $data['op_type'] = CustomerLog::OP_FOLLOW_UP;
        $desc = config('format_string.customer_follow_up');
        $temp['name'] = request()->user['name'];
        $temp['customer'] = $customer['name'];
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['content'] = [];

        return CustomerLog::create($data);
    }
    
    /**
     * 添加一条客户编辑 日志
     * @param array  $content [description]
     * @param [type] $cu_id   [description]
     */
    public static function addCustomerEditLog(array $content,$cu_id)
    {
        $customer = get_customer_info($cu_id);
        $data = [];
        array_copy($data,$customer,['og_id','bid','cu_id']);
        $data['op_type'] = CustomerLog::OP_EDIT;
        $desc = config('format_string.customer_edit');
        $temp['name'] = request()->user['name'];
        $temp['customer'] = $customer['name'];
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['content'] = $content;
        return CustomerLog::create($data);
    }
    
    /**
     * 添加一天客户添加记录
     * @param [type] $cu_id [description]
     */
    public static function addCustomerInsertLog($cu_id)
    {
    	$customer = get_customer_info($cu_id);
    	$data = [];
    	array_copy($data,$customer,['og_id','bid','cu_id']);
    	$data['op_type'] = CustomerLog::OP_ADD;
    	$desc = config('format_string.customer_add');
    	$temp['name'] = request()->user['name'];
    	$temp['customer'] = $customer['name'];
    	$data['desc'] = str_replace(array_keys($temp),$temp,$desc);
    	$data['content'] = [];

        return CustomerLog::create($data);
    }

    /**
     * 添加一条客户删除日志
     * @param Customer $customer [description]
     */
    public static function addCustomerDeleteLog(Customer $customer)
    {
    	// print_r($customer);exit;
    	$data = [];
    	array_copy($data,$customer,['og_id','bid','cu_id']);
    	$data['op_type'] = CustomerLog::OP_DELETE;
    	$desc = config('format_string.customer_delete');
    	$temp['name'] = request()->user['name'];
    	$temp['customer'] = $customer['name'];
    	$data['desc'] = str_replace(array_keys($temp),$temp,$desc);
    	$data['content'] = [];
        return CustomerLog::create($data);
    }

    /**
     * 添加一条 客户导入日志
     * @param [type] $cu_id [description]
     */
    public static function addCustomerImportLog($cu_id)
    {
    	$customer = get_customer_info($cu_id);
    	array_copy($data,$customer,['og_id','bid','cu_id']);
    	$data['op_type'] = CustomerLog::OP_IMPORT;
    	$desc = config('format_string.customer_import');
    	$temp['name'] = request()->user['name'];
    	$temp['customer'] = $customer['name'];
    	$data['desc'] = str_replace(array_keys($temp),$temp,$desc);
    	$data['content'] = [];
        return CustomerLog::create($data);

    }


}




    