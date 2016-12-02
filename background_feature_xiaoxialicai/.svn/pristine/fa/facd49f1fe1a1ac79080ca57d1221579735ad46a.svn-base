<?php

namespace Lib\Redis;

class XRedis {

    private $redis;
    private $host = '127.0.0.1';
    private $port = 6379;

    /**
     * @param string $host 链接ip地址
     * @param int $post 端口
     */
    public function __construct($host, $port) {
        $this->setConfig($host, $port);
        $this->getConnect();
    }

    /**
     * 连接
     * @param string $host 链接ip地址
     * @param int $port 端口
     */
    public function getConnect() {
        $this->redis = new \Redis();
        $this->redis->connect($this->host, $this->port);
        return $this;
    }
    
    /**
     * 设置配置
     * @param string $host 链接ip地址
     * @param int $port 端口
     */
    public function setConfig($host, $port) {
        $host && $this->host = $host;
        $port && $this->port = $port;
        return $this;
    }

    /**
     * 设置值  构建一个字符串
     * @param string $key KEY名称
     * @param string $value  设置值
     * @param int $timeOut 时间  0表示无过期时间
     */
    public function set($key, $value, $timeOut = 0) {
        $retRes = $this->redis->set($key, $value);
        if ($timeOut > 0)
            $this->redis->expire($key, $timeOut);
        return $retRes;
    }

    /*
     * 构建一个集合(无序集合)
     * @param string $key 集合Y名称
     * @param string|array $value  值
     */

    public function sadd($key, $value) {
        return $this->redis->sadd($key, $value);
    }

    /*
     * 构建一个集合(有序集合)
     * @param string $key 集合名称
     * @param string|array $value  值
     */

    public function zadd($key, $value) {
        return $this->redis->zadd($key, $value);
    }

    /**
     * 构建一个列表(先进后去，类似栈)
     * @param sting $key KEY名称
     * @param string $value 值
     */
    public function lpush($key, $value) {
        echo "$key - $value \n";
        return $this->redis->LPUSH($key, $value);
    }

    /**
     * 构建一个列表(先进先去，类似队列)
     * @param sting $key KEY名称
     * @param string $value 值
     */
    public function rpush($key, $value) {
        echo "$key - $value \n";
        return $this->redis->rpush($key, $value);
    }

    /**
     * HASH类型
     * @param string $tableName  表名字key
     * @param string $key            字段名字
     * @param sting $value          值
     */
    public function hset($tableName, $field, $value) {
        return $this->redis->hset($tableName, $field, $value);
    }

    public function hget($tableName, $field) {
        return $this->redis->hget($tableName, $field);
    }

    /**
     * 设置多个值
     * @param array $keyArray KEY名称
     * @param string|array $value 获取得到的数据
     * @param int $timeOut 时间
     */
    public function sets($keyArray, $timeout) {
        if (is_array($keyArray)) {
            $retRes = $this->redis->mset($keyArray);
            if ($timeout > 0) {
                foreach ($keyArray as $key => $value) {
                    $this->redis->expire($key, $timeout);
                }
            }
            return $retRes;
        } else {
            return "Call  " . __FUNCTION__ . " method  parameter  Error !";
        }
    }

    /**
     * 通过key获取数据
     * @param string $key KEY名称
     */
    public function get($key) {
        $result = $this->redis->get($key);
        return $result;
    }

    /**
     * 删除一条数据key
     * @param string $key 删除KEY的名称
     */
    public function del($key) {
        return $this->redis->delete($key);
    }

    /**
     * 数据自增
     * @param string $key KEY名称
     */
    public function increment($key) {
        return $this->redis->incr($key);
    }

    /**
     * 数据自减
     * @param string $key KEY名称
     */
    public function decrement($key) {
        return $this->redis->decr($key);
    }   

    /**
     * 清空数据
     */
    public function flushAll() {
        return $this->redis->flushAll();
    }

    /**
     * 返回redis对象
     */
    public function getRedisObj() {
        return $this->redis;
    }

}
