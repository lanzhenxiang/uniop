<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/11
 * Time: 15:37
 */

namespace App\Controller\Admin;


use App\Controller\AdminController;
use App\Controller\SobeyController;
use Cake\ORM\TableRegistry;

class BasicDataTypeController extends AdminController{

    public $paginate = [
        'limit' => 15,
    ];

    //显示
    public function index(){
        $basic_data_types = TableRegistry::get('BasicDataType',['classname'=>'App\Model\Table\BasicDataTypeTable']);
        $basic= $basic_data_types->find('all');
        $basic=$this->paginate($basic);
        $this->set('data',$basic);
    }

    //增加修改
    public function addedit($id=0){
        $basic_data_types = TableRegistry::get('BasicDataType',['classname'=>'App\Model\Table\BasicDataTypeTable']);
        if($this->request->is('get')){
            if($id){
                $basic_data = $basic_data_types->find('all')->where(array('type_id'=>$id))->toArray();
                $this->set('data',$basic_data[0]);
            }
        }else{
            $message = array('code'=>0,'msg'=>'操作失败');
            $basic_data_type=$basic_data_types->newEntity();
            if(isset($this->request->data['type_id'])){
                $type_id = $this->request->data['type_id'];
                $t_result = $basic_data_types->updateAll($this->request->data,array('type_id'=>$type_id));
                if(isset($t_result)){
                    $message = array('code'=>0,'msg'=>'操作成功');
                }
                echo json_encode($message);exit;
            }else{
                $basic_data = $basic_data_types->patchEntity($basic_data_type,$this->request->data);
                $result = $basic_data_types->save($basic_data);
                if($result){
                    $message = array('code'=>0,'msg'=>'操作成功');
                }
                echo json_encode($message);exit;

            }
        }
    }

    //删除参数类型
    public function delete(){
        if($this->request->data['id']){
            $basic_data_types = TableRegistry::get('BasicDataType',['classname'=>'App\Model\Table\BasicDataTypeTable']);
            $id=$this->request->data['id'];
            $res = $basic_data_types->deleteAll(array('type_id'=>$id));
            if($res){
                $message = array('code'=>0,'msg'=>'操作成功');
            }
            echo json_encode($message);exit();
        }
    }
}