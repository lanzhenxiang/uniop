<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/8
 * Time: 15:54
 */

namespace App\Controller\Admin;
use App\Controller\AdminController;
use App\Controller\SobeyController;
use Cake\ORM\TableRegistry;
use Cake\Network\Exception\BadRequestException;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Configure;

class SoftwareListController extends AdminController{

    public $paginate = [
    'limit' => 15,
    ];
    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_para_apps');
        if (! $checkPopedomlist) {
            return $this->redirect('/admin/');
        }
    }
    public function index($name=''){
        $SoftwareList = TableRegistry::get('SoftwareList');
        $where = array();
        if($name){
            $where['OR'] =array('software_name like'=>"%$name%",'software_code like'=>"%$name%");
        }
        $query = $SoftwareList->find('all',array('order' => array('sort_order') ))->where($where);
        $data=$this->paginate($query);
        $this->set('name',$name);
        $this->set('data',$data);
    }

    public function addedit($id=0,$page = 1){
        $limit = 15;
        if($page > 0){
            $offset = $page-1;
        }else {
            $offset = 0;
        }
        $public = new PublicController();
        $offset = $offset*$limit;
        $SoftwareList = TableRegistry::get('SoftwareList');
        $SoftwaresDesktop = TableRegistry::get('SoftwaresDesktop');
        $DesktopExtend = TableRegistry::get('HostExtend');

        $connection = ConnectionManager::get('default');
        $sql = "SELECT cp_host_extend.id AS id, cp_instance_basic.name AS name, cp_departments.`name` AS department_name, cp_instance_basic.location_name AS location_name FROM cp_instance_basic";

        $sql .=" LEFT JOIN cp_departments on cp_instance_basic.department_id = cp_departments.id LEFT JOIN cp_host_extend ON cp_instance_basic.id = cp_host_extend.basic_id WHERE cp_instance_basic.type = 'desktop' AND cp_host_extend.id <> '' AND cp_instance_basic.code <> '' AND cp_instance_basic.isdelete <> '1' ";
        $sql_row = $sql . " limit " . $offset . "," . $limit;

        if($this->request->is('get')){
            // $data['DesktopExtend'] = $DesktopExtend->find('all')->where(array('name <>'=> ''))->toArray();//获取应用桌面对呀的桌面
            $i = ceil($connection->execute($sql)->count()/$limit);
            if($i<=0){
                $i=1;
            }
            $data['DesktopExtend']['total'] = $i;
            $data['DesktopExtend']['data'] = $connection->execute($sql_row)->fetchAll('assoc');//获取桌面信息
            $this->set('page',$page);
            $this->set('query',$data);
             //编辑时
            if($id){
                $department_data['SoftwareList']  = $SoftwareList->find('all')->where(array('id'=> $id))->toArray();//获取桌面应用信息
                $query['SoftwaresDesktop'] = $SoftwaresDesktop->find('all')->where(array('software_id'=> $id))->toArray();//获取应用桌面对呀的桌面
                foreach ($query['SoftwaresDesktop'] as $key => $value) {
                    $department_data['host_id'][]=$value->host_id;
                }
                $this->set('department_data',$department_data);
            }
        }else{
            //var_dump($this->request->data);exit;
            if(empty($this->request->data['id'])){
                $where =[];
            }else{
                $where['id <>'] =$this->request->data['id'];
            }
            $name = $this->request->data['software_name'];
            $code = $this->request->data['software_code'];
            $checkName = $SoftwareList->find()->where(['software_name' => $name])->where($where)->first();
            if(!empty($checkName)){
                $message = array('code'=>1,'msg'=>'软件名称不能重复');
                echo json_encode($message);exit();
            }
            $checkCode = $SoftwareList->find()->where(['software_code' => $code])->where($where)->first();
            if(!empty($checkCode)){
                $message = array('code'=>1,'msg'=>'软件code不能重复');
                echo json_encode($message);exit();
            }
            $check_desktop=explode(',', $this->request->data['checkDesktop']);
            $message = array('code'=>1,'msg'=>'操作失败');
            $order = $SoftwareList->newEntity();
            $order = $SoftwareList->patchEntity($order,$this->request->data);
            $result = $SoftwareList->save($order);
            $info['software_id']=(string)$result->id;
            if($result){
                if(empty($this->request->data['id'])){
                    $public->adminlog('SoftwareList','添加桌面类型---'.$this->request->data['software_name']);
                }else{
                    $public->adminlog('SoftwareList','修改桌面类型---'.$this->request->data['software_name']);
                }
                $res = $SoftwaresDesktop->deleteAll(array('software_id'=>$info['software_id']));
                $rs = '';
                foreach ($check_desktop as $key => $desktop) {
                    if(!empty($desktop)){
                        $info['host_id']=$desktop;
                        $softwaredsktopinfo = $SoftwaresDesktop->newEntity();
                        $softwaredsktopinfo = $SoftwaresDesktop->patchEntity($softwaredsktopinfo,$info);
                        $rs = $SoftwaresDesktop->save($softwaredsktopinfo);

                    }
                }
                if($rs){
                    $public->adminlog('SoftwareList','修改桌面类型的应用桌面---'.$this->request->data['software_name']);
                }else if($res >0){
                    $public->adminlog('SoftwareList','删除桌面类型的应用桌面---'.$this->request->data['software_name']);
                }
                $message = array('code'=>0,'msg'=>'操作成功');
            }
            echo json_encode($message);exit();
        }
    }

    //删除
    public function dele(){
        $public = new PublicController();
        $this->layout = false;
        if($this->request->data['id']){
            $id=$this->request->data['id'];
            $SoftwareList = TableRegistry::get('SoftwareList');
            $SoftwaresDesktop = TableRegistry::get('SoftwaresDesktop');
            $SoftwaresDesktopInfo = $SoftwaresDesktop->find()->where(['software_id' =>$id])->first();
            if($SoftwaresDesktopInfo){
                $message = array('code'=>1,'msg'=>'该桌面类型绑定了云桌面');
                echo json_encode($message);exit();
            }
            $data = $SoftwareList->find()->where(['id'=>$id])->first();
            $res = $SoftwareList->deleteAll(array('id'=>$id));
            if($res){
                $result = $SoftwaresDesktop->deleteAll(array('software_id'=>$id));
                $message = array('code'=>0,'msg'=>'操作成功');
                $public->adminlog('SoftwareList','删除桌面类型---'.$data['software_name']);
            }
            echo json_encode($message);exit();
        }

    }


    //获取桌面信息
    public function checkDesktop($page =1,$name=''){

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

        $sql .=" LEFT JOIN cp_departments on cp_instance_basic.department_id = cp_departments.id LEFT JOIN cp_host_extend ON cp_instance_basic.id = cp_host_extend.basic_id WHERE cp_instance_basic.type = 'desktop' AND cp_host_extend.id <> '' AND cp_instance_basic.code <> '' AND cp_instance_basic.isdelete <> '1'";
        $sql .=" AND cp_instance_basic.name like'%".$name."%'";
        $sql_row = $sql . " limit " . $offset . "," . $limit;
        $i = ceil($connection->execute($sql)->count()/$limit);
        $data['total'] = $i;
        $data['data'] = $connection->execute($sql_row)->fetchAll('assoc');//获取桌面信息
        $data['page'] = $page;
        echo json_encode($data);exit();
        $this->lauout = 'ajax';
    }

    // public function

}