<?php

/**
 * @Author: Administrator
 * @Date:   2017-11-07 14:44:14
 * @Last Modified by:   Administrator
 * @Last Modified time: 2017-11-17 20:14:30
 */
namespace app\api\model;

class PrintTpl extends Base
{
	const FORMAT_BAIDAN 	= 1;			//打白单
	const FORMAT_XIAOPIAO   = 2;			//小票打印
	const FORMAT_TAODA		= 3;			//针式套打

    public $skip_insert_bid = true;

	protected $type = [
		'json'	=> 'json'
	];
	/**
	 * 获得缺省打印模板配置
	 * 结构为
	 * [
	 * 1=>[
	 * 		1=>['content'=>''],
	 * 		2=>['content'=>''],
	 * 		3=>['content'=>['paper_width'=>600,'paper_height'=>400,'items'=>[]]
	 * 		],
	 * 	2=>[
	 * 	]
	 * ]
	 * ]
	 * @return [type] [description]
	 */
	public function getDefaultTpls(){
		$ret = [];
		$bill_types = include(CONF_PATH.'print'.DS.'bills.php');
		foreach($bill_types as $id=>$bill){
			$ret[$id]   = $this->get_filetype_config($id);
			$ret[$id]	= array_merge($ret[$id],$bill);
		}
		return $ret;
	}

	protected function get_filetype_config($bill_type){
		$ret = [];

		$config_root_path = CONF_PATH.'print'.DS.'tpl';
		$file_format = '%s'.DS.'%s'.DS.'%s'.'%s';

		$baidan_content_file = sprintf($file_format,
								$config_root_path,
								$bill_type,
								PrintTpl::FORMAT_BAIDAN,
								'.html'
								);
		$ret[0] = [];
		if(file_exists($baidan_content_file)) {
            $ret[1] = [
                'content' => file_get_contents($baidan_content_file)
            ];

        }else{
		    $ret[1] = ['content'=>''];
        }

		$xiaopiao_content_file = sprintf($file_format,
								$config_root_path,
								$bill_type,
								PrintTpl::FORMAT_XIAOPIAO,
								'.html'
								);
		if(file_exists($xiaopiao_content_file)){
            $ret[2] = [
                'content'	=> file_get_contents($xiaopiao_content_file)
            ];
        }else{
            $ret[2] = ['content'=>''];
        }



		$taoda_content_file = sprintf($file_format,
								$config_root_path,
								$bill_type,
								PrintTpl::FORMAT_TAODA,
								'.json'
								);

		if(file_exists($taoda_content_file)){
            $ret[3] = [
                'content'	=> json_decode(file_get_contents($taoda_content_file))
            ];

        }else{
            $ret[3] = ['content'=>''];
        }


		return $ret;
	}

	/**
	 * 获取所有的打印模板配置
	 * @return [type] [description]
	 */
	public function getAllPrintTplConfig(){
		$ret['list']    = (array)$this->select();
		$ret['default'] = $this->getDefaultTpls();
		return $ret;
	}


	public function addPrintTpl($input){
		$input['bid' ] = 0;
		request()->bind('bid',0);
		try{
			$this->resetDefault($input['bid'],$input['bill_type']);
			$this->isUpdate(false)->allowField(true)->save($input);
		}catch(\Exception $e){
        	$this->user_error($e->getMessage());
            return false;
        }
		return true;
	}


	public function updatePrintTpl($input,$pt_id){
		$tpl = $this->find($pt_id);
		if(!$tpl){
			return $this->input_param_error('pt_id');
		}
		try{
			$this->resetDefault($input['bid'],$input['bill_type'],$tpl->pt_id);
			$input['bid'] = 0;
			$tpl->data($input, true)->save();
		}catch(\Exception $e){
        	$this->user_error($e->getMessage());
            return false;
        }
		return true;
	}

	/**
	 * 重置所有同类型模板的初始值
	 * @param  [type] $bid       [description]
	 * @param  [type] $bill_type [description]
	 * @return [type]            [description]
	 */
	public function resetDefault($bid,$bill_type,$pt_id = 0){
        $w['og_id']     = gvar('og_id');
		$w['bill_type'] = $bill_type;
		if($pt_id != 0){
			$w['pt_id'] = ['NEQ',$pt_id];
		}
		$update['is_default'] = 0;
		$result = $this->save($update,$w);
		if(false === $result){
		    return $this->sql_save_error('print_tpl');
        }
		return true;
	}
}