<?php
/** 
* 文件描述文字
* 
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date  2015年9月21日下午4:28:04
* @source ConsoleController.php
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 

namespace App\Controller\Console;

use App\Controller\Console\ConsoleController;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Network\Http\Client;
class MyorderController extends  ConsoleController
{	
    private function _checkPopedom($param)
    {
        $popedom = parent::checkConsolePopedom($param);
        return $popedom;
    }

    public function index($status=0,$flow_id=0,$start=0,$end=0,$search='') {
        $limit=10;$offset=0;
        $checkPopedomlist = $this->_checkPopedom('ccm_user_myorder');
        if (! $checkPopedomlist)
        {
            return $this->redirect('/console/');
        }

        if ($offset>0) {
            $offset = $offset-1;
        }
        $offset =  $offset*$limit;
        

        $id = $this->request->session()->read('Auth.User.id');

        $orders = TableRegistry::get('Orders');
        $Agent = TableRegistry::get('Agent');
        $Imagelist = TableRegistry::get('Imagelist');
        $GoodsSpec = TableRegistry::get('GoodsSpec');
        $Goods = TableRegistry::get('Goods');
        $WorkflowTemplate = TableRegistry::get('WorkflowTemplate');
        $WorkflowDetail = TableRegistry::get('WorkflowDetail');

        $workflow_template_data = $WorkflowTemplate->find('all')->toArray();

        $where = array();
        $where['Orders.account_id'] = $this->request->session()->read('Auth.User.id');
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
        }else{
            $where['Orders.flow_id <>'] = 0;
        }
        if($status!=0){
            // $where .=" AND cp_orders.`status` = ".$status;
            $where['Orders.status'] = $status;
            $step = $WorkflowDetail->find()->where(['lft' => $status,'flow_id' => $flow_id])->first();
            $this->set('step',$step);
        }else{
            // $where .=" AND cp_orders.`status` <> 0 ";
            $where['Orders.status <>'] = 0;
        }
        
        $orderinfo = $orders->find('all')->contain(['OrdersGoods','Account','WorkflowDetail'])->where(['is_console' => 0])->where($where)->order(['Orders.create_time DESC'])->limit($limit)->offset($offset)->toArray();
        // $count = $orders->find('all')->contain(['OrdersGoods','Account'])->where(['is_console' => 0])->count();
       
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
        $this->set('info',$info);
        $this->set('search',$search);
        $this->set('status',$status);
        $this->set('end',$end);
        $this->set('start',$start);
        $this->set('flow_id',$flow_id);
        $this->set('template',$workflow_template_data);
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
        $where['Orders.account_id'] = $this->request->session()->read('Auth.User.id');

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
        }else{
            $where['Orders.flow_id <>'] = 0;
        }
        if($status!=0){
            // $where .=" AND cp_orders.`status` = ".$status;
            $where['Orders.status'] = $status;
        }else{
            // $where .=" AND cp_orders.`status` <> 0 ";
            $where['Orders.status <>'] = 0;
        }
        
        $orderinfo = $orders->find('all')->contain(['OrdersGoods','Account','WorkflowDetail'])->where(['is_console' => 0])->where($where)->order(['Orders.create_time DESC'])->limit($limit)->offset($offset)->toArray();
        // $count = $orders->find('all')->contain(['OrdersGoods','Account'])->where(['is_console' => 0])->where($where)->count();

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
    
    // public function index($limit=10,$offset=0,$status=0)
    // {
    //     $checkPopedomlist = $this->_checkPopedom('ccm_user_myorder');
    //     if (!$checkPopedomlist)
    //     {
    //         return $this->redirect('/console/');
    //     }
    //     if ($offset>0) {
    //         $offset = $offset-1;
    //     }
    //     $offset =  $offset*$limit;

    //     $id = $this->request->session()->read('Auth.User.id');

    //     $orders = TableRegistry::get('Orders');
    //     $Agent = TableRegistry::get('Agent');
    //     $Imagelist = TableRegistry::get('Imagelist');
    //     $GoodsSpec = TableRegistry::get('GoodsSpec');
    //     $Goods = TableRegistry::get('Goods');
    //     $where =" AND cp_orders.account_id = ".$this->request->session()->read('Auth.User.id');
    //     if($status!=0){
    //         $where .=" AND cp_orders.`status` = ".$status;
    //     }else{
    //         $where .=" AND cp_orders.`status` <> 0 ";
    //     }
        
    //     $connection = ConnectionManager::get('default');
    //     $sql = "SELECT cp_orders.*, B.good_id AS ordersgoodsid, B.good_name AS ordersgoodsname, B.goods_snapshot AS ordersgoodslist, B.num AS ordersgoodsnum, C.id AS userid, C.username AS username FROM cp_orders";
    //     $sql .=" LEFT JOIN `cp_orders_goods` AS B ON cp_orders.id =B.order_id LEFT JOIN `cp_accounts` AS C  ON cp_orders.account_id = C.id";

    //     $sql .=" WHERE B.is_console = 0".$where;
    //     $sql .=" ORDER BY cp_orders.create_time DESC";
    //     $sql_row =$sql." limit ".$limit." OFFSET ".$offset;

    //     $orderinfo = $connection->execute($sql_row)->fetchAll('assoc');
    //     $count = $connection->execute($sql)->count();
    //     $info['count'] = $count;

    //     $page = (int)ceil($info['count']/$limit);
    //     $info['page'] = $page;

    //     $goodsinfo = $Goods->find('all')->select(['id','mini_icon','name'])->toArray();
    //     $imagelistinfo = $Imagelist->find('all')->select(['image_name','os_family','image_code'])->toArray();
    //     $agentinfo = $Agent->find('all')->select(['display_name','region_code'])->toArray();
        


    //     $info = array();
    //     foreach ($orderinfo as $key => $ordergoods) {
    //         $info['orderinfo'][$key]=$ordergoods;//订单信息

    //         if(!empty($ordergoods['ordersgoodslist'])){
    //             $str=$ordergoods['ordersgoodslist'];
    //             $str = substr($str,1,strlen($str)-2);
    //             $arr = explode(',', $str);  
    //             $list = array();  
    //             foreach ($arr as $val) {  
    //                 $val = explode(':', $val);
    //                 if (strlen($val[0])-2 > 0){
    //                     $list[substr($val[0],1,strlen($val[0])-2)] = trim($val[1], '"');
    //                 }
    //             } /*var_dump($ordergoods);*/
    //             $info['goods'][$key]=$list;//订单商品信息
    //             if (!empty($list['regionCode'])) { 
    //                 foreach ($agentinfo as $agent) {
    //                     if ($agent['region_code']==$list['regionCode']) {
    //                         $info['region'][$key] =$agent;//租户信息
    //                     }
    //                 }
    //             }  
    //             if (!empty($list['imageCode'])) {
    //                 foreach ($imagelistinfo as $image) {
    //                     if ($image['image_code']==$list['imageCode']) {
    //                         $info['image'][$key] =$image; //镜像信息
    //                     }
    //                 }
    //             }
                
    //         } 
    //         if (!empty($ordergoods['ordersgoodsid'])) {
    //             $GoodsSpecinfo = $GoodsSpec->find('all')->where(array('goods_id' => $ordergoods['ordersgoodsid'] ))->toArray();//商品详细信息
    //             foreach ($GoodsSpecinfo as $speckey => $specinfo) {
    //                 if($specinfo['spec_name']=='软件版本'){
    //                     $info['version'][$key]=$specinfo['spec_value'];
    //                 }else if($specinfo['spec_name']=='机房位置'){
    //                     $info['labs'][$key]=$specinfo['spec_value'];
    //                 }else if($specinfo['spec_name']=='软件厂商'){
    //                     $info['activision'][$key]=$specinfo['spec_value'];
    //                 }else if($specinfo['spec_name']=='显存大小'){
    //                     $info['gpu'][$key]=$specinfo['spec_value'];
    //                 }else if($specinfo['spec_name']=='CPU核数'){
    //                     $info['cpu'][$key]=$specinfo['spec_value'];
    //                 }else if($specinfo['spec_name']=='内存大小'){
    //                     $info['rom'][$key]=$specinfo['spec_value'];
    //                 }else if($specinfo['spec_name']=='操作系统'){
    //                     $info['OS'][$key]=$specinfo['spec_value'];
    //                 }
    //             }
    //             foreach ($goodsinfo as $goodkey => $goodinfo) {
    //                 if($goodinfo['id']==$ordergoods['ordersgoodsid']){
    //                     $info['mini_icon'][$key] =$goodinfo['mini_icon'];//商品图标
    //                 }
    //             }
    //         }
    //     }
    //     $this->set('info',$info);
    //     $this->set('status',$status);
    // }

    // public function getOrderInfo($limit=10,$offset=0,$status=0){
    //     if ($offset>0) {
    //         $offset = $offset-1;
    //     }
    //     $offset =  $offset*$limit;

    //     $orders = TableRegistry::get('Orders');
    //     $Agent = TableRegistry::get('Agent');
    //     $Imagelist = TableRegistry::get('Imagelist');
    //     $GoodsSpec = TableRegistry::get('GoodsSpec');
    //     $Goods = TableRegistry::get('Goods');
    //     $where =" AND cp_orders.account_id = ".$this->request->session()->read('Auth.User.id');
    //     if($status!=0){
    //         $where .=" AND cp_orders.`status` = ".$status;
    //     }else{
    //         $where .=" AND cp_orders.`status` <> 0 ";
    //     }
    //     $connection = ConnectionManager::get('default');
    //     $sql = "SELECT cp_orders.*, B.good_id AS ordersgoodsid, B.good_name AS ordersgoodsname, B.goods_snapshot AS ordersgoodslist, B.num AS ordersgoodsnum, C.id AS userid, C.username AS username FROM cp_orders";
    //     $sql .=" LEFT JOIN `cp_orders_goods` AS B ON cp_orders.id =B.order_id LEFT JOIN `cp_accounts` AS C  ON cp_orders.account_id = C.id";

    //     $sql .=" WHERE B.is_console = 0".$where;
    //     $sql .=" ORDER BY cp_orders.create_time DESC";
    //     $sql_row =$sql." limit ".$limit." OFFSET ".$offset;

    //     $orderinfo = $connection->execute($sql_row)->fetchAll('assoc');

    //     $goodsinfo = $Goods->find('all')->select(['id','mini_icon','name'])->toArray();
    //     $imagelistinfo = $Imagelist->find('all')->select(['image_name','os_family','image_code'])->toArray();
    //     $agentinfo = $Agent->find('all')->select(['display_name','region_code'])->toArray();
        
    //     $info = array();
    //     foreach ($orderinfo as $key => $ordergoods) {
    //         $info['orderinfo'][$key]=$ordergoods;//订单信息

    //         if(!empty($ordergoods['ordersgoodslist'])){
    //             $str=$ordergoods['ordersgoodslist'];
    //             $str = substr($str,1,strlen($str)-2);
    //             $arr = explode(',', $str);  
    //             $list = array();  
    //             foreach ($arr as $val) {  
    //                 $val = explode(':', $val);
    //                 if (strlen($val[0])-2 > 0){
    //                     $list[substr($val[0],1,strlen($val[0])-2)] = trim($val[1], '"');
    //                 }
    //             } /*var_dump($ordergoods);*/
    //             $info['goods'][$key]=$list;//订单商品信息
    //             if (!empty($list['regionCode'])) { 
    //                 foreach ($agentinfo as $agent) {
    //                     if ($agent['region_code']==$list['regionCode']) {
    //                         $info['orderinfo'][$key]['region'] =$agent;//租户信息
    //                     }
    //                 }
    //             }  
    //             if (!empty($list['imageCode'])) {
    //                 foreach ($imagelistinfo as $image) {
    //                     if ($image['image_code']==$list['imageCode']) {
    //                         $info['orderinfo'][$key]['image'] =$image; //镜像信息
    //                     }
    //                 }
    //             }
                
    //         } 
    //         if (!empty($ordergoods['ordersgoodsid'])) {
    //             $GoodsSpecinfo = $GoodsSpec->find('all')->where(array('goods_id' => $ordergoods['ordersgoodsid'] ))->toArray();//商品详细信息
    //             foreach ($GoodsSpecinfo as $speckey => $specinfo) {
    //                 if($specinfo['spec_name']=='软件版本'){

    //                     $info['orderinfo'][$key]['version']=$specinfo['spec_value'];
    //                 }else if($specinfo['spec_name']=='机房位置'){

    //                     $info['orderinfo'][$key]['labs']=$specinfo['spec_value'];
    //                 }else if($specinfo['spec_name']=='软件厂商'){

    //                     $info['orderinfo'][$key]['activision']=$specinfo['spec_value'];
    //                 }else if($specinfo['spec_name']=='显存大小'){

    //                     $info['orderinfo'][$key]['gpu']=$specinfo['spec_value'];
    //                 }else if($specinfo['spec_name']=='CPU核数'){

    //                     $info['orderinfo'][$key]['cpu']=$specinfo['spec_value'];
    //                 }else if($specinfo['spec_name']=='内存大小'){

    //                     $info['orderinfo'][$key]['rom']=$specinfo['spec_value'];
    //                 }else if($specinfo['spec_name']=='操作系统'){

    //                     $info['orderinfo'][$key]['OS']=$specinfo['spec_value'];
    //                 }
    //             }
    //             foreach ($goodsinfo as $goodkey => $goodinfo) {
    //                 // var_dump($goodinfo);
    //                 if($goodinfo['id']==$ordergoods['ordersgoodsid']){
    //                     $info['orderinfo'][$key]['mini_icon'] =$goodinfo['mini_icon'];//商品图标
    //                 }
    //             }
    //         }
    //     }
       
    //     echo json_encode($info);exit;
       
    // }
}