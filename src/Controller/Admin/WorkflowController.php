<?php
/**
* 文件用途描述
* 
* @file: WorkflowController.php
* @date: 2016年1月20日 下午2:28:36
* @author: xingshanghe
* @email: xingshanghe@icloud.com
* @copyright poplus.com
*
*/
namespace App\Controller\Admin;

use App\Controller\AdminController;
use App\Controller\SobeyController;
use Cake\ORM\TableRegistry;
use Cake\ORM\Behavior;

class WorkflowController extends AdminController
{
    public $paginate = [
    'limit' => 15,
    ];

    //列表
    public function lists($name ='') {
        $where = array();
        if ($name) {
            $where['flow_name like'] = '%' . $name . '%';
        }
        $workflow_template_table = TableRegistry::get('WorkflowTemplate');
        $workflow_template_data = $workflow_template_table->find('all')->where($where);
        $data = $this->paginate($workflow_template_data); 
        $this->set('data',$data);
        $this->set('name',$name);
    }
    
    //添加修改订单流程
    public function addedit($id=0){
        $public = new PublicController();
        
        $workflow_template_table = TableRegistry::get('WorkflowTemplate');
        $workflow_detail_table = TableRegistry::get('WorkflowDetail');
        $workflow_detail_table->removeBehavior('Tree');
        if($this->request->is('get')){
            if($id){
                $data = $workflow_template_table->find('all')->where(['flow_id'=>$id])->first();
                $this->set('data',$data);
            }
        }else{
            $flow_id = empty($this->request->data['flow_id'])?0:$this->request->data['flow_id'];
            $code = $workflow_template_table ->find()->where(['flow_code'=>$this->request->data['flow_code'],'flow_id <>' =>$flow_id])->first();
            if ($code) {
                $message = array('code'=>1,'msg'=>'编码重复');
                echo json_encode($message);exit();
            }
            $name = $workflow_template_table ->find()->where(['flow_name'=>$this->request->data['flow_name'],'flow_id <>' =>$flow_id])->first();
            if ($name) {
                $message = array('code'=>1,'msg'=>'名称重复');
                echo json_encode($message);exit();
            }

            $order = $workflow_template_table->newEntity();
            $order = $workflow_template_table->patchEntity($order,$this->request->data);
            $result = $workflow_template_table->save($order);
            if(empty($this->request->data['flow_id'])){
                //添加开始步骤
                $info['flow_id']=$result['flow_id'];
                $info['step_code']='start';
                $info['step_name']='提交订单';
                $info['parent_id']='0';
                $info['action_type']='1';
                $info['lft']='1';
                $info['rgt']='4';
                $info['step_popedom_code']='__stystem';
                $start_data = $workflow_detail_table->newEntity();
                $start_data = $workflow_detail_table->patchEntity($start_data,$info);
                $start_res = $workflow_detail_table->save($start_data);
                if(!empty($start_res)){
                    //添加结束步骤
                    $info['step_code']='end';
                    $info['step_name']='结束';
                    $info['parent_id']=$start_res['id'];
                    $info['action_type']='1';
                    $info['lft']='2';
                    $info['rgt']='3';
                    $info['step_popedom_code']='__stystem';
                    $end_data = $workflow_detail_table->newEntity();
                    $end_data = $workflow_detail_table->patchEntity($end_data,$info);
                    $end_res = $workflow_detail_table->save($end_data);
                }
            }
            if($result){
                $message = array('code'=>0,'msg'=>'操作成功');
                if(empty($this->request->data['flow_id'])){
                    $public->adminlog('WorkflowTemplate','添加订单流程---'.$this->request->data['flow_name']);
                }else{
                    $public->adminlog('WorkflowTemplate','修改订单流程---'.$this->request->data['flow_name']);
                }
            }
            echo json_encode($message);exit();
        }
    }
    
    public function edit() {
        ;
    }

