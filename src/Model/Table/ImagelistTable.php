<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/8
 * Time: 16:08
 */
namespace App\Model\Table;

class ImagelistTable extends SobeyTable{
	public function initialize(array $config)
    {
        parent::initialize($config);
        $this->belongsTo('Agent', [
            'foreignKey' => 'agent_id',
        ]);
        $this->belongsTo('Accounts', [
            'foreignKey' => 'creat_by',
        ]);

        $this->hasMany('AgentImagelist', [
            'foreignKey' => 'image_id',
        ]);
        $this->belongsToMany('Agents', [
            'className' => 'Agent',
            'joinTable' => 'agent_imagelist',
            'foreignKey' => 'image_id',
        ]);
        $this->hasMany('GoodsVpcDetail', [
        ]);
    }

    public function getImageByAgent($agent_id)
    {
        $field = [
             'id'       =>'Imagelist.id',
             'image_name'     =>'Imagelist.image_name',
             'image_code'     =>'Imagelist.image_code',
             'image_type' =>'Imagelist.image_type'
        ];

        $where = [
            'AgentImagelist.agent_id'=>$agent_id
        ];

        $query = $this->find()->hydrate(false)->join([
            'AgentImagelist' =>
            ['table' => 'cp_agent_imagelist', 'type' => 'LEFT', 'conditions' => 'AgentImagelist.image_id = Imagelist.id']
        ]);
        return $query->select($field)->where($where)->toArray();
    }
}