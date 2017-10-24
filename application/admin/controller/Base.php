<?php
namespace app\admin\controller;
use think\Controller;
use app\common\controller\Base as BaseCommon;

class Base extends Controller {
    protected $param_data;  // 接收post 或 get 传值
    const SAVE_INSERT = 'insert';
    const SAVE_UPDATE = 'update';
    protected $redis;
    protected function _initialize()
    {
        $this->param_data  = $this->request->param();
        $baseCommon = new BaseCommon();
        $this->redis = $baseCommon->redis;
    }
}