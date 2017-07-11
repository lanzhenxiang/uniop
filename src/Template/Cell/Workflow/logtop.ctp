<!-- 根据 _detail_info 和 _popdom_info -->
<div class="panel panel-default">
	<div class="order-crumb panel-body clearfix">
        <div class="order-crumb-info pull-left">
        	<dl>
        		<dt>订单号 : </dt>
        		<dd><?= $_orders_info['number'] ?></dd>
        		<dt>当前环节 : </dt>
        		<?php 
        		  if ($_orders_info['status'] == -1 || $_orders_info['detail_id'] == 0){
        		?>
        		<dd class="text-danger">已结束</dd>
        		<?php       
        		  }else{
        		      if (!is_null($_neighbors_detail_info['next'])){
                    if($_orders_info['is_back'] == 1){ ?>
                      <dd class="text-danger">已退回</dd>
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

<script>
$(document).ready(
	function(){
		var width = (100/$('.order-flow li').length + '%' );
		$('.order-flow li').width(width);
    $('.order-flow').css('display','block');
	}
);
</script>