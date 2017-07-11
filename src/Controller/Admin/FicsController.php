<?php
/**
 * Created by PhpStorm.
 * User: kelly
 * Date: 2016/12/15
 * Time: 10:28
 */
namespace App\Controller\Admin;

use App\Controller\AdminController;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Composer\Autoload\ClassLoader;
use PHPExcel_IOFactory;
use Cake\Datasource\ConnectionManager;

// use App\Controller\PHPExcel;
// App::import('Vender', 'Vender/phpexcel/PHPExcel');
class FicsController extends AdminController
{
    public $_pageList = array(
        'total' => 0,
        'rows' => array(),
    );

    public $paginate = [
        'limit' => 15,
    ];

    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow('image');
        $this->loadComponent('Paginator');
        $this->loadComponent('RequestHandler');

    }
    public function index(){

    }

    public function upexcel()
    {
        if (isset($this->request->query['action']) && $this->request->query['action'] == 1) {
            $public = new PublicController();
            if (!empty($_FILES)) {

                $fics_extend_key = ['vol_name', 'total_cap', 'label', 'department_id', 'warn_cap', 'store_code', 'region_code', 'vol_type'];
                $fics_users_key = ['total_cap', 'name', 'password', 'department_id', 'warn_cap', 'store_code', 'region_code', 'storetype'];
                $store_user_key = ['vol_name', 'limit'];

                $loader = new ClassLoader();
                $loader->add('PHPExcel', ROOT . DS . 'vendor' . DS . 'phpexcel');
                $loader->register();
                $path = "excel/";
                // 得到上传的临时文件流
                $tempFile = $_FILES['userfile']['tmp_name'];

                // 允许的文件后缀
                $fileTypes = array(
                    'xls',
                    'xlsx',
                );

                // 得到文件原名
                $fileName = iconv("UTF-8", "GB2312", $_FILES["userfile"]["name"]);
                $fileParts = pathinfo($_FILES['userfile']['name']);

                // 最后保存服务器地址
                if (!is_dir($path)) {
                    mkdir($path);
                }
                $time = time();
                $name = $time . '.' . pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION);
                $count1=0;
                $count2=0;
                $count3=0;
                if (move_uploaded_file($tempFile, $path . $name)) {
                    if (pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION) == 'xls') {
                        $reader = PHPExcel_IOFactory::createReader('Excel5'); // use excel2007 for 2007 format
                    } else {
                        $reader = PHPExcel_IOFactory::createReader('Excel2007'); // use excel2007 for 2007 format
                    }
                    $PHPExcel = $reader->load($path . $name); // 载入excel文件
                    $sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
                    $highestRow = $sheet->getHighestRow(); // 取得总行数
                    $highestColumm = $sheet->getHighestColumn(); // 取得总列数

                    /** 循环读取每个单元格的数据 */
                    $ficsextend = TableRegistry::get('FicsExtend');
                    $ficsusers = TableRegistry::get('FicsUsers');
                    $storeuser = TableRegistry::get('StoreUserP');
                    $FicsRelationInfo = TableRegistry::get('FicsRelationInfo');
                    $arr_extend = array('A', 'B', 'C', 'G', 'H', 'I', 'J', 'K');
                    $arr_users = array('B', 'D', 'E', 'G', 'H', 'I', 'J', 'L');
                    $arr_store_user = array('A', 'F');

                    $ficsextend->deleteAll(array('vol_id >'=>0));
                    $ficsusers->deleteAll(array('userid >'=>0));
                    $storeuser->deleteAll(array('id >'=>0));
                    $FicsRelationInfo->deleteAll(array('id >'=>0));
                    for ($row = 2; $row <= $highestRow; $row++) {
//行数是以第1行开始
                        $fics_extend_data = array();
                        $fics_users_data = array();
                        $store_user_data = array();
                        $i = 0;
                        $j = 0;
                        $h = 0;
                        for ($column = 'A'; $column <= $highestColumm; $column++) {
//列数是以第0列开始
                            if (in_array($column, $arr_extend)) {
                                $fics_extend_data[$fics_extend_key[$i]] = $PHPExcel->getActiveSheet()->getCell("$column$row")->getValue();
                                $i++;
                            }
                            if (in_array($column, $arr_users)) {
                                $fics_users_data[$fics_users_key[$j]] = $PHPExcel->getActiveSheet()->getCell("$column$row")->getValue();
                                $j++;
                            }
                            if (in_array($column, $arr_store_user)) {
                                $store_user_data[$store_user_key[$h]] = $PHPExcel->getActiveSheet()->getCell("$column$row")->getValue();
                                $h++;
                            }
                        }
                        //保存到fics_extend
                        $save_extend = $ficsextend->newEntity();
                        $save_extend = $ficsextend->patchEntity($save_extend, $fics_extend_data);
                        $save_extend['create_time'] = $time;
                        $result = $ficsextend->save($save_extend);
                        if ($result) {
                            $count1+=1;
                        }
                        //保存到fics_users
                        $save_fics_users = $ficsusers->newEntity();
                        $save_fics_users = $ficsusers->patchEntity($save_fics_users, $fics_users_data);
                        $result2 = $ficsusers->save($save_fics_users);
                        if ($result2) {
                            $user_id = $result2->userid;
                            $count2+=1;
                        } else {
                            $user_id = 0;
                        }
                        //保存到store-user
                        if($store_user_data['limit']=='读'){
                            $store_user_data['limit']=0;
                        }elseif($store_user_data['limit']=='完全控制'){
                            $store_user_data['limit']=1;
                        } elseif($store_user_data['limit']=='禁止'){
                            $store_user_data['limit']=2;
                        }elseif($store_user_data['limit']=='读写'){
                            $store_user_data['limit']=4;
                        }
                        $save_store_user = $storeuser->newEntity();
                        $save_store_user = $storeuser->patchEntity($save_store_user, $store_user_data);
                        $save_store_user['user_id'] = $user_id;
                        $result3 = $storeuser->save($save_store_user);
                        if ($result3) {
                            $count3+=1;
                        }
                    }
                }
                $public->adminlog('Fics', '导入'.$count1.'条数据到fics_extend');
                $public->adminlog('Fics', '导入'.$count2.'条数据到到fics_users');
                $public->adminlog('Fics', '导入'.$count3.'条数据到store_user');
            $this->redirect(array('controller' => 'Fics', 'action' => 'upexcel'));
            }

        }
    }
}