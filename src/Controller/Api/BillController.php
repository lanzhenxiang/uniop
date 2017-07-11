<?php
/**
 * 新计费接口
 *
 * @file: BillController.php
 * @date(2016-12-6)
 * @author: lan
 *
 */
namespace App\Controller\Api;

//use App\Controller\AccountsController;
use App\Auth\CmopPasswordHasher;
use App\Controller\SobeyController;
use Cake\Datasource\ConnectionManager;
use Cake\Error\FatalErrorException;
use Cake\I18n\Time as CakeTime;
use Cake\Log\Log;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\ORM\Entity;

//TODO  交付生产环境时候此类应该继承AppController类，保证接口权限验证

class BillController extends SobeyController {

	//接口属性
	private $_data = null;
	private $_error = null;
	private $_serialize = array('code', 'msg', 'data');
	private $_code = 0;
	private $_msg = "";
	/**
	 * 资源实例
	 * @var [App\Model\Table\InstanceBasicTable]
	 */
	private $_instance_basic_table;

	/**
	 * 账单明细表
	 * @var [App\Model\Table]
	 */
	private $_bill_base_table;

	private $_instance_charge_table;
	/**
	 * 数据库连接
	 * @var 
	 */
	private $_db_conn = null;


	/**
	 * 重写init函数
	 * {@inheritDoc}
	 * @see \App\Controller\AppController::initialize()
	 */
	public function initialize() {
		parent::initialize();
		//修改视图类
		$this->viewClass = 'Json';
		//加载组件
		$this->loadComponent('RequestHandler');

		$this->_db_conn = ConnectionManager::get('default');

		//初始化实例
		$this->_instance_basic_table = TableRegistry::get('InstanceBasic');
		$this->_bill_base_table = TableRegistry::get('BillBase');

	}
	/**
	 * 循环周期计费入口
	 * @return [void] 
	 */
	public function cycleCheckout(){
		$this->_instance_charge_table = TableRegistry::get('InstanceCharge');
		$time = new CakeTime();
		$timestamp_now = $time::now()->toUnixString();
		$charge_lists = $this->_instance_charge_table->find()->contain(['InstanceChargeDetail'])->where(['charge_mode'=>'cycle','begin <'=>$timestamp_now,'price >'=>0])
		->where(function($q) use($timestamp_now) {
			return $q->or_(['next <'=>$timestamp_now,'next is'=>null]);
		})->toArray();
		$detail = TableRegistry::get('InstanceChargeDetail')->find()->contain(['InstanceCharge']);

		if(!empty($charge_lists)){
			try {
				$data = [];
				$msg = '设备';
				$count = 0;
				foreach ($charge_lists as $key => $charge_entity) {
					$method = '_'.$charge_entity->instance_type.'Checkout';
					if(method_exists($this, $method)){

						$extend_entity = $this->$method($charge_entity);
						if($extend_entity instanceof Entity){
							$result = $this->_billBaseSave($charge_entity,$extend_entity);
							if($result === true){
								$count++;
							}
						}
					}
				}
				$data['count'] = $count;
				$msg   .= '完成周期性循环计费';
				$code 	= '0000'; 
			} catch (Exception $e) {
				$msg = $e->getMessage();
				$code = $e->getCode();
			}
		}else{
			$msg = '没有需要计费的数据';
			$code = '0000';
		}

		$this->set(compact(array_values($this->_serialize)));
		$this->set('_serialize', $this->_serialize);
	}

