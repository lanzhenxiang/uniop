<?php
/**
 * 控制台 － 工作流管理
 *
 *
 * 使用cell实现
 *
 * @file: WorkflowController.php
 * @date: 2016年1月20日 下午6:24:10
 * @author: xingshanghe
 * @email: xingshanghe@icloud.com
 * @copyright poplus.com
 *
 */

namespace App\Controller\Console;

use App\Controller\AccountsController;
use App\Controller\Admin\GoodsVpcController;
use App\Controller\OrdersController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Network\Http\Client;
use Cake\Log\Log;
use Requests as Requests;

class WorkflowController extends AccountsController
{

    private $_code      = 0;
    private $_msg       = "";
    private $_data      = null;
    private $_error     = null;
    private $_serialize = array('code', 'msg', 'data');

    public function initialize()
    {
        parent::initialize();

        $this->viewClass = 'Json';
        $this->loadComponent('RequestHandler');

        $this->_data = $this->_getData();
    }

    public function batchAuth(){
        $data = $this->request->data;
        $this->viewClass = 'Json';
        $orders_table             = TableRegistry::get('Orders');
        $workflow_detail_table      = TableRegistry::get('WorkflowDetail');
        $order_ids = explode(',',trim($data['order_ids'],','));
        foreach ($order_ids as $key => $order_id) {
            $order_entity = $orders_table->find()->contain(['WorkflowDetail'])->where(['Orders.id'=>$order_id])->first();

            $workflow = $this->_getCurrentTrace($order_id);
            // debug($workflow);exit;
            $workflow_detail = $workflow_detail_table->find()->where(['flow_id'=>$order_entity->flow_id,'parent_id'=>$workflow['flow_detail_id']])->first();
            $apiData = array();
            $apiData['order_id'] = $order_id;
            $apiData['flow_detail_name'] = $workflow_detail['step_name'];
            $apiData['flow_detail_id']  = $workflow_detail['id'];
            $apiData['auth_note']   = $data['auth_note'];
            $apiData['auth_action'] = $data['auth_action'];
            $this->_data = $apiData;

            $orders_table             = TableRegistry::get('Orders');
            $order_process_flow_table = TableRegistry::get('OrdersProcessFlow');
            $order_process_flow       = $order_process_flow_table->newEntity();

            $order_process_flow->order_id         = $order_id;
            $order_process_flow->create_time      = time();
            $order_process_flow->user_id          = $this->request->session()->read('Auth.User.id');
            $order_process_flow->user_name        = $this->request->session()->read('Auth.User.username');
            $order_process_flow->auth_action      = $this->_data['auth_action'];
            $order_process_flow->auth_note        = $this->_data['auth_note'];
            $order_process_flow->flow_detail_name = $this->_data['flow_detail_name'];
            $order_process_flow->flow_detail_id   = $this->_data['flow_detail_id'];

            $orders = $orders_table->newEntity();

            $orders->id = $order_id;
            //设置数据
            if ($order_process_flow_table->save($order_process_flow)) {

                $workflow_detail_table = TableRegistry::get('WorkflowDetail');

                //审核通过 业务逻辑
                $current_step = $workflow_detail_table->find()->where(['id' => $this->_data['flow_detail_id']])->first();

                if ($this->_data['auth_action'] >= 0) {

                    $orders->status    = $current_step['lft'];
                    $orders->detail_id = $current_step['id'];
                    $orders->is_back   = 0;
                } else {
                    $back_step = $workflow_detail_table->find()->where(['lft' => $current_step['lft'] - 1, 'flow_id' => $current_step['flow_id']])->first();
                    if ($back_step) {
                        $orders->status    = -2;
                        $orders->detail_id = $back_step['id'];
                    } else {
                        //异常结束
                        $orders->status    = -1;
                        $orders->detail_id = null;
                    }
                    $orders->is_back = 1;
                }

                $orders_table->save($orders);
                $orders_goods_table = TableRegistry::get('OrdersGoods');
                $orders_goods_data  = $orders_goods_table->find()->contain(['Goods'])->where(['OrdersGoods.order_id' => $order_id])->toArray();

                //$this->email($order_id, $this->_data['auth_action'], $this->_data['flow_detail_id'], $current_step['send_email']);
                if ($this->_data['auth_action'] == 1) {
 
                    $next_step = $workflow_detail_table->find()->where(['parent_id' => $current_step["id"]])->first();
                    // debug($next_step);die();
                    if ((int) $next_step["action_type"] == 1||is_null($next_step)) {
                        //循环并遍历订单中的商品是否满足一键VPC条件
                        foreach ($orders_goods_data as $g => $gg) {
                            if (!empty($gg["good"]["goods_vpc"]) && $gg["good"]["goods_vpc"] != 0) {
                                $this->_doVpcEcs($gg);
                            } else {
                                if ($gg["is_console"] == 0 && $gg["good"]["fixed"] == 0) {
                                    //如果不是后台创建，并且fixed==0 订单流程为自动 都走的时候自动购买流程（三者缺一不可）
                                    // debug($gg);die;
                                    if (in_array($gg['good']['goodType'], array('citrix', 'ecs', 'citrix_public','vpc','elb','disks','eip'))) {
                                        $this->_doApi($gg);
                                    }
                                    //一次新计费，写入账单
                                    if (in_array($gg['good']['goodType'], array('bs', 'mpaas'))) {
                                        $this->_doChare($gg);
                                    }
                                }
                            }

                        }
                    }

                    $_children = $workflow_detail_table->find('children', ['for' => $this->_data['flow_detail_id']])->where(['flow_id' => $current_step['flow_id']])->toArray();

                    // Log::info(json_encode($_children));

                    if ($_children) {
                        $_js = 0;
                        foreach ($_children as $_k => $_v) {

                            if ($_v['rgt'] - $_v['lft'] == 1) {
                                $_js++;
                                if ($_k + 1 != $_js) {
                                    break;
                                }
                                $order_process_flow_end = $order_process_flow_table->newEntity();

                                $order_process_flow_end->order_id         = $order_id;
                                $order_process_flow_end->create_time      = time();
                                $order_process_flow_end->user_id          = $this->request->session()->read('Auth.User.id');
                                $order_process_flow_end->user_name        = '系统';
                                $order_process_flow_end->auth_action      = 0;
                                $order_process_flow_end->auth_note        = '系统生成';
                                $order_process_flow_end->flow_detail_name = $_v['step_name'];
                                $order_process_flow_end->flow_detail_id   = $_v['id'];

                                $orders = $orders_table->newEntity();

                                $orders->id        = $order_id;
                                $orders->status    = $_v['lft'];
                                $orders->detail_id = $_v['id'];

                                try {
                                    $order_process_flow_table->save($order_process_flow_end);
                                    $orders_table->save($orders);
                                } catch (\Exception $e) {
                                    echo json_encode([
                                        'code' => '0003',
                                        'msg'  => '数据库操作失败',
                                        'data' => $order_process_flow_end,
                                    ]);
                                    die;
                                }
                                break;
                            } elseif ($_v['action_type'] == 1) {
                                $_js++;
                                if ($_k + 1 != $_js) {
                                    break;
                                }
                                $order_process_flow = $order_process_flow_table->newEntity();

                                $order_process_flow->order_id         = $order_id;
                                $order_process_flow->create_time      = time();
                                $order_process_flow->user_id          = $this->request->session()->read('Auth.User.id');
                                $order_process_flow->user_name        = '系统';
                                $order_process_flow->auth_action      = 0;
                                $order_process_flow->auth_note        = '系统生成';
                                $order_process_flow->flow_detail_name = $_v['step_name'];
                                $order_process_flow->flow_detail_id   = $_v['id'];

                                $orders = $orders_table->newEntity();

                                $orders->id        = $order_id;
                                $orders->status    = $_v['lft'];
                                $orders->detail_id = $_v['id'];
                                try {
                                    $order_process_flow_table->save($order_process_flow);
                                    $orders_table->save($orders);
                                    //审核通过 业务逻辑
                                    $current_step = $workflow_detail_table->find()->where(['id' => $_v['id']])->first();
                                    //解析step_bizinfo处理业务逻辑,存在业务数据逻辑并且自动执行
  
                                    $next_step = $workflow_detail_table->find()->where(['parent_id' => $_v["id"]])->first();
                                    if ((int) $_v["action_type"] == 1||is_null($next_step)) {
                                        //循环并遍历订单中的商品是否满足一键VPC条件
                                        // foreach ($orders_goods_data as $g => $gg) {
                                        //     if (!empty($gg["good"]["goods_vpc"]) && $gg["good"]["goods_vpc"] != 0) {
                                        //         Log::error("二次+++++++++++++++++++++++++++++++++++++++++++++");
                                        //         // $this->_doVpcEcs($gg);
                                        //     }
                                        // }
                                    }

                                } catch (\Exception $e) {
                                    echo json_encode([
                                        'code' => '0003',
                                        'msg'  => '数据库操作失败',
                                        'data' => $order_process_flow,
                                    ]);
                                    die;
                                }
                                //$this->email($order_id, $this->_data['auth_action'], $_v['id'], $next_step['send_email']);
                            }
                        }
                    }
                }
                $code = '0000';
                $msg  = '操作成功';
                $data = $order_process_flow;
            } else {
                $code = '0003';
                $msg  = '数据库操作失败';
                $data = $order_process_flow;
                $this->set('_serialize',compact(['code','msg','data']));exit;
            }
        }
        $code = '0000';
        $msg  = '操作成功';
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize', $this->_serialize);
    }

