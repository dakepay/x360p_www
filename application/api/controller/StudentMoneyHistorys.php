<?php
/**
 * Author: luo
 * Time: 2018/1/9 19:53
 */

namespace app\api\controller;


use app\api\model\Student;
use app\api\model\StudentMoneyHistory;
use think\Request;

class StudentMoneyHistorys extends Base
{

    public function get_list(Request $request)
    {
        $with = [];
        $input = $request->get();
        if(isset($input['with'])){
            $with[] = $input['with'];
        }
        $model = new StudentMoneyHistory();

        $ret = $model->getSearchResult($input,$with,true);
        
        return $this->sendSuccess($ret);

    }


    /**
     * @desc  学员电子钱包变动
     * @author luo
     * @method POST
     */
    public function post(Request $request) {
        $post = $request->post();
        $student = Student::get($post['sid']);
        if(empty($student)){
            return $this->sendError(400,'学员不存在，或已经删除');
        }
        if($post['business_type'] == StudentMoneyHistory::BUSINESS_TYPE_DEC) {
            $post['money'] = -$post['amount'];
        } else {
            $post['money'] = $post['amount'];
        }
        $rs = $student->changeMoney($student, $post);
        if($rs === false) return $this->sendError(400, $student->getErrorMsg());

        return $this->sendSuccess();
    }

    /**
     * @return Redirect|\think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function print()
    {
        $smh_id = input('smh_id');
        $m_smh = new StudentMoneyHistory();
        $data = $m_smh->makePrintData($smh_id);
        if(empty($data)) return $this->sendError(400, $m_smh->getErrorMsg());

        return $this->sendSuccess($data);
    }

}