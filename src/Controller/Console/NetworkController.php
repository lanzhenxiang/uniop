<?php
/**
* 计算机网络
*
*
* @author XingShanghe<xingshanghe@gmail.com>
* @date  2015年9月23日下午3:03:33
* @source NetworkController.php
* @version 1.0.0
* @copyright  Copyright 2015 sobey.com
*/


namespace App\Controller\Console;

use App\Controller\Console\ConsoleController;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Network\Http\Client;
use Cake\Error\FatalErrorException;
use PhpParser\Node\Stmt\Switch_;
use Cake\Datasource\ConnectionManager;
use Cake\Test\TestCase\ORM\ProtectedArticle;

class NetworkController extends ConsoleController
{
    public function initialize()
    {
        parent::initialize();
        parent::left('network');//树形图导航

    }
    private $_popedomName = array(
        'hosts' => 'ccm_ps_hosts',
        'disks' => 'ccm_ps_disks',
        'images' => 'ccm_ps_images',
        'router' => 'ccm_ps_routers',
        'subnet' => 'ccm_ps_subnets',
        'elb' => 'ccm_ps_load_banlance',
        'eip' => 'ccm_ps_eip',
        'vpc' => 'ccm_ps_vpc',
        'vpx' => 'ccf_vpx',
        'server' => 'ccm_sm_MPC_Dispatch',
        'EipbHosts' => 'ccf_eip_alloc_hosts',
        'EipbElb'=>'ccf_eip_alloc_banlance',
        'Elblisten'=>'ccf_load_banlance_configure',
        'fics' => 'ccm_ps_fics',
        'settinglist' => 'ccm_ps_fics_settinglist',
        'ficsHosts' => 'ccm_ps_fics_hosts'
    );
    private $_addPopedomName = array(
        'hosts' => 'ccf_host_new',
        'hostDetail' => 'ccm_ps_hosts',
        'router' => 'ccf_router_new',
        'subnet' => 'ccf_subnet_new',
        'elb' => 'ccf_load_banlance_new',
        'eip' => 'ccf_eip_new',
        'fics' =>'ccf_fics_new',
    );
    private function _checkPopedom($param)
    {
        $popedom = parent::checkConsolePopedom($param);
        return $popedom;
    }
	
    /**
     * 显示Web控制台页面
     * @param number $id
     */
    public function webConsole($instanceCode = '')
    {
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        $this->layout = false;
        if ($instanceCode){
            $http = new Client();
            $obj_response = $http->post(Configure::read('URL'),json_encode([
                "method"=>"ecs_webadmin",
                "uid"=>strval($this->request->session()->read('Auth.User.id')),
                "instanceCode"=>$instanceCode,
                "basicId"=>(string)$instance_basic_table->find()->select(['id'])->where(['code'=>$instanceCode])->first()->id
                ]),['type' => 'json']);
            $response = json_decode($obj_response->body,true);
            if ($response['Code'] == 0){
                // 有vncURL 直接跳转
                if (isset($response['vncURL']))
                {
                    Header("HTTP/1.1 303 See Other");
                    Header("Location: ".$response['vncURL']);
                    exit;
                }
                //替换
                $response['Data']['userName']=str_replace('\\',"\\\\",$response['Data']['userName']);
                $response['Data']['url'] = urldecode($response['Data']['url']);
                $response['Data']['url'] = str_replace("wss://", "",$response['Data']['url']);
                $response['Data']['url'] = str_replace("ws://", "",$response['Data']['url']);

                //获取主机的子网虚拟技术信息
                $options = ['ecs_code'=>$instanceCode];
                $instance = TableRegistry::get('InstanceBasic')->find("FusionType",$options)->select(["id"=>'InstanceBasic.id'])->first();
                
                //判断子网虚拟技术是否采用的阿里云
                if($instance["fusionType"] == Configure::read("virtual_tech.aliyun"))
                {
                    //查询密码
                    $password = TableRegistry::get('HostExtend')->find()->select('vnc_password')->where(array("basic_id"=>$instance["id"]))->first();
                    $this->autoRender = false;
                    if($password['vnc_password'] == "")
                    {
                         throw new FatalErrorException("webconsole没有初始化");exit;
                    }
                    $this->set('data',array(
                                // 'url'=>str_replace("wss://", "",$response['Data']['url']),
                                'url'=>$response['Data']['url'],
                                'password'=>$password['vnc_password']
                        ));
                   return $this->render("aliyun_console");
                }
                //区分阿里以及vmware的
                if(isset($response['Data']['url'])&&!empty($response['Data']['url'])){
                    $this->autoRender = false;
                    header('Location:'.$response['Data']['url']);exit();
                }else{
                    $this->set('ticketPieces',$response['Data']);
                }
            }else{
                throw new FatalErrorException($response['Message']);
            }

        }else{
            //不存在该code
            throw new NotFoundException();
        }
    }

