<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2016/7/5
 * Time: 14:30
 */

namespace App\Controller\Admin;

use App\Controller\AdminController;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

class StoreController  extends AdminController
{

    public $paginate = [
        'limit' => 15,
    ];

    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_commodity_goods_vpc');
        if (!$checkPopedomlist) {
            return $this->redirect('/admin/');  
        }
    }

    public function index($vpc_id=0)
    {
        $this->set("_id",$vpc_id);
    }

    public function add($vpc_id=0)
    {
        $this->set("_id",$vpc_id);
        # code...
    }

    public function setting($vol_id=0,$vpc_id=0)
    {
        $this->set("_id",$vpc_id);//VPCid
        $this->set("_vid",$vol_id);//VPCid
        $table = TableRegistry::get('VpcStoreExtend');
        $entity = $table->get($vol_id);
        $this->set("_vol_name",$entity->vol_name);//存储卷名
    }

    public function storehosts($vol_id=0,$vpc_id=0)
    {
        $this->set("_id",$vpc_id);//VPCid
        $this->set("_vid",$vol_id);//vol_id
        $table = TableRegistry::get('VpcStoreExtend');
        $region_table = TableRegistry::get('Agent');
        $entity = $table->get($vol_id);
        $this->set("_entity",$entity);//存储卷名
        $agent_entity = $region_table->find()->select(['display_name'])->where(array('region_code'=>$entity->region_code))->first();
        $this->set("_display_name",$agent_entity->display_name);
        //获取IP
        $store_table = TableRegistry::get('Store');
        $store_entity = $store_table->find()->select(['ip'])->where(array('store_code'=>$entity->store_code))->first();
        $this->set("_store_ip",$store_entity->ip);
    }
}
