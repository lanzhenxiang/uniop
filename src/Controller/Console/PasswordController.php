<?php
/** 
* 文件描述文字
* 
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date  2015年9月21日下午4:28:04
* @source ConsoleController.php
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 

namespace App\Controller\Console;

use App\Auth\CmopPasswordHasher;
use App\Controller\Console\ConsoleController;
use Cake\ORM\TableRegistry;

class PasswordController extends  ConsoleController
{	
    private function _checkPopedom($param)
    {
        $popedom = parent::checkConsolePopedom($param);
        return $popedom;
    }
    public function index(){
        $checkPopedomlist = $this->_checkPopedom('ccm_user_changepass');
        if (!$checkPopedomlist)
        {
            return $this->redirect('/console/');
        }
        if($this->request->is('post')){
            $message = array('code'=>1,'msg'=>'修改密码失败');
            $old_password = $this->request->data['old_password'];
            $password = $this->request->data['password'];
            $id = $this->request->session()->read('Auth.User.id');
            $account = TableRegistry::get('Accounts');
            $account_password = $account->find()->select(['password'])->where(['id'=>$id])->toArray();
            if($account_password){
                if((new CmopPasswordHasher(array('salt'=>$this->request->session()->read('Auth.User.salt'))))->check($old_password,$account_password[0]['password'])){
                    $password=(new CmopPasswordHasher(array('salt'=>$this->request->session()->read('Auth.User.salt'))))->hash($password);
                    $result = $account->updateAll(array('password'=>$password),array('id'=>$id));
                    if($result){
                        $message = array('code'=>0,'msg'=>'修改密码成功');
                    }
                }else{
                    $message = array('code'=>1,'msg'=>'旧密码输入有误');
                }
            }
            echo json_encode($message);exit;
        }
    }

}