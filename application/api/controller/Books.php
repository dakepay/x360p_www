<?php

namespace app\api\controller;


use app\api\model\BookLending;
use app\api\model\BookStore;
use think\Request;
use app\api\model\Book;


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



    /**
     * @desc  创建书籍借阅信息
     * @param array
     * @return int
     */
    public function post(Request $request){
        $input = input();
        $book_data = $input;

        $mBook = new Book();
        $rs = $mBook->createBook($book_data);
        if(!$rs) {
            return $this->sendError(400, $mBook->getError());
        }
        return $this->sendSuccess();
    }

    /**
     * 修改图书信息接口
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function put(Request $request){
        $bk_id = input('bk_id/d');
        $input = $request->put();

        $mBook = new Book();
        $rs = $mBook->updateBook($bk_id,$input);
        if(!$rs) return $this->sendError(400, $mBook->getError());

        return $this->sendSuccess();

    }

    /**
     * 删除图书接口
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml|void
     */
    public function delete(Request $request){
        $id = input('id/d');
        $is_force_del = input('force/d', 0);

        $mBook = new Book();

        $rs = $mBook->deleteOneBook($id, $is_force_del);
        if(!$rs) {
            if($mBook->get_error_code() == $mBook::CODE_HAVE_RELATED_DATA) {
                return $this->sendConfirm($mBook->getError());
            }
            return $this->sendError(400, $mBook->getError());
        }

        return $this->sendSuccess();
    }

    /**
     * @desc  书籍出入库
     * @param array
     * @return bool
     */
    public function change_qty(Request $request){
        $input = input();
        $mBook = new Book();
        $rs = $mBook->changeBook($input);
        if(!$rs) return $this->sendError(400, $mBook->getError());

        return $this->sendSuccess();
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

        if(!$rs) {
            return $this->sendError(400, $mBook->getError());
        }
        return $this->sendSuccess();
    }

    /**
     * @desc  书籍归还
     * @param array
     * @return bool
     */
    public function doreturn(Request $request){
        $bkl_id = input('bkl_id/d');

        $mBook = new Book();
        $rs = $mBook->bookIn($bkl_id);

        if(!$rs) {
            return $this->sendError(400, $mBook->getError());
        }
        return $this->sendSuccess();
    }


    /**
     * 从豆瓣API获取图书信息
     * @param $isbn
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function getBookForDb($isbn){
        $mBook = new Book();
        $book_info =  $mBook->getBookInfoForDb($isbn);
        return $this->sendSuccess($book_info);

    }




}