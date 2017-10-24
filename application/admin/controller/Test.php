<?php
namespace app\admin\controller;
use app\common\controller\File;

class Test extends Auth
{

    public function pwd(){
        echo think_ucenter_md5('123456', PWD_KEY);
    }

    public function upload(){
        return $this->fetch();
    }

    public function fileSave(){
        $file = new File();
        $data = $file->upload();
        echo json_encode($data);
    }
}