    /**
     * 获取当前流程
     *
     * @param integer $order_id
     */
    protected function _getCurrentTrace( $order_id ) {
        //加载WorkflowDetail模型类
        $this->loadModel('OrdersProcessFlow');

        $process_flow = $this->OrdersProcessFlow->find()->where(['order_id'=>$order_id])->toArray();

        $process_flow_format = [];
        foreach ($process_flow as $_key => $_value){
            if ($_value['auth_action'] != -1){
                $process_flow_format[] = $_value;
            }else{
                if(count($process_flow_format)>1){//处理第一步退回
                    array_pop($process_flow_format);
                }
            }
        }
        return array_pop($process_flow_format);;
    }


    /**
     * 审核
     * @param integer $current_user_id
     * @param integer $order_id
     * @return array
     */
    public function auth()
    {

        $this->viewClass = 'Json';

        $code = '0001';
        $msg  = '操作失败';
        $data = '';

        //数据校验
        $lack_fields = [];

        $_needed_fileds = [
            'order_id', 'auth_action', 'auth_note', 'flow_detail_name', 'flow_detail_id',
        ];
        // debug($this->_data);die();
        if ($_needed_fileds) {
            foreach ($_needed_fileds as $_key) {
                if (!isset($this->_data[$_key])) {
                    $lack_fields[] = $_key;
                }
            }
        }
        if (empty($lack_fields)) {
            $orders_table             = TableRegistry::get('Orders');
            $order_process_flow_table = TableRegistry::get('OrdersProcessFlow');
            $order_process_flow       = $order_process_flow_table->newEntity();

            $order_process_flow->order_id         = $this->_data['order_id'];
            $order_process_flow->create_time      = time();
            $order_process_flow->user_id          = $this->request->session()->read('Auth.User.id');
            $order_process_flow->user_name        = $this->request->session()->read('Auth.User.username');
            $order_process_flow->auth_action      = $this->_data['auth_action'];
            $order_process_flow->auth_note        = $this->_data['auth_note'];
            $order_process_flow->flow_detail_name = $this->_data['flow_detail_name'];
            $order_process_flow->flow_detail_id   = $this->_data['flow_detail_id'];
            // debug($order_process_flow);die;

            $orders = $orders_table->newEntity();

            $orders->id = $this->_data['order_id'];
            //设置数据
            if ($order_process_flow_table->save($order_process_flow)) {

                $workflow_detail_table = TableRegistry::get('WorkflowDetail');

                //审核通过 业务逻辑
                $current_step = $workflow_detail_table->find()->where(['id' => $this->_data['flow_detail_id']])->first();

                if ($this->_data['auth_action'] >= 0) {

                    $orders->status    = $current_step['lft'];
                    $orders->detail_id = $current_step['id'];
                    $orders->is_back   = 0;
                } else {
                //审批退回
                    $back_step = $workflow_detail_table->find()->where(['lft' => $current_step['lft'] - 1, 'flow_id' => $current_step['flow_id']])->first();
                    if ($back_step) {
                        //$orders->status    = $current_step['lft'] - 1;
                        $orders->status    = -2;
                        $orders->detail_id = $back_step['id'];
                    } else {
                        //异常结束
                        $orders->status    = -1;
                        $orders->detail_id = null;
                    }
                    $orders->is_back = 1;
                }

                $orders_table->save($orders);
                $order_id           = $orders->id;
                $orders_goods_table = TableRegistry::get('OrdersGoods');
                $orders_goods_data  = $orders_goods_table->find()->contain(['Goods'])->where(['OrdersGoods.order_id' => $order_id])->toArray();

                // $this->email($this->_data['order_id'], $this->_data['auth_action'], $this->_data['flow_detail_id'], $current_step['send_email']);
                if ($this->_data['auth_action'] == 1) {
                    
                    $next_step = $workflow_detail_table->find()->where(['parent_id' => $current_step["id"]])->first();
                    if ((int) $next_step["action_type"] == 1|| is_null($next_step)) {
                        //循环并遍历订单中的商品是否满足一键VPC条件
                        foreach ($orders_goods_data as $g => $gg) {
                            // debug($gg);exit;
                            if (!empty($gg["good"]["goods_vpc"]) && $gg["good"]["goods_vpc"] != 0) {
                                $this->_doVpcEcs($gg);
                            } else {
                                if ($gg["is_console"] == 0 && $gg["good"]["fixed"] == 0) {
                                    //如果不是后台创建，并且fixed==0 订单流程为自动 都走的时候自动购买流程（三者缺一不可）
                                    if (in_array($gg['good']['goodType'], array('citrix', 'ecs', 'citrix_public', 'vpc','elb','disks','eip'))) {
                                        $this->_doApi($gg);
                                    }
                                    //一次新计费，写入账单
                                    if (in_array($gg['good']['goodType'], array('bs', 'mpaas'))) {
                                        $this->_doChare($gg);
                                    }
                                }
                            }

                        }
                    }

                    $_children = $workflow_detail_table->find('children', ['for' => $this->_data['flow_detail_id']])->where(['flow_id' => $current_step['flow_id']])->toArray();

                    if ($_children) {
                        $_js = 0;
                        foreach ($_children as $_k => $_v) {
                            if ($_v['rgt'] - $_v['lft'] == 1) {
                                $_js++;
                                if ($_k + 1 != $_js) {
                                    break;
                                }
                                $order_process_flow_end = $order_process_flow_table->newEntity();

                                $order_process_flow_end->order_id         = $this->_data['order_id'];
                                $order_process_flow_end->create_time      = time();
                                $order_process_flow_end->user_id          = $this->request->session()->read('Auth.User.id');
                                $order_process_flow_end->user_name        = '系统';
                                $order_process_flow_end->auth_action      = 0;
                                $order_process_flow_end->auth_note        = '系统生成';
                                $order_process_flow_end->flow_detail_name = $_v['step_name'];
                                $order_process_flow_end->flow_detail_id   = $_v['id'];

                                $orders = $orders_table->newEntity();

                                $orders->id        = $this->_data['order_id'];
                                $orders->status    = $_v['lft'];
                                $orders->detail_id = $_v['id'];

                                try {
                                    $order_process_flow_table->save($order_process_flow_end);
                                    $orders_table->save($orders);
                                } catch (\Exception $e) {
                                    echo json_encode([
                                        'code' => '0003',
                                        'msg'  => '数据库操作失败',
                                        'data' => $order_process_flow_end,
                                    ]);
                                    die;
                                }
                                break;
                            } elseif ($_v['action_type'] == 1) {
                                $_js++;
                                if ($_k + 1 != $_js) {
                                    break;
                                }
                                $order_process_flow = $order_process_flow_table->newEntity();

                                $order_process_flow->order_id         = $this->_data['order_id'];
                                $order_process_flow->create_time      = time();
                                $order_process_flow->user_id          = $this->request->session()->read('Auth.User.id');
                                $order_process_flow->user_name        = '系统';
                                $order_process_flow->auth_action      = 0;
                                $order_process_flow->auth_note        = '系统生成';
                                $order_process_flow->flow_detail_name = $_v['step_name'];
                                $order_process_flow->flow_detail_id   = $_v['id'];

                                $orders = $orders_table->newEntity();

                                $orders->id        = $this->_data['order_id'];
                                $orders->status    = $_v['lft'];
                                $orders->detail_id = $_v['id'];
                                try {
                                    $order_process_flow_table->save($order_process_flow);
                                    $orders_table->save($orders);
                                    //审核通过 业务逻辑
                                    $current_step = $workflow_detail_table->find()->where(['id' => $_v['id']])->first();
                                    
                                    $next_step = $workflow_detail_table->find()->where(['parent_id' => $_v["id"]])->first();
                                    if ((int) $_v["action_type"] == 1||is_null($next_step)) {
                                        //循环并遍历订单中的商品是否满足一键VPC条件
                                        // foreach ($orders_goods_data as $g => $gg) {
                                        //     if (!empty($gg["good"]["goods_vpc"]) && $gg["good"]["goods_vpc"] != 0) {
                                        //         Log::error("二次+++++++++++++++++++++++++++++++++++++++++++++");
                                        //         // $this->_doVpcEcs($gg);
                                        //     }
                                        // }
                                        //循环并遍历订单中的商品是否满足一键VPC条件
                                    }

                                } catch (\Exception $e) {
                                    echo json_encode([
                                        'code' => '0003',
                                        'msg'  => '数据库操作失败',
                                        'data' => $order_process_flow,
                                    ]);
                                    die;
                                }
                                //$this->email($this->_data['order_id'], $this->_data['auth_action'], $_v['id'], $next_step['send_email']);
                            }
                        }
                    }
                }
                $code = '0000';
                $msg  = '操作成功';
                $data = $order_process_flow;
            } else {
                $code = '0003';
                $msg  = '数据库操作失败';
                $data = $order_process_flow;
            }
        } else {
            $code = '0002';
            $msg  = '缺少相应参数:' . implode(',', $lack_fields);
            $data = '';

        }

        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize', $this->_serialize);

    }

