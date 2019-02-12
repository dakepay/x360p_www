<?php
/**
 * Author: luo
 * Time: 2018/3/29 9:02
 */

namespace app\api\controller;


use think\Request;

class System extends Base
{

    /**
     * @desc  重置数据库
     * @author luo
     * @method POST
     */
    public function reset(Request $request)
    {
        $m_system = new \app\api\model\System();
        $is_force = input('force', 0);
        $user = gvar('user');
        if($user['is_admin'] != 1) return $this->sendError(400, '只有admin才有权限进行此操作');

        $mobile = (new \app\api\model\User())->where('uid', $user['uid'])->value('mobile');
        if(empty($mobile)) return $this->sendError(400, 'admin还没有绑定手机号');
        $reset_code = input('reset_code');
        $rs = check_verify_code($mobile, $reset_code, 'reset_code');
        if($rs !== true) {
            return $this->sendError(400, $rs);
        }

        $post = $request->post();
        //选择要删除的表
        $table = !empty($post['table']) ? $post['table'] : [];

        $rs = $m_system->reset($is_force, $table);
        if($rs === false) {
            if($m_system->get_error_code() == $m_system::CODE_HAVE_RELATED_DATA) {
                return $this->sendConfirm($m_system->getErrorMsg());
            }

            return $this->sendError(400, $m_system->getErrorMsg());
        }
        
        return $this->sendSuccess();
    }


    /**
     * 获得最新的版本
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function latestversion(Request $request){
        $latest_time = strtotime("-7 days",time());

        $w['publish_date'] = ['EGT',date('Ymd',$latest_time)];

        $latest_version = $this->m_system_version->where($w)->order('publish_date DESC')->limit('0,1')->select();

        if(!$latest_version){
            $latest_version = [];
        }

        return $this->sendSuccess($latest_version);

    }

}