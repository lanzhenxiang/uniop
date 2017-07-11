<?php
/**
* 设备  实例基类
*
*
* @author XingShanghe<xingshanghe@gmail.com>
* @date  2015年9月22日下午4:18:37
* @source InstanceBasicTable.php
* @version 1.0.0
* @copyright  Copyright 2015 sobey.com
*/
namespace App\Model\Table;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\ORM\Query;

class InstanceBasicTable extends SobeyTable{
    // public $_pageList = array(
    //     'total' => 0,
    //     'rows'  => array()
    // );

    protected  $query;
    /**
     * 初始化
     * (non-PHPdoc)
     * @see \Cake\ORM\Table::initialize()
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->hasOne('HostExtend',[
            'className' => 'HostExtend',
            'foreignKey' => 'basic_id',
            //'bindingKey'=>'',
        ]);
        $this->hasOne('ServiceList',[
            'className' => 'ServiceList',
            'foreignKey' => 'basic_id',
            //'bindingKey'=>'',
        ]);
        $this->hasOne('EipExtend',[
            'className' => 'EipExtend',
            'foreignKey' => 'basic_id',
        ]);
        $this->hasOne('VpcExtend',[
            'className' => 'VpcExtend',
            'foreignKey' => 'basic_id',
        ]);
        $this->hasOne('LbsExtend',[
            'className' => 'LbsExtend',
            'foreignKey' => 'basic_id',
        ]);
        $this->hasOne('NetworkParams',[
            'className' => 'NetworkParams',
            'foreignKey' => 'basic_id',
        ]);
        $this->hasOne('RouterExtend', [
            'className' => 'RouterExtend',
            'foreignKey' => 'basic_id',
        ]);
        //关联硬盘扩展表
        $this->hasMany('DisksMetadata', [
            'className' => 'DisksMetadata',
            'foreignKey' => 'hosts_id',
        ]);
        $this->hasOne('DisksMetaHosts', [
            'className' => 'DisksMetadata',
            'foreignKey' => 'disks_id',
        ]);
        //关联子网扩展表
        $this->hasOne('SubnetExtend', [
            'className' => 'SubnetExtend',
            'foreignKey' => 'basic_id',
        ]);
        $this->hasOne('InstanceRelation', [
            'className' => 'InstanceRelation',
            'foreignKey' => 'fromid',
        ]);
        $this->belongsTo('Agent', [
            'foreignKey' => 'location_code',
        ]);
        //关联云桌面扩展表
        $this->hasOne('DesktopExtend', [
            'className' => 'DesktopExtend',
            'foreignKey' => 'basic_id',
        ]);

        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
        ]);
        $this->belongsTo('Accounts', [
            'foreignKey' => 'create_by',
        ]);

        $this->hasMany('InstanceLogs',[
            'className' => 'InstanceLogs',
            'foreignKey'=>'user_id'
        ]);
        $this->hasMany('HostsNetworkCard',[
            'className' => 'HostsNetworkCard',
            'foreignKey'=> 'basic_id'
        ]);

        //关联硬盘扩展表
        $this->hasOne('DisksMetadatas', [
            'className' => 'DisksMetadata',
            'foreignKey' => 'disks_id',
        ]);

        //关联硬盘扩展表
        $this->hasOne('InstanceRecycle', [
            'className' => 'InstanceRecycle',
            'foreignKey' => 'basic_id',
        ]);
        //关联设备计费表
        $this->hasOne('InstanceCharge', [
            'className' => 'InstanceCharge',
            'foreignKey' => 'basic_id',
        ]);
        //关联桌面分组表
        $this->hasOne('SoftwaresDesktop',[
            'className'=>'SoftwaresDesktop',
            'foreignKey'=>'host_id'
        ]);
        //关联主机分组表
        $this->hasOne('HostsGroupToHosts',[
            'className'=>'HostsGroupToHosts',
            'foreignKey'=>'host_id'
        ]);
        //关联边界路由器扩展表
        $this->hasOne('VbrExtends',[
            'className'=>'VbrExtends',
            'foreignKey'=>'basic_id'
        ]);
        //关联边界路由器接口表
        $this->hasOne('VbrConnects',[
            'className'=>'VbrConnects',
            'foreignKey'=>'basic_id'
        ]);
        //关联Agent表
        $this->hasOne('Agent',[
            'className'=>'Agent',
            'foreignKey'=>false,
            'conditions'=>'Agent.class_code = InstanceBasic.location_code']
        );
        //关联边界路由器接口表
        $this->hasOne('vpcInfo',[
            'className'=>'InstanceBasic',
            'foreignKey'=>'vpc',
            'bindingKey'=>'code'
        ]);
	//关联安全组扩展表
        $this->hasOne('SecurityGroupExtends',[
            'className'=>'SecurityGroupExtends',
            'foreignKey'=>'basic_id'
        ]);
    }


    public function afterSave($entity, $options = []) {
        //if ($entity->isNew()){
            // Cache::clear(true,'memcache_code_name_maps');
            // Cache::clear(true,'memcache_name_code_maps');
        //}
    }
    /**
     * [getHostBasicInfoByID 根据id获取主机信息]
     * @author [lanzhenxiang] <[<lanzhenxiang@sobey.com>]>
     * @param  [int]    $id    [instance_basic主键id]
     * @param  [array]  $field [设置所需要查询出的字段]
     * @return [array]         [获取的结果集]
     */
    public function getHostBasicInfoByID($id,array $field = [],$type="hosts"){
        $basic_field = [
            'H_Code'        =>'instance_basic.code',
            'H_Name'        =>'instance_basic.name',
            'H_Description' =>'instance_basic.description',
            'D_Os_Form'     =>'host.os_family',
            'E_Ip'          =>'eip.ip',
            'E_BandWidth'   =>'eip.bandwidth',
            'H_Status'      =>'instance_basic.status',
            'H_VPC'         =>'instance_basic.vpc',
            'H_Subnet'      =>'sub_b.code',
            'H_ID'          =>'instance_basic.id',
            'Biz_tid'       =>'instance_basic.biz_tid'
        ];

        $joinTable = [
                'host'=>[
                        'table'=>'cp_host_extend',
                        'type'=>'LEFT',
                        'conditions'=>'host.basic_id = instance_basic.id'
                    ],
                'eip'=>[
                        'table'=>'cp_eip_extend',
                        'type'=>'LEFT',
                        'conditions'=>'eip.bindcode = instance_basic.code'
                    ],
                'eip_b'=>[
                        'table'=>'cp_instance_basic',
                        'type'=>'LEFT',
                        'conditions'=>'eip_b.id = eip.basic_id'
                    ],
                'agent'=>[
                        'table'=>'cp_agent',
                        'type'=>'LEFT',
                        'conditions'=>'agent.class_code = instance_basic.location_code'
                    ],
                'vpc_b'=>[
                        'table'=>'cp_instance_basic',
                        'type'=>'LEFT',
                        'conditions'=>'vpc_b.code = instance_basic.vpc'
                    ],
                'router_b'=>[
                        'table'=>'cp_instance_basic',
                        'type'=>'LEFT',
                        'conditions'=>'router_b.code = instance_basic.subnet'
                    ],
                'network'=>[
                        'table'=>'cp_hosts_network_card',
                        'type'=>'LEFT',
                        'conditions'=>'network.basic_id = instance_basic.id'
                    ],
                'sub_b'=>[
                        'table'=>'cp_instance_basic',
                        'type' => 'LEFT',
                        'conditions'=>'sub_b.code = network.subnet_code'
                    ]
                ];
        if($type == 'subnet'){
            unset($joinTable['agent']);
        }

        $query  = $this->find()
            ->hydrate(false)
            ->join($joinTable);
        ///
        //$query = $query->where(['instance_basic.type'=>$type]);
        $query = $query->where(['sub_b.code !='=>'']);
        if(null !== $field){
            $field = array_merge($basic_field,$field);
            $query = $query->select($field);
        }
        return $query->where(['instance_basic.id'=>$id])->order('network.is_default desc')->toArray();
    }

