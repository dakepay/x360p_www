<?php
namespace app\admapi\command;


use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;


/**
 * shell useage
 * php think fixdata Elh smtjy|tee ./del_tally.log
 * Class FixData
 * @package app\admapi\command
 */

class FixData extends Command
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
        $this->setName('fixdata')->setDescription('fixdata data');
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
            printf('Error:parameter invalid,method not exists!-----%s, available parameter:Elh', $method);
        }
    }

   


    public function Elh(){
        /**
         *
         * select catt.*,elh.total_lesson_hours,elh.student_nums from x360p_class_attendance catt
        left join x360p_employee_lesson_hour elh
        on catt.catt_id = elh.catt_id
        where elh.catt_id is null
         *
         */
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

}