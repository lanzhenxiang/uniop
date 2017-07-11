<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2016/1/5
 * Time: 14:41
 */

namespace App\Controller\Admin;


use App\Controller\AdminController;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Requests as Requests;

class ServiceRulesController extends AdminController{
	public $paginate = [
	'limit' => 15,
	];

    //列表显示
	public function index($name = ''){
		$service_rules = TableRegistry::get('ServiceRules');
		$where = array();
		if ($name) {
			$where['ServiceType.service_name like'] = '%' . $name . '%';
		}
		$data = $service_rules->find('all')->where($where)->contain(['ServiceType']);
		$data=$this->paginate($data);
		$this->set('name',$name);
		$this->set('data',$data);
	}

	public function addedit($id=0){
		$public = new PublicController();
		$service_rules = TableRegistry::get('ServiceRules');
		$service_type = TableRegistry::get('ServiceType');
		if($this->request->is('get')){
			$service_type_data = $service_type->find('all')->toArray();
			$this->set('service_type_data',$service_type_data);
			if($id){
				$data = $service_rules->find('all')->where(['id'=>$id])->first();
				$this->set('data',$data);

			}
		}else{
			$requset = Requests::post(Configure::read('Api.cmop').'/Mpc/checkRules',[],[
				'rules'=>$this->request->data['rule_expression'],
				],[
				'verify'=>false
				]);
			$requset_arr = json_decode($requset->body,true);
			$res = json_encode($requset_arr);
			if($res == 'null'){
				$message = array('code'=>1,'msg'=>'计算规格错误');
				echo json_encode($message);exit();
			}

			$order = $service_rules->newEntity();
			$order = $service_rules->patchEntity($order,$this->request->data);
			$result = $service_rules->save($order);
			if($result){
				$message = array('code'=>0,'msg'=>'操作成功');
				if(empty($this->request->data['id'])){
					$public->adminlog('ServiceRules','添加弹性规则---'.$this->request->data['type_id'].'-'.$this->request->data['rule_expression']);
				}else{
					$public->adminlog('ServiceRules','修改弹性规则---'.$this->request->data['type_id'].'-'.$this->request->data['rule_expression']);
				}
			}
			echo json_encode($message);exit();
		}
	}

    //删除
	public function delete(){
		$public = new PublicController();
		$this->layout = false;
		$message = array('code'=>1,'msg'=>'操作失败');
		if($this->request->data['id']){
			$id=$this->request->data['id'];
			$service_rules = TableRegistry::get('ServiceRules');

			$data = $service_rules->find()->where(['id'=>$id])->first();	
			$res = $service_rules->deleteAll(array('id'=>$id));
			if($res){
				$message = array('code'=>0,'msg'=>'操作成功');
				$public->adminlog('ServiceRules','删除弹性规则---'.$data['type_id'].'-'.$data['rule_expression']);
			}
			echo json_encode($message);exit;
		}
	}

	// //验证
	// public function check(){
	// 	$requset = Requests::post(Configure::read('Api.cmop').'/Mpc/checkRules',[],[
	// 		'rules'=>$this->request->data['text'],
	// 		],[
	// 		'verify'=>false
	// 		]);
	// 	$requset_arr = json_decode($requset->body,true);
	// 	echo json_encode($requset_arr);exit;
	// }
}