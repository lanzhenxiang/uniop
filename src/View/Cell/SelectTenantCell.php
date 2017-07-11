<?php
/**
* 租户选择 cell包
*
* @file: SelectTenantCell.php
* @date: 2016年12月21日 下午2:37:39
* @author: chenqiang
* @email: small_chen@msn.cn
* @copyright chinamcloud.com
*/

namespace App\View\Cell;
use Cake\View\Cell;
use Cake\ORM\TableRegistry;


class SelectTenantCell extends Cell{


	public function display($formName,$id=0){
		$this->set('formName',$formName);
		$this->set('did',$id);
	}
}

?>