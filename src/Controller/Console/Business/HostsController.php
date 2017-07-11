<?php
/**
 * 文件描述文字
 *
 *
 * @author XingShanghe<xingshanghe@gmail.com>
 * @date  2015年10月8日下午5:21:24
 * @source HostsController.php
 * @version 1.0.0
 * @copyright  Copyright 2015 sobey.com
 */
namespace App\Controller\Console\Business;

use App\Controller\Console\ConsoleController;
use App\Controller\OrdersController;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Requests as Requests;

class HostsController extends ConsoleController
{

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }

    private $_serialize = array(
        'code',
        'msg',
        'data',
    );

    public $_pageList = array(
        'total' => 0,
        'rows'  => array(),
    );

    /**
     * [getBusinessTemplateDetail 通过模板id获取资源清单]
     * @param  [type] $request_data [description]
     * @return [type]               [description]
     */
    public function getBusinessTemplateDetail($request_data){
        $biz_tid = $request_data['biz_tid'];
        if($biz_tid < 1){
            return array();
        }
        $business_template        = TableRegistry::get('BusinessTemplate');
        $template_detail = $business_template->find()->where(['BusinessTemplate.biz_tid'=>$biz_tid])->first()->toArray();
        return $template_detail;
    }
    /**
     * [getTemplateDetail 获取业务模板资源清单]
     * @param  [type] $request_data [description]
     * @return [type]               [description]
     */
    public function getTemplateDetail($request_data){
        $biz_tid = $request_data['biz_tid'];
        if($biz_tid < 1){
            return array();
        }
        $business_template_detail        = TableRegistry::get('BusinessTemplateDetail');
        $detailList = $business_template_detail->find()->where(['biz_tid'=>$biz_tid])->contain(['SetHardware'])->map(function($rows){
            $rows['cpu_sub_total'] = $rows['number'] * $rows['set_hardware']['cpu_number'];
            $rows['rom_sub_total'] = $rows['number'] * $rows['set_hardware']['memory_gb'];
            return $rows;
        });

        $this->_pageList['total'] = $business_template_detail->find()->where(['biz_tid'=>$biz_tid])->count();
        $this->_pageList['rows']  = $detailList;

        return $this->_pageList;
    }

    /**
     * 获取列表数据,
     * 新增关联查询
     */
    public function lists($request_data = array())
    {
        $limit  = $request_data['limit'];
        $offset = $request_data['offset'];
        $where  = ' AND a.isdelete = 0';

        if (!empty($request_data['department_id'])) {
            $where .= ' AND a.department_id = ' . $request_data['department_id'];
        }
        if (isset($request_data['search'])) {
            if ($request_data['search'] != "") {
                $where .= ' AND (a.name like\'%' . $request_data['search'] . '%\' OR a.code like\'%' . $request_data['search'] . '%\' OR c.ip like\'%' . $request_data['search'] . '%\' OR d.ip like\'%' . $request_data['search'] . '%\')';
            }
        }

        if (isset($request_data['search_biz'])) {
            if ($request_data['search_biz'] != "") {
                $where .= ' AND (t.biz_temp_name like\'%' . $request_data['search_biz'] . '%\')';
            }
        }

        if (!empty($request_data['class_code'])) {
            $where .= ' AND a.location_code like\'' . $request_data['class_code'] . '%\'';
        }
        if (!empty($request_data['class_code2'])) {
            $where .= ' AND a.location_code like\'' . $request_data['class_code2'] . '%\'';
        } elseif (!empty($request_data['class_code'])) {
            $where .= ' AND a.location_code like\'' . $request_data['class_code'] . '%\'';
        }
        $connection = ConnectionManager::get('default');
        $sql        = ' SELECT ';
        $sql .= ' a.id AS \'H_ID\', ';
        $sql .= ' a.`code` as \'H_Code\', ';
        $sql .= ' a.name as \'H_Name\', ';
        $sql .= ' a.location_code as \'H_L_Code\', ';
        $sql .= ' a.status as \'H_Status\', ';
        $sql .= ' a.description \'H_Description\', ';
        $sql .= ' a.create_time as \'H_time\', ';
        $sql .= ' a.department_id as \'H_department\', ';
        $sql .= ' a.vpc as \'F_Code\', ';
        $sql .= ' c.`bindcode` AS \'E_BindCode\', ';
        $sql .= ' c.ip as \'E_Ip\', ';
        $sql .= ' d.os_family AS \'D_Os_Form\', ';
        $sql .= ' d.plat_form AS \'D_Plat_form\', ';
        $sql .= ' d.isFusion as \'D_isFusion\', ';
        $sql .= ' d.cpu as \'D_Cpu\', ';
        $sql .= ' d.memory as \'D_Memory\', ';
        $sql .= ' d.gpu as \'D_Gpu\', ';
        $sql .= ' d.vnc_password as \'D_Vnc_password\', ';
        $sql .= ' e.display_name as \'E_Name\', ';
        $sql .= ' t.biz_temp_name as \'T_Name\', ';
        $sql .= ' t.version as \'T_Version\', ';
        $sql .= ' t.system_level as \'T_System_level\', ';
        $sql .= ' (SELECT GROUP_CONCAT(i.subnet_code) ';
        $sql .= ' FROM cp_hosts_network_card i ';
        $sql .= ' WHERE i.basic_id = a.`id` AND i.network_code !="" ';
        $sql .= ' ) as \'I_SubnetCode\', ';
        $sql .= ' (SELECT GROUP_CONCAT(s.fusionType)';
        $sql .= ' FROM cp_subnet_extend s ';
        $sql .= ' LEFT JOIN cp_instance_basic as ns on s.basic_id = ns.id';
        $sql .= ' LEFT JOIN cp_hosts_network_card as nc on ns.code = nc.subnet_code';
        $sql .= ' WHERE nc.hosts_code = a.`code` and nc.is_default = 1';
        $sql .= ' ) as \'S_FusionType\', ';
        $sql .= ' (SELECT GROUP_CONCAT(i.ip) ';
        $sql .= ' FROM cp_hosts_network_card i ';
        $sql .= ' WHERE i.basic_id = a.`id` ';
        $sql .= ' ) as \'I_Ip\' ';
        $sql .= ' FROM ';
        $sql .= ' `cp_instance_basic` as a ';
        $sql .= ' LEFT JOIN `cp_eip_extend` as c ON a.`code` = c.bindcode ';
        $sql .= ' LEFT JOIN `cp_host_extend` as d ON d.basic_id=a.id ';
        $sql .= ' LEFT JOIN cp_agent as e ON e.class_code=a.location_code ';
        $sql .= ' LEFT JOIN cp_instance_basic as f ON f.`code`=a.vpc  and a.vpc<>\'\' and a.vpc is not null ';
        $sql .= ' LEFT JOIN cp_instance_basic as g ON g.`code`=a.subnet and a.subnet<>\'\' and a.subnet is not null';
        $sql .= ' LEFT JOIN cp_instance_basic as h ON h.`code`=a.router and a.router<>\'\' and a.router is not null';
        $sql .= ' LEFT JOIN cp_business_template as t ON a.biz_tid = t.biz_tid';
        $sql .= ' WHERE a.biz_tid >0 and a.type=\'hosts\' ' . $where;
        $sql .= ' group by a.id';
        $sql .= ' ORDER BY a.create_time desc ';
        $sql_row                  = $sql . ' limit ' . $offset . ',' . $limit;
        $query                    = $connection->execute($sql_row)->fetchAll('assoc');
        $this->_pageList['total'] = $connection->execute($sql)->count();
        $this->_pageList['rows']  = $query;
        return $this->_pageList;
    }

    public function getmonitor($data)
    {
        $instance_basic_table         = TableRegistry::get('InstanceBasic');
        $orders                       = new OrdersController();
        $request_data['instanceCode'] = $data['code'];
        $request_data['method']       = 'monitor';
        $request_data['uid']          = (string) $this->request->session()->read('Auth.User.id'); //uid
        $request_data['basicId']      = $parameter['basicId']      = (string) $instance_basic_table->find()->select(['id'])->where(['code' => $data['code']])->first()->id;

        $arr       = $orders->ajaxFun($request_data);
        $returnArr = array(
            'Code'    => $arr['Code'],
            'Message' => '',
        );
        if ($arr['Code'] != '0') {
            $returnArr['Message'] = $arr['Message'];
        } else {
            $chart['cpu'] = $chart['disk'] = $chart['network'] = $chart['memory'] = array();
            // 修改时间轴数据
            $MessageData = array_reverse(json_decode($arr['Message'], true));
            $times       = array();
            foreach ($MessageData as $key => $value) {
                // 时间点生成
                $times[] = date('H:i', $value['timestamp']);
                // 生成cpu
                $chart['cpu']['data'][] = $value['cpu'];
                // 生成memory
                $chart['memory']['data'][] = $value['memory'];
                // 入网
                $chart['network']['data1'][] = $value['networkIn'] / 1024;
                // 出网
                $chart['network']['data2'][] = $value['networkOut'] / 1024;
                // 读取
                $chart['disk']['data1'][] = $value['diskWrite'] / 1024;
                // 写入
                $chart['disk']['data2'][] = $value['diskRead'] / 1024;
            }
            $chart['cpu']['time'] = $chart['disk']['time'] = $chart['network']['time'] = $chart['memory']['time'] = $times;
            $returnArr['chart']   = $chart;
        }
        return $returnArr;
    }

    public function getDisksCount($code)
    {
        $disksMetadata = TableRegistry::get('DisksMetadata');
        $where         = array(
            'attachhostid' => $code['code'],
        );
        $count = $disksMetadata->find('all')
            ->where($where)
            ->count();
        return $count;
    }

    public function getDisksLimit($request_data = array())
    {
        $table_userSetting = TableRegistry::get('UserSetting');
        $uid               = (string) $this->request->session()->read('Auth.User.department_id');

        $whereUser = array(
            'para_code'  => 'host_max_disk',
            'owner_id'   => $uid,
            'owner_type' => 2,
        );
        $count = $table_userSetting->find()
            ->where($whereUser)
            ->first();
        if ($count != null) {
            return $count->para_value;
            die();
        } else {
            $disksMetadata = TableRegistry::get('Systemsetting');
            $where         = array(
                'para_code' => 'host_max_disk',
            );
            $count = $disksMetadata->find()
                ->where($where)
                ->first();
            return $count->para_value;
            die();
        }
    }

    public function formatter_subnet($request_data)
    {
        $subnet      = $request_data['subnet'];
        $table_hosts = TableRegistry::get('InstanceBasic');
        $entity      = $table_hosts->find()->where(array(
            'type' => 'subnet',
            'code' => $subnet,
        ));
        foreach ($entity as $key => $value) {
            return $value['name'];
            die();
        }
    }

    public function formatter_eip($request_data)
    {
        $code        = $request_data['code'];
        $table_hosts = TableRegistry::get('EipExtend');
        $entity      = $table_hosts->find()
            ->where(array(
                'bindcode' => $code,
            ))
            ->first();
        if (!empty($entity)) {
            return $entity->ip;
            die();
        }
    }

    public function formatter_router($request_data)
    {
        $subnet      = $request_data['router'];
        $table_hosts = TableRegistry::get('InstanceBasic');
        $entity      = $table_hosts->find()->where(array(
            'type' => 'router',
            'code' => $subnet,
        ));
        foreach ($entity as $key => $value) {
            return $value['name'];
            die();
        }
    }

    /**
     * 编辑数据列表
     */
    public function edit($request_data = array())
    {
        $code = '0001';
        $data = array();
        // 编辑操作
        $host        = TableRegistry::get('InstanceBasic');
        $account     = $host->get($request_data['id']);
        $account     = $host->patchEntity($account, $request_data);
        $account->id = $request_data['id'];
        if ($host->save($account)) {
            $code = '0000';
            $data = $host->get($request_data['id'])->toArray();
        }
        $msg = Configure::read('MSG.' . $code);
        return compact(array_values($this->_serialize));
    }

    /**
     * @func: 启动主机
     *
     * @param
     *            :@type:方法名称 @hostsId:操作主机ID
     *            @date: 2015年10月12日 下午2:45:17
     * @author : shrimp liao
     * @return : null
     */
    public function ajaxHosts($request_data = array())
    {
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        $orders               = new OrdersController();
        $result               = array();
        $isEach               = $request_data['isEach'];
        if ($request_data['method'] == 'ecs_delete') {
            $request_data['method']     = 'trash';
            $request_data['methodType'] = 'ecs_delete';
        } else {
            $request_data['methodType'] = '';
            $request_data['method'] = $request_data['method'];
        }
        $request_data['uid'] = (string) $this->request->session()->read('Auth.User.id');

        if ($isEach == 'true') {
            $table = $request_data['table'];
            foreach ($table as $key => $value) {
                if(!$this->_isAllowAjaxHosts($value['H_ID'])){
                    return ['Code'=>'1','Message'=>'该主机正在创建镜像中，正在被使用不能操作'];
                }
                if (!empty($value)) {
                    if ($request_data['methodType'] == 'ecs_delete') {
                        $is_relate_elb = $this->checkEcsAndElbIsRelate($value['H_ID']);#判断是否有elb监听主机网卡
                        if($is_relate_elb['is_relate'] == 1){
                            return ['Code'=>'1','Message'=>'主机，主机名称:'.$value['H_Name'].'('.$value['H_Code'].')下有网卡被负载均衡监听，负载均名称：'.$is_relate_elb["name"].'('.$is_relate_elb["code"].')，请先删除监听关系'];
                        }
                    }
                    $interface = array('method' => $request_data['method'], 'uid' => $request_data['uid'], 'basicId' => $value['H_ID'], 'instanceCode' => $value['H_Code']);
                    $result    = $orders->ajaxFun($interface);
                    if ($result['Code'] != '0') {
                        return $result;
                        die;
                    }
                }
            }
            $result['Code'] = '0';
            return $result;
            die();
        } else {
            if(!$this->_isAllowAjaxHosts($request_data['basicId'])){
                return ['Code'=>'1','Message'=>'该主机正在创建镜像中，正在被使用不能操作'];
            }
            if ($request_data['methodType'] == 'ecs_delete') {
                $is_relate_elb = $this->checkEcsAndElbIsRelate($request_data['basicId']);#判断是否有elb监听主机网卡
                if($is_relate_elb['is_relate'] == 1){
                    return ['Code'=>'1','Message'=>'该主机下有网卡被负载均衡监听，负载均名称：'.$is_relate_elb["name"].'('.$is_relate_elb["code"].')，请先删除监听关系'];
                }
            }
            // uid
            $request_data['instanceCode'] = $request_data['instanceCode'];
            unset($request_data['isEach']);
            return $orders->ajaxFun($request_data);
        }
    }

    private function _isAllowAjaxHosts($basic_id){
        $instance_basic = TableRegistry::get("InstanceBasic");
        $hostsEntity = $instance_basic->find()->select(['code',"status"])->where(['id'=>$basic_id])->first();
        if($hostsEntity !=null && $hostsEntity->status == "创建镜像中"){
            return false;
        }
        return true;
    }

    /**
     * [ajaxExtendNetCard 判断主机是否有扩展网卡]
     * @param  [array] $request_data [主机信息]
     * @return [array]               [code=>'0|1']
     */
    public function ajaxExtendNetCard($request_data){
        $basic_id = $request_data['id'];
        $hosts_network_card = TableRegistry::get('HostsNetworkCard');
        $cards_count = $hosts_network_card->find()->where(['is_default'=>0,'basic_id'=>$basic_id])->count();
        return ['code'=>$cards_count > 0 ? 1 : 0];
    }

    /**
     * [ajaxExtendNetCardAllow 添加主机时，根据选择的默认子网，判断是否允许添加扩展网卡]
     * @param  [array] $request_data ['subnet_code'=>'']
     * @return [array]
     */
    public function ajaxExtendNetCardAllow($request_data){
        $subnet_code = $request_data['subnet_code'];
        $instance_basic = TableRegistry::get('InstanceBasic');
        $subnet_extend  = $instance_basic->find('SubnetExtend')->select(['fusionType'=>'sub_e.fusionType'])->where(['InstanceBasic.code'=>$subnet_code])->first();
        $allow = false;
        if(is_array($subnet_extend) && !empty($subnet_extend)){
            $allow = ($subnet_extend['fusionType'] == "vmware" || $subnet_extend['fusionType'] == "openstack" )?true :false;
        }
        return ['code'=>'0','allow'=>$allow];
    }
    /**
     * [getPublicSubnetExtend 获取公共子网列表]
     * @return [array] [description]
     */
    public function getPublicSubnetExtend(){
        $instance_basic      = TableRegistry::get('InstanceBasic');

        $subnet_extend_list = $instance_basic->find('SubnetExtend')->join([
                'vpc'=>[
                    'table'=>'cp_instance_basic',
                    'type'=>'LEFT',
                    'conditions'=>'vpc.code = InstanceBasic.vpc'
                ]
            ])->select(['vpc_name'=>'vpc.name','subnet_code'=>'InstanceBasic.code','id'=>'InstanceBasic.id','subnet_name'=>'InstanceBasic.name'])->where(['sub_e.isPublic'=>'1'])->toArray();
        $this->_pageList['rows'] = $subnet_extend_list;
        $this->_pageList['total'] = count($subnet_extend_list);
        return $this->_pageList;
    }

    /**
    * 主机网卡 --- ajax操作
    */
    public function ajaxNetCard($request_data)
    {
        //判断是否允许添加扩展网卡，阿里云不支持添加扩展网卡
        if($request_data['method'] == 'network_card_add'){
            $this->_isAllowAjaxNetCard($request_data);
        }
        $orders = new OrdersController();
        $request_data['uid'] = (string) $this->request->session()->read('Auth.User.id');
        return $orders->ajaxFun($request_data);
    }
    /**
     * [ajaxImage 主机镜像操作]
     * @param  [array] $request_data
     * @return [json]
     */
    public function ajaxImage($request_data){
        $orders = new OrdersController();
        $request_data['uid'] = (string) $this->request->session()->read('Auth.User.id');
        return $orders->ajaxFun($request_data);
    }
    public function editImage($request_data){
        $id = $request_data['image_id'];
        $image_list = TableRegistry::get('Imagelist');
        $imageEntity = $image_list->find()->where(['id'=>$id])->first();
        if($imageEntity !=null){
            $imageEntity->set('image_name',$request_data['image_name']);
            $imageEntity->set('image_note',$request_data['image_note']);
            $re = $image_list->save($imageEntity);
            if($re === false){
                return ['Code'=>'1','Message'=>'修改镜像失败'];
            }else{
                return ['Code'=>'0','Message'=>'修改镜像成功'];
            }
        }else{
            return ['Code'=>'1','Message'=>'修改镜像失败,指定镜像不存在'];
        }

    }

    /**
     * [ajaxSnap 主机快照操作]
     * @param  [array] $request_data
     * @return [array]
     */
    public function ajaxSnap($request_data){
        $orders = new OrdersController();
        $request_data['uid'] = (string) $this->request->session()->read('Auth.User.id');
        return $orders->ajaxFun($request_data);
    }

    /**
     * [ajaxImageDel 主机镜像删除操作]
     * @param  [array] $request_data ['basic_id'=>string,'method'=>'image_del']
     * @return [array]
     */
    public function ajaxImageDel($request_data){
        $popedom_list = $this->request->session()->read('Auth.User.popedomname');
        $department_id = $this->request->session()->read('Auth.User.department_id');

        $basic_ids = explode(',', $request_data['basic_id']);
        $image_list = TableRegistry::get('Imagelist');
        $host_extend = TableRegistry::get('HostExtend');
        if(empty($basic_ids) || !is_array($basic_ids)){
            return ['Code'=>'1','Message'=>'参数错误'];
        }
        $results = [];
        $message = '';
        foreach ($basic_ids as $key => $value) {

            $data =  array();
            $imageEntity = $image_list->find()->select(['image_code','status','department_id'])->where(['id'=>$value])->first();
            if($imageEntity == null){
                continue;
            }
            if($imageEntity->status == "创建中"){
                $message .= "镜像创建中，不能删除。";
                continue;
            }
            if($imageEntity->department_id != $department_id){
                $message .= "镜像".$imageEntity->image_code."不属于当前租户下资源，不允许删除。";
                continue;
            }else{
                if(!in_array("ccf_image_del", $popedom_list)){
                    $message .= "当前用户没有删除镜像权限，不允许删除。";
                    continue;
                }
            }

            //如果主机正在使用镜像，不允许删除镜像
            $hosts = $host_extend->find()->select(['basic_id'])->where(['image_code'=>$imageEntity->image_code])->first();
            if($hosts == null){
                $data['method']     = $request_data['method'];
                $data['basic_id']   = $value;
                $data['imageCode']  = $imageEntity->image_code;
                $results[] = $this->ajaxImage($data);
            }else{
                $message .= "镜像".$imageEntity->image_code."有主机正在使用，不能删除。";
            }
        }
        if($message !=""){
            return ['Code'=>'1','Message'=>$message];
        }
    }
    /**
     * [imageList 镜像列表]
     * @param  [array] $request_data ['basic_id'=>int]
     * @return [array]               [_pageList]
     */
    public function imageList($request_data){
        $image_list = TableRegistry::get('Imagelist');

        $query = $image_list->find()->select(['id','image_name','image_code','image_note','is_private','create_time'])->where(['basic_id'=>$request_data['basic_id']]);
        $this->_pageList['total'] = $query->count();
        $this->_pageList['rows']  = $query->toArray();
        return $this->_pageList;
    }

    public function snapList($request_data){
        $snap_list = TableRegistry::get('Snapshotlist');

        $query = $snap_list->find()->select(['id','description','code','create_time'])->where(['basic_id'=>$request_data['basic_id']]);
        $this->_pageList['total'] = $query->count();
        $this->_pageList['rows']  = $query->toArray();
        return $this->_pageList;
    }


    /**
     * [_isAllowAjaxNetCard 阿里云不支持添加扩展网卡]
     * @param  [array]  $request_data
     */
    private function _isAllowAjaxNetCard($request_data){
        $instance_basic = TableRegistry::get('instance_basic');
        $agent_info = $instance_basic->find()->select(['status','agent_code'=>'agentP.agent_code'])->join(['agent'=>[
                        'table'=>'cp_agent',
                        'type'=>'LEFT',
                        'conditions'=>'agent.class_code = instance_basic.location_code'
                    ],
                    'agentP'=>[
                        'table'=>'cp_agent',
                        'type'=>'LEFT',
                        'conditions'=>'agent.parentid = agentP.id'
                    ]
                    ])->where(['instance_basic.id'=>$request_data['basic_id']])->first();
        if('aliyun' == $agent_info['agent_code']){
            echo json_encode(['code'=>1,'msg'=>'很抱歉，阿里云不支持扩展网卡！']);
            exit;
        }
        if($agent_info['status'] != "运行中"){
            echo json_encode(['code'=>1,'msg'=>'无法在当前状况（已关闭电源）下操作！']);
            exit;
        }
    }

    public function test($data)
    {
        $orders = new OrdersController();
        return $orders->ajaxFun($data);
    }

    public function getHostsEntityByCode($code)
    {
        $basic_table = TableRegistry::get('InstanceBasic');
        $where       = array(
            'code' => $code,
        );
        $entity = $basic_table->find('all')
            ->where($where)
            ->toArray()[0];
        return $entity;
    }

    /**
     * [getHostHardwareSet 根据主机id，获取当前主机可修改的配置]
     * @author [lanzhenxiang] <[<lanzhenxiang@sobey.com>]>
     * @param  [array] $param ["id"=>1]
     * @return [array]        [可用配置]
     */
    public function getHostHardwareSet($param){
        $id = $param['id'];
        $instance_basic = TableRegistry::get('instance_basic');
        $field = ['agent_id'=>'agent.id'];
        $basic_info = $instance_basic->getHostBasicInfoByID($id,$field);
        $agent  = TableRegistry::get('agent_set');
        $query = $agent->find();
        $query = $query->select([
                'set_code'=>'hardware.set_code',
                'cpu_number'=>'hardware.cpu_number',
                'memory_gb'=>'hardware.memory_gb',
                'gpu_gb'=>'hardware.gpu_gb'
            ]);
        $query->join(
                [
                'hardware'=>[
                        'table'=>'cp_set_hardware',
                        'type'=>'LEFT',
                        'conditions'=>'hardware.set_id = agent_set.set_id'
                    ]
                ]
            )->where(['agent_set.agent_id'=>$basic_info[0]['agent_id']]);
        $hardwareList = $query->toArray();
        $result = [];
        $tmpCPUList = [];
        $tmpGPUList = [];
        foreach ($hardwareList as $key => $value) {
            $tmpArr = array(
                            'num'     => $value->memory_gb,
                        );
            $tmpArrb = array(
                    'gpu'   =>$value->gpu_gb,
                    'setCode' => $value->set_code
                );
            $tmpCPUList[$value->cpu_number][] = $tmpArr;
            $tmpGPUList[$value->memory_gb][] = $tmpArrb;
        }
        ksort($tmpCPUList);
        ksort($tmpGPUList);
        //debug($tmpGPUList);exit;
        foreach ($tmpCPUList as $k => $v) {
            asort($v);
            sort($v);
            foreach ($tmpGPUList as $key => $gv) {
                foreach ($v as $vk =>$vv) {
                    if(intval($vv['num']) == $key){
                        $v[$vk]['gpu'] = $gv;
                    }
                }
            }
            $tarr = array(
                'cpu' => $k,
                'rom' => $v,
            );
            $result[] = $tarr;
        }
        return $result;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \App\Controller\Console\ConsoleController::test()
     */
    public function createHostsArray()
    {
        $agent_table = TableRegistry::get('Agent');
        $data_list   = $agent_table->find('tree', array(
            'order' => 'Agent.sort_order ASC',
        ))
            ->contain(array(
                'SetHardware',
                'Imagelist'=>function($q){
                    //主机镜像image_type = 1
                    $query =  $q->Where(['image_type'=>'1'])
                        ->Where(function($exp){
                            //私有镜像，只可见当前租户下的镜像
                            $orCondition = $exp->and_(['is_private'=>'1','department_id'=>(string) $this->request->session()->read('Auth.User.department_id')]);
                            //系统镜像,公共镜像，租户不限制
                            return $exp->or_(['is_private'=>'0','image_source <>'=>'2'])
                                       ->add($orCondition);
                        });
                    return $query;
                }
            ))
            ->where(array(
                'is_enabled' => 1,
            ))
            ->toArray();
        //debug($data_list);exit;
        $data_agent = array();
        // 城市
        // $baseNet= $this->getBaseTypeByName('subnet',$vv['location_code']);
        // $subnet= $this->getBaseTypeObjeByArray($baseNet,'net');//加载子网络
        // $data_fc = [];//厂商
        // 顶级的hardware赋值给children
        foreach ($data_list as $key => $value) {
            // debug($value['children']);die();
            if (!empty($value['children'])) {
                $data_agent[$key] = array(
                    'id'      => $value['id'],
                    'company' => array(
                        'name'        => $value['agent_name'],
                        'companyCode' => $value['agent_code'],
                    ),
                    'area'    => array(),
                );
                foreach ($value['children'] as $kk => $vv) {
                    // if (!empty($vv['set_hardware'])){
                    // $vv['set_hardware'] = array_merge($vv['set_hardware'] ,$value['set_hardware']);
                    $tmpArr    = array();
                    $tmpArrTwo = array();
                    $tmpset    = array();
                    $tmpOs     = array();
                    // debug($vv['set_hardware']);
                    // sort($vv['set_hardware']);
                    foreach ($vv['set_hardware'] as $kkk => $vvv) {
                        $tmpArr = array(
                            'num'     => $vvv->memory_gb,
                            'setCode' => $vvv->set_code,
                        );
                        $tmpArrTwo[$vvv->cpu_number][] = $tmpArr;
                    }
                    ksort($tmpArrTwo);
                    // sort($tmpArrTwo);
                    // if($vv['id']==19){
                    // debug($tmpArrTwo);
                    // }
                    // debug($tmpArrTwo);
                    // ksort ($tmpArrTwo);
                    // debug($tmpArrTwo);die();
                    foreach ($tmpArrTwo as $k => $v) {
                        asort($v);
                        sort($v);
                        $tarr = array(
                            'cpu' => $k,
                            'rom' => $v,
                        );

                        // $tarr['cpu'] = $k;
                        // $tarr['rom'] = $v;
                        // $data_agent[$key]['set'][]=$tarr;
                        $tmpset[] = $tarr;
                    }
                    // debug($tmpset);
                    // $baseNet= $this->getBaseTypeByName('subnet',$vv['class_code']);
                    // $net= $this->getBaseTypeObjeByArray($baseNet,'net');
                    // debug($vv);
                    // debug($net);
                    $data_agent[$key]['area'][] = array(
                        'id'       => $vv['id'],
                        'name'     => $vv['agent_name'],
                        'areaCode' => $vv['region_code'],
                        'set'      => $tmpset,
                        'imageType'       => $this->createOSArray($vv['imagelist']),
                        'vpc'      => $this->getAllVpc($vv['class_code']),
                        'eip'      => $this->getAllEip($vv['class_code']),
                    );
                }
            }
        }
        return $data_agent;
    }

    public function getAllVpc($class_code)
    {
        $table         = TableRegistry::get('InstanceBasic');
        $vpcArray      = array();
        $department_id = $this->request->session()->read('Auth.User.department_id') ? $this->request->session()->read('Auth.User.department_id') : 0;
        $where         = array(
            'status'        => '运行中',
            'type'          => 'vpc',
            'location_code' => $class_code,
            'department_id' => $department_id,
        );
        $vpcList = $table->find('all')
            ->where($where)
            ->toArray();
        // debug($vpcList);
        foreach ($vpcList as $key => $value) {
            $vpcArray[] = array(
                'name'   => $value['name'],
                'vpCode' => $value['code'],
                'net'    => $this->getAllsubNet($value['code']),
            );
        }
        return $vpcArray;
    }

    public function getAllEip($class_code)
    {
        $account_table = TableRegistry::get('Accounts');
        $user          = $account_table->find()
            ->select('department_id')
            ->where(array(
                'id' => $this->request->session()
                    ->read('Auth.User.id'),
            ))
            ->first();
        $table    = TableRegistry::get('InstanceBasic');
        $vpcArray = array();
        $where    = array(
            'status'        => '运行中',
            'type'          => 'eip',
            'location_code' => $class_code,
            'department_id' => $user['department_id'],
        );
        $vpcList = $table->find('all')
            ->contain(array(
                'EipExtend',
            ))
            ->where(function ($exp, $q) {
                return $exp->isNull('bindcode');
            })
            ->orWhere(array(
                'bindcode' => '',
            ))
            ->where($where)
            ->toArray();
        foreach ($vpcList as $key => $value) {
            $vpcArray[] = array(
                'name'    => $value['name'],
                'eipCode' => $value['code'],
            );
        }
        return $vpcArray;
    }

    public function getAllsubNet($vpCode)
    {
        if(is_array($vpCode)){
            $vpCode=$vpCode["vpCode"];
        }
        $table = TableRegistry::get('InstanceBasic');
        $where = array(
            'status' => '运行中',
            'type'   => 'subnet',
            'vpc'    => $vpCode,
        );
        $netArray = $table->find('all')
            ->contain(array(
                'SubnetExtend'
            ))
            ->where($where)
            ->toArray();
        //debug($netArray);exit;
        $subnetList = array();
        foreach ($netArray as $key => $value) {
            if($value['subnet_extend']['fusionType'] == ""){
                $isFusion = "vmware";
            } else {
                $isFusion = $value['subnet_extend']['fusionType'];
            }
            $subnetList[] = array(
                'name'     => $value['name'],
                'netCode'  => $value['code'],
                'isFusion' => $isFusion,
            );
        }
        return $subnetList;
    }

    /**
     * @func: 创建前台所需的套餐数据
     *
     * @param
     *            :
     *            @date: 2015年10月15日 下午4:37:46
     * @author : shrimp liao
     * @return : null
     */
    public function test11111()
    {
        $str            = array();
        $agent          = TableRegistry::get('Agent');
        $agentimagelist = TableRegistry::get('AgentImagelist', array(
            'classname' => 'App\\Model\\Table\\AgentImagelistTable',
        ));
        $setsoftware = TableRegistry::get('GoodsVersionSpec', array(
            'classname' => 'App\\Model\\Table\\GoodsVersionSpecTable',
        ));
        $where = array(
            '1' => '1',
        );
        // 获取aduser
        $aduser = TableRegistry::get('AdUser', array(
            'classname' => 'App\\Model\\Table\\AdUserTable',
        ));
        $aduserinfo = $aduser->find('all', array(
            'fields' => array(
                'id',
                'loginName',
            ),
        ))->toArray();
        // 获取商品信息，包含商品分类
        $agentInfo = $agent->find('all')
            ->contain(array(
                'AgentImagelist',
                'AgentSet',
            ))
            ->where($where)
            ->toArray();
        // 获取商品信息，包含商品分类
        $setsoftwareInfo = $setsoftware->find('all')
            ->select('brand')
            ->distinct(array(
                'brand',
            ))
            ->toArray();
        // 获取基础数据信息（加载一次）
        $baseNet = $this->getBaseTypeByName('subnet');
        $subnet  = $this->getBaseTypeObjeByArray($baseNet, 'net');
        // 加载子网络
        foreach ($agentInfo as $index => $item) {
            if ($item['parentid'] == 0) {
                $agent            = array();
                $agent['id']      = $item['id'];
                $agent['company'] = array(
                    'name'        => $item['agent_name'],
                    'companyCode' => $item['agent_code'],
                );
                $agent['area'] = $this->getAreaListById($item['id'], $agentInfo);
                // $agent['set']=$this->getSetObjectByArray($item['agent_set']);
                $agent['net'] = $subnet;
                // $agent['Os']=$this->createOSArray($item['agent_imagelist']);
                // $agent['Firewall']=$this->createOSArray($item['agent_imagelist']);
                // $agent['AdUser']=$aduserinfo;
                // $agent['setsoftwareInfo']=$this->createSoftwareArray($setsoftwareInfo);
                $str[] = $agent;
            }
        }
        return $str;
    }

    /**
     * @func: 获取厂商对应的地区机房str
     *
     * @param
     *            :$id 厂商ID,$agentInfo 厂商集合
     *            @date: 2015年10月15日 下午5:16:07
     * @author : shrimp liao
     * @return : null
     */
    public function getAreaListById($id, $agentInfo)
    {
        $str               = array();
        $instance_basic    = TableRegistry::get('InstanceBasic');
        $instance_relation = TableRegistry::get('InstanceRelation');
        $vpc_extend        = TableRegistry::get('VpcExtend');
        $department_id     = $this->request->session()->read('Auth.User.department_id') ? $this->request->session()->read('Auth.User.department_id') : 0;
        foreach ($agentInfo as $index => $item) {
            if ($item['parentid'] == $id) {
                $router_data = array();
                $router      = $instance_basic->find()
                    ->select(array(
                        'id',
                        'name',
                    ))
                    ->where(array(
                        'location_code' => $item['class_code'],
                        'type'          => 'router',
                        'code <>'       => '',
                        'department_id' => $department_id,
                    ))
                    ->toArray();
                foreach ($router as $key => $value) {
                    $vpcid = $instance_relation->find()
                        ->select(array(
                            'toid',
                        ))
                        ->where(array(
                            'fromid'   => $value['id'],
                            'fromtype' => 'router',
                            'totype'   => 'vpc',
                        ))
                        ->toArray();
                    if ($vpcid) {
                        $cidr = $vpc_extend->find()
                            ->select(array(
                                'cidr',
                            ))
                            ->where(array(
                                'basic_id' => $vpcid[0]['toid'],
                            ))
                            ->toArray();
                        if ($cidr) {
                            $router_data[$key]['cidr'] = $cidr[0]['cidr'];
                        }
                    }
                    $router_data[$key]['id']   = $value['id'];
                    $router_data[$key]['name'] = $value['name'];
                }
                $str[] = array(
                    'name'     => $item['agent_name'],
                    'areaCode' => $item['region_code'],
                    'router'   => $router_data,
                );
            }
        }
        // var_dump($str);exit;
        return $str;
    }

    /**
     * @func: 根据套餐返回套餐数组
     *
     *
     * @param
     *            :
     *            @date: 2015年10月15日 下午6:41:38
     * @author : shrimp liao
     * @return : null
     */
    public function getSetObjectByArray($setInfo)
    {
        $setHardware = TableRegistry::get('SetHardware');
        $setHardware->find()
            ->select(array(
                'cpu_number',
            ))
            ->where($where)
            ->toArray();
        $cpu = array();
        foreach ($setInfo as $item) {
            $set_id = $item['set_id'];
            $where  = array(
                'set_id' => $set_id,
            );
            $cpu_s = $setHardware->find()
                ->select(array(
                    'cpu_number',
                ))
                ->where($where)
                ->toArray();
            // debug($cpu_s);die();
            $cpu[] = $cpu_s;
        }
        $info = array_unique($info);
        foreach ($info as $index => $i) {
            $set = array();
            foreach ($setInfo as $item) {
                if ($item['cpu_number'] == $i) {
                    $set[] = array(
                        'num'     => $item['memory_gb'],
                        'setCode' => $item['set_type_code'],
                    );
                    $info[$index] = array(
                        'cpu' => $item['cpu_number'],
                        'rom' => $set,
                    );
                }
            }
        }
        sort($info);
        return $info;
    }

    public function getImageNameById($request_data = array())
    {
        $image_code = $request_data['image_code'];
        $imageTable = TableRegistry::get('AgentImagelist');
        $image      = $imageTable->find('all')
            ->distinct(array(
                'image_code',
            ))
            ->where(array(
                'image_code' => $image_code,
            ))
            ->toArray();
        return $image;
    }

    /**
     * @func:获取基础数据更具TyepName
     *
     * @param
     *            :
     *            @date: 2015年10月16日 上午11:04:18
     * @author : shrimp liao
     * @return : null
     */
    public function getBaseTypeByName($name, $regionCode)
    {
        $baseTable = TableRegistry::get('InstanceBasic');
        $where     = array(
            'type'          => $name,
            'code <>'       => '',
            'status'        => '运行中',
            'location_code' => $regionCode,
        );
        $agentInfo = $baseTable->find('all')
            ->where($where)
            ->toArray();
        return $agentInfo;
    }

    /**
     * @func:更具数据源，得到基础需要类型对应数组
     *
     * @param
     *            :
     *            @date: 2015年10月16日 上午11:12:58
     * @author : shrimp liao
     * @return : null
     */
    public function getBaseTypeObjeByArray($array, $name)
    {
        $str = array();
        foreach ($array as $item) {
            $str[] = array(
                'name'         => $item['name'],
                $name . 'Code' => $item['code'],
            );
        }
        return $str;
    }

    public function createOSArray($imageArray)
    {
        if (empty($imageArray)) {
            return array();
        }
        $platlist = array();
        $image_type = ['system'=>[],'private'=>[],'public'=>[]];
        foreach ($imageArray as $key => $value) {
            $platlist[] = $value['plat_form'];
            if($value['image_source'] =='2'){
                if($value['is_private'] == 1){//私有镜像
                    $image_type['private'][] = $value;
                }else{
                    $image_type['public'][] = $value;
                }
            }else{
                $image_type['system'][] = $value;
            }
        }

        foreach ($image_type as $key => $value) {
            if(empty($value)){
                unset($image_type[$key]);
                continue;
            }
            $name = str_replace(array('system','private','public'),array('系统镜像','私有镜像','公共镜像'),$key);
            $str[] = array(
                'name' =>$name,
                'Os'=>$this->_createOSArray($image_type[$key],$platlist)
            );
        }
        return $str;
    }

    private function _createOSArray($image_array,$index_list){
        $str = [];
        //debug($index_list);exit;
        $index_list = array_unique($index_list);
        foreach ($index_list as $index) {
            $type = array();
            foreach ($image_array as $item) {
                if ($item['plat_form'] == $index) {
                    $type[] = array(
                        'name'     => $item['image_name'],
                        'typeCode' => $item['image_code'],
                    );
                }
            }
            if(!empty($type)){
                $str[] = array(
                    'name'  => $index,
                    'types' => $type,
                );
            }
        }
        return $str;
    }
    /**
    private function _array_group_value(array $input,$group_array){
        if(!is_array($input) || empty($input) || !is_array($group_array) || empty($group_array)){
            return array();
        }
        $index_value = end($group_array);
        $group_array = array_pop($group_array);
        $index_list = [];
        foreach ($input as $key => $value) {
            $index_list[] = $value[$index_value];
        }
        $index_list = array_unique($index_list);
        if(empty($index_list))return array();
        $result_set = array();

        foreach ($index_list as $key => $index) {
            $items = array();
            foreach ($input as $key =>$item) {
                if ($item[$index_value] == $index) {
                    $items[] = $item;
                }
            }
            $result_set[] = array(
                'name'  => $index,
                'items' => $this->_array_group_value($items,$group_array),
            );
        }
        return $result_set;
    }
    */

    public function createSoftwareArray($setsoftware)
    {
        $setsoftwareTable     = TableRegistry::get('GoodsVersionSpec');
        $sethardwareTable     = TableRegistry::get('SetHardware');
        $setsoftwareinfoArray = $setsoftwareTable->find('all')->toArray();
        $sethardwareinfoArray = $sethardwareTable->find('all')->toArray();
        $str                  = array();
        foreach ($setsoftware as $image) {
            $type = array();
            foreach ($setsoftwareinfoArray as $item) {
                if ($item['brand'] == $image['brand']) {
                    foreach ($sethardwareinfoArray as $key => $value) {
                        if ($value['set_code'] == $item['instancetype_code']) {
                            $software[] = array(
                                'name'            => $item['set_name'],
                                'imageCode'       => $item['image_code'],
                                'softwareNote'    => $item['set_note'],
                                'setCode'         => $item['set_code'],
                                'hardware_name'   => $value['set_name'],
                                'hardware_code'   => $value['set_code'],
                                'hardware_cpu'    => $value['cpu_number'],
                                'hardware_memory' => $value['memory_gb'],
                            );
                        }
                    }
                }
            }
            $str[] = array(
                'name'         => $image['brand'],
                'softwareInfo' => $software,
            );
        }
        return $str;
    }

    public function desktop_add($data)
    {
        $data['uid'] = (string) $this->request->session()->read('Auth.User.id');
        $orders      = new OrdersController();
        $table       = TableRegistry::get('Accounts');
        $entity      = $table->find()
            ->where(array(
                'id' => $data['uid'],
            ))
            ->first();
        $data['username']     = $entity->loginname;
        $data['userpassword'] = $entity->password;
        $data['fimasName']    = 'fimas';
        $result               = $orders->ajaxFun($data);
        return $result;
        die();
    }

    public function webadmin($data)
    {
        $orders               = new OrdersController();
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        $data['uid']          = (string) $this->request->session()->read('Auth.User.id');
        $data['basicId']      = (string) $instance_basic_table->find()->select(['id'])->where(['code' => $data['instanceCode']])->first()->id;
        $data['password']     = '123456';
        $result               = $orders->ajaxFun($data);
        return $result;
        die();
    }

    public function hostDetail($basic_id = 6021)
    {
        $basic_id          = 6021;
        $instance_basic    = TableRegistry::get('InstanceBasic');
        $instance_relation = TableRegistry::get('InstanceRelation');
        $imagelist         = TableRegistry::get('Imagelist');
        $data              = $instance_basic->find('all')
            ->contain(array(
                'HostExtend',
                'Agent',
            ))
            ->where(array(
                'id' => $basic_id,
            ))
            ->toArray();
        $subnet = $instance_relation->find()
            ->where(array(
                'fromid' => $data[0]['id'],
                'totype' => 'subnet',
            ))
            ->first();
        $image = $imagelist->find()->where(array(
            'image_code' => $data[0]['HostExtend']['image_code'],
        ));
        var_dump($data);
        die();
    }

    public function createHostDetailArray()
    {}

    //根据不同类型查询该设备使用的配合
    public function getInstanceQuotaByType($data)
    {
        $type           = $data["type"];
        $basicId        = $data["basicId"];
        $code           = $data["code"];
        $instance_basic = TableRegistry::get('HostExtend');
        $disks_metadata = TableRegistry::get('DisksMetadata');
        $extends        = [];
        $disks          = array("count" => 0);

        // $result = array('code' => '0','data'=>[]);
        $message = array('code' => 0, 'msg' => '');

        $id       = $this->request->session()->read('Auth.User.id');
        $response = Requests::post(Configure::read('Api.cmop') . '/SystemInfo/getDepartBuget', [],
            [
                'userid' => $id,
            ], ['verify' => false]);
        $limit   = json_decode($response->body, true);
        $limit   = $limit['data'];
        $requset = Requests::post(Configure::read('Api.cmop') . '/SystemInfo/getDepartUsed', [],
            [
                'userid'      => $id,
                "source_type" => "cpu_used,router_used,subnet_used,disks_used",
            ], ['verify' => false]);
        $used = json_decode($requset->body, true);
        if (empty($used['data'])) {
            $used = array('0');
        } else {
            $used = $used['data'];
        }
        $userLimti = array_merge($limit, $used);
        switch ($type) {
            case 'hosts':
                $extends = $instance_basic->find()->select(['cpu', 'memory', 'gpu'])->where(["basic_id" => $basicId])->first();
                if (!empty($code)) {
                    // $disks = $disks_metadata->find()->select(["capacity"=>$disks_metadata->func()->count('capacity')->where(["attachhostid"=>$code])]);
                    $find  = $disks_metadata->find();
                    $disks = $find->select(['count' => $find->func()->sum('capacity')])->where(["attachhostid" => $code])->first();
                }
                if ((int) $userLimti["cpu_used"] + (int) $extends["cpu"] > (int) $userLimti["cpu_bugedt"]) {
                    $message = array('code' => 1, 'msg' => 'CPU配额不足');
                } else if ((int) $userLimti["memory_used"] + (int) $extends["memory"] > (int) $userLimti["memory_buget"]) {
                    $message = array('code' => 1, 'msg' => '内存配额不足');
                } else if ((int) $userLimti["gpu_used"] + (int) $extends["gpu"] > (int) $userLimti["gpu_bugedt"]) {
                    $message = array('code' => 1, 'msg' => 'GPU配额不足');
                } else if ((int) $userLimti["disks_used"] + (int) $disks["count"] > (int) $userLimti["disks_bugedt"]) {
                    $message = array('code' => 1, 'msg' => '磁盘配额不足');
                }
                break;
            case 'desktop':
                $extends = $instance_basic->find()->select(['cpu', 'memory', 'gpu'])->where(["basic_id" => $basicId])->first();
                if (!empty($code)) {
                    $disks = $disks_metadata->find()->select(["capacity" => $disks_metadata->func()->sum()])->where(["attachhostid" => $code]);
                }
                if ((int) $userLimti["cpu_used"] + (int) $extends["cpu"] > (int) $userLimti["cpu_bugedt"]) {
                    $message = array('code' => 1, 'msg' => 'CPU配额不足');
                } else if ((int) $userLimti["memory_used"] + (int) $extends["memory"] > (int) $userLimti["memory_buget"]) {
                    $message = array('code' => 1, 'msg' => '内存配额不足');
                } else if ((int) $userLimti["gpu_used"] + (int) $extends["gpu"] > (int) $userLimti["gpu_bugedt"]) {
                    $message = array('code' => 1, 'msg' => 'GPU配额不足');
                } else if ((int) $userLimti["disks_used"] + (int) $disks["count"] > (int) $userLimti["disks_bugedt"]) {
                    $message = array('code' => 1, 'msg' => '磁盘配额不足');
                }
                break;
        }
        // $message = array('code'=>1,'msg'=>'CPU配额不足');
        echo json_encode($message);exit();
        //{"cpu_bugedt":1000,"memory_buget":1000,"gpu_bugedt":81920,"router_bugedt":5,"subnet_bugedt":10,"disks_bugedt":10000,"0":"0"}
    }

    /**
     * [loadAvailableSubnetPublic 获取当前主机可以添加网卡的子网，去掉已经添加网卡的子网]
     * @param  [array] $data ['vpc'=>'?','basicid'=>?]
     * @return [array]       [可用结果集]
     */
    public function loadAvailableSubnetPublic($data){
        $is_public = 0;
        $instance_basic = TableRegistry::get('instance_basic');
        $field = [
            'I_SubnetCode'=>'network.subnet_code',
            'H_Vpc' =>'instance_basic.vpc',
            'I_SubnetId'=>'sub_b.id'
        ];
        $subnetList = $instance_basic->getHostBasicInfoByID($data['basicid'],$field);
        $subnet = [];
        foreach ($subnetList as $key => $sub) {
            $subnet[] = $sub['I_SubnetCode'];
        }
        $subnet_extend_table = TableRegistry::get('SubnetExtend');
        $subnetEntity = $subnet_extend_table->find()->select(['fusionType'])->where(['basic_id'=>$subnetList[0]['I_SubnetId']])->first();

        $data['fusionType'] = $subnetEntity->fusionType;
        $data['vpc'] = $subnetList[0]['H_Vpc'];
        // if($data['vpc'] != $subnetList[0]['H_Vpc']){//公共子网
        //     $is_public = 1;
        // }
        $vpcList = $this->loadSubnetPublic($data,$is_public);

        foreach ($vpcList['vpc'] as $key => $vpc) {
            foreach ($vpc['subnet'] as $k => $v) {
                if(in_array($v['code'], $subnet)){
                    unset($vpcList['vpc'][$key]['subnet'][$k]);
                }
            }
        }
        return $vpcList;
    }

    public function loadSubnetPublic($data,$is_public = 0){
        $connection = ConnectionManager::get('default');
        $sql = ' (SELECT a.id,a.type,a.`name`,a.`code`,c.type as \'type1\',c.id as \'id1\',c.`code` as \'code1\',c.`name` as \'name1\'FROM cp_instance_basic as a  ';
        $sql .= ' LEFT JOIN cp_vpc_extend as b ON a.id=b.basic_id ';
        $sql .= ' LEFT JOIN cp_instance_basic as c ON c.vpc=a.`code` ';
        $sql .= ' LEFT JOIN cp_subnet_extend as d ON d.basic_id=c.id ';
        $sql .= ' where a.type=\'vpc\' AND c.type=\'subnet\' AND a.`code`=\'' . $data['vpc'] . '\' AND a.`status` = \'运行中\' ';
        if(isset($data['fusionType'])){
            $sql .= 'AND d.fusionType = "'.$data['fusionType'].'"';
        }
        $sql .= 'AND d.isPublic='.$is_public.' ORDER BY a.id ASC )';
        $sql .= ' UNION ';
        $sql .= ' (SELECT a.id,a.type,a.`name`,a.`code`,c.type as \'type1\',c.id as \'id1\',c.`code` as \'code1\',c.`name` as \'name1\'FROM cp_instance_basic as a  ';
        $sql .= ' LEFT JOIN cp_vpc_extend as b ON a.id=b.basic_id ';
        $sql .= ' LEFT JOIN cp_instance_basic as c ON c.vpc=a.`code` ';
        $sql .= ' LEFT JOIN cp_subnet_extend as d ON d.basic_id=c.id ';
        $sql .= ' where a.type=\'vpc\' AND c.type=\'subnet\'';
        if(isset($data['fusionType'])){
            $sql .= 'AND d.fusionType = "'.$data['fusionType'].'"';
        }
        $sql .= 'AND d.isPublic=1 AND a.`status` = \'运行中\' ORDER BY a.id ASC)';
        // debug($sql);
        $query                    = $connection->execute($sql)->fetchAll('assoc');
        //debug($query);
        //数据组装
        $p['vpc'] = array();
            if(count($query)!=0){
                for ($i=0; $i < count($query); $i++) {
                    for ($k=0; $k < count($query)-$i ; $k++) {
                         $p['vpc'][]=array('name'=>$query[$i]['name'],'code'=>$query[$i]['code']);
                    }
            }
            if(!empty($p['vpc'])){
                $p['vpc'] = $this->unique_arr($p['vpc'],true,true);

                foreach ($p['vpc'] as $key => $value) {
                    foreach ($query as $v => $b) {
                        if($value['code']==$b['code']){
                            $value['subnet'][]=array('name'=>$b['name1'],'code'=>$b['code1']);
                        }
                    }
                    $p['vpc'][$key]=$value;
                }
            }
        }
        //debug($p);
        return $p;
    }
    /*
     *二维数组去重
     */
    function unique_arr($array2D,$stkeep=false,$ndformat=true)
    {
        // 判断是否保留一级数组键 (一级数组键可以为非数字)
        if($stkeep) $stArr = array_keys($array2D);

        // 判断是否保留二级数组键 (所有二级数组键必须相同)
        if($ndformat) $ndArr = array_keys(end($array2D));

        //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
        foreach ($array2D as $v){
            $v = join(",",$v);
            $temp[] = $v;
        }

        //去掉重复的字符串,也就是重复的一维数组
        $temp = array_unique($temp);

        //再将拆开的数组重新组装
        foreach ($temp as $k => $v)
        {
            if($stkeep) $k = $stArr[$k];
            if($ndformat)
            {
                $tempArr = explode(",",$v);
                foreach($tempArr as $ndkey => $ndval) $output[$k][$ndArr[$ndkey]] = $ndval;
            }
            else $output[$k] = explode(",",$v);
        }

        return $output;
    }


    /**
    * 检查主机下的网卡是否被elb监听
    * @author wangjincheng
    * @param $id  Ecs的basic_id
    * 返回 状态，code, name
    */
    public function checkEcsAndElbIsRelate($id){
        $hosts_network_card_table = TableRegistry::get("HostsNetworkCard");
        $elb_netcard_table = TableRegistry::get("ElbNetcard");
        $instance_basic_table = TableRegistry::get("InstanceBasic");
        $hosts_network_card_data = $hosts_network_card_table->find()->where(['basic_id'=>$id])->toArray();
        $card_id_array = array();
        if(!empty($hosts_network_card_data)){
            foreach ($hosts_network_card_data as $key => $value) {
                $card_id_array[] = $value['id'];
            }
        }
        $elb_netcard_data = $elb_netcard_table->find()->where(['netcard_id in' => $card_id_array])->first();
        $is_relate['is_relate'] = 0;
        $is_relate['name'] = '';
        $is_relate['code'] = '';
        if(!empty($elb_netcard_data)){

            $instance_basic_data = $instance_basic_table->find()->where(['id' => $elb_netcard_data['elb_id']])->first();
            if(!empty($instance_basic_data)){
                //有关联关系
                $is_relate['is_relate'] = 1;
                $is_relate['name'] = $instance_basic_data["name"];
                $is_relate['code'] = $instance_basic_data["code"];
            }


        }
        return $is_relate;
    }
}