    private function _check_popedomlist($type){
        $subject_array = ['hosts','disks','images','router','subnet','elb','eip','vpc','vpx'];
        $check_vale = '';
        foreach ($subject_array as $key => $value) {
            if($type == 'list'){
                $popedomName = $this->_popedomName[$value];
            }
            if (! empty($popedomName)) {
                $check = $this->_checkPopedom($popedomName);
                if($check) {
                    $check_vale = $value;
                    break;
                }
            }
        }
        return $check_vale;
    }

    /**
     * 网络实例显示
     * @param string $subject 主题
     * @param string $category 分类
     * @param number $tab 标签
     * @throws MissingTemplateException
     * @throws NotFoundException
     * @return Ambigous <void, \Cake\Network\Response>
     */
    public function lists($subject = 'hosts')
    {
        if (! empty($this->_popedomName[$subject])) {
            $checkPopedomlist = $this->_checkPopedom($this->_popedomName[$subject]);
            if (! $checkPopedomlist) {
                $subject = $this->_check_popedomlist('list');
            }
        }else{
            $subject = '123';
        }

        if(empty($subject)){
            return $this->redirect('/console/');
        }
        $this->autoRender = false;
        $agent = TableRegistry::get('Agent');
        $agents = $agent->find('all')->where(['parentid'=>0])->toArray();
        $popedomname = $this->request->session()->read('Auth.User.popedomname') ? $this->request->session()->read('Auth.User.popedomname'):0;
        $this->set('agent',$agents);
        $this->set('popedomname',$popedomname);
        try {
            $func_name = '_get_vars_'.$subject;
            //判断是否存在函数
            if (method_exists($this,$func_name)){
                $this->set('_view_vars',call_user_func_array([$this,$func_name],[
                    'options'   =>  [
                    'page'  =>  1,
                    'limit' =>  10
                    ],
                ]));
            }
            if((strtolower($subject)=='eipbhosts')||(strtolower($subject)=='eipbelb')||(strtolower($subject)=='elbhosts')){
                if ($this->request->is('get')){
                    $request_data = $this->request->query;
                    $agent = TableRegistry::get('InstanceBasic');
                    $enttity = $agent->get($request_data['e']);
                    $this->set('_EipId',$enttity);
                    if($request_data['department_id']!=""){
                        $this->set('department_id',$request_data['department_id']);
                    }
                }
            } else if(strtolower($subject)=='elblisten'){
                if ($this->request->is('get')){
                    $request_data = $this->request->query;
                    $agent = TableRegistry::get('InstanceBasic');
                    $enttity = $agent->get($request_data['e']);
                    if(!empty($enttity->subnet)){

                        $subnet= $agent->find('all')->where(['code'=>$enttity->subnet])->first();
                        $vpc = $agent->find('all')->where(['code'=>$enttity->vpc])->first();
                        $this->set('_Vpc',$vpc);
                        $this->set('_Subnet',$subnet);
                        $table = TableRegistry::get('ElbListen');
                        $listen = $table->find("all")->where(['elb_id'=>$enttity->id,'vpc_id'=>$vpc->id])->order(['create_time' => 'DESC'])->toArray();

                        $this->set('_listens',$listen);

                    }
                    $this->set('_Lib',$enttity);
                }
            }else if(strtolower(($subject)=='server')){ //如果是服务管理
                // var_dump($this->request->query);exit;
                 if ($this->request->is('get')){
                    $request_data = $this->request->query;
                    $type_table = TableRegistry::get('ServiceType');
                    $enttity = $type_table->get($request_data['t']);
                    // var_dump($enttity);exit;
                     //actor 状态统计
                     if($request_data['t'] == 2 || $request_data['t'] == 3 || $request_data['t'] == 5 ||$request_data['t'] == 7){
                         $sql = "SELECT service_status, count(cp_service_list.service_id) as `count` FROM cp_service_list LEFT JOIN cp_service_type ON cp_service_list.type_id = cp_service_type.type_id WHERE cp_service_list.type_id = ".$request_data['t']." and cp_service_type.department_id=".$this->request->session()->read('Auth.User.department_id')." GROUP BY service_status";
                       /*  $sql = "SELECT service_status,count(service_id) as count FROM cp_service_list where type_id=".$request_data['t']." GROUP BY service_status";*/
                         $connection = ConnectionManager::get('default');
                         $data = $connection->execute($sql)->fetchAll('assoc');
                         $datajson = "[";
                         $array = array();
                         $array['total']=0;
                         foreach ($data as $v) {
                             if($v['service_status'] !== null) {
                                 if ($v['service_status'] === 0) {
                                     $count = $v['count'];
                                     $datajson .= "{value:$count,color:'#d5ccc5'},";
                                 } elseif ($v['service_status'] == 1) {
                                     $count = $v['count'];
                                     $datajson .= "{value:$count,color:'#949fb1'},";
                                 } elseif ($v['service_status'] == 2) {
                                     $count = $v['count'];
                                     $datajson .= "{value:$count,color:'#4c5260'},";
                                 } elseif ($v['service_status'] == 3) {
                                     $count = $v['count'];
                                     $datajson .= "{value:$count,color:'#f64649'},";
                                 }
                                 $array['total'] += $v['count'];
                                 $array[$v['service_status']] = $v['count'];
                             }
                         }
                         $datajson = substr($datajson,0,strlen($datajson)-1);
                         if(strlen($datajson) ==0){
                             $datajson .='';
                         }else{
                             $datajson .= "]";
                         }

                         $this->set('actorarray',$array);
                         $this->set('actor',$datajson);

                     }
                     //任务数统计
                     if($request_data['t'] == 2 || $request_data['t'] == 3 || $request_data['t'] == 5 ||$request_data['t']){
                         $taskdata = $type_table->find()->select(['wait_job','exec_job'])->where(['department_id'=>$this->request->session()->read('Auth.User.department_id'),'type_id'=>$request_data['t']])->first();
                         if(!empty($taskdata)){
                             $taskdata = $taskdata->toArray();
                             $taskjson = "[";
                             $arraytask = array();
                             $arraytask['total']=$taskdata['wait_job']+$taskdata['exec_job'];
                             foreach ($taskdata as $k=>$vt) {
                                 if($k=='wait_job'){
                                     $taskjson .="{value:$vt,color:'#f64649'},";
                                 }elseif($k=='exec_job'){
                                     $taskjson .="{value:$vt,color:'#d5ccc5'},";
                                 }
                                 $arraytask[$k]=$vt;
                             }
                             $taskjson = substr($taskjson,0,strlen($taskjson)-1);
                             $taskjson .= "]";
                             $this->set('taskarray',$arraytask);
                             $this->set('taskdata',$taskjson);
                         }else{
                             $this->set('taskarray','');
                             $this->set('taskdata','');
                         }

                     }
                    if(!empty($enttity)){
                        $this->set('_Type',$enttity);
                    }
                 }
            }else if(strtolower(($subject)=='images')){ //如果是镜像管理
                $this->set('data', json_encode($agents));
            }
            else if(strtolower(($subject)=='settinglist'))
            {
                $request_data = $this->request->query;
                $fics_id = $request_data["f"];
                $this->set('_id',$fics_id);
                $template = TableRegistry::get('FicsVolAcces_template');
                $template=$template->find()->find("all")->where(array('vol_id'=>$fics_id))->order(array('acces_limit' => 'DESC'))->toArray();
                $template = TableRegistry::get('FicsExtend');
                $template=$template->find("all")->where(array('vol_id'=>$fics_id))->first();
                $this->set('_template',$template);
                $region=TableRegistry::get('Agent');
                $region = $region->find("all")->where(array('region_code'=>$template->region_code))->first();
                //." 品牌： ".template->store_type."存储卷：".template->vol_name
                $display_note = "部署区位: ".$region->display_name."&nbsp;&nbsp;&nbsp;&nbsp;品牌: ".$template->vol_type."&nbsp;&nbsp;&nbsp;&nbsp;存储卷：".$template->vol_name;
                $this->set('_display_note',$display_note);
            }else if (strtolower($subject)=='fics') {
                # code...
                $_store_Typs = Configure::read('SotreType');
                $this->set('_store_types',$_store_Typs);
            }else if (strtolower($subject) =='ficshosts'){
                $ficshosts_data = $this->request->query;
                $vol_id = 0;
                if(isset($ficshosts_data['vol_id']) && !empty($ficshosts_data['vol_id'])){
                    $vol_id = $ficshosts_data['vol_id'];
                }
                $this->set('vol_id',$vol_id);

                $fics_extend_table = TableRegistry::get('FicsExtend');
                $agent_table = TableRegistry::get('Agent');
                $store_table = TableRegistry::get('Store');
                $fics_extend_data = $fics_extend_table->find()->contain(['FicsRelationDevice'])->where(['FicsExtend.vol_id'=>$vol_id])->first();
                $data['display_name'] = "";
                $store_data = '';
                if(isset($fics_extend_data['region_code']) && !empty($fics_extend_data['region_code'])){
                    $agent_data = $agent_table->find()->where(['region_code'=>$fics_extend_data['region_code']])->first();
                    if(isset($agent_data['display_name']) && !empty($agent_data['display_name'])){
                        $fics_extend_data['display_name'] = $agent_data['display_name'];
                    }


                    $store_data = $store_table->find()
                        ->where([
                            // 'department_id'=>$fics_extend_data['department_id'],
                            // 'region_code'=>$fics_extend_data['region_code'],
                            'store_code'=>$fics_extend_data['store_code']
                            ])->first();
                        
                }
                $this->set('store_data',$store_data);
                $this->set('data',$fics_extend_data);

            }
            $account_table = TableRegistry::get('Accounts');
            // $user = $account_table->find()->select('department_id')->where(array('id' => $this->request->session()->read('Auth.User.id')))->first();
            $department_id = $this->getOwnByDepartmentId();
            $deparments = TableRegistry::get('Departments');
            $this->set('_default',$deparments->get($department_id));
            $table = $deparments->find('all');
            $this->set('_deparments', $table);
            $this->render('lists/'.$subject );
        } catch (MissingTemplateException $e) {
            if (Configure::read('debug')) {
                throw $e;
            }
            throw new NotFoundException();
        }
    }

