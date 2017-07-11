<?php
/** 
* 索贝树形结构behavior
* 
* 
* $category = TableRegistry::get('Category');
* debug($category->find('tree')->select(['cat_id','cat_name','parent_id'])->toArray());
* debug($category->find('optionList')->toArray());
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date  2015年9月10日下午5:03:14
* @source SobeyTreeBehavior.php
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 


namespace App\Model\Behavior;

//use Cake\Datasource\EntityInterface;
//use Cake\Datasource\Exception\RecordNotFoundException;
//use Cake\Event\Event;
use Cake\ORM\Behavior;
//use Cake\ORM\Entity;
use Cake\ORM\Query;

class SobeyTreeBehavior extends Behavior
{
    
    /**
     * Cached copy of the first column in a table's primary key.
     *
     * @var string
     */
    protected $_primaryKey;
    
    protected $_defaultConfig = [
        'implementedFinders'  =>  [
            'tree'      =>  'findTree',
            'optionList'  =>  'findOptionList',
        ],
        'implementedMethods'=>[
            'formatOptionList'=>  'formatOptionList',
        ],
        'pid'       =>  0,
        'parent'    =>  'parent_id',
        'order'     =>  'sort_order',
        'displayField'=>'name',
        'spacer'    =>  '-',
        'scope'     =>  null,
        'level'     =>  null,
    ];
    
    public function findTree(Query $query, array $options = []){
        return $this->_scope($query)
            ->find('threaded', [
                'parentField' => $this->config('parent'),
                'order' => [$this->config('order') => 'ASC']
            ]);
    }
    
    
    public function findOptionList(Query $query, array $options = []){
        $result = $this->_scope($query)
            ->find('threaded', [
                'parentField' => $this->config('parent'),
                'order' => [$this->config('order') => 'ASC']
            ]);
        return $this->formatOptionList($result,$options);            
    }
    
    
    
    public function formatOptionList(Query $query, array $options = [])
    {
        return $query->formatResults(function ($results) use ($options) {
            $options += [
                'keyPath' => $this->_getPrimaryKey(),
                'valuePath' => $this->config('displayField'),//$this->_table->displayField(),
                'spacer' => $this->config('spacer'),
            ];
            return $results
                ->listNested()
                ->printer($options['valuePath'], $options['keyPath'], $options['spacer']);
        });
    }
    
    
    /**
     * Alters the passed query so that it only returns scoped records as defined
     * in the tree configuration.
     *
     * @param \Cake\ORM\Query $query the Query to modify
     * @return \Cake\ORM\Query
     */
    protected function _scope($query)
    {
        $config = $this->config();
    
        if (is_array($config['scope'])) {
            return $query->where($config['scope']);
        } elseif (is_callable($config['scope'])) {
            return $config['scope']($query);
        }
    
        return $query;
    }
    
    
    protected function _fields($query)
    {
        $config = $this->config();
    
        if (is_array($config['fields'])) {
            return $query->where($config['fields']);
        }elseif (strpos($config['fields'], ',')){
            return $query->select(explode(',', $config['fields']));
        }
    
        return $query;
    }
    
    /**
     * Returns a single string value representing the primary key of the attached table
     *
     * @return string
     */
    protected function _getPrimaryKey()
    {
        if (!$this->_primaryKey) {
            $this->_primaryKey = (array)$this->_table->primaryKey();
            $this->_primaryKey = $this->_primaryKey[0];
        }
        return $this->_primaryKey;
    }
    
}