<?php
/**
 * 主页，主要为页面展示控制器
 *
 *
 * @author XingShanghe<xingshanghe@gmail.com>
 * @date  2015年9月6日下午5:30:30
 * @source HomeController.php
 * @version 1.0.0
 * @copyright  Copyright 2015 sobey.com
 */
namespace App\Controller;

use App\Controller\Admin\GoodsVpcController;
use App\Controller\SobeyController;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Cake\View\Exception\MissingTemplateException;
use Cake\Cache\Cache;

class HomeController extends SobeyController
{

    public $paginate = [
        'limit' => 20,
    ];

    protected static $_new_goods = ["citrix", "bs", "mpaas", 'ecs', 'citrix_public', 'eip', 'vfw', 'waf', 'vpc', 'elb', 'disks'];    
    protected $_need_config_goods = ['ecs', 'citrix_public', 'eip', 'vfw', 'waf', 'vpc', 'elb', 'disks']; // 需要配置的商品

    /**
     * 展示普通页面
     * @param string $subject 主题
     * @param string $category 分类
     * @param number $tab 标签
     * @throws MissingTemplateException
     * @throws NotFoundException
     * @return Ambigous <void, \Cake\Network\Response>
     */
    public function display($subject = 'default', $category = 'index', $tab = 0)
    {
        if (!$subject) {
            return $this->redirect('/');
        }
        $this->set(compact('subject', 'category', 'tab'));
        $this->autoRender = false;
        try {
            $func_name = '_get_vars_' . implode('_', array($subject, $category));
            //判断是否存在函数
            if (method_exists($this, $func_name)) {
                $this->set('_view_vars', call_user_func_array(array($this, $func_name), array($tab)));
            }
            $this->set('good_category', $this->getCategoeyGoodsData());
            $this->render($subject . '/' . $category);
        } catch (MissingTemplateException $e) {
            if (Configure::read('debug')) {
                throw $e;
            }
            throw new NotFoundException();
        }
    }
    /**
     * 主页获取 页面参数变量
     * @return multitype:multitype:
     */
    protected function _get_vars_default_index()
    {
        $this->set('_number', parent::readCookieByNumber()); //读取cookie
       
        return array('hot' => $this->getCanDisplayGoodsList());
    }

    /**
     * @author wangjincheng
     * 获取课展示的桌面
     */
    private function _getCanDisplayCateInfo($goodsCategory,$type='goods'){
        $department_id  = $this->request->session()->read('Auth.User.department_id');//租户id
        
        $GoodsAttributeTable = TableRegistry::get('GoodsAttributeDetail');
        $goods_version_table = TableRegistry::get('GoodsVersion');
        $goods_version_detail_table = TableRegistry::get('GoodsVersionDetail');
        
        //获取版本id
        $version_data = $goods_version_table->find('list', [
            'keyField' => 'id',
            'valueField' => 'gid',
            'groupField' => 'gid'
        ])->where(['gid is not' => null])->toArray();
        
        
        //获取版本 vid=>租户id
        $detail_data = $goods_version_detail_table->find('list', [
            'keyField' => 'vid',
            'valueField' => 'value'
        ])
            ->where(['key' => 'tenantid'])
            ->toArray();
        
        //需要判断租户的商品类型
        $need_check_type = array("citrix", "mapaas", "bs");
        
        $c = array();
        foreach ($goodsCategory as $key => $cate) {
            $g = array();
            foreach ($cate[$type] as $key => $good) {
                

                
                //获取能够展示的商品
                $arr = array();
                
                //判断一键vpc
                if (empty($good['goods_vpc'])) {
                    $i = isset($version_data[$good['id']]) ? count($version_data[$good['id']]) : 0; //获取商品对应版本数
                    $n = isset($good['goodStatus']) ? $good['goodStatus'] : 0; //是否
                    if ($i > 0 && $n == 1) {
                        //检查商品版本是否售卖给该租户
                        foreach ($version_data[$good['id']] as $k => $v) {
                            if (isset($detail_data[$k])) {
                                $can_buy_depart_id_array = split(',', $detail_data[$k]);
                                if (in_array($department_id, $can_buy_depart_id_array) || in_array(0, $can_buy_depart_id_array)) {
                                    $g[]=$good;
                                }
                            } else {
                                $g[]=$good;
                            }
                        }
                    }
                } else {//一键vpc
                    $g[]=$good;
                }
            }
            
            $cate[$type] = $g;
            if(!empty($cate[$type])){
                $c[] = $cate;
            }
        }
        return $c;
    }

