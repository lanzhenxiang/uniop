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
use Cake\Datasource\ConnectionManager;

class AgentController extends AdminController
{
    public $paginate = [
        'limit' => 15,
    ];

    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_para_region');
        if (! $checkPopedomlist) {
            return $this->redirect('/admin/');
        }
    }

    public function index($name="")
    {
        $where = array();
        if($name){
            $where["agent_name like"] = "%$name%";
        }
        $where["parentid"]='0';
        $agent = TableRegistry::get('Agent');

        $query = $agent->find('all')->where($where)->contain("Accounts")->order(['sort_order'=>'ASC']);
        // debug($this->paginate($query));die;
        // $this->set('c',$this);
        $this->set('name',$name);
        $this->set('_data', $this->paginate($query));

    }

    public function arealist($id,$name="")
    {
        $agent = TableRegistry::get('Agent');
        if($name){
            $where["agent_name like"] = "%$name%";
        }
        $where["parentid"]=$id;
        $query = $agent->find('all')->where($where)->contain("Accounts")->order(['sort_order'=>'ASC']);
        $this->set('c',$this);
        $this->set('_data',$this->paginate($query));
        $this->set('name',$name);
        $this->set('_agentid',$id);
    }

    //定义云主机计算能力
    public function agentset($agentId,$areaId,$name="")
    {
        $where=array('set_type like'=>'%云主机%');
        if($name){
            $where['OR'] =array('SetHardware.set_name like'=>"%$name%",'SetHardware.set_code like'=>"%$name%");
        }
        $table = TableRegistry::get('SetHardware');
        $query = $table->find('all')->where($where)->order(['SetHardware.set_id'=>'ASC']);

        $setIdList = $this->getSetPowerID($areaId);

        $this->set('_setIdList',$setIdList);

        $this->set('_data',$this->paginate($query));
        $agent = TableRegistry::get('Agent');

        $agent_entity=$agent->get($agentId);
        $area_entity=$agent->get($areaId);

        $this->set('_agent',$agent_entity);
        $this->set('_area',$area_entity);
        $this->set('name',$name);
    }

    public function imageset($agentId,$areaId,$name="")
    {
        $where=array('Imagelist.image_type'=>'1');
        if($name){
            $where['OR'] =array('Imagelist.image_name like'=>"%$name%",'Imagelist.image_code like'=>"%$name%",'Imagelist.plat_form like'=>"%$name%");
        }
        $table = TableRegistry::get('Imagelist');
        $query = $table->find('all')->where($where)->order(['Imagelist.id'=>'ASC']);
        $setIdList = $this->getImagePowerID($areaId);

        $this->set('_imageIDList',$setIdList);

        $this->set('_data',$this->paginate($query));
        $agent = TableRegistry::get('Agent');

        $agent_entity=$agent->get($agentId);
        $area_entity=$agent->get($areaId);

        $this->set('_agent',$agent_entity);
        $this->set('_area',$area_entity);
        $this->set('name',$name);
    }

    public function Departmentset($agentId,$areaId,$name="")
    {
       // $where=array('Departments.type'=>'platform');
        $where =[];
        if($name){
            $where['OR'] =array('Departments.name like'=>"%$name%",'Departments.dept_code like'=>"%$name%");
        }
        $table = TableRegistry::get('Departments');
        $query = $table->find('all')->where($where)->order(['Departments.id'=>'ASC']);
        $setIdList = $this->getDepartmentPowerID($areaId);
        $this->set('_departmentIDList',$setIdList);

        $this->set('_data',$this->paginate($query));
        $agent = TableRegistry::get('Agent');

        $agent_entity=$agent->get($agentId);
        $area_entity=$agent->get($areaId);

        $this->set('_agent',$agent_entity);
        $this->set('_area',$area_entity);
        $this->set('name',$name);
    }

    private function getSetPowerID($agentId)
    {
        $table = TableRegistry::get('AgentSet');
        $query = $table->find("all")->where(array('agent_id'=>$agentId))->toArray();
        $setIDSTR = "";
        foreach ($query as $key => $value) {
            if(!empty($value["set_id"])){
                $setIDSTR .= $value["set_id"].",";
            }
        }
        if(strlen($setIDSTR)>1){
            $setIDSTR = substr($setIDSTR, 0, -1);
        }
        return $setIDSTR;
    }

    private function getImagePowerID($agentId)
    {
        $table = TableRegistry::get('AgentImagelist');
        $query = $table->find("all")->where(array('agent_id'=>$agentId))->toArray();
        $setIDSTR = "";
        foreach ($query as $key => $value) {
            if(!empty($value["image_id"])){
                $setIDSTR .= $value["image_id"].",";
            }
        }
        if(strlen($setIDSTR)>1){
            $setIDSTR = substr($setIDSTR, 0, -1);
        }
        return $setIDSTR;
    }

    private function getDepartmentPowerID($agentId)
    {
        $table = TableRegistry::get('AgentDepartments');
        $query = $table->find("all")->where(array('agent_id'=>$agentId))->toArray();
        $setIDSTR = "";
        foreach ($query as $key => $value) {
            if(!empty($value["dept_id"])){
                $setIDSTR .= $value["dept_id"].",";
            }
        }
        if(strlen($setIDSTR)>1){
            $setIDSTR = substr($setIDSTR, 0, -1);
        }
        return $setIDSTR;
    }

    private function getDesktopPowerID($agentId)
    {
        $table = TableRegistry::get('DesktopSet');
        $query = $table->find("all")->where(array('agent_id'=>$agentId))->toArray();
        $setIDSTR = "";
        foreach ($query as $key => $value) {
            if(!empty($value["set_id"])){
                $setIDSTR .= $value["set_id"].",";
            }
        }
        if(strlen($setIDSTR)>1){
            $setIDSTR = substr($setIDSTR, 0, -1);
        }
        return $setIDSTR;
    }

    //保存计算能力
    public function saveAgentPower()
    {
        $this->layout = false;
        $data = $this->request->data;
        $agentid = $data["agentid"];
        $table = TableRegistry::get('AgentSet');
        $data_array = explode(',',$data["req"]);
        $table->deleteAll(array('agent_id'=>$agentid));
        foreach ($data_array as $key => $value) {

            $entity = $table->newEntity();
            $entity->agent_id=$agentid;
            $entity->set_id=$value;
            $table->save($entity);
        }
        $message = array('code'=>0,'msg'=>'操作成功');
        echo json_encode($message);exit;
    }

    //保存镜像计算能力
    public function saveImagePower()
    {
        $this->layout = false;
        $data = $this->request->data;
        $agentid = $data["agentid"];
        $table = TableRegistry::get('AgentImagelist');
        $data_array = explode(',',$data["req"]);
        $table->deleteAll(array('agent_id'=>$agentid));

        foreach ($data_array as $key => $value) {

            $entity = $table->newEntity();
            $entity->agent_id=$agentid;
            $entity->image_id=$value;
            $table->save($entity);
        }
        $message = array('code'=>0,'msg'=>'操作成功');
        echo json_encode($message);exit;
    }

    //保存租户
    public function saveDepartmentPower()
    {
        $this->layout = false;
        $data = $this->request->data;
        $agentid = $data["agentid"];
        $table = TableRegistry::get('AgentDepartments');
        $data_array = explode(',',$data["req"]);
        $table->deleteAll(array('agent_id'=>$agentid));
        foreach ($data_array as $key => $value) {

            $entity = $table->newEntity();
            $entity->agent_id=$agentid;
            $entity->dept_id=$value;
            $table->save($entity);
        }
        $message = array('code'=>0,'msg'=>'操作成功');
        echo json_encode($message);exit;
    }

    //保存云桌面计算能力计算能力
    public function saveDesktopPower()
    {
        $this->layout = false;
        $data = $this->request->data;
        $agentid = $data["agentid"];
        $table = TableRegistry::get('DesktopSet');
        $data_array = explode(',',$data["req"]);
        $table->deleteAll(array('agent_id'=>$agentid));

        foreach ($data_array as $key => $value) {

            $entity = $table->newEntity();
            $entity->agent_id=$agentid;
            $entity->set_id=$value;
            $table->save($entity);
        }
        $message = array('code'=>0,'msg'=>'操作成功');
        echo json_encode($message);exit;
    }

    public function edit($id=0){
        $public = new PublicController();
        $agent = TableRegistry::get('Agent');
        $imagelist = TableRegistry::get('Imagelist');
        $sethardware = TableRegistry::get('SetHardware');
        $agentimagelist = TableRegistry::get('AgentImagelist');
        $agentset = TableRegistry::get('AgentSet');
        $virtual = TableRegistry::get('Systemsetting');
        if($this->request->is('get')){
            $data['agent']  = $agent->find('all')->where(array('parentid = '=> '0'))->select(['id','agent_name','class_code'])->toArray();
            $data['imagelist']  = $imagelist->find('all')->toArray();
            $data['sethardware']  = $sethardware->find('all')->toArray();
            //虚拟化技术
            $data['virtual']=$virtual->find('all')->where(array('para_code like' =>'virtual%'))->toArray();

            $this->set('query',$data);
             //编辑时
            if($id){
                $department_data['agent'] = $agent->find('all',array('conditions'=>array('id'=>$id)))->toArray();
                // $department_data['agentimagelist'] = $agentimagelist->find('all',array('conditions'=>array('agent_id'=>$id)))->select('image_id')->toArray();
                // $department_data['agentset'] = $agentset->find('all',array('conditions'=>array('agent_id'=>$id)))->toArray();
                // foreach ($department_data['agentimagelist'] as $key => $value) {
                //     $department_data['image_id'][]=$value->image_id;
                // }
                // foreach ($department_data['agentset'] as $key => $val) {
                //     $department_data['set_id'][]=$val->set_id;
                // }
                $this->set('department_data',$department_data);
            }
        }else{
            // var_dump($this->request->data);exit;
            // debug($this->request->data);die();
            if (empty($this->request->data['class_code'])) {
                $display_name = split('-', $this->request->data['display_name']);
                if ($display_name[0] == "顶级厂商") {
                    $code = $agent->find()->order(['class_code DESC'])->first();
                    $code = (int)substr($code['class_code'] , 0 , 3);
                    $code++;
                    $code = sprintf("%03d", $code);

                }else{
                    $code2 = 1; //区域class_code后三位
                    $parentid = $this->request->data['parentid'];
                    $agnet_code = $agent->find()->where(['id' => $parentid])->first();
                    $code1 = $agnet_code['class_code'];//区域前三位
                    $code = $agent->find()->where(['parentid' => $parentid])->order(['class_code DESC'])->first();
                    if(!empty($code)){
                        $code2 = (int)substr($code['class_code'] , 3 , 6);
                        $code2++;
                    }
                    $code2 = sprintf("%03d", $code2);
                    $code = $code1.$code2;//拼接区域class_code
                }
                $this->request->data['class_code'] = $code;
            }
            if(empty($this->request->data['id'])&& !empty($this->request->data['region_code'])){
                $code = $this->request->data['region_code'];
                $checkCode = $agent->find()->where(['region_code' => $code])->first();
                if(!empty($checkCode)){
                    $message = array('code'=>1,'msg'=>'已存在该地域Code');
                    echo json_encode($message);exit();
                }
            }
            if(!empty($this->request->data['check-image'])&&empty(!$this->request->data['check-hardware'])){
                $check_image=explode(',', $this->request->data['check-image']);
                $check_hardware=explode(',', $this->request->data['check-hardware']);
            }

            // $message = array('code'=>1,'msg'=>'操作失败');
            unset($this->request->data['check-image']);
            unset($this->request->data['check-hardware']);
            unset($this->request->data['imagelist']);
            unset($this->request->data['hardware']);
            $this->request->data['display_name'] = $this->request->data['display_name']. $this->request->data['agent_name'];
            //编辑虚拟化技术
            if(isset($this->request->data['virtual_technology'])) {
                $this->request->data['virtual_technology'] = implode(',', $this->request->data['virtual_technology']);
            }else{
                $this->request->data['virtual_technology']='';
            }
            $this->request->data["create_by"]=$this->request->session()->read('Auth.User.id'); //uid
            $this->request->data["create_time"]=date('Y-m-d H:i:s',time());
            $order = $agent->newEntity();

            //编辑厂商
            $order = $agent->patchEntity($order,$this->request->data);
            $result = $agent->save($order);
            $info['agent_id']=$result->id;
            $res_image = $agentimagelist->deleteAll(array('agent_id'=>$info['agent_id']));
            $res_set = $agentset->deleteAll(array('agent_id'=>$info['agent_id']));
            if($result){
                if(empty($this->request->data['id'])){
                    $public->adminlog('Agent','添加厂商或者区域---'.$this->request->data['display_name']);
                }else{
                    $public->adminlog('Agent','修改厂商或者区域---'.$this->request->data['display_name']);
                }
                if(!empty($check_image)){
                    $rs = '';
                    foreach ($check_image as $key => $image) {
                        if($image!=""){
                            $info['image_id']=$image;
                            $agentimageinfo = $agentimagelist->newEntity();
                            $agentimageinfo = $agentimagelist->patchEntity($agentimageinfo,$info);
                            $rs = $agentimagelist->save($agentimageinfo);

                        }
                    }
                    if($rs){
                        $public->adminlog('AgentImagelist','修改厂商或者区域的可选镜像---'.$this->request->data['display_name']);
                    }else if($res_image >0){
                        $public->adminlog('AgentImagelist','删除厂商或者区域的可选镜像---'.$this->request->data['display_name']);
                    }
                }
                if(!empty($check_hardware)){
                    $query = '';
                    foreach ($check_hardware as $key => $hardware) {
                        if($hardware!=""){
                            $info['set_id']=$hardware;
                            $agentsetinfo = $agentset->newEntity();
                            $agentsetinfo = $agentset->patchEntity($agentsetinfo,$info);
                            $query = $agentset->save($agentsetinfo);

                        }
                    }
                    if($query){
                        $public->adminlog('AgentSet','修改厂商或者区域的可选硬件套餐---'.$this->request->data['display_name']);
                    }else if($res_set >0){
                        $public->adminlog('AgentSet','删除厂商或者区域的可选硬件套餐---'.$this->request->data['display_name']);
                    }
                }

                $message = array('code'=>0,'msg'=>'操作成功');
            }
            echo json_encode($message);exit();
        }
    }

    public function areaedit($agentid=0,$id=0){
        $virtual = TableRegistry::get('Systemsetting');
        $agent = TableRegistry::get('Agent');
        $agent_entity  = $agent->get($agentid);
        if($this->request->is('get')){

            //虚拟化技术
            $data['virtual']=$virtual->find('all')->where(array('para_code like' =>'virtual%'))->toArray();
            $this->set('_query',$data);
            $this->set('_agent',$agent_entity);
            if($id!=0){
                $department_data['agent'] = $agent->find('all',array('conditions'=>array('id'=>$id)))->toArray();
                $this->set('department_data',$department_data);
            }
        }else{
            // debug($this->request->data);die();

            //重组class_code
            $code2 = 1; //区域class_code后三位
            $parentid = $this->request->data['parentid'];
            $agnet_code = $agent->get($agentid);
            $code1 = $agnet_code['class_code'];//区域前三位
            $code = $agent->find()->where(['parentid' => $parentid])->order(['class_code DESC'])->first();
            if(!empty($code)){
                $code2 = (int)substr($code['class_code'] , 3 , 6);
                $code2++;
            }
            $code2 = sprintf("%03d", $code2);
            $code = $code1.$code2;//拼接区域class_code
            //新建时添加
            if(!isset($this->request->data['id'])||empty($this->request->data['id'])) {
                $this->request->data["class_code"] = $code;
                $this->request->data["create_by"] = $this->request->session()->read('Auth.User.id'); //uid
                $this->request->data["create_time"] = date('Y-m-d H:i:s', time());
            }
            //编辑虚拟化技术
            if(isset($this->request->data['virtual_technology'])) {
                $this->request->data['virtual_technology'] = implode(',', $this->request->data['virtual_technology']);
            }else{
                $this->request->data['virtual_technology']='';
            }
            $entity = $agent->newEntity();
            $order = $agent->patchEntity($entity,$this->request->data);
            $result = $agent->save($order);
            $message = array('code'=>0,'msg'=>'操作成功');
            echo json_encode($message);exit();
        }

    }

    //删除
    public function dele(){
        $this->layout = false;
        $public = new PublicController();
        $message = array('code'=>1,'msg'=>'操作失败');
        if($this->request->data['id']){
            $id=$this->request->data['id'];
            $agent = TableRegistry::get('Agent');
            $agentimagelist = TableRegistry::get('AgentImagelist');
            $desktopsetlist = TableRegistry::get('DesktopSet');
            $InstanceBasic = TableRegistry::get('InstanceBasic');
            $agentset = TableRegistry::get('AgentSet');
            $query = $agent->find('all',array('conditions'=>array('parentid'=>$id)))->toArray();
            $deleteInfo = $agent->find()->select(['class_code'])->where(['id' => $id])->first()->toArray();
            if ($query) {
                $message = array('code'=>1,'msg'=>'该供应商有地区不能删除');
                echo json_encode($message);exit;
            }else{
                $instance = $InstanceBasic->find()->where(['location_code' => $deleteInfo['class_code'] ])->first();
                if($instance){
                    $message = array('code'=>1,'msg'=>'该供应商有机器');
                    echo json_encode($message);exit;
                }
                $data = $agent->find()->select(['display_name'])->where(['id'=>$id])->first();
                $result = $agentimagelist->deleteAll(array('agent_id'=>$id));
                $result = $agentset->deleteAll(array('agent_id'=>$id));
                $result = $desktopsetlist->deleteAll(array('agent_id'=>$id));
                $res = $agent->deleteAll(array('id'=>$id));

                if($res){
                    $public->adminlog('Agent','删除厂商或者区域---'.$data['display_name']);
                    $message = array('code'=>0,'msg'=>'操作成功');
                }
                echo json_encode($message);exit;
            }
        }
    }


    public function check($data,$id){

        $departments = TableRegistry::get('Departments');
        $sun = $departments->find('all')->select(['id'])->where(array('parent_id'=>$id))->toArray();
        if(!empty($sun)){
            foreach($sun as $va){
                $data[]=$va['id'];
                $data =$this->check($data,$va['id']);
            }
        }
        return $data;
    }

    public function account_department(){
        $departments = TableRegistry::get('Departments');
        //动态配置behavior
        $departments->behaviors()->Departments->config('scope',['1'=>1]);
        $data = $departments->find('optionList')->select(['id','name','parent_id'])->toArray();
        return $data;
    }

    /**
     * 对数据进行树形结构排序
     */
    private function _get_tree($cate,$pid=0,$level=0,$html='--'){
        $tree = array();
        foreach($cate as $v){
            if($v['parentid'] == $pid){
                $v['level'] = $level;
                $v['html'] = str_repeat($html,$level);
                $tree[] = $v;
                $tree = array_merge($tree, $this->_get_tree($cate,$v['id'],$level+1));
            }
        }
        return $tree;
    }

    public function getUserById($id)
    {
        $account = TableRegistry::get('Accounts');
        return $account->findById($id);
    }

    public function getAgentById($id)
    {
        $agent = TableRegistry::get('Agent');
        return $agent->findById($id)->first();
    }

    public function getCheckIds($areaId)
    {
        $this->layout = false;
        $setIdList = $this->getDesktopPowerID($areaId);
        echo $setIdList;exit;
    }
}