    public function addnew($flow_id=0){
        $public = new PublicController();
        $workflow_detail_table = TableRegistry::get('WorkflowDetail');
        $workflow_template_table = TableRegistry::get('WorkflowTemplate');
        $workflow_detail_table->removeBehavior('Tree');
        $popedomlist = TableRegistry::get('Popedomlist');

        if($this->request->is('get')){
            $popedomlist_info = $popedomlist->find()->where(['popedomname' => 'popedom_workflow'])->first();
            $popedomlist_info = $popedomlist->find('all')->where(['parent_id' => $popedomlist_info['popedomid']])->toArray();
            $this->set('popedomlist_info',$popedomlist_info);
            //获取流程步骤
            $data_detail = $workflow_detail_table->find('all')->where(['flow_id' => $flow_id,'AND' => [['step_code <>' => 'start'],['step_code <>' => 'end']]])->order(['lft'])->toArray();
            if($flow_id){
                $data = $workflow_template_table->find('all')->where(['flow_id'=>$flow_id])->first();
                $this->set('data',$data);
                $this->set('data_detail',$data_detail);
            }
            $this->set('flow_id',$flow_id);
        }else{
            $flow_id = empty($this->request->data['flow_id'])?0:$this->request->data['flow_id'];
            $code = $workflow_template_table ->find()->where(['flow_code'=>$this->request->data['flow_code'],'flow_id <>' =>$flow_id])->first();
            if ($code) {
                $message = array('code'=>1,'msg'=>'编码重复');
                echo json_encode($message);exit();
            }
            $name = $workflow_template_table ->find()->where(['flow_name'=>$this->request->data['flow_name'],'flow_id <>' =>$flow_id])->first();
            if ($name) {
                $message = array('code'=>1,'msg'=>'名称重复');
                echo json_encode($message);exit();
            }

            $details_id = split(',', $this->request->data['flow_order']);
            //
            $start = $workflow_detail_table->find()->where(['flow_id' => $flow_id,'step_popedom_code ' => '__stystem','parent_id ' => 0])->first();
            $end = $workflow_detail_table->find()->where(['flow_id' => $flow_id,'step_popedom_code ' => '__stystem','parent_id <>' => 0])->first();
            $count = $workflow_detail_table->find('all')->where(['flow_id'=>$flow_id])->count();
            $level = 1;
            $info['lft'] = $level;
            $info['rgt'] = 2*$count+1-$level;
            $info['id'] = $start['id'];
            $info['parent_id'] = '0';
            $res = $this->_save_step_data($info);
            foreach ($details_id as $key => $detail_id) {
                if(!empty($detail_id)){
                    $level ++;
                    $info['id'] = $detail_id;
                    if($key == 0){
                        $info['parent_id'] = $start['id'];
                    }else{
                        $info['parent_id'] = $details_id[$key-1];
                    }
                    $info['lft'] = $level;
                    $info['rgt'] = 2*$count+1-$level;
                    $res = $this->_save_step_data($info);
                }
            }
            $level ++;
            $info['id'] = $end['id'];
            if(!empty($details_id[$key])){
                $info['parent_id'] =  $details_id[$key];
            }else{
                $info['parent_id'] = $start['id'];
            }
            $info['lft'] = $level;
            $info['rgt'] = 2*$count+1-$level;
            $res = $this->_save_step_data($info);

            $order = $workflow_template_table->newEntity();
            $order = $workflow_template_table->patchEntity($order,$this->request->data);
            $result = $workflow_template_table->save($order);

            if($result){
                $message = array('code'=>0,'msg'=>'操作成功');
                $public->adminlog('WorkflowTemplate','修改订单流程---'.$this->request->data['flow_name']);
                $public->adminlog('WorkflowTemplate','修改订单流程步骤顺序---'.$this->request->data['flow_name']);
            }
            echo json_encode($message);exit();
        }
    }
    
