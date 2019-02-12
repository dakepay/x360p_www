<?php

namespace app\api\controller;

use think\Request;
use app\api\model\BookLending;

class BookLendings extends Base
{

    /**
     * @desc  书籍借阅详细信息
     * @param array
     * @return bool
     */
    public function get_list(Request $request){
        $input = $request->get();
        $bookLengding = new BookLending();

        if(!empty($input['name'])){
            $name = $input['name'];
            $bookLengding->alias('bl')->join('book b','bl.bk_id = b.bk_id','left')->distinct(true);
            $bookLengding->where('b.name|author|isbn','like','%'.$name.'%');
            unset($input['name']);
        }

        if (!empty($input['back_int_day'])){
            unset($input['back_int_day']);
            $bookLengding->where('back_int_day','gt',1);
        }

        if (!empty($input['student_name'])){
            $student_name = $input['student_name'];
            unset($input['student_name']);
            $sids = $bookLengding->searchByNameAndCard($student_name);
            $bookLengding->where('sid','in',$sids);
        }

        $rs = $bookLengding->getSearchResult($input);
        if (empty($rs)){
            return $this->sendError(400, '暂无借阅记录');
        }

        foreach ($rs['list'] as $k => $v){
            $student = get_student_info($v['sid']);
            $book = get_book_info($v['bk_id']);

            $rs['list'][$k]['student_name'] = $student['student_name'];
            $rs['list'][$k]['name'] = $book['name'];


            if ($v['back_status'] == 0 && $v['lending_int_day'] > 0) {
                $over_days = int_day_diff($v['lending_int_day'], int_day(time())) - $v['lending_days'];
                if ($over_days < 1) {
                    $over_days = 0;
                }
                $bl_update = ['over_days' => $over_days];
                $bl_id = ['bkl_id' => $v['bkl_id']];
                $bookLengding->allowField(true)->save($bl_update,$bl_id);
            }
        }

        return $this->sendSuccess($rs);
    }

    /**
     * 线上借书确认
     * @param Request $request
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function do_pass(Request $request)
    {
        $input = input();

        $mBookLengding = new BookLending();
        $lending_int_day = isset($input['lending_int_day']) ? $input['lending_int_day'] : 0;

        $result = $mBookLengding->do_pass($input['bkl_id'],$lending_int_day);
        if (false === $result){
            return $this->sendError(400,$mBookLengding->getError());
        }

        return $this->sendSuccess();
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