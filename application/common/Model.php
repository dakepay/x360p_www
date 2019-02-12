<?php
namespace app\common;

use think\Config;
use think\Db;
use think\Model as ThinkModel;
use think\Request;
use think\Exception;
use think\Log;
use traits\model\SoftDelete;
use app\common\traits\ModelTrait;

class Model extends ThinkModel
{
    use SoftDelete;
    use ModelTrait;

    protected $query = 'app\\common\\db\\Query';
    protected $autoWriteTimestamp = true;
    protected $deleteTime = 'delete_time';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $soft_delete = true;     //不用软删除


    /**
     * 创建模型的查询对象
     * @access protected
     * @return Query
     */
    protected function buildQuery()
    {
        // 合并数据库配置
        if (!empty($this->connection)) {
            if (is_array($this->connection)) {
                $connection = array_merge(config('database'), $this->connection);
            } else {
                $connection = $this->connection;
            }
        } else {
            $connection = [];
        }


        $con = Db::connect($connection);
        // 设置当前模型 确保查询返回模型对象
        $queryClass = $this->query ?: $con->getConfig('query');
        $query      = new $queryClass($con, $this->class);

        // 设置当前数据表和模型名
        if (!empty($this->table)) {
            $query->setTable($this->table);
        } else {
            $query->name($this->name);
        }

        if (!empty($this->pk)) {
            $query->pk($this->pk);
        }

        $query->setCallModel($this);

        return $query;
    }

    /**
     * 设置数据库连接
     * @param array $connection
     */
    public function setConnection($connection = []){
        $this->connection = $connection;
        $this->getQuery()->connect($connection);
        return $this;
    }


    /**
     * 删除当前的记录
     * @access public
     * @param bool  $force 是否强制删除
     * @return integer
     */
    public function delete($force = false)
    {
        if (false === $this->trigger('before_delete', $this)) {
            return false;
        }
        $name = $this->getDeleteTimeField();
        if (!$force) {
            // 软删除
            $this->data[$name] = $this->autoWriteTimestamp($name);
            $this->isUpdate    = true;
            $result            = $this->save();
        } else {
            $result = $this->getQuery()->delete($this->data);
        }

        // 关联删除
        if (!empty($this->relationWrite)) {
            foreach ($this->relationWrite as $key => $name) {
                $name  = is_numeric($key) ? $name : $key;
                $model = $this->getAttr($name);
                if ($model instanceof Model) {
                    $model->delete($force);
                }
            }
        }

        $this->trigger('after_delete', $this);
        // 清空原始数据
        $this->origin = [];
        return $result;
    }


}