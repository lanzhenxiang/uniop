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
use App\Controller\OrdersController;
use Cake\Core\Configure;

class SetHardwareController extends AdminController{

    public $paginate = [
    'limit' => 15,
    ];
    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_para_hardware_set');
        if (! $checkPopedomlist) {
            return $this->redirect('/admin/');
        }
    }
    public function index($name=''){
        $agent = TableRegistry::get('SetHardware');
        $where = array();
        if($name){
            $where['OR'] =array('set_name like'=>"%$name%",'set_code like'=>"%$name%",'provider like'=>"%$name%");
        }
        $data = $agent->find('all')->contain('Accounts')->where($where);
        $data=$this->paginate($data);
        $this->set('name',$name);
        $this->set('data',$data);
    }

    public function lists($name =''){
        $request=$this->request->query;
        $this->autoRender = false;
        $agent = TableRegistry::get('SetHardware');
        $where = array();
        if($name){
            $where['OR'] =array('set_name like'=>"%$name%",'set_code like'=>"%$name%",'provider like'=>"%$name%");
        }
        $data = $agent->find('all')->contain('Accounts')->where($where)->offset($request['offset'])->limit($request['limit']);
        $json = array(
            'total'=>$agent->find('all')->contain('Accounts')->where($where)->count(),
            'rows'=>$data,
        );
        echo json_encode($json);
    }


    public function addedit($id=0){
        $public = new PublicController();
        $SetHardware = TableRegistry::get('SetHardware');


        $departments = TableRegistry::get('Departments');
        $charge_template = TableRegistry::get('ChargeTemplate');
        if($this->request->is('get')){
             //编辑时
            $department = $departments->find('all')->select(['id','name'])->toArray();
            $this->set('dept',$department);
            $template = $charge_template->find('all')->select(['id','template_name'])->toArray();
            $this->set('template',$template);

            if($id){
                $department_data = $SetHardware->find('all',array('conditions'=>array('set_id'=>$id)))->toArray();
                $this->set('department_data',$department_data[0]);
            }
        }else{
            // var_dump($this->request->data);exit;
            if(empty($this->request->data['set_id'])){
                $code = $this->request->data['set_code'];
                $checkCode = $SetHardware->find()->where(['set_code' => $code])->first();
                if(!empty($checkCode)){
                     $message = array('code'=>1,'msg'=>'已存在该计算能力Code');
                     echo json_encode($message);exit();
                }
            }
            $message = array('code'=>1,'msg'=>'操作失败');

            $this->request->data['create_time'] = time();

            if(empty($this->request->data['set_id'])) {
                $this->request->data['create_by']   = $this->request->session()->read('Auth.User.id');
            }

            $data = $SetHardware->newEntity();
            $data = $SetHardware->patchEntity($data,$this->request->data);
            $url=Configure::read('URL');
            $info = $this->request->data;
            $parameter['name'] = $info['set_name'];
            $parameter['instanceTypeCode'] = $info['set_code'];
            $parameter['cpuNumber'] = $info['cpu_number'];
            $parameter['memoryGB'] = $info['memory_gb'];
            $parameter['gpuMB'] = $info['gpu_gb'];
            $parameter['method'] = 'instanceType_edit';
            $parameter['uid'] = $this->request->session()->read('Auth.User.id') ? (string)$this->request->session()->read('Auth.User.id'):(string)0;
            unset($info['cpu_number']);
            unset($info['memory_gb']);
            unset($info['gpu_gb']);
            unset($info['set_code']);
            $order =new OrdersController();
            $re_code = array();
            $re_code['Code']=0;
          //  $re_code=$order->postInterface($url,$parameter);//调用接口
            //if($re_code['Code']!=0){
              //  $message = array('code'=>1,'msg'=>$re_code['Message']);
              //   echo json_encode($message);exit;
          //  }
          //  
            if($data['set_type'] =="云桌面"){
                $data['price_day'] = 0;
                $data['price_month'] = 0;
                $data['price_year'] = 0;
            }  
          
            if(!empty($this->request->data['set_id'])){
                if($re_code['Code']==0){
                    $result = $SetHardware->save($data);
                    $message = array('code'=>0,'msg'=>'操作成功');
                    $public->adminlog('计算能力','修改计算能力---'.$this->request->data['set_code']);
                }else{
                    $message = array('code'=>1,'msg'=>$re_code['Message']);
                }
            }else{
                $result = $SetHardware->save($data);
                if($result){
                    $message = array('code'=>0,'msg'=>'操作成功');
                    $public->adminlog('计算能力','添加计算能力---'.$this->request->data['set_code']);
                }
            }
            echo json_encode($message);exit();
        }
    }

    /**
     * 修改计算能力的类型时候，判断是否允许修改
     * @return boolean [description]
     */
    public function isAllowEdit(){
        if(isset($this->request->data['set_id'])){
            $set_id   = $this->request->data['set_id'];
            $set_type = $this->request->data['set_type'];
            $set_code = $this->request->data['set_code'];
            $agent_set      = TableRegistry::get('AgentSet');
            $set_hardware   = TableRegistry::get('SetHardware');
            $spec_table = TableRegistry::get('GoodsVersionSpec');
            
            $hardware = $set_hardware->find()->where(['set_id'=> $set_id,'set_type'=> $set_type])->first();
            $spec = $spec_table->find()->where(['instancetype_code'=>$set_code])->first();
            $set = $agent_set->find()->where(['set_id'=>$set_id])->contain('Agent')->first();
            if($hardware === null && ($set || $spec )){
                $result = ['valid'=>false];
            }else{
                $result = ['valid'=>true];
            }
        }else{
            $result = ['valid'=>true];
        }
        echo json_encode($result);exit;
    }

    //是否关联厂商地域
    public function getAgent(){
        $agent_imagelist=TableRegistry::get('AgentSet');
        $ids=$this->request->query['ids'];
        $ids=explode(',',rtrim($ids,','));
        $count=0;
        foreach($ids as $key =>$value){
            $exist=$agent_imagelist->find()->select(['id'])->where(array('set_id'=>$value))->count();
            if($exist>0){
                $count+=1;
            }
        }
        if($count>0){
            echo json_encode(array('code'=>1,'msg'=>'选中计算能力中有'.$count.'个已关联厂商地域'));exit;
        }else{
            echo json_encode(array('code'=>0,'msg'=>'未关联厂商地域'));exit;
        }
    }
    //删除
    public function dele(){
        $public = new PublicController();
        $this->layout = false;
        if($this->request->data['id']){
            $id=$this->request->data['id'];
            $SetHardware = TableRegistry::get('SetHardware');
            $SetSoftware = TableRegistry::get('GoodsVersionSpec');
            $AgentSet = TableRegistry::get('AgentSet');
            $sethardwareinfo = $SetHardware->find()->select(['set_code'])->where(['set_id' => $id])->first()->toArray();
            $setsoftwareinfo = $SetSoftware->find()->where(['instancetype_code' => $sethardwareinfo['set_code']])->first();
            if(!empty($setsoftwareinfo)){
                $message = array('code'=>1,'msg'=>'有非编套餐使用了该计算能力');
                echo json_encode($message);exit();
            }
            $data = $SetHardware->find()->select(['set_code'])->where(['set_id'=>$id])->first();
            $res = $AgentSet->deleteAll(array('set_id'=>$id));
            $res = $SetHardware->deleteAll(array('set_id'=>$id));
            if($res){
                $message = array('code'=>0,'msg'=>'操作成功');
                $public->adminlog('SetHardware','删除计算能力---'.$data['set_code']);
            }
            echo json_encode($message);exit();

        }
    }

    //批量删除
    public function deleAll(){
        $public = new PublicController();
        $this->layout = false;
        if($this->request->data['ids']){
            $ids=$this->request->data['ids'];
            $id_array = explode(',',rtrim($ids,','));
            $msg = "";
            $SetHardware = TableRegistry::get('SetHardware');
            $SetSoftware = TableRegistry::get('GoodsVersionSpec');
            $AgentSet = TableRegistry::get('AgentSet');
            
            foreach ($id_array as $key => $id) {
                $sethardwareinfo = $SetHardware->find()->select(['set_code'])->where(['set_id' => $id])->first()->toArray();
                $setsoftwareinfo = $SetSoftware->find()->where(['instancetype_code' => $sethardwareinfo['set_code']])->first();
                if(!empty($setsoftwareinfo)){
                    $message = array('code'=>1,'msg'=>'有非编套餐使用了该计算能力');
                    echo json_encode($message);exit();
                }
                $msg .= $sethardwareinfo['set_code'].',';
            }

            $data = $SetHardware->find()->select(['set_code'])->where(['set_id'=>$id_array])->first();
            $res = $AgentSet->deleteAll(array('set_id in'=>$id_array));
            $res = $SetHardware->deleteAll(array('set_id in'=>$id_array));
            if($res){
                $message = array('code'=>0,'msg'=>'操作成功');
                $public->adminlog('计算能力','删除计算能力---'.$msg);
            }
            echo json_encode($message);exit();

        }
    }
}