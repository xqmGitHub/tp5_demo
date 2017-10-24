<?php
namespace app\admin\validate;
use think\Validate;

class Config extends Validate{
    protected $rule = [
        'name'    =>  'require',
        'value'   =>  'require',

    ];

    protected $message = [
        'name.require'  => '请填写配置标识',
        'value.require'  => '请填写配置值',
    ];
}

