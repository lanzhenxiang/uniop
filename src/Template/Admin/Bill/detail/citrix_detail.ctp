<!-- author by lixin -->
<?= $this->Html->css(['bootstrap-datetimepicker.min.css','styleBack.css','bootstrap-table.css']); ?>
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
<div class="wrap-nav-right" style="margin: 0">
	<div class="wrap-manage">
		<div class="top">
			<span class="title">消费明细</span>
		</div>
		<?= $this -> element('charge/detail/top'); ?>
		<div class="center clearfix">
			<div class="pull-left">

				<?php if (in_array('cmop_global_sys_admin', $this->Session->read('Auth.User.popedomname'))) { ?>
				Citrix桌面名：&nbsp;
					<input style="margin-left:8px;margin-right:20px;line-height: 24px;height: 24px;width: 160px;" type="text" id="name" name="name" 
					value="<?php if(array_key_exists('name',$this->request->query)){echo $this->request->query['name'];} ?>">
				<?php }?>

				<?php if (in_array('cmop_global_sys_admin', $this->Session->read('Auth.User.popedomname'))) { ?>
				计费方式:&nbsp;
				<select style="margin-left:8px;margin-right:20px;" onchange="citrixSearch()" id="select-charge">
					<option value="1" <?php if($charge_type == 1):?> selected="selected" <?php endif?>>按天计费</option>
					<option value="2" <?php if($charge_type == 2):?> selected="selected" <?php endif?>>按月计费</option>
					<option value="4" <?php if($charge_type == 4):?> selected="selected" <?php endif?>>按年计费</option>
					<option value="5" <?php if($charge_type == 5):?> selected="selected" <?php endif?>>按时长计费</option>	
				</select>
				<?php }?>
	
				<!-- 数据库类型&nbsp;
				<input id="dbname" type="text" name="dbname" /> -->
			</div>
			<div class="pull-right">
				 <button class="btn btn-addition" onclick="citrixSearch()">查询</button>
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
				data-url="<?= $this->Url->build(['prefix'=>'admin','controller'=>'bill','action'=>'detail',$department_id,$type,$start,$end]); ?>?name=<?php if(array_key_exists('name',$this->request->query)){echo $this->request->query['name'];}?>&charge_type=<?php if(array_key_exists('charge_type',$this->request->query)){echo $this->request->query['charge_type'];} ?>"
				data-unique-id="id"
				>
				<thead>
					<tr>
						<th data-field="bill_date" >账单日期</th>
						<th data-field="name" >citrix桌面名</th>
						<th data-field="loginname" >使用人</th>
						<th data-field="logintime" data-formatter="formatter_time">登录时间</th>
						<th data-field="logoutime" data-formatter="formatter_time">登出时间</th>
						<th data-field="charge_type_txt" >计费类型</th>
						<th data-field="duration" >使用时长</th>
						<th data-field="price" data-formatter="formatter_price">单价</th>
						<th data-field="charge_unit_txt">单位</th>
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
				location.href="<?= $this ->Url ->build(['prefix' =>'admin', 'controller' =>'bill', 'action' =>'detail',$department_id,$type,$start]);?>/"+end;
			});
			$('#datetimepicker-start').datetimepicker({
				autoclose:true,
				minView:2,
				endDate:'<?= $end?>',
			}
			).on('changeDate', function(){
				var start = $('#start-time').val();
				location.href="<?= $this ->Url ->build(['prefix' =>'admin', 'controller' =>'bill', 'action' =>'detail',$department_id,$type]);?>/"+start+'/<?php echo $end?>';
			});
		}
	);

	function formatter_time(value){
		if(value == 0){
			return '-';
		}else{
			return value;
		}
	}

	function timestrap2date(value) {
		value = parseInt(value);
		var now = new Date(value);

		var y = now.getFullYear();  
		var m = now.getMonth() + 1;  
		m = m < 10 ? '0' + m : m;  
		var d = now.getDate();  
		d = d < 10 ? ('0' + d) : d;  
		now = y + '-' + m + '-' + d;  

		return now;
	}

	function citrixSearch(){
		var name = $('#name').val();
		var charge_type = $('#select-charge').val();
		location.href="<?= $this ->Url ->build(['prefix' =>'admin', 'controller' =>'bill', 'action' =>'detail',$department_id,$type,$start,$end]);?>?name="+name+"&charge_type="+charge_type;

	}
</script> 	