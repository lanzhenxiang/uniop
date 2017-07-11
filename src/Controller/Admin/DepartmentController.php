<?php
/**
 * Created by PhpStorm.
 * User: kelly
 * Date: 2016/12/16
 * Time: 17:11
 */
namespace App\Controller\Admin;

use App\Controller\AdminController;
use App\Controller\AccountsController;
use App\Controller\OrdersController;
use App\Controller\SobeyController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Controller\Controller;
use Requests as Requests;

class DepartmentController extends AdminController
{
    public $paginate = [
        'limit' => 15,
    ];

    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_tenant_tenants');
        if (!$checkPopedomlist) {
            return $this->redirect('/');
        }

    }

    public $_pageList = array(
        'total' => 0,
        'rows' => array()
    );

    public function index()
    {

    }

    public function lists()
    {
        $departments = TableRegistry::get('Departments');
        $request = $this->request->query;
        $this->paginate['limit'] = $request['limit'];
        $this->paginate['page'] = $request['offset'] / $request['limit'] + 1;
        $where = array();
        //是否为系统管理员
        if(!in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))) {
            $where['id'] = $this->request->session()->read('Auth.User.department_id');
        }
        //搜索框
        if (isset($request['search']) && trim($request['search']) != '') {
            $search = $request['search'];
            $where['or'] = array(
                'Departments.name like' => "%$search%",
                'Departments.identifier like' => "%$search%",
                'Departments.access_key like' => "%$search%"
            );
        }

        $this->_pageList['total'] = $departments->find()->where(array($where))->count();
        $this->_pageList['rows'] = $departments->find()->where(array($where))->offset($request['offset'])->limit($request['limit'])->map(function ($row) {
            //翻译租户类型
            if ($row['type'] == 'normal_inner') {
                $row['type'] = '内部租户';
            }elseif ($row['type'] == 'normal_outer') {
                $row['type'] = '外部租户';
            } elseif ($row['type'] == 'platform') {
                $row['type'] = '平台租户';
            }
            //添加创建人
            $row['username'] = TableRegistry::get('Accounts')->find()->select(['username'])->where(array('Accounts.id' => $row['create_by']))->first()['username'];
            //修改时间格式
            $row['create_time'] = date('Y-m-d H:i:s', $row['create_time']);
            //限制备注字数
            $row['note'] = mb_substr($row['note'], 0, 20);
            return $row;
        });
        echo json_encode($this->_pageList);
        exit();
    }

    //新建基本信息
    public function addinfo()
    {
        $checkPopedomlist_tenant = parent::checkPopedomlist('cmop_global_tenant_admin');
        $checkPopedomlist_sys = parent::checkPopedomlist('cmop_global_sys_admin');
        if ($checkPopedomlist_tenant) {
            if (!$checkPopedomlist_sys) {
                return $this->redirect('/admin/department');
            }
        }
        $systemsetting = TableRegistry::get('Systemsetting');
        $para = $systemsetting->find()->select(['id', 'para_code', 'para_value', 'para_note'])->where(array('para_type' => 1))->toArray();
        $this->set('para', $para);
    }

    public function postadd()
    {
        $departments = TableRegistry::get('Departments');
        $usersetting = TableRegistry::get('UserSetting');
        $systemsetting = TableRegistry::get('Systemsetting');
        $budget_template = TableRegistry::get('BudgetTemplate');
        $public = new PublicController();
        $request = $this->request->data;
        $message = array('code' => 1, 'msg' => '添加失败');
//租户名称是否存在
        $name_count = $departments->find()->select(['name'])->where(array('name' => $request['name']))->count();
        if ($name_count > 0) {
            $message = array('code' => 1, 'msg' => '该租户名称已存在');
            echo json_encode($message);
            exit;
        }
        $parameter['name'] = $request['name'];
        //内、外部租户都传normal
        if($request['type']=='platform') {
            $parameter['type'] = $request['type'];
            $parameter['tenantsType'] = $request['type'];
        }else{
            $parameter['type'] ='normal';
            $parameter['tenantsType'] = $request['normal'];
        }
        $parameter['note'] = $request['note'];
        $parameter['email'] = 'sobey@cmop.com';
//        $parameter["dept_code"]=$request["dept_code"];
        $parameter['method'] = 'tenants_add';
        $parameter['parent_id'] = '0';
        $parameter['sort_order'] = '0';
        $parameter['uid'] = (string)$this->request->session()->read('Auth.User.id');
        $order = new OrdersController();
        $url = Configure::read('URL');
        $IN_result = $order->postInterface($url, $parameter);
        if ($IN_result['Code'] == 0) {
            $message = array('code' => 0, 'msg' => '添加成功');
            $public->adminlog('Department', '添加租户---' . $request['name'] . '的基本信息及租户参数');
            //添加的租户id
            $department_id = $IN_result['Data']['id'];

            //修改类型
            $departments->updateAll(array('type'=> $request['type']),array('id'=>$department_id));

            //添加默认配额
            $type=$departments->find()->select(['type'])->where(array('id'=>$department_id))->first()['type'];
            $info=$budget_template->find()->select(['para_code','para_value','para_note'])->where(array('depart_type'=>$type))->toArray();

            foreach ($info as $key => $value) {
                $data['para_code'] = $value['para_code'];
                $data['para_value'] = $value['para_value'];
                $data['para_note'] = $value['para_note'];
                $data['owner_id'] = $department_id;
                $data['owner_type'] = 2;
                $user_data = $usersetting->newEntity();
                $user_data = $usersetting->patchEntity($user_data, $data);
                $res = $usersetting->save($user_data);
            }
        } else {
            $message = array('code' => 1, 'msg' => $IN_result['Message']);
            $public->adminlog('Department', '添加租户失败');
        }
        echo json_encode($message);
        exit;
    }

