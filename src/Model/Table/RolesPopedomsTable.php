<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2015/11/7
 * Time: 15:37
 */

namespace App\Model\Table;


class RolesPopedomsTable extends SobeyTable{
	public function initialize(array $config)
    {
        parent::initialize($config);
         $this->belongsTo('Popedomlist', [
            'className' => 'Popedomlist',
            'foreignKey' => 'popedomlist_id',
        ]);
    }
}