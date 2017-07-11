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

class FicsController extends ConsoleController
{
    public function initialize()
    {
        parent::initialize();
        parent::left('network');//树形图导航

    }
    private $_popedomName = array(
        'fics' => 'ccm_ps_fics',
        'settinglist' => 'ccm_ps_fics_settinglist',
        'ficsHosts' => 'ccm_ps_fics_hosts'
    );
    private $_addPopedomName = array(
        'fics' =>'ccf_fics_new',
    );
    private function _checkPopedom($param)
    {
        $popedom = parent::checkConsolePopedom($param);
        return $popedom;
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
        $agents = $agent->find('all')->toArray();
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
            if(strtolower(($subject)=='settinglist')) {
                $request_data = $this->request->query;
                $fics_id = $request_data["vol_id"];
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
            }else if (strtolower($subject) =='ficshosts') {
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
            
            $data_charge = parent::_GetBillCycle(null);
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

}