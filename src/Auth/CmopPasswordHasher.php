<?php
/** 
* 文件描述文字
* 
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date  2015年9月6日下午2:35:08
* @source CmopPasswordHasher.php
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 

namespace App\Auth;

use Cake\Auth\AbstractPasswordHasher;

class CmopPasswordHasher extends AbstractPasswordHasher
{
    protected $_defaultConfig = [
        'salt'=>''
    ];
    
    public function __construct(array $config = []){
        parent::__construct($config);
    }
    
    public function hash($password){
        //return $password;//测试用明文密码
        //加入对salt支持
        return md5(md5($password).$this->_config['salt']);
    }
    
    public function check($password, $hashedPassword){
        //return $password === $hashedPassword;//测试用明文密码
        //对salt支持
        return md5(md5($password).$this->_config['salt']) ===  $hashedPassword;
    }
    public function setSalt($salt = null){
        if(!is_null($salt))
            $this->_config['salt'] = $salt;
    }
    
}