    /**
     * @func:获取当前商品分类
     * @param:
     * @date: 2015年9月14日 上午11:42:22
     * @author: shrimp liao
     * @return: null
     */
    public function menuCategory()
    {
        $goodsCategoryTable = TableRegistry::get('GoodsCategory');
        $menuCategory       = $goodsCategoryTable->find('all')->contain('Goods')->order(array('sort_order' => 'asc'))->toArray();
        return $menuCategory;
    }
    /**
     * @func: 初始化菜单信息
     * @param:
     * @date: 2015年9月14日 下午3:51:39
     * @author: shrimp liao
     * @return: null
     */
    //     public static function initProducts()
    //     {
    //         $products=Configure::read('products');
    //         return $products;
    //     }
    /**
     * @func: 获取ecs商品信息
     * @param:
     * @date: 2015年9月11日 下午2:47:20
     * @author: shrimp liao
     * @return: null
     */
    protected function _get_vars_products_ecs()
    {
        $this->set('_menuCategory', $this->menuCategory());
        $_products = self::initProducts();
        $goodsId   = $_products['ecs'];
        //获取配置文件中ecs的id
        $goodsInfoDesc = self::getGoodsInfoDes($goodsId);
        //构造商品信息
        $this->set('goodsUrl', 'ecs');
        $this->set('_products', $_products);
        return array('goodsInfo' => $goodsInfoDesc);
    }
    /**
     * @func:通用的商品详细页面
     * @param:商品id
     * @date: 2015年10月9日 下午2:10:21
     * @author: shrimp liao
     * @return: null
     */
    public function products($goodsId)
    {
        $goodsInfoDesc = self::getGoodsInfoDes($goodsId);
        //构造商品信息
        if ($this->request->session()->check('Auth.User.id')) {
            $this->set('user', 'true');
        } else {
            $this->set('user', 'false');
        }
        $this->set('good_category', $this->getCategoeyGoodsData()); // 加载菜单
        $this->set('_number', parent::readCookieByNumber());

        //商品和分类信息
        $goods=TableRegistry::get('Goods');
        $category=TableRegistry::get('GoodsCategory');
        $goodinfo=$goods->find()->where(array('id'=>$goodsId))->first();
        $this->set('this_good_info',$goodinfo);
        $categoryinfo=$category->find()->where(array('id'=>$goodinfo['category_id']))->first();
        $this->set('this_good_cate',$categoryinfo);

        //一键vpc
        if (!empty($goodsInfoDesc["goods"][0]["goods_vpc"])) {
            $vpcID   = $goodsInfoDesc["goods"][0]["goods_vpc"];
            $goods   = new GoodsVpcController();
            $this->set('goodsInfo', $goodsInfoDesc);
            $vpcInfo = $goods->findVpcEcsConfigure($vpcID);
            $this->set('_vpcInfo', $vpcInfo);
            $this->set('_vpcId', $vpcID);
            $this->render('products' . '/' . 'vpc');
        } else {

            //判断商品是否发布
            if ($goodinfo['goodStatus'] == '0') {
                // 未发布
                return $this->redirect(
                    ['controller' => 'Home', 'action' => 'display']
                );
                exit;
             }
            //定义商品展示的页面类型
            $html_type = array(
                "bs"            => "bs",
                "citrix"        => "citrix",
                "citrix_public" => "citrix_public",
                "ecs"           => "ecs",
                "eip"           => "eip",
                "mpaas"         => "mpaas",
                "mysql"         => "mysql",
                "oracle"        => "oracle",
                "vfw"           => "vfw",
                "waf"           => "vfw",
                'vpc'           => "vfw",
                'elb'           => "vfw",
                'ebs'           => "vfw",
                'disks'           => "vfw"
            );
            
            $this->set('need_config_goods', $this->_need_config_goods);
            $this->set('html_type', $html_type[$goodsInfoDesc['goods'][0]["goodType"]]);
            $this->set('good_type', $goodsInfoDesc['goods'][0]["goodType"]);
            //发送post数据
            $this->set("config", $this->request->data);

//             debug($this->request->data);die;
            
            $this->set('goodsInfo', $goodsInfoDesc);
            $this->render('products' . '/' . 'goods-new');
        }
    }
    /**
     * @func:通用的分类--商品详细页面
     * @param:商品id
     * @date: 2015年10月9日 下午2:10:21
     * @author: shrimp liao
     * @return: null
     */
    public function category()
    {
        $this->set('good_category', $this->getCategoeyGoodsData());
        $this->render('category' . '/' . 'index');
    }
    /**
     * @func:通用的分类--商品详细页面
     * @param:商品id
     * @author: wangjc
     * @return: null
     */
    public function goods($category_id = 0)
    {
        $goods_table          = TableRegistry::get('Goods');
        $goods_category_table = TableRegistry::get('GoodsCategory');
        $goods_version_table = TableRegistry::get('GoodsVersion');
        $goods_version_detail_table = TableRegistry::get('GoodsVersionDetail');
        $goods_data           = $goods_table->find()->where(['category_id =' => $category_id, 'fixed' => 0]);
        $g = array();
        $department_id  = $this->request->session()->read('Auth.User.department_id');
        
        //获取版本id
        $version_data = $goods_version_table->find('list', [
            'keyField' => 'id',
            'valueField' => 'gid',
            'groupField' => 'gid'
        ])->toArray();
        
        //获取版本 vid=>租户id
        $detail_data = $goods_version_detail_table->find('list', [
            'keyField' => 'vid',
            'valueField' => 'value'
        ])
        ->where(['key' => 'tenantid'])
        ->toArray();
        
        foreach ($goods_data as $key => $good) {
            
            
            //判断一键vpc
            if (empty($good['goods_vpc'])) {
                $i = isset($version_data[$good['id']]) ? count($version_data[$good['id']]) : 0; //获取商品对应版本数
                $n = isset($good['goodStatus']) ? $good['goodStatus'] : 0; //是否
                if ($i > 0 && $n == 1) {
                    //检查商品版本是否售卖给该租户
                    foreach ($version_data[$good['id']] as $k => $v) {
                        if (isset($detail_data[$k])) {
                            $can_buy_depart_id_array = split(',', $detail_data[$k]);
                            if (in_array($department_id, $can_buy_depart_id_array) || in_array(0, $can_buy_depart_id_array)) {
                                $g[]=$good['id'];
                            }
                        } else {
                            $g[]=$good['id'];
                        }
                    }
                }
            } else {//一键vpc
                $g[]=$good['id'];
            }
        }
        $goods_data           = $goods_table->find()->where(['id in' => $g, 'fixed' => 0]);
        $goods_data           = $this->paginate($goods_data);

        $goods_category_data = $goods_category_table->find()->where(['id =' => $category_id])->first();
        $this->set('good_category', $this->getCategoeyGoodsData());
        $this->set('goods_data', $goods_data);
        $this->set('goods_category_data', $goods_category_data);
        $this->render('category' . '/' . 'goods');
    }
    /**
     * @func:读取Cookie中的number数量
     * @param:
     * @date: 2015年10月8日 上午10:45:10
     * @author: shrimp liao
     * @return: null
     */
    public function readCookieByNumber()
    {
        $number = 0;
        if ($this->Cookie->read('user.car')) {
            $unser_goods = $this->readCookie('user.car');
            foreach ($unser_goods as $item) {
                $number += (int) json_decode($item['attr'], true)['number'];
            }
        }
        return $number;
    }
    /**
     * @func: 读取Cookie（反序列化->反Base64）
     * @param:
     * @date: 2015年9月24日 下午5:11:09
     * @author: shrimp liao
     * @return: null
     */
    public function readCookie($name)
    {
        $goods_cars = $this->Cookie->read($name);
        if ($goods_cars) {
            $unser_goods = unserialize(base64_decode($goods_cars));
            return $unser_goods;
        } else {
            return false;
        }
    }
    
    
    public static function getGoodsinfoByID($goodsId)
    {
        $goods = TableRegistry::get('Goods');
        $goodsInfo = $goods->find('all')->where(['id' => $goodsId])->first();
        return $goodsInfo;
    }
    
    
    /**
     * @func: 获取商品信息，以及商品相关属性信息
     * @param: $goodsId 商品ID
     * @date: 2015年9月25日 下午3:08:36
     * @author: shrimp liao
     * @return: null
     */
    public static function getGoodsInfoDes($goodsId)
    {
        $goods = TableRegistry::get('Goods');
        $goods_version_table = TableRegistry::get("GoodsVersion");
        $goods_version_detail_table = TableRegistry::get("GoodsVersionDetail");
        

        $where = array('Goods.id' => $goodsId);
        //获取商品信息，包含商品分类
        $goodsInfo     = $goods->find('all')->contain(array('GoodsCategory'))->where($where)->order(array('sort_order' => 'ASC'))->map(function($row){
            if("" == $row['icon']){
                $row['icon'] = 'nophoto.jpg';
            }
            if("" == $row['mini_icon']){
                $row['mini_icon'] = 'nophoto.jpg';
            }
            return $row;
        })->toArray();


        //获取新版本信息
        foreach ($goodsInfo as &$value) {
            // debug($value);die;
            $goods_version_info = array();
            if (in_array($value["goodType"], self::$_new_goods)) {
                //
                $goods_version_data = $goods_version_table->find()->where(["gid" => $goodsId])->toArray();
                
                if (!empty($goods_version_data)) {
                    foreach ($goods_version_data as $version_data) {
                        $detail_data = $goods_version_detail_table->find()->where(["vid" => $version_data["id"]])->toArray();
                        $version_data["info"] = $detail_data;
                        $goods_version_info[] = $version_data;
                    }
                }
            }
            $value["version_info"] = $goods_version_info;
        }
        $goodsInfoDesc = array('goods' => $goodsInfo);
        return $goodsInfoDesc;
    }

