<?php
/**
 * 文件描述文字
 *
 *
 * @author shrimpliao
 * @date  2016-1-27 10:37:06
 * @source AccountsController.php
 * @version 1.0.0
 * @copyright  Copyright 2015 sobey.com
 */
namespace App\Controller\Admin;

use App\Controller\AdminController;
use App\Controller\SobeyController;
use Cake\ORM\TableRegistry;
use Composer\Autoload\ClassLoader;
use App\Controller\OrdersController;
use Cake\Core\Configure;
use Cake\I18n\Time as CakeTime;

class LogController extends AdminController
{
    public $paginate = array('limit' => 15);
    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow('image');
    }

    /**
     * 初始化默认日期
     * @param  [string] $start [开始日期]
     * @param  [string] $end   [结束日期]
     * @return [array]        [$start,$end]
     */
    protected function _initStartAndEndDate($start,$end){
        $I18n_time = new CakeTime();
        $I18n_time->setToStringFormat('yyyy-MM-dd');
        //默认结束日期为当前日期
        if(empty($end)){
            $end = $I18n_time->addDay()->i18nFormat();
        }
        //默认开始日期为当前月第一天
        if(empty($start)){
            $start = $I18n_time->startOfMonth()->i18nFormat();
        }
        return [$start,$end];
    }

    public function instancelog($department_id = -1,$start = '',$end = '',$loginname ="")
    {
        $instance_Log = TableRegistry::get('InstanceLogs');
        $department_table = TableRegistry::get('Departments');
        $where = array();

        list($start,$end) = $this->_initStartAndEndDate($start,$end);
        if($start != ''){
            $where['InstanceLogs.create_time >='] = strtotime($start);
        }
        if($end != ''){
            $where['InstanceLogs.create_time <='] = strtotime($end);
        }

        $query = $instance_Log->find('all')->where($where)
        ->matching('Accounts',function($q) use ($department_id,$loginname){
            $condition['1'] = 1;
            if($department_id != -1){
                $condition['department_id'] = $department_id;
            }
            if($loginname !=""){
                $condition['loginname like'] = '%'.$loginname.'%';
            }
            return $q->where($condition);
        })
        ->contain(array('InstanceBasic', 'Accounts'))->order(['InstanceLogs.create_time'=>'DESC']);
        //debug($data);exit;
        //获取租户信息
        $departments = $department_table->find()->toArray();
        $department  = $department_table->findById($department_id)->first();
        if($department == null){
            $department['name'] = "全部";
            $department['id']   = '-1';
        }
        $data = $this->paginate($query);
        $this->set('department',$department);
        $this->set('departments',$departments);
        $this->set('loginname',$loginname);
        $this->set('start',$start);
        $this->set('end',$end);
        $this->set('data', $data);
    }
    public function adminlog($department_id = -1,$start = '',$end = '',$loginname ="")
    {
        $instance_Log = TableRegistry::get('AdminLogs');
        $department_table = TableRegistry::get('Departments');
        $where = array();
        
        list($start,$end) = $this->_initStartAndEndDate($start,$end);
        if($start != ''){
            $where['AdminLogs.create_time >='] = strtotime($start);
        }
        if($end != ''){
            $where['AdminLogs.create_time <='] = strtotime($end);
        }

        $query = $instance_Log->find('all')->where($where)
        ->matching('Accounts',function($q) use ($department_id,$loginname){
            $condition['1'] = 1;
            if($department_id != -1){
                $condition['department_id'] = $department_id;
            }
            if($loginname !=""){
                $condition['loginname like'] = '%'.$loginname.'%';
            }
            return $q->where($condition);
        })
        ->contain(array('Accounts'))->order(['AdminLogs.create_time'=>'DESC']);
        //debug($data);exit;
        //获取租户信息
        $departments = $department_table->find()->toArray();
        $department  = $department_table->findById($department_id)->first();
        if($department == null){
            $department['name'] = "全部";
            $department['id']   = '-1';
        }
        $data = $this->paginate($query);
        $this->set('department',$department);
        $this->set('departments',$departments);
        $this->set('loginname',$loginname);
        $this->set('start',$start);
        $this->set('end',$end);
        $this->set('data', $data);
    }

    public function servicelog($name=''){
        $AutoTaskLogs = TableRegistry::get('AutoTaskLogs');
        $where = array();
        if ($name) {
            $where['task_name like'] = "%{$name}%";
        }
        $data = $AutoTaskLogs->find('all')->where($where)->order(['begin_time'=>'DESC']);
        $data = $this->paginate($data);
        $this->set('name', $name);
        $this->set('data', $data);
    }
    
    public function checkresult(){
    	$AutoTaskLogs = TableRegistry::get('AutoTaskLogs');
    	$data = $AutoTaskLogs->find('all')->select(['exec_result'])->where(array('id'=>$this->request->data['id']))->first();
    	echo json_encode($data['exec_result']);exit;
    	
    }
}