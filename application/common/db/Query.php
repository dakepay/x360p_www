<?php

namespace app\common\db;

use think\db\Query as ThinkQuery;

class Query extends ThinkQuery
{
    protected $call_model;      //调用的模型实例
	protected $_where_fields = [];
    protected $_auto_order  = false;
    protected $_auto_page  = false;
    protected $_auto_options = [];

    protected $_cache_options = [];

    protected $_next_prev_fields = [];

    protected $_where_or = [];

    //排除og_id条件约束
    protected $_skip_og_id_condition = false;

    //排除自动bid条件约束
    protected $_skip_bid_condition = false;

    //是否统计
    protected $_with_sum = [];

    public function skipOgId($skip = true){
        $this->_skip_og_id_condition = $skip;
        return $this;
    }

    public function skipBid($skip = true){
        $this->_skip_bid_condition = $skip;
        return $this;
    }

	public function setOptions($options = []){
		$this->options = $options;
		return $this;
	}

	public function withSum($field){
        if(is_array($field)){
            $this->_with_sum = $field;
        }else {
            $this->_with_sum = explode(',',$field);
        }
        return $this;
    }


    public function addWhereOr($field, $op = null, $condition = null){
        $args = func_get_args();
        array_push($this->_where_or,$args);
        return $this;
    }

    public function setNextPrevFields($fields){
        if(is_string($fields)){
            $fields = explode(',',$fields);
        }
        $this->_next_prev_fields = $fields;
        return $this;
    }

	 /**
     * 获取搜索结果默认
     * @param array $input
     * @param array $with relation
     * @param bool $pagenation
     * @return mixed
     */
    public function getSearchResult($input = [], $with=[], $pagenation = true)
    {
        if (is_bool($with)) {
            $pagenation = $with;
            $with = [];
        }

        if (!$pagenation) {
            $ret['list'] = $this->autoWhere($input)->autoOrder($input)->autoWith($input,$with)->select();
        } else {
            $page     = isset($input['page']) ? intval($input['page']) : 1;
            $pagesize = isset($input['pagesize']) ? intval($input['pagesize']) : intval(config('default_pagesize'));

            //如果是distinct字段，计算总数用该字段
            if(!empty($this->options['distinct']) && !empty($this->options['field'][0])) {
                $ret['total'] = $this->autoWhere($input)->cacheOptions()->count('DISTINCT ' . $this->options['field'][0]);
            } else {
                $ret['total'] = $this->autoWhere($input)->cacheOptions()->count();
            }

            if(!empty($this->_with_sum)){
                if($ret['total'] > 0){
                    foreach($this->_with_sum as $f){
                        $ret['total_'.$f] = $this->setCacheOption()->sum($f);
                    }
                }else{
                    foreach($this->_with_sum as $f){
                        $ret['total_'.$f] = 0;
                    }
                }
            }



            //$ret['total_sql'] = $this->getLastSql();
            $ret['list']  = [];

            if($ret['total'] > 0) {
                $ret['list'] = $this->setCacheOption()->autoPage($input)->autoOrder($input)->autoWith($input,$with)->cacheOptions()->select();
                if(!empty($this->_with_sum)) {
                    foreach($this->_with_sum as $f){
                        $ret['page_'.$f] = 0;
                    }
                    foreach ($ret['list'] as $row) {
                        foreach($this->_with_sum as $f){
                            $ret['page_'.$f]+= isset($row[$f])?$row[$f]:0;
                        }
                    }
                }

            }
            //$ret['sql']  = $this->getLastSql();
            $ret['page'] = $page;
            $ret['pagesize'] = $pagesize;
        }
        if(config('app_debug')){
            $ret['sql'] = $this->getLastSql();
        }
        if (!empty($ret['list'])) {
            $ret['list'] = collection($ret['list'])->toArray();
        }

        if(!empty($this->_next_prev_fields) && !empty($ret['list'])){
            //$ret['options'] = $this->getCacheOption();
            $options = $this->getCacheOption();

            $start_page = $options['page'][0];
            $pagesize   = $options['page'][1];

            $limit = [0,1];

            $first = null;
            $last  = null;

            if($page > 1){
                $limit[0] = ($start_page - 1) * $pagesize -1;
                unset($options['page']);
                $first_rows = $this->setQueryOptions($options)->limit(implode(',',$limit))->select();
                if(!empty($first_rows)){
                    $first = $first_rows[0];
                }
            }

            if( $start_page*$pagesize < $ret['total']){
                $limit[0] = $start_page * $pagesize;
                unset($options['page']);
                $last_rows = $this->setQueryOptions($options)->limit(implode(',',$limit))->select();
                if(!empty($last_rows)){
                    $last = $last_rows[0];
                }
            }
           
            $total = count($ret['list']);
            foreach($ret['list'] as $k=>$r){

                $next = null;
                $prev = null;

                if($k == 0){
                    $prev = $this->next_prev_row($first);
                }else{
                    $prev = $this->next_prev_row($ret['list'][$k-1]);
                }

                if($k == $total-1){
                    $next = $this->next_prev_row($last);
                }else{
                    $next = $this->next_prev_row($ret['list'][$k+1]);
                }

                $ret['list'][$k]['_next'] = $next;
                $ret['list'][$k]['_prev'] = $prev;
                
            }

        }

        return $ret;
    }