    //修改步骤页面
    public function flow($flow_id=0) {
        $public = new PublicController();
        $workflow_detail_table = TableRegistry::get('WorkflowDetail');
        $workflow_template_table = TableRegistry::get('WorkflowTemplate');
        $workflow_detail_table->removeBehavior('Tree');
        $popedomlist = TableRegistry::get('Popedomlist');

        if($this->request->is('get')){
            $popedomlist_info = $popedomlist->find()->where(['popedomname' => 'popedom_workflow'])->first();
            $popedomlist_info = $popedomlist->find('all')->where(['parent_id' => $popedomlist_info['popedomid']])->toArray();
            $this->set('popedomlist_info',$popedomlist_info);
            //获取流程步骤
            $data_detail = $workflow_detail_table->find('all')->where(['flow_id' => $flow_id,'AND' => [['step_code <>' => 'start'],['step_code <>' => 'end']]])->order(['lft'])->toArray();
            if($flow_id){
                $data = $workflow_template_table->find('all')->where(['flow_id'=>$flow_id])->first();
                $this->set('data',$data);
                $this->set('data_detail',$data_detail);
            }
            $this->set('flow_id',$flow_id);
        }else {
            $flow_id = empty($this->request->data['flow_id']) ? 0 : $this->request->data['flow_id'];
            $code = $workflow_template_table->find()->where(['flow_code' => $this->request->data['flow_code'], 'flow_id <>' => $flow_id])->first();
            if ($code) {
                $message = array('code' => 1, 'msg' => '编码重复');
                echo json_encode($message);
                exit();
            }
            $name = $workflow_template_table->find()->where(['flow_name' => $this->request->data['flow_name'], 'flow_id <>' => $flow_id])->first();
            if ($name) {
                $message = array('code' => 1, 'msg' => '名称重复');
                echo json_encode($message);
                exit();
            }

            if (!empty($this->request->data['flow_order'])) {//新建时有添加步骤或修改
                $details_id = split(',', $this->request->data['flow_order']);
                $start = $workflow_detail_table->find()->where(['flow_id' => $flow_id, 'step_popedom_code ' => '__stystem', 'parent_id ' => 0])->first();
                $end = $workflow_detail_table->find()->where(['flow_id' => $flow_id, 'step_popedom_code ' => '__stystem', 'parent_id <>' => 0])->first();
                $count = $workflow_detail_table->find('all')->where(['flow_id' => $flow_id])->count();
                $level = 1;
                $info['lft'] = $level;
                $info['rgt'] = 2 * $count + 1 - $level;
                $info['id'] = $start['id'];
                $info['parent_id'] = '0';
                $res = $this->_save_step_data($info);
                foreach ($details_id as $key => $detail_id) {
                    if (!empty($detail_id)) {
                        $level++;
                        $info['id'] = $detail_id;
                        if ($key == 0) {
                            $info['parent_id'] = $start['id'];
                        } else {
                            $info['parent_id'] = $details_id[$key - 1];
                        }
                        $info['lft'] = $level;
                        $info['rgt'] = 2 * $count + 1 - $level;
                        $res = $this->_save_step_data($info);
                    }
                }
                $level++;
                $info['id'] = $end['id'];
                if (!empty($details_id[$key])) {
                    $info['parent_id'] = $details_id[$key];
                } else {
                    $info['parent_id'] = $start['id'];
                }
                $info['lft'] = $level;
                $info['rgt'] = 2 * $count + 1 - $level;
                $res = $this->_save_step_data($info);

                $order = $workflow_template_table->newEntity();
                $order = $workflow_template_table->patchEntity($order, $this->request->data);
                $result = $workflow_template_table->save($order);

                if ($result) {
                    //修改步骤的流程id,确认添加步骤
                    if($flow_id==0) {
                        $workflow_detail_table->updateAll(array('flow_id' => $result['flow_id'],'sure_add'=>1), array('flow_id' => 0));
                    }else{
                        $res=$workflow_detail_table->updateAll(array('sure_add'=>1), array('flow_id' => $flow_id));
                    }
                    $message = array('code' => 0, 'msg' => '操作成功');
                    $public->adminlog('WorkflowTemplate', '修改订单流程---' . $this->request->data['flow_name']);
                    $public->adminlog('WorkflowTemplate', '修改订单流程步骤顺序---' . $this->request->data['flow_name']);
                } else {
                    $workflow_detail_table->deleteAll(array('flow_id' => 0));
                    $message = array('code' => 1, 'msg' => '操作失败');
                }
                echo json_encode($message);
                exit();
            }else{//新建时没添加步骤
                $this->addedit($id=0);
            }
        }
    }

    /**
     * @func 保存流程步骤信息
     * @param $info:步骤信息 
     */
    private function _save_step_data($info){
        $workflow_detail_table = TableRegistry::get('WorkflowDetail');
        $workflow_detail_table->removeBehavior('Tree');//移除Behavior 
        $step_info = $workflow_detail_table->newEntity();
        $step_info = $workflow_detail_table->patchEntity($step_info,$info);
        $res = $workflow_detail_table->save($step_info);
        return  $res;
    }

