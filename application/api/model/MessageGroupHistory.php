<?php
/**
 * Author: luo
 * Time: 2018/8/9 9:47
 */

namespace app\api\model;


use app\common\exception\FailResult;

class MessageGroupHistory extends Base
{

    const TYPE_SMS = 1; # 短信
    const TYPE_WECHAT = 2; # 微信

    protected $type = [
        'tpl_data' => 'json'
    ];

    protected function setTplDataAttr($value)
    {
        return is_array($value) ? json_encode($value) : $value;
    }

    public function sendGroup($post)
    {
        if(empty($post['type'])) return $this->user_error('发送类型错误');

        if($post['type'] == self::TYPE_SMS) {
            $rs = $this->sendGroupWithSms($post);
        } elseif($post['type'] == self::TYPE_WECHAT) {
            $rs = $this->sendGroupWithWechat($post);
        } else {
            return $this->user_error('发送类型错误');
        }

        return $rs;
    }

    public function sendGroupWithSms($post)
    {
        if(empty($post['std_id']) || empty($post['sms_content'])) return $this->user_error('短信模板错误');

        $m_std = new SmsTplDefine();
        $sms_tpl_define = $m_std->find($post['std_id']);
        if(empty($sms_tpl_define)) return $this->user_error('模板不存在');

        $sms_tpl_define = $sms_tpl_define->toArray();
        $system_sms_content_field = array_column($sms_tpl_define['tpl_define'], 'r');
        $post_sms_content_field = array_keys($post['sms_content']);
        if(!empty(array_diff($post_sms_content_field, $system_sms_content_field))
            || !empty(array_diff($system_sms_content_field, $post_sms_content_field))) {
            return $this->user_error('短信内容与模板定义不匹配');
        }

        $send_data = [
            'tpl_id'       => $sms_tpl_define['tpl_id'],
            'tpl_data'     => json_encode($post['sms_content']),
            'service_name' => $sms_tpl_define['service_name'],
            'status'       => 2,
            'content'      => tpl_replace($sms_tpl_define['apply_tpl'], $post['sms_content']),
        ];


        $group_data = $send_data;
        $group_data['type'] = self::TYPE_SMS;
        $rs = $this->allowField(true)->isUpdate(false)->save($group_data);
        if($rs === false) return false;

        $send_data['mgh_id'] = $mgh_id = $this->mgh_id;

        $ids = $post['data'];
        if(empty($ids)) return $this->user_error('发送对象错误');

        $mcl_ids = array_column($ids, 'mcl_id');
        $cu_ids = array_column($ids, 'cu_id');
        $sids = array_column($ids, 'sid');
        $eids = array_column($ids, 'eid');

        $tv = [];

        $m_sh = new SmsHistory();
        if(!empty($mcl_ids)) {
            $m_mc = new MarketClue();
            $mcl_mobile_list = $m_mc->where('mcl_id', 'in', $mcl_ids)->field('mcl_id,name,tel')->select();

            foreach($mcl_mobile_list as $clue) {
                if(empty($clue['tel'])) continue;
                $data = $send_data;
                $tv['name'] = $clue['name'];
                $data['content'] = $this->text_var_replace($data['content'],$tv);
                $data['mobile'] = $clue['tel'];
                $data['mcl_id'] = $clue['mcl_id'];
                $rs = $m_sh->addSmsHistory($data);
                if($rs === false) throw new FailResult($m_sh->getErrorMsg());
            }
        }

        if(!empty($cu_ids)) {
            $m_customer = new Customer();
            $customer_mobile_list = $m_customer->where('cu_id', 'in', $cu_ids)->field('cu_id,name,first_tel')->select();
            foreach($customer_mobile_list as $customer) {
                if(empty($customer['first_tel'])) continue;
                $data = $send_data;
                $tv['name'] = $customer['name'];
                $data['content'] = $this->text_var_replace($data['content'],$tv);
                $data['mobile'] = $customer['first_tel'];
                $data['cu_id'] = $customer['cu_id'];
                $rs = $m_sh->addSmsHistory($data);
                if($rs === false) throw new FailResult($m_sh->getErrorMsg());
            }
        }

        if(!empty($sids)) {
            $m_student = new Student();
            $student_mobile_list = $m_student->where('sid', 'in', $sids)->field('sid,student_name,first_tel')->select();
            foreach($student_mobile_list as $student) {
                $data = $send_data;
                $tv['name'] = $student['student_name'];
                $data['content'] = $this->text_var_replace($data['content'],$tv);
                $data['mobile'] = $student['first_tel'];
                $data['sid'] = $student['sid'];
                $rs = $m_sh->addSmsHistory($data);
                if($rs === false) throw new FailResult($m_sh->getErrorMsg());
            }
        }

        if(!empty($eids)) {
            $m_employee = new Employee();
            $employee_list = $m_employee->where('eid', 'in', $eids)->field('eid,ename,mobile')->select();
            foreach($employee_list as $employee) {
                $data = $send_data;
                $tv['name'] = $employee['ename'];
                $data['content'] = $this->text_var_replace($data['content'],$tv);
                $data['mobile'] = $employee['mobile'];
                $data['eid'] = $employee['eid'];
                $rs = $m_sh->addSmsHistory($data);
                if($rs === false) throw new FailResult($m_sh->getErrorMsg());
            }
        }

        $queue_data = [
            'class'  => 'SendGroupMsg',
            'mgh_id' => $mgh_id
        ];

        queue_push('Base', $queue_data);

        self::UpdateGroupHistory($mgh_id);

        return true;
    }

