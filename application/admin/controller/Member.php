<?php
namespace app\admin\controller;
use app\admin\model\AuthGroupAccess;
use app\admin\model\Member as MemberModel;

class Member extends Auth{
    public function dataList(){
        if($this->request->isAjax()){
            $model = new MemberModel;
            $param = $this->request->param();
            $draw  = $param['draw'];    //这个值直接返回给前台
            $start = $param['start']; //起始条数
            $length= $param['length']; //查询多少条
            $search= trim($param['search']['value']); //搜索
            $where=[];
            if(strlen($search)>0){
                $where =[
                    'username' => ['like', '%' . $search . '%'],
                    'nick_name'  => ['like', '%' . $search . '%']
                ];
            }
            $order = $param['order']['0']['dir'];
            $orderField = (empty($orderArr[$param['order']['0']['column']])) ? 'id' : $orderArr[$param['order']['0']['column']];
            $orders = $orderField . ' ' . $order;
            $recordsTotal = $model->count(); //得到总条数
            $recordsFiltered = $model->whereOr($where)->count();
            $list = $model->order($orders)->whereOr($where)->limit($start, $length)->select();
            $result = array(
                "draw" => intval($draw),
                "recordsTotal" => intval($recordsTotal),
                "recordsFiltered" => intval($recordsFiltered),
                "data" => $list,
            );
            return $result;
        }
        return $this->fetch();
    }

    public function memberDel(){
        if($this->request->isAjax()){
            $model = new MemberModel();
            $data = $this->request->post();
            $id = $data['id'];
            if($id == 1){
                return ajax_return_adv_error('管理员不可删除');
            }
            $result = $model->where('id','=',$id)->delete();
            if($result){
                return ajax_return_adv('操作成功');
            }elseif ($result === false){
                return ajax_return_adv_error($model->getError());
            }
        }
        return ajax_return_adv_error('操作失败');
    }

    public function memberDisable(){
        if($this->request->isAjax()){
            $model = new MemberModel();
            $pram = $this->request->post();
            $id = $pram['id'];
            if($id == 1){
                return ajax_return_adv_error('管理员不可禁用');
            }
            $data = ['status'=>$pram['status']];
            $result = $model->isUpdate(true)->save($data,['id'=>$id]);
            if($result){
                return ajax_return_adv('操作成功');
            }elseif ($result === false){
                return ajax_return_adv_error($model->getError());
            }
        }
        return ajax_return_adv_error('操作失败');
    }

    public function memberAdd(){
        if($this->request->isAjax()) return $this->memberSave(self::SAVE_INSERT);
        return $this->fetch();
    }

    public function memberEdit(){
        if($this->request->isAjax()) return $this->memberSave(self::SAVE_UPDATE);
        $model = new MemberModel();
        $parm =$this->request->request();
        $info = $model->where('id','=',$parm['id'])->find();
        $this->assign('info',$info);
        return $this->fetch();
    }

    public function memberSave($type)
    {
        $update = $type == self::SAVE_INSERT ? false : true;
        $data = $this->request->param();
        $model = new MemberModel();
        $model::event('before_insert',function(MemberModel $Member){
            $data = $this->request->param();
            $Member->reg_time   = time();
            $Member->reg_ip = ip2long($this->request->ip());
            $Member->status = 1;
            $Member->password = think_ucenter_md5($data['password'],PWD_KEY);
        });
        $model::event('before_update',function(MemberModel $Member){
            $Member->update_time   = time();
        });
        if ($update) {
            if(empty($data['password'])){
                unset($data['password']);
                unset($data['repassword']);
            }else{
                $data['password'] = think_ucenter_md5($data['password'],PWD_KEY);
            }
            $result = $model->validate('Member.edit')->allowField(true)->isUpdate($update)->save($data, ['id' => $data['id']]);
        } else {
            $result = $model->validate(true)->allowField(true)->isUpdate($update)->save($data);
            if($result) {
                $member_id = $model->getLastInsID();
                $group_access = new AuthGroupAccess();
                $data = ['uid' => $member_id, 'group_id' => 1];
                $re = $group_access->save($data);
                if (!$re) {
                    return ajax_return_adv_error($group_access->getError());
                }
            }
        }
        if ($result) {
            $res = ajax_return_adv('操作成功', '', false, false, url('memberList'));
        } else {
            $res = ajax_return_adv_error($model->getError());
        }
        return $res;
    }

}