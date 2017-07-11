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

class OrdersGoodsController extends AdminController{

    public $paginate = [
    'limit' => 15,
    ];
    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_opt_orders');
        if (! $checkPopedomlist) {
            return $this->redirect('/admin/');
        }
    }
    public function index($id=0){
        $ordersgoods = TableRegistry::get('OrdersGoods');
        $data =  $ordersgoods->find('all',array('conditions'=>array('order_id ='=>$id)))->contain(['Goods']);
        $data=$this->paginate($data);
        $this->set('data',$data);
    }

    public function addedit($id=0){
        $departments = TableRegistry::get('Departments');
        $orders = TableRegistry::get('Orders');
        if($this->request->is('get')){
            //动态配置behavior
            $departments->behaviors()->Departments->config('scope',['1'=>1]);
            //获取数据
            $data  = $departments->find('optionList')->select(['id','name','parent_id'])->toArray();
            $this->set('query',$data);
            $department_data =  $orders->find('all',array('conditions'=>array('Orders.id ='=>$id)))->contain(['Department','Account'])->toArray();
            $this->set('department_data',$department_data[0]);
        }else{
            $message = array('code'=>1,'msg'=>'操作失败');
            $order = $orders->newEntity();
            //编辑租户
            $this->request->data['modify_time'] = time();

            $order = $orders->patchEntity($order,$this->request->data);
            $result = $orders->save($order);
            if($result){
                $message = array('code'=>0,'msg'=>'操作成功');
            }
            echo json_encode($message);exit();
        }

    }
}