    protected function next_prev_row($row){
        if(is_null($row)){
            return $row;
        }
        $ret = [];

        foreach($this->_next_prev_fields as $f){
            $ret[$f] = isset($row[$f])?$row[$f]:'';
        }

        return $ret;
    }

    /**
     * 自动with
     * @param  array  $with [description]
     * @return [type]       [description]
     */
    public function autoWith($input = [],$with = []){
        $arr_with = [];
        if(isset($input['with']) && $input['with'] != ''){
            $arr_with = explode(',',$input['with']);
        }
        return $this->with(array_merge($with,$arr_with));
    }

    /**
     * 自动排序
     * @param  [type] &$input [description]
     * @return [type]         [description]
     */
    public function autoOrder($input = [])
    {
        if ($this->_auto_order || isset($this->options['order'])) {
            return $this;
        }
        $table_info = $this->getTableInfo();
        $fields = $table_info['fields'];
        $order_field = isset($input['order_field']) ? $input['order_field'] : 'create_time';
        $order_sort = isset($input['order_sort']) ? $input['order_sort'] : 'desc';
        if (!empty($order_field) && in_array($order_field, $fields)) {
            $order_field = '__TABLE__.' . $order_field;
            $this->order($order_field,$order_sort);
        }
        $this->_auto_order = true;
        return $this;
    }

    /**
     * 自动分页
     * @param  [type] &$input [description]
     * @return [type]         [description]
     */
    public function autoPage($input = [])
    {

        $page     = isset($input['page']) ? intval($input['page']) : 1;
        $pagesize = isset($input['pagesize']) ? intval($input['pagesize']) : config('default_pagesize');
        $this->page($page,$pagesize);
        $this->_auto_page = true;
        return $this;
    }


    /**
     * 自动查询条件
     * @param  [type] &$input [description]
     * @return [type]         [description]
     */
    public function autoWhere($input = [])
    {
        if(!empty($this->_where_fields)){
            foreach($this->_where_fields as $field=>$query){
                if ($query[0] == '[' && $query[strlen($query) - 1] == ']'){
                    $this->parseExpAutoWhere($field,$query);
                } else {
                    $this->parseValueAutoWhere($field,$query);
                }
            }
        }else{
            $table_info   = $this->getTableInfo();  
            $table_fields = $table_info['fields'];
            $field_types  = $table_info['type'];
            $removed_input_bid = false;
           
            if(isset($input['search_field']) && isset($input['search_value'])){
                $input[$input['search_field']] = $input['search_value'];
            }
   
            $this->autoHeaderWhere($input);

            foreach($input as $field=>$query){
                if(in_array($field,$table_fields)){
                    if(is_string($query) || is_numeric($query)){
                        if(substr($query,0,1) == '[' && substr($query,-1) == ']'){
                            $this->parseExpAutoWhere($field,$query);
                        }else{
                            $this->parseValueAutoWhere($field,$query);
                        }
                    }elseif(is_array($query)){
                        $query = '['.implode(',',$query).']';
                        $this->parseExpAutoWhere($field,$query);
                    }
                    $this->_where_fields[$field] = $query;
                }
            }


            if(!empty($this->_where_or)){
                foreach($this->_where_or as $args){
                    call_user_func_array(array($this,'whereOr'),$args);
                }
            }

            if($removed_input_bid){
                $input['bid'] = $input_bid;
            }
            
        }
        return $this;
    }