    /*
     *
     */
    public function getCategoeyGoodsData()
    {
        //获取商品分类信息
        $goods_category_data['category_data'] = $this->getCanDisplayGoodsList();
        $count = [];
        foreach ($goods_category_data['category_data'] as $_k => $_v) {
            $count[$_v['id']] = count($_v['good']);
        }
        $goods_category_data['goods_count'] = $count;
        return $goods_category_data;
    }
    
    /**
     * 获取能够展示的商品列表
     * @param unknown $id
     * @return unknown
     */
    private function getCanDisplayGoodsList() 
    {
        //获取商品分类信息
        $goods_category_table                 = TableRegistry::get('GoodsCategory');
        $goods_category_data = $goods_category_table->find()->contain(['Good'])->order(['sort_order ASC'])->toArray();
        $goods_category_data = $this->_getCanDisplayCateInfo($goods_category_data,'good');
        
        return $goods_category_data;
    }

    public static function getAttributeById($id)
    {
        $attribute = TableRegistry::get('GoodsAttribute');
        $attribute = $attribute->find("all")->contain(array('GoodsAttributeDetail'))->where(array('GoodsAttribute.id' => $id))->first();
        return $attribute;
    }

    public static function getConfigByCode($code)
    {
        $attribute = TableRegistry::get('SetHardware');
        $attribute = $attribute->find("all")->where(array('set_code' => $code))->first();
        if ($attribute->gpu_gb != 0 && !empty($attribute->gpu_gb)) {
            $str = $attribute->cpu_number . "核" . $attribute->memory_gb . "G-" . $attribute->gpu_gb . "MB (显存)";
        } else {
            $str = $attribute->cpu_number . "核" . $attribute->memory_gb . "G";
        }
        $data = array(
            'id' => $attribute->set_id,
            'name' => $attribute->set_name,
            'cpu' => $attribute->cpu_number,
            'memory' => $attribute->memory_gb,
            'gpu' => $attribute->gpu_gb,
            'gpu_type' => $attribute->gpu_type,
            'code' => $attribute->set_code, 'config' => $str);
        return $data;
    }

