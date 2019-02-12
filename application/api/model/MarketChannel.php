<?php
/**
 * Author: luo
 * Time: 2018/4/20 11:16
 */

namespace app\api\model;


use think\Exception;

class MarketChannel extends Base
{
    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];

    protected $type = [
        'qr_config' => 'json'
    ];

    public static function FindOrAdd($channel_name)
    {
        if(empty($channel_name)) return 0;

        $model = new self();

        $channel = $model->where('channel_name', $channel_name)->find();
        if(!empty($channel)) return $channel['mc_id'];

        $rs = $model->isUpdate(false)->save(['channel_name' => $channel_name]);
        if($rs === false) return false;

        return $model->mc_id;
    }

    /**
     * 合并渠道
     * @param $mc_id
     * @param $mc_ids
     * @return bool
     */
    public function mergeChannel($mc_id,$mc_ids)
    {
        if (!is_array($mc_ids)){
            return $this->user_error('mc_ids not array');
        }
        $to_mcl_model = $this->get($mc_id);
        if (empty($to_mcl_model)){
            return $this->user_error('合并到渠道不存在');
        }
        $mMarkeClue = new MarketClue();
        $mStudent = new Student();

        $this->startTrans();
        try{
            foreach ($mc_ids as $k => $v){
                $be_cal = $this->get($v);
                if (empty($be_cal)){
                    return $this->user_error('被合并渠道不存在');
                }

                $rs = $mMarkeClue->updateMakeChannelID($v,$mc_id);
                if (!$rs){
                    $this->rollback();
                    return $this->user_error('market_clue');
                }

                $rs = $mStudent->updateMakeChannelID($v,$mc_id);
                if (!$rs){
                    $this->rollback();
                    return $this->user_error('student');
                }

                $rs = $this->uodateChannelNums($v,$mc_id);
                if (!$rs){
                    $this->rollback();
                    return $this->user_error('market channel num');
                }

                $rs = $be_cal->delete();
                if (!$rs){
                    $this->rollback();
                    return $this->user_error('market channel');
                }
            }
        }catch (\Exception $e){
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        $this->commit();
        return true;
    }

    /**
     * 合并渠道名单数量
     * @param $be_channel 被合并的
     * @param $to_channel 合并到的
     */
    public function uodateChannelNums($be_channel,$to_channel)
    {
        $to_mcl = $this->get($to_channel);
        $be_cal = $this->get($be_channel);

        $this->startTrans();
        try{
            $update = [
                'total_num' => $to_mcl['total_num'] + $be_cal['total_num'],
                'valid_num' => $to_mcl['valid_num'] + $be_cal['valid_num'],
                'visit_num' => $to_mcl['visit_num'] + $be_cal['visit_num'],
                'deal_num' => $to_mcl['deal_num'] + $be_cal['deal_num'],
            ];
            $update_w['mc_id'] = $to_mcl['mc_id'];
            $rs = $this->save($update,$update_w);
            if(!$rs){
                $this->rollback();
                return $this->user_error('market channel num');
            }
        }catch (\Exception $e){
            $this->rollback();
            return $this->deal_exception($e->getMessage(), $e);
        }

        $this->commit();
        return true;
    }

}