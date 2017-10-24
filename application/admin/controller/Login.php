<?php
namespace app\admin\controller;
use app\admin\model\Member;
use think\Request;
use think\Session;

class Login extends Base
{
    public function login()
    {
        return $this->fetch();
    }

    public function login_on()
    {
        //接收数据
        $post_data = Request::instance()->post();
        $result = $this->validate($post_data, 'Login');
        if ($result !== true) {
            return (["state" => "error", "msg" => $result]);
        }
        $model = new Member();
        $member = $model->where(['username' => $post_data['username'], 'status' => 1])
            ->find();
        if (!$member) {
            return (["status" => "-1", "msg" => '用户不存在']);
        }
        if (think_ucenter_md5($post_data['password'], PWD_KEY) !== $member['password']) {
            return (["status" => "-2", "msg" => '密码错误']);
        } else {
            $this->login_ok($member);
            return (["status" => "1", "msg" => '登陆成功', "url" => url('/admin/index')]);
        }
    }

    public function login_ok($member){
        $model = new Member();
        $data = [
            'id'=>$member['id'],
            'login_num'=>['exp','`login_num`+1'],
            'last_login_time' => time(),
            'last_login_ip' => get_client_ip(1),
        ];
        $result = $model->allowField(true)->isUpdate(true)->save($data, ['id' => $data['id']]);
        if($result){
            $auth = array(
                'uid'             => $member['id'],
                'nick_name'        => $member['nick_name'],
                'login_num'        => $member['login_num'],
                'last_login_time' => $member['last_login_time'],
                'last_login_ip' => $member['last_login_ip'],
            );
            Session::set('admin_user', $auth);
        }
    }

    public function logOut(){
        Session::delete('admin_user');
        $this->redirect(url('login','',false));
    }

}