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
					<input style="line-height: 24px;height: 24px;width: 160px;margin-left:8px;margin-right:20px;" type="text" id="txtsearch" value="<?=$name?>" name="search" placeholder="搜索边界路由器接口名称">
				<?php }?>
				<?php if (in_array('cmop_global_sys_admin', $this->Session->read('Auth.User.popedomname'))) { ?>
				计费方式&nbsp;
				<select style="margin-left:8px;margin-right:20px;" onchange="clickSearch()" id="select-charge">
					<option value="" selected="selected">未选择</option>
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
				data-url="<?= $this->Url->build(['prefix'=>'console','controller'=>'Charge','action'=>'detail',$department_id,$type,$start,$end]); ?>?name=<?=$name?>&charge_type=<?=$charge_type?>"
				data-unique-id="id"
				>
				<thead>
					<tr>
						<th data-field="bill_date" >账单日期</th>
						<th data-field="name" >边界路由器接口名称</th>
						<th data-field="routerCode" >边界路由器code</th>
						<th data-field="buyer_name" >购买人</th>
						<th data-field="charge_type_txt" >计费方式</th>
						<th data-field="initiatingSideRouterInterfaceCode" >发起端接口code</th>
						<th data-field="acceptingSideRouterInterfaceCode" >接收端接口code</th>
						<!--&lt;!&ndash; <th data-field="spec" >截止日</th>-->
						<th data-field="spec" >规格</th>
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

	function formatter_bandwidth(value){
		return value+'Mbps';
	}
	
    function clickSearch(){
    	var charge_type = $('#select-charge').val();
    	var name 		= $('#txtsearch').val();
    	location.href="<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'Charge', 'action' =>'detail',$department_id,$type,$start,$end]);?>?charge_type="+charge_type+'&name='+name;
    }
    
</script> 	