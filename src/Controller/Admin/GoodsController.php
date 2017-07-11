<?php
/**
 * 文件描述文字
 *
 *
 * @author XingShanghe<xingshanghe@gmail.com>
 * @date  2015年9月8日下午3:39:25
 * @source AccountsController.php
 * @version 1.0.0
 * @copyright  Copyright 2015 sobey.com
 */
namespace App\Controller\Admin;

use App\Controller\AdminController;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Composer\Autoload\ClassLoader;
use PHPExcel_IOFactory;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Configure;

class GoodsController extends AdminController
{
    public $_pageList = array(
        'total' => 0,
        'rows'  => array(),
    );

    public $paginate = [
        'limit' => 15,
    ];
    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow('image');
        $this->Auth->allow('image2');
        $this->loadComponent('Paginator');
        $this->loadComponent('RequestHandler');
        /*  $checkPopedomlist = parent::checkPopedomlist('bgm_commodity_manage');
            if (! $checkPopedomlist) {
            return $this->redirect('/admin/');
        }*/
    }

    public function selectVersion(){
        $gid = intval($_GET['gid']);
        $resource_type = Configure::read('resource_type');
        $GoodsVersionM = TableRegistry::get('GoodsVersion');
        $this->set('resource_type',$resource_type);
        if ($_POST) {
            $GoodsVersionM = TableRegistry::get('GoodsVersion');
            //判断版本类型是否一致
            $utype = '';
            $exist_publish=0;
            if (isset($_POST['ids']) && !empty($_POST['ids'])) {
                foreach ($_POST['ids'] as $key => $value) {
                    $good_id=$GoodsVersionM->find()->select(['gid'])->where(array('id'=>$value))->first()['gid'];
                    if(!empty($good_id)&&$good_id!=$gid) {
                        $publish = TableRegistry::get('Goods')->find()->where(array('id' => $good_id,'goodStatus'=>1))->count();
                        if($publish>0){
                            $exist_publish+=1;
                        }
                    }

                    $type = $GoodsVersionM->find()->where(array('id' => $value))->first()['type'];
                    if ($utype != '') {
                        if ($utype != $type) {
                            return $this->redirect(array('controller' => 'Goods', 'action' => 'select_version', 'gid' => $gid, 'unsimilar' => 1));
                        }
                    } else {
                        $utype = $type;
                    }
                }
                //选择的版本关联已发布的商品
                if($exist_publish>0){
                    return $this->redirect(array('controller' => 'Goods', 'action' => 'select_version', 'gid' => $gid, 'published' => 1));
                }


                //清除原关联版本
                $delete = $GoodsVersionM->updateAll(array('gid' => ''), array('gid' => $gid));
                //修改版本类型
                foreach ($_POST['ids'] as $key => $value) {
                    $entity = $GoodsVersionM->find()->where(array('id' => $value))->first();
                    $entity = $GoodsVersionM->patchEntity($entity, array(
                        'gid' => $gid
                    ));
                    $GoodsVersionM->save($entity);
                }
                //商品更新类型
                $goods = TableRegistry::get('Goods');
                $update = $goods->updateAll(array('goodType' => $utype), array('id' => $gid));
                return $this->redirect(array('controller' => 'Goods', 'action' => 'indexNew'));
            } else {
                $delete = $GoodsVersionM->updateAll(array('gid' => ''), array('gid' => $gid));
                return $this->redirect(array('controller' => 'Goods', 'action' => 'indexNew'));
            }
        }

        //获取所有IDS
        $all = $GoodsVersionM->find()->where(array('gid'=>$gid))->select(['id']);
        $allArr=[];
        foreach ($all as $key => $value) {
           $allArr[]=$value->id;
        }

        $this->set('data',json_encode($allArr));
    }

    public function addGoods(){
        $goods = TableRegistry::get('Goods');
        if(isset($_GET['gid'])){
            $vinfo =$goods->find()->where(array('id'=>$_GET['gid']))->first();
            $this->set('vinfo',$vinfo);
        }
        if($_POST){
            if(isset($vinfo) && isset($vinfo->id)){
                $newGoods = $vinfo;
            }else{
                $newGoods= $goods->newEntity();
            }
            $newGoods = $goods->patchEntity($newGoods,array(
                        'name'=>$_POST['name'],
                        'category_id'=>$_POST['category_id'],
                        'remark'=>$_POST['remark'],
                        'sort'=>$_POST['sort']
                    ));
                $goods->save($newGoods);
                return $this->redirect(array('controller' => 'Goods', 'action' => 'indexNew'));
        }
        //$resource_type = Configure::read('resource_type');
       // $this->set('resource_type',$resource_type);
        $category = TableRegistry::get('goods_category')->find()->select();
        $this->set('category',$category);
    }
    public function pushGoods($id,$status){

        $this->autoRender = false;
        //检查
        $goods = TableRegistry::get('Goods');
        $vinfo =$goods->find()->where(array('id'=>$id))->first();
        $json = array(
                'code'=>1,
                'msg'=>'',
            );

        if($vinfo['detail'] == ''){
            $json['msg']='商品详情不能为空';
            exit(json_encode($json));
        }

        //检查版本
        $count = TableRegistry::get('cp_goods_version')->find()->where(array('gid'=>$vinfo->id))->count();
        if($count<1){
            $json['msg']='商品没有关联版本';
            exit(json_encode($json));
        }
        $list = TableRegistry::get('goodsVersion')->find()->where(array('gid'=>$vinfo->id))->select();
        foreach ($list as $key => $value) {
            //检查订单流程
            $param  = $value->getDetails();
            if(!isset($param['processid']) || $param['processid'] =="" || $param['processid'] =="0" ){
                $json['msg']='商品关联版本-'.$value->name.'：还没有设置订单流程';
                exit(json_encode($json));
            }
            //检查定价
            if(!in_array($value->type, array('ecs', 'vpc', 'eip', 'elb', 'ebs', 'disks'))){
                $prices = $value->getPrices();
                if(count($prices->toArray()) <1){
                     $json['msg']='商品关联版本-'.$value->name.'：似乎还没定价呢！';
                     exit(json_encode($json));
                }
            }
        }



        $vinfo = $goods->patchEntity($vinfo,array(
                        'goodStatus'=>$status,
                    ));
        $goods->save($vinfo);
        $json['code'] = 0;
        exit(json_encode($json));
    }

    public function editDatail(){
        $goods = TableRegistry::get('Goods');
        if(isset($_GET['gid'])){
            $vinfo =$goods->find()->where(array('id'=>$_GET['gid']))->first();
            $this->set('vinfo',$vinfo);
        }
        if($_POST){
//            if(isset($vinfo) && isset($vinfo->id)){
//                $newGoods = $vinfo;
//
//                $newGoods = $goods->patchEntity($newGoods,array(
//
//                        'description'=>$_POST['description'],
//                        'detail'=>$_POST['detail'],
//                        'icon'=>$_POST['icon'],
//                        'picture'=>$_POST['picture'],
//                        'mini_icon'=>$_POST['mini_icon'],
//                    ));
//                $goods->save($newGoods);
//                return $this->redirect(array('controller' => 'Goods', 'action' => 'indexNew'));
//            }
            $res=$goods->updateAll(
                array(
                        'description'=>$_POST['description'],
                        'detail'=>$_POST['detail'],
                        'icon'=>$_POST['icon'],
                        'picture'=>$_POST['picture'],
                        'mini_icon'=>$_POST['mini_icon'],
                    ),array('id'=>$_POST['id'])
            );
            if($res) {
                echo json_encode(array('code'=>0,'msg'=>'修改成功'));exit;
            }else{
                echo json_encode(array('code'=>1,'msg'=>'修改失败'));exit;
            }
        }
    }

    public function indexNew(){
        $category = TableRegistry::get('goods_category')->find()->select();
        $this->set('category',$category);
    }

    public function getIndexList(){

        $this->autoRender = false;
        $goods = TableRegistry::get('Goods');
        $where= array();
        if(isset($_GET['goodType']) && $_GET['goodType']!="all" && $_GET['goodType']!="" ){
            $where['category_id']=$_GET['goodType'];
        }
        if(isset($_GET['goodStatus']) && $_GET['goodStatus']!="all" && $_GET['goodStatus']!="" ){
            $where['goodStatus']=$_GET['goodStatus'];
        }
        if(isset($_GET['name']) && $_GET['name']!="all" && $_GET['name']!="" ){
            $where['Goods.name LIKE']="%".$_GET['name']."%";
        }
        $where['Goods.fixed'] = 0;
        $where['OR'] = array(array("Goods.goodType <>" => "onvpc"), array("Goods.goodType is" => null));
        $pageNumber = (intval(@$_GET['pageNumber']) >0)?intval(@$_GET['pageNumber']):1;
        $pageSize = (intval(@$_GET['pageSize']) >0)?intval(@$_GET['pageSize']):5;
        $list = $goods->find()->contain([
                        'GoodsCategory',
                    ])->where($where)->page($pageNumber,$pageSize)->toArray();
        foreach ($list as $key => $value) {
            $list[$key]['_id'] = $value['id'];
        }
        $json = array(
            'total'=>$goods->find()->where($where)->count(),
            'rows'=>$list,
            );
        echo json_encode($json);
    }

    public function price(){
        $vid = intval($_GET['vid']);
        $versionM = TableRegistry::get('GoodsVersion');
        $GoodsVersionDetailM = TableRegistry::get('GoodsVersionDetail');
        $GoodsVersionSpecM = TableRegistry::get('GoodsVersionSpec');
        $vinfo = $versionM->find()->where(array('id'=>$_GET['vid']))->first();
        $this->set('vinfo',$vinfo);
        if($vinfo->type =='citrix'){
            $spec = $GoodsVersionDetailM->find()->where(array('vid'=>$vinfo->id,'key'=>'specid'))->first();
            $price= $GoodsVersionSpecM->find()->where(array('id'=>$spec->value))->first();
            $priceInfo =$price->toArray();

            $image = TableRegistry::get('Imagelist')->find()->where(array('image_code'=>$priceInfo['image_code']))->first();
            $priceInfo['image_name'] = $image->image_name;

            $instance = TableRegistry::get('SetHardware')->find()->where(array('set_code'=>$priceInfo['instancetype_code']))->first();
            $priceInfo['instancetype_name'] = @$instance->set_name;
            $this->set('priceInfo',$priceInfo);
            if($_POST){
                $st = $GoodsVersionSpecM->patchEntity($price,array(
                        'price'=>$_POST['price'],
                        'unit'=>$_POST['unit']
                    ));
                $GoodsVersionSpecM->save($st);
                $this->redirect(array('controller' => 'Goods', 'action' => 'version'));
            }
        }else if($vinfo->type=='bs' || $vinfo->type == "eip" || $vinfo->type == "vfw" || $vinfo->type == "waf"){
            $GoodsVersionPriceM = TableRegistry::get('GoodsVersionPrice');
            $ac = (isset($_GET['ac']))?$_GET['ac']:'index';
            if($ac=='addPost'){
                $this->autoRender = false;
                $newPrice = $GoodsVersionPriceM->newEntity();
                $newPrice = $GoodsVersionPriceM->patchEntity($newPrice,array(
                        'price'=>$_POST['price'],
                        'unit'=>$_POST['unit'],
                        'charge_mode'=>'oneoff',
                        'interval'=>'M',
                        'vid'=>$vinfo->id
                    ));
                $GoodsVersionPriceM->save($newPrice);
                exit('ok');
            }else if($ac=='editPost'){
                $this->autoRender = false;
                $price = $GoodsVersionPriceM->find()->where(array('id'=>$_POST['id']))->first();
                $price = $GoodsVersionPriceM->patchEntity($price,array(
                            'price'=>$_POST['price'],
                            'unit'=>$_POST['unit']
                        ));
                $GoodsVersionPriceM->save($price);
                exit('ok');
            }else if($ac=='del'){
                $this->autoRender = false;
                $GoodsVersionPriceM->deleteAll(array('id IN'=>explode(',',$_POST['ids'])));
                exit('ok');
            }


            $pricelist = $GoodsVersionPriceM->find()->where(array('vid'=>$vinfo->id))->select();
            $this->set('price',$pricelist);
        }else{
            $GoodsVersionPriceM = TableRegistry::get('GoodsVersionPrice');
            $priceInfo =$GoodsVersionPriceM->find()->where(array('vid'=>$vinfo->id))->first();

            $this->set('priceInfo',$priceInfo);
            if($_POST){
                if(!isset($priceInfo->id)){
                    $priceInfo = $GoodsVersionPriceM->newEntity();
                    $priceInfo->charge_mode = 'duration';
                    $priceInfo->unit = '1';
                    $priceInfo->vid=$vinfo->id;
                }

                $priceInfo = $GoodsVersionPriceM->patchEntity($priceInfo,array(
                        'price'=>$_POST['price'],
                        'interval'=>$_POST['unit']
                    ));
                $GoodsVersionPriceM->save($priceInfo);

                $this->redirect(array('controller' => 'Goods', 'action' => 'version'));
            }
        }
    }

    public function version(){
        $resource_type = Configure::read('resource_type');
        $this->set('resource_type',$resource_type);
    }

    public function cloneVersion(){
        $this->autoRender = false;
        $id = intval($_POST['id']);
        $versionM = TableRegistry::get('GoodsVersion');
        $versionDetailM = TableRegistry::get('GoodsVersionDetail');
        $versionPriceM = TableRegistry::get('GoodsVersionPrice');
        //克隆基础信息
        $info = $versionM->find()->where(array('id'=>$id))->first()->toArray();

        unset($info['id']);
        $info['name'] = $info['name'].'-克隆';
        $newVersion = $versionM->newEntity();
        $newVersion = $versionM->patchEntity($newVersion,$info);
        $newVersion = $versionM->save($newVersion);
        //克隆详细信息
        $details = $versionDetailM->find()->where(array('vid'=>$id))->select();
        foreach ($details as $key => $value) {
            $new = $versionDetailM->newEntity();
            $new = $versionDetailM->patchEntity($new,array(
                    'vid'=>$newVersion->id,
                    'key'=>$value->key,
                    'value'=>$value->value,
                ));
            $versionDetailM->save($new);
        }
        //克隆定价
        $priceList = $versionPriceM->find()->where(array('vid'=>$id))->select();
        foreach ($priceList as $key => $value) {
            $value = $value->toArray();
            unset($value['id']);
            $value['vid'] = $newVersion->id;
            $newPrice = $versionPriceM->newEntity();
            $newPrice = $versionPriceM->patchEntity($newPrice,$value);
            $newPrice = $versionPriceM->save($newPrice);
        }
        exit('ok');
    }

    public function delVersion(){
        $this->autoRender = false;
        $GoodsVersionDetailM = TableRegistry::get('GoodsVersionDetail');
        $versionM = TableRegistry::get('GoodsVersion');

        $versionM->deleteAll(array('id IN'=>explode(',',$_POST['id'])));
        $GoodsVersionDetailM->deleteAll(array('vid IN'=>explode(',',$_POST['id'])));
        exit("ok");

    }
    //删除版本时判断是否绑定有商品
    public function bindGoods(){
        $versionM = TableRegistry::get('GoodsVersion');
        if(isset($_POST['id'])){
            $id_arr=explode(',',trim($_POST['id'],','));
            foreach($id_arr as $key => $value){
                $exist=$versionM->find()->where(array('id'=>$value,'gid is not'=>null))->count();
                if($exist>0){
                    exit('yes');
                }
            }
            exit('no');
        }else{
            exit('no');
        }
    }

    public function verifyService(){
        $this->autoRender = false;
        $service_type = $_GET['service_type'];
        $service_brand= $_GET['service_brand'];

        $goods_version_detail_table = TableRegistry::get("GoodsVersionDetail");
        $res = $goods_version_detail_table->find()
        ->hydrate(false)
        ->join([
            "VersionDetail" => [
                'table' => 'cp_goods_version_detail',
                'type' => 'LEFT',
                'conditions' => 'GoodsVersionDetail.vid = VersionDetail.vid',
            ]
        ])
        ->where(["VersionDetail.key" => "service_brand", "VersionDetail.value" => $service_brand])
        ->where(["GoodsVersionDetail.key" => "service_type", "GoodsVersionDetail.value" => $service_type])
        ->toArray();

        if(is_array($res) && count($res) > 0){
            
                if(isset($_GET['vid'])){
                    if($res[0]['vid'] == $_GET['vid']){
                        echo 'true';exit();
                    }
                }
            echo 'false';
        }else{
            echo 'true';
        }
        
    }
    public function addVersion(){
        $this->set('isedit',0);
        $GoodsVersionDetailM = TableRegistry::get('GoodsVersionDetail');
        $versionM = TableRegistry::get('GoodsVersion');
        if(isset($_GET['vid'])){
            $vinfo = $versionM->find()->where(array('id'=>$_GET['vid']))->first();
            $this->set('vinfo',$vinfo);
            $vinfoDetails = $GoodsVersionDetailM->find()->where(array('vid'=>$_GET['vid']))->select()->toArray();
            $vinfoD = array();
            foreach ($vinfoDetails as $key => $value) {
                if($value['key'] =='subnet_extends'){
                    $vinfoD[$value['key']]=explode(',',$value['value']);
                }else{
                    $vinfoD[$value['key']]=$value['value'];
                }

            }
            $this->set('vinfoD',$vinfoD);
            $this->set('isedit',1);
        }
        if($_POST){

                if(!isset($vinfo)){
                    $version = $versionM->newEntity();
                    $version->create = time();
                }else{
                    $version =$vinfo;
                }
                 $version = $versionM->patchEntity($version,array(
                        'name'=>$_POST['name'],
                        'type'=>$_POST['type'],
                        'description'=>$_POST['discription']
                    ));

                $versionM->save($version);

                if($_POST['type'] == 'citrix'){
                    $arr = array(
                    'tenantid'=>$_POST['tenantid'],
                    'processid'=>$_POST['processid'],
                    'region'=>$_POST['region'],
                    'specid'=>$_POST['spec'],
                    'vpc'=>$_POST['vpc'],
                    'instance_name'=>$_POST['instance_name'],
                    'subnet'=>$_POST['subnet'],
                    );
                    $str ='';
                    if(isset($_POST['subnet_extends'])){
                        $str = implode(',',$_POST['subnet_extends']);
                    }
                    $arr['subnet_extends'] = $str;
                }else if($_POST['type'] == 'ecs'){
                    $arr = array(
                    'processid'=>$_POST['processid2'],
                    );
                }else if($_POST['type'] == 'citrix_public'){
                    $arr = array(
                    'processid'=>$_POST['processid2'],
                    'region'=>$_POST['region'],
                    'specid'=>$_POST['spec'],
                    );
                }else if($_POST['type'] == 'mpaas'){
                    if ($_POST['alltenants'] == "all"){
                        $tids == "all";
                    }else{
                        $tids = implode(',',$_POST['selectTenants']);
                    }

                    $arr = array(
                    'processid'=>$_POST['processid2'],
                    'tenantid'=>$tids,
                    'region'=>$_POST['region'],
                    'service_brand'=>$_POST['service_brand'],
                    'service_type'=>$_POST['service_type'],
                    );
                }else{
                    if ($_POST['alltenants'] == "all"){
                        $tids == "all";
                    }else{
                        $tids = implode(',',$_POST['selectTenants']);
                    }
                    
                    $arr = array(
                    'tenantid'=>$tids,
                    'processid'=>$_POST['processid2'],
                    );
                }

                foreach ($arr as $key => $value) {
                     if(isset($vinfo)){
                        $st = $GoodsVersionDetailM->find()->where(array('vid'=>$vinfo->id,'key'=>$key))->first();
                        if(!isset($st->id)){
                            $st = $GoodsVersionDetailM->newEntity();
                        }
                    }else{
                        $st = $GoodsVersionDetailM->newEntity();
                    }

                     $st = $GoodsVersionDetailM->patchEntity($st,array(
                        'vid'=>$version->id,
                        'key'=>$key,
                        'value'=>$value
                    ));
                     $GoodsVersionDetailM->save($st);
                }
                exit('success');
        }
        $workflow_template = TableRegistry::get('WorkflowTemplate');
        $resource_type = Configure::read('resource_type');
         $flow_data = $workflow_template->find()->toArray();

        $agents= TableRegistry::get('agent')->find()->toArray();
        $agentsFM = array();
        foreach ($agents as $key => $value) {
            if($value['parentid'] == 0){
                $value['subs'] = array();
                $agentsFM[$value['id']] =$value;
            }else{
                $agentsFM[$value['parentid']]['subs'][$value['id']] = $value;
            }
        }
        $brands= TableRegistry::get('GoodsVersionSpec')->find()->toArray();

        $brandsFM = array();
        $brandsIds=array();
        foreach ($brands as $key => $value) {
            $value['price'] = '';
            $prices =  TableRegistry::get('GoodsVersionPrice')->find()->where(array('sid'=>$value['id']))->order(array('interval'=>'desc'))->toArray();
            foreach ($prices as $k => $v) {
                if($value['price'] == ''){
                    $value['price'] .=$v['price'].$v['interval'];
                }else{
                    $value['price'] .='   '.$v['price'].$v['interval'];
                }
            }
            $arr = array_keys($brandsIds,$value['brand'],true);
            if(isset($arr[0])){
                $brandsFM[$arr[0]]['name']=$value['brand'];
                $brandsFM[$arr[0]]['subs'][]=$value;
            }else{
                $brandsIds[] = $value['brand'];
                $arr = array_keys($brandsIds,$value['brand'],true);
                $brandsFM[$arr[0]]['name']=$value['brand'];
                $brandsFM[$arr[0]]['subs'][]=$value;
            }
        }


        $service_brand = Configure::read('service_brand');
        $this->set('service_brand',$service_brand);
        $service_type = Configure::read('service_type');
        $this->set('service_type',$service_type);

        $this->set('agent',json_encode($agentsFM));
        $this->set('brand',json_encode($brandsFM));
        $this->set('flow_data', $flow_data);
        $this->set('resource_type',$resource_type);
    }

    public function getVpc(){
        $this->autoRender = false;
        $did = $_GET['did'];
        $vpc_code=array();
        $arr=TableRegistry::get('InstanceBasic')->find()->select(['vpc'])->where(array(
            'department_id'=>$did,
            'type in'=>['ad','ddc']
        ))->toArray();
        if(!empty($arr)) {
            foreach ($arr as $key => $value) {
                $vpc_code[] = $value['vpc'];
            }
        }
        $vpcs   = TableRegistry::get('InstanceBasic')->find()->where(
            array('department_id'=>$did,
                'type'=>'vpc',
                'code in'=>$vpc_code
                )
            )->toArray();
        $subnets = TableRegistry::get('InstanceBasic')->find()->where(
            array('department_id'=>$did,
                'type'=>'subnet'
                )
            )->toArray();
        echo json_encode(
                array('vpcs'=>$vpcs,
                    'subnets'=>$subnets
                    )
            );
    }

    public function versionData(){
        $this->autoRender = false;
        $goods = TableRegistry::get('GoodsVersion');
        $where= array();
        if(isset($_GET['goodType']) && $_GET['goodType']!="all" && $_GET['goodType']!="" ){
            $where['type']=$_GET['goodType'];
        }

        if(isset($_GET['name']) && $_GET['name']!="all" && $_GET['name']!="" ){
            $where['name LIKE']="%".$_GET['name']."%";
        }


        $pageNumber = (intval(@$_GET['pageNumber']) >0)?intval(@$_GET['pageNumber']):1;
        $pageSize = (intval(@$_GET['pageSize']) >0)?intval(@$_GET['pageSize']):5;
        $lists = $goods->find()->where($where)->page($pageNumber,$pageSize);
        $list = array();
        foreach ($lists as $key => $value) {
            $list[$key] = $value->toArray();
            $list[$key]['details'] = $value->getDetails();
            $list[$key]['_id'] = $value['id'];
            $list[$key]['goods_name'] = "";
            if ($value['gid'] != "" ) {
                 $list[$key]['goods_name'] = $this->__getVersionName($value['gid']);
            }
        }
        $json = array(
            'total'=>$goods->find()->where($where)->count(),
            'rows'=>$list,
            );
        echo json_encode($json);
    }

    private function __getVersionName($gid){
        $info = TableRegistry::get('Goods')->find()->where(array('id'=>$gid))->first();
        return (isset($info->name))?$info->name:"";
    }

    public function index($category_id = 0, $name = '')
    {
        if (!is_numeric($category_id)) {
            $name        = $category_id;
            $category_id = 0;
        }

        // $category_id = isset($this->request->data['category_id'])?$this->request->data['category_id']:$category_id;
        $goods = TableRegistry::get('Goods');

        $goods_category = TableRegistry::get('GoodsCategory');
        // $where['department_id']=$department_id;
        $arr = array(
            'category_id =' . $category_id,
        );
        $de    = $this->get_catid($arr, $category_id);
        $de_id = $de['cat_id'];

        $where = array();
        //一键vpc商品筛选
        $where['goods_vpc is not']=null;
//        $where['goodType']='onvpc';
        // filter条件拼凑
        if ($name) {
            $where['Goods.name like'] = '%' . $name . '%';
        }

        // TODO
        if ($category_id == 0) {
            $data = $goods->find('all')
                ->contain([
                    'GoodsCategory',
                ])
                ->where(['fixed' => 0])
                ->where($where);
            $cat_name['name'] = '全部';
        } else {
            $cat_name = $goods_category->find()
                ->where([
                    'id' => $category_id,
                ])
                ->select([
                    'name',
                ])
                ->first();
            if (isset($de_id)) {
                $data = $goods->find('all', array(
                    'conditions' => array(
                        'OR' => $de_id,
                    ),
                ))
                    ->contain([
                        'GoodsCategory',
                    ])
                    ->where(['fixed' => 0])
                    ->where($where);
            } else {
                $data = $goods->find('all')
                    ->contain([
                        'GoodsCategory',
                    ])
                    ->where(['fixed' => 0])
                    ->where($where);
            }
        }
        $rs = $this->paginate($data);

        // var_dump($cat_name);exit;

        $this->set('category_id', $category_id);
        $this->set('name', $name);
        $this->set('cat_name', $cat_name['name']);
        $this->set('data', $rs->toArray());
        $this->set('de', $de['depart']);
    }

    /*
     *添加商品
     */
    public function add()
    {
        $service_type      = TableRegistry::get('ServiceType');
        $goods_category    = TableRegistry::get('GoodsCategory');
        $workflow_template = TableRegistry::get('WorkflowTemplate');
        $goods_spec_define = TableRegistry::get('GoodsSpecDefine');
        $goods_vpc         = TableRegistry::get('GoodsVpc');
        $query             = $goods_spec_define->find('all', array(
            'group' => array(
                'group_id',
            ),
        ))
            ->select([
                'group_id',
                'group_name',
            ])
            ->toArray();
        // 动态配置behavior
        $goods_category->behaviors()->SobeyTree->config('scope', [
            '1' => 1,
        ]);
        $data = $goods_category->find('optionList')
            ->select([
                'id',
                'name',
                'parent_id',
            ])
            ->toArray();

        $flow_data = $workflow_template->find()->toArray();

        $goods_vpc_data = $goods_vpc->find()->select(['vpc_id', 'vpc_name'])->toArray();
        $service_data   = $service_type->find('all')->toArray();
        //获取计费
        $billcycle = parent::_GetBillCycle(null);
        $this->set('billcycle',$billcycle);
        $this->set('goods_vpc_data', $goods_vpc_data);
        $this->set('service_data', $service_data);
        $this->set('flow_data', $flow_data);
        $this->set('data', $data);
        $this->set('query', $query);

    }

    /*
     *编辑商品
     */
    public function editadd($id = 0)
    {
//         debug($this->request->data);die;
        $public           = new PublicController();
        $goods            = TableRegistry::get('Goods');
        $goods_spec       = TableRegistry::get('GoodsSpec');
        $this->autoRender = false;
        $message          = array(
            'code' => 1,
            'msg'  => '操作失败',
        );
        if ($this->request->is('post')) {
            if (isset($this->request->data['id'])) {
                $id = $this->request->data['id'];
                $sn = $this->request->data['sn'];
                $rs = $goods->find('all', array(
                    'conditions' => array(
                        'sn ='  => $sn,
                        'id <>' => $id,
                    ),
                ))->toArray();
                if (!empty($rs)) {
                    $message = array(
                        'code' => 1,
                        'msg'  => '序列号重复',
                    );
                    echo json_encode($message);
                    exit();
                }
                // var_dump($rs);exit;
                $category = $goods->newEntity();
                // debug($this->request->data);die();
                if ((int) $this->request->data["goods_buytype"] == 1) {
                    // $this->request->data["goods_vpc"] =$this->request->data["goods_txt_buytype"];
                    $this->request->data["attribute_ids"]='';
                } else if ((int) $this->request->data["goods_buytype"] == 2) {
                    $this->request->data["attribute_ids"] = $this->request->data["goods_txt_buytype"];
                    $str                                  = $this->request->data["goods_txt_buytype"];
                    $bbb                                  = explode(',', $str);
                    $str                                  = implode(',', array_unique($bbb));
                    $this->request->data["attribute_ids"] = $str;
                    $this->request->data["goods_vpc"]=0;
                }
                if(empty($this->request->data["user_chargings"])){
                    $this->request->data["user_chargings"]=$this->request->data["goods_charging_ways"].",";
                }

                // debug($this->request->data);die();
                if(!empty($this->request->data["goods_vpc"])){
                    $this->request->data["goodType"]="onvpc";
                }
                $category         = $goods->patchEntity($category, $this->request->data);
                $result           = $goods->save($category);
                $info['goods_id'] = $id;
                $res              = $goods_spec->deleteAll(array(
                    'goods_id' => $info['goods_id'],
                ));
            } else {
                // debug($this->request->data);die();
                // var_dump($this->request->data);
                $sn = $this->request->data['sn'];
                $rs = $goods->find('all', array(
                    'conditions' => array(
                        'sn =' => $sn,
                    ),
                ))->toArray();
                if (!empty($rs)) {
                    $message = array(
                        'code' => 1,
                        'msg'  => '序列号重复',
                    );
                    echo json_encode($message);
                    exit();
                }
                $category = $goods->newEntity();
                if ((int) $this->request->data["goods_buytype"] == 1) {
                    $this->request->data["goods_vpc"] = $this->request->data["goods_txt_buytype"];
                } else if ((int) $this->request->data["goods_buytype"] == 2) {
                    $str                                  = $this->request->data["goods_txt_buytype"];
                    $bbb                                  = explode(',', $str);
                    $str                                  = implode(',', array_unique($bbb));
                    $this->request->data["attribute_ids"] = $str;
                }
                if(empty($this->request->data["user_chargings"])){
                    $this->request->data["user_chargings"]=$this->request->data["goods_charging_ways"].",";
                }
                $category = $goods->patchEntity($category, $this->request->data);

                $result           = $goods->save($category);
                $info['goods_id'] = $result->id;
                $res              = $goods_spec->deleteAll(array(
                    'goods_id' => $info['goods_id'],
                ));
            }
            if (!empty($this->request->data["display_spec_name"])) {
                $_display_spec_name  = explode(',', $this->request->data["display_spec_name"]);
                $_display_spec_value = explode(',', $this->request->data["display_spec_value"]);
                $_display_spec_code  = explode(',', $this->request->data["display_spec_code"]);

                foreach ($_display_spec_name as $_d_k => $value) {
                    if (!empty($value)) {
                        $info['spec_name']  = $value;
                        $info['spec_value'] = $_display_spec_value[$_d_k];
                        $info['spec_code']  = $_display_spec_code[$_d_k];
                        $info['is_display'] = 1;
                        $info['is_need']    = 0;
                        $goodsspec          = $goods_spec->newEntity();
                        $goodsspec          = $goods_spec->patchEntity($goodsspec, $info);
                        $query              = $goods_spec->save($goodsspec);
                    }
                }
                if (empty($this->request->data['id'])) {
                    $public->adminlog('GoodsSpec', '添加商品规格---' . $this->request->data['name']);
                } else {
                    $public->adminlog('GoodsSpec', '修改商品规格---' . $this->request->data['name']);
                }
            }
            if (!empty($this->request->data["need_spec_name"])) {
                $_need_spec_name  = explode(',', $this->request->data["need_spec_name"]);
                $_need_spec_value = explode(',', $this->request->data["need_spec_value"]);
                $_need_spec_code  = explode(',', $this->request->data["need_spec_code"]);

                foreach ($_need_spec_name as $_n_k => $_n_v) {
                    if (!empty($_n_v)) {
                        $info['spec_name']  = $_n_v;
                        $info['spec_value'] = $_need_spec_value[$_n_k];
                        $info['spec_code']  = $_need_spec_code[$_n_k];
                        $info['is_display'] = 0;
                        $info['is_need']    = 1;
                        $goodsspec          = $goods_spec->newEntity();
                        $goodsspec          = $goods_spec->patchEntity($goodsspec, $info);
                        $query              = $goods_spec->save($goodsspec);
                    }
                }
                if (empty($this->request->data['id'])) {
                    $public->adminlog('GoodsSpec', '添加商品规格---' . $this->request->data['name']);
                } else {
                    $public->adminlog('GoodsSpec', '修改商品规格---' . $this->request->data['name']);
                }
            }
            // 保存分类的拥有的属性信息
            if ($result) {
                $message = array(
                    'code' => 0,
                    'msg'  => '操作成功',
                );
                if (empty($this->request->data['id'])) {
                    $public->adminlog('Goods', '添加商品---' . $this->request->data['name']);
                } else {
                    $public->adminlog('Goods', '修改商品---' . $this->request->data['name']);
                }
            }
        }

        echo json_encode($message);
        exit();
        $this->lauout = 'ajax';
    }

    public function edit($id = 0)
    {
        $id                = isset($this->request->data['id']) ? $this->request->data['id'] : $id;
        $service_type      = TableRegistry::get('ServiceType');
        $goods_category    = TableRegistry::get('GoodsCategory');
        $goods_spec_define = TableRegistry::get('GoodsSpecDefine');
        $goods_spec        = TableRegistry::get('GoodsSpec');
        $workflow_template = TableRegistry::get('WorkflowTemplate');

        $query = $goods_spec_define->find('all', array(
            'group' => array(
                'group_id',
            ),
        ))
            ->select([
                'group_id',
                'group_name',
            ])
            ->toArray();
        // 动态配置behavior
        $goods_category->behaviors()->SobeyTree->config('scope', [
            '1' => 1,
        ]);
        $data = $goods_category->find('optionList')
            ->select([
                'id',
                'name',
                'parent_id',
            ])
            ->toArray();
        $this->set('data', $data);
        $goods = TableRegistry::get('Goods', [
            'classname' => 'App\Model\Table\GoodsTable',
        ]);
        $acc = $goods->find('all', array(
            'conditions' => array(
                'id =' => $id,
            ),
        ))->toArray(); // 获取该分类的信息
        $result = $goods_spec->find('all', array(
            'conditions' => array(
                'goods_id =' => $id,
            ),
        ))->toArray();
        // var_dump($result);exit;
        $flow_data      = $workflow_template->find()->toArray();
        $service_data   = $service_type->find('all')->toArray();
        $goods_vpc      = TableRegistry::get('GoodsVpc');
        $goods_vpc_data = $goods_vpc->find()->select(['vpc_id', 'vpc_name'])->toArray();
        //获取计费
        $billcycle = parent::_GetBillCycle(null);
        $this->set('billcycle',$billcycle);
        $this->set('goods_vpc_data', $goods_vpc_data);
        $this->set('service_data', $service_data);
        $this->set('flow_data', $flow_data);
        $this->set('acc', $acc);
        $this->set('data', $data);
        $this->set('query', $query);
        $this->set('result', $result);
    }

    public function deleteNew(){
        $this->autoRender = false;
        $connection = ConnectionManager::get('default');
        $is_true = preg_match("/^[0-9]+(\,[0-9]+)*$/", $_GET['ids']);
        if ($is_true) {
            $sql = "delete from cp_goods where id in(".$_GET['ids'].") AND fixed = 0";
            $connection->execute($sql);
        }
    }
    public function delete($id = 0)
    {
        $public           = new PublicController();
        $goods            = TableRegistry::get('Goods');
        $goods_spec       = TableRegistry::get('GoodsSpec');
        $this->autoRender = false;
        if ($this->request->is('post')) {
            $id     = isset($this->request->data['id']) ? $this->request->data['id'] : $id;
            $result = array(
                'code' => 1,
                'msg'  => '操作失败',
            );
            $data        = $goods->find()->where(['id' => $id])->first();
            $account     = $goods->get($id);
            $account     = $goods->patchEntity($account, $this->request->data);
            $account->id = $id;
            if ($goods->delete($account)) {
                $result = array(
                    'code' => 0,
                    'msg'  => '操作成功',
                );
                $public->adminlog('Goods', '删除商品---' . $data['name']);
            }
            $res = $goods_spec->deleteAll(array(
                'goods_id' => $id,
            ));
            echo json_encode($result);
            exit();
            $this->lauout = 'ajax';
        }
    }

    private function get_catid($arr, $cid)
    {
        $cat_id         = $arr;
        $goods_category = TableRegistry::get('GoodsCategory');
        if ($cid == 0) {
            $catid          = $goods_category->find('all')->toArray();
            $info['cat_id'] = $cat_id;
            $goods_category->behaviors()->SobeyTree->config('scope', [
                '1' => 1,
            ]);
            $depart = $goods_category->find('optionList')
                ->select([
                    'id',
                    'name',
                    'parent_id',
                ])
                ->toArray();
            $info['depart'] = $depart;

            return $info;
        }
        $catid = $goods_category->find('all')
            ->where(array(
                'parent_id' => $cid,
            ))
            ->toArray();
        // var_dump($catid);
        foreach ($catid as $value) {
            $cat_id[] = 'category_id =' . $value['id'];
            $cd       = $goods_category->find('all')
                ->where(array(
                    'parent_id' => $value['id'],
                ))
                ->count();
            if ($cd > 0) {
                $cat_id = $this->get_catid($cat_id, $value['id']);
            }
        }
        $info['cat_id'] = $cat_id;

        // 动态配置behavior
        $goods_category->behaviors()->SobeyTree->config('scope', [
            '1' => 1,
        ]);
        $depart = $goods_category->find('optionList')
            ->select([
                'id',
                'name',
                'parent_id',
            ])
            ->toArray();
        $info['depart'] = $depart;

        return $info;
    }

    public function checkSpec()
    {
        $group_id          = $this->request->data['id'];
        $goods_spec_define = TableRegistry::get('GoodsSpecDefine');
        $query             = $goods_spec_define->find('all', array(
            'conditions' => array(
                'group_id' => $group_id,
            ),
        ))
            ->select([
                'group_id',
                'spec_name',
                'spec_code',
                'is_display',
                'is_need',
            ])
            ->toArray();
        echo json_encode($query);
        exit();
        $this->lauout = 'ajax';
    }

    public function image()
    {
        header('Access-Control-Allow-Origin:*');
        // var_dump($_FILES);exit;
        if (!empty($_FILES)) {

            // 得到上传的临时文件流
            foreach ($_FILES['upfile']['tmp_name'] as $key => $tmp_name) {
                $i = $this->upload($tmp_name, $_FILES['upfile']['name'][$key],$returnPath);
            }
            if ($i) {
                if($returnPath){
                    echo json_encode($i);
                }else{
                    echo json_encode("上传成功！");
                }

            } else {
                echo json_encode("上传失败！");
            }
        }
        exit;
    }

    public function image2()
    {
        header('Access-Control-Allow-Origin:*');
        // var_dump($_FILES);exit;
        if (!empty($_FILES)) {


            $i = $this->upload($_FILES['upfile']['tmp_name'], $_FILES['upfile']['name'],true);

            if ($i) {

                $image = getimagesize("images/".$i);
                $data = [];
                $data['width'] = $image[0];
                $data['height'] = $image[1];
                $data['name']   = $i;
                echo json_encode($data);
            } else {
               echo 'error';
            }
        }
        exit;
    }

    private function upload($tmp_name, $name,$returnPath=false)
    {
        // 设置上传目录
        $path     = "images/";
        $tempFile = $tmp_name;

        // 允许的文件后缀
        $fileTypes = array(
            'jpg',
            'jpeg',
            'gif',
            'png',
        );

        // 得到文件原名
        $fileName  = iconv("UTF-8", "GB2312", $name);
        $fileParts = pathinfo($name);

        // 接受动态传值
        // $files = $_POST['typeCode'];
        // 最后保存服务器地址
        if (!is_dir($path)) {
            mkdir($path);
        }
        if($returnPath){
            $fileName=uniqid().'.'.$fileParts['extension'];
        }
        if (move_uploaded_file($tempFile, $path . $fileName)) {
            if($returnPath){
                return $fileName;
            }
            return 1;
        } else {
            return 0;
        }
    }

    /*
     *通过excel 上传商品属性
     */
    public function upexcel($good_id = 0)
    {
        $public = new PublicController();
        $this->set('good_id', $good_id);
        // var_dump($_FILES);
        // var_dump($_POST);exit;
        if (!empty($_FILES)) {
            $goods_id      = $this->request->data['good_id'];
            $good_spec_key = ['spec_name', 'spec_code', 'spec_value', 'is_display', 'is_need', 'sort_order'];
            // var_dump($good_spec_key);exit;
            $loader = new ClassLoader();
            $loader->add('PHPExcel', ROOT . DS . 'vendor' . DS . 'phpexcel');
            $loader->register();
            $path = "excel/";
            // 得到上传的临时文件流
            $tempFile = $_FILES['userfile']['tmp_name'];

            // 允许的文件后缀
            $fileTypes = array(
                'xls',
                'xlsx',
            );

            // 得到文件原名
            $fileName  = iconv("UTF-8", "GB2312", $_FILES["userfile"]["name"]);
            $fileParts = pathinfo($_FILES['userfile']['name']);

            // 最后保存服务器地址
            if (!is_dir($path)) {
                mkdir($path);
            }
            $time = time();
            $name = $time . '.' . pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION);
            if (move_uploaded_file($tempFile, $path . $name)) {
                if (pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION) == 'xls') {
                    $reader = PHPExcel_IOFactory::createReader('Excel5'); // use excel2007 for 2007 format
                } else {
                    $reader = PHPExcel_IOFactory::createReader('Excel2007'); // use excel2007 for 2007 format
                }
                $PHPExcel      = $reader->load($path . $name); // 载入excel文件
                $sheet         = $PHPExcel->getSheet(0); // 读取第一個工作表
                $highestRow    = $sheet->getHighestRow(); // 取得总行数
                $highestColumm = $sheet->getHighestColumn(); // 取得总列数

                /** 循环读取每个单元格的数据 */
                $GoodsSpec = TableRegistry::get('GoodsSpec');
                $res       = $GoodsSpec->deleteAll(array(
                    'goods_id' => $goods_id,
                ));
                for ($row = 3; $row <= $highestRow; $row++) {
//行数是以第1行开始
                    $good_spec = array();
                    $i         = 0;
                    for ($column = 'A'; $column < $highestColumm; $column++) {
//列数是以第0列开始
                        $good_spec[$good_spec_key[$i]] = $PHPExcel->getActiveSheet()->getCell("$column$row")->getValue();
                        $i++;
                    }
                    $good_spec['goods_id'] = $goods_id;
                    $goodSpec              = $GoodsSpec->newEntity();
                    $goodSpec              = $GoodsSpec->patchEntity($goodSpec, $good_spec);
                    $result                = $GoodsSpec->save($goodSpec);
                }
            }
            $this->redirect(array('controller' => 'Goods', 'action' => 'index'));
        }

    }

    public function goodspec($good_id)
    {

        $goods_spec_data = '';
        if (!empty($good_id)) {
            $goods_spec_table = TableRegistry::get('GoodsSpec');
            $goods_spec_data  = $goods_spec_table->find()->where(['goods_id' => $request_data['good_id']]);
            $goods_spec_data  = $this->paginate($data);

        }

        $this->set('goods_spec_data', $goods_spec_data);
        $this->set('good_id', $good_id);
    }
    /**
     * 保存商品属性信息
     * @date: 2016年5月3日 下午2:18:21
     * @author: wangjc
     * @return:
     */
    public function getGoodSpec()
    {
        $request_data            = $this->request->query;
        $this->paginate['limit'] = $request_data['limit'];
        $this->paginate['page']  = $request_data['offset'] / $request_data['limit'] + 1;
        if (!empty($request_data['good_id'])) {
            $goods_spec_table = TableRegistry::get('GoodsSpec');
            $goods_spec_data  = $goods_spec_table->find()->where(['goods_id' => $request_data['good_id']]);

            $this->_pageList['total'] = $goods_spec_table->find()->where(['goods_id' => $request_data['good_id']])->count();
            $this->_pageList['rows']  = $this->paginate($goods_spec_table->find()->where(['goods_id' => $request_data['good_id']]));
            echo json_encode($this->_pageList);
            exit;
        }
        exit;

    }

    public function attribute($name = 'index')
    {
        //获取厂商，地区
        $agent  = TableRegistry::get('Agent');
        $agents = $agent->find('all')->toArray();
        $this->set('agent', $agents);
        // $this->redirect(array('controller' => 'Goods', 'action' => 'attirbuteindex'));
    }

    //商品属性管理菜单
    public function attributelist()
    {
        $this->autoRender        = false;
        $request_data            = $this->request->query;
        $this->paginate['limit'] = $request_data['limit'];
        $this->paginate['page']  = $request_data['offset'] / $request_data['limit'] + 1;
        //table 获取数据
        $table = TableRegistry::get('GoodsAttribute');
        $where = array();
        if (isset($request_data["search"])) {
            if (!empty($request_data["search"])) {
                $where['attribute_name like'] = '%' . $request_data['search'] . '%';
            }
        }
        if (!empty($request_data['class_code'])) {
            $where['attribute_classCode like'] = $request_data['class_code'] . '%';
        }
        if (!empty($request_data['class_code2'])) {
            $where['attribute_classCode like'] = $request_data['class_code2'] . '%';
        } elseif (!empty($request_data['class_code'])) {
            $where['attribute_classCode like'] = $request_data['class_code'] . '%';
        }
        $this->_pageList['total'] = $table->find('all')->where($where)->order(array('create_time' => 'DESC'))->count();
        $this->_pageList['rows']  = $this->paginate($table->find('all')->where($where)->order(array('create_time' => 'DESC')));
        echo json_encode($this->_pageList);
    }

    /**
     * [copyAttribute 复制版本]
     * @return [json] [结果信息]
     */
    public function copyAttribute(){
        $id = $this->request->data['id'];
        $goods_attribute = TableRegistry::get('GoodsAttribute');
        $attribute =  $goods_attribute->find()->where(['id'=>$id])->first();
        $attribute['attribute_name'] = $this->request->data['attribute_name'];
        $attribute['create_time']    = time();
        unset($attribute['id']);
        $attribute->isNew(true);
        $result = $goods_attribute->save($attribute);
        if($result != false){
            $re = ['code'=>1,'msg'=>"复制成功！"];
        }else{
            $re = ['code'=>0,'msg'=>'复制失败！'];
        }
        echo json_encode($re);exit;
    }

    public function addAttribute()
    {
        //获取厂商，地区
        $agent  = TableRegistry::get('Agent');
        $agents = $agent->find('all')->toArray();
        $this->set('agent', $agents);
        //获取租户
        $departments = TableRegistry::get('Departments');
        $departments = $departments->find('all')->toArray();
        $this->set('departments', $departments);

        //硬件套餐
        $instance_set = TableRegistry::get('SetHardware');
        $instance_set = $instance_set->find('all')->toArray();
        $this->set('instance_set', $instance_set);
        //镜像
        $instance_image = TableRegistry::get('Imagelist');
        $instance_image = $instance_image->find('all')->toArray();
        $this->set('instance_image', $instance_image);
        //工作流
        $instance_work = TableRegistry::get('WorkflowTemplate');
        $instance_work = $instance_work->find('all')->toArray();
        $this->set('instance_work', $instance_work);
    }

    public function editattribute($id = 0)
    {
        // debug($this->request->data);die;
        if ($this->request->is('get')) {
            //获取详细信息
            $table = TableRegistry::get('GoodsAttribute');
            $table = $table->find("all")->contain(['GoodsAttributeDetail'])->where(array('GoodsAttribute.id' => $id))->first();
            $this->set('table', $table);
            //获取厂商，地区
            $agent  = TableRegistry::get('Agent');
            $agents = $agent->find('all')->toArray();
            $this->set('agent', $agents);
            //获取租户
            $departments = TableRegistry::get('Departments');
            $departments = $departments->find('all')->toArray();
            $this->set('departments', $departments);

            //硬件套餐
            $instance_set = TableRegistry::get('SetHardware');
            $instance_set = $instance_set->find('all')->toArray();
            $this->set('instance_set', $instance_set);
            //镜像
            $instance_image = TableRegistry::get('Imagelist');
            $instance_image = $instance_image->find('all')->toArray();
            $this->set('instance_image', $instance_image);
            //工作流
            $instance_work = TableRegistry::get('WorkflowTemplate');
            $instance_work = $instance_work->find('all')->toArray();
            $this->set('instance_work', $instance_work);

        } else {
            $request_data = $this->request->data;
            // debug($request_data);die();
            if ($id == 0) {
                $GoodsAttribute                  = TableRegistry::get('GoodsAttribute');
                $attribute                       = $GoodsAttribute->newEntity();
                $attribute->attribute_name       = $request_data["attribute_name"];
                $attribute->attribute_regionCode = $request_data["attribute_region"];
                $agent                           = TableRegistry::get('Agent');
                $agent                           = $agent->find("all")->where(array('region_code' => $request_data["attribute_region"]))->first();
                $attribute->attribute_classCode  = $agent->class_code;
                $attribute->attribute_className  = $agent->display_name;
                $attribute->create_time          = time();
                if ($GoodsAttribute->save($attribute)) {
                    $GoodsAttributeDetail                 = TableRegistry::get('GoodsAttributeDetail');
                    $attribute_detail                     = $GoodsAttributeDetail->newEntity();
                    $attribute_detail->goods_attribute_id = $attribute->id;
                    $attribute_detail->sell_tenant        = $request_data["departments"];
                    $attribute_detail->vpcCode            = $request_data["instance_vpc"];
                    $attribute_detail->subnetCode         = $request_data["instance_subnet"];
                    $attribute_detail->instance_name      = $request_data["name"];
                    if ((int) $request_data["product_type"] == 1) {
                        //云桌面
                        $attribute_detail->method  = "desktop_add";
                        $attribute_detail->set_ids = $request_data["set_ids"];
                    } else if ((int) $request_data["product_type"] == 0) {
                        $attribute_detail->method    = "ecs_add";
                        $attribute_detail->set_ids   = $request_data["set_ids"];
                        $attribute_detail->image_ids = $request_data["image_ids"];
                    }
                    $attribute_detail->imageCode        = $request_data["instance_image"];
                    $attribute_detail->instanceTypeCode = $request_data["instance_set"];
                    $attribute_detail->flow_id          = $request_data["instance_work"];
                    $attribute_detail->attribute_detail = isset($request_data["detail"]) ? $request_data["detail"] : "";
                    if (isset($request_data["haveEip"])) {
                        $attribute_detail->haveEip = 1;
                        //添加eip相关信息
                        $attribute_detail->bandWidth    = $request_data["txtbandwidth"];
                        $attribute_detail->bandMaxWidth = $request_data["txtMaxbandwidth"];
                        if (isset($request_data["updateEip"])) {
                            $attribute_detail->updateEip = 1;
                        } else {
                            $attribute_detail->updateEip = 0;
                        }
                    } else {
                        $attribute_detail->haveEip = 0;
                        //添加eip相关信息
                        $attribute_detail->bandWidth    = 0;
                        $attribute_detail->bandMaxWidth = 0;
                        $attribute_detail->updateEip = 0;
                    }
                    if (isset($request_data["haveMemory"])) {
                        //添加存储相关
                        $attribute_detail->haveMemory = 1;
                        $attribute_detail->size       = $request_data["txtSize"];
                    } else {
                        $attribute_detail->haveMemory = 0;
                        $attribute_detail->size       = 0;
                    }
                    if ($GoodsAttributeDetail->save($attribute_detail)) {
                        $this->redirect([
                            'controller' => 'Goods', 'action' => 'attribute',
                        ]);
                        //echo $attribute_detail->id;
                    } else {
                        //echo "0";
                    }
                } else {
                    echo "500";
                }
            } else {
                // debug($request_data);die();
                $GoodsAttribute       = TableRegistry::get('GoodsAttribute');
                $GoodsAttributeDetail = TableRegistry::get('GoodsAttributeDetail');
                $attribute            = $GoodsAttribute->get($request_data["attribute_id"]);

                $attribute_detail     = $GoodsAttributeDetail->find()->where(['id'=>$request_data["attributedetail_id"]])->first();
                if($attribute_detail == null){
                    $attribute_detail     = $GoodsAttributeDetail->newEntity();
                    $attribute_detail->goods_attribute_id = $attribute->id;
                }

                // debug($addAttribute->attribute_name);die();
                $attribute->attribute_name       = $request_data["attribute_name"];
                $attribute->attribute_regionCode = $request_data["attribute_region"];
                $agent                           = TableRegistry::get('Agent');
                $agent                           = $agent->find("all")->where(array('region_code' => $request_data["attribute_region"]))->first();
                $attribute->attribute_classCode  = $agent->class_code;
                $attribute->attribute_className  = $agent->display_name;
                $GoodsAttribute->save($attribute);
                //
                $attribute_detail->sell_tenant   = $request_data["departments"];
                $attribute_detail->vpcCode       = $request_data["instance_vpc"];
                $attribute_detail->subnetCode    = $request_data["instance_subnet"];
                $attribute_detail->instance_name = $request_data["name"];
                if ((int) $request_data["product_type"] == 1) {
                    //云桌面
                    $attribute_detail->method  = "desktop_add";
                    $attribute_detail->set_ids = $request_data["set_ids"];
                } else if ((int) $request_data["product_type"] == 0) {
                    $attribute_detail->method    = "ecs_add";
                    $attribute_detail->set_ids   = $request_data["set_ids"];
                    $attribute_detail->image_ids = $request_data["image_ids"];
                }
                $attribute_detail->imageCode        = $request_data["instance_image"];
                $attribute_detail->instanceTypeCode = $request_data["instance_set"];
                $attribute_detail->flow_id          = $request_data["instance_work"];

                $attribute_detail->attribute_detail = isset($request_data["detail"]) ? $request_data["detail"] : "";
                if (isset($request_data["haveEip"])) {
                    $attribute_detail->haveEip = 1;
                    //添加eip相关信息
                    $attribute_detail->bandWidth    = $request_data["txtbandwidth"];
                    $attribute_detail->bandMaxWidth = $request_data["txtMaxbandwidth"];
                    if (isset($request_data["updateEip"])) {
                        $attribute_detail->updateEip = 1;
                    } else {
                        $attribute_detail->updateEip = 0;
                    }
                } else {
                    $attribute_detail->haveEip = 0;
                    //添加eip相关信息
                    $attribute_detail->bandWidth    = 0;
                    $attribute_detail->bandMaxWidth = 0;
                    $attribute_detail->updateEip = 0;
                }
                if (isset($request_data["haveMemory"])) {
                    //添加存储相关
                    $attribute_detail->haveMemory = 1;
                    $attribute_detail->size       = $request_data["txtSize"];
                } else {
                    $attribute_detail->haveMemory = 0;
                    $attribute_detail->size       = 0;
                }
                $GoodsAttributeDetail->save($attribute_detail);
                $this->redirect([
                    'controller' => 'Goods', 'action' => 'attribute',
                ]);
            }
        }
    }

    public function delattribute()
    {
        $this->autoRender = false;
        $request_data     = $this->request->data;
        //获取vpc
        $GoodsAttribute       = TableRegistry::get('GoodsAttribute');
        $GoodsAttributeDetail = TableRegistry::get('GoodsAttributeDetail');
//        $Goods=TableRegistry::get('Goods');
        $arr=array();
        $connection = ConnectionManager::get('default');
        debug($request_data);die;
        foreach($request_data['table'] as $key => $value){
            $id=$value['id'];
            if (is_numeric($id)) {
                $res=$connection->execute("select * from cp_goods where attribute_ids LIKE '%$id%'")->fetchAll('assoc');
                if(!empty($res)){
                    $arr['if']=1;
                }else{
                    $arr['if']=0;
                }
            } else {
                $arr['if']=1;
            }
        }
        if($arr['if']==0){
            foreach ($request_data["table"] as $key => $value) {
                //获取GoodsAttributeDetail id
                $detail = $GoodsAttributeDetail->find("all")->where(array('goods_attribute_id' => $value['id']))->first();
                if (!empty($detail)) {
                    $entity = $GoodsAttributeDetail->get($detail->id);
                    $result = $GoodsAttributeDetail->delete($entity);
                }
                $entity1 = $GoodsAttribute->get($value["id"]);
                $result1 = $GoodsAttribute->delete($entity1);
            }

        }

        $arr['code']=0;
        echo json_encode($arr);
//        echo json_encode(array('code' => '0'));
    }

    public function getVpcByRegion()
    {
        $this->autoRender = false;
        $request_data     = $this->request->data;
        //获取vpc
        $instance_basic = TableRegistry::get('InstanceBasic');
        $instance_vpc   = $instance_basic->find('all')->where(array('type' => 'vpc', 'location_code' => $request_data["location_code"]))->toArray();
        echo json_encode($instance_vpc);
    }