    /**
     * 自动Header条件
     * @return [type] [description]
     */
    protected function autoHeaderWhere(& $input){
        
    	$table_info   = $this->getTableInfo();  
        $table_fields = $table_info['fields'];
        
    	 //校区ID 自动
        if(isset($input['bid'])){
            if(intval($input['bid']) == -1){
                $input_bid = $input['bid'];
                $removed_input_bid = true;
                unset($input['bid']);
            }
        }else{
            if(!$this->_skip_bid_condition && $this->name != 'Branch' && in_array('bid',$table_fields) && !isset($this->_where_fields['bid'])){
                $bid = request()->header('x-bid');
                if($bid){
                    if(strpos($bid,',') !== false){
                        $bids = explode(',',$bid);
                        $this->whereIn('__TABLE__.bid',$bids);
                        $this->_where_fields['bid'] = "[in, $bid]";
                    }else{
                        $bid = intval($bid);
                        if($bid !== -1){
                            $this->where('__TABLE__.bid',$bid);
                        }

                        $this->_where_fields['bid'] = $bid;
                    }
                }
            }
            //如果表中有bids字段，则使用find_in_set()语句查询
            if(in_array('bids', $table_fields) && !isset($this->_where_fields['bids'])) {
                $bid = isset($input['bid']) ? $input['bid'] : request()->header('x-bid');
                if($bid && $bid !== -1){
                    if(strpos($bid,',') !== false){
                        $bids = array_filter(explode(',',$bid));
                        if(!empty($bids)) {
                            $where = array_reduce($bids, function($where, $val){
                                $where[] = "find_in_set($val, bids)";
                                return $where;
                            });
                            $where = implode(' or ', $where);
                            $this->where($where);
                        }

                    }else{
                        $bid = intval($bid);
                        if($bid !== -1){
                            $this->where("find_in_set($bid, bids)");
                        }

                    }
                    $this->_where_fields['bids'] = $bid;
                }
            }
        }

        //机构ID 自动
        if(isset($input['og_id'])){
            if(intval($input['og_id']) == -1){
                $input_og_id = $input['og_id'];
                $removed_input_og_id = true;
                unset($input['og_id']);
            }
        }else{
            $this->autoOgidWhere();
        }

        return $this;    
    }

