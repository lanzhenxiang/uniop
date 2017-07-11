<?php

/**
 * class
 *
 * @author wangjincheng@sobey.com
 * @date 
 * @source PopedomlistController.php
 * @version 1.0.0
 * @copyright  Copyright 2015 sobey.com
 */
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

//TODO  交付生产环境时候此类应该继承AppController类，保证接口权限验证
class PopedomlistController extends AppController
{
    //接口属性
    public  $_db;
    private $_data = null;
    private $_code = 0;
    private $_msg = "";
    private $_serialize = array("code","msg","data");
    
    //数据库链接
    private $_db_conn = null;

    public function initialize()
    {
        parent::initialize();
        $this->_db = ConnectionManager::get('default');
        $this->viewClass = 'Json';
        
        $this->loadComponent('RequestHandler');
        
        // 获取参数
        $this->_data = $this->_getData();
    }
    
    /**
     * 获取用户权限
     */
    public function  getUserPopedomInfo(){
        if ($this->_data && is_array($this->_data)) {
            $code = "0";

            $request=$this->_data;
            // 根据userid获取租户（租户id）
            if (isset($request["userid"])) {
                $rolesAccounts = TableRegistry::get('RolesAccounts');
                $where['RolesAccounts.account_id'] = $request['userid'];
                $result = $rolesAccounts->find()->select(['popedomname'=>'popedomlist.popedomname'])->where($where)->join(
                    [
                        'rolesPopedoms'=>[
                            'table'=>'cp_roles_popedoms',
                            'type'=>'LEFT',
                            'conditions'=>'rolesPopedoms.role_id = RolesAccounts.role_id'
                        ],
                        'popedomlist'=>[
                            'table'=>'cp_popedomlist',
                            'type'=>'LEFT',
                            'conditions'=>'rolesPopedoms.popedomlist_id = popedomlist.popedomid'
                        ]
                    ]
                )->group('popedomlist.popedomname')->toArray();

                    foreach ($result as $info) {
                        $data[] = $info["popedomname"];
                    }

            } else {
                // post 数据空
                $code = "-1";
                $msg = "传入参数有误123";
                // $msg = $this->_getMsg($code);
            }
        } else{
            // post 数据空
            $code = '-1';
            $msg = "传入参数为空";
            
            // $msg = $this->_getMsg($code);
        }
        $this->set(compact(array_values($this->_serialize)));
        $this->set("_serialize",$this->_serialize);
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
}

?>