<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2015/11/6
 * Time: 17:24
 */

namespace App\Model\Table;


class PopedomlistTable extends SobeyTable{
	 public function initialize(array $config)
    {
        parent::initialize($config);

        $this->addBehavior('SobeyTree', [
                'order' => 'serinalno',
                'displayField' => 'popedomnote'
        ]);
    }

    public function getWorkFlow($name){
        // $field = ['name' => 'InstanceBasic.name','code'=>'InstanceBasic.code','id'=>'InstanceBasic.id'];
        // $query = $this->find()->hydrate(false)->join([
        //     'subnet_extend' => ['table' => 'cp_subnet_extend', 'type' => 'LEFT', 'conditions' => 'InstanceBasic.id = subnet_extend.basic_id']]);
        // return $query->select($field)->where(array('subnet_extend.isPublic'=>1))->toArray();

        $where = array('popedomname'=>$name,'parent_id'=>0);
        $query = $this->find("all")->where($where)->first();
        return $query;
    }
}