    /**
     * [getHostDiskById 获取主机的硬盘信息]
     * @param  [int]    $id [主机id]
     * @return [array]      [description]
     */
    public function getHostDiskById($id){
        $field = [
             'id'       =>'instance_basic.id',
             'name'     =>'instance_basic.name',
             'code'     =>'instance_basic.code',
             'capacity' =>'disks.capacity'
        ];

        $where = [
            'instance_basic.type'=>'disks',
            'disks.attachhostid'=>$id
        ];

        $query  = $this->find()
            ->hydrate(false)
            ->join([
                'host'=>[
                        'disks'=>'cp_disks_metadata',
                        'type'=>'LEFT',
                        'conditions'=>'disks.disks_id = instance_basic.id'
                    ]
                ]);
        return $query->select($field)->where($where)->toArray();
    }

    public function findFusionType(query $query,array $options){
        $where = array();
        if(isset($options['ecs_code']) && $options['ecs_code'] !=""){//主机code
            $where['InstanceBasic.code'] = $options['ecs_code'];
        }
        if(isset($options['basic_id']) && $options['basic_id'] !=""){//主机id
            $where['InstanceBasic.id'] = $options['basic_id'];
        }
        
        return $query->hydrate(false)->select(['fusionType'=>'sub_e.fusionType'])->join([
                'network'=>[
                    'table'=>'cp_hosts_network_card',
                    'type'=>'INNER',
                    'conditions'=>'network.basic_id = InstanceBasic.id'
                ],
                'sub_basic'=>[
                    'table' =>'cp_instance_basic',
                    'type'  =>'INNER',
                    'conditions'=>'sub_basic.code = network.subnet_code AND network.is_default = 1'
                ],
                'sub_e'=>[
                    'table' =>'cp_subnet_extend',
                    'type'  =>'INNER',
                    'conditions'=>'sub_e.basic_id = sub_basic.id'
                ]
            ])->where($where);
    }

