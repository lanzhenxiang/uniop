<?php
/**
 * 云桌面站点首页
 *
 * 包含
 *
 * @author XingShanghe<xingshanghe@gmail.com>
 * @date  2015年11月5日上午10:19:11
 * @source HomeController.php
 * @version 1.0.0
 * @copyright  Copyright 2015 sobey.com
 */
namespace App\Controller\Xdesktop;


use App\Controller\AccountsController;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Error\FatalErrorException;
use Cake\Network\Http\Client;
use Cake\Core\Configure;
use Cake\Log\Log;
use Composer\Autoload\ClassLoader;
use Requests as Requests;
use Cake\Filesystem\File;
use Cake\Controller\Controller;
use App\Controller\HomeController as Home;

class HomeController extends AccountsController
{

    //private $_http;

    public function initialize()
    {
        parent::initialize();
        //$this->db_conn = ConnectionManager::get('default');
        //$this->_http  = new Client(['scheme'=>'https','ssl_verify_peer'=>false]);

        $loader = new ClassLoader();
        $loader->add('Requests', ROOT.DS.'vendor/requests');
        $loader->register();
        Requests::register_autoloader();

        $this->Auth->allow(['cer']);
    }
    /*
     * 默认主页函数
     */
    public function index() {
        $this->layout = 'xdesktop';
        $_useable_lists = [];

        $account_table = TableRegistry::get('Accounts');
        $user = $account_table->find()->where(['id'=>$this->request->session()->read('Auth.User.id')])->first();
        /*
        $response = $this->_http->post(Configure::read('Api.cmop').'/citrix/getUseableList',[
            'username'=>$user['loginname'],
            'password'=>$user['password'],
        ]);
        */
        $response = Requests::post(Configure::read('Api.cmop').'/citrix/getUseableList',[],[
            'loginname'=>$user['loginname'],
            'password'=>$user['password'],
        ],[
            'verify'=>false
        ]);

        // $response_arr = json_decode($response->body,true);
        $response_arr = json_decode(trim($response->body, chr(239) . chr(187) . chr(191)), true); //所以问题来了，不小心在返回的json字符串中返回了BOM头的不可见字符，某些编辑器默认会加上BOM头，如下处理才能
        //json_last_error() == JSON_ERROR_NONE
        if ((json_last_error() == JSON_ERROR_NONE) && ($response_arr['code'] == '0000')){
            $_lists = $response_arr['data'];
        }else{
            throw new FatalErrorException($response_arr['msg'],$response_arr['code']);
        }

        $_useable_lists = [];
        $_code_list = [];
        $_vpc_list = [];
        if (!empty($_lists)){
            foreach ($_lists as $key => $value ){
                $_useable_lists[$value['software_code']][] = $value;
                $_code_list[] = $value['hostname'];
                if(!isset($_vpc_list[$value['vpc']])){
                    $_vpc_list[$value['vpc']] = $value['code'];
                }
            }
        }

        //查询所有vpc
        $vpc_table = TableRegistry::get('InstanceBasic');
        $vpc_name_list = $vpc_table->find()->select(['name','code'])->where(['type'=>'vpc'])->toArray();

        $vpcs = [];

        if (!empty($vpc_name_list)){
            foreach ($vpc_name_list as $_k => $_v){
                $vpcs[$_v['code']] = $_v['name'];
            }
        }

        $this->set('vpcs',$vpcs);

        $this->set('_useable_lists',$_useable_lists);
        $soft_list = $this->_getSoftList();

        $icons_list = [];
        foreach ($soft_list as $list ){
            $icons_list[$list['software_code']] = $list['icon_file'];
        }


        $tempUser = "xdesktop".time();
        //未tempUser定义机器的消息
        $topics = implode(',',array_unique($_code_list));


        if (is_string(Configure::read('NotifyUrl'))){
            $response_notify_obj = @Requests::post(Configure::read('NotifyUrl').'/subscribe',[],[
                'sign'=>md5($topics.Configure::read('NotifyKey').$tempUser),
                'uid'=>$tempUser,
                'topics'=>$topics
            ]);
        }elseif(is_array(Configure::read('NotifyUrl'))){
            $urls = Configure::read('NotifyUrl');
            foreach ($urls as $url){
                try {
                    $response_notify_obj = @Requests::post($url.'/subscribe',[],[
                        'sign'=>md5($topics.Configure::read('NotifyKey').$tempUser),
                        'uid'=>$tempUser,
                        'topics'=>$topics
                    ]);
                } catch (\Exception $e) {
                    continue;
                }

            }
        }


        $_home = new Home();

        //print_r($topics);
        $this->set('good_category',$_home->getCategoeyGoodsData());
        $this->set('tempUser',$tempUser);
        $this->set('soft_lists',$soft_list);
        $this->set('icons_list',$icons_list);
        $this->set('vpc_list',$_vpc_list);
    }


