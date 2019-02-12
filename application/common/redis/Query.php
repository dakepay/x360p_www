<?php

namespace app\common\redis;

use think\cache\driver\Redis;

class Query extends Redis
{

    private $prefix = 'x360p_';
    private $expire = 300;

    public function __construct(array $options = [])
    {
        if(!isset($options['prefix'])) {
            $options['prefix'] = !empty(gvar('client.domain'))
                ? $this->prefix . gvar('client.domain') . '_'
                : $this->prefix;
        }
        if(!isset($options['expire'])) {
            $options['expire'] = $this->expire;
        }
        parent::__construct($options);
    }

    public function hSet($key, $field, $value, $expire = 300)
    {
        $key = $this->getCacheKey($key);
        $this->handler->hSet($key, $field, $value);
        return $this->handler->expire($key, $expire);
    }

    public function hGet($key, $field)
    {
        $key = $this->getCacheKey($key);
        return $this->handler->hGet($key, $field);
    }

    //获取所有hash
    public function hGetAll($key) {
        $key = $this->getCacheKey($key);
        return $this->handler->hGetAll($key);
    }

    public function hExists($key, $field) {
        $key = $this->getCacheKey($key);
        return $this->handler->hExists($key, $field);
    }

    public function hDel($key, $field) {
        $key = $this->getCacheKey($key);
        return $this->handler->hDel($key, $field);
    }

}