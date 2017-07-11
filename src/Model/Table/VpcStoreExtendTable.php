<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/8
 * Time: 16:08
 */
namespace App\Model\Table;

use Cake\Core\Configure;
class VpcStoreExtendTable extends SobeyTable{

	public function initialize(array $config)
    {
        parent::initialize($config);
        $this->belongsTo('VpcFicsRelationDevice', [
            'foreignKey' => 'vol_id',
        ]);
        $this->primaryKey('vol_id');
    }
}