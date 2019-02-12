<?php
namespace app\api\controller;

use app\api\model\TextbookSection;
use think\Request;

class TextbookSections extends Base
{

    public function get_list(Request $request)
    {
        $get = $request->get();

        $mTbs = new TextbookSection();
        $result = $mTbs->skipBid()->getSearchResult($get);

        return $this->sendSuccess($result);
    }

    public function post(Request $request)
    {
        $input = $request->post();

        $mTbs = new TextbookSection();
        $result = $mTbs->batchBookSection($input);
        if (false === $result){
            return $this->sendError(400,$mTbs->getError());
        }

        return $this->sendSuccess();
    }



}