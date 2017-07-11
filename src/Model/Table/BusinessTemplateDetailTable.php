<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2016/7/5
 * Time: 14:34
 */

namespace App\Model\Table;


class BusinessTemplateDetailTable extends SobeyTable{
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->belongsTo('BusinessTemplate', [
            'className' => 'BusinessTemplate',
            'foreignKey' => 'biz_tid',
        ]);

        $this->belongsTo('Imagelist', [
            'className' => 'Imagelist',
            'foreignKey' => false,
            'conditions' =>'Imagelist.image_code = BusinessTemplateDetail.image_code'
        ]);
        $this->belongsTo('SetHardware', [
            'className'  => 'SetHardware',
            'foreignKey' => false,
            'conditions' =>'set_code = instance_code'
        ]);
    }
}