    public function data($subject = 'hosts',$id=0,$type='hosts')
    {

        $this->autoRender = false;
         if($type=='desktop'){
             parent::left('desktop');
      }else{
            parent::left('hosts');
         }
        try {
            $func_name = '_get_datas_'.$subject;
            // debug($id);
            //判断是否存在函数
            if (method_exists($this,$func_name)){

                $rs = call_user_func_array([$this,$func_name],[$id]);
                $this->set('_data',$rs);
            }
            
            if(!empty($rs[0]['E_Id'])){
                $agent_id = $rs[0]['E_Id'];
                $desc = call_user_func_array([$this,'_get_hosts_desc'],[$agent_id]);
                $this->set('_desc',$desc);
            }
            if(!empty($rs[0]['H_Code'])){
               $code = $rs[0]['H_Code'];
               $disks = call_user_func_array([$this,'_get_hosts_disks'],[$code]);
               $this->set('_disks',$disks);
            }
            $log = call_user_func_array([$this,'_get_vars_log'],[$id]);
            $this->set('_log',$log);
            $this->set('type',$type);
            $this->render('data/' . $subject);
        } catch (MissingTemplateException $e) {
            if (Configure::read('debug')) {
                throw $e;
            }
            throw new NotFoundException();
        } 
    }

    protected function _get_datas_mirror($id){
        $department_name = $this->request->session()->read('Auth.User.department_name');
        $this->set('user_name',$department_name);
        return $this->_retrieveHostData($id);
    }

