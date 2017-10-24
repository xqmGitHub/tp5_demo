<?php
namespace app\admin\validate;
use think\Validate;

class AuthRule extends Validate{
    protected $rule = [
        'title'    =>  'require',
        'name'       => 'require',
    ];

    protected $message = [
        'title.require'  => '请填写节点标题',
        'name.require'  => '请填写节点链接',
    ];
}

