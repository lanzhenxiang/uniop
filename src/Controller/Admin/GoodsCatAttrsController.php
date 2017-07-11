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

class GoodsCatAttrsController extends AdminController{

    public $paginate = [
    'limit' => 15,
    ];

    public function index($goods_category_id=0){
        $departments = TableRegistry::get('GoodsCategory');
        $query =  $departments->find('all',array('fields' => array('id','name')));
        $goodscatattrs = TableRegistry::get('GoodsCatAttrs');
        $goodscatattrscat = TableRegistry::get('GoodsCatAttrsCat');
        $i=1;
        if($goods_category_id==0){
            $data =  $goodscatattrs->find('all')->contain(['GoodsCatAttrsCat']);
        }else{
            $catattrs = $goodscatattrscat->find('all',array('conditions'=>array('goods_category_id ='=>$goods_category_id)))->toArray();
            if(!empty($catattrs)){
                foreach ($catattrs as $key => $value) {
                    $goods_cat_attrs_cat_id[]='goods_cat_attrs_cat_id ='.$value['id'];
                }
                $data = $goodscatattrs->find('all',array('conditions'=>array('OR'=>$goods_cat_attrs_cat_id)))->contain(['GoodsCatAttrsCat']);
            }else{
                $i=0;
                $data=null;
            }
        }
        $data=$this->paginate($data);
        if($i==0){
            $data=null;
        }
        $this->set('data',$data);
        $this->set('query',$query);
        
    }

    public function addedit($id=0){
        if($this->request->is('get')){
            $departments = TableRegistry::get('GoodsCategory');
            //动态配置behavior
            $departments->behaviors()->SobeyTree->config('scope',['1'=>1]);
            //获取数据
            $data  = $departments->find('optionList')->select(['id','name','parent_id'])->toArray();
            $this->set('query',$data);
            // var_dump($data);exit;
            //编辑时
            if($id){
                $goodscatattrs = TableRegistry::get('GoodsCatAttrs');
                $goodscatattrscat = TableRegistry::get('GoodsCatAttrsCat');
                $department_data = $goodscatattrs->find('all',array('conditions'=>array('id'=>$id)))->toArray();
                // var_dump($department_data[0]['goods_cat_attrs_cat_id']);exit;
                $catgory = $goodscatattrscat->find('all',array('conditions'=>array('id'=>$department_data[0]['goods_cat_attrs_cat_id'])))->first();
                $catattrs = $goodscatattrscat->find('all',array('conditions'=>array('goods_category_id'=>$catgory['goods_category_id'])))->select(['id','name'])->toArray();
                // var_dump($catattrs);exit;
                $this->set('catgory',$catgory);
                $this->set('catattrs',$catattrs);
                $this->set('department_data',$department_data[0]);
            }
        }else{
            // var_dump($this->request->data);exit;
            $message = array('code'=>1,'msg'=>'操作失败');
            $departments = TableRegistry::get('GoodsCatAttrs');
            $department = $departments->newEntity();
            if(isset($this->request->data['id'])){
                //编辑租户
                $ids = $this->request->data['id'];
                

            }else{
                $ids = 0;
            }
            $names = $departments->find('all')->select('value')->where(array('goods_cat_attrs_cat_id'=>$this->request->data['goods_cat_attrs_cat_id'],'id <>'=>$ids))->toArray();
            foreach($names as $v){
                if($this->request->data['value'] == $v['value']){
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
        $goods = TableRegistry::get('GoodsCatAttrs');

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