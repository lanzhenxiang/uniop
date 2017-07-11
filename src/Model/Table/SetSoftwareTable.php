<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/11
 * Time: 15:37
 */

namespace App\Model\Table;


class SetSoftwareTable extends SobeyTable{
	/**
     * @func:
     * @param:
     * @date: 2015年9月11日 下午2:38:03
     * @author: shrimp liao
     * @return: null
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->belongsTo('SetHardware', [
            //'className' => 'Publishing.Authors',
            'className' => 'SetHardware',
            'foreignKey' => 'hardware_set',
            'bindingKey' => 'set_code',
        ]);
    }
}