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

trait IntervalToUnitTrait{

	/**
	 * 定义计费周期对应的秒数
	 * @var [array]
	 */
	protected $_intervalToUnit=[
		'S' 	=>1,
		'I'		=>60,
		'H'		=>3600,
		'D'		=>86400
	];

	/**
	 * 定义计费周期对应的文本值
	 * @var [array]
	 */
	protected $_intervalToTxt = [
		'S' =>'秒',
		'I' =>'分钟',
		'H' =>'小时',
		'D' =>'天',
		'M' =>'月',
		'Y' =>'年'
	];

	//protected $_virtual = ['unit','interval_txt'];

	/**
	 * 获取用计费周期的计算列
	 * @return [int] [周期秒数]
	 */
	protected function _getUnit()
    {	
    	if(!isset($this->_properties['interval'])){
    		return false;
    	}

    	if(array_key_exists($this->_properties['interval'], $this->_intervalToUnit)){
    		return $this->_intervalToUnit[$this->_properties['interval']];
    	}else{
    		return 1;
    	}
    }

    /**
     * 获取计费周期的翻译值
     * @return [type] [description]
     */
    protected function _getIntervalTxt(){
    	if(!isset($this->_properties['interval'])){
    		return false;
    	}
    	if(array_key_exists($this->_properties['interval'], $this->_intervalToTxt)){
    		return $this->_intervalToTxt[$this->_properties['interval']];
    	}else{
    		return '天';
    	}
    }
}