    /**
     * @param $field
     * @param $query
     * @return $this|mixed
     * @usage:/api/goods?field=value
     * @usage:/api/goods?lid=5
     * @usage:/api/goods?field=[logic,op,condition],logic:['OR','XOR'], op:['Null','NotNull','Exists','NotExists','In','NotIn','Like','NotLike','Between','NotBetween','Exp','Time'];
     * @usage:/api/goods?gid=[in,1,2,3]
     * @usage:/api/goods?gid=[in,1,3]&lid=[OR,in,5,6,7]
     * @usage:/api/goods?gid=[in,1,3]&lid=[OR,in,5,6,7]
     * @usage:/api/goods?gid=[in,1,3]&lid=[OR,in,5,6,7]&title=[like,%title%]
     * @usage:/api/goods?gid=[>,0]&gid=[OR,<,10]
     */
    protected function parseExpAutoWhere($field, $query)
    {
       $query_string = trim($query, '[]');
       $args        = explode(',',$query_string);

       if(count($args) == 0){
            return $this->parseValueAutoWhere($field,$query_string);
       }
       $logics      = ['OR','XOR'];
       $quick_ops   = ['Null','NotNull','Exists','NotExists','In','NotIn','Like','NotLike','Between','NotBetween','Exp','Time'];
       $quick_ops_lower = array_map('strtolower', $quick_ops);

       $whereSurfix = '';

       if(in_array($args[0],$logics)){
           $logic = $args[0];
           array_shift($args);
           $whereSurfix = ucfirst(strtolower($logic));
       }else{
           $logic = 'AND';
       }

       $funcArgs['logic'] = $logic;
       $funcArgs['field'] = $field;

       if(in_array(strtolower($args[0]), $quick_ops_lower)){
           $whereSurfix = ucfirst(strtolower($args[0]));
           array_shift($args);

           if(substr($field,-5) == '_time' && ($whereSurfix == 'Between' or $whereSurfix == 'NotBetWeen')){
               if(is_date_format($args[0])){
                   $args[0] = str_to_time($args[0]);
               }
               if(is_date_format($args[1])){
                   $args[1] = str_to_time($args[1],true);
               }
           }

           if(substr($field,-4) == '_day' && ($whereSurfix == 'Between' or $whereSurfix == 'NotBetWeen')){
               if(isset($args[0])) $args[0] = format_int_day($args[0]);
               if(isset($args[1])) $args[1] = format_int_day($args[1]);
           }
       }

       if(in_array($whereSurfix, ['In','NotIn','Between','NotBetween'])){
            $funcArgs['condition'] = $args;
            $funcArgs['op'] = null;
       }else{
            $args_length = count($args);
            if($args_length == 0){
                $funcArgs['op'] = null;
                $funcArgs['condition'] = null;
            }elseif($args_length == 1){

                if($whereSurfix == 'Time'){
                    $funcArgs['op'] = $args[0];
                    $funcArgs['range'] = null;
                }else{
                    $funcArgs['op'] = null;
                    $funcArgs['condition'] = $args[0];
                }
                
            }else{
                $funcArgs['op'] = $args[0];
                 if($whereSurfix == 'Time'){
                    $funcArgs['range'] = $args[1];
                 }else{
                    $funcArgs['condition'] = $args[1];
                 }
                
            }
       }
       $queryObj = $this->getQuery();
       $whereFunc = 'where'.$whereSurfix;
       $reflec = new \ReflectionMethod($queryObj, $whereFunc);
       $func_params = $reflec->getParameters();
       $call_args = [];

       foreach($func_params as $p){
            if(isset($funcArgs[$p->name])){
                array_push($call_args,$funcArgs[$p->name]);
            }else{
                array_push($call_args,null);
            }
       }
       return call_user_func_array(array($this,$whereFunc),$call_args);
    }

    /**
     * 定义Query对象
     * @param  [type] $fn [description]
     * @return [type]     [description]
     */
    public function defineQuery($fn){
        if ($fn instanceof \Closure) {
            $query = $this->getQuery();
            call_user_func_array($fn, [& $query]);
        }
        return $this;
    }

    /**
     * 缓存设置项
     * @param  [type] $field [description]
     * @return [type]        [description]
     */
    protected function cacheOptions(){
        $query = $this->getQuery();
        $this->_cache_options = $query->getOptions();
        return $this;
    }

    /**
     * 设置缓存查询条件
     * @return [type] [description]
     */
    protected function setCacheOption(){
        $query = $this->getQuery();
        $option = $query->_cache_options;
        $query->setOptions($option);
        return $this;
    }


    protected function setQueryOptions($options){
        $query = $this->getQuery();
        $query->setOptions($options);
        return $this;
    }

    /**
     * 获得缓存查询条件
     * @return [type] [description]
     */
    protected function getCacheOption(){
        return $this->_cache_options;
    }

    protected function parseValueAutoWhere($field,$value)
    {
        $table_info = $this->getTableInfo();
        $field_type = $table_info['type'][$field];

        if(strpos($field_type,'char') !== false or strpos($field_type,'text') !== false){
            return $this->whereLike($field,'%'.$value.'%');
        }elseif(strpos($field_type,'int') !== false && strpos($value,',') !== false){
            return $this->where($field,explode(',',$value));
        }
        return $this->where($field,$value);
    }

    protected function getQuery(){
    	return $this;
    }

    /**
     * 条件里面是否含有og_id
     * @return boolean [description]
     */
    protected function hasOgidInWhere(){
        $options = $this->options;
        if(!isset($options['where'])){
            return false;
        }
        $where_str = strtolower(json_encode($options['where']));
        return strpos($where_str,'og_id') !== false;
    }

