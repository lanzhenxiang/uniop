<?php
/**
 * Created by PhpStorm.
 * User: kelly
 * Date: 2017/5/8
 * Time: 18:34
 */
namespace App\Controller\Console;

use App\Controller\Console\ConsoleController;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Network\Http\Client;
use Cake\Error\FatalErrorException;


class SecurityGroupController extends ConsoleController
{
    private $_popedomName = array(
        'firewall' => 'ccm_ps_security_firewall',
        'firewallpolicy' => 'ccm_ps_security_firewall_policy',
        'firewalltemplate' => 'ccm_ps_security_firewall_template',
        'securitygroup' => 'ccm_ps_security_firewall',
    );
    public $_pageList = array(
        'total' => 0,
        'rows' => array()
    );

    private function _checkPopedom($param)
    {
        $popedom = parent::checkConsolePopedom($param);
        return $popedom;
    }


    private function _check_popedomlist($type)
    {
        $subject_array = ['firewall', 'firewalltemplate', 'securitygroup'];
        $check_vale = '';
        foreach ($subject_array as $key => $value) {
            if ($type == 'list') {
                $popedomName = $this->_popedomName[$value];
            }
            if (!empty($popedomName)) {
                $check = $this->_checkPopedom($popedomName);
                if ($check) {
                    $check_vale = $value;
                    break;
                }
            }
        }
        return $check_vale;
    }

    public function index()
    {
        if (!empty($this->_popedomName['securitygroup'])) {
            $checkPopedomlist = $this->_checkPopedom($this->_popedomName['securitygroup']);
            if (!$checkPopedomlist) {
                return $this->redirect('/console/');
            }
        }
        $this->autoRender = false;

        $agent = TableRegistry::get('Agent');
        $agents = $agent->find('all')->toArray();
        $this->set('agent', $agents);

        try {

            $popedomname = $this->request->session()->read('Auth.User.popedomname') ? $this->request->session()->read('Auth.User.popedomname') : 0;
            $this->set('popedomname', $popedomname);
            $account_table = TableRegistry::get('Accounts');
            $user = $account_table->find()->select('department_id')->where(array('id' => $this->request->session()->read('Auth.User.id')))->first();
            $deparments = TableRegistry::get('Departments');
            $this->set('_default', $deparments->get($user["department_id"]));
            $table = $deparments->find('all');
            $this->set('_deparments', $table);
            $this->render('index');
        } catch (MissingTemplateException $e) {
            if (Configure::read('debug')) {
                throw $e;
            }
            throw new NotFoundException();
        }
    }

    public function groupList()
    {

        $basic_table = TableRegistry::get('InstanceBasic');
        $request = $this->request->query;
        $where['type'] = 'securityGroup';

        //租户
        if (isset($request['department_id']) && !empty($request['department_id'])) {
            $where['department_id'] = $request['department_id'];
        }
        //地域
        if (isset($request['class_code2']) && !empty($request['class_code2'])) {
            $where['location_code'] = $request['class_code2'];
        } else {
            //厂商
            if (isset($request['class_code']) && !empty($request['class_code'])) {
                $location_code = $request['class_code'];
                $where['location_code like'] = "$location_code%";
            }
        }
        //搜索
        if (isset($request['search']) && !empty($request['search'])) {
            $search = $request['search'];
            $where['or']['name like'] = "%$search%";
            $where['or']['code like'] = "%$search%";
        }

        $query = $basic_table->find()->contain(['SecurityGroupExtends'])->where($where);
        $this->_pageList['total'] = $query->count();
        $this->_pageList['rows'] = $query->offset($request['offset'])->limit($request['limit'])->map(function ($row) {
            //实例数
            $row['entry_count'] = 2;
            //vpc名
            $row['vpcName'] = TableRegistry::get('InstanceBasic')->find()->select(['name'])->where(array('code' => $row['vpc']))->first()['name'];
            return $row;
        });
        echo json_encode($this->_pageList);
        exit;
    }

//新建安全组
    public function addGroup()
    {
        if (!empty($this->_popedomName['securitygroup'])) {
            $checkPopedomlist = $this->_checkPopedom($this->_popedomName['securitygroup']);
            if (!$checkPopedomlist) {
                return $this->redirect('/console/');
            }
        }
        $this->autoRender = false;
        try {

            $goods_fixed = parent::readGoodsList('securityGroup');
            $goods_table = TableRegistry::get('Goods');
            $Systemsetting_table = TableRegistry::get('Systemsetting');
            $goods = $goods_table->find()
                ->where([
                    'fixed' => $goods_fixed
                ])
                ->first();
            if (!empty($goods)) {
                $this->set('goods_id', $goods->id);
//                $firewall_imageCode = $Systemsetting_table->find()->select(['para_value'])->where(['para_code'=>'firewall_imageCode'])->first()->para_value;
//                $this->set('imageCode',$firewall_imageCode);
//                $firewall_instanceTypeCode = $Systemsetting_table->find()->select(['para_value'])->where(['para_code'=>'firewall_instanceTypeCode'])->first()->para_value;
//                $this->set('instanceTypeCode',$firewall_instanceTypeCode);
            } else {
                $this->set('goods_id', 0);
            }
            $popedomname = $this->request->session()->read('Auth.User.popedomname') ? $this->request->session()->read('Auth.User.popedomname') : 0;
            $this->set('popedomname', $popedomname);
            $this->render('addgroup');
        } catch (MissingTemplateException $e) {
            if (Configure::read('debug')) {
                throw $e;
            }
            throw new NotFoundException();
        }
    }

