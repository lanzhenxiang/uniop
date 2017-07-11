<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/8
 * Time: 16:08
 */
namespace App\Model\Table;

use Cake\Core\Configure;
class FicsUsersTable extends SobeyTable{

	protected  $query;
	public function initialize(array $config)
    {
        parent::initialize($config);
       
        $this->hasOne('StoreUserP',[
            'foreignKey'=>'user_id'
        ]);
    }

    public function initJoinQuery()
    {
        $this->query = $this->find()->hydrate(false);
        return $this;
    }

    public function joinStoreUserP()
    {
        $this->query = $this->query->join([
            'StoreUserP'=>[
                'table' => 'cp_store_user_p',
                'type'  => 'LEFT',
                'conditions' => 'StoreUserP.user_id = FicsUsers.userid'
            ]
        ]);
        return $this;
    }
    public function getJoinQuery(){
        return $this->query;
    }
}