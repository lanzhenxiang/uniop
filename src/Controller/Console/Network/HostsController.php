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
namespace App\Controller\Console\Network;

use App\Controller\Console\ConsoleController;
use App\Controller\OrdersController;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\ORM\Entity;
use Requests as Requests;

class HostsController extends ConsoleController
{

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }

    private $_serialize = array(
        'code',
        'msg',
        'data',
    );

    public $_pageList = array(
        'total' => 0,
        'rows' => array(),
    );

    public function firewall($request_data = array())
    {
        $hostid = intval($_GET['id']);
        //获取主网卡IP
        $cModel = TableRegistry::get("HostsNetworkCard");
        $networkCard = $cModel->find()->where(array('basic_id' => $hostid))->first();
        if (!isset($networkCard->id)) {
            return false;
        }
        $firewall = TableRegistry::get("FirewallPolicy")->find()->where(['source_ip' => $networkCard->ip])->orWhere(['target_ip' => $networkCard->ip]);
        if (isset($firewall->id)) {
            return true;
        }
        return false;


    }

    /**
     * 获取列表数据,
     * 新增关联查询
     */
    public function lists($request_data = array())
    {
        $limit = $request_data['limit'];
        $offset = $request_data['offset'];
        //$where = ' AND a.isdelete = 0';
        $where['InstanceBasic.isdelete'] = 0;

        if (!empty($request_data['department_id'])) {
            $where['InstanceBasic.department_id'] = $request_data['department_id'];
            //$where .= ' AND a.department_id = ' . $request_data['department_id'];
        }
        $andwhere = [];
        if (isset($request_data['search'])) {
            if ($request_data['search'] != "") {
                //$where['InstanceBasic.name like '] = '%' . $request_data['search'] . '%';
                $andwhere['OR'] = [
                    ['InstanceBasic.name like ' => '%' . $request_data['search'] . '%'],
                    ['InstanceBasic.code like ' => '%' . $request_data['search'] . '%'],
                    ['eipExtend.ip like ' => '%' . $request_data['search'] . '%'],
                    ['hostsNetworkCard.ip like ' => '%' . $request_data['search'] . '%'],
                ];
                //$where .= ' AND (a.name like\'%' . $request_data['search'] . '%\' OR a.code like\'%' . $request_data['search'] . '%\' OR c.ip like\'%' . $request_data['search'] . '%\' OR d.ip like\'%' . $request_data['search'] . '%\')';
            }
        }

        if (!empty($request_data['class_code'])) {
            $where['InstanceBasic.location_code like '] = $request_data['class_code'] . '%';
            //$where .= ' AND a.location_code like\'' . $request_data['class_code'] . '%\'';
        }
        if (!empty($request_data['vpc_code'])) {
            $where['InstanceBasic.vpc like '] = $request_data['vpc_code'] . '%';
            //$where .= ' AND a.vpc like\'' . $request_data['vpc_code'] . '%\'';
        }
        if (!empty($request_data['class_code2'])) {
            $where['InstanceBasic.location_code like '] = $request_data['class_code2'] . '%';
            //$where .= ' AND a.location_code like\'' . $request_data['class_code2'] . '%\'';
        } elseif (!empty($request_data['class_code'])) {
            $where['InstanceBasic.location_code like '] = $request_data['class_code'] . '%';
            //$where .= ' AND a.location_code like\'' . $request_data['class_code'] . '%\'';
        }

        $field = [
            'H_ID'          => 'InstanceBasic.id',
            'H_Code'        => 'InstanceBasic.code',
            'H_Name'        => 'InstanceBasic.name',
            'H_L_Code'      => 'InstanceBasic.location_code',
            'H_Status'      => 'InstanceBasic.status',
            'H_Description' => 'InstanceBasic.description',
            'H_time'        => 'InstanceBasic.create_time',
            'H_department'  => 'InstanceBasic.department_id',
            'F_Code'        => 'InstanceBasic.vpc',

            'E_BindCode'    => 'eipExtend.bindcode',
            'E_Ip'          => 'eipExtend.ip',

            'EIP_code'      => 'eip.code',
            'EIP_id'        => 'eip.id',

            'D_Os_Form'     => 'hostExtend.os_family',
            'D_Plat_form'   => 'hostExtend.plat_form',
            'D_isFusion'    => 'hostExtend.isFusion',
            'D_Cpu'         => 'hostExtend.cpu',
            'D_Memory'      => 'hostExtend.memory',
            'D_Gpu'         => 'hostExtend.gpu',
            'D_sys_size' => 'hostExtend.sys_disk_size',


            'E_Name'        => 'Agent.display_name',
            'VPC_name'      => 'vpc.name',
            'D_Gpu'         => 'hostExtend.gpu',

            // 'I_SubnetCode'  => 'hostsNetworkCard.subnet_code',
            'S_FusionType'  => 'subnetExtend.fusionType',
            // 'I_Ip'          => 'hostsNetworkCard.ip',
            

        ];

        $where['InstanceBasic.type'] = 'hosts';
        $where['OR'] = [['InstanceBasic.biz_tid' => 0], ['InstanceBasic.biz_tid is' => null]];

        $instanceBasic = TableRegistry::get("InstanceBasic");
        $hosts_network_card_table = TableRegistry::get("HostsNetworkCard");

        $query =$instanceBasic->initJoinQuery()
            ->joinSubnet()
            ->joinRouter()
            ->joinAgent()
            ->joinVpc()
            ->joinHostExtend()
            // ->joinHostsNetworkCard()
            ->getJoinQuery()
            ->select($field)->join(
            [
                'eipExtend'=>[
                    'table' =>  'cp_eip_extend',
                    'type'  =>  'LEFT',
                    'conditions' => 'InstanceBasic.code = eipExtend.bindcode'
                ],
                'subnetExtend'=>[
                    'table' => 'cp_subnet_extend',
                    'type'  => 'LEFT',
                    'conditions' => 'subnet.id = subnetExtend.basic_id'
                ],
                'eip'=>[
                    'table' => 'cp_instance_basic',
                    'type'  => 'LEFT',
                    'conditions' => 'eipExtend.basic_id = eip.id and eip.code<> "" and eip.code is not null'
                ],
            ]
        )->where($where)->andWhere($andwhere)->group('InstanceBasic.id')->order('InstanceBasic.create_time DESC');

        $this->_pageList['total'] = $query->count();
        $this->_pageList['rows'] = $query->offset($offset)->limit($limit)
        ->map(function($row){
            $data = TableRegistry::get("HostsNetworkCard")
            ->find()
            ->select(['subnet_code', 'ip'])
            ->where(['basic_id' => $row['H_ID']])->toArray();
            $code = array();
            $ip = array();
            if (!empty($data)) {
                foreach ($data as $v) {
                    $code[] = $v['subnet_code'];
                    $ip[] = $v['ip'];
                }
            }
            $row['I_SubnetCode'] = implode(',', $code);
            $row['I_Ip'] = implode(',', $ip);
            return $row;

        });
        return $this->_pageList;
    }

    public function getmonitor($data)
    {
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        $orders = new OrdersController();
        $request_data['instanceCode'] = $data['code'];
        $request_data['method'] = 'monitor';
        $request_data['uid'] = (string)$this->request->session()->read('Auth.User.id'); //uid
        $request_data['basicId'] = $parameter['basicId'] = (string)$instance_basic_table->find()->select(['id'])->where(['code' => $data['code']])->first()->id;

        $arr = $orders->ajaxFun($request_data);
        $returnArr = array(
            'Code' => $arr['Code'],
            'Message' => '',
        );
        if ($arr['Code'] != '0') {
            $returnArr['Message'] = $arr['Message'];
        } else {
            $chart['cpu'] = $chart['disk'] = $chart['network'] = $chart['memory'] = array();
            // 修改时间轴数据
            $MessageData = array_reverse(json_decode($arr['Message'], true));
            $times = array();
            foreach ($MessageData as $key => $value) {
                // 时间点生成
                $times[] = date('H:i', $value['timestamp']);
                // 生成cpu
                $chart['cpu']['data'][] = $value['cpu'];
                // 生成memory
                $chart['memory']['data'][] = $value['memory'];
                // 入网
                $chart['network']['data1'][] = $value['networkIn'] / 1024;
                // 出网
                $chart['network']['data2'][] = $value['networkOut'] / 1024;
                // 读取
                $chart['disk']['data1'][] = $value['diskWrite'] / 1024;
                // 写入
                $chart['disk']['data2'][] = $value['diskRead'] / 1024;
            }
            $chart['cpu']['time'] = $chart['disk']['time'] = $chart['network']['time'] = $chart['memory']['time'] = $times;
            $returnArr['chart'] = $chart;
        }
        return $returnArr;
    }

    public function getDisksCount($code)
    {
        $disksMetadata = TableRegistry::get('DisksMetadata');
        $where = array(
            'attachhostid' => $code['code'],
        );
        $count = $disksMetadata->find('all')
            ->where($where)
            ->count();
        return $count;
    }

    public function getDisksLimit($request_data = array())
    {
        $table_userSetting = TableRegistry::get('UserSetting');
        $uid = (string)$this->request->session()->read('Auth.User.department_id');

        $whereUser = array(
            'para_code' => 'host_max_disk',
            'owner_id' => $uid,
            'owner_type' => 2,
        );
        $count = $table_userSetting->find()
            ->where($whereUser)
            ->first();
        if ($count != null) {
            return $count->para_value;
            die();
        } else {
            $disksMetadata = TableRegistry::get('Systemsetting');
            $where = array(
                'para_code' => 'host_max_disk',
            );
            $count = $disksMetadata->find()
                ->where($where)
                ->first();
            return $count->para_value;
            die();
        }
    }

    public function formatter_subnet($request_data)
    {
        $subnet = $request_data['subnet'];
        $table_hosts = TableRegistry::get('InstanceBasic');
        $entity = $table_hosts->find()->where(array(
            'type' => 'subnet',
            'code' => $subnet,
        ));
        foreach ($entity as $key => $value) {
            return $value['name'];
            die();
        }
    }

    public function formatter_eip($request_data)
    {
        $code = $request_data['code'];
        $table_hosts = TableRegistry::get('EipExtend');
        $entity = $table_hosts->find()
            ->where(array(
                'bindcode' => $code,
            ))
            ->first();
        if (!empty($entity)) {
            return $entity->ip;
            die();
        }
    }

    public function formatter_router($request_data)
    {
        $subnet = $request_data['router'];
        $table_hosts = TableRegistry::get('InstanceBasic');
        $entity = $table_hosts->find()->where(array(
            'type' => 'router',
            'code' => $subnet,
        ));
        foreach ($entity as $key => $value) {
            return $value['name'];
            die();
        }
    }

    /**
     * 编辑数据列表
     */
    public function edit($request_data = array())
    {
        $code = '0001';
        $data = array();
        // 编辑操作
        $host = TableRegistry::get('InstanceBasic');
        $account = $host->get($request_data['id']);
        $account = $host->patchEntity($account, $request_data);
        $account->id = $request_data['id'];
        if ($host->save($account)) {
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
     *            :@type:方法名称 @hostsId:操作主机ID
     * @date: 2015年10月12日 下午2:45:17
     * @author : shrimp liao
     * @return : null
     */
    public function ajaxHosts($request_data = array())
    {
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        $orders = new OrdersController();
        $result = array();
        $isEach = $request_data['isEach'];
        if ($request_data['method'] == 'ecs_delete') {
            $request_data['method'] = 'trash';
            $request_data['methodType'] = 'ecs_delete';
        } else {
            $request_data['methodType'] = '';
            $request_data['method'] = $request_data['method'];
        }
        $request_data['uid'] = (string)$this->request->session()->read('Auth.User.id');
        try {
            if ($isEach == 'true') {
                $table = $request_data['table'];
                foreach ($table as $key => $value) {
                    //主机操作预判断
                    $this->_ajaxHostBefore($value['H_ID'], $request_data['method'], $request_data);
                    if (!empty($value)) {
                        $interface = array('method' => $request_data['method'], 'uid' => $request_data['uid'], 'basicId' => $value['H_ID'], 'instanceCode' => $value['H_Code']);
                        $result = $orders->ajaxFun($interface);
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
                //主机操作预判断
                $this->_ajaxHostBefore($request_data['basicId'],$request_data['method'],$request_data);
                //其他操作预处理
                $request_data = $this->_doHostsBefore($request_data['method'], $request_data);

                // uid
                $request_data['instanceCode'] = $request_data['instanceCode'];
                unset($request_data['isEach']);
                return $orders->ajaxFun($request_data);
            }
        } catch (\Exception $e) {
            $code = $e->getCode();
            $msg = $e->getMessage();
            return ['Code' => $code, 'Message' => $msg];
        }
    }
    
    /**
     * 当前主机操作重新配置参数
     * 
     */
    protected function _doHostsBefore($method, $request_data)
    {
        //判断主机是否在创建镜像中是否允许操作
        $method_before = '_get_'.$method.'_para';
        //主机特殊操作之前判断
        if(method_exists($this,$method_before)){
            $request_data = $this->$method_before($request_data);
        }
        return $request_data;
    }
    
    protected function _get_eip_unbind_para($request_data)
    {
        $request_data['eipCode'] = $request_data['instanceCode'];
        return $request_data;
    }

    /**
     * [_ajaxHostBefore 当前主机操作是否合理]
     * @param  [int] $basic_id    [主机id]
     * @param  [string] $method   [操作主机方法]
     */
    protected function _ajaxHostBefore($basic_id, $method, $request_data = [])
    {
        //判断主机是否在创建镜像中是否允许操作
        $this->_isAllowAjaxHosts($basic_id);
        $method_before = '_' . $method . '_before';
        //主机特殊操作之前判断
        if (method_exists($this, $method_before)) {
            $this->$method_before($basic_id, $request_data);
        }
    }

    /**
     * [_ecs_reconfig_before 修改主机配置前判断]
     */
    protected function _ecs_reconfig_before($basic_id, $request_data)
    {
        $instance_basic = TableRegistry::get("InstanceBasic");
        $hostsEntity = $instance_basic->find()->select(['instance_type' => 'extend.type'])->join([
            'extend' => [
                'table' => 'cp_host_extend',
                'type' => 'LEFT',
                'conditions' => 'extend.basic_id = InstanceBasic.id'
            ]
        ])->where(['InstanceBasic.id' => $basic_id])->first();
        if ($request_data['instanceTypeCode'] == "") {
            throw new \Exception("没有选择配置，不能修改", 1);
        }
        if ($hostsEntity->instance_type == $request_data['instanceTypeCode']) {
            throw new \Exception("修改后主机配置相同，不能修改", 1);
        }
    }

    /**
     * [_trash_before 删除主机之前判断]
     */
    protected function _trash_before($basic_id, $request_data)
    {
        //获取当前主机是否绑定EIP
        if ($this->_isBindingEip($basic_id)) {
            throw new \Exception("主机包含EIP，请先解除EIP绑定", 1);
        }

        if($this->_hasExtendNetworkCard($basic_id)){
            throw new \Exception("主机拥有扩展网卡，不能被删除", 1);
        }

        //#判断是否有elb监听主机网卡
        $is_relate_elb = $this->checkEcsAndElbIsRelate($basic_id);
        if ($is_relate_elb['is_relate'] == 1) {
            throw new \Exception('该主机下有网卡被负载均衡监听，负载均名称：' . $is_relate_elb["name"] . '(' . $is_relate_elb["code"] . ')，请先删除监听关系', 1);
        }
    }

    /**
     * 判断主机是否有扩展网卡
     * @param $basic_id
     * @return bool
     */
    protected function _hasExtendNetworkCard($basic_id){
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        $count = $instance_basic_table->find()->contain(['HostsNetworkCard'])->matching('HostsNetworkCard',function ($q){
            return $q->where(['is_default'=>0,'network_code !='=>'']);
        })->where(['InstanceBasic.id'=>$basic_id])->count();
        return $count > 0 ? true : false;
    }

    /**
     * [_isAllowAjaxHosts 判断当前主机是否允许删除操作]
     * @param  [int]  $basic_id [主机id]
     */
    private function _isAllowAjaxHosts($basic_id)
    {
        $instance_basic = TableRegistry::get("InstanceBasic");
        $hostsEntity = $instance_basic->find()->select(['code', "status"])->where(['id' => $basic_id])->first();
        if ($hostsEntity != null && $hostsEntity->status == "创建镜像中") {
            throw new \Exception("该主机正在创建镜像中，正在被使用不能操作", 1);
        }
    }

    /**
     * 判断当前主机是否绑定eip
     * @param  int $basic_id 主机basic_id
     * @return boolean
     */
    protected function _isBindingEip($basic_id)
    {
        $eip_extend = TableRegistry::get('EipExtend');
        $eipEntity = $eip_extend->find()->where(['basic_id' => $basic_id])->first();
        if ($eipEntity != null && $eipEntity instanceof Entity) {
            return true;
        }
        return false;
    }

    /**
     * [ajaxExtendNetCard 判断主机是否有扩展网卡]
     * @param  [array] $request_data [主机信息]
     * @return [boolean]
     */
    protected function _isExtendNetCardExists($basic_id)
    {
        $hosts_network_card = TableRegistry::get('HostsNetworkCard');
        $cards_count = $hosts_network_card->find()->where(['is_default' => 0, 'basic_id' => $basic_id])->count();
        return $cards_count > 0 ? true : false;
    }

    /**
     * [ajaxExtendNetCardAllow 添加主机时，根据选择的默认子网，判断是否允许添加扩展网卡]
     * @param  [array] $request_data ['subnet_code'=>'']
     * @return [array]
     */
    public function ajaxExtendNetCardAllow($request_data)
    {
        $subnet_code = $request_data['subnet_code'];
        $instance_basic = TableRegistry::get('InstanceBasic');
        $subnet_extend = $instance_basic->find('SubnetExtend')->select(['fusionType' => 'sub_e.fusionType'])->where(['InstanceBasic.code' => $subnet_code])->first();

        $allow = false;
        //判断租户是否配置了可创建的公共子网
        $hasPublicSubnet = $instance_basic->find('PublicSubnetExtend')->where(['sub_e.isPublic' => '1', 'dept_subnet.dept_id' => $this->_createResourceDeptId])->count();
        if (is_array($subnet_extend) && !empty($subnet_extend)) {
            $allow = ($subnet_extend['fusionType'] == "vmware"
                || $subnet_extend['fusionType'] == "openstack")
            && (bool)$hasPublicSubnet == true ? true : false;
        }

        return ['code' => '0', 'allow' => $allow];
    }

    /**
     * [getPublicSubnetExtend 获取公共子网列表]
     * @return [array] [description]
     */
    public function getPublicSubnetExtend()
    {
        $instance_basic = TableRegistry::get('InstanceBasic');
        $subnet_extend_list = $instance_basic->find('PublicSubnetExtend')->join([
            'vpc' => [
                'table' => 'cp_instance_basic',
                'type' => 'LEFT',
                'conditions' => 'vpc.code = InstanceBasic.vpc'
            ]
        ])->select(['vpc_name' => 'vpc.name', 'subnet_code' => 'InstanceBasic.code', 'id' => 'InstanceBasic.id', 'subnet_name' => 'InstanceBasic.name'])->where(['sub_e.isPublic' => '1', 'dept_subnet.dept_id' => $this->_createResourceDeptId])->toArray();
        $this->_pageList['rows'] = $subnet_extend_list;
        $this->_pageList['total'] = count($subnet_extend_list);
        return $this->_pageList;
    }

    /**
     * 主机网卡 --- ajax操作
     */
    public function ajaxNetCard($request_data)
    {
        //判断是否允许添加扩展网卡，阿里云不支持添加扩展网卡
        if ($request_data['method'] == 'network_card_add') {
            $this->_isAllowAjaxNetCard($request_data);
        }
        //检查时候已添加该网卡
        $netWorkCard = TableRegistry::get("HostsNetworkCard");

        $data = $netWorkCard->find()->where(['basic_id' => $request_data['basic_id'], 'subnet_code' => $request_data['subnetCode']])->first();
        if (!empty($data)) {
            return array("code" => 1, "message" => "已有该子网下网卡");
            exit;
        }
        $orders = new OrdersController();
        $request_data['uid'] = (string)$this->request->session()->read('Auth.User.id');
        $request_data['is_eager'] = 'true';
        return $orders->ajaxFun($request_data);
    }

    /**
     * [ajaxImage 主机镜像操作]
     * @param  [array] $request_data
     * @return [json]
     */
    public function ajaxImage($request_data)
    {
        $orders = new OrdersController();
        $request_data['uid'] = (string)$this->request->session()->read('Auth.User.id');
        return $orders->ajaxFun($request_data);
    }

    public function editImage($request_data)
    {
        $id = $request_data['image_id'];
        $image_list = TableRegistry::get('Imagelist');
        $imageEntity = $image_list->find()->where(['id' => $id])->first();
        if ($imageEntity != null) {
            $imageEntity->set('image_name', $request_data['image_name']);
            $imageEntity->set('image_note', $request_data['image_note']);
            $re = $image_list->save($imageEntity);
            if ($re === false) {
                return ['Code' => '1', 'Message' => '修改镜像失败'];
            } else {
                return ['Code' => '0', 'Message' => '修改镜像成功'];
            }
        } else {
            return ['Code' => '1', 'Message' => '修改镜像失败,指定镜像不存在'];
        }

    }

    /**
     * [ajaxSnap 主机快照操作]
     * @param  [array] $request_data
     * @return [array]
     */
    public function ajaxSnap($request_data)
    {
        $ajaxBefore = $this->_ajaxSnapBefore($request_data);
        if (is_array($ajaxBefore)) {
            return $ajaxBefore;
        }
        $orders = new OrdersController();
        $request_data['uid'] = (string)$this->request->session()->read('Auth.User.id');
        return $orders->ajaxFun($request_data);
    }

    /**
     * 快照操作之前的条件判断
     * @param  array $request_data
     * @return array | boolean
     */
    public function _ajaxSnapBefore($request_data)
    {
        $instance_basic_table = TableRegistry::get("InstanceBasic");
        if ($request_data['method'] == "snapshot_add") {
            $count = $instance_basic_table->find()->contain(['HostExtend'])->matching('HostExtend', function ($q) {
                return $q->where(['gpu <>' => 0]);
            })->where(['code' => $request_data['code']])->count();
            if ((bool)$count) {
                return ['Code' => -1, 'Message' => "主机配置有GPU，不能创建快照"];
            }
            //[浙江环境]启用内存快照，Vmware主机如果挂载了独立硬盘，则不允许添加快照。
            if ($request_data['isMemory'] == "true") {
                $options['ecs_code'] = $request_data['code'];
                $disksCount = $instance_basic_table->find('FusionType', $options)->join([
                    'disks_extend' => [
                        'table' => 'cp_disks_metadata',
                        'type' => 'INNER',
                        'conditions' => 'disks_extend.attachhostid = InstanceBasic.code'
                    ],
                    'disks' => [
                        'table' => 'cp_instance_basic',
                        'type' => 'INNER',
                        'conditions' => 'disks.id = disks_extend.disks_id'
                    ]
                ])->where(['fusionType' => 'vmware'])->count();
                if ($disksCount > 0) {
                    return ['Code' => -1, 'Message' => "Vmware主机挂载有独立硬盘，不能创建内存快照"];
                }
            }
        }
        return true;
    }

    /**
     * [ajaxImageDel 主机镜像删除操作]
     * @param  [array] $request_data ['basic_id'=>string,'method'=>'image_del']
     * @return [array]
     */
    public function ajaxImageDel($request_data)
    {
        $popedom_list = $this->request->session()->read('Auth.User.popedomname');
        $department_id = $this->request->session()->read('Auth.User.department_id');

        $basic_ids = explode(',', $request_data['basic_id']);
        $image_list = TableRegistry::get('Imagelist');
        $host_extend = TableRegistry::get('HostExtend');
        if (empty($basic_ids) || !is_array($basic_ids)) {
            return ['Code' => '1', 'Message' => '参数错误'];
        }
        $results = [];
        $message = '';
        foreach ($basic_ids as $key => $value) {

            $data = array();
            $imageEntity = $image_list->find()->select(['image_code', 'status', 'department_id'])->where(['id' => $value])->first();
            if ($imageEntity == null) {
                continue;
            }
            if ($imageEntity->status == "创建中") {
                $message .= "镜像创建中，不能删除。";
                continue;
            }
            if ($imageEntity->department_id != $department_id) {
                $message .= "镜像" . $imageEntity->image_code . "不属于当前租户下资源，不允许删除。";
                continue;
            } else {
                if (!in_array("ccf_image_del", $popedom_list)) {
                    $message .= "当前用户没有删除镜像权限，不允许删除。";
                    continue;
                }
            }

            //如果主机正在使用镜像，不允许删除镜像
            $hosts = $host_extend->find()->select(['basic_id'])->where(['image_code' => $imageEntity->image_code])->first();
            if ($hosts == null) {
                $data['method'] = $request_data['method'];
                $data['basic_id'] = $value;
                $data['imageCode'] = $imageEntity->image_code;
                $results[] = $this->ajaxImage($data);
            } else {
                $message .= "镜像" . $imageEntity->image_code . "有主机正在使用，不能删除。";
            }
        }
        if ($message != "") {
            return ['Code' => '1', 'Message' => $message];
        }
    }

    /**
     * [imageList 镜像列表]
     * @param  [array] $request_data ['basic_id'=>int]
     * @return [array]               [_pageList]
     */
    public function imageList($request_data)
    {
        $image_list = TableRegistry::get('Imagelist');

        $query = $image_list->find()->select(['id', 'image_name', 'image_code', 'image_note', 'is_private', 'create_time'])->where(['basic_id' => $request_data['basic_id']]);
        $this->_pageList['total'] = $query->count();
        $this->_pageList['rows'] = $query->toArray();
        return $this->_pageList;
    }

    public function snapList($request_data)
    {
        $snap_list = TableRegistry::get('Snapshotlist');

        $query = $snap_list->find()->select(['id', 'description', 'code', 'create_time', 'status', 'isMemory'])->where(['basic_id' => $request_data['basic_id']]);
        $this->_pageList['total'] = $query->count();
        $this->_pageList['rows'] = $query->toArray();
        return $this->_pageList;
    }


    /**
     * [_isAllowAjaxNetCard 阿里云不支持添加扩展网卡]
     * @param  [array]  $request_data
     */
    private function _isAllowAjaxNetCard($request_data)
    {
        $instance_basic = TableRegistry::get('InstanceBasic');
        $agent_info = $instance_basic->find()->select(['status', 'agent_code' => 'agentP.agent_code'])->join(['agent' => [
            'table' => 'cp_agent',
            'type' => 'LEFT',
            'conditions' => 'agent.class_code = InstanceBasic.location_code'
        ],
            'agentP' => [
                'table' => 'cp_agent',
                'type' => 'LEFT',
                'conditions' => 'agent.parentid = agentP.id'
            ]
        ])->where(['InstanceBasic.id' => $request_data['basic_id']])->first();
        $options['basic_id'] = $request_data['basic_id'];
        $fusionType = $instance_basic->find('FusionType', $options)->first();
        if ('aliyun' == $fusionType['fusionType']) {
            echo json_encode(['code' => 1, 'message' => '很抱歉，阿里云不支持扩展网卡！']);
            exit;
        }
        if ('openstack' == $fusionType['fusionType']) {
            echo json_encode(['code' => 1, 'message' => '很抱歉，openstack不支持扩展网卡！']);
            exit;
        }
        if ($agent_info['status'] != "运行中") {
            echo json_encode(['code' => 1, 'message' => '无法在当前状况（已关闭电源）下操作！']);
            exit;
        }
    }

    public function test($data)
    {
        $orders = new OrdersController();
        return $orders->ajaxFun($data);
    }

    public function getHostsEntityByCode($code)
    {
        $basic_table = TableRegistry::get('InstanceBasic');
        $where = array(
            'code' => $code,
        );
        $entity = $basic_table->find('all')
            ->where($where)
            ->toArray()[0];
        return $entity;
    }

    /**
     * [getHostHardwareSet 根据主机id，获取当前主机可修改的配置]
     * @author [lanzhenxiang] <[<lanzhenxiang@sobey.com>]>
     * @param  [array] $param ["id"=>1]
     * @return [array]        [可用配置]
     */
    public function getHostHardwareSet($param)
    {
        $id = $param['id'];
        $instance_basic = TableRegistry::get('instance_basic');
        $field = ['agent_id' => 'agent.id'];
        $basic_info = $instance_basic->getHostBasicInfoByID($id, $field);
        $agent = TableRegistry::get('agent_set');
        $query = $agent->find();
        $query = $query->select([
            'set_code' => 'hardware.set_code',
            'cpu_number' => 'hardware.cpu_number',
            'memory_gb' => 'hardware.memory_gb',
            'gpu_gb' => 'hardware.gpu_gb'
        ]);
        $query->join(
            [
                'hardware' => [
                    'table' => 'cp_set_hardware',
                    'type' => 'LEFT',
                    'conditions' => 'hardware.set_id = agent_set.set_id'
                ]
            ]
        )->where(['agent_set.agent_id' => $basic_info[0]['agent_id']]);
        $hardwareList = $query->toArray();
        $result = [];
        $tmpCPUList = [];
        foreach ($hardwareList as $key => $value) {
            //把内存值作为cpu的下层级数组
            $tmpArr = array(
                'num' => $value->memory_gb,
            );
            //利用配置的cpu数量为索引，建立层级数组
            $tmpCPUList[$value->cpu_number][] = $tmpArr;
        }
        ksort($tmpCPUList);
        //循环临时数组，建立rom层的下级数组gpu
        foreach ($tmpCPUList as $cpu_number => $cpu_set) {
            //排序
            asort($cpu_set);
            sort($cpu_set);
            //根据内存值的大小获取关联下层的GPU数组
            foreach ($cpu_set as $k => $rom_set) {
                $cpu_set[$k]['gpu'] = $this->_getGpuArray($cpu_number, $rom_set['num'], $hardwareList);
            }
            $tarr = array(
                'cpu' => $cpu_number,
                'rom' => $cpu_set,
            );
            $result[] = $tarr;
        }
        return $result;
    }

    /**
     * [_getGpuArray 获取相同cpu数量，内存大小的GPU数组]
     * @param  [int] $cpu
     * @param  [int] $rom
     * @param  [array] $hardwareList
     * @return [array]
     */
    protected function _getGpuArray($cpu, $rom, $hardwareList)
    {
        $gpuList = [];
        foreach ($hardwareList as $key => $value) {
            if ($value['cpu_number'] == $cpu && $value['memory_gb'] == $rom) {
                $gpu[$value->set_code] = $value->gpu_gb;
            }
        }
        asort($gpu);
        foreach ($gpu as $set_code => $num) {
            $gpuList[] = [
                'gpu' => $num,
                'setCode' => $set_code
            ];
        }
        return $gpuList;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \App\Controller\Console\ConsoleController::test()
     */
    public function createHostsArray($request_data)
    {
        if (isset($request_data['dept_id']) && (int)$request_data['dept_id'] > 0) {
            $department_id = (int)$request_data['dept_id'];
        } else {
            $department_id = $this->getOwnByDepartmentId();
        }
        $agent_table = TableRegistry::get('Agent');
        $agent_departments = TableRegistry::get('AgentDepartments');
        $agentList = $agent_departments->find()->select('agent_id')->where(['dept_id'=>$department_id])->all()->extract('agent_id')->toArray();
        $data_list = $agent_table->find('tree', array(
            'order' => 'Agent.sort_order ASC',
        ))
            ->contain(array(
                'SetHardware',
                'Imagelist' => function ($q) use ($department_id) {
                    //主机镜像image_type = 1
                    $query = $q->Where(['image_type' => '1'])
                        ->Where(function ($exp) use ($department_id) {
                            //私有镜像，只可见当前租户下的镜像
                            $orCondition = $exp->and_(['is_private' => '1', 'department_id' => $department_id]);
                            //系统镜像,公共镜像，租户不限制
                            return $exp->or_(['is_private' => '0', 'image_source <>' => '2'])
                                ->add($orCondition);
                        });
                    return $query;
                }
            ))
            ->where(array(
                'is_enabled' => 1,
                'or'=>['parentid'=>0,'id in'=>$agentList]
            ))
            ->toArray();
//        debug($data_list);exit;
        $data_agent = array();
        // 城市
        // 顶级的hardware赋值给children
        foreach ($data_list as $key => $value) {
            if (!empty($value['children'])) {
                $data_agent[$key] = array(
                    'id' => $value['id'],
                    'company' => array(
                        'name' => $value['agent_name'],
                        'companyCode' => $value['agent_code'],
                        'price' => self::getPrice($value['id'], 'elb', '元/'),
                        'disksPrice' => self::getPrice($value['id'], 'disks', '元/')
                    ),
                    'area' => array(),
                );
                foreach ($value['children'] as $kk => $vv) {
                    $tmpArr = array();
                    $tmpArrTwo = array();
                    $tmpset = array();
                    $tmpOs = array();
                    $gpuArr = array();
                    foreach ($vv['set_hardware'] as $kkk => $vvv) {
                      $tmpArr = array(
                            'num' => $vvv->memory_gb,
                            'setCode' => $vvv->set_code,
                            'priceDay' => $vvv->price_day,
                            'priceMonth' => $vvv->price_month,
                            'priceYear' => $vvv->price_year,
                        );
                        $tmpArrTwo[$vvv->cpu_number][] = $tmpArr;
                    }
                    ksort($tmpArrTwo);
                    foreach ($tmpArrTwo as $k => $v) {
                        asort($v);
                        sort($v);
                        $tarr = array(
                            'cpu' => $k,
                            'rom' => $v,
                        );
                        $tmpset[] = $tarr;
                    }

                    $imageType = $this->createOSArray($vv['imagelist']);
                    $vpc = $this->getAllVpc($vv['class_code'], $department_id);
                    $eip = $this->getAllEip($vv['class_code'], $department_id);

                    $data_agent[$key]['area'][] = array(
                        'id' => $vv['id'],
                        'name' => $vv['agent_name'],
                        'areaCode' => $vv['region_code'],
                        'displayName' => $vv['display_name'],
                        'set' => $tmpset,
                        'imageType' => $imageType,
                        'vpc' => $vpc,
                        'eip' => $eip,
                    );
//                     if (empty($imageType)) {
//                         unset($data_agent[$key]);
//                     }
                }
            }
        }
        $data_agent = array_values($data_agent);
        // debug($data_agent);die;
        return $data_agent;
    }

    //2017-3-13防火墙
    public function createHostsFireArray($request_data)
    {
        if (isset($request_data['dept_id']) && (int)$request_data['dept_id'] > 0) {
            $department_id = (int)$request_data['dept_id'];
        } else {
            $department_id = $this->getOwnByDepartmentId();
        }
        $agent_table = TableRegistry::get('Agent');
        $data_list = $agent_table->find('tree', array(
            'order' => 'Agent.sort_order ASC',
        ))
        ->where(array(
            'is_enabled' => 1,
        ))
        ->toArray();
        // debug($data_list);exit;
        $data_agent = array();
        // 城市
        // 顶级的hardware赋值给children
        foreach ($data_list as $key => $value) {
            if (!empty($value['children'])) {
                $data_agent[$key] = array(
                    'id' => $value['id'],
                    'company' => array(
                        'name' => $value['agent_name'],
                        'companyCode' => $value['agent_code'],
                        'price'       => self::getPrice($value['id'], 'vfw', '元/')  
                    ),
                    'area' => array(),
                );
                foreach ($value['children'] as $kk => $vv) {
                    $data_agent[$key]['area'][] = array(
                        'id' => $vv['id'],
                        'name' => $vv['agent_name'],
                        'areaCode' => $vv['region_code'],
                        'displayName' => $vv['display_name'],
                        'vpc' => $this->getSelectVpc($vv['class_code'], $department_id)
                    );

                }
            }
        }
        $data_agent = array_values($data_agent);
        return $data_agent;
    }


    public function createSecurityGroupArray(){
        $str = array();
        $agent = TableRegistry::get('Agent');
        $where = array('is_enabled' => 1);
        //获取商品信息，包含商品分类
        $agentInfo = $agent->find('all')->where($where)->order(array('Agent.sort_order' => 'ASC'))->toArray();
        $str = array();
        foreach ($agentInfo as $index => $item) {
            if ($item['parentid'] == 0) {
                $agent = array();
                $agent['company'] = array('name' => $item['agent_name'], 'companyCode' => $item['agent_code'], 'price' => self::getPrice($item['id'], 'securityGroup', '元/'),);
                $agent['area'] = $this->getAreaListByIdVT($item['id'], $agentInfo);
                $str[] = $agent;
            }
        }
        return $str;
    }


    public function hasFirewallEcs($request_data)
    {
        $subnetCode = $request_data['subnetCode'];
        $instance_basic = TableRegistry::get("InstanceBasic");
        $subnet = $instance_basic->find()->hydrate(false)
            ->select(['fusionType'=>'subextend.fusionType'])
            ->join([
                "subextend"=>[
                    'table'=>'cp_subnet_extend',
                    'type' =>'INNER',
                    'conditions'=>'subextend.basic_id = InstanceBasic.id'
                ]
            ])->where(['InstanceBasic.code'=>$subnetCode])->first();
        $msg = null;
        $code = 0;
        if(!$subnet){
            $code =1;
            $msg = "子网code不能为空";
        }
        if($subnet['fusionType'] == "vmware" || $subnet['fusionType'] == "openstack"){

            $firewallecs = $instance_basic->find()->hydrate(false)
                ->select(['firewallecs_id'=>'firewallecs.id','firewallstatus'=>'firewallecs.status'])->join([
                    "firewallecs"=>[
                        'table' =>'cp_instance_basic',
                        'type'  =>'LEFT',
                        'conditions' =>'firewallecs.vpc = InstanceBasic.vpc AND firewallecs.type ="firewallecs"'
                    ]
               ])->where(['InstanceBasic.code'=>$subnetCode])->first();

            if(empty($firewallecs['firewallecs_id'])){
                $code = 1;
                $msg = "创建EIP之前，请先在选中VPC下创建防火墙";
            } else {
                if($firewallecs['firewallstatus'] != "运行中"){
                    $code = 1;
                    $msg = "当前防火墙实例状态不可用";
                }
            }
        }
        echo json_encode(compact(['code','msg']));exit;
    }

    public function createFirewallArray($request_data)
    {
        if((int)$request_data['dept_id'] > 0){
            $department_id = (int)$request_data['dept_id'];
        }else{
            $department_id = $this->getOwnByDepartmentId();
        }
        
        $agent_table = TableRegistry::get('Agent');
        $data_list   = $agent_table->find('tree', array(
            'order' => 'Agent.sort_order ASC',
        ))
        ->where(array(
            'is_enabled' => 1,
        ))
        ->toArray();
        $data_agent = array();
       
        foreach ($data_list as $key => $value) {
            if (!empty($value['children'])) {
                $data_agent[$key] = array(
                    'id'      => $value['id'],
                    'company' => array(
                        'name'        => $value['agent_name'],
                        'companyCode' => $value['agent_code'],
                        'price'       => self::getPrice($value['id'], 'vfw', '元/')  
                    ),
                    'area'    => array(),
                );
                foreach ($value['children'] as $kk => $vv) {
                    $data_agent[$key]['area'][] = array(
                        'id'       => $vv['id'],
                        'name'     => $vv['agent_name'],
                        'areaCode' => $vv['region_code'],
                        'displayName' => $vv['display_name'],
                        'vpc'      => $this->getAllVpc($vv['class_code'],$department_id),
                    );
                }
            }
        }
        $data_agent = array_values($data_agent);
        return $data_agent;
    }
    
    /**
     * 获取厂商eip价格
     * @param string $angent_id
     * @return array
     */
    public static function getPrice($agent_id, $type, $unit) {
        $charge_extend_table = TableRegistry::get("ChargeExtend");
        $price = $charge_extend_table->find()->where(["agent_id" => $agent_id, "charge_object" => $type])->first();
        if (!empty($price)) {
            $price_info[0]['id'] = 1;
            $price_info[0]['name'] = '按天计费';
            $price_info[0]['price'] = $price["daily_price"];
            $price_info[0]['unit'] = $unit.'天';
            $price_info[1]['id'] = 2;
            $price_info[1]['name'] = '按月计费';
            $price_info[1]['price'] = $price["monthly_price"];
            $price_info[1]['unit'] = $unit.'月';
            $price_info[2]['id'] = 3;
            $price_info[2]['name'] = '按年计费';
            $price_info[2]['price'] = $price["yearly_price"];
            $price_info[2]['unit'] = $unit.'年';
        } else {
            $price_info[0]['id'] = 1;
            $price_info[0]['name'] = '按天计费';
            $price_info[0]['price'] = 0;
            $price_info[0]['unit'] = $unit.'天';
            $price_info[1]['id'] = 2;
            $price_info[1]['name'] = '按月计费';
            $price_info[1]['price'] =0;
            $price_info[1]['unit'] = $unit.'月';
            $price_info[2]['id'] = 3;
            $price_info[2]['name'] = '按年计费';
            $price_info[2]['price'] = 0;
            $price_info[2]['unit'] = $unit.'年';
        }
        return $price_info;
    
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
        // debug($vpcList);
        foreach ($vpcList as $key => $value) {
            $net = $this->getAllsubNet($value['code']);
            if(!empty($net)){
                $vpcArray[] = array(
                    'name'   => $value['name'],
                    'vpCode' => $value['code'],
                    'net'    => $this->getAllsubNet($value['code']),
                );
            }
        }
        return $vpcArray;
    }

    //防火墙2017-3-13
    public function getSelectVpc($class_code,$department_id)
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
        // debug($vpcList);
        foreach ($vpcList as $key => $value) {
            if($table->find()->select(['id'])->where(array('type'=>'firewall','vpc'=>$value['code']))->count()==0){
                // $net = $this->getAllsubNet($value['code']);
                // if(!empty($net)){
                    $vpcArray[] = array(
                        'name'   => $value['name'],
                        'vpCode' => $value['code'],
                        // 'net'    => $net,
                    );
                // }
            }

        }
        return $vpcArray;
    }

    public function getAllEip($class_code,$department_id)
    {
        $account_table = TableRegistry::get('Accounts');
        $table    = TableRegistry::get('InstanceBasic');
        $vpcArray = array();
        $where    = array(
            'status'        => '运行中',
            'type'          => 'eip',
            'location_code' => $class_code,
            'department_id' => $department_id,
        );
        $vpcList = $table->find('all')
            ->contain(array(
                'EipExtend',
            ))
            ->where(function ($exp, $q) {
                return $exp->isNull('bindcode');
            })
            ->orWhere(array(
                'bindcode' => '',
            ))
            ->where($where)
            ->toArray();
        foreach ($vpcList as $key => $value) {
            $vpcArray[] = array(
                'name'    => $value['name'],
                'eipCode' => $value['code'],
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
        //debug($netArray);exit;
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
            );
        }
        return $subnetList;
    }

    /**
     * @func: 创建前台所需的套餐数据
     *
     * @param
     *            :
     *            @date: 2015年10月15日 下午4:37:46
     * @author : shrimp liao
     * @return : null
     */
    public function test11111()
    {
        $str            = array();
        $agent          = TableRegistry::get('Agent');
        $agentimagelist = TableRegistry::get('AgentImagelist', array(
            'classname' => 'App\\Model\\Table\\AgentImagelistTable',
        ));
        $setsoftware = TableRegistry::get('GoodsVersionSpec', array(
            'classname' => 'App\\Model\\Table\\GoodsVersionSpecTable',
        ));
        $where = array(
            '1' => '1',
        );
        // 获取aduser
        $aduser = TableRegistry::get('AdUser', array(
            'classname' => 'App\\Model\\Table\\AdUserTable',
        ));
        $aduserinfo = $aduser->find('all', array(
            'fields' => array(
                'id',
                'loginName',
            ),
        ))->toArray();
        // 获取商品信息，包含商品分类
        $agentInfo = $agent->find('all')
            ->contain(array(
                'AgentImagelist',
                'AgentSet',
            ))
            ->where($where)
            ->toArray();
        // 获取商品信息，包含商品分类
        $setsoftwareInfo = $setsoftware->find('all')
            ->select('brand')
            ->distinct(array(
                'brand',
            ))
            ->toArray();
        // 获取基础数据信息（加载一次）
        $baseNet = $this->getBaseTypeByName('subnet');
        $subnet  = $this->getBaseTypeObjeByArray($baseNet, 'net');
        // 加载子网络
        foreach ($agentInfo as $index => $item) {
            if ($item['parentid'] == 0) {
                $agent            = array();
                $agent['id']      = $item['id'];
                $agent['company'] = array(
                    'name'        => $item['agent_name'],
                    'companyCode' => $item['agent_code'],
                );
                $agent['area'] = $this->getAreaListById($item['id'], $agentInfo);
                // $agent['set']=$this->getSetObjectByArray($item['agent_set']);
                $agent['net'] = $subnet;
                // $agent['Os']=$this->createOSArray($item['agent_imagelist']);
                // $agent['Firewall']=$this->createOSArray($item['agent_imagelist']);
                // $agent['AdUser']=$aduserinfo;
                // $agent['setsoftwareInfo']=$this->createSoftwareArray($setsoftwareInfo);
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
    public function getAreaListById($id, $agentInfo)
    {
        $str               = array();
        $instance_basic    = TableRegistry::get('InstanceBasic');
        $instance_relation = TableRegistry::get('InstanceRelation');
        $vpc_extend        = TableRegistry::get('VpcExtend');
        $department_id     = $this->request->session()->read('Auth.User.department_id') ? $this->request->session()->read('Auth.User.department_id') : 0;
        foreach ($agentInfo as $index => $item) {
            if ($item['parentid'] == $id) {
                $router_data = array();
                $router      = $instance_basic->find()
                    ->select(array(
                        'id',
                        'name',
                    ))
                    ->where(array(
                        'location_code' => $item['class_code'],
                        'type'          => 'router',
                        'code <>'       => '',
                        'department_id' => $department_id,
                    ))
                    ->toArray();
                foreach ($router as $key => $value) {
                    $vpcid = $instance_relation->find()
                        ->select(array(
                            'toid',
                        ))
                        ->where(array(
                            'fromid'   => $value['id'],
                            'fromtype' => 'router',
                            'totype'   => 'vpc',
                        ))
                        ->toArray();
                    if ($vpcid) {
                        $cidr = $vpc_extend->find()
                            ->select(array(
                                'cidr',
                            ))
                            ->where(array(
                                'basic_id' => $vpcid[0]['toid'],
                            ))
                            ->toArray();
                        if ($cidr) {
                            $router_data[$key]['cidr'] = $cidr[0]['cidr'];
                        }
                    }
                    $router_data[$key]['id']   = $value['id'];
                    $router_data[$key]['name'] = $value['name'];
                }
                $str[] = array(
                    'name'     => $item['agent_name'],
                    'areaCode' => $item['region_code'],
                    'router'   => $router_data,
                    'displayName' => $item['display_name']
                );
            }
        }
        // var_dump($str);exit;
        return $str;
    }
    public function getAreaListByIdVT($id, $agentInfo)
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
                $virtualList = $systemsetting->find()->select(['para_value','para_note'])->where(['para_value in'=>$virtual_technology])->toArray();

                $str[] = array('name' => $item['agent_name'], 'areaCode' => $item['region_code'], 'router' => $router_data,'virtual'=>$virtualList,'vpc'=>$this->getSelectVpc($item['class_code'],$this->getOwnByDepartmentId()));
            }
        }
        return $str;
    }
    /**
     * @func: 根据套餐返回套餐数组
     *
     *
     * @param
     *            :
     *            @date: 2015年10月15日 下午6:41:38
     * @author : shrimp liao
     * @return : null
     */
    public function getSetObjectByArray($setInfo)
    {
        $setHardware = TableRegistry::get('SetHardware');
        $setHardware->find()
            ->select(array(
                'cpu_number',
            ))
            ->where($where)
            ->toArray();
        $cpu = array();
        foreach ($setInfo as $item) {
            $set_id = $item['set_id'];
            $where  = array(
                'set_id' => $set_id,
            );
            $cpu_s = $setHardware->find()
                ->select(array(
                    'cpu_number',
                ))
                ->where($where)
                ->toArray();
            // debug($cpu_s);die();
            $cpu[] = $cpu_s;
        }
        $info = array_unique($info);
        foreach ($info as $index => $i) {
            $set = array();
            foreach ($setInfo as $item) {
                if ($item['cpu_number'] == $i) {
                    $set[] = array(
                        'num'     => $item['memory_gb'],
                        'setCode' => $item['set_type_code'],
                    );
                    $info[$index] = array(
                        'cpu' => $item['cpu_number'],
                        'rom' => $set,
                    );
                }
            }
        }
        sort($info);
        return $info;
    }

    public function getImageNameById($request_data = array())
    {
        $image_code = $request_data['image_code'];
        $imageTable = TableRegistry::get('AgentImagelist');
        $image      = $imageTable->find('all')
            ->distinct(array(
                'image_code',
            ))
            ->where(array(
                'image_code' => $image_code,
            ))
            ->toArray();
        return $image;
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
    public function getBaseTypeByName($name, $regionCode)
    {
        $baseTable = TableRegistry::get('InstanceBasic');
        $where     = array(
            'type'          => $name,
            'code <>'       => '',
            'status'        => '运行中',
            'location_code' => $regionCode,
        );
        $agentInfo = $baseTable->find('all')
            ->where($where)
            ->toArray();
        return $agentInfo;
    }

    /**
     * @func:更具数据源，得到基础需要类型对应数组
     *
     * @param
     *            :
     *            @date: 2015年10月16日 上午11:12:58
     * @author : shrimp liao
     * @return : null
     */
    public function getBaseTypeObjeByArray($array, $name)
    {
        $str = array();
        foreach ($array as $item) {
            $str[] = array(
                'name'         => $item['name'],
                $name . 'Code' => $item['code'],
            );
        }
        return $str;
    }

    public function createOSArray($imageArray)
    {
        if (empty($imageArray)) {
            return array();
        }
        $platlist = array();
        $image_type = ['system'=>[],'private'=>[],'public'=>[]];
        foreach ($imageArray as $key => $value) {
            $platlist[] = $value['plat_form'];
            if($value['image_source'] =='2'){
                if($value['is_private'] == 1){//私有镜像
                    $image_type['private'][] = $value;
                }else{
                    $image_type['public'][] = $value;
                }
            }else{
                $image_type['system'][] = $value;
            }
        }

        foreach ($image_type as $key => $value) {
            if(empty($value)){
                unset($image_type[$key]);
                continue;
            }
            $name = str_replace(array('system','private','public'),array('系统镜像','私有镜像','公共镜像'),$key);
            $str[] = array(
                'name' =>$name,//名称
                'Os'=>$this->_createOSArray($image_type[$key],$platlist)//镜像信息
            );
        }
        return $str;
    }

    private function _createOSArray($image_array,$index_list){
        $str = [];
        // debug($image_array);exit;
        $index_list = array_unique($index_list);
        foreach ($index_list as $index) {
            $type = array();
            foreach ($image_array as $item) {
                if ($item['plat_form'] == $index) {
                    $type[] = array(
                        'name'     => $item['image_name'],
                        'typeCode' => $item['image_code'],
                        'priceYear' => $item['price_year'],
                        'priceMonth' => $item['price_month'],
                        'priceDay' => $item['price_day']
                    );
                }
            }
            if(!empty($type)){
                $str[] = array(
                    'name'  => $index,
                    'types' => $type,
                );
            }
        }
        return $str;
    }
    /**
    private function _array_group_value(array $input,$group_array){
        if(!is_array($input) || empty($input) || !is_array($group_array) || empty($group_array)){
            return array();
        }
        $index_value = end($group_array);
        $group_array = array_pop($group_array);
        $index_list = [];
        foreach ($input as $key => $value) {
            $index_list[] = $value[$index_value];
        }
        $index_list = array_unique($index_list);
        if(empty($index_list))return array();
        $result_set = array();

        foreach ($index_list as $key => $index) {
            $items = array();
            foreach ($input as $key =>$item) {
                if ($item[$index_value] == $index) {
                    $items[] = $item;
                }
            }
            $result_set[] = array(
                'name'  => $index,
                'items' => $this->_array_group_value($items,$group_array),
            );
        }
        return $result_set;
    }
    */

    public function createSoftwareArray($setsoftware)
    {
        $setsoftwareTable     = TableRegistry::get('GoodsVersionSpec');
        $sethardwareTable     = TableRegistry::get('SetHardware');
        $setsoftwareinfoArray = $setsoftwareTable->find('all')->toArray();
        $sethardwareinfoArray = $sethardwareTable->find('all')->toArray();
        $str                  = array();
        foreach ($setsoftware as $image) {
            $type = array();
            foreach ($setsoftwareinfoArray as $item) {
                if ($item['brand'] == $image['brand']) {
                    foreach ($sethardwareinfoArray as $key => $value) {
                        if ($value['set_code'] == $item['instancetype_code']) {
                            $software[] = array(
                                'name'            => $item['set_name'],
                                'imageCode'       => $item['image_code'],
                                'softwareNote'    => $item['set_note'],
                                'setCode'         => $item['set_code'],
                                'hardware_name'   => $value['set_name'],
                                'hardware_code'   => $value['set_code'],
                                'hardware_cpu'    => $value['cpu_number'],
                                'hardware_memory' => $value['memory_gb'],
                            );
                        }
                    }
                }
            }
            $str[] = array(
                'name'         => $image['brand'],
                'softwareInfo' => $software,
            );
        }
        return $str;
    }

    public function desktop_add($data)
    {
        $data['uid'] = (string) $this->request->session()->read('Auth.User.id');
        $orders      = new OrdersController();
        $table       = TableRegistry::get('Accounts');
        $entity      = $table->find()
            ->where(array(
                'id' => $data['uid'],
            ))
            ->first();
        $data['username']     = $entity->loginname;
        $data['userpassword'] = $entity->password;
        $data['fimasName']    = 'fimas';
        $result               = $orders->ajaxFun($data);
        return $result;
        die();
    }

    public function webadmin($data)
    {
        $orders               = new OrdersController();
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        $data['uid']          = (string) $this->request->session()->read('Auth.User.id');
        $data['basicId']      = (string) $instance_basic_table->find()->select(['id'])->where(['code' => $data['instanceCode']])->first()->id;
        $data['password']     = '123456';
        $result               = $orders->ajaxFun($data);
        return $result;
        die();
    }

    public function hostDetail($basic_id = 6021)
    {
        $basic_id          = 6021;
        $instance_basic    = TableRegistry::get('InstanceBasic');
        $instance_relation = TableRegistry::get('InstanceRelation');
        $imagelist         = TableRegistry::get('Imagelist');
        $data              = $instance_basic->find('all')
            ->contain(array(
                'HostExtend',
                'Agent',
            ))
            ->where(array(
                'id' => $basic_id,
            ))
            ->toArray();
        $subnet = $instance_relation->find()
            ->where(array(
                'fromid' => $data[0]['id'],
                'totype' => 'subnet',
            ))
            ->first();
        $image = $imagelist->find()->where(array(
            'image_code' => $data[0]['HostExtend']['image_code'],
        ));
        var_dump($data);
        die();
    }

    public function createHostDetailArray()
    {}

    //根据不同类型查询该设备使用的配合
    public function getInstanceQuotaByType($data)
    {
        $type           = $data["type"];
        $basicId        = $data["basicId"];
        $code           = $data["code"];
        $instance_basic = TableRegistry::get('HostExtend');
        $disks_metadata = TableRegistry::get('DisksMetadata');
        $extends        = [];
        $disks          = array("count" => 0);

        // $result = array('code' => '0','data'=>[]);
        $message = array('code' => 0, 'msg' => '');

        $id       = $this->request->session()->read('Auth.User.id');
        $response = Requests::post(Configure::read('Api.cmop') . '/SystemInfo/getDepartBuget', [],
            [
                'userid' => $id,
            ], ['verify' => false]);
        $limit   = json_decode($response->body, true);
        $limit   = $limit['data'];
        $requset = Requests::post(Configure::read('Api.cmop') . '/SystemInfo/getDepartUsed', [],
            [
                'userid'      => $id,
                "source_type" => "cpu_used,router_used,subnet_used,disks_used",
            ], ['verify' => false]);
        $used = json_decode($requset->body, true);
        if (empty($used['data'])) {
            $used = array('0');
        } else {
            $used = $used['data'];
        }
        $userLimti = array_merge($limit, $used);
        switch ($type) {
            case 'hosts':
                $extends = $instance_basic->find()->select(['cpu', 'memory', 'gpu'])->where(["basic_id" => $basicId])->first();
                if (!empty($code)) {
                    // $disks = $disks_metadata->find()->select(["capacity"=>$disks_metadata->func()->count('capacity')->where(["attachhostid"=>$code])]);
                    $find  = $disks_metadata->find();
                    $disks['count'] = $find->where(["attachhostid" => $code])->count();
                }
                if ((int) $userLimti["cpu_used"] + (int) $extends["cpu"] > (int) $userLimti["cpu_bugedt"]) {
                    $message = array('code' => 1, 'msg' => 'CPU配额不足');
                } else if ((int) $userLimti["memory_used"] + (int) $extends["memory"] > (int) $userLimti["memory_buget"]) {
                    $message = array('code' => 1, 'msg' => '内存配额不足');
                } else if ((int) $userLimti["gpu_used"] + (int) $extends["gpu"] > (int) $userLimti["gpu_bugedt"]) {
                    $message = array('code' => 1, 'msg' => 'GPU配额不足');
                } else if ((int) $userLimti["disks_used"] + (int) $disks["count"] > (int) $userLimti["disks_bugedt"]) {
                    $message = array('code' => 1, 'msg' => '磁盘配额不足');
                }
                break;
            case 'desktop':
                $extends = $instance_basic->find()->select(['cpu', 'memory', 'gpu'])->where(["basic_id" => $basicId])->first();
                if (!empty($code)) {
                    $disks = $disks_metadata->find()->select(["capacity" => $disks_metadata->func()->sum()])->where(["attachhostid" => $code]);
                }
                if ((int) $userLimti["cpu_used"] + (int) $extends["cpu"] > (int) $userLimti["cpu_bugedt"]) {
                    $message = array('code' => 1, 'msg' => 'CPU配额不足');
                } else if ((int) $userLimti["memory_used"] + (int) $extends["memory"] > (int) $userLimti["memory_buget"]) {
                    $message = array('code' => 1, 'msg' => '内存配额不足');
                } else if ((int) $userLimti["gpu_used"] + (int) $extends["gpu"] > (int) $userLimti["gpu_bugedt"]) {
                    $message = array('code' => 1, 'msg' => 'GPU配额不足');
                } else if ((int) $userLimti["disks_used"] + (int) $disks["count"] > (int) $userLimti["disks_bugedt"]) {
                    $message = array('code' => 1, 'msg' => '磁盘配额不足');
                }
                break;
        }
        // $message = array('code'=>1,'msg'=>'CPU配额不足');
        echo json_encode($message);exit();
        //{"cpu_bugedt":1000,"memory_buget":1000,"gpu_bugedt":81920,"router_bugedt":5,"subnet_bugedt":10,"disks_bugedt":10000,"0":"0"}
    }

    /**
     * [loadAvailableSubnetPublic 获取当前主机可以添加网卡的子网，去掉已经添加网卡的子网]
     * @param  [array] $data ['vpc'=>'?','basicid'=>?]
     * @return [array]       [可用结果集]
     */
    public function loadAvailableSubnetPublic($data){
        $is_public = 0;
        $instance_basic = TableRegistry::get('instance_basic');
        $field = [
            'I_SubnetCode'=>'network.subnet_code',
            'H_Vpc' =>'instance_basic.vpc',
            'I_SubnetId'=>'sub_b.id'
        ];
        $subnetList = $instance_basic->getHostBasicInfoByID($data['basicid'],$field);
        $subnet = [];
        foreach ($subnetList as $key => $sub) {
            $subnet[] = $sub['I_SubnetCode'];
        }
        // $subnet_extend_table = TableRegistry::get('SubnetExtend');
        // $subnetEntity = $subnet_extend_table->find()->select(['fusionType'])->where(['basic_id'=>$subnetList[0]['I_SubnetId']])->first();

        // $data['fusionType'] = $subnetEntity->fusionType;
        // $data['vpc'] = $subnetList[0]['H_Vpc'];
        // if($data['vpc'] != $subnetList[0]['H_Vpc']){//公共子网
        //     $is_public = 1;
        // }
        $vpcList = $this->loadSubnetPublic();

        foreach ($vpcList['vpc'] as $key => $vpc) {
            foreach ($vpc['subnet'] as $k => $v) {
                if(in_array($v['code'], $subnet)){
                    unset($vpcList['vpc'][$key]['subnet'][$k]);
                }
            }
        }
        return $vpcList;
    }

    public function loadSubnetPublic(){
        $connection = ConnectionManager::get('default');
        // $sql = ' (SELECT a.id,a.type,a.`name`,a.`code`,c.type as \'type1\',c.id as \'id1\',c.`code` as \'code1\',c.`name` as \'name1\'FROM cp_instance_basic as a  ';
        // $sql .= ' LEFT JOIN cp_vpc_extend as b ON a.id=b.basic_id ';
        // $sql .= ' LEFT JOIN cp_instance_basic as c ON c.vpc=a.`code` ';
        // $sql .= ' LEFT JOIN cp_subnet_extend as d ON d.basic_id=c.id ';
        // $sql .= ' where a.type=\'vpc\' AND c.type=\'subnet\' AND a.`code`=\'' . $data['vpc'] . '\' AND a.`status` = \'运行中\' ';
        // if(isset($data['fusionType'])){
        //     $sql .= 'AND d.fusionType = "'.$data['fusionType'].'"';
        // }
        // $sql .= 'AND d.isPublic='.$is_public.' ORDER BY a.id ASC )';
        // $sql .= ' UNION ';
        $sql = ' (SELECT a.id,a.type,a.`name`,a.`code`,c.type as \'type1\',c.id as \'id1\',c.`code` as \'code1\',c.`name` as \'name1\'FROM cp_instance_basic as a  ';
        $sql .= ' LEFT JOIN cp_vpc_extend as b ON a.id=b.basic_id ';
        $sql .= ' LEFT JOIN cp_instance_basic as c ON c.vpc=a.`code` ';
        $sql .= ' LEFT JOIN cp_subnet_extend as d ON d.basic_id=c.id ';
        $sql .= ' where a.type=\'vpc\' AND c.type=\'subnet\'';
        // if(isset($data['fusionType'])){
        //     $sql .= 'AND d.fusionType = "'.$data['fusionType'].'"';
        // }
        $sql .= 'AND d.isPublic=1 AND a.`status` = \'运行中\' ORDER BY a.id ASC)';
         // debug($sql);exit;
        $query                    = $connection->execute($sql)->fetchAll('assoc');
        //debug($query);
        //数据组装
        $p['vpc'] = array();
            if(count($query)!=0){
                for ($i=0; $i < count($query); $i++) {
                    for ($k=0; $k < count($query)-$i ; $k++) {
                         $p['vpc'][]=array('name'=>$query[$i]['name'],'code'=>$query[$i]['code']);
                    }
            }
            if(!empty($p['vpc'])){
                $p['vpc'] = $this->unique_arr($p['vpc'],true,true);

                foreach ($p['vpc'] as $key => $value) {
                    foreach ($query as $v => $b) {
                        if($value['code']==$b['code']){
                            $value['subnet'][]=array('name'=>$b['name1'],'code'=>$b['code1']);
                        }
                    }
                    $p['vpc'][$key]=$value;
                }
            }
        }
        //debug($p);
        return $p;
    }
    /*
     *二维数组去重
     */
    function unique_arr($array2D,$stkeep=false,$ndformat=true)
    {
        // 判断是否保留一级数组键 (一级数组键可以为非数字)
        if($stkeep) $stArr = array_keys($array2D);

        // 判断是否保留二级数组键 (所有二级数组键必须相同)
        if($ndformat) $ndArr = array_keys(end($array2D));

        //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
        foreach ($array2D as $v){
            $v = join(",",$v);
            $temp[] = $v;
        }

        //去掉重复的字符串,也就是重复的一维数组
        $temp = array_unique($temp);

        //再将拆开的数组重新组装
        foreach ($temp as $k => $v)
        {
            if($stkeep) $k = $stArr[$k];
            if($ndformat)
            {
                $tempArr = explode(",",$v);
                foreach($tempArr as $ndkey => $ndval) $output[$k][$ndArr[$ndkey]] = $ndval;
            }
            else $output[$k] = explode(",",$v);
        }

        return $output;
    }


    /**
    * 检查主机下的网卡是否被elb监听
    * @author wangjincheng
    * @param $id  Ecs的basic_id
    * 返回 状态，code, name
    */
    public function checkEcsAndElbIsRelate($id){
        $hosts_network_card_table = TableRegistry::get("HostsNetworkCard");
        $elb_netcard_table = TableRegistry::get("ElbNetcard");
        $instance_basic_table = TableRegistry::get("InstanceBasic");
        $hosts_network_card_data = $hosts_network_card_table->find()->where(['basic_id'=>$id])->toArray();
        $card_id_array = array();
        if(!empty($hosts_network_card_data)){
            foreach ($hosts_network_card_data as $key => $value) {
                $card_id_array[] = $value['id'];
            }
        }
        $elb_netcard_data = $elb_netcard_table->find()->where(['netcard_id in' => $card_id_array])->first();
        $is_relate['is_relate'] = 0;
        $is_relate['name'] = '';
        $is_relate['code'] = '';
        if(!empty($elb_netcard_data)){

            $instance_basic_data = $instance_basic_table->find()->where(['id' => $elb_netcard_data['elb_id']])->first();
            if(!empty($instance_basic_data)){
                //有关联关系
                $is_relate['is_relate'] = 1;
                $is_relate['name'] = $instance_basic_data["name"];
                $is_relate['code'] = $instance_basic_data["code"];
            }


        }
        return $is_relate;
    }
}
