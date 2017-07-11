<?php
/**
 * 费用总览
 * 
 * @file: ChargeController.php
 * @date: 2016年2月29日 上午10:20:28
 * @author: xingshanghe
 * @email: xingshanghe@icloud.com
 * @copyright poplus.com
 *
 */
namespace App\Controller\Console;

use App\Controller\Console\ConsoleController;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Core\App;
use Cake\I18n\Time as CakeTime;

class ChargeController extends ConsoleController
{

    protected $_limit  = 10;

    protected $_offset = 0;

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
        $this->loadComponent('Excel');

        $this->_limit  = !empty($this->request->query['limit']) ? $this->request->query['limit'] : 10;
        $this->_offset = !empty($this->request->query['offset']) ? $this->request->query['offset'] : 0;

    }

    /**
     * 检查权限
     * @date: 2016年3月10日 下午4:50:06
     *
     * @author : wangjc
     * @access private
     * @param string $param            
     * @return boolean
     */
    private function _checkPopedom($param)
    {
        $popedom = parent::checkConsolePopedom($param);
        return $popedom;
    }

    public $_pageList = array(
        'total' => 0,
        'rows' => array()
    );

    /**
     * 初始化默认日期
     * @param  [string] $start [开始日期]
     * @param  [string] $end   [结束日期]
     * @return [array]        [$start,$end]
     */
    protected function _initStartAndEndDate($start,$end){
        $time_now = new CakeTime(new \DateTime("NOW"));
        $time_now->startOfDay();
        //默认结束日期为当前日期
        if(empty($end)){
            $end = clone $time_now;
        }else{
            $end = new CakeTime(new \DateTime($end));
            
        }
        $end->addDay()->subSecond();
        //默认开始日期为当前月第一天
        if(empty($start)){
            $start = $time_now->startOfMonth();
        }else{
            $start = new CakeTime(new \DateTime($start));
        }
        return [$start,$end];
    }


    public function subject($department_id = '',$start = '', $end = '')
    {
        
        $departments_table = TableRegistry::get('Departments');
        
        $_pop = false;
        $_pop = $this->_checkPopedom('ccm_user_charge_subject'); // 检查权限
        if (! $_pop) {
            return $this->redirect('/console/');
        }
        //设置默认日期        
        list($start,$end) = $this->_initStartAndEndDate($start,$end);

        $_start_time    = $start->toUnixString();
        $_end_time      = $end->toUnixString();
            
        // 所属部门id
        if (empty($department_id)) {
            $department_id = $this->request->session()->read('Auth.User.department_id') ? $this->request->session()->read('Auth.User.department_id') : 0;
        }
        
        $_sys_pop = false;
        $_sys_pop = $this->_checkPopedom('cmop_global_sys_admin'); // 检查系统管理员权限
        if ($_sys_pop) {
            $departments_data = $departments_table->find()->toArray();
            $this->set('departments_data', $departments_data);
        }else {
            $department_id = $this->request->session()->read('Auth.User.department_id') ? $this->request->session()->read('Auth.User.department_id') : 0;
            $department_data = $departments_table -> find() ->where(['id'=> $department_id])->first();
            $this->set('department_data', $department_data);
        }
        
        $bill_base_table = TableRegistry::get('BillBase');
        //获取饼图数据
        $pie_chart_data     = $bill_base_table->getPieChartData($start->i18nFormat("yyyy-MM-dd HH:mm:ss",'PRC'),$end->i18nFormat("yyyy-MM-dd HH:mm:ss",'PRC'),$department_id);
        //饼图消费总金额
        $pie_total_amount   = collection($pie_chart_data)->sumOf('cost'); 
                
        $_l_y = date("Y", $_start_time);
        $_l_m = date("m", $_start_time);
        $_l_end = $_l_y . '-' . $_l_m . '-01';
        $_l_end_time = strtotime($_l_end);
        
        $where = array();
        $where['department_id'] = $department_id;
        $where['bill_date <']     = $_l_end;
        $bill_query = $bill_base_table->find();
        $line_query = $bill_query->select([
                'cost'=>$bill_query->func()->sum('amount'),
                'y'   =>'year',
                'm'   =>'month',
                'time'  =>'create_time'
            ])->where($where)->group('year')->group('month')->order('year desc,month desc')->toArray();

        $l_query = [];
        $_l_num = 6; // 截取6个月的数据
        if (! empty($line_query)) {
            // $line_query = array_chunk($line_query, 6);//截取6个月的数据
            // $line_query = $line_query[0];
            for ($_l_i = 0; $_l_i < $_l_num; $_l_i ++) {
                $_l_j = 6;
                $_l_time = date("Y-m-d", strtotime("-$_l_i months", strtotime($_l_end))); // 每个月的时间
                
                foreach ($line_query as $_l_v) { // 对查询到的数据进行循环
                    $_l_v_t_m = $_l_v['m']; // 数据的月份
                    
                    if ($_l_v_t_m < 10) { // 当月分小于10时，在前面加0
                        $_l_v_t_m = '0' . $_l_v_t_m;
                    }
                    
                    $_l_v_t = $_l_v['y'] . '-' . $_l_v_t_m . '-01'; // 拼接时间，为数据月份的第一天
                    
                    $_l_v_time = date("Y-m-d", strtotime("+1 months", strtotime($_l_v_t)));
                    
                    if ($_l_v_time == $_l_time) { // 获取对应月份的数据
                        $l_query[] = $_l_v;
                        $_l_j --;
                        break;
                    }
                }
                
                if ($_l_j == $_l_num) { // 当没有对应月份数据时，赋值为0
                    $_time_y = date("Y", strtotime("-1 months", strtotime($_l_v_time)));
                    $_l_q_v['cost'] = 0;
                    $_l_q_v['y'] = date("Y", strtotime("-1 months", strtotime($_l_time)));
                    $_l_q_v['m'] = date("n", strtotime("-1 months", strtotime($_l_time)));
                    $l_query[] = $_l_q_v;
                }
            }
        } else { // 当查询到的数据为空的时候，给所有的月份数据赋值0
                 // $line_query = $line_query[0];
            for ($_l_i = 0; $_l_i < $_l_num; $_l_i ++) {
                $_l_time = date("Y-m-d", strtotime("-$_l_i months", strtotime($_l_end)));
                
                $_l_q_v['cost'] = 0;
                $_l_q_v['y'] = date("Y", strtotime("-1 months", strtotime($_l_time)));
                $_l_q_v['m'] = date("n", strtotime("-1 months", strtotime($_l_time)));
                $l_query[] = $_l_q_v;
            }
        }
        $line_query = $l_query;
       
        
        $this->set('department_id', $department_id);
        $this->set('sum_cost', $pie_total_amount);
        // $this->set('time_query', $time_query);
        $this->set('line_query', $line_query);
        $this->set('query', $pie_chart_data);
        $this->set('start', $start->i18nFormat("yyyy-MM-dd",'PRC'));
        $this->set('end', $end->i18nFormat('yyyy-MM-dd','PRC'));
    }

    /**
     * 服务计费详情
     *
     * @author wangjc
     * @param
     *            $start:开始时间，$end:结束时间（格式为yyyy-mm-dd）
     */
    public function subjectData($department_id,$start, $end)
    {
        $limit = ! empty($this->request->query['limit']) ? $this->request->query['limit'] : 10;
        $offset = ! empty($this->request->query['offset']) ? $this->request->query['offset'] : 0;
        
        $_time = time(); // 当前时间
        
        //设置默认日期        
        list($start,$end) = $this->_initStartAndEndDate($start,$end);
        // $_end_time = strtotime($end) + 86400;
        // 获取部门id
        if(empty($department_id)){
            $department_id = $this->request->session()->read('Auth.User.department_id') ? $this->request->session()->read('Auth.User.department_id') : 0;
        }


        $bill_base_table = TableRegistry::get('BillBase');

        $condition = array();
        $condition['bill_date >='] = $start->i18nFormat("yyyy-MM-dd HH:mm:ss",'PRC');
        $condition['bill_date <='] = $end->i18nFormat("yyyy-MM-dd HH:mm:ss",'PRC');
        $condition['department_id'] = $department_id;
        

        $query  = $bill_base_table->find();
        $result = $query->select([
                    'resource_type',
                    'charge_type',
                    'cost'=>$query->func()->sum('amount')])
            ->where($condition)
            ->group('charge_type')
            ->group('resource_type')
            ->order('cost desc')->offset($offset)->limit($limit)
            ->map(function($row){
                $constant_resource_type = Configure::read('resource_type');
                $row['name'] = $constant_resource_type[$row['resource_type']];
                return $row;
            })
            ->toArray();
        
        $this->_pageList['total'] = count($result);
        $this->_pageList['rows'] = $result;
        $this->createView('json');
        $this->set('total',$this->_pageList['total']);
        $this->set('rows',$this->_pageList['rows']);
        $this->set('_serialize',['total','rows']);
        //$this->_serialize(['total','rows']);
        // echo json_encode($this->_pageList);
        // exit();
    }

    /**
     * [_initDepartment 初始化租户]
     * @param  [int] $department_id [租户id]
     * @return [int]                
     */
    protected function _initDepartment($department_id){
        //初始化可选租户
        $departments_table = TableRegistry::get('Departments');
        // 筛选部门(无值初始化)
        if ($department_id == '') {
            $department_id = $this->request->session()->read('Auth.User.department_id') ? $this->request->session()->read('Auth.User.department_id') : 0;
        }

        $_sys_pop = $this->_checkPopedom('cmop_global_sys_admin'); // 检查系统管理员权限
        if (true === $_sys_pop) {
            $departments_data = $departments_table->find()->toArray();
            $this->set('departments_data', $departments_data);
        }else {
            $department_id = $this->request->session()->read('Auth.User.department_id') ? $this->request->session()->read('Auth.User.department_id') : 0;
            $departments_data = $departments_table -> find() ->where(['id'=> $department_id])->first();
            $this->set('departments_data', $departments_data);
        }
        if($department_id == 0){
            $this->set('department_name','全部');
        }else{
            $departments_entity = $departments_table -> find()->select(['name']) ->where(['id'=> $department_id])->first();
            $this->set('department_name',$departments_entity->name);
        }

        return $department_id;
    }

    /**
     * 导出Excel文件单元格数据装填，表头设置
     * @param  [\PHPExcel_Worksheet] &$objActSheet [Excel_Worksheet操作对象]
     * @param  [array] $lists        [装填数据]
     */
    protected function _get_citrix_field_title()
    {
        return [
            'bill_date'         => '账单日期',
            'name'              => '桌面名称',
            'loginname'         => '使用人',
            'logintime'         => '登陆时间',
            'logoutime'         => '登出时间',
            'charge_type_txt'   => '计费类型',
            'duration'          => '使用时长',
            'price'             => '单价',
            'price'             => '成交价',
            'charge_unit_txt'   => '计费单位',
            'amount'            => '消费金额'
        ];
    }
    /**
     * 导出Excel文件单元格数据装填，表头设置
     * @param  [\PHPExcel_Worksheet] &$objActSheet [Excel_Worksheet操作对象]
     * @param  [array] $lists        [装填数据]
     */
    protected function _get_bs_field_title()
    {
        return [
            'name'           => 'b/s工具名',
            'buyer_name'     => '购买人',
            'order_date'     => '购买日期',
            'start_date'     => '生效日',
            'end_date'       => '截止日',
            'duration'       => '购买时长/月',
            'amount'         => '消费金额'
        ];
    }
    /**
     * 导出Excel文件单元格数据装填，表头设置
     * @param  [\PHPExcel_Worksheet] &$objActSheet [Excel_Worksheet操作对象]
     * @param  [array] $lists        [装填数据]
     */
    protected function _get_rds_field_title()
    {
        return [
            'name'           => '数据库类型',
            'buyer_name'     => '购买人',
            'order_date'     => '购买日期',
            'start_date'     => '生效日',
            'end_date'       => '截止日',
            'amount'         => '消费金额'
        ];
    }
    /**
     * 导出Excel文件单元格数据装填，表头设置
     * @param  [\PHPExcel_Worksheet] &$objActSheet [Excel_Worksheet操作对象]
     * @param  [array] $lists        [装填数据]
     */
    protected function _get_mpaas_field_title()
    {
        return [
            'column_code'           => '栏目名',
            'program_name'          => '节目名',
            'vendor_code'           => '服务商',
            'consumption_subjects'  => '服务类型',
            'duration'              => '节目时长（秒）',
            'price'                 => '单价（元/秒）',
            'amount'                => '消费金额'
        ];
    }
    /**
     * 导出Excel文件单元格数据装填，表头设置
     * @param  [\PHPExcel_Worksheet] &$objActSheet [Excel_Worksheet操作对象]
     * @param  [array] $lists        [装填数据]
     */
    protected function _get_ecs_field_title()
    {
        return [
            'bill_date'         => '账单日期',
            'name'              => '主机名',
            'code'              => '主机code',
            'buyer_name'        => '购买人',
            'charge_type_txt'   => '计费方式',
            'order_date'        => '购买日',
            'start_date'        => '生效日',
            'market_price'      => '原价',
            'price'             => '成交价',
            'charge_unit_txt'   => '计费单位',
            'amount'            => '消费金额'
        ];
    }

    /**
     * 导出Excel文件单元格数据装填，表头设置
     * @param  [\PHPExcel_Worksheet] &$objActSheet [Excel_Worksheet操作对象]
     * @param  [array] $lists        [装填数据]
     */
    protected function _get_vfw_field_title()
    {
        return $this->_get_ecs_field_title();
    }
    /**
     * 导出Excel文件单元格数据装填，表头设置
     * @param  [\PHPExcel_Worksheet] &$objActSheet [Excel_Worksheet操作对象]
     * @param  [array] $lists        [装填数据]
     */
    protected function _get_eip_field_title()
    {
        $ecsTitle = $this->_get_ecs_field_title();
        $ecsTitle['name'] = 'EIP名称';
        $ecsTitle['code'] = 'EIP code';

        return $ecsTitle;
    }

    public function _get_vpc_field_title()
    {
        $ecsTitle = $this->_get_ecs_field_title();
        $ecsTitle['name'] = 'VPC名称';
        $ecsTitle['code'] = 'VPC code';

        return $ecsTitle;
    }

    public function _get_elb_field_title()
    {
        $ecsTitle = $this->_get_ecs_field_title();
        $ecsTitle['name'] = '负载均衡名称';
        $ecsTitle['code'] = '负载均衡code';

        return $ecsTitle;
    }

    public function _get_disks_field_title()
    {
        $ecsTitle = $this->_get_ecs_field_title();
        $ecsTitle['name'] = '块存储名称';
        $ecsTitle['code'] = '块存储 code';

        return $ecsTitle;
    }

    public function _get_firewall_field_title()
    {
        $ecsTitle = $this->_get_ecs_field_title();
        $ecsTitle['name'] = '防火墙名称';
        $ecsTitle['code'] = '防火墙 code';

        return $ecsTitle;
    }

    public function _get_mstorage_field_title()
    {
        $ecsTitle = $this->_get_ecs_field_title();
        $ecsTitle['name'] = '媒体云存储名称';
        $ecsTitle['code'] = '媒体云存储 code';

        return $ecsTitle;
    }

    public function _get_vbrI_field_title()
    {
        $vbriTitle =  [
            'bill_date'         => '账单日期',
            'buyer_name'        => '购买人',
            'charge_type_txt'   => '计费方式',
            'market_price'      => '原价',
            'price'             => '成交价',
            'charge_unit_txt'   => '计费单位',
            'amount'            => '消费金额'
        ];
        $vbriTitle['name'] = '边界路由器接口名称';
        $vbriTitle['routerCode'] = '边界路由器code';
        $vbriTitle['initiatingSideRouterInterfaceCode'] = '发起端接口code';
        $vbriTitle['acceptingSideRouterInterfaceCode'] = '接收端接口code';
        $vbriTitle['spec'] = '规格';

        return $vbriTitle;
    }


    /**
     * 导出Excel文件单元格数据装填，表头设置
     * @return [type] [description]
     */
    protected function _get_hive_field_title()
    {
        return [
            'bill_date'         => '账单日期',
            'objnums'           => '文件使用数量',
            'GB'                => '磁盘空间占用大小',
            'price'             => '成交价',
            'amount'            => '消费金额'
        ];
    }
    /**
     * 消费明细列表
     * 1.正常请求不带$download参数，为渲染访问的列表页面数据
     * 2.ajax请求返回列表数据，渲染lists列表数据
     * 3.download参数为true,下载当前筛选条件下的列表数据
     * 
     * @param  string  $department_id [租户id]
     * @param  string  $resource_type [资源类型]
     * @param  string  $start         [开始时间]
     * @param  string  $end           [结束时间]
     * @param  boolean $download      [下载标志]
     */
    public function detail($department_id = '',$resource_type = 'bs',$start = '',$end = '',$download = false){

        // 检查权限
        $is_allow = $this->_checkPopedom('ccm_user_charge_detail'); 
        if (true !== $is_allow) {
            return $this->redirect('/console/');
        }

        //系统定义的资源类型常量(参数类型检查)
        $resource_type_constant = Configure::read('resource_type');
        if(!array_key_exists($resource_type, $resource_type_constant)){
            return $this->redirect('/console/charge/subject');
        }

        unset($resource_type_constant['citrix_public']);

        //设置默认日期        
        list($start,$end) = $this->_initStartAndEndDate($start,$end);

        try {
            $department_id = $this->_initDepartment($department_id);
            //获取筛选列表
            $method     = "_get_".$resource_type.'_lists';
            $export_method = "_get_".$resource_type."_field_title";
            $template   = $resource_type."_detail";

            if(method_exists($this, $method)){
                $condition = array();
                if($department_id >0){
                    $condition['department_id'] = $department_id;
                }
                $condition['resource_type'] = $resource_type;
                $condition['bill_date >=']   = $start->i18nFormat("yyyy-MM-dd HH:mm:ss",'PRC');
                $condition['bill_date <=']   = $end->i18nFormat("yyyy-MM-dd HH:mm:ss",'PRC');

                if($download == true){//如果导出则导出全部内容
                    $this->_limit  = 100000;
                    $this->_offset = 0;
                }

                list($lists,$total,$total_amount) = $this->$method($condition);
                if($download == true){
                    if(method_exists($this, $export_method)){
                        $filename = $resource_type.'-report-';
                        $filename .= $start->i18nFormat("yyyyMMdd")."-".$end->i18nFormat("yyyyMMdd");
                        //获取下载文件表格的title定义
                        $fieldTitle = $this->$export_method();
                        $this->Excel->setSheetCloumnTitle($fieldTitle)
                            ->setSheetColumnBody($lists)
                            ->export($filename);
                    }
                }
                //页面ajax请求数据列表
                if($this->request->isAjax()){
                    $this->_pageList['total']   = $total;
                    $this->_pageList['rows']    = $lists;
                    echo json_encode($this->_pageList);
                    exit();
                }
                $this->set('lists',$lists);
            }else{
                throw new \Exception("the method {$method} is not exsits", 1);
            }

            $this->set('end', $end->i18nFormat("yyyy-MM-dd"));
            $this->set('start', $start->i18nFormat("yyyy-MM-dd"));
            $this->set('type', $resource_type);
            $this->set('total',$total);
            $this->set('total_amount',$total_amount);
            $this->set('department_id', $department_id);
            $this->set('resource_type_data', $resource_type_constant);
            
            $this->render('detail/'.$template );

        } catch (MissingTemplateException $e) {
            if (Configure::read('debug')) {
                throw $e;
            }
        } catch (\Exception $e) {
            if (Configure::read('debug')) {
                throw $e;
            }
        }
    }

    /**
     * [计费详情，供后台使用]
     */
    public function detailForAdmin($department_id = '',$resource_type = 'bs',$start = '',$end = '',$download = false)
    {

        // 检查权限
        $is_allow = $this->_checkPopedom('ccm_user_charge_detail'); 
        if (true !== $is_allow) {
            return $this->redirect('/console/');
        }

        //系统定义的资源类型常量(参数类型检查)
        $resource_type_constant = Configure::read('resource_type');
        if(!array_key_exists($resource_type, $resource_type_constant)){
            return $this->redirect('/console/charge/subject');
        }

        unset($resource_type_constant['citrix_public']);

        //设置默认日期        
        list($start,$end) = $this->_initStartAndEndDate($start,$end);


            $department_id = $this->_initDepartment($department_id);
            //获取筛选列表
            $method     = "_get_".$resource_type.'_lists';
            $export_method = "_get_".$resource_type."_field_title";
            $template   = $resource_type."_detail";

            if(method_exists($this, $method)){
                $condition = array();
                if($department_id >0){
                    $condition['department_id'] = $department_id;
                }
                $condition['resource_type'] = $resource_type;
                $condition['bill_date >=']   = $start->i18nFormat("yyyy-MM-dd HH:mm:ss",'PRC');
                $condition['bill_date <=']   = $end->i18nFormat("yyyy-MM-dd HH:mm:ss",'PRC');

                if($download == true){//如果导出则导出全部内容
                    $this->_limit  = 100000;
                    $this->_offset = 0;
                }

                list($lists,$total,$total_amount) = $this->$method($condition);
                if($download == true){
                    if(method_exists($this, $export_method)){
                        $filename = $resource_type.'-report-';
                        $filename .= $start->i18nFormat("yyyyMMdd")."-".$end->i18nFormat("yyyyMMdd");
                        //获取下载文件表格的title定义
                        $fieldTitle = $this->$export_method();
                        $this->Excel->setSheetCloumnTitle($fieldTitle)
                            ->setSheetColumnBody($lists)
                            ->export($filename);
                    }
                }
                //页面ajax请求数据列表
                if($this->request->isAjax()){
                    $this->_pageList['total']   = $total;
                    $this->_pageList['rows']    = $lists;
                    echo json_encode($this->_pageList);
                    exit();
                }
                $this->set('lists',$lists);
            }else{
                throw new \Exception("the method {$method} is not exsits", 1);
            }

            $this->set('end', $end->i18nFormat("yyyy-MM-dd"));
            $this->set('start', $start->i18nFormat("yyyy-MM-dd"));
            $this->set('type', $resource_type);
            $this->set('total',$total);
            $this->set('total_amount',$total_amount);
            $this->set('department_id', $department_id);
            $this->set('resource_type_data', $resource_type_constant);
    }

    /**
     * 获取bs消费明细
     * @param  [array] $condition [公共筛选条件]
     * @return [array]            [lists=>列表数据,$total=>数据总条目数,$total_amount=>消费合计金额]
     */
    protected function _get_bs_lists($condition)
    {
        return $this->_get_service_lists($condition);
    }
    /**
     * 获取rds服务消费明细
     * @param  [array] $condition [公共筛选条件]
     * @return [array]            [lists=>列表数据,$total=>数据总条目数,$total_amount=>消费合计金额]
     */
    protected function _get_rds_lists($condition)
    {
        return $this->_get_service_lists($condition);
    }

    protected function _get_eip_lists($condition)
    {
        return $this->_get_ecs_lists($condition);
    }

    protected function _get_vfw_lists($condition)
    {
        return $this->_get_ecs_lists($condition);
    }

    protected function _get_firewall_lists($condition)
    {
        return $this->_get_ecs_lists($condition);
    }

    protected function _get_waf_lists($condition)
    {
        return $this->_get_ecs_lists($condition);
    }

    protected function _get_elb_lists($condition)
    {
        return $this->_get_ecs_lists($condition);
    }
    protected function _get_vpc_lists($condition)
    {
        return $this->_get_ecs_lists($condition);
    }

    protected function _get_disks_lists($condition)
    {
        return $this->_get_ecs_lists($condition);
    }

    protected function _get_mstorage_lists($condition)
    {
        return $this->_get_ecs_lists($condition);
    }

    protected function _get_vbrI_lists($condition)
    {
        $field = ['amount','name'=>'vbri.name','spec'=>"vbri.spec",'routerCode'=>"vbri.routerCode",'initiatingSideRouterInterfaceCode'=>'vbri.initiatingSideRouterInterfaceCode','acceptingSideRouterInterfaceCode'=>"vbri.acceptingSideRouterInterfaceCode",'buyer_name','price','market_price','bill_date','interval','charge_type'];

        $name = array_key_exists('name', $this->request->query) ? $this->request->query['name'] : '';
        $this->set('name',$name);

        $charge_type = array_key_exists('charge_type', $this->request->query) ? $this->request->query['charge_type'] : '';
        if($charge_type !='' && $charge_type !='undefined'){
            $condition['charge_type'] = $charge_type;
        }
        $this->set('charge_type',$charge_type);


        $bill_base_table = TableRegistry::get('BillBase');
        $query = $bill_base_table->find();
        $lists = $query->join([
            'vbri'=>[
                'table' =>'cp_bill_vbri',
                'type'  =>'LEFT',
                'conditions' =>'vbri.id = BillBase.resource_id'
            ]
        ])->select($field)->where($condition)->where(function($exp) use($name){
            if($name !="" && $name !='undefined'){
                $or_condition = $exp->or_(['vbri.name like'=>'%'.$name.'%']);
                return $exp->add($or_condition);
            }
            return $exp->and_(['1'=>'1']);
        })->order('bill_date desc')
            ->limit($this->_limit)->offset($this->_offset)->map(function($row){
                $row->bill_date = $row->bill_date->i18nFormat('yyyy-MM-dd HH:mm:ss');
                return $row;
            })->toArray();
        $total = $query->join([
            'vbri'=>[
                'table' =>'cp_bill_vbri',
                'type'  =>'LEFT',
                'conditions' =>'vbri.id = BillBase.resource_id'
            ]
        ])->select($field)->where($condition)->count();
        $result = $query->select(['total_amount'=>$query->func()->sum('amount')])->where($condition)->first();
        $total_amount = $result['total_amount'] >0 ? $result['total_amount'] : 0;
        return [$lists,$total,$total_amount];
    }

    /**
     * 获取Hive存储计费信息
     * @param  array $condition [description]
     * @return [type]            [description]
     */
    protected function _get_hive_lists($condition)
    {
        $field = ['amount','objnums'=>'items.objnums','MB'=>'items.MB','GB'=>'items.GB','create_time'=>'items.create_time','buyer_name','interval','charge_type','bill_date',"price"];

        $bill_base_table = TableRegistry::get('BillBase');
        $query = $bill_base_table->find();
        $lists = $query->join([
                'items'=>[
                    'table' =>'cp_hive_statistics_items',
                    'type'  =>'LEFT',
                    'conditions' =>'items.id = BillBase.resource_id'
                ]
            ])->select($field)->where($condition)->order('bill_date desc')->limit($this->_limit)->offset($this->_offset)
            ->map(function($row){
                $row->bill_date = $row->bill_date->i18nFormat('yyyy-MM-dd HH:mm:ss');
                return $row;
            })->toArray();

        $total = count($lists);
        $result = $query->select(['total_amount'=>$query->func()->sum('amount')])->where($condition)->first();
        $total_amount = $result['total_amount'] >0 ? $result['total_amount'] : 0;
        return [$lists,$total,$total_amount];
    }

    /**
     * 获取购买一次性计费的服务列表
     * @param  [array] $condition 
     * @return [array]            
     */
    protected function _get_service_lists($condition)
    {
        $field = ['amount','name'=>'bs.name','duration'=>'bs.duration','start_date'=>'bs.start_date','end_date'=>'bs.end_date','order_date'=>'bs.order_date','buyer_name','interval','charge_type'];

        $name = array_key_exists('name', $this->request->query) ? $this->request->query['name'] : '';
        if($name !=''){
            $condition['bs.name like'] = '%'.$name.'%';
        }
        $this->set('name',$name);


        $bill_base_table = TableRegistry::get('BillBase');
        $query = $bill_base_table->find();
        $lists = $query->join([
                'bs'=>[
                    'table' =>'cp_bill_service',
                    'type'  =>'LEFT',
                    'conditions' =>'bs.id = BillBase.resource_id'
                ]
            ])->select($field)->where($condition)->order('bill_date desc')->limit($this->_limit)->offset($this->_offset)->toArray();

        $total = count($lists);
        $result = $query->select(['total_amount'=>$query->func()->sum('amount')])->where($condition)->first();
        $total_amount = $result['total_amount'] >0 ? $result['total_amount'] : 0;
        return [$lists,$total,$total_amount];
    }
    
    /**
     * 获取ecs云主机消费明细
     * @param  [array] $condition [公共筛选条件]
     * @return [array]            [lists=>列表数据,$total=>数据总条目数,$total_amount=>消费合计金额]
     */
    protected function _get_ecs_lists($condition)
    {
        $field = ['bandwidth'=>'ecs.bandwidth','amount','name'=>'ecs.name','code'=>'ecs.code','start_date'=>'ecs.start_date','end_date'=>'ecs.end_date','order_date'=>'ecs.order_date','buyer_name','price','market_price','bill_date','interval','charge_type'];

        $name = array_key_exists('name', $this->request->query) ? $this->request->query['name'] : '';
        $this->set('name',$name);

        $charge_type = array_key_exists('charge_type', $this->request->query) ? $this->request->query['charge_type'] : '';
        if($charge_type !=''){
            $condition['charge_type'] = $charge_type;
        }
        $this->set('charge_type',$charge_type);


        $bill_base_table = TableRegistry::get('BillBase');
        $query = $bill_base_table->find();
        $lists = $query->join([
                'ecs'=>[
                    'table' =>'cp_bill_ecs',
                    'type'  =>'LEFT',
                    'conditions' =>'ecs.id = BillBase.resource_id'
                ]
            ])->select($field)->where($condition)->where(function($exp) use($name){
                if($name !=""){
                    $or_condition = $exp->or_(['name like'=>'%'.$name.'%']);
                    return $exp->or_(['code like' =>'%'.$name.'%'])
                            ->add($or_condition);
                }
                return $exp->and_(['1'=>'1']);
            })->order('bill_date desc')
            ->limit($this->_limit)->offset($this->_offset)->map(function($row){
                $row->bill_date = $row->bill_date->i18nFormat('yyyy-MM-dd HH:mm:ss');
                return $row;
            })->toArray();
        $total = $query->join([
                'ecs'=>[
                    'table' =>'cp_bill_ecs',
                    'type'  =>'LEFT',
                    'conditions' =>'ecs.id = BillBase.resource_id'
                ]
            ])->select($field)->where($condition)->count();
        $result = $query->select(['total_amount'=>$query->func()->sum('amount')])->where($condition)->first();
        $total_amount = $result['total_amount'] >0 ? $result['total_amount'] : 0;
        return [$lists,$total,$total_amount];
    }
    /**
     * 获取mpaas服务消费明细
     * @param  [array] $condition [公共筛选条件]
     * @return [array]            [lists=>列表数据,$total=>数据总条目数,$total_amount=>消费合计金额]
     */
    protected function _get_mpaas_lists($condition)
    {
        $field = ['interval','price','charge_type','amount','program_name'=>'mpaas.program_name','vendor_code'=>'mpaas.vendor_code','consumption_subjects'=>'mpaas.consumption_subjects','duration'=>'mpaas.duration','column_code'=>'mpaas.column_code'];
        
        //处理服务商参数和选择菜单枚举值
        $vendor = ['ArcSoft'=>'虹软','DaYang'=>'大洋','Sobey'=>'索贝'];
        $this->set('vendor',$vendor);
        $vendor_code = array_key_exists('vendor_code', $this->request->query) ? $this->request->query['vendor_code'] : '';
        if($vendor_code !=''){
            $condition['mpaas.vendor_code'] = $vendor_code;
        }
        $this->set('vendor_code',$vendor_code);

        //处理服务类型参数和选择菜单枚举值
        $consumption_subjects = ['迁移','技审','转码','合成'];
        $this->set('consumption_subjects',$consumption_subjects);
        $subject = array_key_exists('subject', $this->request->query) ? $this->request->query['subject'] : '';
        if($subject !=''){
            $condition['mpaas.consumption_subjects'] = $subject;
        }
        $this->set('consumption_subject',$subject);

        //处理栏目名查询参数
        $column = array_key_exists('column', $this->request->query) ? $this->request->query['column'] : '';
        if($column !=''){
            $condition['mpaas.column_code'] = $column;
        }
        $this->set('column',$column);

        //处理节目名查询参数
        $program_name = array_key_exists('program_name', $this->request->query) ? $this->request->query['program_name'] : '';
        if($program_name !=''){
            $condition['mpaas.program_name like'] = '%'.$program_name.'%';
        }
        $this->set('program_name',$program_name);

        $bill_base_table = TableRegistry::get('BillBase');
        $query = $bill_base_table->find();
        $lists = $query->select($field)->join([
                'mpaas'=>[
                    'table' =>'cp_mpaas_detail_account',
                    'type'  =>'LEFT',
                    'conditions' =>'mpaas.id = BillBase.resource_id'
                ]
            ])->where($condition)->order('bill_date desc')->limit($this->_limit)->offset($this->_offset)->map(function($row){
                $row['duration']  = ceil($row['duration']/$row->unit);//计费周期
                return $row;
            })->toArray();

        $total = count($lists);
        $result = $query->select(['total_amount'=>$query->func()->sum('amount')])->where($condition)->first();
        $total_amount = $result['total_amount'] >0 ? $result['total_amount'] : 0;
        $unittxt = empty($lists) ? '分钟' : $lists[0]->intervalTxt;
        $this->set('unittxt',$unittxt);
        return [$lists,$total,$total_amount];
    }

    /**
     * 获取citrix桌面工具消费明细
     * @param  [array] $condition [公共筛选条件]
     * @return [array]            [lists=>列表数据,$total=>数据总条目数,$total_amount=>消费合计金额]
     */
    protected function _get_citrix_lists($condition)
    {
        $field = ['name'=>'citrix.name','loginname'=>'citrix.loginname','logintime'=>'citrix.logintime','logoutime'=>'citrix.logoutime','duration'=>'citrix.duration','price','interval','bill_date','amount','charge_type','bill_date'];
        $name = array_key_exists('name', $this->request->query) ? $this->request->query['name'] : '';
        if($name !=''){
            $condition['citrix.name like'] = '%'.$name.'%';
        }
        
        $charge_type = array_key_exists('charge_type', $this->request->query) ? $this->request->query['charge_type'] : '';
        if($charge_type !=''){
            $condition['charge_type'] = $charge_type;
        }
        $this->set('charge_type',$charge_type);
        
        $bill_base_table = TableRegistry::get('BillBase');
        $query = $bill_base_table->find();
        
        $lists = $query->select($field)->join([
                'citrix'=>[
                    'table' => 'cp_bill_citrix',
                    'type'  => 'INNER',
                    'conditions' => 'citrix.id = BillBase.resource_id'
                ]
            ])->where($condition)->order('bill_date desc')->limit($this->_limit)->offset($this->_offset)
            ->map(function($row){
                $row->logintime = $row->logintime > 0 ? date('Y-m-d H:i:s',$row->logintime) : 0;
                $row->logoutime = $row->logoutime > 0 ? date('Y-m-d H:i:s',$row->logoutime) : 0;
                $row->bill_date = $row->bill_date->i18nFormat('yyyy-MM-dd HH:mm:ss');
                $duration  = ceil($row->duration/$row->unit);//计费周期
                $row->duration = $duration.$row->interval_txt;
                return $row;
            })->toArray();
        
        $total = $query->select($field)->join([
                'citrix'=>[
                    'table' => 'cp_bill_citrix',
                    'type'  => 'INNER',
                    'conditions' => 'citrix.id = BillBase.resource_id'
                ]
            ])->where($condition)->count();
        $result = $query->select(['total_amount'=>$query->func()->sum('amount')])->where($condition)->first();
        $total_amount = $result['total_amount'] >0 ? $result['total_amount'] : 0;
        
        return [$lists,$total,$total_amount];
    }

    /**
     *
     * @author : wangjc
     * @param : $department_id:部门id，$instance_id:硬件id,$type_id:服务类型id，$start:开始时间，$end:结束时间（格式为yyyy-mm-dd）            
     */
    public function detailData($department_id = 0, $type_id = 0, $instance_id = 0, $in_type = 0, $start = '', $end = '')
    {


        $limit = ! empty($this->request->query['limit']) ? $this->request->query['limit'] : 10;
        $offset = ! empty($this->request->query['offset']) ? $this->request->query['offset'] : 0;
        
        $this->paginate['limit'] = $limit;
        $this->paginate['page'] = $offset / $limit + 1;
        
        $charge_daily_table = TableRegistry::get('ChargeDaily');
        $where = '';
        if ($department_id != 0) { // 对部门筛选
            $where .= ' AND cp_charge_daily.dept_id = ' . $department_id;
        }
        
        $_1_where = '';
        $_2_where = '';
        if (is_numeric($instance_id)) { // 判断是否是数字
            
            if ($instance_id != 0) {
                if ($in_type != 3) {
                    $where .= ' AND cp_charge_daily.device_id = ' . $instance_id;
                    $_2_where = 'AND cp_charge_daily.device_id = ""';
                } elseif ($in_type == 3) {
                    $where .= ' AND cp_charge_daily.charge_body = ' . $instance_id;
                    $_1_where = 'AND cp_charge_daily.device_id = ""';
                }
            } else {
                if ($in_type == 3) {
                    $_1_where = 'AND cp_charge_daily.device_id = ""';
                } elseif ($in_type == 1 || $in_type == 2) {
                    $_2_where = 'AND cp_charge_daily.device_id = ""';
                }
            }
        } else {
            $_1_where = 'AND cp_charge_daily.device_id = ""';
            
            $where .= ' AND cp_charge_daily.charge_body = "' . $instance_id . '"';
        }
        if ($type_id != 0 && $type_id != - 1) { // 对服务类型筛选
            $where .= ' AND cp_charge_daily.type_id = ' . $type_id;
        }
        
        if (! empty($start)) { // 对开始时间筛选
            $start_time = strtotime($start);
            
            $where .= ' AND cp_charge_daily.billing_date >= "' . $start . '"';
        }
        if (! empty($end)) { // 对结束时间筛选
            $end_time = strtotime($end) + 86400;
            $where .= ' AND cp_charge_daily.billing_date <= "' . $end . '"';
        }
        
        $connection = ConnectionManager::get('default');
        $sql = '(SELECT cp_charge_daily.type_id, cp_charge_daily.service_name AS service_name, cp_charge_daily.device_name AS basic_name, ';
        $sql .= ' cp_charge_daily.charge_type AS charge_type, cp_charge_daily.billing_date AS billing_date, cp_charge_daily.daily_cost AS cost';
        $sql .= ' FROM cp_charge_daily';
        //$sql .= ' LEFT JOIN cp_service_type ON cp_service_type.type_id = cp_charge_daily.type_id';
        //$sql .= ' LEFT JOIN cp_instance_basic ON cp_charge_daily.device_id = cp_instance_basic.id';
        $sql .= ' WHERE (cp_charge_daily.charge_type = 1 OR cp_charge_daily.charge_type = 2)';
        $sql .= $where;
        $sql .= $_1_where;
        $sql .= ' ORDER BY cp_charge_daily.billing_date ASC';
        $sql .= ' ) ';
        $sql .= ' UNION ALL ( ';
        $sql .= ' SELECT cp_charge_daily.type_id, cp_charge_daily.service_name AS service_name, cp_accounts.loginname AS basic_name,';
        $sql .= ' cp_charge_daily.charge_type AS charge_type, cp_charge_daily.billing_date AS billing_date, SUM(cp_charge_daily.daily_cost) AS cost';
        $sql .= ' FROM cp_charge_daily';
        //$sql .= ' LEFT JOIN cp_service_type ON cp_service_type.type_id = cp_charge_daily.type_id';
        //$sql .= ' LEFT JOIN cp_instance_basic ON cp_charge_daily.device_id = cp_instance_basic.id';
        $sql .= ' LEFT JOIN cp_accounts ON cp_accounts.loginname = cp_charge_daily.charge_body';
        $sql .= ' WHERE cp_charge_daily.charge_type = 3';
        $sql .= $where;
        $sql .= $_2_where;
        $sql .= ' GROUP BY cp_charge_daily.charge_body, cp_charge_daily.type_id, cp_charge_daily.billing_date';
        $sql .= ' ) ';
        $sql_row = $sql . ' limit ' . $offset . ',' . $limit;
        
        $device_usedata_data = $connection->execute($sql_row)->fetchAll('assoc');
        
        $this->_pageList['total'] = $connection->execute($sql)->count();
        $this->_pageList['rows'] = $device_usedata_data;
        
        echo json_encode($this->_pageList);
        exit();
    }

    /**
     * 人员消费统计
     *
     * @author : wangjc
     * @param $department_id:部门id，$account_id: 人员id，$instance_id:硬件id,$type_id:服务类型id，$start:开始时间，$end:结束时间（格式为yyyy-mm-dd）            
     */
    public function personDetail($department_id = '', $account_id = '', $type_id = 0, $instance_id = 0, $start = 0, $end = 0)
    {
        $_pop = false;
        $_pop = $this->_checkPopedom('ccm_user_charge_person_detail'); // 检查权限
        if (! $_pop) {
            return $this->redirect('/console/');
        }
        $_time = time();
        if (empty($start)) {
            $_y = date("Y", $_time);
            $_m = date("m", $_time);
            $start = $_y . '-' . $_m . '-01';
        }
        $_start_time = strtotime($start);
        
        if (empty($end)) {
            $end = date("Y-m-d", $_time);
        }
        $_end_time = strtotime($end) + 86400;
        
        $departments_table = TableRegistry::get('Departments');
        $accounts_table = TableRegistry::get('Accounts');
        $service_type_table = TableRegistry::get('ServiceType');
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        
        // 筛选条件
        if ($department_id == '') {
            $department_id = $this->request->session()->read('Auth.User.department_id') ? $this->request->session()->read('Auth.User.department_id') : 0;
        }
        if ($account_id == '') {
            $account_id = $this->request->session()->read('Auth.User.id') ? $this->request->session()->read('Auth.User.id') : 0;
        }
        
        $_sys_pop = false;
        $_tenant_admin = false;
        $_sys_pop = $this->_checkPopedom('cmop_global_sys_admin'); // 检查系统管理员权限
        $_tenant_admin = $this->_checkPopedom('cmop_global_tenant_admin'); // 检查系统管理员权限
        
        if ($_sys_pop) { // 有系统管理员权限的可以查看不同部门的信息
            $departments_data = $departments_table->find()->toArray();
            $this->set('departments_data', $departments_data);
        }
        
        if (! $_sys_pop && $_tenant_admin) { // 只有租户管理员权限的可以查看不同部门的信息
            $department_id = $this->request->session()->read('Auth.User.department_id') ? $this->request->session()->read('Auth.User.department_id') : 0;
        }
        
        if (! $_sys_pop && ! $_tenant_admin) {
            $department_id = $this->request->session()->read('Auth.User.department_id') ? $this->request->session()->read('Auth.User.department_id') : 0;
            $account_id = $this->request->session()->read('Auth.User.id') ? $this->request->session()->read('Auth.User.id') : 0;
        }
        
        // 获取用户名称
        $account_name = $accounts_table->find()
            ->where([
            'id' => $account_id
        ])
            ->first();
        if (empty($account_name['username'])) {
            $account_name['username'] = '全部';
        }
        // 获取部门
        $department_name = $departments_table->find()
            ->where([
            'id' => $department_id
        ])
            ->first();
        
        if (empty($department_name['name'])) {
            $department_name['name'] = '全部';
        }
        $_where = ' WHERE cp_device_usedata.charge_type = 3';
        $where = array();
        if ($department_id != 0) {
            $where['department_id'] = $department_id;
            $_where .= ' AND cp_device_usedata.department_id = ' . $department_id;
        }
        $_instance_where = [];
        if ($type_id != 0) {
            $_where .= ' AND cp_device_usedata.type_id = ' . $type_id;
            $_instance_where['ServiceList.type_id'] = $type_id;
        }
        
        // 获取部门对应的人员账户信息
        $accounts_data = $accounts_table->find()
            ->select([
            'id',
            'username',
            'loginname'
        ])
            ->where($where)
            ->toArray();
        
        if($department_id != 0){
            $_ser_where['OR'] = [['department_id' => $department_id],['department_id ' => 0]];
        }else{
            $_ser_where['department_id'] = $department_id;
        }
        
        // 获取部门对应的服务信息
        $service_type_data = $service_type_table->find()
            ->where($_ser_where)
            ->toArray();
        
        // 获取部门对于的消费科目信息
        $instance_basic_data = $instance_basic_table->find()
            ->contain([
            'ServiceList'
        ])
            ->where([
            'ServiceList.service_id <>' => '',
            'code <>' => '',
            'type IN' => [
                'desktop'
            ]
        ])
            ->where($where)
            ->where($_instance_where)
            ->group([
            'InstanceBasic.id'
        ])
            ->toArray();
        
        // 拼接sql语句条件
        if ($account_id != 0) {
            $account_data = $accounts_table->find()
                ->where([
                'id' => $account_id
            ])
                ->first();
            $_where .= ' AND cp_device_usedata.loginname = "' . $account_data['loginname'] . '"';
        }
        
        if ($instance_id != 0) {
            $_where .= ' AND cp_device_usedata.device_id = ' . $instance_id;
        }
        if ($start != 0) {
            $_start_time = strtotime($start);
            $_where .= ' AND cp_device_usedata.begin_time >=' . $_start_time;
        }
        if ($end != 0) {
            $_end_time = strtotime($end) + 86400;
            $_where .= ' AND cp_device_usedata.end_time <' . $_end_time;
        }
        $_where .=' AND cp_device_usedata.is_compute = 1';
        $connection = ConnectionManager::get('default');
        $sql = 'SELECT SUM(cp_device_usedata.cost) AS cost ';
        $sql .= ' FROM cp_device_usedata';
//         $sql .= ' LEFT JOIN cp_service_list ON cp_service_list.basic_id = cp_device_usedata.device_id';
//         $sql .= ' LEFT JOIN cp_service_type ON cp_service_list.type_id = cp_service_type.type_id';
        $sql .= $_where;
        $device_usedata_data = $connection->execute($sql)->fetchAll('assoc');
        
        $sum = 0;
        if (!empty($device_usedata_data[0]['cost'])) {
            $sum = $device_usedata_data[0]['cost'];
        }
        
        $this->set('service_type_data', $service_type_data); // 部门对应的服务信息
        $this->set('instance_basic_data', $instance_basic_data); // 部门对于的消费科目信息
        $this->set('accounts_data', $accounts_data); // 部门对应的人员账户信息
        $this->set('account_name', $account_name); // 用户名称
        $this->set('department_name', $department_name); // 部门名称
        $this->set('department_id', $department_id); // 部门id
        $this->set('account_id', $account_id); // 用户id
        $this->set('instance_id', $instance_id); // 硬件id
        $this->set('start', $start); // 开始时间
        $this->set('end', $end); // 结束时间
        $this->set('sum', $sum); // 总费用
        $this->set('type_id', $type_id); // 服务类型id
    }

    /**
     *
     * @author wangjc
     * @param number $department_id
     *            部门id
     * @param number $account_id
     *            人员id
     * @param number $type_id
     *            服务类型id
     * @param number $instance_id
     *            消费科目id
     * @param number $start
     *            开始时间
     * @param number $end
     *            结束时间
     */
    public function personDetailData($department_id = 0, $account_id = 0, $type_id = 0, $instance_id = 0, $start = 0, $end = 0)
    {
        $accounts_table = TableRegistry::get('Accounts');
        $limit = ! empty($this->request->query['limit']) ? $this->request->query['limit'] : 10;
        $offset = ! empty($this->request->query['offset']) ? $this->request->query['offset'] : 0;
        
        $this->paginate['limit'] = $limit;
        $this->paginate['page'] = $offset / $limit + 1;
        $_where = ' WHERE cp_device_usedata.charge_type = 3';
        if ($department_id != 0) {
            $_where .= ' AND cp_device_usedata.department_id = ' . $department_id;
        }
        
        if ($account_id != 0) {
            $account_data = $accounts_table->find()
                ->where([
                'id' => $account_id
            ])
                ->first();
            $_where .= ' AND cp_device_usedata.loginname = "' . $account_data['loginname'] . '"';
        }
        if ($type_id != 0) {
            $_where .= ' AND cp_device_usedata.type_id = ' . $type_id;
        }
        if ($instance_id != 0) {
            $_where .= ' AND cp_device_usedata.device_id = ' . $instance_id;
        }
        if ($start != 0) {
            $_start_time = strtotime($start);
            $_where .= ' AND cp_device_usedata.begin_time >=' . $_start_time;
        }
        if ($end != 0) {
            $_end_time = strtotime($end) + 86400;
            $_where .= ' AND cp_device_usedata.end_time <=' . $_end_time;
        }
        $_where .=' AND cp_device_usedata.is_compute = 1';
        $connection = ConnectionManager::get('default');
        $sql = 'SELECT cp_device_usedata.loginname AS loginname, cp_device_usedata.username AS username, cp_departments.`name` AS department_name,';
        $sql .= ' cp_device_usedata.device_name AS instance_name, cp_device_usedata.service_name AS service_name, cp_device_usedata.begin_time AS start_time,';
        $sql .= ' cp_device_usedata.end_time AS end_time, cp_device_usedata.use_time AS use_time, cp_device_usedata.cost AS cost';
        
        $sql .= ' FROM cp_device_usedata';
//         $sql .= ' LEFT JOIN cp_service_list ON cp_service_list.basic_id = cp_device_usedata.device_id';
//         $sql .= ' LEFT JOIN cp_service_type ON cp_service_list.type_id = cp_service_type.type_id';
        $sql .= ' LEFT JOIN cp_departments ON cp_device_usedata.department_id = cp_departments.id';
//         $sql .= ' LEFT JOIN cp_instance_basic ON cp_instance_basic.id = cp_device_usedata.device_id';
        $sql .= $_where;
        $sql .= ' GROUP BY cp_device_usedata.id';
        $sql .= ' ORDER BY cp_device_usedata.begin_time DESC';
        
        $sql_row = $sql . ' limit ' . $offset . ',' . $limit;
        
        $device_usedata_data = $connection->execute($sql_row)->fetchAll('assoc');
        
        if (! empty($device_usedata_data)) {
            foreach ($device_usedata_data as $_u_data) {
                if (empty($_u_data['cost'])) { 
                    $_u_data['cost'] = 0;
                }
                $use_data[] = $_u_data;
            }
            $device_usedata_data = $use_data;
        } 
        
        $this->_pageList['total'] = $connection->execute($sql)->count();
        $this->_pageList['rows'] = $device_usedata_data;
        // var_dump($this->_pageList);exit;
        echo json_encode($this->_pageList);
        exit();
    }

    /**
     * 科目消费统计
     *
     * @author wangjc
     * @param unknown $start
     *            开始时间
     * @param unknown $end
     *            结束时间
     * @param unknown $device_id
     *            科目id
     * @return Ambigous <void, \Cake\Network\Response>
     */
    public function subjectDetail($start, $end, $charge_body, $type_id)
    {
        $_pop = false;
        $_pop = $this->_checkPopedom('ccm_user_charge_detail'); // 检查权限
        if (! $_pop) {
            return $this->redirect('/console/');
        }
        $_start_time = strtotime($start);
        $_end_time = strtotime($end);
        
        $service_type_table = TableRegistry::get('ServiceType');
        $charge_daily_table = TableRegistry::get('ChargeDaily');
        $instance_basic_table = TableRegistry::get('InstanceBasic');
        $departments_table = TableRegistry::get('Departments');
        $accounts_table = TableRegistry::get('Accounts');
        
        $connection = ConnectionManager::get('default');
        
        $sql = 'SELECT sum(cp_device_usedata.cost) AS cost,cp_device_usedata.service_name AS service_name';
        //$sql .= ' ,cp_instance_basic.`name` AS basic_name, cp_departments.`name` AS department_name';
        $sql .= ' FROM cp_device_usedata';
        //$sql .= ' LEFT JOIN cp_service_list ON cp_service_list.basic_id = cp_device_usedata.device_id';
        //$sql .= ' LEFT JOIN cp_service_type ON cp_service_list.type_id = cp_service_type.type_id';
        //$sql .= ' LEFT JOIN cp_charge_template ON cp_service_type.charge_type = cp_charge_template.id';
        //$sql .= ' LEFT JOIN cp_instance_basic ON cp_instance_basic.id = cp_device_usedata.device_id';
        //$sql .= ' LEFT JOIN cp_departments ON cp_departments.id = cp_instance_basic.department_id';
        $sql .= ' WHERE cp_device_usedata.loginname = "' . $charge_body . '"';
        $sql .= ' AND cp_device_usedata.type_id = ' . $type_id;
        $sql .= ' AND cp_device_usedata.begin_time >= ' . $_start_time;
        $sql .= ' AND cp_device_usedata.end_time <= ' . $_end_time;
        
        $device_usedata_data = $connection->execute($sql)->fetchAll('assoc');
        
        $sum = 0;
        foreach ($device_usedata_data as $data) {
            if (! empty($data['cost'])) {
                $sum += $data['cost'];
            }
        }
        
        $account_data = $accounts_table->find()->contain('Departments')->where(['loginname' => $charge_body])->first();
        $department_data = $account_data['department'];
        
        $this->set('department_name',$department_data['name']);
        $this->set('data', $device_usedata_data);
        $this->set('sum', $sum);
        $this->set('start', $start);
        $this->set('end', $end);
        $this->set('charge_body', $charge_body);
        $this->set('type_id', $type_id);
    }

    /**
     *
     * @author wangjc
     * @param unknown $start            
     * @param unknown $end            
     * @param unknown $device_id            
     */
    public function subjectDetailData($start, $end, $charge_body, $type_id)
    {
        $_start_time = strtotime($start);
        $_end_time = strtotime($end);
        
        $limit = ! empty($this->request->query['limit']) ? $this->request->query['limit'] : 10;
        $offset = ! empty($this->request->query['offset']) ? $this->request->query['offset'] : 0;
        
        $connection = ConnectionManager::get('default');
        
        $sql = 'SELECT cp_device_usedata.username AS username, cp_departments.`name` AS department_name, cp_device_usedata.begin_time AS start_time,';
        $sql .= ' cp_device_usedata.end_time AS end_time, cp_device_usedata.cost AS cost, cp_device_usedata.use_time AS use_time, cp_device_usedata.device_name AS basic_name';
        $sql .= ' FROM cp_device_usedata';
        //$sql .= ' LEFT JOIN cp_service_list ON cp_service_list.basic_id = cp_device_usedata.device_id';
        //$sql .= ' LEFT JOIN cp_service_type ON cp_service_list.type_id = cp_service_type.type_id';
        //$sql .= ' LEFT JOIN cp_charge_template ON cp_service_type.charge_type = cp_charge_template.id';
        //$sql .= ' LEFT JOIN cp_instance_basic ON cp_instance_basic.id = cp_device_usedata.device_id';
        $sql .= ' LEFT JOIN cp_departments ON cp_departments.id = cp_device_usedata.department_id';
        $sql .= ' WHERE cp_device_usedata.loginname = "' . $charge_body . '"';
        $sql .= ' AND cp_device_usedata.type_id = ' . $type_id;
        $sql .= ' AND cp_device_usedata.begin_time >= ' . $_start_time;
        $sql .= ' AND cp_device_usedata.end_time <= ' . $_end_time;
        
        $sql_row = $sql . ' limit ' . $offset . ',' . $limit;
        
        $device_usedata_data = $connection->execute($sql_row)->fetchAll('assoc');
        
        $device_data = array();
        if (! empty($device_usedata_data)) {
            foreach ($device_usedata_data as $data) {
                if (empty($data['cost']) ) {
                    $data['cost'] = 0;
                }
                $device_data[] = $data;
            }
            $device_usedata_data = $device_data;
        }
        
        $this->_pageList['total'] = $connection->execute($sql)->count();
        $this->_pageList['rows'] = $device_usedata_data;
        // var_dump($this->_pageList);exit;
        echo json_encode($this->_pageList);
        exit();
    }

    /**
     * 根据公式计算数值
     *
     * @param string $expression            
     * @param int $t            
     * @throws FatalErrorException
     */
    protected function _cal($expression = null, $t = 0)
    {
        $num = 0;
        if (! is_null($expression)) {
            $_expression = str_replace('T', $t, $expression);
            $count = null;
            try {
                @eval('$num = ' . $_expression . ';');
            } catch (\Exception $e) {
                throw new FatalErrorException($e->getMessage(), $e->getCode());
            }
        }
        return $num;
    }
}