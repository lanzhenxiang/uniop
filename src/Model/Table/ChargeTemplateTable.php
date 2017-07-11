<?php

/**
* 文件用途描述
* 
* @file: ChargeTemplateTable.php
* @date: Dec 23, 2015 6:38:59 PM
* @author: xingshanghe
* @email: xingshanghe@icloud.com
* @copyright poplus.com
*
*/

namespace App\Model\Table;

use App\Model\Table\SobeyTable;
class ChargeTemplateTable extends SobeyTable
{

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
        ]);

    }

}