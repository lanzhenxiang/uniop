<?php
/**
 * 文件描述文字
 *
 *
 * @author lanzhenxiang
 * @date  2017年2月16日
 * @version 1.0.0
 * @copyright  Copyright 2017 sobey.com
 */
namespace App\Controller\Console\Desktop;

use App\Controller\Console\Desktop\DesktopController;
use App\Controller\OrdersController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use App\Controller\HomeController as Home;

class DesktopChargeController extends DesktopController
{

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }

    /**
     * 获取列表数据,
     * 新增关联查询
     */
    public function lists($request_data = [])
    {
        $checkPopedomlist = $this->_checkPopedom('ccm_ps_desktop');
        if (!$checkPopedomlist) {
            return $this->redirect('/console/');
        }
        $this->paginate['limit'] = $request_data['limit'];
        $this->paginate['page']  = $request_data['offset'] / $request_data['limit'] + 1;

        $instance_basic = TableRegistry::get('InstanceBasic');
        $where          = [
            'InstanceBasic.type' => 'desktop',
            'isdelete'           => '0',
        ];
        if (!empty($request_data['department_id'])) {
            $where['department_id'] = $request_data['department_id'];
        }
        if (isset($request_data['search'])) {
            if ($request_data['search'] != "") {
                $where['OR'] =[
                    ["InstanceBasic.name like"=>"%" . $request_data['search'] . "%"],
                    ["InstanceBasic.code like"=>"%" . $request_data['search'] . "%"]
                ];
            }
        }
        if (!empty($request_data['class_code'])) {
            $where["InstanceBasic.location_code like"] = $request_data['class_code'] . "%";
        }
        if (!empty($request_data['class_code2'])) {
            $where["InstanceBasic.location_code like"] = $request_data['class_code2'] . "%";
        } elseif (!empty($request_data['class_code'])) {
            $where["InstanceBasic.location_code like"] = $request_data['class_code'] . "%";
        }
        $this->_pageList['total'] = $instance_basic->find('all')
            ->contain([
                'HostExtend',
                'Agent',
                'HostsNetworkCard',
                'InstanceCharge'
            ])
            ->where($where)
            ->count();
        $this->_pageList['rows'] = $this->paginate($instance_basic->find('all')
                ->contain([
                    'HostExtend',
                    'Agent',
                    'HostsNetworkCard',
                    'InstanceCharge'
                ])
                ->where($where)
                ->order([
                    'InstanceBasic.create_time' => 'DESC',
                ]));
        return $this->_pageList;
    }

    public function modeEdit($request_data)
    {	
    	//获取计费模式和计费周期
    	$data = [];
    	$code = '0001';
    	list($data['charge_mode'],$data['interval']) 	= explode("|",$request_data['charge_mode']);
    	//获取计费成交单价
    	$data['price'] 	= $data['interval'] == 'P' ? 0 : $request_data['price'];
    	//获取要修改的设备id
    	$charge_mode_desktopids = explode(",",rtrim($request_data['charge_mode_desktopids'],','));
    	$InstanceCharge = TableRegistry::get("InstanceCharge");
    	$query = $InstanceCharge->find()->where(['basic_id in'=>$charge_mode_desktopids]);
    	try {
    		$InstanceCharge->connection()->begin();
    		$updateIds = [];
	    	foreach ($query as $key => $value) {
	    		$entity = $InstanceCharge->patchEntity($value,$data);
	    		if(!$InstanceCharge->save($entity)){
	    			throw new \Exception("id为".$value['basic_id']."的桌面修改失败！");
	    		}
				$updateIds[] = $value['basic_id'];
	    	}
	    	//如果选中的设备原来没有计费信息，则添加新的计费
	    	$insertIds = array_diff($charge_mode_desktopids, $updateIds);
	    	if(is_array($insertIds) && !empty($insertIds)){
	    		$rows = [];
	    		foreach ($insertIds as $key => $value) {
	    			$data['basic_id'] = $value;
	    			$data['instance_type'] = 'desktop';
	    			$data['begin']	= time();
	    			$rows[] = $data;
	    		}
	    		$chargeEntities = $InstanceCharge->newEntities($rows);
	    		foreach ($chargeEntities as $key => $chargeEntity) {
	    			if(!$InstanceCharge->save($chargeEntity)){
	    				throw new \Exception("id为".$chargeEntity['basic_id']."的桌面新增计费失败！");
	    			}
	    		}
	    	}
	    	$code = '0000';
	    	$msg = "桌面修改计费模式成功！";
	    	$InstanceCharge->connection()->commit();
    	} catch (\Exception $e) {
    		$msg = $e->getMessage();
    		$InstanceCharge->connection()->rollback();
    	}
    	$instance_basic = TableRegistry::get('InstanceBasic');
    	$data = $instance_basic->find('all')
                ->contain([
                    'HostExtend',
                    'Agent',
                    'HostsNetworkCard',
                    'InstanceCharge'
                ])
                ->where(['InstanceBasic.id in'=>$charge_mode_desktopids])
                ->order([
                    'InstanceBasic.create_time' => 'DESC',
                ])->toArray();
    	$msg = Configure::read('MSG.' . $code);
        return compact(array_values($this->_serialize));
    }
    public function priorityEdit($request){
        $priority=isset($request['priority_data'])?$request['priority_data']:0;
        $ids=isset($request['priority_desktopids'])?trim($request['priority_desktopids'],','):'';
        $id_array=explode(',',$ids);
        $instance_basic = TableRegistry::get('InstanceBasic');
        $res=$instance_basic->updateAll(array('priority'=>$priority),array('id in'=>$id_array));
        $data = $instance_basic->find('all')
            ->contain([
                'HostExtend',
                'Agent',
                'HostsNetworkCard',
                'InstanceCharge'
            ])
            ->where(['InstanceBasic.id in'=>$id_array])
            ->order([
                'InstanceBasic.create_time' => 'DESC',
            ])->toArray();
        if($res){
            echo json_encode(array('code'=>0,'msg'=>'修改优先级成功','data'=>$data));exit;
        }else{
            echo json_encode(array('code'=>1,'msg'=>'修改优先级失败','data'=>$data));exit;
        }
    }
}