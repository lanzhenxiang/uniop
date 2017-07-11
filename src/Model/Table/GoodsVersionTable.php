<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2015/10/26
 * Time: 17:20
 */

namespace App\Model\Table;


class GoodsVersionTable extends SobeyTable{

    public function initialize(array $config)
    {	
    	$this->entityClass('App\Model\Entity\GoodsVersionEntity');
        parent::initialize($config);
    }
}