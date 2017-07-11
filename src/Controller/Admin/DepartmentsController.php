<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/8
 * Time: 15:54
 */

namespace App\Controller\Admin;
use App\Controller\AdminController;
use App\Controller\OrdersController;
use App\Controller\SobeyController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Controller\Controller;

class DepartmentsController extends AdminController{

    public $paginate = [
    'limit' => 15,
    ];

    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_tenant_tenants');
        if (!$checkPopedomlist) {
            return $this->redirect('/');
        }

    }



    public function index($name=''){
       /* if(in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))){*/
            $departments = TableRegistry::get('Departments');
            $where = array();
            if($name){
                $where['OR'] =array('name like'=>"%$name%",'dept_code like'=>"%$name%");
            }
            $data = $departments->find('all')->where($where)->contain(['Accounts']);
            $data=$this->paginate($data);
            $this->set('name',$name);
            $this->set('data',$data);
        /*}else{
            $datas='';
            //$datas=$this->paginate($datas);
            $this->set('name',$name);
            $this->set('data',$datas);
        }*/

    }


    //添加租户
    public function add(){
        $checkPopedomlist_tenant = parent::checkPopedomlist('cmop_global_tenant_admin');
        $checkPopedomlist_sys = parent::checkPopedomlist('cmop_global_sys_admin');
        if ($checkPopedomlist_tenant) {
            if (!$checkPopedomlist_sys) {
                return $this->redirect('/admin/departments');
            }
        }
        $systemsetting = TableRegistry::get('Systemsetting');
        $para = $systemsetting->find()->select(['id', 'para_code', 'para_value', 'para_note'])->where(array('para_type' => 1))->toArray();
        $this->set('para', $para);
    }

    public function postadd(){
        $departments = TableRegistry::get('Departments');
        $public = new PublicController();
        if(empty($this->request->data['id'])){
            $code = $this->request->data['dept_code'];
            $checkCode = $departments->find()->where(['dept_code' => $code])->first();
            if(!empty($checkCode)){
                $message = array('code'=>1,'msg'=>'该租户Code被'.$checkCode->name.'使用');
                echo json_encode($message);exit();
            }
        }
        $message = array('code'=>1,'msg'=>'添加失败');
        $datas = $this->request->data;
        $name_count = $departments->find()->select(['name'])->where(array('name'=>$datas['name']))->count();
        if($name_count>0){
            $message = array('code'=>1,'msg'=>'该租户名称已存在');
            echo json_encode($message);exit;
        }
        $parameter["dept_code"]=$datas["dept_code"];
        $parameter['method'] = 'tenants_add';
        $parameter['email']=$datas['email'];
        $parameter['name']=$datas['name'];
        $parameter['parent_id']='0';
        $parameter['sort_order']='0';
        $parameter['uid'] = (string) $this->request->session()->read('Auth.User.id');
        $parameter['type']=$datas['type'];
        $order = new OrdersController();
        $url = Configure::read('URL');
        $IN_result = $order->postInterface($url,$parameter);
        if($IN_result['Code']==0){
            if(!empty($datas))
            {
                $bugedt = $datas;
                unset($bugedt['name']);
                unset($bugedt['dept_code']);
                unset($bugedt['email']);
                unset($bugedt['para_id']);
                unset($bugedt['type']);
                $datas_count = count($bugedt);
                $i=0;
                $user = array();
                $user['owner_id']=$IN_result["Data"]["id"];
                $user['owner_type'] = 2;
                $user['para_value1'] = '';
                $usersetting = TableRegistry::get('UserSetting');
                foreach($bugedt as $key =>$value){
                    $user['para_code']=$key;
                    foreach ($value as $k=>$v) {
                        $user[$k]=$v;
                    }
                    $seting = $usersetting->newEntity();
                    $seting = $usersetting->patchEntity($seting,$user);
                    $result = $usersetting->save($seting);
                    if($result){
                        $i++;
                    }
                }

                if($datas_count == $i){
                    $message = array('code'=>0,'msg'=>'添加成功');
                    $public->adminlog('Departments','添加租户---'.$datas['name'].'的基本信息及租户参数');
                }else{
                    $public->adminlog('Departments','添加租户---'.$datas['name'].'的基本信息');
                }

            }else{
                $message = array('code'=>0,'msg'=>'添加成功');
                $public->adminlog('Departments','添加租户---'.$datas['name'].'的基本信息');
            }
        }else{
            $message = array('code'=>1,'msg'=>$IN_result['Message']);
        }
        echo json_encode($message);exit;
    }


    public function edit($id){
        $checkPopedomlist_tenant = parent::checkPopedomlist('cmop_global_tenant_admin');
        $checkPopedomlist_sys = parent::checkPopedomlist('cmop_global_sys_admin');
        if ($checkPopedomlist_tenant) {
            if (!$checkPopedomlist_sys) {
                return $this->redirect('/admin/departments');
            }
        }
        $departments = TableRegistry::get('Departments');
        $depart_data = $departments->find()->select(['name', 'id', 'sort_order', 'parent_id', 'dept_code', 'email', 'type'])->where(array('id' => $id))->toArray();
        $systemsetting = TableRegistry::get('Systemsetting');
        $para = $systemsetting->find()->select(['id', 'para_code', 'para_value', 'para_note'])->where(array('para_type' => 1))->toArray();
        $this->set('para', $para);
        $this->set('parent_depart', $depart_data[0]);
        $usersetting = TableRegistry::get('UserSetting');
        $user_data = $usersetting->find('all')->where(array('owner_type' => 2, 'owner_id' => $id))->toArray();
        foreach ($user_data as $key => $value) {
            $para_code[] = $value['para_code'];
            $this->set('para_code', $para_code);
        }
        if ($user_data) {
            $this->set('user_data', $user_data);
        }
    }


    public function postedit(){
        $message = array('code'=>1,'msg'=>'修改失败');
        $public = new PublicController();
        $departments = TableRegistry::get('Departments');
        $checkCode = $departments->find()->where(array('dept_code' => $this->request->data['dept_code'],'id <>'=>$this->request->data['id']))->count();
        if($checkCode>0){
            $message = array('code'=>1,'msg'=>'该租户Code已被使用');
            echo json_encode($message);exit();
        }
        $data = $this->request->data;
        $basic['name'] = $data['name'];
        $basic['dept_code'] = $data['dept_code'];
        $basic['email'] = $data['email'];
       /* $basic['parent_id'] = $data['parent_id'];*/
        // $basic['sort_order'] = $data['sort_order'];
        $id=$data['id'];
        //$basic['create_by']= $this->request->session()->read('Auth.User.id');
        $basic['modify_time'] = time();
        unset($data['name']);
        unset($data['dept_code']);
        unset($data['email']);
        // unset($data['sort_order']);
        unset($data['id']);
        unset($data['para_id']);
        $name_count = $departments->find()->select(['name'])->where(array('name'=>$basic['name'],'id <>'=>$id))->count();
        if($name_count>0){
            $message = array('code'=>1,'msg'=>'该租户名称已存在');
        }else {
            $usersetting = TableRegistry::get('UserSetting');
            $depart_result = $departments->updateAll($basic, array('id' => $id));
            if ($depart_result) {
                $usersetting->deleteAll(array('owner_id'=>$id));
                if(!empty($data)){
                    $datas_count = count($data);
                    $i=0;
                    $user = array();
                    $user['owner_id']=$id;
                    $user['owner_type'] = 2;
                    $user['para_value1'] = '';
                    foreach($data as $key =>$value){
                        $user['para_code']=$key;
                        foreach ($value as $k=>$v) {
                            $user[$k]=$v;
                        }
                        $seting = $usersetting->newEntity();
                        $seting = $usersetting->patchEntity($seting,$user);
                        $result = $usersetting->save($seting);
                        if($result){
                            $i++;
                        }
                    }
                    if($datas_count == $i){
                        $public->adminlog('Departments','修改租户---'.$basic['name'].'的基本信息及租户参数');
                        $message = array('code'=>0,'msg'=>'修改成功');
                    }else{
                        $public->adminlog('Departments','修改租户---'.$basic['name'].'的基本信息');
                    }

                }else{
                    $message = array('code'=>0,'msg'=>'修改成功');
                    $public->adminlog('Departments','修改租户---'.$basic['name'].'的基本信息');
                }
            }
        }

        echo json_encode($message);exit;

    }

    //删除租户
    public function delete(){
        if($this->request->data['id']){
            $public = new PublicController();
            $message = array('code'=>1,'msg'=>'删除失败');
            $id=$this->request->data['id'];
            $departments = TableRegistry::get('Departments');
            $name= $departments->find()->select(['name'])->where(array('id'=>$id))->toArray();
            $account = TableRegistry::get('Accounts');
            $accounts = $account->find()->select('id')->where(array('department_id'=>$id))->count();
            $instance_basic = TableRegistry::get('InstanceBasic');
            $count = $instance_basic->find()->select(['id'])->where(array('department_id'=>$id))->count();
            //判断是否存在资源
            if($count > 0){
                $message = array('code'=>1,'msg'=>'该租户下存在资源，不能删除');
            }elseif($accounts>0){
                $message = array('code'=>1,'msg'=>'该租户下存在人员，不能删除');
            }else{
                $parameter['method'] = 'tenants_del';
                $parameter['department_id'] = $id;
                $parameter['uid'] = (string) $this->request->session()->read('Auth.User.id');
                $order = new OrdersController();
                $url = Configure::read('URL');
                $IN_result = $order->postInterface($url, $parameter);
                if($IN_result['Code']==0) {
                    $usersetting = TableRegistry::get('UserSetting');
                    $count = $usersetting->find()->select(['id'])->where(array('owner_id' => $id))->count();
                    $user_result = $usersetting->deleteAll(array('owner_id' => $id));
                    if ($count == $user_result) {
                        $message = array('code' => 0, 'msg' => '删除成功');
                        $public->adminlog('Departments','删除租户---'.$name[0]['name'].'及租户参数');
                    }
                }else{
                    $departments->deleteAll(array('id' => $id));
                    $message = array('code'=>0,'msg'=>$IN_result['Message']);
                }
            }
            echo json_encode($message);exit();
        }
    }


    public function check($data,$id){

        $departments = TableRegistry::get('Departments');
        $sun = $departments->find('all')->select(['id'])->where(array('parent_id'=>$id))->toArray();
        if(!empty($sun)){
            foreach($sun as $va){
                $data[]=$va['id'];
                $data =$this->check($data,$va['id']);
            }
        }
        return $data;
    }

    public function account_department(){
        $departments = TableRegistry::get('Departments');
        //动态配置behavior
        $departments->behaviors()->Departments->config('scope',['1'=>1]);
        $data = $departments->find('optionList')->select(['id','name','parent_id'])->toArray();
        return $data;
    }
}