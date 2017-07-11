<?php
/**
 * 文件用途描述
 *
 * @file: OrdersController.php
 * @date: 2016年1月21日 下午4:44:52
 * @author: xingshanghe
 * @email: xingshanghe@icloud.com
 * @copyright poplus.com
 *
 */

namespace App\Controller\Console;

use App\Controller\Console\ConsoleController;
use Cake\ORM\TableRegistry;
use Cake\Network\Exception\NotFoundException;
use Cake\Error\FatalErrorException;
use Cake\Core\Configure;

class OrdersController extends ConsoleController
{

    //后台菜单
    protected function _get_admin_menu()
    {
        //获取用户权限
        $popedomname = $this->request->session()->read('Auth.User.popedomname') ? $this->request->session()->read('Auth.User.popedomname') : [];

        $admin_memu = TableRegistry::get('AdminMenu');
        return $admin_memu->find('tree')->where(['visibility'=>'1', 'popedom_code in' => $popedomname])->order('sort asc')->toArray();
    }
    public function admin_menu(){
        $checkPopedomlist = parent::checkPopedomlist('cmop_global_sys_admin');
        if (! $checkPopedomlist) {
            return $this->redirect('/');
        }
        $this->loadComponent('RequestHandler');
        $this->set('_admin_menu',$this->_get_admin_menu());
        $this->set('notifyUrl',Configure::read('NotifyUrl'));
    }
    /**
     *@author wangjc
     * 待审核订单列表
     */
    public function lists($start= -1,$end= -1,$department_id = 'all')
    {
        //判断资源中心还是后台
        $file_position=isset($this->request->query['file_position'])?$this->request->query['file_position']:'console';
        if(isset($file_position)&&$file_position=='admin'){
            $this->admin_menu();
            $this->layout='default_admin';
        }

        if(!in_array('ccm_user_lists',$this->request->session()->read('Auth.User.popedomname'))) {
            $this->redirect('/console');
        }
        $search = isset($this->request->query['search']) ? trim($this->request->query['search']) : '';
        $create_by = isset($this->request->query['create_by']) ? trim($this->request->query['create_by']) : '';
        //注意权限

        $orders_process_flow_table = TableRegistry::get('OrdersProcessFlow');
        $workflow_detail_table = TableRegistry::get('WorkflowDetail');
        $orders_table = TableRegistry::get('Orders');
        $department_table = TableRegistry::get('Departments');

        $pop = $this->request->session()->read('Auth.User.popedomname');#获取权限

        $detail_info = $workflow_detail_table->find() ->where(['step_popedom_code IN' =>$pop])->toArray();#获取权限对应审核步骤
        foreach ($detail_info as $key => $value) {
            $user_detail_info[$value['flow_id']][] = $value['lft']-1;#获取待审核步骤前一步的lft
        }
        $info=array();
        $where_or = array();
        if (!empty($user_detail_info)) {
            foreach ($user_detail_info as $_k => $_v) {
                $where['Orders.flow_id'] = $_k;
                $where['Orders.status IN'] = $_v;
                $where_and['AND'] = $where;
                $where_or['OR'][]= $where_and;
            }
        }

        $departments = $department_table->find()->select(['id','name'])->toArray();
        if($department_id !='all' && $department_id > 0 ){
            $where_or['Orders.department_id'] = $department_id;
            $department = $department_table->get($department_id);
        }else{
            $department['id'] = 0;
            $department['name'] = '全部';
        }


        //增加status!= -1;
        $where_or['Orders.status <>'] = -1;
        // debug($where_or);exit;
        $query = $orders_table->find('all')
            ->find('Displayed')
            ->find('BetweenCreateTime',['start'=>$start,'end'=>$end])
            ->find('NumberLike',['number' => $search])
            ->contain(['OrdersGoods','Account','Department','WorkflowDetail'])
            ->where($where_or)
            ->matching('OrdersGoods',function($q){
                return $q->where(['OrdersGoods.good_id >'=>0]);
            })
            ->matching('Account',function($q) use($create_by){
                if($create_by != ''){
                    return $q->where(['username like'=>'%'.$create_by.'%']);
                }else {
                    return $q;
                }
            })
            ->order(['Orders.create_time DESC'])/*->toArray()*/;
        $data = $this->paginate($query);
        $this->set('create_by',$create_by);
        $this->set('department',$department);
        $this->set('departments',$departments);
        $this->set('data',$data);
        $this->set('start',$start);
        $this->set('end',$end);
        $this->set('search',$search);
        $this->set('list_title',"待审订单");
    }


