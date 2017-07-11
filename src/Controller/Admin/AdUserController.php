<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2015/10/26
 * Time: 17:19
 */

namespace App\Controller\Admin;


use App\Controller\AdminController;
use App\Controller\OrdersController;
use App\Controller\SobeyController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class AdUserController extends AdminController
{

    public $paginate = [
        'limit' => 15
    ];

    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_tenant_adusers');
        if (! $checkPopedomlist) {
            return $this->redirect('/admin/');
        }
    }

    public function index($code='all',$name = '')
    {
    	$instance_basic = TableRegistry::get('InstanceBasic');
    	//判断登陆用户权限
    	$where_vpc = array();
    	$where_vpc['status']='运行中';
    	$where_vpc['type']='vpc';
     	if(!in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))){
        	$where_vpc['department_id']=$this->request->session()->read('Auth.User.department_id');
        }
    	$vpc_data = $instance_basic->find()->select(['code','name'])->where($where_vpc)->toArray();
    	
    	$this->set('vpc_data',$vpc_data);
        $aduser = TableRegistry::get('AdUser');
        $where = array();
        $vpc_name='全部vpc';
        if($code != 'all'){
        	$where['AdUser.vpcCode'] = $code;
        	$vpc_name = $instance_basic->find()->select(['name'])->where(array('code'=>$code))->toArray()[0]['name'];
        }
        $this->set('vpc_name',$vpc_name);
        if ($name) {
            $where['AdUser.loginName like'] = '%' . $name . '%';
        }
        $data = $aduser->find('all')->where($where)->contain(['Accounts']);
        $data=$this->paginate($data);
        
        $this->set('code',$code);
        $this->set('name',$name);
        $this->set('data',$data);
    }


    //修改桌面账号
    public function addedit($id=0){
        if($this->request->is('get')){
            $aduser = TableRegistry::get('AdUser');
            $data = $aduser->find()->where(array('id'=>$id))->toArray();
            if($data){
                $this->set('data',$data[0]);
            }
        }else{
            $public = new PublicController();
            $message = array('code'=>1,'msg'=>'操作失败');
            $datas = $this->request->data;
            if($datas['loginPassword'] !=''){
                unset($datas['id']);
                $datas['method'] = 'desktop_ad_add';
                $order = new OrdersController();
                $url = Configure::read('URL');
                $result = $order->postInterface($url,$datas);
                if($result['Code']==0){
                    $message = array('code'=>0,'msg'=>'操作成功');
                    $public->adminlog('AdUser','修改桌面账号---'.$datas['loginName'].'的密码');
                }
            }

            echo json_encode($message);exit;
        }
    }


    //删除桌面账号
    public function delete(){
        if ($this->request->is('post')){
            $public = new PublicController();
            $message = array('code'=>1,'msg'=>'操作失败');
            $datas = $this->request->data;
            $datas['method'] = 'desktop_ad_del';
            $order = new OrdersController();
            $url = Configure::read('URL');
            $result = $order->postInterface($url,$datas);
            if($result['Code']==0){
                $message = array('code'=>0,'msg'=>'操作成功');
                $public->adminlog('AdUser','删除桌面账号---'.$datas['loginName']);
            }else{
                $message = array('code'=>1,'msg'=>$result['Message']);
            }
            echo json_encode($message);exit;
        }
    }

}