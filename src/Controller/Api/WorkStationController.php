<?php

/**
 * class
 *
 * @author wangjincheng@sobey.com
 * @date
 * @source WorkStationController.php
 * @version 1.0.0
 * @copyright  Copyright 2015 sobey.com
 */
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use App\Controller\OrdersController;
use Cake\Network\Http\Client;
use Cake\Core\Configure;
use Composer\Autoload\ClassLoader;
use \Requests as Requests;
use Cake\Log\Log;

//TODO  交付生产环境时候此类应该继承AppController类，保证接口权限验证
class WorkStationController extends AppController
{
    //接口属性
    public $_db;
    private $_data      = null;
    private $_code      = 0;
    private $_msg       = "";
    private $_serialize = array("code", "msg", "data");

    //数据库链接
    private $_db_conn = null;

    public function initialize()
    {
        parent::initialize();
        $this->_db       = ConnectionManager::get('default');
        $this->viewClass = 'Json';

        $this->loadComponent('RequestHandler');

        $loader = new ClassLoader();
        $loader->add('Requests', ROOT . DS . 'vendor/requests');
        $loader->register();
        Requests::register_autoloader();
        // 获取参数
        $this->_data = $this->_getData();
    }

    /**
     * 获取Token
     *
     * @author wangjincheng
     * @return json
     */
    public function getToken()
    {
        $this->_serialize = ['code', 'msg', 'token', 'requestId'];
        $token='';
        //数据校验
        $lack_fields = [];
        $_needed_fileds = [
            'loginname', 'password'
        ];
        if($_needed_fileds){
            foreach ($_needed_fileds as $_key) {
                if (!isset($this->_data[$_key])) {
                    $lack_fields[] = $_key;
                }
            }
        }
        if(!isset($this->_data['requestId'])){
            $this->_data['requestId'] = "";
        }
        if (empty($lack_fields)) {
            $accounts_table = TableRegistry::get('Accounts');
            $token_table = TableRegistry::get('Token');
            $requestId = $this->_data['requestId'];
            //获取客户信息
            $accounts = $accounts_table->find()->where([
                'loginname' => $this->_data['loginname']
            ])->first();

            if ($accounts){
                //校验密码
                if ($this->_chkPwd($this->_data['password'], $accounts['salt'], $accounts['password'])){
                    $time = time();

                    #TODO 获取用户权限
                    $response = Requests::post(Configure::read('Api.cmop') . '/Popedomlist/getUserPopedomInfo', [], [
                        'userid' => $accounts['id'],
                    ], [
                        'verify' => false,
                    ]);
                    $response_arr = json_decode(trim($response->body,chr(239).chr(187).chr(191)), true);
                    if ($response_arr['code'] == 0) {
                        if (isset($response_arr['data'])) {
                            $popedomname = $response_arr['data'];
                        }
                    }
                    //检验权限
                    if(!empty($popedomname) && is_array($popedomname)){
                        if(in_array('api_mpaas', $popedomname)){
                            $loginname = $this->_data['loginname'];
                            $password = $this->_data['password'];

                            // 根据时间和所传参数生产唯一码
                            $data = $time . $loginname . $password;
                            $token = md5(md5($data) + $time);

                            $token_data = $token_table->newEntity();
                            $token_data->token = $token;
                            $token_data->uid = $accounts['id'];
                            $token_data->loginname = $loginname;
                            $token_data->create_time = $time;
                            try {
                                $token_table->save($token_data);
                                $code = '0';
                                $msg = 'Success';
                            } catch (\Exception $e) {
                                // TODO 数据库操作失败
                                $code = '0001';
                                $msg = 'Error';
                                $token='';
                            }
                        }else{
                            $code = '3005';
                            $msg = 'You don`t have permission';
                        }
                    }
                } else {
                    $code = '3002';
                    $msg = 'Password error';
                }
            }else{
                $code = '3001';
                $msg = 'Loginname error';
            }
        }else{
            $code ='3000';
            $msg = 'Missing field';
        }
        //记录操作
        $this->_setLog('getToken');

        $this->set(compact(array_values($this->_serialize)));
        $this->set("_serialize", $this->_serialize);
    }

