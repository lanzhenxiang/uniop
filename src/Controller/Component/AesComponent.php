<?php
/**
* Rsa 加密解密
*
* @author XingShanghe<xingshanghe@gmail.com>
* @date 2015年5月20日下午11:06:07
* @source RsaComponent.php
* @version 1.0.0
* @copyright  Copyright 2015 sobey.com
*/
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Filesystem\File;

class AesComponent extends Component
{
    //密钥
    private $_secrect_key;
    private $_iv;

    public function __construct(){
        $this->_secrect_key = 'SobeyHive1234567';
        $this->_iv='SobeyHive1234567';
    }
    /**
     * 加密方法
     * @param string $str
     * @return string
     */
    public static function encrypt($str){
        //AES, 128 CBC模式加密数据
        $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $_secrect_key, $data, MCRYPT_MODE_CBC, $iv);
        echo(base64_encode($encrypted));
        echo '<br/>';
    }

    /**
     * 解密方法
     * @param string $str
     * @return string
     */
    public static function decrypt($str){
        //AES, 128 CBC模式加密数据
        //排除乱码
        $encryptedData = base64_decode(str_replace(" ","+",$str));
        // debug($encryptedData);die();
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $privateKey, $encryptedData, MCRYPT_MODE_CBC, $iv);
        // return substr($str, 0, strlen($str) - $pad);
        $decrypted=trim($decrypted);
    }
}