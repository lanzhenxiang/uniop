<?php
/**
 * Created by PhpStorm.
 * User: kelly
 * Date: 2017/2/16
 * Time: 10:27
 */
namespace App\Controller\Admin;

use App\Controller\AdminController;
use App\Controller\AccountsController;
use App\Controller\OrdersController;
use App\Controller\SobeyController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Controller\Controller;
use Requests as Requests;
use Cake\Datasource\ConnectionManager;

class BudgetTemplateController extends AdminController
{
    public $paginate = [
        'limit' => 15,
    ];
    public function initialize()
    {
        parent::initialize();
        $checkPopedomlist = parent::checkPopedomlist('bgm_tenant_budget_template');
        if (!$checkPopedomlist) {
            return $this->redirect('/admin');
        }

    }

    public $_pageList = array(
        'total' => 0,
        'rows' => array()
    );

    public function index()
    {
        $budget_template = TableRegistry::get('BudgetTemplate');
        $data = array();
        if (isset($this->request->query['type']) && !empty($this->request->query['type'])) {
            $type = $this->request->query['type'];
            $data[0] = $budget_template->find()->select(['title', 'para_value','para_code'])->where(array('depart_type' => $type))->toArray();
            $data[0]['title_name'] = '';
            if ($type == 'platform') {
                $data[0]['title_name'] = '平台租户配额模板';
                $data[0]['depart_type'] = 'platform';
            } else if ($type == 'normal_inner') {
                $data[0]['title_name'] = '内部租户配额模板';
                $data[0]['depart_type'] = 'normal_inner';
            } else if ($type == 'normal_outer') {
                $data[0]['title_name'] = '外部租户配额模板';
                $data[0]['depart_type'] = 'normal_outer';
            }
        } else {
            $type = '';
            $data[0] = $budget_template->find()->select(['title', 'para_value','para_code'])->where(array('depart_type' => 'normal_inner'))->toArray();
            $data[0]['title_name'] = '内部租户配额模板';
            $data[0]['depart_type'] = 'normal_inner';
            $data[1] = $budget_template->find()->select(['title', 'para_value','para_code'])->where(array('depart_type' => 'normal_outer'))->toArray();
            $data[1]['title_name'] = '外部租户配额模板';
            $data[1]['depart_type'] = 'normal_outer';
            $data[2] = $budget_template->find()->select(['title', 'para_value','para_code'])->where(array('depart_type' => 'platform'))->toArray();
            $data[2]['title_name'] = '平台租户配额模板';
            $data[2]['depart_type'] = 'platform';
        }

        foreach($data as $key => $value){
            unset($value['title_name']);
            unset($value['depart_type']);
            foreach($value as $keys => $values){
                if($values['para_code']=='disks_bugedt'){
                    $data[$key][$keys]['title']='块存储(个,单个最大容量'.$data[$key][$keys+1]['para_value'].'GB)';
                    unset($data[$key][$keys+1]);
                }else if($values['para_code']=='fics_num_bugedt'){
                    $data[$key][$keys]['title']='FICS存储卷(个,总容量'.$data[$key][$keys+1]['para_value'].'GB)';
                    unset($data[$key][$keys+1]);
                }else if($values['para_code']=='oceanstor9k_num_bugedt'){
                    $data[$key][$keys]['title']='H9000(个,总容量'.$data[$key][$keys+1]['para_value'].'GB)';
                    unset($data[$key][$keys+1]);
                }else if($values['para_code']=='subnet_bugedt'){
                    unset($data[$key][$keys]);
                }

            }
        }
//        var_dump($data);exit;
        $this->set('type', $type);
        $this->set('data', $data);
    }

    public function lists()
    {

    }

    public function adjust()
    {
        if (isset($this->request->query['depart_type']) && !empty($this->request->query['depart_type'])) {
            $type = $this->request->query['depart_type'];
            $this->set('type', $type);
        } else {
            $this->redirect('/admin/BudgetTemplate');
        }
        $budget_template = TableRegistry::get('BudgetTemplate');
        $data = $budget_template->find()->select(['title', 'para_value', 'para_code'])->where(array('depart_type' => $type))->toArray();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $data[$key]['title_f'] = strstr($value['title'], '(', true);
                $data[$key]['title_b'] = strstr($value['title'], '(');
                if($value['para_code']=='subnet_bugedt'){
                    unset ($data[$key]);
                }
            }
            $this->set('data', $data);
        } else {
            $this->redirect('/admin/BudgetTemplate');
        }
    }

    public function postadjust()
    {
        $public = new PublicController();
        $connection = ConnectionManager::get('default');
        $message = array();
        $count=0;
        $edit=array();
        if (isset($this->request->query['type'])&&!empty($this->request->query['type'])) {
            if (isset($this->request->data) && !empty($this->request->data)) {
                $type=$this->request->query['type'];
                $request = $this->request->data;
                $budget_template = TableRegistry::get('BudgetTemplate');
                $origin=$budget_template->find()->select(['para_code','para_value'])->where(array('depart_type'=>$type))->toArray();
                foreach($origin as $key => $value){
                    if($value['para_code']!='subnet_bugedt'){
                        if($request[$value['para_code']]!=$value['para_value']){
                            $edit[]=array('para_code'=>$value['para_code'],'para_value'=>$request[$value['para_code']]);
                        }
                    }

                }
                if(empty($edit)){
                    $message=array('code'=>4,'msg'=>'未进行修改');
                    echo json_encode($message);
                    exit;
                }
               foreach($edit as $key => $value){

                   $para_code=$value['para_code'];
                   $para_value=$value['para_value'];
                   if($value['para_code']=='router_bugedt'){
                       $connection->execute("update cp_budget_template set para_value=$para_value*15 where para_code= 'subnet_bugedt' and depart_type='$type'");
                   }
                   $res=$connection->execute("update cp_budget_template set para_value='$para_value' where para_code= '$para_code' and depart_type='$type'");

                   if($res){
                       $count+=1;
                   }
               }
                if($count>0){
                    $public->adminlog('BudgetTemplate', '调整配额成功');
                    $message=array('code'=>0,'msg'=>'调整配额成功');
                }else{
                    $public->adminlog('BudgetTemplate', '调整配额失败');
                    $message=array('code'=>1,'msg'=>'调整配额失败');
                }
            } else {
                $message = array('code' => 2, 'msg' => '未传入数据');
            }
        }else{
            $message = array('code' => 3, 'msg' => '未指定租户类型');
        }

        echo json_encode($message);
        exit;

    }


}