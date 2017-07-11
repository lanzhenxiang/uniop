<?php
/**
* 文件描述文字
*
*
* @author XingShanghe<xingshanghe@gmail.com>
* @date  2015年9月8日下午2:19:09
* @source HomeController.php
* @version 1.0.0
* @copyright  Copyright 2015 sobey.com
*/

namespace App\Controller\Console;

use App\Controller\Console\ConsoleController;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Network\Http\Client;

use Composer\Autoload\ClassLoader;
use Requests as Requests;


class HomeController extends ConsoleController
{

    public $paginate = [
    'limit' => 15,
    ];
    private $_http;

    public function initialize()
    {
        parent::initialize();
        //$this->db_conn = ConnectionManager::get('default');
        $this->_http  = new Client();
        $loader = new ClassLoader();
        $loader->add('Requests', ROOT.DS.'vendor/requests');
        $loader->register();
        Requests::register_autoloader();
    }
    private function _checkPopedom($param)
    {
        $popedom = parent::checkConsolePopedom($param);
        return $popedom;
    }

    public function display($subject = 'index',$tab = 0 )
    {
        //$this->layout = false;
        $path = func_get_args();

        if (!$subject) {
            return $this->redirect('/');
        }
        $this->set(compact('title','tab'));

        $this->autoRender = false;
        try {
            $this->render($subject);
        } catch (MissingTemplateException $e) {
            if (Configure::read('debug')) {
                throw $e;
            }
            throw new NotFoundException();
        }
    }


    public function test()
    {
        $goods = TableRegistry::get('ConsoleCategoryTable');

        //$a = $goods->get(1,['cache'=>'memcache'])->toArray();
        //debug($a);

        $b = $goods->find('all')->where(['id'=>1])->toArray();
        //debug($a);
        debug($b);

    }

    public static function initProducts()
    {
        $products=Configure::read('products');
        return $products;
    }

    /*
     * 默认主页函数
     */
    public function index(){

        $id = $this->request->session()->read('Auth.User.id') ? $this->request->session()->read('Auth.User.id') : 0;
        $department_id = $this->request->session()->read('Auth.User.department_id');
        //租户配额，组合资源使用量
        $bugedt = Requests::post(Configure::read('Api.cmop').'/SystemInfo/getDepartBuget',[],[
         'userid'=>$id,
         'department_id'=>$department_id
         ],[
         'verify'=>false
         ]);
        $bugedt_arr = json_decode(trim($bugedt->body, chr(239) . chr(187) . chr(191)), true); //所以问题来了，不小心在返回的json字符串中返回了BOM头的不可见字符，某些编辑器默认会加上BOM头，如下处理才能

        $used = Requests::post(Configure::read('Api.cmop') . '/SystemInfo/getDepartUsed', [], [
            'userid' => $id,
            'department_id'=>$department_id,
            "source_type" => "cpu_used,memory_used,gpu_used,router_used,subnet_used,disks_used,fics_num_used,fics_cap_used,oceanstor9k_num_used,oceanstor9k_cap_used,basic_used,fire_used,elb_used,eip_used"
        ], [
            'verify' => false
        ]);

        $used_arr = json_decode(trim($used->body, chr(239) . chr(187) . chr(191)), true); //所以问题来了，不小心在返回的json字符串中返回了BOM头的不可见字符，某些编辑器默认会加上BOM头，如下处理才能


        $_time = time();

        $y = date("Y", $_time);//年
        $m = date("m", $_time);//月
        $start = $y . '-' . $m . '-01';

        $_start_time = strtotime($start);

        $end = date("Y-m-d", $_time + 86400);

        $_end_time = strtotime($end) + 86400;

        // 所属部门id
        $department_id = $this->request->session()->read('Auth.User.department_id') ? $this->request->session()->read('Auth.User.department_id') : 0;

        $bill_base_table = TableRegistry::get('BillBase');
        //获取饼图数据
        $pie_chart_data     = $bill_base_table->getPieChartData($start,$end,$department_id);
        //饼图消费总金额
        $pie_total_amount   = collection($pie_chart_data)->sumOf('cost'); 

        $instance_logs_table = TableRegistry::get('InstanceLogs');

        $instance_logs_data = $instance_logs_table->find()->contain(['Accounts','InstanceBasic'])->where(['Accounts.department_id'=>$department_id])->order(['InstanceLogs.create_time   DESC'])->limit(7)->offset(0)->toArray();

        $instance_logs_sum = $instance_logs_table->find()->contain(['Accounts','InstanceBasic'])->where(['Accounts.department_id'=>$department_id])->count();

        $this->set('mon_charge',$pie_chart_data);
        $this->set('y',$y);
        $this->set('m',$m);
        $this->set('sum',$pie_total_amount);

        $this->set('budget',$bugedt_arr['data']);
        $this->set('used',$used_arr['data']);

        $this->set('instance_logs_data',$instance_logs_data);
        $this->set('instance_logs_sum',$instance_logs_sum);

    }

