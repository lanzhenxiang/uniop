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

class GoodsVpcController extends AdminController
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

    public function index($name = '')
    {
        $goods_vpc = TableRegistry::get('GoodsVpc');
        $where     = array();
        if ($name) {
            //$where['OR'] =array('popedomname like'=>"%$name%",'popedomnote like'=>"%$name%");
        }
        $data = $goods_vpc->find('all');
        $data = $this->paginate($data);
        $this->set('data', $data);
        $this->set('name', $name);
    }

    public function addedit($id = 0)
    {

        $goods_vpc = TableRegistry::get('GoodsVpc');
        $agent     = TableRegistry::get('Agent');
        if ($this->request->is('get')) {
            $agent_data = $agent->find()->select(['agent_name', 'region_code','display_name'])->where(array('parentid >' => 0,'is_enabled'=>1));
            $this->set('agent', $agent_data);
            if ($id) {
                $data = $goods_vpc->find('all')->where(array('vpc_id' => $id))->first();
                $this->set('data', $data);
            }
        } else {
            $display_name                       = $agent->find()->select(['display_name'])->where(array('region_code' => $this->request->data['region_code']))->first();
            $this->request->data['create_time'] = time();
            $this->request->data['region_name'] = $display_name['display_name'];
            $goodsvpc                           = $goods_vpc->newEntity();
            $goodsvpc                           = $goods_vpc->patchEntity($goodsvpc, $this->request->data);
            if ($goods_vpc->save($goodsvpc)) {
                echo json_encode(array('code' => 0, 'msg' => '操作成功'));exit;
            } else {
                echo json_encode(array('code' => 1, 'msg' => '操作失败'));exit;
            }
        }
    }

    //配置单显示
    public function configure($vpc_id = 0)
    {
        $goods_vpc        = TableRegistry::get('GoodsVpc');
        $goods_vpc_detail = TableRegistry::get('GoodsVpcDetail');

        if ($vpc_id == 0 || !is_numeric($vpc_id)) {
            $this->redirect('/admin/goods-vpc');
        } else {
            $goods_vpc_detail = TableRegistry::get('GoodsVpcDetail');
            $vpcdata          = $goods_vpc->find('all')->where(array('vpc_id' => $vpc_id))->first();
            $this->set('vpcdata', $vpcdata);
            $connection = ConnectionManager::get('default');
            $sql        = ' SELECT ';
            $sql .= ' a.*,b.cpu_number,b.memory_gb,b.gpu_gb,c.image_name,c.plat_form,d.tagname as \'subnetName\' FROM cp_goods_vpc_detail a LEFT JOIN cp_set_hardware b ON a.instance_code=b.set_code LEFT JOIN cp_imagelist c ON a.image_code=c.image_code LEFT JOIN cp_goods_vpc_detail d ON a.subnet_id=d.id ';
            $sql .= ' where a.vpc_id =\'' . $vpc_id . '\' ORDER BY a.sort_order';
            $query = $connection->execute($sql)->fetchAll('assoc');
            $this->set('vpcdetaildata', $query);
            $this->set('vpc_id', $vpc_id);

            // $goods_vpc        = TableRegistry::get('GoodsVpc');
            // $goods_vpc_detail = TableRegistry::get('GoodsVpcDetail');
            // $vpcdata          = $goods_vpc->find('all')->where(array('vpc_id' => $vpc_id))->first();
            // $this->set('vpcdata', $vpcdata);
            // $vpcdetaildata = $goods_vpc_detail->find('all')->contain(['GoodsVpcDetails'])->where(array('or' => array(array('GoodsVpcDetail.vpc_id' => $vpc_id), array('GoodsVpcDetail.vpc_id' => 0))))->order(['GoodsVpcDetail.sort_order', 'GoodsVpcDetail.id']);
            // debug($this->paginate($vpcdetaildata));die;
            // $vpcdetaildata = $this->paginate($vpcdetaildata);
            // $this->set('vpcdetaildata', $vpcdetaildata);
            // $this->set('vpc_id', $vpc_id);
        }
    }

    public function findVpcEcsConfigure($vpc_id = 0)
    {
        if ($vpc_id == 0 || !is_numeric($vpc_id)) {
            return '';
        } else {
            $connection = ConnectionManager::get('default');
            $sql        = ' SELECT ';
            $sql .= ' a.*,b.cpu_number,b.memory_gb,b.gpu_gb,c.image_name,c.plat_form,d.tagname as \'subnetName\',e.vol_name,e.total_cap,e.warn_cap FROM cp_goods_vpc_detail a LEFT JOIN cp_set_hardware b ON a.instance_code=b.set_code LEFT JOIN cp_imagelist c ON a.image_code=c.image_code LEFT JOIN cp_goods_vpc_detail d ON a.subnet_id=d.id LEFT JOIN cp_vpc_store_extend e ON (e.vpcId=a.vpc_id AND a.type=e.vol_type) ';
            $sql .= ' where a.vpc_id =\'' . $vpc_id . '\' ORDER BY a.sort_order';
            // debug($sql);die();
            $query = $connection->execute($sql)->fetchAll('assoc');
            // $this->set('vpcdetaildata', $query);
            return $query;
        }
    }

    //添加子网
    public function addsubnet($vpc_id = 0, $id = 0)
    {
        if (!$vpc_id) {
            $this->redirect('/admin/goods-vpc');
        } else {
            if ($id) {
                $goods_vpc_detail = TableRegistry::get('GoodsVpcDetail');
                $data             = $goods_vpc_detail->find()->where(array('id' => $id))->first();
                $this->set('data', $data);
            }
            $this->set('vpc_id', $vpc_id);
        }
    }

    public function store($vpc_id = 0)
    {
        if($vpc_id){
            $this->redirect('/admin/store/index/'.$vpc_id);
        }
    }

    public function addstore($vpc_id=0)
    {
        if($vpc_id){
            $this->redirect('/admin/store/add/'.$vpc_id);
        }
    }

    public function storehosts($value='')
    {
        if($vpc_id){
            $this->redirect('/admin/store/storehosts/'.$vpc_id);
        }
    }

    public function addsubnetpost()
    {
        $goods_vpc_detail              = TableRegistry::get('GoodsVpcDetail');
        $this->request->data['type']   = 'subnet';
        $this->request->data['number'] = '1';
        $goodsvpcdetails               = $goods_vpc_detail->newEntity();
        $goodsvpcdetails               = $goods_vpc_detail->patchEntity($goodsvpcdetails, $this->request->data);
        if ($goods_vpc_detail->save($goodsvpcdetails)) {
            echo json_encode(array('code' => 0, 'msg' => '操作成功', 'vpc_id' => $this->request->data['vpc_id']));exit;
        } else {
            echo json_encode(array('code' => 1, 'msg' => '操作失败'));exit;
        }
    }

    //子网cidr排重
    public function cidr()
    {
        $cidr             = $this->request->data['cidr'];
        $id = $this->request->data['vpc_id'];
        $goods_vpc_detail = TableRegistry::get('GoodsVpcDetail');
        $count            = $goods_vpc_detail->find()->select(['id'])->where(array('subnet_cidr' => $cidr,'vpc_id'=>$id))->count();
        if ($count > 0) {
            echo json_encode(array('code' => 1, 'msg' => ''));exit;
        } else {
            echo json_encode(array('code' => 0, 'msg' => ''));exit;
        }
    }

    //添加ecs,云桌面，火墙
    public function addecs($vpc_id = 0, $id = 0)
    {
        $goods_vpc_detail = TableRegistry::get('GoodsVpcDetail');
        $goods_vpc = TableRegistry::get('GoodsVpc');
        if (!$vpc_id) {
            $this->redirect('/admin/goods-vpc');
        } else {
            //获取vpc配置单对应的AgentID
            $agent_id = $goods_vpc->getAgentIDByGVPCID($vpc_id)["agent_id"];
            if ($id) {
                $data = $goods_vpc_detail->find()->where(array('id' => $id))->first();
                $this->set('data', $data);
            }

            //显示镜像
            $imagelist      = TableRegistry::get('Imagelist');
            $imagelist_data = $imagelist->getImageByAgent($agent_id);
            $this->set('imagelist_data', $imagelist_data);

            //显示硬件套餐
            $set_hardware = TableRegistry::get('SetHardware');
            $set_data     = $set_hardware->getSetSWByAgent($agent_id);
            $this->set('set_data', $set_data);

            //显示硬件套餐
            $set_software = TableRegistry::get('GoodsVersionSpec');
            $desktopset_data     = $set_software->getDesktopSetByAgent($agent_id);
            $this->set('desktopset_data', $desktopset_data);

            $this->set('vpc_id', $vpc_id);

            //显示所属子网
            $subnet_data = $goods_vpc_detail->find()->select(['id', 'tagname'])->where(array('type' => 'subnet', 'vpc_id' => $vpc_id));
            $this->set('subnet_data', $subnet_data);

            //查询扩展子网
            $subnet_table = TableRegistry::get('InstanceBasic');
            $public_subnet = $subnet_table->getPublicSubnet();
            $this->set('_public_subnet',$public_subnet);

            $canCreate = $this->_isCanCreate($agent_id);
            $this->set('_canCreate',$canCreate);
            // debug($public_subnet);die;
        }
    }

    public function addecspost()
    {
        $goods_vpc_detail = TableRegistry::get('GoodsVpcDetail');
        $imagelist        = TableRegistry::get('Imagelist');
        $set_hardware     = TableRegistry::get('SetHardware');
        if ($this->request->data['type'] != 'firewall' && $this->request->data['type']!='elb') {
            $set_data                             = $set_hardware->find()->select(['set_name'])->where(array('set_code' => $this->request->data['instance_code']))->first();
            $imagelist_data                       = $imagelist->find()->select(['image_name'])->where(array('image_code' => $this->request->data['image_code']))->first();
            $this->request->data['instance_name'] = $set_data['set_name'];
            $this->request->data['image_name']    = $imagelist_data['image_name'];
        } else if($this->request->data['type'] == 'elb') {
            $Systemsetting_table                  = TableRegistry::get('Systemsetting');
            $elb_imageCode                   = $Systemsetting_table->find()->select(['para_value'])->where(['para_code' => 'lbs_imageCode'])->first()->para_value;
            $elb_instanceTypeCode            = $Systemsetting_table->find()->select(['para_value'])->where(['para_code' => 'lbs_instanceTypeCode'])->first()->para_value;
            $this->request->data['image_code']    = $elb_imageCode;
            $this->request->data['instance_code'] = $elb_instanceTypeCode;
            $set_data                             = $set_hardware->find()->select(['set_name'])->where(array('set_code' => $this->request->data['instance_code']))->first();
            $imagelist_data                       = $imagelist->find()->select(['image_name'])->where(array('image_code' => $this->request->data['image_code']))->first();
            $this->request->data['instance_name'] = $set_data['set_name'];
            $this->request->data['image_name']    = $imagelist_data['image_name'];
        }else{
            $Systemsetting_table                  = TableRegistry::get('Systemsetting');
            $firewall_imageCode                   = $Systemsetting_table->find()->select(['para_value'])->where(['para_code' => 'firewall_imageCode'])->first()->para_value;
            $firewall_instanceTypeCode            = $Systemsetting_table->find()->select(['para_value'])->where(['para_code' => 'firewall_instanceTypeCode'])->first()->para_value;
            $this->request->data['image_code']    = $firewall_imageCode;
            $this->request->data['instance_code'] = $firewall_instanceTypeCode;
            $set_data                             = $set_hardware->find()->select(['set_name'])->where(array('set_code' => $this->request->data['instance_code']))->first();
            $imagelist_data                       = $imagelist->find()->select(['image_name'])->where(array('image_code' => $this->request->data['image_code']))->first();
            $this->request->data['instance_name'] = $set_data['set_name'];
            $this->request->data['image_name']    = $imagelist_data['image_name'];
        }
        $goodsvpcdetails = $goods_vpc_detail->newEntity();
        $data = $this->request->data;
        if(!empty($data["subnet_id"])){
            if($data["subnet_id"]!="0"&&!empty($data["subnet_id"])){
                $data["subnet_tag"]=$goods_vpc_detail->get($data["subnet_id"])->tagname;
            }
        }

        $goodsvpcdetails = $goods_vpc_detail->patchEntity($goodsvpcdetails, $data);
        if (!isset($goodsvpcdetails['sort_order'])) {
            $goodsvpcdetails['sort_order'] = 0;
        }
        if ($goods_vpc_detail->save($goodsvpcdetails)) {
            echo json_encode(array('code' => 0, 'msg' => '操作成功', 'vpc_id' => $this->request->data['vpc_id']));exit;
        } else {
            echo json_encode(array('code' => 1, 'msg' => '操作失败'));exit;
        }
    }

    //删除vpc
    public function deletevpc()
    {
        $public = new PublicController();
        if ($this->request->is('post')) {
            $goods_vpc = TableRegistry::get('GoodsVpc');
            $vpc_id    = $this->request->data['vpc_id'];
            $message   = array('code' => 1, 'msg' => '操作失败');
            $data      = $goods_vpc->find()->where(['vpc_id' => $vpc_id])->first();
            //如果是共享存储，则需要删除VPC信息存储关联表
            $vpc_store_table = TableRegistry::get('VpcStoreExtend');
            $vpc_store_table->deleteAll(array('vpcId'=>$data["vpc_id"]));
            if ($goods_vpc->deleteAll(array('vpc_id' => $vpc_id))) {
                $goods_vpc_detail = TableRegistry::get('GoodsVpcDetail');
                $count            = $goods_vpc_detail->find()->where(array('vpc_id' => $vpc_id))->count();
                $result           = $goods_vpc_detail->deleteAll(array('vpc_id' => $vpc_id));
                if ($count == $result) {
                    $message = array('code' => 0, 'msg' => '操作成功');
                    $public->adminlog('GoodsVpc', '删除vpc编排---' . $data['vpc_name']);
                }
            }

            echo json_encode($message);exit;
            // $this->lauout = 'ajax';
        }
    }

    //删除vpc配置
    public function deletevpcdetail()
    {
        $public = new PublicController();
        if ($this->request->is('post')) {
            $goods_vpc_detail = TableRegistry::get('GoodsVpcDetail');
            $id               = $this->request->data['id'];
            $message          = array('code' => 1, 'msg' => '操作失败');
            $data             = $goods_vpc_detail->find()->where(['id' => $id])->first();
            //如果是共享存储，则需要删除VPC信息存储关联表
            if($data["type"]=="oceanstor9k"||$data["type"]=="fics"){
                $vpc_store_table = TableRegistry::get('VpcStoreExtend');
                $vpc_store_table->deleteAll(array('vpcId'=>$data["vpc_id"]));
            }
            if ($goods_vpc_detail->deleteAll(array('id' => $id))) {
                $message = array('code' => 0, 'msg' => '操作成功');
                $public->adminlog('GoodsVpc', '删除vpc配置---' . $data['tagname']);
            }
            echo json_encode($message);exit;
            // $this->lauout = 'ajax';
        }
    }
