<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2015/10/30
 * Time: 16:21
 */

namespace App\Model\Table;


class InstanceRelationTable extends SobeyTable {

	public function saveInstanceRelation($fromid,$fromtype,$toid,$totype){
        $relation_data =$this->newEntity();
        $relation_data->fromid = $fromid;
        $relation_data->fromtype = $fromtype;
        $relation_data->toid = $toid;
        $relation_data->totype = $totype;
        $this->save($relation_data);
    }
}