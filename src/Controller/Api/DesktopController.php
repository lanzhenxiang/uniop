<?php
/**
 * ==============================================
 * class.php
 * @author: zhaodanru
 * @date: 2016年3月31日 下午2:17:34
 * @version: v1.0.0
 * @desc:
 * ==============================================
 **/

namespace App\Controller\Api;
use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Log\Log;
use Composer\Autoload\ClassLoader;
use Cake\Network\Http\Client;
use Requests as Requests;
use App\Controller\Api\Utility\Citrix;
class DesktopController extends AppController
{
    //接口属性
    private $_data = null;
    private $_error = null;
    private $_serialize = array('code', 'msg', 'data');
    private $_code = 0;
    private $_msg = '';
    private $_is_auth = false;


    /**
     * 重写init函数
     * {@inheritDoc}
     * @see \App\Controller\AppController::initialize()
     */
    public function initialize()
    {
        parent::initialize();
        //修改视图类
        $this->viewClass = 'Json';
        //加载组件
        $this->loadComponent('RequestHandler');
        //获取参数
        $this->_data = $this->_getData();
        $this->_db = ConnectionManager::get('default');


        $loader = new ClassLoader();
        $loader->add('Requests', ROOT.DS.'vendor/requests');
        $loader->register();
        Requests::register_autoloader();
    }


    public function getDesktopList(){
        //获取桌面列表
        $return_data = array('code'=>'0','data'=>'','msg'=>'');
//        $conn = ConnectionManager::get('default');

        //查询条件
//        $where = '';
        if(!empty($this->_data)){
            if(isset($this->_data['department_id'])){
//                $where = ' and a.department_id ='.$this->_data['department_id'];
                $where['InstanceBasic.department_id'] = $this->data['department_id'];
            }

            if(isset($this->_data['desktop_name'])){
                $where['InstanceBasic.name like'] = "%".$this->_data['desktop_name']."%";
//                $where = ' and a.name like "%'.$this->_data['desktop_name'].'%"';
            }

            if(isset($this->_data['ecs_code'])){
                $where['InstanceBasic.code like'] = "%".$this->_data['ecs_code']."%";
//                $where = ' and a.code like "%'.$this->_data['ecs_code'].'%"';
            }
        }
//$sql="SELECT
//    a.`id` AS basic_id,
//    a.`name`,
//    a.`code`,
//    a.`location_code`,
//    a.`status`,
//    a.`description`,
//    a.`location_name`,
//    a.`vpc`,
//    a.`router`,
//    a.`subnet`,
//    b.`name` AS hostname,
//    b.`ip`,
//    b.`cpu`,
//    b.`gpu`,
//    b.`memory`,
//    b.aduser AS aduser,
//    b.adpwd AS adpass,
//    d.`dept_code` AS department_code,
//    d.`name` AS department_name
//FROM
//    cp_instance_basic AS a
//LEFT JOIN cp_host_extend AS b ON a.id = b.basic_id
//LEFT JOIN cp_departments AS d ON a.department_id = d.id
//WHERE
//    a.isdelete = 0
//AND a.type = 'desktop'
//AND a. CODE <> ''
//AND a. CODE IS NOT NULL
//AND (
//    a.`status` = '运行中'
//    OR a.`status` = '已停止'
//)";
//$sql.=$where;

            $field = [
                'basic_id'=>'InstanceBasic.id','name'=>'InstanceBasic.name','code'=>'InstanceBasic.code',
                'location_code'=>'InstanceBasic.location_code','status'=>'InstanceBasic.status',
                'InstanceBasic.description','InstanceBasic.location_name','InstanceBasic.vpc',
                'InstanceBasic.router','InstanceBasic.subnet','hostname'=>'hostExtend.name',
                'ip'=>'hostExtend.ip','cpu'=>'hostExtend.cpu','gpu'=>'hostExtend.gpu','memory'=>'hostExtend.memory',
                'hostname'=>'hostExtend.aduser','adpass'=>'hostExtend.adpwd',
                'department_code'=>'departments.dept_code','department_name'=>'departments.name',
                ];

            $where['InstanceBasic.isdelete']= 0;
            $where['InstanceBasic.type']= 'desktop';
            $where['InstanceBasic.CODE <>']= '';
            $where['OR'] = [
                ['InstanceBasic.status'=>'运行中'],
                ['InstanceBasic.status'=>'已停止']
            ];

            $instanceBasic = TableRegistry::get('InstanceBasic');
            $return_data['data'] =$instanceBasic->find()->select($field)->where($where)->join([
                'hostExtend'=>[
                    'table'=>'cp_host_extend',
                    'type'=>'LEFT',
                    'conditions'=>'hostExtend.basic_id = InstanceBasic.id'
                ],
                'departments'=>[
                    'table'=>'cp_departments',
                    'type'=>'LEFT',
                    'conditions'=>'departments.id = InstanceBasic.department_id'
                ]
            ])->toArray();

/*         $sql ="SELECT "
            ."a.`id` AS basic_id,a.`name`,a.`code`,a.`location_code`,a.`status`,a.`description`,a.`location_name`,a.`vpc`,a.`router`,a.`subnet`,"
            ."b.`name` AS hostname,b.`ip`,b.`cpu`,b.`gpu`,b.`memory`,c.`loginName` AS aduser,c.`loginPassword` AS adpass,d.`dept_code` AS department_code,d.`name` AS department_name "
            ." FROM cp_instance_basic AS a"
            ." LEFT JOIN cp_host_extend AS b ON a.id = b.basic_id"
            ." LEFT JOIN cp_ad_user AS c ON b.`aduser` = c.loginName and a.vpc = c.vpcCode AND b.`aduser` <> '' AND b.`aduser` IS NOT NULL"
            ." LEFT JOIN cp_departments AS d on a.department_id = d.id"
            ." where a.isdelete=0 and a.type='desktop' and a.code <> '' and a.code is not null and (a.`status` = '运行中' or a.`status` = '已停止')".$where;

 */
//        $return_data['data'] = $conn->execute($sql)->fetchAll('assoc');
        if(empty($return_data['data'])){
            $return_data['code']='1';
            $return_data['msg']='没有返回数据';
        }

        echo json_encode($return_data);exit;

    }