//修改
    public function edit()
    {
//        $checkPopedomlist_tenant = parent::checkPopedomlist('cmop_global_tenant_admin');
//        $checkPopedomlist_sys = parent::checkPopedomlist('cmop_global_sys_admin');
//        if ($checkPopedomlist_tenant) {
//            if (!$checkPopedomlist_sys) {
//                return $this->redirect('/admin/department');
//            }
//        }
        $departments = TableRegistry::get('Departments');
        $id = $this->request->query['id'];
        $depart_data = $departments->find()->select(['name', 'type', 'note', 'id'])->where(array('id' => $id))->first();
        $this->set('depart_data', $depart_data);

    }

    public function postedit()
    {

        $public = new PublicController();
        $departments = TableRegistry::get('Departments');
        $request = $this->request->data;
        $data['modify_time'] = time();
//        $data['email']='sobey@cmop.com';
        $data['name'] = $request['name'];
        $data['type'] = $request['type'];
        $data['note'] = $request['note'];
        $id = $request['id'];
        $origin=$departments->find()->where(array('id'=>$id))->first();
        if($origin['name']==$data['name']&&$origin['type']==$data['type']&&$origin['note']==$data['note']){
            $message=array('code'=>2,'msg'=>'未进行修改');
        }else {
            if($departments->find()->where(array('name'=>$data['name'],'id <>'=>$id))->count()>0){
                $message=array('code'=>3,'msg'=>'该租户名已存在');
            }else {
                $result = $departments->updateAll($data, array('id' => $id));
                if ($result) {
                    $public->adminlog('Department', '修改租户---' . $data['name'] . '的基本信息成功');
                    $message = array('code' => 0, 'msg' => '修改成功');
                } else {
                    $public->adminlog('Department', '修改租户---' . $data['name'] . '的基本信息失败');
                    $message = array('code' => 1, 'msg' => '修改失败');
                }
            }
        }
        echo json_encode($message);
        exit;
    }

    //配额明细
    public function management()
    {
        $departments = TableRegistry::get('Departments');
        $id = $this->request->query['id'];
        $depart_data = $departments->find()->select(['name', 'id'])->toArray();
        //如果没有配额,自动添加
        $usersetting = TableRegistry::get('UserSetting');
        $budget_template = TableRegistry::get('BudgetTemplate');
        $budget_exist=$usersetting->find()->select(['id'])->where(array('owner_id'=>$id))->count();
        if($budget_exist==0){
            $type=$departments->find()->select(['type'])->where(array('id'=>$id))->first()['type'];
            $budget_data=$budget_template->find()->select(['para_code','para_value','para_note'])->where(array('depart_type'=>$type))->toArray();
            foreach($budget_data as $key => $value){
                $insert_data['para_code']=$value['para_code'];
                $insert_data['para_value']=$value['para_value'];
                $insert_data['para_note']=$value['para_note'];
                $insert_data['owner_id']=$id;
                $insert_data['owner_type']=2;
                $insert=$usersetting->newEntity();
                $insert=$usersetting->patchEntity($insert,$insert_data);
                $usersetting->save($insert);
            }
        }
        //获得一个userid
        $accounts = TableRegistry::get('Accounts');
//        $userid = $accounts->find()->select(['id'])->where(array('department_id' => $id))->first()['id'];
        //租户配额，组合资源使用量
        $bugedt = Requests::post(Configure::read('Api.cmop') . '/SystemInfo/getDepartBuget', [], [
//            'userid' => $userid,
            'department_id' => $id
        ], [
            'verify' => false
        ]);
        $bugedt_arr = json_decode(trim($bugedt->body, chr(239) . chr(187) . chr(191)), true);
        //已用
        $used = Requests::post(Configure::read('Api.cmop') . '/SystemInfo/getDepartUsed', [], [
//            'userid' => $userid,
            'department_id' => $id,
            "source_type" => "cpu_used,router_used,subnet_used,disks_used,gpu_used,memory_used,fics_num_used,oceanstor9k_num_used,basic_used,fire_used,elb_used,eip_used"
        ], [
            'verify' => false
        ]);
        $used_arr = json_decode(trim($used->body, chr(239) . chr(187) . chr(191)), true);
//var_dump($bugedt_arr);
//        var_dump($used_arr);
//exit;
        //vpc,路由器
        if (isset($bugedt_arr['data']['router_bugedt']) && !empty($bugedt_arr['data']['router_bugedt'])) {
            $router['budget'] = $bugedt_arr['data']['router_bugedt'];
        } else {
            $router['budget'] = 0;
        }
        if (isset($used_arr['data']['router_used']) && !empty($used_arr['data']['router_used'])) {
            $router['used'] = $used_arr['data']['router_used'];
        } else {
            $router['used'] = 0;
        }
        if ($router['budget'] != 0) {
            $router['percent'] = ceil($router['used'] / $router['budget'] * 100) . '%';
        } else {
            $router['percent'] = '0%';
        }
        $router['can_use'] = $router['budget'] - $router['used'];
        $router['name'] = 'VPC/路由器(个)';
        //cpu
        if (isset($bugedt_arr['data']['cpu_bugedt']) && !empty($bugedt_arr['data']['cpu_bugedt'])) {
            $cpu['budget'] = $bugedt_arr['data']['cpu_bugedt'];
        } else {
            $cpu['budget'] = 0;
        }
        if (isset($used_arr['data']['cpu_used']) && !empty($used_arr['data']['cpu_used'])) {
            $cpu['used'] = $used_arr['data']['cpu_used'];
        } else {
            $cpu['used'] = 0;
        }
        if ($cpu['budget'] != 0) {
            $cpu['percent'] = ceil($cpu['used'] / $cpu['budget'] * 100) . '%';
        } else {
            $cpu['percent'] = '0%';
        }
        $cpu['can_use'] = $cpu['budget'] - $cpu['used'];
        $cpu['name'] = 'CPU(核)';

        //内存
        if (isset($bugedt_arr['data']['memory_buget']) && !empty($bugedt_arr['data']['memory_buget'])) {
            $memory['budget'] = $bugedt_arr['data']['memory_buget'];
        } else {
            $memory['budget'] = 0;
        }
        if (isset($used_arr['data']['memory_used']) && !empty($used_arr['data']['memory_used'])) {
            $memory['used'] = $used_arr['data']['memory_used'];
        } else {
            $memory['used'] = 0;
        }
        if ($memory['budget'] != 0) {
            $memory['percent'] = ceil($memory['used'] / $memory['budget'] * 100) . '%';
        } else {
            $memory['percent'] = '0%';
        }
        $memory['can_use'] = $memory['budget'] - $memory['used'];
        $memory['name'] = '内存(GB)';

        //gpu
        if (isset($bugedt_arr['data']['gpu_bugedt']) && !empty($bugedt_arr['data']['gpu_bugedt'])) {
            $gpu['budget'] = $bugedt_arr['data']['gpu_bugedt'];
        } else {
            $gpu['budget'] = 0;
        }
        if (isset($used_arr['data']['gpu_used']) && !empty($used_arr['data']['gpu_used'])) {
            $gpu['used'] = $used_arr['data']['gpu_used'];
        } else {
            $gpu['used'] = 0;
        }
        if ($gpu['budget'] != 0) {
            $gpu['percent'] = ceil($gpu['used'] / $gpu['budget'] * 100) . '%';
        } else {
            $gpu['percent'] = '0%';
        }
        $gpu['can_use'] = $gpu['budget'] - $gpu['used'];
        $gpu['name'] = 'GPU(MB)';

        //块存储
        if (isset($bugedt_arr['data']['disks_bugedt']) && !empty($bugedt_arr['data']['disks_bugedt'])) {
            $disk['budget'] = $bugedt_arr['data']['disks_bugedt'];
        } else {
            $disk['budget'] = 0;
        }
        if (isset($used_arr['data']['disks_used']) && !empty($used_arr['data']['disks_used'])) {
            $disk['used'] = $used_arr['data']['disks_used'];
        } else {
            $disk['used'] = 0;
        }
        if ($disk['budget'] != 0) {
            $disk['percent'] = ceil($disk['used'] / $disk['budget'] * 100) . '%';
        } else {
            $disk['percent'] = '0%';
        }
        $disk['can_use'] = $disk['budget'] - $disk['used'];
        if (isset($bugedt_arr['data']['disks_cap_bugedt']) && !empty($bugedt_arr['data']['disks_cap_bugedt'])) {
            $disk['cap_budget'] = $bugedt_arr['data']['disks_cap_bugedt'];
        } else {
            $disk['cap_budget'] = 0;
        }
        $disk['name'] = '块存储(个,单个最大容量' . $disk['cap_budget'] . 'GB)';

        //fics存储卷
        if (isset($bugedt_arr['data']['fics_num_bugedt']) && !empty($bugedt_arr['data']['fics_num_bugedt'])) {
            $fics['budget'] = $bugedt_arr['data']['fics_num_bugedt'];
        } else {
            $fics['budget'] = 0;
        }
        if (isset($used_arr['data']['fics_num_used']) && !empty($used_arr['data']['fics_num_used'])) {
            $fics['used'] = $used_arr['data']['fics_num_used'];
        } else {
            $fics['used'] = 0;
        }
        if ($fics['budget'] != 0) {
            $fics['percent'] = ceil($fics['used'] / $fics['budget'] * 100) . '%';
        } else {
            $fics['percent'] = '0%';
        }
        $fics['can_use'] = $fics['budget'] - $fics['used'];
        if (isset($bugedt_arr['data']['fics_cap_bugedt']) && !empty($bugedt_arr['data']['fics_cap_bugedt'])) {
            $fics['cap_budget'] = $bugedt_arr['data']['fics_cap_bugedt'];
        } else {
            $fics['cap_budget'] = 0;
        }
        $fics['name'] = 'FICS存储卷(个,总容量' . $fics['cap_budget'] . 'GB)';

        //h9000存储卷
        if (isset($bugedt_arr['data']['oceanstor9k_num_bugedt']) && !empty($bugedt_arr['data']['oceanstor9k_num_bugedt'])) {
            $h9000['budget'] = $bugedt_arr['data']['oceanstor9k_num_bugedt'];
        } else {
            $h9000['budget'] = 0;
        }
        if (isset($used_arr['data']['oceanstor9k_num_used']) && !empty($used_arr['data']['oceanstor9k_num_used'])) {
            $h9000['used'] = $used_arr['data']['oceanstor9k_num_used'];
        } else {
            $h9000['used'] = 0;
        }
        if ($h9000['budget'] != 0) {
            $h9000['percent'] = ceil($h9000['used'] / $h9000['budget'] * 100) . '%';
        } else {
            $h9000['percent'] = '0%';
        }
        $h9000['can_use'] = $h9000['budget'] - $h9000['used'];
        if (isset($bugedt_arr['data']['oceanstor9k_cap_bugedt']) && !empty($bugedt_arr['data']['oceanstor9k_cap_bugedt'])) {
            $h9000['cap_budget'] = $bugedt_arr['data']['oceanstor9k_cap_bugedt'];
        } else {
            $h9000['cap_budget'] = 0;
        }
        $h9000['name'] = 'H9000存储卷(个,总容量' . $h9000['cap_budget'] . 'GB)';

        //桌面基础套件
        if (isset($bugedt_arr['data']['basic_budget']) && !empty($bugedt_arr['data']['basic_budget'])) {
            $basic['budget'] = $bugedt_arr['data']['basic_budget'];
        } else {
            $basic['budget'] = 0;
        }
        if (isset($used_arr['data']['basic_used']) && !empty($used_arr['data']['basic_used'])) {
            $basic['used'] = $used_arr['data']['basic_used'];
        } else {
            $basic['used'] = 0;
        }
        if ($basic['budget'] != 0) {
            $basic['percent'] = ceil($basic['used'] / $basic['budget'] * 100) . '%';
        } else {
            $basic['percent'] = '0%';
        }
        $basic['can_use'] = $basic['budget'] - $basic['used'];
        $basic['name'] = '桌面基础套件(套)';
        //防火墙
        if (isset($bugedt_arr['data']['fire_budget']) && !empty($bugedt_arr['data']['fire_budget'])) {
            $fire['budget'] = $bugedt_arr['data']['fire_budget'];
        } else {
            $fire['budget'] = 0;
        }
        if (isset($used_arr['data']['fire_used']) && !empty($used_arr['data']['fire_used'])) {
            $fire['used'] = $used_arr['data']['fire_used'];
        } else {
            $fire['used'] = 0;
        }
        if ($fire['budget'] != 0) {
            $fire['percent'] = ceil($fire['used'] / $fire['budget'] * 100) . '%';
        } else {
            $fire['percent'] = '0%';
        }
        $fire['can_use'] = $fire['budget'] - $fire['used'];
        $fire['name'] = '防火墙(套)';
        //负载均衡
        if (isset($bugedt_arr['data']['elb_budget']) && !empty($bugedt_arr['data']['elb_budget'])) {
            $elb['budget'] = $bugedt_arr['data']['elb_budget'];
        } else {
            $elb['budget'] = 0;
        }
        if (isset($used_arr['data']['elb_used']) && !empty($used_arr['data']['elb_used'])) {
            $elb['used'] = $used_arr['data']['elb_used'];
        } else {
            $elb['used'] = 0;
        }
        if ($elb['budget'] != 0) {
            $elb['percent'] = ceil($elb['used'] / $elb['budget'] * 100) . '%';
        } else {
            $elb['percent'] = '0%';
        }
        $elb['can_use'] = $elb['budget'] - $elb['used'];
        $elb['name'] = '负载均衡(套)';
        //公网ip
        if (isset($bugedt_arr['data']['eip_budget']) && !empty($bugedt_arr['data']['eip_budget'])) {
            $eip['budget'] = $bugedt_arr['data']['eip_budget'];
        } else {
            $eip['budget'] = 0;
        }
        if (isset($used_arr['data']['eip_used']) && !empty($used_arr['data']['eip_used'])) {
            $eip['used'] = $used_arr['data']['eip_used'];
        } else {
            $eip['used'] = 0;
        }
        if ($eip['budget'] != 0) {
            $eip['percent'] = ceil($eip['used'] / $eip['budget'] * 100) . '%';
        } else {
            $eip['percent'] = '0%';
        }
        $eip['can_use'] = $eip['budget'] - $eip['used'];
        $eip['name'] = '公网IP(个)';


        $quota = array($router, $cpu, $disk, $gpu, $memory, $fics, $h9000, $basic, $fire, $elb, $eip);
        $this->set('quota', $quota);
        $this->set('depart_data', $depart_data);
        $this->set('select_id', $id);
        $this->set('budget', $bugedt_arr['data']);
        $this->set('used', $used_arr['data']);

    }

