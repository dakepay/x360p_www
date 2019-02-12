<?php
namespace app\admapi\model;

class Dictionary extends Base
{

    protected $hidden = ['create_time', 'create_uid', 'update_time', 'is_delete', 'delete_time', 'delete_uid'];

    public function updateDictionary($input){
        $dictionary = $this->getData();
        if(empty($dictionary)) return $this->user_error('模型数据错误');

        $update_data = [];
//        $update_data['name'] = !empty($input['name']) ? $input['name'] : $dictionary['name'];
        $update_data['title'] = !empty($input['title']) ? $input['title'] : $dictionary['title'];
        $update_data['desc'] = !empty($input['desc']) ? $input['desc'] : $dictionary['desc'];
        $update_data['display'] = !empty_except_zero($input['display']) ? $input['display'] : $dictionary['display'];

        $w_update['did'] = $dictionary['did'];
        $rs = $this->save($update_data,$w_update);
        if(!$rs){
            return $this->sql_save_error('dictionary');
        }

        return true;
    }

}