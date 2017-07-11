<?php
/**
* 运营管理中心后台
*
*
* @author lan
* @date  2016年12月22日下午4:28:04
* @copyright  Copyright 2016 sobey.com
*/

namespace App\Controller;

use App\Controller\AccountsController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;


class AdminController extends  AccountsController
{
    public function initialize()
    {
        parent::initialize();
        //RequestHandler
        $this->loadComponent('RequestHandler');
        $this->set('_admin_menu',$this->_get_admin_menu());
        $this->set('notifyUrl',Configure::read('NotifyUrl'));
    }

    /**
     * 获取后台菜单数据
     */
    protected function _get_admin_menu()
    {
        //获取用户权限
        $popedomname = $this->request->session()->read('Auth.User.popedomname') ? $this->request->session()->read('Auth.User.popedomname') : [];
        
        $admin_memu = TableRegistry::get('AdminMenu');
        return $admin_memu->find('tree')->where(['visibility'=>'1', 'popedom_code in' => $popedomname])->order('sort asc')->toArray();
    }

    public function checkAdminPopedom($param)
    {
        $adminPopedom = parent::checkPopedomlist($param);
        return $adminPopedom;
    }
}