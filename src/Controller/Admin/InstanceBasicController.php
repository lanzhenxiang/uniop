<?php
/** 
 * 文件描述文字
 * 
 * 
 * @author XingShanghe<xingshanghe@gmail.com>
 * @date  2015年9月8日下午3:39:25
 * @source AccountsController.php
 * @version 1.0.0 
 * @copyright  Copyright 2015 sobey.com 
 */
namespace App\Controller\Admin;

use App\Controller\AdminController;
use App\Controller\SobeyController;
use Cake\ORM\TableRegistry;
use Composer\Autoload\ClassLoader;
use App\Controller\OrdersController;
use Cake\Core\Configure;

class InstanceBasicController extends AdminController
{

    public $paginate = [
    'limit' => 15
    ];
    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow('image');
        $checkPopedomlist = parent::checkPopedomlist('bgm_service_host');
        if (! $checkPopedomlist) {
            return $this->redirect('/admin/');
        }
    }

    public function index($name=''){
        $instance_basic = TableRegistry::get('InstanceBasic');
        $where=array();
        if($name){
            $where['OR'] =array('InstanceBasic.name like'=>"%$name%",'InstanceBasic.code like'=>"%$name%");
        }
        $data = $instance_basic->find('all')->where(['OR' =>[['InstanceBasic.type'=>'hosts'],['InstanceBasic.type' => 'desktop']]])->where($where)->contain([
            'HostExtend',
            'Agent'
            ]);
        $data = $this->paginate($data);
        $this->set('name',$name);
        $this->set('data',$data);
    }

    public function addedit($id=0){
        $instance_basic = TableRegistry::get('InstanceBasic');
        $host_extend = TableRegistry::get('HostExtend');
        $department = TableRegistry::get('Departments');
        $agent = TableRegistry::get('Agent');
        $data['department_info'] = $department->find('all')->toArray();
        $data['agent_info'] = $agent->find('all')->where(['parentid <>' => '0'])->toArray();
        $this->set('data',$data);
        if($this->request->is('get')){
            //编辑时
            if($id){
                $department_data = $instance_basic->find('all')->where(['InstanceBasic.id'=>$id])->contain(['HostExtend','Agent']);
                $this->set('department_data',$department_data[0]);
            }
        }else{
            // var_dump($this->request->data);exit;
            if(empty($this->request->data['id'])){
                $code = $this->request->data['image_code'];
                $checkCode = $imagelist->find()->where(['image_code' => $code])->first();
                if(!empty($checkCode)){
                    $message = array('code'=>1,'msg'=>'该镜像Code已被使用');
                    echo json_encode($message);exit();
                }
            }
            $message = array('code'=>1,'msg'=>'操作失败');
            $order = $imagelist->newEntity();
            $order = $imagelist->patchEntity($order,$this->request->data);
            $result = $imagelist->save($order);
            if($result){
                $message = array('code'=>0,'msg'=>'操作成功');
                if(empty($this->request->data['id'])){
                    $public->adminlog('ServiceRules','添加弹性规则---'.$this->request->data['type_id'].'-'.$this->request->data['rule_expression']);
                }else{
                    $public->adminlog('ServiceRules','修改弹性规则---'.$this->request->data['type_id'].'-'.$this->request->data['rule_expression']);
                }

            }
            echo json_encode($message);exit();
        }

    }

    public function editip($id = 0){
        $public = new PublicController();
        $instance_basic = TableRegistry::get('InstanceBasic');
        $host_extend = TableRegistry::get('HostExtend');
        $agent = TableRegistry::get('Agent');
        $data['agent_info'] = $agent->find('all')->where(['parentid <>' => '0'])->toArray();
        $this->set('data',$data);
        if($this->request->is('get')){
            //编辑时
            if($id){
                $department_data = $instance_basic->find('all')->where(['InstanceBasic.id'=>$id])->contain(['HostExtend'])->toArray();
                // var_dump($department_data[0]);exit;
                $this->set('department_data',$department_data[0]);
            }
        }else{
            // var_dump($this->request->data);exit;
            $message = array('code'=>1,'msg'=>'操作失败');
            $host_extend_data['id'] = $this->request->data['id'];
            $host_extend_data['cpu'] = $this->request->data['cpu'];
            $host_extend_data['memory'] = $this->request->data['memory'];
            $host_extend_data['gpu'] = $this->request->data['gpu'];
            $host_extend_data['plat_form'] = $this->request->data['plat_form'];
            $host_extend_data['os_family'] = $this->request->data['os_family'];
            $host_extend_data['ip'] = $this->request->data['ip'];
            $host_extend_data['basic_id'] = $this->request->data['basic_id'];
            $host_data['id']= $this->request->data['basic_id'];
            $host_data['location_code']= $this->request->data['location_code'];
            $host_data['location_name']= $this->request->data['location_name'];

            $host = $instance_basic->newEntity();
            $host = $instance_basic->patchEntity($host,$host_data);
            $result = $instance_basic->save($host);
            $order = $host_extend->newEntity();
            $order = $host_extend->patchEntity($order,$host_extend_data);
            $result = $host_extend->save($order);
            
            if($result){
                $message = array('code'=>0,'msg'=>'操作成功');
                $public->adminlog('InstanceBasic','修改主机信息---'.$this->request->data['code']);
            }
            echo json_encode($message);exit();
        }
    }
    //删除
    public function dele(){
        $public = new PublicController();
        $this->layout = false;
        $message = array('code'=>1,'msg'=>'删除失败');
        $order = new OrdersController();
        $url = Configure::read('URL');
        if($this->request->data['id']){
            $id=$this->request->data['id'];
            $instance_basic = TableRegistry::get('InstanceBasic');
            $instance_basic_info = $instance_basic->find()->select(['id','code'])->where(['id'=>$id])->first();
            $parameter['method'] = 'ecs_delete';
            $parameter['uid'] = $this->request->session()->read('Auth.User.id') ? (string) $this->request->session()->read('Auth.User.id') : (string) 0;
            if(!empty($instance_basic_info['code'])){
                $parameter['instanceCode'] = $instance_basic_info['code'];
            }else{
                $parameter['instanceCode'] = '';
            }
            $parameter['basicId'] = (string)$id;
            $request = $order->postInterface($url, $parameter);
            if ($request['Code'] == 0) {
                $message = array('code'=>0,'msg'=>'操作成功');
                $public->adminlog('InstanceBasic','删除主机信息---'.$instance_basic_info['code']);
            }else{
                $message = array('code'=>1,'msg'=>$request['Message']);
            }
            echo json_encode($message);exit();
        }
    }
}