    public function isSobeyEcs($ecs_code){
        $option['ecs_code'] = $ecs_code;
        $subnet = $this->find('FusionType',$option)->first();
        $fusion_type = $subnet['fusionType'];
        if($fusion_type == Configure::read("virtual_tech.vmware") || $fusion_type == Configure::read("virtual_tech.openstack")){//sobey支持的虚拟化技术
            return true;
        }else{
            return false;
        }
    }

    /**
     * [findSubnetExtend 获取子网扩展信息]
     * @param  query  $query
     * @param  array  $options
     * @return [query]
     */
    public function findSubnetExtend(query $query,array $options)
    {
        return $query->hydrate(false)->join([
                'sub_e'=>[
                    'table' =>'cp_subnet_extend',
                    'type'  =>'LEFT',
                    'conditions'=>'sub_e.basic_id = InstanceBasic.id'
                ]
            ]);
    }

    public function findPublicSubnetExtend(query $query , array $options)
    {
        return $query->find('SubnetExtend')
            ->join([
                'dept_subnet'=>[
                    'table' =>'cp_department_subnet',
                    'type'  =>'LEFT',
                    'conditions'=>'dept_subnet.subnet_id = InstanceBasic.id'
                ]]);
    }

    public function test($request_data){
        // $this->paginate['limit'] = $request_data['limit'];
        // $this->paginate['page']  = $request_data['offset'] / $request_data['limit'] + 1;

        $field = [
             'id' => 'instance_basic.id',
             'name' => 'instance_basic.name',
             'code' => 'instance_basic.code',
             'os' => 'host_extend.plat_form',
             'type' => 'fics.type',
             'param' => 'fics.parameter',
             'authority' => 'fics.authority',
             'network' => 'network.subnet_code'
             ];

        $where = array();
        $where  = [
            'instance_basic.type in' =>['desktop','hosts'] ,
            'isdelete' => '0',
            'code <>' =>'',
            'network.subnet_code' => Configure::read("Hawei9k_subnet_code")
        ];
        if (!empty($request_data['department_id'])) {
            $where['department_id'] = $request_data['department_id'];
        }
        if (!empty($request_data['type'])) {
            $where['instance_basic.type'] = $request_data['type'];
        }
        if (!empty($request_data['plat_form'])) {
            if($request_data['plat_form'] == "Windows"){
                $where["OR"] = [['host_extend.plat_form'=>'windows'],['host_extend.plat_form'=>'云主机']];
            } elseif ($request_data['plat_form'] == "linux"){
                $where["AND"] = [['host_extend.plat_form <>'=>'windows'],['host_extend.plat_form <>'=>'云主机']];
            }
            // $where['host_extend.plat_form'] = $request_data['plat_form'];
        }
        if (!empty($request_data['auth'])) {
            $where['authority'] = $request_data['auth'];
            // $where['host_extend.plat_form'] = $request_data['plat_form'];
        } elseif (isset($request_data['auth']) && $request_data['auth'] == '0') {
            $where['authority'] = $request_data['auth'];
        }
        if (isset($request_data['search'])) {
            if ($request_data['search'] != "") {
                $where['OR'] =[
                    ["instance_basic.name like"=>"%" . $request_data['search'] . "%"],
                    ["instance_basic.code like"=>"%" . $request_data['search'] . "%"]
                ];
            }
        }
        $query  = $this->find()
            ->hydrate(false)
            ->join([
                'host_extend'=>[
                        'table'=>'cp_host_extend',
                        'type'=>'LEFT',
                        'conditions'=>'host_extend.basic_id = instance_basic.id'
                    ],
                'fics' => [
                        'table'=>'cp_fics_relation_device',
                        'type'=>'LEFT',
                        'conditions'=>['fics.basic_id = instance_basic.id', 'vol_id'=>$request_data['vol_id']]
                    ],
                'network' => [
                    'table'=>'cp_hosts_network_card',
                    'type'=>'LEFT',
                    'conditions'=>['network.basic_id = instance_basic.id', 'network.ip <>' => '', 'network.network_code <>'=>'']
                    ]
                ])
            ->group(['instance_basic.id']);
        return $query->select($field)->where($where);
    }


