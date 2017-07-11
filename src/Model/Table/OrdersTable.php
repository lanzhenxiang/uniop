<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/11
 * Time: 15:37
 */

namespace App\Model\Table;


class OrdersTable extends SobeyTable{
	public function initialize(array $config)
    {
        parent::initialize($config);
        $this->belongsTo('Department', [
            'className' => 'Departments',
            'foreignKey' => 'department_id',
        ]);

        $this->belongsTo('Account', [
            'className' => 'Accounts',
            'foreignKey' => 'account_id',
        ]);

        $this->belongsTo('WorkflowDetail', [
            'className' => 'WorkflowDetail',
            'foreignKey' => 'detail_id',
            //'joinType'  =>'LEFT'
            // 'conditions' => ['WorkflowDetail.flow_id' => 'Orders.flow_id']
        ]);
         
        $this->hasMany('OrdersGoods',[
             'className'=>'OrdersGoods',
             'foreignkey'=>'id',
             //'conditions' => ['OrdersGoods.is_console' => 0],
        ]);

        $this->belongsToMany('Departments', [
            'joinTable' => 'accounts',
            'foreignKey' => 'id',
            'targetForeignKey' => 'department_id',
        ]);
    }

    /**
     * 查找可见的订单
     * @param  [Cake\ORM\Query] $query   [查询对象]
     * @param  array  $options [参数]
     * @return [Cake\ORM\Query]          
     */
    public function findDisplayed($query,$options = []){
        return $query->where(['Orders.is_display'=> 1]);
    }
    /**
     * 查找正常的订单
     */
    public function findNormal($query,$options = []){
        return $query->where(['Orders.status <>' => -1]);
    }
    /**
     * 查找用户所拥有的订单
     */
    public function findOwnedBy($query,$options){
        $account_id = isset($options['account_id']) ? intval($options['account_id']) : 0;
        return $query->where(['Orders.account_id' =>$account_id]);
    }

    public function findNumberLike($query,$options){
        $order_number = $options['number'];
        if($order_number != ""){
            return $query->where(['Orders.number like' => "%" . $order_number . "%"]);
        }
        return $query;
    }

    /**
     * 订单的创建时间区间finder
     * @param  [Cake\ORM\Query] $query   
     * @param  array  $options ['start'=>,'end'=>]
     * @return [Cake\ORM\Query]          [description]
     */
    public function findBetweenCreateTime($query,$options = []){
        $start_date = isset($options['start']) && intval($options['start']) > 0 ? $options['start'] : null;
        $end_date   = isset($options['end']) && intval($options['end']) > 0 ? $options['end'] : null;
        if($start_date !== null){
            $where['Orders.create_time >='] = strtotime($start_date);
        }
        
        if($end_date !== null){
            $where['Orders.create_time <='] = strtotime($end_date) + 86400;
        }

        if(isset($where)){
            return $query->where($where);
        }
        return $query;
    }

}