<?php
namespace app\ftapi\controller;

use app\ftapi\model\Employee;
use app\ftapi\model\User as UserModel;
use think\Request;

class Employees extends Base
{
    public function get_list(Request $request)
    {
        $input = $request->get();
        $where = [];

        $mEmployee = new Employee();

        if (!empty($input['ename'])) {
            $name = $input['ename'];
            unset($input['ename']);
            $mEmployee->where(function ($query) use ($name) {
                $query->where('ename',  'like', '%' . $name . '%');
                if (preg_match("/^[a-z]*$/i", $name)) {/*全英文*/
                    $query->whereOr('pinyin', 'like', '%' . $name . '%');
                    $query->whereOr('pinyin_abbr', $name);
                }
            });
        }

        if(isset($input['rids']) && !empty($input['rids'])) {
            $rid = intval($input['rids']);
            $where[] = ['exp', "find_in_set({$rid},rids)"];
            unset($input['rids']);
        }


        $result = $mEmployee->scope('rids')->where($where)->getSearchResult($input);

        return $this->sendSuccess($result);
    }

    public function get_detail(Request $request, $id = 0)
    {
        $uid = input('id/d');
        $data = UserModel::get($uid, ['employee']);
        return $this->sendSuccess($data);
    }


    public function put(Request $request){
        $id = $request->param('id');
        $input = $request->put();

        $mEmployee = new Employee();
        $employee_info = $mEmployee->get($id);
        if (!$employee_info){
            return $this->sendError(400,'employee not exists');
        }
        if (isset($input['birth_time'])){
            $input['birth_time'] = int_to_time(format_int_day($input['birth_time']));
            $input['birth_year'] = intval(date('Y',$input['birth_time']));
            $input['birth_month'] = intval(date('m',$input['birth_time']));
            $input['birth_day'] = intval(date('d',$input['birth_time']));
        }

        $result = $mEmployee->updateEmployee($employee_info, $input);

        if(!$result){
            return $this->sendError(400, $mEmployee->getError());
        }
        return $this->sendSuccess();
    }

}