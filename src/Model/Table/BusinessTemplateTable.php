<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2016/7/5
 * Time: 14:33
 */

namespace App\Model\Table;


class BusinessTemplateTable extends SobeyTable{
	public function initialize(array $config)
    {
        parent::initialize($config);
        $this->belongsTo('BusinessTemplateDetail', [
            'className' => 'BusinessTemplateDetail',
            'foreignKey' => false,
            'conditions' =>'BusinessTemplateDetail.biz_tid = BusinessTemplate.biz_tid'
        ]);
    }
}