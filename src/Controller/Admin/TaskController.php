<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/14
 * Time: 14:11
 */

namespace App\Controller\Admin;

use App\Controller\AdminController;
use App\Controller\SobeyController;
use Cake\ORM\TableRegistry;

class TaskController extends AdminController
{
    
    // 分页
    public $paginate = [
        'limit' => 15
    ];

    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_opt_tasks');
        if (! $checkPopedomlist) {
            return $this->redirect('/admin/');
        }
    }
    // 显示任务列表
    public function index()
    {
        $task = TableRegistry::get('Task');
        $task_data = $task->find()
            ->select([
            'task_id',
            'task_type',
            'Task.create_time',
            'Task.begin_time',
            'api_url',
            'status',
            'Accounts.loginname'
        ])
            ->contain([
            'Accounts'
        ])
            ->order([
            'Task.create_time' => 'desc'
        ]);
        // var_dump($task_data[0]['account']->loginname);exit;
        $task_data = $this->paginate($task_data);
        $this->set('data', $task_data);
    }

    public function request(){
       $ids =  $this->request->data['id'];
        foreach($ids as $id){
            if($id !== 'on'){
                var_dump($id);
            }
        }
        exit;
    }

    //删除任务列表
    public function delete(){
        if($this->request->data['id']){
            $public = new PublicController();
            $task = TableRegistry::get('Task');
            $id=$this->request->data['id'];
            $res = $task->deleteAll(array('task_id'=>$id));
            if($res){
                $message = array('code'=>0,'msg'=>'操作成功');
                $public->adminlog('Task','删除任务'.$id);
            }
            echo json_encode($message);exit();
        }
    }
}