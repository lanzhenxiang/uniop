<?php
/**
 * ==============================================
 * OrdersController.php
 * @author: shrimp liao
 * @date: 2015年9月14日 下午3:00:17
 * @version: v1.0.0
 * @desc:订单
 * ==============================================
 **/
namespace App\Controller;

use App\Controller\HomeController as Home;
use App\Controller\SobeyController;
use Cake\Core\Configure;
use Cake\Network\Http\Client;
use Cake\ORM\TableRegistry;
use App\Controller\Console\WorkflowController;
use App\Controller\Console\ConsoleController;

class OrdersController extends SobeyController
{
    protected $new_goods = ["citrix", "bs", "mpaas", "ecs", "citrix_public", "eip", "vfw", "waf", 'vpc', 'elb', 'disks'];
    protected $no_new_goods = ["onvpc", "rds"];
    protected $_need_config_goods = ['ecs', 'citrix_public', 'eip', 'vfw', 'waf', 'vpc', 'elb', 'disks']; // 需要配置的商品
    /**
     * @func: diy 商品配置页面，如果不是可配商品直接跳转到 商品清单页面  ecs 页面
     * @param:
     * @date: 2015年9月23日 下午6:28:05
     * @author: shrimp liao
     * @return: null
     */
    public function diy($goods_id = 0, $n = 1)
    {
    }

    public function car()
    {
        $goods_id         = $this->request->data['goods_id'];
        $n                = $this->request->data['n'];
        $this->set('_number', parent::readCookieByNumber());
        $goodsDesc   = Home::getGoodsInfoDes($goods_id);
        $unser_goods = parent::readCookie('user.car');
        $attr        = array();
        
        if ($this->request->data["isCustom"] == "1") {
            $unser_goods[] = array('goods_id' => $goods_id, 'attr' => null, 'is_console' => 0, 'num' => $n,'charge'=>$this->request->data["charge"],'charge'=>$this->request->data["charge"]);
        } else {
            //新商品
            if (isset($this->request->data["goodType"]) && !in_array($this->request->data["goodType"], $this->no_new_goods)){
                switch ($this->request->data["goodType"]) {
                    case "ecs":
                    case "citrix_public":
                    case "eip":
                    case "vfw":
                    case "waf":
                    case "vpc":
                    case 'elb':
                    case 'disks':
                        unset($this->request->data['n']);
                        
                        $this->request->data["good_type"] = $this->request->data["goodType"];
                        $this->request->data['is_console'] = 0;
                        $this->request->data['attr'] = null;
                        $this->request->data['is_console'] = 0;
                        $hash = hash("md5", serialize($this->request->data), false);
                        if (isset($unser_goods[$hash])) {
                            $unser_goods[$hash]['num'] += $n; 
                        } else {
                            $this->request->data['num'] = $n;
                            $unser_goods[$hash] = $this->request->data;
                        }
                        break;
                    default :
                        $is_set = 0;//判断购物车中是否有该商品
                        if(!empty($unser_goods)){
                            foreach ($unser_goods as $key => &$gv) {
                                if ($gv['goods_id'] == $goods_id && $gv['version'] == $this->request->data["version"] && $gv['price_id'] == $this->request->data["price_id"]) {
                                    $gv['num'] = empty($gv['num']) ? 0:$gv['num']+1;
                                    $is_set = 1;
                                    break;
                                }
                            }
                        }
                        if($is_set == 0) {
                            $bscharge = isset($this->request->data["bscharge"])?$this->request->data["bscharge"]:0;
                            $unser_goods[$goods_id.'-'.$this->request->data["version"].'-'.$this->request->data["price_id"]] = array('goods_id' => $goods_id, 'attr' => null, 'is_console' => 0, 'num' => $n,'charge'=>$this->request->data["charge"], 'version' => $this->request->data["version"], 'good_type' => $this->request->data["goodType"], "bscharge" => $bscharge, "price_id" => $this->request->data["price_id"]);
                        }
                        break;
                }
            } else {
                if (!empty($this->request->data["version"]) && $this->request->data["version"] != "0") {
                    $unser_goods[] = array('goods_id' => $goods_id, 'attr' => null, 'is_console' => 0, 'num' => $n, 'version' => $this->request->data["version"], 'charge'=>$this->request->data["charge"]);
                } else {
                    $unser_goods[] = array('goods_id' => $goods_id, 'attr' => null, 'is_console' => 0, 'num' => $n, 'charge'=>$this->request->data["charge"]);
                }
            }
        }
        //如果商品是固定的那么属性不记录使用时去数据库查询
        $ser_goods = serialize($unser_goods);
        $this->Cookie->write('user.car', base64_encode($ser_goods));
        $this->viewClass = 'Json';
        $number = parent::readCookieByNumber();
        $this->set(compact(['number']));
        $this->set("_serialize",['number']);
    }

    public function del($index)
    {
        $this->autoRender = false;
        //读取Cookie 数组
        //删除索引的，从新加到cookie中去
        $goodsCookie = parent::readCookie('user.car');
        unset($goodsCookie[$index]);
        $this->Cookie->delete('user.car');
        $ser_goods = serialize($goodsCookie);
        $this->Cookie->write('user.car', base64_encode($ser_goods));
        $this->lauout = 'ajax';
        echo "ok";
    }

    public function changenum()
    {
        $this->autoRender           = false;
        $index                      = $this->request->data['index'];
        $num                        = $this->request->data['num'];;
        $goodsCookie                = parent::readCookie('user.car');
        $goodsCookie[$index]["num"] = $num;
        $ser_goods                  = serialize($goodsCookie);
        $this->Cookie->write('user.car', base64_encode($ser_goods));
        $goodsCookie  = parent::readCookie('user.car');
        $this->lauout = 'ajax';
        echo "ok";
    }

    public function delCarCookieByIndex()
    {
        $this->autoRender = false;
        $this->lauout     = 'ajax';
        $this->Cookie->delete('user.car');
        $goodsCookie = parent::readCookie('user.car');
        debug($goodsCookie);
    }

    public function red()
    {
        $this->autoRender = false;
        $this->lauout     = 'ajax';
        $goodsCookie      = parent::readCookie('user.car');
        // $goodsCookie = $this->Cookie->read();
        debug($goodsCookie);
    }

    public function ttttt()
    {
        // $url = "http://127.0.0.1:9090";
        // $array = array("method"=>"onvpc","uid"=>"133","regionCode"=>"region-sobeycymis","name"=>"haha");
        // $vpc =  array("cidr"=>"172.16.0.0/20","tag"=>"_vpc");
        // $router =  array("vpc_tag"=>"_vpc","tag"=>"_vpc");
        // $firewall = array("vpc_tag"=>"_vpc","tag"=>"_firewall");;
        // $subnet =  array();
        // $subnet[] = array("vpc_tag"=>"_vpc","cidr"=>"172.16.1.0/24","fusionType"=>"vmware","tag"=>"_subnet1");
        // $subnet[] = array("vpc_tag"=>"_vpc","cidr"=>"172.16.15.0/24","fusionType"=>"vmware","tag"=>"_subnet2");
        // $eip = array();
        // // $eip[] = array();
        // // $eip[] = array("instanceTypeCode"=>"","imageCode"=>"","vpc_tag"=>"_vpc","subnet_tag"=>"_subnet1","netcard_tag"=>"_subnet2");
        // $hosts =  array();
        // $hosts[] = array("instanceTypeCode"=>"","imageCode"=>"","vpc_tag"=>"_vpc","subnet_tag"=>"_subnet1","netcard_tag"=>"_subnet2");
        // $hosts[] = array("instanceTypeCode"=>"","imageCode"=>"","vpc_tag"=>"_vpc","subnet_tag"=>"_subnet1","netcard_tag"=>"_subnet2");
        // $desktop =  array();
        // $desktop[] = array("instanceTypeCode"=>"","imageCode"=>"","vpc_tag"=>"_vpc","subnet_tag"=>"_subnet1","netcard_tag"=>"_subnet2");
        // $desktop[] = array("instanceTypeCode"=>"","imageCode"=>"","vpc_tag"=>"_vpc","subnet_tag"=>"_subnet1","netcard_tag"=>"_subnet2");
        // $data=array("method"=>"onvpc","uid"=>"133","regionCode"=>"region-sobeycymis","name"=>"haha","vpc"=>$vpc,"router"=>$router,"subnet"=>$subnet,"firewall"=>$firewall,"eip"=>$eip,"hosts"=>$hosts,"desktop"=>$desktop);
        // $aaa = json_encode($data, true);
        // $array["vpcData"] = $aaa;
        // // debug($array);die;
        // $result = $this->postInterface($url, $array);
        // debug($result);die;

        $vpc_store_user_table  = TableRegistry::get('VpcFicsUsers');
        $user_data = $vpc_store_user_table->getUserLimitByVpcId(16);
        debug($user_data);die;
    }
    
