<?php
namespace app\home\controller;
use app\home\model\Follow as FollowModel;
use app\home\model\User;
use app\home\model\UserInfo;
use think\Db;

class Follow extends Base{

    public function testFollow(){
        return $this->fetch();
    }

    /**
     * 添加关注
     * @param $uid int 用户id
     * @param $to_uid int 要关注的用户id
     * @param $follow_state int 关注的状态 1:单方面关注,2:互相关注
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function addFollowsFansById()
    {
        $data = $this->request->param();
        if (empty($data)) return ajax_return_adv_error('数据为空');
        $data['add_time'] = time();
        $model = new FollowModel();
        if ($data['follow_state'] == 1) {//单方面关注
            $result = $model->isUpdate(false)->save($data);//添加到数据库
            if ($result) {//添加到有序集合
                $this->redis->zAdd('shc_follow:' . $data['uid'], time(), $data['to_uid']);
                $this->redis->zAdd('shc_fans:' . $data['to_uid'], time(), $data['uid']);
                return ajax_return_adv('操作成功');
            } else {
                return ajax_return_adv_error($model->getError());
            }
        } elseif ($data['follow_state'] == 2) {//相互关注
            Db::startTrans();
            try {
                $result1 = $model->isUpdate(false)->save($data);
                $result2 = $model->isUpdate(true)->save(['follow_state' => 2], ['uid' => $data['to_uid'], 'to_uid' => $data['uid']]);
                if ($result1 && $result2) {
                    $this->redis->zAdd('shc_follow:' . $data['uid'], time(), $data['to_uid']);
                    $this->redis->zAdd('shc_fans:' . $data['to_uid'], time(), $data['uid']);
                    Db::commit();
                    return ajax_return_adv('操作成功');
                } else {
                    return ajax_return_adv_error($model->getError());
                }
            } catch (\Exception $e) {
                //回滚事务
                Db::rollback();
                return ajax_return_adv_error($e->getMessage());
            }
        } else {
            return ajax_return_adv_error('操作失败');
        }
    }

    /**
     * 取消关注
     * @param $user_id int 用户id
     * @param $follow_id int 取消关注的用户id
     * @param $state int 取消关注之前的关系 1:单方面关注 2:互相关注
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function deleteFollowFansById($user_id,$follow_id,$state)
    {

        $model = new FollowModel();
        if ($state == 1) {
            $result = $model->where(['uid' => $user_id, 'to_uid' => $follow_id])->delete();
            if ($result) {//缓存删除关注
                $this->redis->zRem('shc_follow:' . $user_id, $follow_id);
                $this->redis->zRem('shc_fans:' . $follow_id, $user_id);
                return ajax_return_adv('操作成功');
            } else {
                return ajax_return_adv_error($model->getError());
            }
        } elseif ($state == 2) {
            //开启事务
            Db::startTrans();
            try {
                $result1 = $model->where(['uid' => $user_id, 'to_uid' => $follow_id])->delete();
                $result2 = $model->isUpdate(true)->save(['follow_state' => 1], ['uid' => $follow_id, 'to_uid' => $user_id]);
                if ($result1 && $result2) {
                    $this->redis->zRem('shc_follow:' . $user_id, $follow_id);
                    $this->redis->zRem('shc_fans:' . $follow_id, $user_id);
                    Db::commit();
                    return ajax_return_adv('操作成功');
                } else {
                    return ajax_return_adv_error($model->getError());
                }
            } catch (\Exception $e) {
                //回滚事务
                Db::rollback();
                return ajax_return_adv_error($e->getMessage());
            }
        } else {
            return ajax_return_adv_error('操作失败');
        }
    }


    /**
     * 查看我的关注列表
     * @param $user_id int 用户id
     * @param $pageNo int 页码数
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function getFollowsListById()
    {
        $param = $this->request->param();
        $user_id = $param['user_id'];
        $pageNo = $param['pageNo'];
        $pageNo = empty($pageNo)? 1: $pageNo;
        $showNum = 10;
        $findStart = $pageNo == 1 ? 0 : ($pageNo-1) * $showNum;
        $findEnd = $pageNo * $showNum -1;
        $followModel = new FollowModel();
        $userModel = new User();
        $follow_list = $this->redis->zRevRange('shc_follow:' . $user_id, $findStart, $findEnd);
        if ($follow_list) {
            $member_list = $userModel->alias('u')
                ->join('user_info ui', 'u.id=ui.uid', 'left')
                ->field('u.id,u.nick_name,ui.img')
                ->whereIn('u.id', $follow_list)
                ->select();
            if ($member_list) {
                return ajax_return($member_list, '操作成功');
            } else {
                return ajax_return('', $userModel->getError(),0);
            }
        } else {
            $offset = $showNum * ($pageNo - 1);
            $result = $followModel->alias('f')
                ->join('user_info u','f.uid=u.id')
                ->where('f.uid','=',$user_id)
                ->field('u.id,u.nick_name,u.img')
                ->limit($offset,$showNum)
                ->select();
            if ($result){
                return ajax_return($result, '操作成功');
            }else {
                return ajax_return('', $followModel->getError(),0);
            }
            //return $this->getFollowsListByDb(['a.sns_frommid' => $user_id], 'b.user_id,b.user_nickname,b.user_portrait', $limit);
        }
    }


    /**
     * 查看我的粉丝列表
     * @param $user_id int 用户id
     * @param $pageNo int 页码数
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     */
    public function getFansListById()
    {
        $param = $this->request->param();
        $user_id = $param['user_id'];
        $pageNo = $param['pageNo'];
        $pageNo = empty($pageNo)? 1: $pageNo;
        $showNum = 10;
        $findStart = $pageNo == 1 ? 0 : ($pageNo-1) * $showNum;
        $findEnd = $pageNo * $showNum -1;
        $followModel = new FollowModel();
        $userModel = new User();
        $follow_list = $this->redis->zRevRange('shc_fans:' . $user_id, $findStart, $findEnd);
        if ($follow_list) {
            $member_list = $userModel->alias('u')
                ->join('user_info ui', 'u.id=ui.uid', 'left')
                ->field('u.id,u.nick_name,ui.img')
                ->whereIn('u.id', $follow_list)
                ->select();
            if ($member_list) {
                return ajax_return($member_list, '操作成功');
            } else {
                return ajax_return('', $userModel->getError(),0);
            }
        } else {
            $offset = $showNum * ($pageNo - 1);
            $result = $followModel->alias('f')
                ->join('user_info u','f.uid=u.id')
                ->where('f.to_uid','=',$user_id)
                ->field('u.id,u.nick_name,u.img')
                ->limit($offset,$showNum)
                ->select();
            if ($result){
                return ajax_return($result, '操作成功');
            }else {
                return ajax_return('', $followModel->getError(),0);
            }
        }
    }


    //测试redis
    public function testRedis(){
        $this->redis->set('testRedis','hello redis');
        echo $this->redis->get('testRedis');
    }
}