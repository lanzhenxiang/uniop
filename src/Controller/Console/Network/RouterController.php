<?php
/**
 * 控制台 ajax控制器
 *
 *
 * @author XingShanghe<xingshanghe@gmail.com>
 * @date  2015年9月24日下午2:39:53
 * @source RouterController.php
 * @version 1.0.0
 * @copyright  Copyright 2015 sobey.com
 */
namespace App\Controller\Console\Network;

use App\Controller\Console\ConsoleController;
use Cake\Network\Exception\BadRequestException;
use App\Controller\OrdersController;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Configure;
use App\Controller\Console\Network\HostsController;

class RouterController extends ConsoleController
{
    private $_serialize = array('code', 'msg', 'data');
    public function initialize()
    {
        if (!$this->request->is('ajax')) {
            throw new BadRequestException();
        }
        parent::initialize();
        $this->viewClass = 'Json';
        $this->loadComponent('Paginator');
    }
    public $_pageList = array('total' => 0, 'rows' => array());
    /**
     * 获取列表数据,
     * 新增关联查询
     */
    public function lists($request_data = array())
    {
        $limit = $request_data['limit'];
        $offset = $request_data['offset'];

        if (!empty($request_data['department_id'])) {
            $where['InstanceBasic.department_id'] = $request_data['department_id'];
        }

        if (isset($request_data['search'])) {
            if ($request_data['search'] != '') {
                $where['OR'] = [
                    ["InstanceBasic.name like"=>'%'.$request_data['search'].'%'],
                    ["InstanceBasic.code like"=>'%'.$request_data['search'].'%']
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
            'vpcip'=>'vpcExtend.cidr','vpcname'=>'vpc.name','InstanceBasic.department_id'
        ];

        $where['InstanceBasic.isdelete'] = 0;
        $where['InstanceBasic.type'] = 'router';

        $instanceBasic = TableRegistry::get('InstanceBasic');
        $query = $instanceBasic->find()->hydrate(false)->join(
            [
                'instanceRelation'=>[
                    'table' =>'cp_instance_relation',
                    'type'  =>'LEFT',
                    'conditions'=>'instanceRelation.fromid = InstanceBasic.id AND instanceRelation.totype = "vpc"'
                ],
                'vpc'=>[
                    'table' =>'cp_instance_basic',
                    'type'  =>'LEFT',
                    'conditions'=>'instanceRelation.toid = vpc.id'
                ],
                'vpcExtend'=>[
                    'table' =>'cp_vpc_extend',
                    'type'  =>'LEFT',
                    'conditions'=>'vpcExtend.basic_id = instanceRelation.toid'
                ]
            ]
        )->autoFields(true)->select($field)->where($where)->group('InstanceBasic.id')->order('InstanceBasic.create_time DESC')
            ->offset($offset)->limit($limit);

        $this->_pageList['total'] = $query->count();
        $this->_pageList['rows'] = $query;
        return $this->_pageList;
    }
    /**
     * @func: 获取vpcCode
     *
     * @param :$fromtype:查询类型 $param:$fromid:basic_id
     *            $param:$totype:获取类型
     *            @date: 2015年11月3日 下午4:16:50
     * @author : zhaodanru
     * @return : null
     */
    public function findVpcCode($fromtype, $fromid, $totype)
    {
        $instance_basic = TableRegistry::get('InstanceBasic');
        $instance_relation = TableRegistry::get('InstanceRelation');
        $vpc = $instance_relation->find()->select(array('toid'))->where(array('fromid' => $fromid, 'fromtype' => $fromtype, 'totype' => $totype))->toArray();
        if($vpc){
            $vpcCode = $instance_basic->find()->select(array('code'))->where(array('id' => $vpc[0]['toid']))->toArray();
            if ($vpcCode) {
                return $vpcCode[0]['code'];
            } else {
                return '';
            }
        }else{
            return '';
        }
    }
    /**
     * @func: 获取basicId
     *
     * @param :$fromtype:查询类型 $param:$fromid:basic_id
     *            $param:$totype:获取类型
     *            @date: 2015年11月3日 下午4:16:50
     * @author : zhaodanru
     * @return : null
     */
    public function findbasicId($fromtype, $fromid, $totype)
    {
        $instance_relation = TableRegistry::get('InstanceRelation');
        $vpc = $instance_relation->find()->select(array('toid'))->where(array('fromid' => $fromid, 'fromtype' => $fromtype, 'totype' => $totype))->toArray();
        if ($vpc) {
            return (string) $vpc[0]['toid'];
        } else {
            return '';
        }
    }
    /**
     * 编辑 计算机与网络_路由器
     */
    public function editNetworkRouter($value)
    {
        $code = '0001';
        $data = array();
        $_request_data = $value;
        // 逻辑处理
        // debug($_request_data);
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        $instance_basic = $instance_basic_table->get($_request_data['id']);
        // 需要修改的字段
        $_fields = array('name', 'description');
        foreach ($_fields as $_field) {
            if (isset($_request_data[$_field])) {
                $instance_basic->{$_field} = $_request_data[$_field];
            }
        }
        if ($instance_basic_table->save($instance_basic)) {
            $code = '0000';
            $data = $instance_basic_table->get($_request_data['id'])->toArray();
        }
        $msg = Configure::read('MSG.' . $code);
        // var_dump(compact(array_values($this->_serialize));exit;
        return compact(array_values($this->_serialize));
    }
    /**
     * 关闭 计算机与网络_路由器
     */
    public function stopNetworkRouter($value)
    {
        $code = '0001';
        $data = array();
        $ids = $value;
        $_request_data = explode(',', $ids['ids']);
        // 逻辑处理
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        foreach ($_request_data as $key => $val) {
            $instance_basic = $instance_basic_table->get($val);
            // 需要修改的字段
            $_fields = array('status');
            switch ($value['state']) {
                case '2':
                    $instance_basic->status = '已停止';
                    break;
                case '1':
                    $instance_basic->status = '运行中';
                    break;
                default:
                    // code...
                    break;
            }
            // var_dump($instance_basic->status);exit;
            $rs = $instance_basic_table->save($instance_basic)->toArray();
            $data = $instance_basic_table->get($val)->toArray();
            if (empty($rs)) {
                $msg = Configure::read('MSG.' . $code);
                return compact(array_values($this->_serialize));
            }
        }
        $code = '0000';
        $msg = Configure::read('MSG.' . $code);
        return compact(array_values($this->_serialize));
    }
    /**
     * 删除 计算机与网络_路由器
     */
    public function deleteNetworkRouter($value)
    {
        $code = '0001';
        $data = array();
        $ids = $value;
        $result = 0;
        $_request_id = explode(',', $ids['ids']);
        $_request_code = explode(',', $ids['codes']);
        // 逻辑处理
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        // $instance_basic_table = TableRegistry::get('InstanceBasic');
        $parameter['method'] = 'router_del';
        $uid = (string) $this->request->session()->read('Auth.User.id');
        $parameter['uid'] = $uid;
        $order = new OrdersController();
        $vpc = new OrdersController();
        $firewall = new OrdersController();
        $url = Configure::read('URL');
        foreach ($_request_id as $key => $value) {
            $parameter['basicId'] = $value;
            //判断router资源是否在创建中，创建中的资源不允许删除
            $routerEntity = $instance_basic_table->find()->select(['status'])->where(['id'=>$value])->first();
            if($routerEntity->status == "创建中"){
                $code = '0003';
                $msg = '该路由器正在创建中，不能删除';
                return compact(array_values($this->_serialize));
            }

            if ($_request_code[$key] == '-' || $_request_code[$key] == '') {
                $parameter['routerCode'] = '';
                $re_code = $order->postInterface($url, $parameter);
            } else {
                $parameter['routerCode'] = $_request_code[$key];
                // 解绑子网
                $subnetcode = $this->getSubnetByRouterId($value);
                // 获取子网code
                if (!empty($subnetcode)) {
                    $code = '0002';
                    $msg = '该路由器绑定了子网不能删除';
                    return compact(array_values($this->_serialize));
                } else {
                    $vpcCode = $this->findVpcCode('router', $parameter['basicId'], 'vpc');
                    $firewallCode = $this->findVpcCode('router', $parameter['basicId'], 'firewall');
                    $vpcId = $this->findbasicId('router', $parameter['basicId'], 'vpc');
                    $firewallId = $this->findbasicId('router', $parameter['basicId'], 'firewall');
                    $re_code = $order->postInterface($url, $parameter);
                }
            }
            // $re_code=$order->postInterface($url,$parameter);//调用接口
            // var_dump($parameter);exit;
            if ($re_code['Code'] == 0) {
                $result++;
            } else {
                $code = '0002';
                $msg = $re_code['Message'];
                break;
            }
        }
        if ($result == count($_request_id)) {
            $code = '0000';
        }
        // $msg = Configure::read('MSG.'.$code);
        return compact(array_values($this->_serialize));
    }
    /**
     * 获取 计算机与网络_路由器 描述
     */
    public function getDescribeNetworkRouter($value)
    {
        $code = '0001';
        $_request_data = $value;
        // 逻辑处理
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        $data = $instance_basic_table->find('all', array('conditions' => array('id = ' => $_request_data['id']), 'fields' => array('description')))->first()->toArray();
        if ($data) {
            $code = '0000';
            $msg = Configure::read('MSG.' . $code);
            return compact(array_values($this->_serialize));
        }
    }
    /**
     * @func: 创建前台所需的套餐数据
     * @param : @date: 2015年10月15日 下午4:37:46
     * @author : shrimp liao
     * @return : null
     */
    public function createRouterArray()
    {
        $str = array();
        $agent = TableRegistry::get('Agent');
        $where = array('is_enabled' => 1);
        //获取商品信息，包含商品分类
        $agentInfo = $agent->find('all')->contain(array('AgentImagelist', 'AgentSet'))->where($where)->order(array('Agent.sort_order' => 'ASC'))->toArray();
        foreach ($agentInfo as $index => $item) {
            if ($item['parentid'] == 0) {
                $agent = array();
                $agent['id'] = $item['id'];
                $agent['company'] = array('name' => $item['agent_name'], 'companyCode' => $item['agent_code'], 'price' => HostsController::getPrice($item['id'], 'vpc', '元/'));
                $agent['area'] = $this->getAreaListById($item['id'], $agentInfo);
                $str[] = $agent;
            }
        }
        return $str;
    }
    /**
     * @func: 获取厂商对应的地区机房str
     *
     * @param $id 厂商ID,$agentInfo 厂商集合
     *            @date: 2015年10月15日 下午5:16:07
     * @author : shrimp liao
     * @return : null
     */
    public function getAreaListById($id, $agentInfo)
    {
        $str = array();
        $instance_basic = TableRegistry::get('InstanceBasic');
        $instance_relation = TableRegistry::get('InstanceRelation');
        $vpc_extend = TableRegistry::get('VpcExtend');
        foreach ($agentInfo as $index => $item) {
            if ($item['parentid'] == $id) {
                $router_data = array();
                $router_data = '';
//                 $router = $instance_basic->find()->select(array('id', 'name'))->where(array('location_code' => $item['class_code'], 'type' => 'router', 'code <>' => ''))->toArray();
//                 foreach ($router as $key => $value) {
//                     $vpcid = $instance_relation->find()->select(array('toid'))->where(array('fromid' => $value['id'], 'fromtype' => 'router', 'totype' => 'vpc'))->toArray();
//                     if ($vpcid) {
//                         $cidr = $vpc_extend->find()->select(array('cidr'))->where(array('basic_id' => $vpcid[0]['toid']))->toArray();
//                         if ($cidr) {
//                             $router_data[$key]['cidr'] = $cidr[0]['cidr'];
//                         }
//                     }
//                     $router_data[$key]['id'] = $value['id'];
//                     $router_data[$key]['name'] = $value['name'];
//                 }
                $str[] = array('name' => $item['agent_name'], 'areaCode' => $item['region_code'], 'router' => $router_data);
            }
        }
        // var_dump($str);exit;
        return $str;
    }
    /**
     * @func: 根据套餐返回套餐数组
     *
     * @param
     * @date: 2015年10月15日 下午6:41:38
     * @author : shrimp liao
     * @return : null
     */
    public function getSetObjectByArray($setInfo)
    {
        $instance_basic = TableRegistry::get('SetHardware');
        $instance_basic->find()->select(array('cpu_number'))->where($where)->toArray();
        $cpu = array();
        foreach ($setInfo as $item) {
            $set_id = $item['set_id'];
            $where = array('set_id' => $set_id);
            $cpu_s = $instance_basic->find()->select(array('cpu_number'))->where($where)->toArray();
        }
        $info = array_unique($info);
        foreach ($info as $index => $i) {
            $set = array();
            foreach ($setInfo as $item) {
                if ($item['cpu_number'] == $i) {
                    $set[] = array('num' => $item['memory_gb'], 'setCode' => $item['set_type_code']);
                    $info[$index] = array('cpu' => $item['cpu_number'], 'rom' => $set);
                }
            }
        }
        sort($info);
        return $info;
    }
    /**
     * @func:获取基础数据更具TyepName
     *
     * @param
     *            :
     *            @date: 2015年10月16日 上午11:04:18
     * @author : shrimp liao
     * @return : null
     */
    public function getBaseTypeByName($name)
    {
        $baseTable = TableRegistry::get('InstanceBasic');
        $where = array('type' => $name, 'code <>' => '');
        $agentInfo = $baseTable->find('all')->where($where)->toArray();
        return $agentInfo;
    }
    /**
     * @func:获取防火墙模板
     *
     * @param : @date: 2015年10月16日 上午11:04:18
     * @author : shrimp liao
     * @return : array
     */
    public function getFirewallTemplateId()
    {
        $FirewallTemplate = TableRegistry::get('FirewallTemplate');
        $query = $FirewallTemplate->find('all')->toArray();
        return $query;
    }
    /**
     * @func:获取路由器绑定的子网code
     *
     * @param
     *            :
     *            @date: 2015年10月16日 上午11:04:18
     * @author : shrimp liao
     * @return : array
     */
    public function getSubnetByRouterId($id)
    {
        $where['instanceRelation.totype'] = 'subnet';
        $where['instanceRelation.fromid'] = $id;

        $instanceRelation = TableRegistry::get('instanceRelation');
        $query = $instanceRelation->find()->hydrate(false)
            ->join(
                [
                    'subnet'=>[
                        'table'=>'cp_instance_basic',
                        'type'=>'LEFT',
                        'conditions'=>'instanceRelation.toid = subnet.id'
                    ]
                ]
            )->select(['code'=>'subnet.code'])->where($where);

        return $query->first();
    }
}