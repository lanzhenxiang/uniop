<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2015/10/28
 * Time: 10:28
 */

namespace App\Model\Table;


class SubnetExtendTable extends SobeyTable{
    /**
     * 初始化
     * (non-PHPdoc)
     * @see \Cake\ORM\Table::initialize()
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->hasOne('InstanceBasic',[
            'className' => 'InstanceBasic',
            'foreignKey' => 'id'
        ]);
        // $this->hasOne('HostExtend',[
        //     'className' => 'HostExtend',
        //     'foreignKey' => 'basic_id',
        //     //'bindingKey'=>'',
        // ]);
    }
}