    /**
     * @author wangjc
     * 已审核订单列表
     * @param $start:开始时间,$end:结束时间,$search:搜索信息
     */
    public function listsed($start= -1,$end= -1,$process_status = 0)
    {
        //判断资源中心还是后台
        $file_position=isset($this->request->query['file_position'])?$this->request->query['file_position']:'console';
        if(isset($file_position)&&$file_position=='admin'){
            $this->admin_menu();
            $this->layout='default_admin';
        }

        if(!in_array('ccm_user_listsed',$this->request->session()->read('Auth.User.popedomname'))) {
            $this->redirect('/console');
        }

        $search = isset($this->request->query['search']) ? trim($this->request->query['search']) : '';

        $orders_table = TableRegistry::get('Orders');
        $department_table = TableRegistry::get('Departments');

        //增加status = -1;
        //当前用户处理过的流程
        $where_or['process_flow.user_id'] = $this->request->session()->read('Auth.User.id');
        $query = $orders_table->find('all')
            ->find('Displayed')
            ->find('BetweenCreateTime',['start'=>$start,'end'=>$end])
            ->find('NumberLike',['number' => $search])
            ->contain(['OrdersGoods','Department','Account','WorkflowDetail'])
            ->join([
                "process_flow"=>[
                    "table" =>"cp_orders_process_flow",
                    "type"  =>"LEFT",
                    "conditions" =>"process_flow.order_id = Orders.id"
                ]

            ])
            ->matching('OrdersGoods',function($q){
                return $q->where(['OrdersGoods.good_id >'=>0]);
            })
            ->matching('WorkflowDetail',function($q) use($process_status){
                if($process_status == -1){
                    return $q->where(['step_code !='=>'end']);
                }else if($process_status == 1){
                    return $q->where(['step_code'=>'end']);
                }
                return $q;
            })
            ->where($where_or)->order(['Orders.create_time DESC'])->group("Orders.id");
        $data = $this->paginate($query);

        $this->set(compact(['data','start','end','search','process_status']));
        $this->set('list_title',"已审订单");
    }

    /**
     * 我的订单列表
     * @param  integer $start          [开始时间]
     * @param  integer $end            [结束时间]
     * @param  integer $process_status [审核状态]
     */
    public function ownlists($start= -1,$end= -1,$process_status = 0)
    {
        //判断资源中心还是后台
        $file_position=isset($this->request->query['file_position'])?$this->request->query['file_position']:'console';
        if(isset($file_position)&&$file_position=='admin'){
            $this->admin_menu();
            $this->layout='default_admin';
        }

        if(!in_array('ccm_user_myorder',$this->request->session()->read('Auth.User.popedomname'))) {
            $this->redirect('/console');
        }
        $search = isset($this->request->query['search']) ? trim($this->request->query['search']) : '';

        //注意权限
        $orders_table = TableRegistry::get('Orders');

        $query = $orders_table->find('all')
            ->find('Normal')
            ->find('Displayed')
            ->find('OwnedBy',['account_id' => $this->request->session()->read('Auth.User.id')])
            ->find('BetweenCreateTime',['start'=>$start,'end'=>$end])
            ->find('NumberLike',['number' => $search])
            ->contain(['OrdersGoods','Account','Department','WorkflowDetail'])
            ->matching('OrdersGoods',function($q){
                return $q->where(['OrdersGoods.good_id >'=>0]);
            })
            ->matching('WorkflowDetail',function($q) use($process_status){
                if($process_status == -1){
                    return $q->where(['step_code !='=>'end']);
                }else if($process_status == 1){
                    return $q->where(['step_code'=>'end']);
                }
                return $q;
            })
            ->order(['Orders.create_time DESC']);

        $data = $this->paginate($query);

        $this->set(compact(['data','start','end','search','process_status']));
        $this->set('list_title',"我的订单");
    }

