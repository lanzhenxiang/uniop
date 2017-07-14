<?php
/**
* 物理资产
*
*
* @author lanzhenxiang<lanzhenxiang@gmail.com>
* @date  2017年7月11日下午3:03:33
* @source HardwareController.php
* @version 1.0.0
* @copyright  Copyright 2017 sobey.com
*/


namespace App\Controller\Console;

use App\Controller\Console\ConsoleController;
use Aura\Intl\Exception;
use Cake\Filesystem\File;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\Table;
use Cake\View\Exception\MissingTemplateException;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Network\Http\Client;
use Cake\Error\FatalErrorException;
use PhpParser\Node\Stmt\Switch_;
use Cake\Datasource\ConnectionManager;
use App\Model\Table\HardwareAssetsTable;
use Cake\I18n\Time;
use Cake\Filesystem\Folder;

require_once ROOT . DS . 'vendor' . DS . 'phpexcel' . DS . 'PHPExcel.php';
require_once ROOT . DS . 'vendor' . DS . 'phpexcel' . DS . 'PHPExcel' . DS .'IOFactory.php';


class EcsController extends ConsoleController
{

    protected $_pageList = array(
        'total' => 0,
        'rows' => array(),
    );

    public function initialize()
    {
        parent::initialize();
        parent::left('network');//树形图导航

        $this->loadComponent('Excel');

    }
    private $_popedomName = array(
        'ecs' => 'ccm_ps_hosts',
        'switch' => 'ccm_ps_disks',
        'firewall' => 'ccm_ps_images',
        'storage' => 'ccm_ps_routers',
        'slb' => 'ccm_ps_subnets',
        'other' => 'ccm_ps_load_banlance',
        'eip' => 'ccm_ps_eip',
        'vpc' => 'ccm_ps_vpc',
        'vpx' => 'ccf_vpx',
        'server' => 'ccm_sm_MPC_Dispatch',
        'EipbHosts' => 'ccf_eip_alloc_hosts',
        'EipbElb'=>'ccf_eip_alloc_banlance',
        'Elblisten'=>'ccf_load_banlance_configure',
        'fics' => 'ccm_ps_fics',
        'settinglist' => 'ccm_ps_fics_settinglist',
        'ficsHosts' => 'ccm_ps_fics_hosts'
    );
    private $_addPopedomName = array(
        'hosts' => 'ccf_host_new',
        'hostDetail' => 'ccm_ps_hosts',
        'router' => 'ccf_router_new',
        'subnet' => 'ccf_subnet_new',
        'elb' => 'ccf_load_banlance_new',
        'eip' => 'ccf_eip_new',
        'fics' =>'ccf_fics_new',
    );
    private function _checkPopedom($param)
    {
        $popedom = parent::checkConsolePopedom($param);
        return $popedom;
    }

    private function _check_popedomlist($type){
        $subject_array = ['ecs','switch','firewall','slb','storage','other'];
        $check_vale = '';
        foreach ($subject_array as $key => $value) {
            if($type == 'list'){
                $popedomName = $this->_popedomName[$value];
            }
            if (! empty($popedomName)) {
                $check = $this->_checkPopedom($popedomName);
                if($check) {
                    $check_vale = $value;
                    break;
                }
            }
        }
        return $check_vale;
    }


    public function index()
    {

    }

    public function lists()
    {

        $limit = $this->request->query('limit');
        $offset = $this->request->query('offset');

        $where['type'] = HardwareAssetsTable::ASSET_ECS;
        $hardwareAssetsTable = TableRegistry::get('HardwareAssets');
        $query = $hardwareAssetsTable->find()->contain(['HardwareAssetsEcs'])->where($where)->offset($offset)->limit($limit);

        $pageList['total'] = $query->count();
        $pageList['rows'] = $query->toArray();
        $this->viewBuilder()->className('json');
        $this->set(compact(['pageList']));
        $this->set('_serialize', 'pageList');
    }

    public function detail($assets_no)
    {
        $hardwareAssetsTable = TableRegistry::get('HardwareAssets');
        $where['assets_no'] = $assets_no;
        $entity = $hardwareAssetsTable->find()->contain(['HardwareAssetsEcs'])->where($where)->first();
        $this->set('ecs',$entity);
    }

