<?php
/**
* 文件用途描述
*
* @file: AccountsController.php
* @date: 2016年3月18日 下午2:40:34
* @author: xingshanghe
* @email: xingshanghe@icloud.com
* @copyright poplus.com
*
*/

namespace App\Controller\Api;

use App\Controller\SobeyController;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Composer\Autoload\ClassLoader;
use Cake\Cache\Cache;
use \Requests as Requests;
use Cake\Datasource\ConnectionManager;


class AccountsController extends SobeyController
{
    private $_data = null;
    private $_error = null;
    private $_serialize = array('code','msg','data');
    private $_code = '0000';
    private $_msg = "";

    public function initialize() {

        $loader = new ClassLoader();
        $loader->add('Requests', ROOT.DS.'vendor/requests');
        $loader->register();
        Requests::register_autoloader();

        $this->_url = Configure::read('Api.cmop');
        //加载Cookie组件
        $this->loadComponent('Cookie',[
            'encryption' => false
        ]);
        //Rsa加密解密算法
        $this->loadComponent('Rsa');
        //Des加密解密算法
        $this->loadComponent('Des');

        $this->loadComponent('RequestHandler');

        //获取参数
        $this->_data = $this->_getData();
        //设置布局文件为json
        $this->viewClass = 'Json';
    }