    //返回租户列表
    public function getDepartmentList(){
        $departments = TableRegistry::get('Departments');
        $dept_data = $departments->find('all')->select(['id','name'])->toArray();
        echo json_encode(array('code'=>0,'data'=>$dept_data));exit;
    }

    //获取ctrix的地址和证书
    public function getCtrixData(){

        $vpcCode = $this->_data['vpcCode'];
        $where = '';
        $return_data = array('code'=>'0','data'=>'','msg'=>'');
        if($vpcCode){
//            $where = " and code = '$vpcCode'";
//
//            $sql = "SELECT desktop_server_url,cer_url FROM cp_vpc_extend where basic_id=(SELECT id FROM cp_instance_basic where type = 'vpc' $where)";
//            $conn = ConnectionManager::get('default');
//            $return_data['data'] = $conn->execute($sql)->fetchAll('assoc');

            $instanceBasic = TableRegistry::get('InstanceBasic');
            $return_data['data'] = $instanceBasic->find()->join([
                'vpcExtend'=>[
                    'table'=>'cp_vpc_extend',
                    'type' =>'INNER',
                    'conditions'=>'InstanceBasic.id = vpcExtend.basic_id'
                ]
            ])->select(['desktop_server_url'=>'vpcExtend.desktop_server_url','cer_url'=>'vpcExtend.cer_url'])->where(['InstanceBasic.type'=>'vpc','InstanceBasic.code'=>$vpcCode])->toArray();


            if(empty($return_data['data'])){
                $return_data['code']='1';
                $return_data['msg']='没有返回数据';
            }
        }else{
            $return_data = array('code'=>'1','data'=>'','msg'=>'未传vpccode');
        }
        echo json_encode($return_data);exit;
    }


    /**
     * 从http报文中获取参数
     */
    protected function _getData()
    {
        $data = $this->request->data ? $this->request->data : file_get_contents('php://input', 'r');
        //处理非x-form的格式
        if (is_string($data)) {
            $data_tmp = json_decode($data, true);
            if (json_last_error() == JSON_ERROR_NONE) {
                $data = $data_tmp;
            }
        }
        return $data;
    }
    //桌面管理系统发送的消息提醒和关机
    public function SendNotify($reqdata = []){
        $reqdata = empty($reqdata) ? $this->_data : $reqdata;
        $hostname = strtoupper($reqdata['hostname']);
        $method=$reqdata['method'];
        $msg=$reqdata['msg'];
        $current_time='';//time();
        $delaytime=0;
        if(isset($reqdata['delaytime']))
            $delaytime=$reqdata['delaytime'];
        $urls = Configure::read('NotifyUrl');
        $data = [];
        $msg = "123123";
        foreach ($urls as $url)
        {
            try {
                // 通知消息服务器
                $response_notify_obj = @Requests::post($url.'/send',[],[
                    'time'=>$current_time,
                    'sign'=>'9a3517f00669f77fe85eb5693c07eb1c',
                    'data'=>json_encode([
                        'SendType'=>'websocket',
                        'Msg'=>$msg,
                        'Uid'=>'sobeyDesktop-'.$hostname,
                        'Data'=>[
                            'method'=>$method,//,//logoff / notify
                            'delaytime'=>'$delaytime'
                        ]
                    ]),
                ]);
                $response = json_decode($response_notify_obj->body,true);

                //启动成功
                if ($response['code'] == 0){
                    $data[] = "已发送至".$url;
                }else{
                    $data[] = "发送至".$url."失败";
                }
            } catch (\Exception $e) {
                $data[] = "发送至".$url."失败";
                Log::error($e->getMessage().':'.$url);
                continue;
            }

       }
       //渲染视图
       $code = 0;

       $this->set(compact(array_values($this->_serialize)));
       $this->set('_serialize',$this->_serialize);
    }

