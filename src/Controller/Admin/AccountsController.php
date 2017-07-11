<?php
/**
* 文件描述文字
*
*
* @author XingShanghe<xingshanghe@gmail.com>
* @date  2015年9月8日下午3:39:25
* @source AccountsController.php
* @version 1.0.0
* @copyright  Copyright 2015 sobey.com
*/

namespace App\Controller\Admin;

use App\Auth\CmopPasswordHasher;
use App\Controller\Admin\BasicController;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use App\Controller\Admin\DepartmentsController;
use App\Controller\AdminController;

class AccountsController extends AdminController
{

    public $paginate = [
        'limit' => 15
    ];

    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_tenant_tenants');
        if (! $checkPopedomlist) {
            return $this->redirect('/admin/');
         }
     }

    public function login()
    {
        $this->layout = 'login';
    }


    /**
     * 显示人员信息
     * @param number $department_id 部门id
     * @param string $name 搜索参数
     */
    public function index($department_id=0,$name=''){
        if(!is_numeric($department_id) && empty($name)){
            $name=$department_id;
            $department_id=0;
        }
        $department_id = isset($this->request->data['department_id'])?$this->request->data['department_id']:$department_id;
        $accounts = TableRegistry::get('Accounts');
        $departments = TableRegistry::get('Departments');
        $dept_grout = $departments->find()->select(['id','name'])->toArray();
        $this->set('dept_grout',$dept_grout);
        $where = array();
        //filter条件拼凑
        if($name){
            $where['OR'] =array('loginname like'=>"%$name%",'username like'=>"%$name%");
        }
        //TODO
        if($department_id==0){
            if(in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))){
                $data =  $accounts->find('all')->contain(['Departments'])->where($where);
                $department_data['name']='所有租户';
                $department_data['id']=$department_id;
                $this->set('priont','1');
            }elseif(!in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname')) && in_array('cmop_global_tenant_admin',$this->request->session()->read('Auth.User.popedomname'))){
                $where['department_id']=$this->request->session()->read('Auth.User.department_id');
                $data =  $accounts->find('all')->contain(['Departments'])->where($where);
                $department_data = $departments->find()->select(['id','name'])->where(array('id'=>$this->request->session()->read('Auth.User.department_id')))->toArray();
                $this->set('priont','2');
            }
        }else {
            $where['department_id']=$department_id;
            $data = $accounts->find('all')->contain(['Departments'])->where($where);
            $department_data = $departments->find()->select(['id','name'])->where(array('id'=>$department_id))->toArray();
            if(in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))){
                $this->set('priont','1');
            }elseif(!in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname')) && in_array('cmop_global_tenant_admin',$this->request->session()->read('Auth.User.popedomname'))){
                $this->set('priont','2');
            }
        }
        if(isset($data)){
            $rs=$this->paginate($data);
            $this->set('data',$rs->toArray());
        }else{
            $rs=$this->paginate();
        }
        if(isset($department_data)){

            $this->set('department_id',isset($department_data[0])?$department_data[0]['id']:$department_data['id']);
            $this->set('department_name',isset($department_data[0])?$department_data[0]['name']:$department_data['name']);
        }else{
            $this->set('department_id','');
            $this->set('department_name','');
        }
        $this->set('name',$name);

        /*$this->set('de',$info);*/
    }

    public function department($department_id=0){
        $departments = TableRegistry::get('Departments');
        $arr =array('department_id ='.$department_id);
        $de['depart'] = $this->_get_info($departments);
        $info1 = $this->_get_tree($de['depart']);
        $info = $this->arrayToTree($info1, 'id', 'parent_id', 'children');
        echo json_encode($info);exit();
    }

    public function add($id,$page = 1){
        $limit = 15;
        if($page > 0){
            $offset = $page-1;
        }else {
            $offset = 0;
        }
        $offset = $offset*$limit;
        $checkPopedomlist_tenant = parent::checkPopedomlist('cmop_global_tenant_admin');
        $checkPopedomlist_sys = parent::checkPopedomlist('cmop_global_sys_admin');
        if ($checkPopedomlist_tenant) {
            if (!$checkPopedomlist_sys) {
                if($id != $this->request->session()->read('Auth.User.department_id')){
                    return $this->redirect('/admin/accounts/add/'.$this->request->session()->read('Auth.User.department_id'));
                }
            }
        }
        $departments = TableRegistry::get('Departments');

        $data = $departments->find()->select(['id', 'name'])->where(array('id'=>$id))->toArray();
        $connection = ConnectionManager::get('default');
        $sql = "SELECT cp_roles.id AS id, cp_roles.name AS name, cp_roles.note AS note, cp_roles.department_id FROM cp_roles where department_id = 0 or department_id =".$this->request->session()->read('Auth.User.department_id');
        $sql_row = $sql . " limit " . $offset . "," . $limit;
        $i = ceil($connection->execute($sql)->count()/$limit);
        $roles_data['roles']['total'] = $i;
        $roles_data['roles']['data'] = $connection->execute($sql_row)->fetchAll('assoc');//获取桌面信息
        $systemsetting = TableRegistry::get('Systemsetting');
        $para = $systemsetting->find()->select(['id', 'para_code', 'para_value', 'para_note'])->where(array('para_type' => 2))->toArray();
        $this->set('para', $para);
        $this->set('roles', $roles_data);
        $this->set('depart_id', $id);
        $this->set('page',$page);
        $this->set('data', $data);
    }


    public function editadd($id=0){
        $accounts = TableRegistry::get('Accounts');
        $roles = TableRegistry::get('RolesAccounts');
        $message = array('code'=>1,'msg'=>'操作失败');
        if ($this->request->is('post')){
            $public = new PublicController();
            $loginname=$this->request->data['loginname'];
            if($loginname ==''){
                $message = array('code'=>1,'msg'=>'登录名不能为空');
                echo json_encode($message);exit();
            }
            $basic = array();
            $basic['loginname'] = $this->request->data['loginname'];
            $basic['username'] = $this->request->data['username'];
            $basic['email'] = $this->request->data['email'];
            $basic['mobile']=$this->request->data['mobile'];
            $basic['address'] = $this->request->data['address'];
            $basic['department_id'] = $this->request->data['department'];
            $basic['create_by']=$this->request->session()->read('Auth.User.id');
            unset($this->request->data['address']);
            unset($this->request->data['department']);
            unset($this->request->data['mobile']);
            unset($this->request->data['email']);
            unset($this->request->data['username']);
            unset($this->request->data['loginname']);
            unset($this->request->data['para_id']);
            unset($this->request->data['repassword']);
            unset($this->request->data['roles']);
            $usersetting = TableRegistry::get('UserSetting');
            if(isset($this->request->data['userid'])){
                $time=time();
                $id = $this->request->data['userid'];
                unset($this->request->data['userid']);
                $accountsss = $accounts->get($id);
                $rs = $accounts->find('all',array('conditions' => array('loginname =' => $loginname,'id <>'=>$id)))->toArray();
                if(!empty($rs)){
                    $message = array('code'=>1,'msg'=>'登录名重复');
                    echo json_encode($message);exit();
                }
                if(!empty($this->request->data['password'])){
                    $basic['password']= (new CmopPasswordHasher(array('salt'=>$accountsss->salt)))->hash($this->request->data['password']);
                }
                unset($this->request->data['password']);

                $roleid = $this->request->data['role_id'];
                unset($this->request->data['role_id']);
                $basic['modify_time']=$time;
                if($this->request->data['date-mode']==0){
                    $basic['expire']=strtotime($this->request->data['time'])+86400;
                }else{
                    $basic['expire']='-1';
                }
                unset($this->request->data['time']);
                unset($this->request->data['date-mode']);
                $result =$accounts->updateAll($basic,array('id'=>$id));//向数据库更新用户信息
                if($result){
                    //修改用户参数
                    $usersetting->deleteAll(array('owner_id'=>$id));
                    $print =false;
                    if(!empty($this->request->data)) {
                        $datas_count = count($this->request->data);
                        $j = 0;
                        $user = array();
                        $user['owner_id'] = $id;
                        $user['owner_type'] = 1;
                        $user['para_value1'] = '';
                        foreach ($this->request->data as $key => $value) {
                            $user['para_code'] = $key;
                            foreach ($value as $k => $v) {
                                $user[$k] = $v;
                            }
                            $seting = $usersetting->newEntity();
                            $seting = $usersetting->patchEntity($seting, $user);
                            $result = $usersetting->save($seting);
                            if ($result) {
                                $j++;
                            }
                        }
                        if($datas_count==$j){
                            $print=true;
                        }
                    }

                    //修改用户权限
                    $print_t = false;
                    $roles->deleteAll(array('account_id'=>$id));
                    if(!empty($roleid)){
                        $roleid = explode(',',$roleid);
                        $roleid = array_filter($roleid);
                        $roleid = implode(',',$roleid);
                        $connection = ConnectionManager::get('default');
                        $sql ="insert into cp_roles_accounts(account_id,role_id) select $id,id from cp_roles where id in ($roleid);";
                        $result_r = $connection->execute($sql);
                        if($result_r){
                            $print_t=true;
                        }

                    }
                    //用户参数与角色都为空时
                    if(empty($this->request->data) && empty($roleid)){
                        $message = array('code'=>0,'msg'=>'修改成功');
                        $public->adminlog('Accounts','修改人员---'.$basic['username'].'的基础信息');
                    }

                    //用户参数为空，角色不为空时
                    if(empty($this->request->data) && !empty($roleid)){
                        if($print_t){
                            $message = array('code'=>0,'msg'=>'修改成功');
                            $public->adminlog('Accounts','修改人员---'.$basic['username'].'的基础信息和用户的角色');
                        }else{
                            $public->adminlog('Accounts','修改人员---'.$basic['username'].'的基础信息');
                        }
                    }

                    //用户参数不为空，角色为空时
                    if(!empty($this->request->data) && empty($roleid)){
                        if($print){
                            $message = array('code'=>0,'msg'=>'修改成功');
                            $public->adminlog('Accounts','修改人员---'.$basic['username'].'的基础信息和用户参数');
                        }else{
                            $public->adminlog('Accounts','修改人员---'.$basic['username'].'的基础信息');
                        }
                    }

                    //用户参数不为空，角色不为空时
                    if(!empty($roleid) && !empty($this->request->data) ){
                        if($print || $print_t){
                            $message = array('code'=>0,'msg'=>'修改成功');
                            $public->adminlog('Accounts','修改人员---'.$basic['username'].'的基础信息，用户参数，角色');
                        }else{
                            $public->adminlog('Accounts','修改人员---'.$basic['username'].'的基础信息');
                        }
                    }
                }
            }else{
                $rs = $accounts->find('all',array('conditions' => array('loginname =' => $loginname)))->toArray();
                if(!empty($rs)){
                    $message = array('code'=>1,'msg'=>'登录名重复');
                    echo json_encode($message);exit();
                }

                if($this->request->data['date-mode']==0){
                    $basic['expire']=strtotime($this->request->data['time'])+86400;
                }else{
                    $basic['expire']='-1';
                }
                unset($this->request->data['time']);
                unset($this->request->data['date-mode']);
                $basic['create_time'] = time();
                $salt =$this-> random(6, '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ');
                $accountsss=$accounts->newEntity();
                $password = $this->request->data['password'];
                unset($this->request->data['password']);
                $newpassword = (new CmopPasswordHasher(array('salt'=>$salt)))->hash($password);
                $basic['password']=$newpassword;
                $basic['salt']=$salt;

                $accountsss=$accounts->patchEntity($accountsss,$basic);

                $result =$accounts->save($accountsss);//向数据库添加用户信息

                if(!empty($this->request->data)){

                    $_con = $roles->connection();
                    $roles_id = explode(',',$this->request->data['role_id']);
                    $roles_id = array_filter($roles_id);
                    unset($this->request->data['role_id']);
                    $_con->begin();
                    if($result['id']){
                        //添加用户参数
                        $datas = $this->request->data;
                        $datas_count = count($datas);
                        $j=0;
                        $user = array();
                        $user['owner_id']=$result['id'];
                        $user['owner_type'] = 1;
                        $user['para_value1'] = '';
                        foreach($datas as $key =>$value){
                            $user['para_code']=$key;
                            foreach ($value as $k=>$v) {
                                $user[$k]=$v;
                            }
                            $seting = $usersetting->newEntity();
                            $seting = $usersetting->patchEntity($seting,$user);
                            $result_setting = $usersetting->save($seting);
                            if($result_setting){
                                $j++;
                            }
                        }

                        //添加用户角色
                        $number = count($roles_id);
                        $i=0;
                        foreach ($roles_id as $value) {
                            $role_arr=array();
                            $role_arr['account_id'] = $result['id'];
                            $role_arr['role_id']=$value;
                            $role=$roles->newEntity();
                            $role=$roles->patchEntity($role,$role_arr);
                            $roles_result =$roles->save($role);//向数据库添加用户信息
                            if($roles_result){
                                $i++;
                            }
                        }
                        if($number == $i && $datas_count == $j){
                            $_con->commit();
                            $message = array('code'=>0,'msg'=>'操作成功');
                            $public->adminlog('Accounts','添加人员---'.$basic['username'].'的基础信息,用户参数，角色');
                        }else{
                            $_con->rollback();
                            $message = array('code'=>0,'msg'=>'操作失败');
                        }
                    }
                    echo json_encode($message);exit();
                }else{
                    if($result){
                        $message = array('code'=>0,'msg'=>'操作成功');
                        $public->adminlog('Accounts','添加人员---'.$basic['username'].'的基础信息');
                    }
                }
            }
            //保存分类的拥有的属性信息
        }
        echo json_encode($message);exit();
        $this->lauout = 'ajax';
    }

    public function edit($id=0,$depart_id,$page=1){
        $checkPopedomlist_tenant = parent::checkPopedomlist('cmop_global_tenant_admin');
        $checkPopedomlist_sys = parent::checkPopedomlist('cmop_global_sys_admin');
        if ($checkPopedomlist_tenant) {
            if (!$checkPopedomlist_sys) {
                if($depart_id != $this->request->session()->read('Auth.User.department_id')){
                    return $this->redirect('/admin/accounts/edit/'.$id.'/'.$this->request->session()->read('Auth.User.department_id'));
                }
            }
        }

        $id = isset($this->request->data['id'])?$this->request->data['id']:$id;
        //显示部门
        $departments = TableRegistry::get('Departments');
        $data = $departments->find()->select(['id','name'])->toArray();
        $accounts = TableRegistry::get('Accounts');
        $acc = $accounts->find('all',array('conditions' => array('id =' => $id)))->toArray();//获取该分类的信息

        //角色显示
        $limit = 15;
        if($page > 0){
            $offset = $page-1;
        }else {
            $offset = 0;
        }
        $offset = $offset*$limit;
        $connection = ConnectionManager::get('default');
        $sql = "SELECT cp_roles.id AS id, cp_roles.name AS name, cp_roles.note AS note , cp_roles.department_id FROM cp_roles  where department_id = 0 or department_id =".$this->request->session()->read('Auth.User.department_id');
        $sql_row = $sql . " limit " . $offset . "," . $limit;
        $i = ceil($connection->execute($sql)->count()/$limit);
        $roles_data['roles']['total'] = $i;
        $roles_data['roles']['data'] = $connection->execute($sql_row)->fetchAll('assoc');//获取桌面信息
        //显示服务已关联的主机
        if($id){
            $sql_r_a = "SELECT role_id FROM `cp_roles_accounts` where account_id =".$id;
            $role_a = $connection->execute($sql_r_a)->fetchAll('assoc');
            $RoleID = array();
            if($role_a){
                foreach ($role_a as $key => $value) {
                    $RoleID[]=$value['role_id'];
                }
            }
            //var_dump(implode(',',$RoleID));exit;
            $this->set('RoleID',implode(',',$RoleID));
        }

        $systemsetting = TableRegistry::get('Systemsetting');
        $para = $systemsetting->find()->select(['id','para_code','para_value','para_note'])->where(array('para_type'=>2))->toArray();
        $this->set('para',$para);
        $usersetting = TableRegistry::get('UserSetting');
        $user_data = $usersetting->find('all')->where(array('owner_type'=>1,'owner_id'=>$id))->toArray();
        foreach ($user_data as $key => $value) {
            $para_code[]=$value['para_code'];
            $this->set('para_code',$para_code);
        }
        if($user_data){
            $this->set('user_data',$user_data);
        }
        $this->set('roles', $roles_data);
        $this->set('depart_id', $depart_id);
        $this->set('page',$page);
        $this->set('acc',$acc);
        $this->set('data',$data);
    }

    //随机码
    public  function random($length, $chars = '0123456789') {
        $hash = '';
        $max = strlen($chars) - 1;
        for($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
        return $hash;
    }

    /*
    *删除
    */
    public function delete(){
        $id=$this->request->data['id'];
    	$accounts = TableRegistry::get('Accounts',['classname'=>'App\Model\Table\AccountsTable']);
    	// $this->autoRender = false;
        if ($this->request->is('post')){
            $public = new PublicController();
            $id = isset($this->request->data['id'])?$this->request->data['id']:$id;
            $message = array('code'=>1,'msg'=>'操作失败');
            $account = $accounts->get($id);
            $account = $accounts->patchEntity($account,$this->request->data);
            $account->id = $id;
            $accounname = $accounts->find()->select(['username'])->where(array('id'=>$id))->toArray();
            if($this->request->session()->read('Auth.User.id')==$id){
                $message = array('code'=>1,'msg'=>'该账户正在登陆中，无法删除');
            }else {
                if ($accounts->delete($account)) {
                    $roles_accounts = TableRegistry::get('RolesAccounts');
                    $count = $roles_accounts->find()->where(array('account_id' => $id))->count();
                    $result = $roles_accounts->deleteAll(array('account_id' => $id));

                    $usersetting = TableRegistry::get('UserSetting');
                    $user_count = $usersetting->find('all')->where(array('owner_type' => 1, 'owner_id' => $id))->count();
                    $results = $usersetting->deleteAll(array('owner_id' => $id, 'owner_type' => 1));
                    if ($count == $result && $user_count == $results) {
                        $message = array('code' => 0, 'msg' => '操作成功');
                        $public->adminlog('Accounts','删除人员---'.$accounname[0]['username']);
                    }
                }
            }

            echo json_encode($message);exit;
            // $this->lauout = 'ajax';
        }

    }

    //ajax分页
    public function getroles($page =1){

        $limit = 15;
        if($page > 0){
            $offset = $page-1;
        }else {
            $offset = 0;
        }
        $offset = $offset*$limit;
        $connection = ConnectionManager::get('default');
        $sql = "SELECT cp_roles.id AS id, cp_roles.name AS name, cp_roles.note AS note, cp_roles.department_id FROM cp_roles where department_id = 0 or department_id =".$this->request->session()->read('Auth.User.department_id');
        $sql_row = $sql . " limit " . $offset . "," . $limit;
        $i = ceil($connection->execute($sql)->count()/$limit);
        $data['total'] = $i;
        $data['data']  = $connection->execute($sql_row)->fetchAll('assoc');//获取角色信息
        $data['page'] = $page;
        echo json_encode($data);exit();
        $this->lauout = 'ajax';
    }

 /*   private function _get_childrenid($departments,$arr,$cid){
        $cat_id = $arr;
        if($cid==0){
            $info['cat_id']=null;
            $departments->behaviors()->Departments->config('scope',['1'=>1]);
            $depart = $departments->find('all')->toArray();
            $info['depart']=$depart;
            return $info;
        }
        $catid = $departments->find('all')->where(array('parent_id'=>$cid))->toArray();
        foreach($catid as $value){
            $cat_id[] = 'department_id ='.$value['id'];
            $cd = $departments->find('all')->where(array('parent_id'=>$value['id']))->count();
            if($cd>0){
                $cat_id = $this->_get_childrenid($departments,$cat_id,$value['id']);
            }
        }
        $info=$cat_id;
        return $info;
    }

    private function _get_info($departments){
         //动态配置behavior
        $departments->behaviors()->Departments->config('scope',['1'=>1]);
        $depart = $departments->find('all')->toArray();
        $info=$depart;
        return $info;
    }


     private function _get_tree($cate,$pid=0,$level=0,$html='　　'){
        $tree = array();
        foreach($cate as $v){
            if($v['parent_id'] == $pid){
                $v['level'] = $level;
                $v['html'] = str_repeat($html,$level);
				$v['href'] = '/admin/accounts/index/'.$v['id'];
                $tree[] = $v;
                $tree = array_merge($tree, $this->_get_tree($cate,$v['id'],$level+1));
            }
        }
        return $tree;
    }

    public function arrayToTree($sourceArr, $key, $parentKey, $childrenKey)
    {
        $tempSrcArr = array();

        $allRoot = TRUE;
        foreach ($sourceArr as  $v)
        {
            $isLeaf = TRUE;
            foreach ($sourceArr as $cv )
            {
                if (($v[$key]) != $cv[$key])
                {
                    if ($v[$key] == $cv[$parentKey])
                    {
                        $isLeaf = FALSE;
                    }
                    if ($v[$parentKey] == $cv[$key])
                    {
                        $allRoot = FALSE;
                    }
                }
            }
            if ($isLeaf)
            {
                $leafArr[$v[$key]] = $v;
            }
            $tempSrcArr[$v[$key]] = $v;
        }
        if ($allRoot){
            return $tempSrcArr;
        }else{
            unset($v, $cv, $sourceArr, $isLeaf);
            foreach ($leafArr as  $v)
            {
                if (isset($tempSrcArr[$v[$parentKey]]))
                {
                    $tempSrcArr[$v[$parentKey]][$childrenKey] = (isset($tempSrcArr[$v[$parentKey]][$childrenKey]) && is_array($tempSrcArr[$v[$parentKey]][$childrenKey])) ? $tempSrcArr[$v[$parentKey]][$childrenKey] : array();
                    array_push ($tempSrcArr[$v[$parentKey]][$childrenKey], $v);
                    unset($tempSrcArr[$v[$key]]);
                }
            }
            unset($v);
            return $this->arrayToTree($tempSrcArr, $key, $parentKey, $childrenKey);
        }
    }  */
}