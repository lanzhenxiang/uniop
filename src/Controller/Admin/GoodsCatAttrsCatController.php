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

class GoodsCatAttrsCatController extends AdminController{

    public $paginate = [
    'limit' => 15,
    ];

    public function index(){
        $departments = TableRegistry::get('GoodsCatAttrsCat');
        $data =  $departments->find('all')->contain(['GoodsCategory']);
        $data=$this->paginate($data);
        $this->set('data',$data);
    }

    public function addedit($id=0){
        if($this->request->is('get')){
            $departments = TableRegistry::get('GoodsCategory');
            //动态配置behavior
            $departments->behaviors()->SobeyTree->config('scope',['1'=>1]);
            //获取数据
            $data  = $departments->find('optionList')->select(['id','name','parent_id'])->toArray();
            $this->set('query',$data);
            //编辑时
            if($id){
                $goodscatattrscat = TableRegistry::get('GoodsCatAttrsCat');

                $department_data = $goodscatattrscat->find('all',array('conditions'=>array('id'=>$id)))->toArray();
                $this->set('department_data',$department_data[0]);
            }
        }else{
            // var_dump($this->request->data);exit;
            $message = array('code'=>1,'msg'=>'操作失败');
            $departments = TableRegistry::get('GoodsCatAttrsCat');
            $department = $departments->newEntity();
            if(isset($this->request->data['id'])){
                //编辑租户
                $ids = $this->request->data['id'];
                
            }else{
                $ids = 0;
            }
            $names = $departments->find('all')->select('name')->where(array('goods_category_id'=>$this->request->data['goods_category_id'],'id <>'=>$ids))->toArray();
            foreach($names as $v){
                if($this->request->data['name'] == $v['name']){
                    $message = array('code'=>1,'msg'=>'同级下名字不能重复');
                    echo json_encode($message);exit();
                }
            }
            $department = $departments->patchEntity($department,$this->request->data);
            $result = $departments->save($department);
            if($result){
                $message = array('code'=>0,'msg'=>'操作成功');
            }
            echo json_encode($message);exit();
        }

    }

    //删除
    public function delete(){
        $goods = TableRegistry::get('GoodsCatAttrsCat');

        if ($this->request->is('post')){
            $id = isset($this->request->data['id'])?$this->request->data['id']:$id;
            $result = array('code'=>1,'msg'=>'操作失败');
            $account = $goods->get($id);
            $account = $goods->patchEntity($account,$this->request->data);
            $account->id = $id;
            if ($goods->delete($account)){
                $result = array('code'=>0,'msg'=>'操作成功');
            }

            echo json_encode($result);exit();
            $this->lauout = 'ajax';
        }
    }
}