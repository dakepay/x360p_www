<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/6/28
 * Time: 11:21
 */
namespace app\api\model;

use think\Exception;

class AccountingAccount extends Base
{
    const TYPE_CASH = 0; # 现金
    const TYPE_BANK = 1; # 银行存款
    const TYPE_ELECTRONIC = 2; # 电子钱包

	public function getBidsAttr($value, $data)
    {
       return split_int_array($value);
    } 

    public function setBidsAttr($value, $data)
    {
        if (is_array($value)) {
            return join(',', $value);
        }
        return $value;
    }

    public function tallyHelp()
    {
        return $this->hasOne('TallyHelp', 'th_id', 'th_id');
    }

    public function createOneAccount($data)
    {
        $this->startTrans();
        try {

            $rs = $this->data([])->allowField(true)->isUpdate(false)->save($data);

            if (!$rs) return $this->user_error('创建帐户失败');
            $aa_id = $this->getLastInsID();
            if (isset($data['start_amount']) && $data['start_amount'] > 0) {
                $tally_data = $this->makeStartTallyData($aa_id, $data['start_amount']);
                $rs = (new Tally())->createOneTally($tally_data);
                if(!$rs) exception('创建初始流水失败');
            }
            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        return true;
    }

    //建立帐户初始流水
    public function makeStartTallyData($aa_id, $amount)
    {
        $tally = [
            'type' => Tally::TALLY_TYPE_INCOME,
            'cate' => Tally::CATE_INCOME,
            'aa_id' => $aa_id,
            'amount' => $amount,
            'remark' => '创建帐户初始金额',
            'int_day' => date('Ymd'),
        ];
        return $tally;
    }

    //根据帐户取得支付配置
    public static function getConfigByAaId($aa_id)
    {
        $account = self::get($aa_id);
        if(empty($account) || $account['cp_id'] <= 0) return [];

        $config_pay = ConfigPay::get($account['cp_id']);
        if(empty($config_pay)) return [];

        $config_pay['config'] = $config_pay->getData('config');
        $config_pay['config'] = json_decode($config_pay['config'], true);

        return $config_pay;
    }

	
}