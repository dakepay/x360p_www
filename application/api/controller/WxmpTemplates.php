<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/11/25
 * Time: 9:28
 */
namespace app\api\controller;

use app\api\model\WxmpTemplate;
use app\common\Wechat;
use think\Exception;
use think\Request;

class WxmpTemplates extends Base
{
    /*返回已经设置的模板信息*/
    public function get_list(Request $request)
    {
        $appid = input('appid');
        $wechat = Wechat::getInstance($appid);
        $data = [];
        $m_wt = new WxmpTemplate();
        $data['list'] = WxmpTemplate::all(['appid' => $wechat->appid]);

        $notice = Wechat::getApp($appid)->notice;
        $data['wechat_list'] = $notice->getPrivateTemplates();

        $need_clear_tpl = true;
        $org_api = user_config('org_api');
        if(isset($org_api['message_push_callback_url']) && !empty($org_api['message_push_callback_url'])){
            $need_clear_tpl = false;
        }
        //如果客户在微信公众号后台已经删除了某个模板，则把数据库的模板也删除
        if($need_clear_tpl) {
            foreach ($data['list'] as $key => &$local_item) {
                if (!isset($data['wechat_list']['template_list'])) break;

                $remote_list = $data['wechat_list']['template_list'];
                $in_remote = false;
                foreach ($remote_list as $remote_item) {
                    if ($local_item['template_id'] == $remote_item['template_id']) {
                        $in_remote = true;
                        break;
                    }
                }
                if ($in_remote == false) {
                    $m_wt->where('template_id', $local_item['template_id'])->delete(true);
                    unset($data['list'][$key]);
                }

            }
        }

        return $this->sendSuccess($data);
    }

    public function remove(Request $request)
    {
        $list = config('tplmsg');
        $local_tpl_ids = [];
        foreach ($list as $item) {
            $local_tpl_ids[] = $item['weixin']['template_id'];
        }
        unset($item);

        $notice = Wechat::getApp()->notice;
        $wechat_list = $notice->getPrivateTemplates();

        $wechat_tpl_ids = [];
        foreach ($wechat_list['template_list'] as $item) {
            $wechat_tpl_ids[] = $item['template_id'];
        }
        $diff = array_diff($wechat_tpl_ids, $local_tpl_ids);
        foreach ($diff as $tpl_id) {
            $response = $notice->deletePrivateTemplate($tpl_id);
            if ($response['errcode'] !== 0) {
                return $this->sendError(400, $response['errmsg']);
            }
        }
        return $this->sendSuccess();
    }

    /*返回设置的行业列表*/
    public function industry(Request $request)
    {
        $notice = Wechat::getApp()->notice;
        $list = $notice->getIndustry();
        return $this->sendSuccess($list);
    }

    /*修改账号所属行业*/
    public function set_industry(Request $request)
    {
        $model = new WxmpTemplate();
        $rs = $model->setIndustry();
        if (!$rs) {
            return $this->sendError(400, $model->getError());
        }
        return $this->sendSuccess();
    }

    /*一键同步模板消息配置*/
    /**
     * https://chahe.pro.xiao360.com/#/system/configs/wxmp?appid=wx99769d25a27c9ee7
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function sync(Request $request)
    {
        $m_template = new WxmpTemplate();
        $appid = input('appid');
        $rs = $m_template->syncTemplate($appid);

        if(!empty($m_template->sync_fail_info) && $m_template->get_error_code() != 303) {
            $msg = is_array($m_template->sync_fail_info) ? implode(',', $m_template->sync_fail_info) : $m_template->sync_fail_info;
            return $this->sendError(400, $msg);
        }

        if($rs === true) {
            return $this->sendSuccess();
        }

        //可能没设置行业信息，需要返回给前端确认设置行业信息
        $fail_info = [];
        if (!$rs) {
            $fail_info = $m_template->sync_fail_info;
            $fail_info['errormsg'] = $m_template->getError();
        }
        $temp = $this->system_status($request);
        $data['info'] = array_merge($temp, $fail_info);
        return $this->sendSuccess($data);
    }

    /*客户公众号行业和消息模板状态*/
    protected function system_status(Request $request)
    {
        $appid = $request->param('appid');
        $data['default_industry'] = array_values(config('wxopen.industry'));
        $notice   = Wechat::getApp($appid)->notice;

        try {
            $industry = $notice->getIndustry();
        } catch (\Exception $e) {
            $industry = [];
        }

        $customer_industry = !is_array($industry) ? $industry->toArray() : [];
        $data['original_customer_industry'] = $customer_industry;
        $data['customer_industry'] = [];
        foreach ($customer_industry as $key => $item) {
            if (!empty($customer_industry[$key]['first_class'])) {/*有可能只设置了一个行业*/
                $data['customer_industry'][] = join('-', array_values($item));
            }
        }
        $data['customer_templates'] = $notice->getPrivateTemplates()['template_list'];
        unset($item);
        foreach ($data['customer_templates'] as &$item) {
            $temp = $item['primary_industry'] . '-' . $item['deputy_industry'];
            if (!in_array($temp, $data['default_industry'])) {
                $item['delete'] = true;
            }
        }
        return $data;
    }

    public function do_test(Request $request)
    {
        $id = $request->param('id');
        $template = WxmpTemplate::get($id);
        if (empty($template)) {
            return $this->sendError('recource not found!', 400);
        }
        $rs = $template->test();
        if (!$rs) {
            return $this->sendError(400, $template->getError());
        }
        return $this->sendSuccess();
    }
}