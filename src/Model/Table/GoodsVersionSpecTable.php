<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2015/10/26
 * Time: 17:20
 */

namespace App\Model\Table;


class GoodsVersionSpecTable extends SobeyTable{
    public function initialize(array $config)
    {
        parent::initialize($config);

    }

    public function getDesktopSetByAgent($agent_id)
    {
        $field = [
             'id'       =>'GoodsVersionSpec.id',
             'name'     =>'GoodsVersionSpec.name',
             'image_code'     =>'GoodsVersionSpec.image_code',
             'set_code'     =>'GoodsVersionSpec.instancetype_code',
             'image_name'     =>'Imagelist.image_name',
             'set_name'     =>'SetHardware.set_name',
        ];

        $where = [
            'DesktopSet.agent_id'=>$agent_id
        ];

        $query = $this->find()->hydrate(false)->join([
            'DesktopSet' =>['table' => 'cp_desktop_set', 'type' => 'LEFT', 'conditions' => 'DesktopSet.set_id = GoodsVersionSpec.id'],
            'Imagelist' =>['table' => 'cp_imagelist', 'type' => 'LEFT', 'conditions' => 'Imagelist.image_code = GoodsVersionSpec.image_code'],
            'SetHardware' =>['table' => 'cp_set_hardware', 'type' => 'LEFT', 'conditions' => 'SetHardware.set_code = GoodsVersionSpec.instancetype_code']
        ]);
        return $query->select($field)->where($where)->toArray();
    }

    public function getDesktopSetByAgent1($agent_id)
    {
        // $field = [
        //      'id'       =>'GoodsVersionSpec.id',
        //      'name'     =>'GoodsVersionSpec.name',
        //      'image_code'     =>'GoodsVersionSpec.image_code',
        //      'set_code'     =>'GoodsVersionSpec.instancetype_code',
        //      'image_name'     =>'Imagelist.image_name',
        //      'set_name'     =>'SetHardware.set_name',
        // ];

        $where = [
            'DesktopSet.agent_id'=>$agent_id
        ];

        $query = $this->find()->hydrate(false)->join([
            'DesktopSet' =>['table' => 'cp_desktop_set', 'type' => 'LEFT', 'conditions' => 'DesktopSet.set_id = GoodsVersionSpec.id'],
            'Imagelist' =>['table' => 'cp_imagelist', 'type' => 'LEFT', 'conditions' => 'Imagelist.image_code = GoodsVersionSpec.image_code'],
            'SetHardware' =>['table' => 'cp_set_hardware', 'type' => 'LEFT', 'conditions' => 'SetHardware.set_code = GoodsVersionSpec.instancetype_code']
        ]);
        // debug($query->find("all")->where($where));die();
        return $query->find("all")->where($where)->toArray();
    }
}