    public function initJoinQuery()
    {
        $this->query = $this->find()->hydrate(false);
        return $this;
    }

    public function getJoinQuery(){
        return $this->query;
    }

    public function joinSubnet()
    {
        $this->query = $this->query->join([
            'subnet'=>[
                'table' => 'cp_instance_basic',
                'type'  => 'LEFT',
                'conditions' => 'InstanceBasic.subnet = subnet.code and InstanceBasic.subnet<> "" and InstanceBasic.subnet is not null'
            ]
        ]);
        return $this;
    }

    public function joinVpc()
    {
        $this->query = $this->query->join([
            'vpc'=>[
                'table' => 'cp_instance_basic',
                'type'  => 'LEFT',
                'conditions' => 'InstanceBasic.vpc = vpc.code and InstanceBasic.vpc<> "" and InstanceBasic.vpc is not null'
            ]
        ]);
        return $this;
    }

    public function joinRouter()
    {
        $this->query = $this->query->join([
            'router'=>[
                'table' => 'cp_instance_basic',
                'type'  => 'LEFT',
                'conditions' => 'InstanceBasic.router = router.code and InstanceBasic.router<> "" and InstanceBasic.router is not null'
            ]
        ]);
        return $this;
    }

    public function joinAgent()
    {
        $this->query = $this->query->join([
            'Agent'=>[
                'table' => 'cp_agent',
                'type'  => 'LEFT',
                'conditions' => 'InstanceBasic.location_code = Agent.class_code'
            ]
        ]);
        return $this;
    }

    public function joinAccounts()
    {
        $this->query = $this->query->join([
            'accounts'=>[
                'table' => 'cp_accounts',
                'type'  => 'LEFT',
                'conditions' => 'InstanceBasic.create_by = accounts.id'
            ]
        ]);
        return $this;
    }