    /**
     * 导入Excel文件
     */
    public function importExcel()
    {
        if($this->request->is('post')){
            if($fileName = $this->_uploadFile($this->request->data)) {
                if ($fileName != '') {

                    $inputFileType = \PHPExcel_IOFactory::identify($fileName);
                    $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
                    $objReader->setReadDataOnly(true);
                    $objPHPExcel = $objReader->load($fileName);
                    $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
                    $highestRow = $objWorksheet->getHighestRow();

                    for ($row =2; $row <= $highestRow; ++$row) {

                        $data['assets_no'] = $objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
                        $data['SN'] = $objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
                        $data['manufacturer'] = $objWorksheet->getCellByColumnAndRow(2, $row)->getValue();

                        $data['hardware_assets_ec']['cpu'] = $objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
                        $data['hardware_assets_ec']['memory'] = $objWorksheet->getCellByColumnAndRow(4, $row)->getValue();
                        $data['hardware_assets_ec']['disks'] = $objWorksheet->getCellByColumnAndRow(5, $row)->getValue();
                        $data['hardware_assets_ec']['network'] = $objWorksheet->getCellByColumnAndRow(6, $row)->getValue();
                        $data['hardware_assets_ec']['gpu'] = $objWorksheet->getCellByColumnAndRow(7, $row)->getValue();
                        $data['IP'] = $objWorksheet->getCellByColumnAndRow(8, $row)->getValue();
                        $data['hardware_assets_ec']['EIP'] = $objWorksheet->getCellByColumnAndRow(9, $row)->getValue();

                        $data['status'] = $objWorksheet->getCellByColumnAndRow(10, $row)->getValue();
                        $data['location'] = $objWorksheet->getCellByColumnAndRow(11, $row)->getValue();
                        $data['cabinet_no'] = $objWorksheet->getCellByColumnAndRow(12, $row)->getValue();
                        $data['department'] = $objWorksheet->getCellByColumnAndRow(13, $row)->getValue();
                        $data['manager'] = $objWorksheet->getCellByColumnAndRow(14, $row)->getValue();
                        $data['buy_date'] = strtotime($objWorksheet->getCellByColumnAndRow(15, $row)->getValue());
                        $data['warranty'] = $objWorksheet->getCellByColumnAndRow(16, $row)->getValue();
                        $data['updated_by'] = $objWorksheet->getCellByColumnAndRow(17, $row)->getValue();
                        $data['type'] = HardwareAssetsTable::ASSET_ECS;
                        $resultarray[$row - 2] = $data;
                    }
                    try{
                        $hardwareAssetsTable = TableRegistry::get('HardwareAssets');
                        $entities = $hardwareAssetsTable->newEntities($resultarray,[
                            'associated' => ['HardwareAssetsEcs']
                        ]);
                        if($hardwareAssetsTable->saveMany($entities)){
                            $this->Flash->success("导入数据成功");
                            $this->redirect(['prefix'=>'console','controller'=>'ecs']);
                        }
                        //删除上传的临时文件
                        $file = new File($fileName);
                        $file->delete();
                    }catch (\Exception $e){
                        $errorMsg = $e->getMessage();
                        $this->Flash->error($errorMsg);
                    }
                }
            }
        }
    }

    /**
     * 上传文件
     * @param $data
     * @return string
     */
    protected function _uploadFile($data)
    {
        if (!empty($data['file']['name'] )) {
            $tmp_file = $data['file']['tmp_name'];
            $file_types = explode(".", $data['file']['name']);
            $extension = $file_types [count($file_types) - 1];
            /*判别是不是.xls文件，判别是不是excel文件*/
            if (strtolower($extension) == "xls" || strtolower($extension) == "xlsx") {

                $fileTmpPath = TMP . DS . 'Excel';
                $folder = new Folder();
                if (!is_dir($fileTmpPath) ) {
                    if(!$folder->create($fileTmpPath, 755)){
                        $this->Flash->error('文件临时目录创建失败！');
                        return false;
                    }
                }
                $filename = md5(time() . mt_rand(10, 99)) . "." . $extension; //新图片名称
                $newFile = $fileTmpPath . DS . $filename;
                //要生成的图片名字
                if (move_uploaded_file($tmp_file, $newFile)) {
                    return $newFile;
                }
                $this->Flash->error('文件上传失败');
            } else {
                $this->Flash->error('不是Excel文件，重新上传');
            }
        }else{
            $this->Flash->error('请选择上传的文件');
        }
        return false;
    }


