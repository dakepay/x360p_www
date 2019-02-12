<?php
/**
 * luo
 */
namespace app\api\model;

use app\common\exception\FailResult;
use app\common\Wechat;
use EasyWeChat\OpenPlatform\OpenPlatform;
use think\Exception;
use think\Log;

class Branch extends Base
{

    public function branchEmployees()
    {
        return $this->belongsToMany('employee', 'branch_employee', 'eid', 'bid');
    }

    //创建校区
    public function createBranch($data) {
        /*
        if($og_id = gvar('og_id')) {
            if(Org::isOverBranchLimit($og_id)) return $this->user_error('加盟商校区数量已经达到限制');
        }*/

        if(is_client_overflow('branch')){
            return $this->user_error('校区数量超出许可限制!');
        }

        try {
            $rs = $this->data([])->validate(true)->allowField(true)->isUpdate(false)->save($data);
            if($rs === false) exception($this->getErrorMsg());
            $bid = $this->getLastInsID();

            $branch = $this->find($bid);
            $rs = $this->autoCreateRelevance($branch);
            if($rs === false) exception($this->getErrorMsg());

        } catch(Exception $e) {
            return $this->deal_exception($e->getMessage(), $e);
        }

        return $bid;
    }

    public function editBranch($data) {
        $rs =  $this->allowField(true)->isUpdate(true)->save($data);
        return $rs;
    }

    //创建校区，创建关联数据
    public function autoCreateRelevance(Branch $branch)
    {
        $this->startTrans();
        try {
            /* 创建帐户 */
            $account_data = [
                'name'      => '现金',
                'type'      => 0,
                'bids'      => $branch->getAttr('bid'),
                'is_public' => 0,
                'is_front'  => 1,
                'is_default' => 1,
            ];
            $account_model = new AccountingAccount();
            $rs = $account_model->createOneAccount($account_data);
            if (!$rs) exception($account_model->getErrorMsg());

            /* 创建仓库 */
            $store_data = [
                'bids' => [$branch->getAttr('bid')],
                'name' => $branch->getAttr('branch_name'),
                'desc' => '校区仓库'
            ];
            $store_model = new MaterialStore();
            $rs = $store_model->createOneStore($store_data);
            if (!$rs) exception($account_model->getErrorMsg());

            $this->commit();
        } catch(Exception $e) {
            $this->rollback();
            //return $this->user_error(['msg' => $e->getMessage(), 'trace' => $e->getTrace()]);
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    //删除校区，更新校区的帐户
    public function delOneBranch(Branch $branch)
    {
        $bid = $branch->bid;

        $student = Student::get(['bid' => $bid]);
        if(!empty($student)) return $this->user_error('校区有相关学生，删除不了。');

        $m_account = new AccountingAccount();
        $account_list = $m_account->where("find_in_set($bid, bids)")->select();
        if(count($account_list) > 1) return $this->user_error('校区有多个财务帐户，不能删除');

        $this->startTrans();
        try {
            foreach ($account_list as $account) {
                if ($account instanceof AccountingAccount) {
                    $account_bids = $account->bids;
                    foreach ($account_bids as $key => $account_bid) {
                        if ($account_bid === $bid) {
                            unset($account_bids[$key]);
                            break;
                        }
                    }
                    $rs = $account->save(['bids' => $account_bids]);
                    if ($rs === false) throw new Exception('更新校区关联的帐户失败');
                }
            }

            $rs = $branch->delete();
            if ($rs === false) throw new Exception('删除校区失败');
            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;

    }

    public static function getMsId($bid)
    {
        $branch = Branch::get(['bid' => $bid]);
        return $branch ? $branch->ms_id : 0;
    }

    //获取校区电话
    public static function GetTel($bid, $get_system_tel = false)
    {
        $tel = '';
        $branch = Branch::get($bid);
        if(!empty($branch)) {
            $tel = $branch['branch_tel'];
        }

        if(empty($tel) && $get_system_tel) {
            $params = user_config('params');
            $tel = isset($params['mobile']) ? $params['mobile'] : '';
        }

        return $tel;
    }

    //获取校区公众号二维码
    public static function GetWechatQrcode($bid, $default = false)
    {
        $branch = Branch::get($bid);
        if(empty($branch)) return '';

        $appid = $branch['appid'];
        if(empty($appid) && !$default) return '';

        if(empty($appid) && $default) {
            $wxmp = Wxmp::get(['is_default' => 1]);
        } else {
            $wxmp = Wxmp::get(['authorizer_appid' => $appid]);
        }
        if(empty($wxmp)) return '';

        if(!empty($wxmp['qrcode_url'])) return $wxmp['qrcode_url'];

        try {
            $wechat = Wechat::getApp($wxmp['authorizer_appid']);
            $authorizer = $wechat->open_platform->getAuthorizerInfo($wxmp['authorizer_appid']);
        } catch(\Exception $e) {
            Log::record($e->getMessage(), 'error');
            return '';
        }

        return isset($authorizer['authorizer_info']['qrcode_url']) ? $authorizer['authorizer_info']['qrcode_url'] : '';
    }

    //获取校区上级大区dpt_id，储蓄卡用到
    public static function GetParentAreaId($id, $is_branch = true)
    {
        $m_dpt = new Department();

        if($is_branch) {
            $department = $m_dpt->where('bid', $id)->cache(2)->find();
            if(empty($department)) return 0;
        } else {
            $department = $m_dpt->cache(2)->find($id);
            if(empty($department)) return 0;
        }

        if($department['dpt_type'] == 2) {
            return $department['dpt_id'];
        }

        $parent_department = $m_dpt->where('dpt_id', $department['pid'])->cache(2)->find();
        if(empty($parent_department)) return 0;

        if($parent_department['dpt_type'] == 2) {
            return $parent_department['dpt_id'];
        } else {
            return Branch::GetParentAreaId($parent_department['dpt_id'], false);
        }

    }

}