<?php
/**
 * Author: luo
 * Time: 2017/12/13 11:15
 */

namespace app\api\controller;


use app\api\model\Org;
use app\api\model\Franchisee;
use app\api\model\User;
use think\Request;

class Orgs extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->get();
        if(isset($input['eid'])){
            $input['charge_eid'] = $input['eid'];
            unset($input['eid']);
        }
        $m_org = new Org();
        $ret = $m_org->getSearchResult($input);

        $m_user = new User();
        foreach($ret['list'] as &$row) {
            $user = $m_user->skipOgId(true)->where('is_main=1')->where('og_id', $row['og_id'])->find();
            $row['user'] = $user;
        }

        $w['is_init'] = 0;
        $wait_check_nums = $m_org->where($w)->count();
        $ret['wait_check_nums'] = $wait_check_nums;

        return $this->sendSuccess($ret);
    }

    /**
     * 获得所有的加盟商账号
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function get_list_users(Request $request){
        $og_id = input('id/d');
        $input = $request->get();
        $input['user_type'] = 1;
        $m_user = new User();
        $input['og_id'] = $og_id;

        $ret    = $m_user->skipOgId()->getSearchResult($input);

        return $this->sendSuccess($ret);
    }


    public function post(Request $request)
    {
        $input = $request->post();
        $org_data = isset($input['org']) ? $input['org'] : [];
        $account_data = isset($input['user']) ? $input['user'] : [];

        $m_org = new Org();
        $rs = $m_org->addOrgAndAccount($org_data, $account_data);
        if($rs === false) return $this->sendError(400, $m_org->getErrorMsg());

        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $og_id = input('id/d');
        $org = Org::get($og_id);

        $input = $request->put();
        if(!isset($input['org'])) return $this->sendError(400, '参数错误');

        $rs = $org->updateOrg($input['org'], $og_id, $org);
        if($rs === false) return $this->sendError(400, $org->getErrorMsg());

        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $og_id = input('id/d');
        $is_force = input('force/d', 0);

        $org = Org::get(['og_id' => $og_id]);
        $rs = $org->delOneOrg($og_id, $org, $is_force);
        if($rs === false) {
            if($org->get_error_code() === $org::CODE_HAVE_RELATED_DATA) {
                return $this->sendConfirm($org->getErrorMsg());
            }
            return $this->sendError(400, $org->getErrorMsg());
        }

        return $this->sendSuccess();
    }


    /**
     * @desc  加盟商延期
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function post_renew(Request $request)
    {
        $og_id = input('id/d');
        $input = $request->post();
        if(!isset($input['new_day']) || empty($input['new_day'])) return $this->sendError(400, '延期日期不正确');

        $m_org = new Org();
        $rs = $m_org->renew($og_id, $input);
        if($rs === false) return $this->sendError(400, $m_org->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  冻结加盟商
     * @author luo
     * @method POST
     */
    public function do_freeze(Request $request)
    {
        $og_id = input('id/d');
        $is_force = input('force/d', 0);
        $org = Org::get($og_id);
        if(empty($org)) return $this->sendError(400, '加盟商不存在');

        $rs = $org->freeze($og_id, $org, $is_force);
        if($rs === false) {
            if($org->get_error_code() == Org::CODE_HAVE_RELATED_DATA) {
                return $this->sendConfirm($org->getErrorMsg());
            }
            return $this->sendError(400, $org->getErrorMsg());
        }

        return $this->sendSuccess();
    }

    /**
     * @desc  加盟商解冻
     * @author luo
     * @method POST
     */
    public function do_unfreeze(Request $request)
    {
        $og_id = input('id/d');
        $mOrg = new Org();
        $rs = $mOrg->unfreeze($og_id);
        if($rs === false) return $this->sendError(400, $mOrg->getError());

        return $this->sendSuccess();
    }

    /**
     * 生成登录token
     * @return [type] [description]
     * post: /api/orgs/1/domktoken
     * { uid: 1}
     */
    public function do_mktoken(Request $request)
    {
        $client = gvar('client');
        $cid    = $client['cid'];
        $org_id  = input('id/d');
        $uid    = input('uid/d',1);

        $org_client = db('client','center_database')->where(['parent_cid'=>$cid,'og_id'=>$org_id])->find();
        if(!$org_client || empty($org_client['host'])){
            return $this->sendError(400, "加盟商不存在或者二级域名没填");
        }
        $option = [
            $cid,
            $org_id,
            request()->time(),
            request()->ip(),
            random_str()
        ];
        $token  = md5(implode('', $option));


        $cache_key = cache_key($token);

        $login_user['uid']   = $uid;
        $login_user['og_id'] = $org_id;
        $login_user['from']  = 'org';

        $login_expire = config('api.login_expire');

        cache($cache_key,$login_user,$login_expire);

        $host = $org_client['host'];

        $base_domain = config('ui.domain');

        $tokenurl = sprintf("%s://%s.%s/#/tklogin?tk=%s",$request->scheme(),$host,$base_domain,$token);

        $ret['url'] = $tokenurl;

        return $this->sendSuccess($ret);
    }


    public function do_config(Request $request)
    {
        $set_og_id = input('id/d');
        $input = $request->post();

        $m_org  = new Org();
        $result = $m_org->setConfig($set_og_id,$input);
        if(!$result){
            return $this->sendError(400,$m_org->getError());
        }

        return $this->sendSuccess('ok');
    }

}
