<?php 

namespace app\api\export;

use app\common\Export;
use app\api\model\Tally as TallyModel;

class ReportTallyByStats extends Export
{
	protected $columns = [
        ['field'=>'aa_id','title'=>'账户','width'=>20],
        ['field'=>'income','title'=>'收入','width'=>20],
        ['field'=>'payout','title'=>'支出','width'=>20]
	];

    protected function get_title()
    {
    	$title = '收支汇总表';
    	return $title;
    }

    protected function get_account_name($aa_id)
    {
    	return m('accounting_account')->where('aa_id',$aa_id)->value('name');
    }

    protected function get_data()
    {
    	$input = $this->params;
        $group = 'aa_id';
        $model = new TallyModel;
        $fields = $model->getTableFields();

        $income_list = $model->where('type', TallyModel::TALLY_TYPE_INCOME)->autoWhere($input)
            ->group($group)->field("$group, sum(amount) as income")->select();
        $payout_list = $model->where('type', TallyModel::TALLY_TYPE_PAYOUT)->autoWhere($input)
            ->group($group)->field("$group, sum(amount) as payout")->select();

        $data = [];
        foreach($income_list as $per) {
            $per['payout'] = isset($per['payout']) ? $per['payout'] : 0;
            $per['income'] = isset($per['income']) ? $per['income'] : 0;
            $data[$per[$group]] = $per;
        }

        foreach($payout_list as $per) {
            $per['payout'] = isset($per['payout']) ? $per['payout'] : 0;
            if(isset($data[$per[$group]])) {
                $data[$per[$group]]['payout'] = $per['payout'];
            } else {
                $data[$per[$group]]['payout'] = $per['payout'];
                $data[$per[$group]]['income'] = 0;
                $data[$per[$group]][$group] = $per[$group];
            }
        }

        $data = array_values($data);

        foreach ($data as $k => $v) {
        	$data[$k]['aa_id'] = $this->get_account_name($v['aa_id']);
        }

        if($data){
        	return collection($data)->toArray();
        }
        return [];
  

    }



}