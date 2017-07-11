<?php
/**
 * Created by PhpStorm.
 * User: kelly
 * Date: 2017/5/2
 * Time: 15:44
 */
namespace App\Controller\Console;

use App\Controller\Console\ConsoleController;
use Cake\ORM\TableRegistry;
use Cake\Network\Http\Client;
use Cake\Core\Configure;

class BoundaryRouterListController extends ConsoleController
{
    public $paginate = [
        'limit' => 15,
    ];
    public $_pageList = array(
        'total' => 0,
        'rows' => array()
    );
    public function initialize(){
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('ccm_ps_vbr');
        if (! $checkPopedomlist) {
            return $this->redirect('/console/');
        }
    }
    //边界路由器列表
    public function vbr()
    {
        $department_table = TableRegistry::get('Departments');
        $departments = $department_table->find()->select(['id', 'name'])->toArray();
        $this->set('departments', $departments);
        
        $department_id = $this->request->session()->read('Auth.User.department_id105');
        $this->set('department_id', $department_id);
    }

    public function vbrList()
    {
        $basic_table = TableRegistry::get('InstanceBasic');
        $extend_table = TableRegistry::get('VbrExtends');
        $request = $this->request->query;
        $where['InstanceBasic.type'] = 'vbr';
        //租户
        if (isset($request['department_id']) && !empty($request['department_id'])) {
            $where['InstanceBasic.department_id'] = $request['department_id'];
        }
        //搜索
        if (isset($request['search']) && !empty($request['search'])) {
            $search = $request['search'];
            $where['InstanceBasic.name like'] = "%$search%";
        }

        $query = $basic_table->find()->contain(['VbrExtends'])->where($where);
        $this->_pageList['total'] = $query->count();
        
        $data = $query->offset($request['offset'])->limit($request['limit'])->map(function ($row) {
            switch ($row['vbr_extend']['physicallineCode']) {
                case 'PhysicalLine-zrtgPL01':
                    $row['physicallineCode'] = '专线1';
                    break;
                case 'PhysicalLine-zrtgPL02':
                    $row['physicallineCode'] = '专线2';
                    break;
                default:
                    $row['physicallineCode'] = '-';
            }
            return $row;
        })->toArray();
        
        foreach ($data as &$v) {
            $dui  = $basic_table->find()->contain(['VbrExtends'])->where(['VbrExtends.subnet' => $v['vbr_extend']['subnet'], 'VbrExtends.aliyun_vpcCode' => $v['vbr_extend']['aliyun_vpcCode'], 'InstanceBasic.id <>' => $v['id']])->first();
            $v['other'] = $dui['name'];
        }
        $this->_pageList['rows'] = $data;
        echo json_encode($this->_pageList);
        exit;
    }

    //重命名边界路由器
    public function renameVbr()
    {
        $request = $this->request->data;
        $basic_table = TableRegistry::get('InstanceBasic');
        if (!isset($request['vbr_id']) || empty($request['vbr_id'])) {
            echo json_encode(array('code' => 2, 'msg' => '请选择要重命名的边界路由器'));
            exit;
        }
        if (!isset($request['new_name']) || empty($request['new_name'])) {
            echo json_encode(array('code' => 3, 'msg' => '请输入新边界路由器名'));
            exit;
        }
        if ($basic_table->find()->where(array('id <>' => $request['vbr_id'], 'name' => $request['new_name']))->count() > 0) {
            echo json_encode(array('code' => 4, 'msg' => '该名称已存在'));
            exit;
        }
        if ($basic_table->find()->select(['name'])->where(array('id' => $request['vbr_id']))->first()['name'] == $request['new_name']) {
            echo json_encode(array('code' => 5, 'msg' => '未进行修改'));
            exit;
        }
        $res = $basic_table->updateAll(array('name' => $request['new_name']), array('id' => $request['vbr_id']));
        if ($res) {
            echo json_encode(array('code' => 0, 'msg' => '重命名成功'));
            exit;
        } else {
            echo json_encode(array('code' => 1, 'msg' => '重命名失败'));
            exit;
        }
    }

