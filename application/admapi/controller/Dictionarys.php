<?php

namespace app\admapi\controller;

use think\Request;
use app\admapi\model\Dictionary;

class Dictionarys extends Base
{
    /**
     * @desc  所有字典
     * @author luo
     * @param Request $request
     * @url   dictionary
     * @method GET
     */
    public function get_list(Request $request) {
        $pid = input('get.pid',0,'intval');
        $w['pid'] = $pid;
        $m_dictionary = new Dictionary();
        $ret['list'] = $m_dictionary->where($w)->order('sort DESC')->select();

        return $this->sendSuccess($ret);
    }

    /**
     * @desc  获取字典的具体信息
     * @author luo
     * @url   dictionary/:id/
     * @method GET
     */
    public function get_detail(Request $request, $id = 0) {
        $id = input('id/d');
        $m_dictionary = new Dictionary();

        $group = $m_dictionary::all(['pid' => $id]);

        foreach($group as $per_group) {
            $list = $m_dictionary::get(['pid' => $per_group['did']]);
            $per_group['options'] = $list;
        }

        $ret['list'] = $group;
        return $this->sendSuccess($ret);
    }

    public function put(Request $request)
    {
        $did = input('did/d');
        $input = $request->put();

        $dictionary = Dictionary::get($did);
        if(empty($dictionary)) {
            return $this->sendError(400, '字典不存在,无法修改');
        }

        $rs = $dictionary->updateDictionary($input);
        if($rs === false) return $this->sendError(400, $dictionary->getErrorMsg());

        return $this->sendSuccess();
    }

}