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
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use App\Controller\OrdersController;
use Cake\Datasource\ConnectionManager;
use Cake\Database\Schema\Table;
use Cake\Log\Log;

/**
 *EipController
 */
class EipController extends ConsoleController
{
   /** 
* some_func  
* 函数的含义说明 
* 
* @access public 
* @param mixed $arg1 参数一的说明 
* @param mixed $arg2 参数二的说明 
* @param mixed $mixed 这是一个混合类型 
* @since 1.0 
* @return array 
*/ 
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }
    private $_serialize = array('code', 'msg', 'data');
    public $_pageList = array('total' => 0, 'rows' => array());
    /**
     * @func: 获取EIP数据列表 josn
     * @param:
     * @date: 2016-1-27 15:34:59
     * @author: shrimp liao
     * @return: array()
     */
    public function lists($request_data = array())
    {
        $limit = $request_data['limit'];
        $offset = $request_data['offset'];
        $where['InstanceBasic.isdelete'] = 0;
        $where['InstanceBasic.type'] = 'eip';

        if (!empty($request_data['department_id'])) {
            $where['InstanceBasic.department_id'] = $request_data['department_id'];
        }
        if (isset($request_data['search'])) {
            if ($request_data['search'] != '') {
                $where['OR'] = [
                    ["InstanceBasic.name like"=>'%'.$request_data['search'].'%'],
                    ["InstanceBasic.code like"=>'%'.$request_data['search'].'%'],
                    ["eipExtend.ip like"=>'%'.$request_data['search'].'%'],
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
            'InstanceBasic.create_time','InstanceBasic.create_time',

            'bandwidth'=>'eipExtend.bandwidth','eip'=>'eipExtend.ip','bindcode'=>'eipExtend.bindcode',
            'agent_code'=>'agent.agent_code'
        ];

        $instanceBasic = TableRegistry::get('InstanceBasic');
        $query = $instanceBasic->find()->hydrate(false)->join(
            [
                'eipExtend'=>[
                    'table' =>'cp_eip_extend',
                    'type'  =>'LEFT',
                    'conditions'=>'InstanceBasic.id = eipExtend.basic_id'
                ],
                'agent'=>[
                    'table' =>'cp_agent',
                    'type'  =>'LEFT',
                    'conditions'=>'InstanceBasic.location_code = agent.class_code'
                ]
            ]
        )->select($field)->where($where)->group('InstanceBasic.id')->order('InstanceBasic.create_time DESC')
            ->offset($offset)->limit($limit);

        $this->_pageList['total'] = $query->count();
        $this->_pageList['rows'] = $query;

        return $this->_pageList;
    }

    public function eipHaveFireWallEcs($request_data)
    {

        $where['InstanceBasic.type'] = 'eip';
        $where['InstanceBasic.id'] = $request_data['id'];

        $field = ['InstanceBasic.code','InstanceBasic.id','InstanceBasic.name',
            'firewallcode'=>'firewallecs.code','firewallstatus'=>'firewallecs.status'];

        $instanceBasic = TableRegistry::get('InstanceBasic');
        $query = $instanceBasic->find()->hydrate(false)->join(
            [
                'firewallecs'=>[
                    'table' =>'cp_instance_basic',
                    'type'  =>'LEFT',
                    'conditions'=>'InstanceBasic.vpc = firewallecs.vpc AND firewallecs.type = "firewallecs" '
                ]
            ]
        )->select($field)->where($where)->group('InstanceBasic.id')->order('InstanceBasic.id DESC');

        return $query->toArray();
    }

    /**
     * @func: 获取绑定EIP主机数据列表 josn
     * @param:
     * @date: 2016-1-27 15:36:13
     * @author: shrimp liao
     * @return: null
     */
    public function bindEipHostsList($request_data)
    {
        $instanceBasic = TableRegistry::get('InstanceBasic');

        $limit = $request_data['limit'];
        $offset = $request_data['offset'];
        $where = '';
        if (!empty($request_data['staute'])) {
            if ($request_data['staute'] != '0') {
                if ($request_data['staute'] == '2') {
                    $where['eip.code <>'] = '';
                } else {
                    if ($request_data['staute'] == '1') {
                        $where['OR'] = [
                            ['eip.code'=>''],
                            ['eip.code is'=> null]
                        ];
                    }
                }
            }
        }
        if (isset($request_data['search'])) {
            if ($request_data['search'] != '') {
                $where['InstanceBasic.name'] = '%'.$request_data['search'].'%';
            }
        }

        if (!empty($request_data['eipCode'])) {
            $subQuery = $instanceBasic->find()->select('vpc')->where(['code'=>$request_data['eipCode']]);
            $where['InstanceBasic.vpc'] = $subQuery;
        }

        if (!empty($request_data['class_code'])) {
            $where['InstanceBasic.location_code like'] = '%'.$request_data['class_code'].'%';
        }
        if (!empty($request_data['class_code2'])) {
            $where['InstanceBasic.location_code like'] = '%'.$request_data['class_code2'].'%';
        }

        if (!empty($request_data['department_id'])) {
            $where['InstanceBasic.department_id'] = $request_data['department_id'];
        }

        $where['InstanceBasic.isdelete'] = 0;
        $andWhere['OR'] = [
            ['InstanceBasic.status'=>'运行中'],
            ['InstanceBasic.status'=>'已停止']
        ];
        $where['InstanceBasic.type'] = 'hosts';

        $field = [
            'A_ID'=>'InstanceBasic.id','A_Name'=>'InstanceBasic.name','A_Code'=>'InstanceBasic.code','A_Status'=>'InstanceBasic.status',
            'E_ID'=>'eip.id','E_Name'=>'eip.name','E_Code'=>'eip.code',
            'E_DisplayName'=>'agent.display_name'
        ];

        $query = $instanceBasic->find()->hydrate(false)->join(
            [
                'eipExtend'=>[
                    'table' =>'cp_eip_extend',
                    'type'  =>'LEFT',
                    'conditions'=>'InstanceBasic.code = eipExtend.bindcode'
                ],
                'agent'=>[
                    'table' =>'cp_agent',
                    'type'  =>'LEFT',
                    'conditions'=>'InstanceBasic.location_code = agent.class_code'
                ],
                'eip'=>[
                    'table' =>'cp_instance_basic',
                    'type'  =>'LEFT',
                    'conditions'=>'eipExtend.basic_id = eip.id'
                ]
            ]
        )->select($field)->where($where)->andWhere($andWhere)->group('InstanceBasic.id')->order('InstanceBasic.id DESC')
        ->offset($offset)->limit($limit);

        $this->_pageList['total'] = $query->count();
        $this->_pageList['rows'] = $query;

        return $this->_pageList;
    }

    /**
     * @func: 获取子网相关详细信息 josn
     * @param:
     * @date: 2016-1-27 15:37:21
     * @author: shrimp liao
     * @return: null
     */
    public function getEipByDesc($request_data)
    {
        $instance_basic = TableRegistry::get('InstanceBasic');
        $where = array('basic_id' => $request_data['id']);
        $entity = $instance_basic->find('all')->contain(array('EipExtend'))->where($where)->first();
        if (!empty($entity)) {
            if (!empty($entity->eip_extend->bindcode)) {
                $where1 = array('code' => $entity->eip_extend->bindcode);
                $table = $instance_basic->find()->select(array('name', 'type', 'code'))->where($where1)->first();

                return json_encode($table);
            }
        }
    }
    /**
     * @func: 添加EIP josn
     * @param:
     * @date: 2016-1-27 15:37:21
     * @author: shrimp liao
     * @return: null
     */
    public function addEip($request_data)
    {
        $orders = new OrdersController();
        $uid = (string) $this->request->session()->read('Auth.User.id');
        $interface = array('method' => 'eip_add', 'uid' => $uid, 'regionCode' => $request_data['regionCode'], 'eipName' => $request_data['eipName'], 'description' => $request_data['description'], 'bandwidth' => $request_data['bandwidth']);
        $result = $orders->ajaxFun($interface);

        return $result;
    }
    /**
     * 编辑数据列表.
     */
    public function edit($request_data = array())
    {
        $code = '0001';
        $data = array();
        //编辑操作
        $host = TableRegistry::get('InstanceBasic', array('classname' => 'App\\Model\\Table\\InstanceBasicTable'));
        $result = $host->updateAll($request_data, array('id' => $request_data['id']));
        if ($result) {
            $code = '0000';
            $data = $host->get($request_data['id'])->toArray();
        }
        $msg = Configure::read('MSG.'.$code);

        return compact(array_values($this->_serialize));
    }
    /**
     * @func: EIPajax 方法
     * @param: null
     * @date: 2015年10月12日 下午2:45:17
     * @author: shrimp liao
     * @return: null
     */
    public function ajaxEip($request_data = array())
    {
        $orders = new OrdersController();
        $result = array();
        $isEach = $request_data['isEach'];
        unset($request_data['isEach']);
        if (empty($request_data['eipCode']) || $request_data['eipCode'] == 'null') {
            $request_data['eipCode'] = '';
        }
        if($request_data["method"]=="eip_bind"){
            $is_sobey = $this->_isAllowEipBind($request_data);
            if($is_sobey === false){
                $result = ["Code"=>'1',"Message"=>"阿里云和亚马逊的负载均衡不能绑定EIP"];
                echo json_encode($result);exit();
            }
        }
        
        if ($isEach == 'true') {
            $table = $request_data['table'];
            foreach ($table as $key => $value) {
                if (!empty($value)) {
                    $interface = array('method' => $request_data['method'], 'uid' => (string) $this->request->session()->read('Auth.User.id'), 'basicId' => $value['id'], 'eipCode' => $value['code']);
                    $result = $orders->ajaxFun($interface);
                    if ($result['Code'] != '0') {
                        return $result;
                        die;
                    }
                }
            }
            $result['Code'] = '0';

            return $result;
            die;
        } else {
            if ($request_data['method'] == 'eip_attribute') {
                $request_data['uid'] = (string) $this->request->session()->read('Auth.User.id');
                $request_data['basicId'] = $request_data['eipId'];

                return $orders->ajaxFun($request_data);
            } elseif ($request_data['method'] == 'eip_unbind') {
                $request_data['uid'] = (string) $this->request->session()->read('Auth.User.id');

                return $orders->ajaxFun($request_data);
            } elseif ($request_data['method'] == 'eip_del') {
                $request_data['uid'] = (string) $this->request->session()->read('Auth.User.id');

                return $orders->ajaxFun($request_data);
            } else {
                $request_data['bindCode'] = $request_data['iaasCode'];
                if ($request_data['isEcs'] == 'true') {
                    //判断sobey主机下是否有防火墙实例
                    $this->_hasFirewallEsc($request_data['iaasCode']);
                    $basic_table = TableRegistry::get('InstanceBasic');
                    $where = array('code' => $request_data['iaasCode']);
                    $id = $basic_table->find()->select(array('id'))->where($where)->first();
                    $hostextend_table = TableRegistry::get('HostsNetworkCard');
                    $where = array('basic_id' => $id['id'], 'is_default' => '1');
                    $code = $hostextend_table->find()->select(array('network_code'))->where($where)->first();
                    $request_data['iaasCode'] = $code['network_code'];
                }
                $result = array();
                $request_data['uid'] = (string) $this->request->session()->read('Auth.User.id');
                //uid
                return $orders->ajaxFun($request_data);
            }
        }
    }
    /**
     * [_isAllowEipBind 判断EIP绑定的目标对象是否是阿里云的负载均衡]
     * @param  [array]  $request_data 
     * @return boolean               
     */
    private function _isAllowEipBind($request_data){
        $instance_basic = TableRegistry::get("InstanceBasic");
        $lbs_fusion_type = $instance_basic->find()->hydrate(false)->select(['fusionType'=>"subnet_e.fusionType"])->join([
            "subnet"=>[
                    "table"=>"cp_instance_basic",
                    "type"=>"LEFT",
                    "conditions"=>"subnet.code = InstanceBasic.subnet"
            ],
            "subnet_e"=>[
                    "table"=>"cp_subnet_extend",
                    "type"=>"LEFT",
                    "conditions"=>"subnet_e.basic_id = subnet.id"
            ]])->where([
                'InstanceBasic.code'=>$request_data['iaasCode']
            ])->first();
        if($lbs_fusion_type["fusionType"] == Configure::read('virtual_tech.aliyun') || $lbs_fusion_type["fusionType"] == Configure::read('virtual_tech.aws')){
            return false;
        }
        return true;
    }
    /**
     * [_hasFirewallEsc 判断EIP绑定主机是否需要防火墙实例]
     * @param  [string]  $ecs_code 主机code
     */
    protected function _hasFirewallEsc($ecs_code){
        $instance_basic = TableRegistry::get("InstanceBasic");
        //判断是否是sobey的主机
        $is_sobey = $instance_basic->isSobeyEcs($ecs_code);
        if($is_sobey){
            $firewallecs = $instance_basic->find()->hydrate(false)->select(['firewallecs_id'=>'firewallecs.id','firewallstatus'=>'firewallecs.status'])->join([
                "firewallecs"=>[
                        'table' =>'cp_instance_basic',
                        'type'  =>'LEFT',
                        'conditions' =>'firewallecs.vpc = InstanceBasic.vpc AND firewallecs.type ="firewallecs"'
                    ]
                ])->where(['InstanceBasic.code'=>$ecs_code])->first();
            $msg = null;
            if(empty($firewallecs['firewallecs_id'])){
                $msg = "当前EIP下没有防火墙实例，请先创建防火墙实例";
            } else {
                if($firewallecs['firewallstatus'] != "运行中"){
                    $msg = "当前防火墙实例状态不可用";
                } 
            }
            
            if($msg === null){
                return ;
            }
            $result = ['Code'=>'1',"Message"=>$msg];
            echo json_encode($result);exit;
        }
    }

    public function isUserEip($request_data = array())
    {
        $instanceBasic = TableRegistry::get("InstanceBasic");

        $field = ['InstanceBasic.name'];

        $where['InstanceBasic.code'] = $request_data['code'];
        $where['OR'] = [
            ['eipExtend.bindcode IS NOT '=>null],
            ['eipExtend.bindcode <> '=> ''],
        ];

        $query = $instanceBasic->find()->hydrate(false)
            ->select($field)
            ->where($where)
            ->join(
                [
                    'eipExtend'=>[
                        'table'=>'cp_eip_extend',
                        'type' =>'LEFT',
                        'conditions'=>'eipExtend.basic_id = InstanceBasic.id'
                    ],
                    'elbExtend'=>[
                        'table'=>'cp_elb_extend',
                        'type' =>'LEFT',
                        'conditions'=>'elbExtend.eipCode = InstanceBasic.code'
                    ],
                ]
            );

        return $query->toArray();
        die();
    }

    /**
     * @func: 根据CODE获取主表相关信息
     * @param: null
     * @date: 2016-1-27 15:40:23
     * @author: shrimp liao
     * @return: null
     */
    public function getHostsEntityByCode($code)
    {
        $basic_table = TableRegistry::get('InstanceBasic');
        $where = array('code' => $code);
        $entity = $basic_table->find('all')->where($where)->first();

        return $entity;
    }

     //新建子网页面厂商区域信息
    public function createEIPArray($request_data)
    {

        if(isset($request_data['dept_id']) && (int)$request_data['dept_id'] > 0){
            $department_id = (int)$request_data['dept_id'];
        }else{
            $department_id = $this->getOwnByDepartmentId();
        }
        $str = array();
        $agent = TableRegistry::get('Agent');
        $where = array('is_enabled' => 1);
        //获取商品信息，包含商品分类
        $agentInfo = $agent->find('all')->contain(array('AgentImagelist', 'AgentSet'))->where($where)->order(array('Agent.sort_order' => 'ASC'))->toArray();
        $str = array();
        foreach ($agentInfo as $index => $item) {
            if ($item['parentid'] == 0) {
//                 debug($item);die;
                $agent = array();
                $agent['company'] = array('name' => $item['agent_name'], 'companyCode' => $item['agent_code']);
                $agent['area'] = $this->getAreaListById($item['id'], $agentInfo,$department_id);
                $agent['company']['price'] = $this->getPrice($item["id"]);//计费 
                $str[] = $agent;
            }
        }
        return $str;
    }
    
    /**
     * 获取厂商eip价格
     * @param string $angent_id
     * @return array
     */
    public function getPrice($agent_id) {
        $charge_extend_table = TableRegistry::get("ChargeExtend");
        $price = $charge_extend_table->find()->where(["agent_id" => $agent_id, "charge_object" => 'eip'])->first();
       
        if (!empty($price)) {
            $price_info[0]['id'] = 1;    
            $price_info[0]['name'] = '按天计费'; 
            $price_info[0]['price'] = $price["daily_price"];
            $price_info[0]['unit'] = '元/天';
            $price_info[1]['id'] = 2;
            $price_info[1]['name'] = '按月计费';
            $price_info[1]['price'] = $price["monthly_price"];
            $price_info[1]['unit'] = '元/月';
            $price_info[2]['id'] = 3;
            $price_info[2]['name'] = '按年计费';
            $price_info[2]['price'] = $price["yearly_price"];
            $price_info[2]['unit'] = '元/年';
        } else {
            $price_info[0]['id'] = 1;
            $price_info[0]['name'] = '按天计费'; 
            $price_info[0]['price'] = 0;
            $price_info[0]['unit'] = '元/天';
            $price_info[1]['id'] = 2;
            $price_info[1]['name'] = '按月计费';
            $price_info[1]['price'] =0;
            $price_info[1]['unit'] = '元/月';
            $price_info[2]['id'] = 3;
            $price_info[2]['name'] = '按年计费';
            $price_info[2]['price'] = 0;
            $price_info[2]['unit'] = '元/年';
        }
        return $price_info;
        
    }

    public function getAreaListById($id, $agentInfo,$dept_id = null)
    {
        
        $str = array();
        foreach ($agentInfo as $index => $item) {
            if ($item['parentid'] == $id) {
                $str[] = array('name' => $item['agent_name'], 'areaCode' => $item['region_code'], 'vpc' => $this->getAllVpc($item['class_code'],$dept_id));
            }
        }

        return $str;
    }

    public function getAllVpc($class_code,$dept_id = null)
    {
        $table = TableRegistry::get('InstanceBasic');
        $vpcArray = array();

        $department_id = $this->request->session()->read('Auth.User.department_id') ? $this->request->session()->read('Auth.User.department_id') : 0;
        if($dept_id > 0){
            $department_id = $dept_id;
        }
        $where = array(
            'status' => '运行中',
            'type' => 'vpc',
            'location_code' => $class_code,
            'department_id' => $department_id,
        );
        $vpcList = $table->find('all')
            ->where($where)
            ->toArray();
        foreach ($vpcList as $key => $value) {
            $vpcArray[] = array(
                'name' => $value['name'],
                'vpCode' => $value['code'],
            );
        }

        return $vpcArray;
    }
}
