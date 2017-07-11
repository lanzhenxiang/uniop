<?php

/**
 * 回收站
 * @date: 2016年4月1日 上午9:51:09
 * @author: wangjc
 */
namespace App\Controller\Console;

use App\Controller\Console\ConsoleController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Network\Http\Client;
use App\Controller\OrdersController;
use Cake\Controller\Controller;
use App\Controller\Console\Desktop\DesktopController;

class RecycledController extends ConsoleController
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
     *
     * 该方法只支持post和get方式请求
     *
     * @param 实例类型 $type
     * @param 请求方法 $action
     */
    public function ajaxFun($type, $action)
    {
        $this->autoRender = false;
        $this->layout = false;
        $request_data = [];
        if ($this->request->is('post')) {
            $request_data = $this->request->data;
        }
        if ($this->request->is('get')) {
            $request_data = $this->request->query;
        }

        if ($action == 'delete') {
            if ($type == 'hosts') {
                $res = $this->deleteHosts($request_data);
            } else {
                $res = $this->deleteDesktop($request_data);
            }
        } else {
            if ($action == 'recover') {
                $res = $this->recover($request_data);
            }
        }

        echo json_encode($res);
        exit();
    }

    public function index()
    {
        $checkPopedomlist = $this->_checkPopedom('ccm_ps_recycle');
        if (! $checkPopedomlist) {
            return $this->redirect('/console/');
        }
        $popedomname = $this->request->session()->read('Auth.User.popedomname') ? $this->request->session()->read('Auth.User.popedomname') : 0;
        $this->set('popedomname', $popedomname);
        $account_table = TableRegistry::get('Accounts');
        $user = $account_table->find()
            ->select('department_id')
            ->where(array(
            'id' => $this->request->session()
                ->read('Auth.User.id')
        ))
            ->first();
        $deparments = TableRegistry::get('Departments');
        $this->set('_default', $deparments->get($user["department_id"]));
        $table = $deparments->find('all');
        $this->set('_deparments', $table);

        $agent = TableRegistry::get('Agent');
        $agents = $agent->find('all')->toArray();
        $this->set('agent', $agents);
    }

    /**
     * 获取列表数据,
     * 新增关联查询
     */
    public function listsDesktop()
    {
        $request_data = $this->request->query;
        $checkPopedomlist = $this->_checkPopedom('ccm_ps_recycle');
        if (! $checkPopedomlist) {
            return $this->redirect('/console/');
        }
        $this->paginate['limit'] = $request_data['limit'];
        $this->paginate['page'] = $request_data['offset'] / $request_data['limit'] + 1;

        $instance_basic = TableRegistry::get('InstanceBasic');

        $where = [
            'InstanceBasic.type' => 'desktop',
            'isdelete' => '1'
        ];
        $where['department_id'] = $this->request->session()->read('Auth.User.department_id');
        if (in_array('ccf_all_select_department', $this->request->session()->read('Auth.User.popedomname')) && ! empty($request_data['department_id'])) {
            $where['department_id'] = $request_data['department_id'];
        }
        // var_dump($this->paginate);
        // var_dump($request_data);
        if(isset($request_data['search'])){
            if ($request_data['search']!="") {
            $where["OR"]["InstanceBasic.name like"] = "%" . $request_data['search'] . "%";
            $where["OR"]["InstanceBasic.code like"] = "%" . $request_data['search'] . "%";
        }
        }

        if (! empty($request_data['class_code'])) {
            $where["InstanceBasic.location_code like"] = $request_data['class_code'] . "%";
        }
        if (! empty($request_data['class_code2'])) {
            $where["InstanceBasic.location_code like"] = $request_data['class_code2'] . "%";
        } elseif (! empty($request_data['class_code'])) {
            $where["InstanceBasic.location_code like"] = $request_data['class_code'] . "%";
        }
        $this->_pageList['total'] = $instance_basic->find('all')
            ->contain([
            'HostExtend',
            'Agent',
            'InstanceRecycle',
            'HostsNetworkCard'
        ])
            ->where($where)
            ->count();
        $this->_pageList['rows'] = $this->paginate($instance_basic->find('all')
            ->contain([
            'HostExtend',
            'Agent',
            'InstanceRecycle',
            'HostsNetworkCard'
        ])
            ->where($where)
            ->order([
            'InstanceRecycle.create_time' => 'DESC'
        ])
            ->group(['InstanceBasic.id'])
            );
        // foreach ($this->_pageLis['rows'] as $key => $value) {
        // var_dump($this->_pageList['rows']);
        // }
        // exit;
        echo json_encode($this->_pageList);
        exit();
    }

    /**
     * 获取列表数据,
     * 新增关联查询
     * 回收站主机列表
     */
    public function listsHosts()
    {
        $request_data = $this->request->query;
        $checkPopedomlist = $this->_checkPopedom('ccm_ps_recycle');
        if (! $checkPopedomlist) {
            return $this->redirect('/console/');
        }
        $this->paginate['limit'] = $request_data['limit'];
        $this->paginate['page'] = $request_data['offset'] / $request_data['limit'] + 1;

        $instance_basic = TableRegistry::get('InstanceBasic');

        $where = [
            'InstanceBasic.type' => 'hosts',
            'isdelete' => '1'
        ];
        $where['department_id'] = $this->request->session()->read('Auth.User.department_id');
        if (in_array('ccf_all_select_department', $this->request->session()->read('Auth.User.popedomname')) && ! empty($request_data['department_id'])) {
            $where['department_id'] = $request_data['department_id'];
        }
        // var_dump($this->paginate);
        // var_dump($request_data);
        if(isset($request_data['search'])){
            if ($request_data['search']!="") {
            $where["OR"]["InstanceBasic.name like"] = "%" . $request_data['search'] . "%";
            $where["OR"]["InstanceBasic.code like"] = "%" . $request_data['search'] . "%";
//             $where["InstanceBasic.name like"] = "%" . $request_data['search'] . "%";
            // $where['OR'] =array('loginname like'=>"%$name%",'username like'=>"%$name%");
        }
        }

        if (! empty($request_data['class_code'])) {
            $where["InstanceBasic.location_code like"] = $request_data['class_code'] . "%";
        }
        if (! empty($request_data['class_code2'])) {
            $where["InstanceBasic.location_code like"] = $request_data['class_code2'] . "%";
        } elseif (! empty($request_data['class_code'])) {
            $where["InstanceBasic.location_code like"] = $request_data['class_code'] . "%";
        }
        $this->_pageList['total'] = $instance_basic->find('all')
            ->contain([
            'HostExtend',
            'Agent',
            'InstanceRecycle',
            'HostsNetworkCard'
        ])
            ->where($where)
            ->count();
        $this->_pageList['rows'] = $this->paginate($instance_basic->find('all')
            ->contain([
            'HostExtend',
            'Agent',
            'InstanceRecycle',
            'HostsNetworkCard'
        ])
            ->where($where)
            ->order([
            'InstanceRecycle.create_time' => 'DESC'
        ])
            ->group(['InstanceBasic.id'])
            );
        // debug($this->_pageList);die;
        // return $this->_pageList;
        echo json_encode($this->_pageList);
        exit();
    }

    /**
     * 清空回收站（主机或云桌面）
     * @date: 2016年3月31日 下午2:23:24
     *
     * @author : wangjc
     * @return :
     */
    public function deleteAll($type, $department_id)
    {
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        $instance_basic_data = $instance_basic_table->find()
            ->where([
            'type' => $type,
            'department_id' => $department_id,
            'isdelete' => 1
        ])
            ->toArray();
        $request_data = [];
        $request_data['code'] = '';
        $request_data['id'] = '';
        if (! empty($instance_basic_data)) {
            foreach ($instance_basic_data as $data) {
                $request_data['code'] .= $data['code'] . ',';
                $request_data['id'] .= $data['id'] . ',';
            }
            if ($type == 'hosts') {
                $request_data['method'] = 'ecs_delete';
                //$request_data['isEach'] = 'true';
                $res = $this->deleteHosts($request_data);
            } else {
                $request_data['method'] = 'desktop_del';
                $res = $this->deleteDesktop($request_data);
            }
        } else {
            $code = '0001';
            $msg = '已清空回收站';
            $res = compact(array_values($this->_serialize));
        }
        echo json_encode($res);
        exit();
    }

    /**
     * 删除主机
     * @date: 2016年3月31日 下午2:36:37
     *
     * @author : wangjc
     * @return :
     */
    public function deleteHosts($request_data = [])
    {
        $instance_basic_table = TableRegistry::get('InstanceBasic');

        $code = '0001';
        $data = [];
        $url = Configure::read('URL');
        $orders = new OrdersController();
        $result = array();
        $request_data['uid'] = (string) $this->request->session()->read('Auth.User.id');

        $_request_id = explode(',', $request_data['id']);
        $_request_code = explode(',', $request_data['code']);
        // uid
        unset($request_data['code']);
        unset($request_data['id']);
        $stop_hosts['Code'] = '0';
        foreach ($_request_id as $key => $value) {
            if (! empty($value)) {
                $request_data['basicId'] = $value;
                $request_data['instanceCode'] = $_request_code[$key];
                $result = $orders->ajaxFun($request_data);
            }
        }

        $code = '0000';

        return compact(array_values($this->_serialize));
    }

    /**
     * 删除云桌面
     * @date: 2016年3月31日 下午2:57:49
     *
     * @author : wangjc
     * @return :
     */
    public function deleteDesktop($request_data=[])
    {
        $code = '0001';
        $data = [];
        $result = 0;
        $_request_id = explode(',', $request_data['id']);
        $_request_code = explode(',', $request_data['code']);
        // 逻辑处理
        $instance_basic_table = TableRegistry::get('InstanceBasic');

        $request_data['method'] = 'desktop_del';
        $request_data['uid'] = $this->request->session()->read('Auth.User.id') ? (string) $this->request->session()->read('Auth.User.id') : (string) 0;
        $orders = new OrdersController();
        $url = Configure::read('URL');
        unset($request_data['code']);
        unset($request_data['id']);
        foreach ($_request_id as $key => $value) {
            if (! empty($value)) {
                $request_data['basicId'] = $value;
                $request_data['instanceCode'] = $_request_code[$key];
                $result = $orders->ajaxFun($request_data);
            }
        }
        $code = '0000';
        // $msg = Configure::read('MSG.'.$code);
        return compact(array_values($this->_serialize));
    }

    /**
     * 恢复
     * @date: 2016年3月31日 下午3:19:40
     *
     * @author : wangjc
     * @return :
     */
    public function recover($request_data)
    {
        $code = '0001';
        $data = [];
        $ids = $request_data;
        $result = 0;
        $_request_id = explode(',', $ids['id']);
        // 逻辑处理

        $parameter['method'] = 'RemoveTrash';
        $parameter['uid'] = $this->request->session()->read('Auth.User.id') ? (string) $this->request->session()->read('Auth.User.id') : (string) 0;
        $order = new OrdersController();
        $url = Configure::read('URL');

        foreach ($_request_id as $key => $value) {

            $parameter['basicId'] = $value;
            $re_code = $order->postInterface($url, $parameter); // 调用接口
            if ($re_code['Code'] != 0) {
                $code = '0002';
                $msg = $re_code['Message'];
                return compact(array_values($this->_serialize));
                die();
            }
        }

        $code = '0000';

        return compact(array_values($this->_serialize));
    }
}