    /**
     * 导出Excel文件
     */
    public function exportExcel()
    {
        $timeNow = new Time(new \DateTime("NOW"));
        $filename = 'Hardware-ECS-';
        $filename .= $timeNow->i18nFormat("yyyyMMdd");

        $hardwareAssetsTable = TableRegistry::get('HardwareAssets');
        $where['type'] = HardwareAssetsTable::ASSET_ECS;

        $fields = [
            "cpu"   =>"HardwareAssetsEcs.cpu",
            "disks" =>"HardwareAssetsEcs.disks",
            "network" =>"HardwareAssetsEcs.network",
            "gpu"   =>"HardwareAssetsEcs.gpu",
            "memory" =>"HardwareAssetsEcs.memory",
            "EIP"   =>"HardwareAssetsEcs.EIP"
        ];

        $lists = $hardwareAssetsTable->find()->select($fields)->autoFields(true)->contain(['HardwareAssetsEcs'])->where($where)->toArray();
        //获取下载文件表格的title定义
        $fieldTitle = $this->_getExcelSheetCloumnTitle();
        $this->Excel->setSheetCloumnTitle($fieldTitle)
            ->setSheetColumnBody($lists)
            ->export($filename);
    }

    /**
     * 定义导出的Excel文件 行标题
     * @return array
     */
    protected function _getExcelSheetCloumnTitle()
    {
        return [
            'assets_no'         => '资产编号',
            'SN'                => 'SN',
            'manufacturer'      => '品牌型号',
            'IP'                => 'IP',
            'status'            => '状态',
            'location'          => '位置',
            'cabinet_no'        => '机架编号',
            'department'        => '部门',
            'buy_date'          => '购买日期',
            'manager'           => '所属人员',
            'cpu'               => 'CPU',
            'memory'            => '内存',
            'disks'             => '硬盘',
            'gpu'               => 'GPU',
            'network'           => '网卡',
            'EIP'               => 'EIP'
        ];
    }

    /**
     * 添加服务器记录
     */
    public function add()
    {
        if($this->request->is('post')){
            $data = $this->request->data;

            $hardwareAssetsTable = TableRegistry::get('HardwareAssets');
            $data['type'] = HardwareAssetsTable::ASSET_ECS;//服务器类型
            $hardwareAssets = $hardwareAssetsTable->newEntity($data,[
                'associated' => ['HardwareAssetsEcs']
            ]);
            if(!$hardwareAssetsTable->save($hardwareAssets)) {
                $code = -1;
                $msg = '保存失败';
            }else{
                $code = 0;
                $msg = '保存成功';
            }
            $this->viewBuilder()->className('json');
            $this->set(compact(['code','msg']));
            $this->set('_serialize', ['code','msg']);
        }
    }

    /**
     * 删除服务器记录
     */
    public function del()
    {
        if($this->request->is('post')){
            $data = $this->request->data;
            $hardwareAssetsTable = TableRegistry::get('HardwareAssets');
            $ids = explode(",",rtrim($data['ids'],","));
            $conditions['id in'] = $ids;
            $entities = $hardwareAssetsTable->find()->where($conditions)->toArray();
            foreach ($entities as $entity){
                $success = $hardwareAssetsTable->delete($entity);
                if(!$success){
                    break;
                }
            }
            if($success){
                $code = "1";
                $msg = "删除成功";
            }else{
                $code = "0";
                $msg = "删除失败";
            }
        }else{
            $code = "-1";
            $msg = "请求错误";
        }

        $this->viewBuilder()->className('json');
        $this->set(compact(['code','msg']));
        $this->set('_serialize', ['code','msg']);
    }
}