    /**
     * 获取主机关联的存储--通用
     */
    public function GetHostsFicsList()
    {
        $name = $this->_data['name'];
        $host_extend_table = TableRegistry::get("HostExtend");
        $data = $host_extend_table->find()->where(['name'=>$name])->first();
        if (!empty($data)) {
            $id = $data['basic_id'];

            $fics_relation_device_table = TableRegistry::get("FicsRelationDevice");
            $data = $fics_relation_device_table->find()->where(['basic_id'=>$id,'type <>' => 'unc', 'json_parp <>' => '[]'])->toArray();
            $fics_list = array();
            if(!empty($data)){
                foreach ($data as  $v) {
                    $fics_list[] = json_decode($v['json_parp']);
                }

            }
            $code = "0";
            $msg = "OK";
            $data = $fics_list;
        } else {
            $code = "1";
            $msg = "没有找到主机";
            $data = "";
        }

        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }

    /**
     * 获取主机关联的存储--fics--随时控制挂载卸载
     */
    public function GetDesktopFicsList()
    {
        $name = $this->_data['name'];
        $host_extend_table = TableRegistry::get("HostExtend");
        $data = $host_extend_table->find()->where(['name'=>$name])->first();
        if (!empty($data)) {
            $id = $data['basic_id'];

            $fics_relation_info_table = TableRegistry::get("FicsRelationInfo");
            $data = $fics_relation_info_table->find()->where(['basic_id' => $id, 'status' => '1', 'type <>' => 'unc','is_operate' => 0])->toArray();
            $fics_relation_info_table->query()->update()->set(['is_running' => 1])->where(['basic_id' => $id, 'status' => '1', 'type <>' => 'unc', 'is_operate' => 0])->execute();
            $fics_relation_info_table->query()->update()->set(['is_operate' => 1])->where(['basic_id' => $id, 'status' => '1', 'type <>' => 'unc'])->execute();
            $fics_list = array();
            if(!empty($data)){
                foreach ($data as  $v) {
                    $fics_list[] = json_decode($v['json_parp']);
                }
            }
            $code = "0";
            $msg = "OK";
            $data = $fics_list;
        } else {
            $code = "1";
            $msg = "没有找到主机";
            $data = "";
        }
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }

    /**
     * 获取主机关联的存储---卸载
     */
    public function GetHostsFicsListUninstall()
    {
        $name = $this->_data['name'];
        $host_extend_table = TableRegistry::get("HostExtend");
        $data = $host_extend_table->find()->where(['name'=>$name])->first();
        if (!empty($data)) {
            $id = $data['basic_id'];

            $fics_relation_device_table = TableRegistry::get("FicsRelationDevice");
            $data = $fics_relation_device_table->find()->where(['basic_id'=>$id,'type <>' => 'unc'])->toArray();
            $fics_list = array();
            if(!empty($data)){
                foreach ($data as  $v) {
                    $fics_list[] = json_decode($v['json_uninstall']);
                }

            }
            $code = "0";
            $msg = "OK";
            $data = $fics_list;
        } else {
            $code = "1";
            $msg = "没有找到主机";
            $data = "";
        }

        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }

    /**
     * 获取主机关联的存储---随时控制挂载卸载
     */
    public function GetDesktopFicsListUninstall()
    {
        $name = $this->_data['name'];
        $host_extend_table = TableRegistry::get("HostExtend");
        $data = $host_extend_table->find()->where(['name'=>$name])->first();
        if (!empty($data)) {
            $id = $data['basic_id'];
            $fics_relation_info_table = TableRegistry::get("FicsRelationInfo");
            $data = $fics_relation_info_table->find()->where(['basic_id' => $id, 'status <>' => "1",'type <>' => 'unc', 'is_operate'=>0])->toArray();
            $fics_relation_info_table->query()->update()->set(['is_running' => 1])->where(['basic_id' => $id, 'status' => '0', 'type <>' => 'unc', 'is_operate' => 0])->execute();
            $fics_relation_info_table->query()->update()->set(['is_operate' => 1])->where(['basic_id' => $id, 'status' => '0', 'type <>' => 'unc'])->execute();
            $fics_list = array();
            if(!empty($data)){
                foreach ($data as  $v) {
                    $fics_list[] = json_decode($v['json_uninstall']);
                }
            }
            $code = "0";
            $msg = "OK";
            $data = $fics_list;
        } else {
            $code = "1";
            $msg = "没有找到主机";
            $data = "";
        }

        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }


