<?php
/**
* 索贝cmop控制器基类
*
*
* @author XingShanghe<xingshanghe@gmail.com>
* @date  2015年9月6日上午11:36:40
* @source SobeyController.php
* @version 1.0.0
* @copyright  Copyright 2015 sobey.com
*/

namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;
use Cake\Error\FatalErrorException;


class SobeyController extends AppController
{
    public function initialize(){
        parent::initialize();

        //加载 Cookie组件
        $this->loadComponent('Cookie',[
                'encryption' => false
        ]);
        $this->set('system_info',Configure::read('System'));
        $this->set('_request_params',$this->request->params);
        $this->set('notifyUrl',Configure::read('NotifyUrl'));
    }


    /**
     * 公共js变量
     */
    public function common_js(){
        $this->layout = false;
        $this->autoRender = false;
        $js = '';
        $js.='SEARCH_GLOBAL_URL="'.Router::url(['controller'=>'Search','action'=>'index']).'";';
        //$js.='SEARCH_EXPANDED_URL="'.Router::url(['controller'=>'Search','action'=>'getExpanded']).'";';
        header("Content-Type:application/javascript");
        echo $js;
    }

    /**
     * @func:删除购物车Cookie
     * @param:
     * @date: 2015年9月24日 下午4:37:26
     * @author: shrimp liao
     * @return: null
     */
    public function delCarCookie($index)
    {
        return  $this->Cookie->delete($index);
    }

    /**
     * @func: 读取Cookie（反序列化->反Base64）
     * @param:
     * @date: 2015年9月24日 下午5:11:09
     * @author: shrimp liao
     * @return: null
     */
    public function readCookie($name){
        $goods_cars=$this->Cookie->read($name);
        if($goods_cars){
            $unser_goods= unserialize(base64_decode($goods_cars));
            return $unser_goods;
        }else{
            return false;
        }

    }

    /**
     * @func:读取Cookie中的number数量
     * @param:
     * @date: 2015年10月8日 上午10:45:10
     * @author: shrimp liao
     * @return: null
     */
    public function readCookieByNumber()
    {
        $number=0;
        if ($this->Cookie->read('user.car')) {
            $unser_goods = $this->readCookie('user.car');
            $number=count($unser_goods);
        }
        return $number;
    }


    public function readGoodsList($index)
    {
        $goodsList = Configure::read('Goods_Fixed');
        $goodsList = array_flip($goodsList);
        return $goodsList[$index];
    }




    /**
     * 获取 code为键,值包含name的maps
     * 获取 instance-basic  code-name map
     * @author xingshanghe
     * @param string $type
     */
    protected function _getCodeNameMaps($type = null)
    {
        if (!is_null($type)){
            return Cache::remember('_code_name_maps_'.$type, function () use ($type){
                //获取菜单模型
                $instance_basic_table = TableRegistry::get('InstanceBasic');

                $_results = $instance_basic_table->find()->where(['type'=>$type])->select(['id','name','type','code'])->toArray();
                $_maps = [];

                if(!empty($_results)){
                    foreach ($_results as $_result){
                        $_maps[$_result['code']] = $_result;
                    }
                }

                 return json_decode(json_encode($_maps),true);
            },'memcache_code_name_maps');
        }else{
            return Cache::remember('_code_name_maps', function (){
                //设置各个分类缓存
                //获取菜单模型
                $instance_basic_table = TableRegistry::get('InstanceBasic');
                $_results = $instance_basic_table->find()->where([])->select(['id','name','type','code'])->toArray();

                $_maps = [];
                if(!empty($_results)){
                    foreach ($_results as $_result){
                        if ($_result['code']){
                            $_maps[$_result['type']][$_result['code']] = $_result;
                        }
                    }
                }
                if (!empty($_maps)){
                    foreach ($_maps as $type => $_map){
                         Cache::write('_code_name_maps_'.$type, json_decode(json_encode($_map),true),'memcache_code_name_maps');
                    }
                }
                return json_decode(json_encode($_maps),true);
            },'memcache_code_name_maps');
        }
    }


    /**
     * 根据code获取name
     * @author xingshanghe
     * @param string $type
     * @param string $name
     * @throws FatalErrorException
     */
    protected function _getNameByCode($type = null,$name= null)
    {
        if ((!is_null($type))&&(!is_null($name)))
        {
            return Cache::remember('_code_name_maps_'.$type.'_'.$name, function () use ($type,$name){

                $type_maps = $this->_getCodeNameMaps($type);
                return isset($type_maps[$name]['name'])?$type_maps[$name]['name']:false;

            },'memcache_code_name_maps');
        }else{
            throw new FatalErrorException('unpassed params "type" or "name"');
        }
    }