    /**
     * [_get_datas_basic_info 主机详情获取主机基础信息]
     * @author lanzhenxiang
     * @param  [int] $id    [主机实例id]
     * @return [array]      [主机信息]
     */
    protected function _get_datas_basic_info($id){
        $field = [
                'D_Vnc_password'=>'host.vnc_password',
                'Sub_basic_id'=>'sub_b.id',
                'Host_extend_name'=>'host.name',
                'E_Name'        =>'agent.display_name',
                'Host_extend_plat_form'=>'host.plat_form',
                'Host_extend_connect_status'=>'host.connect_status',
            ];
        $dataList =  $this->_retrieveHostData($id,$field);
        $dataList[0]['fusionType'] = $this->_get_hosts_fusionType($dataList[0]['H_Code']);
        $subnetTable = TableRegistry::get('SubnetExtend');
        $subnetInfo  = $subnetTable->find()->where(['basic_id'=>$dataList[0]['Sub_basic_id']])->select(['aduser','adpwd'])->first();
        $dataList[0]['desktop_server_url'] = $this->_getDesktopServerUrlByVpcCode($dataList[0]['H_VPC']);
        $dataList[0]['aduser'] = $subnetInfo['aduser'];
        $dataList[0]['adpwd']  = $subnetInfo['adpwd'];
        return $dataList;
    }

