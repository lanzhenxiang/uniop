<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/11
 * Time: 16:32
 */

namespace App\Model\Table;


class BasicDataListTable extends SobeyTable{
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->belongsTo('BasicDataType', [
            'foreignKey' => 'type_id',
        ]);
    }
}