<?php
namespace Controller\Console\Network;

/**
 * ==============================================
 * Fics.php
 * @author: shrimp liao
 * @date: 2016年3月19日 上午11:09:31
 * @version: v1.0.0
 * @desc:
 * ==============================================
 **/
namespace App\Controller\Console\Network;

use App\Controller\Console\ConsoleController;
use App\Controller\OrdersController;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class VpcStoreController extends ConsoleController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }
    private $_serialize = array('code', 'msg', 'data');
    public $_pageList = array('total' => 0, 'rows' => array());

    public function lists($request_data = array())
    {
        $vpcId = $request_data["id"];
        $limit = $request_data['limit'];
        $offset = $request_data['offset'];


        if(isset($request_data['type'])){
            if ($request_data['type']!="") {
                $where['VpcStoreExtend.vol_type'] = $request_data['type'];

            }
        }



        if (!empty($request_data['department_id'])) {
            $where['VpcStoreExtend.department_id'] = $request_data['department_id'];
        }

        if (isset($request_data['search'])) {
            if ($request_data['search'] != '') {
                $where["VpcStoreExtend.name like"] = '%'.$request_data['search'].'%';
            }
        }

        if (!empty($request_data['class_code'])) {
            $where['Agent.location_code like'] = '%'.$request_data['class_code'].'%';
        }
        if (!empty($request_data['class_code2'])) {
            $where['Agent.location_code like'] = '%'.$request_data['class_code2'].'%';
        }

        $field = [
            'agent_name'=>'Agent.agent_name','store_name'=>'store.store_name','name'=>'departments.name'
        ];

        $where['InstanceBasic.isdelete'] = 0;
        $where['InstanceBasic.type'] = 'subnet';
        $where['VpcStoreExtend.vpcId'] = $vpcId;


        $vpcStoreExtend = TableRegistry::get('VpcStoreExtend');
        $query = $vpcStoreExtend->find()->hydrate(false)->join(
            [
                'Agent'=>[
                    'table' =>'cp_agent',
                    'type'  =>'LEFT',
                    'conditions'=>'Agent.region_code = VpcStoreExtend.region_code'
                ],
                'store'=>[
                    'table' =>'cp_store',
                    'type'  =>'LEFT',
                    'conditions'=>'VpcStoreExtend.store_code = store.store_code'
                ],
                'departments'=>[
                    'table' =>'cp_departments',
                    'type'  =>'LEFT',
                    'conditions'=>'departments.id = VpcStoreExtend.department_id'
                ]
            ]
        )->autoFields(true)->select($field)->where($where)->group('VpcStoreExtend.id')->order('VpcStoreExtend.id DESC')
            ->offset($offset)->limit($limit);

        $this->_pageList['total'] = $query->count();
        $this->_pageList['rows'] = $query;
        return $this->_pageList;
    }

    public function addstore($request_data = array())
    {
        $code = '0001';
        $data = array();
        // debug($this->request);die;
        // debug($request_data);die;
        // 编辑操作
        $table        = TableRegistry::get('VpcStoreExtend');
        $entity = $table->newEntity();
        $entity                           = $table->patchEntity($entity, $request_data);
        $entity->create_time = time();
        $goods_vpc_detail = TableRegistry::get('GoodsVpcDetail');
        $store = $goods_vpc_detail->newEntity();
        $store->vpc_id = $entity->vpcId;
        $store->type = $entity->vol_type;
        $store->tagname = $entity->vol_name;

        if ($table->save($entity)&&$goods_vpc_detail->save($store)) {
            $code = '0000';
            $data = $table->get($entity->vol_id)->toArray();
        }
        $msg = Configure::read('MSG.' . $code);
        return compact(array_values($this->_serialize));
    }
    /**
     * 编辑数据列表
     */
    public function editstore($request_data = array())
    {
        $code = '0001';
        $data = array();
        // debug($request_data);die;
        // 编辑操作
        $store_table        = TableRegistry::get('VpcStoreExtend');
        $store_entity     = $store_table->get($request_data['vol_id']);
        $goods_vpc_detail = TableRegistry::get('GoodsVpcDetail');
        $entity = $goods_vpc_detail->find()->where(array('vpc_id'=>$store_entity["vpcId"],'tagname'=>$store_entity["vol_name"]))->first();
        $entity = $goods_vpc_detail->get($entity["id"]);//获取detail 数据
        $entity->tagname = $request_data["vol_name"];
        $store_entity     = $store_table->patchEntity($store_entity, $request_data);
        $store_entity->id = $request_data['vol_id'];

        if ($store_table->save($store_entity)&&$goods_vpc_detail->save($entity)) {
            $code = '0000';
            $data = $store_table->get($request_data['vol_id'])->toArray();
        }
        $msg = Configure::read('MSG.' . $code);
        return compact(array_values($this->_serialize));
    }
    public function deleteId($request_data = array())
    {
        $table = TableRegistry::get('VpcStoreExtend');
        $id = (int)$request_data["id"];
        $data = $table->find()->where(['vol_id' => $id])->first();
        $goods_vpc_detail = TableRegistry::get('GoodsVpcDetail');
        $VpcFicsUsers = TableRegistry::get('VpcFicsUsers');
        $VpcStoreUserP = TableRegistry::get('VpcStoreUserP');
        $goods_vpc_detail->deleteAll(array('tagname' => $data["vol_name"],'vpc_id'=>$data["vpcId"]));
        $VpcFicsUsers->deleteAll(array('vpcId' => $data["vpcId"]));
        $VpcStoreUserP->deleteAll(array('vpcId' => $data["vpcId"]));
        if ($table->deleteAll(array('vol_id' => $id))) {
            $code = '0000';
            // $data = $host->get($request_data['vol_id'])->toArray();
        }
        $msg = Configure::read('MSG.' . $code);
        return compact(array_values($this->_serialize));
    }

    public function settinglist($request_data = array())
    {
        $vpcId = $request_data["id"];
        $limit = $request_data['limit'];
        $offset = $request_data['offset'];
        $where = '';
        if (!empty($request_data['department_id'])) {
            $where .= ' AND a.department_id = ' . $request_data['department_id'];
        }
        if(isset($request_data['search'])){
            if ($request_data['search']!="") {
                $where .= ' AND b.search like\'%' . $request_data['search'] . '%\'';
            }
        }

        if(isset($request_data['t'])){
            if ($request_data['t']!="") {
                $where .= ' AND b.`limit`=\''.$request_data['t'].'\'';
            }
        }
        if(isset($request_data['vol_name'])){
            if ($request_data['vol_name']!="") {
                $where .= ' AND b.vol_name=\''.$request_data['vol_name'].'\'';
            }
        }
        $connection = ConnectionManager::get('default');
        $sql = ' SELECT a.userid,b.id,a.`name`,a.`password`,a.storetype,a.store_code,a.department_id,b.vol_name,b.`limit`,a.region_code FROM cp_vpc_fics_users a ';
        $sql .= ' LEFT JOIN cp_vpc_store_user_p b ON b.user_id=a.userid ';
        $sql .= ' WHERE 1=1 and a.vpcId=b.vpcId and a.vpcId=\''.$vpcId.'\'' . $where;
        $sql .= ' GROUP BY a.userid ';
        $sql .= ' ORDER BY a.userid DESC ';
        $sql_row = $sql . ' limit ' . $offset . ',' . $limit;
        $query = $connection->execute($sql_row)->fetchAll('assoc');
        $this->_pageList['total'] = $connection->execute($sql)->count();
        $this->_pageList['rows'] = $query;
        return $this->_pageList;
    }

    public function cheklist($request_data = array())
    {
        $where = '';
        if (!empty($request_data['template_id'])) {
            $where .= ' AND a.template_id = ' . $request_data['template_id'];
        }
        if (!empty($request_data['basic_id'])) {
            $where .= ' AND a.basic_id =' . $request_data['basic_id'];
        }
        $connection = ConnectionManager::get('default');
        $sql = ' SELECT id,basic_id,account_id,a.template_id FROM';
        $sql .= ' cp_fics_vol_account AS a ';
        $sql .= ' LEFT JOIN cp_fics_vol_acces_template AS b ON a.basic_id = b.vol_id ';
        $sql .= ' AND a.template_id = b.template_id ';
        $sql .= ' WHERE ';
        $sql .= ' 1 = 1 ' . $where;
        $query = $connection->execute($sql)->fetchAll('assoc');
        return $query;
    }

    public function ajaxFics($request_data = array())
    {
        $orders = new OrdersController();
        $result = array();
        $uid = (string) $this->request->session()->read('Auth.User.id');
        $request_data['method'] = $request_data['method'];
        $request_data['uid'] = (string) $this->request->session()->read('Auth.User.id');
        $request_data['loadbalanceCode'] = $request_data['loadbalanceCode'];
        $request_data['basicId'] = $request_data['basicId'];
        $request_data['uid'] = (string) $this->request->session()->read('Auth.User.id');
        if ($request_data['method'] == 'lbs_unbind') {
            unset($request_data['protocol']);
            unset($request_data['port']);
            unset($request_data['weight']);
        }
        return $orders->ajaxFun($request_data);
    }

    public function addUser($request_data = array()){
        // debug($request_data);die();
        // $orders = new OrdersController();
        // $uid = (string) $this->request->session()->read('Auth.User.id');
        // $request_data['method'] = "store_addUser";
        // $request_data['uid'] = (string) $this->request->session()->read('Auth.User.id');
        $table = TableRegistry::get('VpcStoreExtend');
        $user_table = TableRegistry::get('VpcFicsUsers');
        $user_table_p = TableRegistry::get('VpcStoreUserP');
        $entity = $table->find("all")->where(array('vol_id'=>$request_data["vol_id"],'vpcId'=>$request_data["vpcId"]))->first();
        //赋值信息
        // $user = array();
        $entity1 = $user_table->newEntity();
        $entity1->name = $request_data["name"];
        $entity1->password = $request_data['password'];
        $entity1->region_code = $entity->region_code;
        $entity1->storetype = $entity->vol_type;
        $entity1->store_code = $entity->store_code;
        $entity1->vpcId = $request_data["vpcId"];
        if ($user_table->save($entity1)) {
            // $data = $table->get($entity->vol_id)->toArray();
            $entity2 = $user_table_p->newEntity();
            $entity2->limit=$request_data["type"];
            $entity2->user_id=$entity1->userid;
            $entity2->vol_name=$entity->vol_name;
            $entity2->vpcId=$request_data["vpcId"];

            if ($user_table_p->save($entity2)) {
                $code = '0000';
            }
        }
        $msg = Configure::read('MSG.' . $code);
        return compact(array_values($this->_serialize));
    }

    public function delUser($request_data = array()){
        $table1 = TableRegistry::get('VpcFicsUsers');
        $table2 = TableRegistry::get('VpcStoreUserP');
        $id = (int)$request_data["id"];
        if ($table1->deleteAll(array('userid' => $id))&&$table2->deleteAll(array('user_id' => $id))) {
            $code = '0000';
        }
        $msg = Configure::read('MSG.' . $code);
        return compact(array_values($this->_serialize));
    }

    public function savefics($request_data = array())
    {
        $code = '0000';
        $msg = "操作成功";
        $accounts = TableRegistry::get('FicsVolAccount');
        $id = $request_data["id"];
        $template_id = $request_data["template_id"];
        $array = explode(',', $request_data["account"]);
        $array1 = explode(',', $request_data["account1"]);
        foreach ($array1 as $key => $value) {
            $del_result = $accounts->deleteAll(['basic_id' => $id, 'account_id' => $value, 'template_id' => $template_id]);
        }
        foreach ($array as $key => $value) {
            if ($value != "") {
                $fics = $accounts->newEntity();
                $fics->basic_id = $id;
                $fics->account_id = $value;
                $fics->template_id = $template_id;
                if (!$accounts->save($fics)) {
                    $code = "0500";
                    $msg = "操作失败";
                }
            }
        }
        return compact(array_values($this->_serialize));
        //删除关系
    }



    public function createStroArray()
    {
        $str = array();
        $agent = TableRegistry::get('Agent');
        $where = array('is_enabled' => 1);
        //获取商品信息，包含商品分类
        $agentInfo = $agent->find('all')->contain(array('AgentImagelist', 'AgentSet'))->where($where)->order(array('Agent.sort_order' => 'ASC'))->toArray();
        $str = array();
        foreach ($agentInfo as $index => $item) {
            if ($item['parentid'] == 0) {
                $agent = array();
                $agent['company'] = array('name' => $item['agent_name'], 'companyCode' => $item['agent_code']);
                $agent['area'] = $this->getAreaListById($item['id'], $agentInfo);
                $str[] = $agent;
            }
        }
        return $str;
    }

    public function getAreaListById($id, $agentInfo)
    {
        $str = array();
        $instance_basic = TableRegistry::get('InstanceBasic');
        $store_agent_table = TableRegistry::get('StoreAgent');
        $store_table = TableRegistry::get('Store');
        foreach ($agentInfo as $index => $item) {
            if ($item['parentid'] == $id) {
                $stor_agent = $store_agent_table->find("all")->where(array('region_code'=>$item["region_code"]))->toArray();
                $store_data = array();
                foreach ($stor_agent as $key => $v) {
                    $d = $store_table->find("all")->where(array('region_code'=>$item["region_code"],'store_type'=>$v['type']))->toArray();
                    $v["store"] = $d;
                }
                $str[] = array('name' => $item['agent_name'], 'areaCode' => $item['region_code'],'storeType'=>$stor_agent);
            }
        }
        return $str;
    }

    /**
    * fics关联
    * @author wangjincheng
    *
    */
    public function deviceList($request_data)
    {
        $table = TableRegistry::get("GoodsVpcDetail");
        $data = $table->test($request_data);
        $this->paginate['limit'] = $request_data['limit'];
        $this->paginate['page']  = $request_data['offset'] / $request_data['limit'] + 1;
        $this->_pageList['total'] = $data->count();
        $this->_pageList['rows'] = $this->paginate($data);
        return $this->_pageList;
    }


    /**
    * 设置fics关联
    * @author wangjincheng
    *
    */
    public function setFicsRelationHosts($request_data)
    {
        // debug($request_data);die;
        $code = "0";
        $msg = "操作成功";
        $data = array();//创建参数
        //获取获取存储军对应的user信息
        $store_extend_userp_table = TableRegistry::get('VpcStoreUserP');
        $store_user_table = TableRegistry::get('VpcFicsUsers');
        $goods_detail_table = TableRegistry::get('GoodsVpcDetail');
        $relation_table = TableRegistry::get('VpcFicsRelationDevice');
        if($request_data["authority"]!="2"){
            $user_entity = $store_extend_userp_table->find()->select("user_id")->where(array('vpcId'=>$request_data["vpcId"],'vol_name'=>$request_data["vol_name"]))->first();
            // debug($user_entity);die;
            if(!empty($user_entity)){
                $user_data = $store_user_table->get($user_entity->user_id);
            }else{
                $code = "1";
                $msg = "没有对应权限的账号，请先添加账号";
                return compact(array_values($this->_serialize));
            }
        }
        //批量设置挂卷授权
        $basic_id_array = explode(',', $request_data['basic_id']);
        foreach ($basic_id_array as $key => $value) {
            if($request_data["authority"]!="2"){
                $data["username"]=$user_data->name;
                $data["password"]=$user_data->password;
            }
            $data["basic_id"]=$value;
            $data['plat_form'] = $goods_detail_table->getPlatFromById($value);
            if ($request_data["type"] == "net use"){
                $relation_entity = $relation_table->find()->where(['basic_id' => $value,"label" => strtolower($request_data['drive']), "vol_id <>" => $request_data["vid"],'vpcId'=>$request_data["vpcId"]])->first();
                if(!empty($relation_entity)){
                    $code = "2";
                    $msg = "所选盘符重复，请重新选择，主机名称为：".$instance_basic_data["name"];
                    return compact(array_values($this->_serialize));
                }
            }
            $data["basic_ip"] = "0.0.0.0";
            $data["vol_id"]=$request_data["vid"];
            $data["type"]=$request_data["type"];
            $data["authority"]=$request_data["authority"];
            $data["drive"]=$request_data["drive"];
            $data["vpcId"]=$request_data["vpcId"];
            $data["vol_type"]=$request_data["vol_type"];
            $data["vol_name"]=$request_data["vol_name"];
            $data["ip"]="0.0.0.0";
            $data["path"]=$request_data["path"];
            $this->_relationHosts($data);
        }
        $data = "";
        return compact(array_values($this->_serialize));
    }

    /**
     *@author
     * 关联主机
     */
    protected function _relationHosts($data)
    {
        $store_user_p_table = TableRegistry::get("VpcStoreUserP");
        $fics_users_table = TableRegistry::get("VpcFicsUsers");
        $fics_relation_device_table = TableRegistry::get("VpcFicsRelationDevice");
        $del_result = $fics_relation_device_table->deleteAll(['basic_id' => $data['basic_id'], 'vol_id' => $data['vol_id'],'vpcId'=>$data["vpcId"]]);

        $path = '';
        $label = '';
        $parp = array();
        $uninstall = array();



        if($data['authority'] == 2){
            $data['type'] = '';
            $path = '';
            $parp = array();
            $uninstall = array();
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
                $data['vol_name'] = $data['vol_name']."_share";

                switch ($data['plat_form']) {
                    case 'Windows':
                    case '云主机':
                    case "Adobe":
                        switch ($data['type']) {
                            case 'unc':
                                $path = "\\\\".$data['ip']."\\".$data['vol_name'];
                                $parp["0"] = $path;
                                break;
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
                                $path = "mount \\\\".$data['ip']."\\".$data['vol_name']." ".$data['path']." -o codepage=cp936,iocharset=utf8,username=".$data['username'].",password=".$data['password'];
                                $parp["0"] = "mount";
                                $parp["1"] = "\\\\".$data['ip']."\\".$data['vol_name'];
                                $parp["2"] = $data['path'];
                                $parp["3"] = "-o";
                                $parp["4"] = "codepage=cp936,iocharset=utf8,username=".$data['username'].",password=".$data['password'];
                                break;
                            default:
                                break;
                        }
                        break;
                    default:
                        switch ($data['type']) {
                            case 'unc':
                                $path = "//".$data['ip']."/".$data['vol_name'];
                                $parp["0"] = $path;
                                break;
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
                                $path = "mount -t cifs //".$data['ip']."/".$data['vol_name']." ".$data['path']." -o iocharset=utf8,username=".$data['username'].",password=".$data['password'];
                                $parp[] = "mount";
                                $parp[] = "-t";
                                $parp[] = "cifs";
                                $parp[] = "-o";
                                $parp[] = "username=".$data['username'].",password=".$data['password'];
                                $parp[] = "//".$data['ip']."//".$data['vol_name'];
                                $parp[] = $data['path'];
                                break;

                            default:
                                break;
                        }
                       break;
                }
            }
        }

        $relation_data = $fics_relation_device_table->newEntity();
        $relation_data['vol_id'] = $data['vol_id'];
        $relation_data['vpcId'] = $data['vpcId'];
        $relation_data['basic_id'] = $data['basic_id'];
        $relation_data['type'] = $data['type'];
        $relation_data['parameter'] = $path;
        $relation_data['authority'] = $data['authority'];
        $relation_data['json_parp'] = json_encode($parp);
        $relation_data['json_uninstall'] = json_encode($uninstall);
        $relation_data['label'] = $label;
        $fics_relation_device_table->save($relation_data);
    }

}