    /**
     * 用户中心--查看订单
     * @date: 2016年3月14日 上午11:25:31
     * @author: wangjc
     * @access public
     *
     */
    public function order($status=0,$flow_id=0,$start=0,$end=0,$search='') {
        $limit=10;$offset=0;
        $checkPopedomlist = $this->_checkPopedom('ccm_user_myorder');
        if (! $checkPopedomlist)
        {
            return $this->redirect('/');
        }

        if ($offset>0) {
            $offset = $offset-1;
        }
        $offset =  $offset*$limit;

        $id = $this->request->session()->read('Auth.User.id');
        // $response = Requests::post(Configure::read('Api.cmop').'/SystemInfo/getDepartBuget',[],[
        //  'userid'=>$id,
        //  ],[
        //  'verify'=>false
        //  ]);
        // $response_arr = json_decode($response->body,true);

        // $requset = Requests::post(Configure::read('Api.cmop').'/SystemInfo/getDepartUsed',[],[
        //     'userid'=>$id,
        //     "source_type"=>"cpu_used,router_used,subnet_used,disks_used",
        //     ],[
        //     'verify'=>false
        //     ]);
        // $requset_arr = json_decode($requset->body,true);

        $orders = TableRegistry::get('Orders');
        $Agent = TableRegistry::get('Agent');
        $Imagelist = TableRegistry::get('Imagelist');
        $GoodsSpec = TableRegistry::get('GoodsSpec');
        $Goods = TableRegistry::get('Goods');
        $WorkflowTemplate = TableRegistry::get('WorkflowTemplate');
        $WorkflowDetail = TableRegistry::get('WorkflowDetail');

        $workflow_template_data = $WorkflowTemplate->find('all')->toArray();


        $where = array();

        if ($this->_checkPopedom('cmop_global_sys_admin')||$this->_checkPopedom('cmop_global_tenant_admin')) {
            $where['Orders.department_id'] = $this->request->session()->read('Auth.User.department_id');
        }else{
            $where['Orders.account_id'] = $this->request->session()->read('Auth.User.id');
        }

        // $where =" AND cp_orders.department_id = ".$this->request->session()->read('Auth.User.department_id');
        if (! empty($search)) {
            $where["Orders.number like"] = "%" . $search . "%";
        }

        if($start !=0 ){
            $start_time=strtotime($start);
            $where['Orders.create_time >='] = $start_time;
        }
        if($end !=0 ){
            $end_time=strtotime($end)+86400;
            $where['Orders.create_time <='] = $end_time;
        }

        if($flow_id !=0 ){
            $where['Orders.flow_id'] = $flow_id;
            $workflow_detail_data = $WorkflowDetail->find()->where(['flow_id' => $flow_id])->toArray();
            $flow = $WorkflowTemplate->find()->where(['flow_id' => $flow_id])->first();
            // var_dump($workflow_detail_data);exit;
            $this->set('flow',$flow);
            $this->set('detail',$workflow_detail_data);
        }
        if($status!=0){
            // $where .=" AND cp_orders.`status` = ".$status;
            $where['Orders.status'] = $status;
            $step = $WorkflowDetail->find()->where(['lft' => $status,'flow_id' => $flow_id])->first();
            $this->set('step',$step);
        }

        $orderinfo = $orders->find('all')->contain(['OrdersGoods','Account','WorkflowDetail'])->where(['is_console' => 0])->where($where)->order(['Orders.create_time DESC'])->limit($limit)->offset($offset)->toArray();
        // var_dump($orderinfo[0]['workflow_detail']);exit;
        // $count = $orders->find('all')->contain(['OrdersGoods','Account'])->where(['is_console' => 0])->where($where)->count();
        // var_dump($orderinfo[0]);exit;

        // $connection = ConnectionManager::get('default');
        // $sql = "SELECT cp_orders.*, B.good_id AS ordersgoodsid, B.good_name AS ordersgoodsname, B.goods_snapshot AS ordersgoodslist, B.num AS ordersgoodsnum, C.id AS userid, C.username AS username FROM cp_orders";
        // $sql .=" LEFT JOIN `cp_orders_goods` AS B ON cp_orders.id =B.order_id LEFT JOIN `cp_accounts` AS C  ON cp_orders.account_id = C.id";

        // $sql .=" WHERE B.is_console = 0".$where;
        // $sql .=" ORDER BY cp_orders.create_time DESC";
        // $sql_row =$sql." limit ".$limit." OFFSET ".$offset;

        // $orderinfo = $connection->execute($sql_row)->fetchAll('assoc');
        // $count = $connection->execute($sql)->count();

        // $info['count'] = $count;

        // $page = (int)ceil($info['count']/$limit);
        // $info['page'] = $page;

        $info = array();
        foreach ($orderinfo as $key => $order_info)
            {   $order_info = $order_info->toArray();
            $info['orderinfo'][$key]=$order_info;//订单信息
            foreach ($order_info['orders_goods'] as $k => $order_good_info) {

                if (!empty($order_good_info['good_id'])) {

                    // var_dump($order_good_info['good_id']);exit;
                    $GoodsSpecinfo = $GoodsSpec->find('all')->where(array('goods_id' => $order_good_info['good_id'] ))->toArray();//商品详细信息

                    $good_info = $good_info = $Goods->getGoodsInfo(['id' => $order_good_info['good_id']],['name','mini_icon']);//商品信息
                    if(!empty($GoodsSpecinfo)){
                        // var_dump($info['orderinfo'][0]);exit;
                        foreach ($GoodsSpecinfo as $specinfo) {

                            if($specinfo['spec_name']=='软件版本'){
                                $info['orderinfo'][$key]['goodinfo'][$k]['version']=$specinfo['spec_value'];

                            }else if($specinfo['spec_name']=='机房位置'){
                                $info['orderinfo'][$key]['goodinfo'][$k]['labs']=$specinfo['spec_value'];

                            }else if($specinfo['spec_name']=='软件厂商'){
                                $info['orderinfo'][$key]['goodinfo'][$k]['activision']=$specinfo['spec_value'];

                            }else if($specinfo['spec_code']=='gpu'){
                                $info['orderinfo'][$key]['goodinfo'][$k]['gpu']=$specinfo['spec_value'];

                            }else if($specinfo['spec_code']=='cpu'){
                                $info['orderinfo'][$key]['goodinfo'][$k]['cpu']=$specinfo['spec_value'];

                            }else if($specinfo['spec_code']=='memory'){
                                $info['orderinfo'][$key]['goodinfo'][$k]['rom']=$specinfo['spec_value'];

                            }else if($specinfo['spec_name']=='操作系统'){
                                $info['orderinfo'][$key]['goodinfo'][$k]['OS']=$specinfo['spec_value'];

                            }else if($specinfo['spec_code']=='imageCode'){
                                $info['orderinfo'][$key]['goodinfo'][$k]['image']=$specinfo['spec_value'];

                            }else if($specinfo['spec_code']=='instanceType'){
                                $info['orderinfo'][$key]['goodinfo'][$k]['instance']=$specinfo['spec_value'];

                            }
                        }
                    }
                    if(!empty($good_info['mini_icon'])){
                        $info['orderinfo'][$key]['goodinfo'][$k]['mini_icon'] = $good_info['mini_icon'];
                    }
                    if(!empty($good_info['name'])){
                        $info['orderinfo'][$key]['goodinfo'][$k]['name'] = $good_info['name'];

                    }

                }
            }
        }
        // $this->set('query',$response_arr['data']);
        // $this->set('data',$requset_arr['data']);
        $this->set('info',$info);
        $this->set('search',$search);
        $this->set('start',$start);
        $this->set('end',$end);
        $this->set('status',$status);
        $this->set('flow_id',$flow_id);
        $this->set('template',$workflow_template_data);
    }

