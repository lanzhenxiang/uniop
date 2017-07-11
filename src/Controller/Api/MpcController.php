<?php
namespace App\Controller\Api;
use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\Network\Http\Client;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class MpcController extends AppController{
	private $_db;
	private $_serialize = array('code','msg','data');
	private $_data;

	public function initialize(){
        parent::initialize();
        $this->_data = $this->_getData();
        $this->_db =ConnectionManager::get('default');
        $this->viewClass = 'Json';     

        $this->loadComponent('RequestHandler');
    }

    /**
     * 对弹性规则的格式进行检查
     */
    public function checkRules()
    {   
        $busy_instance =11;
        $free_instance=0;
        $wait_job=0;
        $min_instance=12;
        $max_instance=0;
        $process_efficiency=0;
        $current_instance=0;
        $find = array(
                '@busy_instance',
                '@free_instance',
                '@wait_job',
                '@min_instance',
                '@max_instance',
                '@process_efficiency',
                '@current_instance',
            );
        $replace = array(
                $busy_instance,
                $free_instance,
                $wait_job,
                $min_instance,
                $max_instance,
                $process_efficiency,
                $current_instance,
            );
        $str = $this->_data['rules'];
        $str = str_replace($find,$replace,$str);
        

        $result = ( @eval('
            try{
                if('.$str.'){
                    return true;
                }else{
                    return false;
                }
            }catch(\Exception $e){
                return FALSE;
            };'
            ) === FALSE ) ? FALSE : TRUE;
         
       
        if($result)
        {
            $code='0';
            $msg = '验证成功';
        }else{
            $code='1';
            $msg = '验证失败';
        }
       
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }

    /**
     *弹性计算检查器
     */
    public function serviceRules(){
        //获取服务列表
//         $sql = 'select * from cp_service_type';

         $serviceType = TableRegistry::get("ServiceType");
         $serviceRules = TableRegistry::get('ServiceRules');
         $result = $serviceType->find()->all();

//         $result = $this->_db->execute($sql)->fetchAll('assoc');
        //循环服务对应的规则
         foreach ($result as $key => $service) {
//            $rules = $this->_db->execute('select * from cp_service_rules where type_id='.$service['type_id'].' order by rule_weight desc')->fetchAll('assoc');

             $rules = $serviceRules->find()->where(['type_id'=>$service['type_id']])->order('rule_weight DESC')->toArray();
            //检查规则,符合条件在执行
            $this->_serviceRule($rules);
         }
        $code='0';
        $msg = '操作成功';
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }

    public function _serviceRule($rules,$i=0)
    {
        $serviceType = TableRegistry::get("ServiceType");
        $serviceList = TableRegistry::get('ServiceList');
        //初始化规则参数
        if(count($rules) >0){
//            $service = $this->_db->execute('select * from cp_service_type where type_id='.$rules[0]['type_id'])->fetchAll('assoc');
            $service = $serviceType->find()->where(['type_id'=>$rules[0]['type_id']])->first();
//            $service = $service[0];
        }else{
            return;
        }
        $busy_instance =$service['busy_instance'];
        $free_instance=$service['free_instance'];
        $wait_job=$service['wait_job'];
        $min_instance=$service['min_instance'];
        $max_instance=$service['max_instance'];
        $process_efficiency=$service['process_efficiency'];
        $current_instance=$service['current_instance'];
        $find = array(
                '@busy_instance',
                '@free_instance',
                '@wait_job',
                '@min_instance',
                '@max_instance',
                '@process_efficiency',
                '@current_instance',
            );
        $replace = array(
                $busy_instance,
                $free_instance,
                $wait_job,
                $min_instance,
                $max_instance,
                $process_efficiency,
                $current_instance,
            );

        $iscontinue = false;
        foreach ($rules as $key => $rule) {
            $str = str_replace($find,$replace,$rule['rule_expression']);
            $result = ( @eval('if('.$str.'){return true;}else{return false;};') === FALSE ) ? FALSE : TRUE;
            if($result === true){
                //执行对应操作
                if($rule['action_type'] == 1){
                    //获取服务所有开启实例列表，并且选择空闲最长时间的机器关闭。
//                    $sql = 'select basic.code,basic.create_by from cp_service_list as serverlist
//left join cp_instance_basic as basic on serverlist.basic_id=basic.id
//where serverlist.type_id='.$service['type_id'].' and serverlist.service_status =1 order by last_worktime';
//                    echo $sql;

                    $field = ['code'=>'desktop.code','create_by'=>'desktop.create_by'];

                    $where['ServiceList.type_id'] = $service['type_id'];
                    $where['ServiceList.service_status'] = 1;
                    $list = $serviceList->find()->join([
                        'desktop'=>[
                            'table'=>'cp_instance_basic',
                            'type'=>'LEFT',
                            'conditions'=>'desktop.id = ServiceList.basic_id'
                        ]
                    ])->select($field)->where($where)->order('last_worktime')->toArray();

//                    $list = $this->_db->execute($sql)->fetchAll('assoc');
                    if(!is_array($list) ||  count($list) >0){
                        $basic = $list[0];
                        print_r($basic);
                        $this->_instanceAction('ecs_stop',$basic['code'],$basic['create_by']);
                    }
                    $this->_instanceAction('ecs_stop','fff','11');
                    return ;
                }else{
                    //开启
                }
            }
         
        }
    }

    private function _instanceAction($method,$instanceCode,$uid=''){
         $http = new Client();
            $obj_response = $http->post(Configure::read('URL'),json_encode([
                "method"=>$method,
                "uid"=>strval($uid),
                "instanceCode"=>$instanceCode
                ]),['type' => 'json']);
            $response = json_decode($obj_response->body,true);
            print_r($response);
    }
    /**
     *更新服务处理任务的历史记录
     */
    public function updateServiceJobsHistory()
    {
        $this->_updateServiceJobsHistory(1);
        $code='0';
        $msg = '操作成功';
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }

    private function _updateServiceJobsHistory($page)
    {   
       
        $soap = $this->_getSoap();
        // 分页
         $returnStr= $soap->GetProjectList(array('request'=>'<GetProjectListRequest xsi:noNamespaceSchemaLocation="MPCWebService.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><SearchTaskMode>History</SearchTaskMode><Begin>2016-01-17</Begin><End>2016-02-03</End><Paging><PageSize>10</PageSize><PageIndex>'.$page.'</PageIndex></Paging></GetProjectListRequest>'))->GetProjectListResult;
        //不分页 
        //$returnStr= $soap->GetProjectList(array('request'=>'<GetProjectListRequest xsi:noNamespaceSchemaLocation="MPCWebService.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><SearchTaskMode>History</SearchTaskMode><Begin>2016-01-17</Begin><End>2016-02-03</End></GetProjectListRequest>'))->GetProjectListResult;
        
        $xml = simplexml_load_string($returnStr);

        $mpcTask = TableRegistry::get('MpcTask');
       
        foreach ($xml->ProjectList->Project as $k => $project) {
            
            foreach ($project->MPC_Job as $key => $job) {
                //插入前判断是否有值
//                $sql = 'select id from cp_mpc_task where job_id="'.$job->JobID->__toString().'"';
                $result = $mpcTask->find()->select('id')->where(['job_id'=>$job->JobID->__toString()])->toArray();
//                $result = $this->_db->execute($sql)->fetchAll('assoc');
                if(!is_array($result) ||  count($result) <1){
                    $length = $project->TaskLength->__toString()/10000;
                    $etime = strtotime($job->FinishTime->__toString())-strtotime($job->ExecuteTime->__toString());
                    $sql = 'insert into cp_mpc_task (project_id,task_guid,task_name,task_length,Receive_Time,column_name,column_code,policy_id,job_id,job_type,exec_server,exec_time,finish_time,begin_time) ';
                    $sql .=" values ('".$project->ProjectID->__toString()."','".$project->TaskGuid->__toString()."','".$project->TaskName->__toString()."','".$length."','".$project->ReceiveTime->__toString()."','".$project->ColumnName->__toString()."','".$project->ColumnCode->__toString()."','".$project->PolicyID->__toString()."','".$job->JobID->__toString()."','".$job->JobType->__toString()."','".$job->ExecuteServer->__toString()."','".$etime."','".$job->FinishTime->__toString()."','".$job->ExecuteTime->__toString()."')";
                    //echo $sql;
                    $this->_db->execute($sql);
                }
            }

        }
        if($xml->Paging->PageCount->__toString() > $page){
            $this->_updateServiceJobsHistory($page+1);
        }

        //判断
    }

    public function updateServiceJobs()
    {
        $soap = $this->_getSoap();
        $returnStr= $soap->GetProjectList(array('request'=>'<GetProjectListRequest xsi:noNamespaceSchemaLocation="MPCWebService.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><SearchTaskMode>Current</SearchTaskMode></GetProjectListRequest>'))->GetProjectListResult;
        $xml = simplexml_load_string($returnStr);
        if($xml->Result->Status =="Success"){
            //循环出所有的服务
            $sql = "select * from cp_service_type";
            $result=$this->_db->execute($sql)->fetchAll('assoc');
            foreach ($result as $key => $value) {
                $waitJobsNum=0;
                $jobTypeArr = explode(',',$value['job_type']);
                foreach ($xml->ProjectList->Project as $k => $v) {
                   foreach ($v->MPC_Job as $kk => $job) {
                       if(in_array($job->JobType->__toString(),$jobTypeArr) && $job->ExecuteStatus->__toString() == '2'){
                            $waitJobsNum+=1;
                       }
                   }
                }

             $this->_db->execute('update cp_service_type set wait_job ='.$waitJobsNum.' where type_id="'.$value['type_id'].'"');  
            }
        }
        $code='0';
        $msg = '操作成功';
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }

    public function updateServiceStatus()
    {	

    	//$soap = new \SoapClient("http://172.28.29.80/MPCWebService/MPCService",array("trace" => 1, "exception" => 0));
    	$soap = $this->_getSoap();
    	$xml = simplexml_load_string($soap->GetSvcList()->GetSvcListResult);
       // print_r($xml);
    	if($xml->Result->Status =="Success"){
            $this->_db->execute('update cp_service_type set busy_instance =0,free_instance=0,current_instance=0');
            $list = $this->_getServerList($xml->ServiceList->MPC_Service);
    		foreach ($list as $key => $value) {
    			//更新服务状态
    			$sql = "select a.service_id,a.type_id from cp_host_extend as h left join cp_service_list as a on a.basic_id=h.basic_id where h.ip='".$value->IPAddr."' ";
                $result=$this->_db->execute($sql)->fetchAll('assoc');
                $service_id =$type_id =0;
                //根据服务详情取得服务类别code
                if(!is_array($result) || count($result) <1){
                    continue;
                }
                $sql = "select service_code from cp_service_type where type_id=".$result[0]['type_id'];
                $r=$this->_db->execute($sql)->fetchAll('assoc');
                foreach ($result as $kk => $serviceInstance) {

                    if($r[0]['service_code'] == $value->ServiceName){
                        $service_id = $serviceInstance['service_id'];
                        $type_id = $serviceInstance['type_id'];
                    }
                }
                if ($service_id ==0)
                {
                    continue;
                }
                
                //echo $service_id;
                $status = "3";
                switch ($value->ServiceStatus) {
                    case '0':
                        $status = "1";
                        $this->_db->execute('update cp_service_type set free_instance =free_instance+1 where type_id="'.$type_id.'"');
                        break;
                    case '8':
                        $status = "2";
                        $this->_db->execute('update cp_service_type set busy_instance =busy_instance+1 where type_id="'.$type_id.'"');
                        break;
                    default:
                        $status = "3";
                        break;
                }
                
                $this->_db->execute('update cp_service_type set current_instance =current_instance+1 where type_id="'.$type_id.'"');
                $this->_db->execute('update cp_service_list set service_status ='.$status.' where service_id="'.$service_id.'"');
    		}
    	}
    	$code='0';
    	$msg = '操作成功';
    	$this->set(compact(array_values($this->_serialize)));
    	$this->set('_serialize',$this->_serialize);
    }

    protected function _getData()
    {
        $data = $this->request->data?$this->request->data:file_get_contents('php://input', 'r');
        //处理非x-form的格式
        if (is_string($data)){
            $data_tmp = json_decode($data,true);
            if (json_last_error() == JSON_ERROR_NONE){
                $data = $data_tmp;
            }
        }
        return $data;
    }
    private function _getServerList($list)
    {   
        $returnList = array();
        foreach ($list as $key => $value) {
            $returnList = $this->_serverIsTrue($value,$returnList);
        }
        return $returnList;
    }

    private function _serverIsTrue($server,$list){
        foreach ($list as $key => $value) {
            if($server->ServiceStatus->__toString() == 2 && $server->ServiceName->__toString() ==$value->ServiceName->__toString() && $server->IPAddr->__toString() == $value->IPAddr->__toString()){
      
                return $list;
            }
            if($server->ServiceStatus->__toString() != 2 && $server->ServiceName->__toString() ==$value->ServiceName->__toString() && $server->IPAddr->__toString() == $value->IPAddr->__toString()){
                $list[$key] = $server;
                return $list;
            }
        }
        $list[]=$server;
        return $list;
    }

    private function _getSoap()
    {
        $opt = array(
            'location' => "http://172.28.29.80/MPCWebService/MPCService",
            'uri'=> "http://tempuri.org/IMPCService/"
            );

        $soap = new \SoapClient('MPCService.wsdl', $opt);
        return $soap;
    }

}
?>