    /**
     * 审核
     * @author wangjc
     * @param integer $current_user_id
     * @param integer $order_id
     * @return array
     */
    protected function _doBiz($a)
    {
        $parameter['uid'] = (string) $this->request->session()->read('Auth.User.id');
        $url              = Configure::read('URL');
        $order            = new OrdersController();
        if ($a['action'] == 'create') {
            //创建机器
            $order_id           = $a['parameter']['order_id'];
            $status             = $a['parameter']['status'];
            $orders_table       = TableRegistry::get('Orders');
            $orders_goods_table = TableRegistry::get('OrdersGoods');
            $goods_spec_table   = TableRegistry::get('GoodsSpec');
            $orders_goods_data  = $orders_goods_table->find()->contain(['Goods'])->where(['OrdersGoods.order_id' => $order_id])->toArray();
            // debug($orders_goods_data);exit;
            foreach ($orders_goods_data as $key => $good_data) {
                $goods_id       = $good_data['good']['id'];
                $num            = $good_data['num'];
                $good_spec_data = $goods_spec_table->find()->where(['goods_id' => $goods_id, 'is_need' => 1])->toArray();
                foreach ($good_spec_data as $k => $spec_data) {
                    $parameter[$spec_data['spec_code']] = (string) $spec_data['spec_value'];
                }
                // $parameter['orderId'] = (string) $order_id;
                if ($num > 1) {
                    for ($i = 1; $i <= $num; $i++) {

                        $interface = $order->postInterface($url, $parameter); //调用接口
                        if ($interface['Code'] != '0') {
                            return 0;
                            die;
                        }
                    }
                } else {
                    $interface = $order->postInterface($url, $parameter); //调用接口
                    if ($interface['Code'] != '0') {
                        return 0;
                        die;
                    }
                }

            }
        } else if (!empty($a['action'])) {
            //更新action，调用接口
            foreach ($a['parameter'] as $_p_k => $_p_v) {
                $parameter[$_p_k] = $_p_v;
            }
            $parameter['method'] = $a['action'];
            // var_dump($parameter);exit;
            $interface = $order->postInterface($url, $parameter); //调用接口
            if ($interface['Code'] != '0') {
                return 0;
                die;
            }
        }

        return 1;
    }

