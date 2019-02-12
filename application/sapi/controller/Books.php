<?php

namespace app\sapi\controller;

use app\sapi\model\BookStore;
use think\Request;
use app\sapi\model\Book;
class Books extends Base
{

    /**
     * @desc  获取书籍信息
     * @param array
     * @return int
     */
    public function get_list(Request $request){
        $input = $request->get();
        $m_book = new Book();
        if(!empty($input['name'])){
            $name = $input['name'];
            $m_book->where('name|author|isbn','like','%'.$name.'%');
            unset($input['name']);
        }
        $res = $m_book->getSearchResult($input);

        $book_store = new BookStore();
        foreach ($res['list'] as $k => $v){
            $data = $book_store->getTotalQty($v['bk_id']);
            $res['list'][$k]['total_qty'] = $data['total_qty'];
            $res['list'][$k]['place_no'] = $data['place_no'];

            if ($v['is_public'] == 0){
                $suit_bids = explode(',',$v['suit_bids']);
                $res['list'][$k]['suit_bids'] = array_intval($suit_bids);
            }
            if ($v['is_public'] == 1){
                $res['list'][$k]['suit_bids'] = [];
            }
        }

        return $this->sendSuccess($res);
    }

    public function get_detail(Request $request, $id = 0)
    {
        $bk_id = input('id/d');
        $mBook = new Book();
        $book = $mBook->get($bk_id);
        $book_store = new BookStore();

        $data = $book_store->getTotalQty($book['bk_id']);
        $book['total_qty'] = $data['total_qty'];
        $book['place_no'] = $data['place_no'];

        if ($book['is_public'] == 0){
            $suit_bids = explode(',',$book['suit_bids']);
            $book['suit_bids'] = array_intval($suit_bids);
        }
        if ($book['is_public'] == 1){
            $book['suit_bids'] = [];
        }

        return $this->sendSuccess($book);
    }


    /**
     * @desc  书籍借阅
     * @param array
     * @return bool
     */
    public function dolending(Request $request){
        $input = input();
        $bk_ids = $input['bk_id'];
        unset($input['bk_id']);
        $mBook = new Book();

        $rs = $mBook->bookOuts($bk_ids,$input);

        if(false === $rs) {
            return $this->sendError(400, $mBook->getError());
        }
        return $this->sendSuccess();
    }

}