    /**
     * 更改订单商品购买数量
     * @return [type] [description]
     */
    public function updateNum(){
        $this->viewClass = 'Json';
        $order_goods = TableRegistry::get("OrdersGoods");
        $order_good = $order_goods->get($this->request->data['order_good_id']);
        $order_good->num = $this->request->data['num'];
        if($order_goods->save($order_good)){
            $code   = "0";
            $msg    = "修改商品数量成功！";
        }else{
            $code   = "-1";
            $msg    = "修改商品数量失败！";
        }
        $this->set(compact(['code','msg']));
        $this->set('_serialize', ['code','msg']);
    }


    /**
     * @author wangjc
     * 订单详情
     * @param number $id
     */
    public function detail($id) {

        //加载订单基础模型
        $orders_table = TableRegistry::get('Orders');
        $orders_info = $orders_table->find()->contain(['OrdersGoods'])->where(['Orders.id'=>$id,'is_console'=>0])->first();

        if (empty($orders_info)){
            throw new NotFoundException();
        }


        //查找并缓存 商品图片
        $good_id_images = [];
        $order_goodsconfig = array();
        $order_goodsvpc = array();
        $flow_id = 0;
        if(!empty($orders_info['orders_goods'])){
            $good_ids = [];
            $order_goodslist=array();
            foreach ($orders_info['orders_goods'] as $_key => $_value){
                $good_ids[] = $_value['good_id'];
                if($_value["instance_conf"]!="[]"&&!empty($_value["instance_conf"])){
                    $json_str = json_decode($_value["instance_conf"],true);

                    if (isset($json_str["subnetCode"])) {
                        $json_str["subnet"]  = parent::getNameByCode($json_str["subnetCode"]);
                    }
                    if (isset($json_str["regionCode"])) {
                        $json_str["region"]  = parent::getRegionInfoByCode($json_str["regionCode"]);
                    }
                    if (isset($json_str["vpcCode"])) {
                        $json_str["vpc"]  = parent::getNameByCode($json_str["vpcCode"]);
                    }
                    if (isset($json_str["specid"])) {
                        $json_str["spec"]  = parent::getSpecInfoById($json_str["specid"]);
                    }
                    if (isset($json_str["versionId"])) {
                        $json_str["name"]  = parent::getSpecInfoById($json_str["versionId"]);
                    }
                    if (isset($json_str["bscharge"])) {
                        $json_str["duration"]  = parent::getBsPriceById($json_str["bscharge"]);
                    }
                    $order_goodsconfig[]= $json_str;
                }
            }
            $goods_table = TableRegistry::get('Goods');
            $_goods = $goods_table->find()->contain(['GoodsSpec'])->where(function ($exp, $q) use ($good_ids) {
                return $exp->in('id',$good_ids);
            })->toArray();
            $service_list_table = TableRegistry::get('ServiceList');
            $service_type_table = TableRegistry::get('ServiceType');
            $charge_template_table = TableRegistry::get('ChargeTemplate');
            $_goods_format = [];
            foreach ($_goods as $img){
                $good_id_images[$img['id']] = $img['icon'];
                $_service_ids[$img['id']] = $img['service_id'];
                $_goods_format[$img['id']] = $img;
                // $value[$img['id']]
            }

        }
        $this->set('order_goodsconfig',$order_goodsconfig);
        // $this->set('_service_ids',$_service_ids);
        // $this->set('_fee_info',$fee_info);
        $this->set('_goods_format',$_goods_format);
        $this->set('_orders_info',$orders_info);
        $this->set('good_id_images',$good_id_images);
    }

