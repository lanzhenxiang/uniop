<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/11
 * Time: 16:31
 */

namespace App\Controller\Admin;


use App\Controller\AdminController;
use App\Controller\SobeyController;
use Cake\ORM\TableRegistry;

class BasicDataListController extends AdminController{

    public $paginate = [
        'limit' => 15,
    ];

    public function index($type_id=0){

        $type_id = isset($this->request->data['$type_id'])?$this->request->data['$type_id']:$type_id;
        $basic_data_list = TableRegistry::get('BasicDataList',['classname'=>'App\Model\Table\BasicDataListTable']);
        $basic_data_type = TableRegistry::get('BasicDataType',['classname'=>'App\Model\Table\BasicDataTypeTable']);
        $type = $basic_data_type->find('all')->select(['type_id','type_name']);
        if($type_id==0){
            //若类型id为0就查询全部参数
            $data =  $basic_data_list->find('all')->contain(['BasicDataType']);
        }else {
            //根据类型查询参数
            $data = $basic_data_list->find('all',array('conditions'=>array('BasicDataList.type_id'=>$type_id)))->contain(['BasicDataType']);
        }
        $data=$this->paginate($data);
        $this->set('type',$type);
        $this->set('data',$data);

    }

    //添加修改
    public function addedit($id=0){
        $basic_data_list = TableRegistry::get('BasicDataList',['classname'=>'App\Model\Table\BasicDataListTable']);
        $basic_data_lists = $basic_data_list->newEntity();
        $basic_data_type = TableRegistry::get('BasicDataType',['classname'=>'App\Model\Table\BasicDataTypeTable']);
        $type = $basic_data_type->find()->select(['type_id','type_name'])->toArray();
        $this->set('type',$type);
        //显示页面
        if($this->request->is('get')){
            if($id){
                $data = $basic_data_list->find('all')->where(array('id'=>$id))->toArray();
                $this->set('data',$data[0]);
            }
        }else{
            //保存修改操作
            $message = array('code'=>1,'msg'=>'操作失败');
            if(isset($this->request->data['id'])){
                $update = $basic_data_list->updateAll($this->request->data,array('id'=>$this->request->data['id']));
                if($update){
                    $message = array('code'=>0,'msg'=>'操作成功');
                }
            }else{
                $basic_data_lists = $basic_data_list->patchEntity($basic_data_lists,$this->request->data);
                $result = $basic_data_list->save($basic_data_lists);
                if($result){
                    $message = array('code'=>0,'msg'=>'操作成功');
                }
            }
            echo json_encode($message);exit;
        }
    }

    //删除
    public function delete(){
        if($this->request->data['id']){
            $basic_data_list = TableRegistry::get('BasicDataList',['classname'=>'App\Model\Table\BasicDataListTable']);
            $id=$this->request->data['id'];
            $res = $basic_data_list->deleteAll(array('id'=>$id));
            if($res){
                $message = array('code'=>0,'msg'=>'操作成功','url'=>$this->request->data['url']);
            }
            echo json_encode($message);exit();
        }
    }
}