	protected function _getResourceType($instance_type){
		$resource_type = "";
		switch ($instance_type) {
			case 'hosts':
				$resource_type = 'ecs';//云主机类型
				break;
			case 'desktop':
				$resource_type = 'citrix';//云桌面类型
				break;
			default:
				$resource_type = $instance_type;
				break;
		}
		return $resource_type;
	}
	/**
	 * 循环周期计费——主表记录计算保存
	 * @param  [object] $charge_entity [description]
	 * @param  [object] $extend_entity [description]
	 * @return [type]                [description]
	 */
	protected function _billBaseSave($charge_entity,$extend_entity){
		$order_table = 	TableRegistry::get('Orders');
		$resource_type = $this->_getResourceType($charge_entity->instance_type);
		//如果计费资源类型没有定义则不保存
		if($resource_type == ""){
			return false;
		}

        if($charge_entity->instance_type == "mstorage") {
            $basic_info = $this->_getMstorageInfo($charge_entity->basic_id);
        }else if($charge_entity->instance_type == "vbrI"){
            $vbrConnects = TableRegistry::get("VbrConnects");
            $vbrI = $vbrConnects->find()->where(["id"=>$charge_entity->basic_id])->first();
            $basic_info = $this->_getBasicInfo($vbrI->basic_id);
		}else{
            $basic_info = $this->_getBasicInfo($charge_entity->basic_id);
        }

		if(empty($basic_info) || $basic_info == null || empty($basic_info['code'])){
			return false;
		}
		$bill_base_entity = $this->_initBillBaseEntity($charge_entity->billTimestamp);
		$bill_base_entity->resource_type = $resource_type;

		//计费类型
		$bill_base_entity->charge_type	= $charge_entity->billChargeType;
		//购买人信息
		$bill_base_entity->buyer_name 	= $basic_info['buyer_name'];
		$bill_base_entity->buyer_id 	= $basic_info['buyer_id'];
		//租户信息
		$bill_base_entity->department_id 	= $basic_info['department_id'];
		$bill_base_entity->department_name 	= $basic_info['department_name']; 
		//账单计费价格及周期
		$bill_base_entity->price 		= $charge_entity->price;//计费周期成交单价
		$bill_base_entity->market_price = $charge_entity->market_price;//计费周期原价
		$bill_base_entity->interval 	= $charge_entity->interval;//计费周期单位（
		//获取计算金额
		// if($resource_type == 'disks'){ //如果设备是EIP，总价需要单价*带宽
		// 	$bill_base_entity->amount 	= $this->_getDisksAmount($charge_entity->price,$charge_entity->basic_id);
		// }else{
		$bill_base_entity->amount 	= $charge_entity->price;
		// }
		
		$bill_base_entity->order_id = $charge_entity->order_id;
		//账单扩展表主键id
		$bill_base_entity->resource_id 	= $extend_entity->id;

		if($this->_bill_base_table->save($bill_base_entity)){
			//更改计费项目的下次计费时间
			$charge_entity->next = $charge_entity->nextBillTimestamp;
			$this->_instance_charge_table->save($charge_entity);
				
			Log::info('bill_'.$resource_type.':' . json_encode($bill_base_entity));
		}else{
			throw new \Exception("Error Save bill_base_entity", 1);
		}
		return true;
	}

    /**
     * 获取媒体云存储计费信息
     * @param $id
     * @return mixed
     */
	protected  function  _getMstorageInfo($id){
	    $mstorageTable = TableRegistry::get("FicsExtend");
	    $mstorageInfo  = $mstorageTable->find()
            ->select([
                'basic_id'=>'FicsExtend.vol_id',
                'name'=>'FicsExtend.vol_name',
                'code'=>'FicsExtend.vol_name',
                'department_name'=>'dept.name',
                'department_id'=>'dept.id'
                ])
            ->join([
	            "dept"=>[
                    "table"=>'cp_departments',
                    "type" => 'INNER',
                    "conditions"=>"dept.id = FicsExtend.department_id"
                ]
        ])->where(['FicsExtend.vol_id'=>$id])->first();
	    $mstorageInfo->buyer_name = "";
        $mstorageInfo->buyer_id = "";
        return $mstorageInfo;
    }

	/**
	 * 获取硬盘的计费总金额
	 * @param  [type] $price    [description]
	 * @param  [type] $basic_id [description]
	 * @return [type]           [description]
	 */
	public function _getDisksAmount($price,$basic_id)
	{
		$disk = $this->_instance_basic_table->find()->join([
				'disks'=>[
					"table"=>"cp_disks_metadata",
					"type"	=>"inner",
					"conditions" => "disks.disks_id = InstanceBasic.id"
				]
			])->select(['capacity'=>'disks.capacity'])->where(['InstanceBasic.id'=>$basic_id])->first();

		return $disk['capacity'] * $price;
	}

