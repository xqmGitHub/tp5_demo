<?php
namespace app\admin\model;

use think\Model;

class AuthGroupAccess extends Model{
    public function authGroup()
    {
        return $this->hasOne('authGroup', 'id', 'group_id');
    }
}