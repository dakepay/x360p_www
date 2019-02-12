<?php
/**
 * Author: luo
 * Time: 2017-12-06 12:13
**/

namespace app\admapi\model;

use app\common\exception\FailResult;
use think\Exception;

class Employee extends Base
{

    protected function setJoinIntDay($value)
    {
        return $value ? format_int_day($value) : $value;
    }

    public function client()
    {
        return $this->belongsToMany('Client', 'employee_client', 'cid', 'eid');
    }

    public function user()
    {
        return $this->hasOne('User', 'uid', 'uid')->field('uid,account');
    }

    //新建员工，并开通账号
    public function addOneEmployee($data, $is_open_account = 0)
    {
        if (!$this->checkInputParam($data['employee'],['name','mobile','join_int_day'])){
            return false;
        }
        $this->startTrans();
        try {
            $data['employee']['rids'] = implode(',', $data['employee']['rids']);
            //--1-- 添加员工
            $rs = $this->allowField(true)->save($data['employee']);
            $eid = $this->getAttr('eid');
            if ($rs === false) throw new FailResult("添加员工失败");

            //--2-- 开通帐号
            if ($is_open_account) {
                $common_fields = ['mobile', 'email', 'name', 'sex'];
                $data['user'] = isset($data['user']) ? $data['user'] : [];
                foreach ($common_fields as $f) {
                    if (isset($data['employee'][$f]) && !empty($data['employee'][$f])) {
                        $data['user'][$f] = $data['employee'][$f];
                    }
                }
                $user = $data['user'];
                if (empty($user) || !isset($user['account']) || !isset($user['password'])) throw new FailResult("开通账号缺少参数");

                $m_user = new User();
                $rs = $this->validateData($user, 'User.post');
                if($rs !== true) throw new FailResult($this->getErrorMsg());

                $uid = $m_user->addOneUser($user);
                if ($uid === false) throw new FailResult($m_user->getErrorMsg());

                $rs = $this->where('eid', $eid)->update(['uid' => $uid]);
                if ($rs === false) throw new FailResult("更新员工登录帐号失败");
            }

            $this->commit();
        } catch (FailResult $e) {
            $this->rollback();
            return $this->user_error($e->getMessage());
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    public function updateEmployee($eid,$input){
        if (!empty($input['rids'])){
            $rids = implode(',',$input['rids']);
        }

        $update = [
            'name' => $input['name'],
            'sex' => $input['sex'],
            'mobile' => $input['mobile'],
            'email' => $input['email'],
            'join_int_day' => $input['join_int_day'],
            'rids' => $rids,
        ];

        $w_update['eid'] = $eid;
        $rs = $this->save($update,$w_update);
        if (!$rs){
            return $this->user_error('编辑失败');
        }
        return true;
    }

    //员工增加所属客户
    public function addOneClient($cid)
    {
        $is_exist = (new EmployeeClient())->where('cid', $cid)->find();
        if(!empty($is_exist)) return $this->user_error('客户已经绑定其他员工');

        $rs = $this->client()->save($cid);
        if($rs === false) return $this->user_error('员工增加客户失败');

        return true;
    }

    public function addBatchClient($cids, $eid, Employee $employee = null)
    {
        if(is_null($employee)) {
            $employee = $this->findOrFail($eid);
        }

        $this->startTrans();
        try {
            foreach ($cids as $cid) {
                $rs = $employee->addOneClient($cid);
                if ($rs === false) throw new FailResult($employee->getErrorMsg());
            }
            $this->commit();
        } catch (FailResult $e) {
            $this->rollback();
            return $this->user_error($this->getErrorMsg());
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    public function delOneEmployee($eid, Employee $employee = null)
    {
        if(!($employee instanceof Employee)) {
            $employee = $this->find($eid);
        }

        $client = $employee->getAttr("client");
        if(!empty($client)) return $this->user_error('有相关客户不能删除');

        $rs = $employee->delete();
        if($rs === false) return $this->user_error("删除失败");

        return true;
    }


}