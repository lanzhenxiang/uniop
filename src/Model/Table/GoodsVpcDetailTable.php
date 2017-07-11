<?php
/**
 * Created by PhpStorm.
 * User: zhaodanru
 * Date: 2016/7/5
 * Time: 14:34
 */

namespace App\Model\Table;


class GoodsVpcDetailTable extends SobeyTable{
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->belongsTo('GoodsVpcDetails', [
            'className' => 'GoodsVpcDetail',
            'foreignKey' => 'subnet_id',
        ]);

        $this->belongsTo('Imagelist', [
            'foreignKey' => 'image_code',
            'bindingKey'=>'SetHardware.image_code'
        ]);
        $this->belongsTo('SetHardware', [
            'foreignKey' => 'instance_code',
            'bindingKey'=>'SetHardware.set_code'
        ]);
    }

    public function test($request_data)
    {
        $field = ['id' => 'GoodsVpcDetail.id', 'name' => 'GoodsVpcDetail.tagname', 'os' => 'image.plat_form', 'type' => 'vpcstore.type', 'param' => 'vpcstore.parameter', 'authority' => 'vpcstore.authority'];
        $where = array();
        $where = ['GoodsVpcDetail.type in' => ['desktop', 'ecs']];
        if (!empty($request_data['department_id'])) {
            $where['department_id'] = $request_data['department_id'];
        }
        if (!empty($request_data['type'])) {
            $where['GoodsVpcDetail.type'] = $request_data['type'];
        }
        if (!empty($request_data['vpcId'])) {
            $where['GoodsVpcDetail.vpc_id'] = $request_data['vpcId'];
        }
        if (!empty($request_data['image_name'])) {
            if ($request_data['image_name'] == "Windows") {
                $where["OR"] = [['host_extend.plat_form' => 'windows'], ['host_extend.plat_form' => '云主机']];
            } elseif ($request_data['plat_form'] == "linux") {
                $where["AND"] = [['host_extend.plat_form <>' => 'windows'], ['host_extend.plat_form <>' => '云主机']];
            }
        }
        if (!empty($request_data['auth'])) {
            $where['authority'] = $request_data['auth'];
        } elseif (isset($request_data['auth']) && $request_data['auth'] == '0') {
            $where['authority'] = $request_data['auth'];
        }
        if (isset($request_data['search'])) {
            if ($request_data['search'] != "") {
                $where['AND'] = [["GoodsVpcDetail.tagname like" => "%" . $request_data['search'] . "%"]];
            }
        }
        $query = $this->find()->hydrate(false)->join([
            'image' => ['table' => 'cp_imagelist', 'type' => 'LEFT', 'conditions' => 'image.image_code = GoodsVpcDetail.image_code'],
            'vpcstore' => ['table' => 'cp_vpc_fics_relation_device', 'type' => 'LEFT', 'conditions' => [ 'vpcstore.basic_id=GoodsVpcDetail.id','vol_id' => $request_data['vol_id'],'vpcId'=>$request_data["vpcId"]]]]);
        // debug($query->select($field)->where($where));die;
        return $query->select($field)->where($where);
    }

    //根据vpc_detail id 获取 镜像对应的 plat_from
    public function getPlatFromById($id)
    {
        $field = ['plat_form' => 'imageg.plat_form'];
        $query = $this->find()->hydrate(false)->join([
            'imageg' => ['table' => 'cp_imagelist', 'type' => 'LEFT', 'conditions' => 'imageg.image_code = GoodsVpcDetail.image_code']]);
        return $query->select($field)->where(array('GoodsVpcDetail.id'=>$id))->first();
    }

    //根据id 获取tagName
    public function getTagNameById($id)
    {
        return $this->find()->select(['tagname'])->where(array('GoodsVpcDetail.id'=>$id))->first();
    }
}