    /**
     * 根据flow_id获取权限，判断当前用户是否有该权限
     * @param unknown $flow_id
     * @return boolean
     */
    protected function _checkPopedom($flow_id){
        //根据flow_id获取权限，判断当前用户是否有该权限
        $workflow_detail_table = TableRegistry::get('WorkflowDetail');
        $workflow_detail_info = $workflow_detail_table->find()->select(['id','step_popedom_code'])->where(['flow_id'=>$flow_id])->toArray();
        $popedom = false;
        $popedom_info = [];
        if (!empty($workflow_detail_info)){
            foreach ($workflow_detail_info as $_key => $_value){
                if (is_null($_value['step_popedom_code'])){
                    $popedom = true;break;
                }else{
                    $popedom_info[] = $_value['step_popedom_code'];
                }
            }
        }

        //根据session判断是否有权限
        if (!$popedom){
            if (array_intersect($popedom_info,$this->request->session()->read('Auth.User.popedomname'))){
                $popedom = true;
            }
        }

        return $popedom;
    }
    /**
     * @author wangjc
     *获取待审订单信息
     *@post：ajax提交
     *@param：$limit:数据行数,$offset:页数,$start:开始时间,$end:结束时间,$search:搜索信息
     *@return：json数据
     */
    public function getOrderInfo($limit=10,$offset=0,$start=0,$end=0,$search=''){
        if ($offset>0) {
            $offset = $offset-1;
        }
        $offset =  $offset*$limit;

        //注意权限

        $orders_process_flow_table = TableRegistry::get('OrdersProcessFlow');
        $workflow_detail_table = TableRegistry::get('WorkflowDetail');
        $orders_table = TableRegistry::get('Orders');

        $query = $orders_process_flow_table->find()->contain(['WorkflowDetail'])->limit($limit)->offset($offset);
        $pop = $this->request->session()->read('Auth.User.popedomname');//获取权限

        //根据权限信息获取可审查的步骤
        $detail_info = $workflow_detail_table->find() ->where(['step_popedom_code IN' =>$pop])->toArray();
        foreach ($detail_info as $key => $value) {
            $user_detail_info[$value['flow_id']][] = $value['lft']-1;
        }
        $info=array();
        $where_or = array();
        foreach ($user_detail_info as $_k => $_v) {
            $where['Orders.flow_id'] = $_k;
            $where['status IN'] = $_v;
            $where_and['AND'] = $where;
            $where_or['OR'][]= $where_and;
        }

        $id = $this->request->session()->read('Auth.User.department');//部门id

        $Agent = TableRegistry::get('Agent');
        $GoodsSpec = TableRegistry::get('GoodsSpec');
        $Goods = TableRegistry::get('Goods');
        $where = array();
        $where_or['Orders.department_id'] = $this->request->session()->read('Auth.User.department_id');
        if (! empty($search)) {
            $where_or["Orders.number like"] = "%" . $search . "%";
        }

        if($start !=0 ){
            $start_time=strtotime($start);
            $where_or['Orders.create_time >='] = $start_time;
        }
        if($end !=0 ){
            $end_time=strtotime($end)+86400;
            $where_or['Orders.create_time <='] = $end_time;
        }

        $orderinfo = $orders_table->find('all')->contain(['OrdersGoods','Account','WorkflowDetail'])->where(['is_console' => 0])->where($where_or)->order(['Orders.create_time DESC'])->limit($limit)->offset($offset)->toArray();

        $info = $this->_orders_data($orderinfo);


        echo json_encode($info);exit;
    }


