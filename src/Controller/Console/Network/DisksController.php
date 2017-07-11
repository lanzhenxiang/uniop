<?php
/**
 * ==============================================
 * EipController.php.
 *
 * @author:  shrimp liao
 * @date:    2015年11月3日 上午10:35:14
 * @version: v1.0.0
 * @desc:    子网控制器
 * @category EipController
 * ==============================================
 **/
namespace App\Controller\Console\Network;
use App\Controller\Console\ConsoleController;
use App\Controller\OrdersController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;

/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2015/10/8
 * Time: 16:59
 */
class DisksController extends ConsoleController {

    private $_serialize = array('code','msg','data');
    //初始化
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }


    public $_pageList = array(
        'total'   =>  0,
        'rows'    =>  array()
    );


    /**
     * 获取列表数据,
     *
     * 新增关联查询
     */
    public function lists($request_data = []){
        $limit = $request_data['limit'];
        $offset = $request_data['offset'];
        $where['InstanceBasic.isdelete'] = 0;
        $where['InstanceBasic.type'] = 'disks';
        if (!empty($request_data['department_id'])) {
            $where['InstanceBasic.department_id'] = $request_data['department_id'];
        }
        if (isset($request_data['search'])) {
            if ($request_data['search']!="") {
                $where['OR'] = [
                    ["InstanceBasic.name like"=>'%'.$request_data['search'].'%'],
                    ["InstanceBasic.code like"=>'%'.$request_data['search'].'%'],
                    ["hosts.name like"=>'%'.$request_data['search'].'%'],
                    ["hostExtend.ip like"=>'%'.$request_data['search'].'%'],
                ];
            }
        }

        if (!empty($request_data['class_code'])) {
            $where['InstanceBasic.location_code like'] = '%'.$request_data['class_code'].'%';
        }
        if (!empty($request_data['class_code2'])) {
            $where['InstanceBasic.location_code like'] = '%'.$request_data['class_code2'].'%';
        }

        $field = [
            'InstanceBasic.id','InstanceBasic.code','InstanceBasic.department_id',
            'InstanceBasic.name','InstanceBasic.location_name','InstanceBasic.status',
            'InstanceBasic.create_time',

            'capacity'=>'disksMetadata.capacity','attachhostid'=>'disksMetadata.attachhostid',
            'H_Name'=>'hosts.name','H_Code'=>'hosts.code'
            ];

        $instanceBasic = TableRegistry::get('InstanceBasic');
        $query = $instanceBasic->find()->hydrate(false)->join(
            [
                'disksMetadata'=>[
                    'table' =>'cp_disks_metadata',
                    'type'  =>'LEFT',
                    'conditions'=>'InstanceBasic.id = disksMetadata.disks_id'
                ],
                'hosts'=>[
                    'table' =>'cp_instance_basic',
                    'type'  =>'LEFT',
                    'conditions'=>'hosts.code = disksMetadata.attachhostid'
                ],
                'hostExtend'=>[
                    'table' =>'cp_host_extend',
                    'type'  =>'LEFT',
                    'conditions'=>'hosts.id = hostExtend.basic_id'
                ]
            ]
        )->select($field)->where($where)->group('InstanceBasic.id')->order('InstanceBasic.create_time DESC')
        ->offset($offset)->limit($limit);
        $this->_pageList['total'] = $query->count();
        $this->_pageList['rows'] = $query;
        return $this->_pageList;
    }

    /**
     * @func:在使用的硬盘
     * @date: 2015年11月5日17:12:11
     * @author: shrimp liao
     * @return: null
     */
    public function uselist($request_data =[]){
        $attachhostid=$request_data['id'];
        $instanceBasic = TableRegistry::get('InstanceBasic');

        $field = ['InstanceBasic.id','InstanceBasic.code','InstanceBasic.name','capacity'=>'disksMetadata.capacity'];

        $where['InstanceBasic.type'] = 'disks';
        $where['disksMetadata.attachhostid'] = $attachhostid;

        $query = $instanceBasic->find()->hydrate(false)
            ->select($field)
            ->where($where)
            ->join(
            [
                'disksMetadata'=>[
                    'table'=>'cp_disks_metadata',
                    'type' => 'LEFT',
                    'conditions'=>'InstanceBasic.id = disksMetadata.disks_id'
                ]
            ]);
        $result = $query->toArray();
        return $result;
    }

    //硬盘扩容
    public function addvolume($data){
        $code = '0001';
        $order = new OrdersController();
        $data['method']='volume_resize';
        $url=Configure::read('URL');
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        $data['basicId'] = (string)$instance_basic_table->find()->select(['id'])->where(['code'=>$data['volumeCode']])->first()->id;
        $data['uid']=(string)$this->request->session()->read('Auth.User.id');
        $re_code=$order->postInterface($url,$data);//调用接口\
        if($re_code['Code']==0){
            $code = '0000';
            $msg='扩容成功';
        }else{
            $msg=$re_code['Message'];
        }
        return compact(array_values($this->_serialize));
    }

    /**
     * @fun    未使用的硬盘
     * @date   2015-11-06T18:21:51+0800
     * @author shrimp liao
     * @param  array
     * @return [type]
     */
    public function unuselist($request_data = []){
        $this->paginate['limit'] = $request_data['limit'];
        $this->paginate['page'] = $request_data['offset']/$request_data['limit']+1;
        $vpc=$request_data['vpc'];
        $instance_basic = TableRegistry::get('InstanceBasic');
        $where = [
            'InstanceBasic.type'  =>  'disks',
            'attachhostid'=>'0',
            'status'=>'运行中',
            'InstanceBasic.vpc'=>$vpc
        ];
        $this->_pageList['total']=$instance_basic->find('all')->contain(['DisksMetadata'])->where($where)->count();
        $this->_pageList['rows']=$this->paginate($instance_basic->find('all')->contain(['DisksMetadata'])->where($where)->order(['create_time'=>'DESC']));
        return $this->_pageList;
    }


    //修改计算机与网络-硬盘
    public function updateDisks($datas){
        $code = '0001';
        $data = [];
        $disks = TableRegistry::get('InstanceBasic',['classname'=>'App\Model\Table\InstanceBasicTable']);
        $result = $disks->updateAll($datas,array('id'=>$datas['id']));
        if(isset($result)){
            $code = '0000';
            $data = $disks->get($datas['id'])->toArray();
        }
        $msg = Configure::read('MSG.'.$code);
        return compact(array_values($this->_serialize));

    }

    /**
     * 解绑硬盘
     * @fun    name
     * @date   2015-11-07T14:39:42+0800
     * @author shrimp liao
     * @param  array                    $request_data [硬盘code]
     * @return [type]                                 [description]
     */
    public function detachDisks($data = []){
            $this->_detachDisksBefore($data);
            $order = new OrdersController();
            $data['uid']=(string)$this->request->session()->read('Auth.User.id');//uid
            $url=Configure::read('URL');
            $interface= $order->postInterface($url,$data);
            // if($interface['Code']!=0){
                echo json_encode($interface);die();
            // }
    }

    /**
     * 快照删除之前判断
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function _detachDisksBefore($data)
    {
        $instanceBasic = TableRegistry::get("InstanceBasic");
        $disksCount = $instanceBasic->find()->join(
            [
                'snap_disks'=>[
                    'table'=>'cp_snap_disks',
                    'type'=>'INNER',
                    'conditions'=>'snap_disks.disk_id = InstanceBasic.id'
                ]
            ])->where(['InstanceBasic.code'=>$data['volumeCode']])->count();
        if($disksCount > 0){
            $result['Code'] = -1;
            $result['Message'] = '无法从虚拟机中移除磁盘,因为它是该虚拟机的某个快照的一部分';
            echo json_encode($result);die();
        }
    }

    /**
     * 删除多条硬盘
     * 
     * @param array
     * @return string
     */
    public function deleteDisksAll($datas){

        $uid=(string)$this->request->session()->read('Auth.User.id');
        $instanceBasic = TableRegistry::get("InstanceBasic");

        $field = ['InstanceBasic.id','InstanceBasic.code','InstanceBasic.department_id','H_Name'=>'hosts.name'];
        $where['InstanceBasic.type'] = 'disks';
        $where['InstanceBasic.id in'] = $datas['ids'];
        $query = $instanceBasic->find()->hydrate(false)
            ->select($field)->where($where)->join([
                'disksMetadata'=>[
                    'table'=>'cp_disks_metadata',
                    'type' => 'LEFT',
                    'conditions'=>'InstanceBasic.id = disksMetadata.disks_id'
                ],
                'hosts'=>[
                    'table'=>'cp_instance_basic',
                    'type' => 'LEFT',
                    'conditions'=>'hosts.code = disksMetadata.attachhostid'
                ]
            ]);
        foreach ($query as $v) {
            $post = array();
            $post['uid'] = $uid;
            $post['volumeCode'] = $v['code'];
            $post['basicId'] = (string)$v['id'];
            $post['host'] = !empty($v['H_Name']) ? $v['H_Name'] : "";
            $re = $this->delDisk($post);
            $response = json_decode($re);
            if(is_object($response) && $response->Code!=0){
                echo $re ;exit;
            }
        }
        echo json_encode(array('Code' => '0', 'Message' => '删除硬盘任务发送成功'));exit;
    }
    
    /**
     *  删除硬盘--调用接口
     *  
     * @param array $data
     * @return string
     */
    public function delDisk($data) {
        $order =new OrdersController();
        $url=Configure::read('URL');
        //该硬盘不存在code时
        if($data['volumeCode']==''){
            $data['method']='volume_del';
            $re_code=$order->postInterface($url,$data);//调用接口
            return json_encode($re_code);
            //硬盘未绑定主机时
        }else{
            if(empty($data['host'])){
                unset($data['host']);
                $data['method']='volume_del';
                $re_code=$order->postInterface($url,$data);//调用接口
                return json_encode($re_code);
            }else{
                $data['method']='volume_detach';
                if(isset($data['basicId'])){
                    $re_code =$order->postInterface($url,$data);//调用接口
                    return json_encode($re_code);
                }
            }
        }
    }
    
    /**
     * 删除单条硬盘
     * @param unknown $datas
     */
    public function deleteDisks($datas){
        $datas['uid']=(string)$this->request->session()->read('Auth.User.id');
        $re = $this->delDisk($datas);
        echo $re;die;
    }

    public function createDisks($data){
        $basic_id=$data['id'];
        $vpc=$data['vpcCode'];
        $instanceCode=$data['instanceCode'];
        $name=$data['name'];
        $size=$data['size'];
        $regionCode=$data['regionCode'];
        $goods=$this->getGoodBySn('disks');
        if($goods == null){
            return ['Code'=>-1,'Message'=>'基础数据disks商品不存在，请联系管理员！'];
        }
        $id= $this->createOrder($goods[0]['id']);//创建硬盘订单 10硬盘
        $orderGoodsTable=TableRegistry::get('OrdersGoods');
        $ordergoods=$orderGoodsTable->newEntity();
        $ordergoods->order_id=$id;
        $ordergoods->good_id=$goods['0']['id'];
        $ordergoods->good_name=$goods['0']['name'];
        $ordergoods->good_sn='';
        $ordergoods->num=1;
        $ordergoods->benefit=0;
        $ordergoods->price_per=0;
        $ordergoods->price_total=0;
        $ordergoods->transaction_price=$goods['0']['price'];
        $ordergoods->facilitator_id=0;
        $ordergoods->good_type = 'disks';
        $ordergoods->instance_conf=json_encode($data);
        $ordergoods->goods_snapshot=json_encode($data);
        $ordergoods->duration=$goods['0']['time_unit'];

        $ordergoods->duration_unit=$goods['0']['time_duration'];
        $ordergoods->description=0;
        $result= $orderGoodsTable->save($ordergoods);

        $orders=new OrdersController();

        $url=Configure::read('URL');
        $parameter['method']='volume_add';//方法名
        $parameter['uid']=(string)$this->request->session()->read('Auth.User.id');//uid
        $parameter['volumeName']=$name;
        $parameter['size']=$size;
        $parameter['iaasCode']=$instanceCode;
        $parameter['vpcCode']=$vpc;
        $parameter['regionCode']=$regionCode;
        $parameter['order_id']= (string)$id;
        $disksChargeParameter = $this->_assembleDisksCharge($data);
        $parameter = array_merge($parameter,$disksChargeParameter);
        $price = $parameter['price'] * $parameter['size'];
        $parameter['price'] = (string)$price;
        $parameter['real_price'] = (string)$price;
        $parameter['isEager'] = "true";//立即执行
        $interface = $orders->postInterface($url,$parameter);//调用接口
        return $interface;
    }

    /**
     * 获取硬盘的计费价格信息，根据主机的计费方式自动获取
     * @param $data
     * @return array
     */
    protected function _assembleDisksCharge($data)
    {
        $ecsCode = $data['instanceCode'];
        $instanceBasicTable = TableRegistry::get('InstanceBasic');
        $agentTable = TableRegistry::get('Agent');
        $chargeExtendTable = TableRegistry::get('ChargeExtend');
        $instanceInfo = $instanceBasicTable->find()->hydrate(false)->join([
            'charge'=>[
                "table" => 'cp_instance_charge',
                "type"  => 'INNER',
                "conditions"=>'charge.basic_id = InstanceBasic.id'
            ],
            "agent"=>[
                "table" => 'cp_agent',
                "type"  => 'INNER',
                "conditions"=>"agent.class_code = InstanceBasic.location_code"
            ]
        ])->select(
            [
                "charge_mode"=>"charge.charge_mode",
                "interval"=>"charge.interval",
                "agent_id"=>"agent.id"
            ])->where(['InstanceBasic.code'=>$ecsCode])->first();
        $agentRootEntity = $agentTable->getAgentRoot($instanceInfo['agent_id']);
        $disksCharge = $chargeExtendTable->find()->where(['charge_object'=>'disks','agent_id'=>$agentRootEntity->id])->first();
        if(strtoupper($instanceInfo['interval']) == 'D'){
            $price = (string)$disksCharge['daily_price'];
        }else if(strtoupper($instanceInfo['interval']) == 'M'){
            $price = (string)$disksCharge['monthly_price'];
        }else if(strtoupper($instanceInfo['interval']) == 'Y'){
            $price = (string)$disksCharge['yearly_price'];
        }else{
            $price = (string)$disksCharge['daily_price'];
        }
        return ['charge_mode'=>$instanceInfo["charge_mode"],'interval'=>$instanceInfo['interval'],'price'=>$price,'real_price'=>$price];
    }

    public function attachDisks($data){
        $instanceCode=$data['instanceCode'];
        $code=$data['volumeCode'];
        $goods=$this->getGoodBySn('disks');
        $id= $this->createOrder($goods[0]['id']);//创建硬盘订单 6硬盘
        $orderGoodsTable=TableRegistry::get('OrdersGoods');
        $ordergoods=$orderGoodsTable->newEntity();
        $ordergoods->order_id=$id;
        $ordergoods->good_id=$goods['0']['id'];
        $ordergoods->good_name=$goods['0']['name'];
        $ordergoods->good_sn='';
        $ordergoods->num=1;
        $ordergoods->benefit=0;
        $ordergoods->price_per=0;
        $ordergoods->price_total=0;
        $ordergoods->facilitator_id=0;
        $ordergoods->instance_conf=json_encode($data);
        $ordergoods->goods_snapshot=json_encode($data);
        $ordergoods->duration=$goods['0']['time_unit'];

        $ordergoods->duration_unit=$goods['0']['time_duration'];
        $ordergoods->description=0;
        $result= $orderGoodsTable->save($ordergoods);

        $orders=new OrdersController();

        $url=Configure::read('URL');
        $parameter['method']='volume_attach';//方法名
        $parameter['uid']=(string)$this->request->session()->read('Auth.User.id');//uid
        $parameter['volumeCode']=$code;
        $parameter['iaasCode']=$instanceCode;
        $interface = $orders->postInterface($url,$parameter);//调用接口
        return $interface;
    }



    /**
     * @func:ajax请求
     * @param:
     * @date: 2015年11月3日 下午4:24:35
     * @author: shrimp liao
     * @return: null
     */
    public function ajaxDisks($data){
        $method=$data['method'];
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        if($method=='volume_attach'){
            foreach (explode(',',$data['volumeCode']) as $key => $value) {
                if(!empty($value)){
                    $data['volumeCode']=$value;
                    $data['basicId'] = (string)$instance_basic_table->find()->select(['id'])->where(['code'=>$$value])->first()->id;
                    $interface = $this->attachDisks($data);
                    if($interface['Code']!=0){
                        echo json_encode($interface);die();
                    }else{
                        $arrayName = array();
                        $arrayName['Code']=0;
                        $arrayName['Message']="";
                        echo json_encode($arrayName);die();
                    }
                }
            }
        }else if($method=='volume_add'){
            $Agent=TableRegistry::get('Agent');
            $where = array('class_code' => $data['class_code']);
            $data['regionCode']=$Agent->find()->select(['region_code'])->where($where)->toArray()[0]['region_code'];
            $interface = $this->createDisks($data);
            //查询计费周期
            $charge = TableRegistry::get('InstanceCharge');
            $charge = $charge->find()->select(['charge_type'])->where(array('basic_id'=>$data["id"]))->first();
            if($charge){
                $interface["billCycle"]=$charge->charge_type;
            }
            echo json_encode($interface);die();
        }elseif ($method=='volume_detach') {
            $interface= $this->detachDisks($data);
            echo json_encode($interface);die();
        }
    }

    /**
     * 主机系统盘扩容
     * @param $requestData
     */
    public function ajaxSysDisks($requestData)
    {
        if($requestData['size'] < 40 || $requestData['ecsCode'] ==""){
            return ['Code'=>-1,'Message'=>'参数错误！'];
        }

        $hostExtend = TableRegistry::get('hostExtend');
        $entity = $hostExtend->find()->where(['basic_id'=>$requestData['ecsId']])->first();
        if(empty($entity) || $entity->sys_disk_size >= $requestData['size'] ){
            return ['Code'=>'1','Message'=>'扩容系统盘大小不能小等于当前系统盘大小'];
        }
        $orders=new OrdersController();

        $url=Configure::read('URL');
        $parameter['method']='resize_sys_disk';//方法名
        $parameter['uid']=(string)$this->request->session()->read('Auth.User.id');//uid
        $parameter['ecsCode']=$requestData['ecsCode'];
        $parameter['size']=$requestData['size'];
        $interface = $orders->postInterface($url,$parameter);//调用接口
        return $interface;
    }

    /**
     * @func: 创建订单
     * @param: $goods_id 商品ID 硬盘
     * @date: 2015年11月3日 下午3:15:55
     * @author: shrimp liao
     * @return: 新订单id
     */
    public function createOrder($goods_id) {
        $orders=new OrdersController();
        //创建订单信息
        $orderTable=TableRegistry::get('Orders');
        $ordes = $orderTable->newEntity();
        $ordes->number=$orders->build_order_no();
        $ordes->product_id=0;
        $ordes->goods_snapshot='';
        $ordes->facilitator_id=0;
        $ordes->instance_conf='';
        $ordes->duration=0;
        $ordes->duration_unit='月';
        $ordes->price_per=0;
        $ordes->num=1;
        $ordes->benefit=0;
        $ordes->price_total=0;
        $ordes->department_id=0;
        $ordes->tenant_id=0;
        $ordes->description='';
        $ordes->create_time=time();
        $ordes->create_by=0;
        $ordes->modify_time=time();
        $ordes->modify_by=0;
        $ordes->is_console = 1;
        $orderTable->save($ordes);

        return $ordes->id;
    }

    /**
     * @func: 根据Sn查询商品信息
     * @param:
     * @date: 2015年11月3日 下午3:25:18
     * @author: shrimp liao
     * @return: null
     */
    public function getGoodBySn($sn){
        $goods=TableRegistry::get('Goods');
        $where=array('Goods.sn'=>$sn);
        //获取商品信息，包含商品分类
        $goodsInfo=$goods->find('all')->where($where)->toArray();
        return $goodsInfo;
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see \App\Controller\Console\ConsoleController::test()
     */
    public function createDisksArray($request_data)
    {
        if(isset($request_data['dept_id']) && (int)$request_data['dept_id'] > 0){
            $department_id = (int)$request_data['dept_id'];
        }else{
            $department_id = $this->getOwnByDepartmentId();
        }
        $agent_table = TableRegistry::get('Agent');
        $data_list   = $agent_table->find('tree', array('order' => 'sort_order ASC'))
        ->where(array('is_enabled' => 1, 'is_desktop' => 1))
        ->toArray();
        $data_agent = array();
        // 城市
        // 顶级的hardware赋值给children
        foreach ($data_list as $key => $value) {
            if (!empty($value['children'])) {
                $data_agent[$key] = array(
                    'id'      => $value['id'],
                    'company' => array(
                        'name'        => $value['agent_name'],
                        'companyCode' => $value['agent_code'],
                        'price' => HostsController::getPrice($value['id'], 'disks', '元/')
                    ),
                    'area'    => array(),
                );
                foreach ($value['children'] as $kk => $vv) {
                    $vpc = $this->getAllVpc($vv['class_code'],$department_id);
                    $data_agent[$key]['area'][] = array(
                        'id'       => $vv['id'],
                        'name'     => $vv['agent_name'],
                        'areaCode' => $vv['region_code'],
                        'displayName' => $vv['display_name'],
                        'vpc'      => $vpc,
                    );
                }
            }
        }
        $data_agent = array_values($data_agent);
        return $data_agent;
    }
    
    public function getAllVpc($class_code,$department_id)
    {
        $table         = TableRegistry::get('InstanceBasic');
        $vpcArray      = array();
        $where         = array(
            'status'        => '运行中',
            'type'          => 'vpc',
            'location_code' => $class_code,
            'department_id' => $department_id,
        );
        $vpcList = $table->find('all')
        ->where($where)
        ->toArray();
        foreach ($vpcList as $key => $value) {
            $vpcArray[] = array(
                'name'   => $value['name'],
                'vpCode' => $value['code'],
                'net'    => $this->getAllsubNet($value['code']),
            );
        }
        return $vpcArray;
    }
    
    public function getAllsubNet($vpCode)
    {
        if(is_array($vpCode)){
            $vpCode=$vpCode["vpCode"];
        }
        $table = TableRegistry::get('InstanceBasic');
        $where = array(
            'status' => '运行中',
            'type'   => 'subnet',
            'vpc'    => $vpCode,
        );
        $netArray = $table->find('all')
        ->contain(array(
            'SubnetExtend'
        ))
        ->where($where)
        ->toArray();
        $subnetList = array();
        foreach ($netArray as $key => $value) {
            if($value['subnet_extend']['fusionType'] == ""){
                $isFusion = "vmware";
            } else {
                $isFusion = $value['subnet_extend']['fusionType'];
            }
            $subnetList[] = array(
                'name'     => $value['name'],
                'netCode'  => $value['code'],
                'isFusion' => $isFusion,
                'hosts'    => $this->getHostsBySubnet($value['code']) 
            );
        }
        return $subnetList;
    }

     public function getHostsBySubnet($code)
    {
        $table = TableRegistry::get('InstanceBasic');
        $where = array(
            'status' => '运行中',
            'type'   => 'hosts',
            'subnet' => $code,
            'isdelete' =>'0'
        );
        $hostsArray = $table->find('all')->where($where)->toArray();
        
        $hostsList = array();
        foreach ($hostsArray as $key => $value) {
            $hostsList[] = array(
                'name'  => $value['name'],
                'code'  => $value['code']
            );
            
        }
        return $hostsList;
    }

}
