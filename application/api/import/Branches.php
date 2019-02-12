<?php
namespace app\api\import;

use app\common\Import;

class Branches extends Import{
	protected $res = 'branch';
	protected $start_row_index = 3;
	protected $pagesize = 20;
	protected $area_map = null;
	protected $area_list = [];
	protected $cache_province_id = [];
	protected $cache_city_id = [];
	protected $cache_district_id = [];



	protected $fields = [
		['field'=>'branch_name','name'=>'校区名','must'=>true],
		['field'=>'short_name','name'=>'校区简称','must'=>true],
		['field'=>'branch_type','name'=>'校区类型','must'=>true],
		['field'=>'branch_tel','name'=>'校区电话'],
		['field'=>'province_id','name'=>'所在省'],
		['field'=>'city_id','name'=>'所在市'],
		['field'=>'district_id','name'=>'所在区县'],
		['field'=>'address','name'=>'详细地址']
	];	

	public function __init(){
		$this->init_area_map();
	}

	protected function init_area_map(){
		if(is_null($this->area_map)){
			$area_list = db('area')->select();
			$this->area_list = $area_list;
		}
	}


	protected function get_province_id($province){
		$province_id = 0;
		if(isset($this->cache_province_id[$province])){
			return $this->cache_province_id[$province];
		}
		foreach($this->area_list as $area){
			if($area['level'] == 1 && strpos($area['name'],$province) !== false){
				$province_id = $area['area_id'];
				$this->cache_province_id[$province] = $area['area_id'];
				break;
			}
		}
		return $province_id;
	}

	protected function get_city_id($city,$province_id){
		$city_id = 0;
		if(isset($this->cache_city_id[$city])){
			return $this->cache_city_id[$city];
		}
		foreach($this->area_list as $area){
			if($area['level'] == 2 && $area['parent_id'] == $province_id && strpos($area['name'],$city) !== false){
				$city_id = $area['area_id'];
				$this->cache_city_id[$city] = $area['area_id'];
				break;
			}
		}
		return $city_id;
	}


	protected function get_district_id($district,$province_id,$city_id){
		$district_id = 0;
		if(isset($this->cache_district_id[$district])){
			return $this->cache_district_id[$district];
		}
		foreach($this->area_list as $area){
			if($area['level'] == 3 && $area['parent_id'] == $city_id && strpos($area['name'],$district) !== false){
				$district_id = $area['area_id'];
				$this->cache_district_id[$district] = $area['area_id'];
				break;
			}
		}
		return $district_id;
	}


	/**
	 * 添加数据到数据库
	 * @param [type] $data   [description]
	 * @param [type] $row_no [description]
	 * @return  0 成功
	 * @return  2 失败
	 * @return  1 重复
	 */
	protected function add_data($data,$row_no){
		
		$w['branch_name'] = $data['branch_name'];
		

		$exists_branch = m('branch')->where($w)->find();


		if($exists_branch){
			$w_branch['bid'] = $exists_branch['bid'];

			$update_branch = [];

			$update_fields = ['short_name','address','branch_type','province_id','city_id','district_id','area_id'];

			foreach($update_fields as $f){
				if(!empty($data[$f]) && $data[$f] != $exists_branch[$f]){
					$update_branch[$f] = $data[$f];
				}
			}

			if(!empty($update_branch)){
				$result = $exists_branch->save($update_branch);
				if(false === $result){
					$this->import_log[] = '第'.$row_no.'行的校区资料有更新，但是更新失败,SQL:'.m('branch')->getLastSql().print_r($update_branch,true);
				}else{
					$this->import_log[] = '第'.$row_no.'行的校区资料有更新，更新成功!';
				}
				return 1;
			}
			$this->import_log[] = '第'.$row_no.'行的数据有重复!';
			return 1;
		}

		$bid = m('branch')->data($data)->isUpdate(false)->save();

		if(!$bid){
			$this->import_log[] = '第'.$row_no.'行的数据写入数据库失败:'.m('branch')->getError();
			return 2;
		}

		return 0;

	}

	public function convert_province_id($province,&$add,&$row){
		$province_id = $this->get_province_id($province);
		if($province_id > 0){
			$add['area_id'] = $province_id;
		}
		return $province_id;
	}


	public function convert_city_id($city,&$add,&$row){
		$city_id = $this->get_city_id($city,$add['province_id']);
		if($city_id > 0){
			$add['area_id'] = $city_id;
		}
		return $city_id;
	}

	public function convert_district_id($district,&$add,&$row){
		$district_id = $this->get_district_id($district,$add['province_id'],$add['city_id']);
		if($district_id > 0){
			$add['area_id'] = $district_id;
		}
		return $district_id;
	}



	public function convert_branch_type($branch_type,&$add,&$row){
		$type_map = ['直营'=>1,'加盟'=>2];

		if(isset($type_map[$branch_type])){
			return $type_map[$branch_type];
		}

		return $branch_type;
	}



	protected function get_fields(){
		return $this->fields;
	}


	protected function import_row(&$row,$row_no){

        $fields = $this->get_fields();

        $add = [];

        foreach($fields as $index=>$f){
            $field = $f['field'];
            $cell = $row[$index];
            if(is_object($cell)){
                $value = $cell->getPlainText();
            }else{
                $value = $cell;
            }

            $func = 'convert_'.$field;
           
            if(empty($value)){
                if(isset($f['must']) && $f['must'] === true){
                    $this->import_log[] = '第'.$row_no.'行的['.$f['name'].']没有填写!';
                    return 2;
                }
            }else{
                $add[$field] = trim($value);
                if(method_exists($this, $func)){  
                    $add[$field] = $this->$func($value,$add,$row);
                }
            }
        }
        return $this->add_data($add,$row_no);
    }
}