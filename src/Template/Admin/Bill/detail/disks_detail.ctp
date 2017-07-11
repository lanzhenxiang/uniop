<!-- author by lixin -->
<?= $this->Html->css(['bootstrap-datetimepicker.min.css','styleBack.css','bootstrap-table.css']); ?><!-- TODO 添加消费列表在此 -->
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
<div class="wrap-nav-right" style="margin: 0;">
	<div class="wrap-manage">
		<div class="top">
			<span class="title">消费明细</span>
		</div>
		<?= $this -> element('charge/detail/top'); ?>
		<div class="center clearfix">
			<div class="pull-left">

				<?php if (in_array('cmop_global_sys_admin', $this->Session->read('Auth.User.popedomname'))) { ?>
					<input style="line-height: 24px;height: 24px;width: 160px;margin-left:8px;margin-right:20px;" type="text" id="txtsearch" value="<?=$name?>" name="search" placeholder="搜索名称、code">
				<?php }?>
				<?php if (in_array('cmop_global_sys_admin', $this->Session->read('Auth.User.popedomname'))) { ?>
				计费方式&nbsp;
				<select style="margin-left:8px;margin-right:20px;" onchange="clickSearch()" id="select-charge">
					<option value="1" <?php if($charge_type == 1):?> selected="selected" <?php endif?>>按天计费</option>
					<option value="2" <?php if($charge_type == 2):?> selected="selected" <?php endif?>>按月计费</option>
					<option value="4" <?php if($charge_type == 4):?> selected="selected" <?php endif?>>按年计费</option>	
				</select>
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
				data-url="<?= $this->Url->build(['prefix'=>'admin','controller'=>'bill','action'=>'detail',$department_id,$type,$start,$end]); ?>?name=<?=$name?>&charge_type=<?=$charge_type?>"
				data-unique-id="id"
				>
				<thead>
					<tr>
						<th data-field="bill_date" >账单日期</th>
						<th data-field="name" >块存储名</th>
						<th data-field="code" >块存储code</th>
						<th data-field="buyer_name" >购买人</th>
						<th data-field="charge_type_txt" >计费方式</th>
						<th data-field="order_date" >购买日</th>
						<th data-field="start_date" >生效日</th>
						<!-- <th data-field="billing_date" >截止日</th>
						<th data-field="billing_date" >天数</th> -->
						<th data-field="market_price" data-formatter="formatter_price">原价</th>
						<th data-field="price" data-formatter="formatter_price">成交价</th>
						<th data-field="charge_unit_txt" >计费单位</th>
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

	function formatter_bandwidth(value){
		return value+'Mbps';
	}
	
    function clickSearch(){
    	var charge_type = $('#select-charge').val();
    	var name 		= $('#txtsearch').val();
    	location.href="<?= $this ->Url ->build(['prefix' =>'admin', 'controller' =>'bill', 'action' =>'detail',$department_id,$type,$start,$end]);?>?charge_type="+charge_type+'&name='+name;
    }
    
</script> 	