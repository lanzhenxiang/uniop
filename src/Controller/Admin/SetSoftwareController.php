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

class SetSoftwareController extends AdminController{

    public $paginate = [
    'limit' => 15,
    ];
    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_para_software_set');
        if (! $checkPopedomlist) {
            return $this->redirect('/admin/');
        }
    }
    public function index($name=''){
        $where = array();
        $datas = '';
        if($name){
            $where['OR'] =array('set_name like'=>"%$name%",'set_code like'=>"%$name%");
        }
        $SetSoftware = TableRegistry::get('GoodsVersionSpec');
        $SetHardware = TableRegistry::get('SetHardware');
        $Imagelist = TableRegistry::get('Imagelist');
        $data = $SetSoftware->find('all')->where($where);
        $data=$this->paginate($data);
        $data = $data->toArray();
        $imageInfo = $Imagelist->find('all')->select(['image_code','image_name'])->toArray();
        $hardInfo = $SetHardware->find('all')->select(['set_code','set_name'])->toArray();
        foreach ($data as $key => $value) {
            foreach ($imageInfo as $image) {
                if ($value['image_code']==$image['image_code']) {
                    $value['image'] = $image['image_name'];
                }
            }
            foreach ($hardInfo as $hard) {
                if ($value['instancetype_code']==$hard['set_code']) {
                    $value['hard'] = $hard['set_name'];
                }
            }
            $datas[]=$value;
        }
        $this->set('name',$name);
        $this->set('query',$datas);
    }

    public function addedit($id=0){
        $public = new PublicController();
        $SetSoftware = TableRegistry::get('GoodsVersionSpec');
        $SetHardware = TableRegistry::get('SetHardware');
        $Imagelist = TableRegistry::get('Imagelist');
        if($this->request->is('get')){

            $query['hardware'] = $SetHardware->find('all')->select(['set_name','set_code'])->toArray();
            $query['image'] = $Imagelist->find('all')->select(['image_name','image_code'])->where(['image_type' =>2])->order(['sort_order'])->toArray();
            $this->set('query',$query);
             //编辑时
            if($id){
                $department_data = $SetSoftware->find('all',array('conditions'=>array('set_id'=>$id)))->toArray();
                $this->set('department_data',$department_data[0]);
            }
        }else{
            // var_dump($this->request->data);exit;
            if(empty($this->request->data['set_id'])){
                $code = $this->request->data['set_name'];
                $checkCode = $SetSoftware->find()->where(['set_name' => $code])->first();
                if(!empty($checkCode)){
                    $message = array('code'=>1,'msg'=>'非编规格不能重复');
                    echo json_encode($message);exit();
                }
            }
            $message = array('code'=>1,'msg'=>'操作失败');
            $data = $SetSoftware->newEntity();
            $data = $SetSoftware->patchEntity($data,$this->request->data);
            $result = $SetSoftware->save($data);
            if($result){
                $message = array('code'=>0,'msg'=>'操作成功');
                if(empty($this->request->data['set_id'])){
                    $public->adminlog('SetHardware','添加非编套餐---'.$this->request->data['set_name']);
                }else{
                    $public->adminlog('SetHardware','修改非编套餐---'.$this->request->data['set_name']);
                }
            }
            echo json_encode($message);exit();
        }

    }

    //删除
    public function dele(){
        $public = new PublicController();
        $this->layout = false;
        if($this->request->data['id']){
            $id=$this->request->data['id'];
            $SetSoftware = TableRegistry::get('GoodsVersionSpec');
            $data = $SetSoftware->find()->where(['set_id'=>$id])->first();
            $res = $SetSoftware->deleteAll(array('set_id'=>$id));
            if($res){
                $message = array('code'=>0,'msg'=>'操作成功');
                $public->adminlog('SetHardware','删除非编套餐---'.$data['set_name']);
            }
            echo json_encode($message);exit();

        }
    }
}