    public function getUserLimit()
    {
        $id = $this->request->session()->read('Auth.User.id');
        //获取创建资源的租户id
        $department_id = $this->getOwnByDepartmentId();
        $response = Requests::post(Configure::read('Api.cmop').'/SystemInfo/getDepartBuget',[],
           [
           'userid'=>$id,
           'department_id'=>$department_id
           ],['verify'=>false]);
        $limit = json_decode(trim($response->body,chr(239).chr(187).chr(191)),true);//所以问题来了，不小心在返回的json字符串中返回了BOM头的不可见字符，某些编辑器默认会加上BOM头，如下处理才能
        $requset = Requests::post(Configure::read('Api.cmop').'/SystemInfo/getDepartUsed',[],
            [
            'userid'=>$id,
            'department_id'=>$department_id,
//            "source_type"=>"cpu_used,router_used,subnet_used,disks_used",
                "source_type"=>"cpu_used,router_used,subnet_used,disks_used,gpu_used,memory_used,fics_num_used,oceanstor9k_num_used,basic_used,fire_used,elb_used,eip_used"
            ],['verify'=>false]);
        $used = json_decode(trim($requset->body,chr(239).chr(187).chr(191)),true);
        //所以问题来了，不小心在返回的json字符串中返回了BOM头的不可见字符，某些编辑器默认会加上BOM头，如下处理才能
        if (empty($used['data'])) {
            $used = array('0');
        }else{
            $used = $used['data'];
        }
//        debug($limit);
//        debug($used);
        echo json_encode(array_merge($limit,$used));exit();
    }