    //删除边界路由器
    public function deleteVbr()
    {

        $checkPopedomlist = parent::checkPopedomlist('ccf_del_vbr');
        if (! $checkPopedomlist) {
            return $this->redirect('/console/');
        }

        $request = $this->request->data;
        if (!isset($request['rows']) || empty($request['rows'])) {
            echo json_encode(array('code' => 2, 'msg' => '请选择要删除的边界路由器'));
            exit;
        }
        $count=0;
        $info='';
        foreach ($request['rows'] as $key => $value) {
            $url=Configure::read('URL');
            $array=array(
                'method' => 'vbr_del',
                'uid' =>  (string)$this->request->session()->read('Auth.User.id'),
                'basicId' => (string)$value['id'],
                'vbrCode' => $value['code']
            );
            set_time_limit(0);
            //设置请求接口不超时
            $http          = new Client();
            $obj_response  = $http->post($url, json_encode($array), array('type' => 'json'));
            $data_response = json_decode($obj_response->body, true);

            if($data_response['Code']==0){
                $count+=1;
            }else{
                $info.=$data_response['Message'].'<br>';
            }
        }
        if($count>0){
            echo json_encode(array('code'=>0,'msg'=>'删除成功'));exit;
        }else{
            echo json_encode(array('code'=>$data_response['Code'],'msg'=>'删除失败','info'=>$info));exit;
        }
    }

    //路由器接口列表
    public function vbrPorts($page = 1)
    {
        $connects_table = TableRegistry::get('VbrConnects');

        $limit = 15;
        if ($page > 0) {
            $offset = $page - 1;
        } else {
            $offset = 0;
        }
        $request = $this->request->query;
        if (!isset($request['vbr_id']) && empty($request['vbr_id'])) {
            $this->render('/Console/BoundaryRouterList/vbr');
        }
        $basic_table = TableRegistry::get('InstanceBasic');
        $extend_table = TableRegistry::get('VbrExtends');
        //路由器信息
        $vbr_data = $basic_table->find()->where(array('id' => $request['vbr_id']))->first();
        $this->set('vbr_data', $vbr_data);


        //路由器接口列表
        $where['basic_id'] = $request['vbr_id'];
        $query = $connects_table->find()->where($where);
        $list = $query->offset($offset)->limit($limit)->toArray();
        
        

        $all = array();
        foreach ($list as $key => $value) {
            
            // vbr信息
            $extend_data = $extend_table->find()->where(['basic_id' => $value['basic_id']])->first();
            $ali_location = $basic_table->find()->where(array('InstanceBasic.code' => $extend_data['aliyun_vpcCode']))->first();
            
            $initiate['id'] = $value['id'];
            $accept['id'] = $value['id'];
            //接口
            $initiate['name'] = $value['customName'] . '-发';
            $accept['name'] = $value['customName'] . '-收';
            //对端接口
            $initiate['opposide_name'] = $value['customName'] . '-收';
            $accept['opposide_name'] = $value['customName'] . '-发';
            //接口code
            $initiate['initiatingSideRouterInterfaceCode'] = $value['initiatingSideRouterInterfaceCode'];
            $accept['initiatingSideRouterInterfaceCode'] = $value['acceptingSideRouterInterfaceCode'];
            //对端接口code
            $initiate['acceptingSideRouterInterfaceCode'] = $value['acceptingSideRouterInterfaceCode'];
            $accept['acceptingSideRouterInterfaceCode'] = $value['initiatingSideRouterInterfaceCode'];
            //接口类型
            $initiate['type'] = '发起端';
            $accept['type'] = '接收端';
            //规格
            $initiate['spec'] = $accept['spec'] = $value['spec'];
            //本端部署区位
            $vbr_data = $basic_table->find()->contain(['VbrExtends'])->where(array('InstanceBasic.id' => $value['basic_id']))->first();
            $initiate['this_location'] = $vbr_data['location_name'];
            $accept['this_location'] = $ali_location['location_name'];
            //对端部署区位
            $initiate['opposide_location'] = $ali_location['location_name'];
            $accept['opposide_location'] = $vbr_data['location_name'];
            //对端vpc
            $initiate['opposide_vpc'] = $vbr_data['vbr_extend']['aliyun_vpcCode'];
            $accept['opposide_vpc'] = $vbr_data['vpc'];


            $all[] = $initiate;
            $all[] = $accept;
        }
        $data['total'] = ceil($query->count() / $limit);
        $data['ports'] = $all;
        $this->set('data', $data);
        $this->set('page', $page);
    }

