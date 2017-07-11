<?php

/**
 * ==============================================
 * class.php
 * @author: shrimp liao
 * @date: 2015年11月2日 下午5:07:04
 * @version: v1.0.0
 * @desc:安全控制台
 * ==============================================
 **/

namespace App\Controller\Console;

use App\Controller\Console\ConsoleController;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Network\Http\Client;
use Cake\Error\FatalErrorException;


class SecurityController extends ConsoleController
{
    private $_popedomName = array(
        'firewall' => 'ccm_ps_security_firewall',
        'firewallpolicy' => 'ccm_ps_security_firewall_policy',
        'firewalltemplate' => 'ccm_ps_security_firewall_template',
        'securitygroup' => 'ccm_ps_security_firewall',
    );
    private function _checkPopedom($param)
    {
        $popedom = parent::checkConsolePopedom($param);
        return $popedom;
    }


    private function _check_popedomlist($type){
        $subject_array = ['firewall','firewalltemplate','securitygroup'];
        $check_vale = '';
        foreach ($subject_array as $key => $value) {
            if($type == 'list'){
                $popedomName = $this->_popedomName[$value];
            }
            if (! empty($popedomName)) {
                $check = $this->_checkPopedom($popedomName);
                if($check) {
                    $check_vale = $value;
                    break;
                }
            }
        }
        return $check_vale;
    }
    /**
     * 网络实例显示
     * @param string $subject 主题
     * @param string $category 分类
     * @param number $tab 标签
     * @throws MissingTemplateException
     * @throws NotFoundException
     * @return Ambigous <void, \Cake\Network\Response>
     */
    public function lists($subject = 'firewall')
    {
        if (! empty($this->_popedomName[$subject])) {
            $checkPopedomlist = $this->_checkPopedom($this->_popedomName[$subject]);
            if (! $checkPopedomlist) {
                $subject = $this->_check_popedomlist('list');
            }
        }else{
           $subject='';
        }
        if(empty($subject)){
            return $this->redirect('/console/');
        }
        $this->autoRender = false;

        $agent = TableRegistry::get('Agent');
        $agents = $agent->find('all')->toArray();
        $this->set('agent',$agents);

        try {
            $func_name = '_get_vars_'.$subject;
            //判断是否存在函数
            if (method_exists($this,$func_name)){
                $this->set('_view_vars',call_user_func_array([$this,$func_name],[
                    'options'   =>  [
                        'page'  =>  1,
                        'limit' =>  10
                    ],
                ]));
            }
            $popedomname = $this->request->session()->read('Auth.User.popedomname') ? $this->request->session()->read('Auth.User.popedomname') : 0;
            $this->set('popedomname', $popedomname);
            $account_table = TableRegistry::get('Accounts');
            $user = $account_table->find()->select('department_id')->where(array('id' => $this->request->session()->read('Auth.User.id')))->first();
            $deparments = TableRegistry::get('Departments');
            $this->set('_default',$deparments->get($user["department_id"]));
            $table = $deparments->find('all');
            $this->set('_deparments', $table);
            $this->render('lists/'.$subject );
        } catch (MissingTemplateException $e) {
            if (Configure::read('debug')) {
                throw $e;
            }
            throw new NotFoundException();
        }
    }

    public function add( $subject = 'firewall'){
        if (! empty($this->_popedomName[$subject])) {
            $checkPopedomlist = $this->_checkPopedom($this->_popedomName[$subject]);
            if (! $checkPopedomlist) {
                return $this->redirect('/console/');
            }
        }else{
            $subject = '';
        }
        if(empty($subject)){
            return $this->redirect('/console/');
        }
        $this->autoRender = false;
        try {

            $goods_fixed = parent::readGoodsList($subject);
            $goods_table = TableRegistry::get('Goods');
            $Systemsetting_table = TableRegistry::get('Systemsetting');
            $goods = $goods_table->find()
                ->where([
                'fixed' => $goods_fixed
            ])
                ->first();
            if (! empty($goods)) {
                $this->set('goods_id', $goods->id);
                $firewall_imageCode = $Systemsetting_table->find()->select(['para_value'])->where(['para_code'=>'firewall_imageCode'])->first()->para_value;
                $this->set('imageCode',$firewall_imageCode);
                $firewall_instanceTypeCode = $Systemsetting_table->find()->select(['para_value'])->where(['para_code'=>'firewall_instanceTypeCode'])->first()->para_value;
                $this->set('instanceTypeCode',$firewall_instanceTypeCode);
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

    public function _get_vars_firewallpolicy(){
        if ($this->request->is('get')){
            $request_data = $this->request->query;
            if(isset($request_data['templateId'])){
                $this->set('_select','firewall_template');//左侧菜单select
                $this->set('_name','防火墙模板规则');
            }
            elseif (isset($request_data['id'])) {
                $this->set('_select','firewall');//左侧菜单select
                $this->set('_name','防火墙规则');
            }
        }
    }


}

?>