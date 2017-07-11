<?php
/**
* citrix 对外接口
*
*
* @author XingShanghe<xingshanghe@gmail.com>
* @date  2015年11月3日下午2:39:11
* @source CitrixController.php
* @version 1.0.0
* @copyright  Copyright 2015 sobey.com
*/
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Composer\Autoload\ClassLoader;
use App\Auth\CmopPasswordHasher;
use Cake\Error\FatalErrorException;
use Cake\Utility\Xml;
use Cake\Datasource\ConnectionManager;
use Cake\Network\Http\Client;

use Requests as Requests;

class CitrixController extends AppController
{
    //接口属性
    private $_data = null;
    private $_error = [];
    private $_serialize = array('code','msg','data');
    private $_code = 0;
    private $_msg = "";

    //数据库链接
    private $_db_conn = null;

    private $_is_auth = false;

    //用户以及ad账号信息
    private $_user = null;
    private $_aduser = null;

    private $_is_https = false;

    //请求信息
    protected $_url;
    protected $_headers;
    protected $_options;


    public function initialize(){
        parent::initialize();
        $this->viewClass = 'Json';
        $this->loadComponent('RequestHandler');

        $this->_is_https = Configure::read('Citrix.is_https');

        //获取参数
        $this->_data = $this->_getData();

        //获取接口中的cmop的用户信息
        if (!in_array($this->request->params['action'], ['status'])){
            $this->_user = $this->_getUser();
        }

        $loader = new ClassLoader();
        $loader->add('Requests', ROOT.DS.'vendor/requests');
        $loader->register();
        Requests::register_autoloader();

        //$this->_initRequest();//初始化request对象以及配置信息
    }
    /**
     * 初始化Requests类
     */
    private function _initRequest()
    {


        if (isset($this->_data['name'])){
            $this->_initCitrixServer();
        }

        $this->_headers['Accept-Language'] = 'zh-cn,zh;q=0.5';
        $this->_headers['Accept-Charset'] = 'utf-8,GB2312;q=0.7,*;q=0.7';

        $this->_headers['X-Citrix-IsUsingHTTPS'] = $this->_is_https?'Yes':'No';

        $this->_options['verify'] = false;
        //$this->_options['timeout'] = 2;
        $this->_options['cookies'] = [];
    }

    private function _initRequest20160229()
    {
        if (!in_array($this->request->params['action'], ['status'])){
            $this->_initCitrixServer20160229();
        }

        $this->_headers['Accept-Language'] = 'zh-cn,zh;q=0.5';
        $this->_headers['Accept-Charset'] = 'utf-8,GB2312;q=0.7,*;q=0.7';

        $this->_headers['X-Citrix-IsUsingHTTPS'] = $this->_is_https?'Yes':'No';

        $this->_options['verify'] = false;
        //$this->_options['timeout'] = 2;
        $this->_options['cookies'] = [];
    }