	/**
	 * 按时长计费入口
	 * @return [void] 
	 */
	public function durationCheckout(){
		//桌面计费
		$this->citrixDurationCheckOut();
		//mpaas计费
		$this->mpaasDurationCheckOut();
	}

	public function HiveStorageCycleCheckOut()
	{
		$hiveStatTable = TableRegistry::get('HiveStatistics');
		$time = new CakeTime();
		// $endDate = $time->startOfMonth()->toDateTimeString();
		// $startDate = $time->subMonth()->toDateTimeString();
		$startDate = $time->startOfMonth()->toDateTimeString();
		$endDate = $time->addMonth()->toDateTimeString();

		$chargeTable 	= TableRegistry::get("ChargeExtend");
		$billBaseTable  = TableRegistry::get("BillBase");
		$departments    = TableRegistry::get("Departments");
		$hivestatItemsTable = TableRegistry::get("HiveStatisticsItems");

		$chargeEntity 	= $chargeTable->find()->where(['charge_object'=>"hive"])->first();
		$query = $hiveStatTable->getHiveStatisticsChargeList($startDate,$endDate);
		$lists = $query->toArray();
		if(!empty($lists) && is_array($lists)){
			try {
				foreach($lists as $key=>$value){
					$bill_base_entity = $this->_initBillBaseEntity($value['create_time']);
					$bill_base_entity->resource_type = "hive";

					//计费类型
					$bill_base_entity->charge_type	= 2;
					//购买人信息
					// $bill_base_entity->buyer_name 	= $basic_info['buyer_name'];
					// $bill_base_entity->buyer_id 	= $basic_info['buyer_id'];
					$department = $departments->findByDeptCode($value['value'])->first();
					//租户信息
					$bill_base_entity->department_id 	= $department['id'];
					$bill_base_entity->department_name 	= $department['name']; 
					//账单计费价格及周期
					$bill_base_entity->price 		= $chargeEntity->monthly_price;//计费周期成交单价
					$bill_base_entity->market_price = $chargeEntity->monthly_price;//计费周期原价
					$bill_base_entity->interval 	= "M";//计费周期单位（
					//获取计算金额
					//每个月每个G单价*GB=总价
					$bill_base_entity->amount 	= $chargeEntity->monthly_price * $value->GB;
					//账单扩展表主键id
					$bill_base_entity->resource_id 	= $value->id;
					if($this->_bill_base_table->save($bill_base_entity) 
						&& $hivestatItemsTable->hiveItemsCharged($value["id"])){
					}else{
						throw new \Exception("hive计费信息保存错误", 1);
					}
				}
				$msg = "完成HIVE周期性计费";
				$code = "0000";
			} catch (\Exception $e) {
				$msg = $e->getMessage();
				$code = $e->getCode();
			}
		}else{
			$msg = '没有需要计费的数据';
			$code = '0000';
		}

		$this->set(compact(array_values($this->_serialize)));
		$this->set('_serialize', $this->_serialize);
	}

