<?php
namespace app\admin\validate;
use think\Validate;

class Member extends Validate{
    protected $rule = [
        'username'    =>  'require|max:25|unique:member',
        'password'    =>  'require|min:6',
        'repassword' => 'require|confirm:password',
        'email'    =>  'require|email',
    ];

    protected $message = [
        'username.require'  => '请填写用户名',
        'username.max'        => '用户名不能超过25个字符',
        'username.unique'     => '用户名已存在',
        'password.require'  => '请填写密码',
        'password.min'        => '密码位数不得少于6位',
        'repassword.require'  => '请填写确认密码',
        'repassword.confirm'  => '两次输入的密码不相同',
        'email.require'  => '请填写邮箱',
        'email'               => '邮箱格式不正确',
    ];

    protected $scene = [
        'edit'  =>  ['name','email','password'=>'min:6'],
   ];

}

