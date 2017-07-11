<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/8
 * Time: 16:08
 */
namespace App\Model\Table;

class DepartmentSubnetTable extends SobeyTable{

	public function initialize(array $config)
    {
        parent::initialize($config);
        $this->belongsTo('Departments', [
            // //'className' => 'Publishing.Authors',
            'className' => 'Departments',
            'foreignKey' => 'dept_id',
        ]);
        $this->belongsTo('InstanceBasic', [
            // //'className' => 'Publishing.Authors',
            'className' => 'InstanceBasic',
            'foreignKey' => 'subnet_id',
        ]);
    }
}