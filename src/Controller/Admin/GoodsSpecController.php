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

class GoodsSpecController extends AdminController{

    public $paginate = [
    'limit' => 15,
    ];

    public function index($goods_id=0){
        $goodsspec = TableRegistry::get('GoodsSpec');
        $goods = TableRegistry::get('Goods');
        $query = $goods->find('all')->select(['id','name']);
        if($goods_id==0){
            $data =  $goodsspec->find('all')->contain(['Goods']);
        }else{
            $data = $goodsspec->find('all',array('conditions'=>array('goods_id ='=>$goods_id)))->contain(['Goods']);
        }
        $data=$this->paginate($data);
        // foreach ($data as $key => $value) {
        //     var_dump($value);
        // }
        // exit;
        $datas = $data->toArray();
        $this->set('data',$datas);
        $this->set('query',$query);
    }
    

    public function addedit($type='edit',$id=0){
        // echo $type;exit;
        $goods = TableRegistry::get('Goods');
        $goodsspec = TableRegistry::get('GoodsSpec');
        $data  = $goods->find('all')->select(['id','name'])->toArray();
        if($this->request->is('get')){
            $this->set('query',$data);
            if($type=='add'){
                $this->set('id',$id);
            }else{
                $department_data = $goodsspec->find('all',array('conditions'=>array('id'=>$id)))->toArray();
                $this->set('department_data',$department_data[0]);
            }
        }else{
            $message = array('code'=>1,'msg'=>'操作失败');
            $order = $goodsspec->newEntity();
            //编辑租户
            $order = $goodsspec->patchEntity($order,$this->request->data);
            $result = $goodsspec->save($order);
            if($result){
                $message = array('code'=>0,'msg'=>'操作成功');
            }
            echo json_encode($message);exit();
        }

    }

    //删除
    public function dele(){
        $goodsspec = TableRegistry::get('GoodsSpec');
        $result = array('code'=>1,'msg'=>'操作失败');
        if ($this->request->is('post')){
            $id = isset($this->request->data['id'])?$this->request->data['id']:$id;
            $res = $goodsspec->deleteAll(array('id'=>$id));
            if ($res){
                $result = array('code'=>0,'msg'=>'操作成功');
            }
            echo json_encode($result);exit();
            $this->lauout = 'ajax';
        }
        echo json_encode($result);exit();
        $this->lauout = 'ajax';
    }

    public function checkcat($id=0){
        $id = isset($this->request->data['id'])?$this->request->data['id']:$id;
        $goodscatattrscat = TableRegistry::get('GoodsCatAttrsCat');
        $catattrs = $goodscatattrscat->find('all',array('conditions'=>array('goods_category_id ='=>$id)))->toArray();
        if(!empty($catattrs)){
            foreach ($catattrs as $key => $value) {
                $data['id'][]=$value['id'];
                $data['name'][]=$value['name'];
            }
        }else{
            $data=1;
        }
        echo json_encode($data);exit;
    }

}