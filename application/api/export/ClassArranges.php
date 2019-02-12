<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/7/15
 * Time: 18:43
 */
namespace app\api\export;

use app\common\Export;
use app\api\model\CourseArrange;
use app\api\model\ClassStudent;
use app\api\model\CourseArrangeStudent;

class ClassArranges extends Export
{
    protected $res_name = 'course_arrange';

    protected function get_columns(){

    }

    protected function get_title(){
        $title = '上课名单';
        return $title;
    }

    protected function get_real_name($sid,$cu_id){
        if($sid){
            $one = m('student')->where('sid',$sid)->field('student_name')->find();
            if($one){
                return $one->student_name;
            }else{
                return '-';
            }
        }else{
            $one = m('customer')->where('cu_id',$cu_id)->field('name')->find();
            if($one){
                return $one->name;
            }else{
                return '-';
            }
        }
    }

    protected function get_nick_name($sid,$cu_id){
        if($sid){
            $one = m('student')->where('sid',$sid)->field('nick_name')->find();
        }else{
            $one = m('customer')->where('sid',$sid)->field('nick_name')->find();
        }
        if($one){
            return $one->nick_name;
        }else{
            return '-';
        }
    }

    protected function get_birth_time($sid,$cu_id){
        if($sid){
            $one = m('student')->where('sid',$sid)->field('birth_time')->find();
        }else{
            $one = m('customer')->where('sid',$sid)->field('birth_time')->find();
        } 
        if($one->birth_time){
            return $one->birth_time;
        }else{
            return '-';
        }
    }

    protected function get_first_tel($sid,$cu_id){
        if($sid){
            $one = m('student')->where('sid',$sid)->field('first_tel')->find();
        }else{
            $one = m('customer')->where('cu_id',$cu_id)->field('first_tel')->find();
        }
        if($one->first_tel){
            return $one->first_tel;
        }else{
            return '-';
        }
    }

    protected function get_infos($lid,$sj_id,$teach_eid,$second_eid,$start,$end,$cr_id,$ca_id){
        $ca_info = get_ca_info($ca_id);
        if($ca_info['cid']){
            return get_class_name($ca_info['cid']).' ['.int_hour_to_hour_str($start).'-'.int_hour_to_hour_str($end).' 教室：'.get_class_room($cr_id).' 主讲：'.get_teacher_name($teach_eid).' 助教：'.get_teacher_name($second_eid).' ]';
        }else{
            return $ca_info['name'].' ['.int_hour_to_hour_str($start).'-'.int_hour_to_hour_str($end).' 教室：'.get_class_room($cr_id).' 主讲：'.get_teacher_name($teach_eid).' 助教：'.get_teacher_name($second_eid).' ]';
        }
    }

    public function get_data()
    {
        $model = new CourseArrange();
        if($this->params['teach_eid']){
            $this->params['teach_eid'] = $this->params['teach_eid'];
        }else{
            unset($this->params['teach_eid']);
        }
        if($this->params['cr_id']){
            $this->params['cr_id'] = $this->params['cr_id'];
        }else{
            unset($this->params['cr_id']);
        }
        if($this->params['int_start_hour']){
            $this->params['int_start_hour'] = date('Hi',strtotime($this->params['int_start_hour']));
        }else{
            unset($this->params['int_start_hour']);
        }
        if($this->params['int_end_hour']){
            $this->params['int_end_hour'] = date('Hi',strtotime($this->params['int_end_hour']));;
        }else{
            unset($this->params['int_end_hour']);
        }
        $result = $model->field('int_day,cr_id,ca_id,lid,sj_id,teach_eid,second_eid,int_start_hour,int_end_hour')->order('int_start_hour asc')->getSearchResult($this->params,[],false);
        $list = collection($result['list'])->toArray();
        foreach ($list as $k => $v) {
            $list[$k]['infos'] = $this->get_infos($v['lid'],$v['sj_id'],$v['teach_eid'],$v['second_eid'],$v['int_start_hour'],$v['int_end_hour'],$v['cr_id'],$v['ca_id']);
            $list[$k]['c_student'] = (new CourseArrangeStudent)->field('sid,cu_id')->where('ca_id',$v['ca_id'])->getSearchResult([],[],false);
            foreach ($list[$k]['c_student']['list'] as $k1 => $v1) {
                $list[$k]['c_student']['list'][$k1]['number'] = $k1+1;
                $list[$k]['c_student']['list'][$k1]['student_name'] = $this->get_real_name($v1['sid'],$v1['cu_id']);
                $list[$k]['c_student']['list'][$k1]['nick_name'] = $this->get_nick_name($v1['sid'],$v1['cu_id']);
                $list[$k]['c_student']['list'][$k1]['birth_time'] = $this->get_birth_time($v1['sid'],$v1['cu_id']);
                $list[$k]['c_student']['list'][$k1]['first_tel'] = $this->get_first_tel($v1['sid'],$v1['cu_id']);
                $list[$k]['c_student']['list'][$k1]['parent'] = '';
            } 
        }

        // print_r($list);exit;

        if (!empty($list)) {
            return collection($list)->toArray();
        }
        return [];
    }

    /**
     * 自定义导出方法
     * @param $list
     * @param $export_params
     * @param $excel
     * @return mixed
     */
    public function customExport($list,$excel,$params){
        $int_day = date('Y-m-d',strtotime($list[0]['int_day']));
        $sheet = $excel->getSheet();
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(20);
        // 合并单元格
        $sheet->mergeCells('A1:F1');
        // 设置标题
        $sheet->setCellValue("A1","上课名单(".$int_day.")");
        $style = $sheet->getStyle('A1');
        $style->getFont()->setName('宋体')->setSize(14)->setBold(false);
        $sheet->getRowDimension('1')->setRowHeight(25);
        $i = 2;
        foreach ($list as $k => $v) {
            $sheet->setCellValue("A".$i,'序号')->setCellValue("B".$i,'学生姓名')->setCellValue("C".$i,'学生昵称')->setCellValue("D".$i,'生日')->setCellValue("E".$i,'手机号码')->setCellValue("F".$i,'家长签到');
            $sheet->getRowDimension($i)->setRowHeight(25);
            $i++;
            $sheet->setCellValue("A".$i,$v['infos']);
            $sheet->mergeCells('A'.$i.':F'.$i);
            $sheet->getRowDimension($i)->setRowHeight(25);
            $i++;
            foreach ($v['c_student']['list'] as $k1 => $v1) {
                $sheet->setCellValue("A".$i,$v1['number'])->setCellValue("B".$i,$v1['student_name'])->setCellValue("C".$i,$v1['nick_name'])->setCellValue("D".$i,$v1['birth_time'])->setCellValue("E".$i,$v1['first_tel'])->setCellValue("F".$i,$v1['parent']);
                $sheet->getRowDimension($i)->setRowHeight(25);
                $i++;
            }
            $sheet->setCellValue("A".$i,'');
            $sheet->mergeCells('A'.$i.':F'.$i);
            $i++;
        }

        $sheet->getStyle('A1:F'.$i)->getAlignment()->applyFromArray( [ 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER] );

        $sheet->getStyle('A1:F'.$i)->getBorders()->applyFromArray( [ 'allBorders' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => [ 'rgb' => '000000' ] ] ] );

        $sheet->setTitle($params['title']);
        return $excel->output();

    }
}