    /**
     * 拼装CitrixPublic 的cofig信息
     * @param unknown $config
     * 
     */
    protected function getCitrixPublicConfigInfo($config){
        $config = (array)json_decode($config);
        $arr = array();
        $arr['vpc']['name'] = $config['vpcName'];
        $arr['vpc']['code'] = $config['vpcCode'];
        $arr['subnet']['name'] = $config['netName'];
        $arr['subnet']['code'] = $config['subnetCode'];
        $arr['subnet2']['name'] = $config['netName2'];
        $arr['subnet2']['code'] = $config['subnetCode2'];
        $arr['ecsName'] = $config['ecsName'];
        return $arr;
    }

    /**
     * @func: buy 清单页面（读取的都是Cookie）
     * @param: $id 商品id
     * @date: 2015年9月14日 下午3:02:27
     * @author: shrimp liao
     * @return: null
     */
    public function buy()
    {
        $goodsCookie = parent::readCookie('user.car');
        $this->Cookie->delete('user.car');
        $home = new Home();
        $type = TableRegistry::get('ServiceType');
        if ($goodsCookie) {
            $goodsPrice = 0.0;
            $cookie1    = array();
            $cookie2    = array();
            foreach ($goodsCookie as $index => $item) {
                $goodsDesc = Home::getGoodsInfoDes($item['goods_id']);
                $item['fixed'] = $goodsDesc['goods'][0]['fixed'];
                $cookie2[]        = $item;
                $item['info']  = $goodsDesc;
                //判断新版商品
                if (isset($item["good_type"]) && !empty($item["good_type"])) {
                    switch ($item["good_type"]) {
                        case 'ecs':
                            $item['version'] = json_decode($item['config'],true);
                            $item['version']["id"] = $item['version']["csid"];
                            break;
                        case 'citrix_public':
                            //获取商品规格
                            $HomeController = new HomeController();
                            $item['version'] = $HomeController->getNewGoodVersionByVersionId($item["version"]);
                            $item['version'] = array_merge($item['version'], $this->getCitrixPublicConfigInfo($item['config']));
                            $item['version']["price_info"] = Home::getPriceInfoById($item["price_id"]);
                            break;
                        case 'eip':
                        case 'vfw':
                        case 'waf': 
                        case 'vpc':
                        case 'elb':
                        case 'disks':
                            $HomeController = new HomeController();
                            $item['version'] = $HomeController->getNewGoodVersionByVersionId($item["version"]);
                            $item['version'] = array_merge($item['version'],json_decode($item['config'],true));
                            break;
                        default:
                            $HomeController = new HomeController();
                            $item['version'] = $HomeController->getNewGoodVersionByVersionId($item["version"]);
                            $item['version']["price_info"] = Home::getPriceInfoById($item["price_id"]);
                            break;
                    }
                } else { // 一键vpc
                    if (!empty($item['version']) && $item['version'] != "0") {
                        $goodsAttribute = Home::getAttributeById($item['version']);
                    }
                    if (!empty($item['version']) && $item['version'] != "0") {
                        $item['version']           = $goodsAttribute;
                        $item['version']["config"] = Home::getConfigByCode($goodsAttribute->goods_attribute_detail["instanceTypeCode"]);
                        $item['version']["image"]  = Home::getImageByCode($goodsAttribute->goods_attribute_detail["imageCode"]);
                        $item['charge'] = array("id" => $item['charge'], 'name' => parent::_GetBillCycle($item['charge']));
                    } else {
                        $item['charge'] = $type->find('all')->contain(array('ChargeTemplate'))->where(array('type_id' => $goodsDesc["goods"][0]["service_id"]))->toArray();
                    }
                }
                $cookie1[]        = $item;
            }
            $ser_goods = serialize($cookie2);
            $this->Cookie->write('user.car', base64_encode($ser_goods));
            $goodsCookie = parent::readCookie('user.car');
            $this->set('_goodsCookie', $cookie1);
            $this->set('_number', parent::readCookieByNumber());

        }
        $this->set('good_category', $home->getCategoeyGoodsData());
        $this->set('no_new_goods', $this->no_new_goods);
    }
    /**
     * @func: 通过cookie保存用户购物车信息 ajax
     * @param:
     * @date: 2015年9月22日 下午4:32:51
     * @author: shrimp liao
     * @return: null
     */
    public function addShoppingCar()
    {
        $this->autoRender = false;
        $goods_id         = isset($this->request->data['goods_id']) ? $this->request->data['goods_id'] : 0;
        //商品ID
        $attr = $this->request->data['attr'];
        //商品属性txt
        $type = $this->request->data['type'];
        //商品属性txt
        $is_console = $this->request->data['is_console'];
        $number     = 0;
        //保存car
        //如果存在，累加
        $goods_fixed = TableRegistry::get('Goods');
        $fixed       = $goods_fixed->find()->select(array('fixed'))->where(array('id =' => $goods_id))->toArray();

//         if ($this->Cookie->read('user.webcar')) {
//             $unser_goods   = parent::readCookie('user.webcar');
//             $jsonAttr      = json_encode($this->createEcsCookieObjectArray($attr, $fixed[0]['fixed']));
//             $unser_goods[] = array('goods_id' => $goods_id, 'attr' => $jsonAttr, 'is_console' => $is_console, 'config' => json_encode($this->request->data));
            
//             $ser_goods     = serialize($unser_goods);
//             $this->Cookie->write('user.webcar', base64_encode($ser_goods));
//             foreach ($unser_goods as $item) {
//                 if (isset(json_decode($jsonAttr, true)['number'])) {
//                     $number = (int) json_decode($jsonAttr, true)['number'];
//                 } else {
//                     $number = 1;
//                 }
//             }
//         } else {
            $jsonAttr  = json_encode($this->createEcsCookieObjectArray($attr, $fixed[0]['fixed']));
            $goods     = array(array('goods_id' => $goods_id, 'attr' => $jsonAttr, 'is_console' => $is_console));
            $ser_goods = serialize($goods);
            //序列号
            $this->Cookie->write('user.webcar', base64_encode($ser_goods));
            //base64加密
            if (isset(json_decode($jsonAttr, true)['number'])) {
                $number = (int) json_decode($jsonAttr, true)['number'];
            } else {
                $number = 1;
            }
//         }
        $this->begainBuy('webcar');
        $this->lauout = 'ajax';
    }

    public function addTemplateShoppingCar()
    {
        $this->autoRender = false;
        $goods_id         = $this->request->data['goods_id'];
        //业务模板id
        $biz_tid = $this->request->data['biz_tid'];
        //商品属性txt
        $type = $this->request->data['type'];
        //商品属性txt
        $is_console = $this->request->data['is_console'];
        $number     = 0;
        //保存car
        //如果存在，累加
        $goods_fixed = TableRegistry::get('Goods');
        $fixed       = $goods_fixed->find()->select(array('fixed'))->where(array('id =' => $goods_id))->toArray();

        //商品属性
        $attrBase = $this->request->data['attr'];
        $business_template_detail = TableRegistry::get('BusinessTemplateDetail');
        $detail_list = $business_template_detail->find()->where(['biz_tid'=>$biz_tid])->contain('SetHardware')->toArray();
        $goods_list = array();
        foreach ($detail_list as $key=>$value) {
            $attr = $attrBase;
            $attr['ecsName']   = $value['tagname'];
            $attr['imageCode'] = $value['image_code'];
            $attr['imageName'] = $value['image_name'];
            $attr['cpu']       = (string)$value['set_hardware']['cpu_number'];
            $attr['rom']       = (string)$value['set_hardware']['memory_gb'];
            $attr['instanceTypeCode'] = $value['instance_code'];
            $attr['number']    = (string)$value['number'];
            $attr['bizTid']   = $biz_tid;
            $jsonAttr      = json_encode($this->createEcsCookieObjectArray($attr, $fixed[0]['fixed']));
            $goods_list[] = array('goods_id' => $goods_id, 'attr' => $jsonAttr, 'is_console' => $is_console);
        }
        if ($this->Cookie->read('user.webcar')) {
            $unser_goods   = parent::readCookie('user.webcar');
            if(!empty($unser_goods) && is_array($unser_goods)){
                $unser_goods = array_merge($unser_goods,$goods_list);
            }
            $ser_goods     = serialize($unser_goods);
            $this->Cookie->write('user.webcar', base64_encode($ser_goods));
            foreach ($unser_goods as $item) {
                if (isset(json_decode($jsonAttr, true)['number'])) {
                    $number = (int) json_decode($jsonAttr, true)['number'];
                } else {
                    $number = 1;
                }
            }
        } else {
            $ser_goods = serialize($goods_list);
            //序列号
            $this->Cookie->write('user.webcar', base64_encode($ser_goods));
            //base64加密
            if (isset(json_decode($jsonAttr, true)['number'])) {
                $number = (int) json_decode($jsonAttr, true)['number'];
            } else {
                $number = 1;
            }
        }

        $this->begainBuy('webcar');
        $this->lauout = 'ajax';
    }



