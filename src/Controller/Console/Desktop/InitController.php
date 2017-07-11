<?php
/**
 * 文件描述文字
 *
 *
 * @author XingShanghe<xingshanghe@gmail.com>
 * @date  2015年10月8日下午5:21:24
 * @source HostsController.php
 * @version 1.0.0
 * @copyright  Copyright 2015 sobey.com
 */
namespace App\Controller\Console\Desktop;

use App\Controller\Console\ConsoleController;
use Cake\Network\Exception\BadRequestException;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use App\Controller\OrdersController;
use Cake\Datasource\ConnectionManager;

class InitController extends ConsoleController
{

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }

    private $_serialize = array(
        'code',
        'msg',
        'data'
    );

    public $_pageList = array(
        'total' => 0,
        'rows' => array()
    );

    private function _checkPopedom($param)
    {
        $popedom = parent::checkConsolePopedom($param);
        return $popedom;
    }

    /**
     * 获取列表数据,
     * 新增关联查询
     */
    public function lists($request_data = [])
    {
        $checkPopedomlist = $this->_checkPopedom('ccm_ps_desktop');
        if (! $checkPopedomlist) {
            return $this->redirect('/console/');
        }
        $limit = $request_data['limit'];
        $offset = $request_data['offset'];
        $where = ' AND a.isdelete = 0';
        if (! empty($request_data['department_id'])) {
            $where .= ' AND a.department_id = ' . $request_data['department_id'];
        }
        if(isset($request_data['search'])){
            if ($request_data['search']!="") {
                $where .= ' AND (a.name like\'%' . $request_data['search'] . '%\' OR a.code like\'%' . $request_data['search'] . '%\')';
            }
        }

        if (! empty($request_data['class_code'])) {
            $where .= ' AND a.location_code like\'' . $request_data['class_code'] . '%\'';
        }
        if (! empty($request_data['class_code2'])) {
            $where .= ' AND a.location_code like\'' . $request_data['class_code2'] . '%\'';
        } elseif (! empty($request_data['class_code'])) {
            $where .= ' AND a.location_code like\'' . $request_data['class_code'] . '%\'';
        }

        $connection = ConnectionManager::get('default');
        $sql = ' SELECT ';
        $sql.= ' a.id,a.type,a.`code`,a.`name`,a.`status`,a.department_id,b.plat_form,a.location_name,a.location_code,a.subnet,b.ip,b.cpu,b.memory,b.gpu,a.create_time, c.`name` as \'vpcName\',a.vpc,';
        $sql .= ' (SELECT GROUP_CONCAT(i.subnet_code) ';
        $sql .= ' FROM cp_hosts_network_card i ';
        $sql .= ' WHERE i.hosts_code = a.`code` ';
        $sql .= ' ) as \'I_SubnetCode\', ';
        $sql .= ' (SELECT GROUP_CONCAT(i.ip) ';
        $sql .= ' FROM cp_hosts_network_card i ';
        $sql .= ' WHERE i.hosts_code = a.`code` ';
        $sql .= ' ) as \'I_Ip\', ';
        $sql .= ' s.fusionType as S_FusionType';
        $sql .= ' FROM cp_instance_basic as a ';

        $sql .= ' LEFT JOIN `cp_host_extend` b ON a.`id` = (b.basic_id) ';
        $sql .= ' LEFT JOIN cp_instance_basic c ON a.vpc = c.`code` ';
        $sql .= ' LEFT JOIN cp_hosts_network_card d ON d.basic_id = a.id AND d.is_default = 1 ';
        $sql .= ' LEFT JOIN cp_instance_basic e ON e.code = d.subnet_code ';
        $sql .= ' LEFT JOIN cp_subnet_extend  s ON s.basic_id = e.id ';
        if(isset($request_data['type'])){
            $sql .= ' WHERE ( a.type = "'.$request_data['type'].'") ';
        }else{
            $sql .= ' WHERE ( a.type = \'ad\' OR a.type = \'ddc\' ) ';
        }
        $sql .= $where;
        $sql .= ' group by a.id';
        $sql .= ' ORDER BY a.create_time desc ';
        $sql_row = $sql . ' limit ' . $offset . ',' . $limit;
        $query = $connection->execute($sql_row)->fetchAll('assoc');
        $this->_pageList['total'] = $connection->execute($sql)->count();
        $this->_pageList['rows'] = $query;
        return $this->_pageList;
    }

    /**
     * [_haveDesktopExternal 判断桌面desktop套件是否已经创建]
     * @param  [string] $vpcCode [vpcCode]
     * @return [boolean]         [是否已创建状态]
     */
    public function hasDesktopExternal($vpcCode){
        $connection = ConnectionManager::get('default');
        $where = ' AND c.code = "'.$vpcCode['vpcCode'].'"';
        $where.= ' AND a.isdelete = 0';
        $sql = ' SELECT ';
        $sql.= ' a.id';
        $sql .= ' FROM cp_instance_basic as a ';
        $sql .= ' LEFT JOIN `cp_host_extend` b ON a.`id` = (b.basic_id) ';
        $sql .= ' LEFT JOIN cp_instance_basic c ON a.vpc = c.`code` ';
        $sql .= ' WHERE ( a.type = \'ad\' OR a.type = \'ddc\') ' . $where;
        $have_num = $connection->execute($sql)->count();
        return ['allow'=>$have_num>0 ?false:true];
    }

    /**
     * 编辑数据列表
     */
    public function edit($request_data = [])
    {
        $code = '0001';
        $data = [];
        // 编辑操作
        $host = TableRegistry::get('InstanceBasic', [
            'classname' => 'App\Model\Table\InstanceBasicTable'
        ]);
        $result = $host->updateAll($request_data, array(
            'id' => $request_data['id']
        ));
        if ($result) {
            $code = '0000';
            $data = $host->get($request_data['id'])->toArray();
        }

        $msg = Configure::read('MSG.' . $code);
        return compact(array_values($this->_serialize));
    }

    /**
     * @func: 启动主机
     *
     * @param
     * @type:方法名称 @hostsId:操作主机ID
     * @date: 2015年10月12日 下午2:45:17
     * @author : shrimp liao
     * @return : null
     */
    public function ajaxHosts($request_data = [])
    {
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        $orders               = new OrdersController();
        $result               = array();
        $isEach               = $request_data['isEach'];
        if ($request_data['method'] == 'ecs_delete') {
            $request_data['method']     = 'trash';
            $request_data['methodType'] = 'ecs_delete';
        } else {
            $request_data['method'] = $request_data['method'];
        }
        $request_data['uid'] = (string) $this->request->session()->read('Auth.User.id');
        if ($isEach == 'true') {
            $table = $request_data['table'];
            foreach ($table as $key => $value) {
                if (!empty($value)) {
                    if($this->isElbExists($value['vpc']) && $request_data['methodType'] == 'ecs_delete'){
                        $result['Code'] = '1';
                        $result['Message'] = 'vpx下存在elb，不允许删除';
                        return $result;
                    }
                    $interface = array('method' => $request_data['method'], 'uid' => $request_data['uid'], 'basicId' => $value['id'], 'instanceCode' => $value['code']);
                    $result    = $orders->ajaxFun($interface);
                    if ($result['Code'] != '0') {
                        return $result;
                        die;
                    }
                }
            }
            $result['Code'] = '0';
            return $result;
            die();
        } else {
            // uid
            $request_data['instanceCode'] = $request_data['instanceCode'];
            unset($request_data['isEach']);
            return $orders->ajaxFun($request_data);
        }
    }

    /**
     * [isElbExists 判断VPX是否有elb]
     * @param  [string]  $vpc [vpccode]
     * @return boolean
     */
    protected function isElbExists($vpc){
        $instance_basic = TableRegistry::get('InstanceBasic');
        $elb_entity = $instance_basic->find()->select(['id'])->where(['vpc'=>$vpc,'type'=>'lbs'])->first();
        if($elb_entity!=null && $elb_entity->id > 0){
            return true;
        }
        return false;
    }

    /**
     * 删除 计算机与网络_路由器
     */
    public function deleteDesktop($value)
    {
        $code = '0001';
        $data = [];
        $ids = $value;
        $result = 0;
        $_request_id = explode(',', $ids['ids']);
        $_request_code = explode(',', $ids['codes']);
        // 逻辑处理
        $instance_basic_table = TableRegistry::get('InstanceBasic');

        // var_dump($_request_id);exit;

        $parameter['method'] = 'trash';
        $parameter['methodType'] = 'desktop_del';
        $parameter['uid'] = $this->request->session()->read('Auth.User.id') ? (string) $this->request->session()->read('Auth.User.id') : (string) 0;
        $order = new OrdersController();
        $url = Configure::read('URL');
        foreach ($_request_code as $key => $value) {


            $parameter['desktopCode'] = $value;
            $parameter['basicId'] = $_request_id[$key];
            $re_code = $order->postInterface($url, $parameter); // 调用接口
            if ($re_code['Code'] == 0) {
                $result ++;
            } else {
                $code = '0002';
                $msg = $re_code['Message'];
                break;
            }
        }
        if ($result == count($_request_code)) {
            $code = '0000';
        }
        // $msg = Configure::read('MSG.'.$code);
        return compact(array_values($this->_serialize));
    }

    /**
     * 关闭 计算机与网络_路由器
     */
    public function stopDesktop($value)
    {
        $code = '0001';

        $data = [];

        $ids = $value;
        if (! empty($ids['ids'])) {
            $_request_id = explode(',', $ids['ids']);
        }

        $_request_code = explode(',', $ids['codes']);
        // 逻辑处理
        $instance_basic_table = TableRegistry::get('InstanceBasic');

        $parameter['uid'] = $this->request->session()->read('Auth.User.id') ? (string) $this->request->session()->read('Auth.User.id') : (string) 0;
        ;
        $order = new OrdersController();
        $url = Configure::read('URL');
        $all = 0;
        $result = 0;
        foreach ($_request_code as $key => $val) {
            if (! empty($val)) {
                $parameter['basicId'] = (string)$instance_basic_table->find()->select(['id'])->where(['code'=>$val])->first()->id;
                $all ++;
                $parameter['desktopCode'] = $val;

                switch ($value['status']) {
                    case '2': // 重启
                        $parameter['method'] = "desktop_reboot";
                        break;
                    case '1': // 停止
                        $parameter['method'] = "desktop_stop";
                        break;
                    case '0': // 启动
                        $parameter['method'] = "desktop_start";
                        break;
                    default:
                        // code...
                        break;
                }

                $re_code = $order->postInterface($url, $parameter); // 调用接口
                                                                    // debug($re_code);die();
                if ($re_code['Code'] == '0') {
                    $result ++;
                } else {
                    $code = '0002';
                    $msg = $re_code['Message'];
                    break;
                }
            }
        }
        if ($result == $all) {
            $code = '0000';
        }
        return compact(array_values($this->_serialize));
    }

    /**
     * @func: 创建前台所需的套餐数据
     *
     * @date: 2015年10月15日 下午4:37:46
     * @author : shrimp liao
     * @return : null
     */
    public function createDesktopArray()
    {
        $str = array();
        $agent = TableRegistry::get('Agent');
        $setsoftware = TableRegistry::get('GoodsVersionSpec');
        $where = array(
            'Agent.is_desktop' => 1
        );
        // 获取aduser
        // $aduser = TableRegistry::get('AdUser');
        // $aduserinfo = $aduser->find('all', array(
        // 'fields' => array(
        // 'id',
        // 'loginName'
        // )
        // ))->toArray();
        // 获取厂商信息
        $agentInfo = $agent->find('all')
            ->contain(array(
            'AgentImagelist',
            'AgentSet'
        ))
            ->where($where)
            ->toArray();
        // 获取非编套餐信息
        $setsoftwareInfo = $setsoftware->find('all')
            ->select('brand')
            ->distinct([
            'brand'
        ])
            ->toArray();
        // 获取基础数据信息（加载一次）
        $baseNet = $this->getSubnetByName('subnet');
        $subnet = $this->getBaseTypeObjeByArray($baseNet, 'net'); // 加载子网络

        foreach ($agentInfo as $index => $item) {
            if ($item['parentid'] == 0) {
                $agent = array();
                $agent['id'] = $item['id'];
                $agent['company'] = array(
                    'name' => $item['agent_name'],
                    'companyCode' => $item['agent_code']
                );
                $agent['area'] = $this->getAreaListById($item['id'], $agentInfo,$setsoftwareInfo);
                // $agent['net']=$subnet;
                // $agent['AdUser'] = $aduserinfo;
                // $agent['setsoftwareInfo'] = $this->createSoftwareArray($setsoftwareInfo);
                $str[] = $agent;
            }
        }
        return $str;
    }

    /**
     * @func: 获取厂商对应的地区机房str
     *
     * @param
     *            :$id 厂商ID,$agentInfo 厂商集合
     *            @date: 2015年10月15日 下午5:16:07
     * @author : shrimp liao
     * @return : null
     */
    public function getAreaListById($id, $agentInfo,$setsoftwareInfo)
    {
        $str = array();
        $instance_basic = TableRegistry::get('InstanceBasic');
        $instance_relation = TableRegistry::get('InstanceRelation');
        $vpc_extend = TableRegistry::get('VpcExtend');
        foreach ($agentInfo as $index => $item) {
            if ($item['parentid'] == $id) {
                $router_data = array();
                $router = $instance_basic->find()
                    ->select([
                    'id',
                    'name'
                ])
                    ->where(array(
                    'location_code' => $item['class_code'],
                    'type' => 'router',
                    'code <>' => '',
                    'department_id' => $this->request->session()
                        ->read('Auth.User.department_id')
                ))
                    ->toArray();
                foreach ($router as $key => $value) {
                    $vpcid = $instance_relation->find()
                        ->select([
                        'toid'
                    ])
                        ->where(array(
                        'fromid' => $value['id'],
                        'fromtype' => 'router',
                        'totype' => 'vpc'
                    ))
                        ->toArray();
                    if ($vpcid) {
                        $cidr = $vpc_extend->find()
                            ->select([
                            'cidr'
                        ])
                            ->where(array(
                            'basic_id' => $vpcid[0]['toid']
                        ))
                            ->toArray();
                        if ($cidr) {
                            $router_data[$key]['cidr'] = $cidr[0]['cidr'];
                        }
                    }
                    $router_data[$key]['id'] = $value['id'];
                    $router_data[$key]['name'] = $value['name'];
                }
                $sub = $instance_basic->find('all')
                    ->contain('SubnetExtend')
                    ->where(array(
                    'type' => 'subnet',
                    'code <>' => '',
                    'location_code' => $item['class_code'],
                    'department_id' => $this->request->session()
                        ->read('Auth.User.department_id')
                ))
                    ->toArray();
                $subnet = '';
                if (! empty($sub)) {
                    foreach ($sub as $net) {
                        $subnet[] = array(
                            'name' => $net['name'],
                            'netCode' => $net['code'],
                            'netcidr' => $net['subnet_extend']['cidr'],
                            'netId' => $net['id']
                        );
                    }
                } else {
                    $subnet = [];
                }

                $str[] = array(
                    'id' => $item['id'],
                    'name' => $item['agent_name'],
                    'areaCode' => $item['region_code'],
                    'router' => $router_data,
                    'vpc' => $this->getAllVpc($item['class_code']),
                    'setsoftwareInfo'=>$this->createSoftwareArray($setsoftwareInfo,$item['id'])
                );
            }
        }
        // var_dump($str);exit;
        return $str;
    }

    public function getAllVpc($class_code)
    {
        $table = TableRegistry::get('InstanceBasic');
        $vpcArray = array();
        $department_id = $this->request->session()->read('Auth.User.department_id') ? $this->request->session()->read('Auth.User.department_id') : 0;
        $where = array(
            'status' => '运行中',
            'type' => 'vpc',
            'location_code' => $class_code,
            'department_id' => $department_id
        );
        $vpcList = $table->find("all")
            ->where($where)
            ->toArray();
        // debug($vpcList);
        foreach ($vpcList as $key => $value) {
            $vpcArray[] = array(
                'name' => $value['name'],
                'vpcCode' => $value['code'],
                'net' => $this->getAllsubNet($value['code']),
                'aduser' => $this->getAllAduser($value['code'])
            );
        }
        return $vpcArray;
    }

    /*
     * 获取子网信息
     */
    public function getAllsubNet($vpCode)
    {
        $table = TableRegistry::get('InstanceBasic');
        $where = array(
            'status' => '运行中',
            'type' => 'subnet',
            'vpc' => $vpCode
        );
        $netArray = $table->find("all")
            ->contain('SubnetExtend')
            ->where($where)
            ->toArray();
        $subnetList = array();
        foreach ($netArray as $key => $value) {
            if ($value['subnet_extend']['isFusion'] == "true") {
                $isFusion = "OpenStack";
            } else {
                $isFusion = "VMware";
            }
            $subnetList[] = array(
                'name' => $value['name'],
                'netCode' => $value['code'],
                'netcidr' => $value['subnet_extend']['cidr'],
                'netId' => $value['id'],
                'isFusion' => $isFusion
            );
        }
        return $subnetList;
    }

    /*
     * 获取ad账号信息
     */
    public function getAllAduser($vpCode)
    {
        $aduser = TableRegistry::get('AdUser');
        $where = array(
            // 'department_id' => 'subnet',
            'vpcCode' => $vpCode
        );
        $aduserinfo = $aduser->find('all')
            ->where($where)
            ->toArray();

        $aduserList = array();
        foreach ($aduserinfo as $key => $value) {
            $aduserList[] = array(
                'id' => $value['id'],
                'loginName' => $value['loginName'],
                'vpcCode' => $value['vpcCode'],
                'department_id' => $value['department_id']
            );
        }
        return $aduserList;
    }

    /**
     * @func: 根据套餐返回套餐数组
     *
     * @param
     *            :@date: 2015年10月15日 下午6:41:38
     * @author : shrimp liao
     * @return : null
     */
    public function getSetObjectByArray($setInfo)
    {
        $instance_basic = TableRegistry::get('SetHardware');
        $instance_basic->find()
            ->select([
            'cpu_number'
        ])
            ->where($where)
            ->toArray();
        $cpu = array();
        foreach ($setInfo as $item) {
            $set_id = $item['set_id'];
            $where = array(
                'set_id' => $set_id
            );
            $cpu_s = $instance_basic->find()
                ->select([
                'cpu_number'
            ])
                ->where($where)
                ->toArray();
            // $cpu[]=$cpu_s;
        }
        $info = array_unique($info);
        foreach ($info as $index => $i) {
            $set = array();
            foreach ($setInfo as $item) {
                if ($item['cpu_number'] == $i) {
                    $set[] = array(
                        'num' => $item['memory_gb'],
                        'setCode' => $item['set_type_code']
                    );
                    $info[$index] = array(
                        'cpu' => $item['cpu_number'],
                        'rom' => $set
                    );
                }
            }
        }
        sort($info);
        return $info;
    }

    /**
     * @func:获取基础数据更具TyepName
     *
     * @param:
     * @date: 2015年10月16日 上午11:04:18
     * @author : shrimp liao
     * @return : null
     */
    public function getBaseTypeByName($name)
    {
        $baseTable = TableRegistry::get('InstanceBasic');
        $where = array(
            'type' => $name,
            'code <>' => ''
        );
        $agentInfo = $baseTable->find('all')
            ->where($where)
            ->toArray();
        return $agentInfo;
    }

    /**
     * @func:获取子网数据更具TyepName
     *
     * @param : @date: 2015年10月16日 上午11:04:18
     * @author : shrimp liao
     * @return : null
     */
    public function getSubnetByName($name)
    {
        $baseTable = TableRegistry::get('InstanceBasic');
        $where = array(
            'type' => $name,
            'code <>' => ''
        );
        $agentInfo = $baseTable->find('all')
            ->contain('SubnetExtend')
            ->where($where)
            ->toArray();
        return $agentInfo;

        // $connection = ConnectionManager::get('default');
        // $sql = "SELECT cp_instance_basic.*,b.cidr AS vpcip FROM `cp_instance_basic` LEFT JOIN `cp_instance_relation` ON ";
        // $sql .= " cp_instance_basic.id = cp_instance_relation.fromid AND cp_instance_relation.totype = 'vpc' LEFT JOIN `cp_vpc_extend` AS b ON cp_instance_relation.toid = b.basic_id";
        // $sql .=" WHERE cp_instance_basic.type = 'router' AND cp_instance_basic.`code` <> '' ";
        // $sql_row =$sql;

        // $query = $connection->execute($sql_row)->fetchAll('assoc');
        // return $query;
    }

    /**
     * @func:更具数据源，得到基础需要类型对应数组
     *
     * @param
     *            : @date: 2015年10月16日 上午11:12:58
     * @author : shrimp liao
     * @return : null
     */
    public function getBaseTypeObjeByArray($array, $name)
    {
        $str = array();
        foreach ($array as $item) {
            $str[] = array(
                'name' => $item['name'],
                $name . 'Code' => $item['code'],
                $name . 'cidr' => $item['subnet_extend']['cidr'],
                'netId' => $item['id']
            );
        }
        return $str;
    }

    /**
     * @func: 返回云桌面可选系统数组
     *
     * @param : @date: 2015年10月15日 下午6:41:38
     * @author : shrimp liao
     * @return : null
     */
    public function createSoftwareArray($setsoftware,$agnet_id)
    {
        $setsoftwareTable = TableRegistry::get('GoodsVersionSpec');
        $sethardwareTable = TableRegistry::get('SetHardware');
        $imagelistTable   = TableRegistry::get('Imagelist');

        $setsoftwareinfoArray = $setsoftwareTable->getDesktopSetByAgent1($agnet_id); // 非编套餐信息
        $sethardwareinfoArray = $sethardwareTable->find('all')->toArray(); // 硬件信息
        $imagelistinfoArray   = $imagelistTable->find('all')->toArray(); // 镜像信息
        $str                  = array();
        $software             = array();
        $type                 = array();
        // debug($setsoftwareTable->getDesktopSetByAgent1($agnet_id));die();
        foreach ($setsoftware as $image) {
            $software = "";
            foreach ($setsoftwareinfoArray as $item) {
                if ($item['brand'] == $image['brand']) {
                    foreach ($sethardwareinfoArray as $key => $value) {
                        if ($value['set_code'] == $item['instancetype_code']) {
                            $is_image = true;
                            foreach ($imagelistinfoArray as $imageinfo) {
                                if ($imageinfo['image_code'] == $item['image_code']) {
                                    $is_image   = false;
                                    $software[] = array(
                                        'name'           => $item['name'],
                                        'imageCode'      => $item['image_code'],
                                        'softwareNote'   => $item['description'],
                                        'hardwareName'   => $value['name'],
                                        'hardwareCode'   => $value['set_code'],
                                        'hardwareCpu'    => $value['cpu_number'],
                                        'hardwareMemory' => $value['memory_gb'],
                                        'hardwareGpu'    => $value['gpu_gb'],
                                        'imageName'      => $imageinfo['image_name'],
                                    );
                                    break;
                                }
                            }

                            if ($is_image) {
                                $software[] = array(
                                    'name'           => $item['name'],
                                    'imageCode'      => $item['image_code'],
                                    'softwareNote'   => $item['description'],
                                    'hardwareName'   => $value['name'],
                                    'hardwareCode'   => $value['set_code'],
                                    'hardwareCpu'    => $value['cpu_number'],
                                    'hardwareMemory' => $value['memory_gb'],
                                    'hardwareGpu'    => $value['gpu_gb'],
                                    'imageName'      => '未知操作系统',
                                );
                            }
                            break;
                        }
                    }
                }
            }

            if(!empty($software)){
                $str[] = array(
                'name'         => $image['brand'],
                'softwareInfo' => $software,
                );
            }

        }
        return $str;
    }

    /**
     * @func:检查aduser是否重复
     *
     * @author : wangjc
     * @return : null
     */
    public function checkAduser($request_data)
    {
        $name = $request_data['aduser'];
        $vpcCode = $request_data['vpcCode'];
        $AdUser = TableRegistry::get('AdUser');
        $request = $AdUser->find('all')
            ->where([
            'loginName =' => $name,
            'vpcCode ' => $vpcCode
        ])
            ->toArray();
        if (empty($request)) {
            $data = 0;
        } else {
            $data = 1;
        }
        return $data;
    }

    /**
     * @func:检查aduser是否重复
     *
     * @author : wangjc
     * @return : null
     */
    public function checkDesktopName($request_data)
    {
        $name = $request_data['name'];
        $num = $request_data['num'];
        if (empty($name)) {
            return 1;
        }
        if ($num > 1) {
            $where['name like'] = $name . '%';
        } else {
            $where['name'] = $name;
        }
        $instanc_table = TableRegistry::get('InstanceBasic');
        $request = $instanc_table->find()
            ->where([
            'type' => 'desktop'
        ])
            ->where($where)
            ->first();
        if (empty($request)) {
            $data = 0;
        } else {
            $data = 1;
        }
        return $data;
    }

    /**
     * 获取云桌面绑定的磁盘
     * @date: 2016年3月17日 下午7:15:20
     *
     * @author : wangjc
     * @return :
     */
    public function getVolumeData($code){

        $instance_basic_table = TableRegistry::get('InstanceBasic');
        $disks_metadata_data = $instance_basic_table->find()
            ->contain([
            'DisksMetadatas'
        ])
            ->where([
            'DisksMetadatas.attachhostid' => $code
        ])
            ->toArray();
        // debug($disks_metadata_data);die;
        return $disks_metadata_data;
    }
}