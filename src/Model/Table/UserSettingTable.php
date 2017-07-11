<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2015/11/12
 * Time: 19:19
 */

namespace App\Model\Table;


class UserSettingTable extends SobeyTable{
	public function initialize(array $config)
    {
        parent::initialize($config);
        $this->belongsTo('Accounts', [
            // 'className' => 'Publishing.Authors',
            'foreignKey' => 'owner_id'
        ]);

        $this->belongsTo('Dpartments', [
            // 'className' => 'Publishing.Authors',
            'foreignKey' => 'owner_id'
        ]);

        $this->belongsTo('Agent', [
            // 'className' => 'Publishing.Authors',
            'foreignKey' => 'owner_id'
        ]);

    }
}