    //修改安全组
    public function modifyGroup()
    {
        $request = $this->request->data;
        $basic_table = TableRegistry::get('InstanceBasic');
        if (!isset($request['id']) || empty($request['id']) || $basic_table->find()->where(array('id' => $request['id'], 'type' => 'securityGroup'))->count() == 0 || !isset($request['name']) || empty($request['name'])) {
            echo json_encode(array('code' => 2, 'msg' => '数据错误'));
            exit;
        }
        //是否修改
        if ($basic_table->find()->where(array('type' => 'securityGroup', 'name' => $request['name'], 'id' => $request['id']))->count() > 0) {
            echo json_encode(array('code' => 3, 'msg' => '未进行修改'));
            exit;
        }
        //组名是否存在
        if ($basic_table->find()->where(array('type' => 'securityGroup', 'name' => $request['name'], 'id <>' => $request['id']))->count() > 0) {
            echo json_encode(array('code' => 4, 'msg' => '安全组名已存在'));
            exit;
        }
        $res = $basic_table->updateAll(array('name' => $request['name']), array('id' => $request['id']));
        if ($res) {
            echo json_encode(array('code' => 0, 'msg' => '修改成功'));
            exit;
        } else {
            echo json_encode(array('code' => 1, 'msg' => '修改失败'));
            exit;
        }
    }

    //删除安全组
    public function delGroup()
    {
        $request = $this->request->data;
        if (!isset($request['ids']) || empty($request['ids'])) {
            echo json_encode(array('code' => 2, 'msg' => '请选择要删除的安全组'));
            exit;
        }
        $count = 0;
        $info = '';
        foreach (explode(',', trim($request['ids'], ',')) as $key => $value) {
            $url = Configure::read('URL');
            $array = array(
                'method' => 'security_group_del',
                'uid' => $this->request->session()->read('Auth.User.id'),
                'basicId' => $value
            );

            $http = new Client();
            $obj_response = $http->post($url, json_encode($array), array('type' => 'json'));
            $data_response = json_decode($obj_response->body, true);

            if ($data_response['code'] == 0) {
                $count += 1;
            } else {
                $info .= $data_response['Message'] . '<br>';
            }
        }
        if ($count > 0) {
            echo json_encode(array('code' => 0, 'msg' => '删除成功'));
            exit;
        } else {
            echo json_encode(array('code' => 1, 'msg' => '删除失败', 'info' => $info));
            exit;
        }

    }

    //安全组规则管理页面
    public function securityGroupRule()
    {
        $this->securityGroupEntry();

    }