    //ajax分页
    public function getports($page = 1)
    {
        $basic_table = TableRegistry::get('InstanceBasic');
        $extend_table = TableRegistry::get('VbrExtends');
        $connects_table = TableRegistry::get('VbrConnects');
        $request = $this->request->query;
        $limit = 15;
        if ($page > 0) {
            $offset = $page - 1;
        } else {
            $offset = 0;
        }
        $offset = $offset * $limit;

        //路由器接口列表
        if (isset($request['search']) && !empty(trim($request['search']))) {
            $search = trim($request['search']);
            $where['customName like'] = "%$search%";
        }
        $where['basic_id'] = $request['vbr_id'];
        $query = $connects_table->find()->where($where);
        $list = $query->offset($offset)->limit($limit)->toArray();
        $all = array();
        foreach ($list as $key => $value) {
            $initiate['id'] = $value['id'];
            $accept['id'] = $value['id'];
            //接口
            $initiate['name'] = $value['customName'] . '-发';
            $accept['name'] = $value['customName'] . '-收';
            //对端接口
            $initiate['opposide_name'] = $value['customName'] . '-收';
            $accept['opposide_name'] = $value['customName'] . '-发';
            //接口code
            $initiate['initiatingSideRouterInterfaceCode'] = $value['initiatingSideRouterInterfaceCode'];
            $accept['initiatingSideRouterInterfaceCode'] = $value['acceptingSideRouterInterfaceCode'];
            //对端接口code
            $initiate['acceptingSideRouterInterfaceCode'] = $value['acceptingSideRouterInterfaceCode'];
            $accept['acceptingSideRouterInterfaceCode'] = $value['initiatingSideRouterInterfaceCode'];
            //接口类型
            $initiate['type'] = '发起端';
            $accept['type'] = '接收端';
            //规格
            $initiate['spec'] = $accept['spec'] = $value['spec'];
            //本端部署区位
            $vbr_data = $basic_table->find()->contain(['VbrExtends'])->where(array('InstanceBasic.id' => $value['basic_id']))->first();
            $initiate['this_location'] = $vbr_data['location_name'];
            $accept['this_location'] = $vbr_data['vbr_extend']['customName'];
            //对端部署区位
            $initiate['opposide_location'] = $vbr_data['vbr_extend']['customName'];
            $accept['opposide_location'] = $vbr_data['location_name'];
            //对端vpc
            $initiate['opposide_vpc'] = $vbr_data['vbr_extend']['aliyun_vpcCode'];
            $accept['opposide_vpc'] = $vbr_data['vpc'];


            $all[] = $initiate;
            $all[] = $accept;
        }
        $data['total'] = ceil($query->count() / $limit);
        $data['data'] = $all;
        $data['page'] = $page;
        echo json_encode($data);
        exit();
        $this->lauout = 'ajax';
    }

//重命名接口
    public function renamePort()
    {
        $request = $this->request->data;
        $connects_table = TableRegistry::get('VbrConnects');
        if (!isset($request['port_id']) || empty($request['port_id'])) {
            echo json_encode(array('code' => 2, 'msg' => '请选择要重命名的路由器接口'));
            exit;
        }
        if (!isset($request['new_name']) || empty($request['new_name'])) {
            echo json_encode(array('code' => 3, 'msg' => '请输入新路由器接口名'));
            exit;
        }
        if ($connects_table->find()->where(array('id <>' => $request['port_id'], 'customName' => $request['new_name']))->count() > 0) {
            echo json_encode(array('code' => 4, 'msg' => '该名称已存在'));
            exit;
        }
        if ($connects_table->find()->select(['customName'])->where(array('id' => $request['port_id']))->first()['customName'] == $request['new_name']) {
            echo json_encode(array('code' => 5, 'msg' => '未进行修改'));
            exit;
        }
        $res = $connects_table->updateAll(array('customName' => $request['new_name']), array('id' => $request['port_id']));
        if ($res) {
            echo json_encode(array('code' => 0, 'msg' => '重命名成功'));
            exit;
        } else {
            echo json_encode(array('code' => 1, 'msg' => '重命名失败'));
            exit;
        }
    }

//删除路由器接口
    public function delPort()
    {
        $checkPopedomlist = parent::checkPopedomlist('ccf_del_vbr_port');
        if (! $checkPopedomlist) {
            return $this->redirect('/console/');
        }

        $connects_table = TableRegistry::get('VbrConnects');
        $request = $this->request->data;
        if (!isset($request['select_id']) || empty(trim($request['select_id'], ','))) {
            echo json_encode(array('code' => 2, 'msg' => '请选择要删除的路由器接口'));
            exit;
        }
        $select_id = trim($request['select_id'], ',');
        $count=0;
        $info='';
        foreach(explode(',',$select_id) as $key => $value) {
            $port_data=$connects_table->find()->where(array('id'=>$value))->first();
            $url = Configure::read('URL');
            $array = array(
                'method' => 'vbr_del_interface',
                'uid' => (string)$this->request->session()->read('Auth.User.id'),
                'interfaceId' => $value,
                'initiatingSideRouterInterfaceCode' => $port_data['initiatingSideRouterInterfaceCode'],
                'acceptingSideInterfaceCode' => $port_data['acceptingSideInterfaceCode'],
            );
            $http = new Client();
            $obj_response = $http->post($url, json_encode($array), array('type' => 'json'));
            $data_response = json_decode($obj_response->body, true);


            if($data_response['Code']==0){
                $count+=1;
            }else{
                $info.=$data_response['Message'].'<br>';
            }
        }
        if($count>0){
            echo json_encode(array('code'=>0,'msg'=>'删除成功'));exit;
        }else{
            echo json_encode(array('code'=>$data_response['Code'],'msg'=>'删除失败','info'=>$info));exit;
        }
    }


