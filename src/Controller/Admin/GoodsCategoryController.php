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

class GoodsCategoryController extends AdminController{

    public $paginate = [
    'limit' => 15,
    ];
    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_commodity_type');
        if (! $checkPopedomlist) {
            return $this->redirect('/admin/');
        }
    }
    public function index(){
        $GoodsCategory = TableRegistry::get('GoodsCategory');
        $data =  $GoodsCategory->find('all')->select(['id','name','parent_id'])->contain(['GoodsCategorys'])->order('GoodsCategory.sort_order asc');
        $this->set('data',json_encode($data));
    }

    public function addedit($id=0){
        $public = new PublicController();
        $departments = TableRegistry::get('GoodsCategory');
        if($this->request->is('get')){
            //动态配置behavior
            $departments->behaviors()->SobeyTree->config('scope',['1'=>1]);
            //获取数据
            $data  = $departments->find('optionList')->select(['id','name','parent_id'])->toArray();
            $this->set('query',$data);

            //编辑时
            if($id){
                $department_data = $departments->find('all',array('conditions'=>array('id'=>$id)))->toArray();
                $this->set('department_data',$department_data[0]);
            }
        }else{
            // var_dump($this->request->data);exit;
            $message = array('code'=>1,'msg'=>'操作失败');
            $department = $departments->newEntity();
            if(isset($this->request->data['id'])){
                //编辑
                $ids = $this->request->data['id'];
                $pid = $this->request->data['parent_id'];
                $data = array();
                $suns = $this->check($data,$ids);
                if(in_array($this->request->data['parent_id'],$suns)){
                    $message = array('code'=>1,'msg'=>'不能将租户更改到子租户下');
                    echo json_encode($message);exit();
                }
                if($this->request->data['parent_id'] == $this->request->data['id']){
                    $message = array('code'=>1,'msg'=>'不能将租户更改到到自己下');
                    echo json_encode($message);exit();
                }
                
                // $this->request->data['modify_time'] = time();

            }else{
                //添加
                $ids = 0;
            }
            $names = $departments->find('all')->select('name')->where(array('parent_id'=>$this->request->data['parent_id'],'id <>'=>$ids))->toArray();
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
                if(empty($this->request->data['id'])){
                    $public->adminlog('GoodsCategory','添加商品分类---'.$this->request->data['name']);
                }else{
                    $public->adminlog('GoodsCategory','修改商品分类---'.$this->request->data['name']);
                }
            }
            echo json_encode($message);exit();
        }

    }

    //删除
    public function dele(){
        $public = new PublicController();
        $GoodsCategory = TableRegistry::get('GoodsCategory');
        $Goods = TableRegistry::get('Goods');

        if ($this->request->is('post')){
            $id = isset($this->request->data['id'])?$this->request->data['id']:$id;
            $result = array('code'=>1,'msg'=>'操作失败');
            $query = $Goods->find()->where(['category_id' => $id])->first();
            if($query){
                $result = array('code'=>1,'msg'=>'该分类下有商品，不能删除！');
                echo json_encode($result);
                exit;
                $this->lauout = 'ajax';
            }
            $data = $GoodsCategory->find()->where(['id'=>$id])->first();
            $account = $GoodsCategory->get($id);
            $account = $GoodsCategory->patchEntity($account,$this->request->data);
            $account->id = $id;
            if ($GoodsCategory->delete($account)){
                $result = array('code'=>0,'msg'=>'操作成功');
                $public->adminlog('GoodsCategory','删除商品分类---'.$data['name']);
            }

            echo json_encode($result);exit();
            $this->lauout = 'ajax';
        }


    }


    public function check($data,$pid,$id=0){

        $departments = TableRegistry::get('GoodsCategory');
        $sun = $departments->find('all')->select(['id'])->where(array('parent_id'=>$pid))->toArray();
        if(!empty($sun)){
            foreach($sun as $va){
                $data[]=$va['id'];
                $data =$this->check($data,$va['id']);
            }
        }
        return $data;
    }

}