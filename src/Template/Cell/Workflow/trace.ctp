<!-- _current_trace _trace_info -->
<?php 
if (!empty($_trace_info)){
    foreach ($_trace_info as $_key => $_value){
?>
	<tr>
    	<td><?= date('Y-m-d H:i:s',$_value['create_time']) ?></td>
    	<td>
    		<?php
    	       switch ($_value['auth_action'])
    	       {
    	           case 0:
                       if($_value['flow_detail_name']=='提交订单'){
                            echo "<span style='color:green'>订单提交成功，正等待后续处理<span>"; 
                       }else if($_value['flow_detail_name']=='结束'){
                            echo "<span style='color:green'>订单已完成<span>"; 
                       }else{
                            echo "<span style='color:green'>您的订单已通过".$_value['flow_detail_name']."环节<span>";
                       }
    	               break;
    	           case 1:
    	               echo "<span style='color:green'>您的订单已通过".$_value['flow_detail_name']."环节<span>";
    	               break;
    	           case -1:
    	               echo "<span style='color:red'>您的订单未通过".$_value['flow_detail_name']."环节<span>";
    	               break;
    	       }
    		?>
    	</td>
    	<td><?= $_value['auth_note'] ?></td>
    	<td><?= $_value['user_name'] ?></td>
	</tr>
<?php         
    }
}else{
?>
<tr>
	<td colspan="3">暂无此类信息</td>
</tr>
<?php 
}
?>
