        	<!-- <button class="btn btn-addition">订单打印</button>  -->
        
        <?php 
        
            $_popdom_info = $this->request->session()->read('Auth.User.popedomname');
        
        ?>
        <?php 
        //debug($_current_detail_info);
        //debug($_neighbors_detail_info);
        
            //未开始
            //重新生成 
            //通过|已通过
            //退回|已退回
            //已完成
            if (!is_null($_neighbors_detail_info['next'])){
                //该步骤没有被操作
                $_neighbors_detail_info_copy = $_neighbors_detail_info;
                //last_op上次操作
                $last_op_arr = ['passed'=>null,'reback'=>null];
                $last_op = null;
                
                $last_op_arr['passed'] = $_neighbors_detail_info_copy['passed']?array_pop($_neighbors_detail_info_copy['passed']):null;
                $last_op_arr['reback'] = $_neighbors_detail_info_copy['reback']?array_pop($_neighbors_detail_info_copy['reback']):null;
                
                $last_op = $last_op_arr['passed'];
                
                if (isset($last_op_arr['reback']['create_time'])&&$last_op_arr['reback']['create_time']>=$last_op_arr['passed']['create_time']){
                    $last_op = $last_op_arr['reback'];
                }
                //debug($last_op);
                //debug($_neighbors_detail_info);
                
                //根据 next 判断通过|已审核
                //根据pre判断 退回|已退回
                
                //先判断该步是否有权限
                if (!is_null($_neighbors_detail_info['next']['step_popedom_code'])&&in_array($_neighbors_detail_info['next']['step_popedom_code'], $_popdom_info) ||is_null($_neighbors_detail_info['next']['step_popedom_code'])){
                    //该步骤有权限判断，或者开放权限
                    //有该步骤操作权限
                    //debug($_neighbors_detail_info);
                    if (is_null($_orders_info['detail_id'])){
                    //if (is_null($_neighbors_detail_info['pre'])&&($last_op['auth_action']== '-1')){
                        //刚已经退至顶点
           ?>
           <button class="btn btn-xs btn-addition" disabled="disabled" >已结束</button><br/><br/>
           <?php 
                    }else{
           ?>
           <button bt="btn-workflow" class="btn btn-xs btn-addition"  order_id="<?= $_orders_info['id']; ?>" auth_action="1"  flow_detail_name="<?= $_neighbors_detail_info['next']['step_name']; ?>"  to_detail_id="<?= $_neighbors_detail_info['next']['id'] ?>" >通 过</button><br/><br/>
           <button bt="btn-workflow" class="btn btn-xs btn-addition"  order_id="<?= $_orders_info['id']; ?>" auth_action="-1"  flow_detail_name="<?= $_neighbors_detail_info['next']['step_name']; ?>" to_detail_id="<?= $_neighbors_detail_info['next']['id'] ?>" >退 回</button><br/><br/>
           <?php 
                    }
                }else{
                    //无该步骤操作权限，，判断之前步骤权限，判断是否是已审核状态
                    $_ops = array_unique(array_merge($_neighbors_detail_info['passed']?$_neighbors_detail_info['passed']:[],$_neighbors_detail_info['reback']?$_neighbors_detail_info['reback']:[]));
                    if ($_ops){
                        foreach ($_ops as $_key =>$_value){
                            if(in_array($_value['step_popedom_code'], $_popdom_info)){
            ?>
            <button class="btn btn-addition btn-xs" disabled="disabled" >已操作</button><br/><br/>
            <?php                 
                                break;
                            }
                        }
                    }
                }
                
                
            }else{
                //已完成 按钮
        ?>
        <button class="btn btn-xs btn-addition" disabled="disabled" >已完成</button><br/><br/>
        <?php 
            }
        ?>