	/**
	 * 一次性计费-b/s工具入口
	 * b/s工具计费--订单审核结束调用直接计费
	 *
	 * 接口参数
	 * $data =[
			'name' =>'索贝JOVE快编',
			'duration' =>'1',
			'buyer_id' =>67,
			'buyer_name'=>'fox',
			'order_date' =>'2016-12-1',
			'department_id' =>67,
			'department_name'=>'fox',
			'market_price'=>122,//原价
			'price'		=>100,//实际成交价
			'resource_type'=>'bs' | 'rds' 
		];
	 *
	 */
	public function serviceCheckOut(){
		$data = $this->request->data;
		
		try {
			$count = 0;
			$timeObj = new CakeTime();	
			
			//获取计费的开始时间和结束时间
			$start_date  	= $timeObj->i18nFormat('yyyy-MM-dd');
			$end_date  		= $timeObj->addMonths($data['duration'])->i18nFormat('yyyy-MM-dd');

			$bill_bs_table 	= TableRegistry::get("BillService");
			$bill_bs_entity = $bill_bs_table->newEntity();
			$bill_bs_entity->duration 		= $data['duration'];
			$bill_bs_entity->start_date 	= $start_date;
			$bill_bs_entity->end_date 		= $end_date;
			$bill_bs_entity->order_date 	= $data['order_date'];
			$bill_bs_entity->name 			= $data['name'];
			$result = $bill_bs_table->save($bill_bs_entity);

			$bill_base_entity = $this->_initBillBaseEntity($timeObj::now()->toUnixString());
			$bill_base_entity->resource_type = $data['resource_type'];

			//按包月计费
			$bill_base_entity->charge_type	= Configure::read('charge_type.order');
				
			$bill_base_entity->buyer_name 	= $data['buyer_name'];
			$bill_base_entity->buyer_id 	= $data['buyer_id'];

			$bill_base_entity->department_id 	= $data['department_id'];
			$bill_base_entity->department_name 	= $data['department_name']; 

			$bill_base_entity->price 		= $data['price'];//计费周期单价
			$bill_base_entity->market_price = $data['market_price'];
			$bill_base_entity->interval 	= 'M';//计费周期单位

			$bill_base_entity->num 			= $data['duration'];
			$bill_base_entity->order_id 	= $data['order_id'];
			//获取计算金额
			$bill_base_entity->amount = $bill_base_entity->price;

			$bill_base_entity->resource_id 	= $result['id'];
			if($this->_bill_base_table->save($bill_base_entity)){
				Log::info('bill_service:' . json_encode($bill_base_entity));
				$count++;
			}else{
				throw new \Exception("Error Save bill_base_entity", 1);
			}
			if ($count > 0) {
				$code = '0000';
				$msg = '共计算了' . $count . '条数据';
			} else {
				$code = '0000';
				$msg = '无需要计算数据(' . $count . ')';
			}
		} catch (\Exception $e) {
			$msg = $e->getMessage();
			$code = $e->getCode();
		}
		
		$this->set(compact(array_values($this->_serialize)));
		$this->set('_serialize', $this->_serialize);
	}

	/**
	 * mpaas计费
	 */
	public function mpaasDurationCheckOut(){
		$mpaas_table = TableRegistry::get('MpaasDetailAccount');

		$mpaas_lists = $mpaas_table->find()->where(['is_compute'=>0])->limit(100)->toArray();
		try {
			$count = 0;
			foreach ($mpaas_lists as $key => $mpaas) {
				$bill_base_entity = $this->_initBillBaseEntity($mpaas['end_time']->toUnixString());
				$bill_base_entity->resource_type = 'mpaas';

				//按业务时长计费
				$bill_base_entity->charge_type	= Configure::read('charge_type.duration');
				$department_info = $this->_getDepartmentInfo($mpaas['dept_id']);
				
				$bill_base_entity->buyer_name 	= $department_info['id'];
				$bill_base_entity->buyer_id 	= $department_info['name'];
				

				$bill_base_entity->department_id 	= $department_info['id'];
				$bill_base_entity->department_name 	= $department_info['name']; 

				$mpaas_charge = $this->_getMpaasCharge($mpaas);
				if($mpaas_charge == null){
					continue;
				}
				$bill_base_entity->price 		= $mpaas_charge->price;//计费周期单价
				$bill_base_entity->market_price = $mpaas_charge->market_price;//计费周期单价
				$bill_base_entity->interval 	= $mpaas_charge->interval;//计费周期单位
				$bill_base_entity->order_id		= $mpaas_charge->order_id;//订单id
				//获取计算金额
				$bill_base_entity->amount = $this->_getSubAmountByDuration($mpaas['duration'],$mpaas_charge);

				$bill_base_entity->charge_detail = json_encode($mpaas_charge);

				$bill_base_entity->resource_id 	= $mpaas['id'];
				if($this->_bill_base_table->save($bill_base_entity)){
					Log::info('bill_citrix:' . json_encode($bill_base_entity));
					$count++;
					$mpaas->is_compute = 1;//更改计费状态
					$mpaas_table->save($mpaas);
				}else{
					throw new \Exception("Error Save bill_base_entity", 1);
				}
			}
			if ($count > 0) {
				$code = '0000';
				$msg = '共计算了' . $count . '条数据';
			} else {
				$code = '0000';
				$msg = '无需要计算数据(' . $count . ')';
			}
		} catch (\Exception $e) {
			$msg = $e->getMessage();
			$code = $e->getCode();
		}
		

		$this->set(compact(array_values($this->_serialize)));
		$this->set('_serialize', $this->_serialize);
	}

