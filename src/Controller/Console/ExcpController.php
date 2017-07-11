<?php
/**
* 异常管理控制器
*
* @file: ExcpController.php
* @date: 2016年3月10日 下午2:52:49
* @author: xingshanghe
* @email: xingshanghe@icloud.com
* @copyright poplus.com
*
*/

namespace App\Controller\Console;

use App\Controller\Console\ConsoleController;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

class ExcpController extends ConsoleController
{

    /**
     * @func: 异常页面加载显示
     * @param:
     * @date: 2016年3月10日 下午6:48:08
     * @author: zhaodanru
     * @return: null
     */
    public function lists($interface='excp',$type='excp',$department_id=0,$operation_code='all',$start = 0, $end = 0,$basic_id=0) {

    	$limit=10;$offset=0;
    	if ($offset>0) {
    		$offset = $offset-1;
    	}
    	$offset =  $offset*$limit;
    	 //var_dump($department_id);exit;
    	$task = TableRegistry::get('task');
    	$where =array();
    	if($interface=='excp'){
    		$where['task.status not in'] = array('1','2','6','7');
    	}elseif($interface =='normal'){
    		$where['task.status'] = '2';
    	}elseif($interface=='executing'){
			$where['task.status in']=array('1','6','7');
		}

    	if($operation_code !='all'){
    		$where['task.task_type'] = $operation_code;
    	}


     	if($basic_id==0){
     		//返回租户

     		$_time = time();

     		if (!$start) {
     			$_y = date("Y", $_time);
     			$_m = date("m", $_time);
     			$_h = date("H:i:s", $_time);
     			$start = $_y . '-' . $_m . '-01'.' '.$_h;
     		}
     		$where['task.create_time >'] = strtotime($start);

     		if (!$end) {
     			$end = date("Y-m-d H:i:s", $_time);

     		}
     		$where['task.create_time <'] = strtotime($end);

     		$dept_grout =array();
     		$departments = TableRegistry::get('Departments');
     		$operation_data = json_decode(file_get_contents('../tools_go/cmopApi/configs/api.json'));
     		$operation_data = $this->objecttoarray($operation_data);
     		ksort($operation_data);
     		$this->set('operation_data',$operation_data);
     		//添加租户判断
     		if(in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))){
     			$dept_grout = $departments->find()->select(['id','name'])->toArray();
     			if($department_id!=0){
     				$where['InstanceBasic.department_id']=$department_id;
     			}
     		}else{
     			$department_id=$this->request->session()->read('Auth.User.department_id');
     			$where['InstanceBasic.department_id']=$this->request->session()->read('Auth.User.department_id');

     		}
     		$this->set('dept_grout',$dept_grout);

     		//$this->set('department_id',$department_id);

     		$task_data = $task->find('all')->contain(['InstanceBasic'])->where($where)->order(['task.create_time' => 'desc'])->limit($limit)->offset($offset)->toArray();
     		//$this->set('action',$type);
     		//$this->set('task_data',$task_data);
     		//$this->set('department_id',$department_id);
     	}else{
     		$where['or'] = array('task.assistant_basic_id'=>$basic_id,'task.basic_id'=>$basic_id);
     		$task_data = $task->find('all')->contain(['InstanceBasic'])->where($where)->order(['task.create_time' => 'desc'])->limit($limit)->offset($offset)->toArray();

     	}
     	//var_dump($type);exit;
     	//var_dump($start,$end);exit;
     	$this->set('start', $start);
     	$this->set('end', $end);
     	$this->set('operation_code',$operation_code);
     	$this->set('department_id',$department_id);
     	$this->set('action',$type);
     	$this->set('interface',$interface);
     	$this->set('task_data',$task_data);
     	$this->set('id',$basic_id);
    }

    public function gettask($interface='excp',$type='excp',$limit=10,$offset=0,$department_id=0,$operation_code='all',$start = 0, $end = 0,$basic_id=0) {
    	if ($offset>0) {
    		$offset = $offset-1;
    	}
    	$offset =  $offset*$limit;
    	$task = TableRegistry::get('task');
    	$where =array();
    	if($interface=='excp'){
    		$where['task.status not in'] = array('1','2','6','7');
    	}elseif($interface =='normal'){
    		$where['task.status'] = '2';
    	}else if($interface=='executing'){
			$where['task.status in']=array('1','6','7');
		}

    	if ($start) {
    		$where['task.create_time >'] = strtotime($start);
    	}


    	if ($end) {
    		$where['task.create_time <'] = strtotime($end);
    	}

        if($operation_code !='all'){
            $where['task.task_type'] = $operation_code;
        }

    	if($basic_id==0){
    		if($department_id!=0){
    			$where['InstanceBasic.department_id']=$department_id;
    		}
    	}else{
    		$where['or'] = array('task.assistant_basic_id'=>$basic_id,'task.basic_id'=>$basic_id);
    	}


    	$task_data = $task->find('all')->contain(['InstanceBasic'])->where($where)->order(['task.create_time' => 'desc'])->limit($limit)->offset($offset)->toArray();
    	echo json_encode($task_data);exit;
    }


    public function messagedata(){
    	$task_id=$this->request->data['task_id'];
    	$task = TableRegistry::get('task');
    	$response_asyn_data = $task->find()->select(['response_asyn_data'])->where(array('task_id'=>$task_id))->first();
    	echo json_encode($response_asyn_data);exit;

    }

    //对象转数组
	public function objecttoarray($e){
	    $e=(array)$e;
	    foreach($e as $k=>$v){
	        if( gettype($v)=='resource' ) return;
	        if( gettype($v)=='object' || gettype($v)=='array' )
	            $e[$k]=(array)$this->objecttoarray($v);
	    }
	    return $e;
	}
}