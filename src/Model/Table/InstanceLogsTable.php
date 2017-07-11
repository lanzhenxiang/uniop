<?php
/**
* 设备日志  实例基类
*
*
* @author shrimpliao
* @date  2015年9月22日下午4:18:37
* @source InstanceLogsTable.php
* @version 1.0.0
* @copyright  Copyright 2015 sobey.com
*/
namespace App\Model\Table;


use Cake\Cache\Cache;
class InstanceLogsTable extends SobeyTable{
    /**
     * 初始化
     * (non-PHPdoc)
     * @see \Cake\ORM\Table::initialize()
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->belongsTo('Accounts',[
            'className' => 'Accounts',
            'foreignKey'=>'user_id'
        ]);

        $this->belongsTo('InstanceBasic',[
            'className' => 'InstanceBasic',
            'foreignKey'=>'basic_id'
        ]);
    }

}
