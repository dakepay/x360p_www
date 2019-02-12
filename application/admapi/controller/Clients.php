<?php
/**
 * Author: luo
 * Time: 2017/12/4 17:58
 */

namespace app\admapi\controller;

use app\admapi\model\Client;
use app\admapi\model\App;
use app\admapi\model\ClientFollowUp;
use app\admapi\model\DatabaseConfig;
use think\Request;
use think\Config;

class Clients extends Base
{

    public function get_list(Request $request)
    {
        $input = $request->request();
        //$input['og_id'] = 0;
        $m_client = new Client();
        if(isset($input['is_db_install']) && intval($input['is_db_install']) == 1){
            $input['cid'] = ['GT',2];
        }
        if(!empty($input['my_eid'])){
            $m_client->where('eid|service_eid',$input['my_eid']);
            unset($input['my_eid']);
        }
        if(!empty($input['expire'])){
            $expire_days = intval($input['expire']);
            $w = [];
            if($expire_days == 1){
                $expire_day = int_day(time() + 86400*7);
                $w['expire_day'] = ['GT',$expire_day];
                $m_client->where($w);
            }elseif($expire_days == 2){
                $expire_day = int_day(time() + 86400*30);
                $w['expire_day'] = ['GT',$expire_day];
                $m_client->where($w);
            }elseif($expire_days == 3){
                $expire_day = int_day(time() + 86400*90);
                $w['expire_day'] = ['GT',$expire_day];
                $m_client->where($w);
            }elseif($expire_days == 4){
                $expire_day = int_day(time());
                $w['expire_day'] = ['LT',$expire_day];
                $m_client->where($w);
            }
            unset($input['expire']);
        }

        $ret = $m_client->getSearchResult($input);
        foreach($ret['list'] as &$client) {
            $database = DatabaseConfig::get(['cid' => $client['cid']]);
            if(!empty($database)) {
                $client['is_open_account'] = 1;
            } else {
                $client['is_open_account'] = 0;
            }
        }

        return $this->sendSuccess($ret);
    }

    /**
     * 获得加盟商列表
     * @param Request $request
     */
    public function get_list_orgs(Request $request)
    {
        $cid = input('id/d');
        $w['cid'] = $cid;
        $dc = db('database_config','center_database')->where($w)->find();
        if(!$dc){
            return $this->sendSuccess([]);
        }
        reset_db_config($dc);
        $input = $request->get();
        $mOrg = new \app\api\model\Org();
        unset($input['cid']);
        $ret = $mOrg->append([],true)->getSearchResult($input);

        foreach($ret['list'] as &$row) {
            $user = db('user',$dc)->where('is_main=1')->where('og_id', $row['og_id'])->find();
            $row['user'] = $user;
        }

        return $this->sendSuccess($ret);
    }


    public function get_list_users(Request $request)
    {
        $cid = input('id/d');
        $w['cid'] = $cid;
        $dc = db('database_config','center_database')->where($w)->find();
        $input = $request->get();
        $m_user = new \app\api\model\User();
        $ret    = $m_user->setConnection($dc)->append([],true)->where('user_type',1)->getSearchResult($input);

        return $this->sendSuccess($ret);
    }

    public function get_list_databaseconfig(Request $request)
    {
        $cid = input('id/d');
        $m_dc = new DatabaseConfig();
        $w['cid'] = $cid;

        $dc = $m_dc->where($w)->find();

        return $this->sendSuccess($dc);
    }

    public function post(Request $request)

    {
        $data = $request->post();
        $m_client = new Client();
        $rs = $m_client->addOneClient($data);
        if(!$rs) return $this->sendError(400, $m_client->getErrorMsg());

        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $input = $request->put();
        if(isset($input['expire_day'])){
            $input['expire_day'] = format_int_day($input['expire_day']);
        }
        $cid = input('id/d', 0);
        $client = Client::get(['cid' => $cid]);
        if(empty($client)) return $this->sendError(400, '不存在此客户');

        $rs = $client->updateClient($client, $input);
        if($rs === false) return $this->sendError(400, $client->getErrorMsg());

        return $this->sendSuccess();
    }

    public function delete(Request $request)

    {
        $cid = input('id/d');
        $client = Client::get(['cid' => $cid]);
        if(empty($client)) return $this->sendError(400, '不存在此客户');

        if($client->is_db_install == 1){
            if($request->confirm){
                $rs = $client->deleteClient($client);
                if($rs === false) return $this->sendError(400, $client->getErrorMsg());
            }else{
                return $this->sendConfirm('该客户数据库已安装,您确定要删除此客户吗?删除会连带数据库一起删除掉,请谨慎选择!');
            }
        }else{
            $rs = $client->deleteClient($client);
            if($rs === false) return $this->sendError(400, $client->getErrorMsg());
        }
        

        return $this->sendSuccess();
    }

    public function post_follow_up(Request $request)

    {
        $cid = input('id/d');
        $input = $request->post();
        $client = Client::get(['cid' => $cid]);
        if(empty($client)) return $this->sendError(400, '客户不存在');

        $input['cid'] = $cid;
        $m_cfu = new ClientFollowUp();
        $rs = $m_cfu->addOneFollowUp($input);
        if($rs === false) return $this->sendError(400, $m_cfu->getErrorMsg());

        return $this->sendSuccess();
    }

    public function do_exesql(Request $request)
    {
        $cid = input('id/d');
        $input = $request->post();
        $m_client = Client::get(['cid'=>$cid]);
        if(empty($m_client)) return $this->sendError(400, '客户不存在');
        $rs = $m_client->exeSql($input['sql']);
        if($rs === false){
            return $this->sendError(400,$m_client->getError());
        }

        return $this->sendSuccess($rs);
    }

