<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

namespace app\common\queue\connector;

use think\Db;
use think\queue\Connector;
use app\common\queue\job\Database as DatabaseJob;

class Database extends Connector
{
    protected $db;

    protected $options = [
        'expire'  => 60,
        'default' => 'default',
        'table'   => 'jobs',
        'dsn'     => []
    ];

    public function __construct($options)
    {
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }

        $this->db = Db::connect($this->options['dsn']);
    }

    public function push($job, $data = '', $queue = null,$task_id = null)
    {
        return $this->pushToDatabase(0, $queue, $this->createPayload($job, $data),0,$task_id);
    }

    public function later($delay, $job, $data = '', $queue = null,$task_id = null)
    {
        return $this->pushToDatabase($delay, $queue, $this->createPayload($job, $data),0,$task_id);
    }

    public function pop($queue = null)
    {
        $queue = $this->getQueue($queue);

        if (!is_null($this->options['expire'])) {
            $this->releaseJobsThatHaveBeenReservedTooLong($queue);
        }

        if ($job = $this->getNextAvailableJob($queue)) {
            $this->markJobAsReserved($job->id);

            $this->db->commit();

            return new DatabaseJob($this, $job, $queue);
        }

        $this->db->commit();
    }

    /**
     * 重新发布任务
     * @param  string    $queue
     * @param  \StdClass $job
     * @param  int       $delay
     * @param string $task_id
     * @return mixed
     */
    public function release($queue, $job, $delay,$task_id = null)
    {
        return $this->pushToDatabase($delay, $queue, $job->payload, $job->attempts,$task_id);
    }

    /**
     * Push a raw payload to the database with a given delay.
     *
     * @param  \DateTime|int $delay
     * @param  string|null   $queue
     * @param  string        $payload
     * @param  int           $attempts
     * @param string $task_id
     * @return mixed
     */
    protected function pushToDatabase($delay, $queue, $payload, $attempts = 0,$task_id = null)
    {
        $job_data = [
            'queue'        => $this->getQueue($queue),
            'payload'      => $payload,
            'attempts'     => $attempts,
            'reserved'     => 0,
            'reserved_at'  => null,
            'available_at' => time() + $delay,
            'created_at'   => time()
        ];
        if(!is_null($task_id)){
            $job_data['task_id'] = strval($task_id);
        }
        return $this->db->name($this->options['table'])->insert($job_data);
    }

    /**
     * 获取下个有效任务
     *
     * @param  string|null $queue
     * @return \StdClass|null
     */
    protected function getNextAvailableJob($queue)
    {
        $this->db->startTrans();

        $job = $this->db->name($this->options['table'])
            ->lock(true)
            ->where('queue', $this->getQueue($queue))
            ->where('reserved', 0)
            ->where('available_at', '<=', time())
            ->order('id', 'asc')
            ->find();

        return $job ? (object) $job : null;
    }

    /**
     * 标记任务正在执行.
     *
     * @param  string $id
     * @return void
     */
    protected function markJobAsReserved($id)
    {
        $this->db->name($this->options['table'])->where('id', $id)->update([
            'reserved'    => 1,
            'reserved_at' => time()
        ]);
    }

    /**
     * 重新发布超时的任务
     *
     * @param  string $queue
     * @return void
     */
    protected function releaseJobsThatHaveBeenReservedTooLong($queue)
    {
        $expired = time() - $this->options['expire'];

        $this->db->name($this->options['table'])
            ->where('queue', $this->getQueue($queue))
            ->where('reserved', 1)
            ->where('reserved_at', '<=', $expired)
            ->update([
                'reserved'    => 0,
                'reserved_at' => null,
                'attempts'    => ['exp', 'attempts + 1']
            ]);
    }

    /**
     * 根据任务ID删除任务
     * @param $task_id
     * @return bool|int
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function deleteByTaskId($task_id){
        if(empty($task_id)){
            return false;
        }
        return $this->db->name($this->options['table'])->where('task_id',$task_id)->where('reserved',0)->delete();
    }

    /**
     * 删除任务
     * @param  string $id
     * @return void
     */
    public function deleteReserved($id)
    {
        $this->db->name($this->options['table'])->delete($id);
    }

    protected function getQueue($queue)
    {
        return $queue ?: $this->options['default'];
    }
}
