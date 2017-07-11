<!-- 根据 _detail_info 和 _popdom_info -->
<div class="panel panel-default">
	<div class="order-crumb panel-body clearfix">
        <div class="order-crumb-info pull-left">
        	<dl>
        		<dt>订单号 : </dt>
        		<dd><?= $_orders_info['number'] ?></dd>
        		<dt>状态 : </dt>
        		<?php 
        		  if (!is_null($_neighbors_detail_info['next'])){
        		?>
        		<dd class="text-success">等待<?=!is_null($_neighbors_detail_info['reback'])?$_neighbors_detail_info['reback']['step_name']:$_neighbors_detail_info['next']['step_name'] ?></dd>
        		<?php       
        		  }
        		?>
        	</dl>
        </div>
        <div class="pull-right">
        	<!-- <button class="btn btn-addition">订单打印</button>  -->
        
        <?php 
        
            debug($_neighbors_detail_info);
        
            //测试  ，设置权限
            $_popdom_info[]='test_01';
            $_popdom_info[]='test_02';
            $_popdom_info[]='test_03';
            $_popdom_info[]='test_04';
        ?>
        
        <?php 
            if (!is_null($_neighbors_detail_info['next'])&&(in_array($_neighbors_detail_info['next']['step_popedom_code'], $_popdom_info) || is_null($_neighbors_detail_info['next']['step_popedom_code'])))
            {
                //通过
        ?>
        	<button id="btn-pass" class="btn btn-addition" data-toggle="modal" data-target="#order-modal" order_id="<?= $_orders_info['id']; ?>" auth_action="1"  flow_detail_name="<?= $_neighbors_detail_info['next']['step_name']; ?>"  to_detail_id="<?= $_neighbors_detail_info['next']['id'] ?>" >通 过</button>
        <?php         
            }else{
                //已经完成
                if (count($_flow_details_info) == count($_neighbors_detail_info['passed'])){
        ?>
        	<button class="btn btn-addition"    >已完成</button>
        <?php             
                //判断是否对 之前步骤有权限，有权限显示已审核
                }elseif (!empty($_neighbors_detail_info['passed'])){
                    foreach ($_neighbors_detail_info['passed'] as $_key =>$_value){
                        if(in_array($_value['step_popedom_code'], $_popdom_info)){
        ?>
        	<button class="btn btn-addition" disabled="disabled" >已审核</button>
        <?php                     
                            break;
                        }
                    }            
                }
            }
        ?>
        
        <?php 
            if (!is_null($_neighbors_detail_info['next'])&&(in_array($_neighbors_detail_info['next']['step_popedom_code'], $_popdom_info) || is_null($_neighbors_detail_info['next']['step_popedom_code']))&&(!is_null($_neighbors_detail_info['pre']))){
        ?>
        	<button id="btn-reback" class="btn btn-danger" data-toggle="modal" data-target="#order-modal-reback" order_id="<?= $_orders_info['id']; ?>" auth_action="-1"  flow_detail_name="<?= $_neighbors_detail_info['next']['step_name']; ?>" to_detail_id="<?= $_current_detail_info['id'] ?>" >退 回</button>
        <?php             
            } else{
        ?>
        	<button class="btn btn-danger" disabled="disabled"  >退 回</button>
        <?php         
            }
        ?>
		</div>
		<div class="order-flow">
		
		<?php 
            $_passed_trace_info = [];
            $_trace_info_formart = [];
            foreach ($_trace_info as $_key => $_value ){
                //只收集通过审核的步骤
                if($_value['auth_action'] >=0 ){
                    $_passed_trace_info[$_value['id']] = $_value['flow_detail_id'];
                    $_trace_info_formart[] = $_value;
                }
            }
            
        ?>
        <ul class="clearfix">
        	<?php 
        	   if (!empty($_flow_info)){
        	       $i = 1;
                   foreach ($_flow_info as $_key => $_value){
        ?>
        	<li <?= !in_array($_value['id'], $_passed_trace_info)?'class="order-flow-diff"':'' ?>>
        	<!-- order-flow-done -->
        		<div class="<?= in_array($_value['id'], $_passed_trace_info)?'order-flow-done':'order-flow-undo' ?>"><?= $i ?></div>
        		<div class="text-center">
        			<p><?= $_value['step_name'] ?></p>
        			<h6><?= in_array($_value['id'], $_passed_trace_info)&&isset($_trace_info_formart[$i-1])?'［'.$_trace_info_formart[$i-1]['user_name'].'］':''; ?></h6>
        			<h6><?= in_array($_value['id'], $_passed_trace_info)&&isset($_trace_info_formart[$i-1])?date('Y-m-d H:i:s',$_trace_info_formart[$i-1]['create_time']):''; ?></h6>
        		</div>
        	</li>
        <?php                  
                        $i++;
                   }       
        	   }
        	?>
        
        	</li>
        </ul>
		
		
		
		</div>
		
		
		
		
		
		
		
		
		


<!-- 弹窗 -->
<div class="modal fade" id="order-modal" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">通过审核</h4>
      </div>
      <div class="modal-body">
        <p><i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;<span class="text-primary">确认通过审核?</span></p>
        <p style="margin-top:8px;"><textarea id="pass-note" name="auth_note" class="form-control"></textarea></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="order-submit">通 过</button>
        <button type="button" class="btn btn-negative" data-dismiss="modal">取 消</button>
      </div>
    </div>
  </div>
</div>	
<div class="modal fade" id="order-modal-reback" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">退 回</h4>
      </div>
      <div class="modal-body">
        <p><i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;<span class="text-primary">确认退回?</span></p>
        <p style="margin-top:8px;"><textarea id="reback-note" name="auth_note" class="form-control"></textarea></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="order-reback-submit">退 回</button>
        <button type="button" class="btn btn-negative" data-dismiss="modal">取 消</button>
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
  					//}
  				}
              };
        $.ajax(settings);
      });
    }
  );
</script>	