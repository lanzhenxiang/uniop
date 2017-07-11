<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/8
 * Time: 16:08
 */
namespace App\Model\Table;

class DepartmentsTable extends SobeyTable{
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->belongsTo('Accounts', [
            //'className' => 'Publishing.Authors',
            'foreignKey' => 'create_by',
        ]);

        $this->addBehavior('Departments', [
                'order' => 'sort_order',
                'parent' => 'parent_id',
                'displayField'=>'name',
        ]);


    }
}