    /**
     * @func: 下订单并发送消息给第三方
     * @param:
     * @date: 2015年10月10日 下午3:17:55
     * @author: shrimp liao
     * @return: null
     */
    public function begainBuy($userCookie)
    {
        $goods_version_detail_table = TableRegistry::get("GoodsVersionDetail");
        $goods_version_price_talbe = TableRegistry::get('GoodsVersionPrice');
        $this->autoRender = false;
        $user_id          = (string) $this->request->session()->read('Auth.User.id');
        if (empty($user_id)) {
            $interface["url"]  = "/accounts/login";
            $interface["Code"] = "400";
            echo json_encode($interface);
        } else {
            $oldCookie = array();
            if ($userCookie == "webcar") {
                $userCookie = "user.webcar";
            } else {
                $userCookie = "user.car";
                //重新整理购物车
                $goodsCookie = parent::readCookie($userCookie);
//                var_dump($goodsCookie);exit;
                $indexs      = $this->request->data["indexs"];
                $array_index = explode(',', $indexs);
                array_pop($array_index);
                $newCookie   = array();
                foreach ($goodsCookie as $k => $c) {
                    if (in_array($k, $array_index)) {
                        $newCookie[] = $c;
                    } else {
                        $oldCookie[] = $c;
                    }
                }
                //存.
                $ser_goods = serialize($newCookie);
                $this->Cookie->write('user.car', base64_encode($ser_goods));

            }
            $data        = $this->request->data;
            $price_total = 0;
            if (!empty($data['totle'])) {
                $price_total = $data['totle'];
            }
            $url = Configure::read('URL');

            $goodsCookie  = parent::readCookie($userCookie);
            $IS_CONSOLE   = $goodsCookie[0]['is_console'];
            $result_array = array();
            //拆单
            foreach ($goodsCookie as $index => $item) {
                $goodsDesc                   = Home::getGoodsInfoDes($item['goods_id']);
                $goodsCookie[$index]['info'] = $goodsDesc;
                //判断非一键vpc
                if ($goodsDesc['goods'][0]['fixed'] == 0 && isset($goodsDesc['goods'][0]['goodType']) && in_array($goodsDesc['goods'][0]['goodType'], $this->new_goods)) {
                    //获取商品规格
                    $HomeController = new HomeController();
                    $goodsCookie[$index]['version_id'] = $item['version']; //版本
                    $goodsCookie[$index]['version'] = $HomeController->getNewGoodVersionByVersionId($item["version"]);
                    //citrix_public 类型商品需要根据config修改vpc,subnet
                    if ($item['good_type'] == 'citrix_public') {
                        $goodsCookie[$index]['version'] = array_merge($goodsCookie[$index]['version'], $this->getCitrixPublicConfigInfo($item['config']));
                    }
                    //ecs费用来源不同
//                     debug($item['good_type']);die;
                    switch ($item['good_type']) {
                        case 'ecs':
                            $config = json_decode($item['config']);
                            $config->imagePay = isset($config->imagePay) ? $config->imagePay : 0;
                            $config->instancePay  = isset($config->instancePay ) ? $config->instancePay  : 0;
                            $goodsCookie[$index]['version']["price_info"]['price'] = (float)$config->imagePay + (float)$config->instancePay;
                            $goodsCookie[$index]['price'] = (float)$config->imagePay + (float)$config->instancePay;
                            $price_total = (float)$config->imagePay + (float)$config->instancePay;
                            $goodsCookie[$index]['version']['goods_info'] =  $goodsDesc;
                            //凭借ecs计费信息
                            switch ($config->billCycle ) {
                                case '1':
                                    $i = 'D';
                                    break;
                                case '2':
                                    $i = 'M';
                                    break;
                                case '4':
                                    $i = 'Y';
                                    break;
                                default:
                                    $i = 'D';
                                    break;
                            }
                            $goodsCookie[$index]['version']["price_info"] = array('duration' => '1', 'interval_type' => $i, 'charge_mode' => 'cycle');
                            break;
                        case 'eip':
                            $config = json_decode($item['config']);
                            $config->price = isset($config->price) ? $config->price : 0;
                            $_eip_p = (float)$config->price * (float)$config->bandwidth;
                            $goodsCookie[$index]['version']["price_info"]['price'] = $_eip_p;
                            $goodsCookie[$index]['price'] = $_eip_p;
                            $price_total = $_eip_p;
                            $goodsCookie[$index]['version']['goods_info'] =  $goodsDesc;
                            //凭借ecs计费信息
                            switch ($config->priceId) {
                                case '1':
                                    $i = 'D';
                                    break;
                                case '2':
                                    $i = 'M';
                                    break;
                                case '3':
                                    $i = 'Y';
                                    break;
                                default:
                                    $i = 'D';
                                    break;
                            }
                            $goodsCookie[$index]['version']["price_info"] = array('duration' => '1', 'interval_type' => $i, 'charge_mode' => 'cycle');
                            break;
                        case 'vfw':
                        case 'waf':
                            $config = json_decode($item['config']);
                            $config->price = isset($config->price) ? $config->price : 0;
                            $goodsCookie[$index]['version']["price_info"]['price'] = (float)$config->price;
                            $goodsCookie[$index]['price'] = (float)$config->price;
                            $price_total = (float)$config->price;
                            $goodsCookie[$index]['version']['goods_info'] =  $goodsDesc;
                            //凭借ecs计费信息
                            switch ($config->priceId) {
                                case '1':
                                    $i = 'D';
                                    break;
                                case '2':
                                    $i = 'M';
                                    break;
                                case '3':
                                    $i = 'Y';
                                    break;
                                default:
                                    $i = 'D';
                                    break;
                            }
                            $goodsCookie[$index]['version']["price_info"] = array('duration' => '1', 'interval_type' => $i, 'charge_mode' => 'cycle');
                            break;
                        case 'vpc':
                        case 'elb':
                        case 'disks':
                            $config = json_decode($item['config']);
                            $config->price = isset($config->price) ? $config->price : 0;
                            $goodsCookie[$index]['version']["price_info"]['price'] = (float)$config->price;
                            $goodsCookie[$index]['price'] = (float)$config->price;
//                            $price_total = (float)$config->price;
                        $price_total = (isset($config->totalPrice)&&!empty($config->totalPrice))?(float)$config->totalPrice:(float)$config->price;
                            $goodsCookie[$index]['version']['goods_info'] =  $goodsDesc;
                            //凭借ecs计费信息
                            switch ($config->priceId) {
                                case '1':
                                    $i = 'D';
                                    break;
                                case '2':
                                    $i = 'M';
                                    break;
                                case '3':
                                    $i = 'Y';
                                    break;
                                default:
                                    $i = 'D';
                                    break;
                            }
                            $goodsCookie[$index]['version']["price_info"] = array('duration' => '1', 'interval_type' => $i, 'charge_mode' => 'cycle');
                            break;
                        default:
                            $goodsCookie[$index]['version']["price_info"] = Home::getPriceInfoById($item["price_id"]);
                            $goodsCookie[$index]['price'] = $goodsCookie[$index]['version']['price_info']['price'];//单价
                            $price_total = $goodsCookie[$index]['version']['price_info']['price'];
                            $goodsCookie[$index]['version']['goods_info'] =  $goodsDesc;
                            break;           
                    }
                } elseif ($goodsDesc['goods'][0]['fixed'] != 0){
                    $goodsCookie[$index]['version'] = json_decode($item['attr'], true);
                    $goodsCookie[$index]['version']['goods_info'] = (array)$goodsDesc;
                    $goodsCookie[$index]['version']['processid'] = $goodsDesc['goods'][0]['flow_id'];
                    $config = json_decode($item['attr']);
                    $goodsCookie[$index]['version']["price_info"] = array('duration' => '1', 'interval_type' => 'Y', 'charge_mode' => 'cycle');
                    if ($goodsDesc['goods'][0]['fixed'] == 1) {
                        //凭借ecs计费信息
                        switch ($config->billCycle ) {
                            case '1':
                                $i = 'D';
                                break;
                            case '2':
                                $i = 'M';
                                break;
                            case '4':
                                $i = 'Y';
                                break;
                            default:
                                $i = 'D';
                                break;
                        }
                        $goodsCookie[$index]['version']["price_info"] = array('duration' => '1', 'interval_type' => $i, 'charge_mode' => 'cycle');
                        $goodsCookie[$index]['price'] = (float)$config->imagePay + (float)$config->instancePay;
                        $goodsCookie[$index]['num'] = $goodsCookie[$index]['version']['number'];
                    } elseif ($goodsDesc['goods'][0]['fixed'] == 5) {
                        
                        $price_data = $goods_version_price_talbe->find()->where(['id' => $config->price_id])->first();
                        
                        $goodsCookie[$index]['version']["price_info"] = array(
                            'duration' => '1',
                            'interval_type' => isset($price_data['interval']) ? (string)$price_data['interval'] : 'I',
                            'charge_mode' => isset($price_data['charge_mode']) ? (string)$price_data['charge_mode'] : 'duration');
                        $goodsCookie[$index]['price'] = isset($price_data['price']) ? (string)$price_data['price'] : '0';
                        $goodsCookie[$index]['num'] = $goodsCookie[$index]['version']['number'];
                    } else {
                        if (isset($goodsCookie[$index]['version']['priceId']) && !empty($goodsCookie[$index]['version']['priceId'])) {
                            switch ($goodsCookie[$index]['version']['priceId']) {
                                case '1':
                                    $i = 'D';
                                    break;
                                case '2':
                                    $i = 'M';
                                    break;
                                case '3':
                                    $i = 'Y';
                                    break;
                                default:
                                    $i = 'D';
                                    break;
                            }
                            $goodsCookie[$index]['version']["price_info"] = array('duration' => '1', 'interval_type' => $i, 'charge_mode' => 'cycle');
                            $goodsCookie[$index]['price'] = isset($goodsCookie[$index]['version']['price']) ? (string)$goodsCookie[$index]['version']['price'] : '0';
                        }
                    }
                    $price_total = isset($goodsCookie[$index]['price']) ? (string)$goodsCookie[$index]['price'] : '0';
                } else {
                    $goodsCookie[$index]['version']['goods_info'] = $goodsDesc;
                    $goodsCookie[$index]['version']['processid'] = $goodsDesc['goods'][0]['flow_id'];
                }
                // debug($value['version']['processid']);die;
                // $goodsCookie[$index]['info']["cookie"] = $item; //放入cookid信息
            }

            $result_array = $goodsCookie;
            $orderTable      = TableRegistry::get('Orders');
            $orderGoodsTable = TableRegistry::get('OrdersGoods');

            //工作流相关
            //--开始--
            $_order_process_flow_table = TableRegistry::get('OrdersProcessFlow');
            $_workflow_detail_table    = TableRegistry::get('WorkflowDetail');
            //--结束--
            $account_table = TableRegistry::get('Accounts');
            $user          = $account_table->find()->select('department_id')->where(array('id' => $user_id))->first();
            
            foreach ($result_array as $key => $value) {

                //获取流程id
                $key = isset($value['version']['processid'])?$value['version']['processid']:0;
                // debug($key);die;
                //获取订单第一步骤信息
                $_first_step = $_workflow_detail_table->find()->where(['lft' => '1', 'flow_id' => $key])->first();
                //控制中心订单暂无流程 TODO加上流程
                if ($IS_CONSOLE) {
                    $_first_step = $_workflow_detail_table->find()->where(['step_code' => 'end'])->first();
                }
                // debug($key);die;
                //创建订单信息
                $ordes                      = $orderTable->newEntity();
                $ordes->number              = $this->build_order_no();
                $ordes->product_id          = 0;
                $ordes->goods_snapshot      = '';
                $ordes->facilitator_id      = 0;
                $ordes->instance_conf       = '';
                $ordes->duration            = 0;
                $ordes->duration_unit       = '月';
                $ordes->price_per           = 0;
                $ordes->num                 = 1;
                $ordes->benefit             = 0;
                $ordes->price_total         = $price_total;
                $ordes->tenant_id           = 0;
                $ordes->description         = '';
                $ordes->create_time         = time();
                $ordes->create_by           = 0;
                $ordes->modify_time         = time();
                $ordes->modify_by           = 0;
                $ordes->account_id          = (string) $this->request->session()->read('Auth.User.id');
                $ordes->department_id       = $user['department_id'];
                $ordes->flow_id             = $key;
                $ordes->detail_id           = $_first_step['id'];
                $ordes->transaction_price   = $price_total; //成交价
                //uid
                $ordes->status              = 1;
                $ordes->is_console          = $IS_CONSOLE;
                //是否展示
                $ordes->is_display          = $value['info']['goods'][0]['is_display'];
                $order_info                 = $orderTable->save($ordes);
                if (!empty($order_info)) {
                    //工作流相关
                    //--开始--
                    if ($ordes->is_console == 0) {
                        //系统生成生成流程 开始步骤
                        //flow_id
                        $_detail_info                          = $_workflow_detail_table->find()->select(['id', 'flow_id', 'step_name'])->where(['parent_id' => 0, 'flow_id' => $key])->first();
                        $_order_process_flow                   = $_order_process_flow_table->newEntity();
                        $_order_process_flow->order_id         = $ordes->id;
                        $_order_process_flow->create_time      = time();
                        $_order_process_flow->user_id          = $this->request->session()->read('Auth.User.id');
                        $_order_process_flow->user_name        = $this->request->session()->read('Auth.User.username');
                        $_order_process_flow->auth_action      = 0;
                        $_order_process_flow->auth_note        = $this->request->session()->read('Auth.User.username') . '提交订单';
                        $_order_process_flow->flow_detail_name = $_detail_info['step_name'];
                        $_order_process_flow->flow_detail_id   = $_detail_info['id'];
                        try {
                            $_order_process_flow_table->save($_order_process_flow);
                        } catch (Exception $e) {
                            //TODO 数据库操作失败
                        }
                    }

                    //发送邮件
                    $Workflow = new WorkflowController();
                    //$Workflow->email($order_info['id'], 1,$_first_step['id'], $_first_step['send_email']);

                    //--结束--
                    $id = $ordes->id;
                    // foreach ($value as $k => $item) {
                    $item = $value;
                    //获取商品单价
                    $price = empty($price_total) ? 0 : $price_total;

                        // debug($item['info']['goods'][0]);die();
                        $fixed     = $item['info']['goods'][0]['fixed'];
                        $parameter = $this->convertJsonByItemUserInterface($item);
                        $parameter['order_id'] = (string)$order_info['id'];
                        //计数
                        //mpass  bs 只能购买一个
                        if (isset($item['good_type']) && in_array($item['good_type'], array('mpaas', 'bs'))) {
                            $item["num"] = 1;
                        }

                        $number    = 1;
                        if (empty($item["num"])) {
                            $number = 1;
                        } else {
                            if ((int) $item["num"] >= 99) {
                                $number = 99;
                            } else {
                                $number = (int) $item["num"];
                            }
                        }
                        
                        //重新拼装商品信息
                        switch ($item['info']['goods'][0]['goodType']) {
                            case 'ecs':
                                $goods_info = $item['version']["goods_info"];
                                $p = $item['version']["price_info"];
                                unset($parameter['instance_price'], $parameter['image_price'], $parameter['unit']);
                                $item['version'] = $parameter;
                                $item['version']["goods_info"] = $goods_info;
                                $item['version']["price_info"] = $p;
                                break;
                            case 'eip':
                            case 'vfw':
                            case 'waf':
                            case 'vpc':
                            case 'elb':
                            case 'disks':
                                $goods_info = $item['version']["goods_info"];
                                $p = $item['version']["price_info"];
                                $item['version'] = $parameter;
                                $item['version']["goods_info"] = $goods_info;
                                $item['version']["price_info"] = $p;
                                break;
                                
                        }
                        
                        
                        $ordergoods                 = $orderGoodsTable->newEntity();
                        $ordergoods->order_id       = $id;
                        $ordergoods->good_id        = $item['info']['goods'][0]['id'];
                        $ordergoods->good_name      = $item['info']['goods'][0]['name'];
                        $ordergoods->good_sn        = '';
                        $ordergoods->num            = $number;
                        $ordergoods->benefit        = 0;
                        $ordergoods->price_per      = $price;
                        $ordergoods->price_total    = $price * $number;
                        $ordergoods->facilitator_id = 0;
                        $ordergoods->instance_conf  = json_encode($parameter);
                        $ordergoods->goods_snapshot = json_encode($item['version']);
                        $ordergoods->duration       = empty($item['info']['goods'][0]['time_duration']) ? 0 : $item['info']['goods'][0]['time_duration'];
                        $ordergoods->duration_unit  = empty($item['info']['goods'][0]['time_unit']) ? 0 : $item['info']['goods'][0]['time_unit'];
                        $ordergoods->description    = '';
                        $ordergoods->is_auto        = empty($item['info']['goods'][0]['is_auto']) ? 0 : $item['info']['goods'][0]['is_auto'];
                        //判断是哪里进入的
                        $ordergoods->is_console     = $fixed == 0 ? 0 : 1;
                        $ordergoods->transaction_price = $price;
                        $ordergoods->good_type  = $item['info']['goods'][0]['goodType'];
                        //判断是否展示
                        $ordergoods->is_display  = $item['info']['goods'][0]['is_display'];
                        //计费方式
                        if($item['info']['goods'][0]["goodType"]=="onvpc"){
                            $ordergoods->units  = "0";
                            $ordergoods->interval  = "0";
                            $ordergoods->charge_mode  = "0";
                        }else{
                            if (isset($item['version']['price_info'])) {
                                $ordergoods->units  = (string)$item['version']['price_info']['duration'];
                                $ordergoods->interval  = $item['version']['price_info']['interval_type'];
                                $ordergoods->charge_mode  = $item['version']['price_info']['charge_mode'];
                            }
                        }
                        $result                 = $orderGoodsTable->save($ordergoods);
                        $interface['url']       = '/console/';
                        // debug($ordergoods);die();
                        if ($ordergoods->is_auto != 0) {
                            $consoleController = new ConsoleController();
                            $parameter['dept_id'] = (string)$consoleController->getOwnByDepartmentId();
                            switch ($fixed) {
                                case 1://主机
                                    if(isset($parameter['bizTid'])){
                                        $interface['url']     = '/console/business/lists/hosts';
                                    }else{
                                        $interface['url']     = '/console/network/lists/hosts';
                                    }
                                    if ($ordergoods->is_console == 1) {
                                        $name = $parameter['ecsName'];
                                        
                                        $parameter['image_price'] = isset($parameter['imagePay']) ? $parameter['imagePay'] : 0; //镜像价格
                                        $parameter['instance_price'] = isset($parameter['instancePay']) ? $parameter['instancePay'] : 0; //规格价格
                                        $parameter['charge_mode'] = 'cycle';
                                        switch ($parameter['billCycle']) {
                                            case '1':
                                                $parameter['interval'] = 'D';
                                                break;
                                            case '2':
                                                $parameter['interval'] = 'M';
                                                break;
                                            case '4':
                                                $parameter['interval'] = "Y";
                                                break;
                                            default:
                                                $parameter['interval'] = 'D';
                                                break;
                                        }
                                        
                                        $parameter['price'] = isset($parameter['totalPay']) ? (string)$parameter['totalPay'] : '0'; //总价
                                        $parameter['real_price'] = $parameter['price']; //实际价格
                                        
                                        unset($parameter['imagePay'], $parameter['instancePay'], $parameter['totalPay']);
                                        
                                        unset($parameter['fixed']);
                                        unset($parameter['csName']);
                                        unset($parameter['dyName']);
                                        unset($parameter['dyCode']);
                                        unset($parameter['cpu']);
                                        unset($parameter['rom']);
                                        unset($parameter['netName']);
                                        unset($parameter['netCode']);
                                        unset($parameter['imageName']);
                                        unset($parameter['month']);
                                        unset($parameter['csCode']);
					                    $parameter['ioOptimized'] = 'true';
					                    $parameter['eipPrice'] = (string)$this->_getEipPrice($parameter);
                                        if ($number != 1) {
                                            for ($i = 0; $i < $number; $i++) {
                                                $parameter['ecsName'] = $name . '-' . ($i + 1);
                                                $parameter['method']  = 'ecs_add';
                                                $parameter['uid']     = $user_id;
                                                $parameter['token']     = $parameter['token'].$i;
                                                $interface            = $this->postInterface($url, $parameter);
                                                //调用接口
                                                if ($interface['Code'] != '0') {
                                                    echo json_encode($interface);
                                                    die;
                                                }
                                            }
                                        } else {
                                            $parameter['method'] = 'ecs_add';
                                            $parameter['uid']    = $user_id;

                                            $interface           = $this->postInterface($url, $parameter);
                                            //$interface['url']    = '/console/network/lists/hosts';
                                            //调用接口
                                            if ($interface['Code'] != '0') {
                                                echo json_encode($interface);
                                                die;
                                            }
                                        }
                                    } else {
                                    }
                                    if(isset($parameter['bizTid'])){
                                        $interface['url']     = '/console/business/lists/hosts';
                                    }else{
                                        $interface['url']     = '/console/network/lists/hosts';
                                    }
                                    break;
                                case 2://负载均衡
                                    if ($ordergoods->is_console == 1) {
                                        $parameter['method'] = 'lbs_add';
                                        $parameter['uid']    = $user_id;
                                        switch ($parameter['priceId']) {
                                            case '1':
                                                $parameter['interval'] = 'D';
                                                break;
                                            case '2':
                                                $parameter['interval'] = 'M';
                                                break;
                                            case '3':
                                                $parameter['interval'] = 'Y';
                                                break;
                                            default:
                                                $parameter['interval'] = 'D';
                                                break;
                                        }
                                        $parameter['duration'] = '1';
                                        $parameter['charge_mode'] = 'cycle';

                                        //查询子网类型
                                        $basic_table    = TableRegistry::get('InstanceBasic');
                                        $subnet = $basic_table->find()->contain(['SubnetExtend'])->where(array('InstanceBasic.code' => $parameter['subnetCode']))->first();
                                        $fusionType = $subnet["subnet_extend"]["fusionType"];
                                        if($fusionType=="vmware"){
                                            //检查VPX是否存在，不存在 ecsCode 参数为空
                                            // $basic_table    = TableRegistry::get('InstanceBasic');
                                            $vpx = $basic_table->find()->where(array('vpc'=>$parameter["vpcCode"],'type'=>'vpx'))->first();
                                            if(empty($vpx)){
                                                $parameter["ecsCode"]="";
                                            }else{
                                                $parameter["ecsCode"]=$vpx["code"];
                                            }
                                        } else if($fusionType=="aws"||$fusionType=="aliyun"){
                                            //'aws','aliyun'

                                        } else if($fusionType=="openstack"){
                                            $interface['Message'] = '所选子网无法创建负载均衡';
                                            $interface['Code'] = '004';
                                            echo json_encode($interface);
                                            die;
                                        }
                                        $interface           = $this->postInterface($url, $parameter);
                                        $interface['url']    = '/console/network/lists/elb';
                                        //调用接口
                                        if ($interface['Code'] != '0') {
                                            echo json_encode($interface);
                                            die;
                                        }
                                    }
                                    $interface['url'] = '/console/network/lists/elb';
                                    break;
                                case 3://硬盘
//                                     if ($ordergoods->is_console == 1) {
//                                         debug($parameter);die();
// // $Agent=TableRegistry::get('Agent');
// // $where = array('class_code' => $data['class_code']);
// // $data['regionCode']=$Agent->find()->select(['region_code'])->where($where)->toArray()[0]['region_code'];
// // $interface = $this->createDisks($data);
// // //查询计费周期
// // $charge = TableRegistry::get('InstanceCharge');
// // $charge = $charge->find()->select(['charge_type'])->where(array('basic_id'=>$data["id"]))->first();
// // $interface["billCycle"]=$charge->charge_type;
// // echo json_encode($interface);die();
//                                         $parameter['method'] = 'volume_add';
//                                         $parameter['uid'] = $user_id;
//                                         $interface = $this->postInterface($url, $parameter);
//                                         //调用接口
//                                         if ($interface['Code'] != '0') {
//                                             echo json_encode($interface);die();
//                                         }
//                                     }
                                    break;
                                case 4://路由器
                                    if ($ordergoods->is_console == 1) {
                                        $parameter['method'] = 'router_add';
                                        $parameter['uid']    = $user_id;
                                        switch ($parameter['priceId']) {
                                            case '1':
                                                $parameter['interval'] = 'D';
                                                break;
                                            case '2':
                                                $parameter['interval'] = 'M';
                                                break;
                                            case '3':
                                                $parameter['interval'] = 'Y';
                                                break;
                                            default:
                                                $parameter['interval'] = 'D';
                                                break;
                                        }
                                        $parameter['duration'] = '1';
                                        $parameter['charge_mode'] = 'cycle';
                                        if ($parameter['csCode'] == "aliyun") {
                                            $department_table = TableRegistry::get("Departments");
                                            
                                            $department_data = $department_table
                                            ->find()
                                            ->select(['aliyun_account'])
                                            ->where(['id' => $parameter['dept_id']])->first();
                                            if (isset($department_data['aliyun_account']) && !empty($department_data['aliyun_account'])) {
                                                $parameter['accountsCode'] = $department_data['aliyun_account'];
                                            }
                                        }
                                        unset($parameter['csName']);
                                        unset($parameter['csCode']);
                                        unset($parameter['dyName']);
                                        unset($parameter['dyCode']);
                                        unset($parameter['month']);
                                        unset($parameter['storeName']);
                                        unset($parameter['billCycleName']);
                                        unset($parameter['unit']);
                                        unset($parameter['fixed']);
                                        $interface           = $this->postInterface($url, $parameter);
                                        $interface['url']    = '/console/network/lists/router';
                                        //调用接口
                                        if ($interface['Code'] != '0') {
                                            echo json_encode($interface);
                                            die;
                                        }
                                    }
                                    $interface['url'] = '/console/network/lists/router';
                                    break;
                                case 5://云桌面
                                    $parameter['method'] = 'desktop_add';
                                    $parameter['uid']    = $user_id;
                                    $num                 = $parameter['number'];
                                    $name                = $parameter['name'];
                                    unset($parameter['subnetId']);
                                    unset($parameter['name']);
                                    unset($parameter['num']);
                                    unset($parameter['number']);
                                    unset($parameter['name']);
                                    if ($parameter['ad'] == 1) {
                                    } elseif ($parameter['ad'] == 2) {
                                        //选择AD新建
                                        $parameter['aduser'] = $parameter['txtaduser'];
                                        unset($parameter['adpass']);
                                    } else {
                                        //不创建
                                        unset($parameter['aduser']);
                                        unset($parameter['adpass']);
                                    }
                                    unset($parameter['txtaduser'], $parameter['vpcName']);
                                    unset($parameter['ad']);
                                    unset($parameter['fixed']);
                                    unset($parameter['csName']);
                                    unset($parameter['dyName']);
                                    unset($parameter['dyCode']);
                                    unset($parameter['cpu']);
                                    unset($parameter['rom']);
                                    unset($parameter['netName']);
                                    unset($parameter['netCode']);
                                    unset($parameter['imageName']);
                                    unset($parameter['month']);
                                    unset($parameter['csCode']);
                                    unset($parameter["spec_name"]);
                                    unset($parameter["image_name"]);
                                    
                                    if (empty($parameter["subnetCode2"])) {
                                        unset($parameter["subnetCode2"]);
                                    }
                                    
                                    $price_data = $goods_version_price_talbe->find()->where(['id' => $parameter['price_id']])->first();
                                    
                                    $parameter['charge_mode'] = isset($price_data['charge_mode']) ? (string)$price_data['charge_mode'] : 'duration'; //方式
                                    $parameter['interval'] = isset($price_data['interval']) ? (string)$price_data['interval'] : 'I'; //周期
                                    $parameter['price'] = isset($price_data['price']) ? (string)$price_data['price'] : '0'; //总价
                                    $parameter['real_price'] = $parameter['price']; //实际价格
                                    if ($ordergoods->is_console == 1) {
                                        if ($num > 1) {
                                            for ($i = 1; $i <= $num; $i++) {
                                                // $namenumber = $nameNum1 + $i;
                                                $parameter['desktopName'] = $name . '-' . $i;
                                                $parameter['token']     = $parameter['token'].$i;
                                                $interface                = $this->postInterface($url, $parameter);
                                                $interface['url']         = '/console/desktop/lists';
                                                //调用接口
                                                if ($interface['Code'] != '0') {
                                                    echo json_encode($interface);
                                                    die;
                                                }
                                            }
                                        } else {
                                            // $parameter["method"]="desktop_ad_add";
                                            $parameter['desktopName'] = $name;
                                            $interface                = $this->postInterface($url, $parameter);
                                            $interface['url']         = '/console/desktop/lists';
                                            //调用接口
                                            if ($interface['Code'] != '0') {
                                                echo json_encode($interface);
                                                die;
                                            }
                                        }
                                    }
                                    $interface['url'] = '/console/desktop/lists';
                                    break;
                                case 6://子网
                                    if ($ordergoods->is_console == 1 || $ordergoods->is_console == 2) {
                                        $parameter['billCycle'] = '1';//计费参数
                                        $parameter['method'] = 'subnet_add';
                                        $parameter['uid']    = $user_id;
                                        $interface           = $this->postInterface($url, $parameter);
                                        if ($ordergoods->is_console == 1) {
                                            $interface['url'] = '/console/network/lists/subnet';
                                        }
                                        //调用接口
                                        if ($interface['Code'] != '0') {
                                            echo json_encode($interface);
                                            die;
                                        }
                                    }
                                    $interface['url'] = '/console/network/lists/subnet';
                                    break;
                                case 7://公网IP
                                    if ($ordergoods->is_console == 1) {
                                        $parameter['method'] = 'eip_add';
                                        $parameter['uid']    = $user_id;
                                        switch ($parameter['priceId']) {
                                            case '1':
                                                $parameter['interval'] = 'D';
                                                break;
                                            case '2':
                                                $parameter['interval'] = 'M';
                                                break;
                                            case '3':
                                                $parameter['interval'] = 'Y';
                                                break;
                                            default:
                                                $parameter['interval'] = 'D';
                                                break;
                                        }
                                        $parameter['duration'] = '1';
                                        $parameter['charge_mode'] = 'cycle';
                                        unset($parameter['csName']);
                                        unset($parameter['csCode']);
                                        unset($parameter['dyName']);
                                        unset($parameter['dyCode']);
                                        unset($parameter['number']);
                                        unset($parameter['fixed']);
                                        unset($parameter['billCycleName']);
                                        unset($parameter['unit']);
                                        unset($parameter['vpcName']);
                                        $parameter['price'] = $parameter['price'] * $parameter['bandwidth'];
                                        $parameter['price'] = (string)$parameter['price'];
                                        $parameter['real_price'] = $parameter['price'];
                                        $interface           = $this->postInterface($url, $parameter);
                                        //调用接口
                                        if ($interface['Code'] != '0') {
                                            echo json_encode($interface);
                                            die;
                                        }
                                    }
                                    $interface['url'] = '/console/network/lists/eip';
                                    break;
                                case 8://存储
                                    if ($ordergoods->is_console == 1) {
                                        $parameter['method'] = 'store_add';
                                        $parameter['uid']    = $user_id;
                                        switch ($parameter['priceId']) {
                                            case '1':
                                                $parameter['interval'] = 'D';
                                                break;
                                            case '2':
                                                $parameter['interval'] = 'M';
                                                break;
                                            case '3':
                                                $parameter['interval'] = 'Y';
                                                break;
                                            default:
                                                $parameter['interval'] = 'D';
                                                break;
                                        }
                                        $parameter['duration'] = '1';
                                        $parameter['charge_mode'] = 'cycle';
                                        unset($parameter['csName']);
                                        unset($parameter['csCode']);
                                        unset($parameter['dyName']);
                                        unset($parameter['dyCode']);
                                        unset($parameter['number']);
                                        unset($parameter['fixed']);
                                        unset($parameter['billCycleName']);
                                        unset($parameter['unit']);
                                        $interface = $this->postInterface($url, $parameter);
                                        //调用接口
                                        if ($interface['Code'] != '0') {
                                            echo json_encode($interface);
                                            die;
                                        }
                                    }
                                    $interface['url'] = '/console/fics/lists/fics';
                                    break;
                                case 9://云桌面套件
                                    if ($ordergoods->is_console == 1) {
                                        $parameter['method'] = 'desktop_init';
                                        $parameter['uid']    = $user_id;
                                        unset($parameter["fixed"]);
                                        $interface        = $this->postInterface($url, $parameter);
                                        $interface['url'] = '/console/desktop/lists/init';
                                        //调用接口
                                        if ($interface['Code'] != '0') {
                                            echo json_encode($interface);
                                            die;
                                        }
                                    }
                                    break;
                                case 10://防火墙
                                    if ($ordergoods->is_console == 1) {
                                        $parameter['method'] = 'firewall_add';
                                        $parameter['uid']    = $user_id;
                                        // switch ($parameter['priceId']) {
                                        //     case '1':
                                        //         $parameter['interval'] = 'D';
                                        //         break;
                                        //     case '2':
                                        //         $parameter['interval'] = 'M';
                                        //         break;
                                        //     case '3':
                                        //         $parameter['interval'] = 'Y';
                                        //         break;
                                        //     default:
                                        //         $parameter['interval'] = 'D';
                                        //         break;
                                        // }
                                        // $parameter['duration'] = '1';
                                        // $parameter['charge_mode'] = 'cycle';
                                        unset($parameter["fixed"]);
                                        $interface        = $this->postInterface($url, $parameter);
                                        $interface['url'] = '/console/security/lists/firewall';
                                        //调用接口
                                        if ($interface['Code'] != '0') {
                                            echo json_encode($interface);
                                            die;
                                        }
                                    }
                                    break;
                                case 11:// 安全组
                                    if ($ordergoods->is_console == 1) {
                                        $parameter['method'] = 'security_group_add';
                                        $parameter['uid']    = $user_id;
                                        unset($parameter["agentCode"]);
                                        unset($parameter["fixed"]);
                                        $interface        = $this->postInterface($url, $parameter);
                                        $interface['url'] = '/console/security-group';
                                        //调用接口
                                        if ($interface['Code'] != '0') {
                                            echo json_encode($interface);
                                            die;
                                        }
                                    }
                                    break;
                                case 12://边界路由器
                                    if ($ordergoods->is_console == 1) {
                                        $physicalLineCodeArr = Configure::read('physicalLineCode');
                                        unset($parameter["fixed"]);
                                        $parameter['physicalLineCode'] = $physicalLineCodeArr[0];
                                        $parameter['physicalLineCodeBak'] = $physicalLineCodeArr[1];
                                        $parameter['method'] = 'vbr_add';
                                        $parameter['uid']    = $user_id;
                                        $interface        = $this->postInterface($url, $parameter);

                                        $interface['url'] = '/console/boundary-router-list/vbr';
                                        //调用接口
                                        if ($interface['Code'] != '0') {
                                            echo json_encode($interface);
                                            die;
                                        }
                                    }
                                    break;
                                case 13://边界路由器接口
                                    if ($ordergoods->is_console == 1) {
                                        unset($parameter["fixed"]);
                                        $parameter['method'] = 'vbr_add_interface';
                                        $parameter["oppositeRouterCode"] = $this->_getRouterCodeByVpc($parameter['oppositeVpcCode']);
                                        $parameter['uid']    = $user_id;
                                        $interface        = $this->postInterface($url, $parameter);

                                        $interface['url'] = "/console/boundary-router-list/vbr-ports?vbr_id=".$parameter['basicId'];
                                        //调用接口
                                        if ($interface['Code'] != '0') {
                                            echo json_encode($interface);
                                            die;
                                        }
                                    }
                                    break;
                                default:
                                    $parameter['uid']     = $user_id;
                                    $parameter['orderId'] = (string) $id;
                                    $interface            = $this->postInterface($url, $parameter);
                                    $interface['url']     = '/console/';
                                    //调用接口
                                    if ($interface['Code'] != '0') {
                                        echo json_encode($interface);
                                        die;
                                    }
                                    break;
                            }
                        }
//                     }
                } else {
                    $interface['Msg'] = '订单创建失败';
                    echo json_encode($interface);
                    die;
                }
            }
            $interface['Code'] = '0';
            
            //存.
            $ser_goods = serialize($oldCookie);
            $this->Cookie->write('user.car', base64_encode($ser_goods));
            
            echo json_encode($interface);
            $this->lauot = 'ajax';
        }
    }