    private function _initCitrixServer20160229()
    {
        $name = $this->_data['name'];
        $this->_db_conn = ConnectionManager::get('default');
        $sql = "select vpc.id ,vpc.desktop_server_url ,basic_b.`code`,desk.aduser,ad_user.loginName,ad_user.loginPassword "
                ." from cp_vpc_extend vpc "
                ." left join cp_instance_basic basic on vpc.basic_id = basic.id "
                ." left join cp_instance_basic basic_b on basic.`code` = basic_b.vpc "
                ." left join cp_host_extend desk on desk.basic_id = basic_b.id "
                ." left join cp_ad_user ad_user on (desk.aduser = ad_user.loginName and  basic_b.vpc = ad_user.vpcCode)"
                ." where desk.name = '".$name."'";
        $data = $this->_db_conn->execute($sql)->fetch('assoc');
        Log::info($sql);
        $this->_aduser = [
            'username' => addslashes($data['loginName']),//为域账户中\转义
            'password' => $data['loginPassword']
        ];
        if (isset($data['desktop_server_url'])&&!empty($data['desktop_server_url'])){
            //parse_url($url, PHP_URL_SCHEME)
            $this->_is_https = parse_url($data['desktop_server_url'],PHP_URL_SCHEME) == 'https'? true:false;
            $this->_headers['X-Citrix-IsUsingHTTPS'] = $this->_is_https?'Yes':'No';
            $this->_url = $data['desktop_server_url'];
        }else{
            try {
               $http = new Client();
                $obj_response = $http->post(Configure::read('URL'),json_encode([
                    "method"=>"desktop_login",
                    "uid"=>strval($this->_user['id']),//此值没有
                    "desktopCode"=>$data['code'],
                ]),['type' => 'json']);

                $response = json_decode($obj_response->body,true);
                if ($response['Code'] == 0){

                    //插入update `cmop`.`cp_vpc_extend` set `desktop_server_url`='https://aaad.xx.com' where `id`='302'
                    $_sql = "update `cp_vpc_extend` set `desktop_server_url`='".$response['Data']['citrixDesktopDomain']."' where `id`='".$data['id']."'";
                    $this->_db_conn->execute($_sql);

                    //parse_url($url, PHP_URL_SCHEME)
                    $this->_is_https = parse_url($response['Data']['citrixDesktopDomain'],PHP_URL_SCHEME) == 'https'? true:false;
                    $this->_headers['X-Citrix-IsUsingHTTPS'] = $this->_is_https?'Yes':'No';
                    $this->_url = $response['Data']['citrixDesktopDomain'];

                }else{
                    Log::error(isset($response['Message'])?$response['Message']:'desktop_login returned error.');
                    $this->_error[] = isset($response['Message'])?$response['Message']:'desktop_login returned error.';
                }

            } catch (\Exception $e) {

                Log::error('do desktop_login error:'.$e->getMessage(),[
                    "method"=>"desktop_login",
                    "uid"=>strval($this->_user['id']),//此值没有
                    "desktopCode"=>$data['code'],
                ]);

                $this->_error[] = 'do desktop_login error:'.$e->getMessage();
            }

        }
    }

    /**
     * 获取citrix信息包含ad账号和请求地址
     */
    private function _initCitrixServer()
    {
        $name = $this->_data['name'];
        $this->_db_conn = ConnectionManager::get('default');
        $sql = "select vpc.desktop_server_url ,basic_b.`code`,desk.aduser,ad_user.loginName,ad_user.loginPassword "
              ." from cp_vpc_extend vpc "
              ." left join cp_instance_basic basic on vpc.basic_id = basic.id "
              ." left join cp_instance_basic basic_b on basic.`code` = basic_b.vpc "
              ." left join cp_host_extend desk on desk.basic_id = basic_b.id "
              ." left join cp_ad_user ad_user on desk.aduser = ad_user.loginName"
              ." where desk.name = '".$name."'";
        $data = $this->_db_conn->execute($sql)->fetch('assoc');
        $this->_aduser = [
                'username' => addslashes($data['loginName']),//为域账户中\转义
                'password' => $data['loginPassword']
        ];

        if (isset($data['desktop_server_url'])&&!empty($data['desktop_server_url'])){
            //parse_url($url, PHP_URL_SCHEME)
            $this->_is_https = parse_url($data['desktop_server_url'],PHP_URL_SCHEME) == 'https'? true:false;
            $this->_headers['X-Citrix-IsUsingHTTPS'] = $this->_is_https?'Yes':'No';
            $this->_url = $data['desktop_server_url'];
        }else{

            $http = new Client();
            $obj_response = $http->post(Configure::read('URL'),json_encode([
                "method"=>"desktop_login",
                "uid"=>strval($this->_user['id']),//此值没有
                "desktopCode"=>$data['code'],
            ]),['type' => 'json']);

            $response = json_decode($obj_response->body,true);


            if ($response['Code'] == 0){

                //parse_url($url, PHP_URL_SCHEME)
                $this->_is_https = parse_url($response['Data']['citrixDesktopDomain'],PHP_URL_SCHEME) == 'https'? true:false;
                $this->_headers['X-Citrix-IsUsingHTTPS'] = $this->_is_https?'Yes':'No';
                $this->_url = $response['Data']['citrixDesktopDomain'];


            }else{
                throw new FatalErrorException($response['Message']);
            }

        }

    }
    /**
     * 桌面首页（门户）获取可用软件主机列表
     */
    public function getUseableList()
    {
        if ($this->_data && is_array($this->_data)){
            if (isset($this->_user['id'])&&(!empty($this->_user))){
                $this->_db_conn = ConnectionManager::get('default');
                $sql = "SELECT DISTINCT basic.`code` as code, basic.`name` as dispname,basic.vpc as vpc,desktop.`name` as hostname,slist.software_code,slist.software_name,basic.`status`,desktop.connect_status,desktop.`connect_user`,desktop.connect_time,desktop.last_reporttime"
                    ." FROM cp_instance_basic basic,cp_host_extend desktop,cp_software_list slist,cp_softwares_desktop sdesktop,cp_roles_accounts role,cp_roles_software srole,cp_accounts accounts"
                    ." where role.account_id= ".$this->_user['id']." and basic.id=desktop.basic_id and"
                    ." desktop.id=sdesktop.host_id and sdesktop.software_id=slist.id "
                    ." and role.role_id=srole.role_id and srole.software_id=slist.id "
                    ." and accounts.id=role.account_id and accounts.department_id=basic.department_id"
                    ." group by basic.`code`";
               $data = $this->_db_conn->execute($sql)->fetchAll('assoc');
               $code = '0000';
               $msg = $this->_getMsg($code);
            }else{
                //用户信息不正确
                //post 数据空
                $code = '0010';
                $msg = $this->_getMsg($code);
            }
        }else{
            //post 数据空
            $code = '0004';
            $msg = $this->_getMsg($code);
        }
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }

