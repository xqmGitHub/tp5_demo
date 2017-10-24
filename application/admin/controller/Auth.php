<?php
namespace app\admin\controller;
use app\admin\model\AuthGroupAccess;
use app\admin\model\AuthRule;
use think\auth\Auth as Auths;
use think\Request;

class Auth extends Base
{
    public function _initialize()
    {
        parent::_initialize();
        define('UID',is_login());
        if(!UID){//判断是否登录
            return $this->redirect('login/login');
        }
        //判断是否是超级管理员
        define('IS_ROOT',is_administrator(UID));
        if(!IS_ROOT) {
            $request = Request::instance();
            $module = $request->module();
            $controller = $request->controller();
            $action = $request->action();
            $ob = new Auths;
            $result = $ob->check(strtolower($module . '/' . $controller . '/' . $action), UID);
            if (!$result) {
                $this->error('未授权访问！', 'login/login');
            }
        }
        $model = new AuthRule();
        $groupAccess = new AuthGroupAccess();
        $group = $groupAccess->with('authGroup')->where('uid', '=', UID)->select();
        $where_1 = '';
        foreach ($group as $key => $value) {
            $where_1 .= $value['auth_group']['rules'];
        }
        $where = ['id' => ['in', implode(',', array_unique(explode(',', trim($where_1, ','))))],'is_nav'=>1];
        if(IS_ROOT){
            $where = ['is_nav'=>1,'hide'=>1];
        }
        $menu = tree($model->where($where)->select());
        $this->assign('menu_list', $menu);
    }
}