<?php
/**
 * 运营管理中心，菜单管理
 * @author lan <[<email address>]>
 * Date: 2016/12/22
 */

namespace App\Controller\Admin;

use App\Controller\AdminController;
use App\Controller\SobeyController;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time as CakeTime;
use Cake\Core\Configure;
use App\Controller\Console\ChargeController;

class BillController extends AdminController
{

    public $paginate = [
        'limit' => 15
    ];

    public $_pageList = array(
        'total' => 0,
        'rows' => array()
    );

    private $_controllerDelegator ;

    public function initialize()
    {
        parent::initialize();
//        $checkPopedomlist = parent::checkPopedomlist('bgm_bill_dashborad');
//        if (! $checkPopedomlist) {
//            return $this->redirect('/admin/');
//        }
        $this->_controllerDelegator = new ChargeController($this->request,$this->response);
    }

    public function subject($department_id = '',$start = '', $end = '')
    {
        $this->_controllerDelegator->subject($department_id,$start,$end);
        $view = $this->_controllerDelegator->getView();

        $this->set('departments_data', $view->get("departments_data"));
        $this->set('departments_name', $view->get("departments_name"));
        $this->set('department_id', $view->get("department_id"));
        $this->set('sum_cost', $view->get("sum_cost"));
        $this->set('line_query', $view->get("line_query"));
        $this->set('query', $view->get("query"));
        $this->set('start', $view->get("start"));
        $this->set('end', $view->get("end"));
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
        $this->_controllerDelegator->subjectData($department_id,$start,$end);
        $view = $this->_controllerDelegator->getView();
        $this->createView('json');
        $this->set('total',$view->get('total'));
        $this->set('rows',$view->get('rows'));
        $this->set('_serialize',['total','rows']);
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
    public function detail($department_id = '',$resource_type = 'bs',$start = '',$end = '',$download = false)
    {

        try {
            
            $this->_controllerDelegator->detailForAdmin($department_id,$resource_type,$start,$end,$download);
            $view = $this->_controllerDelegator->getView();

            $template   = $resource_type."_detail";
            if($resource_type == 'mpaas'){
                $this->set('vendor_code',$view->get('vendor_code'));
                $this->set('consumption_subject',$view->get('consumption_subject'));
                $this->set('column',$view->get('column'));
                $this->set('program_name',$view->get('program_name'));
                $this->set('unittxt',$view->get('unittxt'));
            }
            
            $this->set('name',$view->get('name'));
            $this->set('charge_type',$view->get('charge_type'));
            $this->set('lists',$view->get('lists'));
            $this->set('end', $view->get('end'));
            $this->set('start', $view->get('start'));
            $this->set('type', $view->get('type'));
            $this->set('total',$view->get('total'));
            $this->set('total_amount',$view->get('total_amount'));
            $this->set('department_id', $view->get('department_id'));
            $this->set('departments_data', $view->get("departments_data"));
            $this->set('department_name', $view->get("department_name"));
            $this->set('resource_type_data', $view->get('resource_type_data'));
            
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
}