    //路由管理
    public function editVbr()
    {
        $connects_table = TableRegistry::get('VbrConnects');
        $vbr_entry_table = TableRegistry::get('VbrEntry');
        $basic_table = TableRegistry::get('InstanceBasic');
        $request = $this->request->query;
        if (!isset($request['port_id']) && empty($request['port_id'])) {
            $this->render('/Console/BoundaryRouterList/vbr');
        }
        if (isset($request['type'])) {
            if ($request['type'] == '接收端') {
                $type = 'accept';
            } else {

                $type = 'initiate';
                
            }
        } else {
            $type = 'initiate';
        }
        $port_id = $request['port_id'];
        $port_data = $connects_table->find()->where(array('id' => $port_id))->first();

        $vbr_data = $basic_table->find()->where(array('id' => $port_data['basic_id']))->first();
        $vbr_data['connectionScene'] = '专线接入阿里云';
        $subnet = '';
        if ($type == 'accept' ) {
            $port_data['type'] = '发起端接口';
            $port_data['customName'] = $port_data['customName'] . '-收';
            $port_data['add_nextHop']=$port_data['acceptingSideRouterInterfaceCode'];

            $subnet=TableRegistry::get('VbrExtends')->find()->select(['subnet'])->where(array('basic_id'=>$port_data['basic_id']))->first()['subnet'];
            $vpc = $basic_table->find()->select(['vpc'])->where(array('code'=>$subnet))->first()['vpc'];
            $vbr_data['vpc'] = $vpc;
        } else {
            $port_data['type'] = '接收端接口';
            $port_data['customName'] = $port_data['customName'] . '-发';
            $port_data['add_nextHop']=$port_data['initiatingSideRouterInterfaceCode'];
            
           
            
            $vbr_data['vpc']=TableRegistry::get('VbrExtends')->find()->select(['aliyun_vpcCode'])->where(array('basic_id'=>$port_data['basic_id']))->first()['aliyun_vpcCode'];
            $vpc=$vbr_data['vpc'];
            // TODO 过滤已使用的subnet
            
            
        }
        
        $used_subnet_code = $vbr_entry_table->find()->select(['subnetCode'])->where(['routeCode' => $port_data['routerCode']])->toArray();
        $subnet_array = array();
        foreach ($used_subnet_code as $sub) {
            $subnet_array[] = $sub['subnetCode'];
        }
//         $subnet_array = array_value($subnet_array);
        if (!empty($subnet_array)) {
        $subnet=$basic_table->find()->select(['name','code'])
        ->where(array('type'=>'subnet', 'vpc'=>$vpc, 'code NOT IN' => $subnet_array))
        ->toArray();
        } else {
            $subnet=$basic_table->find()->select(['name','code'])
            ->where(array('type'=>'subnet', 'vpc'=>$vpc))
            ->toArray();
        }
        $this->set('add_subnet',$subnet);
        $this->set('port_data', $port_data);
        $this->set('vbr_data', $vbr_data);
    }

