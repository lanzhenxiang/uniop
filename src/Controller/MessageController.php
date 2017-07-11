<?php
/**
 * class
 *
 * @author chenqiang<chenqiang@gmail.com>
 * @date 2016年3月14日上午10:10:40
 * @source MessageController.php
 * @version 1.0.0
 * @copyright  Copyright 2016 sobey.com
 */
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Network\Email\Email;

class MessageController extends AppController
{

    /**
     * 发送email和短信接口
     * 参数用post方法传递
     * @param sendtype email,sms
     * @param mobile 手机号码
     * @param title 邮件标题
     * @param email 接受邮件的邮件地址
     * @param emailbody 邮件内容
     * @param smsbody 短信内容，注意需要到短信网关加模板
     * @param clientid 客户端自定义标识
     */
    protected $_data = null;
    public function send()
    {
        $this->autoRender = false;

        $data_request = $this->_getData();
        $sendType = explode(',', $data_request['sendtype']);
        $return = array();
        foreach ($sendType as $index => $type) {
            switch ($type) {
                case 'sms':
                    $data = $this->_sendsms($data_request['mobile'], $data_request['smsbody']);
                    $return['sms'] = $data;
                    break;
                case 'email':
                    try {
                        $return['email'] = array('code' => '0', 'msg' => '');
                        require_once ROOT . DS . 'vendor' . DS . 'sobey' . DS . 'sobeySendMail.php';
                        $mail = new \sobeySendMail();
                        $mail->setServer(Configure::read('SobeyEmail.host'), Configure::read('SobeyEmail.username'), Configure::read('SobeyEmail.password'), "25", false);
                        // $mail->setServer("email.sobey.com", "sobeyyun@sobeyyun.com", "MediaCloud2015","25",false);

                        $mail->setFrom(Configure::read('SobeyEmail.username'));
                        $mail->setReceiver($data_request['email']);

                        $mail->setMail($data_request['title'], $data_request['emailbody']);
                        $mail->sendMail();
                        $err = $mail->error();
                        if ($err != "") {
                            $return['email'] = array('code' => '1', 'msg' => $err);
                        }
                        break;
                        return $this->_sendmail($data_request['email'], $data_request['title'], $data_request['emailbody']);
                    } catch (\Exception $e) {
                        $return['email'] = array('code' => '1', 'msg' => $e->getMessage());
                    }
                    break;
                default:
                    $return[$type] = array('code' => '1', 'msg' => '未支持的方法');
            }
        }
        echo json_encode($return);
    }

    /**
     * 发送短信
     * @param 接收短信的手机号码 $mobile
     * @param 短信内容 $content
     */
    private function _sendsms($mobile, $content)
    {
        $obj_response = $this->_http_post(Configure::read('SMS.url'), [
            'apikey' => Configure::read('SMS.apikey'),
            'mobile' => $mobile,
            'text' => $content,
        ]);
        $data_response = json_decode($obj_response, true);
        //print_r($data_response);
        return $data_response;
    }

    /**
     * 发送邮件
     * @param 接收邮件地址 $toemail
     * @param 邮件标题 $title
     * @param 邮件内容 $content
     */
    private function _sendmail($toemail, $title, $content)
    {
        // $this->_intEmail();
        $email = new Email('default');
        $email->to($toemail)
            ->subject($title)
            ->emailFormat('html')
            ->send($content);
    }
    
    protected function _getData()
    {
        $data = $this->request->data ? $this->request->data : file_get_contents('php://input', 'r');

        //处理非x-form的格式
        if (is_string($data)) {
            $data_tmp = json_decode($data, true);
            if (json_last_error() == JSON_ERROR_NONE) {
                $data = $data_tmp;
            }
        }

        //if (Configure::read('debug')){
        //记录日志
        Log::debug("接口调用(" . $this->request->params['action'] . ") :" . json_encode($data));
        //}
        return $data;
    }

    /**
     * post数据
     * @param 地址 $url
     * @param 数据 $data
     */
    private function _http_post($url, $data = array())
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true); //设置为POST,表单式提交

        curl_setopt($ch, CURLOPT_HTTPHEADER, $data);

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); //POST数据
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //获取数据返回
        return curl_exec($ch);
    }
}
