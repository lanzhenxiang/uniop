<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/8
 * Time: 16:08
 */
namespace App\Model\Table;

use Cake\Core\Configure;
class AgentTable extends SobeyTable{

	public function initialize(array $config)
    {
        parent::initialize($config);

        $this->addBehavior('SobeyTree', [
                'order' => 'sort_order',
                'parent' => 'parentid',
                'displayField'=>'agent_name',
        ]);
        $this->hasMany('AgentSet',[
            'className' => 'AgentSet',
            'foreignKey'=>'agent_id',
        ]);
        $this->hasMany('AgentImagelist',[
            'className' => 'AgentImagelist',
            'foreignKey'=>'agent_id',
        ]);
        $this->hasMany('InstanceBasic',[
            'foreignKey'=>'location_code',
        ]);
        $this->belongsToMany('SetHardware',[
                'joinTable' =>  Configure::read('Db.cmop.pre').'agent_set',
                'foreignKey'=>'agent_id',
                'targetForeignKey'=>'set_id'

        ]);
        $this->belongsToMany('Imagelist',[
                'joinTable' =>  Configure::read('Db.cmop.pre').'agent_imagelist',
                'foreignKey'=>'agent_id',
                'targetForeignKey'=>'image_id'
        ]);
        $this->belongsTo('Accounts', [
            // 'className' => 'Publishing.Authors',
            'foreignKey' => 'create_by'
        ]);
    }

    /**
     * [getAgentRootCode 获取顶级厂商agent_code]
     * @param  [string] $class_code [description]
     * @return [string]             [description]
     */
    public function getAgentRootCode($class_code){
        $agentEntity = $this->find()->where(['class_code'=>$class_code])->first();
        if($agentEntity->parentid == 0){
            return $agentEntity->agent_code;
        }
        $agentEntity  = $this->getAgentRoot($agentEntity->parentid);
        return $agentEntity->agent_code;
    }

    /**
     * [getAgentRootCode 获取顶级厂商agent_code]
     * @param  [string] $class_code [description]
     * @return [string]             [description]
     */
    public function getAgentRootEntity($region_code){
        $agentEntity = $this->find()->where(['region_code'=>$region_code])->first();
        if($agentEntity == null || $agentEntity->parentid == 0 ){
            return $agentEntity;
        }
        $agentEntity  = $this->getAgentRoot($agentEntity->parentid);
        return $agentEntity;
    }

    /**
     * [getAgentRoot 获取顶级厂商]
     * @param  [int] $id      [厂商id]
     * @return [object Entity]     [description]
     */
    public function getAgentRoot($id){
        $agentEntity = $this->find()->where(['id'=>$id])->first();
        if($agentEntity->parentid != 0){
            return $this->getAgentRoot($agentEntity->parentid);
        }
        return $agentEntity;
    }

}