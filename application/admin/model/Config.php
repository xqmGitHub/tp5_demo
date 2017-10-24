<?php
namespace app\admin\model;
use think\Model;

class Config extends Model{
    public function getGroupAttr($value){
        $group = ['0'=>'不分组','1'=>'基本','2'=>'内容','3'=>'用户','4'=>'系统','5'=>'网站基本',];
        return $group[$value];
    }
}