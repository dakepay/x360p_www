<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/10/20
 * Time: 18:40
 */
namespace app\api\controller;

use app\api\model\Wxmp;
use app\api\model\WxmpRule;
use app\api\model\WxmpRuleKeyword;
use think\Request;

class WxmpRules extends Base
{
    public function get_list(Request $request)
    {
        $wxmp_id = $request->param('wxmp_id');
        $keyword = $request->param('keyword');
        $name  = $request->param('name');
        $where = [];
        $where['wxmp_id'] = $wxmp_id;
        if ($name) {
            $where['name'] = ['like', '%' . $name . '%'];
        }
        if ($keyword) {
            $w = [];
            $w['content'] = ['like', '%' . $keyword . '%'];
            $w['wxmp_id'] = $wxmp_id;
            $rule_ids = WxmpRuleKeyword::where($w)->column('rule_id');
            if ($rule_ids) {
                $where['rule_id'] = ['in', $rule_ids];
            }
        }
        $result = m('WxmpRule')->where($where)->with(['keywords'])->withCount(['keywords', 'texts', 'images', 'voices', 'news', 'videos'])->getSearchResult([], true);

        /*获取公众号关注事件的欢迎消息和默认的回复消息*/
        $wxmp_id = $request->param('wxmp_id');//todo
        $wxmp    = Wxmp::get($wxmp_id);
        $default = [];
        $welcome = [];
        $default_rule_id = $wxmp['default_message'];
        if (!empty($default_rule_id)) {
            $default['rule_id']  = $default_rule_id;
            $default['keywords'] = WxmpRuleKeyword::where(['rule_id' => $default_rule_id])->column('content');
        }
        $welcome_rule_id = $wxmp['welcome_message'];
        if (!empty($welcome_rule_id)) {
            $welcome['rule_id']  = $welcome_rule_id;
            $welcome['keywords'] = WxmpRuleKeyword::where(['rule_id' => $welcome_rule_id])->column('content');
        }
        $result['default'] = $default;
        $result['welcome'] = $welcome;
        return $this->sendSuccess($result);
    }

    public function get_detail(Request $request, $id = 0)
    {
        $id = $request->param('id');
        $rule = WxmpRule::get($id, ['keywords', 'texts', 'images', 'voices', 'videos', 'news']);
        $rule = $rule->toArray();
        $rule['replys'] = [];
        $types = ['texts','images','voices','videos','news',];
        foreach ($types as $type) {
            if (!empty($rule[$type])) {
                foreach ($rule[$type] as &$item) {
                    if ($type != 'texts') {
                        $material = $item['material'];
                        unset($item['material']);
                        $item = $material;
                    }
                    $item['type'] = rtrim($type, 's');
                    if ($type == 'news') {
                        $item['type'] = 'news';
                    }
                }
                $rule['replys'] = array_merge($rule['replys'], $rule[$type]);
            }
            unset($rule[$type]);
        }
        return $this->sendSuccess($rule);
    }

    public function post(Request $request)
    {
        $input = $request->post();
        $rule  = [
            'wxmp_id'               => 'require|number',
            'name|规则名'            => 'max:255',
            'status|是否开启'        => 'boolean',
            'displayorder|回复优先级' => 'between:0,255',
            'keywords|关键字数组'     => 'require|array',
            'replys|回复内容数组'     => 'require|array',
        ];
        $right = $this->validate($input, $rule);
        if ($right !== true) {
            return $this->sendError(400, $right);
        }
        $rule = new WxmpRule();
        $result = $rule->addRule($input);
        if (!$result) {
            return $this->sendError(400, $rule->getError());
        }
        return $this->sendSuccess($result);
    }

    public function put(Request $request)
    {
        $id = $request->param('id');
        $m_rule = WxmpRule::get($id);
        if (!$m_rule) {
            return $this->sendError(404, 'resource not found!');
        }
        $input = $request->put();
        $rule  = [
            'name|规则名'            => 'max:255',
            'status|是否开启'        => 'boolean',
            'displayorder|回复优先级' => 'between:0,255',
            'keywords|关键字数组'     => 'require|array',
            'replys|回复内容数组'     => 'require|array',
        ];
        $right = $this->validate($input, $rule);
        if ($right !== true) {
            return $this->sendError(400, $right);
        }
        $result = $m_rule->editRule($input);
        if (!$result) {
            return $this->sendError(400, $m_rule->getError());
        }
        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $id = $request->param('id');
        $result = WxmpRule::removeRule([$id]);
        if ($result !== true) {
            return $this->sendError(400, $result);
        }
        return $this->sendSuccess();
    }

    public function batch_delete(Request $request)
    {
        $ids = $request->post('ids/a');
        if (empty($ids) || !is_array($ids)) {
            return $this->sendError(400, 'invalid parameter');
        }
        $result = WxmpRule::removeRule($ids);
        if ($result !== true) {
            return $this->sendError(400, $result);
        }
        return $this->sendSuccess();
    }

    public function do_change_status(Request $request)
    {
        $id = $request->param('id');
        $rule = WxmpRule::get($id);
        if (!$rule) {
            return $this->sendError(404, 'resource not found!');
        }
        $result = $rule->changeStatus();
        if (!$result) {
            return $this->sendError(400, $rule->getError());
        }
        return $this->sendSuccess();
    }
}