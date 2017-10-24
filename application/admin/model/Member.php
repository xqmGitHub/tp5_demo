<?php
namespace app\admin\model;
use think\Model;

class Member extends Model{

    public function getLastLoginIpAttr($value)
    {
        return long2ip($value);
    }
    public function getLastLoginTimeAttr($value)
    {
        return empty($value)? '暂无': date('Y-m-d H:i:s',$value);
    }
}