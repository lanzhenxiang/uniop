<?php
/**
 * Created by PhpStorm.
 * User: kelly
 * Date: 2016/12/20
 * Time: 18:22
 */
namespace App\Controller\Admin;

use App\Auth\CmopPasswordHasher;
use App\Controller\Admin\BasicController;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use App\Controller\Admin\DepartmentsController;
use App\Controller\AdminController;

class AccountController extends AdminController
{

    public $paginate = [
        'limit' => 15
    ];
    public $_pageList = array(
        'total' => 0,
        'rows' => array()
    );

    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_tenant_tenants');
        if (!$checkPopedomlist) {
            return $this->redirect('/admin/');
        }
    }

    public function login()
    {
        $this->layout = 'login';
    }

    public function index()
    {
        $where=array();
        //是否为系统管理员
        if(!in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))) {
            $where['id'] = $this->request->session()->read('Auth.User.department_id');
        }
        $department=TableRegistry::get('Departments')->find()->select(['id','name'])->where($where)->toArray();
        $this->set('department',$department);
    }

    public function lists()
    {
        $accounts = TableRegistry::get('Accounts');
        $request = $this->request->query;
        $where = array();
        //是否为系统管理员
        if(!in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))) {
            $where['department_id'] = $this->request->session()->read('Auth.User.department_id');
        }
        //搜索
        if (isset($request['search']) && trim($request['search']) != '') {
            $search = $request['search'];
            $where['or'] = array(
                'Accounts.loginname like' => "%$search%",
                'Accounts.username like' => "%$search%",
                'Accounts.mobile' => "$search",
            );
        }
        if(isset($request['department_id'])&&$request['department_id']!=0&&$request['department_id']!=''){
            $where['Accounts.department_id']=$request['department_id'];
        }
        $this->_pageList['total'] = $accounts->find()->where(array($where))->count();
        $this->_pageList['rows'] = $accounts->find()->where(array($where))->offset($request['offset'])->limit($request['limit'])->map(function ($row) {
            //翻译租户
            $row['department_name'] = TableRegistry::get('Departments')->find()->select(['name'])->where(array('id' => $row['department_id']))->first()['name'];
            //添加创建人
            $row['create_name'] = TableRegistry::get('Accounts')->find()->select(['username'])->where(array('Accounts.id' => $row['create_by']))->first()['username'];
            //修改时间格式
            $row['create_time'] = date('Y-m-d H:i:s', $row['create_time']);
            //限制备注字数
            $row['note'] = mb_substr($row['note'], 0, 20);
            //有效期
            if ($row['expire'] == '') {
                $row['expire'] = '-';
            } elseif ($row['expire'] == -1) {
                $row['expire'] = '永久';
            } else {
                $row['expire'] = date('Y-m-d H:i:s', $row['expire']);
            }
            return $row;
        });
        echo json_encode($this->_pageList);
        exit();

    }

    //新建用户
    public function addaccount()
    {
        $departments = TableRegistry::get('Departments');
        //是否为系统管理员
        $where=array();
        if(!in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))) {
            $where['id'] = $this->request->session()->read('Auth.User.department_id');
        }
        $data = $departments->find()->select(['name', 'id'])->where($where)->toArray();
        $this->set('info', $data);
    }

    public function postadd()
    {
        $accounts = TableRegistry::get('Accounts');
        $public = new PublicController();
        $request = $this->request->data;
        if ($request['password'] != $request['repassword']) {
            echo json_encode(array('code' => 2, 'msg' => '两次输入密码不一致'));
            exit;
        }
        if (!empty($accounts->find()->select(['id'])->where(array('loginname' => $request['loginname']))->first()['id'])) {
            echo json_encode(array('code' => 3, 'msg' => '登录名已存在'));
            exit;
        }
        if (!empty($accounts->find()->select(['id'])->where(array('username' => $request['username']))->first()['id'])) {
            echo json_encode(array('code' => 3, 'msg' => '用户名已存在'));
            exit;
        }
        if ($request['mobile'] != '' && !empty($accounts->find()->select(['id'])->where(array('mobile' => $request['mobile']))->first()['id'])) {
            echo json_encode(array('code' => 4, 'msg' => '该手机号已有用户'));
            exit;
        }
        $data['loginname'] = $request['loginname'];
        $data['username'] = $request['username'];
        $data['mobile'] = $request['mobile'];
        $data['email'] = $request['email'];
        $data['department_id'] = $request['department'];
        $data['address'] = $request['address'];
        $data['create_by'] = $this->request->session()->read('Auth.User.id');
        if ($request['expire'] == -1) {
            $data['expire'] = -1;
        } else {
            $data['expire'] = strtotime($request['time']) + 86400;
        }
        $data['create_time'] = time();
        $salt = $this->random(6, '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ');
        $data['salt'] = $salt;
        $data['password'] = (new CmopPasswordHasher(array('salt' => $salt)))->hash($request['password']);
        $accountsss = $accounts->newEntity();
        $accountsss = $accounts->patchEntity($accountsss, $data);
        $result = $accounts->save($accountsss);
        if ($result) {
            $public->adminlog('Account', '添加人员---' . $data['username'] . '的基础信息');
            echo json_encode(array('code' => 0, 'msg' => '添加人员成功'));
            exit();
        } else {
            $public->adminlog('Account', '添加人员基础信息失败');
            echo json_encode(array('code' => 1, 'msg' => '添加人员失败'));
            exit();
        }
    }

    //随机码
    public function random($length, $chars = '0123456789')
    {
        $hash = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
        return $hash;
    }

    //修改信息
    public function editinfo()
    {
        $id = $this->request->query['id'];
        $departments = TableRegistry::get('Departments');
        //是否为系统管理员
        $where=array();
        if(!in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))) {
            $where['id'] = $this->request->session()->read('Auth.User.department_id');
        }
        $data = $departments->find()->select(['name', 'id'])->where($where)->toArray();
        $this->set('info', $data);
        $accounts = TableRegistry::get('Accounts');
        $account_info = $accounts->find()->where(array('id' => $id))->first();
        if ($account_info['expire'] != -1) {
            $account_info['expire_new'] = date('Y-m-d', $account_info['expire']);
        }
        $this->set('account_info', $account_info);

    }

    public function postedit()
    {
        $request = $this->request->data;
        $accounts = TableRegistry::get('Accounts');
        $public = new PublicController();
        $id = $request['id'];
        $department_id = $request['department'];
        if (isset($request['time']) && $request['expire'] == 0) {
            $request['expire'] = strtotime($request['time']) + 86400;
        }
        unset($request['time']);
        unset($request['id']);
        unset($request['department']);
        $request['department_id'] = $department_id;
        $request['modify_time'] = time();
        $result = $accounts->updateAll($request, array('id' => $id));
        if ($result) {
            $public->adminlog('Account', '修改人员的基础信息成功');
            echo json_encode(array('code' => 0, 'msg' => '修改人员信息成功'));
            exit();
        } else {
            $public->adminlog('Account', '修改人员的基础信息失败');
            echo json_encode(array('code' => 1, 'msg' => '修改人员信息失败'));
            exit();
        }


    }

    //修改密码
    public function editpassword()
    {
        $request = $this->request->query;
        if (isset($request['id'])) {
            $accounts = TableRegistry::get('Accounts');
            $data = $accounts->find()->select(['loginname', 'username', 'id'])->where(array('id' => $request['id']))->first();
        } else {
            $data = array(
                'loginname' => '',
                'username' => '',
                'id' => 0
            );
        }
        $this->set('data', $data);

    }

    public function postpassword()
    {
        $public = new PublicController();
        $request = $this->request->data;
        $accounts = TableRegistry::get('Accounts');
        $password = $request['password'];
        $repassword = $request['repassword'];
        $username = $request['username'];
        if (!isset($request['id']) || empty($request['id'])) {
            echo json_encode(array('code' => 2, 'msg' => '未传入用户'));
            exit;
        }
        if ($password != $repassword) {
            echo json_encode(array('code' => 3, 'msg' => '两次输入密码不一致'));
            exit;
        }
        $id = $request['id'];
        $salt = $this->random(6, '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ');
        $newpassword = (new CmopPasswordHasher(array('salt' => $salt)))->hash($password);
        $res = $accounts->updateAll(array('salt' => $salt, 'password' => $newpassword), array('id' => $id));
        if ($res) {
            $public->adminlog('Account', '修改账户' . $username . '密码成功');
            echo json_encode(array('code' => 0, 'msg' => '修改账户' . $username . '密码成功'));
            exit();
        } else {
            $public->adminlog('Account', '修改账户' . $username . '密码失败');
            echo json_encode(array('code' => 1, 'msg' => '修改账户' . $username . '密码失败'));
            exit();
        }

    }

    //关联角色
    public function connectroles($page = 1)
    {
        $request = $this->request->query;
        $search = '';
        $id = 0;
        if (isset($request['id'])) {
            $id = $request['id'];
            $accounts = TableRegistry::get('Accounts');
            $data = $accounts->find()->select(['loginname', 'username', 'id'])->where(array('id' => $request['id']))->first();
        } else {
            $data = array(
                'loginname' => '',
                'username' => '',
                'id' => 0
            );
        }
        $this->set('data', $data);
        //显示部门
        $departments = TableRegistry::get('Departments');
        $department_data = $departments->find()->select(['id', 'name'])->toArray();
        $accounts = TableRegistry::get('Accounts');
        $acc = $accounts->find('all', array('conditions' => array('id =' => $id)))->toArray();//获取该分类的信息
        //角色显示
        $limit = 15;

        if ($page > 0) {
            $offset = $page - 1;
        } else {
            $offset = 0;
        }
        $roles = TableRegistry::get('Roles');
        $connection = ConnectionManager::get('default');
        $accounts = TableRegistry::get('Accounts');
        $department_id = $accounts->find()->select(['department_id'])->where(array('id' => $id))->first()['department_id'];
        $where=array();
        //是否为系统管理员
        if(!in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))) {
            $where = array(
                'Roles.department_id' => $this->request->session()->read('Auth.User.department_id')
            );
        }

        //搜索框
        if (isset($request['search']) && trim($request['search']) != '') {
            $search = $request['search'];
            $where['or'] = array(
                'Roles.name like' => "%$search%",
            );
        }
        $this->set('search', $search);
        $query = $roles->find()->select(['id', 'name', 'note', 'department_id'])->where($where)->limit($limit)->offset($offset);
        $i = ceil($query->count() / $limit);
        $roles_data['roles']['total'] = $i;
        $roles_data['roles']['data'] = $query->toArray();//获取桌面信息
        //显示服务已关联的主机
        if ($id) {
            $sql_r_a = "SELECT role_id FROM `cp_roles_accounts` where account_id =" . $id;
            $role_a = $connection->execute($sql_r_a)->fetchAll('assoc');
            $RoleID = array();
            if ($role_a) {
                foreach ($role_a as $key => $value) {
                    $RoleID[] = $value['role_id'];
                }
            }
            //var_dump(implode(',',$RoleID));exit;
            $this->set('RoleID', implode(',', $RoleID));
        }

        $systemsetting = TableRegistry::get('Systemsetting');
        $para = $systemsetting->find()->select(['id', 'para_code', 'para_value', 'para_note'])->where(array('para_type' => 2))->toArray();
        $this->set('para', $para);
        $usersetting = TableRegistry::get('UserSetting');
        $user_data = $usersetting->find('all')->where(array('owner_type' => 1, 'owner_id' => $id))->toArray();
        foreach ($user_data as $key => $value) {
            $para_code[] = $value['para_code'];
            $this->set('para_code', $para_code);
        }
        if ($user_data) {
            $this->set('user_data', $user_data);
        }
        $this->set('roles', $roles_data);
        $this->set('page', $page);
        $this->set('depart_id', $department_id);
        $this->set('acc', $acc);
        $this->set('department_data', $department_data);


    }

    //ajax分页
    public function getroles($page = 1)
    {
        $request = $this->request->query;
        $limit = 15;
        if ($page > 0) {
            $offset = $page - 1;
        } else {
            $offset = 0;
        }
        $offset = $offset * $limit;
        $roles = TableRegistry::get('Roles');
/*        $connection = ConnectionManager::get('default');
        $sql = "SELECT cp_roles.id AS id, cp_roles.name AS name, cp_roles.note AS note, cp_roles.department_id FROM cp_roles where (department_id = 0 or department_id =" . $request['department_id'] . ")";
//            $this->request->session()->read('Auth.User.department_id');
        //搜索框
        if (isset($request['search']) && trim($request['search']) != '') {
            $search = $request['search'];
            $sql .= " and name like '%$search%'";
        }
        $sql_row = $sql . " limit " . $offset . "," . $limit;
        $i = ceil($connection->execute($sql)->count() / $limit);
        $data['total'] = $i;
        $data['data'] = $connection->execute($sql_row)->fetchAll('assoc');//获取角色信息
        $data['page'] = $page;*/

        $where=array();
        //是否为系统管理员
        if(!in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))) {
            $where = array(
                'Roles.department_id' => $this->request->session()->read('Auth.User.department_id')
            );
        }

        //搜索框
        if (isset($request['search']) && trim($request['search']) != '') {
            $search = $request['search'];
            $where['or'] = array(
                'Roles.name like' => "%$search%",
            );
        }
        $query = $roles->find()->select(['id', 'name', 'note', 'department_id'])->where($where);
        $query2=$query->limit($limit)->offset($offset);
        $i = ceil($query->count() / $limit);
        $data['total'] = $i;
        $data['data'] = $query2->toArray();
        $data['page'] = $page;

        echo json_encode($data);
        exit();
        $this->lauout = 'ajax';
    }

    public function rolelist()
    {
        $roles = TableRegistry::get('Roles');
        $request = $this->request->query;
        $where = array();
        //搜索框
        if (isset($request['search']) && trim($request['search']) != '') {
            $search = $request['search'];
            $where['or'] = array(
                'Roles.name like' => "%$search%",
            );
        }
        $this->paginate['limit'] = $request['limit'];
        $this->paginate['page'] = $request['offset'] / $request['limit'] + 1;
        $this->_pageList['total'] = $roles->find('all')->where($where)->count();
        $this->_pageList['rows'] = $this->paginate($roles->find()->where($where));
        echo json_encode($this->_pageList);
        exit();
    }

    public function postconnect()
    {
        $request = $this->request->data;
        $roles = TableRegistry::get('RolesAccounts');
        $public = new PublicController();
        $id = $request['id'];
        if (isset($request['role_id'])&&!empty($request['role_id'])) {
            $roleid = $request['role_id'];
            $roles->deleteAll(array('account_id' => $id));
            if (!empty($roleid)) {
                $roleid = trim($roleid, ',');
                $roleid = explode(',', $roleid);
                $roleid = array_filter($roleid);
                $roleid = implode(',', $roleid);
                $connection = ConnectionManager::get('default');
                $sql = "insert into cp_roles_accounts(account_id,role_id) select $id,id from cp_roles where id in ($roleid);";
                $result_r = $connection->execute($sql);
                if ($result_r) {
                    $public->adminlog('Account', '修改人员---' . $request['username'] . '的关联角色成功');
                    echo json_encode(array('code' => 0, 'msg' => '关联角色成功'));
                    exit;
                } else {
                    $public->adminlog('Account', '修改人员---' . $request['username'] . '的关联角色失败');
                    echo json_encode(array('code' => 1, 'msg' => '关联角色失败'));
                    exit;
                }

            }
        } else {
            $roles->deleteAll(array('account_id' => $id));
            echo json_encode(array('code' => 0, 'msg' => '删除关联角色成功'));
            exit;
        }


    }

    public function delete()
    {
        $request = $this->request->data;
        $accounts = TableRegistry::get('Accounts');
        $public = new PublicController();
        //删除个数
        $count = 0;
        //正在登录的账号
        $logining = 0;
        foreach ($request['rows'] as $key => $value) {
            $res1 = 0;
            $res2 = 0;
            $id = $value['id'];
            if ($this->request->session()->read('Auth.User.id') == $id) {
                $logining = 1;
            } else {
                $res = $accounts->deleteAll(array('id' => $id));
                if ($res) {
                    $roles_accounts = TableRegistry::get('RolesAccounts');
                    $role_count = $roles_accounts->find()->where(array('account_id' => $id))->count();
                    if ($role_count > 0) {
                        $result = $roles_accounts->deleteAll(array('account_id' => $id));
                        if ($role_count == $result) {
                            $res1 = 1;
                        }
                    } else {
                        $res1 = 1;
                    }

                    $usersetting = TableRegistry::get('UserSetting');
                    $user_count = $usersetting->find('all')->where(array('owner_type' => 1, 'owner_id' => $id))->count();
                    if ($user_count > 0) {
                        $results = $usersetting->deleteAll(array('owner_id' => $id, 'owner_type' => 1));
                        if ($user_count == $results) {
                            $res2 = 1;
                        }
                    } else {
                        $res2 = 1;
                    }
                    if ($res1 && $res2) {
                        $count += 1;
                    }
                }

            }
        }

        if ($count > 0) {
            if ($logining == 0) {
                $public->adminlog('Account', '删除' . $count . '个人员');
                echo json_encode(array('code' => 0, 'msg' => '成功删除' . $count . '条数据'));
                exit;
            } else {
                $public->adminlog('Account', '删除' . $count . '个人员。有账号正在登录中,无法删除');
                echo json_encode(array('code' => 0, 'msg' => '成功删除' . $count . '条数据。有账号正在登录中,无法删除'));
                exit;
            }
        } else {
            if ($logining == 0) {
                $public->adminlog('Account', '删除人员失败');
                echo json_encode(array('code' => 1, 'msg' => '删除人员失败'));
                exit;
            } else {
                $public->adminlog('Account', '删除人员失败。其中有1个账号正在登录中');
                echo json_encode(array('code' => 1, 'msg' => '删除人员失败。其中有1个账号正在登录中'));
                exit;
            }
        }

    }


}