    //获取fics卷信息
    public function GetFicsList()
    {
        $this->_serialize = array('code', 'msg', 'requestId', 'data');
        $code = "0";
        $msg = "操作成功";
        $requestId = $this->_data["requestId"];
        $data = array();
        $fics_extend_table = TableRegistry::get("FicsExtend");
        $systemsetting_table = TableRegistry::get("Systemsetting");
        if (isset($this->_data["department_id"])) {
            $department_id = $this->_data["department_id"];
        } else {
            $department_id = 65;
        }
        $fics_data = $fics_extend_table->find("all")->where(["vol_type" => "fics","department_id" => $department_id])->toArray();
        if (!empty($fics_data)) {
            foreach ($fics_data as $value) {
                $f["basic_id"] = $value["vol_id"];
                $f["disk_code"] = $value["vol_name"];
                $f["disk_dispname"] = $value["display_name"];
                $f["disk_lable"] = $value["label"];
                $f["total_cap"] = $value["total_cap"];
                $data[] = $f;
            }
        }
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }

    /**
     * 设置fics关联
     * @author wangjincheng
     *
     */
    public function OperateFics()
    {
        $code = "0";
        $msg = "操作成功";
        $host_extend_table = TableRegistry::get('HostExtend');
        $fics_extend_table = TableRegistry::get('FicsExtend');
        $store_table = TableRegistry::get('Store');
        $fics_users_table = TableRegistry::get('FicsUsers');
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        $store_user_p_table = TableRegistry::get("StoreUserP");
        $fics_relation_log_table = TableRegistry::get("FicsRelationInfo");
        $hosts_network_card_table = TableRegistry::get("HostsNetworkCard");
        $systemsetting_table = TableRegistry::get("Systemsetting");


        //TODO 加锁，防止同时重复调用
        //检查机器是否在操作
        // $is_running = $fics_relation_log_table->find()->where(['basic_id' => $this->_data["host_basicid"],"is_running" => 1])->first();

        //检查主机
        $host_data = $instance_basic_table->find()->where(['id' => $this->_data["host_basicid"]])->first();

        if (!empty($host_data)) {
            //检查硬盘
            $diskinfo = $this->_data["diskinfo"];
            if(!empty($diskinfo)){

                //获取主机扩展信息
                $host_extend_data = $host_extend_table->find()->select(['plat_form','name'])->where(['basic_id' => $this->_data["host_basicid"]])->first();
                foreach ($diskinfo as  $d_v) {

                    // 获取硬盘信息
                    $fics_data = $fics_extend_table->find()->where(['vol_id'=>$d_v['disk_basicid']])->first();

                    if(isset($fics_data) && !empty($fics_data['vol_name'])){
                        $request_data['vol_id'] = $d_v['disk_basicid'];
                        $request_data['loginname'] = $fics_data['loginname'];
                        $request_data['vol_name'] = $fics_data['vol_name'];
                        $request_data['vol_type'] = $fics_data['vol_type'];
                        $request_data['action'] = $this->_data['action'];
                        $request_data['parameter'] = $this->_data;
                        //获取存储信息（目前只要ip）
                        $store_data = $store_table->find()
                            ->where([
                                'store_code'=>$fics_data['store_code']
                            ])->first();
                        $request_data['ip'] = $store_data['ip'];
                        $request_data['drive'] = $fics_data['label'];
                        //获取账户信息

                        $request_data['username'] = "";
                        $request_data['password'] = "";

                        switch ($d_v['readmode']) {
                            case 'rw':
                                $request_data['authority'] = 4;
                                break;
                            case 'r':
                                $request_data['authority'] = 0;
                                break;
                            default:
                                $request_data['authority'] = 2;
                                break;
                        }
                        $authority_data = $store_user_p_table->find()->where(['limit' => $request_data['authority'], "vol_name" => $fics_data['vol_name']])->first();
                        if(!empty($authority_data)){
                            $user_data = $fics_users_table->find()->where(["userid"=>$authority_data["user_id"], "storetype" => "fics"])->first();
                            if (!empty($user_data)) {
                                $request_data['username'] = $user_data['name'];
                                $request_data['password'] = $user_data['password'];
                            }
                        }


                        $request_data['basic_id'] = $this->_data["host_basicid"];
                        $request_data['plat_form'] = $host_extend_data['plat_form'];
                        $request_data['callback_url'] = $this->_data['callback_url'];
                        $request_data['id'] = $d_v['disk_basicid'];
                        $request_data['type'] = 'mount';

                        //获取主机网卡的信息
                        $card_data["ip"] = "";
                        $systemsetting_data = $systemsetting_table->find()->where(['para_code'=>"fics_subnet_code"])->first();
                        if(isset($systemsetting_data["para_value"]) && !empty($systemsetting_data["para_value"])){
                            $card_data = $hosts_network_card_table->find()->where(["subnet_code" => $systemsetting_data["para_value"], "basic_id" => $this->_data["host_basicid"]])->first();
                        }
                        $request_data["basic_ip"] = "";
                        if(!isset($card_data["ip"]) || empty($card_data["ip"])){
                            $card_data = $hosts_network_card_table->find()->where(["is_default" => "1", "basic_id" => $this->_data["host_basicid"]])->first();
                            $request_data["basic_ip"] = $card_data["ip"];
                        } else {
                            $request_data["basic_ip"] = $card_data["ip"];
                        }
                        $relation_id = (string)$this->_relationHosts($request_data);

                        // $relation_id = (string)$this->_relationHostsBylog($request_data);
                        // if ($relation_id == 0) {
                        //     $code = "3004";
                        //     $msg = "没有对应的挂载信息，";
                        // }
                    } else {
                        $code = "3003";
                        $msg = "没有对应的存储卷";
                        if ($this->_data['action'] == "mount") {
                            $action = "1";
                        } else {
                            $action = "0";
                        }
                        $fics_relation_log_table->deleteAll(['basic_id' => $this->_data["host_basicid"], 'status' => $action]);
                    }
                }
                //发送消息挂载
                if ($code == '0') {
                    //发送消息
                    $notify_data = array();
                    //发送消息
                    $notify_data['hostname'] = $host_extend_data['name'];
                    $notify_data['msg'] = '';
                    if ($this->_data['action'] == "mount") {
                        $notify_data['method'] = 'mount';
                    } else {
                        $notify_data['method'] = 'umount';
                    }
                    $this->SendNotify($notify_data);
                }
            } else {
                $code = "3002";
                $msg = "没有存储信息";
            }
        } else {
            $code = "3001";
            $msg = "主机不存在";
        }
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }

    /**
     *@author wnagjincheng
     * 关联主机
     */
    protected function _relationHosts($data)
    {
        $fics_relation_log_table = TableRegistry::get("FicsRelationInfo");

        if ($data['action'] == "mount") {
            $relation_data['status'] = "1";
        } else {
            $relation_data['status'] = "0";
        }

        $del_result = $fics_relation_log_table->deleteAll(['basic_id' => $data['basic_id'], 'vol_id' => $data['id']]);
        $path = '';
        $parp = array();

        if ($data["vol_type"] == "fics") {
            $parp[] = "FShell";
            $parp[] = "mount";
            $parp[] = strtoupper($data["drive"]); #盘符
            $parp[] = $data["ip"]; #服务器ip
            $parp[] = $data["username"]; #用户名
            $parp[] = $data["password"];  #密码
            $parp[] = $data["basic_ip"];  #主机ip
            $parp[] = $data["vol_name"];  #卷名
            $parp[] = $data["vol_id"];  #卷名
            //卸载
            $uninstall[] = "FShell";
            $uninstall[] = "umount";
            $uninstall[] = $data["drive"];
            $uninstall[] = $data["vol_id"];
        } else{
            switch ($data['plat_form']) {
                case 'Windows':
                case '云主机':
                case "Adobe":
                    switch ($data['type']) {
                        case 'unc':
                            $parp["0"] = $path;
                            break;
                        case 'net use':
                            $path = "net use ".strtolower($data['drive']).":\\\\".$data['ip']."\\".$data['vol_name'];
                            $parp[] = "net";
                            $parp[] = "use";
                            $parp[] = strtolower($data['drive']).":";
                            $parp[] = "\\\\".$data['ip']."\\".$data['vol_name'];
                            $parp[] = $user_data["password"];
                            $parp[] = "/user:".$user_data["name"];
                            $parp[] = "/persistent:yes";

                            //卸载
                            $uninstall[] = "net";
                            $uninstall[] = "use";
                            $uninstall[] = strtolower($data['drive']).":";
                            $uninstall[] = "/delete";
                            break;
                        case 'mount':
                            $path = "FShell mount ".strtoupper($data['drive'])." ".$data['ip']." ".$data['username']." ".$data['password']." ".$ip." ".$data['vol_name'];
                            $parp["0"] = "mount";
                            $parp["1"] = "//".$data['ip']."//".$data['vol_name'];
                            $parp["2"] = $data['path'];
                            $parp["3"] = "-o";
                            $parp["4"] = "codepage=cp936,iocharset=utf8,username=".$data['username'].",password=".$data['password'];

                            //卸载
                            $uninstall[] = "FShell";
                            $uninstall[] = "umount";
                            $uninstall[] = strtoupper($data['drive']);

                            break;
                        default:
                            break;
                    }
                    break;
                default:
                    switch ($data['type']) {
                        case 'unc':
                            $parp["0"] = $path;
                            break;
                        case 'net use':
                            $path = "net use ".strtolower($data['drive'])."://".$data['ip']."/".$data['vol_name'];
                            $parp[] = "net";
                            $parp[] = "use";
                            $parp[] = strtolower($data['drive']).":";
                            $parp[] = "//".$data['ip']."/".$data['vol_name'];
                            $parp[] = $user_data["password"];
                            $parp[] = "/user:".$user_data["name"];
                            $parp[] = "/persistent:yes";

                            //卸载
                            $uninstall[] = "net";
                            $uninstall[] = "use";
                            $uninstall[] = strtolower($data['drive']).":";
                            $uninstall[] = "/delete";
                            break;
                        case 'mount':
                            $parp["0"] = "mount";
                            $parp["1"] = "//".$data['ip']."//".$data['vol_name'];
                            $parp["2"] = $data['path'];
                            $parp["3"] = "-o";
                            $parp["4"] = "codepage=cp936,iocharset=utf8,username=".$data['username'].",password=".$data['password'];
                            break;

                        default:
                            break;
                    }
                   break;
            }
        }
        if($data['authority'] == 2){
            $data['type'] = '';
            $path = '';
            $parp = array();
        }

        $relation_data = $fics_relation_log_table->newEntity();
        $relation_data['label'] = strtoupper($data["drive"]);
        $relation_data['vol_id'] = $data['id'];
        $relation_data['basic_id'] = $data['basic_id'];
        $relation_data['type'] = $data['type'];
        $relation_data['parameter'] = $path;
        $relation_data['authority'] = $data['authority'];
        $relation_data['json_parp'] = json_encode($parp);
        $relation_data['json_uninstall'] = json_encode($uninstall);
        $relation_data['callback_url'] = $data['callback_url'];
        $relation_data['loginname'] = $data['loginname'];
        $relation_data['parameter'] = json_encode($data["parameter"]);
        if ($data['action'] == "mount") {
            $relation_data['status'] = "1";
        } else {
            $relation_data['status'] = "0";
        }
        $fics_relation_log_table->save($relation_data);

        return $relation_data->id;
    }

