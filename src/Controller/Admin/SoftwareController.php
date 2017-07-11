<?php
/**
 * Created by PhpStorm.
 * User: kelly
 * Date: 2016/12/23
 * Time: 11:35
 */
namespace App\Controller\Admin;


use App\Controller\AccountsController;
use App\Controller\OrdersController;
use App\Controller\SobeyController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use App\Controller\AdminController;

class SoftwareController extends AdminController
{
    public $_pageList = array(
        'total' => 0,
        'rows' => array()
    );

    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_tenant_adusers');
        if (!$checkPopedomlist) {
            return $this->redirect('/admin/');
        }
    }

    public function index()
    {

    }

    public function lists()
    {
        $software = TableRegistry::get('SoftwareList');
        $request = $this->request->query;
        $where = array();
        if (isset($request['search']) && trim($request['search']) != '') {
            $search = $request['search'];
            $where['or'] = array(
                'SoftwareList.software_name like' => "%$search%",
                'SoftwareList.product_name like' => "%$search%",
            );
        }
        $this->_pageList['total'] = $software->find()->where(array($where))->order('sort_order')->count();
        $this->_pageList['rows'] = $software->find()->where(array($where))->order('sort_order')->offset($request['offset'])->limit($request['limit'])->map(function ($row) {

            //添加创建人
            $row['create_name'] = TableRegistry::get('Accounts')->find()->select(['username'])->where(array('Accounts.id' => $row['create_by']))->first()['username'];
            //修改时间格式
            if (!empty($row['create_time'])) {
                $row['create_time'] = date('Y-m-d H:i:s', $row['create_time']);
            } else {
                $row['create_time'] = '-';
            }

            //限制备注字数
            $row['note'] = mb_substr($row['note'], 0, 20);

            return $row;
        });
        echo json_encode($this->_pageList);
        exit();

    }

    public function addsoft()
    {


    }

    public function postadd()
    {
        $request = $this->request->data;
        $software = TableRegistry::get('SoftwareList');
        $public = new PublicController();
        $exist = $software->find()->select(['id'])->where(array('software_name' => $request['software_name']))->count();
        if ($exist > 0) {
            echo json_encode(array('code' => 2, 'msg' => '该分类名已存在'));
            exit;
        }
        $data['software_name'] = $request['software_name'];
        $data['product_name'] = $request['product_name'];
        $data['sort_order'] = $request['sort_order'];
        $data['note'] = $request['note'];
        $data['icon_file'] = $request['icon_file'];
        $data['create_by'] = $this->request->session()->read('Auth.User.id');
        $data['create_time'] = time();

        $soft_data = $software->newEntity();
        $soft_data = $software->patchEntity($soft_data, $data);
        $result = $software->save($soft_data);
        if ($result) {
            $public->adminlog('Software', '新建工具分类成功');
            echo json_encode(array('code' => 0, 'msg' => '新建工具分类成功'));
            exit();
        } else {
            $public->adminlog('Software', '新建工具分类失败');
            echo json_encode(array('code' => 1, 'msg' => '新建工具分类失败'));
            exit();
        }
    }

    //修改
    public function editsoft()
    {
        if (isset($this->request->query['id']) && $this->request->query['id'] != '') {
            $id = $this->request->query['id'];
        } else {
            $id = 0;
        }
        $this->set('id', $id);
        $software = TableRegistry::get('SoftwareList');
        $data = $software->find()->where(array('id' => $id))->first();
        if (empty($data)) {
            $data = array(
                'software_name' => '',
                'product_name' => '',
                'sort_order' => '',
                'icon_file' => '',
                'note' => ''
            );
        }
        $this->set('data', $data);

    }

    public function postedit()
    {
        $request = $this->request->data;
        $software = TableRegistry::get('SoftwareList');
        $public = new PublicController();
        $id = $request['id'];
        $data = array();
        if (isset($request['software_name']) && $request['software_name'] != '') {
            $data['software_name'] = $request['software_name'];
        }
        if (isset($request['product_name']) && $request['product_name'] != '') {
            $data['product_name'] = $request['product_name'];
        }
        if (isset($request['icon_file']) && $request['icon_file'] != '') {
            $data['icon_file'] = $request['icon_file'];
        }
        if (isset($request['sort_order']) && $request['sort_order'] != '') {
            $data['sort_order'] = $request['sort_order'];
        }
        if (isset($request['note']) && $request['note'] != '') {
            $data['note'] = $request['note'];
        }
        $result = $software->updateAll($data, array('id' => $id));
        if ($result) {
            $public->adminlog('Software', '修改工具分类成功');
            echo json_encode(array('code' => 0, 'msg' => '修改工具分类成功'));
            exit();
        } else {
            $public->adminlog('Software', '修改工具分类失败');
            echo json_encode(array('code' => 1, 'msg' => '修改工具分类失败'));
            exit();
        }

    }

    //关联云桌面
    public function connectdesk($page = 1)
    {
        $department = TableRegistry::get('Departments');
        $connection = ConnectionManager::get('default');
        $software = TableRegistry::get('SoftwareList');
        $SoftwaresDesktop = TableRegistry::get('SoftwaresDesktop');
        $depart = $department->find()->select(['id', 'name', 'parent_id'])->toArray();
        $this->set('depart',$depart);//租户
        if(isset($this->request->query['id'])&&$this->request->query['id']!=''){
            $id=$this->request->query['id'];
        }else{
            $id=$this->request->query['id'];
        }
        $this->set('id',$id);//id
        $softname=$software->find()->select(['software_name'])->where(array('id'=>$id))->first()['software_name'];
        $this->set('softname',$softname);//工具名
        if(isset($this->request->query['department_id'])){
            $department_id=$this->request->query['department_id'];
        }else{
            $department_id=0;
        }
        $this->set('department_selected',$department_id);//工具id

        $limit = 15;
        if($page > 0){
            $offset = $page-1;
        }else {
            $offset = 0;
        }
        $offset = $offset*$limit;
        $sql = "SELECT cp_host_extend.id AS id, cp_instance_basic.name AS name, cp_departments.`name` AS department_name, cp_instance_basic.location_name AS location_name,cp_instance_basic.code AS code, cp_instance_basic.vpc AS vpc,cp_instance_basic.subnet AS subnet FROM cp_instance_basic";

        $sql .=" LEFT JOIN cp_departments on cp_instance_basic.department_id = cp_departments.id LEFT JOIN cp_host_extend ON cp_instance_basic.id = cp_host_extend.basic_id WHERE cp_instance_basic.type = 'desktop' AND cp_host_extend.id <> '' AND cp_instance_basic.code <> '' AND cp_instance_basic.isdelete <> '1' ";
        $sql_row = $sql . " limit " . $offset . "," . $limit;
        if($this->request->is('get')){
            $i = ceil($connection->execute($sql)->count()/$limit);
            if($i<=0){
                $i=1;
            }
            $data['DesktopExtend']['total'] = $i;
            $data['DesktopExtend']['data'] = $connection->execute($sql_row)->fetchAll('assoc');//获取桌面信息
            $this->set('page',$page);
            $this->set('query',$data);
            if($id){
                $department_data['SoftwareList']  = $software->find('all')->where(array('id'=> $id))->toArray();//获取桌面应用信息
                $query['SoftwaresDesktop'] = $SoftwaresDesktop->find('all')->where(array('software_id'=> $id))->toArray();//获取应用桌面对呀的桌面
                foreach ($query['SoftwaresDesktop'] as $key => $value) {
                    $department_data['host_id'][]=$value->host_id;
                }
                $this->set('department_data',$department_data);
            }
        }



    }
    public function checkDesktop($page =1,$department_id='',$name=''){

        $limit = 15;
        if($page > 0){
            $offset = $page-1;
        }else {
            $offset = 0;
        }
        $offset = $offset*$limit;
        // $connection = ConnectionManager::get('default');
        // $sql = "SELECT cp_host_extend.id AS id, cp_instance_basic.name AS name,cp_instance_basic.code AS code,  cp_departments.`name` AS department_name, cp_instance_basic.location_name AS location_name,cp_instance_basic.vpc AS vpc,cp_instance_basic.subnet AS subnet FROM cp_instance_basic";

        // $sql .=" LEFT JOIN cp_departments on cp_instance_basic.department_id = cp_departments.id LEFT JOIN cp_host_extend ON cp_instance_basic.id = cp_host_extend.basic_id WHERE cp_instance_basic.type = 'desktop' AND cp_host_extend.id <> '' AND cp_instance_basic.code <> '' AND cp_instance_basic.isdelete <> '1'";
        // $sql .=" AND (cp_instance_basic.name like'%".$name."%' or cp_instance_basic.code like'%".$name."%')";
        // if($department_id!=0) {
        //     $sql .= " AND (cp_instance_basic.department_id=$department_id )";
        // }
        // $sql_row = $sql . " limit " . $offset . "," . $limit;
        
        if($department_id!=0) {
            $where['InstanceBasic.department_id'] = $department_id;
        }
        $andwhere = [];
        if ($name != "") {
            $andwhere['OR'] = [
                ['InstanceBasic.name like ' => '%' . $request_data['search'] . '%'],
                ['InstanceBasic.code like ' => '%' . $request_data['search'] . '%'],
            ];
        }

        $where['InstanceBasic.type'] = 'desktop';
        $where['hostExtend.id <>'] = '';
        $where['InstanceBasic.code <>'] = '';
        $where['InstanceBasic.isdelete <>'] = '1';

        $field = [
            'id'                => 'hostExtend.id',
            'code'              => 'InstanceBasic.code',
            'name'              => 'InstanceBasic.name',
            'department_name'   => 'departments.name',
            'location_name'     => 'InstanceBasic.location_name',
            'vpc'               => 'InstanceBasic.vpc',
            'subnet'               => 'InstanceBasic.subnet',
        ];

        $instanceBasic = TableRegistry::get("InstanceBasic");

        $query =$instanceBasic->initJoinQuery()
            ->joinDepartments()
            ->joinHostExtend()
            
            ->getJoinQuery()
            ->select($field)
            ->where($where)->andWhere($andwhere)->offset($offset)->limit($limit);

        $i = ceil($query->count()/$limit);
        $data['total'] = $i;
        $data['data'] = $query;//获取桌面信息
        $data['page'] = $page;
        echo json_encode($data);exit();
        $this->lauout = 'ajax';



    }

    public function postconnect()
    {
        $check_desktop=explode(',', $this->request->data['checkDesktop']);
        $SoftwaresDesktop = TableRegistry::get('SoftwaresDesktop');
        $public = new PublicController();
//        var_dump($check_desktop);exit;
        $info['software_id']=$this->request->data['software_id'];
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
            $public->adminlog('Software', '关联云桌面成功');
            echo json_encode(array('code'=>0,'msg'=>'操作成功'));exit;
        }else{
            $public->adminlog('Software', '关联云桌面失败');
            echo json_encode(array('code'=>0,'msg'=>'操作失败'));exit;
        }
    }

    //是否关联云桌面
    public function getDesktop(){
        $SoftwaresDesktop = TableRegistry::get('SoftwaresDesktop');
        $ids=$this->request->query['ids'];
        $ids=explode(',',rtrim($ids,','));
        $count=0;
        foreach($ids as $key =>$value){
            $exist=$SoftwaresDesktop->find()->select(['id'])->where(array('software_id'=>$value))->count();
            if($exist>0){
                $count+=1;
            }
        }
        if($count>0){
            echo json_encode(array('code'=>1,'msg'=>'选中工具分类中有'.$count.'个已关联云桌面,请解绑后删除'));exit;
        }else{
            echo json_encode(array('code'=>0,'msg'=>'未关联云桌面'));exit;
        }
    }

//删除
    public function delete()
    {
        $request = $this->request->data;
        $public = new PublicController();
        $software = TableRegistry::get('SoftwareList');
        $SoftwaresDesktop = TableRegistry::get('SoftwaresDesktop');
        //删除个数
        $count = 0;
        foreach ($request['rows'] as $key => $value) {
            $id=$value['id'];
            $SoftwaresDesktopInfo = $SoftwaresDesktop->find()->where(['software_id' =>$id])->first();
            if($SoftwaresDesktopInfo){
                $public->adminlog('Software',$value['software_name'].'绑定了云桌面');
            }else{
                $result = $software->deleteAll(array('id'=>$id));
                if($result){
                    $count+=1;
                }
            }
        }
        if ($count > 0) {
                $public->adminlog('Software', '删除' . $count . '个分类');
                echo json_encode(array('code' => 0, 'msg' => '成功删除' . $count . '条数据'));
                exit;

        } else {
                $public->adminlog('Software', '删除分类失败');
                echo json_encode(array('code' => 1, 'msg' => '删除分类失败'));
                exit;
            }
    }

}