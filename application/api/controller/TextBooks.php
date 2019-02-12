<?php
namespace app\api\controller;

use app\api\model\Textbook;
use think\Request;

class Textbooks extends Base
{

    public function get_list(Request $request)
    {
        $get = $request->get();

        $mTextbook = new Textbook();
        $result = $mTextbook->getSearchResult($get);

        return $this->sendSuccess($result);
    }

    public function post(Request $request)
    {
        $input = input();

        $mTextbook = new Textbook();
        $result = $mTextbook->addTextbook($input);
        if (false === $result){
            return $this->sendError(400,$mTextbook->getError());
        }

        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $input = input();
        $tb_id = $input['tb_id'];
        unset($input['tb_id']);

        $mTextbook = new Textbook();
        $result = $mTextbook->updateTextbook($tb_id,$input);
        if (false === $result){
            return $this->sendError(400,$mTextbook->getError());
        }

        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $tb_id =input('id/d');

        $mTextbook = new Textbook();
        $result = $mTextbook->delOneTextbook($tb_id);
        if (false === $result){
            return $this->sendError(400,$mTextbook->getError());
        }

        return $this->sendSuccess();
    }
}