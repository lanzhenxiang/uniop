<!-- _flow_info -->

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
<script>
$(document).ready(
	function(){
		var width = (100/$('.order-flow li').length + '%' );
		$('.order-flow li').width(width).css('display','block');
	}
);
</script>