<?php
namespace app\api\controller;
use util\Webcall;
use app\api\model\WebcallCallLog;
use think\Request;

class WebcallCallLogs extends Base
{

    protected $withoutAuthAction = ['callback'];

    public function get_list(Request $request){
        $input = $request->get();
        $m_wcl = new WebcallCallLog();
        $ret = $m_wcl->getSearchResult($input);

        $client = gvar('client');
        $center_call_log_conn = db('vip_client_app', 'db_center');
        $mVca = $center_call_log_conn->where('cid', $client['cid'])->where(['app_ename' => 'webcall'])->find();
        $ret['volume_limit'] = 0;
        $ret['volume_used'] = 0;
        if (!empty($mVca)){
            $ret['volume_limit'] = floor($mVca['volume_limit'] / 60);
            $ret['volume_used'] = floor($mVca['volume_used'] / 60);
        }
//        $ret['total_cacu_minutes'] = $m_wcl->count('cacu_minutes');

        return $this->sendSuccess($ret);
    }


    /**
     * @desc  触宝通话回调
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function callback(Request $request)
    {
        $post = $request->post();
        //回调写入日志
        $log_path = ROOT_PATH.'public/data/cby_log/' . date('Ymd');
        if(!file_exists($log_path)) {
            mkdirss($log_path, 0777, true);
        }
        $source_params = "request_url: " . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] . "\n";
        $source_params .= file_get_contents('php://input');
        file_put_contents($log_path . '/' .date('Ymd') . '.log', '[' . date('Y-m-d H:i:s') . ']' . $source_params . "\n", FILE_APPEND);

        $mWcl = new WebcallCallLog();
        $result = $mWcl->updateCallLog($post);

        $webcall_config = config('webcall');
        $appkey     = $webcall_config['appkey'];
        $appsecret  = $webcall_config['appsecret'];

        $callback_url = ensure_https($request->domain()) . '/api/webcall_call_logs/callback';

        $wc = new Webcall($appkey, $appsecret);
        $wc->setPath($callback_url);

        if($result === false){
            $ret  = $wc->getReturnError($mWcl->getError());
        }else{
            $ret = $wc->getReturnSuccess();
        }
        exit($ret);
    }

    public function post(Request $request)
    {
        $input = $request->post();

        $mWcl = new WebcallCallLog();

        $callback_url = ensure_https($request->domain()) . '/api/webcall_call_logs/callback';

        $result = $mWcl->lanchCall($input,$callback_url);

        if(!$result){
            return $this->sendError(400,$mWcl->getErrorMsg());
        }

        return $this->sendSuccess($result);
    }

    /**
     * @desc  拨打状态
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function call_status(Request $request)
    {
        $token = input('token');
        if(empty($token)) return $this->sendError(400, '参数错误');

        $m_cl = new WebcallCallLog();
        $log = $m_cl->where('token', $token)->find();
        return $this->sendSuccess($log);
    }

    /**
     *  获取通话录音
     * @param Request $request
    * @method GET
     */
//    public function do_recording(Request $request){
//
//        $job = new WebcallAudio();
//        $input = input();
//
//        $aa = $job->download($input['data']);
//
////        $input = input('wcl_id/d');
////        if(empty($input)) return $this->sendError(400, '参数错误');
////        $mWcl = new WebcallCallLog();
////        $rs = $mWcl->getOneRecording($input);
////        if(!$rs){
////            return $this->sendError(400,$mWcl->getErrorMsg());
////        }
////        return $this->sendSuccess();
//
//    }

}