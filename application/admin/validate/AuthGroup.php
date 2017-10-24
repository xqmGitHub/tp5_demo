<?php
namespace app\admin\validate;
use think\Validate;

class AuthGroup extends Validate{
    protected $rule = [
        'title'    =>  'require',
    ];

    protected $message = [
        'title.require'  => '请填写用户组名称',
    ];
}