    public function editvbrList()
    {
        $entry_table = TableRegistry::get('VbrEntry');
        $request = $this->request->query;
        $basic_id = isset($request['basic_id']) ? $request['basic_id'] : 0;

        $query = $entry_table->find()->where(array('basic_id' => $basic_id));
        $this->_pageList['total'] = $query->count();
        $this->_pageList['rows'] = $query->offset($request['offset'])->limit($request['limit'])->map(function ($row) {
            $subnet_table = TableRegistry::get('SubnetExtend');
            $basic_table=TableRegistry::get('InstanceBasic');
            $subnet_data=$basic_table->find()->select(['id','name'])->where(array('code'=>$row['subnetCode']))->first();
            $row['subnetName']=$subnet_data['name'];
            $row['segment'] =$subnet_table->find()->select(['cidr'])->where(array('basic_id'=>$subnet_data['id']))->first()['cidr'];

            return $row;
        });
        echo json_encode($this->_pageList);
        exit;
    }
    //删除路由项
    public function delRouter(){
        $checkPopedomlist = parent::checkPopedomlist('ccf_del_vbr_router');
        if (! $checkPopedomlist) {
            return $this->redirect('/console/');
        }

        $request=$this->request->data;
        if (!isset($request['rows']) || empty($request['rows'])) {
            echo json_encode(array('code' => 2, 'msg' => '请选择要删除的路由器'));
            exit;
        }
        $url = Configure::read('URL');
        $count=0;
        $info='';
        foreach ($request['rows'] as $key => $value) {

            $array=array(
                'method'=>'vbr_del_route',
                'uid'=> (string)$this->request->session()->read('Auth.User.id'),
                'routeId'=>$value['id'],
                'routeEntryCode'=>$value['routeEntryCode']
            );

            $http          = new Client();
            $obj_response  = $http->post($url, json_encode($array), array('type' => 'json'));
            $data_response = json_decode($obj_response->body, true);

            if($data_response['Code']==0){
                $count+=1;
            }else{
                $info.=$data_response['Message'].'<br>';
            }

        }
        if($count>0){
            echo json_encode(array('code'=>0,'msg'=>'删除成功'));exit;
        }else{
            echo json_encode(array('code'=>$data_response['Code'],'msg'=>'删除失败','info'=>$info));exit;
        }
    }
//新建路由
    public function addRouter(){
        $checkPopedomlist = parent::checkPopedomlist('ccf_add_vbr_router');
        if (! $checkPopedomlist) {
            return $this->redirect('/console/');
        }

        $request=$this->request->data;
//debug($request);exit();
        if(isset($request['type'])&&$request['type']=='发起端接口'){
            $isECMPRoute=$request['add_ecm'];
        }else{
            $isECMPRoute=0;
        }
        $basic_id=$request['basic_id'];

        $url = Configure::read('URL');

        $vbrExtends = TableRegistry::get('VbrExtends');
        $vbrConnects = TableRegistry::get("VbrConnects");
        $exist_data=$vbrExtends->find()->where(array('basic_id'=>$basic_id))->first();
        $basic_table = TableRegistry::get('InstanceBasic');
        $dui  = $basic_table->find()->contain(['VbrExtends'])->where(['VbrExtends.subnet' => $exist_data['subnet'], 'VbrExtends.aliyun_vpcCode' => $exist_data['aliyun_vpcCode'], 'InstanceBasic.id <>' => $exist_data['basic_id']])->first();

        $vbrConnectsData = $vbrConnects->find()->select(["acceptingSideRouterInterfaceCode"])->where(["routerCode" => $dui["code"]])->first();

        if (!empty($vbrConnectsData)) {
            $request['add_nextHop'] = $request['add_nextHop'] . "," . $vbrConnectsData["acceptingSideRouterInterfaceCode"];
        }


        $array=array(
            'method'=>'vbr_add_route',
            'uid'=>(string)$this->request->session()->read('Auth.User.id'),
            'basicId'=>$basic_id,
            'routerCode'=>$request['add_routerCode'],
            'subnetCode'=>$request['add_subnet'],
            'nextHop'=>$request['add_nextHop'],
            'isECMPRoute'=>(string)$isECMPRoute,
            'customName'=>$request['add_customName'].'-'.rand(100,999)
        );
        $http          = new Client();
        $obj_response  = $http->post($url, json_encode($array), array('type' => 'json'));

        $data_response = json_decode($obj_response->body, true);
        
        if($data_response['Code']==0){
            echo json_encode(array('code'=>0,'msg'=>'创建中'));exit;
        }else{
            echo json_encode(array('code'=>$data_response['Code'],'msg'=>$data_response['Message']));exit;
        }

    }
    public function existRE(){
        $request=$this->request->query;
        $basic_id=$request['basic_id'];
        $vbrExtends = TableRegistry::get('VbrExtends');
        $vbrConnects = TableRegistry::get("VbrConnects");
        $exist_data=$vbrExtends->find()->where(array('basic_id'=>$basic_id))->first();
        $router=$vbrExtends->find()->where(array('isRedundancy'=>1,'aliyun_vpcCode'=>$exist_data['aliyun_vpcCode'],'subnet'=>$exist_data['subnet']))->first();
        
        if($router){
            $vbrInterface = $vbrConnects->find()->where(['basic_id'=>$router->basic_id])->first();
            if($vbrInterface && $vbrInterface->initiatingSideRouterInterfaceCode){
                echo json_encode(array('code'=>1,'msg'=>'有备vbr','vbrCode'=>$vbrInterface->initiatingSideRouterInterfaceCode));exit;
            }else{
                echo json_encode(array('code'=>0,'msg'=>'备vbr的边界路由器接口不存在，不能为等价路由'));exit;
            }
        }else{
            echo json_encode(array('code'=>0,'msg'=>'没有备用VBR，不能为等价路由'));exit;
        }

    }
}