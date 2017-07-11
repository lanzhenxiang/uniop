<?php
/**
* class
*
* @author liubangguo@sobey.com
* @date 2015年11月6日下午2:30:19
* @source SystemInfoController.php
* @version 1.0.0
* @copyright  Copyright 2015 sobey.com
*/

namespace App\Controller\Api;

use Cake\Datasource\ConnectionManager;
use App\Controller\AppController;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

//TODO  交付生产环境时候此类应该继承AppController类，保证接口权限验证
class SystemInfoController extends AppController{
    public  $_db;
    private $_data = null;
    private $_error = null;
    private $_serialize = array('code','msg','data');
    private $_code = 0;
    private $_msg = "";
  //  private $_userid;
    public function initialize(){
        parent::initialize();
        $this->_db =ConnectionManager::get('default');
        $this->viewClass = 'Json';

        $this->loadComponent('RequestHandler');

        //获取参数
        $this->_data = $this->_getData();
    }


    //获取租户配额，及已使用信息

    public function getDepartBuget(){
        /*   {
         "userid":41
         }   */

        if ($this->_data && is_array($this->_data)){
            //$result="欢迎光临";
           // return $result;
            $code = '0';
           $request=$this->_data;
           //根据userid获取租户（租户id）
           if(isset($request['userid']))
           {
               $sql= "SELECT department_id from cp_accounts where id=".$request['userid'];
               $result=$this->_db->execute($sql)->fetchAll('assoc');
               foreach ($result as $info){
                   $request['department_id']=$info['department_id'];
               }
           }
           //


           $sql= "SELECT para_code,para_value from cp_user_setting where para_code in ('cpu_bugedt','memory_buget','gpu_bugedt','subnet_bugedt','router_bugedt','disks_bugedt','disks_cap_bugedt','fics_cap_bugedt','fics_num_bugedt','oceanstor9k_cap_bugedt','oceanstor9k_num_bugedt','basic_budget','fire_budget','elb_budget','eip_budget')  and owner_type=2 and owner_id="
               .$request['department_id'];
           $data=array();
           //$data 给个默认值
           $data['cpu_bugedt']=0;
           $data['memory_buget']=0;
           $data['gpu_bugedt']=0;
           $data['router_bugedt']=0;
           $data['subnet_bugedt']=0;
           $data['disks_bugedt']=0;
            $data['disks_cap_bugedt']=0;
           $data['fics_cap_bugedt']=0;
           $data['fics_num_bugedt']=0;
           $data['oceanstor9k_cap_bugedt']=0;
           $data['oceanstor9k_num_bugedt']=0;
            $data['basic_budget']=0;
            $data['fire_budget']=0;
            $data['elb_budget']=0;
            $data['eip_budget']=0;
           $result=$this->_db->execute($sql)->fetchAll('assoc');
           if(!empty($result)){//用户参数不为空
               foreach ($result as $info){
                   if($info['para_code']=='cpu_bugedt')
                       $data['cpu_bugedt']=intval($info['para_value']);
                   if($info['para_code']=='memory_buget')
                       $data['memory_buget']=intval($info['para_value']);
                   if($info['para_code']=='gpu_bugedt')
                       $data['gpu_bugedt']=intval($info['para_value']);
                   if($info['para_code']=='router_bugedt')
                       $data['router_bugedt']=intval($info['para_value']);
                   if($info['para_code']=='subnet_bugedt')
                       $data['subnet_bugedt']=intval($info['para_value']);
                   if($info['para_code']=='disks_bugedt')
                       $data['disks_bugedt']=intval($info['para_value']);
                   if($info['para_code']=='fics_cap_bugedt')
                       $data['fics_cap_bugedt']=intval($info['para_value']);
                   if($info['para_code']=='fics_num_bugedt')
                       $data['fics_num_bugedt']=intval($info['para_value']);
                   if($info['para_code']=='oceanstor9k_cap_bugedt')
                       $data['oceanstor9k_cap_bugedt']=intval($info['para_value']);
                   if($info['para_code']=='oceanstor9k_num_bugedt')
                       $data['oceanstor9k_num_bugedt']=intval($info['para_value']);

                   if($info['para_code']=='disks_cap_bugedt')
                       $data['disks_cap_bugedt']=intval($info['para_value']);
                   if($info['para_code']=='basic_budget')
                       $data['basic_budget']=intval($info['para_value']);
                   if($info['para_code']=='fire_budget')
                       $data['fire_budget']=intval($info['para_value']);
                   if($info['para_code']=='elb_budget')
                       $data['elb_budget']=intval($info['para_value']);
                   if($info['para_code']=='eip_budget')
                       $data['eip_budget']=intval($info['para_value']);
               }

           }
           else{//用户参数为空，取系统参数
               $sql = "SELECT para_code,para_value from cp_systemsetting where para_code in ('cpu_bugedt','memory_buget','gpu_bugedt','subnet_bugedt','router_bugedt','disks_bugedt','disks_cap_bugedt','fics_cap_bugedt','fics_num_bugedt','oceanstor9k_cap_bugedt','oceanstor9k_num_bugedt','basic_budget','fire_budget','elb_budget','eip_budget') and para_type=1";

               $result=$this->_db->execute($sql)->fetchAll('assoc');
               foreach ($result as $info){
                   if($info['para_code']=='cpu_bugedt')
                       $data['cpu_bugedt']=intval($info['para_value']);
                   if($info['para_code']=='memory_buget')
                       $data['memory_buget']=intval($info['para_value']);
                   if($info['para_code']=='gpu_bugedt')
                       $data['gpu_bugedt']=intval($info['para_value']);
                    if($info['para_code']=='router_bugedt')
                       $data['router_bugedt']=intval($info['para_value']);
                   if($info['para_code']=='subnet_bugedt')
                       $data['subnet_bugedt']=intval($info['para_value']);
                   if($info['para_code']=='disks_bugedt')
                       $data['disks_bugedt']=intval($info['para_value']);
                   if($info['para_code']=='fics_cap_bugedt')
                       $data['fics_cap_bugedt']=intval($info['para_value']);
                   if($info['para_code']=='fics_num_bugedt')
                       $data['fics_num_bugedt']=intval($info['para_value']);
                   if($info['para_code']=='oceanstor9k_cap_bugedt')
                       $data['oceanstor9k_cap_bugedt']=intval($info['para_value']);
                   if($info['para_code']=='oceanstor9k_num_bugedt')
                       $data['oceanstor9k_num_bugedt']=intval($info['para_value']);

                   if($info['para_code']=='disks_cap_bugedt')
                       $data['disks_cap_bugedt']=intval($info['para_value']);
                   if($info['para_code']=='basic_budget')
                       $data['basic_budget']=intval($info['para_value']);
                   if($info['para_code']=='fire_budget')
                       $data['fire_budget']=intval($info['para_value']);
                   if($info['para_code']=='elb_budget')
                       $data['elb_budget']=intval($info['para_value']);
                   if($info['para_code']=='eip_budget')
                       $data['eip_budget']=intval($info['para_value']);
               }
           }

        }else{
            //post 数据空
            $code = '-1';
            $msg="传入参数为空";
         //   $msg = $this->_getMsg($code);
        }
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }

    public function getDepartUsed(){
      $fics_extend_table = TableRegistry::get("FicsExtend");
/*         {
            "userid":41,
            "source_type":"cpu_used,router_used,subnet_used,disks_used"
        } */
        if ($this->_data && is_array($this->_data)){
            //$result="欢迎光临";
            // return $result;
            $code = '0';
            $request=$this->_data;
            //根据userid获取租户（租户id）
            if(isset($request['userid']))
            {
                $sql= "SELECT department_id from cp_accounts where id=".$request['userid'];
                $result=$this->_db->execute($sql)->fetchAll('assoc');
                foreach ($result as $info){
                    $request['department_id']=$info['department_id'];
                }
            }
            if(isset($request['department_id']) && isset($request['source_type'])){
                $department_id=$request['department_id'];
                $source_type=$request['source_type'];
                $data=array(
                    'cpu_used'=>0,
                    'memory_used'=>0,
                    'gpu_used'=>0,
                    'router_used'=>0,
                    'subnet_used'=>0,
                    'disks_used'=>0,
                    'disks_cap_used'=>0,
                    'fics_num_used'=>0,
                    'fics_cap_used'=>0,
                    'oceanstor9k_cap_used'=>0,
                    'oceanstor9k_num_used'=>0,
                    'basic_used'=>0,
                    'fire_used'=>0,
                    'elb_used'=>0,
                    'eip_used'=>0
                );
                //CPU、memory、gpu

                if(strstr($source_type,'cpu_used')){

                    //主机表
                    // $sql="SELECT sum(cpu) as cpu_used ,sum(memory) as memory_used FROM `cp_host_extend` a,cp_instance_basic b where a.basic_id=b.id and b.department_id=".$department_id;
                    // $result=$this->_db->execute($sql)->fetchAll('assoc');
                    // foreach ($result as $info){
                    //     $data['cpu_used']+=$info['cpu_used'];
                    //     $data['memory_used']+=$info['memory_used'];
                    // }
                    //桌面表
                    $sql="SELECT sum(cpu) as cpu_used ,sum(memory) as memory_used,sum(gpu) as gpu_used FROM `cp_host_extend` a,cp_instance_basic b where a.basic_id=b.id and b.isdelete=0 and b.department_id=".$department_id." and (b.type='hosts' or b.type='desktop')";
                    $result=$this->_db->execute($sql)->fetchAll('assoc');
                    foreach ($result as $info){
                        $data['cpu_used']+=$info['cpu_used'];
                        $data['memory_used']+=$info['memory_used'];
                        $data['gpu_used']+=$info['gpu_used'];
                    }

                }
                //路由
                if(strstr($source_type,'router_used')){
                    //router

                    $sql="SELECT count(id) as router_used  from cp_instance_basic a where a.type='router' and a.isdelete=0 and department_id=".$department_id;
                    $result=$this->_db->execute($sql)->fetchAll('assoc');
                    foreach ($result as $info){
                        $data['router_used']+=$info['router_used'];
                    }
                }
                //子网
                if(strstr($source_type,'subnet_used')){
                    //router

                    $sql="SELECT count(id) as subnet_used  from cp_instance_basic a where a.type='subnet' and a.isdelete=0 and department_id=".$department_id;

                    $result=$this->_db->execute($sql)->fetchAll('assoc');
                    foreach ($result as $info){
                        $data['subnet_used']+=$info['subnet_used'];
                    }
                }
                //硬盘
                if(strstr($source_type,'disks_used')){
                    //router

//                    $sql="SELECT sum(capacity) as disks_used FROM `cp_disks_metadata` a,cp_instance_basic b where a.disks_id=b.id and b.isdelete=0 and b.department_id=".$department_id;
                    $sql="SELECT count(a.id) as disks_used FROM `cp_disks_metadata` a,cp_instance_basic b where a.disks_id=b.id and b.isdelete=0 and b.department_id=".$department_id;
                    $result=$this->_db->execute($sql)->fetchAll('assoc');
                    foreach ($result as $info){
                        $data['disks_used']+=$info['disks_used'];
                    }
                }

                //fics
                if (strstr($source_type,'fics_cap_used') || strstr($source_type,'fics_num_used')) {
                    $fics_data = $fics_extend_table->find()->where(["vol_type" => "fics", "department_id" => $department_id])->toArray();
                    $num = 0; #fics数量
                    $size = 0;#fics容量
                    if (!empty($fics_data)) {
                        foreach ($fics_data as  $fics) {
                          $num ++;
                          $size += $fics["total_cap"];
                        }
                    }

                    $data['fics_num_used'] = $num;
                    $data['fics_cap_used'] = $size;
                }
                //华为9K
                if (strstr($source_type,'oceanstor9k_cap_used') || strstr($source_type,'oceanstor9k_num_used')) {
                    $fics_data = $fics_extend_table->find()->where(["vol_type" => "oceanstor9k", "department_id" => $department_id])->toArray();
                    $num = 0; #华为9K数量
                    $size = 0;#华为9K容量
                    if (!empty($fics_data)) {
                        foreach ($fics_data as  $fics) {
                          $num ++;
                          $size += $fics["total_cap"];
                        }
                    }

                    $data['oceanstor9k_num_used'] = $num;
                    $data['oceanstor9k_cap_used'] = $size;
                }


                //桌面基础套件
                if(strstr($source_type,'basic_used')){

                    $sql="SELECT count(id) as basic_used  from cp_instance_basic a where a.type='ddc' and a.isdelete=0 and department_id=".$department_id;

                    $result=$this->_db->execute($sql)->fetchAll('assoc');
                    foreach ($result as $info){
                        $data['basic_used']+=$info['basic_used'];
                    }
                }
                //防火墙
                if(strstr($source_type,'fire_used')){

                    $sql="SELECT count(id) as fire_used  from cp_instance_basic a where a.type='firewall' and a.isdelete=0 and department_id=".$department_id;

                    $result=$this->_db->execute($sql)->fetchAll('assoc');
                    foreach ($result as $info){
                        $data['fire_used']+=$info['fire_used'];
                    }
                }
                //负载均衡
                if(strstr($source_type,'elb_used')){

                    $sql="SELECT count(id) as elb_used  from cp_instance_basic a where a.type='lbs' and a.isdelete=0 and department_id=".$department_id;

                    $result=$this->_db->execute($sql)->fetchAll('assoc');
                    foreach ($result as $info){
                        $data['elb_used']+=$info['elb_used'];
                    }
                }
                //公网ip
                if(strstr($source_type,'eip_used')){

                    $sql="SELECT count(id) as eip_used  from cp_instance_basic a where a.type='eip' and a.isdelete=0 and department_id=".$department_id;

                    $result=$this->_db->execute($sql)->fetchAll('assoc');
                    foreach ($result as $info){
                        $data['eip_used']+=$info['eip_used'];
                    }
                }

            }

        }else{
            //post 数据空
            $code = '-1';
            $msg="传入参数为空";
            //   $msg = $this->_getMsg($code);
        }
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);

    }
    //获取系统参数
    public function getSysValue(){
        if ($this->_data && is_array($this->_data)){
            //$result="欢迎光临";
            // return $result;
            $code = '0';
            $request=$this->_data;

            $sql= "SELECT para_code,para_value,para_note from cp_systemsetting where para_code='"
                .$request['para_code']."'";
          //  $data=array();
            $result=$this->_db->execute($sql)->fetchAll('assoc');
            if(!empty($result)){//系统参数不为空
                foreach ($result as $info){
                    $data['para_code']=$info['para_code'];
                    $data['para_value']=$info['para_value'];
                    $data['para_note']=$info['para_note'];
                 }

            }
            else{//用户参数为空，取系统参数
                $code=0;
                $msg="没有参数，以默认代替";
                if(!isset($request['para_value']))
                    $request['para_value']='null';
                if(!isset($request['para_note']))
                    $request['para_note']='';
                $sql = "insert into cp_systemsetting(para_code,para_value,para_note) values('".$request['para_code']
                    ."','".$request['para_value']."','".$request['para_note']."')";
                $this->_db->execute($sql);
                $data['para_code']=$request['para_code'];
                $data['para_value']='null';
               $data['para_note']=$request['para_note'];
            }
        }else{
            //post 数据空
            $code = '-1';
            $msg="传入参数为空";
            //   $msg = $this->_getMsg($code);
        }
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }

    public function getSysDValue($value='')
    {
      $data=array();
      if (is_array($value)){
            //$result="欢迎光临";
            // return $result;
            $code = '0';
            $sql= "SELECT para_code,para_value,para_note from cp_systemsetting where para_code='"
                .$value['para_code']."'";

            $result=$this->_db->execute($sql)->fetchAll('assoc');
            if(!empty($result)){//系统参数不为空
                foreach ($result as $info){
                    $data['para_code']=$info['para_code'];
                    $data['para_value']=$info['para_value'];
                    $data['para_note']=$info['para_note'];
                 }
            }
            else{//用户参数为空，取系统参数
                $code=0;
                $msg="没有参数，以默认代替";
                if(!isset($value['para_value']))
                    $value['para_value']='null';
                if(!isset($value['para_note']))
                    $value['para_note']='';
                $sql = "insert into cp_systemsetting(para_code,para_value,para_note) values('".$value['para_code']
                    ."','".$value['para_value']."','".$value['para_note']."')";
                $this->_db->execute($sql);
                $data['para_code']=$value['para_code'];
                $data['para_value']='null';
               $data['para_note']=$value['para_note'];
            }
        }else{
            $code = '-1';
            $msg="传入参数为空";
        }
        return $data;
    }


    private function _getData(){
        $data = $this->request->data?$this->request->data:file_get_contents('php://input', 'r');
        //处理非x-form的格式
        if (is_string($data)){
            $data_tmp = json_decode($data,true);
            if (json_last_error() == JSON_ERROR_NONE){
                $data = $data_tmp;
            }
        }

        Log::debug("Data Posted :".json_encode($data),['action'=>$this->request->params['action'],'host'=>$this->request->host()]);

        return $data;
    }
}