<?php
namespace app\api\model;
use app\common\Export;

class DataExport extends Base
{
    protected $hidden = [
        'update_time',
        'is_delete',
        'delete_time'
    ];

    protected $type = [
        'params' => 'json'
    ];

    /**
     * @param $input
     * @return bool
     * @throws \Exception
     */
    public function addDataExport($input){
        $need_fields = ['res_name'];

        if(!$this->checkInputParam($input,$need_fields)){
            return false;
        }

        $res = $input['res_name'];

        $instance = Export::Load($res,$input);

        $de['title'] = $instance->getTitle();
        $de['params'] = $input;

        $result = $this->save($de);

        if(!$result){
            return $this->sql_add_error('data_export');
        }

        $de['de_id'] = $this->de_id;
        $de['cid'] = gvar('client.cid');
        $job_data = [
            'class' => 'DataExport',
            'params'=>$de
        ];

        queue_push('DataExport',$job_data);

        return $de;
    }
}