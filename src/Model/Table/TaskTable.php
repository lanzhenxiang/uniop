<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/14
 * Time: 14:11
 */

namespace App\Model\Table;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;

class TaskTable extends SobeyTable{
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->belongsTo('Accounts', [
            // 'className' => 'Publishing.Authors',
            'foreignKey' => 'user_id'
        ]);

       
        $this->belongsTo('InstanceBasic', [
        	
        		'foreignKey' => 'basic_id'
        ]);

    }

    public function findTaskLog(Query $query,array $options){
        $id = $options['id'];
        $where =array();
        if($options['type'] =='normal'){//正常日志
            $where['task.status'] = '2';
        }elseif($options['type'] =='excp'){//异常日志
            $where['task.status not in'] = array('1','2');
        }elseif($options['type']=='executing'){
            $where['task.status']='1';
        }
        $where['or'] = ['task.assistant_basic_id'=>$id,'task.basic_id'=>$id];
        return $query->where($where);
    }
}