    /**
     *一键VPC
     *需要传入当前商品信息
     */
    protected function _doVpcEcs($table)
    {
        //获取商品信息
        //获取vpcid
        $goods_vpc = $table->good["goods_vpc"];
        //获取订单id
        $order_id     = $table->order_id;
        $orders_table = TableRegistry::get('Orders');
        //获取uid
        $uid = $orders_table->get($order_id)->account_id;
        //获取商品配置信息
        $goods = new GoodsVpcController();
        //获取vpc配置详情
        $vpcInfo = $goods->findVpcEcsConfigure($goods_vpc);
        //组装json
        $json = $this->createJsonByVpcConfiguration($goods_vpc, $vpcInfo, "onvpc", $uid);
        //发送请求
        $orders = new OrdersController();
        $result = $orders->ajaxFun($json);
        if ($result['Code'] != '0') {
            return 0;die();
        }
        return 1;
    }

    protected function _doApi($table)
    {
        //获取订单id
        $order_id     = $table->order_id;
        $orders_table = TableRegistry::get('Orders');

        //获取uid
        $uid = $orders_table->get($order_id)->account_id;
        $num = $orders_table->get($order_id)->num;
        //获取json 参数
        $str = $table["instance_conf"];
        //转数组
        $json = json_decode($str, true);
        //加uid
        $json["uid"]    = (string) $uid;
        // $json["number"] = "1"; //默认数值1

        $j = 0;//计算创建失败
        $n = $table['num'];
        
        if ($json["method"] == 'volume_add') {
            $disksName = isset($json['disksName']) ? $json['disksName'] : "";
            unset($json['disksName']);
        }
        for ($i = 0; $i < $n; $i++) {
            unset($json['number']);
            $json['order_id'] = (string)$json['order_id'];
            
            //发送请求
            $orders = new OrdersController();
            if (!isset($json["method"]) || empty($json["method"])) {
                return 1;
            } elseif ($json["method"] == 'ecs_add') {
                $json['image_price'] = isset($json['imagePay']) ? $json['imagePay'] : 0; //镜像价格
                $json['instance_price'] = isset($json['instancePay']) ? $json['instancePay'] : 0; //规格价格
                $json['ioOptimized'] = 'true';
            } else {
                unset($json['ecsName']);
            }
           
            
            $json['charge_mode'] = isset($table['charge_mode']) ? (string)$table['charge_mode'] : 'cycle'; 
            $json['interval'] = isset($table['interval']) ? (string)$table['interval'] : 'D'; //周期
            
            $json['price'] = isset($table['price_per']) ? (string)$table['price_per'] : '0'; //总价
            $json['real_price'] = isset($table['transaction_price']) ? (string)$table['transaction_price'] : '0'; //实际价格
            $json['priority'] = isset($table['priority']) ? (string)$table['priority'] : '0';
            
            
            switch ($json["method"]) {
                case 'router_add':
                    unset($json['number']);
                    unset($json['num']);
                    unset($json['vpcip5']);
                    unset($json['vpcip2']);
                    unset($json['vpcip1']);
                    unset($json['version_name']);
                    unset($json['billCycleName']);
                    unset($json['dyName']);
                    unset($json['dyCode']);
                    unset($json['csName']);
                    unset($json['csCode']);
                    unset($json['csid']);
                    if ($json['csCode'] == 'aliyun') {
                        $department_table = TableRegistry::get("Departments");
                        $accounts_table = TableRegistry::get("Accounts");
                        
                        $departmentID = $accounts_table->find()->select(['department_id'])->where(['id' => $json['uid']])->first();
                        if (isset($departmentID['department_id']) && !empty($departmentID['department_id'])) {
                            $department_data = $department_table
                            ->find()
                            ->select(['aliyun_account'])
                            ->where(['id' => $departmentID['department_id']])->first();
                            if (isset($department_data['aliyun_account']) && !empty($department_data['aliyun_account'])) {
                                $json['accountsCode'] = $department_data['aliyun_account'];
                            }
                        }
                    }
                    unset($json['csCode']);
                    
                    break;
                case 'volume_add':
                    $json['volumeName'] = $disksName.$i;
                    sleep(2);
                    // unset($json['disksName']);
                default:
                    unset($json['billCycle']);
                    unset($json['instance_name'], $json['price_name'], $json['version_name'], $json['processid'], $json['specid']);
                    unset($json['netName'], $json['vpcName'], $json['nterval_type']);
                    break;
            }
            
            $json['is_goods'] = '1';
            $result = $orders->ajaxFun($json);
            if ($result['Code'] != '0') {
                $j++;
            }
        }
        if ($j > 0) {
            return 0;die;
        }
        return 1;
    }