public function getVpcByDepartment(){
    $this->autoRender = false;
    $request_data     = $this->request->data;
    //获取vpc
    $instance_basic = TableRegistry::get('InstanceBasic');
    if($request_data["department_id"]!='0') {
        $instance_vpc = $instance_basic->find('all')->where(array('type' => 'vpc', 'location_code' => $request_data["location_code"], 'department_id' => $request_data["department_id"]))->toArray();
    }else{
        $instance_vpc = $instance_basic->find('all')->where(array('type' => 'vpc', 'location_code' => $request_data["location_code"]))->toArray();
    }
    echo json_encode($instance_vpc);

}

    public function getImage()
    {
        //镜像
        $this->autoRender = false;
        $request_data     = $this->request->data;
        $instance_image   = TableRegistry::get('Imagelist');
        $instance_image   = $instance_image->find('all')->toArray();
        echo json_encode($instance_image);
    }

    public function getSubnetByVpc()
    {
        $this->autoRender = false;
        $request_data     = $this->request->data;
        $instance_basic   = TableRegistry::get('InstanceBasic');
        $instance_subnet  = $instance_basic->find('all')->where(array('type' => 'subnet', 'vpc' => $request_data["vpc"]))->toArray();
        echo json_encode($instance_subnet);
    }

    public function getSetByType($type = 0)
    {
        $this->autoRender = false;
        if ($this->request->is('get')) {
            if ((int) $type == 0) {
                //硬件套餐
                $instance_set = TableRegistry::get('SetHardware');
                $instance_set = $instance_set->find('all')->toArray();
                echo json_encode($instance_set);die();
            } elseif ((int) $type == 1) {
                //硬件套餐
                $instance_soft = TableRegistry::get('GoodsVersionSpec');
                $instance_soft = $instance_soft->find('all')->toArray();
                echo json_encode($instance_soft);die();
            }
        } else {
            $request_data = $this->request->data;
            $type         = $request_data["type"];
            if ((int) $type == 0) {
                //硬件套餐
                $instance_set = TableRegistry::get('SetHardware');
                $instance_set = $instance_set->find('all')->toArray();
                echo json_encode($instance_set);die();
            } elseif ((int) $type == 1) {
                //硬件套餐
                $instance_soft = TableRegistry::get('GoodsVersionSpec');
                $instance_soft = $instance_soft->find('all')->toArray();
                echo json_encode($instance_soft);die();
            }
            echo "";
        }
    }

    public function getBillCycle(){
        //镜像
        $this->autoRender = false;
        $char_cycle   = $billcycle = parent::_GetBillCycle(null);
        $array_cycle = array();
        foreach ($char_cycle as $key => $value) {
            if(!empty($value)){
                $array_cycle[]=array('id'=>$key,'name'=>$value);
            }
        }
        echo json_encode($array_cycle);
    }
}
