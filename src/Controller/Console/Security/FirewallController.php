<?php
/**
 * ==============================================
 * FrewallController.php
 * @author: shrimp liao
 * @date: 2015年11月3日 上午10:35:14
 * @version: v1.0.0
 * @desc: 安全控制器-子页面
 * ==============================================
 **/
namespace App\Controller\Console\Security;

use App\Controller\Console\ConsoleController;
use Cake\ORM\TableRegistry;
use App\Controller\OrdersController;
use Cake\Controller\Controller;
use App\Controller\Console\Security\FirewallPolicyController;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;

class FirewallController extends ConsoleController
{
    public function aaa()
    {
    }
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }
    private $_serialize = array('code', 'msg', 'data');
    public $_pageList = array('total' => 0, 'rows' => array());

    /**
     * @func: 获取数据信息
     * @param:
     * @date: 2015年11月3日 上午10:39:15
     * @author: shrimp liao
     * @return: null
     */
    public function lists($request_data = array())
    {
     
        $limit = $request_data['limit'];
        $offset = $request_data['offset'];

        $where = array();

        if (!empty($request_data['department_id'])) {
            $where['InstanceBasic.department_id'] = $request_data['department_id'];
        }
        $andwhere = [];
        if (isset($request_data['search'])) {
            if ($request_data['search'] != "") {
                $andwhere['OR'] = [
                    ['InstanceBasic.name like ' => '%' . $request_data['search'] . '%'],
                    ['InstanceBasic.code like ' => '%' . $request_data['search'] . '%'],
                    ['firewall_ecs_network_card.ip like ' => '%' . $request_data['search'] . '%'],
                    ['firewall_ecs_eip_extend.ip like ' => '%' . $request_data['search'] . '%'],
                ];
            }
        }

        if (!empty($request_data['class_code'])) {
            $where['InstanceBasic.location_code like '] = $request_data['class_code'] . '%';
        }
        if (!empty($request_data['vpc_code'])) {
            $where['InstanceBasic.vpc like '] = $request_data['vpc_code'] . '%';
        }
        if (!empty($request_data['class_code2'])) {
            $where['InstanceBasic.location_code like '] = $request_data['class_code2'] . '%';
        } elseif (!empty($request_data['class_code'])) {
            $where['InstanceBasic.location_code like '] = $request_data['class_code'] . '%';
        }

        $field = [
            'id'                => 'InstanceBasic.id',
            'code'              => 'InstanceBasic.code',
            'name'              => 'InstanceBasic.name',
            'department_id'     => 'InstanceBasic.department_id',
            'status'            => 'InstanceBasic.status',
            'vpc'               => 'InstanceBasic.vpc',
            'vpcName'           => 'vpc.name',
            'router'            => 'router.name',
            'create_time'       => 'InstanceBasic.create_time',
            'description'       => 'InstanceBasic.description',
            'location_name'     => 'InstanceBasic.location_name',

            'firewallecsName'   => 'firewall_ecs.name',
            'firewallecsCode'   => 'firewall_ecs.code',
            'firewallecsStatus' => 'firewall_ecs.status',
            // 'firewallecsIP'        => 'g.`ip`',
            'firewallecsEIP'    => 'firewall_ecs_eip_extend.ip',
            'firewallecsPFM'    => 'firewall_ecs_extend.plat_form',
            'H_ID'              => 'firewall_ecs.id',
            'fusionType'        => 'firewall_ecs_subnet_extend.fusionType',
            
            'I_Ip'              => 'firewall_ecs_network_card.ip',
        ];

        $where['InstanceBasic.type'] = 'firewall';
        $instanceBasic = TableRegistry::get("InstanceBasic");

        $query =$instanceBasic->initJoinQuery()
            ->joinRouter()
            ->joinAgent()
            ->joinVpc()
            ->getJoinQuery()
            ->select($field)->join(
            [
                // 防火墙主机以及主机管理的数据
                'firewall_ecs' => [
                    'table' => 'cp_instance_basic',
                    'type'  => 'LEFT',
                    'conditions' => 'firewall_ecs.vpc = InstanceBasic.vpc AND firewall_ecs.type = \'firewallecs\''
                ],
                // 防火墙ecs网卡表
                'firewall_ecs_network_card' => [
                    'table' => 'cp_hosts_network_card',
                    'type'  => 'LEFT',
                    'conditions' => 'firewall_ecs_network_card.basic_id = firewall_ecs.id AND firewall_ecs_network_card.is_default = 1'
                ],
                // 防火墙ecs_subnet表
                'firewall_ecs_subnet' => [
                    'table' => 'cp_instance_basic',
                    'type'  => 'LEFT',
                    'conditions' => 'firewall_ecs.subnet = firewall_ecs_subnet.code'
                ],  
                // subnet扩展
                'firewall_ecs_subnet_extend'=>[
                    'table' => 'cp_subnet_extend',
                    'type'  => 'LEFT',
                    'conditions' => 'firewall_ecs_subnet.id = firewall_ecs_subnet_extend.basic_id'
                ],
                // 防火墙主机扩展
                'firewall_ecs_extend' => [
                    'table' => 'cp_host_extend',
                    'type'  => 'LEFT',
                    'conditions' => 'firewall_ecs.id = firewall_ecs_extend.basic_id'
                ], 
                // 防火墙eip扩展
                'firewall_ecs_eip_extend'=>[
                    'table' => 'cp_eip_extend',
                    'type'  => 'LEFT',
                    'conditions' => 'firewall_ecs_eip_extend.bindcode = firewall_ecs.code'
                ], 
            ]
        )->where($where)->andWhere($andwhere)->group('InstanceBasic.id')->order('InstanceBasic.create_time DESC')->offset($offset)->limit($limit);

        $this->_pageList['total'] = $query->count();
        $this->_pageList['rows'] = $query->toArray();

        $connection = ConnectionManager::get('default');
        // $sql = ' SELECT ';
        // $sql .= ' a.id,a.`name`,a.`code`,a.`department_id`,a.`status`,a.`vpc`,f.name as \'vpcName\',h.`name` as \'router\',a.create_time,a.description,a.location_name ,g.`name` as \'firewallecsName\',g.`code` as \'firewallecsCode\',g.status as \'firewallecsStatus\',g.ip as \'firewallecsIP\',g.eip as \'firewallecsEIP\',g.pfm as \'firewallecsPFM\',g.H_ID,g.fusionType';
        // $sql .= ' ,(SELECT GROUP_CONCAT(i.ip) ';
        // $sql .= ' FROM cp_hosts_network_card i ';
        // $sql .= ' WHERE i.hosts_code = g.`code` ';
        // $sql .= ' ) as \'I_Ip\' ';
        // $sql .= ' FROM ';
        // $sql .= ' LEFT JOIN (SELECT v.`code`,v.`status`,b.ip,v.`name`,v.vpc,p.ip as \'eip\',b.plat_form as \'pfm\',v.id as \'H_ID\', se.fusionType FROM cp_instance_basic AS v ';
        // $sql .= ' LEFT JOIN cp_hosts_network_card nc ON nc.basic_id = v.id AND nc.is_default = 1 ';
        // $sql .= ' LEFT JOIN cp_instance_basic si ON si.CODE = nc.subnet_code ';
        // $sql .= ' LEFT JOIN cp_host_extend AS b ON b.basic_id = v.id LEFT JOIN cp_eip_extend as p ON p.bindcode=v.`code` WHERE v.type = \'firewallecs\') as g ON g.vpc=a.vpc   ';
        // $sql .= ' WHERE a.type=\'firewall\' ' . $where;
        // $sql .= ' group by a.id';
        // $sql .= ' ORDER BY a.create_time desc ';
        // $sql_row = $sql . ' limit ' . $offset . ',' . $limit;
        // $query = $connection->execute($sql_row)->fetchAll('assoc');
        // $this->_pageList['total'] = $connection->execute($sql)->count();
        // $this->_pageList['rows'] = $query;
//        $this->_pageList['rows'][0]['delete_info']='0';


        // 添加 是否能删除的信息
        foreach($this->_pageList['rows'] as  $key => &$value ){

            $value['delete_info']='0';
            $vpc=$value['vpc'];
            if ($value['status'] != '创建失败') {
            // vpc下面ecs是否绑定eip
                $res = $connection->execute("select id from cp_instance_basic where type='eip' and vpc='$vpc'")->fetchAll('assoc');
                $count = 0;

                if ($res != null) {
                    foreach ($res as $keys => $values) {
                        $code = $values['id'];
                        $exist = $connection->execute("select bindcode from cp_eip_extend where basic_id='$code'")->fetchAll('assoc');
                        
                        if (!empty($exist)) {
                            $exist = $exist[0]["bindcode"];
                            if (!empty($exist)) {
                                $value['delete_info'] = '1';
                            }
                        }
                    }
                }

                // vpc下面有无vpx,ddc,ad,wi
                $res = $connection->execute("select type from cp_instance_basic where vpc='$vpc'")->fetchAll('assoc');
                foreach ($res as $key1 => $value1) {
                    switch ($value1['type']) {
                        case 'vpx':
                            $value['delete_info'] = '2';
                            break;
                        case 'ddc':
                            $value['delete_info'] = '3';
                            break;
                        case 'ad':
                            $value['delete_info'] = '4';
                            break;
                        case 'wi':
                            $value['delete_info'] = '5';
                            break;
                    }
                }
            }
        }
        return $this->_pageList;
    }
    /**
     * 防火墙ajax操作接口
     * @fun    name
     * @date   2015-11-11T10:32:51+0800
     * @author shrimp liao
     * @param  [type]                   $request_data [description]
     * @return [type]                                 [description]
     */
    public function ajaxFun($request_data)
    {
        $order = new OrdersController();
        $url = Configure::read('URL');
        $instance_relation = TableRegistry::get('InstanceRelation');
        $instance_basic = TableRegistry::get('InstanceBasic');
        $request_data['uid'] = (string) $this->request->session()->read('Auth.User.id');
        //uid
        if ($request_data['method'] == 'firewall_del') {
                $re_code = $order->postInterface($url, $request_data);
                // 调用接口
                if ($re_code['Code'] == 0) {
                    $code = '0000';
                } else {
                    $code = '0002';
                    $msg = $re_code['Message'];
                }
                return compact(array_values($this->_serialize));
        }
    }
    /**
     * @func: 获取basicId
     * @param :$fromtype:查询类型 $param:$fromid:basic_id
     * $param:$totype:获取类型
     * @date: 2015年11月3日 下午4:16:50
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

    public function getFirewallByVpc($data){
        $instance = TableRegistry::get('InstanceBasic');
        $vpcCount = $instance->find()->select(['id'])->where(['vpc'=>$data['vpc'],'type'=>'firewall','status'=>'运行中'])->count();
        if ($vpcCount>=1){
            return 1;
        }else{
            return 0;
        }
    }
    
    public function delFirewallAll($ids) 
    {
        $order = new OrdersController();
        $url = Configure::read('URL');
        $instance_basic = TableRegistry::get('InstanceBasic');
        $request_data['uid'] = (string) $this->request->session()->read('Auth.User.id');
        $id = split(',', $ids['ids']);
        $info = $instance_basic->find()->where(['id in' => $id])->toArray();
        $request_data['method'] = 'firewall_del';
        $request_data['uid'] = (string) $this->request->session()->read('Auth.User.id');
        if (!empty($info)) {
            foreach ($info as $i) {
                $request_data['basicId'] = (string)$i['id'];
                $request_data['firewallCode'] = $i['code'];
                $re_code = $order->postInterface($url, $request_data);
            }
        }
        $code = '0000';
        return compact(array_values($this->_serialize));
    }


    public function getEcsListInFirewallVpc($request_data)
    {
        $firewallId = $request_data['firewall_id'];
        $direction  = $request_data['direction'];
        $instance_basic = TableRegistry::get('InstanceBasic');
        $query = $instance_basic->getEcsListByFirewallId($firewallId,$direction);
        $field = [
            "id"    =>"hostsBasic.id",
            "code"  =>"hostsBasic.code",
            "name"  =>"hostsBasic.name",
            "ip"    =>"hostsNetworkCard.ip",
            "eip"   =>"eipExtend.ip"
        ];

        $offset = $request_data['offset'];
        $limit  = $request_data['limit'];

        $rows   = $query->select($field)->offset($offset)->limit($limit)->toArray();
        $total  = $query->count();

        return compact(['rows','total']);
    }
}