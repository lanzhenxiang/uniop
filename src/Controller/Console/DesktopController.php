<?php
/**
 * 云桌面
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
use Cake\Event\Event;
use Requests as Requests;

class DesktopController extends ConsoleController
{
    public function initialize()
    {
        parent::initialize();
        parent::left('desktop');//树形图导航

    }
    protected $_popedomName = array(
        'desktop' => 'ccm_ps_desktop',
    );
    protected $_addPopedomName = array(
        'desktop' => 'ccf_desktop_new',
    );

    protected function _checkPopedom($param)
    {
        $popedom = parent::checkConsolePopedom($param);
        return $popedom;
    }

    public $_pageList = array(
        'total' => 0,
        'rows' => array()
    );

    public function group(){
        $departmentTable = TableRegistry::get('Departments');
        //默认选择当前租户
        $department_id  = $this->request->session()->read('Auth.User.department_id');
        $department     = $departmentTable->get($department_id);
        //获取全部租户信息
        $departments    = $departmentTable->find()->select(['id', 'name'])->toArray();
//当前租户
        $this_department=$departmentTable->find()->select(['id','name'])->where(array('id'=>$this->request->session()->read('Auth.User.department_id')))->first();
        $this->set('this_depart',$this_department);

        $this->set(compact(['departments','department']));
    }

    public function groupData(){
        $this->autoRender=false;

        $gTable = TableRegistry::get('softwareList');
        $where = [];
        $where['department_id'] = isset($_GET['department_id'])?$_GET['department_id']:$this->request->session()->read('Auth.User.department_id');
        if($where['department_id'] == 'all'){
            unset($where['department_id']);
        }

        if(isset($_GET['name']) && $_GET['name']!=''){
            $where['software_name like'] ='%'.$_GET['name'].'%';
        }
        $pageNumber = (intval(@$_GET['pageNumber']) >0)?intval(@$_GET['pageNumber']):1;
        $pageSize = (intval(@$_GET['pageSize']) >0)?intval(@$_GET['pageSize']):5;
        $lists = $gTable->find()->where($where)->order('sort_order asc')->page($pageNumber,$pageSize);

        $json = array(
            'total'=>$gTable->find()->where($where)->count(),
            'rows'=>$lists,
        );
        echo json_encode($json);
    }

    public function groupDel(){
        $this->autoRender = false;
        $gTable = TableRegistry::get('softwareList');
        $gTable->deleteAll(array('id IN'=>explode(',',$_POST['ids'])));
        exit('ok');
    }

    public function groupAdd(){
        $gTable = TableRegistry::get('softwareList');

        if(isset($_GET['department_id'])&&!empty($_GET['department_id'])){
            $department_id=$_GET['department_id'];
        }else{
            $department_id=$this->request->session()->read('Auth.User.department_id');
        }
        $this->set('department_id',$department_id);
        if(isset($_GET['id'])){
            $group  = $gTable->find()->where(array('id'=>$_GET['id']))->first();
            $this->set('group',$group);
        }

        if($_POST){
            if(!isset($group)){
                $group = $gTable->newEntity();
            }
            if(isset($_POST['department_id'])&&!empty($_POST['department_id'])){
                $department_id=$_POST['department_id'];
            }else{
                $department_id=$this->request->session()->read('Auth.User.department_id');
            }
            $group = $gTable->patchEntity($group,array(
                'software_name'=>$_POST['software_name'],
                'sort_order'=>$_POST['sort_order'],
                'note'=>$_POST['note'],
                'software_code'=>'group_'.uniqid(),
                'department_id'=>$department_id
            ));

            $gTable->save($group);exit('ok');
        }
    }


    public function groupPolicy(){
        $gpTable = TableRegistry::get('softwareListPolicy');
        $dTable = TableRegistry::get('softwaresDesktop');
        $count = $dTable->find()->where(['software_id'=>intval($_GET['id'])])->count();
        $this->set('count',$count);
        $groupPolicy = $gpTable->find()->where(array('gid'=>intval($_GET['id'])))->first();
        if(isset($groupPolicy->id)){
            $this->set('groupPolicy',$groupPolicy);
        }
        if($_POST){
            if(!isset($groupPolicy->id)){
                $groupPolicy = $gpTable->newEntity();
            }
            $groupPolicy = $gpTable->patchEntity($groupPolicy,array(
                'gid'=>$_GET['id'],
                'name'=>$_POST['name'],
                'status'=>$_POST['status'],
                'min'=>$_POST['min'],
                'time'=>0,
                'priority'=>$_POST['priority'],

            ));
            $gpTable->save($groupPolicy);exit('ok');

        }
    }


    public function groupJoin(){

    }

    /**
     * 网络实例显示
     *
     * @param string $subject
     *            主题
     * @param string $category
     *            分类
     * @param number $tab
     *            标签
     * @throws MissingTemplateException
     * @throws NotFoundException
     * @return Ambigous <void, \Cake\Network\Response>
     */
    public function lists($subject = 'desktop')
    {
        $this->set('active_action_list',$subject);
        if (!empty($this->_popedomName[$subject])) {
            $checkPopedomlist = $this->_checkPopedom($this->_popedomName[$subject]);
            if (!$checkPopedomlist) {
                return $this->redirect('/console/');
            }
        }

        $this->autoRender = false;
        $agent = TableRegistry::get('Agent');
        $instance_basic = TableRegistry::get('InstanceBasic');
        $agents = $agent->find('all')->toArray();
        $this->set('agent', $agents);

        try {
            $func_name = '_get_vars_' . $subject;
            //判断是否存在函数
            if (method_exists($this, $func_name)) {
                $this->set('_view_vars', call_user_func_array([$this, $func_name], [
                    'options' => [
                        'page' => 1,
                        'limit' => 10
                    ],
                ]));
            }
            $popedomname = $this->request->session()->read('Auth.User.popedomname') ? $this->request->session()->read('Auth.User.popedomname') : 0;
            $this->set('popedomname', $popedomname);
            $account_table = TableRegistry::get('Accounts');
            // $user = $account_table->find()->select('department_id')->where(array('id' => $this->request->session()->read('Auth.User.id')))->first();
            $deparments = TableRegistry::get('Departments');
            $department_id = $this->getOwnByDepartmentId();
            $this->set('_default', $deparments->get($department_id));
            $table = $deparments->find('all');
            $this->set('_deparments', $table);
            $where = [
                'InstanceBasic.type' => 'desktop',
                'isdelete' => '0',
                'HostExtend.name <>' => ""
            ];
            $where['department_id'] = $this->request->session()->read('Auth.User.department_id');

            $host_data = $instance_basic->find()->contain(['HostExtend'])->where($where)->toArray();

            $hostName = array();
            foreach ($host_data as $v) {
                $hostName[] = $v['host_extend']['name'];
            }
            $topics = implode(',', array_unique($hostName));

            $tempUser = "xdesktop" . $this->request->session()->read('Auth.User.id') . time();

            if (is_string(Configure::read('NotifyUrl'))) {
                $response_notify_obj = @Requests::post(Configure::read('NotifyUrl') . '/subscribe', [], [
                    'sign' => md5($topics . Configure::read('NotifyKey') . $tempUser),
                    'uid' => $tempUser,
                    'topics' => $topics
                ]);
            } elseif (is_array(Configure::read('NotifyUrl'))) {
                $urls = Configure::read('NotifyUrl');
                foreach ($urls as $url) {
                    try {
                        $response_notify_obj = @Requests::post($url . '/subscribe', [], [
                            'sign' => md5($topics . Configure::read('NotifyKey') . $tempUser),
                            'uid' => $tempUser,
                            'topics' => $topics
                        ]);
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }

            $this->set('tempUser', $tempUser);
            $this->render('lists/' . $subject);

        } catch (MissingTemplateException $e) {
            if (Configure::read('debug')) {
                throw $e;
            }
            throw new NotFoundException();
        }
    }

    public function add($subject = 'desktop')
    {
        if (!empty($this->_addPopedomName[$subject])) {
            $checkPopedomlist = $this->_checkPopedom($this->_addPopedomName[$subject]);
            if (!$checkPopedomlist) {
                return $this->redirect('/console/');
            }
        }

        $this->autoRender = false;
        try {
            if ($subject == 'desktop') {
                $this->set('goods_id', 8);
                $this->set('_number', parent::readCookieByNumber());
            } elseif ($subject == 'init') {
                $this->set('goods_id', 1);
                $this->set('_number', parent::readCookieByNumber());
            }
            $popedomname = $this->request->session()->read('Auth.User.popedomname') ? $this->request->session()->read('Auth.User.popedomname') : 0;
            $this->set('popedomname', $popedomname);
            $this->render('add/' . $subject);
        } catch (MissingTemplateException $e) {
            if (Configure::read('debug')) {
                throw $e;
            }
            throw new NotFoundException();
        }
    }
//优先级
    public function priorityList($page = 1)
    {
        $checkPopedomlist = $this->_checkPopedom('ccm_desktop_priority');
        if (!$checkPopedomlist) {
            return $this->redirect('/console/');
        }
        $departments = TableRegistry::get('Departments');
        $softwarelist = TableRegistry::get('SoftwareList');
        $request = $this->request->query;
        //租户
        if (in_array('ccf_all_select_department', $this->request->session()->read('Auth.User.popedomname'))) {
            $department_data = $departments->find()->select(['id', 'name'])->toArray();
        }else{
            $department_data=$departments->find()->select(['id','name'])->where(array('id'=>$this->request->session()->read('Auth.User.department_id')))->toArray();
        }
        $this->set('department', $department_data);
        //桌面分组
        if (in_array('ccf_all_select_department', $this->request->session()->read('Auth.User.popedomname'))) {
            $group_data = $softwarelist->find()->select(['id', 'software_name'])->toArray();
        }else{
            $group_data=$softwarelist->find()->select(['id','software_name'])->where(array('department_id'=>$this->request->session()->read('Auth.User.department_id')))->toArray();
        }
        $this->set('group', $group_data);
        //当前租户
        $this_depart=$this->request->session()->read('Auth.User.department_id');
        $this->set('this_depart',$this_depart);
        //修改优先级的桌面列表
        $limit = 20;

        if ($page > 0) {
            $offset = $page - 1;
        } else {
            $offset = 0;
        }
        $instance_basic = TableRegistry::get('InstanceBasic');
        $where = array(
            'InstanceBasic.type' => 'desktop',
            'isdelete' => '0',
            'code <>' => ''
        );
        //搜索
        if (isset($request['search']) && !empty(trim($request['search']))) {
            $search = $request['search'];
            $where['or']=array(
                'InstanceBasic.name like'=>"%$search%",
                'InstanceBasic.code like'=>"%$search%"
            );
        }
        //选择租户
        if (isset($request['department_id']) && $request['department_id'] != 0) {
            $where['InstanceBasic.department_id'] = $request['department_id'];
        }else{
            $where['InstanceBasic.department_id'] = $this->request->session()->read('Auth.User.department_id');
        }
        //选择分组
        $ids = array();
        if (isset($request['group']) && $request['group'] != 0) {
            $softwares_desktop = TableRegistry::get('SoftwaresDesktop');
            $group_ids = $softwares_desktop->find()->select(['host_id'])->where(array('software_id' => $request['group']))->toArray();
            if (!empty($group_ids)) {
                foreach ($group_ids as $key => $value) {
                    $ids[] = $value['host_id'];
                }
            }else{
                //错误条件
                $where['InstanceBasic.id']=0.5;
            }
        }
        //选择计费方式
        $charge_ids = array();
        if (isset($request['charge_mode'])&&!empty($request['charge_mode'])) {
            $instance_charge = TableRegistry::get('InstanceCharge');
            $basic_ids = $instance_charge->find()->select(['basic_id'])->where(array('charge_mode' => $request['charge_mode']))->toArray();
            if (!empty($basic_ids)) {
                foreach ($basic_ids as $key => $value) {
                    $charge_ids[] = $value['basic_id'];
                }
            }else{
                $where['InstanceBasic.id']=0.5;
            }
        }
        if(!empty($ids)){
            if(!empty($charge_ids)){
                $res_ids=array_intersect($ids,$charge_ids);
                $where['InstanceBasic.id in'] = $res_ids;
            }else{
                $res_ids=$ids;
                $where['InstanceBasic.id in'] = $res_ids;
            }
        }else{
            if(!empty($charge_ids)){
                $res_ids=$charge_ids;
                $where['InstanceBasic.id in'] = $res_ids;
            }
        }


        $desktop_data['total']=ceil($instance_basic->find()->contain(['InstanceCharge', 'Departments'])->where(array($where))->count()/$limit);
        $desktop_data['data']=$instance_basic->find()->contain(['InstanceCharge', 'Departments'])->where(array($where))->offset($offset)->limit($limit)->map(function ($row) {
            $software_list = TableRegistry::get('SoftwareList');
            $softwares_desktop = TableRegistry::get('SoftwaresDesktop');
            $software_id = $softwares_desktop->find()->select(['software_id'])->where(array('host_id' => $row['id']))->first()['software_id'];
            if (empty($software_id)) {
                $row['group'] = '-';
            } else {
                $row['group'] = $software_list->find()->select(['software_name'])->where(array('id' => $software_id))->first()['software_name'];
            }
            if ($row['instance_charge']['charge_mode'] == 'oneoff') {
                $row['charge_mode'] = '一次性计费';
            } elseif ($row['instance_charge']['charge_mode'] == 'duration') {
                $row['charge_mode'] = '按时长计费';
            } elseif ($row['instance_charge']['charge_mode'] == 'cycle') {
                $row['charge_mode'] = '固定循环周期计费';
            } elseif ($row['instance_charge']['charge_mode'] == 'permanent') {
                $row['charge_mode'] = '永久许可';
            }
            if ($row['instance_charge']['interval'] == 'S') {
                $row['charge_mode'] .= '(秒)';
            } elseif ($row['instance_charge']['interval'] == 'I') {
                $row['charge_mode'] .= '(分钟)';
            } elseif ($row['instance_charge']['interval'] == 'H') {
                $row['charge_mode'] .= '(小时)';
            } elseif ($row['instance_charge']['interval'] == 'D') {
                $row['charge_mode'] .= '(天)';
            } elseif ($row['instance_charge']['interval'] == 'M') {
                $row['charge_mode'] .= '(月)';
            } elseif ($row['instance_charge']['interval'] == 'Y') {
                $row['charge_mode'] .= '(年)';
            }


            return $row;
        })->toArray();

        $this->set('desktop_data',$desktop_data);
        $this->set('page', $page);

        return $this->render('lists/priorityList');
    }

    //修改优先级显示的列表
    public function getDesktop($page=1){
        $limit = 20;
        $request = $this->request->query;
        if ($page > 0) {
            $offset = $page - 1;
        } else {
            $offset = 0;
        }
        $offset = $offset * $limit;

        $instance_basic = TableRegistry::get('InstanceBasic');

        $where = array(
            'InstanceBasic.type' => 'desktop',
            'isdelete' => '0',
            'code <>' => ''
        );
        //搜索
        if (isset($request['search']) && !empty(trim($request['search']))) {
            $search = $request['search'];
            $where['or']=array(
                'InstanceBasic.name like'=>"%$search%",
                'InstanceBasic.code like'=>"%$search%"
            );
        }
        //选择租户
        if (isset($request['department_id']) && $request['department_id'] != 0) {
            $where['InstanceBasic.department_id'] = $request['department_id'];
        }else{
            $where['InstanceBasic.department_id'] =$this->request->session()->read('Auth.User.department_id');
        }
        //选择分组
        $ids = array();
        if (isset($request['group']) && $request['group'] != 0) {
            $softwares_desktop = TableRegistry::get('SoftwaresDesktop');
            $group_ids = $softwares_desktop->find()->select(['host_id'])->where(array('software_id' => $request['group']))->toArray();
            if (!empty($group_ids)) {
                foreach ($group_ids as $key => $value) {
                    $ids[] = $value['host_id'];
                }
            }else{
                //错误条件
                $where['InstanceBasic.id']=0.5;
            }
        }
        //选择计费方式
        $charge_ids = array();
        if (isset($request['charge_mode'])&&!empty($request['charge_mode'])) {
            $instance_charge = TableRegistry::get('InstanceCharge');
            $basic_ids = $instance_charge->find()->select(['basic_id'])->where(array('charge_mode' => $request['charge_mode']))->toArray();
            if (!empty($basic_ids)) {
                foreach ($basic_ids as $key => $value) {
                    $charge_ids[] = $value['basic_id'];
                }
            }else{
                $where['InstanceBasic.id']=0.5;
            }
        }
        if(!empty($ids)){
            if(!empty($charge_ids)){
                $res_ids=array_intersect($ids,$charge_ids);
                $where['InstanceBasic.id in'] = $res_ids;
            }else{
                $res_ids=$ids;
                $where['InstanceBasic.id in'] = $res_ids;
            }
        }else{
            if(!empty($charge_ids)){
                $res_ids=$charge_ids;
                $where['InstanceBasic.id in'] = $res_ids;
            }
        }



        $i = ceil($instance_basic->find()->contain(['InstanceCharge', 'Departments'])->where(array($where))->count()/$limit);
        $data['total'] = $i;
        $data['data'] =$instance_basic->find()->contain(['InstanceCharge', 'Departments'])->where(array($where))->offset($offset)->limit($limit)->map(function ($row) {
            $software_list = TableRegistry::get('SoftwareList');
            $softwares_desktop = TableRegistry::get('SoftwaresDesktop');
            $software_id = $softwares_desktop->find()->select(['software_id'])->where(array('host_id' => $row['id']))->first()['software_id'];
            if (empty($software_id)) {
                $row['group'] = '-';
            } else {
                $row['group'] = $software_list->find()->select(['software_name'])->where(array('id' => $software_id))->first()['software_name'];
            }
            if ($row['instance_charge']['charge_mode'] == 'oneoff') {
                $row['charge_mode'] = '一次性计费';
            } elseif ($row['instance_charge']['charge_mode'] == 'duration') {
                $row['charge_mode'] = '按时长计费';
            } elseif ($row['instance_charge']['charge_mode'] == 'cycle') {
                $row['charge_mode'] = '固定循环周期计费';
            } elseif ($row['instance_charge']['charge_mode'] == 'permanent') {
                $row['charge_mode'] = '永久许可';
            }
            if ($row['instance_charge']['interval'] == 'S') {
                $row['charge_mode'] .= '(秒)';
            } elseif ($row['instance_charge']['interval'] == 'I') {
                $row['charge_mode'] .= '(分钟)';
            } elseif ($row['instance_charge']['interval'] == 'H') {
                $row['charge_mode'] .= '(小时)';
            } elseif ($row['instance_charge']['interval'] == 'D') {
                $row['charge_mode'] .= '(天)';
            } elseif ($row['instance_charge']['interval'] == 'M') {
                $row['charge_mode'] .= '(月)';
            } elseif ($row['instance_charge']['interval'] == 'Y') {
                $row['charge_mode'] .= '(年)';
            }
            return $row;
        }) ;
        $data['page'] = $page;
        echo json_encode($data);exit;
    }
//选中的列表
    public function pLists()
    {
        $where = array();
        $instance_basic = TableRegistry::get('InstanceBasic');
        $request = $this->request->query;
        if(isset($request['select_id'])&&!empty($request['select_id'])){
            $ids=trim($request['select_id'],',');
        }else{
            $ids='';
        }
        $id_arr=explode(',',$ids);
        if(isset($request['department_id'])&&!empty($request['department_id'])){}

        $this->_pageList['total'] = $instance_basic->find()->select(['id','code','name'])->where(array('id in'=>$id_arr,'department_id'=>$this->request->session()->read('Auth.User.department_id')))->count();
        $this->_pageList['rows'] = $instance_basic->find()->select(['id','code','name'])->where(array('id in'=>$id_arr,'department_id'=>$this->request->session()->read('Auth.User.department_id')))->offset($request['offset'])->limit($request['limit']);
        echo json_encode($this->_pageList);
        exit();
    }
//修改优先级
    public function editPriority(){
        $checkPopedomlist = $this->_checkPopedom('ccm_desktop_priority');
        if (!$checkPopedomlist) {
            return $this->redirect('/console/');
        }
        $arr=array();
        $desk_id=isset($this->request->data['select_id'])?trim($this->request->data['select_id'],','):'';
        $priority=isset($this->request->data['priority_data'])?(string)$this->request->data['priority_data']:0;
        $instance_basic = TableRegistry::get('InstanceBasic');
        if(empty($desk_id)){
            echo json_encode(array('code'=>2,'msg'=>'未选择桌面'));exit;
        }
        $desk_arr=explode(',',$desk_id);
        foreach($desk_arr as $key => $value){
            $arr[]=array(
                'basic_id'=>$value,
                'priority_level'=>$priority
            );
        }
        $array=array('data'=>json_encode($arr));
        $res=$instance_basic->updateAll(array('priority'=>$priority),array('id in'=>$desk_arr));
        if($res){
            $http= new Client();
            $url = Configure::read('Api.vboss').'/Desktops/updateDesktop';
            $response=$http->post($url,json_encode($array),array('type'=>'json'));
            $response_data=json_decode($response->body,true);
            if($response_data['code']==0){
                echo json_encode(array('code'=>0,'msg'=>'修改优先级成功,已上传至vboss'));exit;
            }else{
                echo json_encode(array('code'=>0,'msg'=>'修改优先级成功,未上传至vboss'));exit;
            }

//           echo json_encode(array('code'=>0,'msg'=>'修改优先级成功'));exit;
        }else{
            echo json_encode(array('code'=>1,'msg'=>'修改优先级失败'));exit;
        }
    }

//桌面分组
    public function desktopGroup(){
        $gTable = TableRegistry::get('softwareList');
        if(isset($_GET['id'])){
            $groupInfo  = $gTable->find()->where(array('id'=>$_GET['id']))->first();
            $this->set('groupInfo',$groupInfo);
        }


        $checkPopedomlist = $this->_checkPopedom('ccm_desktop_group');
        if (!$checkPopedomlist) {
            return $this->redirect('/console/');
        }
        $departments = TableRegistry::get('Departments');
        $softwarelist = TableRegistry::get('SoftwareList');
        //租户
        $department_data = $departments->find()->select(['id', 'name'])->toArray();
        $this->set('department', $department_data);
        //桌面分组
        $where['department_id']=$this->request->session()->read('Auth.User.department_id');
        $group_data = $softwarelist->find()->select(['id', 'software_name'])->where($where)->toArray();
        $this->set('group', $group_data);
        //当前租户
        $this_department=$this->request->session()->read('Auth.User.department_id');
        $this->set('this_depart',$this_department);

        return $this->render('lists/desktopGroup');
    }
    //分组显示列表
    public function groupList(){
        $where = array();
        $instance_basic = TableRegistry::get('InstanceBasic');
        $request = $this->request->query;
        $where = array(
            'InstanceBasic.type' => 'desktop',
            'isdelete' => '0',
            'code <>' => ''
        );

        if(isset($_GET['id'])){
            if(isset($request['type'])&&$request['type']=='this'){
                $where['SoftwaresDesktop.id is not']=null;
                $where['SoftwaresDesktop.software_id']=$_GET['id'];
            }elseif(isset($request['type'])&&$request['type']=='other'){
                $where['SoftwaresDesktop.id is not']=null;
                $where['SoftwaresDesktop.software_id <>']=$_GET['id'];
            }else{
                $where['SoftwaresDesktop.id is']=null;
            }
        }else{
            //是否分组
            if(!isset($request['type'])||$request['type']=='had'){
                $where['SoftwaresDesktop.id is not']=null;
            }else{
                $where['SoftwaresDesktop.id is']=null;
            }
        }



        //搜索
        if (isset($request['search']) && !empty(trim($request['search']))) {
            $search = $request['search'];
            $where['or']=array(
                'InstanceBasic.name like'=>"%$search%",
                'InstanceBasic.code like'=>"%$search%"
            );
        }
        //选择租户
        if(isset($_GET['id'])){
               $software_list = TableRegistry::get('SoftwareList');
                $where['InstanceBasic.department_id'] =  $software_list->find()->select(['department_id'])->where(array('id' => $_GET['id']))->first()['department_id'];
        }else {
            $where['InstanceBasic.department_id'] = $this->request->session()->read('Auth.User.department_id');
        }
        $this->_pageList['total'] = $instance_basic->find()->contain(['SoftwaresDesktop'])->where(array($where))->count();
        $this->_pageList['rows'] = $instance_basic->find()->contain(['SoftwaresDesktop'])->where(array($where))->offset($request['offset'])->limit($request['limit'])->map(function ($row) {
            $software_list = TableRegistry::get('SoftwareList');
            $software_id = $row['softwares_desktop']['software_id'];
            if (empty($software_id)) {
                $row['group'] = '-';
            } else {
                $row['group'] = $software_list->find()->select(['software_name'])->where(array('id' => $software_id))->first()['software_name'];
            }

            return $row;
        });

        echo json_encode($this->_pageList);
        exit();
    }
    //根据租户修改分组
    public function getGroupBydepart(){
        $software_list = TableRegistry::get('SoftwareList');
        $id=isset($this->request->data['department_id'])?$this->request->data['department_id']:0;
        $data=$software_list->find()->where(array('department_id'=>$id))->toArray();
        echo json_encode($data);exit;
    }
//加入分组
    public function addGroup(){
        $checkPopedomlist = $this->_checkPopedom('ccm_desktop_group');
        if (!$checkPopedomlist) {
            return $this->redirect('/console/');
        }
        $softwares_desktop = TableRegistry::get('SoftwaresDesktop');
        $request=$this->request->data;
        //桌面id
        if(!isset($request['add_ids'])||empty($request['add_ids'])){
            echo json_encode(array('code'=>2,'msg'=>'未选择桌面'));exit;
        }
        $host_id_arr=explode(',',trim($request['add_ids'],','));
        //租户id
//        if(!isset($request['add_department'])||!in_array('cmop_global_sys_admin', $this->request->session()->read('Auth.User.popedomname'))||$request['add_department']==0){
//            $department_id=$this->request->session()->read('Auth.User.department_id');
//        }else{
//            $department_id=$request['add_department'];
//        }
        //分组id
        if(!isset($request['add_groups'])||empty($request['add_groups'])){
            echo json_encode(array('code'=>3,'msg'=>'未选择分组'));exit;
        }
        $software_id=$request['add_groups'];
        $count=0;
        //添加分组
        foreach($host_id_arr as $key => $value){
            $add_data=$softwares_desktop->newEntity();
            $add_data=$softwares_desktop->patchEntity($add_data,array(
                'software_id'=>$software_id,
                'host_id'=>$value
//                'department_id'=>$department_id
            ));
            $res=$softwares_desktop->save($add_data);
            if($res){
                $count+=1;
            }
        }
        if($count>0){
            echo json_encode(array('code'=>0,'msg'=>'成功添加'.$count.'个桌面到分组'));exit;
        }else{
            echo json_encode(array('code'=>1,'msg'=>'添加分组失败'));exit;
        }

    }
    //移出分组
    public function delGroup(){
        $checkPopedomlist = $this->_checkPopedom('ccm_desktop_group');
        if (!$checkPopedomlist) {
            return $this->redirect('/console/');
        }
        $rows=array();
        if(isset($this->request->data['rows'])&&!empty($this->request->data['rows'])){
            $rows=$this->request->data['rows'];
        }else{
            echo json_encode(array('code'=>2,'msg'=>'未选择桌面'));exit;
        }
        $ids=array();
        foreach($rows as $key => $value){
            $ids[]=$value['id'];
        }
        $softwares_desktop = TableRegistry::get('SoftwaresDesktop');
        $res=$softwares_desktop->deleteAll(array('host_id in'=>$ids));
        if($res){
            echo json_encode(array('code'=>0,'msg'=>'移出分组成功'));exit;
        }else{
            echo json_encode(array('code'=>1,'msg'=>'移出分组失败'));exit;
        }

    }

    /**
     * 计费管理页面渲染
     * @author lan
     */
    public function charge()
    {
        $this->dispatchEvent('Controller.isAuthorized',['ccm_desktop_charge_manage']);
        $this->autoRender = false;
        $departmentTable = TableRegistry::get('Departments');
        //默认选择当前租户
        $department_id  = $this->request->session()->read('Auth.User.department_id');
        $department     = $departmentTable->get($department_id);
        //获取全部租户信息
        $departments    = $departmentTable->find()->select(['id', 'name'])->toArray();

        $chargeType  = ['val'=>"",'name'=>'全部'];
        $chargeTypes = Configure::read('charge_interval');

        $this->set(compact(['departments','department','chargeTypes','chargeType']));
        $this->render('lists/charge');
    }

    /**
     * 桌面计费规则列表
     */
    public function chargeList()
    {
        $this->dispatchEvent('Controller.isAuthorized',['ccm_desktop_charge_manage']);
        $this->createView('json');
        $request_data = $this->request->query;

        $this->paginate['limit'] = $request_data['limit'];
        $this->paginate['page']  = $request_data['offset'] / $request_data['limit'] + 1;

        $instance_basic = TableRegistry::get('InstanceBasic');
        $where          = [
            'InstanceBasic.type' => 'desktop',
            'isdelete'           => '0',
        ];
        if (!empty($request_data['department_id'])) {
            $where['department_id'] = $request_data['department_id'];
        }
        if (isset($request_data['search'])) {
            if ($request_data['search'] != "") {
                $where['OR'] =[
                    ["InstanceBasic.name like"=>"%" . $request_data['search'] . "%"],
                    ["InstanceBasic.code like"=>"%" . $request_data['search'] . "%"]
                ];
            }
        }
        $chargeTypes    = array_keys(Configure::read('charge_interval'));
        $chargeType     = null;
        if(isset($request_data['charge_type']) && in_array($request_data['charge_type'], $chargeTypes)){
            $chargeType = $request_data['charge_type'];
        }

        $query = $instance_basic->find('all')
            ->contain(['InstanceCharge','departments'])
            ->where($where)
            ->matching('InstanceCharge',function($q) use($chargeType){
                if(isset($chargeType)){
                    return $q->where(['interval'=>$chargeType]);
                }
                return $q;
            });

        $total = $query->count();
        $rows = $this->paginate($query->order([
            'InstanceBasic.create_time' => 'DESC',
        ]));
        $this->set(compact('total','rows'));
        $this->set('_serialize', ['total','rows']);
    }

    //修改计费方式 
    public function editChargeMode()
    {
        $this->dispatchEvent('Controller.isAuthorized',['ccm_desktop_charge_manage']);
        $this->createView('json');
        $data = [];
        list($data['charge_mode'],$data['interval'])    = explode("|",$this->request->data['charge_mode']);
        $data['price']  = $data['interval'] == 'P' ? 0 : $this->request->data['price'];

        $instance_charge_table = TableRegistry::get('InstanceCharge');
        $basic_ids = explode(',',rtrim($this->request->data['basicIds'],','));
        $result = $instance_charge_table->updateAll($data,['basic_id in'=>$basic_ids]);

        if($result){
            $msg    = '修改计费模式成功';
            $code   = 0;
        }else{
            $msg    = '修改失败';
            $code   = -1;
        }
        $this->set(compact(['code','msg']));
        $this->set('_serialize',['code','msg']);
    }

}