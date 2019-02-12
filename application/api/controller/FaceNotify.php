<?php

namespace app\api\controller;


use think\Request;
use think\cache\driver\Redis;
use app\api\model\FaceNotifyRecord;
use app\api\model\Student;
use app\api\model\CourseArrange;


class FaceNotify extends Base{
    public $apiAuth = false;
    public $noRest = true;

    /**
     * 面部信息录入通知
     * @param Request $request
     */
    public function input(Request $request){

        //回调写入日志
        $log_path = ROOT_PATH.'public/data/face_notify_log/' . date('Ymd');
        if(!file_exists($log_path)) {
            mkdirss($log_path, 0777, true);
        }
        $source_params = "request_url: " . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] . "\n";
        $source_params .= file_get_contents('php://input');
        file_put_contents($log_path . '/' .date('Ymd') . '.log', '[' . date('Y-m-d H:i:s') . ']' . $source_params . "\n", FILE_APPEND);


        try {
            $input = $request->post();
            if (empty($input)) {
                return $this->sendError(400, 'token不存在');
            }

            if(!isset($input['detail']) || empty($input['detail'])){
                return $this->sendError(400,'缺少参数detail!');
            }

            if(strpos($input['detail'],'|') === false){
                return $this->sendError(400,'参数detail不合法!');
            }

            $face_id = 0;
            if(isset($input['person_id'])){
                $face_id = intval($input['person_id']);
            }
            $head_image_url = '';
            if(isset($input['image_head_url']) && !empty($input['image_head_url'])){
                $head_image_url = $input['image_head_url'];
            }

            $res = explode('|', $input['detail']);
            $redis_key = 'skfrm_' . $res[0];
            $token = $res['3'] . '|' . $res['0'];

            $redis = new Redis();
            $redis_token = $redis->get($redis_key);
            if ($token != $redis_token) {
                return $this->sendError(400, '非法token');
            }

            $cid = $res[1];
            $og_id = $res[2];
            $sid = $res[3];

            $client = db('client','db_center')->where('cid',$cid)->find();
            if(!$client){
                return $this->sendError(400,'invalid argument!');
            }
            $db_cid = $cid;
            if($client['parent_cid'] > 0){
                $db_cid = $client['parent_cid'];
            }
            $dbcfg = db('database_config','db_center')->where('cid',$db_cid)->find();
            if(!$dbcfg){
                return $this->sendError(400,'invalid argument!');
            }
            config('database',$dbcfg);
            gvar('og_id',$og_id);
            gvar('client',$client);

            $m_student = new Student();

            $student = $m_student->where('sid',$sid)->find();

            if(!$student){
                return $this->sendError('invalid argument!sid not exists');
            }

            $student->is_face_input = 1;
            $student->face_id = $face_id;
            if($head_image_url != '' && empty($student->photo_url)){
                $student->photo_url = $head_image_url;
            }
            $student->save();
        }catch(\Exception $e){
            return $this->sendError($e->getMessage());
        }
        return $this->sendSuccess();
    }

    /**
     * 面部识别打卡考勤
     * @param Request $request
     */
    public function attendance(Request $request){
        //回调写入日志
        $log_path = ROOT_PATH.'public/data/face_notify_attendance_log/' . date('Ymd');
        if(!file_exists($log_path)) {
            mkdirss($log_path, 0777, true);
        }
        $source_params = "request_url: " . $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] . "\n";
        $source_params .= file_get_contents('php://input');
        file_put_contents($log_path . '/' .date('Ymd') . '.log', '[' . date('Y-m-d H:i:s') . ']' . $source_params . "\n", FILE_APPEND);

        $input = $request->post();
        $key = '123456';

        if(!isset($input['detail'])){
            return $this->sendError(400,'缺少参数detail!');
        }

        if(strpos($input['detail'],'|') === false){
            return $this->sendError(400,'参数detail不合法!');
        }


        $str = $input['person_id']. '&' .$input['detail']. '&' .$key;
        $token = strtoupper(md5($str));
        if ($token != $input['token']){
            return $this->sendError(400,'非法数据');
        }

        $res = explode('|', $input['detail']);

        $cid = $res[1];
        $og_id = $res[2];

        $client = db('client','db_center')->where('cid',$cid)->find();
        if(!$client){
            return $this->sendError(400,'invalid argument!');
        }
        $client['domain'] = $client['host'];
        $db_cid = $cid;
        if($client['parent_cid'] > 0){
            $db_cid = $client['parent_cid'];
        }
        $dbcfg = db('database_config','db_center')->where('cid',$db_cid)->find();
        if(!$dbcfg){
            return $this->sendError(400,'invalid argument!');
        }
        config('database',$dbcfg);
        gvar('og_id',$og_id);
        gvar('client',$client);



        $user = [
            'name'    => 'skfrm',
            'uid'   => 0
        ];
        $user = new \app\api\model\User($user);
        request()->bind('user',$user);

        $fcn_model = new FaceNotifyRecord();
        $res = $fcn_model->swipeFace($input['person_id']);
        if(!$res){
            return $this->sendError(400,$fcn_model->getError());
        }
        return $this->sendSuccess($res);

    }
}