    public function getUserOrderInfo(){
        $id = $this->request->session()->read('Auth.User.id');
        $orders = TableRegistry::get('Orders',['classname'=>'App\Model\Table\OrdersTable']);
        $orderinfo = $orders->find('all')->contain(['OrdersGoods'])->where($where)->toArray();
        var_dump($orderinfo);exit;
    }

    public function getOrderInfo($limit=10,$offset=0,$status=0,$flow_id=0,$start=0,$end=0,$search=''){
        if ($offset>0) {
            $offset = $offset-1;
        }
        $offset =  $offset*$limit;

        $orders = TableRegistry::get('Orders');
        $Agent = TableRegistry::get('Agent');
        $Imagelist = TableRegistry::get('Imagelist');
        $GoodsSpec = TableRegistry::get('GoodsSpec');
        $Goods = TableRegistry::get('Goods');
        $where = array();
        if (! empty($search)) {
            $where["Orders.number like"] = "%" . $search . "%";
        }
        if ($this->_checkPopedom('cmop_global_sys_admin')||$this->_checkPopedom('cmop_global_tenant_admin')) {
            $where['Orders.department_id'] = $this->request->session()->read('Auth.User.department_id');
        }else{
            $where['Orders.account_id'] = $this->request->session()->read('Auth.User.id');
        }
        // $where =" AND cp_orders.department_id = ".$this->request->session()->read('Auth.User.department_id');
        // if($status!=0){
        //     // $where .=" AND cp_orders.`status` = ".$status;
        //     $where['Orders.status'] = $status;
        // }else{
        //     // $where .=" AND cp_orders.`status` <> 0 ";
        //     $where['Orders.status <>'] = 0;
        // }
        if($start !=0 ){
            $start_time=strtotime($start);
            $where['Orders.create_time >='] = $start_time;
        }
        if($end !=0 ){
            $end_time=strtotime($end)+86400;
            $where['Orders.create_time <='] = $end_time;
        }
        if($flow_id !=0 ){
            $where['Orders.flow_id'] = $flow_id;
        }
        if($status!=0){
            // $where .=" AND cp_orders.`status` = ".$status;
            $where['Orders.status'] = $status;
        }

        $orderinfo = $orders->find('all')->contain(['OrdersGoods','Account','WorkflowDetail'])->where(['is_console' => 0])->where($where)->order(['Orders.create_time DESC'])->limit($limit)->offset($offset)->toArray();
    // $count = $orders->find('all')->contain(['OrdersGoods','Account'])->where(['is_console' => 0])->count();
        // var_dump($orderinfo[0]);exit;

        // $connection = ConnectionManager::get('default');
        // $sql = "SELECT cp_orders.*, B.good_id AS ordersgoodsid, B.good_name AS ordersgoodsname, B.goods_snapshot AS ordersgoodslist, B.num AS ordersgoodsnum, C.id AS userid, C.username AS username FROM cp_orders";
        // $sql .=" LEFT JOIN `cp_orders_goods` AS B ON cp_orders.id =B.order_id LEFT JOIN `cp_accounts` AS C  ON cp_orders.account_id = C.id";

        // $sql .=" WHERE B.is_console = 0".$where;
        // $sql .=" ORDER BY cp_orders.create_time DESC";
        // $sql_row =$sql." limit ".$limit." OFFSET ".$offset;

        // $orderinfo = $connection->execute($sql_row)->fetchAll('assoc');
        // $count = $connection->execute($sql)->count();

    // $info['count'] = $count;

    // $page = (int)ceil($info['count']/$limit);
    // $info['page'] = $page;

        $info = array();
        foreach ($orderinfo as $key => $order_info)
        {
            $order_info = $order_info->toArray();
            $info['orderinfo'][$key]=$order_info;//订单信息
            foreach ($order_info['orders_goods'] as $k => $order_good_info) {

                if (!empty($order_good_info['good_id'])) {

                    // var_dump($order_good_info['good_id']);exit;
                    $GoodsSpecinfo = $GoodsSpec->find('all')->where(array('goods_id' => $order_good_info['good_id'] ))->toArray();//商品详细信息
                    $good_info = $Goods->find()->select(['name','mini_icon'])->where(array('id' => $order_good_info['good_id'] ))->first();//商品信息
                    if(!empty($GoodsSpecinfo)){
                        // var_dump($info['orderinfo'][0]);exit;
                        foreach ($GoodsSpecinfo as $specinfo) {

                            if($specinfo['spec_name']=='软件版本'){
                                $info['orderinfo'][$key]['goodinfo'][$k]['version']=$specinfo['spec_value'];

                            }else if($specinfo['spec_name']=='机房位置'){
                                $info['orderinfo'][$key]['goodinfo'][$k]['labs']=$specinfo['spec_value'];

                            }else if($specinfo['spec_name']=='软件厂商'){
                                $info['orderinfo'][$key]['goodinfo'][$k]['activision']=$specinfo['spec_value'];

                            }else if($specinfo['spec_code']=='gpu'){
                                $info['orderinfo'][$key]['goodinfo'][$k]['gpu']=$specinfo['spec_value'];

                            }else if($specinfo['spec_code']=='cpu'){
                                $info['orderinfo'][$key]['goodinfo'][$k]['cpu']=$specinfo['spec_value'];

                            }else if($specinfo['spec_code']=='memory'){
                                $info['orderinfo'][$key]['goodinfo'][$k]['rom']=$specinfo['spec_value'];

                            }else if($specinfo['spec_name']=='操作系统'){
                                $info['orderinfo'][$key]['goodinfo'][$k]['OS']=$specinfo['spec_value'];

                            }else if($specinfo['spec_code']=='imageCode'){
                                $info['orderinfo'][$key]['goodinfo'][$k]['image']=$specinfo['spec_value'];

                            }else if($specinfo['spec_code']=='instanceType'){
                                $info['orderinfo'][$key]['goodinfo'][$k]['instance']=$specinfo['spec_value'];

                            }
                        }
                    }
                    if(!empty($good_info['mini_icon'])){
                        $info['orderinfo'][$key]['goodinfo'][$k]['mini_icon'] = $good_info['mini_icon'];
                    }
                    if(!empty($good_info['name'])){
                        $info['orderinfo'][$key]['goodinfo'][$k]['name'] = $good_info['name'];
                    }

                }
            }
        }

        echo json_encode($info);exit;
    }

    public function delivery ($id,$status){
        $orders = TableRegistry::get('Orders');
        $message = array('code'=>1,'msg'=>'操作失败');
        $data['id'] = $id;
        $data['status'] = $status;
        $order = $orders->newEntity();
        $order = $orders->patchEntity($order,$data);
        $result = $orders->save($order);
        if($result){
            $message = array('code'=>0,'msg'=>'操作成功');
        }
        echo json_encode($message);exit();
    }
}