//    子网是否绑定ecs
    public function subnetfree(){
        $id=isset($this->request->query['id'])?$this->request->query['id']:'';
        $goods_vpc_detail = TableRegistry::get('GoodsVpcDetail');
        if(!empty($id)){
            $info=$goods_vpc_detail->find()->select(['type','tagname'])->where(array('id'=>$id))->first();
            if($info['type']=='subnet'){
                $res=$goods_vpc_detail->find()->where(array('or'=>array('subnet_tag'=>$info['tagname'],'subnet2_tags'=>$info['tagname'])))->count();
                if($res>0){
                    echo json_encode(array('code'=>0,'msg'=>'不能删除'));exit;
                }else{
                    echo json_encode(array('code'=>1,'msg'=>'可删除'));exit;
                }
            }else{
                echo json_encode(array('code'=>1,'msg'=>'非子网'));exit;
            }
        }else{
            echo json_encode(array('code'=>1,'msg'=>'未选择子网'));exit;
        }
    }

    public function copyvpc()
    {
        $vpc_id           = $this->request->data['vpc_id'];
        $goods_vpc        = TableRegistry::get('GoodsVpc');
        $goods_vpc_detail = TableRegistry::get('GoodsVpcDetail');
        $public           = new PublicController();
        $base_data        = $goods_vpc->find()->where(array('vpc_id' => $vpc_id))->first();
        unset($base_data['vpc_id']);
        $base_data['vpc_name'] = $base_data['vpc_name'] . '(复制)';
        $base_data['create_time'] = time();
        $base_data->isNew(true);
        $result                = $goods_vpc->save($base_data);
        if ($result) {

            $detail_data = $goods_vpc_detail->find()->where(array('vpc_id' => $vpc_id))->toArray();
            if (!empty($detail_data)) {
                foreach ($detail_data as $detail) {
                    unset($detail['id']);
                    $detail['vpc_id']        = $result['vpc_id'];
                    $detail->isNew(true);
                    $goods_vpc_detail->save($detail);
                }
            }

            $public->adminlog('GoodsVpc', '复制vpc编排' . $base_data['vpc_name'] . '------>成功');
            echo json_encode(array('code' => 0, 'msg' => '复制vpc编排成功'));exit;

        } else {
            $public->adminlog('GoodsVpc', '复制vpc编排' . $base_data['vpc_name'] . '------>失败');
            echo json_encode(array('code' => 1, 'msg' => '复制vpc编排失败'));exit;
        }
    }

    public function firewall()
    {
        $vpc_id           = $this->request->data['vpc_id'];
        $goods_vpc_detail = TableRegistry::get('GoodsVpcDetail');
        $count            = $goods_vpc_detail->find()->select(['id'])->where(array('vpc_id' => $vpc_id, 'type' => 'firewall'))->count();
        if ($count > 0) {
            echo json_encode(array('code' => 1, 'msg' => ''));exit;
        } else {
            echo json_encode(array('code' => 0, 'msg' => ''));exit;
        }
    }

    public function elb()
    {
        $vpc_id           = $this->request->data['vpc_id'];
        $goods_vpc_detail = TableRegistry::get('GoodsVpcDetail');
        $count            = $goods_vpc_detail->find()->select(['id'])->where(array('vpc_id' => $vpc_id, 'type' => 'elb'))->count();
        if ($count > 0) {
            echo json_encode(array('code' => 1, 'msg' => ''));exit;
        } else {
            echo json_encode(array('code' => 0, 'msg' => ''));exit;
        }
    }

    public function havefirewall(){
        $vpc_id           = $this->request->data['vpc_id'];
        $goods_vpc_detail = TableRegistry::get('GoodsVpcDetail');
        $count            = $goods_vpc_detail->find()->select(['id'])->where(array('vpc_id' => $vpc_id, 'type' => 'firewall'))->count();
    }

    // public function isCanCreate($vpc_id){
    //     $goods_vpc = TableRegistry::get('GoodsVpc');
    //     $table = TableRegistry::get('Agent');
    //     $agent_id = $goods_vpc->getAgentIDByGVPCID($vpc_id)["agent_id"];
    //     $this->layout = false;
    //     $table = TableRegistry::get('Agent');
    //     $entity = $table->get($agent_id);
    //     if($entity->is_enabled==0){
    //         echo "false";exit;
    //     }else{
    //         //可以建设备
    //         if($entity->is_desktop==1){//可以建桌面
    //             echo "desktop";exit;
    //         }else{
    //             echo "issa";exit;
    //         }
    //     }
    // }

    public function _isCanCreate($agent_id){
        $table = TableRegistry::get('Agent');
        $entity = $table->get($agent_id);
        if($entity->is_enabled==0){
            return "false";
        }else{
            //可以建设备
            if($entity->is_desktop==1){//可以建桌面
                return "desktop";
            }else{
                return "issa";
            }
        }
    }

    public function getGoodsInfoByID($id){
        $table = TableRegistry::get('Goods');
        $entity = $table->findById($id)->first();
        return $entity;
    }
}
