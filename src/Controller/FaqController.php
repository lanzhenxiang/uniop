<?php
/**
* 帮助中心
*
* @file: FaqController.php
* @date: 2016年3月7日 下午3:03:37
* @author: xingshanghe
* @email: xingshanghe@icloud.com
* @copyright poplus.com
*
*/


namespace App\Controller;

use App\Controller\AccountsController;
use App\Controller\HomeController as Home;
use Cake\ORM\TableRegistry;


class FaqController extends AccountsController
{

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
        $popedom = parent::checkPopedomlist($param);
        return $popedom;
    }

    /**
     * 云桌面
     */
    public function desktop()
    {

        $_home = new Home();

        $this->set('good_category',$_home->getCategoeyGoodsData());
    }


    public  function check($desktop_name = ''){

        $department_id = $this->request->session()->read('Auth.User.department_id') ? $this->request->session()->read('Auth.User.department_id') : 0;

        $_pop = false;
        $_pop = $this->_checkPopedom('cmop_global_sys_admin'); // 检查权限
        $where = array();
        if (!$_pop) {
           $where['department_id'] = $department_id;
        }

        $message = array('code'=>1,'msg'=>'检测失败');
        $instanc_table = TableRegistry::get('InstanceBasic');
        $desktop_data = $instanc_table->find()->where(['name'=>$desktop_name,'type'=>'desktop'])->where($where)->first();
        if (! empty($desktop_data)) {
            if (! empty($desktop_data['vpc'])) {

                $vpc_data = $instanc_table->find()
                    ->where(['code' => $desktop_data['vpc']])
                    ->contain('VpcExtend')
                    ->first();

                if (! empty($vpc_data['vpc_extend']['desktop_server_url'])) {
                    $message = array(
                        'code' => 0,
                        'msg' => '检测成功',
                        'data' => $vpc_data['vpc_extend']['desktop_server_url']
                    );
                }
            }
        } else {
            $message = array('code'=>1,'msg'=>'没有检查到云桌面');
        }

        echo json_encode($message);exit();
        $this->lauout = 'ajax';
    }

}