    /**
     * 根据vpc获取routerCode
     * @param $vpcCode
     * @return mixed
     */
    protected  function  _getRouterCodeByVpc($vpcCode)
    {
        $instanceBasic = TableRegistry::get("InstanceBasic");
        $router = $instanceBasic->find()->where(['vpc'=>$vpcCode,'type'=>'router','status'=>'运行中'])->first();
        if($router){
            return $router['code'];
        }
    }

    /**
     * 获取EIP价格
     * @param $request
     */
    protected  function  _getEipPrice($request)
    {
        if (!isset($request['bandwidth']) || empty($request['bandwidth'])) {
            $request['bandwidth'] = '1';
        }
        $agentTable = TableRegistry::get('agent');
        $chargeExtend = TableRegistry::get('chargeExtend');
        $agentEntity = $agentTable->find()->where(['region_code'=>$request['regionCode']])->first();
        if($agentEntity){
            $agentRootEntity = $agentTable->getAgentRoot($agentEntity->parentid);
            $charge = $chargeExtend->find()->where(['agent_id'=>$agentRootEntity->id,'charge_object'=>'eip'])->first();
            if($charge){
                switch ($request['interval']){
                    case 'D':
                        $p = $charge->daily_price;
                        break;
                    case 'M':
                        $p = $charge->monthly_price;
                        break;
                    case 'Y':
                        $p = $charge->yearly_price;
                        break;
                }
                return (string)($p * $request['bandwidth']);
            }
        }
        return 0;
    }

