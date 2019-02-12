<?php
/**
 * Author: luo
 * Time: 2018/5/29 9:57
 */

namespace app\api\controller;


use app\api\model\KnowledgeItem;
use app\api\model\KnowledgeItemLike;
use think\Request;

class KnowledgeItems extends Base
{
    public function get_list(Request $request)
    {
        $get = $request->get();
        $m_ki = new KnowledgeItem();
        if(isset($get['keywords']) && !empty($get['keywords'])) {
            $m_ki->where('keywords', 'like', '%'.$get['keywords'].'%');
            unset($get['keywords']);
        }
        //是否返回当前登录人的点赞
        if(isset($get['with_my_like'])) {
            $with_my_like = true;
            unset($get['with_my_like']);
        }
        $ret = $m_ki->with('knowledge_item_file')->getSearchResult($get);
        $m_kil = m('KnowledgeItemLike');
        $eid = \app\api\model\User::getEidByUid(gvar('uid'));
        foreach($ret['list'] as &$row) {
            if(!empty($with_my_like)) {
                $row['my_like'] = $m_kil->where('eid', $eid)->where('ki_id', $row['ki_id'])->find();
            }
        }

        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        // return parent::post($request);
        $knowledge_data = $request->post();
        $knowledge_file_data = isset($knowledge_data['knowledge_item_file']) ? $knowledge_data['knowledge_item_file'] : [];

        $model = new KnowledgeItem;
        $ret = $model->addOneKnowledge($knowledge_data,$knowledge_file_data);
       
        if($ret !== true){
            return $this->sendError(400,$model->getErrorMsg());
        }

        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        // return parent::put($request);
        $id = input('id/d');
        if(empty($id)){
            return $this->sendError(400,'param error');
        }
        $knowledge_data = KnowledgeItem::get($id);
        if(empty($knowledge_data)){
            return $this->sendError(400,'source not exists');
        }
        $put = $request->put();
        $knowledge_file_data = isset($put['knowledge_item_file']) ? $put['knowledge_item_file'] : [];

        $ret = $knowledge_data->edit($put,$knowledge_file_data);
        
        if($ret === false) return $this->sendError(400, $knowledge_data->getErrorMsg());

        return $this->sendSuccess();

    }

    public function delete(Request $request)
    {
        $ki_id = input('id');
        $knowledge_item = KnowledgeItem::get($ki_id);
        if(empty($knowledge_item)) return $this->sendSuccess();

        $rs = $knowledge_item->delKnowledgeItem();
        if($rs === false) return $this->sendError(400, $knowledge_item->getErrorMsg());
        
        return $this->sendSuccess();
    }

    public function post_my_like(Request $request)
    {
        $ki_id = input('id');
        $eid = input('eid');
        $m_kil = new KnowledgeItemLike();
        $rs = $m_kil->like(['ki_id' => $ki_id, 'eid' => $eid]);
        if($rs === false) return $this->sendError(400, $m_kil->getErrorMsg());

        return $this->sendSuccess();
    }

    public function delete_my_like(Request $request)
    {
        $ki_id = input('id');
        $eid = input('eid');
        $m_kil = new KnowledgeItemLike();
        $rs = $m_kil->cancelLike(['ki_id' => $ki_id, 'eid' => $eid]);
        if($rs === false) return $this->sendError(400, $m_kil->getErrorMsg());
        
        return $this->sendSuccess();
    }


}