    /**
     * b/s工具 和 mpaas服务 一次性计费
     * @param  [goods] $table [description]
     * @return [type]        [description]
     */
    protected function _doChare($order_good_entity) 
    {
        
        //获取json 参数
        $str = $order_good_entity["instance_conf"];
        //转数组
        $json = json_decode($str, true);
        
        if (isset($json["good_type"]) && !empty($json["good_type"]) && $json["good_type"] == "bs"){
//             if(method_exists($this, '_chare_'.$json["good_type"])) {
//               $requset = call_user_func_array(array($this, '_chare_'.$json["good_type"]), array($order_good_entity));
//             }
            $orders_table = TableRegistry::get("Orders");
            $orders_data = $orders_table->find()->contain(['Account','Department'])->where(["Orders.id" => $order_good_entity["order_id"]])->first();
            //计算商品的价格
            $transaction_price  = $order_good_entity->transaction_price * $order_good_entity->num;
            $market_price   = $order_good_entity->transaction_price * $order_good_entity->num;
            
            $data =[
                'name'              => $order_good_entity["good_name"],
                'duration'          => $order_good_entity['units'],
                'resource_type'     => $order_good_entity['good']['goodType'],
                'buyer_id'          => $orders_data["account_id"],
                'buyer_name'        => $orders_data['account']["username"],
                'order_date'        => date('Y-m-d',$orders_data->create_time),
                'order_id'          => $order_good_entity["order_id"],
                'department_id'     => $orders_data["department_id"],
                'department_name'   => $orders_data['department']["name"],
                'price'             => $transaction_price,
                'market_price'      => $market_price
            ];
            $requset = Requests::post(Configure::read('Api.cmop').'/Bill/serviceCheckOut',[],$data,[
                    'verify'=>false
                ]);
            return 0;
        } elseif (isset($json["good_type"]) && !empty($json["good_type"]) && $json["good_type"] == "mpaas") {
            $mpaas_charge_table = TableRegistry::get("MpaasCharge");
            $charge_data = $mpaas_charge_table->newEntity();
            $charge_data->vendor_code = $json['service_brand'];
            $charge_data->consumption_subjects = $json['service_type'];
            $charge_data->dept_id = $json['tenantid'];
            $charge_data->order_id = $json['order_id'];
            $charge_data->maket_price = $json['price'];
            $charge_data->price = $order_good_entity['transaction_price'];
            $charge_data->interval = $json['interval_type'];
            $mpaas_charge_table->save($charge_data);
            return 0;
        } else {
            return 1;
        }
    } 

