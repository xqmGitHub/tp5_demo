<?php
namespace app\common\controller;
use think\Controller;

class Base extends Controller{
    public $redis;
    public function _initialize()
    {
        try {
            $redisConf = config('REDIS_CONF');
            if (empty($redisConf)) exit('No redis config');
            $this->redis = new \Redis();
            $this->redis->connect($redisConf['host'],$redisConf['port']);
            $this->redis->auth($redisConf['password']);
            $this->redis->select($redisConf['select']);
        }catch (\Exception $e){
            exit('Redis The service is not enabled !');
        }
    }
}