    /**
     * 获取用户权限
     */
    public function AddWorkStation()
    {
        $this->_serialize = ['code', 'msg', 'taskId', 'requestId'];
        $msg = 'Success';
        $data = '';
        $taskId = '';
        $requestId = '';
        //记录操作
        $this->_setLog('AddWorkStation');
        //数据校验
        $lack_fields = [];
        $_needed_fileds = [
            "vendorCode","imageCode","instanceTypeCode","token"
        ];
        if($_needed_fileds){
            foreach ($_needed_fileds as $_key) {
                if (!isset($this->_data[$_key])) {
                    $lack_fields[] = $_key;
                }
            }
        }
        // debug($this->_data);die;
        if(!isset($this->_data['requestId'])){
            $this->_data['requestId'] = "";
        }
        if(!isset($this->_data['callbackUrl'])){
            $this->_data['callbackUrl'] = "";
        }
        if (empty($lack_fields)) {
            $code    = "0";
            $taskId  = $this->getGUID();

            $request = $this->_data;

            $requestId = $request['requestId'];
            //检查token
            $token_table = TableRegistry::get('Token');
            $token_data = $token_table->find()->where(['token'=>$request['token']])->first();
            $this->_checkToken($this->_data['token']);
            //检查配额
            $set_hardware_table = TableRegistry::get('SetHardware');
            $set_hardware_data = $set_hardware_table->find()->where(['set_code'=>$request['instanceTypeCode']])->first();
            if (empty($set_hardware_data)) {
                $code = '3004';
                $msg  = "InstanceTypeCode does not exist";
            }
            $this->checkQuota($token_data['uid'],$set_hardware_data);
            //获取系统配置参数
            $table  = TableRegistry::get('Systemsetting');
            $pro_field = strtolower($request['vendorCode']);
            $datas   = $table->find('all')->select(['para_code', 'para_value'])->where(array('para_code like' => $pro_field.'_work_%'))->toArray();
            $params = array();
            $params['subnetCode2'] = '';
            if (!empty($datas) && count($datas) >= 4) {
                //创建接口参数
                foreach ($datas as $key => $value) {
                    switch ($value['para_code']) {
                        case $pro_field.'_work_vpc':
                            $params["vpcCode"] = $value['para_value'];
                            break;
                        case $pro_field.'_work_subnet':
                            $params["subnetCode"] = $value['para_value'];
                            break;
                        case $pro_field.'_work_username':
                            $params["userName"] = $value['para_value'];
                            break;
                        case $pro_field.'_work_pwd':
                            $params["pwd"] = $value['para_value'];
                            break;
                    }
                    if (strstr($value['para_code'], $pro_field.'_work_subnet_')){
                        if (!empty($params['subnetCode2'])) {
                            $params['subnetCode2'] .= ',';
                        }
                        $params['subnetCode2'] .= $value['para_value'];
                    } 

                }
                $params["guid"]             = $taskId;
                $params["requestId"]        = $request["requestId"];
                $params['vendorCode']       = $pro_field;
                $params['regionCode']       = $this->_getRegionCodeByVpcCode($params['vpcCode']); #部署区位
                $params["uid"]              = (string)$token_data['uid'];
                $params["method"]           = "ecs_add";
                $params["imageCode"]        = $request["imageCode"];
                $params["instanceTypeCode"] = $request["instanceTypeCode"];
                $params["number"]           = "1";
                $params["ecsName"]          = $request['vendorCode'];
                $params["backurl"]          = $request["callbackUrl"];
                $params["billCycle"]        = "1";
                $order = new OrdersController();
                $result = $order->ajaxFun($params);
                if ($result['Code'] != '0') {
                     $code = $result['Code'];
                     $msg  = $result['Message'];
                }
            } else {
                $code = '3006';
                $msg  = "Database is not configured";
            }

        }else{
            $code ='3000';
            $msg = 'Missing field';
        }
        $this->set(compact(array_values($this->_serialize)));
        $this->set("_serialize", $this->_serialize);
    }

