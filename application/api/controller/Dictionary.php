<?php
/** 
 * Author: luo
 * Time: 2017-10-11 12:09
**/

namespace app\api\controller;

use think\Request;
use app\api\model\Dictionary as DictionaryModel;

class Dictionary extends Base
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
        $ret = ['list'=>[]];
        $list = $this->m_dictionary->where($w)->order('sort DESC')->select();
        $list = !empty($list) ? collection($list)->toArray() : $list;

        $og_id = gvar('og_id');
        if(!empty($og_id) && $og_id > 0) {
            $org_system_dictionary = \app\api\model\Dictionary::GetOrgSystemDictionary($og_id);
            foreach($list as $key => $first_row) {
                if($first_row['og_id'] != $og_id && $first_row['is_system'] != 1) {
                    unset($list[$key]);
                    continue;
                }
                $list[$key] = !empty($org_system_dictionary[$first_row['did']]) ? $org_system_dictionary[$first_row['did']] : $first_row;
            }
        }
        if($pid == 0) {
            $top_dids = get_top_dict_dids();

            foreach ($list as $k => $r) {
                if (in_array($r['did'], $top_dids)) {
                    array_push($ret['list'], $r);
                }
            }
        }else{
            $ret['list'] = $list;
        }
        sort($ret['list']);
        return $this->sendSuccess($ret);
    }

    public function put(Request $request)
    {
        $did = input('id');
        $put = $request->put();

        $dictionary = \app\api\model\Dictionary::get($did);
        if(empty($dictionary)) {
            return $this->sendError(400, '字典不存在,无法修改');
        }

        $rs = $dictionary->updateDictionary($put);
        if($rs === false) return $this->sendError(400, $dictionary->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @desc  获取字典的具体信息
     * @author luo
     * @url   dictionary/:id/
     * @method GET
     */
    public function get_detail(Request $request, $id = 0) {
        $id = input('id/d');
        $og_id = gvar('og_id');
        $group = DictionaryModel::all(['pid' => $id]);
        $group = !empty($group) ? collection($group)->toArray() : $group;
        if(!empty($og_id) && $og_id > 0) {
            $group = \app\api\model\Dictionary::ReplaceOrgSystemDictionary($group);
        }

        foreach($group as $per_group) {
            $list = DictionaryModel::get(['pid' => $per_group['did']]);
            $list = !empty($list) ? collection($list)->toArray() : $list;
            $list = \app\api\model\Dictionary::ReplaceOrgSystemDictionary($list);
            $per_group['options'] = $list;
        }

        $ret['list'] = $group;
        return $this->sendSuccess($ret);
    }

}