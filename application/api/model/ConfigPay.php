<?php
/**
 * Author: luo
 * Time: 2018/3/1 15:38
 */

namespace app\api\model;

use think\Exception;
use think\Validate;

class ConfigPay extends Base
{
    const TYPE_WECHAT = 1;  # 微信支付
    const TYPE_SQB = 3;  # 收钱吧支付

    protected $key_path = PUBLIC_PATH . 'data' . DS . 'wxpay' . DS;

    protected $type = [
        'config' => 'json',
    ];
    protected $append = [
        'aa_id'
    ];
    protected $auto = ['appid'];

    public function setAppidAttr($value, $data)
    {
        if(!empty($data['config']) && is_string($data['config'])) {
            $tmp = json_decode($data['config'], true);
        } else {
            $tmp = $data['config'];
        }

        return isset($tmp['appid']) ? $tmp['appid'] : $value;
    }

    public function getAaIdAttr($value, $data) {

        $m_aa = (new AccountingAccount());

        $account = $m_aa->where('cp_id', $data['cp_id'])->field('aa_id')->find();
        return !empty($account) ? $account['aa_id'] : '';
    }

    public function getConfigAttr($value)
    {
        $value = json_decode($value, true);
        if(empty($value) || !is_array($value)) return $value;
        foreach($value as $key => &$str) {
            if(strpos($key, 'path') !== false) {
                $str = file_exists($str) ? file_get_contents($str) : '';
            }
        }
        return $value;
    }

    public function accountingAccount()
    {
        return $this->belongsTo('AccountingAccount', 'cp_id', 'cp_id');
    }

