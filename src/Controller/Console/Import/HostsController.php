<?php
 /**
 * ==============================================
 * HostsController.php
 * @author: shrimp liao
 * @date: 2016年4月11日 上午11:11:06
 * @version: v1.0.0
 * @desc:反向引入
 * ==============================================
 **/
namespace App\Controller\Console\Import;

use App\Controller\Console\ConsoleController;
use App\Controller\OrdersController;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

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
        'data'
    );

    public $_pageList = array(
        'total' => 0,
        'rows' => array()
    );

    /**
     * 获取列表数据,
     * 新增关联查询
     */
    public function ecslists($request_data = array())
    {
        $limit  = $request_data['limit'];
        $offset = $request_data['offset'];
        $this->MergeJsonEcs($request_data);//引入
        //查询临时表的主机信息
        $connection = ConnectionManager::get('default');
        $where  = ' AND (a.type = \'ECS\' OR a.type = \'Firewall\' OR a.type = \'AD\' OR a.type = \'DDC\' OR a.type = \'VPX\' OR a.type = \'Desktop\')';
        if (!empty($request_data['department_id'])) {
            $where .= ' AND a.department_id = ' . $request_data['department_id'];
        }
        if (isset($request_data['search'])) {
            if ($request_data['search'] != "") {
                $where .= ' AND (a.code like\'%' . $request_data['search'] . '%\')';
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
        $sql        = ' SELECT ';
        $sql .= ' a.*,b.os_family,c.display_name FROM `cp_temporary` AS a LEFT JOIN cp_imagelist b ON a.image_code = b.image_code LEFT JOIN cp_agent c ON a.location_code=c.class_code';
        $sql .= ' WHERE 1=1' . $where;
        $sql .= ' group by a.code';
        $sql .= ' ORDER BY a.id asc ';
        $sql_row                  = $sql . ' limit ' . $offset . ',' . $limit;
        $query                    = $connection->execute($sql_row)->fetchAll('assoc');
        $this->_pageList['total'] = $connection->execute($sql)->count();
        $this->_pageList['rows']  = $query;
        return $this->_pageList;
    }
    public function vpclists($request_data = array())
    {
        $list = $this->MergeJsonVpc($request_data);
        $connection = ConnectionManager::get('default');
        $limit  = $request_data['limit'];
        $offset = $request_data['offset'];
        $where  = ' AND a.type = \'VPC\'';
        $where .= ' AND a.department_id = ' . $request_data['department_id'];
        if (!empty($request_data['department_id'])) {
            $where .= ' AND a.department_id = ' . $request_data['department_id'];
        }
        if (isset($request_data['search'])) {
            if ($request_data['search'] != "") {
                $where .= ' AND (a.code like\'%' . $request_data['search'] . '%\')';
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
        $sql        = ' SELECT ';
        $sql .= ' a.*,c.display_name FROM `cp_temporary` AS a LEFT JOIN cp_agent c ON a.location_code=c.class_code';
        $sql .= ' WHERE 1=1' . $where;
        $sql .= ' group by a.code';
        $sql .= ' ORDER BY a.id asc ';
        $sql_row                  = $sql . ' limit ' . $offset . ',' . $limit;
        $query                    = $connection->execute($sql_row)->fetchAll('assoc');
        $this->_pageList['total'] = $connection->execute($sql)->count();
        $this->_pageList['rows']  = $query;

        return $this->_pageList;
    }
    public function subnetlists($request_data = array())
    {
        $limit  = $request_data['limit'];
        $offset = $request_data['offset'];
        $this->MergeJsonSubnet($request_data);//引入
        $connection = ConnectionManager::get('default');
        $where  = ' AND a.type = \'Subnet\'';
        $where .= ' AND a.department_id = ' . $request_data['department_id'];
        if (!empty($request_data['department_id'])) {
            $where .= ' AND a.department_id = ' . $request_data['department_id'];
        }
        if (isset($request_data['search'])) {
            if ($request_data['search'] != "") {
                $where .= ' AND (a.code like\'%' . $request_data['search'] . '%\')';
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
        $sql        = ' SELECT ';
        $sql .= ' a.*,b.name,c.display_name FROM `cp_temporary` AS a LEFT JOIN cp_instance_basic b ON a.vpc_code = b.code LEFT JOIN cp_agent c ON a.location_code=c.class_code';
        $sql .= ' WHERE 1=1' . $where;
        $sql .= ' group by a.code';
        $sql .= ' ORDER BY a.id asc ';
        $sql_row                  = $sql . ' limit ' . $offset . ',' . $limit;
        $query                    = $connection->execute($sql_row)->fetchAll('assoc');
        $this->_pageList['total'] = $connection->execute($sql)->count();
        $this->_pageList['rows']  = $query;
        return $this->_pageList;
    }
    public function diskslists($request_data = array())
    {
        $list = $this->MergeJson($request_data["department_id"]);
        $this->_pageList['total'] = count($list);
        $this->_pageList['rows'] = $list;
        return $this->_pageList;
    }
    public function routerlists($request_data = array())
    {
        $limit  = $request_data['limit'];
        $offset = $request_data['offset'];
        $this->MergeJsonRouter($request_data);//引入
        $connection = ConnectionManager::get('default');
        $where  = ' AND a.type = \'Router\'';
        $where .= ' AND a.department_id = ' . $request_data['department_id'];
        if (!empty($request_data['department_id'])) {
            $where .= ' AND a.department_id = ' . $request_data['department_id'];
        }
        if (isset($request_data['search'])) {
            if ($request_data['search'] != "") {
                $where .= ' AND (a.code like\'%' . $request_data['search'] . '%\')';
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
        $sql        = ' SELECT ';
        $sql .= ' a.*,b.name,c.display_name FROM `cp_temporary` AS a LEFT JOIN cp_instance_basic b ON a.vpc_code = b.code LEFT JOIN cp_agent c ON a.location_code=c.class_code';
        $sql .= ' WHERE 1=1' . $where;
        $sql .= ' group by a.code';
        $sql .= ' ORDER BY a.id asc ';
        $sql_row                  = $sql . ' limit ' . $offset . ',' . $limit;
        $query                    = $connection->execute($sql_row)->fetchAll('assoc');
        $this->_pageList['total'] = $connection->execute($sql)->count();
        $this->_pageList['rows']  = $query;
        return $this->_pageList;
    }
    public function firewalllists($request_data = array())
    {
        $list = $this->MergeJsonFirewall($request_data["department_id"]);
        $this->_pageList['total'] = count($list);
        $this->_pageList['rows'] = $list;
        return $this->_pageList;
    }
    public function desktopLists($request_data = array()){

    }
    /**
     * @func: 合并获取来的json数据集合
     * @param:
     * @date: 2016年4月11日 上午10:47:01
     * @author: shrimp liao
     * @return: null
     */
    public function MergeJsonEcs($data)
    {
        $orders = new OrdersController();
        $param["method"]="ecslist";
        // $param["uid"]= $this->request->session()->read('Auth.User.id');
        $param["uid"]= (string)$this->_getUserIdByDepartment($data['department_id']);
        if(empty($param["uid"])){
            return 0;
        }
        $result = $orders->ajaxFun($param);#掉底层接口，获取主机列表
        $message = $result["Message"];
        $msg = json_decode($message,true);
        if(empty($msg)){
            $msg = array();
        }
        $instance_basic = TableRegistry::get('InstanceBasic');
        $where = [
            'OR' => [['InstanceBasic.type' => 'hosts'], ['InstanceBasic.type' => 'desktop'], ['InstanceBasic.type' => 'firewallecs'], ['InstanceBasic.type' => 'ad'], ['InstanceBasic.type' => 'ddc'], ['InstanceBasic.type' => 'vpx']],
            'department_id'=>$data['department_id']
        ];
        $list = $instance_basic->find("all")->where($where)->toArray();#查询数据库的中部门对应的列表
        //获取底层返回的所有code
        $msg_name = array();
        foreach ($msg as $m)
        {
            $msg_name[] = $m["EcsCode"];
        }
        //获取数据库所有的code
        $list_name = array();
        foreach ($list as $l){
            $list_name[] = $l["code"];
        }
        $diff = array_unique(array_diff($msg_name,$list_name));#对比底层与数据库的不同code

        foreach ($msg as $k => $m)
        {
            $msg[$k]["status"]="已引入";
            foreach ($diff as $d){
                if($m["EcsCode"]==$d){
                    $msg[$k]["status"]="未引入";
                    // unset($msg[$k])
                }
            }
        }
        //将不同的主机信息导入临时表
        $temp = TableRegistry::get('Temporary');
        $temp->deleteAll([
            'OR' => [['type' => 'ECS'], ['type' => 'Desktop'], ['type' => 'Firewall'], ['type' => 'AD'], ['type' => 'DDC'], ['type' => 'VPX']]
            ]);
        $t =$temp->newEntity();
        foreach ($msg as $k => $m) {
            if($msg[$k]["status"]=="未引入"){
                if(!in_array($m["EcsType"], ['ECS','Desktop','Firewall','AD','DDC','VPX'])){
                    continue;
                }
                $t =$temp->newEntity();
                $t->code = $m["EcsCode"]; #主机code
                $t->type = $m["EcsType"]; #主机类型
                $t->department_id = (int)$data['department_id']; #租户id
                $t->info = json_encode($m); #底层所有信息
                $t->name = $m["EcsCode"]; #主机名，用code代替
                $t->cpu= $m["CpuCore"]; #CPU数
                $t->pgu= $m["GpuMB"]; #GPU数
                $t->memory= $m["MemoryGB"]; #内存大小
                $t->router_code= $m["RouterCode"]; #路由器code
                $t->vpc_code= $m["VpcCode"]; #VPC Code
                $subnets =""; #子网code
                $ips =""; #IP
                $netcard_codes =""; #网卡code
                foreach ($m["CardDetails"] as $key => $value) {
                    $subnets .= $value["SubnetCode"].",";
                    $ips .= $value["Ipaddress"].",";
                    $netcard_codes .= $value["NetworkCardCode"].",";
                }
                $t->subnet_code=substr($subnets, 0, -1);
                $t->ipaddress=substr($ips, 0, -1);
                $t->netcard_code=substr($netcard_codes, 0, -1);
                $t->location_code=$this->getVpcByLocationCode($m["VpcCode"])->location_code;
                $t->image_code=$m["ImageCode"];
                if ($temp->save($t)) {

                }
            }
        }
    }

    /**
    * 引入未保存的vpc 到临时表
    */
    public function MergeJsonVpc($data)
    {
        $orders = new OrdersController();
        $param["method"]="vpclist";
        $param["uid"]= (string)$this->_getUserIdByDepartment($data['department_id']);
        if(empty($param["uid"])){
            return 0;
        }
        // $param["uid"]= "122";
        $result = $orders->ajaxFun($param);
        // debug($param);die;
        $message = $result["Message"];
        // $message = '[{"Cidr":"172.16.0.0\/20","VpcCode":"Vpc-cKO6VqIq","RegionCode":"region-sobeycymis","status":"\u672a\u5f15\u5165"}]';
        $msg = json_decode($message,true);
        if (empty($msg)) {
            $msg = array();
        }
        $instance_basic = TableRegistry::get('InstanceBasic');
        $where = [
            'InstanceBasic.type' => 'vpc',
            // 'department_id'=>$user["department_id"]
            'department_id'=>$data['department_id']
        ];
        $list = $instance_basic->find("all")->where($where)->toArray();

        $msg_name = array();
        foreach ($msg as $m)
        {
            $msg_name[] = $m["VpcCode"];
        }

        $list_name = array();
        foreach ($list as $l){
            $list_name[] = $l["code"];
        }
        $diff = array_unique(array_diff($msg_name,$list_name));
        foreach ($msg as $k => $m)
        {
            $msg[$k]["status"]="已引入";
            foreach ($diff as $d){
                if($m["VpcCode"]==$d){
                    $msg[$k]["status"]="未引入";
                }
            }
        }
        $temp = TableRegistry::get('Temporary');
        $temp->deleteAll(['type'=>'VPC']);

        $t =$temp->newEntity();
        foreach ($msg as $k => $m) {
            if($msg[$k]["status"]=="未引入"){
                $t =$temp->newEntity();
                $t->code = $m["VpcCode"];
                $t->type = "VPC";
                $t->department_id = (int)$data['department_id'];
                $t->info = json_encode($m);
                $t->name = $m["VpcCode"];
                $t->vpc_code= $m["VpcCode"];
                // $t->location_code=$this->getVpcByLocationCode($m["VpcCode"])->location_code;
                $t->location_code=$this->getLocationCodeByRegionCode($m["RegionCode"]);
                // $t->image_code=$m["ImageCode"];
                if ($temp->save($t)) {

                }
            }
        }
    }

    public function MergeJsonSubnet($data)
    {
        $orders = new OrdersController();
        $param["method"]="subnetlist";
        $param["uid"]= (string)$this->_getUserIdByDepartment($data['department_id']);
        if(empty($param["uid"])){
            return 0;
        }
        $result = $orders->ajaxFun($param);
        $message = $result["Message"];
        // debug($param);die();
        $msg = json_decode($message,true);
        if(empty($msg)){
            return;
        }
        // debug($msg);
        $instance_basic = TableRegistry::get('InstanceBasic');
        $where = [
            'InstanceBasic.type' => 'subnet',
            'department_id'=>$data['department_id']
        ];
        $list = $instance_basic->find("all")->where($where)->toArray();
        $msg_name = array();
        foreach ($msg as $m)
        {
            $msg_name[] = $m["SubnetCode"];
        }

        $list_name = array();
        foreach ($list as $l){
            $list_name[] = $l["code"];
        }
        $diff = array_unique(array_diff($msg_name,$list_name));

        foreach ($msg as $k => $m)
        {
            $msg[$k]["status"]="已引入";
            foreach ($diff as $d){
                if($m["SubnetCode"]==$d){
                    $msg[$k]["status"]="未引入";
                    // unset($msg[$k])
                }
            }
        }

        $temp = TableRegistry::get('Temporary');
        $temp->deleteAll(['type'=>'Subnet']);
        $t =$temp->newEntity();
        foreach ($msg as $k => $m) {
            if($msg[$k]["status"]=="未引入"){
                $t =$temp->newEntity();
                $t->code = $m["SubnetCode"];
                $t->type = "Subnet";
                $t->department_id = (int)$data['department_id'];
                $t->info = json_encode($m);
                $t->name = $m["SubnetCode"];
                $t->router_code= $m["RouterCode"];
                $t->vpc_code= $m["VpcCode"];
                $t->location_code=$this->getVpcByLocationCode($m["VpcCode"])->location_code;
                // $t->image_code=$m["ImageCode"];
                if ($temp->save($t)) {

                }
            }
        }
    }

    public function MergeJsonDisks($d)
    {
        $orders = new OrdersController();
        $param["method"]="subnetlist";
        $param["uid"]= (string)$this->_getUserIdByDepartment($data['department_id']);
        if(empty($param["uid"])){
            return 0;
        }
        $result = $orders->ajaxFun($param);
        $message = $result["Message"];
        $msg = json_decode($message,true);
        debug($msg);
        $instance_basic = TableRegistry::get('InstanceBasic');
        $account_table = TableRegistry::get('Accounts');
        $user = $account_table->find()->select('department_id')->where(array('id' => $this->request->session()->read('Auth.User.id')))->first();
        $where = [
            'InstanceBasic.type' => 'disks',
            // 'department_id'=>$user["department_id"]
            'department_id'=>43
        ];
        $list = $instance_basic->find("all")->where($where)->toArray();
        $msg_name;
        foreach ($msg as $m)
        {
            $msg_name[] = $m["identifier"];
        }

        $list_name;
        foreach ($list as $l){
            $list_name[] = $l["code"];
        }
        debug($msg_name);
        debug($list_name);
        $diff = array_unique(array_diff($msg_name,$list_name));

        foreach ($msg as $k => $m)
        {
            $msg[$k]["status"]="已引入";
            foreach ($diff as $d){
                if($m["identifier"]==$d){
                    $msg[$k]["status"]="未引入";
                }
            }
        }
        debug($msg);
        return $msg;
        // $this->_pageList['total'] = count($msg);
        // $this->_pageList['rows'] = $msg;
        // return $this->_pageList;
    }

    public function MergeJsonRouter($data)
    {
        $orders = new OrdersController();
        $param["method"]="routerlist";
        $param["uid"]= (string)$this->_getUserIdByDepartment($data['department_id']);
        if(empty($param["uid"])){
            return 0;
        }
        $result = $orders->ajaxFun($param);
        $message = $result["Message"];
        // debug($param);die();
        $msg = json_decode($message,true);
        if(empty($msg)){
            return;
        }
        // debug($msg);
        $instance_basic = TableRegistry::get('InstanceBasic');
        $where = [
            'InstanceBasic.type' => 'router',
            'department_id'=>$data['department_id']
        ];
        $list = $instance_basic->find("all")->where($where)->toArray();
        $msg_name = array();
        foreach ($msg as $m)
        {
            $msg_name[] = $m["RouterCode"];
        }

        $list_name = array();
        foreach ($list as $l){
            $list_name[] = $l["code"];
        }
        $diff = array_unique(array_diff($msg_name,$list_name));

        foreach ($msg as $k => $m)
        {
            $msg[$k]["status"]="已引入";
            foreach ($diff as $d){
                if($m["RouterCode"]==$d){
                    $msg[$k]["status"]="未引入";
                    // unset($msg[$k])
                }
            }
        }

        $temp = TableRegistry::get('Temporary');
        $temp->deleteAll(['type'=>'Router']);
        $t =$temp->newEntity();
        foreach ($msg as $k => $m) {
            if($msg[$k]["status"]=="未引入"){
                $t =$temp->newEntity();
                $t->code = $m["RouterCode"];
                $t->type = "Router";
                $t->department_id = (int)$data['department_id'];
                $t->info = json_encode($m);
                $t->name = $m["RouterCode"];
                $t->vpc_code= $m["VpcCode"];
                $t->location_code=$this->getVpcByLocationCode($m["VpcCode"])->location_code;
                // $t->image_code=$m["ImageCode"];
                if ($temp->save($t)) {

                }
            }
        }
    }

    public function MergeJsonAd($data)
    {
        $orders = new OrdersController();
        $param["method"]="desktop_ad_list";
        $param['vpcCode'] = $data['vpcCode'];
        $param["uid"]= (string)$this->_getUserIdByDepartment($data['department_id']);
        if(empty($param["uid"])){
            return 0;
        }
        $result = $orders->ajaxFun($param);
        $message = $result["Message"];
        // debug($param);die();
        $msg = json_decode($message,true);
        if(empty($msg)){
            return;
        }
        // debug($msg);
        $instance_basic = TableRegistry::get('InstanceBasic');
        $where = [
            'InstanceBasic.type' => 'router',
            'department_id'=>$data['department_id']
        ];
        $list = $instance_basic->find("all")->where($where)->toArray();
        $msg_name = array();
        foreach ($msg as $m)
        {
            $msg_name[] = $m["RouterCode"];
        }

        $list_name = array();
        foreach ($list as $l){
            $list_name[] = $l["code"];
        }
        $diff = array_unique(array_diff($msg_name,$list_name));

        foreach ($msg as $k => $m)
        {
            $msg[$k]["status"]="已引入";
            foreach ($diff as $d){
                if($m["RouterCode"]==$d){
                    $msg[$k]["status"]="未引入";
                    // unset($msg[$k])
                }
            }
        }

        $temp = TableRegistry::get('Temporary');
        $temp->deleteAll(['type'=>'Router']);
        $t =$temp->newEntity();
        foreach ($msg as $k => $m) {
            if($msg[$k]["status"]=="未引入"){
                $t =$temp->newEntity();
                $t->code = $m["RouterCode"];
                $t->type = "Router";
                $t->department_id = (int)$data['department_id'];
                $t->info = json_encode($m);
                $t->name = $m["RouterCode"];
                $t->vpc_code= $m["VpcCode"];
                $t->location_code=$this->getVpcByLocationCode($m["VpcCode"])->location_code;
                // $t->image_code=$m["ImageCode"];
                if ($temp->save($t)) {

                }
            }
        }
    }

    public function MergeJsonFirewall($d)
    {
        $orders = new OrdersController();
        $param["method"]="subnetlist";
        $param["uid"]= (string)$this->_getUserIdByDepartment($data['department_id']);
        if(empty($param["uid"])){
            return 0;
        }
        $result = $orders->ajaxFun($param);
        $message = $result["Message"];
        $msg = json_decode($message,true);
        debug($msg);
        $instance_basic = TableRegistry::get('InstanceBasic');
        $account_table = TableRegistry::get('Accounts');
        $user = $account_table->find()->select('department_id')->where(array('id' => $this->request->session()->read('Auth.User.id')))->first();
        $where = [
            'InstanceBasic.type' => 'firewall',
            // 'department_id'=>$user["department_id"]
            'department_id'=>43
        ];
        $list = $instance_basic->find("all")->where($where)->toArray();
        $msg_name;
        foreach ($msg as $m)
        {
            $msg_name[] = $m["identifier"];
        }

        $list_name;
        foreach ($list as $l){
            $list_name[] = $l["code"];
        }
        $diff = array_unique(array_diff($msg_name,$list_name));

        foreach ($msg as $k => $m)
        {
            $msg[$k]["status"]="已引入";
            foreach ($diff as $d){
                if($m["identifier"]==$d){
                    $msg[$k]["status"]="未引入";
                }
            }
        }
        debug($msg);
        return $msg;
        // $this->_pageList['total'] = count($msg);
        // $this->_pageList['rows'] = $msg;
        // return $this->_pageList;
    }

    public function getInstanceByType($request_data){
        $orders = new OrdersController();
        $data["method"]=$request_data["type"];
        $data[$request_data["type"]."Code"]=$request_data["code"];
        $data["uid"]=$request_data["uid"];
        $result = $orders->postInterface("http://10.10.12.123:9090",$data);
        $message = $result["Data"];
        $account_table = TableRegistry::get('Accounts');
         // $user = $account_table->find()->select('department_id')->where(array('id' => $this->request->session()->read('Auth.User.id')))->first();
         // debug($user);die();
        // $msg = json_decode($message,true);
        // debug($message);die();
        return $message;
    }

    /**
    * 获取vpc信息
    */
    public function getVpcByLocationCode($vpc){
        $table = TableRegistry::get('instance_basic');
        $temporary_table = TableRegistry::get('Temporary');
        $where=array('type'=>'vpc','code'=>$vpc);
        $entity = $table->find("all")->where($where)->first();
        if(empty($entity)){
            $entity = $temporary_table->find("all")->where(['type'=>'VPC','code'=>$vpc])->first();
            if(empty($entity)){
                $entity = (object)array("location_code"=>'');
            }
        }
        return $entity;
    }

    //获取地域code
    public function getLocationCodeByRegionCode($region)
    {
        $agent_table = TableRegistry::get('Agent');
        $agent_data = $agent_table->find()->where(['region_code'=>$region])->first();
        $location_code = '';
        if(!empty($agent_data['class_code'])){
            $location_code = $agent_data['class_code'];
        }
        return $location_code;
    }

    //引入
    public function addInstance($request_data = array()){
        $code = '0001';
        $msg = '操作失败';
        $request = $request_data['id'];
        $temporary_table = TableRegistry::get('Temporary');
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        $instance_relation_table = TableRegistry::get('InstanceRelation');
        $host_extend_table = TableRegistry::get('HostExtend');
        $hosts_network_card_table = TableRegistry::get('HostsNetworkCard');
        $subnet_extend_table = TableRegistry::get('SubnetExtend');
        $vpc_extend_table = TableRegistry::get('VpcExtend');
        $instance_charge_table = TableRegistry::get('InstanceCharge');

        if(!empty($request) && is_array($request)){
            $temporary_data = $temporary_table->find()->where(['code in' => $request])->toArray();#获取需要引入机器信息
            foreach ($temporary_data as $key => $data) {
                
                //检查code是否重复
                $is_exit = $instance_basic_table->find()->where(['code' => $data['code']])->first();
                if (!empty($is_exit)) {
                    break;
                }
                
                $basic =$instance_basic_table->newEntity();
                //反向引入公共数据
                $basic->code = $data["code"];  #code
                $basic->status = "运行中"; #状态
                $basic->department_id = $data['department_id']; #部门id
                $basic->location_code = $data['location_code']; #区域code
                $basic->create_time = time(); #创建时间
                $basic->create_by = $this->request->session()->read('Auth.User.id'); #引入人员
                $basic->name = $data["code"]; #name
                $basic->is_delete = 0; #是否删除

                //地域
                $display_name = $this->getLocationNameByLocationCode($data["location_code"]);
                if (!empty($display_name)) {
                    $basic->location_name=$display_name["display_name"];
                } else {
                    $basic->location_name = '';
                }

                switch ($data['type']) {
                    case 'VPC':#引入vpc
                        $basic->type = "vpc";
                        if ($instance_basic_table->save($basic)) {

                            $vpc_extend_data =$vpc_extend_table->newEntity();
                            $vpc_extend_data->basic_id = $basic->id;
                            $json_vpc = json_decode(trim($data->info,chr(239).chr(187).chr(191)), true);
                            if (!empty($json_vpc['Cidr'])) {
                                $vpc_extend_data->cidr = $json_vpc['Cidr'];
                            } else {
                                $vpc_extend_data->cidr = '';
                            }
                            // TODO 添加vpc扩展
                            $vpc_extend_table->save($vpc_extend_data); #保存vpc扩展表信息
                            $temporary_table->deleteAll(['id'=>$data['id']]);#删除临时表记录
                        }
                        break;
                    case 'Router': #路由器
                        $basic->type = "router";
                        $basic->vpc = $data['vpc_code'];
                        $vpc_data = $instance_basic_table->find()->where(['code'=>$data['vpc_code']])->first();
                        if(empty($vpc_data)){
                            $code  = '1';
                            $msg = '没有对应的vpc,请先引入vpc';
                            return compact(array_values($this->_serialize));
                        }else{
                            if ($instance_basic_table->save($basic)) {
                                //配置关联表数据
                                $this->saveInstanceRelation($vpc_data['id'],'vpc',$basic->id,'router');
                                $this->saveInstanceRelation($basic->id,'router',$vpc_data['id'],'vpc');
                                $temporary_table->deleteAll(['id'=>$data['id']]);#删除临时表记录
                            }
                        }


                        break;
                    case 'Subnet': #子网
                        $basic->type = "subnet";
                        $basic->vpc = $data['vpc_code'];
                        $basic->router = $data['router_code'];
                        $vpc_data = $instance_basic_table->find()->where(['code'=>$data['vpc_code']])->first(); #获取对应的vpc信息
                        $router_data = $instance_basic_table->find()->where(['code'=>$data['router_code']])->first(); #获取路由器信息
                        if(empty($vpc_data)){
                            $code  = '1';
                            $msg = '没有对应的vpc,请先引入vpc';
                            $data = '';
                            return compact(array_values($this->_serialize));
                        }
                        if(empty($router_data)){
                            $code  = '1';
                            $msg = '没有对应的路由器,请先引入路由器';
                            $data = '';
                            return compact(array_values($this->_serialize));
                        }

                        if ($instance_basic_table->save($basic)) {
                            //配置子网扩展表
                            $json_subnet = json_decode(trim($data->info,chr(239).chr(187).chr(191)), true);
                            $subnet_extend_data = $subnet_extend_table->newEntity();
                            $subnet_extend_data->basic_id = $basic->id;
                            $subnet_extend_data->cidr = $json_subnet['Cidr'];
                            $subnet_extend_data->isFusion = $json_subnet['FusionType'];
                            if ($json_subnet['FusionType'] == "true") {
                                $subnet_extend_data->fusionType = "openstack";
                            } else {
                                $subnet_extend_data->fusionType = "vmware";
                            }
                            $subnet_extend_table->save($subnet_extend_data);
                            //配置关联表数据
                            $this->saveInstanceRelation($vpc_data['id'],'vpc',$basic->id,'subnet');
                            $this->saveInstanceRelation($basic->id,'subnet',$vpc_data['id'],'vpc');
                            $this->saveInstanceRelation($router_data['id'],'router',$basic->id,'subnet');
                            $this->saveInstanceRelation($basic->id,'subnet',$router_data['id'],'router');
                            $temporary_table->deleteAll(['id'=>$data['id']]);#删除临时表记录
                        }
                        break;
                    case 'Desktop': #桌面
                        $json_host = json_decode(trim($data->info,chr(239).chr(187).chr(191)), true);
                        //检查vpc，路由器
                        $vpc_data = $instance_basic_table->find()->where(['code'=>$data['vpc_code']])->first();
                        $router_data = $instance_basic_table->find()->where(['code'=>$data['router_code']])->first();
                        $sub_default = '';
                        foreach ($json_host['CardDetails'] as $sub) {
                            $subnet_data = $instance_basic_table->find()->contain(['SubnetExtend'])->where(['code'=>$sub['SubnetCode']])->first();
                            if(empty($subnet_data)){
                                $code  = '1';
                                $msg = '没有对应的子网,请先引入子网';
                                $data = '';
                                return compact(array_values($this->_serialize));
                            }
                            if($sub['Type'] == 'default') {
                                $sub_default = $sub;
                            }
                        }

                        if(empty($vpc_data)){
                            $code  = '1';
                            $msg = '没有对应的vpc,请先引入vpc';
                            $data = '';
                            return compact(array_values($this->_serialize));
                        }
                        if(empty($router_data)){
                            $code  = '1';
                            $msg = '没有对应的路由器,请先引入路由器';
                            $data = '';
                            return compact(array_values($this->_serialize));
                        }
                        //查询防火墙
                        $firewall_data = $instance_basic_table->find()->where(['vpc' => $data['vpc_code'], 'type' => 'firewall'])->toArray();
                        $f_count = count($firewall_data);
                        if ($f_count == 0){
                            $code  = '1';
                            $msg = '没有对应的防火墙,请先引入防火墙';
                            $data = '';
                            return compact(array_values($this->_serialize));
                        } elseif ($f_count > 1) {
                            $code  = '1';
                            $msg = '桌面所在vpc的防火墙数量超过一个,无法引入桌面';
                            $data = '';
                            return compact(array_values($this->_serialize));
                        }
                        //检查桌面套件
                        // $vpx_data = $instance_basic_table->find()->where(['vpc' => $data['vpc_code'], 'type' => 'vpx'])->first();
                        $vpx_data = "1";#vpx被wi替代，wi不用cmop管理
                        $ad_data = $instance_basic_table->find()->where(['vpc' => $data['vpc_code'], 'type' => 'ad'])->first();
                        $ddc_data = $instance_basic_table->find()->where(['vpc' => $data['vpc_code'], 'type' => 'ddc'])->first();
                        if(empty($vpx_data) || empty($ad_data) || empty($ddc_data)){
                            $code  = '1';
                            $msg = '桌面所需要的桌面套件不全,请先引入桌面套件';
                            $data = '';
                            return compact(array_values($this->_serialize));
                        }

                        //获取桌面默认子网id，判断是否重置过ad密码
                        if(!empty($sub_default)){
                            $sub = $sub_default;
                            $subnet_data = $instance_basic_table->find()->contain(['SubnetExtend'])->where(['code'=>$sub['SubnetCode']])->first();
                            if(isset($subnet_data['subnet_extend']['isReset']) && $subnet_data['subnet_extend']['isReset']==0){
                                $ad = array();
                                //判断子网是否有账号
                                if (empty($subnet_data['subnet_extend']['aduser'])) {
                                    $ad['method'] = 'desktop_ad_add';#添加ad账号
                                    $ad['loginName'] = $subnet_data['code'];
                                    $ad['loginPassword'] = '123123';
                                    $ad['basicId'] = (string)$subnet_data['id'];
                                }else{
                                    $ad['method'] = 'desktop_ad_pwd';#修改ad密码
                                    $ad['loginName'] = $subnet_data['subnet_extend']['aduser'];
                                    $ad['basicId'] = (string)$subnet_data['id'];
                                }
                                $ad['uid'] = (string)$this->request->session()->read('Auth.User.id');
                                $ad['vpcCode'] = $subnet_data['vpc'];

                                $ad_res = $this->_setAdPwd($ad);
                                if($ad_res == 1){
                                    $code  = '1';
                                    $msg = '修改ad账号失败';
                                    $data = '';
                                    return compact(array_values($this->_serialize));
                                    break;
                                }
                            }
                        }
                    case 'VPX':
                    case 'AD':
                    case 'DDC':
                        $json_host = json_decode(trim($data->info,chr(239).chr(187).chr(191)), true);
                        $vpc = $json_host["VpcCode"];
                        $subnet_data = $instance_basic_table->find()->where(['vpc'=>$vpc,'type'=>'firewall'])->toArray();
                        $i = count($subnet_data);
                        if ($i < 1) {
                            $code  = '1';
                            $msg = '没有对应的防火墙,请先配置防火墙';
                            $data = '';
                            return compact(array_values($this->_serialize));
                        } elseif ($i > 1) {
                            $code  = '1';
                            $msg = '防火墙过多,无法引入';
                            $data = '';
                            return compact(array_values($this->_serialize));
                        }
                    default: #主机
                        $host_extend_data = $host_extend_table->newEntity();
                        $json_host = json_decode(trim($data->info,chr(239).chr(187).chr(191)), true);
                        $basic->type = strtolower($data['type']);
                        if ($basic->type == 'firewall') {
                            $basic->type = 'firewallecs';
                        }
                        if($basic->type == 'ecs'){
                            $basic->type = 'hosts';
                        }
                        $basic->vpc = $data['vpc_code'];
                        $basic->router = $data['router_code'];
                        $basic->subnet = $data['subnet_code'];

                        $vpc_data = $instance_basic_table->find()->where(['code'=>$data['vpc_code']])->first();
                        $router_data = $instance_basic_table->find()->where(['code'=>$data['router_code']])->first();
                        foreach ($json_host['CardDetails'] as $sub) {
                            $subnet_data = $instance_basic_table->find()->contain(['SubnetExtend'])->where(['code'=>$sub['SubnetCode']])->first();
                            if(empty($subnet_data)){
                                $code  = '1';
                                $msg = '没有对应的子网,请先引入子网';
                                $data = '';
                                return compact(array_values($this->_serialize));
                            } else {
                                if($sub['Type'] == 'default' && $basic->type == 'desktop'){
                                    $host_extend_data->aduser = $subnet_data['subnet_extend']['aduser'];
                                    $host_extend_data->adpwd = $subnet_data['subnet_extend']['adpwd'];
                                }
                            }
                        }

                        if(empty($vpc_data)){
                            $code  = '1';
                            $msg = '没有对应的vpc,请先引入vpc';
                            $data = '';
                            return compact(array_values($this->_serialize));
                        }
                        if(empty($router_data)){
                            $code  = '1';
                            $msg = '没有对应的路由器,请先引入路由器';
                            $data = '';
                            return compact(array_values($this->_serialize));
                        }

                        if ($instance_basic_table->save($basic)) {
                            //保存扩展表信息
                            $host_extend_data->basic_id = $basic->id;


                            $host_extend_data->name = $json_host['ComputerName']; #扩展表中的name
                            $host_extend_data->type = $json_host['InstanceTypeCode']; #硬件code
                            $host_extend_data->cpu = $data['cpu'];
                            $host_extend_data->gpu = 0;
                            if (!empty($data['gpu'])){
                                $host_extend_data->gpu = $data['gpu'];
                            }
                            $host_extend_data->memory = $data['memory'];
                            $host_extend_data->image_code = $data['image_code']; #镜像code

                            $image_data = $this->getImageInfoByImageCode($data['image_code']);#获取镜像信息
                            if(!empty($image_data)){
                                $host_extend_data->os_family = $image_data['os_family'];
                                $host_extend_data->plat_form = $image_data['plat_form'];
                            }
                            $host_extend_table->save($host_extend_data);

                            //保存网卡信息
                            foreach ($json_host['CardDetails'] as $key => $value) {
                                $network_card_data = $hosts_network_card_table->newEntity();
                                $network_card_data->network_code = $value['NetworkCardCode'];
                                $network_card_data->basic_id = $basic->id;
                                $network_card_data->hosts_code = $data->code;
                                $network_card_data->ip = $value['Ipaddress'];
                                $network_card_data->subnet_code = $value['SubnetCode'];
                                $network_card_data->is_default = '0';
                                if($value['Type'] == "default"){
                                    $network_card_data->is_default = '1';
                                }
                                $hosts_network_card_table->save($network_card_data);
                            }
                            //保存ecs 的计费周期
                            if ($basic->type == 'ecs') {

                                $charge_data = $instance_charge_table->newEntity();
                                $charge_data->basic_id = $basic->id;
                                $charge_data->instance_type = 'hosts';
                                $charge_data->charge_type = '1';
                                $charge_data->begin = time();
                                $charge_data->save($charge_data);
                            }
                            $temporary_table->deleteAll(['id'=>$data['id']]);#删除临时表记录
                        }
                        break;
                }
                $code  = '0';
                $msg = '操作成功';
                $data = '';
            }
        }
        return compact(array_values($this->_serialize));
    }


    protected function getLocationNameByLocationCode($code){
        $agent_table = TableRegistry::get('Agent');
        $agent_data = $agent_table->find()->where(['class_code'=>$code])->first();
        return $agent_data;
    }

    //获取镜像信息
    protected function getImageInfoByImageCode($code){
        $imagelist_table = TableRegistry::get('Imagelist');
        $agent_data = $imagelist_table->find()->where(['image_code'=>$code])->first();
        return $agent_data;
    }


    //保存关联表信息
    protected function saveInstanceRelation($fromid,$fromtype,$toid,$totype){
        $instance_relation_table = TableRegistry::get('InstanceRelation');
        $relation_data =$instance_relation_table->newEntity();

        $relation_data->fromid = $fromid;
        $relation_data->fromtype = $fromtype;
        $relation_data->toid = $toid;
        $relation_data->totype = $totype;
        $instance_relation_table->save($relation_data);
    }

    protected function _setAdPwd($ad){
        // debug(json_encode($ad));die;
        $orders = new OrdersController();
        $result = $orders->ajaxFun($ad);
        $message = $result["Message"];
        if($result['Code']==0){
            $subnet_extend_table = TableRegistry::get('SubnetExtend');
            $data = $subnet_extend_table->newEntity();
            $subnet_data = $subnet_extend_table->find()->where(['basic_id'=>$ad['basicId']])->first();
            if(!empty($subnet_data) && is_array($subnet_data)){
                $subnet_data['isReset'] = 1;
                $data = $subnet_extend_table->patchEntity($data,$subnet_data);
                $result = $subnet_extend_table->save($data);
            }
        }else{
            return 1;
        }
        return 0;
    }

    /**
    * 根据department_id ,获取一个uid
    */
    protected function _getUserIdByDepartment($department_id){
        $accounts_table = TableRegistry::get('Accounts');
        $data = $accounts_table->find()->where(['department_id'=>$department_id])->first();
        $uid = 0;
        if(!empty($data)){
            $uid = $data['id'];
        }
        return $uid;
    }

    /**
    * 根据子网aduser字段，自动判断添加或修改aduser
    * @param $data:子网数据（包含子网扩展表）
    */
    protected function _addEditUserBySubnetData($data)
    {

        $ad = array();
        //判断子网是否有账号
        if (empty($data['subnet_extend']['aduser'])) {
            $ad['method'] = 'desktop_ad_add';#添加ad账号
            $ad['loginName'] = $data['code'];
            $ad['loginPassword'] = '123123';
            $ad['basicId'] = (string)$data['id'];
        }else{
            $ad['method'] = 'desktop_ad_pwd';#修改ad密码
            $ad['loginName'] = $data['subnet_extend']['aduser'];
            $ad['basicId'] = (string)$data['id'];
        }
        $ad['uid'] = (string)$this->_getUserIdByDepartment($data['department_id']);
        $ad['vpcCode'] = $data['vpc'];

        $ad_res = $this->_setAdPwd($ad); #掉接口
        return $ad_res;
    }

    //更新网卡数据接口同步数据
    public function updateNetCard()
    {
         $connection = ConnectionManager::get('default');
        $sql        = ' SELECT b.network_card,a.id,a.`code`,a.subnet,b.ip,a.type,b.`name`,a.department_id,a.create_by FROM	cp_instance_basic a ';
        $sql .= ' LEFT JOIN cp_host_extend b ON a.id = b.basic_id LEFT JOIN cp_hosts_network_card c ON c.basic_id=a.id ';
        $sql .= ' WHERE 1 = 1 AND a.`code` != "" AND a.`code` IS NOT NULL AND (a.type = \'hosts\'	OR a.type = \'desktop\' OR a.type=\'firewallecs\') and c.id IS NULL ORDER BY id ';
        $query = $connection->execute($sql)->fetchAll('assoc');
        $table = TableRegistry::get('HostsNetworkCard');
        foreach ($query as $key => $value) {
            $entity=$table->newEntity();
            $entity->network_code=$value["network_card"];
            $entity->basic_id=$value["id"];
            $entity->hosts_code=$value["code"];
            $entity->ip=$value["ip"];
            $entity->subnet_code=$value["subnet"];
            $entity->is_default=1;
            $result =$table->save($entity);
        }
    }

    public function updateNetCardOld()
    {
        $connection = ConnectionManager::get('default');
        $sql        = ' SELECT b.network_card,a.id,a.`code`,a.subnet,b.ip,a.type,b.`name`,a.department_id,a.create_by FROM	cp_instance_basic a ';
        $sql .= ' LEFT JOIN cp_host_extend b ON a.id = b.basic_id ';
        $sql .= ' where 1=1 AND (a.`status` != \'创建中\') and a.`code`!="" and a.`code` is not NULL  ';
        $sql .= ' and (b.network_card is NULL or b.network_card="") ';
        $sql .= ' and (a.type=\'hosts\' OR a.type=\'desktop\' OR a.type=\'firewallecs\') ORDER BY id ';
        $query = $connection->execute($sql)->fetchAll('assoc');
        $orders = new OrdersController();
        $param = array();
        $param["method"]="instance_card";
        foreach ($query as $key => $value) {
            $param["uid"]= (string)$value["create_by"];
            $param["code"]= $value["code"];
            $param["id"]= $value["id"];
            $result = $orders->ajaxFun($param);#掉底层接口，获取主机列表
        }
    }
}
