<?php
namespace App\Controller\Api;
use App\Controller\SobeyController;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Core\Configure;

class ChargeInstanceController extends SobeyController {
	//接口属性
	private $_data = null;
	private $_error = null;
	private $_serialize = array('code', 'msg', 'data');
	private $_code = 0;
	private $_msg = "";
	//数据库链接
	private $_db_conn = null;

	private $_is_auth = false;

	protected $_sources = array("sobey", 'DaYang', 'ArcSoft');

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

		//获取参数
		$this->_data = $this->_getData();

		//获取接口中的账户信息，大部分接口需要验证
		$this->_db_conn = ConnectionManager::get('default');
	}

	/**
	 * 通用计费规则
	 * 包括除桌面外的实例计费
	 */
	public function run() {
		//
		$sql = 'select * from cp_instance_charge where next <' . time();
		$result = $this->_db_conn->execute($sql)->fetchall(2);

		foreach ($result as $key => $value) {
			$this->generateBill($value);
		}
		$code = "0000";
		$this->set(compact(array_values($this->_serialize)));
		$this->set('_serialize', $this->_serialize);
	}

	public function generateBill($ins) {
		if ($ins['next'] == "" || $ins['next'] == 0) {
			$ins['next'] = $ins['begin'];
		}
		/**如果是主机**/
		if ($ins['instance_type'] == "hosts") {
			//生成硬件费用
			$sql = "select h.template_id,e.basic_id,b.name,d.id as did,d.dept_code,d.name as dept_name,a.username,t.template_name,t.charge_expression" . $ins["charge_type"] . " as expression from cp_host_extend as e left join cp_set_hardware as h on e.type = h.set_code left join cp_charge_template as t on t.id = h.template_id left join cp_instance_basic as b on b.id = e.basic_id left join cp_accounts as a on a.id = b.create_by left join cp_departments as d on d.id = b.department_id where e.basic_id = " . $ins['basic_id'];
			debug($sql);exit;
			$result = $this->_db_conn->execute($sql)->fetchall(2);
			$result = $result[0];
			$num = $this->_cal($result['expression'], 1);
			$data = array(
				'billing_date' => date("Y-m-d", $ins['next']),
				'charge_body' => $result['name'] . '硬件套餐',
				'user_name' => $result['username'],
				'device_id' => $result['basic_id'],
				'dept_code' => $result['dept_code'],
				'dept_name' => $result['dept_name'],
				'charge_name' => $result['template_name'],
				'daily_cost' => $num,
				'type_id' => 0,
				'charge_type' => $ins['charge_type'],
				'note' => '',
				'billing_y' => date("Y", $ins['next']),
				'billing_m' => date("m", $ins['next']),
				'billing_d' => date("d", $ins['next']),
				'device_name' => $result['name'],
				'modified_time' => '',
				'service_name' => '',
				'dept_id' => $result['did'],
			);
			$this->saveBill($data);

			//镜像费用
			$sql = "select h.template_id,e.basic_id,b.name,d.id as did,d.dept_code,d.name as dept_name,a.username,t.template_name,t.charge_expression" . $ins["charge_type"] . " as expression from cp_host_extend as e left join cp_imagelist as h on e.image_code = h.image_code left join cp_charge_template as t on t.id = h.template_id left join cp_instance_basic as b on b.id = e.basic_id left join cp_accounts as a on a.id = b.create_by left join cp_departments as d on d.id = b.department_id where e.basic_id = " . $ins['basic_id'];
			$result = $this->_db_conn->execute($sql)->fetchall(2);
			$result = $result[0];
			$num = $this->_cal($result['expression'], 1);
			$data = array(
				'billing_date' => date("Y-m-d", $ins['next']),
				'charge_body' => $result['name'] . '镜像',
				'user_name' => $result['username'],
				'device_id' => $result['basic_id'],
				'dept_code' => $result['dept_code'],
				'dept_name' => $result['dept_name'],
				'charge_name' => $result['template_name'],
				'daily_cost' => $num,
				'type_id' => 0,
				'charge_type' => $ins['charge_type'],
				'note' => '',
				'billing_y' => date("Y", $ins['next']),
				'billing_m' => date("m", $ins['next']),
				'billing_d' => date("d", $ins['next']),
				'device_name' => $result['name'],
				'modified_time' => '',
				'service_name' => '',
				'dept_id' => $result['did'],
			);
			$this->saveBill($data);
		} else {
			$sql = "select t.id as template_id,t.template_name,b.name,d.id as did,d.dept_code,d.name as dept_name,a.username,t.charge_expression" . $ins["charge_type"] . " as expression  from cp_instance_basic as b left join  cp_accounts as a on a.id = b.create_by left join cp_departments as d on d.id = b.department_id left join cp_instance_charge as c on c.basic_id = b.id left join cp_charge_template as t on t.tag = c.instance_type where b.id = " . $ins['basic_id'];
			$result = $this->_db_conn->execute($sql)->fetchall(2);
			$result = $result[0];
			$n = 0;

			if ($ins['instance_type'] == "eip") {
				$sql = 'select bandwidth from cp_eip_extend where basic_id = ' . $ins['basic_id'];
				$r = $this->_db_conn->execute($sql)->fetchall(2);
				$n = $r[0]['bandwidth'];
			}
			if ($ins['instance_type'] == "disks") {
				$sql = 'select capacity from cp_disks_metadata where disks_id = ' . $ins['basic_id'];
				$r = $this->_db_conn->execute($sql)->fetchall(2);
				$n = $r[0]['capacity'];
			}

			$num = $this->_cal($result['expression'], 1, $n);

			$data = array(
				'billing_date' => date("Y-m-d", $ins['next']),
				'charge_body' => $result['name'],
				'user_name' => $result['username'],
				'device_id' => $ins['basic_id'],
				'dept_code' => $result['dept_code'],
				'dept_name' => $result['dept_name'],
				'charge_name' => $result['template_name'],
				'daily_cost' => $num,
				'type_id' => 0,
				'charge_type' => $ins['charge_type'],
				'note' => '',
				'billing_y' => date("Y", $ins['next']),
				'billing_m' => date("m", $ins['next']),
				'billing_d' => date("d", $ins['next']),
				'device_name' => $result['name'],
				'modified_time' => '',
				'service_name' => '',
				'dept_id' => $result['did'],
			);
			$this->saveBill($data);

		}

		$this->setNextTime($ins);
	}

	public function setNextTime($ins) {
		//1462302121
		$nextTime = "";
		if ($ins["charge_type"] == 1) {
			$nextTime = strtotime("+1 day", $ins['next']);
		} else if ($ins['charge_type'] == 2) {
			$nextTime = strtotime("+1 month", $ins['next']);
		} else if ($ins['charge_type'] == 3) {
			//$nextTime = strtotime("+1 month", $ins['next']);
		} else if ($ins['charge_type'] == 4) {
			$nextTime = strtotime("+1 year", $ins['next']);
		}
		//设置下次计费是时间
		$sql = 'update cp_instance_charge set next="' . $nextTime . '" where id=' . $ins['id'];
		$this->_db_conn->execute($sql);
	}

	public function saveBill($data) {
		$table = TableRegistry::get('cpChargeDaily');
		$bill = $table->newEntity();
		$bill = $table->patchEntity($bill, $data);
		return $table->save($bill);
	}

	/**
	 * 从http报文中获取参数
	 */
	protected function _getData() {
		$data = $this->request->data ? $this->request->data : file_get_contents('php://input', 'r');
		//处理非x-form的格式
		if (is_string($data)) {
			$data_tmp = json_decode($data, true);
			if (json_last_error() == JSON_ERROR_NONE) {
				$data = $data_tmp;
			}
		}
		return $data;
	}

	/**
	 * 根据公式计算数值
	 * @param string $expression
	 * @param int $t
	 * @throws FatalErrorException
	 */
	protected function _cal($expression = null, $t = 0, $n = 0) {
		$num = 0;
		if (!is_null($expression)) {
			$_expression = str_replace('T', $t, $expression);
			$_expression = str_replace('N', $n, $_expression);
			$count = null;
			try {
				@eval('$num = ' . $_expression . ';');
			} catch (\Exception $e) {
				throw new FatalErrorException($e->getMessage(), $e->getCode());
			}
		}

		return round($num, 2);
	}

	/**
	 *@author wangjincehng
	 *mpaas 计费
	*/

	public function CompletedMediaServiceLists()
	{

// 		$this->_data = json_decode('[{"ColumnCode":"zjxw","ProgramName":"习近平会见美国总统奥巴马","Duration":"0:02:21","VendorCode":"Sobey","ConsumptionSubjects":"Transcoding","SponsorCode":"lbgSN-0088","DepartmentCode":"新蓝网","BeginTime":"2016-04-01 16:50:46","EndTime":"2016-04-01 16:51:26","ActorName" : "PBC1-RENDER-1","ActorIP":"172.10.10.10","Sources":"sobey"},{"ColumnCode":"zjxw","ProgramName":"中国核安全的发条始终上得紧紧的","Duration":"0:03:21","VendorCode":"Sobey", "ConsumptionSubjects":"Transcoding","SponsorCode":"lbgSN-0088","DepartmentCode":"新蓝网","BeginTime":"2016-04-01 16:50:46","EndTime":"2016-04-01 16:51:26","ActorName" : "PBC1-RENDER-2","ActorIP":"172.10.10.10","Sources":"sobey"}]');

		$this->_serialize = array("Status", "Description", "Exception");
		$Status = "0";
		$Description = "推送信息至CMOP成功";
		$Exception = "";
		
		Log::debug('CompletedMediaServiceLists方法调用参数是：'. json_encode($this->_data));

		$mpaas_detail_account_table = TableRegistry::get("MpaasDetailAccount");
		foreach ($this->_data as $v) {
			$v = (array)$v;
			
			//检查参数
			$para = $this->checkPara(array(
					"ColumnCode",
					"ProgramName",
					"Duration",
					"VendorCode",
					"ConsumptionSubjects",
					"SponsorCode",
					"DepartmentCode",
					"BeginTime",
					"EndTime"
				), $v);
			// debug($para);die;
			if ($para) {
				//查询来源
				if (in_array(strtolower($v["VendorCode"]), $this->_sources)) {
					//获取租户code
					$config_data = Configure::read('column_dept');

					//记录信息
					$data = $mpaas_detail_account_table->newEntity();
					$data->column_code	= $v["ColumnCode"];
					$data->program_name	= $v["ProgramName"];
					$data->duration	= $this->transform($v["Duration"]);
					$data->vendor_code	= $v["VendorCode"];
					$data->consumption_subjects	= $this->consumption_subjects($v["ConsumptionSubjects"]);
					$data->sponsor_code	= $v["SponsorCode"];
					$data->dept_id	= isset($config_data[$v["ColumnCode"]]) ? $config_data[$v["ColumnCode"]] : 0;
					$data->begin_time	= $v["BeginTime"];
					$data->end_time	= $v["EndTime"];
					$data->actor_name= $v["ActorName"];
					$data->actor_ip	= $v["ActorIP"];
					try {
						$mpaas_detail_account_table->save($data);
					} catch (\Exception $e) {
					    Log::error($e->getMessage());
						$Status = "-2";
						$Description = "保存数据失败";
						$Exception = "保存数据失败";
					}
				} else {
					$Status = "-1";
					$Description = "推送消息至CMOP失败，非法数据来源";
					$Exception = "非法数据来源";
				}
			} else {
				$Status = "-1";
				$Description = "推送消息至CMOP失败，传入参数不合法";
				$Exception = "传入参数不合法";
			}
		}	
		
		

		$this->set(compact(array_values($this->_serialize)));
		$this->set('_serialize', $this->_serialize);

		
	}


	protected function transform($Duration){
		$time = split(":", $Duration);
		$m = 0;
		foreach ($time as $key => $value) {
			$m += ((int)$value)*(3600/pow(60,$key));
		}
		return $m;
	}

	protected function consumption_subjects($consumption_subjects) {
		switch ($consumption_subjects) {
			case 'Synthesis':
				return "合成";
				break;
			case 'Transcoding':
				return "转码";
				break;
			case 'TechnicalReview':
				return "技审";
				break;
			case 'Transfer':
				return "迁移";
				break;

			default:
				return "";
				break;
		}
	}

	protected function checkPara($_needed_fileds,$value){
		
        if($_needed_fileds){
            foreach ($_needed_fileds as $_key) {
                if (!isset($value[$_key])) {
                    $lack_fields[] = $_key;
                }
            }
        }
        
        if (empty($lack_fields)) {
        	return true;
        }
        return false;
	}

}