    /**
     * 设置主机可用状态（占用/可用）
     */
    public function status()
    {
        Log::debug("status调用:".json_encode($this->_data));
        if ($this->_data && is_array($this->_data)){
            //$this->_initRequest();
            //TODO 获取状态
            $desktop_extend_table = TableRegistry::get('HostExtend');
            if (empty($name)){
                $name = $this->_data['name'];
            }
            if (!isset($this->_data['status'])){
                $data = $desktop_extend_table->find('all')->where([
                    'name'=>$name
                ])->first();

                $code = '0000';
                $msg = $this->_getMsg($code);

            }else{
                $current_time = time();
                //name,status
                $desktop_extend = $desktop_extend_table->find()->where([
                    'name'=>$name
                ])->first();
                if($desktop_extend)//add by lbg 20161222,只有机器在数据库里面存在，才进行下面的操作

                {

                    $last_status = $desktop_extend->connect_status;
                    $new_status=$this->_data['status'];
                    if($new_status !=$last_status){//只有状态变化才更新数据库
                        $tempmsg="接口调用：Citrix--status ";
                        $tempmsg.=json_encode($this->_data);
                        Log::debug($tempmsg);
                        if ($last_status != 1  && $new_status==1 ){
                            //从启动中变成已使用状态
                            $desktop_extend->connect_time = $current_time;

                        }
                        $desktop_extend->connect_status = intval($this->_data['status']);
                        $tmp_connect_id = $desktop_extend->connect_id;
                        if ($desktop_extend->connect_status == 0){
                            $desktop_extend->connect_id = 0;
                            $desktop_extend->connect_user = '';
                        }
                        $desktop_extend->last_reporttime = $current_time;
                        $desktop_extend_table->save($desktop_extend);

                    }

                    //断开连接时计费
/*                     if($this->_data['status'] == 0){
                        //TODO 收集信息云桌面计费信息 埋点
                        $service_list_table = TableRegistry::get('ServiceList');
                        $service_list = $service_list_table->find()->select(['service_id','type_id','basic_id'])->where(['basic_id'=>$desktop_extend['basic_id']])->first();
                        if ($service_list && $tmp_connect_id>0){//用户id>0才收集
                            //调用计费接口 /api/charge/collect
                            $response_charge_obj = Requests::post(Configure::read('Api.cmop').'/charge/collect',[],[
                                'account_id'=>$tmp_connect_id,
                                'device_id'=>$desktop_extend['basic_id'],
                                'begin_time'=>$desktop_extend['connect_time'],
                                'end_time'=>$current_time,
                                'data_source'=>2
                            ],[
                                'verify'=>false
                            ]);

                           // $this->unloadficsdisk($tmp_connect_id, $desktop_extend['basic_id']);
                        }
                    } */
                    //状态不同，才发送通知
                    if($last_status != $this->_data['status']){
                        if (is_string(Configure::read('NotifyUrl'))){
                            // 通知消息服务器
                            $response_notify_obj = @Requests::post(Configure::read('NotifyUrl').'/send',[],[
                                'time'=>$current_time,
                                'sign'=>md5($current_time.Configure::read('NotifyKey')),
                                'data'=>json_encode([
                                    'SendType'=>'websocket',
                                    'MsgType'=>'info',
                                    'Msg'=>'',
                                    'Topic'=>$name,
                                    'Data'=>[
                                        'username'=>$desktop_extend['connect_user'],
                                        'name'=>$name,
                                        'status'=>strval($this->_data['status']),
                                        'description'=>$this->_data['status']==1?'已启动':'已️空闲'
                                    ]
                                ]),
                            ]);
                          //  Log::debug(serialize($response_notify_obj));
                        }elseif (is_array(Configure::read('NotifyUrl'))){
                            $urls = Configure::read('NotifyUrl');
                            foreach ($urls as $url)
                            {
                                try {
                                    // 通知消息服务器
                                    $response_notify_obj = @Requests::post($url.'/send',[],[
                                        'time'=>$current_time,
                                        'sign'=>md5($current_time.Configure::read('NotifyKey')),
                                        'data'=>json_encode([
                                            'SendType'=>'websocket',
                                            'MsgType'=>'info',
                                            'Msg'=>'',
                                            'Topic'=>$name,
                                            'Data'=>[
                                                'username'=>$desktop_extend['connect_user'],
                                                'name'=>$name,
                                                'status'=>strval($this->_data['status']),
                                                'description'=>$this->_data['status']==1?'已启动':'已️空闲'
                                            ]
                                        ]),
                                    ]);
                                   // Log::debug(serialize($response_notify_obj));
                                } catch (\Exception $e) {
                                    Log::error($e->getMessage().':'.$url);
                                    continue;
                                }
                            }
                        }


                    }
                 }//机器在数据库里面存在

                if (Configure::check('Api.dms')){
                    $_dms_urls = Configure::read('Api.dms');
                    if ($_dms_urls){
                        foreach ($_dms_urls as $_url){
                            try {
                                @Requests::post($_url.'/Desktops/status',['Content-Type'=>'application/json'],
                                    json_encode([
                                        'name'=>$this->_data['name'],
                                        'status'=>$this->_data['status'],
                                        'basic_id'=>isset($desktop_extend['basic_id'])?$desktop_extend['basic_id']:0
                                    ]));
                            } catch (\Exception $e) {
                                Log::error($e->getMessage().':'.$_url);
                                continue;
                            }
                        }
                    }
                }



                $data = $desktop_extend;
                $code = '0000';
                $msg = $this->_getMsg($code);
            }
        }else{
            //post 数据空
            $code = '0004';
            $msg = $this->_getMsg($code);
        }
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }





