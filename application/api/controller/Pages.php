<?php
/**
 * Author: luo
 * Time: 2018/5/21 16:37
 */

namespace app\api\controller;


use app\api\model\Page;
use think\Request;

class Pages extends Base
{

    public function get_list(Request $request)
    {
        $get = $request->get();
        $with_count = [];
        if(isset($get['with_count']) && !empty($get['with_count'])) {
            $with_count = explode(',', $get['with_count']);
        }
        $m_page = new Page();
        $ret = $m_page->getSearchResult($get);
        foreach($ret['list'] as &$row) {
            if(in_array('children_num', $with_count)) {
                $row['children_num'] = $m_page->where('parent_pid', $row['page_id'])->count();
            }
        }
        
        return $this->sendSuccess($ret);
    }

    public function delete(Request $request)
    {
        $page_id = input('id');
        $page = Page::get($page_id);
        $rs = $page->delPage();
        if($rs === false) return $this->sendError(400, $page->getErrorMsg());
        return $this->sendSuccess();
    }

}