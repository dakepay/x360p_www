<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/8
 * Time: 18:05
 */

namespace app\api\controller;

use think\helper\Time;
use Think\Request;
use app\api\model\TimeSection;
use app\api\model\SeasonDate;

class TimeSections extends Base
{
    /**
     * 获得时间段设置
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function get_list(Request $request){
        $input = $request->request();
        $mTimeSection = new TimeSection();
        $mSeasonDate = new SeasonDate();
        $result = $mTimeSection->getSearchResult($input,[],false);
        $result['date'] = $mSeasonDate->getSeasonDate($input['bid'],$input['season']);
        return $this->sendSuccess($result);
    }

    public function get_branch_time_sections(Request $request)
    {
        $input = $request->only(['bid', 'season']);
        $mTimeSection = new TimeSection();
        $ret['list']  = $mTimeSection->getBranchTimeSections($input['bid'], $input['season']);
        return $this->sendSuccess($ret);
    }

    public function post(Request $request)
    {
        $input = $request->post();
        $mTimeSection = new TimeSection();
        $result = $mTimeSection->addTimeSections($input);
        if(!$result){
            return $this->sendError(400,$mTimeSection->getError());
        }

        return $this->sendSuccess();
    }

    public function post_copy(Request $request){
        $input = $request->post();
        $id = $request->param('id');
        
        if(empty($input['week_days'])){
            return $this->sendError(400,'请选择要复制的周天!');
        }
        $mTimeSection = new TimeSection();
        $result = $mTimeSection->copyTime($id,$input['week_days']);

        if(!$result){
            return $this->sendError(400,$mTimeSection->getError());
        }

        return $this->sendSuccess();
    }

    public function put(Request $request)
    {
        $tsid = intval($request->param('id/d'));

        $input = $request->put();

        $result = $this->validate($input, 'TimeSection.edit');
        if ($result !== true) {
            return $this->sendError(400, $result);
        }

        $mTimeSection = new TimeSection();

        $result = $mTimeSection->editTimeSection($input,$tsid);

        if(!$result){
            $this->sendError(400,$mTimeSection->getError());
        }

        return $this->sendSuccess();
    }

}