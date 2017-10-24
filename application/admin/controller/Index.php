<?php
namespace app\admin\controller;
use think\Session;

class Index extends Auth
{
    public function index()
    {
        $admin_user = Session::get('admin_user');
        $this->assign('user_name',$admin_user['nick_name']);
        return $this->fetch();
    }

    public function welcome(){
        $admin_user = Session::get('admin_user');
        $this->assign('login_num',$admin_user['login_num']);
        $this->assign('last_login_time',$admin_user['last_login_time']);
        $this->assign('last_login_ip',$admin_user['last_login_ip']);
        return $this->fetch();
    }


}