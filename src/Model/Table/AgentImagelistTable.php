<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/8
 * Time: 16:08
 */
namespace App\Model\Table;

class AgentImagelistTable extends SobeyTable{

	public function initialize(array $config)
    {
        parent::initialize($config);
        $this->belongsTo('Agent', [
            'foreignKey' => 'agent_id',
            'joinType' =>'inner'
        ]);
    }
}