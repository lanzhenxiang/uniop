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

class UserSettingController extends AdminController{

    public $paginate = [
    'limit' => 15,
    ];
    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_para_usersetting');
        if (! $checkPopedomlist) {
            return $this->redirect('/admin/');
        }
    }
    public function index($owner_type=''){
        $Agent = TableRegistry::get('Agent');
        $Accounts = TableRegistry::get('Accounts');
        $Departments = TableRegistry::get('Departments');
        $UserSetting = TableRegistry::get('UserSetting');

        if(strlen($owner_type)>0){
            //根据类型查询参数
            $data = $UserSetting->find('all',array('conditions'=>array('owner_type'=>$owner_type)));
        }else {
            $data =  $UserSetting->find('all');
        }
        $data=$this->paginate($data)->toArray();

        $AgentInfo =  $Agent->find('all')->toArray();
        $AccountsInfo =  $Accounts->find('all')->toArray();
        $DepartmentsInfo =  $Departments->find('all')->toArray();
        $str=array();
        $str='';
        foreach ($data as $key => $value) {
            if ($value['owner_type']==1){
                $i = 0;
                foreach ($AccountsInfo as  $accountinfo) {
                    if ($accountinfo['id']==$value['owner_id']) {
                        $value['owner_name'] = $accountinfo['username'];
                        $str[]= $value;
                        $i++;
                    }
                }
                if($i == 0){
                    $value['owner_name'] = '未知';
                    $str[]= $value;
                }
            }elseif ($value['owner_type']==3) {
                $i = 0;
                foreach ($AgentInfo as  $agentinfo) {
                    if ($agentinfo['id']==$value['owner_id']) {
                        $value['owner_name'] = $agentinfo['agent_name'];
                        $str[]= $value;
                        $i++;
                    }
                }
                if($i == 0){
                    $value['owner_name'] = '未知';
                    $str[]= $value;
                }
            }elseif ($value['owner_type']==2) {
                $i = 0;
                foreach ($DepartmentsInfo as $departmentkey => $departmentinfo) {
                    if ($departmentinfo['id']==$value['owner_id']) {
                        $value['owner_name'] = $departmentinfo['name'];
                        $str[]= $value;
                        $i++;
                    }
                }
                if($i == 0){
                    $value['owner_name'] = '未知';
                    $str[]= $value;
                }
            }else{
                $value['owner_name'] = '_';
                $str[]= $value;
            }
        }

        $this->set('data',$str);
        $this->set('owner_type',$owner_type);
    }

    public function add(){

    }

    public function edit($id){
        $Agent = TableRegistry::get('Agent');
        $Accounts = TableRegistry::get('Accounts');
        $Departments = TableRegistry::get('Departments');
        $UserSetting = TableRegistry::get('UserSetting');
        $data  = $UserSetting->find('all')->where(array('id'=> $id))->toArray();
        if($data[0]['owner_type']==1){
            $query = $Accounts->find('all')->select(['id','username'])->toArray();
        }else if ($data[0]['owner_type']==2) {
            $query = $Departments->find('all')->select(['id','name'])->toArray();
        }else if ($data[0]['owner_type']==3) {
            $query = $Agent->find('all')->select(['id','agent_name'])->toArray();
        }else{      
            $query = null;
        }
        $this->set('data',$data[0]);

    }

    public function addedit($id=0){
        $Agent = TableRegistry::get('Agent');
        $Accounts = TableRegistry::get('Accounts');
        $Departments = TableRegistry::get('Departments');
        $UserSetting = TableRegistry::get('UserSetting');
        if($this->request->is('get')){
            //编辑时
            if($id){
                $usersetting_data  = $UserSetting->find('all')->where(array('id'=> $id))->toArray();
                if($usersetting_data[0]['owner_type']==1){
                    $query = $Accounts->find('all')->select(['id','username'])->toArray();
                }else if ($usersetting_data[0]['owner_type']==2) {
                    $query = $Departments->find('all')->select(['id','name'])->toArray();
                }else if ($usersetting_data[0]['owner_type']==3) {
                    $query = $Agent->find('all')->select(['id','agent_name'])->toArray();
                }else{      
                    $query = null;
                }
                $this->set('query',$query);
                $this->set('data',$usersetting_data[0]);
            }
        }else{
            // var_dump($this->request->data);exit;
            if(empty($this->request->data['id'])){
                $code = $this->request->data['para_code'];
                $type = $this->request->data['owner_type'];
                $owner_id = $this->request->data['owner_id'];
                $checkCode = $UserSetting->find()->where(['para_code' => $code,'owner_type' =>$type,'owner_id' => $owner_id])->first();
                if(!empty($checkCode)){
                     $message = array('code'=>1,'msg'=>'该用户的该数据代码已被使用');
                     echo json_encode($message);exit();
                }
            }
            $message = array('code'=>1,'msg'=>'操作失败');
            $data = $UserSetting->newEntity();
            $data = $UserSetting->patchEntity($data,$this->request->data);
            $result = $UserSetting->save($data);
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
            $UserSetting = TableRegistry::get('UserSetting');
            $res = $UserSetting->deleteAll(array('id'=>$id));
            if($res){
                $message = array('code'=>0,'msg'=>'操作成功');
            }
            echo json_encode($message);exit();
        }

    }

    public function check($id=''){
        $Agent = TableRegistry::get('Agent');
        $Accounts = TableRegistry::get('Accounts');
        $Departments = TableRegistry::get('Departments');

        if($id==1){
            $query['row'] = $Accounts->find('all')->select(['id','username'])->toArray();
        }elseif ($id==2){
            $query['row'] = $Departments->find('all')->select(['id','name'])->toArray();
        }elseif ($id==3){
            $query['row'] = $Agent->find('all')->select(['id','agent_name','display_name'])->toArray();
        }else{
            $query['row'] = null;
        }
        $query['type']=$id;
        echo json_encode($query);exit;
        $this->lauout = 'ajax';
    }


}