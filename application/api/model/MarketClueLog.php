<?php

namespace app\api\model;

class MarketClueLog extends Base
{
	const OP_INSERT = 1; #市场添加分配
    const OP_IMPORT = 2; #市场名单导入
    const OP_EDIT   = 3; #市场名单编辑
    const OP_DELETE = 4; #市场名单删除
    const OP_CHANGE = 5; #市场名单转为客户名单
    const OP_ASSIGN = 6; #市场名单分配


	protected $type = [
        'content' => 'json',
    ];

    protected $hidden = ['update_time', 'is_delete', 'delete_time', 'delete_uid'];
    
    /**
     * 市场名单添加日志
     * @param MarketClue $market_clue [description]
     */
    public static function addMarketClueInsertLog($mcl_id)
    {
        $mcl = get_mcl_info($mcl_id);
        $data = [];
        array_copy($data,$mcl,['og_id','bid','mcl_id']);
        $data['op_type'] = MarketClueLog::OP_INSERT;
        $desc = config('format_string.market_clue_insert');
        $temp['name'] = request()->user['name'];
        $temp['market_clue'] = $mcl['name'];
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['content'] = [];
        return self::create($data);
    }

    /**
     * 市场名单导入日志
     * @param [type] $mcl_id [description]
     */
    public static function addMarketClueImportLog($mcl_id)
    {
        $mcl = get_mcl_info($mcl_id);
        $data = [];
        array_copy($data,$mcl,['og_id','bid','mcl_id']);
        $data['op_type'] = MarketClueLog::OP_IMPORT;
        $desc = config('format_string.market_clue_import');
        $temp['name'] = request()->user['name'];
        $temp['market_clue'] = $mcl['name'];
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['content'] = [];
        return self::create($data);
    }

    /**
     * 市场名单编辑日志
     * @param [type] $mcl_id  [description]
     * @param [type] $content [description]
     */
    public static function addMarketClueEditLog($mcl_id,array $content)
    {
        $mcl = get_mcl_info($mcl_id);
        $data = [];
        array_copy($data,$mcl,['og_id','bid','mcl_id']);
        $data['op_type'] = MarketClueLog::OP_EDIT;
        $desc = config('format_string.market_clue_edit');
        $temp['name'] = request()->user['name'];
        $temp['market_clue'] = $mcl['name'];
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['content'] = $content;
        return self::create($data);
    }
    
    // 市场名单 删除
    public static function addMarketClueDeleteLog(MarketClue $clue)
    {
        $data = [];
        array_copy($data,$clue,['og_id','bid','mcl_id']);
        $data['op_type'] = MarketClueLog::OP_DELETE;
        $desc = config('format_string.market_clue_delete');
        $temp['name'] = request()->user['name'];
        $temp['market_clue'] = $clue['name'];
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['content'] = [];
        return self::create($data);
    }
    
    // 转为客户名单日志
    public static function addMarketClueChangeToCustomerLog($mcl_id)
    {
        $mcl = get_mcl_info($mcl_id);
        $data = [];
        array_copy($data,$mcl,['og_id','bid','mcl_id']);
        $data['op_type'] = MarketClueLog::OP_CHANGE;
        $desc = config('format_string.market_clue_change');
        $temp['name'] = request()->user['name'];
        $temp['market_clue'] = $mcl['name'];
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['content'] = [];
        return self::create($data);
    }

    public static function addMarketClueToMarketToBidLog($mcl_id,$bid)
    {
        $mcl = get_mcl_info($mcl_id);
        $data = [];
        array_copy($data,$mcl,['og_id','bid','mcl_id']);
        $data['op_type'] = MarketClueLog::OP_ASSIGN;
        $desc = config('format_string.market_clue_to_market_to_bid');
        $temp['name'] = request()->user['name'];
        $temp['market_clue'] = $mcl['name'];
        $temp['bid'] = get_branch_name($bid);
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['content'] = [];
        return self::create($data);
    }

    public static function addMarketClueToMarketToEidLog($mcl_id,$eid)
    {
        $mcl = get_mcl_info($mcl_id);
        $data = [];
        array_copy($data,$mcl,['og_id','bid','mcl_id']);
        $data['op_type'] = MarketClueLog::OP_ASSIGN;
        $desc = config('format_string.maeket_clue_to_market_to_eid');
        $temp['name'] = request()->user['name'];
        $temp['market_clue'] = $mcl['name'];
        $temp['eid'] = get_employee_name($eid);
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['content'] = [];
        return self::create($data);
    }

    public static function addMarketClueToCustomerToBidLog($mcl_id,$bid)
    {
        $mcl = get_mcl_info($mcl_id);
        $data = [];
        array_copy($data,$mcl,['og_id','bid','mcl_id']);
        $data['op_type'] = MarketClueLog::OP_ASSIGN;
        $desc = config('format_string.market_clue_to_customer_to_bid');
        $temp['name'] = request()->user['name'];
        $temp['market_clue'] = $mcl['name'];
        $temp['bid'] = get_branch_name($bid);
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['content'] = [];
        return self::create($data);
    }

    public static function addMarketClueToCustomerToEidLog($mcl_id,$eid)
    {
        $mcl = get_mcl_info($mcl_id);
        $data = [];
        array_copy($data,$mcl,['og_id','bid','mcl_id']);
        $data['op_type'] = MarketClueLog::OP_ASSIGN;
        $desc = config('format_string.maeket_clue_to_customer_to_eid');
        $temp['name'] = request()->user['name'];
        $temp['market_clue'] = $mcl['name'];
        $temp['eid'] = get_employee_name($eid);
        $data['desc'] = str_replace(array_keys($temp),$temp,$desc);
        $data['content'] = [];
        return self::create($data);
    }

}