    /**
     * 获取接口中传递过来的参数。
     * 支持form提交以及body提交
     * @return Ambigous <unknown, string, multitype:>
     */
    private function _getData()
    {
        $data = $this->request->data ? $this->request->data : file_get_contents('php://input', 'r');

        //处理非x-form的格式
        if (is_string($data)) {
            $data_tmp = json_decode($data, true);
            if (json_last_error() == JSON_ERROR_NONE) {
                $data = $data_tmp;
            }
        }
        //日志
        //Log::debug("Data Posted :".json_encode($data),['action'=>$this->request->params['action'],'host'=>$this->request->host()]);

        return $data;
    }

    /**
     * 检查密码。
     * @param string $pwd_md5 md5密码。
     * @param string $salt 随机值。
     * @param string $hashed_pwd 加密后密文。
     * @return bool
     */
    private function _chkPwd($pwd_md5,$salt,$hashed_pwd)
    {
        return md5($pwd_md5.$salt) === $hashed_pwd;
    }

    /**
     * 检查Token。
     * @author wangjincheng。
     * @param string $token Token。
     * @return bool
     */
    protected function _checkToken($token)
    {
        $token_table = TableRegistry::get('Token');
        // 产生一个唯一的guid字符串，并存入到表cp_desktop_usertoken

        $code = "0";
        $data = "";
        $msg = "Success";

        //检查token
        $time = time() - 600;#600S内
        $token_data = $token_table->find()
            ->where([
                'token' => $token,
                'create_time >' => $time
            ])
            ->first();
        if (! empty($token_data)) {
            $token_table->deleteAll([
                'id' => $token_data['id']
            ]);
        } else {
            $code = '2001';
            $msg = "Token error or time out";
        }
        //删除过期的token
        $token_table->deleteAll([
                'create_time <' => $time
            ]);
        if(intval($code)>0){
            $result=array(
                'code'=>$code,
                'msg'=>$msg,
                'data'=>$data
            );
            echo json_encode($result);
            exit;
        }
    }

    /**
     * 开关机
     * @author wangjincheng。
     */
    public function OperateWorkStation()
    { 
        $this->_serialize = ['code', 'msg', 'taskId', 'requestId'];
        $code = "0";
        $data = "";
        $taskId = "";
        $requestId = "";
        $msg = "Success";
        //记录操作
        $this->_setLog('OperateWorkStation');
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        $hosts_network_card_table = TableRegistry::get('HostsNetworkCard');
        $token_table = TableRegistry::get('Token');

        $request_data = $this->_data;
        //数据校验
        $lack_fields = [];
        $_needed_fileds = [
            'ecsCode','token', 'action'
        ];

        if($_needed_fileds){
            foreach ($_needed_fileds as $_key){
                if (!isset($request_data[$_key])){
                    $lack_fields[] = $_key;
                }
            }
        }
        if(!isset($request_data['requestId'])){
            $request_data['requestId'] = "";
        }

        if(!isset($request_data['callbackUrl'])){
            $request_data['callbackUrl'] = "";
        }
        if (empty($lack_fields)){
            $requestId = $request_data['requestId'];

            $token_data = $token_table->find()->where(['token'=>$request_data['token']])->first();
            //检查token
            $this->_checkToken($request_data['token']);
            //设置操作类型
            switch ($request_data['action']) {
                case 'start':
                    $params["method"] = "ecs_start";
                    break;
                case 'stop':
                    $params["method"] = "ecs_stop";
                    break;
                default:
                    break;
            }
            if (!isset($params["method"])) {
                $code = '3003';
                $msg = 'Please post the correct type of operation';
            } else {
                // TODO 获取主机信息和Code
                $host_code = '';
                $host_code = $request_data['ecsCode'];
                $host_data = $instance_basic_table->find()->where(['code'=>$request_data['ecsCode']])->first();
               
                if (empty($host_data)) {
                    $code = '3001';
                    $msg = 'Not according to the ecsCode queries to the host, please check whether the host';
                } else {
                    $taskId = $this->getGUID();

                    $params["guid"] = $taskId;
                    $params["requestId"] = $request_data["requestId"];
                    $params["uid"] = (string) $token_data['uid'];
                    $params["basicId"] = (string) $host_data['id'];
                    $params["instanceCode"] = $host_code;
                    $params["backurl"]=$request_data["callbackUrl"];
                    $orders = new OrdersController();
                    $result = $orders->ajaxFun($params);
                    if ($result['Code'] != '0') {
                        $code = $result['Code'];
                        $msg  = $result['Message'];
                    }
                }
            }
        }else{
            //数据校验，缺少字段
            $code ='3000';
            $msg = 'Missing field';
        }

        $this->set(compact(array_values($this->_serialize)));
        $this->set("_serialize", $this->_serialize);
    }

