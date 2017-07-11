<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2016/1/26
 * Time: 13:54
 */

namespace App\Controller\Admin;


use App\Controller\AdminController;
use Cake\ORM\TableRegistry;

class ChargeExtendController extends AdminController{

    private $_charge_objects = [
            'eip'=>'EIP',
            'fics'=>'FICS',
            'disks'=>'块存储',
            'h9000'=>'H9000',
            'vfw'=>'VFW',
            'hive'=>"HIVE",
            'vpc' => 'VPC',
            'elb' => '负载均衡',
            'vbri'=>'边界路由器接口'
        ];

    public $paginate = [
        'limit' => 15,
    ];

    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_charge_extend');
        if (! $checkPopedomlist) {
            return $this->redirect('/admin/');
        }
    }


    public function index($agent_id=0,$charge_object=''){

        $charge_extend  = TableRegistry::get('ChargeExtend');
        $agent_table    = TableRegistry::get("Agent");

        //显示服务列表
        $where = array();
        if($agent_id > 0){
            $where['agent_id'] =$agent_id;
            $agent = $agent_table->get($agent_id);
            $agent_name = $agent->agent_name;
        }else{
            $agent_name = '全部';
        }

        if($charge_object != ""){
            $where['charge_object'] =$charge_object;
            $charge_object_arr = $this->_charge_objects;
            $charge_object_txt = $charge_object_arr[$charge_object];
        }else{
            $charge_object_txt = '全部';
        }

        $query = $charge_extend
            ->find()
            ->where($where);

//             debug($charge_object_arr['disks']);die;

        $agents = $agent_table->find()->where(['parentid'=>0])->toArray();
        $this->set('agents',$agents);
        $charge_object_arr = $this->_charge_objects;
        if(isset($query)){
            $rs=$this->paginate($query)
            ->map(function ($row) use($charge_object_arr){
                $row->charge_object = $charge_object_arr[$row->charge_object];
                    return $row;
            });
            $this->set('data',$rs->toArray());
        }else{
            $rs=$this->paginate();
        }
        $this->set('agent_id',$agent_id);
        $this->set('agent_name',$agent_name);
        $this->set('charge_object',$charge_object);
        $this->set('charge_object_txt',$charge_object_txt);
        $this->set('charge_object_arr',$charge_object_arr);
    }

    /**
     * 其他计费添加，修改
     * @param  integer $id [description]
     * @return [type]      [description]
     */
    public function addedit($id=0){
        $charge_extend = TableRegistry::get('ChargeExtend');
        $agent_table = TableRegistry::get('Agent');
        if($this->request->is('get')){
            $agents = $agent_table->find('all')->select(['id','agent_name'])->where(['parentid'=> 0,'is_enabled'=> 1])->toArray();
            $this->set('agents',$agents);
            $this->set('charge_objects',$this->_charge_objects);
            if($id){
                $charge_extend_entity = $charge_extend->find()->where(array('id'=>$id))->first();
                $this->set('data',$charge_extend_entity);
            }

        }else{
            $data   = $this->request->data;

            $agent  = $agent_table->get($this->request->data['agent_id']);
            $data['agent_name'] = $agent->agent_name;
            $data['region_code'] = $agent->region_code;

            $data['create_by'] = $this->request->session()->read('Auth.User.id');
            $data['create_name'] = $this->request->session()->read('Auth.User.username');

            if($this->request->data['id'] > 0){
                $extend_entity = $charge_extend->get($data['id']);
                $handle_type = '修改';
            }else{
                $data['create_time'] = time();
                $extend_entity = $charge_extend->newEntity();
                $handle_type = '添加';
            }

            $extend_entity = $charge_extend->patchEntity($extend_entity,$data);
            $extend_entity->create_time->timezone('PRC');
            $public = new PublicController();
            if($charge_extend->save($extend_entity)){
                $msg = "保存成功";
                $code = 0;
                $public->adminlog('其他计费',$handle_type.'其他计费成功---计费对象'.$extend_entity->charge_object);
            }else{
                $msg = '保存失败';
                $code = -1;
                $public->adminlog('其他计费',$handle_type.'其他计费失败---计算对象'.$extend_entity->charge_object);
            }
            $result = compact(['msg','code']);
            echo json_encode($result);exit;
        }
    }


    //删除模板
    public function deletes(){
        $message=array('code'=>1,'message'=>'删除其他计费失败');
        $data = $this->request->data;
        $charge_extend = TableRegistry::get('ChargeExtend');
        $extend_entity = $charge_extend->get($data['id']);
        $result = $charge_extend->delete($extend_entity);
        if($result){
            $public = new PublicController();
            $message=array('code'=>0,'message'=>'删除其他计费成功');
            $public->adminlog('其他计费','删除其他计费---计费对象'.$extend_entity->charge_object);
        }
        echo json_encode($message);exit();

    }

    /**
     * 检测其他计费是否存在
     * @return [void]
     */
    public function check(){
        $data = $this->request->data;

        $charge_extend = TableRegistry::get('ChargeExtend');
        $where = array('agent_id'=>$data['agent_id'],'charge_object'=>$data['charge_object']);
        if(!empty($data["id"])){
            $where["id !="]=$data["id"];
        }
        $entity = $charge_extend->find()->where($where)->first();

        if($entity == null){
            $result = ['valid'=>true];
        }else{
            $result = ['valid'=>false];
        }
        echo json_encode($result);exit;
    }

}