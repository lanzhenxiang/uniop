<?php
namespace App\Controller\Console\Security;

use App\Controller\Console\ConsoleController;
use App\Controller\OrdersController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class FirewallPolicyController extends ConsoleController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }
    private $_serialize = array('code', 'msg', 'data');
    public $_pageList   = array('total' => 0, 'rows' => array());
    /**
     * @func: 获取数据信息
     * @param:
     * @date: 2015年11月3日 上午10:39:15
     * @author: shrimp liao
     * @return: null
     */
    public function lists($request_data = array())
    {
        $d = $request_data['d'];
        if ($d == '1') {
            $d = 'Ingress';
        } else {
            if ($d == '2') {
                $d = 'Egress';
            }
        }
        $this->paginate['limit'] = $request_data['limit'];
        $this->paginate['page']  = $request_data['offset'] / $request_data['limit'] + 1;
        if(isset($request_data['search'])){
            if ($request_data['search']!="") {
                $where['rule_name like'] = '%' . $request_data['search'] . '%';
            }
        }

        if (!empty($request_data['templateId'])) {
            //
            $table_templatedetail     = TableRegistry::get('FirewallTemplateDetail');
            $where['template_id']     = $request_data['templateId'];
            $where['direction']       = $d;
            $this->_pageList['total'] = $table_templatedetail->find('all')->where($where)->count();
            $this->_pageList['rows']  = $this->paginate($table_templatedetail->find('all')->where($where));
            return $this->_pageList;
        } else {
            $filewail_id          = $request_data['id'];
            $table_policy         = TableRegistry::get('FirewallPolicy');
            $where['firewall_id'] = $filewail_id;
            $where['direction']   = $d;
            // debug($table_policy->find('all')->where($where));
            $this->_pageList['total'] = $table_policy->find('all')->where($where)->count();
            $this->_pageList['rows']  = $this->paginate($table_policy->find('all')->where($where));
            return $this->_pageList;
        }
    }
    public function addTemplate_policy($request_data)
    {
        $direction            = $request_data['direction'];
        $table_templatedetail = TableRegistry::get('FirewallTemplateDetail');
        $result               = array('Code' => '0', 'Message' => '');
        if (!empty($request_data['stopPort'])) {
            $portRange = $request_data['startPort'] . '-' . $request_data['stopPort'];
        } else {
            $portRange = $request_data['startPort'];
        }
        if ($direction == 'Bothway') {
            //如果是双向
            $request_data['direction'] = 'Ingress';
            $entity                    = $table_templatedetail->newEntity();
            $request_data['portRange'] = $portRange;
            $entity                    = $table_templatedetail->patchEntity($entity, $request_data);
            if ($table_templatedetail->save($entity)) {
                $request_data['direction'] = 'Egress';
                $entity                    = $table_templatedetail->newEntity();
                $entity                    = $table_templatedetail->patchEntity($entity, $request_data);
                unset($entity['startPort']);
                unset($entity['stopPort']);
                if ($table_templatedetail->save($entity)) {
                    return json_encode($result);
                } else {
                    $result['Code'] = '1';
                    -($result['Message'] = $table_templatedetail->save($entity));
                    return json_encode($result);
                }
            } else {
                $result['Code']    = '1';
                $result['Message'] = $table_templatedetail->save($entity);
                return json_encode($result);
            }
        } else {
            $entity                    = $table_templatedetail->newEntity();
            $request_data['portRange'] = $portRange;
            $entity                    = $table_templatedetail->patchEntity($entity, $request_data);
            unset($entity['startPort']);
            unset($entity['stopPort']);
            if ($table_templatedetail->save($entity)) {
                return json_encode($result);
            } else {
                $result['Code']    = '1';
                $result['Message'] = $table_templatedetail->save($entity);
                return json_encode($result);
            }
        }
    }
    public function editTemplate_policy($request_data)
    {
        $direction            = $request_data['direction'];
        $table_templatedetail = TableRegistry::get('FirewallTemplateDetail');
        $result               = array('Code' => '0', 'Message' => '');
        if (!empty($request_data['stopPort'])) {
            $portRange = $request_data['startPort'] . '-' . $request_data['stopPort'];
        } else {
            $portRange = $request_data['startPort'];
        }
        $entity            = $table_templatedetail->get($request_data['txt_id']);
        $entity->rule_name = $request_data['rule_name'];
        $entity->portRange = $portRange;
        $entity->protocol  = $request_data['protocol'];
        $entity->source_ip = $request_data['source_ip'];
        $entity->target_ip = $request_data['target_ip'];
        if ($table_templatedetail->save($entity)) {
            return json_encode($result);
        } else {
            $result['Code']    = '1';
            $result['Message'] = $table_templatedetail->save($entity);
            return json_encode($result);
        }
    }
    /**
     * 添加防火墙规则
     * @fun    name
     * @date   2015-11-17T14:22:30+0800
     * @author shrimp liao
     * @param  [type]                   $request_data [description]
     */
    public function addFilewall_policy($request_data)
    {
        $orders       = new OrdersController();
        $table_policy = TableRegistry::get('InstanceBasic');
        $firewall     = $table_policy->get($request_data['id']);
        $direction = $request_data['direction'];

        if($direction == "Ingress" && !$this->_isBundlingEip($request_data)){//判断目标IP是否绑定Eip
            return json_encode(['Code'=>'1','Message'=>'指定的内网主机没有绑定EIP,不能创建防火墙规则']);
        }
        //格式化端口
        $portRange = $this->_portRangeFormat($request_data);
        
        if ($direction == 'Bothway') {
            //如果是双向
            // debug($request_data);die();
            $interface = array(
                'basicId'          => $request_data["id"],
                'method'           => $request_data['method'],
                'uid'              => (string) $this->request->session()->read('Auth.User.id'),
                'firewallRuleName' => $request_data['rule_name'],
                'firewallCode'     => $firewall->code,
                'vpcCode'          => $firewall->vpc,
                'sourceCidr'       => $request_data['source_ip'],
                'destCidr'         => $request_data['target_ip'],
                'protocol'         => $request_data['protocol'],
                'portRange'        => $portRange, 'direction' =>
                'Ingress',
                'action'           => $request_data['action'],
                'sourceAddressNat' => $request_data['sourceAddressNat'],
                'poolIP'           => $request_data['poolIP']
            );
            if ($request_data['method'] == 'firewall_update_policy') {
                $interface['firewallRuleCode'] = $request_data['firewallRuleCode'];
            }
            $result = $orders->ajaxFun($interface);
            if ($result['Code'] != '0') {
                return json_encode($result);
                die;
            }
            $interface['direction'] = 'Egress';
            $result                 = $orders->ajaxFun($interface);
            if ($result['Code'] != '0') {
                return json_encode($result);
                die;
            } else {
                return json_encode($result);
                die;
            }
        } else {
            $interface = array(
                'basicId'          => $request_data["id"],
                'method'           => $request_data['method'],
                'uid'              => (string) $this->request->session()->read('Auth.User.id'),
                'firewallRuleName' => $request_data['rule_name'],
                'firewallCode'     => $firewall->code,
                'vpcCode'          => $firewall->vpc,
                'sourceCidr'       => $request_data['source_ip'],
                'destCidr'         => $request_data['target_ip'],
                'protocol'         => $request_data['protocol'],
                'portRange'        => $portRange,
                'direction'        => $request_data['direction'],
                'action'           => $request_data['action'],
                'sourceAddressNat' => $request_data['sourceAddressNat'],
                'poolIP'           => $request_data['poolIP']
            );
            if ($request_data['method'] == 'firewall_update_policy') {
                $interface['firewallRuleCode'] = $request_data['firewallRuleCode'];
            }
            $result = $orders->ajaxFun($interface);
            return json_encode($result);
            die;
        }
    }
    /**
     * [_portRangeFormat 格式化防火墙规则端口范围值，厂商采用分隔符不一致(aliyun=>'/','sobey'=>'-','aws'=>'-')]
     * @param  [array]      $request_data 
     * @return [string]     exp: '80/100' | '80-100'
     */
    protected function _portRangeFormat($request_data){
        //端口号不能大于65535
        $request_data['stopPort']  = $request_data['stopPort'] > 65535 ? 65535 : $request_data['stopPort'];
        $request_data['startPort'] = $request_data['startPort'] > 65535 ? 65535 : $request_data['startPort'];

        if (!empty($request_data['stopPort'])) {
                       
            $InstanceBasic = TableRegistry::get('InstanceBasic');
            $agent         = TableRegistry::get('Agent');
            $vpc = $InstanceBasic->find()->hydrate(false)->select(["location_code"=>'vpc.location_code'])->where(['InstanceBasic.id'=>$request_data['id']])->join(
                [
                    'vpc'=>[
                        'table' =>'cp_instance_basic',
                        'type'  =>'LEFT',
                        'conditions' =>'vpc.code = InstanceBasic.vpc'
                    ]
                ])->first();
            $agent_code  = $agent->getAgentRootCode($vpc['location_code']);
            $delimiter = $agent_code == "aliyun" ? '/' :'-';
            $portRange = $request_data['startPort'] . $delimiter . $request_data['stopPort'];
        } else {
            $portRange = $request_data['startPort'];
        }
        return $portRange;
    }

    /**
     * [_isBundlingEip 判断添加的防火墙下行规则，目标主机是否绑定EIP]
     * @param  [type]  $request_data ['target_ip'=>string,'id'=>'']
     * @return boolean               [true 绑定  false 未绑定]
     */
    protected function _isBundlingEip($request_data){
        $ip         = $request_data['target_ip'];
        $basic_id   = $request_data['id'];
        $firewall   = TableRegistry::get('InstanceBasic');
        $where      = ['nc.ip'=>$ip,'InstanceBasic.id'=>$basic_id];
        $field      = ['eip_b.code'];
        //EIP绑定普通网卡情况
        $table      = [
                'hosts'=>[
                        'table'=>'cp_instance_basic',
                        'type'=>'LEFT',
                        'conditions'=>'hosts.vpc = InstanceBasic.vpc'
                ],
                'nc'=>[
                        'table'=>'cp_hosts_network_card',
                        'type'=>'LEFT',
                        'conditions'=>'hosts.code = nc.hosts_code'
                ],
                'eip'=>[
                        'table'=>'cp_eip_extend',
                        'type'=>'INNER',
                        'conditions'=>'eip.bindcode = hosts.code'
                ],
                'eip_b'=>[
                        'table'=>'cp_instance_basic',
                        'type'=>'INNER',
                        'conditions'=>'eip_b.id = eip.basic_id'
                ]    
        ];
        $eip = $firewall->find()->hydrate(false)->select($field)->join($table)->where($where)->first();
       //EIP绑定负载均衡的情况
        $table2      = [
                'vpx'=>[
                        'table'=>'cp_instance_basic',
                        'type'=>'LEFT',
                        'conditions'=>'vpx.vpc = InstanceBasic.vpc AND vpx.type="lbs"'
                ],
                'elb_e'=>[
                        'table'=>'cp_elb_extend',
                        'type'=>'LEFT',
                        'conditions'=>'elb_e.basic_id = vpx.id'
                ],
                'eip'=>[
                        'table'=>'cp_eip_extend',
                        'type'=>'INNER',
                        'conditions'=>'eip.bindcode = vpx.code'
                ],
                'eip_b'=>[
                        'table'=>'cp_instance_basic',
                        'type'=>'INNER',
                        'conditions'=>'eip_b.id = eip.basic_id'
                ]    
        ];
        $eip2 = $firewall->find()->hydrate(false)->select($field)->join($table2)->where(['elb_e.vip'=>$ip,'InstanceBasic.id'=>$basic_id])->first();
        return ($eip == null && $eip2 == null ) ? false : true;
    }

    /**
     * 删除防火墙规则
     * @fun    name
     * @date   2015-11-17T14:22:44+0800
     * @author shrimp liao
     * @param  [type]                   $request_data [description]
     * @return [type]                                 [description]
     */
    public function delFirewall_policy($request_data)
    {
        $result  = array('Code' => '0', 'Message' => '');
        $isEach  = $request_data['isEach'];
        $basicId = $request_data["basic_id"];
        unset($request_data['isEach']);
        if ($isEach == 'true') {
            //多个
            if ($request_data['type'] == 'firewall') {
                $orders = new OrdersController();
                if (!empty($request_data['table_1']) && !empty($request_data['table_2'])) {
                    $table = array_merge($request_data['table_1'], $request_data['table_2']);
                } else {
                    if (!empty($request_data['table_1']) && empty($request_data['table_2'])) {
                        $table = $request_data['table_1'];
                    } else {
                        if (empty($request_data['table_1']) && !empty($request_data['table_2'])) {
                            $table = $request_data['table_2'];
                        }
                    }
                }
                foreach ($table as $key => $value) {
                    $interface = array('method' => 'firewall_del_policy', 'firewallRuleCode' => $value['polic_code'], 'uid' => (string) $this->request->session()->read('Auth.User.id'), 'basicId' => $basicId, 'id' => $value["id"]);
                    $result    = $orders->ajaxFun($interface);
                    if ($result['Code'] != '0') {
                        return json_encode($result);
                        die;
                    }
                }
                return json_encode($result);
                die;
            } elseif ($request_data['type'] == 'firewall_template') {
                $table_templatedetail = TableRegistry::get('FirewallTemplateDetail');
                if (!empty($request_data['table_1']) && !empty($request_data['table_2'])) {
                    $table = array_merge($request_data['table_1'], $request_data['table_2']);
                } else {
                    if (!empty($request_data['table_1']) && empty($request_data['table_2'])) {
                        $table = $request_data['table_1'];
                    } else {
                        if (empty($request_data['table_1']) && !empty($request_data['table_2'])) {
                            $table = $request_data['table_2'];
                        }
                    }
                }
                foreach ($table as $key => $value) {
                    $entity = $table_templatedetail->get($value['id']);
                    $entity = $table_templatedetail->delete($entity);
                    if (!$entity) {
                        $resul['Code']    = '1';
                        $resul['Message'] = $entity;
                        return json_encode($result);
                        die;
                    }
                }
                return json_encode($result);
                die;
            }
        } elseif ($isEach == 'false') {
            //单个
            if ($request_data['type'] == 'firewall') {
                //删除接口
                $orders                 = new OrdersController();
                $request_data['method'] = 'firewall_del_policy';
                $request_data['uid']    = (string) $this->request->session()->read('Auth.User.id');
                //uid
                $request_data['basicId'] = $request_data['basic_id'];
                $result                  = $orders->ajaxFun($request_data);
                return json_encode($result);
                die;
            } else {
                if ($request_data['type'] == 'firewall_template') {
                    //删除数据库
                    $table_templatedetail = TableRegistry::get('FirewallTemplateDetail');
                    $entity               = $table_templatedetail->get($request_data['id']);
                    $entity               = $table_templatedetail->delete($entity);
                    if ($entity) {
                        return json_encode($result);
                        die;
                    } else {
                        $result['Code']    = '1';
                        $result['Message'] = $entity;
                    }
                    return json_encode($result);
                    die;
                }
            }
        }
    }
    /**
     * 编辑数据列表
     */
    public function edit($request_data = array())
    {
        $code = '0001';
        $data = array();
        //编辑操作
        $host   = TableRegistry::get('FirewallPolicy');
        $result = $host->updateAll($request_data, array('id' => $request_data['id']));
        if ($result) {
            $code = '0000';
            $data = $host->get($request_data['id'])->toArray();
        }
        $msg = Configure::read('MSG.' . $code);
        return compact(array_values($this->_serialize));
    }

    public function isRepeatPolicyI($request_data = array())
    {
        $table = TableRegistry::get('FirewallPolicy');
        $query = $table->find()->where
        (
            array(
                'firewall_id' => $request_data["firewall_id"],
                'protocol'    => $request_data["protocol"],
                'source_ip'   => $request_data["source_ip"],
                'target_ip'   => $request_data["target_ip"],
                'portRange'   => $request_data["portRange"],
                'direction'   => $request_data["direction"])
        )->first();
        if (empty($query)) {
            return false;
        } else {
            //如果有但!=前一个规则
            $row = $table->get($request_data["id"]);
            if ($query != $row) {
                return true;
            }
            return false;
        }
    }
    public function isRepeatPolicyT($request_data = array())
    {
        $table = TableRegistry::get('FirewallTemplateDetail');
        $query = $table->find()->where
        (
            array(
                'template_id' => $request_data["template_id"],
                'protocol'    => $request_data["protocol"],
                'source_ip'   => $request_data["source_ip"],
                'target_ip'   => $request_data["target_ip"],
                'portRange'   => $request_data["portRange"],
                'direction'   => $request_data["direction"])
        )->first();
        if (empty($query)) {
            return false;
        } else {
            //如果有但!=前一个规则
            if (!empty($request_data["id"])) {
                $row = $table->get($request_data["id"]);
                if ($query != $row) {
                    return true;
                }
                return false;
            }
            return true;
        }
    }
}