    /**
     * 获取当前vpc的desktop_server_url
     * @param  string $vpc_code vpcCode
     * @return string           
     */
    protected function _getDesktopServerUrlByVpcCode($vpc_code)
    {
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        $entity = $instance_basic_table->find()->select(['url'=>'vpc_extend.desktop_server_url'])->join([
                'vpc_extend'=>[
                        'table'=>'cp_vpc_extend',
                        'type' => 'LEFT',
                        'conditions'=>'InstanceBasic.id = vpc_extend.basic_id'
                    ]
            ])->where(['InstanceBasic.code'=>$vpc_code])->first();

        if($entity != null && !empty($entity)){
            return $entity->url;
        }
        return '';
    }

    /**
     * [_get_hosts_fusionType 获取主机的fusiontype]
     * @param  [string] $code [子网code]
     * @return [string]       [fusiontype]
     */
    protected function _get_hosts_fusionType($code){
        $sql  ="";
        $sql .= 'SELECT GROUP_CONCAT(s.fusionType) as fusionType';
        $sql .= ' FROM cp_subnet_extend s ';
        $sql .= ' LEFT JOIN cp_instance_basic as ns on s.basic_id = ns.id';
        $sql .= ' LEFT JOIN cp_hosts_network_card as nc on ns.code = nc.subnet_code';
        $sql .= ' WHERE nc.hosts_code = "'.$code.'" and nc.is_default = 1';

        $connection = ConnectionManager::get('default');
        $data = $connection->execute($sql)->fetchAll('assoc');
        return $data[0]['fusionType'];
    }
    /**
     * [_get_datas_system_layout 主机详情获取系统配置信息]
     * @author lanzhenxiang
     * @param  [int] $id    [主机实例id]
     * @return [array]      [系统配置信息]
     */
    protected function _get_datas_system_layout($id){
        $field = [
                'D_Cpu'=>'host.cpu',
                'D_Memory'=>'host.memory',
                'D_Gpu'=>'host.gpu',
                'E_Id'=>'agent.id'
            ];
        return $this->_retrieveHostData($id,$field);
    }
    /**
     * [网卡信息]
     */
    protected function _get_datas_network_card($id){
        $field = [
            'I_Ip'=>'network.ip',
            'I_NetCode'=>'network.network_code',
            'I_SubnetCode'=>'network.subnet_code',
            'J_SubnetName'=>'sub_b.name',
            'I_Default'=>'network.is_default',
            'I_NetCardId' =>'network.id',
            'F_Code'=>'vpc_b.code',
            'H_Code'=>'instance_basic.code'
        ];
        return $this->_retrieveHostData($id,$field,'subnet');
    }
    /**
     * [块存储]
     */
    protected function _get_datas_storage($id){
        $field = [
            'F_Code'=>'vpc_b.code',
            'H_Code'=>'instance_basic.code',
            'H_L_Code'=>'instance_basic.location_code',
            'D_isFusion'=>'host.isFusion'
        ];
        return $this->_retrieveHostData($id,$field);
    }
    /**
     * [图形化]
     */
    protected function _get_datas_imaging($id){
        $field = [
                'G_Name'=>'sub_b.name',
                'H_Code'=>'instance_basic.code',
                'D_Image_code'=>'host.image_code',
                'F_Code'=>'vpc_b.code',
                'H_L_Code'=>'instance_basic.location_code',
                'D_isFusion'=>'host.isFusion'
            ];
        return $this->_retrieveHostData($id,$field);
    }
    /**
     * 操作记录
     */
    protected function _get_datas_action_record($id){
        return $this->_retrieveHostData($id);
    }

