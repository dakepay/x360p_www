<?php
namespace app\api\model;

use util\Sqb;
class ShouQianBa extends Base
{

    public function getArea(){
        $url = 'http://sales-web.shouqianba.com/api/v1/allprovinces';
        $area = http_request($url);
        $area = json_decode($area,true);
        if (!empty($area['data'])){
            $data = [
                'authed' => true,
                'data' => $area['data'],
                'error' => 0,
                'message' => 'success'
            ];
        }else{
            return false;
        }

        return $data;
    }

    //  开户银行接口
    public function getBank($bank_card){
        $sab_pay = config('shouqianba');
        $mSqb = new Sqb();
        $rs = $mSqb->getBank($bank_card,$sab_pay['vender_sn'],$sab_pay['vender_key']);

        if ($rs['result_code'] != 200){
            return $this->user_error($rs['error_message']);
        }

        return $rs;
    }

    //  支行列表接口
    public function getBranches($input){
        $need_fields = ['bank_area','bank_name'];
        if (!$this->checkInputParam($input,$need_fields)){
            return false;
        }

        $sab_pay = config('shouqianba');
        $mSqb = new Sqb();
        $rs = $mSqb->getBranches($input['bank_area'],$input['bank_name'],$sab_pay['vender_sn'],$sab_pay['vender_key']);

        if ($rs['result_code'] != 200){
            return $this->user_error($rs['error_message']);
        }

        return $rs;
    }

    //  上传图片接口
    public function upload($file){
        if (empty($file)){
            return $this->user_error('请选择上传图片！');
        }

        $file_info = $file->getInfo();
        $pic_str = file_get_contents($file_info['tmp_name']);
        $pic_str = base64_encode($pic_str);

        $sab_pay = config('shouqianba');
        $mSqb = new Sqb();
        $rs = $mSqb->upload($pic_str,$sab_pay['vender_sn'],$sab_pay['vender_key']);

        if ($rs['result_code'] != 200){
            return $this->user_error($rs['error_message']);
        }

        return $rs;
    }
}

