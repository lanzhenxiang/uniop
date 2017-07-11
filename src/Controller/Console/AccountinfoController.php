<?php
/**
 * 文件描述文字
 *
 *
 * @author XingShanghe<xingshanghe@gmail.com>
 * @date  2015年9月21日下午4:28:04
 * @source ConsoleController.php
 * @version 1.0.0
 * @copyright  Copyright 2015 sobey.com
 */

namespace App\Controller\Console;

use App\Controller\Console\ConsoleController;
use Cake\ORM\TableRegistry;

class AccountinfoController extends ConsoleController
{
    private function _checkPopedom($param)
    {
        $popedom = parent::checkConsolePopedom($param);
        return $popedom;
    }

    public function index()
    {
        $checkPopedomlist = $this->_checkPopedom('ccm_user_account');
        if (!$checkPopedomlist) {
            return $this->redirect('/console/');
        }
        $account = TableRegistry::get('Accounts');
        if ($this->request->is('post')) {
            $message = array('code' => 1, 'msg' => '修改用户信息失败');
            if (empty($this->request->data['image'])) {
                unset($this->request->data['image']);
            }
            $this->request->data['id'] = $this->request->session()->read('Auth.User.id');
            $accounts = $account->newEntity();
            $accounts = $account->patchEntity($accounts, $this->request->data);
            $result = $account->save($accounts);
            //$result = $account->save($this->request->data,array('id'=>$this->request->session()->read('Auth.User.id')));
            if ($result) {
                $message = array('code' => 0, 'msg' => '修改用户信息成功');
            }
            echo json_encode($message);
            exit;
        } else {
            $account_data = $account->find()->select(['loginname', 'username', 'email', 'mobile', 'address', 'image'])->where(array('id' => $this->request->session()->read('Auth.User.id')))->toArray();
            $this->set('data', $account_data[0]);
        }
    }

    public function images()
    {
        if ($this->request->data['file'] == 'undefined') {
            $account = TableRegistry::get('Accounts');
            $image = $account->find()->select(['image'])->where(array('id' => $this->request->session()->read('Auth.User.id')))->toArray();
            if ($image) {
                echo $image[0]['image'];
            }
        } else {
            $file = base64_decode(explode(',', $this->request->data['file'])[1]);
            $imgDir = 'images/head/';

            //要生成的图片名字

            $filename = md5(time() . mt_rand(10, 99)) . ".png"; //新图片名称

            $newFilePath = $imgDir . $filename;

            $data = $file;

            $newFile = fopen($newFilePath, "w"); //打开文件准备写入

            fwrite($newFile, $data); //写入二进制流到文件

            fclose($newFile); //关闭文件
            $this->request->session()->write('Auth.User.image', $newFilePath);
            echo $newFilePath;

        }
        exit;
    }
}
