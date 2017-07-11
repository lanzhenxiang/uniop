<!-- 根据 _detail_info 和 _popdom_info -->
<div class="panel panel-default">
	<div class="order-crumb panel-body clearfix">
        <div class="order-crumb-info pull-left">
        	<dl>
        		<dt>订单号 : </dt>
        		<dd><?= $_orders_info['number'] ?></dd>
        		<dt>状态 : </dt>
        		<?php 
        		  if ($_orders_info['status'] == -1 || $_orders_info['detail_id'] == 0){
        		?>
        		<dd class="text-danger">已结束</dd>
        		<?php       
        		  }else{
        		      if (!is_null($_neighbors_detail_info['next'])){
                    if($_orders_info['is_back'] == 1){ ?>
                      <dd class="text-danger">已结束</dd>
                    <?php }else{ ?>
                      <dd class="text-success">等待<?=$_neighbors_detail_info['next']['step_name'] ?></dd>
                    <?php }
        		?>
        			
        		<?php       
        		      }else{
        		?>
        			<dd class="text-success">已完成</dd>
        		<?php }
        		  }?>
        	</dl>
        </div>
        <div class="pull-right">
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
           <button class="btn btn-addition" disabled="disabled" >已结束</button>
           <?php 
                    }else{
           ?>
           <button id="btn-pass" class="btn btn-addition" data-toggle="modal" data-target="#order-modal" order_id="<?= $_orders_info['id']; ?>" auth_action="1"  flow_detail_name="<?= $_neighbors_detail_info['next']['step_name']; ?>"  to_detail_id="<?= $_neighbors_detail_info['next']['id'] ?>" >通 过</button>
           <button id="btn-reback" class="btn btn-danger" data-toggle="modal" data-target="#order-modal-reback" order_id="<?= $_orders_info['id']; ?>" auth_action="-1"  flow_detail_name="<?= $_neighbors_detail_info['next']['step_name']; ?>" to_detail_id="<?= $_neighbors_detail_info['next']['id'] ?>" >退 回</button>
           <?php 
                    }
                }else{
                    //无该步骤操作权限，，判断之前步骤权限，判断是否是已审核状态
                    $_ops = array_unique(array_merge($_neighbors_detail_info['passed']?$_neighbors_detail_info['passed']:[],$_neighbors_detail_info['reback']?$_neighbors_detail_info['reback']:[]));
                    if ($_ops){
                        foreach ($_ops as $_key =>$_value){
                            if(in_array($_value['step_popedom_code'], $_popdom_info)){
            ?>
            <button class="btn btn-addition" disabled="disabled" >已操作</button>
            <?php                 
                                break;
                            }
                        }
                    }
                }
                
                
            }else{
                //已完成 按钮
        ?>
        <button class="btn btn-addition" disabled="disabled" >已完成</button>
        <?php 
            }
        ?>
        
		</div>
	</div>
	<div class="order-flow">
        <ul class="clearfix">
        	<?php 
        	   if (!empty($_flow_details_info)){
        	       $i = 1;$_c = count($_flow_details_info);
        	       $todo = true;
        	       $_flow_details_info_copy = $_flow_details_info;
        	       $_last_step = array_pop($_flow_details_info_copy);
                   foreach ($_flow_details_info as $_key => $_value){
                       if (isset($_neighbors_detail_info['next'])&&$todo){
                        //根据neighbors next信息处理undo
                       $todo = $_neighbors_detail_info['next']['id']==$_value['id']?false:true;
                       }
                       if (is_null($_orders_info['detail_id'])){
                           $todo = false;
                       }
                       
        ?>
        	<li <?= !$todo?'class="order-flow-diff"':'' ?>>
              <div class="order-flow-first pull-left">
                  <div class="<?= $todo?'order-flow-done':'order-flow-undo' ?>"><?= $i ?></div>
                  <div>
                    <p><?= $_value['step_name'] ?></p>
                  </div> 
              </div>
              <div class="order-flow-last pull-right">
                  <div class="<?= $todo?'order-flow-done':'order-flow-undo' ?>"><?= $i+1 ?></div>
                  <div>
                    <p><?= ($i+1 == $_c)?$_last_step['step_name']:''; ?></p>
                  </div> 
              </div>
          </li>
        <?php                  
                        $i++;
                        if ($i == $_c){ break;}
                   }       
        	   }
        	?>
            
        </ul>
	</div>
		
	
		
		
		
		
		
		


<!-- 弹窗 -->
<div class="modal fade" id="order-modal" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">提示</h4>
      </div>
      <div class="modal-body">
        <p><i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;<span class="text-primary">确认通过审核?</span></p>
        <p style="margin-top:8px;"><textarea id="pass-note" name="auth_note" class="form-control"></textarea></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="order-submit">确 认</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">取 消</button>
      </div>
    </div>
  </div>
</div>	
<div class="modal fade" id="order-modal-reback" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">提示</h4>
      </div>
      <div class="modal-body">
        <p><i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;<span class="text-primary">确认退回?</span></p>
        <p style="margin-top:8px;"><textarea id="reback-note" name="auth_note" class="form-control"></textarea></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="order-reback-submit">确 认</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">取 消</button>
      </div>
    </div>
  </div>
</div>

<script>
  $('#order-submit').click(
    function(){
      $('#order-modal').modal('hide');
      $('#order-modal').one('hidden.bs.modal',function(){
        /* ajax事件 */
		var btn = $('#btn-pass');
        var _data = {
					"order_id":btn.attr('order_id'),
					'auth_action':btn.attr('auth_action'),
					'auth_note':$('#pass-note').val(),
					'flow_detail_name':btn.attr('flow_detail_name'),
					'flow_detail_id':btn.attr('to_detail_id')
                };  
        var settings = {
				url:"<?= $this->Url->build(['prefix'=>'console','controller'=>'workflow','action'=>'auth']); ?>",
				type:"post",
				dataType:"json",
				data:_data,
				success:function(response){
					//if( parseInt(response.code)!= 0 ){
						//TODO 修改样式
						alert(response.msg);
            window.location.href=window.location.href;
					//}
				}
            };
        $.ajax(settings);
      });
    }
  );
  $('#order-reback-submit').click(
    function(){
      $('#order-modal-reback').modal('hide');
      $('#order-modal-reback').one('hidden.bs.modal',function(){
        /* ajax事件 */
    	  var btn = $('#btn-reback');
          var _data = {
  					"order_id":btn.attr('order_id'),
  					'auth_action':btn.attr('auth_action'),
  					'auth_note':$('#reback-note').val(),
  					'flow_detail_name':btn.attr('flow_detail_name'),
  					'flow_detail_id':btn.attr('to_detail_id')
                  };  
          var settings = {
  				url:"<?= $this->Url->build(['prefix'=>'console','controller'=>'workflow','action'=>'auth']); ?>",
  				type:"post",
  				dataType:"json",
  				data:_data,
  				success:function(response){
  					//if( parseInt(response.code)!= 0 ){
  						//TODO 修改样式
  						alert(response.msg);
              window.location.href=window.location.href;
  					//}
  				}
              };
        $.ajax(settings);
      });
    }
  );
$(document).ready(
	function(){
		var width = (100/$('.order-flow li').length + '%' );
		$('.order-flow li').width(width);
    $('.order-flow').css('display','block');
	}
);
</script>