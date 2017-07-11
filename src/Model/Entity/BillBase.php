<?php

/**
 * ==============================================
 * 
 * @author: lan
 * @date: 2016年12月16日 
 * @version: v1.0.0
 * @desc:
 * ==============================================
 **/

namespace App\Model\Entity;
use Cake\ORM\Entity;
use Cake\Core\Configure;

class BillBase extends Entity
{
	protected $_virtual = ['charge_type_txt','unit','interval_txt','charge_unit_txt'];
	/**
	 * 把计费周期转换成秒数——按时长计费方式用
	 */
	use IntervalToUnitTrait;

	/**
	 * 获取账单的计费方式
	 * @return [type] [description]
	 */
	protected function _getChargeTypeTxt(){
		switch ($this->_properties['charge_type']) {
			case Configure::read('charge_type.day'):
				return '按天计费';
				break;
			case Configure::read('charge_type.month'):
				return '按月计费';
				break;
			case Configure::read('charge_type.year'):
				return '按年计费';
				break;
			case Configure::read('charge_type.duration'):
				return '按时长计费';
				break;
			case Configure::read('charge_type.order'):
				return '包月计费';
				break;
			default:
				return '按月计费';
				break;
		}
	}

	public function _getChargeUnitTxt(){
		return '(元/'.$this->intervalTxt.')';
	}
}

?>