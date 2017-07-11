<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/8
 * Time: 16:08
 */
namespace App\Model\Table;

class AccountsTable extends SobeyTable{
	public function initialize(array $config)
    {
        parent::initialize($config);
        $this->belongsTo('Departments', [
            // 'className' => 'Publishing.Authors',
            'foreignKey' => 'department_id'
        ]);
        $this->hasMany('InstanceLogs',[
            'className' => 'InstanceLogs',
            'foreignKey'=>'user_id'
        ]);
    }
}