    public function joinHostExtend()
    {
        $this->query = $this->query->join([
            'hostExtend'=>[
                'table' => 'cp_host_extend',
                'type'  => 'LEFT',
                'conditions' => 'InstanceBasic.id = hostExtend.basic_id'
            ]
        ]);
        return $this;
    }

    public function joinFicsExtend()
    {
        $this->query = $this->query->join([
            'ficsExtend'=>[
                'table' => 'cp_fics_extend',
                'type'  => 'LEFT',
                'conditions' => 'InstanceBasic.id = ficsExtend.basic_id'
            ]
        ]);
        return $this;
    }

    public function joinHostsNetworkCard()
    {
        $this->query = $this->query->join([
            'hostsNetworkCard'=>[
                'table' => 'cp_hosts_network_card',
                'type'  => 'LEFT',
                'conditions' => 'InstanceBasic.id = hostsNetworkCard.basic_id AND hostsNetworkCard.is_default = 1'
            ]
        ]);
        return $this;
    }

    public function joinDepartments()
    {
        $this->query = $this->query->join([
            'departments'=>[
                'table' => 'cp_departments',
                'type'  => 'LEFT',
                'conditions' => 'InstanceBasic.department_id = departments.id'
            ]
        ]);
        return $this;
    }



    public function getPublicSubnet()
    {
        $field = ['name' => 'InstanceBasic.name','code'=>'InstanceBasic.code','id'=>'InstanceBasic.id'];
        $query = $this->find()->hydrate(false)->join([
            'subnet_extend' => ['table' => 'cp_subnet_extend', 'type' => 'LEFT', 'conditions' => 'InstanceBasic.id = subnet_extend.basic_id']]);
        return $query->select($field)->where(array('subnet_extend.isPublic'=>1))->toArray();
    }

    /**
     * 根据firewallId获取firewall当前vpc下已绑定EIP的所有主机
     * @param  [type] $firewallId [description]
     * @return [type]             [description]
     */
    public function getEcsListByFirewallId($firewallId,$direction)
    {
        $query = $this->find();
        $query->hydrate(false)->join(
            [
                //主机主表信息
                "hostsBasic"=>[
                    "table" => 'cp_instance_basic',
                    "type"  => 'INNER',
                    "conditions" => 'hostsBasic.vpc = InstanceBasic.vpc AND hostsBasic.type = \'hosts\''
                ],
                
                "hostsNetworkCard"=>[
                    "table" => 'cp_hosts_network_card',
                    "type"  => 'INNER',
                    "conditions" => 'hostsBasic.id = hostsNetworkCard.basic_id AND hostsNetworkCard.network_code !="" AND hostsNetworkCard.is_default = 1'
                ]
            ]);
        // //关联EIP
        $joinTable = [ //EIP扩展表信息
                "eipExtend" =>[
                    "table" => 'cp_eip_extend',
                    "type"  => 'INNER',
                    "conditions" => 'eipExtend.bindcode = hostsBasic.code'
                ],
                //EIP主表信息
                "eipBasic"=>[
                    "table" => 'cp_instance_basic',
                    "type"  => 'INNER',
                    "conditions" => 'eipBasic.id = eipExtend.basic_id AND eipBasic.type = \'eip\''
                ]];
        $leftJoinTable =  [ //EIP扩展表信息
                "eipExtend" =>[
                    "table" => 'cp_eip_extend',
                    "type"  => 'LEFT',
                    "conditions" => 'eipExtend.bindcode = hostsBasic.code'
                ],
                //EIP主表信息
                "eipBasic"=>[
                    "table" => 'cp_instance_basic',
                    "type"  => 'LEFT',
                    "conditions" => 'eipBasic.id = eipExtend.basic_id AND eipBasic.type = \'eip\''
                ]];       
        //下行规则只能填写关联了EIP的主机
        if($direction == "Ingress"){
            $query = $query->join($joinTable);
        } else {
            $query = $query->join($leftJoinTable);
        }

        $conditions['InstanceBasic.id'] = $firewallId;
        $conditions['hostsBasic.isdelete'] = 0;
        return $query->where($conditions);
    }
}
