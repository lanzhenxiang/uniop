<?php
/**
 * Created by PhpStorm.
 * User: kelly
 * Date: 2016/12/22
 * Time: 9:42
 */
namespace App\Controller\Admin;


use App\Controller\AccountsController;
use App\Controller\SobeyController;
use Aura\Intl\PackageLocator;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use App\Controller\Admin\HomeController;
use App\Controller\AdminController;

class RoleController extends AdminController
{
    public $paginate = [
        'limit' => 15,
    ];
    public $_pageList = array(
        'total' => 0,
        'rows' => array()
    );

    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_tenant_roles');
        if (!$checkPopedomlist) {
            return $this->redirect('/admin/');
        }
    }

    public function index()
    {

    }

    public function lists()
    {
        $request = $this->request->query;
        $roles = TableRegistry::get('Roles');
        $where = array();
//        //是否为系统管理员
//        if(!in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))) {
//            $where['Roles.department_id'] = $this->request->session()->read('Auth.User.department_id');
//        }
        if (isset($request['search']) && trim($request['search']) != '') {
            $search = $request['search'];
            $where['or'] = array(
                'Roles.name like' => "%$search%"
            );
        }
        $this->_pageList['total'] = $roles->find()->where(array($where))->count();
        $this->_pageList['rows'] = $roles->find()->where(array($where))->offset($request['offset'])->limit($request['limit'])->map(function ($row) {
            //翻译创建人
            $row['create_name'] = TableRegistry::get('Accounts')->find()->select(['username'])->where(array('id' => $row['create_by']))->first()['username'];

            return $row;
        });

        echo json_encode($this->_pageList);
        exit();
    }

    //新建
    public function addrole()
    {

    }

    public function postadd()
    {
        $roles = TableRegistry::get('Roles');
        $public = new PublicController();
        $request = $this->request->data;
        if (!isset($request['name']) || empty($request['name'])) {
            echo json_encode(array('code' => 3, 'msg' => '角色名不能为空'));
            exit;
        }
        $data['name'] = $request['name'];
        $data['note'] = $request['note'];
        //是否为系统管理员
        if(!in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))) {
            $data['department_id'] = $this->request->session()->read('Auth.User.department_id');
        }else{
            $data['department_id'] =0;
        }
        $data['create_by'] = $this->request->session()->read('Auth.User.id');
        $time = date('Y-m-d H:i:s');
        //判断角色名是否存在
        $count = $roles->find('all')->select(['id'])->where(array('name' => $this->request->data['name']))->count();
        if ($count > 0) {
            echo json_encode(array('code' => 2, 'msg' => '该角色名已存在'));
            exit;
        } else {
            $role_data = $roles->newEntity();
            $role_data = $roles->patchEntity($role_data, $data);
            $role_data['created'] = $time;
            $role_data['modified'] = $time;
            $result = $roles->save($role_data);
            if ($result) {
                $public->adminlog('角色列表', '新建角色成功');
                echo json_encode(array('code' => 0, 'msg' => '新建角色成功'));
                exit;
            } else {
                $public->adminlog('角色列表', '新建角色失败');
                echo json_encode(array('code' => 1, 'msg' => '新建角色失败'));
                exit;
            }
        }
    }

    //修改
    public function editrole()
    {
        $request = $this->request->query;
        if (isset($request['id']) && !empty($request['id'])) {
            $id = $request['id'];
        } else {
            $id = 0;
        }
        $data['id'] = $id;
        $roles = TableRegistry::get('Roles');
        $info = $roles->find()->select(['id', 'name', 'note'])->where(array('id' => $id))->first();
        if (!empty($info)) {
            $data['name'] = $info['name'];
            $data['note'] = $info['note'];
        } else {
            $data['name'] = '';
            $data['note'] = '';
        }

        $this->set('data', $data);
    }

    public function postedit()
    {
        $roles = TableRegistry::get('Roles');
        $public = new PublicController();
        $request = $this->request->data;
        $id = $request['id'];
        if (!isset($request['name']) || empty($request['name'])) {
            echo json_encode(array('code' => 3, 'msg' => '角色名不能为空'));
            exit;
        }
        $data['name'] = $request['name'];
        $data['note'] = $request['note'];
        $data['modified'] = date('Y-m-d H:i:s');
        $result = $roles->updateAll($data, array('id' => $id));
        if ($result) {
            $public->adminlog('角色列表', '修改角色成功');
            echo json_encode(array('code' => 0, 'msg' => '修改角色成功'));
            exit;
        } else {
            $public->adminlog('角色列表', '修改角色失败');
            echo json_encode(array('code' => 1, 'msg' => '修改角色失败'));
            exit;
        }

    }

    //删除
    public function delete()
    {
        $request = $this->request->data;
        $roles = TableRegistry::get('Roles');
        $roles_accounts = TableRegistry::get('RolesAccounts');
        $roles_popedoms = TableRegistry::get('RolesPopedoms');
        $roles_software = TableRegistry::get('RolesSoftware');
        $public = new PublicController();
        //删除个数
        $count = 0;
        foreach ($request['rows'] as $key => $value) {
            $id = $value['id'];
            $name = $roles->find()->select(['name'])->where(array('id' => $id))->first()['name'];
            $account_count = $roles_accounts->find()->select(['account_id'])->where(array('role_id' => $id))->count();
            if ($account_count == 0) {
                $res = $roles->deleteAll(array('id' => $id));
                if ($res) {
                    $popedom_count = $roles_popedoms->find()->select(['popedomlist_id'])->where(array('role_id' => $id))->count();
                    $software_count = $roles_software->find()->select(['software_id'])->where(array('role_id' => $id))->count();
                    $popedoms_result = $roles_popedoms->deleteAll(array('role_id' => $id));
                    $software_result = $roles_software->deleteAll(array('role_id' => $id));
                    $type = true;

                    if ($popedom_count != $popedoms_result) {
                        $public->adminlog('角色列表', '未成功删除角色-' . $name . '权限');
                        $type = false;
                    }
                    if ($software_count != $software_result) {
                        $public->adminlog('角色列表', '未成功删除角色-' . $name . '关联工具');
                        $type = false;
                    }

                    if ($type == true) {
                        $count += 1;
                    }
                }

            } else {
                $public->adminlog('角色列表', '角色-' . $name . '下存在人员不能删除');
            }

        }
        if ($count > 0) {
            $public->adminlog('角色列表', '成功删除' . $count . '个角色');
            echo json_encode(array('code' => 0, 'msg' => '成功删除' . $count . '个角色'));
            exit;
        } else {
            $public->adminlog('角色列表', '删除角色失败');
            echo json_encode(array('code' => 0, 'msg' => '删除角色失败'));
            exit;
        }

    }

    //关联管理权限
    public function popedom()
    {
        $id = $this->request->query['id'];
        $roles = TableRegistry::get('Roles');
        $role_popedoms = TableRegistry::get('RolesPopedoms');
        $popedomlist = TableRegistry::get('Popedomlist');
        if ($this->request->is('get')) {
            $id = $this->request->query['id'];
            $this->set('id', $id);
            //返回角色名称
            $rolename = $roles->find('all')->select(['name'])->where(array('id' => $id))->toArray();
            if ($rolename) {
                $this->set('rolename', $rolename[0]['name']);
            }
            $where=array();
            //是否为系统管理员
            // if(!in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))) {
            //     $where['department_id'] = $this->request->session()->read('Auth.User.department_id');
            // }
            $roleid = $roles->find('all')->select(['id'])->where($where)->toArray();
            $rolesid=array();
            foreach ($roleid as $value) {
                $rolesid[] = $value['id'];
            }

//返回权限列表
            $popedomname = $this->request->session()->read("Auth.User.popedomname");
            $where = array();
            $where['serinalno <>']=0;
            if (in_array('cmop_global_tenant_admin', $popedomname) && !in_array('cmop_global_sys_admin', $popedomname)) {
                $where['popedomname <>'] = 'cmop_global_sys_admin';
            }
            $popedom_data = $popedomlist->find()->select(['popedomid', 'parent_id', 'popedomnote'])->order('serinalno')->where($where)->toArray();
            $array_p = array();
            if ($id != 0 && $id != '') {
                //角色已存在的列表权限
                $role_p = $role_popedoms->find()->select(['popedomlist_id'])->where(array('role_id' => $id))->toArray();
                if ($role_p) {
                    $popedomlist_ids = array();
                    $popedomlist_id = array();
                    foreach ($role_p as $key => $value) {
                        $popedomlist_ids[] = $value['popedomlist_id'];
                    }
                    foreach ($popedom_data as $key => $value) {
                        $popedomlist_id[] = $value['popedomid'];
                    }

                    foreach ($popedom_data as $val) {
                        $array_p[$val['popedomid']] = $val;
                    }
                    foreach ($popedomlist_ids as $value) {
                        if (in_array($value, $popedomlist_id)) {
                            $array_p[$value]['checked'] = 'true';
                        }
                    }
                    $array_p = array_values($array_p);
                }
            }

//        if(in_array('cmop_global_tenant_admin', $popedomname) && !in_array('cmop_global_sys_admin', $popedomname)) {

            if (in_array($id, $rolesid)) {

                if (empty($array_p)) {
                    $this->set('data', json_encode($popedom_data));
                } else {
                    $this->set('data', json_encode($array_p));
                }

            } else {

                return $this->redirect('/admin/role');
            }
//        }
        }
    }

    public function postpopedom()
    {
        $id = $this->request->query['id'];
        $connection = ConnectionManager::get('default');
        $roles = TableRegistry::get('Roles');
        $data = $this->request->data;
        $public = new PublicController();
        $name = $roles->find()->select(['name'])->where(array('id' => $id))->toArray();
        if (isset($data['type'])) {
            if ($this->request->data['type'] == 'popedom') {
                $popedomlist = TableRegistry::get('RolesPopedoms');
                $popedelete = $popedomlist->deleteAll(array('role_id' => $id));

                if (!empty($data['popeid'])) {
                    $pepoarray = substr($data['popeid'], 0, strlen($data['popeid']) - 1);
                    $is_true = preg_match("/^[0-9]+(\,[0-9]+)*$/", $pepoarray);
                    if ($is_true) {
                        $sql = "insert into cp_roles_popedoms(role_id,popedomlist_id) select $id,popedomid from cp_popedomlist where popedomid in ($pepoarray);";
                        $result = $connection->execute($sql);
                        if ($result) {
                            $public->adminlog('角色列表', '添加角色---' . $name[0]['name'] . '的列表权限成功');
                            $message = array('code' => 0, 'msg' => '添加权限成功');
                        }else{
                            $public->adminlog('角色列表', '添加角色---' . $name[0]['name'] . '的列表权限失败');
                            $message = array('code' => 1, 'msg' => '添加权限失败');
                        }
                    } else {
                        $public->adminlog('角色列表', '添加角色---' . $name[0]['name'] . '的列表权限失败');
                        $message = array('code' => 1, 'msg' => '添加权限失败');
                    }
                    
                } else {
                    $public->adminlog('角色列表', '清空角色下的列表权限');
                    $message = array('code' => 0, 'msg' => '添加权限成功');
                }
            }
        }
        echo json_encode($message);
        exit;

    }

    // 关联工具分裂
    public function software($page = 1)
    {
        $limit = 15;

        if ($page > 0) {
            $offset = $page - 1;
        } else {
            $offset = 0;
        }
        $id = $this->request->query['id'];
        $this->set('id', $id);
        $connection = ConnectionManager::get('default');
        $roles = TableRegistry::get('Roles');
        $where = array();
        $search='';
        if (isset($this->request->query['search']) && trim($this->request->query['search']) != '') {
            $search = $this->request->query['search'];
            $where["OR"] = [["software_name like" => "%$search%"], ["product_name like" => "%$search%"]];
            // $where=" where software_name like '%$search%' or product_name like '%$search%'";
        }
        $this->set('search',$search);
        //返回角色名称
        $rolename = $roles->find('all')->select(['name'])->where(array('id' => $id))->toArray();
        if ($rolename) {
            $this->set('rolename', $rolename[0]['name']);
        }
        $rolesid=array();
        //是否为系统管理员
        $where_did=array();
        if(!in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))) {
            $arr=[0,$this->request->session()->read('Auth.User.department_id')];
            $where_did=array('department_id in' => $arr);
        }

        $roleid = $roles->find('all')->select(['id'])->where($where_did)->toArray();
        foreach ($roleid as $value) {
            $rolesid[] = $value['id'];
        }

        $software_list_table = TableRegistry::get("SoftwareList");

        $query = $software_list_table
        ->find()
        ->select(['id','software_code','software_name','product_name','note'])
        ->where($where)
        ->offset($offset)
        ->limit($limit);

        $software_data['software']['data'] = $query;
        $software_data['software']['total']=ceil($query->count()/$limit);

        $selectID = array();
        if ($id != 0 && is_numeric($id)) {
            $sql_r_s = "SELECT software_id FROM `cp_roles_software` where role_id =" . $id;
            $role_s = $connection->execute($sql_r_s)->fetchAll('assoc');
            if ($role_s) {
              foreach($role_s as $key => $value){
                  $selectID[]=$value['software_id'];
              }
            }
        }
        $this->set('selectID',implode(',',$selectID));
        $this->set('software',$software_data);
        $this->set('page', $page);

    }
    public function getsoftware($page=1){
        $limit = 15;

        if ($page > 0) {
            $offset = $page - 1;
        } else {
            $offset = 0;
        }
        $offset = $offset * $limit;
        // $connection = ConnectionManager::get('default');
        $software_list_table = TableRegistry::get("SoftwareList");
        $where=array();
        if (isset($this->request->query['search']) && trim($this->request->query['search']) != '') {
            $search = $this->request->query['search'];
            $where["OR"] = [["software_name like" => "%$search%"], ["product_name like" => "%$search%"]];
        }
        // $sql_software = "SELECT id,software_code,software_name,product_name,note FROM `cp_software_list`";
        // if($where!=''){
        //     $sql_software.=$where;
        // }
        // $sql=$sql_software." limit $limit offset $offset";
        // $i = ceil($connection->execute($sql_software)->count() / $limit);

         $query = $software_list_table
        ->find()
        ->select(['id','software_code','software_name','product_name','note'])
        ->where($where)
        ->offset($offset)
        ->limit($limit);

        // $software_data['software']['data'] = $query;
        // $software_data['software']['total']=ceil($query->count()/$limit);

        $i = ceil($query->count() / $limit);

        $data['total'] = $i;
        $data['data'] = $query;
        $data['page'] = $page;
        echo json_encode($data);exit;
        $this->layout='ajax';
    }


    public function postsoftware()
    {
        $id = $this->request->query['id'];
        $roles = TableRegistry::get('Roles');
        $connection = ConnectionManager::get('default');
        $data=$this->request->data;
        $public = new PublicController();
        $name=$roles->find()->select(['name'])->where(array('id'=>$id))->toArray();
        if($data['type']=='software'){
            $rolessoftware = TableRegistry::get('RolesSoftware');
            $softdelete = $rolessoftware->deleteAll(array('role_id'=>$id));
            if(!empty($data['softwareid'])){
                $softarray=trim($data['softwareid'],',');
                $is_true = preg_match("/^[0-9]+(\,[0-9]+)*$/", $softarray);
                if ($is_true) {
                    $sql ="insert into cp_roles_software(role_id,software_id) select $id,id from cp_software_list where id in ($softarray);";
                    $result = $connection->execute($sql);
                    if($result){
                        $public->adminlog('角色列表','添加角色---'.$name[0]['name'].'的软件权限');
                        $message = array('code'=>0,'msg'=>'添加权限成功');
                    }
                } else {
                    $public->adminlog('角色列表', '清空角色下的软件权限');
                $message = array('code' => 0, 'msg' => '添加权限失败');
                }
            }else {
                $public->adminlog('角色列表', '清空角色下的软件权限');
                $message = array('code' => 0, 'msg' => '添加权限成功');
            }
        }

        echo json_encode($message);exit;

    }

    //关联用户
    public function accounts($page = 1)
    {
        $limit = 15;

        if ($page > 0) {
            $offset = $page - 1;
        } else {
            $offset = 0;
        }
        $department = TableRegistry::get('Departments');
        $connection = ConnectionManager::get('default');
        $roles = TableRegistry::get('Roles');
        $id = $this->request->query['id'];
        $this->set('id', $id);
        if(isset($this->request->query['department_id'])){
            $department_id=$this->request->query['department_id'];
        }else{
            $department_id=$roles->find()->select(['department_id'])->where(array('id'=>$id))->first()['department_id'];
            $this->request->query['department_id']=$department_id;
//            $department_id=0;
        }
        $this->set('department_selected',$department_id);
        //返回角色名称
        $rolename = $roles->find('all')->select(['name'])->where(array('id' => $id))->toArray();
        if ($rolename) {
            $this->set('rolename', $rolename[0]['name']);
        }
        $rolesid=array();
        //是否为系统管理员
        $where_did=array();
        if(!in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))) {
            $arr=[0,$this->request->session()->read('Auth.User.department_id')];
            $where_did=array('department_id in' => $arr);
        }

        $roleid = $roles->find('all')->select(['id'])->where($where_did)->toArray();
        foreach ($roleid as $value) {
            $rolesid[] = $value['id'];
        }
        $popedomname = $this->request->session()->read("Auth.User.popedomname");
        $depat_where=array();
        if(in_array('cmop_global_tenant_admin', $popedomname) && !in_array('cmop_global_sys_admin', $popedomname)) {
            $depat_where['id'] = $this->request->session()->read('Auth.User.department_id');
        }
        $depart = $department->find()->select(['id', 'name', 'parent_id'])->where($depat_where)->toArray();
        $this->set('depart',$depart);//租户


        $accounts_table = TableRegistry::get("Accounts");
        $where=array();
        $search='';
        if (isset($this->request->query['search']) && trim($this->request->query['search']) != '') {
            $search = $this->request->query['search'];
            // $where=" where (loginname like '%$search%' or username like '%$search%')";
            $where["OR"] = [["loginname like" => "%$search%"], ["username like" => "%$search%"]];
        }
        $this->set('search',$search);
        if(isset($this->request->query['department_id'])&&$this->request->query['department_id']!=0){
            $department_id=$this->request->query['department_id'];
                $where["department_id"]= $department_id;
        }
        // $sql_accounts = "SELECT * FROM `cp_accounts`";
        // if($where!=''){
        //     $sql_accounts.=$where;
        // }
        // $sql=$sql_accounts." limit $limit offset $offset";

        $query = $accounts_table
        ->find()
        ->where($where)
        ->offset($offset)
        ->limit($limit);
        $accounts_data['accounts']['total']=ceil($query->count()/$limit);
        $accounts_data['accounts']['data'] = $query;

        foreach($accounts_data['accounts']['data'] as $key => $value){
            $accounts_data['accounts']['data'][$key]['expire']=($value['expire']==-1)?'永久':date('Y-m-d',$value['expire']);
            $accounts_data['accounts']['data'][$key]['create_time']=date('Y-m-d H:i:s',$value['create_time']);
            $accounts_data['accounts']['data'][$key]['create_account']=TableRegistry::get('Accounts')->find()->select(['username'])->where(array('id'=>$value['create_by']))->first()['username'];
            $accounts_data['accounts']['data'][$key]['department_name']=TableRegistry::get('Departments')->find()->select(['name'])->where(array('id'=>$value['department_id']))->first()['name'];
        }
        $selectID = array();
        if ($id != 0 && is_numeric($id)) {
            $sql_r_s = "SELECT account_id FROM `cp_roles_accounts` where role_id =" . $id;
            $role_s = $connection->execute($sql_r_s)->fetchAll('assoc');
            if ($role_s) {
                foreach($role_s as $key => $value){
                    $selectID[]=$value['account_id'];
                }
            }
        }
        $this->set('selectID',implode(',',$selectID));
        $this->set('page', $page);
        $this->set('accounts',$accounts_data);
    }
    public function getaccounts($page=1){
        $limit = 15;

        if ($page > 0) {
            $offset = $page - 1;
        } else {
            $offset = 0;
        }
        $offset = $offset * $limit;
        
        $accounts_table = TableRegistry::get("Accounts");
        $where=array();
        $search='';
        if (isset($this->request->query['search']) && trim($this->request->query['search']) != '') {
            $search = $this->request->query['search'];
            $where["OR"] = [["loginname like" => "%$search%"], ["username like" => "%$search%"]];
        }
        $this->set('search',$search);
        if(isset($this->request->query['department_id']) && $this->request->query['department_id']!=0){
            $department_id=$this->request->query['department_id'];
                $where["department_id"]= $department_id;
        }

        $query = $accounts_table
        ->find()
        ->where($where)
        ->offset($offset)
        ->limit($limit);
        $i = ceil($query->count() / $limit);

        $data['total'] = $i;
        $data['data'] = $query;
        $data['page'] = $page;
        echo json_encode($data);exit;
        $this->layout='ajax';
    }

    public function postaccounts()
    {
        $id = $this->request->query['id'];
        $roles = TableRegistry::get('Roles');
        $connection = ConnectionManager::get('default');
        $data=$this->request->data;
        $public = new PublicController();
        $name=$roles->find()->select(['name'])->where(array('id'=>$id))->toArray();
        if($data['type']=='account'){
            //查询所选租户下的所有人员
            if(isset($data['department_id']) && $data['department_id'] != 0 && is_numeric($data['department_id'])) {
                $ss = "select id from cp_accounts where department_id =" . $data['department_id'];
            }else{
                $ss = "select id from cp_accounts";
            }
            $depart_a = $connection->execute($ss)->fetchAll('assoc');
            $depart_a = array_column($depart_a,'id');
            $depart_a=implode(',',$depart_a);
            //从角色人员表里删除该租户下的所有人员角色数据
            $sqldelete = "DELETE  FROM cp_roles_accounts where role_id=$id and account_id in (".$depart_a .")";
            $deleteresult = $connection->execute($sqldelete)->count();
            //保存新选择人角色人员数据
            if(!empty($data['accountid'])){
                $accountarray=trim($data['accountid'],',');
                $is_true = preg_match("/^[0-9]+(\,[0-9]+)*$/", $accountarray);
                if ($is_true) {
                    $sql ="insert into cp_roles_accounts(role_id,account_id) select $id,id from cp_accounts where id in ($accountarray);";
                    $result = $connection->execute($sql);
                    if($result){
                        $public->adminlog('角色列表','添加角色---'.$name[0]['name'].'下的人员');
                        $message = array('code'=>0,'msg'=>'添加权限成功');
                    }
                } else {
                    $public->adminlog('角色列表','添加角色---'.$name[0]['name'].'下的人员');
                        $message = array('code'=>0,'msg'=>'添加权限失败');
                }
            }else{
                $public->adminlog('角色列表','清空角色下的人员');
                $message = array('code'=>0,'msg'=>'添加权限成功');
            }
        }
        echo json_encode($message);exit;
    }


}