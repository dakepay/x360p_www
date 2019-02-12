<?php
namespace app\ftapi\controller;


use app\ftapi\model\FtEmployee;
use think\Request;


class FtEmployees extends Base
{
    public function get_details(Request $request, $id = 0){
        $input = input();

        $mFtEmployee = new FtEmployee();
        $rs = $mFtEmployee->with('employee')->getSearchResult($input);

        return $this->sendSuccess($rs);
    }


}