    public function do_renew(Request $request)
    {
        $cid = input('id/d');
        $input = $request->post();
        $m_client = new Client();
        $rs = $m_client->renew($cid, $input);
        if($rs === false) return $this->sendError(400, $m_client->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * 恢复出厂设置
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function do_resetdb(Request $request)
    {
        $cid = input('id/d');
        $m_client = Client::get(['cid'=>$cid]);
        if(empty($m_client)) return $this->sendError(400, '客户不存在');
        $result = $m_client->resetDb();
        if($result === false){
            return $this->sendError(400,$m_client->getError());
        }
        return $this->sendSuccess($result);
    }

    /**
     * 安装数据库
     * @return [type] [description]
     */
    public function do_install(Request $request)

    {
        $cid = input('cid/d');
        $client = Client::get(['cid' => $cid]);
        if(empty($client) || empty($client['host'])) {
            return $this->sendError(400, "客户不存在或者二级域名没填");
        }
        $input = request()->post();
        $m_dc = new DatabaseConfig();
        $result = $m_dc->installDb($client,$input);
        if(!$result){
            return $this->sendError(400,$m_dc->getErrorMsg());
        }
        return $this->sendSuccess($result);
    }

    /**
     * 生成登录token
     * @return [type] [description]
     */
    public function do_mktoken(Request $request)

    {
        $cid    = input('cid/d');
        $og_id  = input('og_id/d');
        $uid    = input('uid/d',1);

        $w['og_id'] = $og_id;
        if($og_id > 0){
            $w['parent_cid'] = $cid;
        }else{
            $w['cid'] = $cid;
        }
        $client = Client::get($w);
        if(empty($client) || empty($client['host'])) {
            return $this->sendError(400, "客户不存在或者二级域名没填");
        }



        $input = $request->get();

        if(isset($input['auth_token'])){
            //客户给的授权码登录
            $auth_token = $input['auth_token'];
            $client_login_user = cache($auth_token);
            if(!$client_login_user){
                return $this->sendError(400,'授权码不存在!');
            }
            $uid = $client_login_user['uid'];
        }

        $option = [
            $cid,
            $og_id,
            request()->time(),
            request()->ip(),
            random_str()
        ];
        $token  = md5(implode('', $option));
        

        $cache_key = cache_key($token);

        $login_user['uid']   = $uid;
        $login_user['og_id'] = $og_id;
        $login_user['from']  = 'admin';

        $login_expire = config('api.login_expire');

        cache($cache_key,$login_user,$login_expire);

        $host = $client['host'];

        $base_domain = config('ui.domain');

        $tokenurl = sprintf("%s://%s.%s/#/tklogin?tk=%s",$request->scheme(),$host,$base_domain,$token);

        $ret['url'] = $tokenurl;

        return $this->sendSuccess($ret);
    }

    /**
     * 系统冻结
     * @param Request $request
     */
    public function do_frozen(Request $request)
    {
        $cid    = input('cid/d');
        $reason = input('reason/s');
        $m_client = Client::get(['cid' => $cid]);
        if(empty($m_client)) {
            return $this->sendError(400, "客户不存在");
        }
        $result = $m_client->frozen($reason);
        if(!$result){
            return $this->sendError(400,$m_client->getError());
        }

        return $this->sendSuccess('ok');

    }

    /**
     * 系统解冻
     * @param Request $request
     */
    public function do_unfrozen(Request $request){
        $cid    = input('cid/d');
        $m_client = Client::get(['cid' => $cid]);
        if(empty($m_client)) {
            return $this->sendError(400, "客户不存在");
        }
        $result = $m_client->unfrozen();
        if(!$result){
            return $this->sendError(400,$m_client->getError());
        }

        return $this->sendSuccess('ok');

    }

    /**
     * 后台添加应用
     * @param Request $request
     */
    public function client_do_app(Request $request)
    {
        $input = input();
        $m_client = new Client();
        $result = $m_client->clientAddApp($input);
        if(!$result){
            return $this->sendError(400,$m_client->getError());
        }

        return $this->sendSuccess();
    }

    /**
     *  续费天数
     * @param Request $request
     */
    public function expire(Request $request)
    {
        $input = input();
        if(!empty($input['expire_day'])){
            $input['expire_day'] = intval(format_int_day($input['expire_day']));
        }else{
            return $this->sendError(400, '请选择到期日期');
        }
        $cid = input('cid/d', 0);
        $client = Client::get(['cid' => $cid]);
        if(empty($client)) return $this->sendError(400, '不存在此客户');

        $rs = $client->expire($input);
        if($rs === false) return $this->sendError(400, $client->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     *  扩容
     * @param Request $request
     */
    public function capacity(Request $request)
    {
        $input = input();
        if(empty($input['num']) || $input['num']<1){
            return $this->sendError(400, '扩容数量不存在或小于1');
        }
        $cid = input('cid/d', 0);
        $client = Client::get(['cid' => $cid]);
        if(empty($client)) return $this->sendError(400, '不存在此客户');

        $rs = $client->capacity($input);
        if(!$rs) return $this->sendError(400, $client->getErrorMsg());

        return $this->sendSuccess();
    }

    /**do_assign
     * /api/clients/0/doassign
     * @desc  批量分配主要负责人
     * @param Request $request
     * @method POST
     */
    public function do_assign(Request $request)
    {
        $post = $request->post();
        $m_client = new Client();

        $result = $m_client->assignToEmployee($post['cids'],$post['service_eid'],$post['sale_eid']);

        if(!$result){
            return $this->sendError(400,$m_client->getError());
        }

        return $this->sendSuccess();
    }


}