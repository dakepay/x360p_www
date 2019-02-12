<?php

namespace app\api\model;

use app\common\Report;

class ReportStudentCollect extends Report
{
    protected $report_name = '学员汇总分析表';
    protected $report_table_name = 'report_student_collect';

    protected $sid_row_field_value = [];


    protected $report_fields = [
        'sid'       => ['title' => '学员ID', 'type' => Report::FTYPE_INT],
        'bid'       => ['title' => '校区ID', 'type' => Report::FTYPE_INT],
        'status'    => ['title' => '学员状态', 'type' => Report::FTYPE_INT],
        'school_id' => ['title' => '学校ID', 'type' => Report::FTYPE_INT],
        'lids'      => ['title' => '课程', 'type' => Report::FTYPE_VARCHAR64],
        'cids'      => ['title' => '班级', 'type' => Report::FTYPE_VARCHAR64],
        'eids'      => ['title' => '老师', 'type' => Report::FTYPE_VARCHAR64],
    ];

    /**
     * 创建表格
     * @throws \Exception
     */
    protected function create_table(){
        if(empty($this->report_table_name)){
            exception('report_table_name empty!');
        }
        $table_pre = config('database.prefix');
        $field_sql = $this->build_create_table_field_sql();
        $sql = <<<EOF
CREATE TABLE `{$table_pre}{$this->report_table_name}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `og_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '机构ID',
  {$field_sql}
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `create_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建用户ID',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `delete_uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除用户ID',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='{$this->report_name}';
EOF;
        db()->execute($sql);
    }




    /**
     * 获取查询报表
     * @param  [type]  $input      [description]
     * @param  boolean $pagenation [description]
     * @return [type]              [description]
     */
    public function getDaySectionReport($input,$pagenation = false){
        

        $og_id = gvar('og_id');

        $w['og_id'] = $og_id;
        $w['bid'] = $input['bid'];


        $query_sids = (new Student)->where($w)->column('sid');

        $model = new ReportStudentCollect;

        if(isset($input['lid'])){
        	$w[] = ['exp', "find_in_set({$input['lid']},lids)"];
        }
        if(isset($input['cid'])){
        	$w[] = ['exp', "find_in_set({$input['cid']},cids)"];
        }
        if(isset($input['eid'])){
        	$w[] = ['exp', "find_in_set({$input['eid']},eids)"];
        }

        $ret = $model->where($w)->getSearchResult($input);
        if(!$ret['list'] || isset($input['refresh']) && $input['refresh'] == 1){
        	$ret['list'] = [];
        	$build_sids = $query_sids;
        }else{
        	if(count($ret['list']) < count($query_sids)){
        		$result_sids = array_column($ret['list'],'sid');
                $build_sids = array_values(array_diff($query_sids,$result_sids));
        	}
        }
        // print_r($build_sids);exit;
        if(!empty($build_sids)){
        	foreach ($build_sids as $sid) {
        		$this->buildDaySectionReports($sid);
        	}
        	$ret = $model->where($w)->getSearchResult($input);
        }

        return $ret;

    }



	/**
     * 构建区间报表
     * @param $start_date
     * @param $end_date
     * @param $bid
     */
    public function buildDaySectionReports($sid){
        
        $sinfo = get_student_info($sid);

        $this->init_sid_row_field($sinfo);
        $this->build_day_section_report_before($sinfo);
        $this->build_day_section_report_center($sinfo);
        $this->build_day_section_report_after($sinfo);

        return $this->save_day_section_report($sinfo);


    }

    public function init_sid_row_field(&$sinfo)
    {
    	$this->sid_row_field_value = [];
    	$this->sid_row_field_value['og_id'] = $sinfo['og_id'];
    	$this->sid_row_field_value['sid'] = $sinfo['sid'];
    	$this->sid_row_field_value['bid'] = $sinfo['bid'];
    	$this->sid_row_field_value['status'] = $sinfo['status'];
    	$this->sid_row_field_value['school_id'] = $sinfo['school_id'];
    	return $this;
    }

    /**
     * 生成报表前段
     * @param $params
     */
    protected function build_day_section_report_before(&$sinfo){
        $this->calculate_lids($sinfo);
        $this->calculate_cids_eids($sinfo);
    }

    /**
     * 生成报表中段
     * @param $params
     */
    protected function build_day_section_report_center(&$sinfo){

    }


    /**
     * 生成报表后段
     * @param $params
     */
    protected function build_day_section_report_after(&$sinfo){

    }


    /**
     * @param $params
     * @return array|false|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function save_day_section_report(&$sinfo){
        if(!$this->save_to_table){
            return array_merge($this->sid_row_field_value,['id'=>0]);
        }

        $model  = new static();
        $w_ex['sid'] = $sinfo['sid'];
        $w_ex['og_id'] = $sinfo['og_id'];
        $ex_model = $model->where($w_ex)->find();
        if($ex_model){
            foreach($this->report_fields as $field=>$r){
                if(isset($this->sid_row_field_value[$field])){
                    $ex_model[$field] = $this->sid_row_field_value[$field];
                }
            }
            $result = $ex_model->save();
            $result = $ex_model->toArray();
        }else{
            $result = $model->save($this->sid_row_field_value);
            if(!$result){
                return [];
            }
            $result = $model->toArray();
        }
        return $result;
    }


    /**
     * 统计学员课程
     * @param  [type] $sinfo [description]
     * @return [type]        [description]
     */
    protected function calculate_lids($sinfo)
    {
        $lids = [];

        $w['sid'] = $sinfo['sid'];

        $model = new StudentLesson;

        $lids = $model->where($w)->column('lid');

        $lids = array_unique($lids);

        $this->sid_row_field_value['lids'] = implode(',',$lids);
    }


    protected function calculate_cids_eids($sinfo)
    {
    	$cids = [];
    	$eids = [];

    	$w['sid'] = $sinfo['sid'];

    	$model = new ClassStudent;

    	$cids = $model->where($w)->column('cid');
    	$cids = array_unique($cids);
    	foreach ($cids as $cid) {
    		$cinfo = get_class_info($cid);
    		$eids[] = $cinfo['teach_eid'];
    	}
    	$eids = array_unique($eids);
    	$this->sid_row_field_value['cids'] = implode(',',$cids);
    	$this->sid_row_field_value['eids'] = implode(',',$eids);
    }







  
}