    /**
     * 启动桌面
     * @author xingshanghe
     * @date 2016-02-29
     */
    public function launch20160229()
    {
        
        if ($this->_data && is_array($this->_data)){
            //合法用户，返回ica文件内容
            if($this->_user){
                $this->_initRequest20160229();
                
                $_data['username'] = $this->_aduser['username'];
                $_data['password'] = $this->_aduser['password'];
                $resource = $this->_launchInit20160229($this->_data['name'], $_data);
//                 debug($resource);die;
                if ($resource){
                    if ($this->_doPreLaunch($resource)){
                        $response = $this->_doLaunch($resource);
                        if (isset($response->success)&&($response->success == true)){
                            $data['body'] = str_replace("=\\", "=\\\\", $response->body);
                            if ($this->_is_https){
                                $data['headers']['content-type'] = $response->headers->offsetGet('content-type');
                            }else{
                                $data['headers'] = $response->headers;
                            }
                            $code = '0000';
                        }
                    }else{
                        //doprelaunch失败。一般为机器状态
                        $code = '0007';
                        $msg = '中间件执行失败:'.$code;
                    }
                }else{
                     //_launchInit20160229失败：一般为citrix地址不能访问
                    $code = '0006';
                    $msg = '中间件执行失败:'.$code;
                }
            }else{
                //post 数据空
                $code = '0005';
                $msg = 'Cmop系统帐号密码错误';
            }
        }else{
            //post 数据空
            $code = '0004';
            $msg = $this->_getMsg($code);
        }

        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }

