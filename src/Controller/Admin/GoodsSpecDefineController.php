<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2015/10/15
 * Time: 11:10
 */

namespace App\Controller\Admin;


use App\Controller\AdminController;
use App\Controller\SobeyController;
use Cake\ORM\TableRegistry;

class GoodsSpecDefineController extends AdminController{

    //分页
    public $paginate = [
    'limit' => 15,
    ];
    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_commodity_specs');
        if (! $checkPopedomlist) {
            return $this->redirect('/admin/');
        }
    }
    //显示页面
    public function index($group_id=0,$name = ''){
        $where = array();
        // filter条件拼凑
        if ($name) {
            $where['OR'] =array('spec_name like'=>"%$name%",'spec_code like'=>"%$name%");
        }
        $goods_spec_define = TableRegistry::get('GoodsSpecDefine');
        $group = $goods_spec_define->find('all')->select(['group_id','group_name'])->group(['group_id']);
        if($group_id == 0){
            $spec_data = $goods_spec_define->find('all')->where($where);
            $group_name['group_name'] = '全部';
        }else{
            $spec_data = $goods_spec_define->find('all')->where(['group_id'=>$group_id])->where($where);
            $group_name = $goods_spec_define->find()->select(['group_name'])->where(['group_id'=>$group_id,'group_name <>' =>''])->first();
        }
        $spec_data=$this->paginate($spec_data);
        if($group_id == '-1'){
            $this->set('groups',$group);
        }else{
            $this->set('group_id',$group_id);
            $this->set('spec_data',$spec_data);
        }
        $this->set('name',$name);
        $this->set('group',$group);
        $this->set('group_name',$group_name['group_name']);
    }

    public function addspec($group_id=0){
        $public = new PublicController();
        $message = array('code'=>1,'msg'=>'操作失败');
        $goods_spec_define = TableRegistry::get('GoodsSpecDefine');
        if($this->request->is('post')){
            $goods_spec_defines = $goods_spec_define->newEntity();
            if(isset($this->request->data['group_id'])){
                $goods_spec_defines = $goods_spec_define->patchEntity($goods_spec_defines,$this->request->data);
                $update = $goods_spec_define->save($goods_spec_defines);
                if($update){
                   $message = array('code'=>0,'msg'=>'操作成功');
                    $public->adminlog('GoodsSpecDefine','修改规格分组---'.$this->request->data['group_name']);
                }
            }else{
                $group_id = $goods_spec_define->find()->select(['group_id'])->order(['group_id'=>'desc'])->group(['group_id'])->toArray();
                $group_name = $goods_spec_define->find()->where(['group_name' => $this->request->data['group_name']])->first();
                if(!empty($group_name)){
                    $message = array('code'=>1,'msg'=>'已存在该规格分组名称');
                    echo json_encode($message);exit;
                }
                $group_id = $group_id[0]['group_id']+1;
                $this->request->data['group_id'] = $group_id;
                $goods_spec_defines = $goods_spec_define->newEntity();
                $goods_spec_defines = $goods_spec_define->patchEntity($goods_spec_defines,$this->request->data);
                $result = $goods_spec_define->save($goods_spec_defines);
                if($result){
                    $message = array('code'=>0,'msg'=>'操作成功');
                    $public->adminlog('GoodsSpecDefine','添加规格分组---'.$this->request->data['group_name']);
                }
            }
            echo json_encode($message);exit;
    }else{
        if($group_id){
            $data = $goods_spec_define->find()->where(array('group_id'=>$group_id))->group(['group_id'])->toArray();
            $this->set('data',$data[0]);
        }
    }

}


public function addedit($id=0)
{
    $public = new PublicController();
    $goods_spec_define = TableRegistry::get('GoodsSpecDefine');
    $group = $goods_spec_define->find('all')->select(['group_id', 'group_name'])->group(['group_id'])->toArray();
        //var_dump($group);exit;
    $this->set('group', $group);
    $message = array('code'=>1,'msg'=>'操作失败');

    if ($this->request->is('post')) {
            //修改
        if (isset($this->request->data['id'])) {
            $group_name = $goods_spec_define->find('all')->select(['group_name'])->where(array('group_id' => $this->request->data['group_id']))->toArray();
            $this->request->data['group_name']=$group_name[0]['group_name'];
            $spec_id = $this->request->data['id'];
            $goods_spec_defines = $goods_spec_define->newEntity();
            $goods_spec_defines = $goods_spec_define->patchEntity($goods_spec_defines,$this->request->data);
            $update = $goods_spec_define->save($goods_spec_defines);
            if($update){
                $message = array('code'=>0,'msg'=>'操作成功');
                $public->adminlog('GoodsSpecDefine','修改规格---'.$this->request->data['group_name'].'-'.$this->request->data['spec_name']);
            }
        } else {
                //添加
            if ($this->request->data) {
                $group_name = $goods_spec_define->find('all')->select(['group_name'])->where(array('group_id' => $this->request->data['group_id']))->toArray();
                $this->request->data['group_name']=$group_name[0]['group_name'];
                $goods_spec_defines = $goods_spec_define->newEntity();
                $goods_spec_defines = $goods_spec_define->patchEntity($goods_spec_defines,$this->request->data);
                $result = $goods_spec_define->save($goods_spec_defines);
                if($result){
                    $message = array('code'=>0,'msg'=>'操作成功');
                    $public->adminlog('GoodsSpecDefine','添加规格---'.$this->request->data['group_name'].'-'.$this->request->data['spec_name']);
                }
            }
        }
        echo json_encode($message);exit;
    }else{
        //显示页面
        if ($id) {
            $data = $goods_spec_define->find('all')->where(array('id'=>$id))->toArray();
            $this->set('data',$data[0]);
        }
    }
}


    //删除
public function specdel(){
    $public = new PublicController();
    $message = array('code'=>1,'msg'=>'操作失败');
    if($this->request->data['group_id']){
        $goods_spec_define = TableRegistry::get('GoodsSpecDefine');
        $group_id=$this->request->data['group_id'];
        $query = $goods_spec_define->find('all')->where(['group_id'=>$group_id,'spec_name <>'=>''])->toArray();
        $data = $goods_spec_define->find()->where(['group_id'=>$group_id])->first();
        if (empty($query)) {
            $res = $goods_spec_define->deleteAll(array('group_id'=>$group_id));
        }else{
            $message = array('code'=>1,'msg'=>'该规格有子规格描述，不能删除');
            echo json_encode($message);exit();
        }
        if($res){
            $message = array('code'=>0,'msg'=>'操作成功');
            $public->adminlog('GoodsSpecDefine','删除规格分组---'.$data['group_name']);
        }
        echo json_encode($message);exit();
    }
}
    //删除
public function delete(){
    $public = new PublicController();
    $message = array('code'=>1,'msg'=>'操作失败');
    if($this->request->data['id']){
        $goods_spec_define = TableRegistry::get('GoodsSpecDefine');
        $id=$this->request->data['id'];
        $data = $goods_spec_define->find()->where(['id'=>$id])->first();
        $res = $goods_spec_define->deleteAll(array('id'=>$id));
        if($res){
            $message = array('code'=>0,'msg'=>'操作成功');
            $public->adminlog('GoodsSpecDefine','删除规格---'.$data['group_name'].'-'.$data['spec_name']);
        }
        echo json_encode($message);exit();
    }
}



}