<?php

namespace app\sapi\controller;

use think\Request;
use app\sapi\model\BookLending;

class BookLendings extends Base
{

    /**
     * @desc  书籍借阅详细信息
     * @param array
     * @return bool
     */
    public function get_list(Request $request){
        $input = $request->get();
        $sid = global_sid();

        $mBld = new BookLending();
        $result = $mBld->where('sid',$sid)->getSearchResult($input);

        return $this->sendSuccess($result);
    }


    public function do_cancel(Request $request)
    {
        $bkl_id = input('bkl_id/d');

        $mBookLengding = new BookLending();
        $result = $mBookLengding->do_cancel($bkl_id);
        if (false === $result){
            return $this->sendError(400,$mBookLengding->getError());
        }
        return $this->sendSuccess();
    }



}