    /**
     * 获取主机状态
     * @author wangjincheng。
     */
    public function GetStatus()
    { 
        $this->_serialize = ['code', 'msg', 'deviceStatus', 'requestId'];
        $code = '0';
        $deviceStatus = "";
        $msg = "Success";
        //记录操作
        $this->_setLog('OperateWorkStation');
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        $hosts_network_card_table = TableRegistry::get('HostsNetworkCard');
        $token_table = TableRegistry::get('Token');

        $request_data = $this->_data;
        //数据校验
        $lack_fields = [];
        $_needed_fileds = [
            'token', 'ecsCode'
        ];
        if($_needed_fileds){
            foreach ($_needed_fileds as $_key){
                if (!isset($request_data[$_key])){
                    $lack_fields[] = $_key;
                }
            }
        }
        if(!isset($request_data['requestId'])){
            $request_data['requestId'] = "";
        }
        if (empty($lack_fields)){
            $token_data = $token_table->find()->where(['token'=>$request_data['token']])->first();
            //检查token
            $this->_checkToken($request_data['token']);
            $requestId = $request_data['requestId'];
            
                // TODO 获取主机信息和Code
                $host_code = '';
                if (isset($request_data['ecsCode']) && !empty($request_data['ecsCode'])) {
                    $host_code = $request_data['ecsCode'];
                    $host_data = $instance_basic_table->find()->where(['code'=>$request_data['ecsCode'], "isdelete <>" => "1"])->first();
                } else {
                    $host_data = $hosts_network_card_table->find()->where(['ip'=>$request_data['ip'],'subnet_code'=>$request_data['subnetCode'],'hosts_code <>'=>''])->first();
                    if(!empty($host_data['hosts_code'])){
                        $host_code = $host_data['hosts_code'];
                        $host_data = $instance_basic_table->find('all')->where(['code'=>$host_code, "isdelete <>" => "1"])->first();
                    }
                }
                if (empty($host_data)) {
                    $code = '3001';
                    $msg = 'Not according to the IP and subnetCode queries to the host, please check whether the host';
                } else {
                    $deviceStatus = $this->_translateStatus($host_data['status']); 
                }
            
        }else{
            //数据校验，缺少字段
            $code ='3000';
            $msg = 'Missing field';
        }

        $this->set(compact(array_values($this->_serialize)));
        $this->set("_serialize", $this->_serialize);
    }

    /**
    * 记录日志
    */
    protected function _setLog($action)
    {
        $msg = $action . "方法调用，参数为：" . json_encode($this->_data);
        $msg .= "\n 时间为：".date('Y-m-d H:i:s', time());
        Log::debug($msg);
    }

    //返回接口
    public function callBack()
    {   
        $msg = 'callBack返回参数是：' . file_get_contents('php://input');
        $msg .= "\n 时间为：".date('Y-m-d H:i:s', time());
        Log::debug($msg);
        exit;
    }

    //获取vpc对应的region_code 
    protected function _getRegionCodeByVpcCode($vpc){
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        $agent_table = TableRegistry::get('Agent');
        $data = $instance_basic_table->find()->where(['code' => $vpc])->first();
        if(!empty($data)){
            $agent = $agent_table->find()->where(['class_code' => $data['location_code']])->first();
        }
        if(empty($agent['region_code'])){
            $agent['region_code'] = '';
        }
        return $agent['region_code'];
    }


