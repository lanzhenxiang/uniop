<?php
/** 
* 管理员后台 控制器基类
* 
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date  2015年9月8日下午2:20:08
* @source BasicController.php
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 
namespace App\Controller\Admin;

use App\Controller\SobeyController;

class BasicController extends SobeyController
{
    /*public function initialize()
    {
        parent::initialize();
        //
        //加载部分组件
        
        //加载cookie组件
        $this->loadComponent('Cookie',[
                'encryption' => false
        ]);
        

        //加载认证组件
        $this->loadComponent('Auth',[
                'loginAction' => [
                        'controller' => 'Accounts',
                        'action' => 'login',
                ],
                'authenticate' => [
                        'Form'=>[
                                'userModel'=>'Accounts',
                                //'scope' =>  ['Accounts.status >'=>0],
                        'passwordHasher' => [
                                'className' => 'Cmop',
                        ],
                ]
        ]
        ]);

    }*/
}