    /**
     * @author wangjincehng
     */
    public function _relationHostsBylog($data)
    {
        $fics_relation_log_table = TableRegistry::get("FicsRelationInfo");
        $fics_relation_device_table = TableRegistry::get("FicsRelationDevice");
        if ($data['action'] == "mount") {
            $info['status'] = "1";
        } else {
            $info['status'] = "0";
        }
        $del_result = $fics_relation_log_table->deleteAll(['basic_id' => $data['basic_id'], 'vol_id' => $data['id']]);
        $relation_data = $fics_relation_device_table->find()->where(['vol_id' => $data["id"], 'basic_id' => $data["basic_id"], 'authority' => $data["authority"]])->first();
        if (empty($relation_data)) {
            return 0;
        }

        $info = $fics_relation_log_table->newEntity();
        $info['vol_id'] = $data['id'];
        $info['basic_id'] = $data['basic_id'];
        $info['type'] = $data['type'];
        $info['authority'] = $data['authority'];
        $info['json_parp'] = $relation_data["json_parp"];
        $info['json_uninstall'] = $relation_data["json_uninstall"];
        $info['callback_url'] = $data['callback_url'];
        $info['loginname'] = $data['loginname'];
        $info['label'] = $relation_data['label'];
        $info['parameter'] = json_encode($data["parameter"]);
        if ($data['action'] == "mount") {
            $info['status'] = "1";
        } else {
            $info['status'] = "0";
        }
        $fics_relation_log_table->save($info);
        return $info->id;
    }


