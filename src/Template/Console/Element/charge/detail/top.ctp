<div class="center clearfix">
			<div class="pull-left">
				<?php if (in_array('cmop_global_sys_admin', $this->Session->read('Auth.User.popedomname'))) { ?>
				租户:&nbsp;
				<select style="margin-left:8px;margin-right:20px;" onchange="clickDepartment()" id="select-depart">
					<option value="0" <?php if($department_id == 0){echo "selected";}?>>全部</option>
					<?php if(isset($departments_data)){?>
					<?php foreach($departments_data as $department){?>
					<option value="<?= $department['id']?>" <?php if($department_id == $department['id']){echo "selected";}?>><?= $department['name']?></option>
					<?php }?>
					<?php }?>
				</select>
				<?php }?>
				
				资源类型:
				<select style="margin-left:8px;margin-right:20px;" onchange="clickResource()" id="select-resource">
					<?php foreach($resource_type_data as $key=>$resource_name){?>
					<option value="<?= $key?>" <?php if($type == $key){echo "selected";}?>><?= $resource_name?></option>
					<?php }?>
				</select>
				账单起止时间:
				<div class="pull-right order-number input-append date" id="datetimepicker-end" data-date-format="yyyy-mm-dd" style="height:24px;line-height:24px;">
					<input size="16" type="text" name="time" id="end-time" value="<?php if($end !=0 ) {echo $end;}?>" readonly style="height:24px;line-height:24px;margin-left:5px;">
					<span class="add-on"><i class="icon-th"></i></span>
				</div>
				<div class="pull-right order-number input-append date" id="datetimepicker-start"  data-date-format="yyyy-mm-dd" style="height:24px;line-height:24px;">
					<input size="16" type="text" name="time" id="start-time" value="<?php if($start !=0 ) {echo $start;}?>" readonly style="height:24px;line-height:24px;margin-left:5px;">
					<span class="add-on"><i class="icon-th"></i></span>
				</div>
				<!-- 数据库类型&nbsp;
				<input id="dbname" type="text" name="dbname" /> -->
			</div>
		</div>
<style type="text/css">
	.search-txt{
		margin-left:8px;
		margin-right:20px;
		line-height: 24px;
	    height: 24px;
	    width: 160px;
	}

</style>
<script type="text/javascript">
	function clickResource(){
    	
    	var type = $("#select-resource").find("option:selected").val();
    	location.href = "<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'charge', 'action' =>'detail',$department_id])?>/"+type+"/<?= $start?>/<?= $end?>";
    }
    
    function formatter_price(value){
		return "￥"+(Number(value)).toFixed(4);
	}

	function clickDepartment(){
		department_id = $('#select-depart').val();
    	location.href = "<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'charge', 'action' =>'detail']);?>/"+department_id+"/<?=$type;?>/<?= $start?>/<?= $end?>";
    }
</script>