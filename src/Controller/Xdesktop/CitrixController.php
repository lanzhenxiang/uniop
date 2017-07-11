<?php
/**
* Citrix控制器- 云桌面
*
* citrix云桌面启动流程：
* 1.获取配置
*
*
* @author XingShanghe<xingshanghe@gmail.com>
* @date  2015年10月28日上午11:12:42
* @source CitrixController.php
* @version 1.0.0
* @copyright  Copyright 2015 sobey.com
*/
namespace App\Controller\Xdesktop;

use App\Controller\AccountsController;

use Cake\Core\Configure;
use Cake\Network\Exception\NotFoundException;
use Cake\Network\Http\Client;
use Cake\ORM\TableRegistry;

use Composer\Autoload\ClassLoader;
use \Requests as Requests;
use Cake\Error\FatalErrorException;
use Cake\Log\Log;

class CitrixController extends AccountsController{

    protected $_http;
    protected $_url;


    protected $_headers;
    protected $_options;

    public function initialize()
    {
        parent::initialize();
        $this->_http = new Client();
        $this->_url = Configure::read('Api.cmop');
        $loader = new ClassLoader();
        $loader->add('Requests', ROOT.DS.'vendor/requests');
        $loader->register();
        Requests::register_autoloader();
    }




    public function launch($name = null)
    {
        $this->layout = false;
        if (is_null($name)){
            $this->set('_msg','请输入正确的主机名称');
        }else{
            $this->set('name',$name);
            $_current_time = time();
            $host_extend_table = TableRegistry::get('HostExtend');

            $desk = $this->_getDeskInfoByName($name);

            switch ($desk['connect_status']){
                case 99:
                    //如果上次汇报时间大于300秒则认为上次启动失败
                    if ((int)$_current_time - (int)$desk['last_reporttime'] >30){
                        $desk["connect_status"] = 0;
                        $desk["connect_id"] = 0;
                        $desk["connect_user"] = '';
                        if($host_extend_table->save($desk)){
                            Log::error('Desktop Status is not automatically updated.',$desk);
                        }
                        $desk = $this->_getDeskInfoByName($name);
                    }else{
                        $this->set('_msg','该桌面正在被其他用户'.$desk['connect_user'].'启动！');
                        break;
                    }
                case 0:
                    //正常启动云桌面
                    //尝试启动
                    $desk->connect_user = $this->request->session()->read('Auth.User.username');
                    $desk->connect_id = $this->request->session()->read('Auth.User.id');
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
                                'username'=>$this->request->session()->read('Auth.User.username'),
                                'name'=>$name,
                                'status'=>'99',
                                'description'=>'启动中'
                            ]
                        ]);
                    }
                    //查询用户密码
                    $user_table = TableRegistry::get('Accounts');
                    $user = $user_table->get($this->request->session()->read('Auth.User.id'));
                    @$this->_doDeskLogic($name);
                    try {
                        $response =  Requests::post($this->_url.'/citrix/launch20160229',[],[
                            'loginname'=>$user->loginname,//cmop密码
                            'password'=>$user->password,//cmop账号
                            'name'=>$name,//cmop主机名
                        ],[
                            'verify'=>false,
                            //'timeout'=>2
                        ]);
                        $response = json_decode(trim($response->body,chr(239).chr(187).chr(191)),true);
                        //启动成功
                        if ($response['code'] == '0000'){
                            if (isset($response['data']['headers'])&&!empty($response['data']['headers'])){
                                header('Content-Type:'.$response['data']['headers']['content-type']);
                            }else{
                                header('Content-Type:application/x-ica');
                            }
                            $response['data']['body'] = str_replace("=\\\\", "=\\", $response['data']['body']);
                            echo $response['data']['body'];die;
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
                                        'username'=>$this->request->session()->read('Auth.User.username'),
                                        'name'=>$name,
                                        'status'=>'0',
                                        'description'=>'启动失败'
                                    ]
                                ]);
                            }
                            $this->set('_msg',$response['msg']?$response['msg']:'启动失败，原因未知2！');
                        }

                    } catch (\Exception $e) {
                       $this->set('_msg',$e->getMessage());
                    }

                    break;
                case 1:
                    $this->set('_msg','该桌面已被其他用户'.$desk['connect_user'].'占用！');
                    break;
                default:
                    $this->set('_msg','启动失败，原因未知！');
            }




        }
    }

    private function _doDeskLogic($name,$user){

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

    private function _getDeskInfoByName($name = null)
    {
        $host_extend_table = TableRegistry::get('HostExtend');
        return $host_extend_table->find()->select([
            'id','connect_status','connect_id','connect_user','connect_time','last_reporttime'
        ])->where([
            'name'=>$name
        ])->first();
    }




    /**
     * 启动桌面
     * @param string $name
     */
    public function launch_old($name){
        $this->autoRender = false;
        if (empty($name)){
            throw new NotFoundException();
        }else{
            $desk_table = TableRegistry::get('DesktopExtend');
            $desk = $desk_table->find('all')->select(['id','connect_status','last_reporttime'])->where(['name'=>$name])->first();

            //如果上次汇报时间上次大于300秒(5分钟)启动失效
            //注意 时间格式注意为时间戳格式
            if (($desk['connect_status'] == 99) && (time()-$desk['last_reporttime'] > 30)){
                $desk['connect_status'] = 0;
                $desk->connect_user = $this->request->session()->read('Auth.User.username');
                $desk_table->save($desk);
                $desk = $desk_table->find('all')->select(['id','connect_status','last_reporttime'])->where(['name'=>$name])->first();
            }

            //更改状态为已被XX占用，
            if($desk['connect_status'] == 0 ){
                //
                //$desk->id = $desk->id;
                $desk->connect_user = $this->request->session()->read('Auth.User.username');
                $desk->connect_id = $this->request->session()->read('Auth.User.id');
                $desk->connect_time = time();
                $desk->connect_status = 99;
               // try {
                    //$desk_table->save($desk);
                    $desk_table->save($desk);
                    //通知消息服务器
                    $current_time = time();

                    if (is_string(Configure::read('NotifyUrl'))){
                        $response_notify_obj = @Requests::post(Configure::read('NotifyUrl').'/send',[],[
                            'time'=>$current_time,
                            'sign'=>md5($current_time.Configure::read('NotifyKey')),
                            'data'=>json_encode([
                                'SendType'=>'websocket',
                                'MsgType'=>'info',
                                'Msg'=>'',
                                'Topic'=>$name,
                                'Data'=>[
                                    'username'=>$this->request->session()->read('Auth.User.username'),
                                    'name'=>$name,
                                    'status'=>'99',
                                    'description'=>'启动中'
                                ]
                            ]),
                        ]);
                    }elseif (is_array(Configure::read('NotifyUrl'))){
                        $urls = Configure::read('NotifyUrl');

                        foreach ($urls as $url){
                            try {
                                $response_notify_obj = @Requests::post($url.'/send',[],[
                                    'time'=>$current_time,
                                    'sign'=>md5($current_time.Configure::read('NotifyKey')),
                                    'data'=>json_encode([
                                        'SendType'=>'websocket',
                                        'MsgType'=>'info',
                                        'Msg'=>'',
                                        'Topic'=>$name,
                                        'Data'=>[
                                            'username'=>$this->request->session()->read('Auth.User.username'),
                                            'name'=>$name,
                                            'status'=>'99',
                                            'description'=>'启动中'
                                        ]
                                    ]),
                                ]);
                            } catch (\Exception $e) {
                                Log::error($e->getMessage().':'.$url);
                                continue;
                            }

                        }
                    }



                //} catch (\Exception $e) {
                   // throw new FatalErrorException($e->getMessage());
                //}

                $user_table = TableRegistry::get('Accounts');
                $user = $user_table->get($this->request->session()->read('Auth.User.id'));

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
                    //查询当前用户密码
                    $response_notify_obj = Requests::post('http://'.Configure::read('NotifyUrl').'/send',[],[
                        'time'=>$current_time,
                        'sign'=>md5($current_time.Configure::read('NotifyKey')),
                        'data'=>json_encode([
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
                        ]),
                    ]);
                 }

                /*
                $user_table = TableRegistry::get('Accounts');
                $user = $user_table->get($this->request->session()->read('Auth.User.id'));
                */
                $response =  Requests::post($this->_url.'/citrix/launch',[],[
                    'loginname'=>$user->loginname,//cmop密码
                    'password'=>$user->password,//cmop账号
                    'name'=>$name,//cmop主机名
                ],[
                    'verify'=>false
                ]);

                $response = json_decode($response->body,true);


                if ($response['code'] == '0000'){
                    if (isset($response['data']['headers'])&&!empty($response['data']['headers'])){
                        header('Content-Type:'.$response['data']['headers']['content-type']);
                    }else{
                        header('Content-Type:application/x-ica');
                    }
                    $response['data']['body'] = str_replace("=\\\\", "=\\", $response['data']['body']);
                    echo $response['data']['body'];die;
               }else{
                   $desk->connect_status = 0;
                   try {
                       $desk_table->save($desk);

                       if (is_string(Configure::read('NotifyUrl'))){
                           //TODO 通知消息服务器
                           $response_notify_obj = @Requests::post(Configure::read('NotifyUrl').'/send',[],[
                               'time'=>$current_time,
                               'sign'=>md5($current_time.Configure::read('NotifyKey')),
                               'data'=>json_encode([
                                   'SendType'=>'websocket',
                                   'MsgType'=>'info',
                                   'Msg'=>'',
                                   'Topic'=>$name,
                                   'Data'=>[
                                       'username'=>$this->request->session()->read('Auth.User.username'),
                                       'name'=>$name,
                                       'status'=>'0',
                                       'description'=>'启动失败'
                                   ]
                               ]),
                           ]);
                       }elseif (is_array(Configure::read('NotifyUrl'))){
                           $urls = Configure::read('NotifyUrl');
                           foreach ($urls as $url){
                               try {
                                   //TODO 通知消息服务器
                                   $response_notify_obj = @Requests::post($url.'/send',[],[
                                       'time'=>$current_time,
                                       'sign'=>md5($current_time.Configure::read('NotifyKey')),
                                       'data'=>json_encode([
                                           'SendType'=>'websocket',
                                           'MsgType'=>'info',
                                           'Msg'=>'',
                                           'Topic'=>$name,
                                           'Data'=>[
                                               'username'=>$this->request->session()->read('Auth.User.username'),
                                               'name'=>$name,
                                               'status'=>'0',
                                               'description'=>'启动失败'
                                           ]
                                       ]),
                                   ]);
                               } catch (\Exception $e) {
                                   Log::error($e->getMessage().':'.$url);
                                   continue;
                               }

                           }
                       }




                   } catch (\Exception $e) {
                       throw new FatalErrorException($e->getMessage());
                   }
                   echo '<script>alert("启动失败")</script>';die;
               }
           }else{
               //判断启动中是否超时
               echo '<script>alert("已被占用")</script>';die;
           }
        }

    }


    protected function _getNas(){
        $nas_table = TableRegistry::get('UserNas');
        return $nas_table->find()->where(['user_id'=>$this->request->session()->read('Auth.User.id')])->first();
    }




}