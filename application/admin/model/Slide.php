<?php
namespace app\admin\model;
use think\Model;

class Slide extends Model{
    public function slideGroup()
    {
        return $this->hasOne('slideGroup', 'group', 'id');
    }
}