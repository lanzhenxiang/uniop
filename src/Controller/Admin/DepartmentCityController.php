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

class DepartmentCityController extends AdminController{

    public $paginate = [
    'limit' => 15,
    ];

    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_para_map');
        if (!$checkPopedomlist) {
            return $this->redirect('/admin/');
        }

    }



    public function index(){
        $departmentCityTable = TableRegistry::get('DepartmentCity');
        $departmentsTable = TableRegistry::get('Departments');
        $where = array();
        $_w = array();

        if (in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))) {
            
            
        } else {
            $where['department_id'] = $this->request->session()->read('Auth.User.department_id');
            $_w['id'] = $this->request->session()->read('Auth.User.department_id');
        }

        $departments = $departmentsTable->find()->where($_w)->toArray();
        
        $data = $departmentCityTable->find()->contain(['Departments'])->where($where);
        $data=$this->paginate($data);
        $this->set('data',$data);
        $this->set('departments',$departments);
    }


    public function addedit($id=0){

        $public = new PublicController();

        $departmentsTable = TableRegistry::get('Departments');
        $departmentCityTable = TableRegistry::get('DepartmentCity');
        if($this->request->is('get')){
            $_w = array();
            if (!in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))) {
                $_w['id'] = $this->request->session()->read('Auth.User.department_id');
            }
            $data['departments'] = $departmentsTable->find()->where($_w)->toArray();
            $this->set('query',$data);
             //编辑时
            if($id){
                $department_data = $departmentCityTable->find()->where(['id'=>$id])->toArray();
                $this->set('department_data',$department_data[0]);
            }
        }else{
            
            // if(empty($this->request->data['id'])&& !empty($this->request->data['city_name'])){
            //     $checkCode = $departmentCityTable->find()->where(['region_code' => $code])->first();
            //     if(!empty($checkCode)){
            //         $message = array('code'=>1,'msg'=>'已存在该地域Code');
            //         echo json_encode($message);exit();
            //     }
            // }
           
            $order = $departmentCityTable->newEntity();

            //编辑厂商
            $order = $departmentCityTable->patchEntity($order,$this->request->data);
            $result = $departmentCityTable->save($order);
            $message = array('code'=>1,'msg'=>'操作失败');
            if($result){
                if(empty($this->request->data['id'])){
                    $public->adminlog('DepartmentCity','添加厂商地点---'.$this->request->data['city_name']);
                }else{
                    $public->adminlog('DepartmentCity','修改厂商地点---'.$this->request->data['city_name']);
                }
                $message = array('code'=>0,'msg'=>'操作成功');
            }
            echo json_encode($message);exit();
        }
    }

    //删除
    public function delete(){
        $this->layout = false;
        $public = new PublicController();
        $message = array('code'=>1,'msg'=>'操作失败');
        if($this->request->data['id']){
            $id=$this->request->data['id'];
            $departmentCityTable = TableRegistry::get('DepartmentCity');

            $data = $departmentCityTable->find()->where(['id' => $id])->toArray();
           
            $res = $departmentCityTable->deleteAll(array('id'=>$id));
            $message = array('code'=>1,'msg'=>'操作失败');
                if($res){
                //     $public->adminlog('Agent','删除厂商或者区域---'.$data['display_name']);
                    $message = array('code'=>0,'msg'=>'操作成功');
                }
            echo json_encode($message);exit;
            

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