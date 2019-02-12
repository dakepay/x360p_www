<?php
/**
 * Author: luo
 * Time: 2018/5/11 18:14
 */

namespace app\api\controller;


use app\api\model\LessonStandardFile;
use think\Request;

class LessonStandardFiles extends Base
{
    public function get_list(Request $request)
    {
        $get = $request->get();
        $m_lsf = new LessonStandardFile();

        if(isset($get['og_id'])) {
            gvar('og_id', $get['og_id']);
            $get['bid'] = -1;
        }

        if(isset($get['lid'])) {
            $get['bid'] = -1;
        }
        $ret = $m_lsf->getSearchResult($get);

        gvar('og_id', gvar('client.og_id'));
        return $this->sendSuccess($ret);
    }

    /**
     * @desc  添加课标
     * @author luo
     * @param Request $request
     * @method POST
     */
    public function post(Request $request)
    {
        $post = $request->post();
        $m_lsf = new LessonStandardFile();
        $rs = $m_lsf->addLessonStandardFile($post);
        if($rs === false) return $this->sendError(400, $m_lsf->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $lsf_id = input('id');
        $put = $request->put();
        $file_items = isset($put['lesson_standard_file_item']) ? $put['lesson_standard_file_item'] : [];

        $lesson_standard_file = LessonStandardFile::get($lsf_id);
        if(empty($lesson_standard_file)) return $this->sendError(400, '课标不存在');
        $rs = $lesson_standard_file->edit($put, $file_items);
        if($rs === false) return $this->sendError(400, $lesson_standard_file->getErrorMsg());

        return $this->sendSuccess();
    }

    public function delete(Request $request)
    {
        $lsf_id = input('id');
        $lesson_standard_file = LessonStandardFile::get($lsf_id);
        if(empty($lesson_standard_file)) return $this->sendSuccess();

        $rs = $lesson_standard_file->delOne();
        if($rs === false) return $this->sendError(400, $lesson_standard_file->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function delete_batch(Request $request)
    {
        $post = $request->post();
        $lsf_ids = empty($post['lsf_ids']) ? [] : $post['lsf_ids'];
        if(empty($lsf_ids) || !is_array($lsf_ids)) return $this->sendError(400, '参数错误');

        $m_lsf = new LessonStandardFile();
        $rs = $m_lsf->delBatch($lsf_ids);
        if($rs === false) return $this->sendError(400, $m_lsf->getErrorMsg());
        
        return $this->sendSuccess();
    }

    /**
     * @desc  启用禁用课标
     * @author luo
     * @param Request $request
     * @method POSt
     */
    public function enable(Request $request)
    {
        $post = $request->post();
        $lsf_ids = empty($post['lsf_ids']) ? [] : $post['lsf_ids'];
        if(!is_array($lsf_ids) || !isset($post['enable'])) return $this->sendError(400, '参数错误');

        $m_lsf = new LessonStandardFile();
        $rs = $m_lsf->where('lsf_id', 'in', $lsf_ids)->update(['enable' => intval($post['enable'])]);
        if($rs === false) return $this->sendError(400, $m_lsf->getErrorMsg());
        
        return $this->sendSuccess();
    }

}