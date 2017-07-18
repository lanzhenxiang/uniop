<?php
/**
* 物理资产更换记录
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
use Cake\Filesystem\File;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use App\Model\Table\HardwareAssetsTable;
use Cake\I18n\Time;


class HardwareInsteadController extends ConsoleController
{

    public function initialize()
    {
        parent::initialize();
    }


    /**
     * 添加服务器记录
     */
    public function add()
    {
        if($this->request->is('post')){
            $data = $this->request->data;
            $hardwareInsteadTable = TableRegistry::get('HardwareInstead');
            $hardwareInstead = $hardwareInsteadTable->newEntity($data);
            if(!$hardwareInsteadTable->save($hardwareInstead)) {
                $code = -1;
                $msg = '保存失败';
            }else{
                $code = 0;
                $msg = '保存成功';
            }
            $this->viewBuilder()->className('json');
        }else{
            $code = -1;
            $msg = '请求不正确';
        }

        $this->set(compact(['code','msg']));
        $this->set('_serialize', ['code','msg']);
    }

    /**
     * 删除服务器记录
     */
    public function del()
    {
        if($this->request->is('post')){
            $hardwareInsteadTable = TableRegistry::get('HardwareInstead');
            if($this->request->data['id'] > 0){
                $conditions['id'] = $this->request->data['id'];
                $success = $hardwareInsteadTable->deleteAll($conditions);
                if($success){
                    $code = "0";
                    $msg = "删除成功";
                }else{
                    $code = "-1";
                    $msg = "删除失败";
                }
            }else{
                $code = "-2";
                $msg = "参数错误";
            }
        }else{
            $code = "-1";
            $msg = "请求错误";
        }

        $this->viewBuilder()->className('json');
        $this->set(compact(['code','msg']));
        $this->set('_serialize', ['code','msg']);
    }

    public function edit($id = null)
    {

        $hardwareInsteadTable = TableRegistry::get('HardwareInstead');

        if($this->request->is('post')){
            $entity = $hardwareInsteadTable->get($this->request->data['id']);
            $hardwareInsteadTable->patchEntity($entity,$this->request->data);
            if($hardwareInsteadTable->save($entity)){
                $code = '0';
                $msg = '修改成功';
            }else{
                $code = '-1';
                $msg = '修改失败';
            }
            $this->viewBuilder()->className('json');
            $this->set(compact(['code','msg']));
            $this->set('_serialize', ['code','msg']);
        }else{
            $entity =$hardwareInsteadTable->find()->where(["HardwareInstead.id"=>$id])->contain(['HardwareAssets'])->first();
            $this->set('instead',$entity);
        }
    }
}