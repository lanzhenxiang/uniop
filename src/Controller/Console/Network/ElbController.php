<?php
/**
* 文件描述文字.
*
*
* @author XingShanghe<xingshanghe@gmail.com>
* @date  2015年10月8日下午5:21:24
* @source HostsController.php
*
* @version 1.0.0
*
* @copyright  Copyright 2015 sobey.com
*/
namespace App\Controller\Console\Network;

use App\Controller\Console\ConsoleController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use App\Controller\OrdersController;
use Cake\Datasource\ConnectionManager;

class ElbController extends ConsoleController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }
    private $_serialize = array('code', 'msg', 'data');
    public $_pageList = array('total' => 0, 'rows' => array());
    /**
     * 获取列表数据,
     * 新增关联查询.
     */
    public function lists($request_data = array())
    {
        $limit = $request_data['limit'];
        $offset = $request_data['offset'];

        if (!empty($request_data['department_id'])) {
            $where['InstanceBasic.department_id'] = $request_data['department_id'];
        }
        if (isset($request_data['search'])) {
            if ($request_data['search']!="") {
                $where['OR'] = [
                    ["InstanceBasic.name like"=>'%'.$request_data['search'].'%'],
                    ["InstanceBasic.code like"=>'%'.$request_data['search'].'%'],
                ];
            }
        }

        if (!empty($request_data['class_code'])) {
            $where['InstanceBasic.location_code like'] = '%'.$request_data['class_code'].'%';
        }
        if (!empty($request_data['class_code2'])) {
            $where['InstanceBasic.location_code like'] = '%'.$request_data['class_code2'].'%';
        }

        $where['InstanceBasic.type'] = 'lbs';
        $where['InstanceBasic.isdelete'] = 0;
        $field = [
            'A_ID'=>'InstanceBasic.id','A_Code'=>'InstanceBasic.code','A_department'=>'InstanceBasic.department_id',
            'A_Name'=>'InstanceBasic.name','A_Status'=>'InstanceBasic.status','H_time'=>'InstanceBasic.create_time',

            'B_Name'=>'subnet.name','B_Code'=>'subnet.code',

            'E_DisplayName'=>'Agent.display_name',
            'C_imageCode' =>'elbExtend.imageCode','C_ip'=>'elbExtend.vip',
            'EIP'=>'eipExtend.ip',
            'Listen'=>'elbListen.elb_id',
            'I_Ip'=>'hostsNetworkCard.ip'
        ];

        $instanceBasic = TableRegistry::get('InstanceBasic');
        $query = $instanceBasic->initJoinQuery()
            ->joinSubnet()
            ->joinAgent()
            ->getJoinQuery()
            ->join(
            [
                'elbExtend'=>[
                    'table' =>'cp_elb_extend',
                    'type'  =>'LEFT',
                    'conditions'=>'InstanceBasic.id = elbExtend.basic_id'
                ],
                'eipExtend'=>[
                    'table' =>'cp_eip_extend',
                    'type'  =>'LEFT',
                    'conditions'=>'InstanceBasic.code = eipExtend.bindcode'
                ],
                'elbListen'=>[
                    'table' =>'cp_elb_listen',
                    'type'  =>'LEFT',
                    'conditions'=>'InstanceBasic.id = elbListen.elb_id'
                ],
                'hostsNetworkCard'=>[
                    'table' =>'cp_hosts_network_card',
                    'type'  =>'LEFT',
                    'conditions'=>'hostsNetworkCard.hosts_code = InstanceBasic.code'
                ]
            ]
        )->select($field)->where($where)->group('InstanceBasic.id')->order('InstanceBasic.create_time DESC')
            ->offset($offset)->limit($limit);

        $this->_pageList['total'] = $query->count();
        $this->_pageList['rows'] = $query;

        return $this->_pageList;
    }

    public function bindElbHostsList($request_data)
    {
        $limit = $request_data['limit'];
        $offset = $request_data['offset'];
        if (!empty($request_data['staute'])) {
            if ($request_data['staute'] != '0') {
                if ($request_data['staute'] == '2') {
                    $where['lbs.code <>'] = "";
                } else {
                    if ($request_data['staute'] == '1') {
                        $where['OR'] = [
                            ['lbs.code'=>''],
                            ['lbs.code is'=>null]
                        ];
                    }
                }
            }
        }

        if (isset($request_data['search'])) {
            if ($request_data['search']!="") {
                $where['InstanceBasic.name like'] = '%'.$request_data['search'].'%';
            }
        }

        if (!empty($request_data['class_code'])) {
            $where['InstanceBasic.location_code like'] = '%'.$request_data['class_code'].'%';
        }
        if (!empty($request_data['class_code2'])) {
            $where['InstanceBasic.location_code like'] = '%'.$request_data['class_code2'].'%';
        }

        $where['InstanceBasic.type'] = 'hosts';
        $where['InstanceBasic.status'] = '运行中';
        $where['InstanceBasic.isdelete'] = 0;

        $field = [
            'A_ID'=>'InstanceBasic.id','A_Code'=>'InstanceBasic.code','A_Name'=>'InstanceBasic.name',

            'B_ID'=>'lbs.id','B_Name'=>'lbs.name','B_Code'=>'lbs.code',

            'E_DisplayName'=>'Agent.display_name'
        ];

        $instanceBasic = TableRegistry::get('InstanceBasic');
        $query = $instanceBasic->initJoinQuery()
            ->joinHostExtend()
            ->joinAgent()
            ->getJoinQuery()
            ->join(
                [
                    'lbs'=>[
                        'table' =>'cp_instance_basic',
                        'type'  =>'LEFT',
                        'conditions'=>'hostExtend.lbsCode = lbs.code'
                    ]
                ]
            )->select($field)->where($where)->order('InstanceBasic.create_time DESC')
            ->offset($offset)->limit($limit);


        $this->_pageList['total'] = $query->count();
        $this->_pageList['rows'] = $query;

        return $this->_pageList;
    }
    public function bindEipElbList($request_data)
    {
        $instanceBasic = TableRegistry::get('InstanceBasic');
        $limit = $request_data['limit'];
        $offset = $request_data['offset'];

        if (!empty($request_data['staute'])) {
            if ($request_data['staute'] != '0') {
                if ($request_data['staute'] == '2') {
                    $where['eip.code <>'] = "";
                } else {
                    if ($request_data['staute'] == '1') {
                        $where['OR'] = [
                            ['eip.code'=>''],
                            ['eip.code is'=>null]
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

        $field = [
            'A_ID'=>'InstanceBasic.id','A_Code'=>'InstanceBasic.code','A_Name'=>'InstanceBasic.name','A_Status'=>'InstanceBasic.status',

            'C_ID'=>'eip.id','C_Name'=>'eip.name','C_Code'=>'eip.code',

            'E_DisplayName'=>'Agent.display_name'
        ];

        $where['InstanceBasic.type'] = 'lbs';
        $where['InstanceBasic.status'] = '运行中';
        $where['InstanceBasic.isdelete'] = 0;

        $query = $instanceBasic->initJoinQuery()
            ->joinAgent()
            ->getJoinQuery()
            ->join(
                [
                    'eipExtend'=>[
                        'table' =>'cp_eip_extend',
                        'type'  =>'LEFT',
                        'conditions'=>'InstanceBasic.code = eipExtend.bindcode'
                    ],
                    'eip'=>[
                        'table' =>'cp_instance_basic',
                        'type'  =>'LEFT',
                        'conditions'=>'eipExtend.basic_id = eip.id'
                    ],
                ]
            )->select($field)->where($where)->order('InstanceBasic.create_time DESC')
            ->offset($offset)->limit($limit);

        $this->_pageList['total'] = $query->count();
        $this->_pageList['rows'] = $query;

        return $this->_pageList;
    }
    public function addElb($request_data)
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
    public function ajaxElb($request_data = array())
    {
        $orders = new OrdersController();

        $result = array();
        $uid = (string) $this->request->session()->read('Auth.User.id');
        $request_data['method'] = $request_data['method'];
        if (isset($request_data['isEach']) && $request_data['isEach'] == 'true') {
            $table = $request_data['table'];
            foreach ($table as $key => $value) {
                if (!empty($value)) {
                    $interface = array('method' => $request_data['method'], 'uid' => $uid, 'basicId' => $value['A_ID'], 'loadBalancerCode' => $value['A_Code']);
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
            if ($request_data['method'] == 'lbs_del') {
                $request_data["uid"]=$uid;
                return $orders->ajaxFun($request_data);
            }
        }

        $request_data['uid'] = (string) $this->request->session()->read('Auth.User.id');
        $request_data['loadBalancerCode'] = $request_data['loadbalanceCode'];
        unset($request_data['loadbalanceCode']);
        //查询网卡
        $request_data['networkCardCode'] = $request_data['netCode'];

        return $orders->ajaxFun($request_data);
    }

    public function getHostsEntityByCode($code)
    {
        $basic_table = TableRegistry::get('InstanceBasic');
        $where = array('code' => $code);
        $entity = $basic_table->find('all')->where($where)->first();

        return $entity;
    }

    public function addElb_Listen($request_data)
    {
        $orders = new OrdersController();
        $param['uid'] = (string) $this->request->session()->read('Auth.User.id');
        $param['method'] = 'lbs_add_listener';
        $param['loadBalancerCode'] = $request_data['elbCode'];
        $param['lbMethod'] = $request_data['lbMethod'];
        $param['serviceType'] = $request_data['protocol'];
        $param['lbPort'] = $request_data['port'];
        $param['servicePort'] = $request_data['port'];
        $param['healthProtocol'] = $request_data['protocol'];
        $param['name'] = $request_data['name'];
        $param['elb_id'] = $request_data['elb_id'];
        $param['vpc_id'] = $request_data['vpc_id'];

        return $orders->ajaxFun($param);
    }

    public function list_listen($request_data)
    {
        $this->paginate['limit'] = $request_data['limit'];
        $this->paginate['page'] = $request_data['offset'] / $request_data['limit'] + 1;
        $elbLissten_table = TableRegistry::get('ElbListen');
        $where = array('elb_id' => $request_data['ELB']);
        $this->_pageList['total'] = $elbLissten_table->find('all')->where($where)->count();
        $this->_pageList['rows'] = $this->paginate($elbLissten_table->find('all')->where($where)->group('id')->order(array('id' => 'DESC')));

        return $this->_pageList;
    }
    public function list_ElbHost($request_data)
    {
        $limit = $request_data['limit'];
        $offset = $request_data['offset'];

        $elbNetcard = TableRegistry::get("ElbNetcard");
        $field = [
            'ElbNetcard.id','code'=>'hosts.code','name'=>'hosts.name','network_code'=>'hostsNetworkCard.network_code',

            'is_default'=>'hostsNetworkCard.is_default'
        ];

        $where['ElbNetcard.elb_id'] = $request_data['ELB'];

        $query = $elbNetcard->find()->hydrate(false)
            ->join(
                [
                    'hostsNetworkCard'=>[
                        'table' =>'cp_hosts_network_card',
                        'type'  =>'LEFT',
                        'conditions'=>'ElbNetcard.netcard_id = hostsNetworkCard.id'
                    ],
                    'hosts'=>[
                        'table' =>'cp_instance_basic',
                        'type'  =>'LEFT',
                        'conditions'=>'hostsNetworkCard.hosts_code = hosts.code'
                    ]
                ]
            )->select($field)->where($where)->order('ElbNetcard.id DESC')
            ->offset($offset)->limit($limit);

        $this->_pageList['total'] = $query->count();
        $this->_pageList['rows'] = $query->toArray();

        return $this->_pageList;
    }
    public function del_Listen($request_data)
    {
        $orders = new OrdersController();
        $id = $request_data['id'];
        $table = TableRegistry::get('ElbListen');
        $entity = $table->get($id);
        $param['uid'] = (string) $this->request->session()->read('Auth.User.id');
        $param['method'] = 'lbs_del_listener';
        $param['elbRuleCode'] = $entity->elbRuleCode;
        $param['basicId'] = (string) $entity->elb_id;
        $param['listenId'] = (string) $id;

        return $orders->ajaxFun($param);
    }
    public function edit_Listen($request_data)
    {
        $name = $request_data['name'];
        $id = $request_data['id'];
        $table = TableRegistry::get('ElbListen');
        $entity = $table->get($id);
        $entity->name = $name;
        $result = $table->save($entity);
        if ($result) {
            $message = array('code' => 0, 'msg' => '操作成功');
        }
        echo json_encode($message);
        die;
    }
    public function unuse_hosts($request_data)
    {
        $limit = $request_data['limit'];
        $offset = $request_data['offset'];

        $instanceBasic = TableRegistry::get('InstanceBasic');

        $field = [
            'InstanceBasic.id','InstanceBasic.code','InstanceBasic.name','InstanceBasic.subnet',
            'S_Name' =>'subnet.name'
        ];

        $where['InstanceBasic.type'] = 'hosts';
        $where['InstanceBasic.vpc']  = $request_data['vpc'];
        $where['InstanceBasic.status'] = '运行中';

        if(isset($request_data['subnet'])){
            $where['InstanceBasic.subnet'] = $request_data['subnet'];
        }

        $query = $instanceBasic->initJoinQuery()
            ->joinSubnet()
            ->getJoinQuery()
            ->select($field)->where($where)->order('InstanceBasic.create_time DESC')
            ->offset($offset)->limit($limit);

        $this->_pageList['total'] = $query->count();
        $this->_pageList['rows'] = $query;

        return $this->_pageList;
    }

    public function getNetCardlistByEcsCode($request_data)
    {
        $limit = $request_data['limit'];
        $offset = $request_data['offset'];

        if (!empty($request_data['code'])) {
            $where['InstanceBasic.code'] = $request_data['code'];
        }

        $field = [
            'id'=>'hostsNetworkCard.id','InstanceBasic.code','InstanceBasic.name',
            'network_code'=>'hostsNetworkCard.network_code',
            'ip'=>'hostsNetworkCard.ip','is_default'=>'hostsNetworkCard.is_default',
            'E_Code'=>'elb.code',
        ];

        $where['InstanceBasic.type'] = 'hosts';
        $where['OR'] = [
            ['InstanceBasic.status'=>'运行中'],
            ['InstanceBasic.status'=>'已停止']
        ];

        $instanceBasic = TableRegistry::get("InstanceBasic");
        $query = $instanceBasic->find()
            ->hydrate(false)
            ->join([
                'hostsNetworkCard'=>[
                    'table'=>'cp_hosts_network_card',
                    'type'=>'LEFT',
                    'conditions'=>'hostsNetworkCard.hosts_code = InstanceBasic.code'
                ],
                'elbNetcard'=>[
                    'table'=>'cp_elb_netcard',
                    'type'=>'LEFT',
                    'conditions'=>'elbNetcard.netcard_id = hostsNetworkCard.id'
                ],
                'elb'=>[
                    'table'=>'cp_instance_basic',
                    'type'=>'LEFT',
                    'conditions'=>'elb.id = elbNetcard.elb_id'
                ]
            ])
            ->select($field)->where($where)->order('InstanceBasic.id DESC')
            ->offset($offset)->limit($limit);

        $this->_pageList['total'] = $query->count();
        $this->_pageList['rows'] = $query;

        return $this->_pageList;
    }

    /**
     * @func: 判断添加的监听器是否重复
     * @param: null
     * @date: 2015年10月12日 下午2:45:17
     * @author: shrimp liao
     * @return: null
     */
    public function isRepeatListen($request_data)
    {
        $table = TableRegistry::get('ElbListen');
        $protocol = $request_data['protocol'];
        $port = $request_data['port'];
        $basic_id = $request_data['basicId'];
        $where = array('elb_id' => $basic_id, 'serviceType' => $protocol, 'servicePort' => $port);
        $entity = $table->find('all')->where($where)->first();

        return $entity;
    }
}
