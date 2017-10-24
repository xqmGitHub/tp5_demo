<?php
namespace app\common\controller;
use app\common\model\File as FileModel;
use think\Image;

class File extends Base {
    protected $exts = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'image/bmp'];
    public function upload($object_type = 0){
        set_time_limit(0);
        $return  = ['status' => 1, 'info' => '上传成功', 'data' => ''];
        $download_upload      = config('DOWNLOAD_UPLOAD');
        $file                 = request()->file();
        if( empty($file) ) {
            $return['status'] = 0;
            $return['info']   = '请先上传文件';
            return $return;
        }
        $fileModel      = new FileModel();
        $result_info    = [];
        //处理图片
        foreach($file as $key=>$value) {
            //验证文件规格信息
            if( ! $value->validate($download_upload)->check($download_upload)) {
                $return['status']   = 0;
                $return['info']     = $value->getError();
                return $return;
            }
            //验证文件是否已存在
            $info = $this->isFile($value->hash('md5'));
            if( isset($info['id']) && is_numeric($info['id']) ){
                if( file_exists($info['path']) ) {
                    $result_info[]  = $info->getData();
                } else {
                    $save_name      = $download_upload['rootPath'];
                    $upload_info    = $value->move( $save_name );
                    //如果上传的是图片,则需要生成缩略图
                    if(in_array($upload_info->getinfo('type'), $this->exts)) {
                        $this->img_tb($upload_info->getPathName());
                    }
                    //更新图片信息
                    if( $upload_info ) {
                        $save_data['title']         = $upload_info->getInfo('name');
                        $save_data['path']          = $upload_info->getPathName();
                        $save_data['extension']          = $upload_info->getInfo('type');
                        $save_data['size']         = $upload_info->getInfo('size');
                        $save_data['add_by']  = UID;
                        $save_data['object_type']  = $object_type;
                        $save_data['md5_hash']     = $value->hash('md5');
                        $save_data['add_time']     = time();
                        $fileModel::get($info['id'])->isUpdate(true)->save($save_data);
                        $result_info[]  = $fileModel::get($info['id'])->getData();
                    } else {
                        $return['status']   = 0;
                        $return['info']     = $value->getError();
                        return $return;
                    }
                }
            } else {
                $save_name      = $download_upload['rootPath'];
                $upload_info    = $value->move( $save_name );
                //如果上传的是图片,则需要生成缩略图
                if(in_array($upload_info->getinfo('type'), $this->exts)) {
                    $this->img_tb($upload_info->getPathName());
                }
                //新增图片信息
                if( $upload_info ) {
                    $save_data['title']         = $upload_info->getInfo('name');
                    $save_data['path']          = $upload_info->getPathName();
                    $save_data['extension']          = $upload_info->getInfo('type');
                    $save_data['size']         = $upload_info->getInfo('size');
                    $save_data['add_by']  = UID;
                    $save_data['object_type']  = $object_type;
                    $save_data['md5_hash']     = $value->hash('md5');
                    $save_data['add_time']     = time();
                    $fileModel->isUpdate(false)->save($save_data);
                    $result_info[]             = $fileModel->getData();
                } else {
                    $return['status']   = 0;
                    $return['info']     = $value->getError();
                    return $return;
                }
            }
        }
        $return['data']     = $result_info;
        return $return;
    }

    /**
     * 检测当前上传的文件是否已经存在
     * @param $md5_hash string  md5哈希值
     * @param $type     int     类型
     * @return mixed
     * @throws \Exception
     */
    public function isFile($md5_hash){
        if(empty($md5_hash)) {
            throw new \Exception('缺少参数:md5');
        }
        $fileModel   = new FileModel();
        return $fileModel->where(['md5_hash' => $md5_hash])->find();
    }

    /**
     * 图片缩略处理
     * @param $path string 处理路径
     */
    function img_tb( $path ){
        $pathinfo   = pathinfo( $path );
        $image      = Image::open( $path );
        $image->thumb(600, 400)->save($pathinfo['dirname'].'/'.$pathinfo['filename'].'_tb.'.$pathinfo['extension']);
        $image->thumb(100, 600)->save($pathinfo['dirname'].'/'.$pathinfo['filename'].'_tbs.'.$pathinfo['extension']);
    }
}