    /**
     * 桌面启动
     */


    public function launch()
    {
        if ($this->_data && is_array($this->_data)){
            //合法用户，返回ica文件内容
            if($this->_user){
                $this->_initRequest();
                $_data['username'] = $this->_aduser['username'];
                $_data['password'] = $this->_aduser['password'];
                $resource = $this->_launchInit($this->_data['name'], $_data);
                if ($resource){
                    if ($this->_doPreLaunch($resource)){
                        $response = $this->_doLaunch($resource);
                        if (isset($response->success)&&($response->success == true)){
                            $data['body'] = str_replace("=\\", "=\\\\", $response->body);
                            if ($this->_is_https){
                                $data['headers']['content-type'] = $response->headers->offsetGet('content-type');
                            }else{
                                $data['headers'] = $response->headers;
                            }
                            $code = '0000';
                        }
                    }
                }else{
                    //post 数据空
                    $code = '0009';
                    $msg = $this->_getMsg($code);
                }
            }

        }else{
            //post 数据空
            $code = '0004';
            $msg = $this->_getMsg($code);
        }
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }


    protected function _launchInit20160229($name,$data){
        //Https下的ad账户校验
        if ($this->_is_https){
            try {
                //0.https的cgi/login
                $response = $this->_doRequest('/cgi/login',[
                    'login'=>$this->_aduser['username'],
                    'passwd'=>$this->_aduser['password'],
                ]);

                if(!($response->success)){
                    Log::error('/cgi/login returned error.params:'.json_encode([
                        'login'=>$this->_aduser['username'],
                        'passwd'=>$this->_aduser['password'],
                    ]));
                    $this->_error[] = isset($response->body)?$response->body:'/cgi/login returned error.';
                    return false;
                }
            } catch (\Exception $e) {
                Log::error('some error occured when do /cgi/login.Msg:'.$e->getMessage());
                $this->_error[] = 'some error occured when do '.$this->_url.'/cgi/login.Msg:'.$e->getMessage();
                return false;
            }
        }

        //获取认证方法
        // 根据配置选项：ExplicitForms|CitrixAuth）
        $response = $this->_doRequest('/Citrix/StoreWeb/Authentication/GetAuthMethods');
        $response_body = Xml::build($response->body);

        $response_body_arr = json_decode(json_encode($response_body),TRUE);
        //根据配置中选择是通过哪种登陆方式获取到ExplicitForms方式
        foreach ($response_body_arr['method'] as $key => $value){
            //CitrixAGBasic
            if ($this->_is_https) {
                if ($value['@attributes']['name'] == 'CitrixAGBasic'){
                    $authLoginUrl = $value['@attributes']['url'];
                    break;
                }
            }elseif($value['@attributes']['name'] == 'ExplicitForms'){
                $authLoginUrl = $value['@attributes']['url'];
                break;
            }
        }
        //登陆，获取表单信息
        $response = $this->_doRequest('/Citrix/StoreWeb/'.$authLoginUrl);
        $response_body = Xml::build($response->body);
        $response_body_arr = json_decode(json_encode($response_body),TRUE);

        if ($this->_is_https){
            if (isset($response->success)&&(true==$response->success)){
                //获取资源列表
                $response = $this->_doRequest('/Citrix/StoreWeb/Resources/List');
                $response_body = json_decode($response->body,true);
                $resource = false;
                foreach ($response_body['resources'] as $value){
                    if (isset($value['isdesktop'])&&($value['isdesktop']===true)&&($name == (isset($value['name'])?$value['name']:$value['name']))){
                        $resource = $value;
                        break;
                    }
                }
                return $resource;
            }else{
                Log::error('some error occured when do /Citrix/StoreWeb/'.$authLoginUrl.isset($response->body)?$response->body:'');
                return false;
            }
        }else{
            if (isset($response->success)&&(true==$response->success)){

                //if ('success' === strtolower($response_body_arr['Status']) ){
                $post['urls']['PostBack'] =  $response_body_arr['AuthenticationRequirements']['PostBack'];
                $post['urls']['CancelPostBack'] =  $response_body_arr['AuthenticationRequirements']['CancelPostBack'];
                $post['keys'] = ['StateContext'];
                $post['data'] = [];
                if (!$this->_is_https) {
                    foreach ($response_body_arr['AuthenticationRequirements']['Requirements']['Requirement'] as $value){
                        $post['keys'][] = $value['Credential']['ID'];
                    }

                    $data['StateContext'] = !empty($response_body_arr['StateContext'])?$response_body_arr['StateContext']:'';
                    $data['saveCredentials'] = false;
                    $data['loginBtn']       = '登录';
                    foreach ($post['keys'] as $key){
                        $post['data'][$key] = isset($data[$key])?$data[$key]:'';
                    }
                    $post['data']["username"]=$this->_aduser['username'];
                    $post['data']["password"]=$this->_aduser['password'];
                }
                //提交数据认证登陆
                $response = $this->_doRequest('/Citrix/StoreWeb/'.$post['urls']['PostBack'],$post['data']);

                //获取认证的id
                $response_body = Xml::build($response->body);
                $response_body_arr = json_decode(json_encode($response_body),true);

                //登录成功失败判断不准确
                //登录。认证成功
                if (('success' === strtolower($response_body_arr['Result']))&&isset($response_body_arr['AuthType'])&&(in_array($response_body_arr['AuthType'], ['ExplicitForms','CitrixAuth'])) ){
                    //获取资源列表
                    $response = $this->_doRequest('/Citrix/StoreWeb/Resources/List');
                    $response_body = json_decode($response->body,true);
                    $resource = false;
                    foreach ($response_body['resources'] as $value){
                        if (isset($value['isdesktop'])&&($value['isdesktop']===true)&&($name == $value['name'])){
                            $resource = $value;
                            break;
                        }
                    }
                    return $resource;

                }else{
                    //登录失败
                    Log::error('some error occured when do /Citrix/StoreWeb/'.$post['urls']['PostBack'].isset($response->body)?$response->body:'');
                    return false;
                }

            }else{
                Log::error('some error occured when do /Citrix/StoreWeb/'.$authLoginUrl.isset($response->body)?$response->body:'');
                return false;
            }
        }

    }