    /**
     * @func:操作按钮通用方法
     * @param:
     * @date: 2015年10月10日 下午6:06:40
     * @author: shrimp liao
     * @return: null
     */
    public function ajaxFun($array)
    {
        $parameter = array();
        $url       = Configure::read('URL');
        //$type= $this->request->data['type'];//
        $http          = new Client();
        $obj_response  = $http->post($url, json_encode($array), array('type' => 'json'));
        $data_response = json_decode($obj_response->body, true);
        return $data_response;
    }
    /**
     * @func:调用接口
     * @param:@url 接口地址
     *        @$array 接口参数
     * @date: 2015年10月10日 下午3:08:59
     * @author: shrimp liao
     * @return: null
     */
    public function postInterface($url, $array)
    {
        set_time_limit(0);
        //0为无限制
        $http          = new Client();
        $obj_response  = $http->post($url, json_encode($array), array('type' => 'json'));
        $data_response = json_decode($obj_response->body, true);
        return $data_response;
    }
    /**
     * @func: cookie字符串参数转json
     * @param:
     * @date: 2015年9月24日 下午3:00:12
     * @author: shrimp liao
     * @return: null
     */
    public function createEcsCookieObjectArray($str, $fixed)
    {
        $returnArray          = array();
        $returnArray          = $str;
        $returnArray['fixed'] = $fixed;
        return $returnArray;
    }
    /**
     * @func: 将购物车中的cookie对象转换问接口所需要的参数
     * @param:
     * @date: 2015年10月10日 下午1:01:43
     * @author: shrimp liao
     * @return: null
     */
    public function convertJsonByItemUserInterface($item)
    {
        $jsonArray = array();
        if (!empty($item['attr'])) {
            $fixed     = $item['info']['goods'][0]['fixed'];
            $jsonArray = $this->objectToArray(json_decode($item['attr']));
            $array     = json_decode($item['attr']);
            //如果attr不为空，那么解析attr中的属性信息，转换成interface需要的数据格式 json

            switch ($fixed) {
                case 1:
                    // 云主机
                    if (!isset($jsonArray['vpcCode']) || $jsonArray['vpcCode'] == '') {
                        $id                   = $this->findBasicByCode($jsonArray['netCode'])['id'];
                        $jsonArray['vpcCode'] = $this->findVpcCode('subnet', $id, 'vpc');
                    }
                    break;
                case 2:
                    if (empty($jsonArray['eipCode'])) {
                        unset($jsonArray['eipCode']);
                    }
                    unset($jsonArray['fixed']);
                    break;
                case 3:
                    //
                    //                    $jsonArray = $this->objectToArray($array);
                    //                    debug($jsonArray);die();
                    //                    if ($jsonArray['vpcCode'] == '') {
                    //                        $jsonArray['vpcCode'] = $this->findVpcCode('disks', $id, 'vpc');
                    //                    }
                    //                    unset($jsonArray['fixed']);
                    //                    unset($jsonArray['area']);
                    break;
                case 4:
                    $jsonArray = $this->objectToArray($array);
                    
                    break;
                case 5:
                    $jsonArray = $this->objectToArray($array);
                    if ($jsonArray['vpcCode'] == '') {
                        $jsonArray['vpcCode'] = $this->findVpcCode('subnet', $jsonArray['subnetId'], 'vpc');
                    }
                    break;
                case 6:
                    $jsonArray      = $this->objectToArray($array);
                    $instance_basic = TableRegistry::get('InstanceBasic');
                    if ($jsonArray['vpcCode'] == '') {
                        $jsonArray['vpcCode'] = $this->findVpcCode('router', $jsonArray['routerid'], 'vpc');
                    }
                    $router                  = $instance_basic->find()->select(array('code'))->where(array('id' => $jsonArray['routerid']))->toArray();
                    $jsonArray['routerCode'] = $router[0]['code'];
                    unset($jsonArray['routerid']);
                    unset($jsonArray['area']);
                    unset($jsonArray['routerName']);
                    unset($jsonArray['fixed']);
                    break;
                case 7:
                    $jsonArray = $this->objectToArray($array);
                    unset($jsonArray['fixed']);
                    break;
                case 8:
                    unset($jsonArray['number']);
                    unset($jsonArray['fixed']);
                    break;
                default:
                    break;
            }
        } else {
            if(isset($item["good_type"]) && !empty($item["good_type"])) {
            if ($item["good_type"] == 'ecs') {
                $jsonArray = json_decode($item['config']);
                $jsonArray = (array)$jsonArray;
                $jsonArray["num"] = $item["num"];
                $jsonArray["number"] = $item["num"];
                $jsonArray["method"] = "ecs_add";
                $jsonArray["uid"] = (string) $this->request->session()->read('Auth.User.id');
            } elseif($item["good_type"] == 'eip') {
                $jsonArray = json_decode($item['config'], true);
                $jsonArray["num"] = $item["num"];
                $jsonArray["number"] = $item["num"];
                $jsonArray["method"] = "eip_add";
                $jsonArray["uid"] = (string) $this->request->session()->read('Auth.User.id');
            } elseif($item["good_type"] == 'vfw' || $item["good_type"] == 'waf') {
                $jsonArray = json_decode($item['config'], true);
                $jsonArray["num"] = $item["num"];
                $jsonArray["number"] = $item["num"];
                $jsonArray["method"] = "eip_add";
                $jsonArray["uid"] = (string) $this->request->session()->read('Auth.User.id');
            } elseif($item["good_type"] == 'waf') {
                $jsonArray = json_decode($item['config'], true);
                $jsonArray["num"] = $item["num"];
                $jsonArray["number"] = $item["num"];
                $jsonArray["method"] = "eip_add";
                $jsonArray["uid"] = (string) $this->request->session()->read('Auth.User.id');
            } elseif($item["good_type"] == 'vpc') {
                $jsonArray = json_decode($item['config'], true);
                $jsonArray["num"] = $item["num"];
                $jsonArray["number"] = $item["num"];
                $jsonArray["method"] = "router_add";
                $jsonArray["uid"] = (string) $this->request->session()->read('Auth.User.id');
            } elseif($item["good_type"] == 'elb') {
                $jsonArray = json_decode($item['config'], true);
                $jsonArray["num"] = $item["num"];
                $jsonArray["number"] = $item["num"];
                $jsonArray["method"] = "lbs_add";
                $jsonArray["uid"] = (string) $this->request->session()->read('Auth.User.id');
            } elseif($item["good_type"] == 'disks') {
                $jsonArray = json_decode($item['config'], true);
                $jsonArray["num"] = $item["num"];
                $jsonArray["number"] = $item["num"];
                $jsonArray["method"] = "volume_add";
                $jsonArray["uid"] = (string) $this->request->session()->read('Auth.User.id');
            } else {
                $jsonArray["num"] = $item["num"];
                if (!empty($item['info']['goods'][0]['version_info'])) {
                    $version_info = $item['info']['goods'][0]['version_info'];
                    foreach ($version_info as $vi) {
                        if ($vi["id"] == $item["version_id"]) {
                            foreach ($vi["info"] as $info) {
                                $jsonArray[$info['key']] = $info['value'];
                            }
                            $jsonArray["version_name"] = $vi["name"];
                        }
                    }
                    if (!empty($item['version']['spec'])) {
                        $jsonArray["imageCode"] = $item['version']['spec']['image']['code'];
                        $jsonArray["instanceTypeCode"] = $item['version']['spec']['instancetype']['code'];
                    }
                    if ($item["good_type"] == "citrix_public") {
                        $config = json_decode($item["config"],true);
                        
                        foreach ($config as $k => $v) {
                            $jsonArray[$k] = $v;
                        }
                    }
//                     debug($item["version"]);die;
                    //组装价格
                    $jsonArray['price'] =  $item["version"]['price_info']['price'];
                    $jsonArray['charge_mode'] =  $item["version"]['price_info']['charge_mode'];
                    $jsonArray['interval'] =  $item["version"]['price_info']['interval'];
                    $jsonArray['interval_type'] =  $item["version"]['price_info']['interval_type'];
                    $jsonArray['price_name'] =  $item["version"]['price_info']['name'];
                    // debug($item["version"]['price_info']);die;
                    $jsonArray["good_type"] = $item["good_type"];
                    // $jsonArray["versionId"] = $item["version"];
                    $jsonArray["bscharge"] = isset($item["bscharge"]) ? $item["bscharge"] : 0;
                    switch ($item["good_type"]) {
                        case 'citrix_public':
                            $jsonArray["method"] = "desktop_add";
                            $jsonArray["adpass"] = "";
                            $jsonArray["aduser"] = "";
                            $jsonArray["regionCode"] = $jsonArray["region"];
                            $jsonArray["desktopName"] = $jsonArray['ecsName'].time().rand(1000,9999);
                            $jsonArray["uid"] = (string) $this->request->session()->read('Auth.User.id');
                            break;
                        case 'citrix':
                            $jsonArray["method"] = "desktop_add";
                            $jsonArray["adpass"] = "";
                            $jsonArray["aduser"] = "";
                            $jsonArray["desktopName"] = time().rand(1000,9999);

                            $jsonArray["subnetCode"] = $jsonArray["subnet"];
                            $jsonArray["regionCode"] = $jsonArray["region"];
                            $jsonArray["subnetCode2"] = '';
                            $jsonArray["vpcCode"] = $jsonArray["vpc"];
                            unset($jsonArray["subnet"], $jsonArray["region"], $jsonArray["vpc"], $jsonArray['bscharge'], $jsonArray['spec']);
                            $jsonArray["uid"] = (string) $this->request->session()->read('Auth.User.id');
                            break;
                        case 'bs':
                            break;
                        default:
                            # code...
                            break;
                    }
                }
            }
        }
        }
        return $jsonArray;
    }
    /**
     * 创建订单显示详情
     * @fun    name
     * @date   2015-11-09T19:53:32+0800
     * @author shrimp liao
     * @param  [type]                   $item [description]
     * @return [type]                         [description]
     */
    public function jsonDisplay($item)
    {
        $jsonArray = array();
        if (!empty($item['attr'])) {
            $jsonArray = json_decode($item['attr']);
        } else {
            $jsonArray = Home::getGoodsInfoDes($item['goods_id']);
        }
        return $jsonArray;
    }
    //PHP stdClass Object转array
    /**
     * @func: 对象转数组
     * @param:
     * @date: 2015年10月19日 下午4:11:37
     * @author: shrimp liao
     * @return: null
     */
    public function objectToArray($e)
    {
        $e = (array) $e;
        foreach ($e as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $e[$k] = (array) $this->objectToArray($v);
            }
        }
        return $e;
    }
    /**
     * @func: 获取vpcCode
     * @param:$fromtype:查询类型
     * $param:$fromid:basic_id
     * $param:$totype:获取类型
     * @date: 2015年11月3日 下午4:16:50
     * @author: zhaodanru
     * @return: null
     */
    public function findVpcCode($fromtype, $fromid, $totype)
    {
        $instance_basic    = TableRegistry::get('InstanceBasic');
        $instance_relation = TableRegistry::get('InstanceRelation');
        $vpc               = $instance_relation->find()->select(array('toid'))->where(array('fromid' => $fromid, 'fromtype' => $fromtype, 'totype' => $totype))->toArray();
        $vpcCode           = $instance_basic->find()->select(array('code'))->where(array('id' => $vpc[0]['toid']))->toArray();
        if ($vpcCode) {
            return $vpcCode[0]['code'];
        } else {
            return '';
        }
    }
    /**
     * @func:生成新的订单号
     * @param:
     * @date: 2015年10月10日 下午3:45:07
     * @author: shrimp liao
     * @return: (string)订单编号
     */
    public function build_order_no()
    {
        /* 选择一个随机的方案 */
        mt_srand((double) microtime() * 1000000);
        return 'CP' . date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }
    /**
     * @func:根据code查找基础信息
     * @param:
     * @date: 2015-11-4 16:11:29
     * @author: shrimp liao
     * @return: (string)code
     */
    public function findBasicByCode($code)
    {
        $instance_basic = TableRegistry::get('InstanceBasic');
        $basic          = $instance_basic->find()->where(array('code' => $code))->toArray();
        return $basic[0];
    }

    public function createJsonByBuy($item)
    {
        $isCustom = 0;
        $version  = 0;
        //判断是否是自定义配置
        if (!empty($item) && $item != "0") {

        } else {
            //不是自定义配置，判断是否是
        }
    }

    //修改商品属性
    public function changeCookie($i)
    {
        // debug($i);
        // debug($this->request->data);die;

        $userCookie = "user.car";
        //重新整理购物车
        $goodsCookie = parent::readCookie($userCookie);
        $goodsCookie[$i]['config'] = json_encode($this->request->data);
        // debug($goodsCookie[$i]);die;
        $ser_goods = serialize($goodsCookie);
        $this->Cookie->write('user.car', base64_encode($ser_goods));

        $this->redirect('/orders/buy');

    }
}
