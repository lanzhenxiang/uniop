<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/10
 * Time: 11:18
 */

namespace App\Model\Table;


class TenantsTable extends SobeyTable{
	public function initialize(array $config)
    {
        parent::initialize($config);
        $this->belongsTo('Accounts', [
            // 'className' => 'Publishing.Authors',
            'foreignKey' => 'create_by'
        ]);

    }
}