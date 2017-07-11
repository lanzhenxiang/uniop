<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2015/11/6
 * Time: 11:00
 */

namespace App\Controller\Admin;


use App\Controller\AdminController;
use App\Controller\SobeyController;
use Aura\Intl\PackageLocator;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use App\Controller\Admin\HomeController;

class RolesController extends AdminController{
    public $paginate = [
        'limit' => 15,
    ];
    
    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_tenant_roles');
        if (! $checkPopedomlist) {
            return $this->redirect('/admin/');
        }
    }
    
    public function index($department_id=0,$name=''){
    	if(!is_numeric($department_id) && empty($name)){
            $name=$department_id;
            $department_id=0;
        }
        $departments = TableRegistry::get('Departments');
        $roles = TableRegistry::get('Roles');
        $where = array();
        if($name){
            $where['name like']='%'.$name.'%';
        }
        
        //返回角色数据
    	if(in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))){
    		$dept_grout = $departments->find()->select(['id','name'])->toArray();
    		if($department_id==0){
    			$where['department_id'] =$this->request->session()->read('Auth.User.department_id');
    		}elseif($department_id==-1){
    			$where['1'] =1;
    		}elseif($department_id==-2){
    			$where['department_id']=0;
    		}else{
    			$where['department_id']=$department_id;
    		}
    		
        }elseif(!in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname')) && in_array('cmop_global_tenant_admin',$this->request->session()->read('Auth.User.popedomname'))){
           $dept_grout = $departments->find()->select(['id','name'])->where(array('id'=>$this->request->session()->read('Auth.User.department_id')))->toArray();
           $where['department_id']=$this->request->session()->read('Auth.User.department_id');
        }
        $roles_data = $roles->find('all')->where($where);
        $data=$this->paginate($roles_data);
        
        //返回租户选择
        if($department_id==-1){
        	$this->set('department_name', '全部租户');
        }elseif($department_id==-2){
        	$this->set('department_name', '系统公用');
        }else{
        	if($department_id==0){
        		$department_id=$this->request->session()->read('Auth.User.department_id');
        	}
        	$department_data = $departments->find()->select(['id','name'])->where(array('id'=>$department_id))->toArray();
        	$this->set('department_name', $department_data[0]['name']);
        }
        
        $this->set('dept_grout', $dept_grout);
        $this->set('name',$name);
        $this->set('data',$data);
    }

    public function addedit($id=0){
        $roles = TableRegistry::get('Roles');
        $departments = TableRegistry::get('Departments');
        $dept_grout = $departments->find()->select(['id','name'])->toArray();
        $this->set('dept_grout', $dept_grout);
        if($this->request->is('get')){
        	
            if($id){
                $roleid =  $roles->find('all')->select(['id'])->where(array('department_id'=>$this->request->session()->read('Auth.User.department_id')))->toArray();
                foreach($roleid as $value){
                    $rolesid[] = $value['id'];
                }
                $popedomname = $this->request->session()->read("Auth.User.popedomname");
                //判断是否是系统管理员权限
                if(in_array('cmop_global_tenant_admin', $popedomname) && !in_array('cmop_global_sys_admin', $popedomname)) {
                    if (in_array($id, $rolesid)) {
                        $role_result = $roles->find('all')->where(array('id' => $id))->toArray();
                        $this->set('data', $role_result[0]);
                    } else {
                        return $this->redirect('/admin/roles');
                    }
                }else{
                    $role_result = $roles->find('all')->where(array('id' => $id))->toArray();
                    $this->set('data', $role_result[0]);
                }
            }
           
        }else{
            $message = array('code'=>1,'msg'=>'操作失败');
            $public = new PublicController();
            $role=$roles->newEntity();
            if(isset($this->request->data['id'])){
            	//var_dump($this->request->data);exit;
                $name = $roles->find('all')->select(['id','name'])->where(array('name'=>$this->request->data['name']))->toArray();
                //var_dump($name);exit;
                //判断修改的名字是否已经存在
                if($name){
                    foreach($name as $va){
                        if($va['id']!=$this->request->data['id'] && $va['name'] == $this->request->data['name']){
                            $message = array('code'=>1,'msg'=>'该角色已存在');
                            echo json_encode($message);exit;
                        }
                    }
                }
              
             	if(isset($this->request->data['department'])) {
                   if ($this->request->data['department'] == -1) {
                        $this->request->data['department_id'] = 0;
                    }
                }
                unset($this->request->data['department']);

                $tid = $this->request->data['id'];
                $this->request->data['modified']=date('Y-m-d H:i:s',time());
                $t_result = $roles->updateAll($this->request->data,array('id'=>$tid));
                if($t_result){
                if( $this->request->data['department_id'] ==0){
                		$message = array('code'=>0,'msg'=>'操作成功','url'=>'/admin/roles/index/-2');
                	}else{
                		$message = array('code'=>0,'msg'=>'操作成功','url'=>'/admin/roles/index/'.$this->request->data['department_id']);
                	}
                    $public->adminlog('Roles','修改角色---'.$this->request->data['name']);
                }
                echo json_encode($message);exit;
            }else{
                $count = $roles->find('all')->select(['id'])->where(array('name'=>$this->request->data['name']))->count();
                //判断名字是否已存在
                if($count>0){
                    $message = array('code'=>1,'msg'=>'该角色已存在');
                    echo json_encode($message);exit;
                }
                if(isset($this->request->data['department'])) {
                   if ($this->request->data['department'] == -1) {
                        $this->request->data['department_id'] = 0;
                        
                    }
                }
                unset($this->request->data['department']);
                $this->request->data['created']=date('Y-m-d H:i:s',time());
                $this->request->data['modified']=date('Y-m-d H:i:s',time());
                $role = $roles->patchEntity($role,$this->request->data);
                $result = $roles->save($role);
                if($result){
                	if( $this->request->data['department_id'] ==0){
                		$message = array('code'=>0,'msg'=>'操作成功','url'=>'/admin/roles/index/-2');
                	}else{
                		$message = array('code'=>0,'msg'=>'操作成功','url'=>'/admin/roles/index/'.$this->request->data['department_id']);
                	}
                    $public->adminlog('Roles','添加角色---'.$this->request->data['name']);
                }
                echo json_encode($message);exit;

            }
        }
    }

    //页面显示权限列表和软件权限列表
    public function addpopedom($id=0){
        $roles = TableRegistry::get('Roles');
        $connection = ConnectionManager::get('default');
        $department = TableRegistry::get('Departments');
        if($this->request->is('get')){
            $rolename =  $roles->find('all')->select(['name'])->where(array('id'=>$id))->toArray();
            //返回角色名称
            if($rolename){
                $this->set('rolename',$rolename[0]['name']);
            }
            $roleid =  $roles->find('all')->select(['id'])->where(array('department_id'=>$this->request->session()->read('Auth.User.department_id')))->toArray();
            foreach($roleid as $value){
                $rolesid[] = $value['id'];
            }
            //返回权限列表
            $popedomname = $this->request->session()->read("Auth.User.popedomname");
            $where ='';
            if(in_array('cmop_global_tenant_admin', $popedomname) && !in_array('cmop_global_sys_admin', $popedomname)) {
                $where = ' where  popedomname <> "cmop_global_sys_admin"';
            }
            $sql_popedom = "SELECT popedomid,parent_id,popedomnote FROM `cp_popedomlist`".$where;
            $popedom_data = $connection->execute($sql_popedom)->fetchAll('assoc');
            $depat_where=array();
            if(in_array('cmop_global_tenant_admin', $popedomname) && !in_array('cmop_global_sys_admin', $popedomname)) {
                $depat_where['id'] = $this->request->session()->read('Auth.User.department_id');
            }
           /* $roleIid=  $roles->find('all')->select(['department_id'])->where(array('id'=>$id))->toArray();
            if($roleIid[0]['department_id']){
                $depat_where['id'] = $this->request->session()->read('Auth.User.department_id');
            }*/
            $depart = $department->find()->select(['id', 'name', 'parent_id'])->where($depat_where)->toArray();
            $array_p = array();
            if ($id != 0) {
                //角色已存在的列表权限
                $sql_r_p = "SELECT popedomlist_id FROM `cp_roles_popedoms` where role_id =" . $id;
                $role_p = $connection->execute($sql_r_p)->fetchAll('assoc');

                if ($role_p) {

                    $popedomlist_ids = array_column($role_p, 'popedomlist_id');
                    $popedomlist_id = array_column($popedom_data, 'popedomid');

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


            //返回软件列表
            $sql_software = "SELECT id,software_code,software_name FROM `cp_software_list`";
            $software_data = $connection->execute($sql_software)->fetchAll('assoc');
            $array_s = array();
            if ($id != 0) {
                $sql_r_s = "SELECT software_id FROM `cp_roles_software` where role_id =" . $id;
                $role_s = $connection->execute($sql_r_s)->fetchAll('assoc');
                if ($role_s) {
                    $software_ids = array_column($role_s, 'software_id');
                    $software_id = array_column($software_data, 'id');
                    foreach ($software_data as $val) {
                        $array_s[$val['id']] = $val;
                    }
                    foreach ($software_ids as $value) {
                        if (in_array($value, $software_id)) {
                            $array_s[$value]['checked'] = 'true';
                        }
                    }
                    $array_s = array_values($array_s);
                }
            }
            if(in_array('cmop_global_tenant_admin', $popedomname) && !in_array('cmop_global_sys_admin', $popedomname)) {
                if (in_array($id, $rolesid)) {

                    if (empty($array_p)) {
                        $this->set('data', json_encode($popedom_data));
                    } else {
                        $this->set('data', json_encode($array_p));
                    }

                    if (empty($array_s)) {
                        $this->set('software', $software_data);
                    } else {
                        $this->set('software', $array_s);
                    }
                    $this->set('depart', json_encode($depart));
                    $this->set('id', $id);

                } else {
                    return $this->redirect('/admin/roles');
                }
            }else{
                if (empty($array_p)) {
                    $this->set('data', json_encode($popedom_data));
                } else {
                    $this->set('data', json_encode($array_p));
                }

                if (empty($array_s)) {
                    $this->set('software', $software_data);
                } else {
                    $this->set('software', $array_s);
                }
                $this->set('depart', json_encode($depart));
                $this->set('id', $id);
            }
        }else{

            $message = array('code'=>1,'msg'=>'操作失败');
            $data=$this->request->data;
            $public = new PublicController();
            $name=$roles->find()->select(['name'])->where(array('id'=>$id))->toArray();
            if(isset($data['type'])){
                if($this->request->data['type']=='popedom'){
                    $popedomlist = TableRegistry::get('RolesPopedoms');
                    $popedelete = $popedomlist->deleteAll(array('role_id'=>$id));
                    if(!empty($data['popeid'])){
                        $pepoarray = substr($data['popeid'],0,strlen($data['popeid'])-1);
                        $sql ="insert into cp_roles_popedoms(role_id,popedomlist_id) select $id,popedomid from cp_popedomlist where popedomid in ($pepoarray);";
                        $result = $connection->execute($sql);
                        if($result){
                            $public->adminlog('Roles','添加角色---'.$name[0]['name'].'的列表权限');
                            $message = array('code'=>0,'msg'=>'添加权限成功');
                        }
                    }else{
                        $public->adminlog('Roles','清空角色下的列表权限');
                        $message = array('code'=>0,'msg'=>'添加权限成功');
                    }
                }else if($data['type']=='software'){
                    $rolessoftware = TableRegistry::get('RolesSoftware');
                    $softdelete = $rolessoftware->deleteAll(array('role_id'=>$id));
                    if(!empty($data['softwareid'])){
                        $softarray=substr($data['softwareid'],0,strlen($data['softwareid'])-1);
                        $sql ="insert into cp_roles_software(role_id,software_id) select $id,id from cp_software_list where id in ($softarray);";
                        $result = $connection->execute($sql);
                        if($result){
                            $public->adminlog('Roles','添加角色---'.$name[0]['name'].'的软件权限');
                            $message = array('code'=>0,'msg'=>'添加权限成功');
                        }
                    }else{
                        $public->adminlog('Roles','清空角色下的软件权限');
                        $message = array('code'=>0,'msg'=>'添加权限成功');
                    }
                }else if($data['type']=='account'){
                   /* $account = TableRegistry::get('Accounts');
                    $depart_a = $account->find()->select('id')->where(array('department_id'=>$data['department']))->toArray();*/
                    //查询所选租户下的所有人员
                    $ss = "select id from cp_accounts where department_id =".$data['department'];
                    $depart_a = $connection->execute($ss)->fetchAll('assoc');
                    $depart_a = array_column($depart_a,'id');
                    $depart_a=implode(',',$depart_a);
                    //从角色人员表里删除该租户下的所有人员角色数据
                    $sqldelete = "DELETE  FROM cp_roles_accounts where role_id=$id and account_id in (".$depart_a .")";
                    $deleteresult = $connection->execute($sqldelete)->count();
                    //var_dump($deleteresult);exit;
                    //保存新选择人角色人员数据
                    if(!empty($data['accountid'])){
                        $accountarray=substr($data['accountid'],0,strlen($data['accountid'])-1);
                        $sql ="insert into cp_roles_accounts(role_id,account_id) select $id,id from cp_accounts where id in ($accountarray);";
                        $result = $connection->execute($sql);
                        if($result){
                            $public->adminlog('Roles','添加角色---'.$name[0]['name'].'下的人员');
                            $message = array('code'=>0,'msg'=>'添加权限成功');
                        }
                    }else{
                        $public->adminlog('Roles','清空角色下的人员');
                        $message = array('code'=>0,'msg'=>'添加权限成功');
                    }
                }
            }
            echo json_encode($message);exit;
        }
    }

    public function accountlist(){
        $connection = ConnectionManager::get('default');
        $sql = "SELECT id,username,loginname FROM `cp_accounts` where department_id =".$this->request->data['depart_id'];
        $account_data = $connection->execute($sql)->fetchAll('assoc');
        $account_id = array_column($account_data,'id');
        if($account_data){
            $sql = "SELECT account_id FROM `cp_roles_accounts` where role_id =".$this->request->data['role_id'];
            $role_account = $connection->execute($sql)->fetchAll('assoc');
            if($role_account){
                $account_ids = array_column($role_account,'account_id');
                $array = array();
                foreach($account_data as $value){
                    $array[$value['id']]=$value;
                }
                foreach($account_ids as $value){
                    if(in_array($value,$account_id)){
                        $array[$value]['checked']='true';

                    }
                }
                $msg=array('code'=>0,'data'=>$array);
            }else{
                $msg=array('code'=>0,'data'=>$account_data);
            }

        }else{
            $msg=array('code'=>1,'data'=>'该租户下暂时无人员存在');
        }
        echo json_encode($msg);exit;
    }


    public function delete(){
        if($this->request->data['id']){
        	//var_dump($this->request->data);exit;
            $message = array('code'=>1,'msg'=>'操作失败');
            $roles = TableRegistry::get('Roles');
            $public = new PublicController();
            $roles_accounts = TableRegistry::get('RolesAccounts');
            $account_count = $roles_accounts->find()->select(['account_id'])->where(array('role_id'=>$this->request->data['id']))->count();
            if($account_count){
                $message = array('code'=>1,'msg'=>'该角色下存在人员不能删除');
            }else{
                $roles_popedoms = TableRegistry::get('RolesPopedoms');
                $roles_software = TableRegistry::get('RolesSoftware');
                $id=$this->request->data['id'];
                $name=$roles->find()->select(['name'])->where(array('id'=>$id))->toArray();
                $res = $roles->deleteAll(array('id'=>$id));
                if($res){
                    $account_count = $roles_accounts->find()->select(['account_id'])->where(array('role_id'=>$id))->count();
                    $popedom_count = $roles_popedoms->find()->select(['popedomlist_id'])->where(array('role_id'=>$id))->count();
                    $software_count = $roles_software->find()->select(['software_id'])->where(array('role_id'=>$id))->count();
                    $account_result = $roles_accounts->deleteAll(array('role_id'=>$id));
                    $popedoms_result = $roles_popedoms->deleteAll(array('role_id'=>$id));
                    $software_result = $roles_software->deleteAll(array('role_id'=>$id));
                    $type = true;
                    if($account_count!=$account_result){
                        $type=false;
                    }
                    if($popedom_count!=$popedoms_result){
                        $type=false;
                    }
                    if($software_count!=$software_result){
                        $type=false;
                    }

                    if($type==true){
                        $public->adminlog('Roles','删除角色---'.$name[0]['name']);
                        $message = array('code'=>0,'msg'=>'操作成功','url'=>$this->request->data['url']);
                    }else{
                        $public->adminlog('Roles','删除角色---'.$name[0]['name'].',成功,删除关联权限,失败');
                    }
                }
            }

            echo json_encode($message);exit();
        }
    }
}