    public function OperateFicsCallback(){
        // debug($this->_data);die;
        $parp_list = json_decode($this->_data["parp"]);
        $res_list = json_decode($this->_data["res"]);

        //TODO 处理返回信息
        $fics_extend_table = TableRegistry::get("FicsExtend");
        $host_extend_table = TableRegistry::get("HostExtend");
        $fics_relation_log_table = TableRegistry::get("FicsRelationInfo");
        //获取主机信息
        $host_data = $host_extend_table->find()->where(['name' => $this->_data["name"]])->first();

        if($this->_data['action'] == "mount"){
            $status = 1;
        } else {
            $status = 0;
        }

        $relation_data = $fics_relation_log_table->find()->where(['basic_id' => $host_data["basic_id"], "status" => $status, "is_running" => 1])->toArray();
        $fics_relation_log_table->query()->update()->set(['is_running' => 0])->where(['basic_id' => $host_data["basic_id"], 'status' => $status, 'type <>' => 'unc'])->execute();
        // debug($relation_data)
        //正式环境有两个消息服务器，有时有两条请求回来，只用处理第一条
        if(empty($relation_data)){
            die;
        }

        $data = array();
        $data = (array)json_decode($relation_data[0]["parameter"]);
        $data["code"] = "0";
        $data["msg"]="调用成功";


        //拼接信息
        $res = array();
        $vol_id = 0;
        foreach ($parp_list as $key => $parp_array){
            //获取最后一位
            foreach ($parp_array as $parp) {
                $vol_id = $parp;
            }

           $res[$vol_id] = $res_list[$key];
        }

        foreach ($data["diskinfo"] as &$value) {
            $value = (array)$value;
            $fics_data = $fics_extend_table->find()->where(['vol_id' => $value["disk_basicid"]])->first();
            $value["mount_state"] = 0;
            $value["disk_lable"] = $fics_data["label"];

            $value["errmsg"] = "挂载成功";

            if($this->_data['action'] == "mount"){
                if($res[$value["disk_basicid"]] == "mount volume success.\r\n") {
                    $value["errmsg"] = "挂载成功";
                    $value["mount_state"] = "0";
                } else {
                    $value["errmsg"] = $res[$value["disk_basicid"]];
                    $value["mount_state"] = "1";
                }
            } else {
                if($res[$value["disk_basicid"]] == "umount volume success.\r\n") {
                    $value["errmsg"] = "挂载成功";
                    $value["mount_state"] = "0";
                } else {
                    $value["errmsg"] = $res[$value["disk_basicid"]];
                    $value["mount_state"] = "1";
                }
            }

        }
        debug($data);die;
        $http = new Client();
        $obj_response = $http->post($data["callback_url"],json_encode($data),['type' => 'json']);
        exit;
    }


    public function test(){
        $data = $this->_data;
        foreach ($data["diskinfo"] as  &$value) {
            $value["mount_state"] = 0;
            $value["disk_lable"] = "p";
            $value["errmsg"] = "挂载成功";
        }
        debug($data);die;
    }

    /**
     * 获取主机已挂载关联的存储--fics
     */
    public function GetDesktopFicsedList()
    {
        $name = $this->_data['name'];
        $host_extend_table = TableRegistry::get("HostExtend");
        $data = $host_extend_table->find()->where(['name'=>$name])->first();
        if (!empty($data)) {
            $id = $data['basic_id'];

            $fics_relation_info_table = TableRegistry::get("FicsRelationInfo");
            $data = $fics_relation_info_table->find()->where(['basic_id' => $id, 'status' => '1', 'type <>' => 'unc','is_operate' => 1])->toArray();
            $fics_list = array();
            if(!empty($data)){
                foreach ($data as  $v) {
                    $fics_list[] = json_decode($v['json_parp']);
                }
            }
            $code = "0";
            $msg = "OK";
            $data = $fics_list;
        } else {
            $code = "1";
            $msg = "没有找到主机";
            $data = "";
        }
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }

    /**
    * 生成GUID
    */
    protected function getGUID(){
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = substr($charid, 0, 8).$hyphen
        .substr($charid, 8, 4).$hyphen
        .substr($charid,12, 4).$hyphen
        .substr($charid,16, 4).$hyphen
        .substr($charid,20,12);
        return $uuid;
    }


