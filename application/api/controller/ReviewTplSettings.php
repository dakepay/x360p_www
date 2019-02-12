<?php
/**
 * Author: luo
 * Time: 2017/12/28 19:53
 */

namespace app\api\controller;

use app\api\model\ReviewTplSetting;
use think\Request;

class ReviewTplSettings extends Base
{

    /**
     * @desc  点评模板
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function get_list(Request $request)
    {
        $input = $request->get();

        $m_rts = new ReviewTplSetting();
        $ret = $m_rts->order('rts_id asc')->getSearchResult($input);
        if(empty($ret['list'])) {
            $tpl = config('org_review_tpl');
            $ret['list'][0] = [
                'setting' => $tpl,
                'rts_id' => 0,
                'tpl_style' => 0,
                'og_id' => 0,
                'bid' => 0,
                'name' => '默认配置模板'
            ];
        }

        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $post = $request->post();
        $m_rts = new ReviewTplSetting();
        $rs = $m_rts->isUpdate(false)->allowField(true)->save($post);
        if($rs === false) return $this->sendError(400, $m_rts->getErrorMsg());
        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        return parent::put($request);
    }

    /**
     * @desc  删除模板
     * @author luo
     * @param Request $request
     * @method DELETE
     */
    public function delete(Request $request)
    {
        $is_force = input('force/d', 0);
        $rts_id = input('id/d');

        $tpl = ReviewTplSetting::get($rts_id);

        $rs = $tpl->delOneTpl($rts_id, $tpl, $is_force);
        if($rs === false) {
            if($tpl->get_error_code() == $tpl::CODE_HAVE_RELATED_DATA) {
                return $this->sendConfirm($tpl->getErrorMsg());
            }
            return $this->sendError(400, $tpl->getErrorMsg());
        }

        return $this->sendSuccess();
    }

}