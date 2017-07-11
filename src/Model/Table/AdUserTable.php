<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2015/10/26
 * Time: 17:20
 */

namespace App\Model\Table;


class AdUserTable extends SobeyTable{
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->belongsTo('Accounts', [
            // 'className' => 'Publishing.Authors',
            'foreignKey' => 'uid'
        ]);



    }
}