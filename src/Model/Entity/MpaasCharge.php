<?php

/**
 * ==============================================
 * 
 * @author: lan
 * @date: 2016年12月19日 
 * @version: v1.0.0
 * @desc:
 * ==============================================
 **/

namespace App\Model\Entity;
use Cake\ORM\Entity;
use Cake\Core\Configure;

class MpaasCharge extends Entity
{
	protected $_virtual = ['unit','interval_txt'];
	/**
	 * 把计费周期转换成秒数——按时长计费方式用
	 */
	use IntervalToUnitTrait;

}

?>