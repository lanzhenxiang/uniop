<?php
/**
* citrix登陆信息同步接口
*
*
* @author lan
* @version 1.0.0
* @copyright  Copyright 2016 sobey.com
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
use \Requests as Requests;

class CitrixRetrieveController extends AppController
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

        $loader = new ClassLoader();
        $loader->add('Requests', ROOT . DS . 'vendor/requests');
        $loader->register();
        Requests::register_autoloader();

        //$this->_initRequest();//初始化request对象以及配置信息
    }

    public function retrieve(){
        $bill_citrix_table = TableRegistry::get('BillCitrix');
        $success_ids = array();
        $dataList = $this->_getCitrixLoginData();

        if(empty($dataList) || !is_array($dataList)){
            $code = '0';
            $msg  = '没有取回记录';
            
            $this->set(compact(array_values($this->_serialize)));
            $this->set('_serialize',$this->_serialize);
        }else{
            foreach ($dataList as $key => $value) {
                $data = array();
                $data['duration'] = $value['duration'];
                $data['logintime'] = $value['login_time'];
                $data['logoutime'] = $value['logout_time'];
                $data['loginname']  = $value['loginname'];
                $data['basic_id']   = $value['basic_id'];
                $data['login_id']   = $value['id'];
                $data['name']       = $value['host_disp_name'];

                $entity = $bill_citrix_table->newEntity();
                $entity = $bill_citrix_table->patchEntity($entity,$data);
                if($bill_citrix_table->save($entity)){
                    $success_ids[] = $value['id'];
                }
            }
            $this->_sendSuccess($success_ids);
            $this->retrieve();
        }
    }


    public function _sendSuccess($success_ids){
        $ids_string = implode(",", $success_ids);
        //日志记录更新请求
        Log::info("Citrix saved ids :".$ids_string);
        
        $response = Requests::post(Configure::read('Api.vboss').'/Desktops/updateLoginData',[],
           [
           'id_lists'=>$ids_string,
           ],['verify'=>false]);
        $body = json_decode(trim($response->body,chr(239).chr(187).chr(191)),true);
        $msg = $body['msg'];
        $code = $body['code'];
        //日志记录
        Log::info("Citrix saved ids return:".json_encode($body));
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }

    public function _getCitrixName($basic_id){
        $instance_table = TableRegistry::get("InstanceBasic");
        $entity = $instance_table->find()->where(['id'=>$basic_id])->select(['name'])->first();
        return $entity->name;
    }

    public function _getCitrixLoginData(){
        $response = Requests::post(Configure::read('Api.vboss').'/Desktops/getLoginData',[],
           [
           'is_compute'=>0,
           ],['verify'=>false]);
        $body = json_decode(trim($response->body,chr(239).chr(187).chr(191)),true);
        $dataList = $body['data'];
        //日志记录更新请求
        Log::info("retrieve citrix login data from ".Configure::read('Api.vboss').'/Desktops/getLoginData',$dataList);
        return $dataList;
    }

}
