<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/11
 * Time: 15:37
 */

namespace App\Model\Table;


class SetHardwareTable extends SobeyTable{
	/**
     * @func:
     * @param:
     * @date: 2015年9月11日 下午2:38:03
     * @author: shrimp liao
     * @return: null
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->hasMany('SetSoftware', [
            'foreignKey' => 'set_code',
            'bindingKey' => 'hardware_set'
        ]);
        $this->belongsTo('Accounts', [
            'foreignKey' => 'create_by'
        ]);


        $this->hasMany('GoodsVpcDetail', [
        ]);
    }

    public function getSetAgentList($where)
    {
        $field = [
            'set_id'=>'SetHardware.set_id',
            'set_name'=>'SetHardware.set_name',
            'set_code'=>'SetHardware.set_code',
            'cpu_number'=>'SetHardware.cpu_number',
            'memory_gb'=>'SetHardware.memory_gb',
            'gpu_gb'=>'SetHardware.gpu_gb',
            'price_day'=>'SetHardware.price_day',
            'price_month'=>'SetHardware.price_month',
            'price_year'=>'SetHardware.price_year',
            'agent_id'=>'Agent.id',
        ];
        $joinTable=[
            'AgentSet'=>[
                'table'=>'cp_agent_set',
                'type'=>'LEFT',
                'conditions'=>'AgentSet.set_id=SetHardware.set_id'
            ],
            'Agent'=>[
                'table'=>'cp_agent',
                'type'=>'LEFT',
                'conditions'=>'Agent.id=AgentSet.agent_id'
            ]
        ];
        $query = $this->find()->hydrate(false)->join($joinTable);

        // debug($query->select($field)->where($where)->order(['SetHardware.set_id'=>'ASC']));die();
        return $query->select($field)->where($where)->order(['SetHardware.set_id'=>'ASC']);
    }

    public function getSetSWByAgent($agent_id)
    {
        $field = [
             'set_id'       =>'SetHardware.set_id',
             'set_name'     =>'SetHardware.set_name',
             'set_code'     =>'SetHardware.set_code'
        ];

        $where = [
            'AgentSet.agent_id'=>$agent_id
        ];

        $query = $this->find()->hydrate(false)->join([
            'AgentSet' =>
            ['table' => 'cp_agent_set', 'type' => 'LEFT', 'conditions' => 'AgentSet.set_id = SetHardware.set_id']
        ]);
        return $query->select($field)->where($where)->toArray();
    }
}