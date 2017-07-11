<?php
/**
 * Created by PhpStorm.
 * User: kelly
 * Date: 2017/5/2
 * Time: 14:06
 */

namespace App\Controller\Api;

use App\Controller\SobeyController;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Composer\Autoload\ClassLoader;
use Cake\Cache\Cache;
use \Requests as Requests;
use Cake\Datasource\ConnectionManager;

class AccountsApiController extends SobeyController
{
    private $_data = null;
    public function initialize()
    {
        parent::initialize();
        $this->_data = $this->_getData();
    }

    //添加用户
    public function addAccount()
    {
        $account_table = TableRegistry::get('Accounts');
        $request = $this->_data;
        if(!isset($request['username'])||empty($request['username'])||!isset($request['loginname'])||empty($request['loginname'])||!isset($request['email'])||empty($request['email'])||!isset($request['departmentID'])||empty($request['departmentID'])){
            echo json_encode(array('code'=>2,'msg'=>'传入参数有误'));exit;
        }
        if ($account_table->find()->where(array('loginname' => $request['loginname']))->count()>0) {
            echo json_encode(array('code' => 4, 'msg' => '登录名已存在'));
            exit;
        }
        if ($account_table->find()->where(array('username' => $request['username']))->count()>0) {
            echo json_encode(array('code' => 4, 'msg' => '用户名已存在'));
            exit;
        }
        $data=array(
            'loginname'=>$request['loginname'],
            'username'=>$request['username'],
            'email'=>$request['email'],
            'department_id'=>$request['departmentID']
        );
        $account_data=$account_table->newEntity();
        $account_data=$account_table->patchEntity($account_data,$data);
        $result=$account_table->save($account_data);
        if($result){
            echo json_encode(array('code'=>0,'msg'=>'success'));exit;
        }else{
            echo json_encode(array('code'=>1,'msg'=>'fail'));exit;
        }

    }

    //编辑用户
    public function editAccount()
    {
        $account_table = TableRegistry::get('Accounts');
        $request = $this->_data;


        echo json_encode(array('code'=>0,'msg'=>'success'));exit;
    }

    //创建角色
    public function addRole()
    {
        $account_table = TableRegistry::get('Roles');
        $request = $this->_data;


        echo json_encode(array('code'=>0,'msg'=>'success','roleID'));exit;
    }

    //编辑角色
    public function editRole()
    {
        $account_table = TableRegistry::get('Roles');
        $request = $this->_data;


        echo json_encode(array('code'=>0,'msg'=>'success','roleID'));exit;
    }

    //分配用户到角色
    public function moveAccountToRole()
    {
        $account_table = TableRegistry::get('Roles');
        $request = $this->_data;


        echo json_encode(array('code'=>0,'msg'=>'success'));exit;
    }

    //从角色中移出用户
    public function deleteAccountfromRole()
    {
        $account_table = TableRegistry::get('Roles');
        $request = $this->_data;


        echo json_encode(array('code'=>0,'msg'=>'success'));exit;
    }

    //获取角色列表
    public function getRoles()
    {
        $role_table = TableRegistry::get('Roles');
        $request = $this->request->query;
        $roleInfo = $role_table->find()->select(['name', 'note'])->toArray();
        echo json_encode(array('code' => 0, 'msg' => 'success', 'roleInfo' => $roleInfo));
        exit;
    }

    //获取租户列表
    public function getDepartments()
    {
        $department_table = TableRegistry::get('Departments');
        $request = $this->request->query;
        $departmentInfo = $department_table->find()->select(['name' => 'name', 'departmentID' => 'id'])->toArray();
        echo json_encode(array('code' => 0, 'msg' => 'success', 'departmentInfo' => $departmentInfo));
        exit;
    }
    protected function _getData()
    {
        $data = $this->request->data ? $this->request->data : file_get_contents('php://input', 'r');
        //处理非x-form的格式
        if (is_string($data)) {
            $data_tmp = json_decode($data, true);
            if (json_last_error() == JSON_ERROR_NONE) {
                $data = $data_tmp;
            }
        }
        return $data;
    }
}