    public static function getImageByCode($code)
    {
        $attribute = TableRegistry::get('Imagelist');
        $attribute = $attribute->find("all")->where(array('image_code' => $code))->first();
        $data      = array('id' => $attribute->id, 'name' => $attribute->image_name, 'code' => $attribute->image_code);
        return $data;
    }

    public static function getNameByCode($code)
    {
        $attribute = TableRegistry::get('InstanceBasic');
        $attribute = $attribute->find("all")->where(array('code' => $code))->first();
        $data = array();
        if (!empty($attribute)) {
            $data      = array('id' => $attribute->id, 'name' => $attribute->name, 'code' => $attribute->code);
        }
        
        return $data;
    }

    //获取价格信息
    public static function getPriceInfoById($id)
    {
        $goods_version_price_table = TableRegistry::get("GoodsVersionPrice");

        $price_data = $goods_version_price_table->find()->where(["id" => $id])->first();

        //获取计费周期
        $intervalArr = Configure::read('charge_interval');
        $item = array();
        if (!empty($price_data)) {
            //名称
            if($price_data["charge_mode"] == 'cycle'){
                $item['name'] = '按固定计费';
            }else if($price_data["charge_mode"] == 'duration'){
                $item['name'] = '按时长计费';
            }else if($price_data["charge_mode"] == 'oneoff'){
                $item['name'] = '按一次性计费';
            }

            $interval = $price_data['interval'];
            $item['duration'] = $price_data['unit'];
            $item['interval'] = '按'.$intervalArr[$interval].'计费';
            $item['id']     = $id;
            $item['unit']   = '(元/'.$intervalArr[$interval].')';;
            $item['price']  = $price_data['price'];

            $item['charge_mode'] = $price_data["charge_mode"];
            $item['interval_type'] = $price_data["interval"];
        }

        return $item;
    }

