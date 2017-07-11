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
use Cake\Core\Configure;
class VpcController extends ConsoleController
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
        $this->paginate['limit'] = $request_data['limit'];
        $this->paginate['page'] = $request_data['offset'] / $request_data['limit'] + 1;
        $instance_basic = TableRegistry::get('InstanceBasic');
        $where = array('InstanceBasic.type' => 'vpc');

        if(!empty($request_data['department_id'])){
           $where['department_id'] = $request_data['department_id'];
        }

        if(isset($request_data['search'])){
            if ($request_data['search']!="") {
                $where['OR'] = [
                    ['InstanceBasic.name like'=>'%' . $request_data['search'] . '%'],
                    ['InstanceBasic.code like'=>'%' . $request_data['search'] . '%']
                    ];
            }
        }

        if (!empty($request_data['class_code'])) {
            $where['InstanceBasic.location_code like'] = $request_data['class_code'] . '%';
        }
        if (!empty($request_data['class_code2'])) {
            $where['InstanceBasic.location_code like'] = $request_data['class_code2'] . '%';
        } elseif (!empty($request_data['class_code'])) {
            $where['InstanceBasic.location_code like'] = $request_data['class_code'] . '%';
        }
        $this->_pageList['total'] = $instance_basic->find('all')->contain(array('VPCExtend', 'Agent'))->where($where)->count();
        $this->_pageList['rows'] = $this->paginate($instance_basic->find('all')->contain(array('VPCExtend', 'Agent'))->where($where)->group('InstanceBasic.id')->order(array('InstanceBasic.create_time' => 'DESC')));
        return $this->_pageList;
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
    /**
     * 编辑 计算机与网络_VPC
     */
    public function editNetworkVpc($value)
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
     * 删除 计算机与网络_VPC
     */
    public function deleteNetworkVpc($value)
    {

        $code = '0001';
        $data = array();
        $ids = $value;
        $result = 0;
        $_request_id = explode(',', $ids['ids']);
        $_request_code = explode(',', $ids['codes']);
        // 逻辑处理
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        $parameter['method'] = 'router_del';
        $uid = $this->request->session()->read('Auth.User.id') ? (string) $this->request->session()->read('Auth.User.id') : (string) 0;
        $parameter['uid'] = $uid;
        $url = Configure::read('URL');
        $order = new OrdersController();
        foreach ($_request_id as $key => $value) {
            $subnet = $this->findbasicId('vpc', $value, 'subnet');
            if (!empty($subnet)) {
                $code = '0002';
                $msg = 'VPC下还有子网,不能删除VPC！';
                return compact(array_values($this->_serialize));
            }
            $router_id = $this->findbasicId('vpc', $value, 'router');
            //查询路由器
            if (!empty($router_id)) {
                //vpc绑定了路由器
                $router_code = $instance_basic_table->find()->where(array('id' => $router_id))->toArray();
                $parameter['basicId'] = (string) $router_code[0]['id'];
                $parameter['routerCode'] = (string) $router_code[0]['code'];
            } else {
                $firewall_id = $this->findbasicId('vpc', $value, 'firewall');
                if (!empty($firewall_id)) {
                    //vpc绑定了防火墙
                    $parameter['method'] = 'firewall_del';
                    $firewall_id = $instance_basic_table->find()->where(array('id' => $firewall_id))->toArray();
                    if (!empty($firewall_id)) {
                        $parameter['basicId'] = (string) $firewall_id[0]['id'];
                        $parameter['firewallCode'] = (string) $firewall_id[0]['code'];
                        $re_code = $order->postInterface($url, $parameter);
                    }
                }
                $parameter['basicId'] = $value;
                $parameter['method'] = 'vpc_del';
                if ($_request_code[$key] == '-') {
                    $parameter['vpcCode'] = '';
                } elseif ($_request_code[$key] == 'null'){
                    $parameter['vpcCode'] = '';
                } else {
                    $parameter['vpcCode'] = $_request_code[$key];
                }
            }
            $re_code = $order->postInterface($url, $parameter);
            if ($re_code['Code'] == 0) {
                $result++;
            } elseif ($re_code['Code'] == 400) {
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
}