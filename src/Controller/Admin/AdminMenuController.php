<?php
/**
 * 运营管理中心，菜单管理
 * @author lan <[<email address>]>
 * Date: 2016/12/22
 */

namespace App\Controller\Admin;

use App\Controller\AdminController;
use App\Controller\SobeyController;
use Cake\ORM\TableRegistry;

class AdminMenuController extends AdminController
{

    public $paginate = [
        'limit' => 15
    ];

    public function initialize()
    {
        parent::initialize();
//        $checkPopedomlist = parent::checkPopedomlist('bgm_para_cc_menus');
        $checkPopedomlist = parent::checkPopedomlist('bgm_system_menu');
        if (! $checkPopedomlist) {
            return $this->redirect('/admin/');
        }
    }

    public function index()
    {
        $AdminMenu = TableRegistry::get('AdminMenu');
        $query = $AdminMenu->find('all')
            ->select([
            'id',
            'name',
            'label',
            'parent_id'
        ])->order('sort ASC')
            ->toArray();
        $data = $this->_get_tree($query);
        // $info = $this->arrayToTree($data, 'id', 'parent_id', 'children'); 
        $this->set('data',json_encode($query));
    }

    public function data(){
        $AdminMenu = TableRegistry::get('AdminMenu');
        $query = $AdminMenu->find('all')->toArray(); 
        $data=$this->_get_tree($query); 
        // $info = $this->arrayToTree($data, 'id', 'parent_id', 'children');
        $this->set('info',$info);
    }

    public function addedit($id=0){
        $public = new PublicController();
        $AdminMenu = TableRegistry::get('AdminMenu');
        $popedomlist = TableRegistry::get('Popedomlist');
        $popedomlist_info = $popedomlist->find('all')->select(['popedomname','popedomnote'])->where(['popedomtype'=>'cmop_admin'])->toArray();
        $this->set('popedomlist_info',$popedomlist_info);
        if($this->request->is('get')){
            //动态配置behavior
            $AdminMenu->behaviors()->SobeyTree->config('scope',['1'=>1]);
            //获取数据
//            $data = $AdminMenu->find('optionList')->select(['id','label','parent_id'])->toArray();
            $data = $AdminMenu->find('optionList')->select(['id','label','parent_id'])->where(array('parent_id'=>0))->toArray();
            // var_dump($data);exit;
            $this->set('query',$data);
             //编辑时
            if($id){
                $department_data = $AdminMenu->find('all',array('conditions'=>array('id'=>$id)))->toArray();
                $this->set('department_data',$department_data[0]);
            }
        }else{
            $message = array('code'=>1,'msg'=>'操作失败');
            $console = $AdminMenu->newEntity();
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
            $names = $AdminMenu->find('all')->select(['name','id'])->where(array('parent_id'=>$this->request->data['parent_id'],'id <>' => $ids))->toArray();
            foreach($names as $v){
                if($this->request->data['name'] == $v['name'] && $this->request->data['parent_id'] != $v['parent_id']){
                    $message = array('code'=>1,'msg'=>'同级下名字不能重复');
                    echo json_encode($message);exit();
                }
            }
            $console = $AdminMenu->patchEntity($console,$this->request->data);
            $result = $AdminMenu->save($console);
            if($result){
                $message = array('code'=>0,'msg'=>'操作成功');
                if(empty($this->request->data['id'])){
                        $public->adminlog('AdminMenu','添加菜单---'.$this->request->data['name']);
                    }else{
                        $public->adminlog('AdminMenu','修改菜单---'.$this->request->data['name']);
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
            $AdminMenu = TableRegistry::get('AdminMenu');
            $query = $AdminMenu->find('all',array('conditions'=>array('parent_id'=>$id)))->toArray();
            if ($query) {
                $message = array('code'=>1,'msg'=>'该菜单有子菜单不能删除');
                echo json_encode($message);exit();
            }else{
                $data = $AdminMenu->find()->where(['id'=>$id])->first();
                $res = $AdminMenu->deleteAll(array('id'=>$id));
                if($res){
                    $message = array('code'=>0,'msg'=>'操作成功');
                    $public->adminlog('AdminMenu','删除菜单---'.$data['name']);
                }
                echo json_encode($message);exit();
            }

        }
    }


    private function _check($data,$id){

        $departments = TableRegistry::get('AdminMenu');
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