    //获取区域信息
    protected function getRegionInfoByCode($code)
    {
        $agent_table = TableRegistry::get("Agent");
        $agent_data = $agent_table->find()->where(["region_code" => $code])->first();
        $data      = array('id' => $agent_data->id, 'name' => $agent_data->agent_name, 'code' => $agent_data->agent_code);
        return $data;

    }

    //获取模板信息
    protected function getSpecInfoById($id)
    {
        $goods_version_spec_table = TableRegistry::get("GoodsVersionSpec");
        $spec_data = $goods_version_spec_table->find()->where(["id" => $id])->first();
        $data =$spec_data;
        if (isset($spec_data["image_code"])) {
            $data["image"] = $this->getImageByCode($spec_data["image_code"]);
        }
        if (isset($spec_data["instancetype_code"])) {
            $data["instancetype"] = $this->getConfigByCode($spec_data["instancetype_code"]);
        }
        // $data["image"] = $this->getImageByCode($spec_data["image_code"]);
        // $data["instancetype"] = $this->getConfigByCode($spec_data["instancetype_code"]);
        return $data;
    }

    //获bs价格
    protected function getBsPriceById($id)
    {
        
        $goods_version_price_table = TableRegistry::get("GoodsVersionPrice");
        $price_data = $goods_version_price_table->find()->where(["vid" => $id])->toArray();
        $data =array();
        if (!empty($price_data)) {
            foreach ($price_data as$value) {
                $data[$value["id"]] = $value;
            }
        }
        return $data;
    }

    //获取商品规格
    public function getNewGoodVersionByVersionId($vid){
        $goods_version_table = TableRegistry::get('GoodsVersion');
        $version_data = $goods_version_table->find()->where(["id" => $vid])->first();
        $data = $this->newGoodVersion($version_data);
        return $data;
    }
    
