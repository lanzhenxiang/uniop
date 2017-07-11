<?php
/** 
* 文件描述文字
* 
* 
* @author wjincheng
* @source HostExtendTable.php
* @version 1.0.0 
* @copyright  Copyright 2016 sobey.com 
*/ 
namespace App\Model\Table;
use Cake\Core\Configure;


class HostsNetworkCardTable extends SobeyTable
{
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->hasOne('InstanceBasic',[
            'className'=>'InstanceBasic',
            'foreignKey'=>'id',
        ]);
        $this->primaryKey('basic_id');
    }
}