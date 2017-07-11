<?php
/**
* Citrix类，获取ica文件
*
* @file: Citrix.php
* @date: 2016年4月8日 下午4:55:38
* @author: xingshanghe
* @email: xingshanghe@icloud.com
* @copyright poplus.com
*
*/

namespace App\Controller\Api\Utility;

use Composer\Autoload\ClassLoader;
use Cake\Log\Log;
use \Requests as Requests;
use Cake\Error\FatalErrorException;
use Cake\Utility\Xml;

class Citrix
{
    protected $_url;
    protected $_aduser;
    protected $_headers;
    protected $_options;
    protected $_is_https;

    public function __construct( $url,$ad )
    {
        $loader = new ClassLoader();
        $loader->add('Requests', ROOT.DS.'vendor/requests');
        $loader->register();
        Requests::register_autoloader();

        $this->_url = $url;
        $this->_is_https = parse_url($url,PHP_URL_SCHEME) == 'https'?true:false;
        $this->_aduser = $ad;
    }

    /**
     * 初始化Requests类
     */
    private function _initRequests()
    {
        $this->_headers['Accept-Language'] = 'zh-cn,zh;q=0.5';
        $this->_headers['Accept-Charset'] = 'utf-8,GB2312;q=0.7,*;q=0.7';

        $this->_headers['X-Citrix-IsUsingHTTPS'] = $this->_is_https?'Yes':'No';

        $this->_options['verify'] = false;
        $this->_options['timeout'] = 10;
        $this->_options['cookies'] = [];
    }


    /**
     * 获取ica文件
     * @param unknown $name
     * @return boolean
     */
    public function ica( $name )
    {
        
        $this->_initRequests();
        $resource = $this->_init($name);

        if (!is_scalar($resource)){

            if ($this->_doPreLaunch($resource)){
                $response = $this->_doLaunch($resource);

                if (isset($response->success)&&($response->success == true)){

                    $result=array(
                        'code'=>0,
                        'msg'=>"操作成功",
                        'data'=>$response->body
                    );
                    return json_encode($result);
                    //return;
                }else{
                    //启动失败
                    Log::error(__LINE__.':'.__FUNCTION__);
                    if($response->body){
                        Log::error($response->body);
                    }
                    else {
                        Log::error($response);
                    }
                    $this->_errorExit(7007,'获取ica失败，_doLaunch');
                }
            }else{
                //预启动失败
                 $this->_errorExit(70051,'获取ica失败，_doPreLaunch');
            }

        }else{
            //init方法失败
            $this->_errorExit(70052,'初始化失败');
        }
    }