    /**
     * 获取规格想问
     * @param object $v
     * @return multitype:unknown multitype: NULL multitype:multitype:string   Ambigous <multitype:, multitype:NULL > multitype:NULL  Ambigous <unknown, multitype:NULL , multitype:string NULL >
     */
    public function newGoodVersion($v) {
        $goods_version_detail_table = TableRegistry::get('GoodsVersionDetail');
        $detail_data = $goods_version_detail_table->find()->where(["vid" => $v->id])->toArray();
        $attribute =array();
        if(!empty($detail_data)) {
            foreach ($detail_data as $dv) {
                switch ($dv["key"]) {
                    case 'subnet':
                        $attribute["subnet"]  = $this->getNameByCode($dv["value"]);
                        break;
                    case 'region':
                        $attribute["region"]  = $this->getRegionInfoByCode($dv["value"]);
                        break;
                    case 'vpc':
                        $attribute["vpc"]  = $this->getNameByCode($dv["value"]);
                        break;
                    case 'specid':
                        $attribute["spec"] = $this->getSpecInfoById($dv["value"]);
                        break;
                    case 'processid':
                        $attribute["processid"] = $dv["value"];
                        break;
                    case 'service_brand':
                        $attribute["service_brand"] = $dv["value"];
                        break;
                    case 'service_type':
                        $attribute["service_type"] = $dv["value"];
                        break;
                }
            }
        }
        // 获取版本对应价格 
        switch ($v->type) {
            case 'citrix':
            case 'citrix_public':
                $goods_version_detail = TableRegistry::get('GoodsVersionDetail');
                $detail = $goods_version_detail->find()->where(['vid'=>$v->id,'key'=>'specid'])->first();
                $attribute["pricelist"] = self::getPriceList($detail->value,'sid');
                break;
            case 'bs':
            case 'mysql':
            case 'oracle':
                $attribute["pricelist"] = $this->getBsPriceList($v->id);
                break;
            case 'vpc':
                $attribute["pricelist"] =[];
                break;
            default:
                $attribute["pricelist"] = self::getPriceList($v->id);
                break;
        }
        
        $attribute["name"] = $v["name"];
        $attribute["price"] = $v["price"];
        $attribute["id"] = $v["id"];
        $attribute["description"] = $v["description"];
        $attribute["unit"] = $v["unit"];
        return $attribute;
    }
    /**
     * @author wangjincheng
     * 获取模板计费列表
     * @param string vid 版本id
     * @return array 版本信息
     */
    public function getBsPriceList($vid) {
        $goods_version_price_table = TableRegistry::get("GoodsVersionPrice");
        $prics_list = $goods_version_price_table->find()->where(['vid' => $vid])->toArray();
        
        $list = array();
        $arr = array();
        foreach ($prics_list as $v) {
            $arr['id'] = $v['id'];
            $arr['vid'] = $vid;
            //TODO 之后这些文字改通过方法获取
            $arr['duration'] = $v['unit'].'月';
            $arr['price'] = $v['price'];
            $arr['interval'] = "按月计费";
            $arr['unit'] = "(元/月)";
            $list[0]['list'][] = $arr;
        }
        return $list;
    }

    
    //获取计费信息  //添加桌面有调用
    public static function getPriceList($vid,$field ='vid'){
        $goods_version_price_table = TableRegistry::get("GoodsVersionPrice");
        $groupData = $goods_version_price_table->find('list',[
                'keyField'      =>'interval',
                'valueField'    =>'id',
                'groupField'    =>'charge_mode'
            ])->where([$field => $vid])->toArray();
        $priceData = $goods_version_price_table->find('list',[
                'keyField'      =>'id',
                'valueField'    =>'price' 
            ])->where([$field => $vid])->toArray();
        $unitData = $goods_version_price_table->find('list',[
                'keyField'      =>'id',
                'valueField'    =>'unit' 
            ])->where([$field => $vid])->toArray();
        //获取计费周期
        $intervalArr = Configure::read('charge_interval');
        
        $result = array();
        foreach($groupData as $key=>$priceList){
            if($key == 'cycle'){
                $style['name'] = '按固定计费';
            }else if($key == 'duration'){
                $style['name'] = '按时长计费';
            }else if($key == 'oneoff'){
                $style['name'] = '按一次性计费';
            }
            $list = [];
            foreach($priceList as $interval =>$id){
                if(array_key_exists($interval, $intervalArr)){
                    $temp['interval'] = '按'.$intervalArr[$interval].'计费';
                    $temp['id']     = $id;
                    $temp['unit']   = '(元/'.$intervalArr[$interval].')';;
                    $temp['price']  = $priceData[$id];
                    $temp['duration'] = $unitData[$id].'月';
                    $list[] = $temp;
                }
            }
            $style['list'] = $list;
            $result[] = $style;
        }
        return $result;
    }


