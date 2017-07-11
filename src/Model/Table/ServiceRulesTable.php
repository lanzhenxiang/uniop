<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/8
 * Time: 16:08
 */
namespace App\Model\Table;

class ServiceRulesTable extends SobeyTable{
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->belongsTo('ServiceType', [
            // 'className' => 'Publishing.Authors',
            'foreignKey' => 'type_id'
        ]);



    }
}