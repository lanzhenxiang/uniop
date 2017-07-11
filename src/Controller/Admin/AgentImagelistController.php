<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/8
 * Time: 15:54
 */

namespace App\Controller\Admin;
use App\Controller\AdminController;
use App\Controller\SobeyController;
use Cake\ORM\TableRegistry;

class AgentImagelistController extends AdminController{

    public $paginate = [
    'limit' => 15,
    ];

    public function index(){
        $agentimagelist = TableRegistry::get('AgentImagelist',['classname'=>'App\Model\Table\AgentImagelistTable']);
        $agent = TableRegistry::get('Agent',['classname'=>'App\Model\Table\AgentTable']);
        $agent->behaviors()->SobeyTree->config('scope',['1'=>1]);
        $query = $agent->find('optionList')->select(['id','agent_name','parentid'])->toArray();
        $data = $agentimagelist->find('all')->contain('Agent');
        $data = $this->paginate($data);
        $this->set('data',$data);
    }

    public function addedit($id=0){
        $agent = TableRegistry::get('Agent',['classname'=>'App\Model\Table\AgentTable']);
        if($this->request->is('get')){
            //动态配置behavior
            $agent->behaviors()->SobeyTree->config('scope',['1'=>1]);
            $data = $agent->find('optionList')->select(['id','agent_name','parentid'])->toArray();
            $this->set('query',$data);
            //编辑时
            if($id){
                $agentimagelist = TableRegistry::get('AgentImagelist',['classname'=>'App\Model\Table\AgentImagelistTable']);
                $department_data = $agentimagelist->find('all',array('conditions'=>array('id'=>$id)))->toArray();
                $this->set('department_data',$department_data[0]);
            }
        }else{
            $message = array('code'=>1,'msg'=>'操作失败');
            $agentimagelist = TableRegistry::get('AgentImagelist',['classname'=>'App\Model\Table\AgentImagelistTable']);
            $order = $agentimagelist->newEntity();
            $order = $agentimagelist->patchEntity($order,$this->request->data);
            $result = $agentimagelist->save($order);
            if($result){
                $message = array('code'=>0,'msg'=>'操作成功');
            }
            echo json_encode($message);exit();
        }

    }

    //删除租户
    public function dele(){
        $this->layout = false;
        if($this->request->data['id']){
            $id=$this->request->data['id'];
            $agentimagelist = TableRegistry::get('AgentImagelist',['classname'=>'App\Model\Table\AgentImagelistTable']);
            $res = $agentimagelist->deleteAll(array('id'=>$id));
            if($res){
                $message = array('code'=>0,'msg'=>'操作成功');
            }
            echo json_encode($message);exit();
        }
    }
}