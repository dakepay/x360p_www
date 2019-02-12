<?php
namespace app\admapi\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Db;

/**
 * shell useage
 * php think uproleper add --per=students |tee ./runtime/log/uproleper.log
 * Class ExImport
 * @package app\admapi\command
 */

class UpdateRolePer extends Command
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
        $this->setName('uproleper')->setDescription('update role per');
        $this->addArgument('action',Argument::OPTIONAL,"the function");
        $this->addOption('pers',null,Option::VALUE_OPTIONAL,"per items",'');
        $this->addOption('expers',null,Option::VALUE_OPTIONAL,"extra per items",'');
    }

    protected function execute(Input $input, Output $output)
    {
        $action = $input->getArgument('action');
        $pers  = $input->getOption('pers');
        $expers = $input->getOption('expers');

        /*合法的method:syncSchool|syncStudent|syncEmployee|syncRole*/
        $method = ucfirst($action);
        if (method_exists($this, $method)) {
            $this->$method($pers,$expers);
        } else {
            printf('Error:parameter invalid,action not exists!-----%s, available parameter:Add Replace Delete', $method);
        }
    }


    public function Add($pers,$expers = ''){
        $w = [];
        foreach(get_all_rows('database_config',$w,20,'host ASC',true,'db_center') as $db_cfg){
            $this->doAddAction($db_cfg,$pers,$expers);
        }
    }


    protected function doAddAction($db_cfg,$pers,$expers = ''){
        echo("start_add_per_item[".$db_cfg['host']."]:\n");
        try {
            $db = Db::connect($db_cfg);
            $sql = "SELECT * FROM `x360p_role` WHERE `pers` IS NOT NULL AND `is_delete` = 0";
            $roles = $db->query($sql);

            $add_pers = explode(',', $pers);

            if (empty($roles)) {
                echo("roles is empty,skip!");
            } else {
                foreach ($roles as $role) {
                    $arr_pers = explode(',', $role['pers']);

                    $new_arr_pers = [];
                    if($expers != ''){
                        if(in_array($expers,$arr_pers)){
                            $new_arr_pers = array_merge($arr_pers, $add_pers);
                        }else{
                            $new_arr_pers = $arr_pers;
                            $success = sprintf("add_per_item_success[%s]:skiped!og_id:%s,rid:%s,role_name:%s\n",$db_cfg['host'],$role['og_id'],$role['rid'],$role['role_name']);
                            echo($success);
                            continue;
                        }
                    }else{
                        $new_arr_pers = array_merge($arr_pers, $add_pers);
                    }
                    $new_arr_pers = array_unique($new_arr_pers);

                    $update['pers'] = implode(',', $new_arr_pers);

                    $update_sql = "UPDATE `x360p_role` set pers = '" . $update['pers'] . "' WHERE `rid`=" . $role['rid'];

                    echo("sql:".$update_sql."\n");
                    $result = $db->execute($update_sql);

                    if (false === $result) {
                        $error = spritnf("add_per_item_error[%s]:og_id:%s,rid:%s,role_name:%s\n",$db_cfg['host'],$role['og_id'],$role['rid'],$role['role_name']);
                        echo($error);
                        continue;
                    }

                    $success = sprintf("add_per_item_success[%s]:og_id:%s,rid:%s,role_name:%s\n",$db_cfg['host'],$role['og_id'],$role['rid'],$role['role_name']);
                    echo($success);
                }
            }

        }catch(\Exception $e){
            echo("add_per_item_error:[".$db_cfg['host']."]:".$e->getMessage()."\n");
        }
        echo("end_add_per_item[".$db_cfg['host']."]\n===========================================\n");

    }

    public function Replace($pers,$expers = ''){
        if(strpos($pers,",") === false){
            echo("format error ,correct format is x,y \n");
            exit;
        }


        $arr_pers = explode(",",$pers);
        $old_pers = $arr_pers[0];
        $new_pers = $arr_pers[1];
        $w = [];
        foreach(get_all_rows('database_config',$w,20,'host ASC',true,'db_center') as $db_cfg){
            $this->doReplaceAction($db_cfg,$old_pers,$new_pers);
        }
    }

    protected function doReplaceAction($db_cfg,$old_pers,$new_pers ){
        echo("start_replace_per_item[".$db_cfg['host']."]:\n");
        try {
            $db = Db::connect($db_cfg);
            $sql = "SELECT * FROM `x360p_role` WHERE `pers` IS NOT NULL AND `is_delete` = 0";
            $roles = $db->query($sql);


            if (empty($roles)) {
                echo("roles is empty,skip!");
            } else {
                foreach ($roles as $role) {

                    $update['pers'] = str_replace($old_pers,$new_pers,$role['pers']);

                    $update_sql = "UPDATE `x360p_role` set pers = '" . $update['pers'] . "' WHERE `rid`=" . $role['rid'];

                    $result = $db->execute($update_sql);

                    if (false === $result) {
                        $error = spritnf("replace_per_item_error[%s]:og_id:%s,rid:%s,role_name:%s\n",$db_cfg['host'],$role['og_id'],$role['rid'],$role['role_name']);
                        echo($error);
                        continue;
                    }

                    $success = sprintf("replace_per_item_success[%s]:og_id:%s,rid:%s,role_name:%s\n",$db_cfg['host'],$role['og_id'],$role['rid'],$role['role_name']);
                    echo($success);
                }
            }

        }catch(\Exception $e){
            echo("add_per_item_error:[".$db_cfg['host']."]:".$e->getMessage()."\n");
        }
        echo("end_add_per_item[".$db_cfg['host']."]\n===========================================\n");

    }



    public function Delete($pers,$expers = ''){
        $w = [];
        foreach(get_all_rows('database_config',$w,20,'host ASC',true,'db_center') as $db_cfg){
            $this->doDeleteAction($db_cfg,$pers);
        }
    }

    protected function doDeleteAction($db_cfg,$pers){
        echo("start_delete_per_item[".$db_cfg['host']."]:\n");
        try {
            $db = Db::connect($db_cfg);
            $sql = "SELECT * FROM `x360p_role` WHERE `pers` IS NOT NULL AND `is_delete` = 0";
            $roles = $db->query($sql);


            if (empty($roles)) {
                echo("roles is empty,skip!");
            } else {
                $delete_pers = explode(',',$pers);
                foreach ($roles as $role) {
                    $old_arr_pers = explode(',',$role['pers']);
                    $new_arr_pers = [];

                    foreach($old_arr_pers as $p){
                        if(!in_array($p,$delete_pers)){
                            $new_arr_pers[] = $p;
                        }
                    }

                    $update['pers'] = implode(',',$new_arr_pers);

                    $update_sql = "UPDATE `x360p_role` set pers = '" . $update['pers'] . "' WHERE `rid`=" . $role['rid'];

                    $result = $db->execute($update_sql);

                    if (false === $result) {
                        $error = spritnf("delete_per_item_error[%s]:og_id:%s,rid:%s,role_name:%s\n",$db_cfg['host'],$role['og_id'],$role['rid'],$role['role_name']);
                        echo($error);
                        continue;
                    }

                    $success = sprintf("delete_per_item_success[%s]:og_id:%s,rid:%s,role_name:%s\n",$db_cfg['host'],$role['og_id'],$role['rid'],$role['role_name']);
                    echo($success);
                }
            }

        }catch(\Exception $e){
            echo("delete_per_item_error:[".$db_cfg['host']."]:".$e->getMessage()."\n");
        }
        echo("delete_per_item[".$db_cfg['host']."]\n===========================================\n");

    }





}