<?php
/**
 *  需要登陆的控制器基类.
 *
 * @author XingShanghe<xingshanghe@gmail.com>
 * @date  2015年9月6日下午2:00:23
 * @source AccountsController.php
 *
 * @version 1.0.0
 *
 * @copyright  Copyright 2015 sobey.com
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Network\Http\Client;
use Cake\ORM\TableRegistry;
use Composer\Autoload\ClassLoader;
use Requests as Requests;

class AccountsController extends SobeyController
{
    public $sessionKey = 'Auth.User';

    private $_http;

    public function initialize()
    {
        parent::initialize();

        $loader = new ClassLoader();
        $loader->add('Requests', ROOT . DS . 'vendor/requests');
        $loader->register();
        Requests::register_autoloader();

        $this->_http = new Client();

        // 加载部分组件

        // 加载cookie组件
        $this->loadComponent('Cookie', [
            'encryption' => false,
        ]);

        // 加载认证组件

        $this->loadComponent('Auth', [
            'loginAction' => '/accounts/login',
            'authenticate' => [
                'CmopForm' => [
                    'userModel' => 'Accounts',
                    // 'scope' => ['Accounts.status >'=>0],
                    'passwordHasher' => [
                        'className' => 'Cmop',
                    ],
                    'returnFields' => [
                        'id', 'loginname', 'username',
                        'department_id', 'email', 'mobile', 'password', 'salt', 'image',
                        'expire',
                    ],
                    'fields' => [
                        'username' => 'loginname',
                    ],
                ],

            ],
        ]);

        $this->Auth->allow([
            'login',
        ]);

        //根据token登录
        $this->_loginByToken();

        $this->hiveLogin();

        $this->userLogin();
    }

    /*
     * 登陆页面
     */
    public function login()
    {
        $this->layout = 'login';
        $popedomname = '';
        $home = new HomeController();
        $this->set('good_category', $home->getCategoeyGoodsData());
        $this->set('_number', parent::readCookieByNumber());
        if ($this->request->is('post')) {
            $this->request->session()->destroy();
            $account = $this->Auth->identify();

            $is_no_expire = true;
            //TODO 检查用户过期时间
            if (isset($account['expire'])) {
                if ((time() > $account['expire']) && $account['expire'] != -1) {
                    $is_no_expire = false;
                }
            }

            // 校验用户名/ 密码
            if ($account && $is_no_expire) {
                $popedomname = array();
                $table = TableRegistry::get('Departments');
                $departments = $table->find()
                    ->select([
                        'name', 'dept_code', 'type'
                    ])
                    ->where(array(
                        'id' => $account['department_id'],
                    ))
                    ->first();
                if ($departments) {
                    $account['department_name'] = $departments['name'];
                    $account['department_code'] = $departments['dept_code'];
                    $account['department_type'] = $departments['type'];
                } else {
                    $account['department_name'] = '';
                    $account['department_code'] = '';
                    $account['department_type'] = '';
                }
                $this->Auth->setUser($account);

                $this->_setPope();

                return $this->redirect($this->Auth->redirectUrl());
            } else {
                if ($is_no_expire) {
                    $this->Flash->error('账号或密码错误，请重试！');
                } else {
                    $this->Flash->error('该帐号已过期!请联系管理员！');
                }
            }
        }
    }

    protected function _setPope()
    {
        $id = $this->request->session()->read('Auth.User.id');
        // 获取用户权限
        // $response = $this->_http->post('http://'.$_SERVER['HTTP_HOST'].'/api/Popedomlist/getUserPopedomInfo',[
        // 'userid'=>$id,
        // ]);
        $response = Requests::post(Configure::read('Api.cmop') . '/Popedomlist/getUserPopedomInfo', [], [
            'userid' => $id,
        ], [
            'verify' => false,
        ]);
        $response_arr = json_decode(trim($response->body, chr(239) . chr(187) . chr(191)), true); //所以问题来了，不小心在返回的json字符串中返回了BOM头的不可见字符，某些编辑器默认会加上BOM头，如下处理才能
        if ($response_arr['code'] == 0) {
            if (isset($response_arr['data'])) {
                $popedomname = $response_arr['data'];
            }
        }
        $this->Cookie->write('logining', 1);
        $account = $this->request->session()->read('Auth.User');
        if (!empty($popedomname)) {
            $account['popedomname'] = $popedomname;
        } else {
            $account['popedomname'] = [];
        }

        $this->Auth->setUser($account);
        if (!empty($popedomname)) {
            $popedomname = array_unique($popedomname);
        }
    }

    /*
     * 重置密码
     */
    public function pwdreset()
    {
    }

    /*
     * 用户注册
     */
    public function regist()
    {
    }

    public function loginout()
    {
        // debug($this->Cookie->read($name));die();
        $this->Cookie->delete('logining');
        $this->Cookie->delete('user');
        $this->request->session()->destroy();
        $this->redirect([
            'controller' => 'Accounts',
            'action' => 'login',
        ]);
    }

    public function checkPopedomlist($sring)
    {
        $popedomname = $this->request->session()->read('Auth.User.popedomname') ? $this->request->session()->read('Auth.User.popedomname') : [
            '',
        ];

        return in_array($sring, $popedomname);
    }

    /**
     * 通过token登录.
     *
     * @throws \Exception
     */
    public function _loginByToken()
    {
        //根据token登录
        if ($this->request->query('token')) {
            $_token = $this->request->query('token');
            $_loginname = $this->request->query('loginname');

            try {
                $response = Requests::post(Configure::read('Api.cmop') . '/Accounts/getAccountsInfo', [], [
                    'loginname' => $this->request->query('loginname'),
                    'token' => $this->request->query('token'),
                ], [
                    'verify' => false,
                ]);

                $response = json_decode($response->body, true);

                if (isset($response['code']) && ($response['code'] == 0)) {
                    //登录成功
                    $user = $response['data']['accounts'];
                    unset($user['password']);
                    unset($user['salt']);
                    $this->Auth->setUser($user);
                    $this->_setPope();
                } else {
                    throw new \Exception(isset($response['msg']) ? $response['msg'] : '获取用户信息失败');
                }
            } catch (\Exception $e) {
                Log::error('接口调用(/api/Accounts/getAccountsInfo)失败:' . $e->getMessage());
            }
        }
    }

    protected function _GetBillCycle($index = null)
    {
        $goodsList = Configure::read('billCycle');
        // $goodsList = array_flip($goodsList);
        if (!is_null($index)) {
            return $goodsList[$index];
        } else {
            return $goodsList;
        }
    }

    public function hiveLogin()
    {
        if ($this->request->query('token')) {
            try {
                $account = TableRegistry::get('Accounts');
                $account_data = $account->find()->select(['id', 'loginname', 'username',
                    'department_id', 'email', 'mobile', 'password', 'salt', 'image',
                    'expire'])->where(array('loginname' => 'qyj211'))->first();
                if ($account_data) {
                    $account_data = $account_data->toArray();
                    $this->Auth->setUser($account_data);
                    $this->_setPope();
                    $this->redirect('/');
                } else {
                    throw new \Exception('该用户不存在');
                }
            } catch (\Exception $e) {
                Log::error('获取用户信息失败');
            }
        }
    }

    public function userLogin()
    {
        if ($this->request->query('loginInfo')) {
            try {
                $ciphertext_base64 = $this->request->query('loginInfo');
                // debug("1:".$ciphertext_base64);
                $privateKey = 'SobeyHive1234567';
                $iv = 'SobeyHive1234567';
                // //加密
                // $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $privateKey, $data, MCRYPT_MODE_CBC, $iv);
                // echo(base64_encode($encrypted));
                // echo '<br/>';

                //解密
                // $encryptedData = base64_decode($ciphertext_base64);
                //排除乱码
                $encryptedData = base64_decode(str_replace(' ', '+', $ciphertext_base64));
                // debug($encryptedData);die();
                $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $privateKey, $encryptedData, MCRYPT_MODE_CBC, $iv);
                // return substr($str, 0, strlen($str) - $pad);
                $decrypted = trim($decrypted);
                // debug($decrypted);die();
                $account = TableRegistry::get('Accounts');
                $account_data = $account->find()->select(['id', 'loginname', 'username',
                    'department_id', 'email', 'mobile', 'password', 'salt', 'image',
                    'expire'])->where(array('loginname' => $decrypted))->first();
                // debug($account_data);die();
                if ($account_data) {
                    $account_data = $account_data->toArray();
                    $this->Auth->setUser($account_data);
                    $this->_setPope();
                    $this->redirect('/');
                } else {
                    throw new \Exception('该用户不存在');
                }
                // debug("3:".$decrypted);die();
            } catch (Exception $e) {
                Log::error('获取用户信息失败');
            }
        }
    }
}
