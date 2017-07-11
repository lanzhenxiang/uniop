<?php
/**
* 引入功能
*
* @file: ImportController.php
* @date: 2016年2月26日 上午10:40:37
* @author: xingshanghe
* @email: xingshanghe@icloud.com
* @copyright poplus.com
*
*/
namespace App\Controller\Console;


use App\Controller\Console\ConsoleController;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
// use App\Controller\Console\Network\HostsController;


class ImportController extends ConsoleController
{
    /**
    * 反向引入列表
    */
    public function lists($subject = 'hosts')
    {
        //租户信息
        $account_table = TableRegistry::get('Accounts');
        $user = $account_table->find()->select('department_id')->where(array('id' => $this->request->session()->read('Auth.User.id')))->first();
        $deparments = TableRegistry::get('Departments');
        $this->set('_default',$deparments->get($user["department_id"]));
        $table = TableRegistry::get('InstanceBasic');
        $department_id = $this->request->session()->read('Auth.User.department_id') ? $this->request->session()->read('Auth.User.department_id') : 0;
        $vpc = $table->find()->select(["id","name","code"])->where(array(
                "type"=>"vpc",
                "status"=>"运行中",
                "department_id"=>$department_id
                ))->toArray();
        $this->set("_vpc",$vpc);

        //地域信息
        $agent = TableRegistry::get('Agent');
        $agents = $agent->find('all')->toArray();
        $popedomname = $this->request->session()->read('Auth.User.popedomname') ? $this->request->session()->read('Auth.User.popedomname'):0;#用户权限
        $this->set('agent',$agents);
        $table = $deparments->find('all');
        $this->set('_deparments', $table);
        $this->render('lists/'.$subject );
    }

    public function addInstance(){
        $msg = ['code'=>'0001','msg'=>'操作失败'];
        $request = $this->request->data['id'];
        $temporary_table = TableRegistry::get('Temporary');
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        $vpc_extend_table = TableRegistry::get('VpcExtend');


        if(!empty($request) && is_array($request)){
            $temporary_data = $temporary_table->find()->where(['id in' => $request])->toArray();
            foreach ($temporary_data as $key => $data) {
                $basic =$instance_basic_table->newEntity();
                //反向引入公共数据
                $basic->code = $data["code"];  #code
                $basic->status = "运行中"; #状态
                $basic->department_id = $data['department_id']; #部门id
                $basic->location_code = $data['location_code']; #区域code
                $basic->create_time = time(); #创建时间
                $basic->create_by = $this->request->session()->read('Auth.User.id'); #引入人员
                $basic->name = $data["code"]; #name
                $basic->is_delete = 0; #是否删除
                
                //地域
                $display_name = $this->getLocationNameByLocationCode($data["location_code"]);
                if (!empty($display_name)) {
                    $basic->location_name=$display_name["display_name"];
                } else {
                    $basic->location_name = '';
                }

                switch ($data['type']) {
                    case 'hosts':
                        # code...
                        break;
                    case 'VPC':
                        
                        $basic->type = "vpc";
                        // $basic_data = $instance_basic_table->save($basic);#保存主表信息
                        if ($instance_basic_table->save($basic)) {

                            $vpc_extend_data =$vpc_extend_table->newEntity();
                            $vpc_extend_data->basic_id = $basic->id;
                            $json_vpc = json_decode(trim($data->info,chr(239).chr(187).chr(191)), true);
                            if (!empty($json_vpc['Cidr'])) {
                                $vpc_extend_table->cidr = $json_vpc['Cidr'];
                            } else {
                                $vpc_extend_table->cidr = '';
                            }
                            
                            // TODO 添加vpc扩展
                            $vpc_extend_table->save($vpc_extend_data);


                            $temporary_table->deleteAll(['id'=>$data['id']]);
                        }
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
    }

    protected function getLocationNameByLocationCode($code){
        $agent_table = TableRegistry::get('Agent');
        $agent_data = $agent_table->find()->where(['class_code'=>$code])->first();
        return $agent_data;
    }

}