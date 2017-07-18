<?php
/**
 * Created by PhpStorm.
 * User: feng
 * Date: 2017/7/17
 * Time: 17:07
 */

namespace App\Controller\Console;

use Cake\ORM\TableRegistry;

require_once ROOT . DS . 'vendor' . DS . 'phpqrcode' . DS . 'qrlib.php';

class QrcodeController extends ConsoleController
{

    public function index()
    {

//        $tempDir = EXAMPLE_TMP_SERVERPATH;
//
//        $codeContents = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Proin nibh augue, suscipit a';
//
//        // generating
//        \QRcode::png($codeContents, $tempDir.'006_L.png', QR_ECLEVEL_L);
//        \QRcode::png($codeContents, $tempDir.'006_M.png', QR_ECLEVEL_M);
//        \QRcode::png($codeContents, $tempDir.'006_Q.png', QR_ECLEVEL_Q);
//        \QRcode::png($codeContents, $tempDir.'006_H.png', QR_ECLEVEL_H);
//
//        // end displaying
//        echo '<img src="'.EXAMPLE_TMP_URLRELPATH.'006_L.png" />';
//        echo '<img src="'.EXAMPLE_TMP_URLRELPATH.'006_M.png" />';
//        echo '<img src="'.EXAMPLE_TMP_URLRELPATH.'006_Q.png" />';
//        echo '<img src="'.EXAMPLE_TMP_URLRELPATH.'006_H.png" />';

    }

    public function qrImage($assets_no = null)
    {
        $this->viewBuilder()->layout(null);
        $this->autoRender = false;
        $hardwareAssetsTable = TableRegistry::get('HardwareAssets');
        $conditions['assets_no'] = $assets_no;
        $entity = $hardwareAssetsTable->find()->where($conditions)->first();
        $this->response->type('image/png');
        $str = '机架编号：4343434  品牌型号：34234ddkj  资产编号：34234ddkj 详情链接：http://local.uniop.com/console/ecs/detail/sgd3de4ee';
        \QRcode::png($str);
    }
}