    /**
     * [监控信息]
     */
    protected function _get_datas_monitor($id){
        return $this->_retrieveHostData($id);
    }
    /**
     * [正常日志]
     */
    protected function _get_datas_normal_log($id){
        $this->set('task_data',$this->_getTask($id));
        return $this->_retrieveHostData($id);
    }
    /**
     * [异常日志]
     */
    protected function _get_datas_abnormal_log($id){
        $this->set('task_data',$this->_getTask($id,'excp'));
        return $this->_retrieveHostData($id);
    }
    /**
     * [执行中日志]
     */
    protected function _get_datas_executing_log($id){
        $this->set('task_data',$this->_getTask($id,'executing'));
        return $this->_retrieveHostData($id);
    }
    protected function _get_datas_snap($id){
        $field = [
            'class_code'        =>'agent.class_code',
        ];
        $hosts =  $this->_retrieveHostData($id,$field);
        $instance_basic = TableRegistry::get("InstanceBasic");
        $fusiontype = $instance_basic->find("FusionType",['basic_id'=>$id])->first();
        $this->set('fusiontype',$fusiontype);
        return $hosts;
    }

    /**
     * [getTask 获取日志信息]
     * @param  [type]  $id     [主机basic_id]
     * @param  string  $type   [normal | excp]
     * @param  integer $limit  [单页数量]
     * @param  integer $offset [页码]
     * @return [array]         [结果集]
     */
    protected function _getTask($id,$type='normal',$limit=10,$offset =0){
        if ($offset>0) {
            $offset = $offset-1;
        }
        $offset =  $offset*$limit;
        $options['id'] = $id;
        $options['type'] = $type;
        $task = TableRegistry::get('task');
        return $task->find('TaskLog',$options)->contain(['InstanceBasic'])->order(['task.create_time' => 'desc'])->limit($limit)->offset($offset)->toArray();
    }
    /**
     * [getTask ajax分页获取日志列表]
     * @param  [type]  $id     [主机basic_id]
     * @param  string  $type   [normal | excp]
     * @param  integer $limit  [单页数量]
     * @param  integer $offset [页码]
     * @return [array]         [结果集]
     */
    public function getTask($id,$type='normal',$limit=10,$offset =0){
        $task_data = $this->_getTask($id,$type,$limit,$offset);
        echo json_encode($task_data);exit;
    }

    /**
     * [_retrieveHostData 获取主机详情信息通用方法]
     * @param  [int] $id        [主机实例id]
     * @param  array  $field    [字段别名信息]
     * @return [array]          [主机信息结果集]
     */
    protected function _retrieveHostData($id,array $field =[],$type ='hosts'){
        $instance_basic = TableRegistry::get('instance_basic');
        if(!$this->checkConsolePopedom('ccf_all_select_department')){
            $department_id = $this->request->session()->read('Auth.User.department_id');
            $entity = $instance_basic->find()->where(['id'=>$id,'department_id'=>$department_id])->first();
            if($entity == null){
                throw new \Exception("此设备不属于当前租户", 1);
            }
        }
        return $instance_basic->getHostBasicInfoByID($id,$field,$type);
    }

    /**
    * 函数用途描述
    * @date: 2016年3月17日 上午11:10:49
    * @author: wangjc
    * @param: variable
    * @return:
    */
    public function statics($subject = 'hosts',$code=''){
        $this->layout = 'special';
        $this->set('code',$code);
        $this->render('statics/' . $subject);
    }

    public function add($subject = 'hosts')
    {

        if (! empty($this->_addPopedomName[$subject])) {
            $checkPopedomlist = $this->_checkPopedom($this->_addPopedomName[$subject]);
            if (! $checkPopedomlist) {
                return $this->redirect('/console/');
            }
        }else{
            $subject = '';
        }
        if(empty($subject)){
            return $this->redirect('/console/');
        }
        $this->autoRender = false;
        try {

            $goods_fixed = parent::readGoodsList($subject);
            $goods_table = TableRegistry::get('Goods');
            $Systemsetting_table = TableRegistry::get('Systemsetting');
            $goods = $goods_table->find()
                ->where([
                'fixed' => $goods_fixed
            ])
                ->first();
            if (! empty($goods)) {
                $this->set('goods_id', $goods->id);
            }
            $popedomname = $this->request->session()->read('Auth.User.popedomname') ? $this->request->session()->read('Auth.User.popedomname') : 0;
            $this->set('popedomname', $popedomname);
            if((strtolower($subject)=='elb')){
                $elb_imageCode = $Systemsetting_table->find()->select(['para_value'])->where(['para_code'=>'lbs_imageCode'])->first()->para_value;
                $this->set('imageCode',$elb_imageCode);
                $elb_instanceTypeCode = $Systemsetting_table->find()->select(['para_value'])->where(['para_code'=>'lbs_instanceTypeCode'])->first()->para_value;
                $this->set('instanceTypeCode',$elb_instanceTypeCode);
            }
            $data_charge = parent::_GetBillCycle(null);
            // $data_agent["chargeList"]=$data_charge;
            $this->set('deparment_type', $this->request->session()->read('Auth.User.department_type'));
            $this->set('chargeList',$data_charge);
            $this->render('add/' . $subject);
        } catch (MissingTemplateException $e) {
            if (Configure::read('debug')) {
                throw $e;
            }
            throw new NotFoundException();
        }
    }