    public static function UpdateGroupHistory($mgh_id)
    {
        $self = new self();
        $history = $self->find($mgh_id);
        if(empty($history)) exception('群发记录不存在');

        if($history['type'] == self::TYPE_SMS) {
            $m_sh = new SmsHistory();
            $num = $m_sh->where('mgh_id', $mgh_id)->count();
            $success_num = $m_sh->where('mgh_id', $mgh_id)->where('status = 0')->count();
            $history->num = $num;
            $history->success_num = $success_num;
            $rs = $history->save();
            if($rs === false) exception($history->getError());
        }

        if($history['type'] == self::TYPE_WECHAT) {
            $m_message = new Message();
            $num = $m_message->where('mgh_id', $mgh_id)->count();
            $success_num = $m_message->where('mgh_id', $mgh_id)->where('status = 0')->count();
            $history->num = $num;
            $history->success_num = $success_num;
            $rs = $history->save();
            if($rs === false) exception($history->getError());
        }

        return true;
    }

    public function sendGroupWithWechat($post)
    {
        if(empty($post['wtd_id']) || empty($post['content'])) return $this->user_error('微信模板错误');

        $tpl_define = WechatTplDefine::get($post['wtd_id']);
        if(empty($tpl_define)) return $this->user_error('模板不存在');

        $msg_body = '';
        $msg_body = !empty($post['content']['first']) ? $msg_body . $post['content']['first'] . ' ' : $msg_body;
        if(!empty($tpl_define['tpl_define']['data'])) {
            foreach($tpl_define['tpl_define']['data'] as $item) {
                $item['label'] = !empty($item['label']) ? $item['label'] . ':' : ' ';
                if($item['field'] == '{{keyword1.DATA}}' && !empty($post['content']['keyword1.DATA'])) {
                    $msg_body .= $item['label'] . $post['content']['keyword1.DATA'] . '  ';
                }
                if($item['field'] == '{{keyword2.DATA}}' && !empty($post['content']['keyword2.DATA'])) {
                    $msg_body .= $item['label'] . $post['content']['keyword2.DATA'] . '  ';
                }
                if($item['field'] == '{{keyword3.DATA}}' && !empty($post['content']['keyword3.DATA'])) {
                    $msg_body .= $item['label'] . $post['content']['keyword3.DATA'] . '  ';
                }
                if($item['field'] == '{{keyword4.DATA}}' && !empty($post['content']['keyword4.DATA'])) {
                    $msg_body .= $item['label'] . $post['content']['keyword4.DATA'] . '  ';
                }
            }
        }
        $msg_body = !empty($post['content']['remark']) ? $msg_body . ' ' . $post['content']['remark'] : $msg_body;

        $group_data = [
            'content'       => $msg_body,
            'tpl_id'        => $tpl_define['tpl_id'],
            'business_type' => $tpl_define['business_type'],
            'tpl_data'      => $post['content'],
            'type'          => self::TYPE_WECHAT
        ];
        $rs = $this->isUpdate(false)->save($group_data);
        if($rs === false) return false;

        $mgh_id = $this->mgh_id;
        $send_data = [
            'business_type' => $tpl_define['business_type'],
            'url'           => !empty($post['content']['url']) ? $post['content']['url'] : '',
            'title'         => !empty($post['content']['title']) ? $post['content']['title'] : '',
            'content'       => $msg_body,
            'send_mode'     => 1,
            'tpl_data'      => $post['content'],
            'status'        => 2,
            'mgh_id'        => $mgh_id,
        ];


        $ids = $post['data'];
        if(empty($ids)) return $this->user_error('发送对象错误');

        $cu_ids = array_column($ids, 'cu_id');
        $sids = array_column($ids, 'sid');
        $eids = array_column($ids, 'eid');

        $m_message = new Message();
        if(!empty($cu_ids)) {
            $m_customer = new Customer();
            $customer_mobile_list = $m_customer->where('cu_id', 'in', $cu_ids)->field('cu_id,first_tel')->select();
            foreach($customer_mobile_list as $customer) {
                $data = $send_data;
                $data['cu_id'] = $customer['cu_id'];
                $rs = $m_message->addMessage($data);
                if($rs === false) throw new FailResult($m_message->getErrorMsg());
            }
        }

        if(!empty($sids)) {
            $m_student = new Student();
            $student_mobile_list = $m_student->where('sid', 'in', $sids)->field('sid,first_tel')->select();
            foreach($student_mobile_list as $student) {
                $data = $send_data;
                $data['sid'] = $student['sid'];
                $rs = $m_message->addMessage($data);
                if($rs === false) throw new FailResult($m_message->getErrorMsg());
            }
        }

        if(!empty($eids)) {
            $m_employee = new Employee();
            $employee_list = $m_employee->where('eid', 'in', $eids)->field('eid,mobile')->select();
            foreach($employee_list as $student) {
                $data = $send_data;
                $data['eid'] = $student['eid'];
                $rs = $m_message->addMessage($data);
                if($rs === false) throw new FailResult($m_message->getErrorMsg());
            }
        }

        $queue_data = [
            'class'  => 'SendGroupMsg',
            'mgh_id' => $mgh_id
        ];

        queue_push('Base', $queue_data);

        self::UpdateGroupHistory($mgh_id);

        return true;
    }

    /**
     * 文本变量替换
     * @param $content
     * @param $tv
     * @return mixed
     */
    protected function text_var_replace($content,$tv){
        $tv_define = [
            'name'  => '[姓名]'
        ];
        foreach($tv_define as $v1=>$v2){
            if(isset($tv[$v1])) {
                $content = str_replace($v2, $tv[$v1],$content);
            }
        }
        return $content;
    }

}