<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/12/23
 * Time: 17:14
 */

namespace app\api\controller;

use app\api\model\Tally;
use think\Request;

class ReportTally extends Base
{
    public function get_list(Request $request)
    {
        $input = $request->get();
        if (!empty($input['group'])) {
            $group = explode(',', $input['group']);
        } else {
            $group = [];
        }
        $model = new Tally();
        $fields = $group;
        $w = [];
        $data = $model->where($w)
            ->group(join(',', $group))
            ->field($fields)
            ->getSearchResult($input);
        return $this->sendSuccess($data);
    }
}