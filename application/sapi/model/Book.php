<?php
namespace app\sapi\model;

use think\Exception;
class Book extends Base
{

    public function bookStore()
    {
        return $this->hasOne('BookStore','bk_id','bk_id');
    }


    /**
     * @desc  单本书籍借阅
     * @param $book_data
     * @return bool
     */
    public function bookOutOne($bk_id,$data){

        $book = $this->find(['bk_id' => $bk_id]);
        if(empty($book) || $book['qty'] < 1) {
            return $this->user_error('借阅书籍信息不存在或以借完');
        }

        $this->startTrans();
        try{
            $m_bl = new BookLending();
            $sid = global_sid();
            $data['sid'] = $sid;
            $data['bid'] = $book['bid'];
            $data['bk_id'] = $bk_id;
            $data['apply_int_day'] = format_int_day($data['apply_int_day']);
            $data['lending_int_day'] = format_int_day($data['lending_int_day']);
            //  借书记录
            $rs = $m_bl->createBookLeding($data);
            if (!$rs) throw new Exception('书籍借阅失败');

            //  借书之后书籍数量变动
            $rs = $this->changeQty($bk_id,$data['qty'],$is_borrowed=0);
            if (!$rs) throw new Exception('书籍借阅失败');


        }catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;

    }

    /**
     * @desc  多本书籍借阅
     * @param $book_data
     * @return bool
     */
    public function bookOuts($bk_ids,$data){
        if (empty($bk_ids) || !is_array($bk_ids)) {
            $this->user_error('请选择借书户ID');
        }
        $this->startTrans();
        try {
            foreach ($bk_ids as $k => $bk_id) {
                $result = $this->bookOutOne($bk_id,$data);
                if (!$result) {
                    $this->rollback();
                    return false;
                }
            }
        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;

    }

    /**
     * @desc  书籍变动
     * @param $book_data
     * @return bool
     */
    public function changeBook($input){
        $book =  $this->get($input['bk_id']);
        if(empty($book)){
            return $this->user_error('修改书籍不存在');
        }

        if (intval($book['qty']) < intval($input['qty']) && $input['op_type'] == 0){
            return $this->user_error('报废书籍超过库存书籍');
        }
        $this->startTrans();
        try {
            $m_bookqh = new BookQtyHistory();
            $rs = $m_bookqh -> createBookQtyHistory($input['bk_id'],$input['qty'],$input['op_type'],$input['remark'],$input['sid']);
            if (!$rs) {
                $this->rollback();
                return $this->user_error('书籍变动失败' . $this->getError());
            }

            $rs = $this->changeQty($input['bk_id'],$input['qty'],$input['op_type']);
            if (!$rs) {
                $this->rollback();
                return $this->user_error('书籍变动失败' . $this->getError());
            }

            $m_bookstore = new BookStore();
            $rs = $m_bookstore->updateTotal($input['bk_id'],$input['qty'],$input['op_type']);
            if (!$rs) {
                $this->rollback();
                return $this->user_error('书籍变动失败' . $this->getError());
            }

            if (!empty($input['bkl_id'])){
                $m_bl = new BookLending();
                $rs = $m_bl->updateBackStatus($input['bkl_id'],BookLending::BOOK_TYPE_DAMAGE);
                if (!$rs) {
                    $this->rollback();
                    return $this->user_error('书籍报损失败' . $this->getError());
                }
            }


        }catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return true;
    }

    /**
     * 书籍数量变动
     * @return array
     */
    public function changeQty($bk_id,$qty,$is_borrowed=0){

        $book_info = $this->get($bk_id);
        $m_BookStore = new BookStore();
        $bookstore = $m_BookStore->where(['bk_id' => $bk_id])->find();
        if(empty($bookstore)){
            return $this->user_error('book_store');
        }

        if ($is_borrowed == 0){
            $qty_b = $book_info['qty'] - $qty;
            $qty_s = $bookstore['qty'] - $qty;
        }
        if ($is_borrowed == 1){
            $qty_b = $book_info['qty'] + $qty;
            $qty_s = $bookstore['qty'] + $qty;
        }

        $this->startTrans();
        try{

            $w = ['bk_id' => $bk_id];
            $rs = $book_info->where($w)->update(['qty' => $qty_b]);
            if (!$rs) {
                $this->rollback();
                return $this->user_error('书籍变动失败1' . $rs->getError());
            }

            $rs = $bookstore->where($w)->update(['qty' => $qty_s]);
            if (!$rs) {
                $this->rollback();
                return $this->user_error('书籍变动失败2' . $rs->getError());
            }

        }catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }

        $this->commit();
        return $rs;
    }


}