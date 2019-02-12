<?php
namespace app\sapi\model;

class BookStore extends Base
{

    public function createBookStore($bk_id,$qty,$place_no=''){
        if (empty($bk_id)){
            return $this->input_param_error('bk_id');
        }
        if (empty($qty)){
            return $this->input_param_error('qty');
        }
        $data = [
            'bk_id' => $bk_id,
            'total_qty' => $qty,
            'qty' => $qty,
            'place_no' => $place_no,
        ];
        $rs = $this->allowField(true)->isUpdate(false)->save($data);
        if(!$rs) return $this->sql_add_error('book_store');
        return $rs;
    }

    public function getTotalQty($bk_id){
        $w['bk_id'] = $bk_id;
        $res = $this->where($w)->field('')->field('total_qty,place_no')->find();
        return $res;
    }

    public function updateTotal($bk_id,$qty,$is_borrowed=0){
        $w['bk_id'] = $bk_id;
        $total = $this->where($w)->field('total_qty')->find();
        if ($is_borrowed == 0){
            $update_tl['total_qty'] =  $total['total_qty'] - $qty;
        }
        if ($is_borrowed == 1){
            $update_tl['total_qty'] =  $total['total_qty'] + $qty;
        }
        $m_bk['bk_id'] = $bk_id;
        $rs = $this->save($update_tl,$m_bk);

        if (empty($rs)){
            return false;
        }
        return true;
    }

    public function uodatePlaceNo($bk_id,$place_no){
        if (empty($bk_id)){
            return $this->input_param_error('bk_id');
        }
        if (empty($place_no)){
            return $this->input_param_error('place_no');
        }
        $up_pn['place_no'] = $place_no;
        $up_bk['bk_id'] = $bk_id;
        $rs = $this->save($up_pn,$up_bk);
        if(!$rs) return $this->sql_save_error('book_store');
        return $rs;
    }


}