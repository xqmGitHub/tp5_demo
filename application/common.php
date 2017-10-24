<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use think\Response;
use think\Session;
/**
 * @param $str 要加密的字符串
 * @param string $key
 * @return string
 */
function think_ucenter_md5($str, $key = 'ThinkUCenter')
{
    return '' === $str ? '' : md5(sha1($str) . $key);
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @return mixed
 */
function get_client_ip($type = 0)
{
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos    =   array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip     =   trim($arr[0]);
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip     =   $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/*
* @param  $msg         string          提示信息
* @param  $redirect    string          重定向类型 current|parent|''
* @param  $alert       string          父层弹框信息
* @param  $close       bool            是否关闭当前层
* @param  $url         string          重定向地址
* @param  $data        string          附加数据
* @param  $code        int             错误码
* @param  $extend      array           扩展数据
* @return Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
*/
function ajax_return_adv($msg = '操作成功', $redirect = 'parent', $alert = '', $close = false, $url = '', $data = '', $code = 1, $extend = [])
{
    $extend['opt'] = [
        'alert'    => $alert,
        'close'    => $close,
        'redirect' => $redirect,
        'url'      => $url,
    ];
    return ajax_return($data, $msg, $code, $extend);
}

/**
 * 返回错误json信息
 * @param  $msg         string          提示信息
 * @param  $code        int             错误码
 * @param  $redirect    string          重定向类型 current|parent|''
 * @param  $alert       string          父层弹框信息
 * @param  $close       bool            是否关闭当前层
 * @param  $url         string          重定向地址
 * @param  $data        string          附加数据
 * @param  $extend      array           扩展数据
 * @return Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
 */
function ajax_return_adv_error($msg = '', $code = 0, $redirect = '', $alert = '', $close = false, $url = '', $data = '', $extend = [])
{
    return ajax_return_adv($msg, $alert, $close, $redirect, $url, $data, $code, $extend);
}

/**
 * ajax数据返回，规范格式
 * @param  $data    array       返回的数据，默认空数组
 * @param  $msg     string      信息
 * @param  $code    int         错误码，1-未出现错误|其他出现错误
 * @param  $extend  array       扩展数据
 * @return Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
 */
function ajax_return($data = [], $msg = "", $code = 1, $extend = [])
{
    $ret = ["code" => $code, "msg" => $msg, "data" => $data];
    $ret = array_merge($ret, $extend);

    return Response::create($ret, 'json');
}

function list_to_tree($list, $pid = 0)
{
    $res = array();
    if (!empty($list)) {
        foreach ($list as $k => &$v) {
            if ($v['pid'] == $pid) {
                $v['child'] = list_to_tree($list, $v['id']);
                $res[] = $v;
            }
        }
        unset($v);
    }
    return $res;
}

function getChildren($parent, $deep = 0)
{
    $data = [];
    foreach ($parent as $row) {
        $data[] = array("id" => $row['id'], "title" => $row['title'], "pid" => $row['pid'], 'deep' => $deep);
        if (!empty($row['child'])) {
            $data = array_merge($data, getChildren($row['child'], $deep + 1));
        }
    }
    return $data;
}

function tree($data, $pid = 0)
{
    $tree = array();
    foreach ($data as $val){
        if ($val['pid'] == $pid) {
            $tmp = tree($data, $val['id']);
            if ($tmp) $val['children'] = $tmp;
            $tree[] = $val;
        }
    }
    return $tree;
}

/**
 * 获取一个整数值(123.45返回123，大于 PHP_INT_MAX 返回默认值 )
 * @param   $value          null|string     原值
 * @param   $defaultValue   int             为空或者=0时返回默认值
 * @return  int
 */
function get_int_value($value = null, $defaultValue = 0)
{
    if (null === $value) {
        return $defaultValue;
    }
    $value = trim($value);
    if (ctype_digit($value)) {
        if ($value <= PHP_INT_MAX) {
            return $value;
        } else {
            return $defaultValue;
        }
    }
    $value = preg_replace("/[^0-9](.*)$/", '', $value);
    if (ctype_digit($value)) {
        if ($value <= PHP_INT_MAX) {
            return $value;
        } else {
            return $defaultValue;
        }
    }
    return $defaultValue;
}

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login()
{
    $user = session('admin_user');
    if(empty($user)){
        return 0;
    }else{
        return $user['uid'];
    }

}

/**
 * 检测当前用户是否为管理员
 * @param  $uid   int  后台用户ID
 * @return boolean true-管理员，false-非管理员
 */
function is_administrator($uid){
    $uid = is_null($uid)?is_login():$uid;
    return $uid&&(intval($uid)===1);
}