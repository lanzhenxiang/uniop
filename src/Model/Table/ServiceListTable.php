<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2016/1/6
 * Time: 14:43
 */
namespace App\Model\Table;

class ServiceListTable extends SobeyTable{
	public function initialize(array $config)
    {
        parent::initialize($config);
        $this->hasOne('InstanceBasic',[
            'className' => 'InstanceBasic',
            'foreignKey' => 'id'
        ]);
        $this->belongsTo('ServiceType',[
            'className' => 'ServiceType',
            'bindingKey'=>'type_id',
            'foreignKey'=>'type_id',
            // 'sort'=>['sort_order' => 'ASC']
        ]);
    }
}