<?php
namespace App\Controller\Api;
use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use App\Controller\OrdersController;
use Cake\ORM\TableRegistry;

class TrashController extends AppController{
	private $_db;
	private $_serialize = array('code','msg','data');
	private $_data;

	public function initialize(){
        parent::initialize();
        $this->_data = $this->_getData();
        $this->_db =ConnectionManager::get('default');
        $this->viewClass = 'Json';     

        $this->loadComponent('RequestHandler');
    }
    
    public function run(){
        //查询列表

        $instanceRecycle = TableRegistry::get("InstanceRecycle");
        $tasks = TableRegistry::get("Task");
        $timestamp = time();
        $result = $instanceRecycle->find()->where(['delete_time <'=>$timestamp])->toArray();


        foreach ($result as $key => $value) {
            //获取参数

             $r = $tasks->find()->where(['task_id'=>$value['taskid']])->first();
             $data = $r["request_data"];
             $data = json_decode($data,true);
             unset($data["method"]);
             if(isset($data['methodType'])){
                 $data["method"] = $data["methodType"];
                 unset($data["methodType"]);
                 $orders = new OrdersController();
                 $orders->ajaxFun($data) ;
             }
        }
        $code='0';
        $msg = 'ok';
        $data = array();
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }
    
    protected function _getData()
    {
        $data = $this->request->data?$this->request->data:file_get_contents('php://input', 'r');
        //处理非x-form的格式
        if (is_string($data)){
            $data_tmp = json_decode($data,true);
            if (json_last_error() == JSON_ERROR_NONE){
                $data = $data_tmp;
            }
        }
        return $data;
    }

}
?>