    /**
     * @author wangjc
     *获取已审订单信息
     *@post：ajax提交
     *@param：$limit:数据行数,$offset:页数,$start:订单开始时间,$end:订单结束时间,$search:搜索信息
     *@return：json数据
     */
    public function getOrderedInfo($limit=10,$offset=0,$start=0,$end=0,$search=''){
        if ($offset>0) {
            $offset = $offset-1;
        }
        $offset =  $offset*$limit;

        //注意权限

        $orders_process_flow_table = TableRegistry::get('OrdersProcessFlow');
        $workflow_detail_table = TableRegistry::get('WorkflowDetail');
        $orders_table = TableRegistry::get('Orders');

        $query = $orders_process_flow_table->find()->contain(['WorkflowDetail'])->limit($limit)->offset($offset);
        $pop = $this->request->session()->read('Auth.User.popedomname');//获取权限信息


        //根据权限信息获取可审查的步骤
        $detail_info = $workflow_detail_table->find() ->where(['step_popedom_code IN' =>$pop])->toArray();
        foreach ($detail_info as $key => $value) {
            $user_detail_info[$value['flow_id']][] = $value['lft'];
            if($value['lft']==2){
                $user_detail_info[$value['flow_id']][] = '-1';
            }
        }
        $info=array();
        $where_or = array();
        foreach ($user_detail_info as $_k => $_v) {
            $where['Orders.flow_id'] = $_k;
            $where['status >='] = min($_v);//已审核的
            $where['Orders.status <>'] = 1;
            $where_and['AND'] = $where;
            $where_or['OR'][]= $where_and;
        }

        $id = $this->request->session()->read('Auth.User.department');//部门id

        $Agent = TableRegistry::get('Agent');
        $GoodsSpec = TableRegistry::get('GoodsSpec');
        $Goods = TableRegistry::get('Goods');
        $where = array();
        $where_or['Orders.department_id'] = $this->request->session()->read('Auth.User.department_id');
        if (! empty($search)) {
            $where_or["Orders.number like"] = "%" . $search . "%";
        }

        if($start !=0 ){
            $start_time=strtotime($start);
            $where_or['Orders.create_time >='] = $start_time;
        }
        if($end !=0 ){
            $end_time=strtotime($end)+86400;
            $where_or['Orders.create_time <='] = $end_time;
        }

        $orderinfo = $orders_table->find('all')->contain(['OrdersGoods','Account','WorkflowDetail'])->where(['is_console' => 0])->where($where_or)->order(['Orders.create_time DESC'])->limit($limit)->offset($offset)->toArray();

        $info = $this->_orders_data($orderinfo);//重组订单数据


        echo json_encode($info);exit;
    }

    public function test() {

        ;
    }

