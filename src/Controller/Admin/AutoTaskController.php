<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2016/1/27
 * Time: 11:29
 */

namespace App\Controller\Admin;


use App\Controller\AdminController;
use Cake\ORM\TableRegistry;

class AutoTaskController extends AdminController{
    public $paginate = [
        'limit' => 15,
    ];

    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_service_auto_task');
        if (! $checkPopedomlist) {
            return $this->redirect('/admin/');
        }
    }

    public function index($name=''){
        $auto_task= TableRegistry::get('AutoTask');
        $where = array();
        if($name){
            $where['task_name like'] ="%$name%";
        }
        $data = $auto_task->find('all')->where($where);
        $data=$this->paginate($data);
        $this->set('name',$name);
        $this->set('data',$data);
    }


    //查看详细信息
    public function check($id=0){
        $auto_task= TableRegistry::get('AutoTask');
        if($id){
            $task_result = $auto_task->find('all')->where(array('id'=>$id))->toArray();
            $this->set('data',$task_result[0]);
        }

    }

    public function addedit($id=0,$source=''){
        $auto_task = TableRegistry::get('AutoTask');
        if($this->request->is('get')){
            if($id){
                $task_result = $auto_task->find('all')->where(array('id'=>$id))->toArray();
                $this->set('data',$task_result[0]);
            }
            if(!empty($source)){
                $this->set('source',$source);
            }
        }else{
            if(!strtotime($this->request->data['end_time']) || strtotime($this->request->data['end_time']) > '2177423999'){
                $message = array('code'=>1,'msg'=>'结束时间超出最大范围值');
                echo json_encode($message);exit;
            }
            if(!strtotime($this->request->data['next_begin_time']) || strtotime($this->request->data['next_begin_time']) > '2177423999'){
                $message = array('code'=>1,'msg'=>'开始时间超出最大范围值');
                echo json_encode($message);exit;
            }
            if(strtotime($this->request->data['end_time'])<strtotime($this->request->data['next_begin_time'])){
                $message = array('code'=>1,'msg'=>'结束时间不能小于开始时间');
                echo json_encode($message);exit;
            }
            $public = new PublicController();
            $autotask_data =$this->request->data;
            $autotask_data['next_begin_time']=strtotime($this->request->data['next_begin_time']);
            $autotask_data['begin_time']=strtotime($this->request->data['next_begin_time']);
            $autotask_data['end_time']=strtotime($this->request->data['end_time']);

            if(!empty($autotask_data['dura_time'])){
                $autotask_data['dura_time']=$this->request->data['dura_time']*60;
            }
            if($autotask_data['task_type']==1){
                $autotask_data['planed_day']='';
                $autotask_data['dura_time']='';
                $autotask_data['task_interval']='';
            }elseif($autotask_data['task_type']==4){
                $autotask_data['planed_day']='';
                $autotask_data['dura_time']='';
            }
            if(isset($this->request->data['id'])){
                $name = $auto_task->find('all')->select(['id','task_name'])->where(array('task_name'=>$this->request->data['task_name']))->toArray();
                //判断修改的名字是否已经存在
                if($name){
                    foreach($name as $va){
                        if($va['id']!=$this->request->data['id'] && $va['task_name'] == $this->request->data['task_name']){
                            $message = array('code'=>1,'msg'=>'该自动任务名称已存在');
                            echo json_encode($message);exit;
                        }
                    }
                }
                $source=$this->request->data['source'];
                unset($this->request->data['source']);
                //var_dump($autotask_data);exit;
                $tasks=$auto_task->newEntity();
                $tasks = $auto_task->patchEntity($tasks,$autotask_data);
                if($autotask_data['task_type']==1){
                    $tasks['planed_day']='';
                    $tasks['dura_time']='';
                    $tasks['task_interval']='';
                }elseif($autotask_data['task_type']==4){
                    $tasks['planed_day']='';
                    $tasks['dura_time']='';
                }
                $t_result = $auto_task->save($tasks);
                if($t_result){
                    if($source=='check'){
                        $message = array('code'=>0,'msg'=>'操作成功','source'=>1,'id'=>$this->request->data['id']);
                    }else{
                        $message = array('code'=>0,'msg'=>'操作成功','source'=>0);
                    }
                    $public->adminlog('AutoTask','修改服务类型---'.$this->request->data['task_name']);
                }
                echo json_encode($message);exit;
            }else{
                $count = $auto_task->find('all')->select(['id'])->where(array('task_name'=>$autotask_data['task_name']))->count();
                //判断名字是否已存在
                if($count>0){
                    $message = array('code'=>1,'msg'=>'该自动任务名称已存在');
                    echo json_encode($message);exit;
                }
                unset($autotask_data['source']);
                $tasks=$auto_task->newEntity();
                $tasks = $auto_task->patchEntity($tasks,$autotask_data);
                $result = $auto_task->save($tasks);
                if($result){
                    $message = array('code'=>0,'msg'=>'操作成功');
                    $public->adminlog('AutoTask','添加服务类型---'.$this->request->data['task_name']);
                }
                echo json_encode($message);exit;

            }
        }
    }


    //删除自动计划任务
    public function deletes(){
        $message=array('code'=>1,'message'=>'删除自动计划任务失败');
        $data = $this->request->data;
        $auto_task = TableRegistry::get('AutoTask');
        $name = $auto_task->find()->select(['task_name'])->where(array('id'=>$data['id']))->toArray();
        $result = $auto_task->deleteAll(array('id'=>$data['id']));
        if($result){
            $public = new PublicController();
            $message=array('code'=>0,'message'=>'删除自动计划任务成功');
            $public->adminlog('AutoTask','删除服务类型---'.$name[0]['task_name']);
        }
        echo json_encode($message);exit();

    }
}