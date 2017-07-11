<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2016/1/5
 * Time: 14:42
 */

namespace App\Model\Table;


class ServiceTypeTable extends SobeyTable{
	public function initialize(array $config)
    {
        parent::initialize($config);
	    $this->belongsTo('ServiceList', [
            //'className' => 'Publishing.Authors',
            'foreignKey' => 'type_id',
        ]);

        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
        ]);

        $this->belongsTo('ChargeTemplate', [
            'foreignKey' => 'template_id',
        ]);
    }


}