     //根据配置信息，创建JSON数据
    public function createJsonByVpcConfiguration($vpcid, $vpcInfo, $method, $uid)
    {
        //获取地区
        $goods_vpc  = TableRegistry::get('GoodsVpc');
        $_goodsVpc       = $goods_vpc->get($vpcid);
        $isFireWall = false;
        $time_now   = date('Y-m-d H:i:s', time());
        //必建立设备VPC、Router
        $vpc = array("cidr"=>$_goodsVpc["vpc_cidr"],"tag"=>"_vpc");
        $router = array("vpc_tag"=>"_vpc","tag"=>"_router");
        $subnet = array();
        $_subnetTag = "";
        $_isEip=false;
        $_isFireWall=false;
        $_isElb=false;
        foreach ($vpcInfo as $key => $value) {
            //'ecs','desktop','subnet','firewall','router'
            switch ($value["type"]) {
                case 'ecs':
                    if($value["number"]>1){
                        for ($i=1; $i <= $value["number"]; $i++) {
                            $hosts[] = array("tag"=>$value["tagname"]."[".$i."]","instanceTypeCode"=>$value["instance_code"],"imageCode"=>$value["image_code"],"vpc_tag"=>"_vpc","subnet_tag"=>$value["subnet_tag"],"subnetCode2"=>$value["subnet2_tags"]);
                        }
                    }else{
                        $hosts[] = array("tag"=>$value["tagname"],"instanceTypeCode"=>$value["instance_code"],"imageCode"=>$value["image_code"],"vpc_tag"=>"_vpc","subnet_tag"=>$value["subnet_tag"],"subnetCode2"=>$value["subnet2_tags"]);
                    }
                    break;
                case 'subnet':
                    $fusionType = "vmware";
                    if(!empty($value["is_fusion"])){
                        if($value["is_fusion"]=="true"){
                            $fusionType = "openstack";
                        }
                    }
                    $subnet[] = array("vpc_tag"=>"_vpc","cidr"=>$value["subnet_cidr"],"fusionType"=>$fusionType,"tag"=>$value["tagname"]);
                    break;
                case 'firewall':
                    $firewall = array("tag"=>$value["tagname"],"vpc_tag"=>"_vpc","instanceTypeCode"=>$value["instance_code"],"imageCode"=>$value["image_code"]);
                     $_isFireWall=true;
                    break;
                case 'elb':
                    $elb = array("tag"=>$value["tagname"],"vpc_tag"=>"_vpc","instanceTypeCode"=>$value["instance_code"],"imageCode"=>$value["image_code"],"subnet_tag"=>$value["subnet_tag"]);
                    $_isElb=true;
                    break;
            }
        }
        if($_isFireWall==true){
            foreach ($vpcInfo as $key => $vv) {

                if($vv["type"]=="desktop"){
                    if($vv["number"]>1){
                        for ($i=1; $i <= $vv["number"]; $i++) {
                            $desktop[] = array("tag"=>$vv["tagname"]."[".$i."]","instanceTypeCode"=>$vv["instance_code"],"imageCode"=>$vv["image_code"],"vpc_tag"=>"_vpc","subnet_tag"=>$vv["subnet_tag"],"subnetCode2"=>$vv["subnet2_tags"]);
                        }
                    }else{
                        $desktop[] = array("tag"=>$vv["tagname"],"instanceTypeCode"=>$vv["instance_code"],"imageCode"=>$vv["image_code"],"vpc_tag"=>"_vpc","subnet_tag"=>$vv["subnet_tag"],"subnetCode2"=>$vv["subnet2_tags"]);
                    }

                }

            }
        }
        if(empty($desktop)){
            $desktop=array();
        }
        if(empty($hosts)){
            $hosts=array();
        }
        //获取存储相关信息
        //1.先获取存储卷信息,通过一键VPCID，获取对应存储信息
        $vpc_store_extend_table  = TableRegistry::get('VpcStoreExtend');
        $store_data = $vpc_store_extend_table->find("all")->where(array('vpcId'=>$vpcid))->toArray();
        foreach ($store_data as $key => $value) {
            $store[] = array("tag"=>$value["vol_name"],"storeCode"=>$value["store_code"],"storeType"=>$value["vol_type"],"volume_name"=>$value["vol_name"],"total_cap"=>(string)$value["total_cap"],"warn_level"=>(string)$value["warn_cap"],"regionCode"=>$value["region_code"]);
        }
        //2.获取存储卷对应用户信息
        //3.获取存储卷对应用户权限信息
        $vpc_store_user_table  = TableRegistry::get('VpcFicsUsers');
        $user_data = $vpc_store_user_table->getUserLimitByVpcId($vpcid);

        foreach ($user_data as $key => $value) {
            $user[] = array("store_tag"=>$value["vol_name"],'regionCde'=>$value["region_code"],'name'=>$value["name"],'password'=>$value["password"],'storeType'=>$value["storetype"],'storeCode'=>$value["store_code"],'volume_name'=>$value["vol_name"],'limit'=>$value["limit"]);
        }
        if(empty($store)){
            $store=array();
        }
        if(empty($user)){
            $user=array();
        }
        if($_isFireWall==true){
            $vpcData = array("uid" => (string) $uid, "method" => $method,"regionCode"=>$_goodsVpc["region_code"],"vpc"=>$vpc,"router"=>$router,"subnet"=>$subnet,"firewall"=>$firewall,"hosts"=>$hosts,"desktop"=>$desktop,"store"=>$store,"user"=>$user,"eip"=>array());
        }else{
            $vpcData = array("uid" => (string) $uid, "method" => $method,"regionCode"=>$_goodsVpc["region_code"],"vpc"=>$vpc,"router"=>$router,"subnet"=>$subnet,"hosts"=>$hosts,"desktop"=>$desktop,"store"=>$store,"user"=>$user,"eip"=>array());
        }
        if($_isElb==true){
            $vpcData["elb"]=$elb;
        }
        // debug($vpcData);die();
        $vpcData               = json_encode($vpcData, true);
        // debug($vpcData);
        $json_str = array("uid" => (string) $uid, "method" => $method,"regionCode"=>$_goodsVpc["region_code"],"vpcData"=>$vpcData);
        return $json_str;
    }

