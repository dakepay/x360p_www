<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/15
 * Time: 18:43
 */
namespace app\api\export;

use app\common\Export;
use app\api\model\MarketChannel;


class MarketChannels extends Export
{
    protected $res_name = 'market_channel';

    protected $columns = [
        ['field'=>'channel_name','title'=>'渠道名称','width'=>20],
        ['field'=>'from_did','title'=>'对应招生来源','width'=>20],
        ['field'=>'total_num','title'=>'名单总数','width'=>20],
        ['field'=>'valid_num','title'=>'有效数量','width'=>20],
        ['field'=>'visit_num','title'=>'上门数量','width'=>20],
        ['field'=>'deal_num','title'=>'成交数量','width'=>20],
    ];

    protected function get_title(){
        $title = '来源渠道';
        return $title;
    }

    public function get_data()
    {
        $model = new MarketChannel();
        $ret = $model->getSearchResult($this->params,[],false);

        foreach ($ret['list'] as &$row) {
            $row['from_did'] = get_did_value($row['from_did']);
        }

        if (!empty($ret['list'])) {
            return collection($ret['list'])->toArray();
        }
        return [];

    }
}