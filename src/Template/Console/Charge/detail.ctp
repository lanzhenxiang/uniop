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
	<?= $this -> element('charge/top', ['active_action' => 'detail']); ?>
	<div class="wrap-manage">
		<div class="center">
			<div style="padding:8px">
				<!-- <h5 class="text-right">消费金额&nbsp;&nbsp;<span class="text-danger">￥<?= $sum['cost']?></span></h5> -->
			</div>
			<div class="panel panel-default">
				<div class="panel-body">
					<ul class="clearfix">
						<li>
							租户
							&nbsp;&nbsp;
							<span class="text-bold"><?= $check_department_data['name']?></span>
						</li>
						<li>
							资源类型
							&nbsp;&nbsp;
							<span class="text-bold"><?= $check_type_data['service_name']?></span>
						</li>
						<li>
							计费周期
							&nbsp;&nbsp;
							<span class="text-bold"><?= $start?> 至 <?= $end?> </span>
						</li>
						<li>
							消费金额
							&nbsp;&nbsp;
							<span class="text-bold">￥<?= $sum['cost']?> </span>
						</li>	
					</ul>
				</div>
			</div>


			<div class="center clearfix bot">
				<table id="table" data-toggle="table"
				data-pagination="true"
				data-side-pagination="server"
				data-locale="zh-CN"
				data-click-to-select="true"
				data-url="<?= $this->Url->build(['prefix'=>'console','controller'=>'Charge','action'=>'detailData',$department_id,$type,$instance_id,$in_type,$start,$end]); ?>"
				data-unique-id="id"
				>
				<thead>
					<tr>
						<th data-field="service_name" >服务类型</th>
						<th data-field="basic_name" >消费科目</th>
						<th data-field="charge_type" data-formatter="countMode">计费方式</th>
						<th data-field="billing_date" data-formatter="timestrap2date">计费时间</th>
						<th data-field="cost">消费金额</th>
						<th data-field="basic_name" data-formatter="operation">操作</th>
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
				location.href="<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'Charge', 'action' =>'detail',$department_id,$in_type,$instance_id,$type,$start]);?>/"+end;
			});
			$('#datetimepicker-start').datetimepicker({
				autoclose:true,
				minView:2,
				endDate:'<?= $end?>',
			}
			).on('changeDate', function(){
				var start = $('#start-time').val();
				location.href="<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'Charge', 'action' =>'detail',$department_id,$in_type,$instance_id,$type]);?>/"+start+'/<?php echo $end?>';
			});
		}
		);

	function formatter_type(value){
		return value.service_name;
	}
	function formatter_name(value){
		return value.name;
	}
	function countMode(value, row, index) {
		switch(value){
			case '1':{
				return "按天计费";
				break;
			}
			case '2':{
				return "包月计费";
				break;
			}
			case '3':{
				return "按分钟计费";
				break;
			}
			default:{
				return "按分钟计费";
			}
		}
	}
	function timestrap2date(value) {
		var now = new Date(value);

		var y = now.getFullYear();  
		var m = now.getMonth() + 1;  
		m = m < 10 ? '0' + m : m;  
		var d = now.getDate();  
		d = d < 10 ? ('0' + d) : d;  
		now = y + '-' + m + '-' + d;  

		return now;
	}
	function operation(value,row,index){
		if(row.charge_type ==3){

			var a = "<a href = '<?= $this->Url->build(['prefix' =>'console','controller' =>'Charge', 'action' =>'subjectDetail',$start,$end]);?>/"+row.basic_name+"/"+row.type_id+"'>详情</a>";
			return a;
		}else{
			return '-';
		}
	}

	function clickDepartment(){
		department_id = $('#select-depart').val();
    	location.href = "<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'charge', 'action' =>'detail']);?>/"+department_id+"/<?php echo 0;?>/<?= '0'?>/<?= '0'?>/<?= $start?>/<?= $end?>";
    }


    function clickService(){
    	
    	value =$('#select-service').val();
		value = value.split(',');
		charge =value[0];
    	type =value[1];
    	location.href = "<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'charge', 'action' =>'detail',$department_id])?>/"+charge+"/0/"+type+"/<?= $start?>/<?= $end?>";
    }


    function clickInstance(){
    	value =$('#select-instance').val();
		value = value.split(',');
    	charge =value[0];
    	instance =value[1];
    	location.href = "<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'charge', 'action' =>'detail',$department_id])?>/"+charge+"/"+instance+"/<?= $type?>/<?= $start?>/<?= $end?>";
    }
</script> 	