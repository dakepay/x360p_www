<?php

namespace app\api\controller;

use app\api\model\PrintTpl;
use think\Request;

class PrintTpls extends Base
{
	public function get_list(Request $request)
    {
        $input 	     = $request->get();
        $mPrintTpl   = new PrintTpl();
        $ret['list'] = $mPrintTpl->getSearchResult($input,false);
        $ret['default'] = $mPrintTpl->getDefaultTpls();
        return $this->sendSuccess($ret);
    }


    public function post(Request $request){
    	$input  = input('post.');
    	$mPrintTpl = new PrintTpl();
    	$result = $mPrintTpl->addPrintTpl($input);

    	if(false === $result){
    		return $this->sendError('400',$mPrintTpl->getError());
    	}

    	return $this->sendSuccess($mPrintTpl->getPkValue());

    }

    public function put(Request $request){
    	$id = input('id/d');
        if (empty($id)) {
            return $this->sendError(400, 'invalid parameter id');
        }
    	$input  = input('put.');
        $mPrintTpl = new PrintTpl();
       	$result = $mPrintTpl->updatePrintTpl($input,$id);

    	if(false === $result){
    		return $this->sendError('400',$mPrintTpl->getError());
    	}

    	return $this->sendSuccess();
    }

}