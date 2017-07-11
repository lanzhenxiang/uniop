<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/8
 * Time: 16:08
 */
namespace App\Model\Table;

class FirewallTemplateDetailTable extends SobeyTable{
	public function initialize(array $config)
    {
        parent::initialize($config);
        // $this->belongsTo('FirewallTemplateTable', [
        //     'foreignKey' => 'id'
        // ]);
    }
}