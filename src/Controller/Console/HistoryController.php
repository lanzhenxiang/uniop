<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2016/2/16
 * Time: 17:06
 */

namespace App\Controller\Console;


use Cake\Datasource\ConnectionManager;

class HistoryController extends ConsoleController{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }

    public $_pageList = array(
        'total'   =>  0,
        'rows'    =>  array()
    );

    public function historycount()
    {
        $this->layout = 'special';
    }

    //服务历史统计列表信息
   public function lists(){
       $request_data = $this->request->query;
       $limit = $request_data['limit'];
       $offset = $request_data['offset'];

       $where = ' type_id =' . $request_data['t'];
       $sql = ' SELECT  * from cp_mpc_task_Summary where '.$where.' group by finish_date desc';
       $sql_row = $sql . ' limit ' . $offset . ',' . $limit;
       $connection = ConnectionManager::get('default');
       $query = $connection->execute($sql_row)->fetchAll('assoc');
       $this->_pageList['total'] = $connection->execute($sql)->count();
       $this->_pageList['rows'] = $query;
      // debug($this->_pageList);
       echo json_encode($this->_pageList);exit;
   }
}