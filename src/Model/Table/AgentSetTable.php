<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/8
 * Time: 16:08
 */
namespace App\Model\Table;

class AgentSetTable extends SobeyTable{

	public function initialize(array $config)
    {
        parent::initialize($config);
        $this->belongsTo('Agent', [
            // //'className' => 'Publishing.Authors',
            // 'className' => 'Departments',
            'foreignKey' => 'agent_id',
            'joinType' =>'inner'
        ]);
    }
}