    /**
     *
     * 获取 name为键,值包含code的maps
     * 获取 instance-basic  code-name map
     * @author xingshanghe
     * @param string $type
     */
    protected function _getNameCodeMaps($type = null)
    {
        if (!is_null($type)){
            return Cache::remember('_name_code_maps_'.$type, function () use ($type){
                //获取菜单模型
                $instance_basic_table = TableRegistry::get('InstanceBasic');

                $_results = $instance_basic_table->find()->where(['type'=>$type])->select(['id','name','type','code'])->toArray();
                $_maps = [];

                if(!empty($_results)){
                    foreach ($_results as $_result){
                        $_maps[$_result['name']] = $_result;
                    }
                }

                return json_decode(json_encode($_maps),true);
            },'memcache_name_code_maps');
        }else{
            return Cache::remember('_name_code_maps', function (){
                //设置各个分类缓存
                //获取菜单模型
                $instance_basic_table = TableRegistry::get('InstanceBasic');
                $_results = $instance_basic_table->find()->where([])->select(['id','name','type','code'])->toArray();

                $_maps = [];
                if(!empty($_results)){
                    foreach ($_results as $_result){
                        if ($_result['name']){
                            $_maps[$_result['type']][$_result['name']] = $_result;
                        }
                    }
                }
                if (!empty($_maps)){
                    foreach ($_maps as $type => $_map){
                        Cache::write('_name_code_maps_'.$type, json_decode(json_encode($_map),true),'memcache_name_code_maps');
                    }
                }
                return json_decode(json_encode($_maps),true);
            },'memcache_name_code_maps');
        }
    }

    /**
     * 设备根据name获取code
     * @author xingshanghe
     * @param unknown $type
     * @param unknown $code
     * @throws FatalErrorException
     */
    protected function _getCodeByName($type = null,$code= null)
    {
        if ((!is_null($type))&&(!is_null($code)))
        {
            return Cache::remember('_code_name_maps_'.$type.'_'.$code, function () use ($type,$code){

                $type_maps = $this->_getNameCodeMaps($type);
                return isset($type_maps[$code]['code'])?$type_maps[$code]['code']:false;

            },'memcache_code_name_maps');
        }else{
            throw new FatalErrorException('unpassed param "type" or "name"');
        }
    }

    /**
     * 根据id获取帐号信息，包含部门信息(已经缓存)
     *
     * @author xingshanghe
     * @param integer $id
     * @throws FatalErrorException
     */
    protected function _getAccountsInfoById($id = null){
        if (!is_null($id)){
            return Cache::remember('_account_maps_id_'.$id, function () use ($id){
                //获取菜单模型
                $accounts_table = TableRegistry::get('Accounts');

                return $accounts_table->find()->where(['Accounts.id'=>$id])->contain(['Departments'])->first();

            },'memcache_account_maps');
        }else {
            throw new FatalErrorException('unpassed param "id"');
        }
    }

    /**
     * 根据loginname获取帐号信息，包含部门信息(已经缓存)
     *
     * @author xingshanghe
     * @param string $loginname
     * @throws FatalErrorException
     */
    protected function _getAccountsInfoByLoginname($loginname = null){
        if (!is_null($loginname)){
            return Cache::remember('_account_maps_loginname_'.$loginname, function () use ($loginname){
                //获取菜单模型
                $accounts_table = TableRegistry::get('Accounts');

                return $accounts_table->find()->where(['Accounts.loginname'=>$loginname])->contain(['Departments'])->first();

            },'memcache_account_maps');
        }else {
            throw new FatalErrorException('unpassed param "username"');
        }
    }


    protected function _getDepartmentInfoById($id= null){
        if (!is_null($id)){
            return Cache::remember('_department_maps_id_'.$id, function () use ($id){
                //获取菜单模型
                $departments_table = TableRegistry::get('Departments');

                return $departments_table->find()->where(['Departments.id'=>$id])->first();

            },'memcache_account_maps');
        }else{
            throw new FatalErrorException('unpassed param "id"');
        }
    }

    protected function _GetBillCycle($index=null){
        $goodsList = Configure::read('billCycle');
        // $goodsList = array_flip($goodsList);
        if(!is_null($index)){
            return $goodsList[$index];
        }else{
            return $goodsList;
        }
    }
}