    /**
     * 证书下载
     * @param string $hostname 主机名
     */
    public function cer($code,$vpc){

        $code = urldecode(pathinfo($code,PATHINFO_FILENAME));
        $vpc = urldecode(pathinfo($vpc,PATHINFO_FILENAME));

        $this->autoRender = false;

        //根据vpc code 查询
        //获取基础实例表
        $instabce_basic_table = TableRegistry::get('InstanceBasic');

        $obj_vpc = $instabce_basic_table->find()->where([
            'InstanceBasic.code'=>$vpc,
            'InstanceBasic.type'=>'vpc',
        ])->contain(['VpcExtend'])->first();
        //补充citrix server url..理论上是此字段不应该缺少的
        if ((!empty($obj_vpc->vpc_extend))&&!empty($obj_vpc->vpc_extend->desktop_server_url)&&!empty($obj_vpc->vpc_extend->cer_url)){

            header('Content-Type:application/x-x509-ca-cert');
            echo $obj_vpc->vpc_extend->cer_url;exit;
        }else{

            $http = new Client();

            $obj_response = $http->post(Configure::read('URL'),json_encode([
                "method"=>"desktop_login",
                "uid"=>strval($this->request->session()->read('Auth.User.id')),
                "desktopCode"=>$code,
            ]),['type' => 'json']);
            $response = json_decode($obj_response->body,true);

            if ($response['Code'] == 0){
                try {
                    $obj_response = Requests::get($response['Data']['cerUrl']);

                    //TODO 保存至本地并更新 $obj_vpc对象
                    $file = new File(WWW_ROOT.'home/cer/'.$code.'/'.$vpc.'.cer');
                    $file->write($obj_response->body);
                    $file->close();

                    //保存对象
                    //获取extend对象
                    $vpc_extend_table = TableRegistry::get('VpcExtend');
                    $obj_vpc->vpc_extend->cer_url = $obj_response->body;

                    try {
                        $vpc_extend_table->save($obj_vpc->vpc_extend);
                    } catch (\Exception $e) {
                        throw new FatalErrorException($e->getMessage());
                    }
                    /*
                    $obj_vpc = $instabce_basic_table->VpcExtend->patchEntity($obj_vpc->vpc_extend, [
                            'desktop_server_url'=>$response['Data']['citrixDesktopDomain'],
                            'cer_url'=>$response['Data']['cerUrl']
                    ]);
                    try {
                        $instabce_basic_table->save($obj_vpc);
                    } catch (\Exception $e) {
                        throw new FatalErrorException($e->getMessage());
                    }
                    */
                    header('Content-Type:application/x-x509-ca-cert');
                    echo $obj_response->body;exit;
                } catch (\Exception $e) {
                    throw new FatalErrorException($e->getMessage());
                }


            }else{
                throw new FatalErrorException($response['Message']);
            }
        }

    }

    /**
     * 获取桌面类型
     * @param array $options
     */
    protected function _getSoftList($options = []) {
        $results = [];
        //获取桌面软件列表
        $conn = ConnectionManager::get('default');

        $sql = "SELECT DISTINCT(slist.software_name),slist.software_code,slist.icon_file "
                ." FROM cp_instance_basic basic,cp_host_extend desktop,cp_software_list slist,cp_softwares_desktop sdesktop,cp_roles_accounts role,cp_roles_software srole,cp_accounts accounts"
                ." where role.account_id= ".$this->request->session()->read('Auth.User.id')." and basic.id=desktop.basic_id and "
                ." desktop.id=sdesktop.host_id and sdesktop.software_id=slist.id "
                ." and role.role_id=srole.role_id and srole.software_id=slist.id "
                ." and accounts.id=role.account_id and accounts.department_id=basic.department_id";
        $data = $conn->execute($sql)->fetchAll('assoc');
        return $data;
    }


    protected function _getList($options = [])
    {
        $results = [];

        return $results;
    }
}