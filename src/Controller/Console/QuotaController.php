<?php
/** 
* 文件描述文字
* 
* 
* @author wangjc
* @date  2015年9月21日下午4:28:04
* @source ConsoleController.php
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 

namespace App\Controller\Console;

use App\Controller\Console\ConsoleController;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Network\Http\Client;
use Cake\Core\Configure;
use Requests as Requests;

class QuotaController extends  ConsoleController
{	
    private function _checkPopedom($param)
    {
        $popedom = parent::checkConsolePopedom($param);
        return $popedom;
    }

    public function index(){
        $checkPopedomlist = $this->_checkPopedom('ccm_user_quota');
        if (! $checkPopedomlist)
        {
            return $this->redirect('/console/');
        }
        
        
        $id = $this->request->session()->read('Auth.User.id') ? $this->request->session()->read('Auth.User.id') : 0;
        $department_id = $this->request->session()->read('Auth.User.department_id');
        //租户配额，组合资源使用量
        $bugedt = Requests::post(Configure::read('Api.cmop').'/SystemInfo/getDepartBuget',[],[
            'userid'=>$id,
            'department_id'=>$department_id
        ],[
            'verify'=>false
        ]);
        $bugedt_arr = json_decode(trim($bugedt->body, chr(239) . chr(187) . chr(191)), true); //所以问题来了，不小心在返回的json字符串中返回了BOM头的不可见字符，某些编辑器默认会加上BOM头，如下处理才能
        
        $used = Requests::post(Configure::read('Api.cmop') . '/SystemInfo/getDepartUsed', [], [
            'userid' => $id,
            'department_id'=>$department_id,
            "source_type"=>"cpu_used,router_used,subnet_used,disks_used,gpu_used,memory_used,fics_num_used,oceanstor9k_num_used,basic_used,fire_used,elb_used,eip_used"
        ], [
            'verify' => false
        ]);
        
        $used_arr = json_decode(trim($used->body, chr(239) . chr(187) . chr(191)), true); //所以问题来了，不小心在返回的json字符串中返回了BOM头的不可见字符，某些编辑器默认会加上BOM头，如下处理才能
        
        
        $this->set('query',$bugedt_arr['data']);
        $this->set('data',$used_arr['data']);
    }

    
}