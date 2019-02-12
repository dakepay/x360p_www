<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/10/21
 * Time: 14:25
 */
namespace app\api\model;

class WxmpRule extends Base
{
    public $reply_type = ['news', 'text', 'image', 'voice', 'video'];

    protected $skip_og_id_condition = true;

    public function keywords()
    {
        return $this->hasMany('WxmpRuleKeyword', 'rule_id', 'rule_id');
    }

    public function texts()
    {
        return $this->hasMany('WxmpReplyText', 'rule_id', 'rule_id');
    }

    public function images()
    {
        return $this->hasMany('WxmpReplyImage', 'rule_id', 'rule_id');
    }

    public function videos()
    {
        return $this->hasMany('WxmpReplyVideo', 'rule_id', 'rule_id');
    }

    public function voices()
    {
        return $this->hasMany('WxmpReplyVoice', 'rule_id', 'rule_id');
    }

    public function news()
    {
        return $this->hasMany('WxmpReplyNews', 'rule_id', 'rule_id');
    }

    public function setContaintypeAttr($value, $data)
    {
        return join(',', $value);
    }

    public function getContaintypeAttr($value, $data)
    {
        return explode(',', $value);
    }


    public function addRule($input)
    {
        $this->startTrans();
        try {
            $keywords = $input['keywords'];
            $replys   = $input['replys'];
            unset($input['keywords'], $input['replys']);
            $input['containtype'] = array_unique(array_column($replys, 'type'));
            $this->allowField(true)->save($input);
            $rule_id = $this->getData('rule_id');
            foreach ($keywords as $key => &$item) {
                if (empty($item['content']) || empty($item['type']) || !in_array($item['type'], [1, 2 ,3])) {
                    throw new \Exception('invalid parameter');
                }
                $item['wxmp_id'] = $input['wxmp_id'];
                $item['rule_id'] = $rule_id;
                $item['displayorder'] = $key;
            }
            $this->keywords()->saveAll($keywords);

            unset($item);
            foreach ($replys as $item) {
                $info = [];
                $info['rule_id'] = $rule_id;
                if (!empty($item['media_id'])) {
                    $info['media_id'] = $item['media_id'];
                }
                if (!empty($item['content'])) {
                    $info['content'] = $item['content'];
                }
                $method_name = rtrim($item['type'], 's') . 's';
                if (method_exists($this, $method_name)) {
                    $this->$method_name()->save($info);
                }
            }
        } catch (\Exception $exception) {
            $this->rollback();
            $this->error = $exception->getMessage();
            throw $exception;
            return false;
        }
        $this->commit();
        return true;
    }

    public function changeStatus()
    {
        $status = $this->getData('status');
        if ($status) {
            $this->save(['status' => 0]);
        } else {
            $this->save(['status' => 1]);
        }
        return true;
    }

    public static function removeRule(array $rule_ids, $except = false)
    {
        if (empty($rule_ids)) {
            return 'invalid parameter!';
        }
        $rule = WxmpRule::get($rule_ids[0]);
        if (empty($rule)) {
            return 'resource not found!';
        }
        $wxmp = Wxmp::get($rule['wxmp_id']);
        $default_message = $wxmp['default_message'];
        $welcome_message = $wxmp['welcome_message'];
        if (in_array($default_message, $rule_ids) || in_array($welcome_message, $rule_ids)) {
            return '不能删除正在使用中的关键字回复规则';
        }
        if (!$except) {
            WxmpRule::destroy($rule_ids, true);
        }
        WxmpRuleKeyword::whereIn('rule_id', $rule_ids)->delete(true);
        WxmpReplyText::whereIn('rule_id', $rule_ids)->delete(true);
        WxmpReplyImage::whereIn('rule_id', $rule_ids)->delete(true);
        WxmpReplyVoice::whereIn('rule_id', $rule_ids)->delete(true);
        WxmpReplyVideo::whereIn('rule_id', $rule_ids)->delete(true);
        WxmpReplyNews::whereIn('rule_id', $rule_ids)->delete(true);
        return true;
    }

    public function editRule($input)
    {
        $this->startTrans();
        try {
            self::removeRule([$this->getData('rule_id')], true);
            foreach ($input['keywords'] as &$item) {
                if (isset($item['keyword_id'])) {
                    unset($item['keyword_id']);
                }
            }
            $result = $this->addRule($input);
            if ($result == false) {
                $this->rollback();
                return false;
            }
        } catch (\Exception $exception) {
            $this->rollback();
            return $this->user_error($exception->getMessage());
        }
        $this->commit();
        return true;
    }
}