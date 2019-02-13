<?php
namespace app\admapi\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Db;
use think\Model;
use \app\api\model\Base;


/**
 * shell useage
 * php think datacount keeprate --host=neza |tee ./runtime/log/datacount_keeprate_20190122.txt
 * Class DataCount
 * @package app\admapi\command
 */

class DataCount extends Command
{
    protected $db_cfg = [];
    public $error_code;

    protected $data;

    protected $start_row_index = 2;
    protected $start_page = 1;
    protected $page_size = 100;

    public function __construct($name = null)
    {
        parent::__construct($name);

    }

    protected function configure()
    {
        $this->setName('datacount')->setDescription('data patch for some host');
        $this->addArgument('func',Argument::OPTIONAL,"the function");
        $this->addOption('host',null,Option::VALUE_OPTIONAL,"host",'');
        $this->addOption('pagesize',null,Option::VALUE_OPTIONAL,"pagesize",100);

    }

    protected function execute(Input $input, Output $output)
    {
        $func = $input->getArgument('func');
        $host  = $input->getOption('host');
        $pagesize = intval($input->getOption('pagesize'));


        $method = ucfirst($func);

        if(!method_exists($this,$method)){
            printf('Error:parameter invalid,func not exists!-----%s, available parameter:studentLessonAmount', $method);
            return;
        }

        $w['is_delete'] = 0;
        if(empty($host) || $host == 'all'){
            if(!$pagesize){
                printf("parameter error ,pagesize ");
                return;
            }
        }else{
            $w['host'] = $host;
        }

        $total = 0;

        foreach(get_all_rows('client',$w,$pagesize,'host ASC',true,'db_center',false) as $client){
            $this->doExec($method,$client);

            $total++;
        }
        echo(sprintf("执行完毕!总计:%s\n",$total));
    }

    /**
     * @param $client
     */
    protected function doExec($method,$client){
        if($client['parent_cid'] > 0){
            $w_dc['cid'] = $client['parent_cid'];
        }else{
            $w_dc['cid'] = $client['cid'];
        }
        $dc = db('database_config','db_center')->where($w_dc)->find();
        if(!$dc){
            printf("error:database config not exists!host:%s,客户:%s\n",$client['host'],$client['client_name']);
            return false;
        }
        $client['database'] = $dc;
        config('database',$dc);
        gvar('client',$client);
        gvar('og_id',$client['og_id']);
        printf("开始统计客户:%s的数据,host:%s,db:%s,og_id:%s\n",$client['client_name'],$client['host'],$dc['database'],$client['og_id']);
        $this->$method();
        printf("end===================================\n");




    }

    /**
     * 计算阳光喔指定校区的保有率
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function KeepRate(){
        \app\api\model\Base::ResetLinks();
        Db::clear();
        $client = gvar('client');
        $dbname = $client['database']['database'];
        $w['og_id'] = gvar('og_id');

        echo("开始日期:2018-09-01,截止日期:2018-12-31\n");
        //$branch_names = '长虹桥,国图,金源,林萃路,天秀,五棵松,学院路,亚运村';
        $branch_names = '';

        $query_bids     = [];
        $query_bnames   = [];

        $branch_list = get_table_list('branch',[]);

        if(!empty($branch_names)){
            $arr_names = explode(',',$branch_names);

            foreach($branch_list as $b){
                if(in_array($b['short_name'],$arr_names)){
                    $query_bids[]   = $b['bid'];
                    $query_bnames[] = $b['short_name'];
                }
            }
        }else{
            foreach($branch_list as $b){
                $query_bids[] = $b['bid'];
                $query_bnames[] = $b['short_name'];
            }
        }

        foreach($query_bids as $index=>$bid){
            $this->count_keep_rate($bid,$query_bnames[$index]);
        }
    }

    /**
     * 统计校区保有率
     * @param $bid
     * @param $branch_name
     */
    private function count_keep_rate($bid,$branch_name){
        /*
         * 保有率核算公式
         * 保有率 = （期初在读生 - 止学合同 - 退费合同 + 转介绍合同 + 市场插班生续费合同) / (期初在读生 + 市场插班生)
         */

        $start_date = '2018-09-01';
        $end_date   = '2018-12-31';

        $params['between_ts'] = [
                                strtotime($start_date.' 00:00:01'),
                                strtotime($end_date. '23:59:59')
                                ];

        $params['between_int_day'] = [
                                format_int_day($start_date),
                                format_int_day($end_date)
                                ];

        $params['bid'] = $bid;

        $class_total_nums = 0;
        $class_rates = [];
        $total_rate  = 0;
        $keep_rate   = 0;
        $v1 = 0;    //期初在读
        $v2 = 0;    //止学合同
        $v3 = 0;    //退费合同
        $v4 = 0;    //转介绍合同
        $v5 = 0;    //市场插班生续费合同
        $v6 = 0;    //市场插班生


        //按班级统计
        $w_class['start_lesson_time'] = ['BETWEEN',$params['between_ts']];
        $w_class['is_delete'] = 0;
        $w_class['bid'] = $params['bid'];

        $class_list = get_table_list('class',$w_class);

        $class_total_nums = count($class_list);

        foreach($class_list as $c){
            $cd = $this->count_class_keep_rate($c,$params,$branch_name);
            $v1 += $cd['v1'];
            $v2 += $cd['v2'];
            //$v3 += $cd['v3'];
            //$v4 += $cd['v4'];
            $v5 += $cd['v5'];
            $v6 += $cd['v6'];
            $total_rate     += $cd['keep_rate'];
            $class_rates[]  = $cd;
        }

        //退费合同
        $sql = "select count(*) as c from x360p_order_refund where is_delete = 0 and bid = $bid and refund_int_day between 20181001 and ".$params['between_int_day'][1];

        $rs = db()->query($sql);

        if($rs){
            $v3 = $rs[0]['c'];
        }

        //转介绍合同数
        $sql = "select count(*) as c from x360p_order o left join x360p_student s on o.sid = s.sid where o.is_delete = 0 and o.bid=$bid and o.money_pay_amount > 0 and s.referer_sid > 0 and paid_time between ".($params['between_ts'][0]+86400*30)." and ".$params['between_ts'][1];
        $rs = db()->query($sql);

        if($rs){
            $v4 = $rs[0]['c'];
        }
        if($v1+$v6 > 0) {
            $keep_rate = ($v1 - $v2 - $v3 + $v4 + $v5) / ($v1 + $v6);
        }

        $line = sprintf(
            "校区:%s,保有率=(期初在读:%s - 止学合同:%s - 退费合同:%s + 转介绍合同:%s + 市场插班生续费合同:%s)/(期初在读:%s+市场插班:%s) = %s\n",
            $branch_name,
            $v1,
            $v2,
            $v3,
            $v4,
            $v5,
            $v1,
            $v6,
            $keep_rate
            );

        echo($line);

    }


