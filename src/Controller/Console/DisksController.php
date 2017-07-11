<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2015/10/8
 * Time: 16:59
 */

namespace App\Controller\Console\Network;
use App\Controller\Console\ConsoleController;
use App\Controller\OrdersController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;

class DisksController extends ConsoleController{

    private $_serialize = array('code','msg','data');
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }


    public $_pageList = array(
        'total'   =>  0,
        'rows'    =>  array()
    );


    /**
     * 获取列表数据,
     *
     * 新增关联查询
     */
    public function lists($request_data = []){
        $limit = $request_data['limit'];
        $offset = $request_data['offset'];
        $account_table = TableRegistry::get('Accounts');
        $user = $account_table->find()->select('department_id')->where(array('id' => $this->request->session()->read('Auth.User.id')))->first();
         $where = '';
        if (!empty($user)) {
            $where .= ' AND a.department_id = ' . $user['department_id'];
        }
        if(isset($request_data['search'])){
            if ($request_data['search']!="") {
            $where .= ' AND (a.name like\'%' . $request_data['search'] . '%\' OR a.code like\'%'.$request_data['search'].'%\' OR ib.name like\'%' . $request_data['search'] . '%\' OR he.ip like\'%' . $request_data['search'] . '%\')';
        }
        }
        if (!empty($request_data['class_code'])) {
            $where .= ' AND a.location_code like\'' . $request_data['class_code'] . '%\'';
        }
        if (!empty($request_data['class_code2'])) {
            $where .= ' AND a.location_code like\'' . $request_data['class_code2'] . '%\'';
        } elseif (!empty($request_data['class_code'])) {
            $where .= ' AND a.location_code like\'' . $request_data['class_code'] . '%\'';
        }
        $connection = ConnectionManager::get('default');
        $sql = ' SELECT ';
        $sql .= ' a.id,a.`code`,a.`name`,dm.capacity,dm.attachhostid,a.location_name,a.`status`,ib.`name` as \'H_Name\',he.ip,a.create_time ';
        $sql .= ' FROM ';
        $sql .= ' cp_instance_basic AS a ';
        $sql .= ' LEFT JOIN cp_disks_metadata AS dm ON a.id = dm.disks_id ';
        $sql .= ' LEFT JOIN cp_instance_basic AS ib ON dm.attachhostid = ib. CODE ';
        $sql .= ' LEFT JOIN cp_host_extend AS he ON ib.id = he.basic_id ';
        $sql .= ' WHERE a.type=\'disks\' ' . $where;
        $sql .= ' group by a.id';
        $sql .= ' ORDER BY a.create_time desc ';
        $sql_row = $sql . ' limit ' . $offset . ',' . $limit;
        $query = $connection->execute($sql_row)->fetchAll('assoc');
        $this->_pageList['total'] = $connection->execute($sql)->count();
        $this->_pageList['rows'] = $query;
        return $this->_pageList;
    }

    /**
     * @func:在使用的硬盘
     * @date: 2015年11月5日17:12:11
     * @author: shrimp liao
     * @return: null
     */
    public function uselist($request_data =[]){
        $attachhostid=$request_data['id'];
        $instance_basic = TableRegistry::get('InstanceBasic');

        $sql =" SELECT a.id,a.name,a.`code`,b.capacity FROM cp_instance_basic AS a";
        $sql.=" LEFT JOIN cp_disks_metadata AS b ON a.id = b.disks_id";
        $sql.=" where a.type='disks' and b.attachhostid='".$attachhostid."'";
        $connection = ConnectionManager::get('default');
        // debug($sql);die();
        $result = $connection->execute($sql)->fetchAll('assoc');
        return $result;
    }

    //硬盘扩容
    public function addvolume($data){
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        $code = '0001';
        $order = new OrdersController();
        $data['method']='volume_resize';
        $url=Configure::read('URL');
        $data['uid']=(string)$this->request->session()->read('Auth.User.id');
        $data['basicId'] = (string)$instance_basic_table->find()->select(['id'])->where(['code'=>$data['volumeCode']])->first()->id;
        $re_code=$order->postInterface($url,$data);//调用接口\
        if($re_code['Code']==0){
            $code = '0000';
            $msg='扩容成功';
        }else{
            $msg=$re_code['Message'];
        }
        return compact(array_values($this->_serialize));
    }

    /**
     * @fun    未使用的硬盘
     * @date   2015-11-06T18:21:51+0800
     * @author shrimp liao
     * @param  array
     * @return [type]
     */
    public function unuselist($request_data = []){
        $this->paginate['limit'] = $request_data['limit'];
        $this->paginate['page'] = $request_data['offset']/$request_data['limit']+1;
        $vpc=$request_data['vpc'];
        $instance_basic = TableRegistry::get('InstanceBasic');
        $where = [
            'InstanceBasic.type'  =>  'disks',
            'attachhostid'=>'0',
            'status'=>'运行中',
            'InstanceBasic.vpc'=>$vpc
        ];
        $this->_pageList['total']=$instance_basic->find('all')->contain(['DisksMetadata'])->where($where)->count();
        $this->_pageList['rows']=$this->paginate($instance_basic->find('all')->contain(['DisksMetadata'])->where($where)->order(['create_time'=>'DESC']));
        return $this->_pageList;
    }


    //修改计算机与网络-硬盘
    public function updateDisks($datas){
        $code = '0001';
        $data = [];
        $disks = TableRegistry::get('InstanceBasic',['classname'=>'App\Model\Table\InstanceBasicTable']);
        $result = $disks->updateAll($datas,array('id'=>$datas['id']));
        if(isset($result)){
            $code = '0000';
            $data = $disks->get($datas['id'])->toArray();
        }
        $msg = Configure::read('MSG.'.$code);
        return compact(array_values($this->_serialize));

    }

    /**
     * 解绑硬盘
     * @fun    name
     * @date   2015-11-07T14:39:42+0800
     * @author shrimp liao
     * @param  array                    $request_data [硬盘code]
     * @return [type]                                 [description]
     */
    public function detachDisks($data = []){
        // $volumeCode=$request_data['volumeCode'];
            $order = new OrdersController();
            $data['uid']=(string)$this->request->session()->read('Auth.User.id');//uid
            $url=Configure::read('URL');
            $interface= $order->postInterface($url,$data);
            if($interface['Code']!=0){
                echo json_encode($interface);die();
            }else{
                $data['method']="volume_del";
                $interface= $order->postInterface($url,$data);
                echo json_encode($interface);die();
            }
    }


    //删除计算机与网络-硬盘
    public function deleteDisks($datas){
        $datas['uid']=(string)$this->request->session()->read('Auth.User.id');
        $order =new OrdersController();
        $url=Configure::read('URL');
        //该硬盘不存在code时
        if($datas['volumeCode']==''){
            $disks = TableRegistry::get('InstanceBasic');
            $result = $disks->deleteAll(array('id'=>$datas['basicId']));
            if($result){
                echo json_encode(array('Code'=>0,'Message'=>'删除成功'));exit;
            }
            //硬盘未绑定主机时
        }elseif($datas['host']=='' ||$datas['host']===0){
            unset($datas['host']);
            $datas['method']='volume_del';
            $re_code=$order->postInterface($url,$datas);//调用接口
            echo json_encode($re_code);exit;
        }else{
            $datas['method']='volume_detach';
            if(isset($datas['basicId'])){
                $result =$order->postInterface($url,$datas);//调用接口
                if($result['Code']==0 || $result['Message']=='存储不存在.' || $result['Message']=='存储未绑定.'){
                    $datas['method']='volume_del';
                    $re_code=$order->postInterface($url,$datas);//调用接口
                    echo json_encode($re_code);exit;
                }

            }
        }
        /*elseif(isset($datas['ids'])){
            $ids = explode(',',$datas['ids']);
            $result=0;
            foreach($ids as $v){
                $volumeCode = $disks->find()->select(['code'])->where(['id'=>$v])->toArray();
                $parameter['volumeCode']=$volumeCode[0]['code'];
                $parameter['basicId']=$v;
                $re_code=$order->postInterface($url,$parameter);//调用接口
                if($re_code['Code']==0){
                    $result++;
                }else{

                    $msg=$re_code['Message'];
                    break;
                }

                if($result == count($ids)){
                    $code = '0000';
                    $msg='删除成功';
                }
            }

        }*/
    }

    public function createDisks($data){
        $basic_id=$data['id'];
        $vpc=$data['vpcCode'];
        $instanceCode=$data['instanceCode'];
        $name=$data['name'];
        $size=$data['size'];
        $regionCode=$data['regionCode'];
        $goods=$this->getGoodBySn('disks');
        $id= $this->createOrder($goods[0]['id']);//创建硬盘订单 6硬盘
        $orderGoodsTable=TableRegistry::get('OrdersGoods');
        $ordergoods=$orderGoodsTable->newEntity();
        $ordergoods->order_id=$id;
        $ordergoods->good_id=$goods['0']['id'];
        $ordergoods->good_name=$goods['0']['name'];
        $ordergoods->good_sn='';
        $ordergoods->num=1;
        $ordergoods->benefit=0;
        $ordergoods->price_per=0;
        $ordergoods->price_total=0;
        $ordergoods->facilitator_id=0;
        $ordergoods->instance_conf=json_encode($data);
        $ordergoods->goods_snapshot=json_encode($data);
        $ordergoods->duration=$goods['0']['time_unit'];

        $ordergoods->duration_unit=$goods['0']['time_duration'];
        $ordergoods->description=0;
        $result= $orderGoodsTable->save($ordergoods);

        $orders=new OrdersController();

        $url=Configure::read('URL');
        $parameter['method']='volume_add';//方法名
        $parameter['uid']=(string)$this->request->session()->read('Auth.User.id');//uid
        $parameter['volumeName']=$name;
        $parameter['size']=$size;
        $parameter['iaasCode']=$instanceCode;
        $parameter['vpcCode']=$vpc;
        $parameter['regionCode']=$regionCode;
        $interface = $orders->postInterface($url,$parameter);//调用接口
        return $interface;
    }

    public function attachDisks($data){
        $instanceCode=$data['instanceCode'];
        $code=$data['volumeCode'];
        $goods=$this->getGoodBySn('disks');
        $id= $this->createOrder($goods[0]['id']);//创建硬盘订单 6硬盘
        $orderGoodsTable=TableRegistry::get('OrdersGoods');
        $ordergoods=$orderGoodsTable->newEntity();
        $ordergoods->order_id=$id;
        $ordergoods->good_id=$goods['0']['id'];
        $ordergoods->good_name=$goods['0']['name'];
        $ordergoods->good_sn='';
        $ordergoods->num=1;
        $ordergoods->benefit=0;
        $ordergoods->price_per=0;
        $ordergoods->price_total=0;
        $ordergoods->facilitator_id=0;
        $ordergoods->instance_conf=json_encode($data);
        $ordergoods->goods_snapshot=json_encode($data);
        $ordergoods->duration=$goods['0']['time_unit'];

        $ordergoods->duration_unit=$goods['0']['time_duration'];
        $ordergoods->description=0;
        $result= $orderGoodsTable->save($ordergoods);

        $orders=new OrdersController();

        $url=Configure::read('URL');
        $parameter['method']='volume_attach';//方法名
        $parameter['uid']=(string)$this->request->session()->read('Auth.User.id');//uid
        $parameter['volumeCode']=$code;
        $parameter['iaasCode']=$instanceCode;
        $interface = $orders->postInterface($url,$parameter);//调用接口
        return $interface;
    }

    /**
     * @func:ajax请求
     * @param:
     * @date: 2015年11月3日 下午4:24:35
     * @author: shrimp liao
     * @return: null
     */
    public function ajaxDisks($data){
        $method=$data['method'];
        if($method=='volume_attach'){
            foreach (explode(',',$data['volumeCode']) as $key => $value) {
                if(!empty($value)){
                    $data['volumeCode']=$value;
                    $interface = $this->attachDisks($data);
                    if($interface['Code']!=0){
                        echo json_encode($interface);die();
                    }else{
                        $arrayName = array();
                        $arrayName['Code']=0;
                        $arrayName['Message']="";
                        echo json_encode($arrayName);die();
                    }
                }
            }
        }else if($method=='volume_add'){
            $Agent=TableRegistry::get('Agent');
            $where = array('class_code' => $data['class_code']);
            $data['regionCode']=$Agent->find()->select(['region_code'])->where($where)->toArray()[0]['region_code'];
            $interface = $this->createDisks($data);
            echo json_encode($interface);die();
        }elseif ($method=='volume_detach') {
            $interface= $this->detachDisks($data);
            echo json_encode($interface);die();
        }
    }

    /**
     * @func: 创建订单
     * @param: $goods_id 商品ID 硬盘
     * @date: 2015年11月3日 下午3:15:55
     * @author: shrimp liao
     * @return: 新订单id
     */
    public function createOrder($goods_id) {
        $orders=new OrdersController();
        //创建订单信息
        $orderTable=TableRegistry::get('Orders');
        $ordes = $orderTable->newEntity();
        $ordes->number=$orders->build_order_no();
        $ordes->product_id=0;
        $ordes->goods_snapshot='';
        $ordes->facilitator_id=0;
        $ordes->instance_conf='';
        $ordes->duration=0;
        $ordes->duration_unit='月';
        $ordes->price_per=0;
        $ordes->num=1;
        $ordes->benefit=0;
        $ordes->price_total=0;
        $ordes->department_id=0;
        $ordes->tenant_id=0;
        $ordes->description='';
        $ordes->create_time=time();
        $ordes->create_by=0;
        $ordes->modify_time=time();
        $ordes->modify_by=0;
        $ordes->is_console = 1;
        $orderTable->save($ordes);
        return $ordes->id;
    }

    /**
     * @func: 根据Sn查询商品信息
     * @param:
     * @date: 2015年11月3日 下午3:25:18
     * @author: shrimp liao
     * @return: null
     */
    public function getGoodBySn($sn){
        $goods=TableRegistry::get('Goods');
        $where=array('Goods.sn'=>$sn);
        //获取商品信息，包含商品分类
        $goodsInfo=$goods->find('all')->where($where)->toArray();
        return $goodsInfo;
    }

}