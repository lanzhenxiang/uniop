<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/8
 * Time: 16:08
 */
namespace App\Model\Table;

class HardwareInsteadTable extends SobeyTable{
	public function initialize(array $config)
    {
        parent::initialize($config);

        $this->belongsTo('HardwareAssets',[
            'foreignKey'=>'assets_id'
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