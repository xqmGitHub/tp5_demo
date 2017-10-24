<?php
namespace app\home\controller;
use think\Controller;
use app\admin\model\Config;
use app\common\controller\Base as BaseCommon;

class Base extends Controller
{
    protected $redis;
    public function _initialize()
    {
        $web = $this->getWebSiteStatus();
        if($web['webStatus'] == 0) {
            $str = '<div style="text-align:center"><b>' . $web['webCloseTip'] . '</b></div>';
            die($str);
        }
        $baseCommon = new BaseCommon();
        $this->redis = $baseCommon->redis;
    }

    private function getWebSiteStatus()
    {
        $model = new Config();
        $result = $model->where('name', '=', 'WEBSITE_BASECON')->find();
        $val = json_decode($result['value']);
        $web_status = $val->WEBSITE_CLOSE;
        $web_close_tip = $val->WEBSITE_CLOSE_TIP;
        return array('webStatus' => $web_status, 'webCloseTip' => $web_close_tip);
    }
}