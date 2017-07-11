<?php
/**
* 工作流 cell包含  审核，退回，以及弹出窗口和历史，
*
* @file: WorkflowCell.php
* @date: 2016年1月21日 下午2:37:39
* @author: xingshanghe
* @email: xingshanghe@icloud.com
* @copyright poplus.com
*
*/


namespace App\View\Cell;
use Cake\View\Cell;
use Cake\Cache\Cache;
use Cake\Core\Configure;

/**
 * Workflow cell
 */
class WorkflowCell extends Cell
{



    /**
     * 工作流 顶部
     *
     * @param integer $order_id
     * @param integer $user_id
     *
     * 根据状态生成，包含按钮和弹窗
     *
     * ==========
     * 已审核|已完成
     * ==========
     * 通过 | 退回
     *
     */
    public function top($order_id,$user_id,$orders_info)
    {

        //获取当前流程步骤id
        $_current_trace = $this->_getCurrentTrace($order_id);

        $_detail_id = $_current_trace['flow_detail_id'];
        $_current_detail_info = $this->_getDetail($_detail_id);
        $_flow_details_info = $this->_getFlowDetail($order_id);

        $_trace = $this->_getTrace($order_id);
        // debug($_flow_details_info);die();
        $_neighbors_detail_info = $this->_getNeighbors($_current_detail_info,$_flow_details_info,$_current_trace,$_trace);

        // debug($_neighbors_detail_info);

        $this->set('_current_trace',$_current_trace);
        $this->set('_current_detail_info',$_current_detail_info);
        $this->set('_neighbors_detail_info',$_neighbors_detail_info);

        $this->set('_flow_details_info',$_flow_details_info);
        //$this->set('_popdom_info',$this->_getPopdomInfo($user_id));//从session中读取
        $this->set('_orders_info',$orders_info);
        $this->set('_trace_info',$this->_getTrace($order_id));

        //_getNeighbors
    }


    public function listBtn($order_id,$user_id,$orders_info)
    {

        //获取当前流程步骤id
        $_current_trace = $this->_getCurrentTrace($order_id);

        $_detail_id = $_current_trace['flow_detail_id'];
        $_current_detail_info = $this->_getDetail($_detail_id);
        $_flow_details_info = $this->_getFlowDetail($order_id);

        $_trace = $this->_getTrace($order_id);

        $_neighbors_detail_info = $this->_getNeighbors($_current_detail_info,$_flow_details_info,$_current_trace,$_trace);

        // debug($_neighbors_detail_info);

        $this->set('_current_trace',$_current_trace);
        $this->set('_current_detail_info',$_current_detail_info);
        $this->set('_neighbors_detail_info',$_neighbors_detail_info);

        $this->set('_flow_details_info',$_flow_details_info);
        //$this->set('_popdom_info',$this->_getPopdomInfo($user_id));//从session中读取
        $this->set('_orders_info',$orders_info);
        $this->set('_trace_info',$this->_getTrace($order_id));

        //_getNeighbors
    }



    protected function _getDetail( $id ) {
        $this->loadModel('WorkflowDetail');
        return $this->WorkflowDetail->find()->where(['id'=> $id])->first();
    }


    protected function _getPopdomInfo ($user_id) {
        return [];
    }

    /**
     * 实现流程cell
     *
     * @param integer $order_id
     */
    public function flow($order_id) {
        $_current_trace = $this->_getCurrentTrace($order_id);
        $_detail_id = $_current_trace['flow_detail_id'];
        $_current_detail_info = $this->_getDetail($_detail_id);
        $_flow_details_info = $this->_getFlowDetail($order_id);

        $_neighbors_detail_info = $this->_getNeighbors($_current_detail_info,$_flow_details_info,$_current_trace);
        $this->set('_flow_info',$_flow_details_info);
        $this->set('_neighbors_detail_info',$_neighbors_detail_info);
        $this->set('_trace_info',$this->_getTrace($order_id));
    }

    /**
     * 获取 上一步和下一步
     * @param array|object $current
     * @param array $flow
     * @param array 当前所走流程
     */
    private function _getNeighbors($current,$flow,$_current_trace,$_trace){

        if (!empty($flow)){
            $_result = [
                'pre'=>null,
                'next'=>null,
                'passed'=>null,
                'reback'=>null,
            ];
            $flow_values = array_values($flow);
            foreach ($flow_values as $_key => $_value){
                //$_result['passed'][] = $_value;


                if ($current->id == $_value->id){
                    if (isset($flow_values[$_key-1])){
                        $_result['pre'] = $flow_values[$_key-1];
                    }
                    if (isset($flow_values[$_key+1])){
                        $_result['next'] = $flow_values[$_key+1];
                    }
                    break;
                }
            }
            //$_result['passed'] =
            foreach ($_trace as $_key => $_value){
                if ($_value['auth_action'] == -1){
                    $_result['reback'][] = $_value;
                }else{
                    $_result['passed'][] = $_value;
                }
            }
            /*
            if (!is_null($_current_trace['auth_action'] )){
                if ($_current_trace['auth_action'] == -1){
                    if (!empty($_result['passed'])){
                        $_result['reback'] = array_pop($_result['passed']);
                    }
                }
            }else{
                $_result['passed'] = null;
            }
            */

            return $_result;
        }else{
            return false;
        }
    }