	/**
	 * 获取部门信息
	 * @param  [type] $department_code [description]
	 * @return [type]                  [description]
	 */
	protected	function _getDepartmentInfo($dept_id){
		$department_table = TableRegistry::get('departments');
		return $department_table->find()->where(['id'=>$dept_id])->first();
	}

	/**
	 * 获取mpaas的单价和计价周期
	 * @param  [object] $mpaas_entity     [mpaas服务待计费entity]
	 * @return [object]                	  [mpaas计费价entity]
	 */
	protected function _getMpaasCharge($mpaas_entity){
		$mpaas_charge_table = TableRegistry::get("MpaasCharge");

		$charge_entity =  $mpaas_charge_table->find()->where([
				'vendor_code'			=> $mpaas_entity->vendor_code,//服务商code
				'consumption_subjects' 	=> $mpaas_entity->consumption_subjects,//服务类型
				'dept_id'				=> $mpaas_entity->dept_id//租户code
			])->first();

		// if($charge_entity == null){
		// 	throw new Exception("Error no mpaas charge entity", 1);
		// }
		return $charge_entity;
	}

	/**
	 * 硬盘计费
	 * @return [type] [description]
	 */
	protected function _disksCheckout($charge_entity)
	{
		return $this->_hostsCheckout($charge_entity);
	}

    /**
     * 媒体云存储计费
     * @return [type] [description]
     */
    protected function _mstorageCheckout($charge_entity)
    {
        return $this->_hostsCheckout($charge_entity);
    }

	protected function _vfwCheckout($charge_entity)
	{
		return $this->_hostsCheckout($charge_entity);
	}

	public function _elbCheckout($charge_entity)
	{
		return $this->_hostsCheckout($charge_entity);
	}

	public function _vpcCheckout($charge_entity)
	{
		return $this->_hostsCheckout($charge_entity);
	}

    public function _firewallCheckout($charge_entity)
    {
        return $this->_hostsCheckout($charge_entity);
    }


	/**
	 * eip计费
	 * @return [type] [description]
	 */
	protected function _eipCheckout($charge_entity)
	{
		$basic_info = $this->_getBasicInfo($charge_entity->basic_id);
		if(empty($basic_info) || $basic_info == null || empty($basic_info['code'])){
			return false;
		}
		$order_table = 	TableRegistry::get('Orders');
		//$instance_charge_detail = TableRegistry::get('InstanceChargeDetail');
		$order_entity = $order_table->findById($charge_entity->order_id)->first();
		$order_time = empty($order_entity) ? 0 : $order_entity->create_time;
		$order_date = date('Y-m-d',$order_time);//订单时间
		$start_date = date('Y-m-d',$charge_entity->begin);	//设备创建时间
		//$details = $instance_charge_detail->findByCid($charge_entity->basic_id)->toArray();
		$charge_detail = json_encode($charge_entity->instance_charge_detail);
		$eip_extend = TableRegistry::get('EipExtend');
		$eip = $eip_extend->find()->select(['bandwidth'])->where(['basic_id'=>$charge_entity->basic_id])->first();
		
		if(null == $eip || $eip->bandwidth <=0 ){
			return false;
		}
		$data =[
			'name'	=>$basic_info->name,
			'code'	=>$basic_info->code,
			'order_date'		=>$order_date,
			'start_date'		=>$start_date,
			'charge_detail'		=>$charge_detail,
			//'bandwidth' =>$eip->bandwidth
		];
		return $this->_billEcsSave($data);
	}


    /**
     * 边界路由器接口计费
     */

    protected function _vbrICheckout($chargeEntity)
    {
        $vbrConnects = TableRegistry::get('VbrConnects');
        $billVbrI = TableRegistry::get("BillVbri");
        $vbrI = $vbrConnects->find()->where(["id"=>$chargeEntity->basic_id])->first();
        if(empty($vbrI) || $vbrI == null || empty($vbrI['initiatingSideRouterInterfaceCode'])){
            return false;
        }
        $data =[
            'name'	=>$vbrI->customName,
            'routerCode'	=>$vbrI->routerCode,
            'initiatingSideRouterInterfaceCode'		=>$vbrI->initiatingSideRouterInterfaceCode,
            'acceptingSideRouterInterfaceCode'		=>$vbrI->acceptingSideRouterInterfaceCode,
            'spec'		=>$vbrI->spec
        ];
        $entity = $billVbrI->newEntity();
        $patchEntity = $billVbrI->patchEntity($entity,$data);
        return $billVbrI->save($patchEntity);
    }

