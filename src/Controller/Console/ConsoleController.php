<?php
/**
* 文件描述文字
*
*
* @author XingShanghe<xingshanghe@gmail.com>
* @date  2015年9月21日下午4:28:04
* @source ConsoleController.php
* @version 1.0.0
* @copyright  Copyright 2015 sobey.com
*/

namespace App\Controller\Console;

use App\Controller\AccountsController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;


class ConsoleController extends  AccountsController
{

    protected $_createResourceDeptId ;

    public function initialize()
    {
        parent::initialize();
        //RequestHandler
        $this->loadComponent('RequestHandler');
        $this->set('_console_category',$this->_get_category());
        $this->set('notifyUrl',Configure::read('NotifyUrl'));
        
        //生产页面唯一标示code
        
        $token = $this->getToken();
        $this->set('token', $token);
        //获取创建资源的租户id,(如果租户不具备跨租户权限，且未垮租户，则为当前登录用户的租户ID)
        $this->_createResourceDeptId = $this->getOwnByDepartmentId();
    }

    /**
     * 获取分类菜单数据
     */
    protected function _get_category()
    {
        $console_category = TableRegistry::get('ConsoleCategory');
        return $console_category->find('tree')->toArray();
    }

    // public function test()
    // {
    //     $console_category = TableRegistry::get('ConsoleCategory');

    //     debug($console_category->find('tree')->toArray());die;
    //     //$this->layout = false;
    //     //$this->autoRender = false;
    // }

    public function checkConsolePopedom($param)
    {
        $consolePopedom = parent::checkPopedomlist($param);
        return $consolePopedom;
    }

    //获bs价格
    protected static function getBsPriceById($id)
    {
        
        $goods_version_price_table = TableRegistry::get("GoodsVersionPrice");
        $price_data = $goods_version_price_table->find()->where(["id" => $id])->first();

        $data =array(
            'id' => $id,
            'price' => $price_data['price'],
            'unit' => $price_data['unit']
            );
        return $data;
    }

    //获取模板信息
    protected static function getSpecInfoById($id)
    {
        $goods_version_spec_table = TableRegistry::get("GoodsVersionSpec");
        $spec_data = $goods_version_spec_table->find()->where(["id" => $id])->first();
        $data["image"] = self::getImageByCode($spec_data["image_code"]);
        $data["instancetype"] = self::getConfigByCode($spec_data["instancetype_code"]);
        return $data;
    }

    public static function getConfigByCode($code)
    {
        $attribute = TableRegistry::get('SetHardware');
        $attribute = $attribute->find("all")->where(array('set_code' => $code))->first();
        if ($attribute->gpu_gb != 0 && !empty($attribute->gpu_gb)) {
            $str = $attribute->cpu_number . "核" . $attribute->memory_gb . "G-" . $attribute->gpu_gb . "MB (显存)";
        } else {
            $str = $attribute->cpu_number . "核" . $attribute->memory_gb . "G";
        }
        $data = array(
            'id' => $attribute->set_id,
            'name' => $attribute->set_name,
            'cpu' => $attribute->cpu_number,
            'memory' => $attribute->memory_gb,
            'gpu' => $attribute->gpu_gb,
            'gpu_type' => $attribute->gpu_type,
            'code' => $attribute->set_code, 'config' => $str);
        return $data;
    }

    public static function getImageByCode($code){
        $attribute                 = TableRegistry::get('Imagelist');
        $attribute = $attribute->find("all")->where(array('image_code'=>$code))->first();
        $data = array('id'=>$attribute->id,'name'=>$attribute->image_name,'code'=>$attribute->image_code);
        return $data;
    }

    public static function getNameByCode($code)
    {
        $attribute = TableRegistry::get('InstanceBasic');
        $attribute = $attribute->find("all")->where(array('code' => $code))->first();
        $data      = array('id' => $attribute->id, 'name' => $attribute->name, 'code' => $attribute->code);
        return $data;
    }
    //获取区域信息
    protected function getRegionInfoByCode($code)
    {
        $agent_table = TableRegistry::get("Agent");
        $agent_data = $agent_table->find()->where(["region_code" => $code])->first();
        $data      = array('id' => $agent_data->id, 'name' => $agent_data->agent_name, 'code' => $agent_data->agent_code);
        return $data;

    }
    
    private function getToken() {
        $time = uniqid();
        $token = $time . rand(0000, 9999);
        // $token = md5(md5($token) + rand(0000, 9999));
        
        return $token;
    }

    /**
     * 跨租户创建资源时，切换租户id
     * @return [type] [description]
     */
    protected function _switchDepartment()
    {
        //是否有跨租户权限
        if($this->checkConsolePopedom('ccf_all_select_department')){
            $department_id = $this->request->data('create_department_id');
            if($department_id > 0){
                $departments = TableRegistry::get('Departments');
                $department  = $departments->findById($department_id)->first();
                
                if($department instanceof \Cake\ORM\Entity){
                    $this->request->session()->write('Auth.User.create_department_id',$department->id);
                    $this->request->session()->write('Auth.User.create_department_name',$department->name);
                    return $department;
                }
            }
        }
        return false;
    }

    /**
     * 跨租户创建资源时，切换租户id
     * @return [type] [description]
     */
    public function switchDepartment(){
        $department = $this->_switchDepartment();
        if($this->request->is('ajax')){
            if(false != $department){
                $code = '0';
                $msg  = '切换租户成功';
                $department_name = $department->name;
            }else{
                $code = '1';
                $msg  = '切换租户失败';
                $data = '';
            }
            echo json_encode(compact(['code','msg','department_name']));exit;
        }else if($this->request->is('post')){
            if(false != $department){
                return $this->redirect($this->request->data('callback_url'));
            }
        }
        return $this->redirect($this->referer());
    }

    /**
     * 创建资源时，获取创建的当前资源，所属租户id
     * @return int 租户id
     */
    public function getOwnByDepartmentId()
    {
        //如果租户有跨租户权限，则获取切换到的租户id用于创建资源
        if($this->checkConsolePopedom('ccf_all_select_department') && null != $this->request->session()->read('Auth.User.create_department_id')){
            return $this->request->session()->read('Auth.User.create_department_id');
        }else{
            return $this->request->session()->read('Auth.User.department_id');
        }
    }
    /**
     * 判断创建资源的租户是否为当前租户
     * @return json 
     */
    public function isCurrentDepartment(){
        $department_id = $this->getOwnByDepartmentId();
        if($department_id != $this->request->session()->read('Auth.User.department_id')){
            $msg = "创建资源租户不是当前租户";
            $code = '1';
        }else{
            $msg = '创建资源租户为当前租户';
            $code = '0';
        }
        echo json_encode(compact(['code','msg']));exit;
    }
}