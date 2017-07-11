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

class BusinessTemplateController extends AdminController
{

    public $paginate = [
        'limit' => 15,
    ];

    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_commodity_business_template');
        if (!$checkPopedomlist) {
            return $this->redirect('/admin/');
        }
    }

    public function index($name = '')
    {
        $business_template = TableRegistry::get('BusinessTemplate');
        $where     = array();
        if ($name) {
            //$where['OR'] =array('popedomname like'=>"%$name%",'popedomnote like'=>"%$name%");
        }
        $data = $business_template->find('all');
        $data = $this->paginate($data);
        $this->set('data', $data);
        $this->set('name', $name);
    }

    public function addedit($id = 0)
    {

        $business_template = TableRegistry::get('BusinessTemplate');
        $agent     = TableRegistry::get('Agent');
        if ($this->request->is('get')) {
            $agent_data = $agent->find()->select(['display_name','agent_code', 'region_code'])->where(array('parentid >' => 0,'region_code !=' =>'','is_enabled'=>'1'));
            $this->set('agent', $agent_data);
            if ($id) {
                $data = $business_template->find('all')->where(array('biz_tid' => $id))->first();
                $this->set('data', $data);
            }
        } else {
            $display_name  = $agent->find()->select(['display_name'])->where(array('region_code' => $this->request->data['region_code'],'is_enabled'=>'0'))->first();
            $this->request->data['create_time'] = time();
            $this->request->data['region_name'] = $display_name['display_name'];
            $BusinessTemplate                           = $business_template->newEntity();
            $BusinessTemplate                           = $business_template->patchEntity($BusinessTemplate, $this->request->data);

            if ($business_template->save($BusinessTemplate)) {
                echo json_encode(array('code' => 0, 'msg' => '操作成功'));exit;
            } else {
                echo json_encode(array('code' => 1, 'msg' => '操作失败'));exit;
            }
        }
    }

    //配置单显示
    public function configure($biz_tid = 0)
    {
        $business_template        = TableRegistry::get('BusinessTemplate');
        $business_template_detail = TableRegistry::get('BusinessTemplateDetail');

        if (!$biz_tid || !is_numeric($biz_tid)) {
            $this->redirect('/admin/business-template');
        } else {
            $business_template_detail = TableRegistry::get('BusinessTemplateDetail');
            $vpcdata          = $business_template->find('all')->where(array('biz_tid' => $biz_tid))->first();
            $this->set('vpcdata', $vpcdata);
            $connection = ConnectionManager::get('default');
            $sql        = ' SELECT ';
            $sql .= ' a.*,b.cpu_number,b.memory_gb,b.gpu_gb,c.image_name,c.plat_form FROM cp_business_template_detail a LEFT JOIN cp_set_hardware b ON a.instance_code=b.set_code LEFT JOIN cp_imagelist c ON a.image_code=c.image_code';
            $sql .= ' where a.biz_tid =\'' . $biz_tid . '\' ORDER BY a.id';
            $query = $connection->execute($sql)->fetchAll('assoc');
            $this->set('vpcdetaildata', $query);
            $this->set('biz_tid', $biz_tid);

            // $business_template        = TableRegistry::get('BusinessTemplate');
            // $business_template_detail = TableRegistry::get('BusinessTemplateDetail');
            // $vpcdata          = $business_template->find('all')->where(array('biz_tid' => $biz_tid))->first();
            // $this->set('vpcdata', $vpcdata);
            // $vpcdetaildata = $business_template_detail->find('all')->contain(['BusinessTemplateDetails'])->where(array('or' => array(array('BusinessTemplateDetail.biz_tid' => $biz_tid), array('BusinessTemplateDetail.biz_tid' => 0))))->order(['BusinessTemplateDetail.sort_order', 'BusinessTemplateDetail.id']);
            // debug($this->paginate($vpcdetaildata));die;
            // $vpcdetaildata = $this->paginate($vpcdetaildata);
            // $this->set('vpcdetaildata', $vpcdetaildata);
            // $this->set('biz_tid', $biz_tid);
        }
    }

    public function findVpcEcsConfigure($biz_tid = 0)
    {
        if (is_numeric($biz_tid)) {
            $connection = ConnectionManager::get('default');
            $sql        = ' SELECT ';
            $sql .= ' a.*,b.cpu_number,b.memory_gb,b.gpu_gb,c.image_name,c.plat_form,d.tagname as \'subnetName\' FROM cp_business_template_detail a LEFT JOIN cp_set_hardware b ON a.instance_code=b.set_code LEFT JOIN cp_imagelist c ON a.image_code=c.image_code LEFT JOIN cp_business_template_detail d ON a.subnet_id=d.id ';
            $sql .= ' where a.biz_tid =\'' . $biz_tid . '\' ORDER BY a.id';
            $query = $connection->execute($sql)->fetchAll('assoc');
            // $this->set('vpcdetaildata', $query);
            return $query;
        }
        return "";
    }

    //添加子网
    public function addsubnet($biz_tid = 0, $id = 0)
    {
        if (!$biz_tid) {
            $this->redirect('/admin/business-template');
        } else {
            if ($id) {
                $business_template_detail = TableRegistry::get('BusinessTemplateDetail');
                $data             = $business_template_detail->find()->where(array('id' => $id))->first();
                $this->set('data', $data);
            }
            $this->set('biz_tid', $biz_tid);
        }
    }

    public function addsubnetpost()
    {
        $business_template_detail              = TableRegistry::get('BusinessTemplateDetail');
        $this->request->data['type']   = 'subnet';
        $this->request->data['number'] = '1';
        $BusinessTemplatedetails               = $business_template_detail->newEntity();
        $BusinessTemplatedetails               = $business_template_detail->patchEntity($BusinessTemplatedetails, $this->request->data);
        if ($business_template_detail->save($BusinessTemplatedetails)) {
            echo json_encode(array('code' => 0, 'msg' => '操作成功', 'biz_tid' => $this->request->data['biz_tid']));exit;
        } else {
            echo json_encode(array('code' => 1, 'msg' => '操作失败'));exit;
        }
    }

    //子网cidr排重
    public function cidr()
    {
        $cidr             = $this->request->data['cidr'];
        $id = $this->request->data['biz_tid'];
        $business_template_detail = TableRegistry::get('BusinessTemplateDetail');
        $count            = $business_template_detail->find()->select(['id'])->where(array('subnet_cidr' => $cidr,'biz_tid'=>$id))->count();
        if ($count > 0) {
            echo json_encode(array('code' => 1, 'msg' => ''));exit;
        } else {
            echo json_encode(array('code' => 0, 'msg' => ''));exit;
        }
    }

    //添加ecs,云桌面，火墙
    public function addecs($biz_tid = 0, $id = 0)
    {   
        $business_template = TableRegistry::get('BusinessTemplate');
        $business_template_detail = TableRegistry::get('BusinessTemplateDetail');
        if (!$biz_tid) {
            $this->redirect('/admin/business-template');
        } else {
            if ($id) {
                $data = $business_template_detail->find()->where(array('id' => $id))->first();
                $this->set('data', $data);
            }
            $template = $business_template->find()->where(['biz_tid'=>$biz_tid])->first();
            //显示镜像
            $imagelist      = TableRegistry::get('Imagelist');
            $imagelist_data = $imagelist->find()->join([
                    'agent_image'=>[
                        'table' =>'cp_agent_imagelist',
                        'type'  =>'LEFT',
                        'conditions' =>'agent_image.image_id = Imagelist.id'
                    ],
                    'agent'=>[
                        'table' =>"cp_agent",
                        'type'  =>"LEFT",
                        'conditions' =>"agent.id = agent_image.agent_id" 
                    ]
                ])->where(['agent.region_code'=>$template['region_code']])->select(['image_name', 'image_code']);
            $this->set('imagelist_data', $imagelist_data);

            //显示硬件套餐
            $set_hardware = TableRegistry::get('SetHardware');
            $set_data     = $set_hardware->find()->select(['set_name', 'set_code'])->join([
                    'agent_set'=>[
                        'table' =>'cp_agent_set',
                        'type'  =>'LEFT',
                        'conditions' =>'agent_set.set_id = SetHardware.set_id'
                    ],
                    'agent'=>[
                        'table' =>"cp_agent",
                        'type'  =>"LEFT",
                        'conditions' =>"agent.id = agent_set.agent_id" 
                    ]
                ])->where(['agent.region_code'=>$template['region_code']]);

            $this->set('set_data', $set_data);
            $this->set('biz_tid', $biz_tid);

        }
    }

    public function addecspost()
    {
        $business_template_detail = TableRegistry::get('BusinessTemplateDetail');
        $imagelist        = TableRegistry::get('Imagelist');
        $set_hardware     = TableRegistry::get('SetHardware');

        if ($this->request->data['type'] != 'firewall') {
            $set_data                             = $set_hardware->find()->select(['set_name'])->where(array('set_code' => $this->request->data['instance_code']))->first();
            $imagelist_data                       = $imagelist->find()->select(['image_name'])->where(array('image_code' => $this->request->data['image_code']))->first();
            $this->request->data['instance_name'] = $set_data['set_name'];
            $this->request->data['image_name']    = $imagelist_data['image_name'];
        } else {
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
        $BusinessTemplatedetails = $business_template_detail->newEntity();
        $BusinessTemplatedetails = $business_template_detail->patchEntity($BusinessTemplatedetails, $this->request->data);
        if (!isset($BusinessTemplatedetails['sort_order'])) {
            $BusinessTemplatedetails['sort_order'] = 0;
        }
        if ($business_template_detail->save($BusinessTemplatedetails)) {
            echo json_encode(array('code' => 0, 'msg' => '操作成功', 'biz_tid' => $this->request->data['biz_tid']));exit;
        } else {
            echo json_encode(array('code' => 1, 'msg' => '操作失败'));exit;
        }
    }

    //删除vpc
    public function deleteBusinessTemplate()
    {
        $public = new PublicController();
        if ($this->request->is('post')) {
            $business_template = TableRegistry::get('BusinessTemplate');
            $instance_basic    = TableRegistry::get('InstanceBasic');
            $biz_tid    = $this->request->data['biz_tid'];
            $message   = array('code' => 1, 'msg' => '操作失败');

            $ecsEntity = $instance_basic->find()->where(['biz_tid'=>$biz_tid])->first();
            if($ecsEntity != null){
                echo json_encode(['code'=>1,'msg'=>'模板正在使用不能被删除']);exit;
            }
            $data      = $business_template->find()->where(['biz_tid' => $biz_tid])->first();
            if ($business_template->deleteAll(array('biz_tid' => $biz_tid))) {
                $business_template_detail = TableRegistry::get('BusinessTemplateDetail');
                $count            = $business_template_detail->find()->where(array('biz_tid' => $biz_tid))->count();
                $result           = $business_template_detail->deleteAll(array('biz_tid' => $biz_tid));
                if ($count == $result) {
                    $message = array('code' => 0, 'msg' => '操作成功');
                    $public->adminlog('BusinessTemplate', '删除成功---' . $data['biz_temp_name']);
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
            $business_template_detail = TableRegistry::get('BusinessTemplateDetail');
            $id               = $this->request->data['id'];
            $message          = array('code' => 1, 'msg' => '操作失败');
            $data             = $business_template_detail->find()->where(['id' => $id])->first();
            if ($business_template_detail->deleteAll(array('id' => $id))) {
                $message = array('code' => 0, 'msg' => '操作成功');
                $public->adminlog('BusinessTemplate', '删除vpc配置---' . $data['tagname']);
            }
            echo json_encode($message);exit;
            // $this->lauout = 'ajax';
        }
    }

    public function copyBusinessTemplate()
    {
        $biz_tid           = $this->request->data['biz_tid'];
        $business_template        = TableRegistry::get('BusinessTemplate');
        $business_template_detail = TableRegistry::get('BusinessTemplateDetail');
        $public           = new PublicController();
        $base_data        = $business_template->find()->where(array('biz_tid' => $biz_tid))->first()->toArray();
        unset($base_data['biz_tid']);
        $base_data['biz_temp_name'] = $base_data['biz_temp_name'] . '(复制)';
        $data                  = $business_template->newEntity();
        $data                  = $business_template->patchEntity($data, $base_data);
        $data->status          = "未发布";
        $result                = $business_template->save($data);
        if ($result) {

            $detail_data = $business_template_detail->find()->where(array('biz_tid' => $biz_tid))->toArray();
            if (!empty($detail_data)) {
                foreach ($detail_data as $detail) {
                    $array = array();
                    unset($detail['id']);
                    $array['biz_tid']        = $result['biz_tid'];
                    $array['type']          = $detail['type'];
                    $array['tagname']       = $detail['tagname'];
                    $array['instance_code'] = $detail['instance_code'];
                    $array['instance_name'] = $detail['instance_name'];
                    $array['image_code']    = $detail['image_code'];
                    $array['image_name']    = $detail['image_name'];
                    $array['number']        = $detail['number'];
                    $array['is_fusion']     = $detail['is_fusion'];

                    $data_detail = $business_template_detail->newEntity();
                    $data_detail = $business_template_detail->patchEntity($data_detail, $array);
                    $business_template_detail->save($data_detail);
                }
            }

            $public->adminlog('BusinessTemplate', '复制业务模板' . $base_data['biz_temp_name'] . '------>成功');
            echo json_encode(array('code' => 0, 'msg' => '复制业务模板成功'));exit;

        } else {
            $public->adminlog('BusinessTemplate', '复制业务模板' . $base_data['biz_temp_name'] . '------>失败');
            echo json_encode(array('code' => 1, 'msg' => '复制业务模板失败'));exit;
        }
    }

    /**
     * [statusTaggle 修改业务模板状态 发布 | 未发布]
     * @return [type] [description]
     */
    public function statusTaggle(){
        $status = ['unpush'=>'未发布','push'=>'已发布'];
        $biz_tid           = $this->request->data['biz_tid'];
        $action            = $this->request->data['action'];
        if(!array_key_exists($action, $status)){
            echo json_encode(array('code' => 1, 'msg' => '参数错误'));exit;
        }
        
        $public           = new PublicController();
        $business_template        = TableRegistry::get('BusinessTemplate');
        $business_template_entity = $business_template->find()->contain(['BusinessTemplateDetail'])->where(array('BusinessTemplate.biz_tid' => $biz_tid))->first();
        if($business_template_entity->business_template_detail == null && $action == 'push'){
            echo json_encode(array('code' => 1, 'msg' => '当前模板没有资源清单，不允许发布'));exit;
        }

        if($business_template_entity->status == $status[$action]){
            echo json_encode(array('code' => 1, 'msg' => '当前模板状态已是'.$business_template_entity->status.'。'));exit;
        }else{
            $business_template_entity->status = $status[$action];
            $result = $business_template->save($business_template_entity);
            if($result){
                $msg = '修改' . $business_template_entity['biz_temp_name'].'状态为'.$status[$action]. '------>成功';
                $public->adminlog('BusinessTemplate', $msg);
                echo json_encode(array('code' => 0, 'msg' => $msg));exit;
            }else{
                $msg = '修改' . $business_template_entity['biz_temp_name'].'状态为'.$status[$action]. '------>失败';
                $public->adminlog('BusinessTemplate', $msg);
                echo json_encode(array('code' => 0, 'msg' => $msg));exit;
            }
        }
    }

    public function firewall()
    {
        $biz_tid           = $this->request->data['biz_tid'];
        $business_template_detail = TableRegistry::get('BusinessTemplateDetail');
        $count            = $business_template_detail->find()->select(['id'])->where(array('biz_tid' => $biz_tid, 'type' => 'firewall'))->count();
        if ($count > 0) {
            echo json_encode(array('code' => 1, 'msg' => ''));exit;
        } else {
            echo json_encode(array('code' => 0, 'msg' => ''));exit;
        }
    }

}