    protected function _get_datas_hosts($id=[]){
        $connection = ConnectionManager::get('default');
        $where = '';
        $where .=" AND a.id = ".$id;
        $sql = " SELECT ";
        $sql .= " a.id AS 'H_ID', ";
        $sql .= " a.`code` as 'H_Code', ";
        $sql .= " a.name as 'H_Name', ";
        $sql .= " a.location_code as 'H_L_Code', ";
        $sql .= " a.status as 'H_Status', ";
        $sql .= " a.description 'H_Description', ";
        $sql .= " a.create_time as 'H_time', ";
        $sql .= " c.`bindcode` AS 'E_BindCode', ";
        $sql .= " c.ip as 'E_Ip', ";
        $sql .= " c.bandwidth as 'E_BandWidth', ";
        $sql .= " d.`type` as 'D_Code', ";
        $sql .= " d.os_family AS 'D_Os_Form', ";
        $sql .= " d.plat_form AS 'D_Plat_form', ";
        $sql .= " d.image_code as 'D_Image_code', ";
        $sql .= " d.isFusion as 'D_isFusion', ";
        $sql .= " d.ip as 'D_Ip', ";
        $sql .= " d.cpu as 'D_Cpu', ";
        $sql .= " d.memory as 'D_Memory', ";
        $sql .= " d.gpu as 'D_Gpu', ";
        $sql .= " e.id as 'E_Id', ";
        $sql .= " e.display_name as 'E_Name', ";
        $sql .= " e.agent_name as 'E_A_Name', ";
        $sql .= " f.`name` as 'F_Name', ";
        $sql .= " f.`code` as 'F_Code', ";
        $sql .= " g.`name` as 'G_Name', ";
        $sql .= " g.`code` as 'G_Code', ";
        $sql .= " h.`name` as 'R_Name', ";
        $sql .= " h.`code` as 'R_Code', ";
        $sql .= " i.ip AS 'I_Ip', ";
        $sql .= " i.subnet_code AS 'I_SubnetCode', ";
        $sql .= " i.network_code AS 'I_NetCode', ";
        $sql .= " j.`name` AS 'J_SubnetName', ";
        $sql .= " i.id AS 'I_NetCardId', ";
        $sql .= " i.is_default AS 'I_Default' ";
        $sql .= " FROM ";
        $sql .= " `cp_instance_basic` as a ";
        $sql .= " LEFT JOIN `cp_eip_extend` as c ON a.`code` = c.bindcode ";
        $sql .= " LEFT JOIN `cp_instance_basic` AS b ON c.basic_id = b.id ";
        $sql .= " LEFT JOIN `cp_host_extend` as d ON d.basic_id=a.id ";
        $sql .= " LEFT JOIN cp_agent as e ON e.class_code=a.location_code ";
        $sql .= " LEFT JOIN cp_instance_basic as f ON f.`code`=a.vpc ";
        $sql .= " LEFT JOIN cp_instance_basic as g ON g.`code`=a.subnet ";
        $sql .= " LEFT JOIN cp_instance_basic as h ON h.`code`=a.router ";
        $sql .= " LEFT JOIN cp_hosts_network_card AS i on i.basic_id = a.id ";
        $sql .= " LEFT JOIN cp_instance_basic AS j ON j.`code` = i.subnet_code ";
        $sql .= " WHERE ";
        $sql .= " a.type = 'hosts' ".$where;
        // $sql_row =$sql." limit ".$offset.",".$limit;
        $query = $connection->execute($sql)->fetchAll('assoc');
        // $this->_pageList['total'] =$connection->execute($sql)->count();
        // $this->_pageList['rows'] =$query;
        return $query;
    }

