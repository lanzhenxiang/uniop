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

class SystemsettingController extends AdminController{

    public $paginate = [
    'limit' => 15,
    ];
    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_para_systemsetting');
        if (! $checkPopedomlist) {
            return $this->redirect('/admin/');
        }
    }
    public function index($name=''){

        $Systemsetting = TableRegistry::get('Systemsetting');

        $where = array();
        $where['para_type'] = 0;//只显示系统类型
        if($name != ""){
            $where['para_code like'] = '%'.$name.'%';
        }

        $data = $Systemsetting->find()->where($where);
        
        $data=$this->paginate($data);
        $this->set('data',$data);
        $this->set('name',$name);
    }

    public function addedit($id=0,$para_type=0){
        $public = new PublicController();
        $Systemsetting = TableRegistry::get('Systemsetting');
        if($this->request->is('get')){
            $this->set('para_type',$para_type);
             //编辑时
            if($id){
                $department_data  = $Systemsetting->find('all')->where(array('id'=> $id))->toArray();
                $this->set('data',$department_data[0]);
            }
        }else{
            // var_dump($this->request->data);exit;
            if(empty($this->request->data['id'])){
                $code = $this->request->data['para_code'];
                $type = $this->request->data['para_type'];
                $checkCode = $Systemsetting->find()->where(['para_code' => $code,'para_type' =>$type])->first();
                if(!empty($checkCode)){
                     $message = array('code'=>1,'msg'=>'该参数类型的数据代码已被使用');
                     echo json_encode($message);exit();
                }
            }
            $message = array('code'=>1,'msg'=>'操作失败');
            $data = $Systemsetting->newEntity();
            $data = $Systemsetting->patchEntity($data,$this->request->data);
            $result = $Systemsetting->save($data);
            if($result){
                $message = array('code'=>0,'msg'=>'操作成功');
                if(empty($this->request->data['id'])){
                    $public->adminlog('Systemsetting','添加系统参数---'.$this->request->data['para_code']);
                }else{
                    $public->adminlog('Systemsetting','修改系统参数---'.$this->request->data['para_code']);
                }
            }
            echo json_encode($message);exit();
        }
    }

    //删除
    public function dele(){
        $public = new PublicController();
        $this->layout = false;
        if($this->request->data['id']){
            $id=$this->request->data['id'];
            $Systemsetting = TableRegistry::get('Systemsetting');

            $data = $Systemsetting->find()->where(['id'=>$id])->first();
            $res = $Systemsetting->deleteAll(array('id'=>$id));
            if($res){
                $message = array('code'=>0,'msg'=>'操作成功');
                $public->adminlog('Systemsetting','删除系统参数---'.$data['para_code']);
            }
            echo json_encode($message);exit();
        }

    }


}