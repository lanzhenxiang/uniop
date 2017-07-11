<?php
/**
 * 
 * @date: 2016年3月14日 下午3:02:48
 * @author: wangjc
 */
namespace App\Controller\Console;

use App\Controller\Console\ConsoleController;
use Cake\ORM\TableRegistry;

class InstanceLogsController extends ConsoleController
{

    /**
     * 检查权限
     * @date: 2016年3月10日 下午4:50:06
     *
     * @author : wangjc
     * @access private
     * @param string $param
     * @return boolean
     */
    private function _checkPopedom($param)
    {
        $popedom = parent::checkConsolePopedom($param);
        return $popedom;
    }
    
    /**
     * 操作记录
     * @date: 2016年3月14日 下午3:04:49
     *
     * @author : wangjc
     */
    public function index($uid = 0, $start = 0, $end = 0)
    {
        $_pop = false;
        $_pop = $this->_checkPopedom('ccm_user_instance_logs'); // 检查权限
        if (! $_pop) {
            return $this->redirect('/console/');
        }
        $instance_logs_table = TableRegistry::get('InstanceLogs');
        $accounts_table = TableRegistry::get('Accounts');
        
        $limit = 10;
        $offset = 0;
        if ($offset > 0) {
            $offset = $offset - 1;
        }
        $offset = $offset * $limit;
        $where = array();
        $user_name = '全部';
        if ($uid != 0) {
            $where['Accounts.id'] = $uid;
            $user_data = $accounts_table->find()->where(['id' => $uid])->first();
            if(!empty($user_data)){
                $user_name = $user_data['username'];
            }else{
                $user_name = '未知';
            }
        }
        if ($start != 0) {
            $_start_time = strtotime($start);
            $where['InstanceLogs.create_time >='] = $_start_time;
        }
        if ($end != 0) {
            $_end_time = strtotime($end) + 86400;
            $where['InstanceLogs.create_time <='] = $_end_time;
        }
        $department_id = $this->request->session()->read('Auth.User.department_id') ? $this->request->session()->read('Auth.User.department_id') : 0;
        $accounts_data = array();
        if ($department_id != 0) {
            $accounts_data = $accounts_table->find()
                ->where([
                'department_id' => $department_id
            ])
                ->toArray();
        }
        $instance_logs_data = $instance_logs_table->find()
            ->contain([
            'Accounts',
            'InstanceBasic'
        ])
            ->where([
            'Accounts.department_id' => $department_id
        ])
            ->where($where)
            ->order([
            'InstanceLogs.create_time   DESC'
        ])
            ->limit($limit)
            ->offset($offset)
            ->toArray();
        
        $this->set('user_name', $user_name);
        $this->set('uid', $uid);
        $this->set('start', $start);
        $this->set('end', $end);
        $this->set('accounts_data', $accounts_data);
        $this->set('instance_logs_data', $instance_logs_data);
    }

    public function logsData($limit = 10, $offset = 0, $uid = 0, $start = 0, $end = 0)
    {
        if ($offset > 0) {
            $offset = $offset - 1;
        }
        $offset = $offset * $limit;
        $where = array();
        if ($uid != 0) {
            $where['Accounts.id'] = $uid;
        }
        if ($start != 0) {
            $_start_time = strtotime($start);
            $where['InstanceLogs.create_time >='] = $_start_time;
        }
        if ($end != 0) {
            $_end_time = strtotime($end) + 86400;
            $where['InstanceLogs.create_time <='] = $_end_time;
        }
        $department_id = $this->request->session()->read('Auth.User.department_id') ? $this->request->session()->read('Auth.User.department_id') : 0;
        $instance_logs_table = TableRegistry::get('InstanceLogs');
        
        $instance_logs_data = $instance_logs_table->find()
            ->contain([
            'Accounts',
            'InstanceBasic'
        ])
            ->where([
            'Accounts.department_id' => $department_id
        ])
            ->where($where)
            ->order([
            'InstanceLogs.create_time   DESC'
        ])
            ->limit($limit)
            ->offset($offset)
            ->toArray();
        
        echo json_encode($instance_logs_data);
        exit;
    }
}