    //添加修改步骤
    public function addeditDetail($id =0){
        $public = new PublicController();
        $workflow_detail_table = TableRegistry::get('WorkflowDetail');
        $workflow_detail_table->removeBehavior('Tree');
        if($this->request->is('get')){
            if($id){
                $data = $workflow_detail_table->find('all')->where(['flow_id'=>$id])->first();
                $this->set('data',$data);
            }
        }else{
            // var_dump($this->request->data);exit;
            if(isset($this->request->data['flow_id'])&&!empty($this->request->data['flow_id'])) {
                $flow_id = $this->request->data['flow_id'];//原有
            }else{
                $this->request->data['flow_id']=0;
                $flow_id=0;
            }
            if($flow_id==0){//新建
                    //添加开始步骤
                if($workflow_detail_table->find()->where(array('flow_id'=>0,'step_code'=>'start'))->count()==0) {
                    $info['flow_id'] = 0;
                    $info['step_code'] = 'start';
                    $info['step_name'] = '提交订单';
                    $info['parent_id'] = '0';
                    $info['action_type'] = '1';
                    $info['lft'] = '1';
                    $info['rgt'] = '4';
                    $info['step_popedom_code'] = '__stystem';
                    $info['sure_add']=0;
                    $start_data = $workflow_detail_table->newEntity();
                    $start_data = $workflow_detail_table->patchEntity($start_data, $info);
                    $start_res = $workflow_detail_table->save($start_data);
                }
                        //添加结束步骤
                if($workflow_detail_table->find()->where(array('flow_id'=>0,'step_code'=>'end'))->count()==0) {
                    $info['flow_id'] = 0;
                    $info['step_code'] = 'end';
                    $info['step_name'] = '结束';
                    $info['parent_id'] = 0;
                    $info['action_type'] = '1';
                    $info['lft'] = '2';
                    $info['rgt'] = '3';
                    $info['step_popedom_code'] = '__stystem';
                    $info['sure_add']=0;
                    $end_data_origin = $workflow_detail_table->newEntity();
                    $end_data_origin = $workflow_detail_table->patchEntity($end_data_origin, $info);
                    $end_res = $workflow_detail_table->save($end_data_origin);
                }
            }




            if(empty($this->request->data['id'])){
                unset($this->request->data['id']);
                $id = 0;
                $end = $workflow_detail_table->find()->where(['flow_id'=>$flow_id,'step_code'=>'end'])->first();
                $this->request->data['parent_id'] = $end['parent_id'];
            }else{
                $id = $this->request->data['id'];
            }
            $code = $workflow_detail_table ->find()->where(['step_code'=>$this->request->data['step_code'],'flow_id' =>$flow_id,'id <>' =>$id])->first();
            if ($code) {
                $message = array('code'=>1,'msg'=>'编码重复');
                echo json_encode($message);exit();
            }
            $name = $workflow_detail_table ->find()->where(['step_name'=>$this->request->data['step_name'],'flow_id' =>$flow_id,'id <>' =>$id])->first();
            if ($name) {
                $message = array('code'=>1,'msg'=>'名称重复');
                echo json_encode($message);exit();
            }
            $this->request->data['sure_add']=0;
            $order = $workflow_detail_table->newEntity();
            $order = $workflow_detail_table->patchEntity($order,$this->request->data);
            $result = $workflow_detail_table->save($order);

            if($id == 0){
                $end_data['id'] = $end['id'];
                $end_data['parent_id'] = $result['id'];
                $end_info = $workflow_detail_table->newEntity();
                $end_info = $workflow_detail_table->patchEntity($end_info,$end_data);
                $end_res = $workflow_detail_table->save($end_info);

                $count = $workflow_detail_table->find('all')->where(['flow_id'=>$flow_id])->count();
                $detail = $workflow_detail_table->find('all')->where(['flow_id'=>$flow_id])->toArray();
                $query = $this-> _get_tree($detail,$count);

            }

            if($result){
                $message = array('code'=>0,'msg'=>'操作成功');
                if(empty($this->request->data['flow_id'])){
                    $public->adminlog('WorkflowDetail','添加订单步骤---'.$this->request->data['step_name']);
                }else{
                    $public->adminlog('WorkflowDetail','修改订单步骤---'.$this->request->data['step_name']);
                }
            }
            echo json_encode($message);exit();
        }
    }
    public function cancelAddeditDetail(){
        $workflow_detail_table = TableRegistry::get('WorkflowDetail');
        $workflow_detail_table->removeBehavior('Tree');
        $res=$workflow_detail_table->deleteAll(array('sure_add'=>0));
        if($res){
            echo json_encode(array('code'=>0));exit;
        }else{
            echo json_encode(array('code'=>1));exit;
        }
    }
    
