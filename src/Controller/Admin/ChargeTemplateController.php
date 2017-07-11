<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2016/1/26
 * Time: 13:54
 */

namespace App\Controller\Admin;


use App\Controller\AdminController;
use Cake\ORM\TableRegistry;

class ChargeTemplateController extends AdminController{
    public $paginate = [
        'limit' => 15,
    ];

    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_service_charge_template');
        if (! $checkPopedomlist) {
            return $this->redirect('/admin/');
        }
    }


    public function index($department_id=0,$name=''){
        $chargetemplate = TableRegistry::get('ChargeTemplate');
        if(!is_numeric($department_id) && empty($name)){
            $name=$department_id;
            $department_id=0;
        }
        $departments = TableRegistry::get('Departments');
        //返回租户
        $dept_grout =array();
        if(in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))){
            $department_id = isset($this->request->data['department_id'])?$this->request->data['department_id']:$department_id;
            $dept_grout = $departments->find()->select(['id','name'])->toArray();
        }
        $this->set('dept_grout',$dept_grout);

        //显示服务列表
        $where = array();
        if($name){
            $where['template_name like'] ="%$name%";
        }

        //当显示全部租户下的服务时
        if($department_id==0){
            //系统权限的前提
            if(in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))){
                $query = $chargetemplate->find('all')->contain(['Departments'])->where($where);
                $department_data['name']='所有租户';
                //租户权限的前提
            }elseif(!in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname')) && in_array('cmop_global_tenant_admin',$this->request->session()->read('Auth.User.popedomname'))){
                $where['department_id']=$this->request->session()->read('Auth.User.department_id');
                $query = $chargetemplate->find('all')->contain(['Departments'])->where($where);
                $department_data = $departments->find()->select(['name'])->where(array('id'=>$this->request->session()->read('Auth.User.department_id')))->toArray();
            }
        }else{
            $where['department_id']=$department_id;
            if(in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))){
                $query = $chargetemplate->find('all')->contain(['Departments'])->where($where);
                $department_data = $departments->find()->select(['id','name'])->where(array('id'=>$department_id))->toArray();
            }
        }



        if(isset($query)){
            $rs=$this->paginate($query);
            $this->set('data',$rs->toArray());
        }else{
            $rs=$this->paginate();
        }

        if(isset($department_data)){
            $this->set('department_name',isset($department_data[0])?$department_data[0]['name']:$department_data['name']);
        }else{
            $this->set('department_name','');
        }
        $this->set('name',$name);

    }


    public function addedit($id=0){
        $chargetemplate = TableRegistry::get('ChargeTemplate');
        $departments = TableRegistry::get('Departments');
        if($this->request->is('get')){
            $department = $departments->find('all')->select(['id','name'])->toArray();
            $this->set('dept',$department);
            if($id){
                $service_result = $chargetemplate->find('all')->where(array('id'=>$id))->toArray();
                $this->set('data',$service_result[0]);
            }

        }else{
            $message = array('code'=>1,'msg'=>'操作失败');
            $public = new PublicController();
            if(!empty($this->request->data['charge_expression'])){
                $charge_expression =$this->request->data['charge_expression'];
                $len = strlen($charge_expression);
                $str = ($charge_expression{$len-1});
                $Symbol = array('+','-','*','/','(');
                if(in_array($str,$Symbol)){
                    $message = array('code'=>1,'msg'=>'计算公式不能以+ - * / (结尾');
                    echo json_encode($message);exit;
                }else{
                    $charge_expression = str_replace("T","2",$charge_expression);
                    $charge_expression = str_replace("N","3",$charge_expression);
                    $arr = array('{','}','_','=','q','w','e','r','t','y','u','i','o','p','a','s','d','f','g','h','j','k','l','z','x','c','v','b','n','m','Q','W','E','R','Y','U','I','O','P','A','S','D','F','G','H','J','K','L','Z','X','C','V','B','N','M','!','@','#','$','%','^','&',':','"','|','<','>','?');
                    for ($i = 0; $i < strlen($charge_expression)-1; $i ++) {
                        if(in_array($charge_expression[$i],$arr)){
                            $message = array('code'=>1,'msg'=>'该计算公式有误,请输入正确的计算公式');
                            echo json_encode($message);exit;
                        }
                    }

                    $result=eval("return $charge_expression;");
                    if($result<0){
                        $message = array('code'=>1,'msg'=>'该计算公式有误,请输入正确的计算公式');
                        echo json_encode($message);exit;
                    }
                }

            }
            if(isset($this->request->data['id'])){
                $name = $chargetemplate->find('all')->select(['id','template_name'])->where(array('template_name'=>$this->request->data['template_name']))->toArray();
                //判断修改的名字是否已经存在
                if($name){
                    foreach($name as $va){
                        if($va['id']!=$this->request->data['id'] && $va['template_name'] == $this->request->data['template_name']){
                            $message = array('code'=>1,'msg'=>'该模板名称已存在');
                            echo json_encode($message);exit;
                        }
                    }
                }
                $department_name = $departments->find('all')->select(['name','dept_code'])->where(array('id'=>$this->request->data['department_id']))->first()->toArray();
                $this->request->data['department_name']=$department_name['name'];
                $this->request->data['department_code']=$department_name['dept_code'];
                $template=$chargetemplate->newEntity();
                $template = $chargetemplate->patchEntity($template,$this->request->data);
                $t_result = $chargetemplate->save($template);
                if($t_result){
                    $message = array('code'=>0,'msg'=>'操作成功','source'=>0);
                    $public->adminlog('ChargeTemplate','修改计费模板---'.$this->request->data['template_name']);
                }
                echo json_encode($message);exit;
            }else{
                $count = $chargetemplate->find('all')->select(['id'])->where(array('template_name'=>$this->request->data['template_name']))->count();
                //判断名字是否已存在
                if($count>0){
                    $message = array('code'=>1,'msg'=>'该服务已存在');
                    echo json_encode($message);exit;
                }
                $department_name = $departments->find('all')->select(['name','dept_code'])->where(array('id'=>$this->request->data['department_id']))->first()->toArray();
                $this->request->data['department_name']=$department_name['name'];
                $this->request->data['department_code']=$department_name['dept_code'];
                $template=$chargetemplate->newEntity();
                $template = $chargetemplate->patchEntity($template,$this->request->data);
                $result = $chargetemplate->save($template);
                if($result){
                    $message = array('code'=>0,'msg'=>'操作成功');
                    $public->adminlog('ChargeTemplate','修改计费模板---'.$this->request->data['template_name']);
                }
                echo json_encode($message);exit;

            }
        }
    }


    //删除模板
    public function deletes(){
        $message=array('code'=>1,'message'=>'删除计费模板失败');
        $data = $this->request->data;
        $chargetemplate = TableRegistry::get('ChargeTemplate');
        $name=$chargetemplate->find()->select(['template_name'])->where(array('id'=>$data['id']))->toArray();
        $result = $chargetemplate->deleteAll(array('id'=>$data['id']));
        if($result){
            $public = new PublicController();
            $message=array('code'=>0,'message'=>'删除计费模板成功');
            $public->adminlog('ChargeTemplate','删除计费模板---'.$name[0]['template_name']);
        }
        echo json_encode($message);exit();

    }

}