    public function checkQuota($id,$data)
    {
        $code = "0";
        $msg = "Success";

        $response = Requests::post(Configure::read('Api.cmop').'/SystemInfo/getDepartBuget',[],
           [
           'userid'=>$id,
           ],['verify'=>false]);
        $limit = json_decode(trim($response->body,chr(239).chr(187).chr(191)),true);//所以问题来了，不小心在返回的json字符串中返回了BOM头的不可见字符，某些编辑器默认会加上BOM头，如下处理才能
        $requset = Requests::post(Configure::read('Api.cmop').'/SystemInfo/getDepartUsed',[],
            [
            'userid'=>$id,
            "source_type"=>"cpu_used,router_used,subnet_used,disks_used",
            ],['verify'=>false]);
        $used = json_decode(trim($requset->body,chr(239).chr(187).chr(191)),true);
        //所以问题来了，不小心在返回的json字符串中返回了BOM头的不可见字符，某些编辑器默认会加上BOM头，如下处理才能
        $used = $used['data'];
        $limit = $limit['data'];
        if(!isset($limit['cpu_bugedt'])){
            $limit['cpu_bugedt'] = 0;
        }
        if(!isset($limit['memory_buget'])){
            $limit['memory_buget'] = 0;
        }
        if(!isset($limit['gpu_bugedt'])){
            $limit['gpu_bugedt'] = 0;
        }
        if ($data['cpu_number']+$used['cpu_used'] > $limit['cpu_bugedt']){
            $code = '4001';
            $msg = "Insufficient CPU quota";
        }
        if ($data['memory_gb']+$used['memory_used'] > $limit['memory_buget']){
            $code = '4002';
            $msg = "Insufficient memory quota";
        }
        if ($data['gpu_gb']+$used['gpu_used'] > $limit['gpu_bugedt']){
            $code = '4003';
            $msg = "Insufficient GPU quota";
        }
        $data = "";
        
        if(intval($code)>0){
            $result=array(
                'code'=>$code,
                'msg'=>$msg,
                'data'=>$data
            );
            echo json_encode($result);
            exit;
        }
    }


    /**
    * 生成GUID
    */
    public function getGUID(){
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = substr($charid, 0, 8).$hyphen
        .substr($charid, 8, 4).$hyphen
        .substr($charid,12, 4).$hyphen
        .substr($charid,16, 4).$hyphen
        .substr($charid,20,12);
        return $uuid;
    } 

    public function GetTaskStatus()
    {
        $this->_serialize = ['code', 'msg', 'taskInfo', 'requestId'];
        $code = '0';
        $taskInfo = "";
        $msg = "Success";
        //记录操作
        $this->_setLog('GetTaskStatus');
        $callback_info_table = TableRegistry::get('CallbackInfo');
        $token_table = TableRegistry::get('Token');

        $request_data = $this->_data;
        //数据校验
        $lack_fields = [];
        $_needed_fileds = [
            'token', 'taskId'
        ];
        if($_needed_fileds){
            foreach ($_needed_fileds as $_key){
                if (!isset($request_data[$_key])){
                    $lack_fields[] = $_key;
                }
            }
        }
        if(!isset($request_data['requestId'])){
            $request_data['requestId'] = "";
        }
        if (empty($lack_fields)){
            $token_data = $token_table->find()->where(['token'=>$request_data['token']])->first();
            //检查token
            $this->_checkToken($request_data['token']);
            $requestId = $request_data['requestId'];
            $taskInfo = "";
            
            $callback_info_data = $callback_info_table->find()->where(['guid'=>$request_data["taskId"]])->first();
            if(isset($callback_info_data['callback_info']) && !empty($callback_info_data['callback_info'])){
                $taskInfo = json_decode($callback_info_data['callback_info']);
            }
            if($taskInfo == ""){
                $taskInfo = null;
            }
            
        }else{
            //数据校验，缺少字段
            $code ='3000';
            $msg = 'Missing field';
        }

        $this->set(compact(array_values($this->_serialize)));
        $this->set("_serialize", $this->_serialize);
    }

