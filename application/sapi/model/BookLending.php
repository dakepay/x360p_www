<?php

namespace app\sapi\model;

class BookLending extends Base
{
    const BOOK_TYPE_NO_YET = 0;  //未还
    const BOOK_TYPE_RETURN = 1;  //已还
    const BOOK_TYPE_DAMAGE = 2;  //报损


    public function book(){
        return $this->hasOne('Book','bk_id','bk_id');
    }

    /**
     * @desc  书籍借阅
     * @param $book_data
     * @return bool
     */
    public function createBookLeding($data){

        $rs = $this->allowField(true)->isUpdate(false)->save($data);
        if(!$rs) return $this->sql_add_error('book');
        return $rs;
    }

    /**
     * @desc  书籍
     * @param $book_data
     * @return bool
     */
    public function updateBackStatus($bkl_id,$status){
        $bkl_info = $this->get($bkl_id);
        if(!$bkl_info) return $this->user_error('无借阅记录');

        $over_days = int_day_diff($bkl_info['lending_int_day'],int_day(time())) - $bkl_info['lending_days'];
        if ($over_days < 1){
            $over_days = 0;
        }
        $bl_update = [
            'back_status' => $status,
            'back_int_day' => int_day(time()),
            'over_days' => $over_days,
        ];
        $rs = $bkl_info->allowField(true)->save($bl_update);
        if(!$rs) return $this->sql_save_error('book_lending');
        if($status == self::BOOK_TYPE_DAMAGE){
            $m_book = new Book();
            $book = $m_book->get($bkl_info['bk_id']);
            $qty = $book['qty'] + $bkl_info['qty'];
            $res = $book->allowField(true)->save(['qty' => $qty]);
            if(!$res) return $this->sql_save_error('book');
        }
        return $rs;
    }

    /**
     * 线上借书取消
     * @param $bkl_id
     * @return bool
     */
    public function do_cancel($bkl_id){
        $book_lending = $this->get($bkl_id);
        if (empty($book_lending)){
            return $this->user_error('借阅记录不存在');
        }

        $w['bkl_id'] = $bkl_id;
        $update['back_status'] = 3;
        $result = $this->allowField(true)->save($update,$w);
        if (false === $result) return $this->sql_save_error('book_lending');

        return true;
    }


}