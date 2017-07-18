<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/8
 * Time: 16:08
 */
namespace App\Model\Table;

class HardwareAssetsTable extends SobeyTable{

    const ASSET_ECS = 'ecs';

	public function initialize(array $config)
    {
        parent::initialize($config);

        $this->hasOne('HardwareAssetsEcs',[
            'className' => 'HardwareAssetsEcs',
            'foreignKey'=>'assets_id',
            //'conditions'=>'HardwareAssets.type ="'.SELF::ASSET_ECS.'"',
            'dependent' => true,
            'cascadeCallbacks' => true
        ]);

        $this->hasMany('HardwareInstead',[
            'className' => 'HardwareInstead',
            'foreignKey' => 'assets_id',
            'dependent' => true,
            'cascadeCallbacks' => true
        ]);

        $this->hasMany('HardwareRepair',[
            'className' => 'HardwareRepair',
            'foreignKey' => 'assets_id',
            'dependent' => true,
            'cascadeCallbacks' => true
        ]);

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created_at' => 'new',
                    'updated_at' => 'always',
                ]
            ]
        ]);
    }

}