<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/10
 * Time: 11:17
 */

namespace App\Controller\Admin;


use App\Controller\AdminController;
use App\Controller\SobeyController;
use Cake\ORM\TableRegistry;

class TenantsController extends AdminController{
    public $paginate = [
        'limit' => 15,
    ];
    public function index(){
        $tenants = TableRegistry::get('Tenants');
        $data = $tenants->find('all')->contain(['Accounts']);
        $data=$this->paginate($data);
        $this->set('data',$data);
    }

    public function addedit($id=0){
        $tenants = TableRegistry::get('Tenants');
        if($this->request->is('get')){
            if($id){
                $tenant_result = $tenants->find('all')->where(array('id'=>$id))->toArray();
                $this->set('data',$tenant_result[0]);
            }
        }else{
            $message = array('code'=>0,'msg'=>'操作失败');
            $tenant=$tenants->newEntity();
            if(isset($this->request->data['id'])){
                if($this->request->data['password']==''){
                    unset($this->request->data['password']);
                }
                $name = $tenants->find('all')->select(['id','name'])->where(array('name'=>$this->request->data['name']))->toArray();
                //var_dump($name);exit;
                //判断修改的名字是否已经存在
                if($name){
                    foreach($name as $va){
                        if($va['id']!=$this->request->data['id'] && $va['name'] == $this->request->data['name']){
                            $message = array('code'=>1,'msg'=>'该名字已存在');
                            echo json_encode($message);exit;
                        }
                    }
                }
                $tid = $this->request->data['id'];
                $this->request->data['modify_time']=time();
                $t_result = $tenants->updateAll($this->request->data,array('id'=>$tid));
                if($t_result){
                    $message = array('code'=>0,'msg'=>'操作成功');
                }
                echo json_encode($message);exit;
            }else{
                $count = $tenants->find('all')->select(['id'])->where(array('name'=>$this->request->data['name']))->count();
                //判断名字是否已存在
                if($count>0){
                    $message = array('code'=>1,'msg'=>'该名字已存在');
                    echo json_encode($message);exit;
                }
                $this->request->data['create_by']='';
                $this->request->data['create_time']=time();
                $this->request->data['modify_time']=time();
                $this->request->data['access_key']='';
                $tenant = $tenants->patchEntity($tenant,$this->request->data);
                $result = $tenants->save($tenant);
                if($result){
                    $message = array('code'=>0,'msg'=>'操作成功');
                }
                echo json_encode($message);exit;

            }
        }
    }

    //删除租户
    public function delete(){
        if($this->request->data['id']){
            $message = array('code'=>1,'msg'=>'操作失败');
            $tenants = TableRegistry::get('Tenants');
            $id=$this->request->data['id'];
            $res = $tenants->deleteAll(array('id'=>$id));
            if($res){
                $message = array('code'=>0,'msg'=>'操作成功');
            }
            echo json_encode($message);exit();
        }
    }

}