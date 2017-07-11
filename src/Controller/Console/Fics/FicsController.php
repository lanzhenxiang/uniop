<?php

/**
 * ==============================================
 * Fics.php
 * @author: shrimp liao
 * @date: 2016年3月19日 上午11:09:31
 * @version: v1.0.0
 * @desc:
 * ==============================================
 **/
namespace App\Controller\Console\Fics;

use App\Controller\Console\Network\HostsController;
use App\Controller\Console\ConsoleController;
use App\Controller\OrdersController;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Network\Http\Client;
use App\Controller\

class FicsController extends ConsoleController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }
    private $_serialize = array('code', 'msg', 'data');
    public $_pageList   = array('total' => 0, 'rows' => array());

    public function lists($request_data = array())
    {
        $limit = $request_data['limit'];
        $offset  = $request_data['offset'];

        $fics_extend_table = TableRegistry::get('FicsExtend');
        
        $where = array();

        if (!empty($request_data['department_id'])) {
            $where["FicsExtend.department_id"] = $request_data['department_id'];
        }
        if(isset($request_data['search'])){
            if ($request_data['search']!="") {
                $where["FicsExtend.vol_name like"] = '%' . $request_data['search'] . '%';
            }
        }
        if (isset($request_data['type'])) {
            if ($request_data['type'] != "") {
                $where["FicsExtend.vol_type like"] = '%' . $request_data['type'] . '%';
            }
        }
        if (!empty($request_data['class_code'])) {
            $where["Agent.class_code like"] = $request_data['class_code'] . '%';
        }
        if (!empty($request_data['class_code2'])) {
            $where["Agent.class_code"] = $request_data['class_code2'];
        } elseif (!empty($request_data['class_code'])) {
            $where["Agent.class_code like"] = $request_data['class_code'] . '%';
        }
        
        $field = [
            "vol_id" => "FicsExtend.vol_id",
            "vol_name" => "FicsExtend.vol_name",
            "total_cap" => "FicsExtend.total_cap",
            "warn_cap" => "FicsExtend.warn_cap",
            "vol_type" => "FicsExtend.vol_type",
            "department_id" => "FicsExtend.department_id",
            "store_code" => "FicsExtend.store_code",
            "region_code" => "FicsExtend.region_code",
            "create_time" => "FicsExtend.create_time",
            "display_name" => "FicsExtend.display_name",
            "label" => "FicsExtend.label",
            "ip" => "FicsExtend.ip",
            "status" => "FicsExtend.status",
            "agent_name" => "Agent.agent_name",
            "store_name" => "Store.store_name",
            "name" => "Departments.name",
            "class_code" => "Agent.class_code"
        ];
        
        $data = $fics_extend_table->initJoinQuery()
        ->joinAgent()
        ->joinStore()
        ->joinDepartments()
        ->getJoinQuery()
        ->select($field)
        ->where($where)
        ->group("FicsExtend.vol_id")
        ->order("FicsExtend.vol_id DESC")
        ->offset($offset)->limit($limit)
        ;
        
        $this->_pageList['total'] = $data->count();
        $this->_pageList['rows'] = $data;
        return $this->_pageList;
    }

    public function settinglist($request_data = array())
    {
        $limit = $request_data['limit'];
        $offset  = $request_data['offset'];
        $fics_users_table = TableRegistry::get('FicsUsers');
        
        $where = array();
        if (!empty($request_data['department_id'])) {
            $where["FicsUsers.department_id"] = $request_data['department_id'];
        }
        if(isset($request_data['search'])){
            if ($request_data['search']!="") {
                $where["FicsUsers.name like"] = '%' . $request_data['search'] . '%';
            }
        }
        if (isset($request_data['t'])) {
            if ($request_data['t'] != "") {
                $where["StoreUserP.limit"] = $request_data['t'];
            }
        }
        
        if (isset($request_data['vol_name'])) {
            if ($request_data['vol_name'] != "") {
                $where["StoreUserP.vol_name"] = $request_data['vol_name'];
            }
        }
        $field = [
            "userid" => "FicsUsers.userid",
            "name" => "FicsUsers.name",
            "password" => "FicsUsers.password",
            "storetype" => "FicsUsers.storetype",
            "store_code" => "FicsUsers.store_code",
            "department_id" => "FicsUsers.department_id",
            "region_code" => "FicsUsers.region_code",
            "id" => "StoreUserP.id",
            "vol_name" => "StoreUserP.vol_name",
            "limit" => "StoreUserP.limit"
        ];
        
        $data = $fics_users_table->initJoinQuery()
        ->joinStoreUserP()
        ->getJoinQuery()
        ->select($field)
        ->where($where)
        ->group("FicsUsers.userid")
        ->order("FicsUsers.userid DESC")
        ->offset($offset)->limit($limit)
        ;
        
        $this->_pageList['total'] = $data->count();
        $this->_pageList['rows'] = $data;
        return $this->_pageList;
    }

    public function cheklist($request_data = array())
    {
        $fics_vol_account_table = TableRegistry::get('FicsVolAccount');
        
        $where = array();
        if (!empty($request_data['template_id'])) {
            $where["FicsVolAccount.template_id"] = $request_data['template_id'];
        }
        if (!empty($request_data['basic_id'])) {
            $where["FicsVolAccount.basic_id"] = $request_data['basic_id'];
        }
        $field = [
            "id" ,
            "basic_id",
            "template_id",
            "account_id"
        ];
        
        $query = $fics_vol_account_table
        ->find()->hydrate(false)
        ->join([
            'FicsVolAccesTemplate'=>[
                'table' => 'cp_fics_vol_acces_template',
                'type'  => 'LEFT',
                'conditions' => 'FicsVolAccount.basic_id = FicsVolAccesTemplate.vol_id AND FicsVolAccount.template_id = FicsVolAccesTemplate.template_id'
            ]
        ])
        ->where($where);
        return $query;
    }

    public function ajaxFics($request_data = array())
    {
        $orders                          = new OrdersController();
        $result                          = array();
        $uid                             = (string) $this->request->session()->read('Auth.User.id');
        $request_data['method']          = $request_data['method'];
        $request_data['uid']             = (string) $this->request->session()->read('Auth.User.id');
        $request_data['loadbalanceCode'] = $request_data['loadbalanceCode'];
        $request_data['basicId']         = $request_data['basicId'];
        $request_data['uid']             = (string) $this->request->session()->read('Auth.User.id');
        if ($request_data['method'] == 'lbs_unbind') {
            unset($request_data['protocol']);
            unset($request_data['port']);
            unset($request_data['weight']);
        }
        return $orders->ajaxFun($request_data);
    }

    public function addUser($request_data = array())
    {
        // debug($request_data);die();
        $orders                 = new OrdersController();
        $uid                    = (string) $this->request->session()->read('Auth.User.id');
        $request_data['method'] = "store_addUser";
        $request_data['uid']    = (string) $this->request->session()->read('Auth.User.id');
        $table                  = TableRegistry::get('FicsExtend');
        $entity                 = $table->find("all")->where(array('vol_id' => $request_data["vol_id"]))->first();
        //赋值信息
        $request_data['name']       = $request_data["name"];
        $request_data['password']   = $request_data['password'];
        $request_data['regionCode'] = $entity->region_code;
        $request_data['storeType']  = $entity->vol_type;
        $request_data['storeCode']  = $entity->store_code;
        unset($request_data["vol_id"]);
        $result = $orders->ajaxFun($request_data);
        if ($result["Code"] == "0") {
            $request_data['method']      = "store_setRoot";
            $request_data['volume_name'] = $entity->vol_name;
            $request_data['username']    = $request_data["name"];
            $request_data['limit']       = $request_data["type"];
            $result                      = $orders->ajaxFun($request_data);
        }
        return $result;
    }

    public function delUser($request_data = array())
    {
        // debug($request_data);die();
        $orders                 = new OrdersController();
        $uid                    = (string) $this->request->session()->read('Auth.User.id');
        $request_data['method'] = "store_delRoot";
        $request_data['uid']    = (string) $this->request->session()->read('Auth.User.id');
        $table                  = TableRegistry::get('FicsExtend');
        $result                 = $orders->ajaxFun($request_data);
        if ($result["Code"] == "0") {
            $request_data['method'] = "store_delUser";
            $request_data['name']   = $request_data["username"];
            $result                 = $orders->ajaxFun($request_data);
        }
        return $result;
    }

    public function savefics($request_data = array())
    {
        $code        = '0000';
        $msg         = "操作成功";
        $accounts    = TableRegistry::get('FicsVolAccount');
        $id          = $request_data["id"];
        $template_id = $request_data["template_id"];
        $array       = explode(',', $request_data["account"]);
        $array1      = explode(',', $request_data["account1"]);
        foreach ($array1 as $key => $value) {
            $del_result = $accounts->deleteAll(['basic_id' => $id, 'account_id' => $value, 'template_id' => $template_id]);
        }
        foreach ($array as $key => $value) {
            if ($value != "") {
                $fics              = $accounts->newEntity();
                $fics->basic_id    = $id;
                $fics->account_id  = $value;
                $fics->template_id = $template_id;
                if (!$accounts->save($fics)) {
                    $code = "0500";
                    $msg  = "操作失败";
                }
            }
        }
        return compact(array_values($this->_serialize));
        //删除关系
    }

    public function deleteId($request_data = array())
    {
        $vol_id_array = explode(',', $request_data['id']);
        $orders                 = new OrdersController();
        $uid                    = (string) $this->request->session()->read('Auth.User.id');
        $request_data['method'] = "store_del";
        $request_data['uid']    = (string) $this->request->session()->read('Auth.User.id');
        $table                  = TableRegistry::get('FicsExtend');
        $entity                 = $table->find("all")->where(array('vol_id in' => $vol_id_array))->toArray();
        if (!empty($entity)) {
            foreach ($entity as $k => $v) {
                //赋值信息
                $request_data['storeCode']  = $v->store_code;
                $request_data['vol_id']     = (string)$v->vol_id;
                $request_data['regionCode'] = $v->region_code;
                $request_data['storeType']  = $v->vol_type;
                $request_data['name']       = $v->vol_name;
                $orders->ajaxFun($request_data);
            }
        }
        
        echo '{"code":"0", "Message":"操作成功"}';exit;
    }

    /**
     * 编辑数据列表
     */
    public function edit($request_data = array())
    {
        $code = '0000';
        $data = array();
        // 编辑操作
        $host        = TableRegistry::get('FicsExtend');
        $fics_info     = $host->get($request_data['vol_id']);
        
        $send_array = array();
        $send_array['storeCode']    = (string)$fics_info->store_code;
        $send_array['regionCode']   = (string)$fics_info->region_code;
        $send_array['storeType']    = (string)$fics_info->vol_type;
        $send_array['name']  = (string)$fics_info->vol_name;
        $send_array['total_cap']    = (string)$request_data['total_cap'];
        $send_array['warn_level']   = (string)$fics_info->warn_cap;
        $send_array['uid']          = (string)$this->request->session()->read('Auth.User.id');
        $send_array['method']       = 'store_edit';
        $send_array['id']           = (string)$fics_info->vol_id;
       
        $url = Configure::read('URL');
        $interface = $this->postInterface($url, $send_array);
        //调用接口
        if ($interface['Code'] != '0') {
            echo json_encode($interface);
            die;
        }
        $msg = Configure::read('MSG.' . $code);
        return compact(array_values($this->_serialize));
    }

    public function createStroArray()
    {
        $str   = array();
        $agent = TableRegistry::get('Agent');
        $where = array('is_enabled' => 1, 'is_desktop' => 1);
        //获取商品信息，包含商品分类
        // debug($agent->find('all')->contain(array('AgentImagelist', 'AgentSet'))->where($where)->order(array('Agent.sort_order' => 'ASC')));die;
        $agentInfo = $agent->find('all')->contain(array('AgentImagelist', 'AgentSet'))->where($where)->order(array('Agent.sort_order' => 'ASC'))->toArray();
        $str       = array();
        foreach ($agentInfo as $index => $item) {
            if ($item['parentid'] == 0) {
                $agent            = array();
                $agent['company'] = array('name' => $item['agent_name'], 'companyCode' => $item['agent_code']);
                $agent['area']    = $this->getAreaListById($item['id'], $agentInfo);
                $str[]            = $agent;
            }
        }
        // die;
        return $str;
    }

    public function getAreaListById($id, $agentInfo)
    {
        $str               = array();
        $store_agent_table = TableRegistry::get('StoreAgent');
        $store_table       = TableRegistry::get('Store');
        foreach ($agentInfo as $index => $item) {
            if ($item['parentid'] == $id) {
                $stor_agent = $store_agent_table->find("all")->where(array('region_code' => $item["region_code"]))->toArray();
                $store_data = array();
                foreach ($stor_agent as $key => &$v) {
                    $d          = $store_table->find("all")->where(array('region_code' => $item["region_code"], 'store_type' => $v['type']))->toArray();
                    $v["store"] = $d;
                    $d[0]['type'] = isset($d[0]['type']) ? $d[0]['type'] : 'h9000';
                    $v["price"] = HostsController::getPrice($id, $d[0]['type'], '元/');
                }
                $str[] = array('name' => $item['agent_name'], 'areaCode' => $item['region_code'], 'storeType' => $stor_agent);
            }
        }
        return $str;
    }

    /**
     * fics关联
     * @author wangjincheng
     *
     */
    public function relevanceDeviceList($request_data)
    {
        $instance_basic_table = TableRegistry::get("instance_basic");

        $data = $instance_basic_table->test($request_data);

        $this->paginate['limit']  = $request_data['limit'];
        $this->paginate['page']   = $request_data['offset'] / $request_data['limit'] + 1;
        $this->_pageList['total'] = $data->count();
        $this->_pageList['rows']  = $this->paginate($data);
        return $this->_pageList;
    }


    /**
     * 设置fics关联
     * @author wangjincheng
     *
     */
    public function setFicsRelationHosts($request_data)
    {
        $code                       = "0";
        $msg                        = "操作成功";
        $agent_table                = TableRegistry::get('Agent');
        $host_extend_table          = TableRegistry::get('HostExtend');
        $instance_basic_table       = TableRegistry::get('InstanceBasic');
        $fics_extend_table          = TableRegistry::get('FicsExtend');
        $store_table                = TableRegistry::get('Store');
        $fics_users_table           = TableRegistry::get('FicsUsers');
        $store_user_p_table         = TableRegistry::get("StoreUserP");
        $fics_relation_device_table = TableRegistry::get("FicsRelationDevice");
        $hosts_network_card_table = TableRegistry::get("HostsNetworkCard");
        $systemsetting_table = TableRegistry::get("Systemsetting");

        $fics_extend_data = $fics_extend_table->find()->where(['vol_id' => $request_data['id']])->first();
        if (isset($fics_extend_data) && !empty($fics_extend_data['vol_name'])) {
            $request_data['vol_name'] = $fics_extend_data['vol_name'];
            $request_data['vol_id'] = $fics_extend_data['vol_id'];
            $request_data['vol_type'] = $fics_extend_data['vol_type'];
            $request_data['store_code'] = $fics_extend_data['store_code'];
            $request_data['region_code'] = $fics_extend_data['region_code'];
            $store_data               = $store_table->find()
                ->where([
                    'store_code' => $fics_extend_data['store_code'],
                ])->first();
            $request_data['store_ip'] = $store_data['ip'] ;
            $request_data['nfs_ip'] = $store_data['nfs_ip'];
            //通过设备获取ip

            $request_data['subnetCode'] = $store_data['subnetCode'];
            if ($request_data['authority'] != "2" && !($request_data['type'] == "mount" && $request_data['access_type'] == "nfs")) {
                $authority_data = $store_user_p_table->find()->where(['limit' => $request_data['authority'], "vol_name" => $request_data["vol_name"]])->toArray();
                if (!empty($authority_data)) {
                    foreach ($authority_data as $value) {
                        $a_v_array[] = $value['user_id'];
                    }
                    $user = $fics_users_table->find()->where(["userid in" => $a_v_array])->first();
                } else {
                    $code = "1";
                    $msg  = "没有对应权限的账号，请先添加账号";
                    return compact(array_values($this->_serialize));exit;
                }
            }
        } else {
            $code = "1";
            $msg  = "没有对应的存储卷";
            return compact(array_values($this->_serialize));exit;
        }

        $basic_id_array = explode(',', $request_data['basic_id']);
        foreach ($basic_id_array as $k => $v) {
            $host_extend_data    = $host_extend_table->find()->select(['plat_form','os_family'])->where(['basic_id' => $v])->first();
            $instance_basic_data = $instance_basic_table->find()->where(['id' => $v])->first();
            $agent_data          = $agent_table->find()->where(['class_code' => $instance_basic_data['location_code']])->first();
            if ($request_data['authority'] != "2"  && !($request_data['type'] == "mount" && $request_data['access_type'] == "nfs")) {
                
                $request_data['username'] = $user['name'];
                $request_data['password'] = $user['password'];
            }
            if ($request_data['authority'] != "2"){
                if($request_data['type'] == 'net use' && stripos($host_extend_data['os_family'],'win') === false){
                    $code = '1';
                    $msg = 'net use 仅windows系统下支持';
                    return compact(array_values($this->_serialize));exit;
                }
                if($request_data['type'] == 'mount' && !(stripos($host_extend_data['os_family'],'linux') !== false || stripos($host_extend_data['os_family'],'centos') !== false )){
                    $code = '1';
                    $msg = 'mount 仅linux系统下支持';
                    return compact(array_values($this->_serialize));exit;
                }
            }
            $request_data['basic_id']  = $v;
            $request_data['plat_form'] = $host_extend_data['plat_form'];
            if ($request_data['authority'] != "2" && $request_data["type"] == "net use") {
                $relation_data = $fics_relation_device_table->find()->where(['basic_id' => $request_data['basic_id'], "label" => strtolower($request_data['drive'])])->first();
                if (!empty($relation_data)) {
                    $code = "2";
                    $msg  = "所选盘符重复，请重新选择，主机名称为：" . $instance_basic_data["name"];
                    return compact(array_values($this->_serialize));exit;
                }

            }

            //获取主机网卡的信息
            $card_data["ip"] = "";
            $systemsetting_data = $systemsetting_table->find()->where(['para_code'=>"fics_subnet_code"])->first();
            if(isset($systemsetting_data["para_value"]) && !empty($systemsetting_data["para_value"])){
                $card_data = $hosts_network_card_table->find()->where(["subnet_code" => $systemsetting_data["para_value"], "basic_id" => $v])->first();
            }
            $request_data["basic_ip"] = "";
            if(!isset($card_data["ip"]) || empty($card_data["ip"])){
                $card_data = $hosts_network_card_table->find()->where(["is_default" => "1", "basic_id" => $v])->first();
                $request_data["basic_ip"] = $card_data["ip"];
            } else {
                $request_data["basic_ip"] = $card_data["ip"];
            }
            // $request_data["basic_ip"] = "10.10.28.200"; #测试用

             $request_data['ip'] =$this->_getCifsIp($v);
            /**
                    在store_ip 里面取该机器没使用过的ip
           * */
                    
            $this->_relationHosts($request_data);
        }

        $data = "";
        return compact(array_values($this->_serialize));
    }

    protected function _getCifsIp($basic_id){
        $store_ip_table = TableRegistry::get("StoreIp");
        $ips = $store_ip_table->find()->where([]);
        $fics_relation_device = TableRegistry::get("FicsRelationDevice");

        foreach ($ips as $key => $row) {
            $r = $fics_relation_device ->find()->where([
                "parameter LIKE" =>"%".$row->ip."%",
                "basic_id" => $basic_id
            ])->first();
            if(!isset($r->id)){
                return $row->ip;
            }
        }
    }
    /**
     *@author wnagjincheng
     * 关联主机
     */
    protected function _relationHosts($data)
    {
        $store_user_p_table         = TableRegistry::get("StoreUserP");
        $fics_users_table           = TableRegistry::get("FicsUsers");
        $fics_relation_device_table = TableRegistry::get("FicsRelationDevice");

        $path      = '';
        $label     = '';
        $parp      = array();
        $uninstall = array();
        //$data['ip'] = "static.com";
        $data['real_name'] = $data['vol_name'];
        $data['vol_name'] = $data['vol_name'] . "_share";



        if($data['authority'] == 2){
            $data['type'] = '';
            $path         = '';
            $parp         = array();
            $uninstall    = array();
        } else {
            if ($data["vol_type"] == "fics") {
                $path = "FShell mount ".strtoupper($data["drive"])." ".$data["store_ip"]." ".$data["username"]." ".$data["password"]." ".$data["basic_ip"]." ".$data["vol_name"];
                $parp[] = "FShell";
                $parp[] = "mount";
                $parp[] = strtoupper($data["drive"]); #盘符
                $parp[] = $data["store_ip"]; #服务器ip
                $parp[] = $data["username"]; #用户名
                $parp[] = $data["password"];  #密码
                $parp[] = $data["basic_ip"];  #主机ip
                $parp[] = $data["vol_name"];  #卷名
                $parp[] = $data["id"];  #卷名
                //卸载
                $uninstall[] = "FShell";
                $uninstall[] = "umount";
                $uninstall[] = $data["drive"];
                $uninstall[] = $data["id"];

                $label = strtolower($data['drive']);

                $data['type'] = "mount";
            } else {
                $data['vol_name'] = $data['vol_name'];

                switch ($data['plat_form']) {
                    case 'Windows':
                    case '云主机':
                    case "Adobe":
                        switch ($data['type']) {
                            case 'net use':
                                $path = "net use ".strtolower($data['drive']).": \\\\".$data['ip']."\\".$data['vol_name']." ".$data["password"]." /user:".$data["username"]." /persistent:yes";
                                $parp[] = "net";
                                $parp[] = "use";
                                $parp[] = strtolower($data['drive']).":";
                                $parp[] = "\\\\".$data['ip']."\\".$data['vol_name'];
                                $parp[] = $data["password"];
                                $parp[] = "/user:".$data["username"];
                                $parp[] = "/persistent:yes";

                                //卸载
                                $uninstall[] = "net";
                                $uninstall[] = "use";
                                $uninstall[] = strtolower($data['drive']).":";
                                $uninstall[] = "/delete";
                                $uninstall[] = "/yes";

                                $label = strtolower($data['drive']);
                                break;
                            case 'mount':
                                if ($data['access_type'] == "nfs") {
                                $path = "mount -t nfs -o rw " . $data['ip'] . ":/" . $data['vol_name'] . " " . $data['path'];
                                $parp = explode(" ", $path);
                            } else {
                                $path      = "mount \\\\" . $data['ip'] . "\\" . $data['vol_name'] . " " . $data['path'] . " -o codepage=cp936,iocharset=utf8,username=" . $data['username'] . ",password=" . $data['password'];
                                $parp["0"] = "mount";
                                $parp["1"] = "\\\\" . $data['ip'] . "\\" . $data['vol_name'];
                                $parp["2"] = $data['path'];
                                $parp["3"] = "-o";
                                $parp["4"] = "codepage=cp936,iocharset=utf8,username=" . $data['username'] . ",password=" . $data['password'];
                            }
                                break;
                        }
                        break;
                    default:
                        switch ($data['type']) {
                            case 'net use':
                                $path = "net use ".strtolower($data['drive']).": //".$data['ip']."/".$data['vol_name']." ".$data["password"]." /user:".$data["username"]." /persistent:yes";
                                $parp[] = "net";
                                $parp[] = "use";
                                $parp[] = strtolower($data['drive']).":";
                                $parp[] = "//".$data['ip']."/".$data['vol_name'];
                                $parp[] = $data["password"];
                                $parp[] = "/user:".$data["username"];
                                $parp[] = "/persistent:yes";

                                //卸载
                                $uninstall[] = "net";
                                $uninstall[] = "use";
                                $uninstall[] = strtolower($data['drive']).":";
                                $uninstall[] = "/delete";
                                $uninstall[] = "/yes";

                                $label = strtolower($data['drive']);
                                break;
                            case 'mount':
                                if ($data['access_type'] == "nfs") {
                                    $path = "mount -t nfs " . $data['nfs_ip'] . ":/" . $data['real_name'] . " " . $data['path'];
                                    $parp = explode(" ", $path);
                                } else {
                                    $path = "mount -t cifs //".$data['ip']."/".$data['vol_name']." ".$data['path']." -o iocharset=utf8,username=".$data['username'].",password=".$data['password'];
                                    $parp = explode(" ", $path);
                                }
                            break;
                        }
                       break;
                }
            }
        }
     
        $oldRow  =  $fics_relation_device_table->find()->where([
                              'basic_id'=>$data['basic_id'],
                              'vol_id'=>$data['id']
        ])->first();

        $iscanel = false;
        $isopen=false;
       
         if($data['type'] == "mount" && $data['access_type'] == "nfs" && $data['authority'] != 2  ){
            if(!isset($oldRow->id)){
                $isopen=true;
            }else if(!strstr($oldRow->parameter,"mount -t nfs")){
                $isopen=true;
            }
        }

           //如果是取消授权需要删除记录
        if($data['authority'] == 2){
             $fics_relation_device_table->deleteAll(['basic_id'=>$data['basic_id'],'vol_id'=>$data['id']]);
             if(isset($oldRow->id) && strstr($oldRow->parameter,"mount -t nfs") ){
                    $iscanel = true;
             }
        }

        if($data['access_type'] != "nfs" || $data['authority'] == 2){
            if(!isset($oldRow->id)){
                $iscanel=false;
            }else if(strstr($oldRow->parameter,"mount -t nfs")){
                $iscanel=true;
            }
        }

        if($data['authority'] != 2){
            if(isset($oldRow->id)){
                    $relation_data = $oldRow;
            }else{
                    $relation_data  = $fics_relation_device_table->newEntity();
            }
       
            $relation_data['vol_id'] = $data['id'];
            $relation_data['basic_id'] = $data['basic_id'];
            $relation_data['type'] = $data['type'];
            $relation_data['parameter'] = $path;
            $relation_data['authority'] = $data['authority'];
            $relation_data['json_parp'] = json_encode($parp);
            $relation_data['json_uninstall'] = json_encode($uninstall);
            $relation_data['label'] = $label;
            $fics_relation_device_table->save($relation_data);
        }
        $orders = new OrdersController();
        $uid = (string) $this->request->session()->read('Auth.User.id');

        if ($isopen) {
            //调用接口授权
            $apidata['method'] = "store_SetNfsAccess";
            $netCardTable       = TableRegistry::get("hostsNetworkCard");
            $netCard = $netCardTable->find()->where([
                        'basic_id'=>$data['basic_id'],
                        'subnet_code'=>$data['subnetCode']
                ])->first();
            //ip
            $apidata['client_ip'] = $netCard->ip;
            $apidata['uid'] = $uid;
            $apidata['volume_name'] = $data['real_name'];
            $apidata['storeType']=$data['vol_type'];
            $apidata['regionCode']=$data['region_code'];
            $apidata['storeCode']=$data['store_code'];
            if($apidata['storeType'] =="fics"){
                      $apidata['limit'] = "755";
            }else{
                  $apidata['limit'] = "1";
            }
            $orders->ajaxFun($apidata); 
        }
        if ($iscanel) {
            //调用接口授权
            $apidata['method'] = "store_DelNfsAccess";
            $netCardTable       = TableRegistry::get("hostsNetworkCard");
            $netCard = $netCardTable->find()->where([
                        'basic_id'=>$data['basic_id'],
                        'subnet_code'=>$data['subnetCode']
                ])->first();
            //ip
            $apidata['client_ip'] = $netCard->ip;
            $apidata['uid'] = $uid;
            $apidata['volume_name'] = $data['real_name'];
            $apidata['storeType']=$data['vol_type'];
            $apidata['regionCode']=$data['region_code'];
            $apidata['storeCode']=$data['store_code'];
            $orders->ajaxFun($apidata); 
        }
    }

    /**
     *@author wangjincheng
     *检查圈名是否存在 
     * 0不存在，1存在  
     */

    public function checkVolName($request_data)
    {
        $fics_extend_table = TableRegistry::get("FicsExtend");

        $fics_data = $fics_extend_table->find()->where(['vol_name' => $request_data["vol_name"]])->first();
        if (empty($fics_data)){
            echo 0;exit;
        }
        echo 1;exit;
    }


    /**
     *@author wangjincheng
     *检查华为9K用户名是否存在 
     * 0不存在，1存在  
     */

    public function checkVolUser($request_data)
    {

        $fics_users_table = TableRegistry::get("FicsUsers");

        $fics_data = $fics_users_table->find()->where(['name' => $request_data["name"]])->first();
        if (empty($fics_data)){
            echo json_encode(array('valid' => 'true'));exit;
        }
        echo json_encode(array('valid' => 'false'));exit;
    }
    
    /**
     * @func:调用接口
     * @param:@url 接口地址
     *        @$array 接口参数
     * @date: 2015年10月10日 下午3:08:59
     * @author: shrimp liao
     * @return: null
     */
    public function postInterface($url, $array)
    {
        set_time_limit(0);
        //0为无限制
        $http          = new Client();
        $obj_response  = $http->post($url, json_encode($array), array('type' => 'json'));
        $data_response = json_decode($obj_response->body, true);
        return $data_response;
    }

}
