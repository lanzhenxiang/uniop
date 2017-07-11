<?php
/**
*
*  索贝Table类父类
*
* @author XingShanghe<xingshanghe@gmail.com>
* @date  2015年8月3日下午3:29:58
* @source SobeyTable.php
* @version 1.0.0
* @copyright  Copyright 2015 sobey.com
*/
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Core\Configure;

class SobeyTable extends Table
{

    /**
     * 初始化
     * (non-PHPdoc)
     * @see \Cake\ORM\Table::initialize()
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->table(Configure::read('Db.cmop.pre').$this->table());
    }



}