    //设置微信支付
    public function addConfig($post)
    {
        if(!isset($post['type']) || !isset($post['config'])) return $this->user_error('param error');
        if($post['type'] == self::TYPE_WECHAT) {
            $post = $this->makeWechatConfig($post);
            if($post === false) return $this->user_error($this->getError());
        }

        try {
            $this->startTrans();
            $rs = $this->allowField(true)->save($post);
            if ($rs === false) throw new Exception($this->getError());

            $cp_id = $this->getAttr('cp_id');
            if (isset($post['aa_id']) && $post['aa_id'] > 0) {
                $account = AccountingAccount::get($post['aa_id']);
                if (empty($account)) throw new Exception('收款帐户不存在');

                $rs = $account->save(['cp_id' => $cp_id]);
                if ($rs === false) throw new Exception('收款帐户绑定支付配置失败');
            }
        } catch (\Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        $this->commit();
        return true;
    }

    //设置微信支付
    public function addSqbConfig($post)
    {
        $post['config'] = [];
        if(!isset($post['type'])) return $this->user_error('param error');

        try {
            $this->startTrans();
            $rs = $this->allowField(true)->save($post);
            if ($rs === false) throw new Exception($this->getError());

            $cp_id = $this->getAttr('cp_id');
            if (isset($post['aa_id']) && $post['aa_id'] > 0) {
                $account = AccountingAccount::get($post['aa_id']);
                if (empty($account)) throw new Exception('收款帐户不存在');

                $rs = $account->save(['cp_id' => $cp_id]);
                if ($rs === false) throw new Exception('收款帐户绑定支付配置失败');
            }
        } catch (\Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        $this->commit();
        return true;
    }

    //更新支付配置
    public function updateConfig($put)
    {
        $old_data = $this->getData();
        if(empty($old_data)) return $this->user_error('程序错误');

        if(isset($put['type']) && $put['type'] != $old_data['type']) return $this->user_error('支付类型不能修改');

        $old_data_config = $this->getAttr('config');
        if($old_data['type'] == self::TYPE_WECHAT) {
            if(isset($put['config']['appid']) && $old_data_config['appid'] != $put['config']['appid']) {
                @remove_dir($this->key_path . $old_data_config['appid']);
            }
            $put = $this->makeWechatConfig($put);
            if($put === false) return $this->user_error($this->getError());
        }

        try {
            $this->startTrans();
            $rs = $this->allowField(true)->save($put);
            if ($rs === false) return $this->user_error($this->getError());

            if (isset($put['aa_id']) && $put['aa_id'] > 0) {
                $m_aa = new AccountingAccount();
                $account = $m_aa->where('aa_id', $put['aa_id'])->find();
                if (empty($account)) throw new Exception('收款帐户不存在');

                $m_aa->where('cp_id', $old_data['cp_id'])->update(['cp_id' => 0]);
                $rs = $m_aa->where('aa_id', $put['aa_id'])->update(['cp_id' => $old_data['cp_id']]);
                if ($rs === false) throw new Exception('收款帐户绑定支付配置失败');
            }
        } catch (\Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        $this->commit();
        return true;
    }

    //更新支付配置
    public function updateSqbConfig($put)
    {
        $old_data = $this->getData();
        if(empty($old_data)) return $this->user_error('程序错误');

        if(isset($put['type']) && $put['type'] != $old_data['type']) return $this->user_error('支付类型不能修改');

        $this->startTrans();
        try {
            $this->startTrans();
            $rs = $this->allowField(true)->save($put);
            if ($rs === false) return $this->user_error($this->getError());

            if (isset($put['aa_id']) && $put['aa_id'] > 0) {
                $m_aa = new AccountingAccount();
                $account = $m_aa->where('aa_id', $put['aa_id'])->find();
                if (empty($account)) throw new Exception('收款帐户不存在');

                $m_aa->where('cp_id', $old_data['cp_id'])->update(['cp_id' => 0]);
                $rs = $m_aa->where('aa_id', $put['aa_id'])->update(['cp_id' => $old_data['cp_id']]);
                if ($rs === false) throw new Exception('收款帐户绑定支付配置失败');
            }
        } catch (\Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        $this->commit();
        return true;
    }


    public function delConfig($cp_id)
    {
        $old_data = $this->getData();
        if(empty($old_data)) return $this->user_error('程序错误');

        $this->startTrans();
        try {
        $account = AccountingAccount::get(['cp_id' => $cp_id]);
        if(!empty($account)) {
            $rs = $account->save(['cp_id' => 0]);
            if($rs === false) return false;
        }

        $old_data_config = $this->getAttr('config');
        if($old_data['type'] == self::TYPE_WECHAT) {
            if(isset($old_data_config['appid'])) {
                @remove_dir($this->key_path . $old_data_config['appid']);
            }
        }
        $rs = $this->delete();
        if($rs === false) return false;

        } catch (\Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        $this->commit();
        return true;
    }

    //创建证书文件
    private function makeKeyFile($appid, $file_name, $text)
    {
        $key_path = $this->key_path . $appid . DS . $file_name;
        $rs = write_file($key_path, $text);
        if($rs === false) return $this->user_error($file_name . '创建失败');

        return $key_path;
    }

    //把证书文本生成文件
    private function makeWechatConfig($post)
    {
        if(!isset($post['config']) || empty($post['config'])) return $this->user_error('config字段错误');
        $rule = [
            'appid' => 'require',
            'merchant_id|商户号' => 'require',
            'key|接口密钥' => 'require',
            'cert_path|支付证书文本' => 'require',
            'key_path|证书密钥文本' => 'require',
        ];
        $validate = new Validate();
        $rs = $validate->check($post['config'], $rule);
        if($rs === false) return $this->user_error($validate->getError());

        $cert_path = $this->makeKeyFile($post['config']['appid'],'apiclient_cert.pem', $post['config']['cert_path']);
        if($cert_path === false) return false;
        $post['config']['cert_path'] = $cert_path;

        $key_path = $this->makeKeyFile($post['config']['appid'],'apiclient_key.pem', $post['config']['key_path']);
        if($key_path === false) return false;
        $post['config']['key_path'] = $key_path;

        return $post;
    }


    // 收钱吧密钥更新
    public function updateTerminal($cp_id,$terminal){
        $config = json_encode($terminal);
        $w['cp_id'] = $cp_id;
        $rs = $this->where($w)->update(['config'=>$config]);
        if (!$rs) {
            return $this->user_error('支付配置更改失败' . $rs->getError());
        }
        return true;
    }

}