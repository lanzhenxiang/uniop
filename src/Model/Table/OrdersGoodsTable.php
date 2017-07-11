<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/11
 * Time: 15:37
 */

namespace App\Model\Table;


class OrdersGoodsTable extends SobeyTable{
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->belongsTo('Orders', [
            'foreignKey' => 'order_id',
        ]);
    	
    	$this->belongsTo('Goods', [
            'foreignKey' => 'good_id',
        ]);
    }
}