<?php
namespace App\Controller\Api;
use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use App\Controller\OrdersController;
use Cake\Core\Configure;
class  CmopController extends AppController
{
    private $_data = null;
    private $_error = [];
    private $_serialize = array('code','msg','data');
    private $_code = 0;
    private $_msg = "";
    private $_db;

    
    public function shutdown(){
       $this->autoRender=false;
       $str =  $this->_http(Configure::read('Api.vboss').'/Desktops/prePowerOff',$_POST,'json');
       $data = json_decode($str,true);
       if($data['code'] =="0"){
            echo "ok";
       }else{
            echo "fail";
       }
    }

    private function _http($url, $data='', $method='GET'){   
        $curl = curl_init(); // 启动一个CURL会话  
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址  
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查  
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在  
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器  
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转  
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer  
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容  
        if($method=='POST'){  
            curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求  
            if ($data != ''){  
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包  
            }  
        }else if($method=="json") {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS,json_encode($data));
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen(json_encode($data)))
            );
        }  
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环  
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回  
        $tmpInfo = curl_exec($curl); // 执行操作  
        curl_close($curl); // 关闭CURL会话  
        return $tmpInfo; // 返回数据  
    }

}



?>