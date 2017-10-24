<?php
namespace app\admin\controller;
use app\admin\model\AuthGroup;
use app\admin\model\AuthGroupAccess;
use app\admin\model\AuthRule;
use app\admin\model\Member as MemberModel;

class AuthManager extends Auth{

    public function groupList(){
        if($this->request->isAjax()){
            $model = new AuthGroup();
            $param = $this->request->param();
            $draw  = $param['draw'];    //这个值直接返回给前台
            $start = $param['start']; //起始条数
            $length= $param['length']; //查询多少条
            $search= trim($param['search']['value']); //搜索
            $where=[];
            if(strlen($search)>0){
                $where =[
                    'title' => ['like', '%' . $search . '%'],
                    'description' => ['like', '%' . $search . '%'],
                    'id'    => ['like', '%' . $search . '%']
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

    public function groupAdd(){
        if($this->request->isAjax()) return $this->groupSave(self::SAVE_INSERT);
        return $this->fetch();
    }

    public function groupEdit(){
        if($this->request->isAjax()) return $this->groupSave(self::SAVE_UPDATE);
        $model = new AuthGroup();
        $param = $this->request->param();
        $data = $model->where('id','=',$param['id'])->find();
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function groupDel(){
        $res = ajax_return_adv_error('操作失败');
        if($this->request->isAjax()){
            $data = $this->request->param();
            $model =new AuthGroup();
            $result = $model->where('id','=',$data['id'])->delete();
            if ($result) {
                return ajax_return_adv('操作成功');
            }
            else if ($result === false) {
                return ajax_return_adv_error($model->getError());
            }
        }
        return $res;
    }

    public function nodeList(){
        if($this->request->isAjax()){
            $model = new AuthRule();
            $param = $this->request->param();
            $draw  = $param['draw'];    //这个值直接返回给前台
            $start = $param['start']; //起始条数
            $length= $param['length']; //查询多少条
            $search= trim($param['search']['value']); //搜索
            $where=[];
            if(strlen($search)>0){
                $where =[
                    'title' => ['like', '%' . $search . '%'],
                    'name'  => ['like', '%' . $search . '%'],
                    'id'    => ['like', '%' . $search . '%']
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

    public function nodeAdd(){
        if ($this->request->isAjax()) return $this->nodeSave(self::SAVE_INSERT);
        $model = new AuthRule();
        $data = $model->select();
        $arr = getChildren(list_to_tree($data));

        $str = '';
        $str = '<div class="row cl">
                <label class="form-label col-xs-4 col-sm-3">
                <span class="c-red">*</span>上级菜单：</label>
			    <div class="formControls col-xs-8 col-sm-9">
				<span class="select-box">';
        $str .= '<select name="pid" class="select">';
        $str .= '<option value="0">顶级菜单</option>';

        foreach ($arr as $row) {
            $str .= '<option value ="' . $row['id'] . '">';
            $str .= str_pad("", $row['deep'] * 3, "|----", STR_PAD_RIGHT);
            $str .= $row['title'];
            $str .= '</option>';
        }
        $str .= '</select></span></div></div>';
        $this->assign('tree_list', $str);
        return $this->fetch();
    }
    public function nodeEdit(){
        if ($this->request->isAjax()) return $this->nodeSave(self::SAVE_UPDATE);
        $model = new AuthRule();
        $param = $this->request->param();
        $list = $model->where('id', '=', $param['id'])->find();
        $data  = $model->select();
        $arr   = getChildren(list_to_tree($data));
        $pid = $list['pid'];
        $str ='';
        $str = '<div class="row cl">
                <label class="form-label col-xs-4 col-sm-3">
                <span class="c-red">*</span>上级菜单：</label>
			    <div class="formControls col-xs-8 col-sm-9">
				<span class="select-box">';
        $str .= '<select name="pid" class="select">';
        $pid == 0 ? $str .= '<option value="0" selected>顶级菜单</option>': $str .= '<option value="0">顶级菜单</option>';

        foreach ($arr as $row) {
            if($pid == $row['id']){
                $str .= '<option value ="' . $row['id'] .'" selected>';
                $str .= str_pad("", $row['deep']*3, "|----", STR_PAD_RIGHT);
                $str .= $row['title'];
                $str .= '</option>';
            }else{
                $str .= '<option value ="' . $row['id'] .'">';
                $str .= str_pad("", $row['deep']*3, "|----", STR_PAD_RIGHT);
                $str .= $row['title'];
                $str .= '</option>';
            }

        }
        $str .= '</select></span></div></div>';
        $this->assign('tree_list', $str);
        $this->assign('list', $list);
        return $this->fetch();
    }

    public function nodeDel(){
        if($this->request->isAjax()){
            $model = new AuthRule();
            $param = $this->request->param();
            $result = $model->where('id', '=',$param['id'])->delete();
            if ($result) {
                return ajax_return_adv('操作成功');
            } else if ($result === false) {
                return ajax_return_adv_error($model->getError());
            }
            return ajax_return_adv_error('操作失败');
        }
    }

    public function nodeBatchDel(){
        if($this->request->isAjax()){
            $model = new AuthRule();
            $param = $this->request->param();
            $ids   = trim($param['ids'],",");
            if($ids==false){
                return ajax_return_adv_error('没有选择');
            }
            $result = $model->destroy($ids);
            if ($result) {
                return ajax_return_adv('操作成功');
            } else if ($result === false) {
                return ajax_return_adv_error($model->getError());
            }
            return ajax_return_adv_error('操作失败');
        }
    }

    /**
     * 权限分配
     * @return mixed
     */
    public function access()
    {
        if ($this->request->isAjax()) return $this->accessSave(self::SAVE_UPDATE);
        $param      = $this->request->param();
        $model      = new AuthRule();
        $auth_group_model = new AuthGroup();
        $data       = $model->where('status','=',1)->select();
        $arr        = tree($data);
        $group_id = get_int_value($param['id']);
        $group_rules = $auth_group_model->where('id','=',$group_id)->field('rules')->find();
        //$group_rules = explode(',',$group_rules);
        $this->assign('group_rules', $group_rules['rules']);
        $this->assign('group_id', $group_id);
        $this->assign('data_list', $arr);
        return $this->fetch();
    }

    public function accessSave($type)
    {
        $data = $this->request->param();
        $group_id = get_int_value($data['id']);
        $update = $type == self::SAVE_INSERT ? false : true;

        if (!$group_id && !is_array($data['rules'])) $this->error('操作失败');

        $model = new AuthGroup;

        AuthGroup::event('before_update', function (AuthGroup $AuthGroupModel) {
            $str = '';
            $data = $this->request->param();
            foreach ($data['rules'] as $v) {
                $str .= $v . ',';
            }
            $AuthGroupModel->rules = trim($str, ',');
        });

        $result = $model->isUpdate($update)->save(['id' => $group_id]);

        if ($result) {
            $res = ajax_return_adv('操作成功', '', false, false, url('groupList'));
        } else {
            $res = ajax_return_adv_error($model->getError());
        }
        return $res;
    }
    /**
     * 成员授权
     * @return array|mixed|string
     */
    public function accessMember()
    {
        if ($this->request->isAjax()) return $this->accessMemberSave();

        $id           = get_int_value($this->request->param()['id']);

        if (!$id) return '';

        $new_arr      = [];
        $member_model = new MemberModel;
        $access_model = new AuthGroupAccess();
        $member_list  = $member_model->where('id','>',1)->field('id,username')->select();
        $access_list  = $access_model->field('uid')->where('group_id', '=', $id)->select();

        $access_arr = [];
        foreach ($access_list as $v) $access_arr[$v['uid']] = $v['uid'];

        foreach ($member_list as $k => $v) {
            if (in_array($v['id'], $access_arr)) {
                $new_arr['access'][]    = $v;
            } else {
                $new_arr['no_access'][] = $v;
            }
        }
        $this->assign('group_id', $id);
        $this->assign('data_list', $new_arr);
        return $this->fetch();
    }

    private function accessMemberSave()
    {
        $param_data = $this->request->param();

        $add_data = [];
        $model    = new AuthGroupAccess;
        $group_id = get_int_value($param_data['group_id']);

        if ($group_id) {
            $del_res  = $model->where('group_id', '=', $group_id)->delete();
            if ($del_res !== false) {
                foreach ($param_data['access'] as $ke => $val) {
                    $add_data[$ke]['uid'] = $val;
                    $add_data[$ke]['group_id']  = $group_id;
                }
                $result = $model->saveAll($add_data, false);
                if (is_array($result)) return ajax_return_adv('操作成功', '', false, false, url('groupList'));
            }
        }
        return ajax_return_adv_error('操作失败');
    }

    public function groupSave($type)
    {
        $model = new AuthGroup();
        $update = $type == self::SAVE_INSERT ? false : true ;
        $param = $this->request->param();
        $model::event('before_insert',function(AuthGroup $AuthGroupModel){
            $AuthGroupModel->type   = $AuthGroupModel::TYPE;
            $AuthGroupModel->module = $AuthGroupModel::MODULE;
            $AuthGroupModel->status = 1;
        });
        $result = $model->allowField(true)->validate(true)->isUpdate($update)->save($param);
        if ($result) {
            return ajax_return_adv('操作成功');
        }
        else if ($result === false) {
            return ajax_return_adv_error($model->getError());
        }
        return ajax_return_adv_error('操作失败');
    }

    private function nodeSave($type)
    {
        $update = $type == self::SAVE_INSERT ? false : true;
        $data = $this->request->post();
        $model = new AuthRule();
        if ($update) {
            $result = $model->validate(true)->allowField(true)->isUpdate($update)->save($data, ['id' => $data['id']]);
        } else {
            $result = $model->validate(true)->allowField(true)->isUpdate($update)->save($data);
        }
        if ($result) {
            $res = ajax_return_adv('操作成功', '', false, false, url('nodeList'));
        } else {
            $res = ajax_return_adv_error($model->getError());
        }
        return $res;
    }
}