    private function count_class_keep_rate($class_info,$params,$branch_name){
        $cid = $class_info['cid'];
        $bid = $class_info['bid'];
        $ret = [
            'cid'   => $cid,
          'v1'  => 0,
          'v2'  => 0,
          'v3'  => 0,
          'v4'  => 0,
          'v5'  => 0,
          'v6'  => 0,
          'keep_rate'   => 0
        ];

        $first_sids = [];
        $insert_sids = [];
        $referer_sids = [];
        $market_insert_sids = [];
        $first_ca_id = 0;

        $w_ca['cid'] = $cid;

        $rs = db()->query("select * from x360p_course_arrange where is_delete = 0 AND cid = $cid order by int_day ASC,int_start_hour ASC limit 0,1");

        if($rs){

            $ca_id = $rs[0]['ca_id'];
            $first_ca_id = $ca_id;
            $sql = "select * from x360p_course_arrange_student where ca_id=$ca_id and is_delete = 0 and is_in > -1";

            $rs = db()->query($sql);
            if($rs){
                $first_sids = array_unique(array_column($rs,'sid'));
            }

            $ret['v1'] = count($first_sids);
        }

        if(!empty($first_sids)) {
            $sql_sids = implode(',', $first_sids);
            $sql = "select count(*) as c from x360p_student where is_delete = 0 AND money<=0 AND student_lesson_remain_hours <=0 and sid in ($sql_sids)";
            //echo($sql . '\n');
            $rs = db()->query($sql);

            if($rs) {
                $ret['v2'] = $rs[0]['c'];

                if($branch_name == '金源'){
                    $sql = "select * from x360p_student where is_delete = 0 AND money<=0 AND student_lesson_remain_hours <=0 and sid in ($sql_sids)";
                    $rs  = db()->query($sql);
                    echo("{$branch_name}止学名单:");
                    echo(implode(',',array_column($rs,'student_name')));
                    echo("\n");
                }
            }
            //计算退费合同

            //$sql = "select count(*) as c from x360p_order_refund where is_delete=0 AND sid in($sql_sids) and refund_int_day between " . $params['between_int_day'][0] . ' and ' . $params['between_int_day'][1];
            //echo($sql . "\n");

            //$rs = db()->query($sql);

            //if($rs) {
                //$ret['v3'] = $rs[0]['c'];
            //}

            //统计插班生
            $sql = "select distinct sid from x360p_course_arrange_student where cid=$cid and is_delete = 0 and is_in > -1 and ca_id<>$first_ca_id and sid not in($sql_sids)";

            //echo($sql . "\n");
            $rs = db()->query($sql);

            if ($rs) {
                $insert_sids = array_unique(array_column($rs, 'sid'));
            }
        }

        $insert_count = count($insert_sids);

        if($insert_count>0) {

            $sql_sids = implode(',', $insert_sids);
            //统计转介绍
            $sql = "select sid from x360p_student where referer_sid > 0 and is_delete=0 and sid in($sql_sids)";

            $rs = db()->query($sql);

            if ($rs) {
                $referer_sids = array_column($rs, 'sid');
            }
        }

        $referer_count = count($referer_sids);

        $ret['v4'] = $referer_count;

        $ret['v6'] = $insert_count - $referer_count;

        $market_insert_sids = array_diff($insert_sids,$referer_sids);

        //统计市场插板生续费合同
        if(!empty($market_insert_sids)) {
            $sql_sids = implode(',', $market_insert_sids);
            $sql = "select count(*) as c1 from (select sid,count(*) as c from x360p_order where is_delete = 0 and bid=$bid and sid in($sql_sids) and money_pay_amount > 0 and paid_time between ".$params['between_ts'][0].' and '.$params['between_ts'][1]." group by sid) cc where cc.c > 1";

            $rs = db()->query($sql);

            if ($rs) {
                $ret['v5'] = $rs[0]['c1'];
            }
        }

        if($ret['v1'] + $ret['v6'] > 0) {

            $ret['keep_rate'] = ($ret['v1'] - $ret['v2'] - $ret['v3'] + $ret['v4'] + $ret['v5']) / ($ret['v1'] + $ret['v6']);

        }
        $line = sprintf("班级:%s,v1:%s,v2:%s,v3:%s,v4:%s,v5:%s,v6:%s\n",
            $class_info['class_name'],
            $ret['v1'],
            $ret['v2'],
            $ret['v3'],
            $ret['v4'],
            $ret['v5'],
            $ret['v6']
        );

        //echo($line);
        return $ret;
    }










}