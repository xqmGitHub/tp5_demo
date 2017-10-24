<?php
namespace app\admin\validate;
use think\Validate;

class Login extends Validate{
    protected $rule = [
        'captcha'    =>  'require|captcha',
        'username'   =>  'require',
        'password'   =>  'require',

    ];

    protected $message = [
        'username.require'  => '请填写登录账号',
        'password.require'  => '请填写登录密码',
        'captcha.require'   => '请填写验证码',
        'captcha.captcha'   => '验证码不正确',
    ];
}

