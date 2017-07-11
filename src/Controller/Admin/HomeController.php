<?php
/** 
 * 文件描述文字
 * 
 * 
 * @author XingShanghe<xingshanghe@gmail.com>
 * @date  2015年9月6日下午5:30:30
 * @source HomeController.php
 * @version 1.0.0 
 * @copyright  Copyright 2015 sobey.com 
 */
namespace App\Controller\Admin;

use App\Controller\AdminController;
use App\Controller\Admin\BasicController;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

class HomeController extends AdminController
{    
    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_tenant_dashboard');
        if (! $checkPopedomlist) {
            return $this->redirect('/admin/department');
        }
    }
    public function display($subject = 'index', $tab = 0)
    {  
        
        // $this->layout = false;
        $path = func_get_args();
        
        if (! $subject) {
            return $this->redirect('/');
        }
        $this->set(compact('title', 'tab'));
        
        $this->autoRender = false;
        try {
            $this->render($subject);
        } catch (MissingTemplateException $e) {
            if (Configure::read('debug')) {
                throw $e;
            }
            throw new NotFoundException();
        }
    }
    
    public function index($department_id=0,$page =1) {
    	
        $limit = 15;
        if($page > 0){
            $offset = $page-1;
        }else {
            $offset = 0;
        }
        $offset = $offset*$limit;
    
        //返回租户数据
        $departments = TableRegistry::get('Departments');
        if(in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))){
        	$dept_grout = $departments->find()->select(['id','name'])->toArray();
        	
        }elseif(!in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname')) && in_array('cmop_global_tenant_admin',$this->request->session()->read('Auth.User.popedomname'))){
        	$dept_grout = $departments->find()->select(['id','name'])->where(array('id'=>$this->request->session()->read('Auth.User.department_id')))->toArray();
        }
        
        //返回租户选择
        $depart_id =0;
        $where ='';
        if($department_id==0){
        	//var_dump(123123);exit;
        	$this->set('department_name', '全部租户');
        	
        }else{
        	$department_data = $departments->find()->select(['id','name'])->where(array('id'=>$department_id))->toArray();
        	$this->set('department_name', $department_data[0]['name']);
        	$depart_id =$department_data[0]['id'];
        }
        
        $this->set('dept_grout', $dept_grout);
        $this->set('department_id',$depart_id);
        $checkSysAdmin = parent::checkPopedomlist('cmop_global_sys_admin');
       
        if(!$checkSysAdmin){
            $checkTenantAdmin = parent::checkPopedomlist('cmop_global_tenant_admin');
            if(!$checkTenantAdmin){
                return $this->redirect('/');
            }else{
            	$this->set('department_name',$this->request->session()->read('Auth.User.department_name'));
                $department_ids = (string) $this->request->session()->read('Auth.User.department_id');
                $where = ' AND a.department_id = '.$department_ids;
            }
        }else{
        	if($department_id !=0 ){
        		$where = ' and a.department_id = '.$department_id;
        	}
        	
        }
        $connection = ConnectionManager::get('default');
        $sql = "SELECT b.`name`, a.location_name, a.type, count(a.id) FROM `cp_instance_basic` a, cp_departments b WHERE a.department_id = b.id ".$where;
        $sql .= " GROUP BY b.`name`, a.location_name, a.type";
        $sql_row = $sql . " limit " . $offset . "," . $limit;
        
        $query = $connection->execute($sql_row)->fetchAll('assoc');
        $i = ceil($connection->execute($sql)->count()/$limit);
        $data['total'] = $i;
        $data['rows'] = $query;
        $this->set('data',$data);
        
        $this->set('page',$page);
    }

}