<?php
/**
* 文件用途描述
*
* @file: AjaxController.php
* @date: 2016年3月18日 下午2:40:34
* @author: chenqiang
*
*/
namespace App\Controller\Api;
use App\Controller\AppController;
use Cake\ORM\TableRegistry;

class AjaxController extends AppController{

//	function getTenants(){
//		$this->autoRender = false;
//
//		$queryStr = addslashes(@$_GET['queryStr']);
//		if($queryStr != ''){
//		    $where['OR'] = [
//		      ['name'=>$queryStr],
//              ['dept_code'=>$queryStr]
//            ];
//		}
//
//		$pageNumber = (intval(@$_GET['pageNumber']) >0)?intval(@$_GET['pageNumber']):1;
//		$pageSize = (intval(@$_GET['pageSize']) >0)?intval(@$_GET['pageSize']):5;
//		$offset = $pageSize*($pageNumber-1);
//
//		$departments = TableRegistry::get('Departments');
//		$query = $departments->find()->where($where)->offset($offset)->limit($pageSize);
//
//		$list = $query->toArray();
//		$count = $query->count();
//		$return = array(
//				'total'=>$count,
//				'rows'=>$list
//			);
//		echo json_encode($return);
//	}
}