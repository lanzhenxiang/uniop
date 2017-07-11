<?php
/**
 * ==============================================
 * 
 * @author: chen qiang
 * @date: 2016年12月16日 
 * @version: v1.0.0
 * @desc:
 * ==============================================
 **/

namespace App\Model\Entity;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

class GoodsVersionEntity extends Entity
{
	public function getDetails(){
		$detailM = TableRegistry::get('GoodsVersionDetail');
		$list = $detailM->find()->where(array('vid'=>$this->id));

		$details = array();
		foreach ($list as $key => $value) {
			$details[$value['key']] = $value['value'];
		}
		return $details;

	}

	public function getPrices(){
		$where = array();
		if($this->type=="citrix" || $this->type == "citrix_public"){
			$param = $this->getDetails();
			$where['sid'] = $param['specid'];
		}else{
			$where['vid'] = $this->id;
		}
		$list = TableRegistry::get('GoodsVersionPrice')->find()->where($where)->select();
		return $list;
	}
}

?>