    /**
     * 获取接口中传递过来的参数。
     * 支持form提交以及body提交
     * @return Ambigous <unknown, string, multitype:>
     */
    private function _getData()
    {
        $data = $this->request->data ? $this->request->data : file_get_contents('php://input', 'r');

        //处理非x-form的格式
        if (is_string($data)) {
            $data_tmp = json_decode($data, true);
            if (json_last_error() == JSON_ERROR_NONE) {
                $data = $data_tmp;
            }
        }
        //日志
        //Log::debug("Data Posted :".json_encode($data),['action'=>$this->request->params['action'],'host'=>$this->request->host()]);
        return $data;
    }

    /*
     *修改订单商品数量
     *ajax提交数据
     */
    public function editGoodNum()
    {
        $message                   = array('code' => 1, 'msg' => '操作失败');
        $orders_goods_table        = TableRegistry::get('OrdersGoods');
        $orders_process_flow_table = TableRegistry::get('OrdersProcessFlow');
        $order_id                  = $orders_goods_table->find()->where(['id' => $this->_data['order_goods_id']])->first();

        $process_flow_last_data = $orders_process_flow_table->find()->where(['OrdersProcessFlow.order_id' => $order_id['order_id']])->order(['OrdersProcessFlow.id DESC'])->first();
        if ($process_flow_last_data['flow_detail_name'] == '结束') {
            $message = array('code' => 1, 'msg' => '订单已完成不能修改商品数量');
            echo json_encode($message);exit;
        }
        $order_good_data = $orders_goods_table->find()->where(['id' => $this->_data['order_goods_id']])->first();
        if (!empty($order_good_data['price_per'])) {
            $price_total = $order_good_data['price_per'] * $this->_data['num'];
        }
        $orders_goods              = $orders_goods_table->newEntity();
        $orders_goods->num         = $this->_data['num'];
        $orders_goods->id          = $this->_data['order_goods_id'];
        $orders_goods->price_total = $price_total;
        $res                       = $orders_goods_table->save($orders_goods);
        if ($res) {
            // $public->adminlog('Agent','删除厂商或者区域---'.$data['display_name']);
            $message = array('code' => 0, 'msg' => '操作成功');
        }
        echo json_encode($message);exit;

    }

