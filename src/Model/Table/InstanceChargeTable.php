<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2016/1/27
 * Time: 11:29
 */

namespace App\Model\Table;


class InstanceChargeTable extends SobeyTable{

	public function initialize(array $config)
    {
        parent::initialize($config);
       	//关联主机计费详情表
        $this->hasMany('InstanceChargeDetail', [
            //'className'     => 'InstanceChargeDetail',
            'foreignKey'    => 'cid',
            // 'conditions'    => 'InstanceChargeDetail.cid = cp_instance_charge.basic_id'
        ]);
    }
	
}