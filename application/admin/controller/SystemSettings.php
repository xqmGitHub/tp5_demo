<?php
namespace app\admin\controller;
use app\admin\model\Config;
use app\admin\model\Slide;
use app\admin\model\SlideGroup;
use app\common\model\File;

class SystemSettings extends Auth{
    public function configList(){
        if($this->request->isAjax()){
            $model = new Config();
            $param = $this->request->param();
            $draw  = $param['draw'];    //这个值直接返回给前台
            $start = $param['start']; //起始条数
            $length= $param['length']; //查询多少条
            $search= trim($param['search']['value']); //搜索
            $where=[];
            if(strlen($search)>0){
                $where =[
                    'name' => ['like', '%' . $search . '%'],
                    'title'  => ['like', '%' . $search . '%'],
                    'remark'  => ['like', '%' . $search . '%']
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

    public function configDel(){
        if($this->request->isAjax()) {
            $id = $this->request->param()['id'];
            $model = new Config();
            $result = $model->where('id', '=', $id)->delete();
            if ($result) {
                return ajax_return_adv('操作成功');
            } else {
                return ajax_return_adv_error($model->getError());
            }
        }
        return ajax_return_adv_error('操作失败');
    }

    public function configAdd(){
        if($this->request->isAjax()) return $this->configSave(self::SAVE_INSERT);
        return $this->fetch();
    }

    public function configEdit(){
        if($this->request->isAjax()) return $this->configSave(self::SAVE_UPDATE);
        $model = new Config();
        $id = $this->request->param()['id'];
        $info = $model->where('id','=',$id)->find();
        $this->assign('info',$info);
        return $this->fetch();
    }

    public function configSave($type)
    {
        $model = new Config();
        $update = $type == self::SAVE_INSERT ? false : true ;
        $data = $this->request->param();
        $model::event('before_insert',function(Config $ConfigModel){
            $ConfigModel->create_time = time();
            $ConfigModel->status = 1;
        });
        if ($update) {
            $result = $model->validate(true)->allowField(true)->isUpdate($update)->save($data, ['id' => $data['id']]);
        } else {
            $result = $model->validate(true)->allowField(true)->isUpdate($update)->save($data);
        }
        if ($result) {
            return ajax_return_adv('操作成功');
        }
        else if ($result === false) {
            return ajax_return_adv_error($model->getError());
        }
        return ajax_return_adv_error('操作失败');
    }

    public function websiteBase(){
        $model = new Config();
        if($this->request->isAjax()) {
            $parm = $this->request->param();
            $id = $parm['id'];
            $arr = array(
                'WEBSITE_TITLE'=>$parm['WEBSITE_TITLE'],
                'WEBSITE_KEYWORDS'=>$parm['WEBSITE_KEYWORDS'],
                'WEBSITE_DESCRIPTION'=>$parm['WEBSITE_DESCRIPTION'],
                'WEBSITE_ICP'=>$parm['WEBSITE_ICP'],
                'WEBSITE_COPYRIGHT'=>$parm['WEBSITE_COPYRIGHT'],
                'WEBSITE_CLOSE'=>$parm['WEBSITE_CLOSE'],
                'WEBSITE_CLOSE_TIP'=>$parm['WEBSITE_CLOSE_TIP'],
            );
            $data =   ['name'=>'WEBSITE_BASECON','title'=>'网站基本配置','group'=>5,'create_time'=>time(),'value'=>json_encode($arr)];
            if($id){
                //$data = ['id'=>$id,'title'=>'网站基本配置','group'=>5,'create_time'=>time(),'value'=>json_encode($arr)];
                $data['id'] = $id;
                $data['update_time'] = time();
                $result = $model->allowField(true)->isUpdate(true)->save($data);
            }else{
                $result = $model->allowField(true)->save($data);
            }
            if ($result){
                return ajax_return_adv('操作成功');
            }else{
                return ajax_return_adv_error($model->getError());
            }
        }
        $websiteInfo = $model->where('name','WEBSITE_BASECON')
            ->field('id,name,value')
            ->find();
        $info = [];
        if($websiteInfo){
            $websiteInfo['value'] = json_decode($websiteInfo['value']);
            foreach ($websiteInfo['value'] as $k => $v){
                $info[$k] = $v;
            }
            $info['id'] = $websiteInfo['id'];
        }else{
            $info['id'] = '';
            $info['WEBSITE_TITLE'] = '';
            $info['WEBSITE_KEYWORDS'] = '';
            $info['WEBSITE_DESCRIPTION'] = '';
            $info['WEBSITE_ICP'] = '';
            $info['WEBSITE_CLOSE'] = 1;
            $info['WEBSITE_CLOSE_TIP'] = '';
            $info['WEBSITE_COPYRIGHT'] = '';
        }
        $this->assign('websiteInfo',$info);
        return $this->fetch();
    }

    public function slideGroupList(){
        if($this->request->isAjax()){
            $model = new SlideGroup();
            $slideModel = new Slide();
            $param = $this->request->param();
            $draw  = $param['draw'];    //这个值直接返回给前台
            $start = $param['start']; //起始条数
            $length= $param['length']; //查询多少条
            $search= trim($param['search']['value']); //搜索
            $where=[];
            if(strlen($search)>0){
                $where =[
                    'title'  => ['like', '%' . $search . '%'],
                    'id'  => $search,
                ];
            }
            $order = $param['order']['0']['dir'];
            $orderField = (empty($orderArr[$param['order']['0']['column']])) ? 'id' : $orderArr[$param['order']['0']['column']];
            $orders = $orderField . ' ' . $order;
            $recordsTotal = $model->count(); //得到总条数
            $recordsFiltered = $model->whereOr($where)->count();
            $list = $model->order($orders)->whereOr($where)->limit($start, $length)->select();
            $slideList = $slideModel->field('id,count(`group`) as slideNum,image,`group`')->group('`group`')->select();
            foreach ($list as $k => $v){
                $list[$k]['slideNum'] = 0;
                $list[$k]['image'] = '';
                foreach ($slideList as $slide_v){
                    if($v->id == $slide_v->group){
                        $list[$k]['slideNum'] = $slide_v->slideNum;
                        $list[$k]['image'] = substr($slide_v->image,1);
                    }
                }
            }
            $list   = array_chunk($list,4);
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

    public function slideGroupDel(){
        if($this->request->isAjax()){
            $id = $this->request->post()['id'];
            $model = new SlideGroup();
            $result = $model->where('id','=',$id)->delete();
            if ($result){
                return ajax_return_adv('操作成功');
            }else{
                return ajax_return_adv_error($model->getError());
            }
        }
        return ajax_return_adv_error('操作失败');
    }

    public function slideGroupAdd(){
        if($this->request->isAjax()) return $this->slideGroupSave(self::SAVE_INSERT);
        return ajax_return_adv_error('操作失败');
    }

    public function slideGroupEdit(){
        if($this->request->isAjax()) return $this->slideGroupSave(self::SAVE_UPDATE);
        return ajax_return_adv_error('操作失败');
    }

    public function slideGroupSave($type){
        $param = $this->request->param();
        $model = new SlideGroup();
        $update = $type == self::SAVE_INSERT ? false : true ;
        if($update){
            $result = $model->allowField(true)->isUpdate($update)->save($param,['id'=>$param['id']]);
        }else{
            $result = $model->allowField(true)->isUpdate($update)->save($param);
        }
        if($result){
            return ajax_return_adv('操作成功');
        }else{
            return ajax_return_adv_error($model->getError());
        }
    }

    public function slide(){
        $id = $this->request->param()['id'];
        $model = new Slide();
        $slideGroup = new SlideGroup();
        $data = $model->where('group','=', $id)->order('sort')->select();
        $slideGroup = $slideGroup->where('id','=',$id)->find();
        $this->assign('slideGroup',$slideGroup);
        $this->assign('slide',$data);
        return $this->fetch();
    }

    public function slideSort(){
        if ($this->request->isAjax()) {
            $model = new Slide();
            $max_sort = $model->max('sort');
            if ($max_sort > 1000) $max_sort = 0;
            $sort = $this->request->param()['sort'];
            foreach ($sort as $id => $s){
                $s = $max_sort + $s;
                $result = $model->isUpdate(true)->save(['sort'=>$s], ['id'=>$id]);
            }
            if ($result){
                return ajax_return_adv('操作成功');
            }else{
                return ajax_return_adv($model->getError());
            }
        }
        return ajax_return_adv('操作失败');
    }

    public function slideDel(){
        if($this->request->isAjax()){
            $id = $this->request->param()['id'];
            $model = new Slide();
            $result = $model->where('id','=',$id)->delete();
            if ($result){
                return ajax_return_adv('操作成功');
            }else{
                return ajax_return_adv($model->getError());
            }
        }
        return ajax_return_adv_error('操作失败');
    }

    public function slideAdd(){
        if ($this->request->isAjax()) return $this->slideSave(self::SAVE_INSERT);
        $this->assign('group_id',$this->request->param()['group_id']);
        return $this->fetch();
    }

    public function slideEdit(){
        if ($this->request->isAjax()) return $this->slideSave(self::SAVE_UPDATE);
        $model = new Slide();
        $id = $this->request->param()['id'];
        $data = $model->where('id','=',$id)->field('id,title,main_link,target,size,extension,md5_hash,image,summary')->find();
        $this->assign('slide',$data);
        return $this->fetch();
    }

    public function slideSave($type){
        $update = $type == self::SAVE_INSERT ? false: true;
        $param = $this->request->param();
        $model = new Slide();
        $model::event('before_insert',function(Slide $SlideModel){
            $SlideModel->add_time = time();
        });
        $model::event('before_update',function(Slide $SlideModel){
            $SlideModel->update_time = time();
        });
        if($update){
            $result = $model->allowField(true)->isUpdate($update)->save($param,['id'=>$param['id']]);
        }else{
            $result = $model->allowField(true)->isUpdate($update)->save($param);
        }
        if ($result){
            return ajax_return_adv('操作成功');
        }else{
            return ajax_return_adv_error($model->getError());
        }
    }

    public function slideFileSave(){
        set_time_limit(0);
        $return  = ['status' => 1, 'info' => '上传成功', 'data' => ''];
        $download_upload      = config('DOWNLOAD_UPLOAD');
        $file                 = request()->file();
        if( empty($file) ) {
            $return['status'] = 0;
            $return['info']   = '请先上传文件';
            return json_encode($return);
        }
        $result_info    = [];
        foreach ($file as $key => $value) {
            //验证文件规格信息
            if (!$value->validate($download_upload)->check($download_upload)) {
                $return['status'] = 0;
                $return['info'] = $value->getError();
                return json_encode($return);
            }
            $hash_md5 = $value->hash('md5');
            if(empty($hash_md5)) {
                $return['status'] = 0;
                $return['info'] = '缺少参数:md5';
                return json_encode($return);
                //throw new \Exception('缺少参数:md5');
            }
            $fileModel = new File();
            //查看文件是否存在，存在就返回，不存在则上传
            $has_img = $fileModel->where(['md5_hash' => $hash_md5])->field('id,size,path as image,md5_hash,extension')->find();
            if(isset($has_img['id']) && is_numeric($has_img['id'])){
                if( file_exists($has_img['image']) ) {
                    $result_info[] = $has_img;
                }
            }else {
                $save_name = $download_upload['rootPath'];
                $upload_info = $value->move($save_name);
                if ($upload_info) {
                    $save_data['image'] = $upload_info->getPathName();
                    $save_data['extension'] = $upload_info->getInfo('type');
                    $save_data['size'] = $upload_info->getInfo('size');
                    $save_data['md5_hash'] = $value->hash('md5');
                    $result_info[] = $save_data;
                } else {
                    $return['status'] = 0;
                    $return['info'] = $value->getError();
                    return json_encode($return);
                }
            }
        }
        $return['data']     = $result_info;
        return json_encode($return);
    }
}