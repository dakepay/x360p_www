<?php
namespace app\mccapi\controller;

use think\Log;
use think\Request;


class Student extends Base{
    public $noRest  = true;

    /**
     * 更新个人信息
     * @param Request $request [description]
     */
    public function UpdateProfile(Request $request){

    }


    /**
     * 提交建议反馈
     * @param Request $request [description]
     */
    public function Feedback(Request $request){

    }


    /**
     * 获得班级列表
     * @param Request $request [description]
     */
    public function Classes(Request $request){

    }


    /**
     * 申请列表
     * @param Request $request [description]
     */
    public function ClassApplys(Request $request){

    }

    /**
     * 提交入班申请
     * @param Request $request [description]
     */
    public function SubmitClassApply(Request $request){

    }

    /**
     * 获取动态
     * 接受参数
     * @param Request $request [description]
     */
    public function Feeds(Request $request){
        
    }
}