<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2016/1/28
 * Time: 15:11
 */

namespace App\Controller\Admin;


use App\Controller\SobeyController;
use Cake\ORM\TableRegistry;

class PublicController extends SobeyController{


    //后台操作日志
    public function adminlog($ui_name='',$user_event=''){
        $adminlogs = TableRegistry::get('AdminLogs');
        $data['ui_name'] =$ui_name;
        $data['create_time']=time();
        $data['user_id'] = $this->request->session()->read('Auth.User.id');
        $data['user_name'] = $this->request->session()->read('Auth.User.username');
        $data['user_event'] =$user_event;
        $adminlogdata = $adminlogs->newEntity();
        $adminlogdata = $adminlogs->patchEntity($adminlogdata,$data);
        $adminlogs->save($adminlogdata);
    }
}