    /**
     * Citrix桌面启动初始化。
     * 初始化，其中流程还需要梳理
     */
    protected function _launchInit($name,$data){
        if ($this->_is_https) {
            //0.https的cgi/login
            $response = $this->_doRequest('/cgi/login',[
                    'login'=>$this->_aduser['username'],
                    'passwd'=>$this->_aduser['password'],
            ]);

            if(!($response->success)){
                Log::write('error', serialize($response));
                return false;
            }
        }
        //获取认证方法
        // 根据配置选项：ExplicitForms|CitrixAuth）
        $response = $this->_doRequest('/Citrix/StoreWeb/Authentication/GetAuthMethods');
        $response_body = Xml::build($response->body);
        $response_body_arr = json_decode(json_encode($response_body),TRUE);
        //根据配置中选择是通过哪种登陆方式获取到ExplicitForms方式
        foreach ($response_body_arr['method'] as $key => $value){
            //CitrixAGBasic
            if ($this->_is_https) {
                if ($value['@attributes']['name'] == 'CitrixAGBasic'){
                    $authLoginUrl = $value['@attributes']['url'];
                    break;
                }
            }elseif($value['@attributes']['name'] == 'ExplicitForms'){
                $authLoginUrl = $value['@attributes']['url'];
                break;
            }
        }
        //登陆，获取表单信息
        $response = $this->_doRequest('/Citrix/StoreWeb/'.$authLoginUrl);

        $response_body = Xml::build($response->body);
        $response_body_arr = json_decode(json_encode($response_body),TRUE);


        if ($this->_is_https){
            if (isset($response->success)&&(true==$response->success)){
                //获取资源列表
                $response = $this->_doRequest('/Citrix/StoreWeb/Resources/List');
                $response_body = json_decode($response->body,true);
                $resource = false;
                foreach ($response_body['resources'] as $value){
                    if (isset($value['isdesktop'])&&($value['isdesktop']===true)&&($name == $value['name'])){
                        $resource = $value;
                        break;
                    }
                }
                return $resource;
            }
        }else{
            if (isset($response->success)&&(true==$response->success)){
            //if ('success' === strtolower($response_body_arr['Status']) ){
                $post['urls']['PostBack'] =  $response_body_arr['AuthenticationRequirements']['PostBack'];
                $post['urls']['CancelPostBack'] =  $response_body_arr['AuthenticationRequirements']['CancelPostBack'];
                $post['keys'] = ['StateContext'];

                $post['data'] = [];
                if (!$this->_is_https) {
                    foreach ($response_body_arr['AuthenticationRequirements']['Requirements']['Requirement'] as $value){
                        $post['keys'][] = $value['Credential']['ID'];
                    }

                    $data['StateContext'] = !empty($response_body_arr['StateContext'])?$response_body_arr['StateContext']:'';
                    $data['saveCredentials'] = false;
                    $data['loginBtn']       = '登录';
                    foreach ($post['keys'] as $key){
                        $post['data'][$key] = isset($data[$key])?$data[$key]:'';
                    }
                }
                //提交数据认证登陆
                $response = $this->_doRequest('/Citrix/StoreWeb/'.$post['urls']['PostBack'],$post['data']);

                //获取认证的id
                $response_body = Xml::build($response->body);
                $response_body_arr = json_decode(json_encode($response_body),true);
                //登录成功失败判断不准确
                //登录。认证成功
                if (('success' === strtolower($response_body_arr['Result']))&&isset($response_body_arr['AuthType'])&&(in_array($response_body_arr['AuthType'], ['ExplicitForms','CitrixAuth'])) ){
                    //获取资源列表
                    $response = $this->_doRequest('/Citrix/StoreWeb/Resources/List');
                    $response_body = json_decode($response->body,true);
                    $resource = false;
                    foreach ($response_body['resources'] as $value){
                        if (isset($value['isdesktop'])&&($value['isdesktop']===true)&&($name == $value['name'])){
                            $resource = $value;
                            break;
                        }
                    }
                    return $resource;

                }else{
                    //登录失败
                    return false;
                }

            }else{
                return false;
            }
        }
    }

