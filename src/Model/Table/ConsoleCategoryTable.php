<?php
/** 
* 文件描述文字
* 
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date  2015年9月21日下午4:06:23
* @source ConsoleCategoryTable.php
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 

namespace App\Model\Table;

class ConsoleCategoryTable extends SobeyTable
{
    
    public function initialize(array $config)
    {
        parent::initialize($config);
        
        $this->addBehavior('SobeyTree', [
                'order' => 'sort_order',
                'displayField' => 'label'
        ]);
    }
    
}