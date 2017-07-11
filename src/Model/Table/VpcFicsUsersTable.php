<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/8
 * Time: 16:08
 */
namespace App\Model\Table;

use Cake\Core\Configure;
class VpcFicsUsersTable extends SobeyTable{

	public function initialize(array $config)
    {
        parent::initialize($config);
       
        $this->hasOne('VpcStoreUserP',[
            'foreignKey'=>'user_id'
        ]);
    }

    public function getUserLimitByVpcId($id)
    {
        $field = ['VpcFicsUsers.userid','VpcFicsUsers.name','VpcFicsUsers.password','VpcFicsUsers.total_cap','VpcFicsUsers.warn_cap','VpcFicsUsers.store_code','VpcFicsUsers.region_code','VpcFicsUsers.storetype','VpcFicsUsers.department_id','VpcFicsUsers.vpcId','vol_name'=>'store_user.vol_name','limit'=>'store_user.limit'];
        $query = $this->find()->hydrate(false)->join([
            'store_user' => ['table' => 'cp_vpc_store_user_p', 'type' => 'LEFT', 'conditions' => 'store_user.user_id = VpcFicsUsers.userid']]);
        return $query->select($field)->where(array('VpcFicsUsers.vpcId'=>'store_user.vpcId','VpcFicsUsers.vpcId'=>$id))->toArray();
    }
}