	/**
	 * 主机计费
	 * @param  [object] $charge_entity [description]
	 * @return [object | false]                [description]
	 */
	protected function _hostsCheckout($charge_entity){
        if($charge_entity->instance_type == "mstorage"){
            $basic_info = $this->_getMstorageInfo($charge_entity->basic_id);
        }else{
            $basic_info = $this->_getBasicInfo($charge_entity->basic_id);
        }
		if(empty($basic_info) || $basic_info == null || empty($basic_info['code'])){
			return false;
		}
		$order_table = 	TableRegistry::get('Orders');
		//$instance_charge_detail = TableRegistry::get('InstanceChargeDetail');
		$order_entity = $order_table->findById($charge_entity->order_id)->first();
		$order_time = empty($order_entity) ? 0 : $order_entity->create_time;
		$order_date = date('Y-m-d',$order_time);//订单时间
		$start_date = date('Y-m-d',$charge_entity->begin);	//设备创建时间
		//$details = $instance_charge_detail->findByCid($charge_entity->basic_id)->toArray();
		$charge_detail = json_encode($charge_entity->instance_charge_detail);
		$data =[
			'name'	=>$basic_info->name,
			'code'	=>$basic_info->code,
			'order_date'		=>$order_date,
			'start_date'		=>$start_date,
			'charge_detail'		=>$charge_detail
		];
		return $this->_billEcsSave($data);
	}

	/**
	 * 桌面计费，保存桌面扩展信息
	 * @param  [type] $charge_entity [description]
	 * @return [type]                [description]
	 */
	protected function _desktopCheckout($charge_entity){
		
		$basic_info = $this->_getBasicInfo($charge_entity->basic_id);
		if(empty($basic_info) || $basic_info == null || empty($basic_info['code'])){
			return false;
		}
		return  $this->_billCitrixSave($basic_info);
	}

	/**
	 * 保存循环周期计费的桌面账单信息
	 * @param  [object] $basic_info 
	 * @return [entity]             
	 */
	protected function _billCitrixSave($basic_info){

		$bill_citrix_table = TableRegistry::get('BillCitrix');
		$data =[
			'basic_id'	=>$basic_info->basic_id,
			'status'	=>1,
			'duration'	=>1,
			'name'		=>$basic_info->name,
			'loginname'	=>$basic_info->buyer_name
		];
		return $this->_save($bill_citrix_table,$data);
	}
	/**
	 * 保存循环周期计费的主机账单信息
	 * @param  [object] $basic_info 
	 * @return [entity]             
	 */
	protected function _billEcsSave($data){
		$bill_ecs_table = TableRegistry::get('BillEcs');
		return $this->_save($bill_ecs_table,$data);
	}
	/**
	 * 保存数据通用方法
	 * @param  [object] $table [description]
	 * @param  [array] $data  [description]
	 * @return [object]        [description]
	 */
	protected function _save($table,$data){
		$entity = $table->newEntity();
		$entity	= $table->patchEntity($entity,$data);
		return $table->save($entity);
	}

