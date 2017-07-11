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

class OrdersGood extends Entity
{
	/**
	 * 把计费周期转换成秒数——按时长计费方式用
	 */
	use IntervalToUnitTrait;

    protected function _getUnitText(){
        if($this->_properties['charge_mode'] == 'oneoff') {
            return $this->_properties['units'].'个'.$this->intervalTxt;
        }
        return $this->intervalTxt;
    }


	/**
	 * 获取计费类型
	 * @return [string]
	 */
	protected function _getChargeTxt(){
		if(!isset($this->_properties['charge_mode'])){
			return false;
		}
    	if(!isset($this->_properties['interval'])){
    		return false;
    	}
    	$charge_txt = '';
    	switch ($this->_properties['charge_mode']) {
    		case 'cycle':
    			$charge_txt = '按'.$this->intervalTxt.'计费';
    			break;
    		case 'oneoff':
    			$charge_txt = '一次性计费';
    			break;
    		case 'duration':
    			$charge_txt = '按时长计费';
                break;
    		default:
    			$charge_txt = '一次性计费';
    			break;
    	}

    	return $charge_txt;
    }
    /**
     * 获取商品快照
     * @return [array] [description]
     */
    protected function _getGoodInfo(){
    	if(!isset($this->_properties['goods_snapshot'])){
    		return false;
    	}
    	return json_decode($this->_properties['goods_snapshot']);
    }

    protected function _getMiniIcon(){
        $good_info = $this->_getGoodInfo();
        if(isset($good_info->goods_info->goods[0]->mini_icon)){
            return $good_info->goods_info->goods[0]->mini_icon;
        }else{
            return 'nophoto.jpg';
        }
    }
}

?>