//调整配额
    public function adjust()
    {
        $id = $this->request->query['id'];
        $usersetting = TableRegistry::get('UserSetting');
        $user_data = $usersetting->find('all')->where(array('owner_type' => 2, 'owner_id' => $id))->toArray();

        if(empty($user_data)){
            $user_data=TableRegistry::get('Systemsetting')->find('all')->where(array('para_type'=>1))->toArray();
        }
        $router = $this->check('router_bugedt', $user_data);
        $cpu = $this->check('cpu_bugedt', $user_data);
        $disk = $this->check('disks_bugedt', $user_data);
        $disk_cap = $this->check('disks_cap_bugedt', $user_data);
        $gpu = $this->check('gpu_bugedt', $user_data);
        $memory = $this->check('memory_buget', $user_data);
        $fics = $this->check('fics_num_bugedt', $user_data);
        $fics_cap = $this->check('fics_cap_bugedt', $user_data);
        $h9000 = $this->check('oceanstor9k_num_bugedt', $user_data);
        $h9000_cap = $this->check('oceanstor9k_cap_bugedt', $user_data);
        $basic = $this->check('basic_budget', $user_data);
        $fire = $this->check('fire_budget', $user_data);
        $elb = $this->check('elb_budget', $user_data);
        $eip = $this->check('eip_budget', $user_data);


        $this->set('id', $id);
        $this->set('router', $router);
        $this->set('cpu', $cpu);
        $this->set('disk', $disk);
        $this->set('disk_cap', $disk_cap);
        $this->set('gpu', $gpu);
        $this->set('memory', $memory);
        $this->set('fics', $fics);
        $this->set('fics_cap', $fics_cap);
        $this->set('h9000', $h9000);
        $this->set('h9000_cap', $h9000_cap);
        $this->set('basic', $basic);
        $this->set('fire', $fire);
        $this->set('elb', $elb);
        $this->set('eip', $eip);

    }

    public function check($name, $arr)
    {
        if (!isset($arr) || empty($arr)) {
            return 0;
        }
        $para_value = 0;
        foreach ($arr as $key => $value) {
            if ($value['para_code'] == $name) {
                $para_value = $value['para_value'];
                break;
            }
        }
        return $para_value;
    }

    public function postadjust()
    {
        $usersetting = TableRegistry::get('UserSetting');
        $public = new PublicController();
        $request = $this->request->data;
        $id = $request['id'];
        $user = array();
        $subnet = array();
        $count = 0;
        if(empty($usersetting->find()->select(['id'])->where(array('owner_id'=>$id))->first())){
            $res=true;
        }else {
            $res = $usersetting->deleteAll(array('owner_id' => $id));
        }
        if ($res) {
            foreach ($request as $key => $value) {
                if ($key == 'router') {
                    $user['para_value'] = $value;
                    $user['para_code'] = 'router_bugedt';
                    $user['para_note'] = 'VPC配额';


                    //子网
                    $subnet = array(
                        'para_value' => 15 * $value,
                        'para_code' => 'subnet_bugedt',
                        'para_note' => '子网配额',
                        'owner_id' => $id,
                        'owner_type' => 2
                    );
                } elseif ($key == 'cpu') {
                    $user['para_value'] = $value;
                    $user['para_code'] = 'cpu_bugedt';
                    $user['para_note'] = '租户CPU配额(核)';
                } elseif ($key == 'disk') {
                    $user['para_value'] = $value;
                    $user['para_code'] = 'disks_bugedt';
                    $user['para_note'] = '磁盘配额';
                } elseif ($key == 'disk_cap') {
                    $user['para_value'] = $value;
                    $user['para_code'] = 'disks_cap_bugedt';
                    $user['para_note'] = '磁盘容量配额';
                } elseif ($key == 'gpu') {
                    $user['para_value'] = $value;
                    $user['para_code'] = 'gpu_bugedt';
                    $user['para_note'] = '租户GPU配额(GB)';
                } elseif ($key == 'memory') {
                    $user['para_value'] = $value;
                    $user['para_code'] = 'memory_buget';
                    $user['para_note'] = '租户内存配额(GB)';
                } elseif ($key == 'fics') {
                    $user['para_value'] = $value;
                    $user['para_code'] = 'fics_num_bugedt';
                    $user['para_note'] = 'fics存储配额(数量）';
                } elseif ($key == 'fics_cap') {
                    $user['para_value'] = $value;
                    $user['para_code'] = 'fics_cap_bugedt';
                    $user['para_note'] = 'fics存储配额(容量）';
                } elseif ($key == 'h9000') {
                    $user['para_value'] = $value;
                    $user['para_code'] = 'oceanstor9k_num_bugedt';
                    $user['para_note'] = '华为9K存储配额(容量）';
                } elseif ($key == 'h9000_cap') {
                    $user['para_value'] = $value;
                    $user['para_code'] = 'oceanstor9k_cap_bugedt';
                    $user['para_note'] = '华为9K存储配额(数量）';
                } elseif ($key == 'basic') {
                    $user['para_value'] = $value;
                    $user['para_code'] = 'basic_budget';
                    $user['para_note'] = '桌面基础套件配额';
                } elseif ($key == 'fire') {
                    $user['para_value'] = $value;
                    $user['para_code'] = 'fire_budget';
                    $user['para_note'] = '防火墙配额';
                } elseif ($key == 'elb') {
                    $user['para_value'] = $value;
                    $user['para_code'] = 'elb_budget';
                    $user['para_note'] = '负载均衡配额';
                } elseif ($key == 'eip') {
                    $user['para_value'] = $value;
                    $user['para_code'] = 'eip_budget';
                    $user['para_note'] = '公网IP配额';
                }

                if (!empty($user)) {
                    $user['owner_id'] = $id;
                    $user['owner_type'] = 2;
                    $seting = $usersetting->newEntity();
                    $seting = $usersetting->patchEntity($seting, $user);
                    $result = $usersetting->save($seting);
                    if ($result) {
                        $count += 1;
                    }
                }

            }
            if (!empty($subnet)) {
                $subseting = $usersetting->newEntity();
                $subseting = $usersetting->patchEntity($subseting, $subnet);
                $res = $usersetting->save($subseting);
            }
            if ($count > 0) {
                $public->adminlog('Department', '调整资源配额成功');
                $message = array('code' => 0, 'msg' => '调整资源配额成功');
            } else {
                $public->adminlog('Department', '调整资源配额失败');
                $message = array('code' => 2, 'msg' => '调整资源配额失败');
            }
        } else {
            $public->adminlog('Department', '调整资源配额失败,未成功删除原信息');
            $message = array('code' => 1, 'msg' => '未成功删除原信息');
        }

        echo json_encode($message);
        exit;


    }

    //删除
    public function delete()
    {
        $request = $this->request->data;
        $departments = TableRegistry::get('Departments');
        $account = TableRegistry::get('Accounts');
        $instance_basic = TableRegistry::get('InstanceBasic');
        $public = new PublicController();
        //删除个数
        $count = 0;
        //数据库删除个数
        $count_delete=0;
        //存在人员个数
        $count_exist_accounts=0;
        //存在资源个数
        $count_exist_basic=0;

        foreach ($request['rows'] as $key => $value) {
            $id = $value['id'];
            //租户下是否存在人员
            $exist_accounts = $account->find()->select(['id'])->where(array('department_id' => $id))->count();
            //租户下是否存在资源
            $exist_basic = $instance_basic->find()->select(['id'])->where(array('department_id' => $id))->count();
            if ($exist_accounts > 0) {
                $count_exist_accounts+=1;
                continue;
            } elseif ($exist_basic > 0) {
                $count_exist_basic+=1;
                continue;
            } else {
                $parameter['method'] = 'tenants_del';
                $parameter['department_id'] = $id;
                $parameter['uid'] = (string)$this->request->session()->read('Auth.User.id');
                $order = new OrdersController();
                $url = Configure::read('URL');
                $IN_result = $order->postInterface($url, $parameter);
               // var_dump($IN_result);exit;
                if ($IN_result['Code'] == 0) {
                    $usersetting = TableRegistry::get('UserSetting');
                    $count_user = $usersetting->find()->select(['id'])->where(array('owner_id' => $id))->count();
                    $user_result = $usersetting->deleteAll(array('owner_id' => $id));
                    if ($count_user == $user_result) {
                        $count += 1;
                    }
                } else {
                    $res_delete=$departments->deleteAll(array('id' => $id));
                    if($res_delete){
                        $count_delete+=1;
                    }
                }
            }
        }
        if ($count > 0) {
            $public->adminlog('Department', '删除' . $count . '个租户及租户参数');
            $msg='成功删除' . $count . '条数据';
            if($count_exist_accounts>0){
                $msg.=',有'.$count_exist_accounts.'个租户下存在人员';
            }
            if($count_exist_basic>0){
                $msg.=',有'.$count_exist_basic.'个租户下存在资源';
            }

            echo json_encode(array('code' => 0, 'msg' => $msg));
            exit;
        } else {
            $public->adminlog('Department', '删除租户失败');
            $msg='删除租户失败';
            if($count_exist_accounts>0){
                $msg.=',有'.$count_exist_accounts.'个租户下存在人员';
            }
            if($count_exist_basic>0){
                $msg.=',有'.$count_exist_basic.'个租户下存在资源';
            }
            if($count_delete>0){
                $msg.=',数据库删除'.$count_delete.'条数据';
            }
            echo json_encode(array('code' => 1, 'msg' => $msg));
            exit;
        }
    }
    /**
     * 租户配置可选公共子网列表
     * @return [type] [description]
     */
    public function subnetSet()
    {
        $departments = TableRegistry::get('Departments');
        $depart_data = $departments->find()->select(['name', 'id'])->toArray();
        $id = $this->request->query['id'];
        
        $condition['InstanceBasic.type'] = "subnet";
        $condition['isPublic'] = 1;
        //可选子网列表
        $instance_basic = TableRegistry::get("InstanceBasic");
        $lists = $instance_basic->find("SubnetExtend")->where($condition)->autofields(true)->toArray();
        //已选子网列表
        $condition['dept_subnet.dept_id'] = $id;
        $selected_lists = $instance_basic->find("SubnetExtend")
                ->find('list',[
                    'keyField' => 'id',
                    'valueField' => 'dept_set_id'
                ])
                ->join(['dept_subnet'=>[
                    'table' =>'cp_department_subnet',
                    'type'  =>'LEFT',
                    'conditions'=>'dept_subnet.subnet_id = InstanceBasic.id'
                ]])->where($condition)->autofields(true)->select(['dept_set_id'=>'dept_subnet.dept_id'])->toArray();
        $this->set('select_id', $id);
        $this->set(compact(['depart_data','lists','selected_lists']));
    }

    /**
     * 保存租户配置的公共子网
     * @return [type] [description]
     */
    public function saveSubnetSet()
    {
        $department_subnet = TableRegistry::get("DepartmentSubnet");
        $subnet_ids = rtrim($this->request->data['subnet_ids'],',');
        $dept_id = $this->request->data['department_id'];
        try {
            $department_subnet->connection()->begin();

            if(empty($subnet_ids) || $subnet_ids == ""){
                $department_subnet->deleteAll(['dept_id'=>$dept_id]);
            }else{
                $subnet_arr = explode(",", $subnet_ids);
                //删除已有但是没有选中的配置
                $result = $department_subnet->deleteAll(['dept_id'=>$dept_id,'subnet_id not in'=>$subnet_ids]);
                foreach ($subnet_arr as $key => $subnet_id) {
                    $entity = $department_subnet->findOrCreate(['dept_id'=>$dept_id,'subnet_id'=>$subnet_id]);
                    //exit;
                    if(!($entity instanceof \Cake\ORM\Entity)){
                        throw new \Exception("保存配置失败【新增配置失败】。", -1);
                    }
                }
            }
            $department_subnet->connection()->commit();
            $msg = "保存配置成功";
            $code = 0;
        } catch (\Exception $e) {
            $department_subnet->connection()->rollback();
            $msg = $e->getMessage();
            $code = $e->getCode();
        }
        $this->viewClass = "Json";
        $this->set(compact(['code','msg']));
        $this->set("_serialize",['code','msg']);
    }
}