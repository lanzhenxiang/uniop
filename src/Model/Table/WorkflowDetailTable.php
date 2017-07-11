<?php
/**
* 工作流 细节表 模型类
* 
* @file: WorkflowDetailTable.php
* @date: 2016年1月25日 下午2:42:50
* @author: xingshanghe
* @email: xingshanghe@icloud.com
* @copyright poplus.com
*
*/

namespace App\Model\Table;

class WorkflowDetailTable extends SobeyTable
{
    
    /**
     * 初始化函数 
     * {@inheritDoc}
     * @see \App\Model\Table\SobeyTable::initialize()
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        
        //增加树形结构，支持二叉树，暂时只用到链表结构
        $this->addBehavior('Tree',[
            'parent'    =>  'parent_id',
            'left'      =>  'lft',
            'right'     =>  'rgt',
        ]);
        
        
    }
    
    
}