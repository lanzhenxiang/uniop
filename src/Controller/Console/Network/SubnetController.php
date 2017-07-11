<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2015/10/28
 * Time: 10:26
 */
namespace App\Controller\Console\Network;

use App\Controller\Console\ConsoleController;
use App\Controller\OrdersController;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
class SubnetController extends ConsoleController
{
    private $_serialize = array('code', 'msg', 'data');
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }
    public $_pageList = array('total' => 0, 'rows' => array());
    /**
     * 获取列表数据,
     *
     *
     * 新增关联查询
     */
    public function lists($request_data = array())
    {
        $limit = $request_data['limit'];
        $offset = $request_data['offset'];

        $andWhere['OR']=[
            ['subnetExtend.subnetType <>'=>'firewall'],
            ['subnetExtend.subnetType is'=>null],
        ];

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
            'routername'=>'router.name','cidr'=>'subnetExtend.cidr','isFusion'=>'subnetExtend.isFusion'
        ];

        $where['InstanceBasic.isdelete'] = 0;
        $where['InstanceBasic.type'] = 'subnet';


        $instanceBasic = TableRegistry::get('InstanceBasic');
        $query = $instanceBasic->find()->hydrate(false)->join(
            [
                'subnetExtend'=>[
                    'table' =>'cp_subnet_extend',
                    'type'  =>'LEFT',
                    'conditions'=>'InstanceBasic.id = subnetExtend.basic_id'
                ],
                'router'=>[
                    'table' =>'cp_instance_basic',
                    'type'  =>'LEFT',
                    'conditions'=>'InstanceBasic.router = router.code AND InstanceBasic.router <> "" AND InstanceBasic.router is not null'
                ]
            ]
        )->autoFields(true)->select($field)->where($where)->andWhere($andWhere)->group('InstanceBasic.id')->order('InstanceBasic.create_time DESC')
            ->offset($offset)->limit($limit);

        $this->_pageList['total'] = $query->count();
        $this->_pageList['rows'] = $query;
        return $this->_pageList;
    }
    //解绑路由
    public function unbing($data)
    {
        if (!empty($data)) {
            /*  $code = '0001';
                $instance_basic = TableRegistry::get('InstanceBasic');
                $subnet = $instance_basic->find()->select(['id'])->where(array('subnet'=>$data['subnetCodes']))->count();
                if($subnet>0){
                $msg='此子网下存在主机，需删除主机才能解绑';
                }else{*/
            $data['basicId'] = $data['id'];
            unset($data['id']);
            $data['method'] = 'router_unbind';
            $data['uid'] = (string) $this->request->session()->read('Auth.User.id');
            $order = new OrdersController();
            $url = Configure::read('URL');
            $re_code = $order->postInterface($url, $data);
        }
        return $re_code;
    }
    
   /**
    * 批量删除子网
    * 
    * @param array $datas
    * @return string
    */
    public function delSubnetAll($datas)
    {
        $id_arr = split(',', $datas['id']); 
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        $info = $instance_basic_table->find()->where(['id in' => $id_arr])->toArray();
        
        if (!empty($info)) {
            $codes = array();
            foreach ($info as $v) {
                $codes[] = $v['code'];
            }
            $subnetUsedList = $this->_isEableDeleteSubnet($codes);
            $usedSubnet = array_keys($subnetUsedList);

            $can_del = array_diff($codes, $usedSubnet);
            $typeName = ['hosts'=>'主机','lbs'=>'负载均衡','desktop'=>'桌面'];
            $msg = "";
            foreach ($subnetUsedList as $subnetCode => $type) {
                $code = 1;
                $msg .= '子网'.$subnetCode."存在".$typeName[$type]."资源,不能删除!<br />";
            }
            $info = $instance_basic_table->find()->where(['code in' => $can_del])->toArray();
            $parameter['method'] = 'subnet_del';
            $parameter['uid'] = (string) $this->request->session()->read('Auth.User.id');
            $url = Configure::read('URL');
            $order = new OrdersController();
            
            if (!empty($info)) {
                foreach ($info as $v) {
                    $parameter['subnetCode'] = $v['code'];
                    $parameter['basicId'] = (string)$v['id'];
                    $re_code = $order->postInterface($url, $parameter);
                    if($re_code['Code'] == '0'){
                        $code =0;
                        $msg .= "子网".$v['code'].'请求删除成功<br />';
                    }else {
                        $code = 1;
                        $msg .= "子网" . $v['code'] . "删除失败" . $re_code['Message'] . '<br />';
                    }
                }
            }
            if($msg !=''){
                echo json_encode(array('Code' => $code, 'Message' => $msg));exit;
            }
        }
        echo json_encode(array('Code' => '0', 'Message' => '删除任务发送成功'));exit;
    }

    protected function _isEableDeleteSubnet($subnetCode)
    {
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        $subnetUsedList = $instance_basic_table->find('list', [
                'keyField' => 'subnet',
                'valueField' => 'type'
            ])->select(['subnet','type'])->where(
            [   
                'subnet in '=> $subnetCode,
                'type in' =>['hosts','lbs','desktop']
            ])->toArray();
        return $subnetUsedList;
    }

    
    private function objToArr($obj, $str) 
    {
        $arr = array();
        if (!empty($obj)) {
            foreach ($obj as $v) {
                $arr[] = $v[$str];
            }
        }
        return $arr;
    }

    //删除子网
    public function deleteSubnet($datas)
    {
        $code = '0001';
        $data = array();
        $instance_basic = TableRegistry::get('InstanceBasic', array('classname' => 'App\\Model\\Table\\InstanceBasicTable'));
        $subnet = $instance_basic->find()->select(array('id'))->where(array('subnet' => $datas['subnetCodes'], 'type' => 'hosts'))->count();
        $lbs = $instance_basic->find()->select(array('id'))->where(array('subnet' => $datas['subnetCodes'], 'type' => 'lbs'))->count();
        $desktop = $instance_basic->find()->select(array('id'))->where(array('subnet' => $datas['subnetCodes'], 'type' => 'desktop'))->count();
        $parameter['method'] = 'subnet_del';
        $parameter['uid'] = (string) $this->request->session()->read('Auth.User.id');
        $parameter['subnetCode'] = $datas['subnetCodes'];
        $parameter['basicId'] = $datas['id'];
        $order = new OrdersController();
        $url = Configure::read('URL');
        //当子网未绑定路由时
        if (empty($datas['routerCode'])) {
            $re_code = $order->postInterface($url, $parameter);
            //调用接口
            if ($re_code['Code'] == 0) {
                $code = '0000';
                $msg = '删除子网成功';
            } else {
                if (!strpos($re_code['Message'], 'Apache')) {
                    $msg = '服务器错误删除失败';
                } else {
                    $msg = $re_code['Message'];
                }
            }
            //当子网绑定了路由时
        } else {
            if ($subnet > 0) {
                $msg = '此子网下存在主机，不能删除';
            } elseif ($lbs > 0) {
                $msg = '此子网下存在负载均衡，不能删除';
            }elseif($desktop>0){
                $msg = '此子网下存在云桌面，不能删除';
            } else {
                $unbing = array('Code'=>0,'Message'=>'OK');
                if ($unbing['Code'] == 0 || $unbing['Message'] == '路由器不存在.') {
                    if (isset($datas['id'])) {
                        $re_code = $order->postInterface($url, $parameter);
                        //调用接口
                        if ($re_code['Code'] == 0) {
                            $code = '0000';
                            $msg = '删除子网成功';
                        } else {
                            if (!strpos($re_code['Message'], 'Apache')) {
                                $msg = '服务器错误删除失败';
                            } else {
                                $msg = $re_code['Message'];
                            }
                        }
                    }
                } else {
                    if (!strpos($unbing['Message'], 'Apache')) {
                        $msg = '服务器错误删除失败';
                    } else {
                        $msg = $unbing['Message'];
                    }
                }
            }
        }
        return compact(array_values($this->_serialize));
    }
    //修改计算机与网络-子网
    public function updateSubnet($datas)
    {
        $code = '0001';
        $data = array();
        $disks = TableRegistry::get('InstanceBasic', array('classname' => 'App\\Model\\Table\\InstanceBasicTable'));
        $result = $disks->updateAll($datas, array('id' => $datas['id']));
        if (isset($result)) {
            $code = '0000';
            $data = $disks->get($datas['id'])->toArray();
        }
        $msg = Configure::read('MSG.' . $code);
        return compact(array_values($this->_serialize));
    }

    //新建子网页面厂商区域信息
    public function createSubnetArray()
    {
        $str = array();
        $agent = TableRegistry::get('Agent');
        $where = array('is_enabled' => 1);
        //获取商品信息，包含商品分类
        $agentInfo = $agent->find('all')->contain(array('AgentImagelist', 'AgentSet'))->where($where)->order(array('Agent.sort_order' => 'ASC'))->toArray();
        $str = array();
        foreach ($agentInfo as $index => $item) {
            if ($item['parentid'] == 0) {
                $agent = array();
                $agent['company'] = array('name' => $item['agent_name'], 'companyCode' => $item['agent_code']);
                $agent['area'] = $this->getAreaListById($item['id'], $agentInfo);
                $str[] = $agent;
            }
        }
        return $str;
    }
    public function getAreaListById($id, $agentInfo)
    {
        $str = array();
        $department_id = $this->request->session()->read('Auth.User.department_id');
        $instance_basic = TableRegistry::get('InstanceBasic');
        $instance_relation = TableRegistry::get('InstanceRelation');
        $vpc_extend = TableRegistry::get('VpcExtend');
        $systemsetting = TableRegistry::get('systemsetting');
        foreach ($agentInfo as $index => $item) {
            if ($item['parentid'] == $id) {
                $router_data = array();
                $router = $instance_basic->find()->select(array('id', 'name'))->where(array('location_code' => $item['class_code'], 'type' => 'router', 'status' => '运行中', 'department_id' => $department_id))->toArray();
                foreach ($router as $key => $value) {
                    $vpcid = $instance_relation->find()->select(array('toid'))->where(array('fromid' => $value['id'], 'fromtype' => 'router', 'totype' => 'vpc'))->toArray();
                    if ($vpcid) {
                        $cidr = $vpc_extend->find()->select(array('cidr'))->where(array('basic_id' => $vpcid[0]['toid']))->toArray();
                        if ($cidr) {
                            $router_data[$key]['cidr'] = $cidr[0]['cidr'];
                        }
                    }
                    $router_data[$key]['id'] = $value['id'];
                    $router_data[$key]['name'] = $value['name'];
                }
                //获取当前厂商机房所支持的虚拟化技术
                $virtual_technology = explode(",", $item['virtual_technology']);
                $virtualList = $systemsetting->find()->select(['para_value','para_note'])->where(['para_value in'=>$virtual_technology])->order(['sort_order'=>"ASC"])->toArray();
                
                $str[] = array('name' => $item['agent_name'], 'areaCode' => $item['region_code'], 'router' => $router_data,'virtual'=>$virtualList);
            }
        }
        return $str;
    }

    function getVpsSubnetsCount($request_data = array()){

        $instanceTable = TableRegistry::get('InstanceBasic');
        $entity = $instanceTable->get($request_data["vpc"]);
//        $vpcCode = $entity->code;
        $vpcCode = $entity->vpc;
        $subnetCount = $instanceTable->find()->where(array("vpc"=>$vpcCode,"type"=>"subnet"))->select()->count();
        echo $subnetCount;die();
    }
    function getVpsSubnetsCountByVpc($request_data = array()){

        $instanceTable = TableRegistry::get('InstanceBasic');
        $vpcCode = $request_data["vpc"];
        $subnetCount = $instanceTable->find()->where(array("vpc"=>$vpcCode,"type"=>"subnet"))->select()->count();
        echo $subnetCount;die();
    }
    /**
     * 新建子网时判断ip是否被占用
     * @param array $request_data：cidr
     */
    public function cidr($request_data = array())
    {
        $routerid = $request_data['routerid'];
        $subnet_extend = TableRegistry::get('SubnetExtend');
        $instance_relation = TableRegistry::get('InstanceRelation');
        $subnet_ids = $instance_relation->find('all')->select(array('toid'))->where(array('fromid' => $routerid, 'fromtype' => 'router', 'totype' => 'subnet'))->toArray();
        if (!empty($subnet_ids)) {
            foreach ($subnet_ids as $value) {
                $cidr = $subnet_extend->find()->select(array('cidr'))->where(array('basic_id' => $value['toid']))->toArray();
                if (!empty($cidr)) {
                    if ($cidr[0]['cidr'] == $request_data['cidr']) {
                        echo json_encode(array('code' => 1, 'msg' => '该网络地址已被使用，请重新选择'));
                        die;
                    }
                }
            }
            echo json_encode(array('code' => 0, 'msg' => ''));
            die;
        } else {
            echo json_encode(array('code' => 0, 'msg' => ''));
            die;
        }
    }
}