<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2015/12/7
 * Time: 10:31
 */

namespace App\Controller\Admin;


use App\Controller\AdminController;
use Cake\ORM\TableRegistry;

class PopedomlistController extends AdminController{
    public $paginate = [
    'limit' => 15
    ];

    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_para_popedom');
        if (! $checkPopedomlist) {
            return $this->redirect('/admin/');
        }
    }

    public function index($name = '')
    {
        $Popedomlist = TableRegistry::get('Popedomlist');
        $where = array();
        if ($name) {
            $where['OR'] =array('popedomname like'=>"%$name%",'popedomnote like'=>"%$name%");
        }
        $data = $Popedomlist->find()->where($where);
        $data = $this->paginate($data);
        $this->set('data', $data);
        $this->set('name',$name);
    }

    public function addedit($id=0){
        $public = new PublicController();
        $message = array('code'=>1,'msg'=>'操作失败');
        $Popedomlist = TableRegistry::get('Popedomlist');

        if($this->request->is('post')){
            $Popedomlists = $Popedomlist->newEntity();
            if(isset($this->request->data['popedomid'])){
                $ids = $this->request->data['popedomid'];
                $data = array();
                $suns = $this->_check($data,$ids);
                if(in_array($this->request->data['parent_id'],$suns)){
                    $message = array('code'=>1,'msg'=>'不能将权限更改到子权限下');
                    echo json_encode($message);exit();
                }
                if($this->request->data['parent_id'] == $this->request->data['popedomid']){
                    $message = array('code'=>1,'msg'=>'不能将权限更改到到自己下');
                    echo json_encode($message);exit();
                }
                $name = $Popedomlist->find('all')->where(['popedomid <>' => $this->request->data['popedomid'],'popedomname' => $this->request->data['popedomname']])->first();
                if(!empty($name)){
                     $message = array('code'=>1,'msg'=>'权限名称不能重复');
                    echo json_encode($message);exit();
                }
                $Popedomlists = $Popedomlist->patchEntity($Popedomlists,$this->request->data);
                $updataresult = $Popedomlist->save($Popedomlists);
                // var_dump($updataresult);exit;
                if($updataresult){
                    $message = array('code'=>0,'msg'=>'操作成功');
                    $public->adminlog('Popedomlist','修改权限---'.$this->request->data['popedomname']);
                }
            }else{
                $name = $Popedomlist->find()->where(['popedomname' => $this->request->data['popedomname']])->first();
                if(!empty($name)){
                     $message = array('code'=>1,'msg'=>'权限名称不能重复');
                    echo json_encode($message);exit();
                }
                $Popedomlists = $Popedomlist->patchEntity($Popedomlists,$this->request->data);
                $result = $Popedomlist->save($Popedomlists);
                if($result){
                    $message = array('code'=>0,'msg'=>'操作成功');
                    $public->adminlog('Popedomlist','添加权限---'.$this->request->data['popedomname']);
                }
            }
            //修改
            echo json_encode($message);exit();
        }else{
            $Popedomlist->behaviors()->SobeyTree->config('scope',['1'=>1]);
            //获取数据
            $query = $Popedomlist->find('optionList')->select(['popedomid','popedomnote','parent_id'])->toArray();
            $this->set('query',$query);
            if($id){
                $data = $Popedomlist->find()->where(array('popedomid'=>$id))->toArray();
                $this->set('data',$data[0]);
            }
        }
    }

    /*
    *删除
    */
    public function delete(){
        $public = new PublicController();
        if ($this->request->is('post')){
            $Popedomlist = TableRegistry::get('Popedomlist');
            $id=$this->request->data['id'];
            $message = array('code'=>1,'msg'=>'操作失败');
            $data = $Popedomlist->find()->where(['popedomid'=>$id])->first();
            if ($Popedomlist->deleteAll(array('popedomid'=>$id))){
                $roles_popedoms = TableRegistry::get('RolesPopedoms');
                $count = $roles_popedoms->find()->where(array('popedomlist_id'=>$id))->count();
                $result=$roles_popedoms->deleteAll(array('popedomlist_id'=>$id));
                if($count==$result){
                    $message = array('code'=>0,'msg'=>'操作成功');
                    $public->adminlog('Popedomlist','删除权限---'.$data['popedomname']);
                }
            }

            echo json_encode($message);exit;
            // $this->lauout = 'ajax';
        }

    }

    private function _check($data,$id){

        $departments = TableRegistry::get('Popedomlist');
        $sun = $departments->find('all')->select(['popedomid'])->where(array('parent_id'=>$id))->toArray();
        if(!empty($sun)){
            foreach($sun as $va){
                $data[]=$va['popedomid'];
                $data =$this->_check($data,$va['popedomid']);
            }
        }
        return $data;
    }


    public function order($name = ''){
        $Popedomlist = TableRegistry::get('Popedomlist');
        $workFlowEntity = $Popedomlist->getWorkFlow('popedom_workflow');
        $workId = $workFlowEntity->popedomid;
        $where = array('parent_id'=>$workId);
        $data = $Popedomlist->find()->where($where);
        $data = $this->paginate($data);

        $this->set('data', $data);
        $this->set('name',$name);
    }
    public function edito($id=0)
    {
        $public = new PublicController();
        $message = array('code'=>1,'msg'=>'操作失败');
        $Popedomlist = TableRegistry::get('Popedomlist');

        if($this->request->is('post')){
            $Popedomlists = $Popedomlist->newEntity();
            if(isset($this->request->data['popedomid'])){
                $ids = $this->request->data['popedomid'];
                $data = array();
                $suns = $this->_check($data,$ids);
                if($this->request->data['parent_id'] == $this->request->data['popedomid']){
                    $message = array('code'=>1,'msg'=>'不能将权限更改到到自己下');
                    echo json_encode($message);exit();
                }
                $name = $Popedomlist->find('all')->where(['popedomid <>' => $this->request->data['popedomid'],'popedomname' => $this->request->data['popedomname']])->first();
                if(!empty($name)){
                     $message = array('code'=>1,'msg'=>'权限名称不能重复');
                    echo json_encode($message);exit();
                }
                $Popedomlists = $Popedomlist->patchEntity($Popedomlists,$this->request->data);
                $updataresult = $Popedomlist->save($Popedomlists);
                // var_dump($updataresult);exit;
                if($updataresult){
                    $message = array('code'=>0,'msg'=>'操作成功');
                    $public->adminlog('Popedomlist','修改权限---'.$this->request->data['popedomname']);
                }
            }else{
                $name = $Popedomlist->find()->where(['popedomname' => $this->request->data['popedomname']])->first();
                if(!empty($name)){
                     $message = array('code'=>1,'msg'=>'权限名称不能重复');
                    echo json_encode($message);exit();
                }
                $Popedomlists = $Popedomlist->patchEntity($Popedomlists,$this->request->data);
                $result = $Popedomlist->save($Popedomlists);
                if($result){
                    $message = array('code'=>0,'msg'=>'操作成功');
                    $public->adminlog('Popedomlist','添加权限---'.$this->request->data['popedomname']);
                }
            }
            //修改
            echo json_encode($message);exit();
        }else{
            $workFlowEntity = $Popedomlist->getWorkFlow('popedom_workflow');
            $workId = $workFlowEntity->popedomid;
            $this->set('_workFlow',$workFlowEntity);
            if($id){
                $data = $Popedomlist->find()->where(array('popedomid'=>$id))->toArray();
                $this->set('data',$data[0]);
            }
        }
    }

}