    /*
     *删除订单商品
     *ajax提交数据
     */
    public function deleOrderGood()
    {

        $message                   = array('code' => 1, 'msg' => '操作失败');
        $orders_goods_table        = TableRegistry::get('OrdersGoods');
        $orders_process_flow_table = TableRegistry::get('OrdersProcessFlow');
        $count                     = $orders_goods_table->find()->where(['order_id' => $this->_data['order_id']])->count();
        if ($count <= 1) {
            $message = array('code' => 1, 'msg' => '购买的商品不能为空');
            echo json_encode($message);exit;
        }
        $order_id               = $orders_goods_table->find()->where(['id' => $this->_data['order_goods_id']])->first();
        $process_flow_last_data = $orders_process_flow_table->find()->where(['OrdersProcessFlow.order_id' => $order_id['order_id']])->order(['OrdersProcessFlow.id DESC'])->first();
        if ($process_flow_last_data['flow_detail_name'] == '结束') {
            $message = array('code' => 1, 'msg' => '订单已完成不能删除商品');
            echo json_encode($message);exit;
        }
        $orders_goods = $orders_goods_table->newEntity();
        $id           = $this->_data['order_goods_id'];
        $res          = $orders_goods_table->deleteAll(array('id' => $id));
        if ($res) {
            // $public->adminlog('Agent','删除厂商或者区域---'.$data['display_name']);
            $message = array('code' => 0, 'msg' => '操作成功');
        }
        echo json_encode($message);exit;

    }

    /*
    *拼接邮箱数据
    */
    public function email ($id, $status, $detail_id, $is_send)
    {
       // debug($id);
        $orders_table = TableRegistry::get('Orders');
        $orders_goods_table = TableRegistry::get('OrdersGoods');
        $workflow_detail_table = TableRegistry::get('WorkflowDetail');

        $order_data = $orders_table->find()->where(['Orders.id'=>$id])->contain(['OrdersGoods','Account'])->first();
        $current_step = $workflow_detail_table->find()->where(['id' => $detail_id])->first();
        $next_step = $workflow_detail_table->find()->where(['parent_id' => $detail_id])->first();
        // debug(json_encode($order_data));die;
        if($is_send == 1 || $is_send == 3){
            $email['email'] = $order_data['account']['email'];
            $email['body'] = '亲爱的用户，您的订单' . $order_data['number'] . '，购买的';
            foreach ($order_data['orders_goods'] as $key => $good) {
                $email['body'] .= $good['good_name'].' ';
            }
            if($status >=0){
                $email['title'] = '审批通过';
                $email['body'] .= '已通过'.$current_step['step_name'].'步骤。';
            }else{
                $email['title'] = '审批未通过';
                $email['body'] .= '未通过'.$current_step['step_name'].'步骤。';
            }

            $this->_sendEmail($email);
        }

        // debug($next_step);die;
        if($status >=0){
            if(!empty($next_step['send_email'])){
                if($next_step['send_email']>=2){

                    $connection = ConnectionManager::get('default');
                    $sql = "SELECT a.email";
                    $sql .= " FROM cp_accounts a";
                    $sql .= " LEFT JOIN cp_roles_accounts r_a ON r_a.account_id = a.id";
                    $sql .= " LEFT JOIN cp_roles_popedoms r_p ON r_a.role_id = r_p.role_id";
                    $sql .= " LEFT JOIN cp_popedomlist p ON p.popedomid = r_p.popedomlist_id";
                    $sql .= " WHERE p.popedomname = '".$next_step['step_popedom_code']."' AND a.department_id =".$order_data['department_id'];
                    $sql .= " GROUP BY a.email";
                    // debug($sql);die;
                    $emails = $connection->execute($sql)->fetchAll('assoc');
                    // debug($emails);die;
                    if(!empty($emails)){
                        foreach ($emails as $key => $a_e) {
                            $email['email'] = $a_e['email'];
                            $email['body'] = '亲爱的管理员，有新的订单等待你审核，订单号为：' . $order_data['number'] . '，' . $order_data['account']['username'] . '购买的';
                            foreach ($order_data['orders_goods'] as $key => $g) {
                                $email['body'] .= $g['good_name'] . ' ';
                            }
                            $email['body'] .= '。';
                            $email['title'] = '等待审核';
                            $this->_sendEmail($email);
                        }
                    }
                }
            }
        }
    }


    /**
    * 发送邮件
    * @date: 2016年5月11日 下午2:23:25
    * @author: wangjc
    * @return:
    */
    private function _sendEmail($_email){
        $data="";
        $msg="调用成功";
        $result = array('code'=>1,'msg'=>'操作失败');
        $http = new Client();
        $message = array();
        $message['url'] = Configure::read('Message.url');
        $message['email'] = $_email['email'];
        $message['title'] = $_email['title'];
        $message['emailbody']=$_email['body'];
        $message['sendtype'] = 'email';
        //debug($message);die;
        for ($i=0; $i <3; $i++) {
            $obj_response = $http->post($message['url'],$message, array('type' => 'json'));
            $data_response = json_decode($obj_response->body, true);
                // debug($obj_response->body);die;
            $code = '1';
            if(count($data_response)==1){
                if($data_response['email']['code']==0){
                    $code = '0';
                    break;
                }else{
                    //debug($data_response)
                    if (intval($data_response)){
                        $log ="sendEmail方法调用:\n";
                        $log.="错误信息:".$data_response['email']['msg']."\n";
                        Log::debug($log);
                    }
                }
            }
        }
        return compact(array_values($this->_serialize));
    }

}
