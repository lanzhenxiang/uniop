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
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use App\Controller\OrdersController;
use Cake\Datasource\ConnectionManager;
class ServerController extends ConsoleController
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
     * 新增关联查询
     */
    public function lists($request_data = array())
    {
        // var_dump($request_data);exit;
        $this->paginate['limit'] = $request_data['limit'];
        $this->paginate['page'] = $request_data['offset'] / $request_data['limit'] + 1;
        $instance_basic = TableRegistry::get('InstanceBasic');
        $account_table = TableRegistry::get('Accounts');
        $department_id = $request_data['department_id'];
        $where = array('InstanceBasic.type' => 'hosts', 'department_id' => $department_id, 'ServiceList.type_id' => $request_data['t']);
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
        $this->_pageList['total'] = $instance_basic->find('all')->contain(array('HostExtend', 'ServiceList'))->where($where)->count();
        $this->_pageList['rows'] = $this->paginate($instance_basic->find('all')->contain(array('HostExtend', 'ServiceList'))->where($where)->order(array('create_time' => 'DESC')));
        return $this->_pageList;
    }
    /**
     * 编辑数据列表
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
        $msg = Configure::read('MSG.' . $code);
        return compact(array_values($this->_serialize));
    }
    /**
     * @func: EIPajax 方法
     * @param: null
     * @date: 2015年10月12日 下午2:45:17
     * @author: shrimp liao
     * @return: null
     */
    public function ajaxHosts($request_data = array())
    {
        $orders = new OrdersController();
        $result = array();
        $isEach = $request_data['isEach'];
        unset($request_data['isEach']);
        if ($isEach == 'true') {
            $instanceCode_str = $request_data['instanceCode'];
            $instanceCode_array = explode(',', $instanceCode_str);
            foreach ($instanceCode_array as $key => $value) {
                if (!empty($value)) {
                    if (!empty($this->getHostsEntityByCode($value))) {
                        $request_data['basicId'] = $this->getHostsEntityByCode($value)[0]['id'];
                    } else {
                        $request_data['basicId'] = '';
                    }
                    $request_data['basicId'] = $request_data['basicId'];
                    $request_data['method'] = $request_data['method'];
                    $request_data['uid'] = (string) $this->request->session()->read('Auth.User.id');
                    // uid
                    $request_data['instanceCode'] = $value;
                    $result = $orders->ajaxFun($request_data);
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
            $request_data['basicId'] = $request_data['basicId'];
            $request_data['method'] = $request_data['method'];
            $request_data['uid'] = (string) $this->request->session()->read('Auth.User.id');
            // uid
            $request_data['instanceCode'] = $request_data['instanceCode'];
            return $orders->ajaxFun($request_data);
        }
    }
}