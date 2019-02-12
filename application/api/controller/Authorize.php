<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/11/30
 * Time: 10:06
 */
namespace app\api\controller;

use app\wxopen\controller\OpenApp;
use think\Request;

class Authorize extends OpenApp
{
    public $apiAuth = true;

    protected $callback_url = 'http://%s/wxopen/callback';
    protected $redirect_url = 'http://%s/api/redirect?url=%s';

    public function index(Request $request)
    {
        $redirect = input('redirect');
        if(!$redirect){
            $client_redirect_url = request()->url(true).'&redirect=1';
           
            exit('<script>location.href=\''.$client_redirect_url.'\';</script>');
        }else{
            $callback_url   = sprintf($this->callback_url,config('ui.domain'));
            $redirect_url   = sprintf($this->redirect_url,config('ui.domain'),urlencode(input('href')));
            $param['cid']   = input('cid');
            $param['og_id'] = $request->user['og_id'];
            $param['bids']  = input('bids', 0);
            $param['href']  = $redirect_url;
            $query_string   = http_build_query($param);
            $pre_auth = $this->openPlatform->pre_auth;
            $response = $pre_auth->redirect($callback_url . '?' . $query_string);
            $response->send(); 
        }
       
    }
}