    /**
     * 发送预启动请求
     * @param unknown $resource
     * @return boolean
     */
    protected function _doPreLaunch($resource){
        $response = $this->_doRequest('/Citrix/StoreWeb/'.$resource['launchstatusurl']);
        $response_body = json_decode($response->body,true);
        if(isset($response_body['status'])&&('success'===strtolower($response_body['status']))){
            return  true;
        }else{
            Log::error("文件名=".__FILE__."行号=".__LINE__.':'.__FUNCTION__);
           // Log::error($response);
           Log::error($resource);

            Log::write('error', isset($response_body['errorId'])?$response_body['errorId']:'do pre lauch failure.');
            return false;
        }
    }

    /**
     * 发送启动请求
     * @param unknown $resource
     * @return Requests_Response
     */
    protected function _doLaunch($resource){
        $url = $resource['launchurl'].'?'.http_build_query(['CsrfToken'=>$this->_options['cookies']->offsetGet('CsrfToken')->value,'launchId'=>time()]);
        $result = $this->_doRequest('/Citrix/StoreWeb/'.$url,[],'get');
        return $result;
    }

    /**
     * 发送请求
     * @param string | array $url
     * @param array | string $data
     * @param string $type post | get
     * @throws FatalErrorException
     */
    protected function _doRequest($url, $data = [], $type = 'post')
    {
        if (is_array($url)&&!empty($url)){
            $url = implode('/', $url);
        }
        $type = strtolower($type);
        if (!in_array($type, ['post','get'])){
            $type = 'post';
        }

        $_headers = $this->_headers;
        $_data = $data;
        $_options = $this->_options;

        //$response = Requests::post($this->_url.'/'.$url,$_headers,$_data,$_options);
         //try {
            if ($type == 'get'){
                $response = Requests::get($this->_url.$url,$_headers,$_options);
            }else{
                $response = Requests::post($this->_url.$url,$_headers,$_data,$_options);
            }
            if($response->cookies){
                if(isset($response->cookies['CsrfToken'])){
                    $this->_headers['Csrf-Token'] = $response->cookies['CsrfToken']->value;
                }
                $this->_options['cookies'] = $response->cookies;

}
/*             Log::info("_doRequest:headers:\n".$this->_url.$url);
            Log::info("_doRequest:headers:\n".json_encode($this->_headers)."\n");
            Log::info("_doRequest:data:\n".json_encode($data)."\n");
            Log::info($response); */
            return $response;
       // } catch (\Exception $e) {
        //    Log::write('error', 'Code:'.$e->getCode().'Msg:'.$e->getMessage());
        //    throw new FatalErrorException($e->getMessage(),$e->getCode());
       // }
    }

