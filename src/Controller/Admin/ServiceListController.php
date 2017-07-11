<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2016/1/5
 * Time: 14:41
 */

namespace App\Controller\Admin;


use App\Controller\AdminController;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

class ServiceListController extends AdminController{
    public $paginate = [
        'limit' => 15,
    ];

    //列表显示
    public function index($name=''){
        $SoftwareList = TableRegistry::get('ServiceList');
        $where = array();
        if($name){
            $where['service_name like'] = '%'.$name.'%';
        }
        $query = $SoftwareList->find('all',array('order' => array('sort_order') ))->where($where);
        $data=$this->paginate($query);
        $this->set('name',$name);
        $this->set('data',$data);
    }

    //添加修改服务基本信息
    public function addedit($id=0){
        $service_type = TableRegistry::get('ServiceType');
        if($this->request->is('get')){
            if($id){
                $service_result = $service_type->find('all')->where(array('type_id'=>$id))->toArray();
                $this->set('data',$service_result[0]);
            }
        }else{
            $message = array('code'=>1,'msg'=>'操作失败');
            $services=$service_type->newEntity();
            if(isset($this->request->data['type_id'])){
                $name = $service_type->find('all')->select(['type_id','service_name'])->where(array('service_name'=>$this->request->data['service_name']))->toArray();
                //判断修改的名字是否已经存在
                if($name){
                    foreach($name as $va){
                        if($va['type_id']!=$this->request->data['type_id'] && $va['service_name'] == $this->request->data['service_name']){
                            $message = array('code'=>1,'msg'=>'该服务已存在');
                            echo json_encode($message);exit;
                        }
                    }
                }
                $tid = $this->request->data['type_id'];
                $t_result = $service_type->updateAll($this->request->data,array('type_id'=>$tid));
                if($t_result){
                    $message = array('code'=>0,'msg'=>'操作成功');
                }
                echo json_encode($message);exit;
            }else{
                $count = $service_type->find('all')->select(['type_id'])->where(array('service_name'=>$this->request->data['service_name']))->count();
                //判断名字是否已存在
                if($count>0){
                    $message = array('code'=>1,'msg'=>'该服务已存在');
                    echo json_encode($message);exit;
                }
                $services = $service_type->patchEntity($services,$this->request->data);
                $result = $service_type->save($services);
                if($result){
                    $message = array('code'=>0,'msg'=>'操作成功');
                }
                echo json_encode($message);exit;

            }
        }
    }

    //引入主机关联
    //关联主机
    public function editdevice($id=0,$page = 1){
        //接口返回的的主机关联
        $InstanceBasic = TableRegistry::get('InstanceBasic');
        $hostdata = json_decode(file_get_contents('data.txt'));
        $host_array=array();
        foreach($hostdata->server as $value){
            $count = $InstanceBasic->find()->select(['id'])->where(array('code'=>$value->uuid))->count();
            if(!$count){
                $host_array['hostdata'][] = $value;
            }
        }
        $host_array['total']= ceil(count($host_array['hostdata'])/15);
        $host_array['hostdata'] = array_slice($host_array['hostdata'],0,15);

        //数据库已有的主机关联
        $limit = 15;
        if($page > 0){
            $offset = $page-1;
        }else {
            $offset = 0;
        }
        $offset = $offset*$limit;
        $SoftwareList = TableRegistry::get('SoftwareList');
        $SoftwaresDesktop = TableRegistry::get('SoftwaresDesktop');
        $DesktopExtend = TableRegistry::get('DesktopExtend');

        $connection = ConnectionManager::get('default');
        $sql = " SELECT cp_instance_basic.id AS basic_id,cp_instance_basic.`name` AS devicename,cp_host_extend.`name` AS hostname,cp_host_extend.ip AS ip FROM cp_instance_basic";

        $sql .=" LEFT JOIN cp_host_extend ON cp_instance_basic.id = cp_host_extend.basic_id WHERE cp_instance_basic.type = 'hosts'";
        $sql_row = $sql . " limit " . $offset . "," . $limit;

        $i = ceil($connection->execute($sql)->count()/$limit);
        $data['hosts']['total'] = $i;
        $data['hosts']['data'] = $connection->execute($sql_row)->fetchAll('assoc');//获取桌面信息
        $this->set('page',$page);
        $this->set('hostdata',$host_array);
        $this->set('query',$data);
       /* //编辑时
        if($id){
            $department_data['SoftwareList']  = $SoftwareList->find('all')->where(array('id'=> $id))->toArray();//获取桌面应用信息
            $query['SoftwaresDesktop'] = $SoftwaresDesktop->find('all')->where(array('software_id'=> $id))->toArray();//获取应用桌面对呀的桌面
            foreach ($query['SoftwaresDesktop'] as $key => $value) {
                $department_data['host_id'][]=$value->host_id;
            }
            $this->set('department_data',$department_data);
        }*/

    }

    //json获取分页
    public function checkhost($page =1,$name=''){

        $limit = 15;
        if($page > 0){
            $offset = $page-1;
        }else {
            $offset = 0;
        }
        $offset = $offset*$limit;
        $DesktopExtend = TableRegistry::get('DesktopExtend');
        $connection = ConnectionManager::get('default');
        $sql = "SELECT cp_host_extend.id AS id, cp_instance_basic.name AS name, cp_departments.`name` AS department_name, cp_instance_basic.location_name AS location_name FROM cp_instance_basic";

        $sql .=" LEFT JOIN cp_departments on cp_instance_basic.department_id = cp_departments.id LEFT JOIN cp_host_extend ON cp_instance_basic.id = cp_host_extend.basic_id WHERE cp_instance_basic.type = 'desktop'";
        $sql .=" AND cp_instance_basic.name like'%".$name."%'";
        $sql_row = $sql . " limit " . $offset . "," . $limit;
        $i = ceil($connection->execute($sql)->count()/$limit);
        $data['total'] = $i;
        $data['data'] = $connection->execute($sql_row)->fetchAll('assoc');//获取桌面信息
        $data['page'] = $page;
        echo json_encode($data);exit();
        $this->lauout = 'ajax';
    }



}