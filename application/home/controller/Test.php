<?php
namespace app\home\controller;

use think\Controller;

class Test extends Controller {
    protected  $redis;
    public function _initialize()
    {
        try {
            $redisConf = config('REDIS_CONF');
            if (empty($redisConf)) exit('No redis config');
            $this->redis = new \Redis();
            $this->redis->connect($redisConf['host'],$redisConf['port']);
            $this->redis->auth($redisConf['password']);
            $this->redis->select(1);
        }catch (\Exception $e){
            exit('Redis The service is not enabled !');
        }
    }

    //测试redis
    public function index(){
        $this->redis->zAdd('k1', 0, 'val0');
         $this->redis->zAdd('k1', 1, 'val1');
         $this->redis->zAdd('k1', 3, 'val3');
        
         $this->redis->zAdd('k2', 2, 'val1');
         $this->redis->zAdd('k2', 3, 'val3');
        
         $inter = $this->redis->zInter('k_inert', array('k1', 'k2'));               // 2, 'ko1' => array('val1', 'val3')
         $union = $this->redis->zUnion('k_union', array('k1', 'k2'));               // 3, 'ko1' => array('val0','val1', 'val3')
        dump($inter);
        dump($union);
         /*$this->redis->zInter('ko2', array('k1', 'k2'), array(1, 1));  // 2, 'ko2' => array('val1', 'val3')*/
    }
}