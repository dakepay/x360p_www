<?php

namespace app\api\export;

use app\common\Export;
use app\api\model\MarketClue;

class MarketRecommends extends Export
{
	protected $res_name = 'market_clue';

	protected $columns = [
        ['field'=>'name','title'=>'姓名','width'=>20],
        ['field'=>'tel','title'=>'电话','width'=>20],
        ['field'=>'birth_time','title'=>'年龄','width'=>20],
        ['field'=>'is_valid','title'=>'有效性','width'=>20],
        ['field'=>'is_change','title'=>'是否转化','width'=>20],
        ['field'=>'is_deal','title'=>'是否报名','width'=>20],
        ['field'=>'is_reward','title'=>'是否奖励','width'=>20],
        ['field'=>'recommend_sid','title'=>'推荐学员','width'=>20],
        ['field'=>'recommend_uid','title'=>'推荐学员用户','width'=>20],
        ['field'=>'recommend_note','title'=>'推荐说明','width'=>40],
        ['field'=>'recommend_reward_note','title'=>'推荐奖励备注','width'=>30],
        ['field'=>'assigned_eid','title'=>'跟进人','width'=>20],
	];

	protected function get_title()
	{
		$title = '推荐名单';
		return $title;
	}

	public function get_data()
	{
		$input = $this->params;
		unset($input['create_time']);
        $model = new MarketClue;
		$data = $model->getSearchResult($input,[],false);

		foreach ($data['list'] as $k => $v) {
			unset($data['list'][$k]['recommend_student']);
			unset($data['list'][$k]['recommend_user']);
		}

		if(!empty($data['list'])){
			return collection($data['list'])->toArray();
		}
		return [];
	}








}