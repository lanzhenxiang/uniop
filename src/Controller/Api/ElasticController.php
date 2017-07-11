<?php
namespace App\Controller\Api;
use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use App\Controller\OrdersController;
use Cake\Core\Configure;
class ElasticController extends AppController
{
    private $_data = null;
    private $_error = [];
    private $_serialize = array('code','msg','data');
    private $_code = 0;
    private $_msg = "";
    private $_db;
    private $_gTable;
    private $_gDTable;

    
    public function start(){
    	$this->_db =ConnectionManager::get('default');
    	//查询所有的桌面组
    	$this->_gTable = TableRegistry::get("SoftwareList");
    	$this->_gDTable= TableRegistry::get("SoftwaresDesktop");
    	$groupList = $this->_gTable->find()->select();
    	foreach ($groupList as $key => $group) {
    		$this->checkGroup($group);
    	}
    	$this->viewClass = 'Json';     
    	$code='0';
        $msg = 'ok';
        $data = array();
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }

    private function checkGroup($group){
    	 $gpTable = TableRegistry::get('softwareListPolicy');
         $groupPolicy = $gpTable->find()->where(array('gid'=>$group->id))->first();
         if(!isset($groupPolicy->id) || $groupPolicy->status == "0"){
            return "";
         }

         $field = ['id'=>'desktop.id','create_by'=>'desktop.create_by','code'=>'desktop.code','priority'=>'desktop.priority','status'=>'desktop.status',
             'software_id'=>'SoftwaresDesktop.software_id','software_id'=>'SoftwaresDesktop.software_id','connect_status'=>'hostExtend.connect_status'];
         $where['SoftwaresDesktop.software_id']=$group->id;

         $softwaresDesktop = TableRegistry::get("SoftwaresDesktop");
         $result = $softwaresDesktop->find()->select($field)->where($where)->join([
             'desktop'=>[
                 'table'=>'cp_instance_basic',
                 'type'=>'LEFT',
                 'conditions'=>'SoftwaresDesktop.host_id = desktop.id'
             ],
             'hostExtend'=>[
                 'table'=>'cp_host_extend',
                 'type'=>'LEFT',
                 'conditions'=>'hostExtend.basic_id = desktop.id'
             ]
         ])->toArray();

    	$list = $this->__format($result);
    	//判断需要开机
    	if($list['off'] > 0 && $groupPolicy->min > $list['free']){
    		$hostList = $list['data'];
    		if($groupPolicy->priority == "1"){
    			usort($hostList, function($a, $b) {
    				$al = $a['priority'];
            		$bl = $b['priority'];
            		if ($al == $bl){
            			return 0;
            		}
            		return ($al > $bl) ? -1 : 1;
        		});
    		}

    		foreach ($hostList as $key => $host) {
    			if($host['status'] !="已停止"){
    				continue;
    			}

    			if($groupPolicy->min <= $list['free']){
    				break;
    			}
    			$this->startDesktop($host);
    			$list['free'] +=1;
    			$list['open'] +=1;
    			
    		}
    		return ;
    	}
    	//需要关机的情况
    	if($list['free'] > 0 && $groupPolicy->min < $list['free'] ){
    		$hostList = $list['data'];
    		if($groupPolicy->priority == "1"){
    			usort($hostList, function($a, $b) {
    				$al = $a['priority'];
            		$bl = $b['priority'];
            		if ($al == $bl){
            			return 0;
            		}
            		return ($al < $bl) ? -1 : 1;
        		});
    		}
    		foreach ($hostList as $key => $host) {

    			if($host['status'] !="运行中" || $host["connect_status"] !="0"){
    				continue;
    			}

    			if($groupPolicy->min >= $list['free']){
    				break;
    			}
    			$this->stopDesktop($host);
    			$list['free'] +=-1;
    			$list['open'] +=-1;
    			
    		}
    		return ;
    	}
    }


    private function __format($list){
    	$return =array(
    			'num'=>0,
    			'open'=>0,
    			'off'=>0,
    			'free'=>0,
    			'data'=>[]
    		);

    	foreach ($list as $key => $host) {
    		if($host['id']==""){
    			continue;
    		}

    		$return['num'] +=1;

    		if($host['status'] =="运行中"){
    			$return['open'] +=1;
    			if($host['connect_status'] == "0"){
    				$return['free'] +=1;
    			}
    		}

    		if($host['status'] =="已停止"){
    			$return['off'] +=1;
    		}

    		$return['data'][] = $host;
    	}

    	return $return;
    }

    private function stopDesktop($host){
    	$order  = new OrdersController();
        $url    = Configure::read('URL');
        $parameter = array(
        		"uid" =>(string)$host['create_by'],
    			"basicId" =>(string)$host['id'],
    			"desktopCode" =>$host['code'] ,
    			"method" => "desktop_stop",
        	);
       $res =  $order->postInterface($url, $parameter);
    }


    private function startDesktop($host){
    	$order  = new OrdersController();
        $url    = Configure::read('URL');
        $parameter = array(
        		"uid" => (string)$host['create_by'],
    			"basicId" => (string)$host['id'],
    			"desktopCode" =>$host['code'] ,
    			"method" => "desktop_start",
        	);
        $order->postInterface($url, $parameter);
    }


}



?>
