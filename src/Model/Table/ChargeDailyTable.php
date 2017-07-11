<?php
/**
* 文件用途描述
* 
* @file: ChargeDailyTable.php
* @date: 2016年2月17日 下午3:04:19
* @author: xingshanghe
* @email: xingshanghe@icloud.com
* @copyright poplus.com
*
*/

namespace App\Model\Table;

use App\Model\Table\SobeyTable;

class ChargeDailyTable extends SobeyTable
{
    /**
     * @func: 
     * @param:
     * @date: 2015年9月11日 上午11:30:55
     * @author: shrimp liao
     * @return: null
     */
    public function initialize(array $config)
    {
    	parent::initialize($config);

    	$this->belongsTo('InstanceBasic',[
    		'className' => 'InstanceBasic',
    		'foreignKey'=>'device_id'
    		]);
    	$this->belongsTo('ServiceType',[
    		'className' => 'ServiceType',
    		'foreignKey'=>'type_id'
    		]);

    }
}