    //规则列表
    public function ruleLists()
    {
        $request = $this->request->query;
        if (!isset($request['id']) || empty($request['id']) || !isset($request['d']) || empty($request['d'])) {
            $this->render('/Console/SecurityGroup/index');
        }
        $where = array();
        $where['basic_id'] = $request['id'];
        $d = $request['d'];
        if ($d == '1') {
            $where['direction'] = 'ingress';
        } else {
            $where['direction'] = 'egress';
        }

        $rule_table = TableRegistry::get('SecurityGroupRules');
        //搜索
        if (isset($request['search']) && !empty($request['search'])) {
            $search = $request['search'];
            $where['name like'] = "%$search%";
        }


        $query = $rule_table->find()->where($where);
        $this->_pageList['total'] = $query->count();
        $this->_pageList['rows'] = $query->offset($request['offset'])->limit($request['limit']);
        echo json_encode($this->_pageList);
        exit;
    }

    //新建规则
    public function addRule()
    {
        $request = $this->request->data;


        try {
            if (!isset($request['id']) || empty($request['id'])) {
                echo json_encode(array('code' => 2, 'msg' => '参数错误'));
                exit;
            }
            $securityGroupCode=TableRegistry::get('InstanceBasic')->find()->select(['code'])->where(array('id'=>$request['id']))->first()['code'];
            $array=array(
                'method' => 'security_group_rule_add',
                'uid' => $this->request->session()->read('Auth.User.id'),
                'securityGroupCode'=>$securityGroupCode,
                'basicId'=>$request['id'],
                'direction'=>$request['direction'],
                'portRange'=>$request['startPort'].'/'.$request['stopPort'],
                'actionType'=>$request['action-type'],
                'action'=>$request['action'],
                'name'=>$request['rule_name']
            );
            if($request['action-type']=='securitygroup'){
                $array['actionObject']=$securityGroupCode;
            }else{
                $array['actionObject']=$request['source_ip'];
            }

            $url = Configure::read('URL');
            $http = new Client();
            $obj_response = $http->post($url, json_encode($array), array('type' => 'json'));
            $data_response = json_decode($obj_response->body, true);
            if ($data_response['code'] == 0) {
                echo json_encode(array('code' => 0, 'msg' => '新建规则成功'));
                exit;
            } else {
                echo json_encode(array('code' => 1, 'msg' => $data_response['Message']));
                exit;
            }
        }catch(\Exception $e){
            echo json_encode(array('code'=>$e->getCode(),'msg'=>$e->getMessage()));exit;
        }
    }

    //规则是否存在
    public function isRepeatRule()
    {
        $rule_table = TableRegistry::get('SecurityGroupRules');
        $request = $this->request->data;
        $where = array(
            'protocol' => $request['protocol'],
            'portRange' => $request['port'],
            'direction' => $request['direction'],
            'actionType' => $request['action_type']
        );
        if ($request['action_type'] == 'securitygroup') {
            $where['basic_id'] = $request['basic_id'];
        } else {
            $where['actionObject'] = $request['source_ip'];
        }
        $exist = $rule_table->find()->where($where)->count();
        if ($exist > 0) {
            echo json_encode(array('code' => 1, '已存在'));
            exit;
        } else {
            echo json_encode(array('code' => 0, '不存在'));
            exit;
        }
    }

