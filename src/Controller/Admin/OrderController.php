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

class OrdersController extends AdminController
{

    public $paginate = [
    'limit' => 15
    ];

    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_opt_orders');
        if (! $checkPopedomlist) {
            return $this->redirect('/admin/');
        }
    }

    public function index($status=0,$flow_id=0,$number='')
    {
        $workflow_template_table = TableRegistry::get('WorkflowTemplate');
        $workflow_detail_table = TableRegistry::get('WorkflowDetail');
        $template_data = $workflow_template_table->find()->toArray();
        $flow['flow_name'] = '全部';
        $flow['flow_id'] = 0;
        $step['step_name'] = '全部';
        $where = array();
        if($flow_id !=0 ){
            $where['Orders.flow_id'] = $flow_id;
            $workflow_detail_data = $workflow_detail_table->find()->where(['flow_id' => $flow_id])->order(['lft'])->toArray();
            $flow = $workflow_template_table->find()->where(['flow_id' => $flow_id])->first();
            $this->set('detail_data',$workflow_detail_data);
        }
        if($status!=0){
            // $where .=" AND cp_orders.`status` = ".$status;
            $where['Orders.status'] = $status;
            $step = $workflow_detail_table->find()->where(['lft' => $status,'flow_id' => $flow_id])->first();
        }

        if (! empty($number)) {
            $where['OR'] =array('Orders.number like'=>"%$number%",'Account.username like'=>"%$number%");
        }
        $orders = TableRegistry::get('Orders');
        $data = $orders->find('all')
        ->contain([
            'Department',
            'Account',
            'WorkflowDetail'
            ])
        ->order('Orders.create_time DESC')
        ->where($where);
        $data = $this->paginate($data);
        $this->set('flow',$flow);
        $this->set('step',$step);
        $this->set('name', $number);
        $this->set('status',$status);
        $this->set('data',$data);
        $this->set('template_data',$template_data);
    }

    public function addedit($id=0){
        $public = new PublicController();
        $departments = TableRegistry::get('Departments');
        $orders = TableRegistry::get('Orders');
        $workflow_detail_table = TableRegistry::get('WorkflowDetail');
        if($this->request->is('get')){
            //动态配置behavior
            $departments->behaviors()->Departments->config('scope',['1'=>1]);
            //获取数据
            $data  = $departments->find('optionList')->select(['id','name','parent_id'])->toArray();
            
            $department_data =  $orders->find('all',array('conditions'=>array('Orders.id ='=>$id)))->contain(['Department','Account','WorkflowDetail'])->first();
            $detail_data = $workflow_detail_table->find()->where(['flow_id' =>$department_data['flow_id']])->toArray();
            $this->set('query',$data);
            $this->set('department_data',$department_data);
            $this->set('detail_data',$detail_data);
        }else{
            unset($this->request->data['create_time']);
            $message = array('code'=>1,'msg'=>'操作失败');
            $order = $orders->newEntity();
            $ordersInfo = $orders->find()->where(['id' =>$this->request->data['id']])->first();
            $order_detail_info = $workflow_detail_table->find()->where(['flow_id' => $ordersInfo['flow_id'],'lft' => $ordersInfo['status']])->first();
            if ($order_detail_info['step_code'] == 'end') {
                $message = array('code'=>1,'msg'=>'该订单已完成不能修改');
                echo json_encode($message);exit();
            }
            // if ($ordersInfo['step_code'] >= 3) {
            //     if($ordersInfo['price_total'] !=$this->request->data['price_total']){
            //         $message = array('code'=>1,'msg'=>'该订单已支付不能修改总价');
            //         echo json_encode($message);exit();
            //     }
            // }
            $this->request->data['modify_time'] = time();
            $this->request->data['modify_by'] = $this->request->session()->read('Auth.User.id') ? $this->request->session()->read('Auth.User.id'):0;
            $order = $orders->patchEntity($order,$this->request->data);
            $result = $orders->save($order);
            if($result){
                $message = array('code'=>0,'msg'=>'操作成功');
                $public->adminlog('Orders','修改订单---'.$ordersInfo['number']);
            }
            echo json_encode($message);exit();
        }

    }

    //删除
    public function delete(){
        $workflow_detail_table = TableRegistry::get('WorkflowDetail');
        $public = new PublicController();
        if($this->request->data['id']){
            $id=$this->request->data['id'];
            $orders = TableRegistry::get('Orders');
            $ordersgoods = TableRegistry::get('OrdersGoods');
            $ordersInfo = $orders->find()->select(['status','flow_id'])->where(['id' =>$id])->first();
            $order_detail_info = $workflow_detail_table->find()->where(['flow_id' => $ordersInfo['flow_id'],'lft' => $ordersInfo['status']])->first();
            if ($order_detail_info['step_code'] == 'end') {
                $message = array('code'=>1,'msg'=>'该订单已完成不能删除');
                echo json_encode($message);exit();
            }
            $data = $orders->find()->where(['id'=>$id])->first();
            $res = $orders->deleteAll(array('id'=>$id));
            if($ordersgoods->find('all')->where(array('order_id'=>$id))->count()>0){
                $ress = $ordersgoods->deleteAll(array('id'=>$id));
                if($res && $ress){
                    $message = array('code'=>0,'msg'=>'操作成功');
                    $public->adminlog('Orders','删除订单---'.$data['number']);
                    echo json_encode($message);exit();
                }
            }
            if($res){
                $message = array('code'=>0,'msg'=>'操作成功');
                $public->adminlog('Orders','删除订单---'.$data['number']);
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