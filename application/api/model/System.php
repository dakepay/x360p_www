<?php
/**
 * Author: luo
 * Time: 2018/3/29 9:04
 */

namespace app\api\model;

use app\common\Cmd;
use app\common\exception\FailResult;

class System extends Base
{
    const DATABASE_PREFIX = 'x360p_';

    //不能删除的表
    private $not_del_tables = [
        'branch',
        'accounting_account',
        'config',
        'config_pay',
        'department',
        'dictionary',
        'file',
        'material_store',
        'org',
        'org_renew_log',
        'role',
        'season_date',
        'wxmp',
    ];
    //客户想删除的表
    private $user_del_tables = [];  # 没前缀
    private $all_tables = [];   # 带前缀

    public function reset($is_force = 0, $user_del_tables = [])
    {
        if(!$is_force) {
            return $this->user_error('是否强制清空所有数据，恢复出厂设置', self::CODE_HAVE_RELATED_DATA);
        }

        //获取客户数据配置
        $client = gvar('client');
        if(empty($client)) return $this->user_error('客户信息错误');
        $database_config = db('database_config', 'center_database')->where('cid', $client['cid'])->find();
        if(empty($database_config)) {
            $database_config = db('database_config', 'center_database')->where('cid', $client['parent_cid'])->find();
        }
        //$database_config = DatabaseConfig::get(['cid' => $client['cid']]);
        $og_id = gvar('og_id');

        //备份数据库
        $rs = $this->backupDatabase($database_config);
        if($rs === false) return false;

        //可选删除的表
        $optional_del_tables = [
            'branch_employee',
            'classroom',
            'customer',
            'customer_follow_up',
            'employee',
            'employee_dept',
            'employee_profile',
            'employee_role',
            'holiday',
            'lesson',
            'lesson_attachment',
            'lesson_material',
            'lesson_standard_file',
            'lesson_standard_file_item',
            'market_clue',
            'market_channel',
            'material',
            'material_store',
            'public_school',
            'student',
            'subject',
            'subject_grade',
            'time_section',
            'user',
            'user_student',
            'wxmp',
            'wxmp_menus',
        ];

        $this->not_del_tables = array_merge($this->not_del_tables, $optional_del_tables);

        foreach($user_del_tables as $key => $val) {
            if(!in_array($val, $optional_del_tables)) {
                unset($user_del_tables[$key]);
            }
        }
        $this->user_del_tables = $user_del_tables;

        //加上表前缀
        $prefix = $database_config['prefix'];
        $not_del_prefix_tables = $this->not_del_tables;
        array_walk($not_del_prefix_tables, function(&$val) use($prefix) {
            $val = $prefix . $val;
        });

        try {
            $tables  = $this->query('show tables');

            $this->startTrans();
            $row_key = 'Tables_in_' . self::DATABASE_PREFIX . $database_config['host'];

            foreach ($tables as $r) {
                $table = $r[$row_key];
                $this->all_tables[] = $table;
                if (!in_array($table, $not_del_prefix_tables)) {
                    $fields = $this->query('SHOW COLUMNS FROM ' . $table);
                    $fields = array_column($fields, 'Field');
                    if(in_array('og_id', $fields)) {
                        $sql = 'delete from ' . $table . ' where og_id = ' . $og_id;
                    } elseif($og_id == 0) {
                        $sql = 'delete from ' . $table . ' where 1';
                    }
                    $rs = $this->query($sql);
                    if ($rs === false) throw new FailResult($table . '删除失败');
                }
            }

            //处理可选删除的表
            foreach($optional_del_tables as $del_table) {
                $method_name = 'del_' . trim($del_table);
                if(method_exists($this, $method_name)) {
                    $this->$method_name($og_id);
                }
            }

            $sql = sprintf('update %saccounting_account set amount = 0 where og_id = %s', $prefix, $og_id);
            $this->query($sql);

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    public function backupDatabase($database_config)
    {
        //保险起见先做个备份
        $bak_sql_dir = DATA_PATH.'reset_system_sql_bak'.DS.$database_config['host'];
        mkdirss($bak_sql_dir);

        $bak_sql_file = $bak_sql_dir.DS.$database_config['database'].date('YmdHis', time()).'.sql';
        $root     = $database_config['username'];
        $password = str_replace('$','\\$',$database_config['password']);
        $database = $database_config['database'];
        $host = $database_config['hostname'];
        $option = '';
        if($host != 'localhost'){
            $option = ' --set-gtid-purged=OFF ';
        }
        $shell    = "mysqldump -h{$host} -u{$root} -p{$password} {$database}{$option} > {$bak_sql_file}";

        list($code, $output, $error) = Cmd::run($shell);
        if ($code !== 0) {
            return $this->user_error('删除前备份数据库出错:'.Cmd::StripWarning($error));
        }

        if(!file_exists($bak_sql_file) || filesize($bak_sql_file) <= 0) {
            return $this->user_error('备份数据出错，不能恢复出厂设置');
        }

        return true;
    }

    //删除员工
    private function del_employee($og_id)
    {
        if($og_id > 0) return true;
        if(in_array('employee', $this->user_del_tables)) {
            $sql = sprintf('delete from %semployee where eid > 10001 and og_id = %s', self::DATABASE_PREFIX, $og_id);
            $rs = $this->query($sql);

            $sql = sprintf('delete from %suser where user_type=1 and uid > 10001 and og_id = %s', self::DATABASE_PREFIX, $og_id);
            $rs = $this->query($sql);

            $sql = sprintf('delete from %semployee_dept where eid > 10001 and og_id = %s', self::DATABASE_PREFIX, $og_id);
            $rs = $this->query($sql);

            $sql = sprintf('delete from %semployee_profile where eid > 10001 and og_id = %s', self::DATABASE_PREFIX, $og_id);
            $rs = $this->query($sql);

            $sql = sprintf('delete from %semployee_role where eid > 10001 and og_id = %s', self::DATABASE_PREFIX, $og_id);
            $rs = $this->query($sql);

            $sql = sprintf('delete from %sbranch_employee where eid > 10001 and og_id = %s', self::DATABASE_PREFIX, $og_id);
            $rs = $this->query($sql);

        } else {
            if(in_array('user', $this->user_del_tables)) {
                $sql = 'update %semployee set lids ="" , sj_ids="" , uid = 0 , account="", user_status=0 where eid > 10001 and og_id=%s';
            } else {
                $sql = 'update %semployee set lids ="" , sj_ids="" where eid > 10001 and og_id=%s';
            }

            $rs = $this->query(sprintf($sql, self::DATABASE_PREFIX, $og_id));
        }
        return $rs;

    }

    //删除市场渠道
    private function del_market_channel($og_id)
    {
        if(in_array('market_channel',$this->user_del_tables) && in_array('market_clue',$this->user_del_tables)){
            $sql = sprintf('delete from %smarket_channel where og_id = %s',self::DATABASE_PREFIX,$og_id);
            $rs  = $this->query($sql);
            return $rs;
        }
        return true;
    }
    //删除市场名单
    private function del_market_clue($og_id)
    {
        if(in_array('market_clue',$this->user_del_tables)){
            $sql = sprintf('delete from %smarket_clue where og_id = %s',self::DATABASE_PREFIX,$og_id);
            $rs  = $this->query($sql);
            return $rs;
        }
        return true;
    }

    //删除教室
    private function del_classroom($og_id)
    {
        if(in_array('classroom', $this->user_del_tables)) {
            $sql = 'delete from %sclassroom where og_id = %s';
            $rs = $this->query(sprintf($sql, self::DATABASE_PREFIX, $og_id));
            return $rs;
        }

        return true;
    }

    //删除客户
    private function del_customer($og_id)
    {
        $rs = true;
        if(in_array('customer', $this->user_del_tables)) {
            $sql = sprintf('delete from %scustomer where og_id = %s', self::DATABASE_PREFIX, $og_id);
            $rs = $this->query($sql);

            $sql = sprintf('delete from %scustomer_follow_up where og_id = %s', self::DATABASE_PREFIX, $og_id);
            $rs = $this->query($sql);
        } else {
            if(in_array('student', $this->user_del_tables)) {
                $sql = 'update %scustomer set from_sid=0 , sid=0 , is_reg=0 , signup_amount=0 , signup_int_day=0 ' .
                    ', referer_sid=0 , follow_eid = 0 , follow_times = 0 where og_id = %s';
                $rs = $this->query(sprintf($sql, self::DATABASE_PREFIX, $og_id));
            } else {
                $sql = 'update %scustomer set signup_amount=0 , signup_int_day=0 , ' .
                    ' follow_eid = 0 , follow_times = 0 where og_id = %s';
                $rs = $this->query(sprintf($sql, self::DATABASE_PREFIX, $og_id));
            }
        }

        return $rs;
    }

    //删除节日
    private function del_holiday($og_id)
    {
        if(in_array('holiday', $this->user_del_tables)) {
            $sql = sprintf('delete from %sholiday where og_id = %s', self::DATABASE_PREFIX, $og_id);
            $rs = $this->query($sql);
            return $rs;
        }
    }

    //删除课程
    private function del_lesson($og_id)
    {
        if(in_array('lesson', $this->user_del_tables)) {
            $sql = sprintf('delete from %slesson where og_id = %s', self::DATABASE_PREFIX, $og_id);
            $rs = $this->query($sql);

            $sql = sprintf('delete from %slesson_attachment where og_id = %s', self::DATABASE_PREFIX, $og_id);
            $rs = $this->query($sql);

            $sql = sprintf('delete from %slesson_material where 1', self::DATABASE_PREFIX);
            $rs = $this->query($sql);

            return $rs;
        } else {
            if(in_array('subject', $this->user_del_tables)) {
                $sql = 'update %slesson set sj_id=0 , sj_ids="" ' .
                    'where og_id = %s';
                $rs = $this->query(sprintf($sql, self::DATABASE_PREFIX, $og_id));
                return $rs;
            }
            if(in_array('material', $this->user_del_tables)) {
                $sql = sprintf('delete from %slesson_material where 1', self::DATABASE_PREFIX);
                $rs = $this->query($sql);
                return $rs;
            }
        }
    }

    //删除课标
    private function del_lesson_standard_file($og_id)
    {
        if(in_array('lesson', $this->user_del_tables) || in_array('lesson_standard_file', $this->user_del_tables)) {
            $sql = sprintf('delete from %slesson_standard_file where og_id = %s', self::DATABASE_PREFIX, $og_id);
            $rs = $this->query($sql);
            $sql = sprintf('delete from %slesson_standard_file_item where og_id = %s', self::DATABASE_PREFIX, $og_id);
            $rs = $this->query($sql);
            return $rs;
        }
    }

    //删除物品
    private function del_material($og_id)
    {
        if(in_array('material', $this->user_del_tables)) {
            $sql = sprintf('delete from %smaterial where og_id = %s', self::DATABASE_PREFIX, $og_id);
            $rs = $this->query($sql);
            return $rs;
        }
    }

    //删除公立学校
    private function del_public_school($og_id)
    {
        if(in_array('public_school', $this->user_del_tables)) {
            $sql = sprintf('delete from %spublic_school where og_id = %s', self::DATABASE_PREFIX, $og_id);
            $rs = $this->query($sql);
            return $rs;
        }
    }

    //删除学生
    private function del_student($og_id)
    {
        if(in_array('student', $this->user_del_tables)) {
            $sql = sprintf('delete from %sstudent where og_id = %s', self::DATABASE_PREFIX, $og_id);
            $rs = $this->query($sql);

            if(!in_array('user', $this->user_del_tables)) {
                $sql = 'delete from %suser where user_type = 2 and og_id = %s';
                $rs = $this->query(sprintf($sql, self::DATABASE_PREFIX, $og_id));
            }

            return $rs;
        } else {
            //student: first_tel, first_uid, second_tel, second_uid, money, credit, student_lesson_times, student_lesson_remain_times
            $sql = sprintf('update %sstudent set money=0 , credit=0 , student_lesson_times=0 ' .
                ' , student_lesson_remain_times=0, student_lesson_hours=0, student_lesson_remain_hours=0 where og_id = %s', self::DATABASE_PREFIX, $og_id);
            $rs = $this->query($sql);
            return $rs;
        }
    }

    //删除科目
    private function del_subject($og_id)
    {
        if(in_array('subject', $this->user_del_tables)) {
            $sql = sprintf('delete from %ssubject where og_id = %s', self::DATABASE_PREFIX, $og_id);
            $rs = $this->query($sql);

            $sql = sprintf('delete from %ssubject_grade where og_id = %s', self::DATABASE_PREFIX, $og_id);
            $rs = $this->query($sql);
            return $rs;
        }
    }

    //删除时间表
    private function del_time_section($og_id)
    {
        if(in_array('time_section', $this->user_del_tables)) {
            $sql = sprintf('delete from %stime_section where og_id = %s', self::DATABASE_PREFIX, $og_id);
            $rs = $this->query($sql);

            return $rs;
        }
    }

    //删除帐号
    private function del_user($og_id)
    {
        //只有删除学生的同时，才能删除帐号，因为学生与帐号强烈关联
        if(in_array('user', $this->user_del_tables) && in_array('student', $this->user_del_tables)) {
            $sql = sprintf('delete from %suser where uid > 10001 and user_type = 2 and is_admin != 1 and og_id = %s', self::DATABASE_PREFIX, $og_id);
            $rs = $this->query($sql);

            return $rs;
        } else {
            if(!in_array('student', $this->user_del_tables)) {
                $key = array_search('user', $this->user_del_tables);
                if($key !== false) unset($this->user_del_tables[$key]);
            }
        }
    }

    //删除微信相关
    private function del_wxmp($og_id)
    {
        if(in_array('wxmp', $this->user_del_tables)) {
            $sql = sprintf('delete from %swxmp where 1 and og_id = %s', self::DATABASE_PREFIX, $og_id);
            $rs = $this->query($sql);

            foreach($this->all_tables as $table) {
                if(strpos($table,self::DATABASE_PREFIX . 'wxmp') !== false
                    && !in_array(str_replace(self::DATABASE_PREFIX, '', $table), $this->not_del_tables)) {
                    $fields = $this->query('SHOW COLUMNS FROM ' . $table);
                    $fields = array_column($fields, 'Field');
                    if (in_array('og_id', $fields)) {
                        $sql = 'delete from ' . $table . ' where og_id = ' . $og_id;
                    } elseif($og_id == 0) {
                        $sql = 'delete from ' . $table . ' where 1';
                    }
                    $rs = $this->query($sql);
                }

            }

            return $rs;
        } else {
            if(in_array('user', $this->user_del_tables) && $og_id == 0) {
                $sql = sprintf('update %swxmp_fans set uid=0 , employee_uid=0', self::DATABASE_PREFIX);
                $rs = $this->query($sql);
                return $rs;
            }
        }

    }




}