    //修改规则
    public function editRule()
    {
        $request = $this->request->data;
        if (!isset($request['id']) || empty($request['id'])) {
            echo json_encode(array('code' => 2, 'msg' => '请选择要修改的规则'));
            exit;
        }
        $rule_table = TableRegistry::get('SecurityGroupRules');
        if (!isset($request['rule_name']) || empty($request['rule_name'])) {
            echo json_encode(array('code' => 2, 'msg' => '请输入要修改的规则名'));
            exit;
        }
        $request['rule_name'] = trim($request['rule_name']);
        if ($rule_table->find()->where(array('name' => $request['rule_name'], 'id <>' => $request['id']))->count() > 0) {
            echo json_encode(array('code' => 3, 'msg' => '该规则名已存在'));
            exit;
        }
        if ($rule_table->find()->where(array('name' => $request['rule_name'], 'id' => $request['id']))->count() > 0) {
            echo json_encode(array('code' => 4, 'msg' => '未进行修改'));
            exit;
        }
        $code = 0;
        try {
            $res = $rule_table->updateAll(array('name' => $request['rule_name']), array('id' => $request['id']));
            if ($res) {
                $code = 0;
                $msg = '修改安全组规则成功';
            } else {
                $code = 1;
                $msg = '修改安全组规则失败';
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
        }

        echo json_encode(array('code' => $code, 'msg' => $msg));
        exit;
    }

    //删除规则
    public function delRules()
    {
        $request = $this->request->data;
        //多选
        if ($request['isEach']) {
            $count = 0;
            $info = '';
            $ids = array();
            if ((!isset($request['table_1']) || empty($request['table_1'])) && (!isset($request['table_2']) || empty($request['table_2']))) {
                echo json_encode(array('code' => 2, 'msg' => '请选择要删除的规则'));
                exit;
            }
            if (!empty($request['table_1'])) {
                foreach ($request['table_1'] as $key => $value) {
                    $ids[] = $value['id'];
                }
            }
            if (!empty($request['table_2'])) {
                foreach ($request['table_2'] as $key => $value) {
                    $ids[] = $value['id'];
                }
            }
            foreach ($ids as $key => $value) {
                $url = Configure::read('URL');
                $array = array(
                    'method' => 'security_group_rule_del',
                    'uid' => $this->request->session()->read('Auth.User.id'),
                    'id' => $value
                );
                $http = new Client();
                $obj_response = $http->post($url, json_encode($array), array('type' => 'json'));
                $data_response = json_decode($obj_response->body, true);
                if ($data_response['code'] == 0) {
                    $count += 1;
                } else {
                    $info .= $data_response['Message'] . '<br>';
                }
            }
            if ($count > 0) {
                echo json_encode(array('code' => 0, 'msg' => '删除规则成功'));
                exit;
            } else {
                echo json_encode(array('code' => 1, 'msg' => '删除规则失败', 'info' => $info));
                exit;
            }

        } else {
            //单选
            if (!isset($request['id']) || empty($request['id'])) {
                echo json_encode(array('code' => 2, 'msg' => '请选择要删除的规则'));
                exit;
            }
            $id = $request['id'];
            $url = Configure::read('URL');
            $array = array(
                'method' => 'security_group_rule_del',
                'uid' => $this->request->session()->read('Auth.User.id'),
                'rule_id' => $id
            );
            $http = new Client();
            $obj_response = $http->post($url, json_encode($array), array('type' => 'json'));
            $data_response = json_decode($obj_response->body, true);
            if ($data_response['code'] == 0) {
                echo json_encode(array('code' => 0, 'msg' => '删除规则成功'));
                exit;
            } else {
                echo json_encode(array('code' => 1, 'msg' => $data_response['Message']));
                exit;
            }
        }
    }


    //实例管理页面
    public function securityGroupEntry()
    {
        $request = $this->request->query;
        if (!isset($request['id']) || empty($request['id'])) {
            $basic_id = 0;
            $this->render('/Console/SecurityGroup/index');
        } else {
            $basic_id = $request['id'];
        }
        $this->set('id', $basic_id);
        $basic_table = TableRegistry::get('InstanceBasic');
        $data = $basic_table->find()->contain(['SecurityGroupExtends'])->where(array('InstanceBasic.id' => $basic_id))->first();
        if (empty($data)) {
            $this->render('/Console/SecurityGroup/index');
        }
        $data['vpcName'] = $basic_table->find()->select(['name'])->where(array('code' => $data['vpc']))->first()['name'];
        $this->set('data', $data);

    }

    //实例管理列表
    public function entryList()
    {
        $request = $this->request->query;
        if (!isset($request['id']) || empty($request['id'])) {
            $this->render('/Console/SecurityGroup/index');
        }
        $basic_table = TableRegistry::get('InstanceBasic');
        $network_table = TableRegistry::get('SecurityGroupRelationNetworks');
        $card_tabe = TableRegistry::get('HostsNetworkCard');
        //安全组code
        $group_code = $basic_table->find()->select(['code'])->where(array('id' => $request['id']))->first()['code'];
        //网卡code
        $network_arr = $network_table->find()->select(['networkCardCode'])->where(array('securityGroupCode' => $group_code))->toArray();
        $network_code = array();
        if (!empty($network_arr)) {
            foreach ($network_arr as $key => $value) {
                $network_code[] = $value['networkCardCode'];
            }
        }
//        $query=$basic_table->find()->contain(['HostsNetworkCard'=>function($q) use($network_code){
//            return $q->where(['network_code in'=>$network_code]);
//        }])->matching('HostsNetworkCard',function($q) use($network_code){
//            return $q->where(['network_code in'=>$network_code]);
//        });
        $query = $card_tabe->find()->contain(['InstanceBasic'])->where(array('network_code in' => $network_code));
        $this->_pageList['total'] = $query->count();
        $this->_pageList['rows'] = $query->offset($request['offset'])->limit($request['limit'])->map(function ($row) use ($group_code) {
            //公网IP
            $row['eip'] = TableRegistry::get('EipExtend')->find()->select(['ip'])->where(array('basic_id' => $row['instance_basic']['id']))->first()['ip'];
            //状态
            $row['status'] = TableRegistry::get('SecurityGroupRelationNetworks')->find()->select(['status'])->where(array('networkCardCode' => $row['network_code'], 'securityGroupCode' => $group_code))->first()['status'];
            return $row;
        });
        echo json_encode($this->_pageList);
        exit;

    }

    //添加实例页面
    public function addEntry()
    {
        $request = $this->request->query;
        if (!isset($request['basic_id']) || empty($request['basic_id'])) {
            $this->render('/Console/SecurityGroup/index');
        }
        $this->set('basic_id', $request['basic_id']);
        $basic_table = TableRegistry::get('InstanceBasic');
        //安全组信息
        $info = $basic_table->find()->where(array('id' => $request['basic_id']))->first();
        if (empty($info)) {
            $this->render('/Console/SecurityGroup/index');
        }
        $this->set('group_info', $info);
        //vpcName
        $this->set('vpcName', $basic_table->find()->select(['name'])->where(array('code' => $info['vpc']))->first()['name']);
        //子网
        $subnet = $basic_table->find()->where(array('type' => 'subnet', 'vpc' => $info['vpc']))->toArray();
        $this->set('subnet', $subnet);

    }

    //添加实例列表
    public function addEntryList()
    {
        $request = $this->request->query;
        $basic_table = TableRegistry::get('InstanceBasic');
        $network_table = TableRegistry::get('SecurityGroupRelationNetworks');
        $card_tabe = TableRegistry::get('HostsNetworkCard');
        //安全组id
        if (!isset($request['basic_id']) || empty($request['basic_id'])) {
            $this->render('/Console/SecurityGroup/index');
        }
        //安全组code
        $group_code = $basic_table->find()->select(['code'])->where(array('id' => $request['basic_id']))->first()['code'];
        $where = array();
        $matching = array();
        //已用网卡
        $network_arr = $network_table->find()->select(['networkCardCode'])->where(array('securityGroupCode <>' => '', 'networkCardCode <>' => ''))->toArray();
        $network_code = array();
        if (!empty($network_arr)) {
            foreach ($network_arr as $key => $value) {
                $network_code[] = $value['networkCardCode'];
            }

            if (!empty($network_code)) {
                if (isset($request['tab']) && $request['tab'] == 'beenused') {
                    $where['network_code in'] = $network_code;
                } else {
                    //可用网卡
                    $where['network_code not in'] = $network_code;
                }
            }
        }
        //搜索
        if (isset($request['search']) && !empty(trim($request['search']))) {
            $search = trim($request['search']);
            $matching['or']['name like'] = "%$search%";
            $matching['or']['code like'] = "%$search%";
        }
        //子网
        if (isset($request['subnet']) && !empty($request['subnet'])) {
            $where['subnet_code'] = $request['subnet'];
        }

        //类型
        if (isset($request['type']) && !empty($request['type'])) {
            $matching['type'] = $request['type'];
        } else {
            $matching['type in'] = array('hosts', 'desktop');
        }
        //安全组所在vpc
        $matching['vpc'] = $basic_table->find()->select(['vpc'])->where(array('id' => $request['basic_id']))->first()['vpc'];

        $query = $card_tabe->find()->contain(['InstanceBasic' => function ($q) use ($matching) {
            return $q->where($matching);
        }])->where($where)->matching('InstanceBasic', function ($q) use ($matching) {
            return $q->where($matching);
        });
        $this->_pageList['total'] = $query->count();
        $this->_pageList['rows'] = $query->offset($request['offset'])->limit($request['limit'])->map(function ($row) use ($group_code) {
            //公网IP
            $row['eip'] = TableRegistry::get('EipExtend')->find()->select(['ip'])->where(array('basic_id' => $row['instance_basic']['id']))->first()['ip'];
            //状态
            $data = TableRegistry::get('SecurityGroupRelationNetworks')->find()->select(['status', 'securityGroupCode'])->where(array('networkCardCode' => $row['network_code']))->first();
            $row['status'] = $data['status'];
            //已关联的安全组
            $row['bind_group'] = TableRegistry::get('InstanceBasic')->find()->select(['name'])->where(array('code' => $data['securityGroupCode']))->first()['name'];
            //安全组code
            $row['securityGroupCode']=$data['securityGroupCode'];
            return $row;
        });
        echo json_encode($this->_pageList);
        exit;

    }

    //移出实例
    public function removeEntry()
    {
        $request = $this->request->data;
        if (!isset($request['rows']) || empty($request['rows'])) {
            echo json_encode(array('code' => 2, 'msg' => '请选择要移出安全组的实例'));
            exit;
        }
        //解绑安全组(不一定当前安全组)
        if(isset($request['removeType'])&&$request['removeType']=='otherGroup'){
            $other=true;
        }else{
            //移出实例(当前安全组下)
            $other=false;
            if (!isset($request['basic_id']) || empty($request['basic_id'])) {
                echo json_encode(array('code' => 3, 'msg' => '数据错误'));
                exit;
            }
        }

        if(!$other){
            $securityGroupCode=TableRegistry::get('InstanceBasic')->find()->select(['code'])->where(array('id'=>$request['basic_id']))->first()['code'];
        }
        $count = 0;
        $info = '';
        foreach ($request['rows'] as $kye => $value) {
            if($other){
                $securityGroupCode=$value['securityGroupCode'];
            }
            $array = array(
                'method' => 'security_group_dissociate',
                'uid' => $this->request->session()->read('Auth.User.id'),
                'networkCardCode' => $value['network_code'],
                'securityGroupCode' => $securityGroupCode
            );
            $url = Configure::read('URL');
            $http = new Client();
            $obj_response = $http->post($url, json_encode($array), array('type' => 'json'));
            $data_response = json_decode($obj_response->body, true);

            if ($data_response['code'] == 0) {
                $count += 1;
            } else {
                $info .= $data_response['Message'] . '<br>';
            }
        }
        if ($count > 0) {
            echo json_encode(array('code' => 0, 'msg' => '成功移出' . $count . '个实例'));
            exit;
        } else {
            echo json_encode(array('code' => 1, 'msg' => '移出实例失败', 'info' => $info));
            exit;
        }

    }

    //关联安全组
    public function connectEntry()
    {
        $request = $this->request->data;
        if (!isset($request['group_code']) || empty($request['group_code'])) {
            echo json_encode(array('code' => 2, 'msg' => '数据错误,请刷新页面'));
            exit;
        }
        if (!isset($request['add_codes']) || empty(trim($request['add_codes'], ','))) {
            echo json_encode(array('code' => 3, 'msg' => '请选择要关联的实例'));
            exit;
        }
        $count = 0;
        $info = '';
        foreach (explode(',', trim($request['add_codes'], ',')) as $key => $value) {
            $array = array(
                'method' => 'security_group_associate',
                'uid' => $this->request->session()->read('Auth.User.id'),
                'securityGroupCode' => $request['group_code'],
                'networkCardCode' => $value
            );
            $url = Configure::read('URL');
            $http = new Client();
            $obj_response = $http->post($url, json_encode($array), array('type' => 'json'));
            $data_response = json_decode($obj_response->body, true);

            if ($data_response['code'] == 0) {
                $count += 1;
            } else {
                $info .= $data_response['Message'] . '<br>';
            }
        }
        if ($count > 0) {
            echo json_encode(array('code' => 0, 'msg' => '成功关联' . $count . '个实例'));
            exit;
        } else {
            echo json_encode(array('code' => 1, 'msg' => '关联实例失败', 'info' => $info));
            exit;
        }

    }

}