    /**
     * 获取接口中传递过来的参数。
     * 支持form提交以及body提交
     * @return Ambigous <unknown, string, multitype:>
     */
    private function _getData(){
        $data = $this->request->data?$this->request->data:file_get_contents('php://input', 'r');

        //处理非x-form的格式
        if (is_string($data)){
            $data_tmp = json_decode($data,true);
            if (json_last_error() == JSON_ERROR_NONE){
                $data = $data_tmp;
            }
        }
        //日志
        //Log::debug("Data Posted :".json_encode($data),['action'=>$this->request->params['action'],'host'=>$this->request->host()]);

        return $data;
    }

    /**
     * 获取操作信息
     * @param string $code
     * @param string $param
     * @return string
     */
    private function _getMsg($code = '0000',$param = ''){
        //extract
        return sprintf(Configure::read('MSG.'.$code),$param);
    }

    /**
     * 获取用户信息
     * @throws FatalErrorException
     * @return Ambigous <boolean, unknown>
     */

    /*
     * 修改记录
     *
     * 2015/12/22 密码修改为密文，以及增加salt后，对验证的更改
     */
    private function _getUser()
    {
        try {

            $account_table = TableRegistry::get('Accounts');
            $user = $account_table->find('all')->where([
                'loginname'=>$this->_data['loginname']
            ])->contain(['Departments'])->first();

            if (!$user){
                return false;
            }
            if ($this->_is_auth){
                $cmop_pwd_hasher = new CmopPasswordHasher(['salt'=>$user['salt']]);
                return $cmop_pwd_hasher->check($this->_data['password'], $user['password'])?$user:false;
            }else{
                return $user?$user:false;
            }

        } catch (\Exception $e) {
            throw new FatalErrorException($e->getMessage(),$e->getCode());
        }
    }

}
