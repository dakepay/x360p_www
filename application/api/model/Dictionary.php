<?php
/** 
 * Author: luo
 * Time: 2017-10-11 11:09
**/

namespace app\api\model;

use app\common\exception\FailResult;
use think\Exception;

class Dictionary extends Base
{

    protected $type = [
    ];
    protected $hidden = ['create_time', 'create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    /**
     * 创建字典条目
     * @param $value
     * @param $pid
     * @param string $title
     * @param string $desc
     * @return mixed
     */
    public static function createDictItem($value,$pid,$title = '',$desc = ''){
        $data['name'] = $value;
        $data['title'] = empty($title)?$value:$title;
        $data['desc'] = empty($desc)?$value:$desc;
        $data['pid'] = $pid;
        $dict = self::create($data);
        if(!$dict){
            return 0;
        }
        return $dict->did;
    }

    //更新字典，如果是加盟商的字典，系统内置部分加入config表
    public function updateDictionary($update_data)
    {
        $data = $this->getData();
        if(empty($data)) return $this->user_error('模型数据错误');

        $old_dictionary = $this->getData();
        $og_id = gvar('og_id');

        try {
            $this->startTrans();

            $m_config = new Config();
            if($og_id > 0 && $old_dictionary['is_system'] == 1) {
                /** @var Config $customer_system_dic */
                $customer_system_dic = Config::get_config('dictionary');
                if(empty($customer_system_dic)) {
                    $system_dictionary = $this->where('is_system = 1')->where('og_id = 0')->order('did asc')->select();
                    $system_dictionary = collection($system_dictionary)->toArray();
                    $config_data = [
                        'og_id'     => $og_id,
                        'cfg_name'  => 'dictionary',
                        'cfg_value' => $system_dictionary,
                        'format'    => 'json'
                    ];
                    $rs = $m_config->addConfig($config_data);
                    if($rs === false) throw new FailResult($m_config->getErrorMsg());

                    $customer_system_dic = Config::get_config('dictionary');
                }

                foreach($customer_system_dic['cfg_value'] as &$row) {
                    if($row['did'] == $old_dictionary['did']) {
                        $row['name'] = !empty($update_data['name']) ? $update_data['name'] : $row['name'];
                        $row['title'] = !empty($update_data['title']) ? $update_data['title'] : $row['title'];
                        $row['desc'] = !empty($update_data['desc']) ? $update_data['desc'] : $row['desc'];
                        $row['sort'] = !empty($update_data['sort']) ? $update_data['sort'] : $row['sort'];
                        $row['display'] = !empty_except_zero($update_data['display']) ? $update_data['display'] : $row['display'];
                    }
                }

                $rs = $m_config->editConfig($customer_system_dic);
                if($rs === false) throw new FailResult($customer_system_dic->getErrorMsg());
            } else {
                $rs = $this->allowField(true)->isUpdate(true)->save($update_data);
                if($rs === false) throw new FailResult($this->getErrorMsg());
            }

            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    //获取加盟商的字典
    public static function GetOrgSystemDictionary($og_id)
    {
        if(is_null($og_id)) exception('og_id错误');

        $login_og_id = gvar('og_id');
        gvar('og_id', $og_id);
        $org_system_dictionary = Config::get_config('dictionary');
        if(empty($org_system_dictionary)) {
            $m_dictionary = new Dictionary();
            $m_dictionary->skip_og_id_condition = true;
            $org_system_dictionary = $m_dictionary->where('is_system = 1')->where('og_id', 0)->select();
            $org_system_dictionary = collection($org_system_dictionary)->toArray();
        } else {
            $org_system_dictionary = $org_system_dictionary['cfg_value'];
        }
        gvar('og_id', $login_og_id);

        $data = [];
        foreach($org_system_dictionary as $row) {
            $data[$row['did']] = $row;
        }

        return $data;
    }

    //把加盟商的系统默认字典替换为修改后的字典
    public static function ReplaceOrgSystemDictionary($dictionary_list)
    {
        if(!is_array($dictionary_list)) return $dictionary_list;
        $og_id = gvar('og_id');

        static $org_system_dictionary;
        if(empty($org_system_dictionary)) {
            $org_system_dictionary = Dictionary::GetOrgSystemDictionary($og_id);
        }

        foreach($dictionary_list as $key => $row) {
            if(!empty($org_system_dictionary[$row['did']])) {
                $dictionary_list[$key] = $org_system_dictionary[$row['did']];
            }
            if($row['is_system'] != 1 && $row['og_id'] != $og_id) {
                unset($dictionary_list[$key]);
            }
        }

        return $dictionary_list;
    }

}