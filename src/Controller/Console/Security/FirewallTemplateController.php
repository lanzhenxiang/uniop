<?php
/**
 * ==============================================
 * FirewallTemplateController.php
 * @author: shrimp liao
 * @date: 2015年11月3日 上午10:35:14
 * @version: v1.0.0
 * @desc: 安全控制器-子页面
 * ==============================================
 **/
namespace App\Controller\Console\Security;

use App\Controller\Console\ConsoleController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
class FirewallTemplateController extends ConsoleController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }
    private $_serialize = array('code', 'msg', 'data');
    public $_pageList = array('total' => 0, 'rows' => array());
    /**
     * @func: 获取数据信息
     * @param:
     * @date: 2015年11月3日 上午10:39:15
     * @author: shrimp liao
     * @return: null
     */
    public function lists($request_data = array())
    {
        $this->paginate['limit'] = $request_data['limit'];
        $this->paginate['page'] = $request_data['offset'] / $request_data['limit'] + 1;
        $where = array('1' => '1');
        $firewalltemplate = TableRegistry::get('FirewallTemplate');
        if(isset($request_data['search'])){
            if ($request_data['search']!="") {
            $where['FirewallTemplate.template_name like'] = '%' . $request_data['search'] . '%';
        }
        }

        $this->_pageList['total'] = $firewalltemplate->find('all')->where($where)->count();
        $this->_pageList['rows'] = $this->paginate($firewalltemplate->find('all')->contain(array('FirewallTemplateDetail'))->where($where));
        return $this->_pageList;
    }
    public function add($request_data)
    {
        $name = $request_data['name'];
        $request_data['template_name'] = $name;
        // debug($name);
        $firewalltemplate_Table = TableRegistry::get('FirewallTemplate');
        $entity = $firewalltemplate_Table->newEntity();
        $entity = $firewalltemplate_Table->patchEntity($entity, $request_data);
        if ($firewalltemplate_Table->save($entity)) {
            $this->_serialize['code'] = '0';
            $this->_serialize['msg'] = '添加成功';
        } else {
            $this->_serialize['code'] = '1';
            $this->_serialize['msg'] = '添加失败';
        }
        return $this->_serialize;
    }
    public function edit($request_data)
    {
        $request_data['template_name'] = $request_data['name'];
        unset($request_data['name']);
        $code = '0001';
        $data = array();
        //编辑操作
        $Table = TableRegistry::get('FirewallTemplate', array('classname' => 'App\\Model\\Table\\FirewallTemplateTable'));
        $result = $Table->updateAll($request_data, array('id' => $request_data['id']));
        if ($result == 1) {
            $code = '0000';
            $data = $Table->get($request_data['id'])->toArray();
        }
        $msg = Configure::read('MSG.' . $code);
        return compact(array_values($this->_serialize));
    }
    public function del($request_data)
    {
        $result = array('Code' => '0', 'Message' => '');
        $firewalltemplate_Table = TableRegistry::get('FirewallTemplate');
        $entity = $firewalltemplate_Table->get($request_data['ids']);
        $entity = $firewalltemplate_Table->delete($entity);
        if (!$entity) {
            $resul['Code'] = '1';
            $resul['Message'] = $entity;
            return json_encode($result);
            die;
        }
        return json_encode($result);
        die;
    }
}