<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2016/1/5
 * Time: 14:41
 */

namespace App\Controller\Admin;


use App\Controller\AdminController;
use App\Controller\OrdersController;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class ServiceTypeController extends AdminController{
    public $paginate = [
        'limit' => 15,
    ];

    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_service_type');
        if (! $checkPopedomlist) {
            return $this->redirect('/admin/');
        }
    }


    //列表显示
    public function index($department_id=0,$name=''){
        if(!is_numeric($department_id) && empty($name)){
            $name=$department_id;
            $department_id=0;
        }
        $departments = TableRegistry::get('Departments');
        //返回租户
        $dept_grout =array();
        if(in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))){
            $department_id = isset($this->request->data['department_id'])?$this->request->data['department_id']:$department_id;
            $dept_grout = $departments->find()->select(['id','name'])->toArray();
        }
        $this->set('dept_grout',$dept_grout);

        //显示服务列表
        $where = array();
        if($name){
            $where['service_name like'] ="%$name%";
        }
        $ServiceType = TableRegistry::get('ServiceType');

        //当显示全部租户下的服务时
        if($department_id==0){
            //系统权限的前提
            if(in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))){
                $query = $ServiceType->find('all')->contain(['Departments','ChargeTemplate'])->where($where);
                $department_data['name']='所有租户';
                //租户权限的前提
            }elseif(!in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname')) && in_array('cmop_global_tenant_admin',$this->request->session()->read('Auth.User.popedomname'))){
                $where['ServiceType.department_id']=$this->request->session()->read('Auth.User.department_id');
                $query = $ServiceType->find('all')->contain(['Departments','ChargeTemplate'])->where($where);
                $department_data = $departments->find()->select(['name'])->where(array('id'=>$this->request->session()->read('Auth.User.department_id')))->toArray();
            }
        }else{
            $where['ServiceType.department_id']=$department_id;
            if(in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))){
                $query = $ServiceType->find('all')->contain(['Departments','ChargeTemplate'])->where($where);
                $department_data = $departments->find()->select(['id','name'])->where(array('id'=>$department_id))->toArray();
            }
        }


        //是否是中式广信接口板
        $isctvit = Configure::read('isctvit');
        $this->set('isctvit',$isctvit);
        if(isset($query)){
            $rs=$this->paginate($query);
            $this->set('data',$rs->toArray());
        }else{
            $rs=$this->paginate();
        }

        if(isset($department_data)){
            $this->set('department_name',isset($department_data[0])?$department_data[0]['name']:$department_data['name']);
        }else{
            $this->set('department_name','');
        }
        $this->set('name',$name);

    }


    //查看信息
    public function check($id=0){
        $service_type = TableRegistry::get('ServiceType');
        if($id){
            $service_result = $service_type->find('all')->contain(['Departments','ChargeTemplate'])->where(array('type_id'=>$id))->toArray();
            $this->set('data',$service_result[0]);
        }

    }

    //添加修改服务基本信息
    public function addedit($id=0,$source=''){
        $service_type = TableRegistry::get('ServiceType');
        $departments = TableRegistry::get('Departments');
        $charge_template = TableRegistry::get('ChargeTemplate');
        if($this->request->is('get')){
            $department = $departments->find('all')->select(['id','name'])->toArray();
            $this->set('dept',$department);
            $template = $charge_template->find('all')->select(['id','template_name'])->toArray();
            $this->set('template',$template);
            if($id){
                $service_result = $service_type->find('all')->where(array('type_id'=>$id))->toArray();
                $this->set('data',$service_result[0]);
            }
            if(!empty($source)){
                $this->set('source',$source);
            }
        }else{
            $message = array('code'=>1,'msg'=>'操作失败');
            $public = new PublicController();
            if($this->request->data['min_instance']>$this->request->data['max_instance']){
                $message = array('code'=>1,'msg'=>'最小实例数量不能大于最大实例数量');
                echo json_encode($message);exit;
            }
            if(isset($this->request->data['type_id'])){
                $name = $service_type->find('all')->select(['type_id','service_name'])->where(array('service_name'=>$this->request->data['service_name']))->toArray();
                //判断修改的名字是否已经存在
                if($name){
                    foreach($name as $va){
                        if($va['type_id']!=$this->request->data['type_id'] && $va['service_name'] == $this->request->data['service_name']){
                            $message = array('code'=>1,'msg'=>'该服务名称已存在');
                            echo json_encode($message);exit;
                        }
                    }
                }
                $source=$this->request->data['source'];
                unset($this->request->data['source']);
                $services=$service_type->newEntity();
                $services = $service_type->patchEntity($services,$this->request->data);
                $t_result = $service_type->save($services);
                if($t_result){
                    if($source=='check'){
                        $message = array('code'=>0,'msg'=>'操作成功','source'=>1,'id'=>$this->request->data['type_id']);
                    }else{
                        $message = array('code'=>0,'msg'=>'操作成功','source'=>0);
                    }
                    $public->adminlog('ServiceType','修改服务类型---'.$this->request->data['service_name']);
                }
                echo json_encode($message);exit;
            }else{
                $count = $service_type->find('all')->select(['type_id'])->where(array('service_name'=>$this->request->data['service_name']))->count();
                //判断名字是否已存在
                if($count>0){
                    $message = array('code'=>1,'msg'=>'该服务名称已存在');
                    echo json_encode($message);exit;
                }
                unset($this->request->data['source']);
                $services=$service_type->newEntity();
                $services = $service_type->patchEntity($services,$this->request->data);
                $result = $service_type->save($services);
                if($result){
                    $message = array('code'=>0,'msg'=>'操作成功');
                    $public->adminlog('ServiceType','添加服务类型---'.$this->request->data['service_name']);
                }
                echo json_encode($message);exit;

            }
        }
    }

    //获取引入主机数据
    public function intrhost(){
        //接口返回的的主机关联
        $InstanceBasic = TableRegistry::get('InstanceBasic');
        $parameter['method']='ecs';
		$parameter['uid']=(string) $this->request->session()->read('Auth.User.id');
        $order = new OrdersController();
        $url = Configure::read('URL');
        $hostdata = $order->postInterface($url,$parameter);
		$hostdata = json_encode($hostdata);
      //  $hostdata = json_decode($IN_result,true);
		//$hostdata = json_decode(file_get_contents('data.txt'));
		$host_array['hostdata'] = array();
		if(!empty($hostdata)){
			foreach($hostdata->server as $value)
			$count = $InstanceBasic->find()->select(['id'])->where(array('code'=>$value->uuid))->count();
			if(!$count){
				$host_array['hostdata'][] = $value;
			}
		
		}
        return $host_array;
    }

    //引入主机关联
    public function introducehost($id=0,$page = 1){
        $limit = 15;
        if($page > 0){
            $offset = ($page-1)*$limit;
        }else {
            $offset = 0;
        }
        $host_array = $this->intrhost();
        $host_array['total']= ceil(count($host_array['hostdata'])/$limit);
        $host_array['hostdata'] = array_slice($host_array['hostdata'],$offset,$limit);
        $this->set('hostdata',$host_array);
        $this->set('page',$page);
        $this->set('server_id',$id);
    }

    //保存引入主机信息
    public function editintroduse(){
        $datas = $this->request->data;
        $message=array('code'=>1,'message'=>'引入主机失败');
        if(!empty($datas['checkHost'])){
            $data = explode(',',$datas['checkHost']);
            $data = array_filter($data);
            //获取所有映入主机的信息
            $host_array = $this->intrhost();
            if(!empty($host_array['hostdata'])){
                $host_array=$host_array['hostdata'];
                //基础信息表
                $instance_basic = TableRegistry::get('InstanceBasic');
                //主机继承表
                $host_extend = TableRegistry::get('HostExtend');
                //服务列表
                $service_list = TableRegistry::get('ServiceList');
                //获取当前登录的用户id及所属部门id
                $create_by = $this->request->session()->read('Auth.User.id');
                $department_id = $this->request->session()->read('Auth.User.department_id');
                //将获取的主机信息与页面上选择的id比对
                $i = 0;
                foreach($host_array as $value){
                    $instance =array();
                    $host =array();
                    $service=array();
                    if(in_array($value->id,$data)){
                        //保存基础信息
                        $instance['name']=$value->title;
                        $instance['code']=$value->uuid;
                        $instance['create_time']= strtotime($value->createTime);
                        $instance['subnet']=$value->networkUuid;
                        $instance['create_by']=$create_by;
                        $instance['department_id']=$department_id;
                        $basic = $instance_basic->newEntity();
                        $basic = $instance_basic->patchEntity($basic,$instance);
                        $result_basic = $instance_basic->save($basic);
                        //保存主机继承信息
                        if($result_basic){
                            $host['image_code']=$value->imageUuid;
                            $host['vxnets']=$value->networkUuid;
                            $host['type']=$value->flavorUuid;
                            $host['ip']=$value->privateIp;
                            $host['vxnets_count']=$value->casId;
                            $host['name']=$value->name;
                            $host['basic_id']= $result_basic['id'];
                            $extend = $host_extend->newEntity();
                            $extend = $host_extend->patchEntity($extend,$host);
                            $result_extend = $host_extend->save($extend);
                            if($result_extend){
                                //保存服务列表信息
                                $service['type_id']=$datas['server_id'];
                                $service['basic_id']=$result_basic['id'];
                                $servicelist = $service_list->newEntity();
                                $servicelist = $service_list->patchEntity($servicelist,$service);
                                $result_service = $service_list->save($servicelist);
                                if($result_service){
                                    $i++;
                                }
                            }
                        }

                    }
                }
                if($i == count($data)){
                    $public = new PublicController();
                    $message=array('code'=>0,'message'=>'引入主机成功');
                    $public->adminlog('ServiceType','引入主机操作');
                }
            }
        }
        echo json_encode($message);exit();
        $this->lauout = 'ajax';

    }

    //数据库已存在的主机显示
    public function editdevice($id=0,$page = 1){
        //数据库已有的主机关联
        $limit = 15;
        if($page > 0){
            $offset = $page-1;
        }else {
            $offset = 0;
        }
        $offset = $offset*$limit;
        $service_list = TableRegistry::get('ServiceList');
        $connection = ConnectionManager::get('default');
        $departments = TableRegistry::get('Departments');
        //返回区域
        $agent = TableRegistry::get('Agent');
        $agents = $agent->find('all')->toArray();
        $this->set('agent',$agents);
        //返回租户
        $where = '';
        $dept_grout =array();
        if(in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))){
        	//$department_id = isset($this->request->data['department_id'])?$this->request->data['department_id']:$department_id;
        
        	$dept_grout = $departments->find()->select(['id','name'])->toArray();
        }
        $where = ' and a.department_id = '.$this->request->session()->read('Auth.User.department_id');
        $department_data['name']=$this->request->session()->read('Auth.User.department_name');
        $department_data['id']=$this->request->session()->read('Auth.User.department_id');
        $this->set('dept_grout',$dept_grout);
        if(isset($department_data)){
        
        	$this->set('department_name',$department_data['name']);
        	$this->set('department_id',$department_data['id']);
        }else{
        	$this->set('department_name','');
        	$this->set('department_id',0);
        }
        
        //获取数据路已有的主机信息
        $sql = ' SELECT a.id AS basic_id,a.`name` AS devicename,a.location_name,a.subnet,a.`code`,c.`name` as dept_name,b.`name` AS hostname,b.ip AS ip FROM cp_instance_basic as a';

        $sql .=' LEFT JOIN cp_host_extend as b ON a.id = b.basic_id LEFT JOIN cp_departments as c ON a.department_id = c.id ';
        $sql .='  WHERE (a.type = "hosts" or a.type = "desktop") and a.status="运行中" '.$where;
        //var_dump($sql);exit;
        $sql_row = $sql . " limit " . $offset . "," . $limit;

        $i = ceil($connection->execute($sql)->count()/$limit);
        $data['hosts']['total'] = $i;
        $data['hosts']['data'] = $connection->execute($sql_row)->fetchAll('assoc');//获取主机信息
        $this->set('page',$page);
        $this->set('query',$data);
        $this->set('server_id',$id);
        //显示服务已关联的主机
        if($id){
            $basic_id  = $service_list->find('all')->select(['basic_id'])->where(array('type_id'=> $id))->toArray();//获取该服务关联的主机id
            $basicID = array();
            if($basic_id){
                foreach ($basic_id as $key => $value) {
                    $basicID[]=$value->basic_id;
                }
            }
            $this->set('basic_id',implode(',',$basicID));
        }

    }

    //保存与服务关联数据库已有的主机
    public function posthost(){
        $data = $this->request->data;
        $message=array('code'=>1,'message'=>'关联主机失败');
        //服务表
        $service_list = TableRegistry::get('ServiceList');
        $server_id = !empty($data['server_id'])?$data['server_id']:0;
        $count = $service_list->find()->select(['service_id'])->where(array('type_id'=>$server_id))->count();
        //先删除原有的主机服务关联
        $result = $service_list->deleteAll(array('type_id'=>$server_id));
        if($count == $result){
            $public = new PublicController();
            if(!empty($data['checkHost'])){
                //保存新的关联
                $service = explode(',',$data['checkHost']);
                $service = array_filter($service);
                $i=0;
                foreach($service as $value){
                    $service_data['type_id'] = $server_id;
                    $service_data['basic_id'] = $value;
                    $servicelist = $service_list->newEntity();
                    $servicelist = $service_list->patchEntity($servicelist,$service_data);
                    $result_service = $service_list->save($servicelist);
                    if($result_service){
                        $i++;
                    }
                }

                if($i == count($service)){
                    $message=array('code'=>0,'message'=>'关联主机成功');
                    $public->adminlog('ServiceType','保存关联主机操作');
                }
            }else{
                $message=array('code'=>0,'message'=>'关联主机成功');
                $public->adminlog('ServiceType','保存关联主机操作');
            }

        }
        echo json_encode($message);exit();
        $this->lauout = 'ajax';
    }

    //json获取关联主机分页
    public function checkhost($page =1,$department=0,$type='total',$class_code='total',$class_code2='total',$name=''){
        $limit = 15;
        if($page > 0){
            $offset = ($page-1)*$limit;
        }else {
            $offset = 0;
        }

        $connection = ConnectionManager::get('default');
       
        //加入设备类型判断
        $types = '(a.type = "hosts"  or a.type = "desktop")';
        if($type != 'total'){
        	$types = 'a.type = "'.$type.'"';
        }
        //加入租户判断
        $where ='';
        if($department){
        	$where = ' and a.department_id = '.$department;
        }
        
        $code ='';
        if($class_code != 'total'){
        	$code = ' and a.location_code like "'.$class_code.'%"';
        }
        
        $code2 ='';
        if($class_code2 != 'total'){
        	$code = ' and a.location_code like "'.$class_code2.'%"';
        }
        
        //获取数据路已有的主机信息
        $sql = ' SELECT a.id AS basic_id,a.`name` AS devicename,a.location_name,a.subnet,a.`code`,c.`name` as dept_name,b.`name` AS hostname,b.ip AS ip FROM cp_instance_basic as a';

        $sql .=' LEFT JOIN cp_host_extend as b ON a.id = b.basic_id LEFT JOIN cp_departments as c ON a.department_id = c.id ';
        $sql .='  WHERE '.$types.$where.$code.$code2 .' and a.status="运行中" ';
     
        if($name){
            $sql.=" and (a.name like '%".$name."%' or b.name like '%".$name."%' or a.code like '%".$name."%')";
        }
        $sql_row = $sql . " limit " . $offset . "," . $limit;
        $i = ceil($connection->execute($sql)->count()/$limit);
        $data['total'] = $i;
        $data['data'] = $connection->execute($sql_row)->fetchAll('assoc');//获取桌面信息
        $data['page'] = $page;
        echo json_encode($data);exit();
        $this->lauout = 'ajax';
    }

    //json获取引入主机分页
    public function introduce($page =1,$name=''){
        $limit = 15;
        if($page > 0){
            $offset = ($page-1)*$limit;
        }else {
            $offset = 0;
        }
        $host_array = $this->intrhost();
        $host_array['total']= ceil(count($host_array['hostdata'])/$limit);
        $host_array['hostdata'] = array_slice($host_array['hostdata'],$offset,$limit);
        echo json_encode($host_array);exit();
        $this->lauout = 'ajax';
    }


    //删除服务及关联关系
    public function deletes(){
        $message=array('code'=>1,'message'=>'删除服务及关联主机失败');
        $data = $this->request->data;
        $ServiceType = TableRegistry::get('ServiceType');
        $service_list = TableRegistry::get('ServiceList');
        $sername = $ServiceType->find()->select(['service_name'])->where(array(array('type_id'=>$data['id'])))->toArray();
        $result = $ServiceType->deleteAll(array('type_id'=>$data['id']));
        $public = new PublicController();
        if($result){
            $count = $service_list->find()->select(['service_id'])->where(array('type_id'=>$data['id']))->count();
            //先删除原有的主机服务关联
            $results = $service_list->deleteAll(array('type_id'=>$data['id']));
            if($count == $results){
                $message=array('code'=>0,'message'=>'删除服务及关联主机成功');
            }
            $public->adminlog('ServiceType','删除服务类型---'.$sername[0]['service_name']);
        }
        echo json_encode($message);exit();
    }


}