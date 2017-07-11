<?php
namespace App\Controller\Admin;

use App\Controller\AccountsController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use App\Controller\AdminController;
use Cake\Datasource\ConnectionManager;

class SpecController extends AdminController
{
    private $_db;
    public $_pageList = array(
        'total' => 0,
        'rows'  => array(),
    );

    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow('image');
        $this->loadComponent('Paginator');
        $this->loadComponent('RequestHandler');
        $this->_db =ConnectionManager::get('default');
    }

    public function index(){

    }

    public function cloneSpec(){
        $this->autoRender = false;
        $id = intval($_GET['id']);
        $specM  = TableRegistry::get('goods_version_spec');
        $priceM = TableRegistry::get('goods_version_price');
        $info = $specM->find()->where(array('id'=>$id))->first()->toArray();
        $new = $specM->newEntity();
        unset($info['id']);
        $info['name'] = $info['name'].'-克隆';
        $new = $specM->patchEntity($new,$info);
        $new = $specM->save($new);
        //写入定价数据
        $priceList = $priceM->find()->where(array('sid'=>$id))->select();
        foreach ($priceList as $key => $value) {
            $value = $value->toArray();
            unset($value['id']);
            $value['sid'] = $new->id;
            $newPrice = $priceM->newEntity();
            $newPrice = $priceM->patchEntity($newPrice,$value);
            $newPrice = $priceM->save($newPrice);
        }
        $this->redirect(array('controller' => 'Spec', 'action' => 'index'));
    }

    public function desktopset($agentId,$areaId){
        $agent = TableRegistry::get('Agent');

        $agent_entity=$agent->get($agentId);
        $area_entity=$agent->get($areaId);

        $this->set('_agent',$agent_entity);
        $this->set('_area',$area_entity);
    }
    public function delete(){
        $this->autoRender = false;
        $id_array = explode(',', $_GET['ids']);
        $specM  = TableRegistry::get('goods_version_spec');
        $specM->deleteAll(['id in' => $id_array]);
        // $connection = ConnectionManager::get('default');
        // $sql = "delete from cp_goods_version_spec where id in(".$_GET['ids'].")";
        // $connection->execute($sql);
    }
    //是否关联厂商地域
    public function getAgent(){
        $desktop_set = TableRegistry::get('DesktopSet');
        $ids=$this->request->query['ids'];
        $ids=explode(',',rtrim($ids,','));
        $count=0;
        foreach($ids as $key =>$value){
            $exist=$desktop_set->find()->select(['id'])->where(array('set_id'=>$value))->count();
            if($exist>0){
                $count+=1;
            }
        }
        if($count>0){
            echo json_encode(array('code'=>1,'msg'=>'选中云桌面规格中有'.$count.'个已关联厂商地域'));exit;
        }else{
            echo json_encode(array('code'=>0,'msg'=>'未关联厂商地域'));exit;
        }
    }
    public function lists(){
    	$this->autoRender = false;
        $specM = TableRegistry::get('goods_version_spec');
        $where= array();
        $whereStr = '';
        if(isset($_GET['name']) && $_GET['name']!="all" && $_GET['name']!="" ){
            $where['name LIKE']="%".$_GET['name']."%";
            $whereStr = ' where name LIKE "%'.$_GET['name'].'%" or brand LIKE "%'.$_GET['name'].'%"';
        }


        $pageNumber = (intval(@$_GET['pageNumber']) >0)?intval(@$_GET['pageNumber']):1;
        $pageSize = (intval(@$_GET['pageSize']) >0)?intval(@$_GET['pageSize']):5;
        $list = $specM->find()->where($where)->page($pageNumber,$pageSize)->toArray();
        $sql = 'select (SELECT group_concat(price,`interval` separator "  ") FROM  cp_goods_version_price where sid=a.id) as price,a.name,a.brand,a.image_code,a.instancetype_code,a.id,b.image_name,c.set_name from cp_goods_version_spec as a left join cp_imagelist as b on a.image_code=b.image_code left join cp_set_hardware as c on c.set_code=a.instancetype_code'.$whereStr.' limit '.($pageNumber-1)*$pageSize.','.$pageSize;
        $lists = $this->_db->execute($sql)->fetchAll('assoc');

        foreach ($list as $key => $value) {
            $list[$key]['_id'] = $value['id'];
        }
        $json = array(
            'total'=>$specM->find()->where($where)->count(),
            'rows'=>$lists,
            );

        echo json_encode($json);
    }
    /**
     * 是否允许修改配置
     * @return boolean [description]
     */
    public function isAllowEdit(){
        $this->autoRender = false;
        if(isset($this->request->data['id'])){
            $id = $this->request->data['id'];
            $image_code = $this->request->data['image_code'];
            $instancetype_code = $this->request->data['instancetype_code'];
            $specM = TableRegistry::get('goods_version_spec');
            $desktop_set = TableRegistry::get('DesktopSet');

            $spec = $specM->find()->where(['id'=>$id,'image_code'=>$image_code,'instancetype_code'=>$instancetype_code])->first();
            $agent = $desktop_set->find()->where(['set_id'=>$id])->contain('Agent')->first();

            if($spec === null && $agent){
                $reuslt = ['valid'=>false];
            }else{
                $reuslt = ['valid'=>true];
            }

        }else{
            $reuslt = ['valid'=>true];
        }
        echo json_encode($reuslt);
    }


    public function add(){
        $priceM = TableRegistry::get('goods_version_price');
        $specM  = TableRegistry::get('goods_version_spec');
        if(isset($_GET['id'])){
            $vinfo = $specM->find()->where(array('id'=>$_GET['id']))->first();
            if(isset($vinfo->id)){
                $priceList = $priceM->find()->where(array('sid'=>$_GET['id']))->select();
                $vinfoD = array();
                foreach ($priceList as $key => $value) {
                    if($value->charge_mode == 'duration'){
                        $vinfoD['price'] = $value->price;
                        $vinfoD['unit'] = $value->interval;
                    }else{
                        $vinfoD[$value->interval] = $value->price;
                    }
                }
                $this->set('vinfo',$vinfo);
                $this->set('vinfoD',$vinfoD);
            }
        }

    	if($_POST){
            if(!isset($_POST['y']) || $_POST['y'] =='' || !isset($_POST['m']) || $_POST['m'] =='' ||!isset($_POST['d']) || $_POST['d'] ==''){
                exit('价格不能为空');
            }
            if(!isset($vinfo->id)){
                $spec = $specM->newEntity();
            }else{
                $spec = $vinfo;
            }
            if(!isset($_GET['edit']) || $_GET['edit'] != 'false'){
                $spec = $specM->patchEntity($spec,array(
                    'name'=>$_POST['name'],
                    'brand'=>$_POST['brand'],
                    'image_code'=>$_POST['image_code'],
                    'instancetype_code'=>$_POST['instancetype_code'],
                    'description'=>$_POST['description']
                ));


                $specM->save($spec);
            }
    		
            /** 存价格 **/
            $priceM->deleteAll(array('sid'=>$spec->id));
            $priceArr = array(
                    'Y'=>$_POST['y'],
                    'M'=>$_POST['m'],
                    'D'=>$_POST['d']
                );
            foreach ($priceArr as $key => $value) {
                $price = $priceM->newEntity();
                $price = $priceM->patchEntity($price,array(
                        'sid'=>$spec->id,
                        'price'=>$value,
                        'unit'=>1,
                        'interval'=>$key,
                        'charge_mode'=>'cycle'
                    ));
                $priceM->save($price);
            }

            $price = $priceM->newEntity();
            $price = $priceM->patchEntity($price,array(
                        'sid'=>$spec->id,
                        'price'=>$_POST['price'],
                        'unit'=>1,
                        'interval'=>$_POST['unit'],
                        'charge_mode'=>'duration'
                    ));
            $priceM->save($price);
            $this->redirect(array('controller' => 'Spec', 'action' => 'index'));
    	}
    	$images= TableRegistry::get('imagelist')->find()->where(array('image_type'=>2))->toArray();
    	$hardwares= TableRegistry::get('set_hardware')->find()->where(array('set_type like'=>'%云桌面%'))->toArray();
    	$this->set('images',$images);
    	$this->set('hardwares',$hardwares);
        $this->set('edit',(isset($_GET['edit']))?$_GET['edit']:"true");
    }
}

?>