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
use Cake\Network\Exception\BadRequestException;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use App\Controller\OrdersController;
use Cake\Datasource\ConnectionManager;

class ImagelistController extends ConsoleController
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
    public function lists($request_data = []){

        $checkPopedomlist = $this->_checkPopedom('ccm_ps_images');
        if (! $checkPopedomlist) {
            return $this->redirect('/console/');
        }
        $this->paginate['limit'] = $request_data['limit'];
        $this->paginate['page'] = $request_data['offset'] / $request_data['limit'] + 1;

        $imagelist = TableRegistry::get('Imagelist');
        $agent_imagelist = TableRegistry::get('AgentImagelist');
        $agent = TableRegistry::get('Agent');
        $deparment_id = $this->request->session()->read('Auth.User.department_id') ? $this->request->session()->read('Auth.User.department_id') : 0;
        if(!empty($request_data['source'])&&$request_data['is_private'] == 1){
            if($request_data['source'] == 2){
                $where["department_id"] = $deparment_id;
            }
        }
        if($request_data['source'] == 2 && $request_data['is_private'] == 0 && isset($request_data['department_id'])){
            $where["department_id"] = $request_data['department_id'];
        }
        if(isset($request_data['is_private'])&& $request_data['source'] == 2){
            $where['is_private'] = $request_data['is_private'];
        }
        $where['image_source'] = $request_data['source'];
        // var_dump($this->paginate);
        // var_dump($request_data);exit;
        if(isset($request_data['search'])){
            if ($request_data['search']!="") {
                $where['OR'] = [
                    ["image_name like"=>"%" . $request_data['search'] . "%"],
                    ["image_code like"=>"%" . $request_data['search'] . "%"]
                ];
            }
        }

        $image_where = '';
        if (! empty($request_data['class_code2'])) {
            $agent_imagelist_info = $agent_imagelist->find()->select(['image_id'])->where(['agent_id'=>$request_data['class_code2']])->group(['image_id'])->toArray();
            if(!empty($agent_imagelist_info)){
                foreach ($agent_imagelist_info as $agent_imagelist_info_key => $image_id) {
                    $image_where[$agent_imagelist_info_key]['id'] = $image_id['image_id'];
                }
                $where['OR'] = $image_where;
            }else{
                $where['id'] = '';
            }
        }else if(! empty($request_data['class_code'])){
            $agnet_info = $agent->find()->select(['id'])->where(['parentid'=>$request_data['class_code']])->toArray();
            if(!empty($agnet_info)){
                foreach ($agnet_info as $key => $value) {
                    $agent_imagelist_where_info[$key]['agent_id'] = $value['id'];
                }
            }
            $agent_imagelist_where_info[$key+1]['agent_id'] = (int)$request_data['class_code'];
            $agent_imagelist_where['OR']=$agent_imagelist_where_info;
            $agent_imagelist_info = $agent_imagelist->find()->select(['image_id'])->where($agent_imagelist_where)->group(['image_id'])->toArray();
            if(!empty($agent_imagelist_info)){
                foreach ($agent_imagelist_info as $agent_imagelist_info_key => $image_id) {
                    $image_where[$agent_imagelist_info_key]['id'] = $image_id['image_id'];
                }
                $where['OR'] = $image_where;
            }else{
                $where['id'] = '';
            }
        }
        // debug($request_data);
        // debug($where);exit;
        // var_dump($image_where);exit;
        $this->_pageList['total'] = $imagelist->find('all')->contain([
            'AgentImagelist',
            ]) ->where($where) ->count();
        $this->_pageList['rows'] = $this->paginate($imagelist->find('all')->contain([
            'Agents',
            ])->where($where));
        // foreach ($this->_pageLis['rows'] as $key => $value) {
        // var_dump($this->_pageList['rows']->toArray());
        // }
        // exit;
        return $this->_pageList;
    }

    /**
     * 编辑数据列表
     */
    public function edit($request_data = [])
    {
        $code = '0001';
        $data = [];
        $agents_id = split(',', $request_data['agentId']);
        $imagelist = TableRegistry::get('Imagelist');
        $agent_imagelist = TableRegistry::get('AgentImagelist');

        $info['image_id'] = $request_data['id'];
        $res = $agent_imagelist->deleteAll(array('image_id'=>$info['image_id']));
        foreach ($agents_id as $key => $agent_id) {
            if($agent_id!=""){
                $info['agent_id']=$agent_id;
                $agent_imagelist_info = $agent_imagelist->newEntity();
                $agent_imagelist_info = $agent_imagelist->patchEntity($agent_imagelist_info,$info);
                $query = $agent_imagelist->save($agent_imagelist_info);
            }
        }
        // 编辑操作
        $imagelist_info = $imagelist->newEntity();
        $imagelist_info = $imagelist->patchEntity($imagelist_info,$request_data);
        $result = $imagelist ->save($imagelist_info);
        if ($result) {
            $message = array('code'=>0,'msg'=>'操作成功');
        }

        echo json_encode($message);exit();
    }

    /**
     * @func: 启动主机
     *
     * @param
     *            :@type:方法名称 @hostsId:操作主机ID
     *            @date: 2015年10月12日 下午2:45:17
     * @author : shrimp liao
     * @return : null
     */
    public function ajaxHosts($request_data = [])
    {
        $orders = new OrdersController();
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        $array = array();
        $hostAarray = explode(',', $request_data['hostsId']);
        $result = 0;
        foreach ($hostAarray as $item) {
            if ($item != '') {
                $array['method'] = $request_data['type'];
                $array['uid'] = $this->request->session()->read('Auth.User.id') ? (string) $this->request->session()->read('Auth.User.id') : (string) 0;
                $array['instanceCode'] = $item;
                $array['basicId'] = (string)$instance_basic_table->find()->select(['id'])->where(['code'=>$item])->first()->id;
                if ($orders->ajaxFun($array)['Code'] != '0') {
                    $result += 1;
                }
            }
        }
        return $result;
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
        $parameter['method'] = 'desktop_del';
        $parameter['uid'] = $this->request->session()->read('Auth.User.id') ? (string) $this->request->session()->read('Auth.User.id') : (string) 0;
        $order = new OrdersController();
        $url = Configure::read('URL');
        foreach ($_request_code as $key => $value) {
            $parameter['desktopCode'] = $value;
            $parameter['basicId'] = $_request_id[$key];
            $re_code = $order->postInterface($url, $parameter); // 调用接口
                                                             // var_dump($re_code);
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

    //根据agentid查找所有的子agentid
    public function checkAgentId(){

    }
}