    /**
     * 自动增加og_id隔离条件
     * @return [type] [description]
     */
    protected function autoOgidWhere(){
        if($this->_skip_og_id_condition){
            return $this;
        }
        $common_tables = ['dictionary'];
        $name = strtolower($this->name);
        $table_info   = $this->getTableInfo();  
        $table_fields = $table_info['fields'];
        if($name != 'org' 
            && in_array('og_id',$table_fields) 
            && !isset($this->_where_fields['og_id'])
            && !$this->hasOgidInWhere()
        ){
            $og_id = request()->header('x-ogid');
            $og_id = $og_id ? $og_id :  gvar('og_id');
            $og_id = intval($og_id);

            if($og_id !== -1){
                if(in_array($name,$common_tables)){
                    $this->where('__TABLE__.og_id','IN',[0,$og_id]);
                }else{
                    $this->where('__TABLE__.og_id', $og_id);
                }
            }elseif($og_id == 0){
                if(in_array($name,$common_tables)){
                    $this->where('__TABLE__.og_id',0);
                }
            }

            $this->_where_fields['og_id'] = $og_id;
            
        }
        return $this;
    }

    /**
     * 查找记录
     * 在Select的时候自动的加入过滤og_id的条件
     * @access public
     * @param array|string|Query|\Closure $data
     * @return Collection|false|\PDOStatement|string
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws DataNotFoundException
     */
    public function select($data = null)
    {
        if ($data instanceof Query) {
            return $data->select();
        } elseif ($data instanceof \Closure) {
            call_user_func_array($data, [ & $this]);
            $data = null;
        }
        //自动og_id隔离
        $this->autoOgidWhere();
        // 分析查询表达式
        $options = $this->parseExpress();

      

        if (false === $data) {
            // 用于子查询 不查询只返回SQL
            $options['fetch_sql'] = true;
        } elseif (!is_null($data)) {
            // 主键条件分析
            $this->parsePkWhere($data, $options);
        }

        $resultSet = false;
        if (empty($options['fetch_sql']) && !empty($options['cache'])) {
            // 判断查询缓存
            $cache = $options['cache'];
            unset($options['cache']);
            $key       = is_string($cache['key']) ? $cache['key'] : md5(serialize($options) . serialize($this->bind));
            $client = gvar('client');
            if($client){
                $key = md5($client['cid'].'-'.$key);
            }
            $resultSet = cache($key);
        }
        if (false === $resultSet) {
            // 生成查询SQL
            $sql = $this->builder->select($options);
            // 获取参数绑定
            $bind = $this->getBind();
            if ($options['fetch_sql']) {
                // 获取实际执行的SQL语句
                return $this->connection->getRealSql($sql, $bind);
            }

            $options['data'] = $data;
            if ($resultSet = $this->trigger('before_select', $options)) {
            } else {
                // 执行查询操作
                $resultSet = $this->query($sql, $bind, $options['master'], $options['fetch_pdo']);

                if ($resultSet instanceof \PDOStatement) {
                    // 返回PDOStatement对象
                    return $resultSet;
                }
            }

            if (isset($cache) && false !== $resultSet) {
                // 缓存数据集
                $this->cacheData($key, $resultSet, $cache);
            }
        }

        // 数据列表读取后的处理
        if (!empty($this->model)) {
            // 生成模型对象
            $modelName = $this->model;
            if (count($resultSet) > 0) {
                foreach ($resultSet as $key => $result) {
                    /** @var Model $model */
                    $model = new $modelName($result);
                    $model->isUpdate(true);
                    $model->append($this->call_model->getAppend(),true);

                    // 关联查询
                    if (!empty($options['relation'])) {
                        $model->relationQuery($options['relation']);
                    }
                    // 关联统计
                    if (!empty($options['with_count'])) {
                        $model->relationCount($model, $options['with_count']);
                    }
                    $resultSet[$key] = $model;
                }
                if (!empty($options['with'])) {
                    // 预载入
                    $model->eagerlyResultSet($resultSet, $options['with']);
                }
                // 模型数据集转换
                $resultSet = $model->toCollection($resultSet);
            } else {
                $resultSet = (new $modelName)->toCollection($resultSet);
            }
        } elseif ('collection' == $this->connection->getConfig('resultset_type')) {
            // 返回Collection对象
            $resultSet = new Collection($resultSet);
        }
        // 返回结果处理
        if (!empty($options['fail']) && count($resultSet) == 0) {
            $this->throwNotFound($options);
        }
        return $resultSet;
    }

    /**
     * todo:hack og_id
     * @return array
     */
    protected function parseExpress()
    {
        $options = parent::parseExpress();
        return $options;
    }


    public function setCallModel($m){
        $this->call_model = $m;
        return $this;
    }
}