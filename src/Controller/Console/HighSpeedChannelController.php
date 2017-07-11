<?php
/**
* 高速通道
*
*
* @author XingShanghe<xingshanghe@gmail.com>
* @date  2015年9月23日下午3:03:33
* @source NetworkController.php
* @version 1.0.0
* @copyright  Copyright 2015 sobey.com
*/


namespace App\Controller\Console;

use App\Controller\Console\ConsoleController;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Network\Http\Client;
use Cake\Error\FatalErrorException;

class HighSpeedChannelController extends ConsoleController
{
    private $_serialize = array('code','msg','data');
    private $_popedomName = array(
//        'hosts' => 'ccm_ps_hosts',

    );
    private $_addPopedomName = array(

    );
    private function _checkPopedom($param)
    {
        $popedom = parent::checkConsolePopedom($param);
        return $popedom;
    }

    /**
     * 新建高速通道边界路由器
     */
    public function add()
    {
        $subject = 'vbr';
        $goods_fixed = parent::readGoodsList($subject);
        $goods_table = TableRegistry::get('Goods');
        $goods = $goods_table->find()
            ->where([
                'fixed' => $goods_fixed
            ])
            ->first();
        if (! empty($goods)) {
            $this->set('goods_id', $goods->id);
        }
        $popedomname = $this->request->session()->read('Auth.User.popedomname') ? $this->request->session()->read('Auth.User.popedomname') : 0;
        $this->set('popedomname', $popedomname);
    }

    /**
     * 创建边界路由器接口
     */
    public function createRouterInterface()
    {
        $subject = 'vbrI';
        $goods_fixed = parent::readGoodsList($subject);
        $goods_table = TableRegistry::get('Goods');
        $goods = $goods_table->find()
            ->where([
                'fixed' => $goods_fixed
            ])
            ->first();
        if (! empty($goods)) {
            $this->set('goods_id', $goods->id);
        }
        $basic_table = TableRegistry::get('InstanceBasic');
        //路由器信息
        $vbr_data = $basic_table->find()->contain('agent')->where(array('InstanceBasic.id' => $this->request->query['vbr_id']))->first();
        $this->set('vbr_data', $vbr_data);

        $popedomname = $this->request->session()->read('Auth.User.popedomname') ? $this->request->session()->read('Auth.User.popedomname') : 0;
        $this->set('popedomname', $popedomname);
        

        //获取边界路由器  阿里云vpc信息
        
        $vbr_extends_table = TableRegistry::get('VbrExtends');
        $data = $vbr_extends_table->find()->where(['basic_id' => $this->request->query['vbr_id']])->first();
        
        $vpc_data = $basic_table->find()->select(['name', "code"])->where(['code' => $data['aliyun_vpcCode']])->first();
        $this->set('vbr_id', $this->request->query['vbr_id']);
        $this->set('vpc', $vpc_data);
    }


    public function createHSCArray()
    {
        $this->viewClass = 'Json';
        $data = [
            0 =>[
                'connectscene'=>[
                    'label' =>   '专线接入阿里云',
                    'code'  => 'toaliyun'
                ],
                //本端配置
                'localconfig'   =>  $this->_getCreateHSCArray('sobey'),
                //对端配置
                'remoteconfig'  =>  $this->_getCreateHSCArray('aliyun')
            ]
        ];
        $code = 0;
        $msg  = '';
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }

    public function createRouterInterfaceArray()
    {
        $this->viewClass = 'Json';
        $data = [
                'priceList'=>[
                    0=>[
                        'label' =>'固定计费',
                        'code'  =>'cycle',
                        'interval'=>[
                            0 =>[
                                'label'=>'按日计费',
                                'code' =>'D',
                            ],
                            1 =>[
                                'label'=>'按月计费',
                                'code' =>'M'
                            ],
                            2 =>[
                                'label'=>'按年计费',
                                'code' =>'Y'
                            ]
                        ]
                    ]
                ],
                //本端配置
                'spec'=>[
                    0 =>[
                        'label' => '小型1档-1.25MB',
                        'code'  => 'Small.1',
                        'number'=>'1.25'
                    ],
                    1 =>[
                        'label' => '小型2档-2.5MB',
                        'code'  => 'Small.2',
                        'number'=>'2.5'
                    ],
                    2 =>[
                        'label' => '小型5档-6.25MB',
                        'code'  => 'Small.5',
                        'number'=>'6.25'
                    ],
                    3 =>[
                        'label' => '中型1档-12.5MB',
                        'code'  => 'Middle.1',
                        'number'=>'12.5'
                    ],
                    4 =>[
                        'label' => '中型2档-25MB',
                        'code'  => 'Middle.2',
                        'number'=>'25'
                    ],
                    5 =>[
                        'label' => '中型5档-62.5MB',
                        'code'  => 'Middle.5',
                        'number'=>'62.5'
                    ],
                    6 =>[
                        'label' => '大型1档-125MB',
                        'code'  => 'Large.1',
                        'number'=>'125'
                    ],
                    7 =>[
                        'label' => '大型2档-256MB',
                        'code'  => 'Large.2',
                        'number'=>'256'
                    ],
                ],
                //对端配置
                'remoteconfig'  =>  $this->_getCreateHSCArray('aliyun')
        ];
        $code = 0;
        $msg  = '';
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }

    public function getRouterInterfacePrice()
    {
        $this->viewClass = 'Json';
        $requestData = $this->request->data();
        $data['totalPrice'] = 0;
        $price = 0;
        $agents = TableRegistry::get("Agent");
        $chargeExtends = TableRegistry::get('chargeExtend');
        $agentEntity =$agents->getAgentRootEntity($requestData['regionCode']);
        if($agentEntity){
            $chargeEntity = $chargeExtends->find()->where(['agent_id'=>$agentEntity->id,'charge_object'=>'vbri'])->first();
            if($chargeEntity){
                switch ($requestData['chargeType']){
                    case 'D':
                        $price = $chargeEntity->daily_price;
                        $data['priceTxt'] = '元/天';
                        break;
                    case 'M':
                        $price = $chargeEntity->monthly_price;
                        $data['priceTxt'] = '元/月';
                        break;
                    case 'Y':
                        $price = $chargeEntity->yearly_price;
                        $data['priceTxt'] = '元/年';
                        break;
                }
                $data['totalPrice'] = $price * $requestData['spec'];
                $code = '0';
                $msg = '成功!';
            }else{
                $code = '-1';
                $msg = '其他计费单价不存在，获取计费失败';
            }
        }else{
            $code = '-1';
            $msg = '区域code不存在，获取计费失败';
        }
        $this->set(compact(array_values($this->_serialize)));
        $this->set('_serialize',$this->_serialize);
    }
    
    protected function _getCreateHSCArray($agentCode)
    {
        $department_id = $this->getOwnByDepartmentId();
        $agent_table = TableRegistry::get('Agent');
        $data_list   = $agent_table->find('tree', array(
            'order' => 'Agent.sort_order ASC',
            'for' => 13
        ))
            ->where(array(
                'is_enabled' => 1,
            ))
            ->toArray();
        foreach ($data_list as $key => $value) {
            // debug($value['children']);die();
            if (!empty($value['children']) && $value['agent_code'] == $agentCode) {
                $data_agent[$key] = array(
                    'id'      => $value['id'],
                    'company' => array(
                        'name'        => $value['agent_name'],
                        'code' => $value['agent_code'],
                    ),
                    'area'    => array(),
                );
                foreach ($value['children'] as $kk => $vv) {
                    $data_agent[$key]['area'][] = array(
                        'name'     => $vv['agent_name'],
                        'code' => $vv['region_code'],
                        'vpc'      => $this->getSelectVpc($vv['class_code'],$department_id),
                    );

                }
            }
        }
        $data_agent = array_values($data_agent);
        return $data_agent;
    }

    public function getSelectVpc($class_code,$department_id)
    {
        $table         = TableRegistry::get('InstanceBasic');
        $vpcArray      = array();
        $where         = array(
            'status'        => '运行中',
            'type'          => 'vpc',
            'location_code' => $class_code,
            'department_id' => $department_id,
        );
        $vpcList = $table->find('all')
            ->where($where)
            ->toArray();
        // debug($vpcList);
        foreach ($vpcList as $key => $value) {
            if($table->find()->select(['id'])->where(array('type'=>'firewall','vpc'=>$value['code']))->count()==0){
                $vpcArray[] = array(
                    'name'   => $value['name'],
                    'code' => $value['code'],
                    'subnet'    => $this->getAllsubNet($value['code']),
                );
            }

        }
        return $vpcArray;
    }

    public function getAllsubNet($vpCode)
    {
        if(is_array($vpCode)){
            $vpCode=$vpCode["vpCode"];
        }
        $table = TableRegistry::get('InstanceBasic');
        $where = array(
            'status' => '运行中',
            'type'   => 'subnet',
            'vpc'    => $vpCode,
        );
        $netArray = $table->find('all')
            ->contain(array(
                'SubnetExtend'
            ))
            ->where($where)
            ->toArray();
        $subnetList = array();
        foreach ($netArray as $key => $value) {
            if($value['subnet_extend']['fusionType'] == ""){
                $fusionType = "vmware";
            } else {
                $fusionType = $value['subnet_extend']['fusionType'];
            }
            $subnetList[] = array(
                'name'     => $value['name'],
                'code'  => $value['code'],
                'fusionType' => $fusionType,
            );
        }
        return $subnetList;
    }

}