	/**
	 * 桌面工具citrix按时长计费
	 */
	public function citrixDurationCheckOut(){
		$bill_citrix_table = TableRegistry::get('BillCitrix');

		$citrix_lists = $bill_citrix_table->find()->where(['status'=>0])->limit(100);
		try {
			$count = 0;
			foreach ($citrix_lists as $key => $citrix) {
				$basic_info = $this->_getBasicInfo($citrix['basic_id']);
				if(empty($basic_info) || $basic_info == null){
					$bill_citrix_table->delete($citrix);
					continue;
				}
				//获取桌面单价，单位
				$price_entity 	= $this->_getCitrixPrice($citrix['basic_id']);
				if(null == $price_entity){//如果当前设备不属于按时长计费则不生成订单
					$bill_citrix_table->delete($citrix);
					continue;
				}

				$bill_base_entity = $this->_initBillBaseEntity($citrix['logintime']);
				$bill_base_entity->resource_type = 'citrix';

				//按业务时长计费
				$bill_base_entity->charge_type	= Configure::read('charge_type.duration');
				$bill_base_entity->resource_id 	= $citrix['id'];
				
				$bill_base_entity->buyer_name 	= $basic_info['buyer_name'];
				$bill_base_entity->buyer_id 	= $basic_info['buyer_id'];

				$bill_base_entity->department_id 	= $basic_info['department_id'];
				$bill_base_entity->department_name 	= $basic_info['department_name']; 
				
				$bill_base_entity->price 		= $price_entity->price;//计费周期单价
				$bill_base_entity->interval 	= $price_entity->interval;//计费周期单位（
				//获取计算金额
				$bill_base_entity->amount = $this->_getSubAmountByDuration($citrix['duration'],$price_entity);

				$bill_base_entity->charge_detail = json_encode($price_entity->toArray());

				if($this->_bill_base_table->save($bill_base_entity)){
					Log::info('bill_citrix:' . json_encode($bill_base_entity));
					$count++;
					$citrix->status = 1;//更改计费状态
					$bill_citrix_table->save($citrix);
				}else{
					throw new \Exception("Error Save bill_base_entity", 1);
				}
			}
			if ($count > 0) {
				$code = '0000';
				$msg = '共计算了' . $count . '条数据';
			} else {
				$code = '0000';
				$msg = '无需要计算数据(' . $count . ')';
			}
		} catch (\Exception $e) {
			$msg = $e->getMessage();
			$code = $e->getCode();
		}
		
		$this->set(compact(array_values($this->_serialize)));
		$this->set('_serialize', $this->_serialize);
	}

	/**
	 * 初始化计费主表
	 * @param  [timestamp] $bill_time [description]
	 * @return [/cake/oRM/]            [description]
	 */
	protected function _initBillBaseEntity($bill_time){
		$bill_base_entity = $this->_bill_base_table->newEntity();
		$bill_base_entity->create_time = time();//账单生成时间
		
		$bill_base_entity->year 	= date('Y', $bill_time);
		$bill_base_entity->month 	= date('m', $bill_time);
		$bill_base_entity->day 		= date('d', $bill_time);

		$bill_base_entity->bill_date = date('Y-m-d H:i:s',$bill_time);
		return $bill_base_entity;	
	}

	protected function _getBasicInfo($basic_id){
		$department = $this->_instance_basic_table->find()->join([
				'c_d'=>[
					'table'=>'cp_departments',
					'type' =>'LEFT',
					'conditions'=>'c_d.id = InstanceBasic.department_id'
				],
				'c_a'=>[
					'table'=>'cp_accounts',
					'type' =>'LEFT',
					'conditions' =>'c_a.id = InstanceBasic.create_by'
				]
			])->select(['basic_id'=>'InstanceBasic.id','name'=>'InstanceBasic.name','code'=>'InstanceBasic.code','department_name'=>'c_d.name','department_id'=>'c_d.id','buyer_name'=>'c_a.username','buyer_id'=>'c_a.id'])->where(['InstanceBasic.id'=>$basic_id,'InstanceBasic.code is not'=>null])->first();
		return $department;
	}

	/**
	 * 获取桌面工具计费单价
	 * @param  [int] $basic_id 
	 * @return [array]           
	 */
	protected function _getCitrixPrice($basic_id){
		$instance_charge_table = TableRegistry::get('InstanceCharge');
		return $instance_charge_table->find()->where(['basic_id'=>$basic_id,'charge_mode'=>'duration','price >'=>0])->first();
	}


	/**
	 * [_getSubAmountByDuration 获取按照使用时长收费的小计金额]
	 * @param  [int] $duration 		[时长]
	 * @param  [object] $price_entity[计费实例]
	 * @return [float]         计费总金额
	 */
	protected function _getSubAmountByDuration($duration,$price_entity){
		
		return ceil($duration / $price_entity->unit) * $price_entity->price;
	}
}