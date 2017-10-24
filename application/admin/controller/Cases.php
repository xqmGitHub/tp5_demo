<?php
namespace app\admin\controller;

use \app\admin\model\Cases as CasesModel;

class Cases extends Auth{
    public function casesList(){
        if($this->request->isAjax()){
            $model = new CasesModel();
            $param = $this->request->param();
            $draw  = $param['draw'];    //这个值直接返回给前台
            $start = $param['start']; //起始条数
            $length= $param['length']; //查询多少条
            $search= trim($param['search']['value']); //搜索
            $where=[];
            if(strlen($search)>0){
                $where =[
                    'name' => ['like', '%' . $search . '%'],
                    'id'  => $search
                ];
            }
            $order = $param['order']['0']['dir'];
            $orderField = (empty($orderArr[$param['order']['0']['column']])) ? 'id' : $orderArr[$param['order']['0']['column']];
            $orders = $orderField . ' ' . $order;
            $recordsTotal = $model->count(); //得到总条数
            $recordsFiltered = $model->whereOr($where)->count();
            $list = $model->order($orders)->whereOr($where)->limit($start, $length)
                ->field('id,uid,name,money,style,huose_type,space,color,area,show_index,pid,status,add_time')
                ->select();
            foreach ($list as $value){

            }
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
}