<?php
/**
 * Author: luo
 * Time: 2017-12-13 16:29
**/

namespace app\admapi\model;

class ClientService extends Base
{

    protected $append = ['ename'];

    public function getEnameAttr($value,$data){
        if($data['eid'] == 0){
            return '系统';
        }
        $u = get_user_info($data['eid']);
        return $u['name'];
    }
    public function client()
    {
        return $this->hasOne('Client', 'cid', 'cid');
    }

    public function employee()
    {
        return $this->hasOne('Employee', 'eid', 'eid');
    }

    /**
     * 添加系统服务内容
     * @param $msg
     * @param $type
     * @param $cid
     */
    public function addSystemService($msg,$type,$cid){
        $cs['trouble'] = $msg;
        $cs['service_did'] = $type;
        $cs['is_solved'] = 1;
        $cs['cid'] = $cid;
        $cs['eid'] = 0;

        $result = $this->data([])->isUpdate(false)->save($cs);

        if(!$result){
            return false;
        }

        return $this->cs_id;
    }

    public function addClientService($input){
        if(isset($input['eid']) && $input['eid'] == 0){
            $input['eid'] = $this->getLoginUserEid();
        }

        $result = $this->allowField(true)->save($input);

        return $result;

    }

    protected function getLoginUserEid(){
        $uid = gvar('uid');
        $w_e['uid'] = $uid;
        $employee = db('employee')->where($w_e)->find();
        
        if($employee){
            return $employee['eid'];
        }
        return $uid;
    }

}