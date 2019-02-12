<?php
namespace app\api\model;

use think\Exception;
use Curl\Curl;
use app\api\model\BookStore;
use app\api\model\BookQtyHistory;
use app\api\model\BookLending;


class Book extends Base
{

    /**
     * @desc  创建图书信息
     * @param $book_data
     * @return bool
     */
    public function createBook($book_data){

        if ($book_data['is_public'] == 0){
            $suit_bids = implode(',',$book_data['suit_bids']);
            $book_data['suit_bids'] = $suit_bids;
        }

        if (empty($book_data['isbn']) || $book_data['isbn'] == ''){
            return $this->user_error('请提供ISBN号!');
        }

        $book_data['isbn'] = preg_replace('/[^\d]+/','',$book_data['isbn']);

        $w['isbn'] = $book_data['isbn'];
        $isbn_info = $this->where($w)->find();
        if ($isbn_info){
            return $this->user_error('书籍已存在,请勿重复添加');
        }

        $this->startTrans();
        try{
            $book_data['pub_int_day'] = format_int_day($book_data['pub_int_day']);
            $rs = $this->allowField(true)->isUpdate(false)->save($book_data);
            if (!$rs) {
                return $this->sql_add_error('book');
            }
            $bk_id = $this->getAttr('bk_id');

            $mBookStore = new BookStore();
            $rs = $mBookStore->createBookStore($bk_id,$book_data['qty'],$book_data['place_no']);
            if (!$rs) {
                return $this->user_error('添加图书库存失败:'.$mBookStore->getError());
            }

            $mBqh = new BookQtyHistory();
            $rs = $mBqh->createBookQtyHistory($bk_id,$book_data['qty'],BookQtyHistory::BOOK_TYPE_INTO);
            if (!$rs) {
                return $this->user_error('添加图书库存变动失败:'.$mBqh->getError());
            }

        }catch (\Exception $e) {
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

    /**
     * @desc  图书信息更改
     * @param $book_data
     * @return bool
     */
    public function updateBook($bk_id,$update){
        $book =  $this->get($bk_id);
        if(empty($book)){
            return $this->user_error('修改书籍不存在');
        }
        if ($update['is_public'] == 0){
            $suit_bids = implode(',',$update['suit_bids']);
            $update['suit_bids'] = $suit_bids;
        }


        $this->startTrans();
        try {
            $update['pub_int_day'] = format_int_day($update['pub_int_day']);
            $rs = $this->isUpdate(true)->allowField(true)->save($update);
            if(!$rs) {
                $this->rollback();
                return $this->sql_save_error('book');
            }

            if (!empty($update['place_no'])){
                $mBookStore = new BookStore();
                $rs = $mBookStore->uodatePlaceNo($bk_id,$update['place_no']);
                if(!$rs) {
                    $this->rollback();
                    return $this->sql_save_error('book_store');
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
            $mBld = new BookLending();
            $data['bk_id'] = $bk_id;
            $data['apply_int_day'] = format_int_day($data['apply_int_day']);
            $data['lending_int_day'] = format_int_day($data['lending_int_day']);
            //  借书记录
            $rs = $mBld->createBookLeding($data);
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
     * @desc  书籍返还
     * @param $book_data
     * @return bool
     */
    public function bookIn($bkl_id){

        $m_bl_info = BookLending::get($bkl_id);
        if(empty($m_bl_info)) return $this->user_error('返还书籍不存在');

        $this->startTrans();
        try{

            $result = $m_bl_info->updateBackStatus($bkl_id,BookLending::BOOK_TYPE_RETURN);

            if (!$result) {
                $this->rollback();
                return $this->user_error('书籍返还失败' . $m_bl_info->getError());
            }

            //  借书之后书籍数量变动
            $rs = $this->changeQty($m_bl_info['bk_id'],$m_bl_info['qty'],$is_borrowed=1);
            if (!$rs) {
                $this->rollback();
                return $this->user_error('书籍返还失败' . $m_bl_info->getError());
            }

        }catch (\Exception $e) {
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
        $m_bookstore = new BookStore();
        $bookstore = $m_bookstore->where(['bk_id' => $bk_id])->find();
        if(empty($bookstore)){
            return $this->user_error('变动书籍不存在');
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

    /**
     * 通过豆瓣获取书本信息
     * @return array
     */
    public function getBookInfoForDb($isbn){
        $book_info = [];
        try {
            $curl = new Curl();
            $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
            $curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $data = $curl->get('https://api.douban.com/v2/book/isbn/:' . $isbn);
            $curl->close();

            $book_info = json_decode($data->response, true);
            $book_info['did_change'] = 0;
            //  获取出版社字典值
            if (!empty($book_info['publisher'])) {
                $book_pub_did = get_dict_id($book_info['publisher'], 'book_pub');

                if (empty($book_pub_did)) {
                    add_dict_value($book_info['publisher'], 'book_pub');
                    $book_pub_did = get_dict_id($book_info['publisher'], 'book_pub');
                    $book_info['did_change'] = 1;
                }
                $book_info['book_pub_did'] = $book_pub_did;
            }

            //  获取装帧字典值
            if (!empty($book_info['binding'])) {
                $book_package_did = get_dict_id($book_info['binding'], 'book_package');
                if ($book_package_did == 0) {
                    add_dict_value($book_info['binding'], 'book_package');
                    $book_package_did = get_dict_id($book_info['binding'], 'book_package');

                    $book_info['did_change'] = 1;
                }
                $book_info['book_package_did'] = $book_package_did;
            }

            //  书籍图片处理
            $image = download_file($book_info['image']);
            $file = upload_file($image['save_path']);
            if ($file) {
                $book_info['image'] = $file['file_url'];
                unlink($image['save_path']);
            }
        }catch(\Exception $e){

        }
        return $book_info;
    }

    /**
     * 删除一本书的接口
     * @param $id
     * @param int $is_force
     */
    public function deleteOneBook($id,$is_force = 0){
        $book = self::get($id);
        if(!$book){
            return $this->user_error('书籍信息不存在或已经被删除!');
        }
        $m_bl = new BookLending();
        $w['bk_id'] = $id;
        if(!$is_force) {

            $bl_info = $m_bl->where($w)->find();
            if ($bl_info) {
                return $this->user_error('书籍存在借阅记录，是否强制删除?', static::CODE_HAVE_RELATED_DATA);
            }
        }

        $this->startTrans();
        try{
            $result = $m_bl->where($w)->delete(true);
            if(false === $result){
                $this->rollback();
                return $this->sql_delete_error('book_lending');
            }
            $m_bqh = new BookQtyHistory();
            $result = $m_bqh->where($w)->delete(true);
            if(false === $result){
                $this->rollback();
                return $this->sql_delete_error('book_qty_history');
            }
            $m_bs = new BookStore();
            $result = $m_bs->where($w)->delete(true);
            if(false === $result){
                $this->rollback();
                return $this->sql_delete_error('book_store');
            }
            $result = $book->delete(true);
            if(false === $result){
                $this->rollback();
                return $this->sql_delete_error('book');
            }

        }catch(\Exception $e){
            $this->rollback();
            return $this->exception_error($e);
        }
        $this->commit();
        return true;
    }

}