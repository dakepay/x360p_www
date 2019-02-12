<?php
namespace app\api\controller;

use think\Request;

class DataExports extends Base
{

    public function get_list(Request $request)
    {
        $mDataExport = new \app\api\model\DataExport();
        $input = $request->get();
        if(!isset($input['create_uid'])){
            $input['create_uid'] = $request->user->uid;
        }
        $ret = $mDataExport->getSearchResult($input);
        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $post = $request->post();
        $mDataExport = new \app\api\model\DataExport();
        $result = $mDataExport->addDataExport($post);
        if($result === false) {
            return $this->sendError(400, $mDataExport->getError());
        }
        return $this->sendSuccess($result);
    }

}