    //删除订单流程
    public function delete()
    {
        $public = new PublicController();
        $this->layout = false;
        $message = array('code'=>1,'msg'=>'操作失败');
        if($this->request->data['id']){
            $id=$this->request->data['id'];
            $workflow_detail_table = TableRegistry::get('WorkflowDetail');
            $workflow_detail_table->removeBehavior('Tree');
            $workflow_template_table = TableRegistry::get('WorkflowTemplate');
            
            $data = $workflow_template_table->find()->where(['flow_id'=>$id])->first();
            $res = $workflow_template_table->deleteAll(array('flow_id'=>$id));
            $res = $workflow_detail_table->deleteAll(array('flow_id'=>$id));
            if($res){
                $message = array('code'=>0,'msg'=>'操作成功');
                $public->adminlog('WorkflowTemplate','删除订单流程---'.$data['flow_name']);
            }
            echo json_encode($message);exit;
        }
    }

    //获取步骤信息
    public function getDetail($id =0){
        $workflow_detail_table = TableRegistry::get('WorkflowDetail');
        $data = $workflow_detail_table->find()->where(['id' => $id])->first();
        if(!empty($data)){
            if($data['step_order']>1){
                $data['per_order'] = $data['step_order']-1;
            }else{
                $data['per_order'] = 1;
            }

        }
        echo json_encode($data);exit();
    }

    //删除步骤
    public function deteltDetail($id = 0 ){
        $public = new PublicController();
        $this->layout = false;
        $message = array('code'=>1,'msg'=>'操作失败');
        if($this->request->data['id']){
            $id=$this->request->data['id'];
            $workflow_detail_table = TableRegistry::get('WorkflowDetail');
            $workflow_detail_table->removeBehavior('Tree');
            $data = $workflow_detail_table->find()->where(['id' => $id])->first();
            $query = $workflow_detail_table->find()->where(['parent_id' => $id])->first();
            $flow_id = $data['flow_id'];
            if(!empty($query)){
                $info['id'] = $query['id'];
                $info['parent_id'] = $data['parent_id'];
                $order = $workflow_detail_table->newEntity();
                $order = $workflow_detail_table->patchEntity($order,$info);
                $result = $workflow_detail_table->save($order);
            }
            $data = $workflow_detail_table->find()->where(['id'=>$id])->first();
            $res = $workflow_detail_table->deleteAll(array('id'=>$id));
            
            $count = $workflow_detail_table->find('all')->where(['flow_id'=>$flow_id])->count();
            $detail = $workflow_detail_table->find('all')->where(['flow_id'=>$flow_id])->toArray();
            $query = $this-> _get_tree($detail,$count);
            if($res){
                $message = array('code'=>0,'msg'=>'操作成功');
                $public->adminlog('WorkflowDetail','删除订单步骤、---'.$data['step_name']);
            }
           
        }
        
        echo json_encode($message);exit;
    } 

    //获取步骤列表
    public function detailLists($flow_id = 0){
        $workflow_detail_table = TableRegistry::get('WorkflowDetail');
        $query =  $workflow_detail_table->find('all')->where(['flow_id' => $flow_id,'AND' => [['step_code <>' => 'start'],['step_code <>' => 'end']]])->order(['lft'])->toArray();
        echo json_encode($query);exit;
    }


    /**
     * 对数据进行树形结构排序
     */
    private function _get_tree($cate,$count,$pid=0,$level=1){
        $tree = array();
        foreach($cate as $v){
            if($v['parent_id'] == $pid){

                $data['lft'] = $level;
                $data['rgt'] = 2*$count+1-$level;
                $data['id'] = $v['id'];
                $res = $this->_save_step_data($data);
                $tree[] = $v;
                $tree = array_merge($tree, $this->_get_tree($cate,$count,$v['id'],$level+1));
            }
        }
        return $tree;
    }

}