    //获取所有租户下的ecs
    public function GetWorkstationLists()
    {
        $this->_serialize = ['code', 'msg', 'workstationLists', 'requestId', 'exception'];
        $code = '0';
        $taskInfo = "";
        $msg = "Success";
        //记录操作
        $this->_setLog('GetTaskStatus');
        $callback_info_table = TableRegistry::get('CallbackInfo');
        $token_table = TableRegistry::get('Token');

        $request_data = $this->_data;
        //数据校验
        $lack_fields = [];
        $_needed_fileds = [
            'token', 'loginname', 'vendorCode'
        ];
        if($_needed_fileds){
            foreach ($_needed_fileds as $_key){
                if (!isset($request_data[$_key])){
                    $lack_fields[] = $_key;
                }
            }
        }
        if(!isset($request_data['requestId'])){
            $request_data['requestId'] = "";
        }
        if (empty($lack_fields)){
            $token_data = $token_table->find()->where(['token'=>$request_data['token']])->first();
            //检查token
            $this->_checkToken($request_data['token']);
            $requestId = $request_data['requestId'];
            $workstationLists = "";
            $exception = "";


            $systemInfo = $this->_getSystemsettingInfoByVendorCode($request_data["vendorCode"]);
            if($systemInfo == "3006"){
                $code = '3006';
                $msg  = "Database is not configured OR VendorCode error";
            } else {
                if(isset($request_data['type'])&&$request_data['type']=='desktop'){//桌面
                    $workstationLists = $this->_getDesktopInfo($this->_data, $systemInfo);
                }else{//主机
                    $workstationLists = $this->_getEcsInfo($this->_data, $systemInfo);
                }
                if($workstationLists == "3001"){
                    $code = '3001';
                    $msg = 'Loginname error';
                }
            }
        }else{
            //数据校验，缺少字段
            $code ='3000';
            $msg = 'Missing field';
        }

        $this->set(compact(array_values($this->_serialize)));
        $this->set("_serialize", $this->_serialize);
    }

    public function test(){

        $this->_getEcsInfo($this->_data);
    }
    protected function _getDesktopInfo($request_data, $SystemInfo)
    {
        $instance_basic_table = TableRegistry::get("InstanceBasic");

        //获取部门信息根据人员名称
        $department_info = $this->_getDepartInfoByLoginname($request_data["loginname"]);

        if($department_info == "0"){
            return "3001";
        }

        //获取桌面信息
        $basic_data = $instance_basic_table->find()->contain([
            "HostsNetworkCard",
            "HostExtend",
            "Accounts"
        ])->where([
            "InstanceBasic.department_id" => $department_info["id"],
            "InstanceBasic.subnet" => $SystemInfo["subnetCode"],
            "InstanceBasic.type" => "desktop",
            "InstanceBasic.code <>" => "",
            "InstanceBasic.isdelete <>" => "1"
        ])->toArray();

        $list = array();
        foreach ($basic_data as $value) {
            $data = array();
            $data["ecsCode"] = $value["code"];
            $data["imageCode"] = $value["host_extend"]["image_code"];
            $data["instanceTypeCode"] = $value["host_extend"]["type"];
            $data["deviceStatus"] = $this->_translateStatus($value["status"]);

            if (!empty($value["hosts_network_card"])) {
                foreach ($value["hosts_network_card"] as $n_v) {
                    $netCard = array();
                    $netCard["subnetCode"] = $n_v["subnet_code"];
                    $netCard["ip"] = $n_v["ip"];
                    $netCard["isdefault"] = (int)$n_v["is_default"];
                    $data["netCardInfo"][] = $netCard;
                }
            } else {
                $data["netCardInfo"] = null;
            }


            //获取配置信息
            $systemInfo = $this->_getSystemsettingInfoByVendorCode($request_data["vendorCode"]);
            $data["username"] = $systemInfo["userName"];
            $data["password"] = $systemInfo["pwd"];
            $data["sshPort"] = $systemInfo["ssh"];
            $data["creater"] = $value["account"]["loginname"];
            $list[] = $data;
        }

        return $list;

    }
    protected function _getEcsInfo($request_data, $SystemInfo)
    {
        // debug($request_data);die;
        $instance_basic_table = TableRegistry::get("InstanceBasic");

        //获取部门信息根据人员名称
        $department_info = $this->_getDepartInfoByLoginname($request_data["loginname"]);

        if($department_info == "0"){
            return "3001";
        }

        //获取主机信息
        $basic_data = $instance_basic_table->find()->contain([
            "HostsNetworkCard",
            "HostExtend",
            "Accounts"
            ])->where([
            "InstanceBasic.department_id" => $department_info["id"],
            "InstanceBasic.subnet" => $SystemInfo["subnetCode"],
            "InstanceBasic.type" => "hosts",
            "InstanceBasic.code <>" => "",
            "InstanceBasic.isdelete <>" => "1"
            ])->toArray();
        
        $list = array();
        foreach ($basic_data as $value) {
            $data = array();
            $data["ecsCode"] = $value["code"];
            $data["imageCode"] = $value["host_extend"]["image_code"];
            $data["instanceTypeCode"] = $value["host_extend"]["type"];
            $data["deviceStatus"] = $this->_translateStatus($value["status"]);

            if (!empty($value["hosts_network_card"])) {
                foreach ($value["hosts_network_card"] as $n_v) {
                    $netCard = array();
                    $netCard["subnetCode"] = $n_v["subnet_code"];
                    $netCard["ip"] = $n_v["ip"];
                    $netCard["isdefault"] = (int)$n_v["is_default"];
                    $data["netCardInfo"][] = $netCard;
                }
            } else {
                $data["netCardInfo"] = null;
            }


            //获取配置信息
            $systemInfo = $this->_getSystemsettingInfoByVendorCode($request_data["vendorCode"]);
            $data["username"] = $systemInfo["userName"];
            $data["password"] = $systemInfo["pwd"];
            $data["sshPort"] = $systemInfo["ssh"];
            $data["creater"] = $value["account"]["loginname"];
            $list[] = $data;
        }

        return $list;

    }



