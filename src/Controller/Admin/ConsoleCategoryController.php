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

class ConsoleCategoryController extends AdminController
{

    public $paginate = [
        'limit' => 15
    ];

    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_para_cc_menus');
        if (! $checkPopedomlist) {
            return $this->redirect('/admin/');
        }
    }

    public function index()
    {
        $consolecategory = TableRegistry::get('ConsoleCategory');
        $query = $consolecategory->find('all')
            ->select([
            'id',
            'name',
            'label',
            'parent_id'
        ])->order('sort_order ASC')
            ->toArray();
        $data = $this->_get_tree($query);
        // $info = $this->arrayToTree($data, 'id', 'parent_id', 'children'); 
        $this->set('data',json_encode($query));
    }

    public function data(){
        $consolecategory = TableRegistry::get('ConsoleCategory');
        $query = $consolecategory->find('all')->toArray(); 
        $data=$this->_get_tree($query); 
        // $info = $this->arrayToTree($data, 'id', 'parent_id', 'children');
        $this->set('info',$info);
    }

    public function addedit($id=0){
        $public = new PublicController();
        $consolecategory = TableRegistry::get('ConsoleCategory');
        $popedomlist = TableRegistry::get('Popedomlist');
        $popedomlist_info = $popedomlist->find('all')->select(['popedomname','popedomnote'])->toArray();
        $this->set('popedomlist_info',$popedomlist_info);
        if($this->request->is('get')){
            //动态配置behavior
            $consolecategory->behaviors()->SobeyTree->config('scope',['1'=>1]);
            //获取数据
            $data = $consolecategory->find('optionList')->select(['id','label','parent_id'])->toArray();
            // var_dump($data);exit;
            $this->set('query',$data);
             //编辑时
            if($id){
                $department_data = $consolecategory->find('all',array('conditions'=>array('id'=>$id)))->toArray();
                $this->set('department_data',$department_data[0]);
            }
        }else{
            $message = array('code'=>1,'msg'=>'操作失败');
            $console = $consolecategory->newEntity();
            $ids = 0;
            if(isset($this->request->data['id'])){
                $ids = $this->request->data['id'];
                $data = array();
                $suns = $this->_check($data,$ids);
                if(in_array($this->request->data['parent_id'],$suns)){
                    $message = array('code'=>1,'msg'=>'不能将菜单更改到子菜单下');
                    echo json_encode($message);exit();
                }
                if($this->request->data['parent_id'] == $this->request->data['id']){
                    $message = array('code'=>1,'msg'=>'不能将菜单更改到到自己下');
                    echo json_encode($message);exit();
                }
            }
            //  var_dump($this->request->data);exit;
            $names = $consolecategory->find('all')->select(['name','id'])->where(array('parent_id'=>$this->request->data['parent_id'],'id <>' => $ids))->toArray();
            foreach($names as $v){
                if($this->request->data['name'] == $v['name'] && $this->request->data['parent_id'] != $v['parent_id']){
                    $message = array('code'=>1,'msg'=>'同级下名字不能重复');
                    echo json_encode($message);exit();
                }
            }
            $console = $consolecategory->patchEntity($console,$this->request->data);
            $result = $consolecategory->save($console);
            if($result){
                $message = array('code'=>0,'msg'=>'操作成功');
                if(empty($this->request->data['id'])){
                        $public->adminlog('ConsoleCategory','添加菜单---'.$this->request->data['name']);
                    }else{
                        $public->adminlog('ConsoleCategory','修改菜单---'.$this->request->data['name']);
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
            $consolecategory = TableRegistry::get('ConsoleCategory');
            $query = $consolecategory->find('all',array('conditions'=>array('parent_id'=>$id)))->toArray();
            if ($query) {
                $message = array('code'=>1,'msg'=>'该菜单有子菜单不能删除');
                echo json_encode($message);exit();
            }else{
                $data = $consolecategory->find()->where(['id'=>$id])->first();
                $res = $consolecategory->deleteAll(array('id'=>$id));
                if($res){
                    $message = array('code'=>0,'msg'=>'操作成功');
                    $public->adminlog('ConsoleCategory','删除菜单---'.$data['name']);
                }
                echo json_encode($message);exit();
            }

        }
    }


    private function _check($data,$id){

        $departments = TableRegistry::get('ConsoleCategory');
        $sun = $departments->find('all')->select(['id'])->where(array('parent_id'=>$id))->toArray();
        if(!empty($sun)){
            foreach($sun as $va){
                $data[]=$va['id'];
                $data =$this->_check($data,$va['id']);
            }
        }
        return $data;
    }

    /**
     * 对数据进行树形结构排序
     */
    private function _get_tree($cate,$pid=0,$level=0,$html='　　'){
        $tree = array();
        foreach($cate as $v){
            if($v['parent_id'] == $pid){
                $v['level'] = $level;
                $v['html'] = str_repeat($html,$level);
                $tree[] = $v;
                $tree = array_merge($tree, $this->_get_tree($cate,$v['id'],$level+1));
            }
        }
        return $tree;
    }
}