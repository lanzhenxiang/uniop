<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2015/10/9
 * Time: 14:45
 */

namespace App\Model\Table;


class DisksMetadataTable extends SobeyTable{
	 public function initialize(array $config)
{
    parent::initialize($config);
    // $this->belongsTo('InstanceBasic',[
    //     'className' => 'InstanceBasic',
    //     'foreignKey'=>'code'
    //     //'bindingKey'=>'',
    // ]);
}

}