    protected function _get_hosts_desc($agent_id){
        $connection = ConnectionManager::get('default');
        $sql = ' SELECT cpu_number,memory_gb,gpu_gb,set_code from cp_set_hardware as a ';
        $sql .= ' LEFT JOIN cp_agent_set as b ON b.set_id=a.set_id ';
        $sql .= ' WHERE b.agent_id='. $agent_id ;
        $query = $connection->execute($sql)->fetchAll('assoc');
        return $query;
    }

    protected function _get_vars_log($id)
    {
        $connection = ConnectionManager::get('default');
        $sql = ' SELECT * FROM cp_instance_logs WHERE basic_id = '.$id.' ORDER BY create_time DESC LIMIT 10 ';
        $query = $connection->execute($sql)->fetchAll('assoc');
        return $query;
    }

    protected function _get_hosts_disks($code){
                $connection = ConnectionManager::get('default');
        $sql = ' SELECT * from cp_disks_metadata';
        $sql .= " WHERE cp_disks_metadata.attachhostid='$code'";
        $query = $connection->execute($sql)->fetchAll('assoc');
        return $query;
    }

    /**
     * 获取主机列表
     *
     * @param array $options 参数列表，主要为分页。过滤参数
     */
    protected function _get_vars_hosts1(array $id=[])
    {

        $instance_basic = TableRegistry::get('InstanceBasic');

        $where = [
        'InstanceBasic.type'  =>  'hosts'
        ];

        return  ['list'=>$instance_basic->find()->where($where)->toArray()];
    }
    
    /**
     * 获取主机列表
     *
     * @param array $options 参数列表，主要为分页。过滤参数
     */
    protected function _get_vars_hosts(array $id=[])
    {
        $depart_id = $this->request->session()->read('Auth.User.department_id');
        $data = $this->_getDepartVpcByDepartID($depart_id);
        $this->set('vpcData', $data);
    }
    
    public function getDepartVpcByDepartID($depart_id)
    {
        $data = $this->_getDepartVpcByDepartID($depart_id);
        echo json_encode($data);die;
    }
    
    /**
     * 获取租户对应的vpc
     * 
     * @param string $depart_mentid
     */
    protected function _getDepartVpcByDepartID($depart_id)
    {
        $instance_basic = TableRegistry::get('InstanceBasic');
        $data = $instance_basic->find()->select(['code', 'name', 'id'])->where(['department_id' => $depart_id, 'type' => 'vpc', 'code <>' => '', 'status' => '运行中'])->toArray();
        return $data;
    }


    /**
     * 获取硬盘列表
     *
     * @param array $options 参数列表，主要为分页。过滤参数
     */
    protected function _get_vars_disks(array $options = [])
    {
        $instance_basic = TableRegistry::get('InstanceBasic');

        $where = [
        'type'  =>  'disks'
        ];

        return  ['list'=>$instance_basic->find()->contain(['DisksMetadata'])->where($where)->toArray()];
    }

    /**
     * 获取查询之后的列表
     * @param $code 地域code
     * @param string $type 类型：disks,host,router
     */
    public function disks($code,$type='disks'){
        $instance_basic = TableRegistry::get('InstanceBasic');

        $where =[];
        if($code){
            $where["location_code like"]='%'.$code.'%';
        }
        $where['type'] = $type;
        $re = $instance_basic->find()->where($where)->toArray();

        echo json_encode($re);exit;
    }

    /**
     * 获取路由列表
     *
     * @param array $options 参数列表，主要为分页。过滤参数
     */
    protected function _get_vars_router(array $options = [])
    {
        $instance_basic = TableRegistry::get('InstanceBasic');

        $where = [
        'InstanceBasic.type'  =>  'router'
        ];

        return  ['list'=>$instance_basic->find()->contain(['RouterExtend'])->where($where)->toArray()];

    }

    /**
     * 获取云桌面列表
     *
     * @param array $options 参数列表，主要为分页。过滤参数
     */
    protected function _get_vars_desktop(array $options = [])
    {
        $instance_basic = TableRegistry::get('InstanceBasic');

        $where = [
        'InstanceBasic.type'  =>  'desktop'
        ];

        return  ['list'=>$instance_basic->find()->where($where)->toArray()];

    }

    /**
     * 获取路由列表
     *
     * @param array $options 参数列表，主要为分页。过滤参数
     */
    protected function _get_vars_subnet(array $options = [])
    {
        $instance_basic = TableRegistry::get('InstanceBasic');

        $where = [
        'InstanceBasic.type'  =>  'subnet'
        ];
        return  ['list'=>$instance_basic->find()->where($where)->toArray()];
    }
}