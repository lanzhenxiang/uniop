<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2016/7/5
 * Time: 14:33
 */

namespace App\Model\Table;


class GoodsVpcTable extends SobeyTable{
    public function initialize(array $config)
    {
        parent::initialize($config);
    }

    public function getAgentIDByGVPCID($vpc_id)
    {
        $field = [
             'agent_id'       =>'Agent.id'
        ];

        $where = [
            'GoodsVpc.vpc_id'=>$vpc_id
        ];

        $query = $this->find()->hydrate(false)->join([
            'Agent' =>
            ['table' => 'cp_agent', 'type' => 'LEFT', 'conditions' => 'GoodsVpc.region_code = Agent.region_code']
        ]);
        return $query->select($field)->where($where)->first();
    }
}