    private function _init( $name )
    {
        //https下ad账号的校验
        if ($this->_is_https){
            try {
                $response = $this->_doRequest('/cgi/login',[
                    'login'=>$this->_aduser['username'],
                    'passwd'=>$this->_aduser['password'],
                ]);

                if (!($response->success)){
                    Log::error('/cgi/login returned error.params:'.json_encode([
                        'login'=>$this->_aduser['username'],
                        'passwd'=>$this->_aduser['password'],
                    ]));
                    //登录citrix失败
                    $this->_errorExit(7001,'登录citrix失败，请管理员检查citrix地址，账号，密码');

                }
            } catch (\Exception $e) {
                Log::error($this->_url.'/cgi/login');
                
                
                $msg=$e->getMessage();
                Log::error($msg);
                //登录citrix异常
                $this->_errorExit(7002,"登录citrix出现异常，请管理员检查citrix地址，账号，密码,$msg");
            }
        }

        //获取认证方法
        //根据配置选项：ExplicitForms|CitrixAuth）
            $response = $this->_doRequest('/Citrix/StoreWeb/Authentication/GetAuthMethods');
            if($response)
            {
                    if (!$response->body){
                        Log::error('response body不存在 do /Citrix/StoreWeb/Authentication/GetAuthMethods login error');
                        //获取认证方法失败
                        $this->_errorExit(7003,"返回数据不合法，请联系管理员");
                        //return false;
                    }
            }
            else {
                $this->_errorExit(7014,"访问网关失败，请检查citrix地址");
            
            }

        $response_body = Xml::build($response->body);
        $response_body_arr = json_decode(json_encode($response_body),TRUE);
        if(!$response_body_arr)
            $this->_errorExit(7015,"citrix返回数据不合法".$this->_url);
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
                $resource = 70061;
                $bfind=false;
                foreach ($response_body['resources'] as $value){
                    if (isset($value['isdesktop'])&&($value['isdesktop']===true)
                        &&(strcasecmp($name,$value['name'])==0)){
                        $resource = $value;
                        $bfind=true;
                        break;
                    }
                }
                if(!$bfind)
                {
                    $this->_errorExit(70061,"机器名在citrix的资源列表中没找到，请检查机器名是否正确");
                }
                return $resource;
            }else{
                Log::error('some error occured when do /Citrix/StoreWeb/'.$authLoginUrl.(isset($response->body)?"\n".$response->body:''));
                //获取资源列表失败
                $this->_errorExit(70041,"citrix列表资源格式不正确");
            
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
                        $post['data'][$key] = isset($this->_aduser[$key])?$this->_aduser[$key]:'';
                    }
                    // Log::info("\n ad帐号\n".json_encode($this->_aduser).json_decode($post['keys']));
                }
                //提交数据认证登陆
                $response = $this->_doRequest('/Citrix/StoreWeb/'.$post['urls']['PostBack'],$post['data']);

                //获取认证的id
                $response_body = Xml::build($response->body);
                $response_body_arr = json_decode(json_encode($response_body),true);
                //登录成功失败判断不准确
                //登录。认证成功
                $resource=70062;
                if (('success' === strtolower($response_body_arr['Result']))&&isset($response_body_arr['AuthType'])&&(in_array($response_body_arr['AuthType'], ['ExplicitForms','CitrixAuth'])) ){
                    //获取资源列表

                    $response = $this->_doRequest('/Citrix/StoreWeb/Resources/List');
                    $response_body = json_decode($response->body,true);
                    $bfind=false;
                    foreach ($response_body['resources'] as $value){
                         
                            if (isset($value['isdesktop'])&&($value['isdesktop']===true)&&(strcasecmp($name,$value['name'])==0)){
                                $resource = $value;
                                $bfind=true;
                                break;
                            }
                        }
                    
                        if(!$bfind)
                        {
                            $this->_errorExit(70062,"机器名在citrix的资源列表中没找到，请检查机器名是否正确");
                        }
                        return $resource;                    

                }else{
                    //登录失败
                    Log::error('some error occured when do /Citrix/StoreWeb/'.$post['urls']['PostBack'].(isset($response->body)?"\n".$response->body:''));
                    //获取资源列表失败
                    $this->_errorExit(70042,"citrix列表资源格式不正确");
                }

            }else{
                Log::error('some error occured when do /Citrix/StoreWeb/'.$authLoginUrl.(isset($response->body)?"\n".$response->body:''));
                //认证失败
                $this->_errorExit(70011,"返回数据不正确");
            }
        }


    }

    /**
     * 发送预启动请求
     * @param unknown $resource
     * @return boolean
     */
    protected function _doPreLaunch( $resource )
    {
        $response = $this->_doRequest('/Citrix/StoreWeb/'.$resource['launchstatusurl']);
        $response_body = json_decode($response->body,true);
        if(isset($response_body['status'])&&('success'===strtolower($response_body['status']))){
            return  true;
        }else{
             Log::error("文件名=".__FILE__."行号=".__LINE__.':'.__FUNCTION__);
            if($response->body){
                Log::error($response->body);
            }
            else {
                Log::error($response);                
            }
            //预启动失败
            
            $this->_errorExit(7005,"返回数据不正确，_doPreLaunch");
            
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
        //启动失败
        return $result;
    }


    /**
     * 发送请求
     * @param unknown $url
     * @param array $data
     * @param string $type
     * @throws FatalErrorException
     */
    private function _doRequest($url,$data=[],$type='post') {

        $type = strtolower($type);

        if (!in_array($type, ['post','get'])){
            $type = 'post';
        }

        try {

          //  Log::info($this->_url.$url);

            if ($type == 'get'){
                $response = Requests::get($this->_url.$url,$this->_headers,$this->_options);
            }else{
                $response = Requests::post($this->_url.$url,$this->_headers,$data,$this->_options);
            }

            if ($response->cookies){
                if (isset($response->cookies['CsrfToken'])){
                    $this->_headers['Csrf-Token'] = $response->cookies['CsrfToken']->value;
                }
                $this->_options['cookies'] = $response->cookies;
            }

         //   Log::info("headers:\n".json_encode($this->_headers)."\n");
         //   Log::info("data:\n".json_encode($data)."\n");

            return $response;
        } catch (\Exception $e) {
            $msg=$e->getMessage();
            
            $citrix_url=$this->_url.$url;
            Log::error("调用发生了异常，地址是：$citrix_url");
            Log::error('Code:'.$e->getCode().'.Msg:'.$msg);
            $msg.="(url=$this->_url)";
            $this->_errorExit(5005,$msg);
           
        }

    }
    protected function _errorExit($code,$msg='参数错误',$data="")
    {
        if(intval($code)>0){
            $lastmsg="【errcode=".$code." from cmop】".$msg;
            $result=array(
                'code'=>$code,
                'msg'=>$lastmsg,
                'data'=>$data
            );
            echo json_encode($result);
            exit;
        }
        else
            return true;
    
    }


}