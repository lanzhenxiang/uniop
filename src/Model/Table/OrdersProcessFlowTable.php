<?php
/**
* 工作流 流程处理数据
* 
* @file: OrdersProcessFlowTable.php
* @date: 2016年1月20日 下午6:37:12
* @author: xingshanghe
* @email: xingshanghe@icloud.com
* @copyright poplus.com
*
*/

namespace App\Model\Table;

use App\Model\Table\SobeyTable;

class OrdersProcessFlowTable extends SobeyTable
{
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->belongsTo('WorkflowDetail', [
            'className' => 'WorkflowDetail',
            //'conditions' => ['Addresses.primary' => '1'],
            'dependent' => true,
            'bindingKey'=>'id',
            'foreignKey'=>'flow_detail_id',
        ]);
    }
}