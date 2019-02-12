<?php
namespace app\admapi\command;

use app\api\model\Student;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;
use think\Log;
use util\excel;

/**
 * shell useage
 * php think dealdata DelTally neza|tee ./del_tally.log
 * php think dealdata DelTally bxwx|tee ./del_tally_bxwx.log
 * php think dealdata PatchRefundEmployeeReceipt neza|tee ./runtime/log/patch_refund_employee_receipt.txt
 * Class DealData
 * @package app\admapi\command
 */

class DealData extends Command
{
    protected $db_cfg = [];
    public $error_code;

    protected $data;

    protected $start_row_index = 3;
    protected $start_page = 1;
    protected $page_size = 100;

    public function __construct($name = null)
    {
        parent::__construct($name);

    }

    protected function configure()
    {
        $this->setName('dealdata')->setDescription('deal data');
        $this->addArgument('func', Argument::REQUIRED, "The function name of the application");
        $this->addArgument('host',Argument::REQUIRED,"The name of the database");

    }

    protected function execute(Input $input, Output $output)
    {
        $func = $input->getArgument('func');
        $host  = $input->getArgument('host');


        $client = db('client','db_center')->where('host',$host)->find();

        if(!$client){
            throw new \Exception('host不存在!');
        }


        $w_db['cid'] = $client['cid'];

        if($client['parent_cid'] > 0){
            $w_db['cid'] = $client['parent_cid'];
        }

        $db_cfg = db('database_config','db_center')->where($w_db)->find();

        if(!$db_cfg){
            throw new \Exception('host对应的数据库配置不存在:' . $host);
        }

        $this->db_cfg = $db_cfg;

        $client['database'] = $db_cfg;

        config('database',$db_cfg);

        gvar('client',$client);
        gvar('og_id',$client['og_id']);

        /*合法的method:syncSchool|syncStudent|syncEmployee|syncRole*/
        $method = ucfirst($func);
        if (method_exists($this, $method)) {
            $this->$method();
        } else {
            printf('Error:parameter invalid,method not exists!-----%s, available parameter:DellTally', $method);
        }
    }


    /**
     * 处理删除收据
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function DelTally(){
        $w_tally['remark'] = ['LIKE','报废收据%'];

        $index = 0;
        $success = 0;

        $update['is_delete'] = 1;
        $update['delete_time'] = time();
        foreach(loop_all_rows('tally',$w_tally,20) as $tly){
            $index++;
            echo("正在处理第{$index}条数据\n");
            $orb_info = db('order_receipt_bill')->find($tly['relate_id']);
            if(!$orb_info){
                echo("failure:处理失败,找不到orb_info,tid:".$tly['tid'].',relate_id:'.$tly['relate_id'].',amount:'.$tly['amount']."\n");
                continue;
            }

            $w_oph['orb_id'] = $orb_info['orb_id'];
            $oph_list = db('order_payment_history')->where($w_oph)->select();
            if(!$oph_list){
                echo("failure:处理失败,找不到oph_list,tid:".$tly['tid'].',relate_id:'.$tly['relate_id'].',amount:'.$tly['amount']."\n");
                continue;
            }
            $delete_amount = 0;
            $found = false;
            $err_msg  = '';

            foreach($oph_list as $oph){

                if($oph['amount'] == $tly['amount']){
                    $w_tly = [];
                    $w_tly['relate_id'] = $oph['oph_id'];
                    $w_tly['aa_id'] = $tly['aa_id'];
                    $w_tly['type'] = 1;
                    $tly_info = get_row_info($w_tly,'tally','tid');
                    if(!$tly_info){
                       $err_msg = "failure:处理失败,找不到tally,tid:".$tly['tid'].',relate_id:'.$tly['relate_id'].',amount:'.$tly['amount']."\n";
                        continue;
                    }

                    db('tally')->where('tid',$tly_info['tid'])->update($update);
                    $found = true;
                }
            }

            if(!$found){
                echo($err_msg);
                continue;
            }


            db('tally')->where('tid',$tly['tid'])->update($update);
            echo('success:处理成功:tid:'.$tly['tid'].',amount:'.$tly['amount']."\n");

            $success++;

        }

        echo("done!total:{$index},success:{$success}\n");
    }

    /**
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public function PatchRefundEmployeeReceipt(){
        $sql = <<<EOF
SELECT orf.* from `x360p_order_refund` orf
LEFT JOIN `x360p_employee_receipt` er
ON orf.or_id = er.or_id
WHERE er.erc_id IS NULL %limit%
EOF;

        $index = 0;
        $success = 0;
        $failure = 0;
        foreach(loop_sql_result($sql,1000) as $row){
            echo("正在处理第{$index}条数据\n");
            $result = $this->do_patch_refund_employee_receipt($row);
            if(!$result){
                $failure++;
            }else{
                $success++;
            }
            $index++;
        }
        echo("done!total:{$index},success:{$success},failure:{$failure}\n");
    }

    /**
     * @param $row
     */
    protected function do_patch_refund_employee_receipt($row){

        $w['sid']       = $row['sid'];
        $w['is_delete'] = 0;

        $erc_list = table('employee_receipt')->where($w)->select();
        if(!$erc_list){
            printf("failure:没有找到正业绩记录,or_id:%s,amount:%s\n",$row['or_id'],$row['refund_amount']);
            return 0;
        }
        $dealed_eids = [];
        $fields = ['og_id','bid','eid','sale_role_did','oid','sid'];
        $auto_fields = ['create_uid'];

        $success_nums = 0;

        foreach($erc_list as $erc){
            if(in_array($erc['eid'],$dealed_eids)){
                continue;
            }
            $new_erc = [];
            array_copy($new_erc,$erc,$fields);
            $new_erc['or_id']  = $row['or_id'];
            $new_erc['amount'] = $row['refund_amount'];
            $new_erc['receipt_time'] = strtotime(int_day_to_date_str($row['refund_int_day']));
            $new_erc['create_time'] = time();
            $new_erc['update_time'] = $new_erc['create_time'];
            array_copy($new_erc,$row,$auto_fields);
            try {
                $result = db('employee_receipt')->insert($new_erc);
                if(!$result){
                    printf("failure:error:%s,eid:%s,or_id:%s,amount:%s\n",'插入数据0条',$erc['eid'],$row['or_id'],$row['refund_amount']);
                    break;
                }else{
                    $success_nums ++;
                    printf("success:or_id:%s,amount:%s,eid:%s,sale_role_did:%s\n",$row['or_id'],$row['refund_amount'],$erc['eid'],$erc['sale_role_did']);
                }
            }catch(\Exception $e){
                printf("failure:error:%s,eid:%s,or_id:%s,amount:%s\n",$e->getMessage(),$erc['eid'],$row['or_id'],$row['refund_amount']);
                return false;
            }
            $dealed_eids[] = $erc['eid'];
        }

        return $success_nums;
    }

}