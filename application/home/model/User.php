<?php
namespace app\home\model;
use think\Model;

class User extends Model{
    public function UserInfo(){
        return $this->hasOne('UserInfo','uid','id');
    }
}