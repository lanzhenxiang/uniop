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

class AgentSetController extends AdminController{

    public $paginate = [
    'limit' => 15,
    ];

    public function index(){
        $agent = TableRegistry::get('AgentSet',['classname'=>'App\Model\Table\AgentSetTable']);
        $data = $agent->find('all')->contain('Agent');
        $data = $this->paginate($data); 
        $this->set('data',$data);
    }

    public function addedit($id=0){
        $agent = TableRegistry::get('Agent',['classname'=>'App\Model\Table\AgentTable']);
        $agentset = TableRegistry::get('AgentSet',['classname'=>'App\Model\Table\AgentSetTable']);
        if($this->request->is('get')){
            $query  = $agent->find('all')->where(['parentid ='=>0])->select(['id','agent_name'])->toArray();
            $this->set('query',$query);
             //编辑时
            if($id){
                $department_data = $agentset->find('all',array('conditions'=>array('id'=>$id)))->toArray();
                $this->set('department_data',$department_data[0]);
            }
        }else{
            $message = array('code'=>1,'msg'=>'操作失败');
            $data = $agentset->newEntity();
            $data = $agentset->patchEntity($data,$this->request->data);
            $result = $agentset->save($data);
            if($result){
                $message = array('code'=>0,'msg'=>'操作成功');
            }
            echo json_encode($message);exit();
        }

    }

    //删除
    public function dele(){
        $this->layout = false;
        if($this->request->data['id']){
            $id=$this->request->data['id'];
            $agentset = TableRegistry::get('AgentSet',['classname'=>'App\Model\Table\AgentSetTable']);
            
            $res = $agentset->deleteAll(array('id'=>$id));
            if($res){
                $message = array('code'=>0,'msg'=>'操作成功');
            }
            echo json_encode($message);exit();

        }
    }
}