<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/8
 * Time: 16:08
 */
namespace App\Model\Table;

class FirewallTemplateTable extends SobeyTable{
	public function initialize(array $config)
    {
        parent::initialize($config);
        $this->hasMany('FirewallTemplateDetail', [
            'foreignKey' => 'id'
        ]);
    }
}