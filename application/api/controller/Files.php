<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/12/1
 * Time: 15:31
 */
namespace app\api\controller;

use app\api\model\File;
use think\Request;

class Files extends Base
{
    public function get_list(Request $request)
    {
        $input = $request->get();
        unset($input['create_uid']);
        $uid = $request->user->uid;
        $list  = (new File())->where('create_uid', $uid)->order('create_time', 'desc')->getSearchResult($input, true);
        return $this->sendSuccess($list);
    }

    public function put(Request $request)
    {
        $file_id = input('id');
        $file = File::get($file_id);
        if(empty($file)) return $this->sendError(400, '文件不存在');

        $put = $request->put();
        $rs = $file->allowField('file_name')->save($put);
        if($rs === false) return $this->sendError(400, $file->getErrorMsg());

        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $file_id = input('id');
        $file = File::get($file_id);
        if(empty($file)) return $this->sendSuccess();

        $rs = $file->delFile();
        if($rs === false) return $this->sendError(400, $file->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function delete_files(Request $request)
    {
        $post = $request->post();
        $file_ids = $post['file_ids'];
        if(empty($file_ids)) return $this->sendError(400, 'param error');

        $m_file = new File();
        $rs = $m_file->delFiles($file_ids);
        if($rs === false) return $this->sendError(400, $m_file->getErrorMsg());
        
        return $this->sendSuccess($rs);
    }

    /**
     * @desc  最新上传的文件
     * @author luo
     * @param Request $request
     * @method GET
     */
    public function recent_file(Request $request)
    {
        $uid = gvar('uid');
        if(empty($uid)) return $this->sendError('uid错误');
        $before_time = time() - 20;

        $m_file = new File();
        $file = $m_file->where('create_uid', $uid)->where('create_time', 'gt', $before_time)
            ->order('file_id desc')->find();

        return $this->sendSuccess($file);
    }

}