    public function getGoodsJson($goodsid = 72)
    {
        //读取缓存
        if ($posts = Cache::read($goodsid, 'goods') != false){
            echo Cache::read($goodsid, 'goods');exit;
        }
        $this->autoRender = false;
        $goodstable       = TableRegistry::get('Goods');
        $goods_version_table = TableRegistry::get('GoodsVersion');
        $goods_version_detail_table = TableRegistry::get('GoodsVersionDetail');
        $agent_table = TableRegistry::get("Agent");

        $goods            = $goodstable->find()->select(['id', 'user_chargings', 'goodType'])->where(array('id' => $goodsid))->first();
        $data_attribute   = array();
        if (!empty($goods["goodType"]) && !in_array($goods["goodType"], ["onevpc", "rds"])) {
            $version_data = $goods_version_table->find()->where(["gid" => $goodsid])->toArray();
            if (!empty($version_data)) {
                foreach ($version_data as $v) {
                    $attribute = $this->newGoodVersion($v);
                    $data_attribute[] = $attribute;     
                }
            }
            Cache::write($goodsid, json_encode($data_attribute), 'goods'); //写入缓存 5分钟
            echo(json_encode($data_attribute)); exit;
        }
        
        echo "";
        exit;
        
    }

    //商品和分类信息
    public function getGoodAndCategory($goodsId){
        //商品和分类信息
        $goods=TableRegistry::get('Goods');
        $category=TableRegistry::get('GoodsCategory');
        $goodinfo=$goods->find()->where(array('id'=>$goodsId))->first();
        $this->set('this_good_info',$goodinfo);
        $categoryinfo=$category->find()->where(array('id'=>$goodinfo['category_id']))->first();
        $this->set('this_good_cate',$categoryinfo);
    }
    public function selectEcs($goods_id,$order_good_id = null)
    {
        $this->getGoodAndCategory($goods_id);
        $data = $this->getOrderGoodInstanceInfo($order_good_id);
        $data_charge = parent::_GetBillCycle(null);
        $this->set('chargeList',$data_charge);
        //计费周期
        $url =  isset($data['url']) ? $data['url'] : '/home/products/'.$goods_id;
        // debug($url);die;
        //跳转地址
        $this->set('url',$url);
        //分类信息
        $this->set('good_category', $this->getCategoeyGoodsData());

        $this->set('config',$data);
        $this->set('goods_id',$goods_id);
        $this->render('products/selectEcs');
    }
    
    public function selectEip($goods_id, $vid, $order_good_id = null) {
        $this->getGoodAndCategory($goods_id);
        $data = $this->getOrderGoodInstanceInfo($order_good_id);
        //debug($data);exit;
        $this->set('_menuCategory', $this->menuCategory());
        $data_charge = parent::_GetBillCycle(null);
        $this->set('chargeList',$data_charge);
        //计费周期
        $url =  isset($data['url']) ? $data['url'] : '/home/products/'.$goods_id;
        
        //规格
        $version = $this->getNewGoodVersionByVersionId($vid);
        $this->set('version',$version);
   
        //跳转地址
        $this->set('url',$url);
//         debug($data);die;
        //分类信息
        $this->set('good_category', $this->getCategoeyGoodsData());
        $this->set('config', $data);
        $this->set('goods_id', $goods_id);
        $this->render('products/selectEip');
    }
    
    public function selectVfw($goods_id, $vid,$order_good_id =null) {
        $this->getGoodAndCategory($goods_id);
        $data_charge = parent::_GetBillCycle(null);
        $this->set('chargeList',$data_charge);
        $data = $this->getOrderGoodInstanceInfo($order_good_id);
        //计费周期
        $url =  isset($data['url']) ? $data['url'] : '/home/products/'.$goods_id;
    
        //规格
        $version = $this->getNewGoodVersionByVersionId($vid);
        $this->set('version',$version);
    
        //         debug($this->request->data);die;
    
        //跳转地址
        $this->set('url',$url);
        //分类信息
        $this->set('good_category', $this->getCategoeyGoodsData());
        $this->set('config', $data);
        $this->set('goods_id', $goods_id);
        $this->render('products/selectVfw');
    }
    
