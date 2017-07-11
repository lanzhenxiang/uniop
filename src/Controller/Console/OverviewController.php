<?php
/**
* 文件用途描述
*
* @file: OverviewController.php
* @date: 2015年12月7日 下午5:10:36
* @author: xingshanghe
* @email: xingshanghe@icloud.com
* @copyright poplus.com
*
*/

namespace App\Controller\Console;
use Cake\ORM\TableRegistry;
use App\Controller\Console\ConsoleController;
use Cake\Datasource\ConnectionManager;
class OverviewController extends ConsoleController
{
    private function _checkPopedom($param)
    {
        $popedom = parent::checkConsolePopedom($param);
        return $popedom;
    }
    public function index()
    {
        $checkPopedomlist = $this->_checkPopedom('ccm_ps_Dashbord');
        if (!$checkPopedomlist)
        {
            return $this->redirect('/console/');
        }
        $user = TableRegistry::get('Accounts')->find()->select('department_id')->where(['id' => $this->request->session()->read('Auth.User.id')])->first();
        $connection = ConnectionManager::get('default');

            //查询用户的vpc
        $vpcModel = TableRegistry::get('InstanceBasic');
        $where = array(
            'type'  =>  'vpc',
            'department_id'=>$user['department_id']
            );
        $vpcs = $vpcModel->find()->where($where)->order('id desc')->toArray();

             //常规变量
        $define = array();
        $list = TableRegistry::get('Goods')->find();
        foreach ($list as $key => $value) {
            $define[$value["fixed"]] = $value["id"];
        }

        foreach ($vpcs as $key => $vpc) {
            //获取vpc的厂商和地域ID
            $class_code =  substr($vpc["location_code"],0,3);
            $agent = TableRegistry::get('Agent')->find()->select('id')->where(array("class_code"=>$class_code))->first();
            $vpcs[$key]["agentId"] =   $agent["id"];
            $agent = TableRegistry::get('Agent')->find()->select(['id','is_desktop'])->where(array("class_code"=>$vpc["location_code"]))->first();
            $vpcs[$key]["regionId"] =   $agent["id"];
            $vpcs[$key]["is_desktop"] =   $agent["is_desktop"];
                //获取防火墙
            $sql = 'select  b.* from cp_instance_relation as a  LEFT JOIN  cp_instance_basic as b ON  b.id = a.toid WHERE a.fromid = "'.$vpc['id'].'" and a.totype="firewall" limit 1';
            $firewall = $connection->execute($sql)->fetch('assoc');
            $vpcs[$key]['firewallArr'] =$firewall;
                //获取路由
            $sql = 'select  b.* from cp_instance_relation as a  LEFT JOIN  cp_instance_basic as b ON  b.id = a.toid WHERE a.fromid = "'.$vpc['id'].'" and a.totype="router" limit 1';
            $router = $connection->execute($sql)->fetch('assoc');
            $vpcs[$key]['routerArr'] =$router;
                //获取子网列表
            $sql='select  * from  cp_instance_basic left JOIN cp_subnet_extend ON cp_instance_basic.id = cp_subnet_extend.basic_id where cp_instance_basic.type="subnet" and cp_instance_basic.vpc = "'.$router['vpc'].'" order by cp_instance_basic.id desc ';
            $subnets = $connection->execute($sql)->fetchAll('assoc');

            $sql= ' select a.id,a.`code`,a.status,b.basic_id,a.`name`,c.id as \'listen\',d.ip as ip from  cp_instance_basic a LEFT JOIN cp_lbs_extend b ON a.id = b.basic_id LEFT JOIN cp_elb_listen c ON c.elb_id=a.id LEFT JOIN cp_eip_extend d ON d.basic_id = a.id where a.type="lbs" and a.vpc = "'.$vpc['code'].'" GROUP BY a.id  order by a.id desc ';
            $vpcs[$key]['lbs']=$connection->execute($sql)->fetchAll('assoc');

            foreach ($subnets as $k => $subnet) {
                $sql='select  cp_host_extend.name AS desktop_name,cp_host_extend.vnc_password,cp_instance_basic.location_name,cp_host_extend.plat_form,cp_instance_basic.status,cp_instance_basic.name,cp_instance_basic.id,cp_instance_basic.code,cp_instance_basic.status,cp_eip_extend.ip,cp_instance_basic.type,cp_instance_basic.isdelete from  cp_instance_basic left JOIN cp_host_extend ON cp_instance_basic.id = cp_host_extend.basic_id LEFT JOIN cp_eip_extend ON cp_eip_extend.basic_id=cp_instance_basic.id where ( cp_instance_basic.type="hosts" or cp_instance_basic.type="desktop") and cp_instance_basic.subnet = "'.$subnet['code'].'" order by cp_instance_basic.id desc ';
                $subnets[$k]['hosts'] = $connection->execute($sql)->fetchAll('assoc');
                $sql2='select  cp_host_extend.name AS desktop_name,cp_host_extend.vnc_password,cp_instance_basic.location_name,cp_host_extend.plat_form,cp_instance_basic.status,cp_instance_basic.name,cp_instance_basic.id,cp_instance_basic.code,cp_instance_basic.status,cp_eip_extend.ip,cp_instance_basic.type,cp_instance_basic.isdelete from  cp_instance_basic left JOIN cp_host_extend ON cp_instance_basic.id = cp_host_extend.basic_id LEFT JOIN cp_eip_extend ON cp_eip_extend.basic_id=cp_instance_basic.id where ( cp_instance_basic.type IN ("ad","vpx","ddc")) and cp_instance_basic.subnet = "'.$subnet['code'].'" order by cp_instance_basic.id desc ';
                $subnets[$k]['ad'] = $connection->execute($sql2)->fetchAll('assoc');
                $sql1='select  cp_instance_basic.name,cp_instance_basic.id,cp_instance_basic.code,cp_instance_basic.status,cp_eip_extend.ip,cp_instance_basic.type from  cp_instance_basic left JOIN cp_host_extend ON cp_instance_basic.id = cp_host_extend.basic_id LEFT JOIN cp_eip_extend ON cp_eip_extend.basic_id=cp_instance_basic.id where ( cp_instance_basic.type="lbs") and cp_instance_basic.subnet = "'.$subnet['code'].'" order by cp_instance_basic.id desc ';
                $subnets[$k]['lbs'] = $connection->execute($sql1)->fetchAll('assoc');
            }

            $vpcs[$key]['subnets'] =$subnets;
        }
        // debug($vpcs);die;
        $this->set("vpcs",$vpcs);
        $this->set("define",$define);
    }

