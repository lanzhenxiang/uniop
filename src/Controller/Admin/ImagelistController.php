<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/8
 * Time: 15:54
 */

namespace App\Controller\Admin;
use App\Controller\AdminController;
use App\Controller\SobeyController;
use Cake\ORM\TableRegistry;

class ImagelistController extends AdminController{

    public $paginate = [
    'limit' => 15,
    ];
    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_para_images');
        if (! $checkPopedomlist) {
            return $this->redirect('/admin/');
        }
    }
    public function index($name=''){
        $imagelist = TableRegistry::get('Imagelist');
        $where=array();
        if($name){
            $where['OR'] =array('image_name like'=>"%$name%",'image_code like'=>"%$name%");
        }
        $data = $imagelist->find('all')->contain('Accounts')->where($where);
        $data = $this->paginate($data);
        $this->set('name',$name);
        $this->set('data',$data);
    }
    //是否关联厂商地域
    public function getAgent(){
        $agent_imagelist=TableRegistry::get('AgentImagelist');
        $ids=$this->request->query['ids'];
        $ids=explode(',',rtrim($ids,','));
        $count=0;
        foreach($ids as $key =>$value){
            $exist=$agent_imagelist->find()->select(['id'])->where(array('image_id'=>$value))->count();
            if($exist>0){
                $count+=1;
            }
        }
        if($count>0){
            echo json_encode(array('code'=>1,'msg'=>'选中系统镜像中有'.$count.'个已关联厂商地域'));exit;
        }else{
            echo json_encode(array('code'=>0,'msg'=>'未关联厂商地域'));exit;
        }
    }
    public function lists( $name =''){
        $imagelist = TableRegistry::get('Imagelist');
        $request_data = $this->request->query;
        $this->paginate['limit'] = $request_data['limit'];
        $this->paginate['page'] = $request_data['offset'] / $request_data['limit'] + 1;
        $where=array();
        if($name){
            $where['OR'] =array('image_name like'=>"%$name%",'image_code like'=>"%$name%");
        }
        $data = $this->paginate($imagelist->find('all')->contain('Accounts')->where($where));
        $json = array(
            'total'=>$imagelist->find('all')->contain('Accounts')->where($where)->count(),
            'rows'=>$data,
        );
        echo json_encode($json); exit;
    }

    public function addedit($id=0){
        $public = new PublicController();
        $imagelist = TableRegistry::get('Imagelist');

        $departments = TableRegistry::get('Departments');
        $charge_template = TableRegistry::get('ChargeTemplate');

        if($this->request->is('get')){

            $department = $departments->find('all')->select(['id','name'])->toArray();
            $this->set('dept',$department);
            $template = $charge_template->find('all')->select(['id','template_name'])->toArray();
            $this->set('template',$template);
            //编辑时
            if($id){
                $department_data = $imagelist->find('all',array('conditions'=>array('id'=>$id)))->toArray();
                $this->set('department_data',$department_data[0]);
            }

        }else{
            // var_dump($this->request->data);exit;
            if(empty($this->request->data['id'])){
                $code = $this->request->data['image_code'];
                $checkCode = $imagelist->find()->where(['image_code' => $code])->first();
                if(!empty($checkCode)){
                     $message = array('code'=>1,'msg'=>'该镜像Code已被使用');
                     echo json_encode($message);exit();
                }
            }
            $message = array('code'=>1,'msg'=>'操作失败');
            $image = $imagelist->newEntity();
            $image = $imagelist->patchEntity($image,$this->request->data);
            if(empty($this->request->data['id'])){
                $image->creat_by = $this->request->session()->read('Auth.User.id');
            }
            $image->create_time = time();
            if($image->image_type == 2){//云桌面价格置为0
                $image->price_day = 0;
                $image->price_month = 0;
                $image->price_year = 0;
            }

            $result = $imagelist->save($image);
            if($result){
                $message = array('code'=>0,'msg'=>'操作成功');
                if(empty($this->request->data['id'])){
                    $public->adminlog('系统镜像','添加镜像---'.$this->request->data['image_name']);
                }else{
                    $public->adminlog('系统镜像','修改镜像---'.$this->request->data['image_name']);
                }
            }
            echo json_encode($message);exit();
        }

    }

    /**
     * 修改计算能力的类型时候，判断是否允许修改
     * @return boolean [description]
     */
    public function isAllowEdit(){
        if(isset($this->request->data['id'])){
            $id   = $this->request->data['id'];
            $image_type = $this->request->data['image_type'];
            $image_code = $this->request->data['image_code'];
            $agent_imagelist        = TableRegistry::get('AgentImagelist');
            $imagelist              = TableRegistry::get('Imagelist');
            $spec_table = TableRegistry::get('GoodsVersionSpec');

            $image = $imagelist->find()->where(['id'=> $id,'image_type'=> $image_type])->first();
            $spec = $spec_table->find()->where(['image_code'=>$image_code])->first();
            $agent_image = $agent_imagelist->find()->where(['image_id'=>$id])->contain('Agent')->first();
            if($image === null && ($agent_image || $spec)){
                $result = ['valid'=>false];
            }else{
                $result = ['valid'=>true];
            }
        }else{
            $result = ['valid'=>true];
        }
        echo json_encode($result);exit;
    }

    /**
     * 系统镜像批量删除
     */
    public function deleAll(){
        $public = new PublicController();
        $this->layout = false;
        if($this->request->data['ids']){
            $ids=$this->request->data['ids'];
            $id_array = explode(',',rtrim($ids,','));
            $msg = "";
            foreach ($id_array as $key => $id) {
                $imagelist = TableRegistry::get('Imagelist');
                $agent_imagelist = TableRegistry::get('AgentImagelist');
                $SetSoftware = TableRegistry::get('GoodsVersionSpec');
                $imagelistInfo = $imagelist->find()->select(['image_code','image_name'])->where(['id' => $id])->first();
                $setsoftwareinfo = $SetSoftware->find()->where(['image_code' => $imagelistInfo['image_code']])->first();
                if(!empty($setsoftwareinfo)){
                    $message = array('code'=>1,'msg'=>'有非编套餐使用了镜像'.$imagelistInfo['image_name'].',该镜像不能删除');
                    echo json_encode($message);exit();
                }
                $msg .= $imagelistInfo['image_name'].',';
            }
            $res = $agent_imagelist->deleteAll(array('image_id in'=>$id_array));
            $res = $imagelist->deleteAll(array('id in'=>$id_array));
            if($res){
                $message = array('code'=>0,'msg'=>'操作成功');
                $public->adminlog('系统镜像','删除镜像---'.$msg);
            }
            echo json_encode($message);exit();
        }
    }

    //删除租户
    public function dele(){
        $public = new PublicController();
        $this->layout = false;
        if($this->request->data['id']){
            $id=$this->request->data['id'];
            $imagelist = TableRegistry::get('Imagelist');
            $agent_imagelist = TableRegistry::get('AgentImagelist');
            $SetSoftware = TableRegistry::get('GoodsVersionSpec');
            $imagelistInfo = $imagelist->find()->select(['image_code'])->where(['id' => $id])->first()->toArray();
            $setsoftwareinfo = $SetSoftware->find()->where(['image_code' => $imagelistInfo['image_code']])->first();
            if(!empty($setsoftwareinfo)){
                $message = array('code'=>1,'msg'=>'有非编套餐使用了该镜像');
                echo json_encode($message);exit();
            }
            $data = $imagelist->find()->where(['id'=>$id])->first();
            $res = $agent_imagelist->deleteAll(array('image_id'=>$id));
            $res = $imagelist->deleteAll(array('id'=>$id));
            if($res){
                $message = array('code'=>0,'msg'=>'操作成功');
                $public->adminlog('系统镜像','删除镜像---'.$data['image_name']);
            }
            echo json_encode($message);exit();
        }
    }
}