    public function launch(){
        $code = $this->_code;
        $msg = $this->_getMsg($code);
        //数据校验
        $lack_fields = [];

        $_needed_fileds = [
            'name',//对应cmop中的loginname
            'connect_user',
            'connect_id'
        ];

        if($_needed_fileds){
            foreach ($_needed_fileds as $_key){
                if (!isset($this->_data[$_key])){
                    $lack_fields[] = $_key;
                }
            }
        }

        if (empty($lack_fields)){

            $name = $this->_data['name'];

            $_current_time = time();
            $host_extend_table = TableRegistry::get('HostExtend');

            $is_used = $host_extend_table->find()->where(['connect_id'=>$this->_data['connect_id'],'connect_status'=>'1'])->first();

            if (empty($is_used)){

                $desk = $this->_getDeskInfoByName($name);

                switch ($desk['connect_status']){
                    case 99:
                        //如果上次汇报时间大于300秒则认为上次启动失败
                        if ($_current_time-$desk['last_reporttime'] >30){
                            $desk->connect_status = 0;
                            $desk->connect_id = 0;
                            $desk->connect_user = '';
                            if($host_extend_table->save($desk)){
                                Log::error('Desktop Status is not automatically updated.:'.$desk);
                            }
                            $desk = $this->_getDeskInfoByName($name);
                        }else{
                            $code = '4001';
                            $msg = $this->_getMsg($code,$desk['connect_user']);
                            break;
                        }
                    case 0:
                        //正常启动云桌面
                        //尝试启动
                        $desk->connect_user = $this->_data['connect_user'];
                        $desk->connect_id = $this->_data['connect_id'];
                        $desk->connect_time = $_current_time;
                        $desk->connect_status = 99;

                        //启动中
                        if($host_extend_table->save($desk)){
                            //发送通知，可能会有异常
                            @$this->_notify([
                                'SendType'=>'websocket',
                                'MsgType'=>'info',
                                'Msg'=>'',
                                'Topic'=>$name,
                                'Data'=>[
                                    'username'=>$this->_data['connect_user'],
                                    'name'=>$name,
                                    'status'=>'99',
                                    'description'=>'启动中'
                                ]
                            ]);
                        }

                        //查询用户密码
                        $user_table = TableRegistry::get('Accounts');
                        $user = $user_table->get($this->_data['connect_id']);
                        @$this->_doDeskLogic($name,$user,$this->_data['connect_id']);
                        try {
                            $response =  Requests::post($this->_url.'/citrix/launch20160229',[],[
                                'loginname'=>$user->loginname,//cmop密码
                                'password'=>$user->password,//cmop账号
                                'name'=>$name,//cmop主机名
                            ],[
                                'verify'=>false,
                                //'timeout'=>2
                            ]);

                            $response = json_decode($response->body,true);

                            //启动成功
                            if ($response['code'] == '0000'){
                                $data = $response['data'];
                            }else{
                                //启动失败，通知消息服务。更改数据库中状态
                                $desk->connect_status = 0;
                                $desk->connect_id = 0;
                                $desk->connect_user = '';
                                $desk->connect_status = 0;

                                //启动中
                                if($host_extend_table->save($desk)){
                                    //发送通知，可能会有异常
                                    @$this->_notify([
                                        'SendType'=>'websocket',
                                        'MsgType'=>'info',
                                        'Msg'=>'',
                                        'Topic'=>$name,
                                        'Data'=>[
                                            'username'=>$this->_data['connect_user'],
                                            'name'=>$name,
                                            'status'=>'0',
                                            'description'=>'启动失败'
                                        ]
                                    ]);
                                }
                                $code = '4009';
                                $msg = $response['msg'];

                            }

                        } catch (\Exception $e) {
                            $this->set('_msg',$e->getMessage());
                        }

                        break;
                    case 1:
                        $code = '4002';
                        $msg = $this->_getMsg($code,$desk['connect_user']);
                        break;
                    default:
                        $msg = $this->_getMsg($code,$desk['connect_user']);
                        break;

                }
            }else{
                $code = '4003';
                $msg = $this->_getMsg($code);
            }

        }else{
            //数据校验，缺少字段
            $code = '3000';
            $msg = $this->_getMsg($code,implode(',', $lack_fields));
        }

        if (intval($code)){
            $log ="getToken方法调用:\n";
            $log.="传递参数:".json_encode($this->_data)."\n";
            $log.="错误信息:".$msg."\n";

            Log::error($log);
        }
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);

    }

    protected function _getNas($account_id){
        $nas_table = TableRegistry::get('UserNas');
        return $nas_table->find()->where(['user_id'=>$account_id])->first();
    }
    //for云工厂
    private function _getDeskInfoByName($name = null)
    {
        $host_extend_table = TableRegistry::get('HostExtend');
        return $host_extend_table->find()->select([
            'id','connect_status','connect_id','connect_user','connect_time','last_reporttime'
        ])->where([
            'name'=>$name
        ])->first();
    }
    //for云工厂
    private function _notify($data= []){
        //try {
        $_current_time = time();

        if (is_string(Configure::read('NotifyUrl'))){
            @Requests::post(Configure::read('NotifyUrl').'/send',[],[
                'time'=>$_current_time,
                'sign'=>md5($_current_time.Configure::read('NotifyKey')),
                'data'=>json_encode($data)
            ],[
                //'timeout'=>2
            ]);
        }elseif(is_array(Configure::read('NotifyUrl'))){
            $urls = Configure::read('NotifyUrl');

            foreach ($urls as $url){
                try {
                    @Requests::post($url.'/send',[],[
                        'time'=>$_current_time,
                        'sign'=>md5($_current_time.Configure::read('NotifyKey')),
                        'data'=>json_encode($data)
                    ],[
                        //'timeout'=>2
                    ]);
                } catch (\Exception $e) {
                    Log::error($e->getMessage().':'.$url);
                    continue;
                }

            }

        }
        //} catch (\Exception $e) {
        //Log::error("Some error occured when notify was sending:".$e->getMessage());
        //}
    }
    //for云工厂
    private function _doDeskLogic($name,$user,$account_id=0){

        $current_time = time();


        $basic_table = TableRegistry::get('InstanceBasic');
        $fimas_table = TableRegistry::get('FimasExtend');
        $fimas = array('volume1_id'=>'','disk1_flag'=>'','volume2_id'=>'','disk2_flag'=>'');

        $basic_table_id = $basic_table->find()->where(['type'=>'fimas','create_by'=>$this->request->session()->read('Auth.User.id')])->order(['id'=>'DESC'])->first();
        if(!empty($basic_table_id)){
            $fimas_entity = $fimas_table->find()->where(['basic_id'=>$basic_table_id->id])->first();
            if(!empty($fimas_entity)){
                $fimas['volume1_id'] = $fimas_entity->volume1_id;
                $fimas['disk1_flag'] = $fimas_entity->disk1_flag;
                $fimas['volume2_id'] = $fimas_entity->volume2_id;
                $fimas['disk2_flag'] = $fimas_entity->disk2_flag;
            }
        }

        //TODO 等待加载用户配置 @2015-11-23
        //有需要挂载的，通知程序挂载
        $nas_info = $this->_getNas();
        if($nas_info){
            @$this->_notify([
                'SendType'=>'websocket',
                'MsgType'=>'info',
                'Msg'=>'通知桌面挂载个人配置',
                'uid'=>'sobeyDesktop-'.$name,
                'Data'=>[
                    'ip'=>$nas_info['ip'],
                    'dir'=>$nas_info['dir'],
                    'user'=>$nas_info['user'],
                    'pass'=>$nas_info['pass'],
                    'path1'=>$nas_info['path1'],
                    'path2'=>$nas_info['path2'],
                    'method'=>'desktop_user_attach',
                    'username'=>$user->loginname,
                    'password'=>$user->password,
                    'volume1_id'=>$fimas['volume1_id'],
                    'disk1_flag'=>$fimas['disk1_flag'],
                    'volume2_id'=>$fimas['volume2_id'],
                    'disk2_flag'=>$fimas['disk2_flag'],
                ]
            ]);

        }
    }

    public function getSoftList(){
        $code = $this->_code;
        $msg = $this->_getMsg($code);
        //数据校验
        $lack_fields = [];

        $_needed_fileds = [
            'account_id',//对应cmop中的loginname
        ];

        if($_needed_fileds){
            foreach ($_needed_fileds as $_key){
                if (!isset($this->_data[$_key])){
                    $lack_fields[] = $_key;
                }
            }
        }

        if (empty($lack_fields)){
            //获取桌面软件列表
            $conn = ConnectionManager::get('default');
            $sql = "SELECT DISTINCT basic.`code` as code, basic.`name` as dispname,basic.vpc as vpc,desktop.`name` as hostname,slist.software_code,slist.software_name,basic.`status`,desktop.connect_status,desktop.`connect_user`,desktop.connect_time,desktop.last_reporttime"
                ." FROM cp_instance_basic basic,cp_host_extend desktop,cp_software_list slist,cp_softwares_desktop sdesktop,cp_roles_accounts role,cp_roles_software srole,cp_accounts accounts"
                ." where role.account_id= ".$this->_data['account_id']." and basic.id=desktop.basic_id and"
                ." desktop.id=sdesktop.host_id and sdesktop.software_id=slist.id "
                ." and role.role_id=srole.role_id and srole.software_id=slist.id "
                ." and accounts.id=role.account_id  and accounts.department_id=basic.department_id "
                ." group by basic.`code` order by slist.software_name ";
            $data = $conn->execute($sql)->fetchAll('assoc');
        }else{
            //数据校验，缺少字段
            $code = '3000';
            $msg = $this->_getMsg($code,implode(',', $lack_fields));
        }

        if (intval($code)){
            $log ="getToken方法调用:\n";
            $log.="传递参数:".json_encode($this->_data)."\n";
            $log.="错误信息:".$msg."\n";

            Log::error($log);
        }
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }

    public function getSoftCatList()
    {
        $code = $this->_code;
        $msg = $this->_getMsg($code);
        //数据校验
        $lack_fields = [];

        $_needed_fileds = [
            'account_id',//对应cmop中的loginname
        ];

        if($_needed_fileds){
            foreach ($_needed_fileds as $_key){
                if (!isset($this->_data[$_key])){
                    $lack_fields[] = $_key;
                }
            }
        }

        if (empty($lack_fields)){
            //获取桌面软件列表
            $conn = ConnectionManager::get('default');
            $sql = "SELECT DISTINCT(slist.software_name),slist.software_code,slist.icon_file "
                ." FROM cp_instance_basic basic,cp_host_extend desktop,cp_software_list slist,cp_softwares_desktop sdesktop,cp_roles_accounts role,cp_roles_software srole,cp_accounts accounts"
                ." where role.account_id= ".$this->_data['account_id']." and basic.id=desktop.basic_id and "
                ." desktop.id=sdesktop.host_id and sdesktop.software_id=slist.id "
                ." and role.role_id=srole.role_id and srole.software_id=slist.id "
                ." and accounts.id=role.account_id  and accounts.department_id=basic.department_id"
                ." ORDER BY slist.sort_order ASC ";
            $data = $conn->execute($sql)->fetchAll('assoc');
        }else{
            //数据校验，缺少字段
            $code = '3000';
            $msg = $this->_getMsg($code,implode(',', $lack_fields));
        }

        if (intval($code)){
            $log ="getToken方法调用:\n";
            $log.="传递参数:".json_encode($this->_data)."\n";
            $log.="错误信息:".$msg."\n";

            Log::error($log);
        }
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }


    /**
     * 索贝云到获取token
     *
     * 参数
     * loginname 索贝云账号
     * @return
     */
    public function getToken()
    {
        $code = $this->_code;
        $msg = $this->_getMsg($code);
        //数据校验
        $lack_fields = [];

        $_needed_fileds = [
            'loginname',//对应cmop中的loginname
            'password',
        ];

        if($_needed_fileds){
            foreach ($_needed_fileds as $_key){
                if (!isset($this->_data[$_key])){
                    $lack_fields[] = $_key;
                }
            }
        }

        if (empty($lack_fields)){

            //cmop和vboss数据库不能互相直接访问
            $accounts_vboss_table = TableRegistry::get('Accounts');
            $accounts_vboss_info  = $accounts_vboss_table ->find()->where(['loginname'=>$this->_data['loginname']])->first();




            if ($accounts_vboss_info){

                //校验密码
                if ($this->_chkPwd($this->_data['password'], $accounts_vboss_info['salt'], $accounts_vboss_info['password'])){
                    $current_time = time();
                    //$expire_time = $current_time+= Configure::read('tokens.duration');

                    if (Cache::write('vboss_'.$this->_data['loginname'], $accounts_vboss_info,'accounts')){
                        $data['token'] = $this->Des->encrypt($current_time.'@'.$accounts_vboss_info['loginname']);
                    }else{
                        //缓存文件失败
                        $code = '2001';
                        $msg = $this->_getMsg($code);
                    }
                }else{
                    //用户不存在
                    $code = '3003';
                    $msg = $this->_getMsg($code);
                }

            }else{
                //用户不存在
                $code = '3001';
                $msg = $this->_getMsg($code,$this->_data['loginname']);
            }

        }else{
            //数据校验，缺少字段
            $code = '3000';
            $msg = $this->_getMsg($code,implode(',', $lack_fields));
        }
        if (intval($code)){
            $log ="getToken方法调用:\n";
            $log.="传递参数:".json_encode($this->_data)."\n";
            $log.="错误信息:".$msg."\n";

            Log::error($log);
        }
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }


    private function _chkPwd($pwd_md5,$salt,$hased_pwd){
        return md5($pwd_md5.$salt) === $hased_pwd;
    }


    public function getCmopInfoByVbossInfo()
    {

        $code = $this->_code;
        $msg = $this->_getMsg($code);
        //数据校验
        $lack_fields = [];

        //TODO
        $_needed_fileds = [
            'loginname',//对应cmop中的loginname
            'password',
        ];

        if($_needed_fileds){
            foreach ($_needed_fileds as $_key){
                if (!isset($this->_data[$_key])){
                    $lack_fields[] = $_key;
                }
            }
        }

        if (empty($lack_fields)){

            $cmop_info = [];

            $accounts_table = TableRegistry::get('Accounts');
            $cmop_info['accounts'] = $accounts_table->find()->where([
                'loginname'=>$this->_data['loginname']
            ])->first();

            if (!$cmop_info['accounts']){
                $current_time = time();

                //不存在账号信息,新建
                $accounts_cmop = $accounts_table->newEntity();

                $accounts_cmop->email = $this->_data['email'];
                $accounts_cmop->loginname = $this->_data['loginname'];
                $accounts_cmop->username = $this->_data['username'];
                $accounts_cmop->mobile = $this->_data['mobile'];
                $accounts_cmop->password = $this->_data['password'];
                $accounts_cmop->salt = $this->_data['salt'];
                //$accounts_cmop->address = $vboss_account_info['address'];
                $accounts_cmop->active = 1;
                $accounts_cmop->department_id = 61;//云工厂默认部门id
                $accounts_cmop->create_by = 0;//系统
                $accounts_cmop->create_time = $current_time;
                $accounts_cmop->modify_time = $current_time;
                $accounts_cmop->expire = -1;//过期时间，用不过期
                $accounts_cmop->source = 1;

                $accounts_cmop = $accounts_table->save($accounts_cmop);

                if ($accounts_cmop->id){
                    $cmop_info['accounts'] = $accounts_cmop;
                    //添加角色信息
                    $roles_accounts_table = TableRegistry::get('RolesAccounts');

                    $roles_accounts = $roles_accounts_table->newEntity();

                    $roles_accounts->role_id  = 61;//云工厂默认角色id
                    $roles_accounts->account_id  = $accounts_cmop->id;
                    $roles_accounts = $roles_accounts_table->save($roles_accounts);
                    $cmop_info['roles'][] = $roles_accounts->role_id;
                    //if ($roles_accounts_cmop->id){
                    //$cmop_info['role'] = $accounts_cmop;

                    //$departments_cmop_table = TableRegistry::get('Departments',[
                    //'className'=>'App\Model\Table\Cmop\DepartmentsTable',
                    //]);

                    //}
                }
            }else{
                $roles_accounts_table = TableRegistry::get('RolesAccounts');
                $roles_cmop = $roles_accounts_table->find('list',['valueField'=>'role_id'])->where(['account_id'=>$cmop_info['accounts']['id']])->toArray();
                $cmop_info['roles'] = $roles_cmop?array_values($roles_cmop):[];
            }

            $data = $cmop_info;

        }else{
            //数据校验，缺少字段
            $code = '3000';
            $msg = $this->_getMsg($code,implode(',', $lack_fields));
        }

        if (intval($code)){
            $log ="getToken方法调用:\n";
            $log.="传递参数:".json_encode($this->_data)."\n";
            $log.="错误信息:".$msg."\n";

            Log::error($log);
        }
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }

    /**
     * 获取cmop账号信息接口
     * 参数
     * loginname登录名
     * token 免校验令牌
     *
     */
    public function getAccountsInfo()
    {
        $code = $this->_code;
        $msg = $this->_getMsg($code);
        //数据校验
        $lack_fields = [];

        $_needed_fileds = [
            'loginname',//对应cmop中的loginname
            'token'
        ];

        if($_needed_fileds){
            foreach ($_needed_fileds as $_key){
                if (!isset($this->_data[$_key])){
                    $lack_fields[] = $_key;
                }
            }
        }

        if (empty($lack_fields)){
            //校验token
            $token_decrypt = $this->Des->decrypt($this->_data['token']);
            $token_decrypt_arr = explode('@', $token_decrypt);
            $current_time = time();

            if ($current_time - $token_decrypt_arr[0] < 1800&&isset($token_decrypt_arr[1])&&($token_decrypt_arr[1]==$this->_data['loginname'])){

                //从缓存中取vboss账号信息
                $_loginname = $this->_data['loginname'];

                $accounts_info = Cache::remember('cmop_'.$this->_data['loginname'], function () use($_loginname){
                     $accounts_table = TableRegistry::get('Accounts');
                     return $accounts_table ->find()->where(['loginname'=>$_loginname])->first();
                },'accounts');


                    if ($accounts_info){
                        $data['accounts'] = $accounts_info;

                    }else{
                        $code = '3003';
                        $msg = $this->_getMsg($code);
                    }


            }else{
                //token已过期
                //删除Cache
                Cache::delete('cmop_'.$this->_data['loginname'],'accounts');
                $code = '3002';
                $msg = $this->_getMsg($code);

            }


        }else{
            //数据校验，缺少字段
            $code = '3000';
            $msg = $this->_getMsg($code,implode(',', $lack_fields));
        }
        if (intval($code)){
            $log ="getToken方法调用:\n";
            $log.="传递参数:".json_encode($this->_data)."\n";
            $log.="错误信息:".$msg."\n";

            Log::error($log);
        }
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }




    /**
     *
     * @param unknown $code
     * @param string $param
     */
    protected function _getMsg($code = null,$param = '')
    {
        $_msg = [
            '0000'=>'操作成功',
            '0001'=>'操作失败',
            '1000'=>'授权失败',
            '2000'=>'数据库操作失败',
            '2001'=>'缓存操作失败',
            '3000'=>'必要参数缺失:%s',
            '3001'=>'不存在该用户:%s',
            '3002'=>'token无效或已过期',
            '3003'=>'密码不正确，请重新核对密码',
            '4001'=>'云桌面正在被%s启动',
            '4002'=>'云桌面正在被%s使用',
            '4003'=>'请先关闭您已经开启的云桌面',
        ];

        if (is_null($code)){
            return $_msg;
        }else{
            if (isset($_msg[$code])){
                return sprintf($_msg[$code],$param);
            }
        }
    }





    protected function _getData() {
        $data = $this->request->data?$this->request->data:file_get_contents('php://input', 'r');

        //处理非x-form的格式
        if (is_string($data)){
            $data_tmp = json_decode($data,true);
            if (json_last_error() == JSON_ERROR_NONE){
                $data = $data_tmp;
            }
        }

        //if (Configure::read('debug')){
        //记录日志
        Log::debug("接口调用(".$this->request->params['action'].") :".json_encode($data));
        //}
        return $data;
    }

    //创建账户在CMOP系统
    protected function CreateAccountByCMOP(){
        if ($this->_data && is_array($this->_data)) {
            $request=$this->_data;
            //获取华栖云租户
            $department_table = TableRegistry::get('Departments');
            $department_id = (int)$department_table->find()->select(['id'])->where(['name'=>'华栖云'])->first()->id;
            //解析新增用户信息
            //
        }
    }

}