    //获取添加子网所需的信息
    public function datainfo(){
        $arr = $this->request->data;
        $instance_relation = TableRegistry::get('InstanceRelation');
        $vpc_id = $instance_relation->find()->select(['toid'])->where(array('fromid'=>$arr['router_id'],'fromtype'=>'router','totype'=>'vpc'))->toArray();
        $returndata = array('code'=>0,'cidr'=>'','region_code'=>'','display_name'=>array());
        $instance_basic = TableRegistry::get('InstanceBasic');
        $status = $instance_basic->find()->select(['status'])->where(array('id'=>$arr['router_id']))->toArray();
        if($status[0]['status'] == '运行中') {
            if ($vpc_id) {
                $vpc_extend = TableRegistry::get('VpcExtend');
                $cidr = $vpc_extend->find()->select(['cidr'])->where(array('basic_id' => $vpc_id[0]['toid']))->toArray();
                if ($cidr) {
                    $cidr = explode('.', explode('/', $cidr[0]['cidr'])[0]);
                    $i = 0;
                    foreach ($cidr as $value) {
                        $i++;
                        $returndata['cidr']['ip' . $i] = $value;
                    }
                }
            } else {
                $returndata['code'] = 1;
            }
            $agent = TableRegistry::get('Agent');
            $agent_data = $agent->find()->select(['display_name', 'region_code'])->where(array('class_code' => $arr['agent_code']))->toArray();
            if ($agent_data) {
                $returndata['region_code'] = $agent_data[0]['region_code'];
                $returndata['display_name']['firm'] = explode('-', $agent_data[0]['display_name'])[0];
                $returndata['display_name']['area'] = explode('-', $agent_data[0]['display_name'])[1];
                $returndata['goods_id'] = $this->find_goodsid('subnet');
            } else {
                $returndata['code'] = 1;
            }
        }else{
            $returndata['code'] = 1;
        }
        echo json_encode($returndata);exit;
    }


    public function find_goodsid($subject){
        $goods_fixed = parent::readGoodsList($subject);
        $goods_table = TableRegistry::get('Goods');
        $goods = $goods_table->find()
        ->where([
            'fixed' => $goods_fixed
            ])
        ->first();
        if (! empty($goods)) {
           return $goods->id;
       }
   }

   public function checkVpc($code){
    $code =    [
    'vpc-ikb3zpCc','vpc-KauTrynE'
    ];
    foreach ($code as $key => $value) {
        if($value){
            $codes[]=  array('code'=>$value);

        }
    }
    $user = TableRegistry::get('Accounts')->find()->select('department_id')->where(['id' => $this->request->session()->read('Auth.User.id')])->first();
    $connection = ConnectionManager::get('default');

    $vpcModel = TableRegistry::get('InstanceBasic');
    $where = array(
        'type'  =>  'vpc',
        'department_id'=>$user['department_id'],
        'or' => $codes
        );
    $vpcs = $vpcModel->find()->where($where)->order('id desc')->toArray();
    foreach ($vpcs as $key => $vpc) {
                //获取防火墙
        $sql = 'select  b.* from cp_instance_relation as a  LEFT JOIN  cp_instance_basic as b ON  b.id = a.toid WHERE a.fromid = "'.$vpc['id'].'" and a.totype="firewall" limit 1';
        $firewall = $connection->execute($sql)->fetch('assoc');
        $vpcs[$key]['firewallArr'] =$firewall;
                //获取路由
        $sql = 'select  b.* from cp_instance_relation as a  LEFT JOIN  cp_instance_basic as b ON  b.id = a.toid WHERE a.fromid = "'.$vpc['id'].'" and a.totype="router" limit 1';
        $router = $connection->execute($sql)->fetch('assoc');
        $vpcs[$key]['routerArr'] =$router;
                //获取子网列表
        $sql='select  * from  cp_instance_basic left JOIN cp_subnet_extend ON cp_instance_basic.id = cp_subnet_extend.basic_id where cp_instance_basic.type="subnet" and cp_instance_basic.router = "'.$router['code'].'" order by cp_instance_basic.id desc ';
        $subnets = $connection->execute($sql)->fetchAll('assoc');

        foreach ($subnets as $k => $subnet) {
            $sql='select  cp_instance_basic.name from  cp_instance_basic left JOIN cp_host_extend ON cp_instance_basic.id = cp_host_extend.basic_id where cp_instance_basic.type="hosts" and cp_instance_basic.subnet = "'.$subnet['code'].'" order by cp_instance_basic.id desc ';
            $subnets[$k]['hosts'] = $connection->execute($sql)->fetchAll('assoc');
        }
        $vpcs[$key]['subnets'] =$subnets;
    }

    $this->set("vpcs",$vpcs);
}
}
