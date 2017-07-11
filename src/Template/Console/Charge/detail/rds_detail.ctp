<?= $this -> element('charge/left', ['active_action' => 'detail']); ?>
<!-- author by lixin -->
<?= $this->Html->css('bootstrap-datetimepicker.min.css'); ?>
<!-- TODO 添加消费列表在此 -->
<style>
	.center select{
		width:120px;
		margin-right: 14px;
	}
	.panel-body{
		padding-left:0;
		padding-right:0;
	}
	.panel-body li{
		float:left;
		width:24.9%;
		text-align:center;
		border-right:1px solid #d2d2d2;
	}
	.panel-body li:last-child{
		border-right:0;
	}
</style>
<div class="wrap-nav-right">
	<div class="wrap-manage">
		<div class="top">
			<span class="title">消费明细</span>
		</div>
		<?= $this -> element('charge/detail/top'); ?>
		<div class="center clearfix">
			<div class="pull-left">

				<?php if (in_array('cmop_global_sys_admin', $this->Session->read('Auth.User.popedomname'))) { ?>
				数据库类型：&nbsp;
					<input style="line-height: 24px;height: 24px;width: 160px;margin-left:8px;margin-right:20px;" type="text" id="txtsearch" value="<?=$name?>" name="name" placeholder="">
				<?php }?>
	
				<!-- 数据库类型&nbsp;
				<input id="dbname" type="text" name="dbname" /> -->
			</div>
			<div class="pull-right">
				 <button class="btn btn-addition" onclick="clickSearch()">查询</button>
			</div>
		</div>
	</div>
	<div class="wrap-manage">
		<?php if($total > 0):?>
			<?= $this -> element('charge/detail/export'); ?>
		<?php endif;?>
		<div class="center">
			<div style="padding:8px">
				<!-- <h5 class="text-right">消费金额&nbsp;&nbsp;<span class="text-danger">￥<?= $sum['cost']?></span></h5> -->
			</div>
			<?= $this -> element('charge/detail/panel'); ?>
			<div class="center clearfix bot">
				<table id="table" data-toggle="table"
				data-pagination="true"
				data-side-pagination="server"
				data-locale="zh-CN"
				data-click-to-select="true"
				data-url="<?= $this->Url->build(['prefix'=>'console','controller'=>'Charge','action'=>'detail',$department_id,$type,$start,$end]); ?>?name=<?=$name?>"
				data-unique-id="id"
				>
				<thead>
					<tr>
						<th data-field="name" >数据库类型</th>
						<th data-field="buyer_name" >购买人</th>
						<th data-field="order_date" >购买日期</th>
						<th data-field="start_date" >生效日</th>
						<th data-field="end_date" >截止日</th>
						<th data-field="amount" data-formatter="formatter_price">消费金额</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>		
</div>
<?= $this->Html->script('bootstrap-datetimepicker.js'); ?>
<script type="text/javascript">
	$(document).ready(
		function(){
			var myDate = new Date();
			var year = myDate.getFullYear();
			var month =myDate.getMonth()+1;
			var day =  myDate.getDate();
			var time =year+'-'+month+'-'+day;
			$('#datetimepicker-end').datetimepicker({
				autoclose:true,
				minView:2,
				startDate:'<?= $start?>',
				endDate:time
			}
			).on('changeDate', function(){
				var end = $('#end-time').val();
				location.href="<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'Charge', 'action' =>'detail',$department_id,$type,$start]);?>/"+end;
			});
			$('#datetimepicker-start').datetimepicker({
				autoclose:true,
				minView:2,
				endDate:'<?= $end?>',
			}
			).on('changeDate', function(){
				var start = $('#start-time').val();
				location.href="<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'Charge', 'action' =>'detail',$department_id,$type]);?>/"+start+'/<?php echo $end?>';
			});
		}
		);

	function formatter_type(value){
		return value.service_name;
	}

	function clickSearch(){
		var name = $('#txtsearch').val();
		location.href="<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'Charge', 'action' =>'detail',$department_id,$type,$start,$end]);?>?name="+name;
	}

	function clickDepartment(){
		department_id = $('#select-depart').val();
    	location.href = "<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'charge', 'action' =>'detail']);?>/"+department_id+"/<?php echo 0;?>/<?= '0'?>/<?= '0'?>/<?= $start?>/<?= $end?>";
    }
</script> 	