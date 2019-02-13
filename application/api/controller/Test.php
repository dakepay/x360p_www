<?php
namespace app\api\controller;

use app\api\model\CourseArrange;
use app\api\model\CourseRemindPlan;
use app\api\model\TextbookSection;
use Overtrue\Pinyin\Pinyin;
use think\Request;
use app\common\job\SendWxTplMsg;
use app\common\job\AutoPushFtReviewRemind;

class Test extends Base
{
    public $apiAuth = false;
    public function index() {
        $job_data['class'] = 'aaabbb';
        $task_id = queue_task_id('course_remind','day0');
        queue_push('Base',$job_data,null,0,$task_id);
        return '0k';
    }

    public function cancel(){
        queue_cancel(queue_task_id('course_remind','day0'));
        return '0k';
    }

    public function download(){
        $url = 'https://img3.doubanio.com/view/subject/l/public/s29857186.jpg';
        $file = download_file($url);
        print_r($file);
        $cloud_file = upload_file($file['save_path']);
        if(!$cloud_file){
            exit('error');
        }

        print_r($cloud_file);
    }


    public function send(Request $request){
        $data = input();
        $a = new AutoPushFtReviewRemind();
        $rs = $a->doAutoPushFtReviewRemindJob($data['data']);
        var_dump($rs);exit;

    }

    public function aaa(Request $request){
        $data = input();
        $a = new CourseArrange();
        $rs = $a->autoOneDayCourseTeachers($data['day'],$data['bid']);

        $bb = $a->AutoRemindTeacher($rs);
        var_dump($bb);

    }

    public function test(Request $request){
        $a = new TextbookSection();
        $pwd = $a->getLastTbs(7,6);
        var_dump($pwd);exit;

    }





}