    public function selectVpc($goods_id, $vid,$order_good_id =null) {
        $this->getGoodAndCategory($goods_id);
        $data = $this->getOrderGoodInstanceInfo($order_good_id);
        $url =  isset($data['url']) ? $data['url'] : '/home/products/'.$goods_id;
        //规格
        $version = $this->getNewGoodVersionByVersionId($vid);
        
        $this->set('version',$version);
        //跳转地址
        $this->set('url',$url);
        //分类信息
        $this->set('good_category', $this->getCategoeyGoodsData());
        $this->set('config', $data);
        $this->set('goods_id', $goods_id);
        $this->render('products/selectVpc');
    }
    
    public function selectDisks($goods_id, $vid,$order_good_id =null) {
        $this->getGoodAndCategory($goods_id);
        $data = $this->getOrderGoodInstanceInfo($order_good_id);
        $url =  isset($data['url']) ? $data['url'] : '/home/products/'.$goods_id;
        //规格
        $version = $this->getNewGoodVersionByVersionId($vid);
    
        $this->set('version',$version);
        //跳转地址
        $this->set('url',$url);
//         debug($data);die;
        //分类信息
        $this->set('good_category', $this->getCategoeyGoodsData());
        $this->set('config', $data);
        $this->set('goods_id', $goods_id);
        $this->render('products/selectDisks');
    }
    
    public function selectElb($goods_id, $vid,$order_good_id =null) {
        $this->getGoodAndCategory($goods_id);
        $data = $this->getOrderGoodInstanceInfo($order_good_id);
        $url =  isset($data['url']) ? $data['url'] : '/home/products/'.$goods_id;
        //规格
        $version = $this->getNewGoodVersionByVersionId($vid);

        $Systemsetting_table = TableRegistry::get('Systemsetting');
        $elb_imageCode = $Systemsetting_table->find()->select(['para_value'])->where(['para_code'=>'lbs_imageCode'])->first()->para_value;
        $this->set('imageCode',$elb_imageCode);
        $elb_instanceTypeCode = $Systemsetting_table->find()->select(['para_value'])->where(['para_code'=>'lbs_instanceTypeCode'])->first()->para_value;
        $this->set('instanceTypeCode',$elb_instanceTypeCode);
        
    
        $this->set('version',$version);
        //跳转地址
        $this->set('url',$url);
        //分类信息
        $this->set('good_category', $this->getCategoeyGoodsData());
        $this->set('config', $data);
        $this->set('goods_id', $goods_id);
         // debug($data);die;
        $this->render('products/selectElb');
    }
    

    public function getOrderGoodInstanceInfo($order_good_id){
        if($order_good_id == null){
            return $this->request->data;
        }
        $orders_table = TableRegistry::get('Orders');
        $order_goods_table = TableRegistry::get('OrdersGoods');
        $order_entity = $order_goods_table->findById($order_good_id)->first();
        if(null == $order_entity){
            throw new \Exception("修改的订单商品不存在", 1);
        }
        $order = $orders_table->get($order_entity->order_id);
        
        $data =  json_decode($order_entity->instance_conf,true);
        $data['url'] = "/console/orders/editGoodConfig";
        $data['order_good_id'] = $order_good_id;
        $data['dept_id'] = $order->department_id;
        return $data;
    }


    public function selectCitrixVpc($goods_id,$vid,$price,$order_good_id = null)
    {
        $this->getGoodAndCategory($goods_id);
        $data = $this->getOrderGoodInstanceInfo($order_good_id);
        $goods_version_detail_table = TableRegistry::get("GoodsVersionDetail");

        $detail_data = $goods_version_detail_table->find()->where(['vid' => $vid, 'key' => 'region'])->first();

        $data_charge = parent::_GetBillCycle(null);

        //规格
        $version = $this->getNewGoodVersionByVersionId($vid);
        $this->set('version',$version);

        //计费 getPriceInfoByI
        $data['priceId'] = $price;

        $price = $this->getPriceInfoById($price);

        $this->set('price', $price);

        $this->set('chargeList',$data_charge);
        //计费周期
        $url = isset($data['url']) ? $data['url'] : '/home/products/';
        //跳转地址
        $this->set('url',$url);
        
        //分类信息
        $this->set('good_category', $this->getCategoeyGoodsData());
        $this->set('_menuCategory', $this->menuCategory());

        $this->set('detail_data', $detail_data);    
        $this->set('config',$data);
        $this->set('goods_id',$goods_id);
        $this->render('products/selectCitrixVpc');

     
    }


}
