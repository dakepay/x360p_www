<?php

namespace app\api\controller;

use app\api\model\Employee;
use think\Request;
use app\api\model\Role;

/**
 * Class Roles
 * @title 角色管理接口
 * @url employees
 * @desc  角色的添加、编辑、删除
 * @version 1.0
 * @readme
 */
class Roles extends Base
{

    /**
     * @title 获取角色信息
     * @desc  根据条件获取单个角色信息
     * @url employees
     * @method GET
     * @readme 详细说明
     */
    public function get_detail(Request $request, $id = 0){
        $role = Role::get($id);
        if (!$role) {
            return $this->sendError(400, 'invalid parameter');
        }

        return $this->sendSuccess($role);
    }

    //角色权限级的菜单描述
    public function getPerDesc($pers = [], array $role_pers)
    {
        static $desc = [];
        if(empty($pers)) {
            $pers = config('org_per_item')['main'];
        }
        foreach($pers as $item) {
            if(!empty($item['uri']) && in_array($item['uri'], $role_pers)) {
                $desc[] = [$item['uri'],$item['text']];
            }
            if(!empty($item['sub'])) {
                $this->getPerDesc($item['sub'], $role_pers);
            }
        }
        return $desc;
    }

	/**
    * @title 获取所有角色列表
    * @desc  根据条件获取角色列表
    * @url roles
    * @method GET
    * @return  返回字段描述
    * @readme 详细说明
    */
    protected function get_list(Request $request)
    {
        $input = $request->param();
        $result = model('role')->addWhereOr('rid','lt',11)->getSearchResult($input);
        return $this->sendSuccess($result);
	}

	/**
	 * @title 创建角色
	 * @desc  创建一个角色
	 * @url roles
	 * @method  POST
	 */
	public function post(Request $request)
    {
        $input = input('post.');
        $result = $this->validate($input,'Role');
        if ($result !== true) {
            return $this->sendError(400, $result);
        }
        $role = new Role();
        $result = $role->createRole($input);
        if ($result === false) {
            return $this->sendError(400, $role->getError());
        }
        return $this->sendSuccess($role);
	}

	/**
	 * @desc  编辑角色的基本信息
	 * @url roles/:id
	 * @method  PUT
	 */
	public function put(Request $request)
    {
        $og_id = gvar('og_id');
        $id = input('id/d');
        if (empty($id)) {
            return $this->sendError(400, 'invalid parameter id');
        }

        $m_role = new Role();

        if($id < 11){       //系统角色
            $m_role->skipOgId();
        }

        $role = $m_role->find($id);

        if (!$role) {

            return $this->sendError(400, '该角色不存在');
        }

        $data = input('put.');

        if(isset($data['role_name'])) {

            $rule = [
                ['role_name|角色名称', 'require|unique:role,role_name,' . $id]
            ];

            $result = $this->validate($data, $rule);
            if ($result !== true) {
                return $this->sendError(400, $result);
            }
        }


        if ($role['is_system']) {
            $rid = $id;
            $allowField = ['role_desc', 'pers', 'mobile_pers'];
            $user_org_role = user_config('org_role');
            $new_user_org_role = [];
            foreach ($user_org_role as $k => $r) {
                $new_r = [];
                $new_r['rid'] = $r['rid'];
                $new_r['role_name'] = $r['role_name'];
                if(isset($r['pers']) && !empty($r['pers'])){
                    $new_r['pers'] = $r['pers'];
                }

                if ($r['rid'] == $rid) {
                    if(isset($data['role_name'])) {
                        $new_r['role_name'] = $data['role_name'];
                    }
                    if($og_id > 0){
                        if(isset($data['pers'])) {
                            $new_r['pers'] = $data['pers'];
                        }
                        if(isset($data['mobile_pers'])) {
                            $new_r['mobile_pers'] = $data['mobile_pers'];
                        }
                    }
                }
                array_push($new_user_org_role, $new_r);
            }

            $input['cfg_name'] = 'org_role';
            $input['cfg_value'] = $new_user_org_role;

            model('Config')->addConfig($input);

            if($og_id > 0){
                return $this->sendSuccess($role);
            }

        } else {
            $allowField = true;
        }


        $result = $role->allowField($allowField)->save($data);
        if ($result === false) {
            return $this->sendError(400, $role->getError());
        }

        //修改权限文件
        if(isset($data['pers']) && !empty($data['pers'])) {
            $this->updateRoleFile($id, $data['pers'], 'pc');
        }
        if(isset($data['mobile_pers']) && !empty($data['mobile_pers'])) {
            $this->updateRoleFile($id, $data['mobile_pers'], 'mobile');
        }

        return $this->sendSuccess($role);
	}

    //修改权限文件
	private function updateRoleFile($id, $pers, $client_type)
    {
        if(!empty(gvar('client.domain')) && gvar('client.domain') == 'base' && gvar('uid') === 1) {
            if(is_array($pers)) $pers = implode(',', $pers);
            $role_path = CONF_PATH.'extra'.DS.'org_role.php';
            $content = file_get_contents($role_path);
            if($client_type == 'pc') {
                $patten = sprintf('/(\'rid\'\s*=>\s*%s.*?\'pers\'\s*=>\s*\')(.*?)(\',\s*\'mobile_pers\')/s', $id);
            } else {
                $patten = sprintf('/(\'rid\'\s*=>\s*%s.*?\'mobile_pers\'.*?\')(.*?)(\')/s', $id);
            }
            $replace = sprintf('${1}%s${3}', $pers);
            $new_content = preg_replace($patten, $replace, $content, 1);
            file_put_contents($role_path, $new_content);
        }

        return true;
    }


	/**
	 * @desc  根据角色ID删除一个角色
	 * @url roles/:id
	 * @method  DELETE
	 */
	public function delete(Request $request)
    {
        $id = input('id');
        if (empty($id)) {
            return $this->sendError(400, 'invalid parameter id');
        }
        if($id < 11){
            return $this->sendError(400,'系统内置角色不允许删除');
        }
        $role = Role::get($id);
        if (!$role) {
            return $this->sendError(400, '该角色不存在');
        }
        $result = $role->deleteRole();
        if ($result === false) {
            return $this->sendError(400, $role->getError());
        }
        return $this->sendSuccess();
	}


    /**
     * @desc  员工批量增加权限
     * @author luo
     * @method GET
     */
    public function post_employees(Request $request)
    {
        $rid = input('id/d');
        $eids = input('eids/a');

        $model = new Employee();
        $rs = $model->addBatchRole($rid, $eids);
        if($rs === false) return $this->sendError(400, $model->getErrorMsg());

        return $this->sendSuccess();
    }

}