    public function getSoftwareName(){
        $this->_serialize = array('code', 'msg', 'name');
        $instance_basic_table = TableRegistry::get("InstanceBasic");
        $host_extend_table = TableRegistry::get("HostExtend");
        $set_software_table = TableRegistry::get("GoodsVersionSpec");
        $code = 0;
        $msg = '调用成功';
        $name = '';
        $basic_data = $instance_basic_table->find()->where(["code" => $this->_data["code"]])->first();
        if (!empty($basic_data)){
            $extend = $host_extend_table->find()->where(["basic_id" => $basic_data["id"]])->first();
            if (!empty($extend["image_code"])) {
                $software_data = $set_software_table->find()->where(["image_code" => $extend["image_code"]])->first();
                $name = $software_data["name"];
            }
        } else {
            $code = 1;
            $msg = '没有找到桌面';
        }
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);

    }
    //从cmop获取桌面的ica信息，供vboss调用，是否从此获取，可以在vboss的系统参数 getIcaFromUrl进行控制
    //basic_id:cmop表里面的id
    public function getIca(){

//        $conn = ConnectionManager::get('default');

        $reqdata=$this->_data;
        $basic_id=0;
        if(isset($reqdata['basic_id']))
            $basic_id=$reqdata['basic_id'];
        if($basic_id<=0)
            $this->_errorExit(9000,"参数缺失，basic_id");
        //从数据库获取机器参数

//        $sql="SELECT b.basic_id,b.`name` as hostname,b.aduser,b.adpwd as adpass,a.`name`,
//        d.desktop_server_url as gateway_url
//        FROM cp_instance_basic a
//        INNER JOIN cp_host_extend b on a.id=b.basic_id
//        INNER JOIN cp_instance_basic c on a.vpc=c.`code`
//        INNER JOIN cp_vpc_extend d on c.id=d.basic_id
//        where b.basic_id=$basic_id";
//
//        $desktop_info=$conn->execute($sql)->fetch('assoc');

        $field =['basic_id'=>'hostExtend.basic_id','hostname'=>'hostExtend.name','aduser'=>'hostExtend.aduser','adpass'=>'hostExtend.adpwd',
                'name'=>'InstanceBasic.name','gateway_url'=>'vpcExtend.desktop_server_url'
            ];

        $instanceBasic = TableRegistry::get('InstanceBasic');
        $desktop_info = $instanceBasic->find()->join([
            'hostExtend'=>[
                'table'=>'cp_host_extend',
                'type'=>'INNER',
                'conditions'=>'hostExtend.basic_id = InstanceBasic.id'
            ],
            'vpc'=>[
                'table'=>'cp_instance_basic',
                'type'=>'INNER',
                'conditions'=>'vpc.code = InstanceBasic.vpc'
            ],
            'vpcExtend'=>[
                'table'=>'cp_vpc_extend',
                'type'=>'INNER',
                'conditions'=>'vpcExtend.basic_id = vpc.id'
            ]
        ])->select($field)->where(['hostExtend.basic_id'=>$basic_id])->first();


        if ($desktop_info){
            $gateway_url=$desktop_info['gateway_url'];
            if(!$gateway_url)
                $this->_errorExit(9111,"(from cmop)citrix网关地址为空，请检查cmop配置，并重新引入");
            $aduser=$desktop_info['aduser'];
            if(!$aduser)
                $this->_errorExit(9112,"(from cmop)桌面aduser账号为空，请检查cmop配置，并重新引入");
            $adpass=$desktop_info['adpass'];
            if(!$adpass)
                $this->_errorExit(9113,"(from cmop)桌面adpass密码为空，请检查cmop配置，并重新引入");
            $citrix = new Citrix($gateway_url, [
                'username'=>$aduser,
                'password'=>$adpass
            ]);
            $result_data = $citrix->ica($desktop_info['hostname']);
            $result=json_decode($result_data,true);

            if($result){
                $code='0000';
                if($result['code']==0 || $result['code']=='0000'){
                    $code='0000';
                    $msg='(from cmop)'.$result['msg'];
                    $data=$result['data'];
                    if(isset($data['errorId'])){
                        $tempstr=$data['errorId'];
                        $msg="(from cmop)ica格式不正确，请联系管理员检查机器状态--$tempstr";
                        $code="7999";
                    }
                }else{//返回失败了
                    $code='5555';
                    $msg="(from cmop)返回数据经过json_decode后为空，请联系开发人员";
                    return $result;
                }
            }
            else{

                $code='9999';
                $msg='(from cmop)获取ica，接口返回数据不合法,不是json数据【cmop】';
                $data="";
            }
        }else{
            $code='7006';
            $msg='(from cmop)云桌面在cmop中不存在';
            $data="";
        }
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }
    protected function _errorExit($code,$msg='参数错误',$data="")
    {
        if(intval($code)>0){
            $lastmsg="【errcode=".$code."from cmop】".$msg;
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
    
    /**
     *返回开机信息
     */
    public function callbackStart()
    {
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        $host_extend_table = TableRegistry::get('HostExtend');
        $callback_info_table = TableRegistry::get('CallbackInfo');
    
        $extend_data = $host_extend_table->find()->select('basic_id')->where(['name' => $this->_data['name']])->first();
        $basic_data = $instance_basic_table->find()->select('code')->where(['id' => $extend_data['id']])->first();
        $info_data = $callback_info_table->find()->where(['code' => $basic_data, 'method' => ''])->order('id DESC')->first();
    
        $data = json_decode(trim($info_data['callback_info'],chr(239).chr(187).chr(191)), true);
        $data['info_data'] = 'desktop';
    
        $http = new Client();
        $obj_response = $http->post($info_data["url"],json_encode($data),['type' => 'json']);
        exit;
    }
}