    /**
     * @author wangjc
     *订单信息重新组装
     */
    public function _orders_data($orderinfo){
        $GoodsSpec = TableRegistry::get('GoodsSpec');
        $Goods = TableRegistry::get('Goods');
        $info = array();
        foreach ($orderinfo as $key => $order_info)
        {

            //$order_info = $order_info->toArray();
            $info['orderinfo'][$key]=$order_info;//订单信息
            foreach ($order_info['orders_goods'] as $k => $order_good_info) {

                if (!empty($order_good_info['good_id'])) {
                    // var_dump($order_good_info['good_id']);exit;
                    $GoodsSpecinfo = $GoodsSpec->find('all')->where(array('goods_id' => $order_good_info['good_id'] ))->toArray();//商品详细信息
                    $good_info = $Goods->getGoodsInfo(['id' => $order_good_info['good_id']],['name','mini_icon']); //商品信息
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

        return $info;
    }

    public function orderlog($order_id){

        //加载订单基础模型
        $orders_table = TableRegistry::get('Orders');
        $orders_info = $orders_table->find()->contain(['OrdersGoods'])->where(['Orders.id'=>$order_id])->first();

        if (empty($orders_info)){
            throw new NotFoundException();
        }

        $this->set('_orders_info',$orders_info);
    }


    public function editprice(){
        $order_goods_table = TableRegistry::get('OrdersGoods');
        $order_good_entity = $order_goods_table->get($this->request->data['order_good_id']);
        $order_good_entity->transaction_price = $this->request->data['transaction_price'];

        if($order_goods_table->save($order_good_entity)){
            $msg    = '修改单价成功';
            $code   = 0;
        }else{
            $msg    = '修改失败';
            $code   = -1;
        }
        echo json_encode(compact(['msg','code']));exit;
    }

    public function editChargeMode(){
        $this->createView('json');
        $data = [];
        list($data['charge_mode'],$data['interval'])    = explode("|",$this->request->data['charge_mode']);
        $data['transaction_price']  = $data['interval'] == 'P' ? 0 : $this->request->data['price'];

        $order_goods_table = TableRegistry::get('OrdersGoods');
        $order_good_entity = $order_goods_table->get($this->request->data['orderGoodId']);
        $entity = $order_goods_table->patchEntity($order_good_entity,$data);
        if($order_goods_table->save($entity)){
            $msg    = '修改计费模式成功';
            $code   = 0;
        }else{
            $msg    = '修改失败';
            $code   = -1;
        }
        $this->set(compact(['code','msg']));
        $this->set('_serialize',['code','msg']);
    }
    public function editPriority(){
        $order_goods_table = TableRegistry::get('OrdersGoods');
        $priority=isset($this->request->data['priority_data'])?$this->request->data['priority_data']:0;
        $id=isset($this->request->data['priority_id'])?$this->request->data['priority_id']:'';
        if(empty($id)){
            echo json_encode(array('code'=>2,'msg'=>'未找到对应桌面'));exit;
        }
        if($order_goods_table->find()->select(['priority'])->where(array('id'=>$id))->first()['priority']==$priority){
            echo json_encode(array('code'=>3,'msg'=>'未进行修改'));exit;
        }
        $res=$order_goods_table->updateAll(array('priority'=>$priority),array('id'=>$id));
        if($res){
            echo json_encode(array('code'=>0,'msg'=>'修改优先级成功'));exit;
        }else{
            echo json_encode(array('code'=>1,'msg'=>'修改优先级失败'));exit;
        }
    }

    public function editOrderGoods(){
        //$this->autoRender = false;
        $order_good_id = (int)$this->request->query('order_good_id');
        if($order_good_id <= 0){
            $this->redirect($this->referer());
        }
        $order_goods_table = TableRegistry::get('OrdersGoods');
        $order_entity = $order_goods_table->findById($order_good_id)->first();
        $instance_conf = json_decode($order_entity->instance_conf);
        switch ($order_entity->good_type) {
            case 'eip':
                $url = "/home/selectEip/".$order_entity->good_id."/".$instance_conf->version."/".$order_good_id;
                break;
            case 'ecs':
                $url = "/home/selectEcs/".$order_entity->good_id."/".$order_good_id;
                break;
            case 'citrix_public':
                $url = "/home/selectCitrixVpc/".$order_entity->good_id."/".$instance_conf->version."/".$instance_conf->priceId."/".$order_good_id;
                break;
            case 'vfw':
            case 'waf':
                $url = "/home/selectVfw/".$order_entity->good_id."/".$instance_conf->version."/".$order_good_id;
                break;
            case 'vpc':
                $url = "/home/selectVpc/".$order_entity->good_id."/".$instance_conf->version."/".$order_good_id;
                break;
            case 'elb':
                $url = "/home/selectElb/".$order_entity->good_id."/".$instance_conf->version."/".$order_good_id;
                break;
            case 'disks':
                $url = "/home/selectDisks/".$order_entity->good_id."/".$instance_conf->version."/".$order_good_id;
                break;
            default:
                throw new \Exception("此商品类型不支持修改配置", 1);
                break;
        }
        $this->redirect($url);
    }

    public function editGoodConfig(){
        $order_goods_table = TableRegistry::get('OrdersGoods');
        $order_entity = $order_goods_table->findById($this->request->data['order_good_id'])->first();
        switch ($order_entity->good_type) {

            case 'ecs':
                $this->setAction('orderGoodsUpdate');
                break;
            case 'citrix_public':
                $this->setAction('orderCitrixGoodsUpdate');
                break;
            case 'eip':
            case 'vfw':
            case 'waf':
            case 'vpc':
            case 'elb':
            case 'disks':
                $this->setAction('orderCommonGoodsUpdate');
                break;
            default:
                throw new \Exception("此商品类型不支持修改配置", 1);
                break;
        }
    }

    public function orderCommonGoodsUpdate()
    {
        $order_goods_table = TableRegistry::get('OrdersGoods');
        $order_good = $order_goods_table->get($this->request->data['order_good_id']);
        switch ($this->request->data['priceId']) {
            case '1':
                $interval = 'D';
                break;
            case '2':
                $interval = 'M';
                break;
            case '3':
                $interval = 'Y';
                break;
            default:
                $interval = 'D';
                break;
        }
        $instance_conf = json_decode($order_good->instance_conf,true);
        $good_info = $order_good->good_info;
        foreach ($this->request->data as $key => $value) {
            $instance_conf[$key] = $value;
        }
        $order_good->price_per          = $this->request->data['price'];
        $order_good->transaction_price  = $this->request->data['price'];
        $order_good->price_total        = $order_good->num * $order_good->price_per;
        $order_good->interval           = $interval;

        $order_good->instance_conf      = json_encode($instance_conf);

        $instance_conf['goods_info']    = $order_good->good_info->goods_info;
        $order_good->goods_snapshot     = json_encode($instance_conf);
        if($order_goods_table->save($order_good)){
            $this->redirect(['action'=>'lists']);
        }else{
            throw new \Exception("Update order goods error", 1);
        }
    }

    /**
     * 订单商品配置修改
     * @return [type] [description]
     */
    public function orderGoodsUpdate()
    {
        $order_goods_table = TableRegistry::get('OrdersGoods');
        $order_good = $order_goods_table->get($this->request->data['order_good_id']);

        $billCycle  = $this->request->data['billCycle'];
        switch ($billCycle) {
            case '1':
                $interval = 'D';
                break;
            case '2':
                $interval = 'M';
                break;
            case '4':
                $interval = 'Y';
                break;
            default:
                $interval = 'D';
                break;
        }

        $instance_conf = $this->request->data;
        $order_good->num                = $this->request->data['number'];
        $order_good->price_per          = $this->request->data['totalPay'];
        $order_good->transaction_price  = $this->request->data['totalPay'];
        $order_good->price_total        = $order_good->num * $order_good->price_per;
        $order_good->interval           = $interval;

        $order_good->instance_conf      = json_encode($this->request->data);

        $instance_conf['goods_info']    = $order_good->good_info->goods_info;
        $order_good->goods_snapshot     = json_encode($instance_conf);
        if($order_goods_table->save($order_good)){
            $this->redirect(['action'=>'lists']);
        }else{
            throw new \Exception("Update order goods error", 1);
        }
        //$old_good_snap
    }

    /**
     * 桌面大众版配置修改
     * @return [type] [description]
     */
    public function orderCitrixGoodsUpdate()
    {

        $order_goods_table = TableRegistry::get('OrdersGoods');
        $goodsVersionPrice = TableRegistry::get('GoodsVersionPrice');
        $order_good = $order_goods_table->get($this->request->data['order_good_id']);

        $instance_conf = json_decode($order_good->instance_conf);
        $good_info = $order_good->good_info;
        foreach ($this->request->data as $key => $value) {
            $instance_conf->$key = $value;
        }

        $good_info->vpc->name = $this->request->data['vpcName'];
        $good_info->vpc->code = $this->request->data['vpcCode'];

        $good_info->subnet->name = $this->request->data['netName'];
        $good_info->subnet->code = $this->request->data['subnetCode'];

        $good_info->price_info = $goodsVersionPrice->findById($this->request->data['priceId'])->first();


        $order_good->price_per          = $good_info->price_info->price;
        $order_good->transaction_price  = $good_info->price_info->price;
        $order_good->price_total        = $order_good->num * $good_info->price_info->price;

        $order_good->instance_conf = json_encode($instance_conf);
        $order_good->goods_snapshot = json_encode($good_info);

        if($order_goods_table->save($order_good)){
            $this->redirect(['action'=>'lists']);
        }else{
            throw new \Exception("Update order goods error", 1);
        }
    }

}