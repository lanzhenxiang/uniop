<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/8
 * Time: 16:08
 */
namespace App\Model\Table;

use Cake\Core\Configure;
class FicsExtendTable extends SobeyTable{

	protected  $query;

	public function initialize(array $config)
    {
        parent::initialize($config);
        $this->belongsTo('FicsRelationDevice', [
            'foreignKey' => 'vol_id',
        ]);
        $this->primaryKey('vol_id');
    }

    public function initJoinQuery()
    {
        $this->query = $this->find()->hydrate(false);
        return $this;
    }

    public function joinAgent()
    {
        $this->query = $this->query->join([
            'Agent'=>[
                'table' => 'cp_agent',
                'type'  => 'LEFT',
                'conditions' => 'FicsExtend.region_code = Agent.region_code'
            ]
        ]);
        return $this;
    }

    public function joinStore()
    {
        $this->query = $this->query->join([
            'Store'=>[
                'table' => 'cp_store',
                'type'  => 'LEFT',
                'conditions' => 'FicsExtend.store_code = Store.store_code'
            ]
        ]);
        return $this;
    }

    public function joinDepartments()
    {
        $this->query = $this->query->join([
            'Departments'=>[
                'table' => 'cp_departments',
                'type'  => 'LEFT',
                'conditions' => 'FicsExtend.department_id = Departments.id'
            ]
        ]);
        return $this;
    }
    public function getJoinQuery(){
        return $this->query;
    }
}