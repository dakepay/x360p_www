<?php
/**
 * Author: luo
 * Time: 2018/5/23 10:13
 */

namespace app\api\controller;


use app\api\model\FilePackage;
use think\Request;

class FilePackages extends Base
{
    public function get_list(Request $request)
    {
        return parent::get_list($request);
    }

    public function get_detail(Request $request, $id = 0)
    {
        $m_fp = new FilePackage();
        $get = $request->get();
        $with = isset($get['with']) ? $get['with'] : [];
        $file_package = $m_fp::get($id, $with);

        return $this->sendSuccess($file_package);
    }

    public function post(Request $request)
    {
        $post = $request->post();
        $m_fp = new FilePackage();
        $rs = $m_fp->addFilePackage($post);
        if($rs === false) return $this->sendError(400, $m_fp->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $put = $request->put();
        $fp_id = input('fp_id');
        $file_package = FilePackage::get($fp_id);
        if(empty($file_package)) return $this->sendError(400, '文件包不存在');

        $rs = $file_package->updateFilePackage($put);
        if($rs === false) return $this->sendError(400, $file_package->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $fp_id = input('id');
        $file_package = FilePackage::get($fp_id);
        $rs = $file_package->deleteFilePackage();
        if($rs === false) return $this->sendError(400, $file_package->getErrorMsg());
        
        return $this->sendSuccess();
    }


}