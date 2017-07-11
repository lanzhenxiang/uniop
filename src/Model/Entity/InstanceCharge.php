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
use Cake\I18n\Time as CakeTime;
use Cake\Core\Configure;

class InstanceCharge extends Entity
{

	protected $_virtual = ['bill_timestamp','bill_charge_type','next_bill_timestamp','unit','interval_txt'];

	/**
	 * 把计费周期转换成秒数——按时长计费方式用
	 */
	use IntervalToUnitTrait;

	/**
	 * 获取循环周期计费的账单时间戳
	 * @return [type] [description]
	 */
	protected function _getBillTimestamp(){
		if($this->_properties['next'] == 0){
			return $this->_properties['begin'];
		}else{
			return $this->_properties['next'];
		}
	}

	/**
	 * 获取循环计费周期的下次账单时间戳
	 * @return [int] [时间戳]
	 */
	protected function _getNextBillTimestamp(){
		//当前账单时间
		$current_bill_time = new CakeTime($this->_getBillTimestamp());
		
		if($this->_properties['interval'] == 'D'){
			
			//如果周期时间间隔为天，则下次计费时间+1天
			$current_bill_time->addDay();
		}else if($this->_properties['interval'] == "M"){
			
			//如果周期时间间隔为月,则下次计费时间+1个月
			$current_bill_time->addMonth();
		}else if($this->_properties['interval'] == "Y"){
			
			//如果周期时间间隔为年,则下次计费时间+1年
			$current_bill_time->addYear();
		}
		return $current_bill_time->toUnixString();
	}

	/**
	 * 获取生成账单的计费类型
	 * @return [int] 
	 */
	protected function _getBillChargeType(){
		//按时长计费
		if($this->_properties['charge_mode'] =='duration'){
			return Configure::read('charge_type.duration');
		//按周期循环计费
		}else if($this->_properties['charge_mode'] == "cycle"){
			//周期按天
			if($this->_properties['interval'] == 'D'){
				return Configure::read('charge_type.day');
			//周期按月
			}else if($this->_properties['interval'] == "M"){
				return Configure::read('charge_type.month');
			//周期按年
			}else if($this->_properties['interval'] == "Y"){
				return Configure::read('charge_type.year');
			}
		}
	}

}

?>