    //获取部门信息
    protected function _getDepartInfoByLoginname($loginname)
    {
        $accounts_table = TableRegistry::get("Accounts");
        $departments_table = TableRegistry::get("Departments");
        $account_data = $accounts_table->find()->where(['loginname' => $loginname])->first();
        if (empty($account_data)) {
            return 0;
        } else {
            $depart_data = $departments_table->find()->where(['id' => $account_data["department_id"]])->first();
            if (empty($depart_data)) {
                return 0;
            }
        }
        return $depart_data;

    }

    //获取系统参数
    protected function _getSystemsettingInfoByVendorCode($vendorCode)
    {
        //获取系统配置参数
        $table  = TableRegistry::get('Systemsetting');
        $pro_field = strtolower($vendorCode);
        $datas   = $table->find('all')->select(['para_code', 'para_value'])->where(array('para_code like' => $pro_field.'_%'))->toArray();
        $params = array();
        $params['subnetCode2'] = '';
        if (!empty($datas) && count($datas) >= 4) {
            //创建接口参数
            foreach ($datas as $key => $value) {
                switch ($value['para_code']) {
                    case $pro_field.'_work_vpc':
                        $params["vpcCode"] = $value['para_value'];
                        break;
                    case $pro_field.'_work_subnet':
                        $params["subnetCode"] = $value['para_value'];
                        break;
                    case $pro_field.'_work_username':
                        $params["userName"] = $value['para_value'];
                        break;
                    case $pro_field.'_work_pwd':
                        $params["pwd"] = $value['para_value'];
                        break;
                    case $pro_field.'_ssh_port':
                        $params["ssh"] = $value['para_value'];
                        break;    
                }
                if (strstr($value['para_code'], $pro_field.'_work_subnet_')){
                    if (!empty($params['subnetCode2'])) {
                        $params['subnetCode2'] .= ',';
                    }
                    $params['subnetCode2'] .= $value['para_value'];
                } 

            }
            return $params;
        } else {
            return '3006';
        }
    }

    //转译状态
    protected function _translateStatus($status)
    {
        $deviceStatus = '';
        switch ($status) {
            case '运行中':
                $deviceStatus = 'Running';
                break;
            case '已暂停(欠费)':
                $deviceStatus = 'Overdue';
                break;
            case '创建中':
                $deviceStatus = 'Creating';
                break;
            case '创建失败':
                $deviceStatus = 'NotCreated';
                break;
            case '销毁中':
                $deviceStatus = 'Destroying';
                break;
            case '销毁失败':
                $deviceStatus = 'NotDestroyed';
                break;
            case '已停止':
                $deviceStatus = 'Stopped';
                break;
        }

        return $deviceStatus;
    }
}