    /**
     * 获取全部流程
     * @param unknown $order_id
     * @return unknown|boolean
     */
    protected function _getFlowDetail($order_id) {
        //加载WorkflowDetail模型类
        $this->loadModel('WorkflowDetail');
        //根据order_id查询该订单所属流程第一步flow_id
        // debug($order_id);die();
        // $flow_id = $this->_getFlowIdByOrderId($order_id);
         $this->loadModel('Orders');
         $flow_id = $this->Orders->find()->where(['id'=>$order_id])->select(['id','flow_id'])->first()->flow_id;
        //设置behaviors条件
        $this->WorkflowDetail->behaviors()->Tree->config('scope',['flow_id'=>$flow_id]);

        //查找开始节点
        $_node_start = $this->WorkflowDetail->find()->where(['flow_id'=>$flow_id,'parent_id'=>0])->first();

        if (!empty($_node_start)){

            //增加缓存
            return Cache::remember('workflow_flow_order_'.$order_id, function () use ($order_id,$_node_start){
                $flow = $this->WorkflowDetail->find('children',['for'=>$_node_start['id']])->toArray();

                if (empty($flow)){
                    //TODO 错误日志
                }
                array_unshift($flow, $_node_start);
                $flow_format = [];
                foreach ($flow as $value){
                    $flow_format[$value['id']] = $value;
                }

                return $flow_format;
            },'long');

        }else{
            //TODO 错误日志
            return false;
        }
    }


    /**
     * 实现流程跟踪cell
     *
     * @param integer $order_id
     */
    public function trace($order_id) {
        $this->set('_flow_details_info',$this->_getFlowDetail($order_id));
        $this->set('_trace_info',$this->_getTrace($order_id));
    }

    /**
     * 获取当前流程跟踪列表
     * @param integer $order_id
     */
    protected function _getTrace($order_id){
        //加载WorkflowDetail模型类
        $this->loadModel('OrdersProcessFlow');
        //$flow_id = $this->_getFlowIdByOrderId($order_id);
        return $this->OrdersProcessFlow->find()->join([
                'accounts'=>[
                    'table'=>'cp_accounts',
                    'type'=>'LEFT',
                    'conditions' =>'accounts.id = OrdersProcessFlow.user_id'
                ],
                'dept'=>[
                    'table'=>'cp_departments',
                    'type'=>'LEFT',
                    'conditions'=> 'dept.id = accounts.department_id'
                ]
            ])->where(['order_id'=>$order_id])->autoFields(true)->select(['department_name'=>'dept.name'])->toArray();
    }

    public function test( $order_id ){
        debug($this->_getCurrentTrace($order_id));
    }

    /**
     * 获取当前流程
     *
     * @param integer $order_id
     */
    protected function _getCurrentTrace( $order_id ) {
        //加载WorkflowDetail模型类
        $this->loadModel('OrdersProcessFlow');

        $process_flow = $this->OrdersProcessFlow->find()->where(['order_id'=>$order_id])->toArray();

        $process_flow_format = [];
        foreach ($process_flow as $_key => $_value){
            if ($_value['auth_action'] != -1){
                $process_flow_format[] = $_value;
            }else{
                if(count($process_flow_format)>1){//处理第一步退回
                    array_pop($process_flow_format);
                }
            }
        }
        return array_pop($process_flow_format);
    }


    /**
     * 根据订单号获取flow_id
     * @param integer $order_id
     */
    protected function _getFlowIdByOrderId( $order_id ) {
        //加载order模型类
        //order_id=>good_id=>flow_id
        $_good_id = $this->_getGoodIdByOrderId( $order_id );
        return $this->_getFlowIdByGoodId( $_good_id );
    }

    /**
     * 通过商品id获取流程id
     * @param integer $good_id
     */
    protected function _getFlowIdByGoodId( $good_id ){
        //good_id => flow_id
        //加载模型类
        $this->loadModel('Goods');
        //查询数据
        $_result = $this->Goods->find()->where(['id'=>$good_id])->select(['id','flow_id'])->first();
        return isset($_result['flow_id'])?$_result['flow_id']:false;
    }

    /**
     * 通过订单id获取商品id
     * @param integer $order_id
     */
    protected function _getGoodIdByOrderId( $order_id ) {
        //order_id=>good_id

        //加载模型类
        $this->loadModel('OrdersGoods');
        //查询数据
        $_result = $this->OrdersGoods->find()->where(['order_id'=>$order_id])->select(['order_id','good_id'])->first();
        return isset($_result['good_id'])?$_result['good_id']:false;
    }


}
