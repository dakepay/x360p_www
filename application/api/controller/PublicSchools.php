<?php
/** 
 * Author: luo
 * Time: 2017-10-23 14:32
**/


namespace app\api\controller;

use app\api\model\PublicSchool;
use app\api\model\StateSchool;
use think\Request;
use app\api\model\PublicSchool as PublicSchoolModel;

class PublicSchools extends Base
{

    public function get_list(Request $request)
    {

        $input = $request->get();
        $school_model = new PublicSchoolModel();
        $ret = $school_model->getSearchResult($input);
        return $this->sendSuccess($ret);
    }

   public function post(Request $request)
   {
       return parent::post($request); 
   }

    public function put(Request $request)
    {
        return parent::put($request);
    }

    public function delete(Request $request)
    {
        return parent::delete($request);
    }

    //搜索学校
    public function search(Request $request)
    {
        $input = $request->param();
        $model = new PublicSchoolModel();
        $ret = $model->getSearchResult($input);

        return $this->sendSuccess($ret);
    }


    public function get_list_center_schools(Request $request)
    {
        $input = $request->param();
        $where = [];

        isset($input['name']) && $where['name|province|city|district'] = [['like', '%'.$input['name'].'%'], 'or'];
        $m_ss = new StateSchool();

        $ret['page'] = $page = input('page', 1);
        $ret['pagesize'] = $pagesize = input('pagesize', config('default_pagesize'));

        $ret['total'] = $m_ss->where($where)->count();
        $ret['list'] = $m_ss->where($where)->page($page, $pagesize)->select();

        return $this->sendSuccess($ret);
    }

}