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
				栏目名:&nbsp;
					<input style="line-height: 24px;height: 24px;width: 160px;margin-left:8px;margin-right:20px;" type="text" value="<?=$column?>" id="column" name="column" placeholder="">
				节目名:&nbsp;
					<input style="line-height: 24px;height: 24px;width: 160px;margin-left:8px;margin-right:20px;" type="text" id="program_name" value="<?=$program_name?>" name="program_name" placeholder="">
				服务商
				<select style="margin-left:8px;margin-right:20px;" onchange="clickSearch()" id="select-vendor">
					<?php foreach($vendor as $key=>$vendor_name){?>
					<option value="<?= $key?>" <?php if($vendor_code == $key){echo "selected";}?>><?= $vendor_name?></option>
					<?php }?>
				</select>
				服务类型
				<select style="margin-left:8px;margin-right:20px;" onchange="clickSearch()" id="select-subject">
					<?php foreach($consumption_subjects as $subject){?>
					<option value="<?= $subject?>" <?php if($consumption_subject == $subject){echo "selected";}?>><?= $subject?></option>
					<?php }?>
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
				data-url="<?= $this->Url->build(['prefix'=>'console','controller'=>'Charge','action'=>'detail',$department_id,$type,$start,$end]);?>?vendor_code=<?=$vendor_code?>&subject=<?=$consumption_subject?>&column=<?=$column?>&program_name=<?=$program_name?>"
				data-unique-id="id"
				>
				<thead>
					<tr>
						<th data-field="column_code" >栏目名</th>
						<th data-field="program_name" >节目名</th>
						<th data-field="vendor_code" data-formatter="formatter_vendor">服务商</th>
						<th data-field="consumption_subjects" >服务类型</th>
						<th data-field="duration">节目时长（<?=$unittxt?>）</th>
						<th data-field="price" data-formatter="formatter_price">单价（元/<?=$unittxt?>）</th>
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

	function formatter_vendor(value){
		switch(value){
			case 'ArcSoft':{
				return "虹软";
				break;
			}
			case 'DaYang':{
				return "大洋";
				break;
			}
			case 'Sobey':{
				return "索贝";
				break;
			}
			default:{
				return "索贝";
			}
		}
	}
	function clickDepartment(){
		department_id = $('#select-depart').val();
    	location.href = "<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'charge', 'action' =>'detail']);?>/"+department_id+"/<?= $type?>/<?= $start?>/<?= $end?>";
    }

    function clickSearch(){
    	var vendor_code = $("#select-vendor").find("option:selected").val();
    	var subject 	= $("#select-subject").find("option:selected").val();
    	var column 		= $('#column').val();
    	var program_name = $('#program_name').val();

    	location.href="<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'Charge', 'action' =>'detail',$department_id,$type,$start,$end]);?>?vendor_code="+vendor_code+"&subject="+subject+"&column="+column+"&program_name="+program_name;
    }
</script> 	