<?php

/**
* 其他资源类型计费
* 
* @file: ChargeExtendTable.php
* @author: lan
* @copyright poplus.com
*
*/

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use App\Model\Table\SobeyTable;
class ChargeExtendTable extends SobeyTable
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->belongsTo('Agent', [
            // 'className' => 'Publishing.Authors',
            'foreignKey' => 'agent_id'
        ]);
    }

    public function buildRules(RulesChecker $rules)
	{
	    // Add a rule that is applied for create and update operations
	    // 同一计费对象，同一厂商只允许存在一条记录
	    $rules->add($rules->isUnique(
		    ['charge_object', 'agent_id'],
		    '同一计费对象，同一厂商只允许存在一条记录.'
		));

	    return $rules;
	}


}