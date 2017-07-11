<?php
/** 
* 文件描述文字
* 
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date  2015年10月10日下午2:19:29
* @source HostExtendTable.php
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 
namespace App\Model\Table;
use Cake\Core\Configure;


class DesktopExtendTable extends SobeyTable
{
    
    /**
     * 初始化
     * (non-PHPdoc)
     * @see \Cake\ORM\Table::initialize()
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->table(Configure::read('Db.cmop.pre').'host_extend');
        //关联
        $this->hasOne('InstanceBasic', [
            'className' => 'InstanceBasic',
            'bindingKey' => 'DesktopExtend.basic_id',
            'foreignKey' => 'id',
        ]);
    }
}