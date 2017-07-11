<?= $this -> element('charge/left', ['active_action' => 'detail']); ?>
<?= $this->Html->css('bootstrap-datetimepicker.min.css'); ?>
<style>
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
		<div class="top clearfix">
			<span class="title pull-left" style="margin-top:7px;">科目消费详情</span>
			<button class="btn btn-default pull-right">返回</button>
		</div>
	</div>	
	<div class="wrap-manage">
		<div class="center">
			<div style="padding:8px">
				<h5 class="text-right">消费金额&nbsp;&nbsp;<span class="text-danger">￥<?= $sum?></span></h5>
			</div>
			<div class="panel panel-default">
			  <div class="panel-body">
			    <ul class="clearfix">
			        <li>
			        	租户
			        	&nbsp;&nbsp;
			        	<span class="text-bold"><?= $department_name?></span>
			        </li>
			    	<li>
			    		服务类型
			    		&nbsp;&nbsp;
			        	<span class="text-bold"><?= $data[0]['service_name']?></span>
			    	</li>
			    	<!-- <li>
			    		消费科目
			    		&nbsp;&nbsp;
			        	<span class="text-bold"><?= $data[0]['basic_name']?></span>
			    	</li> -->
			    	<li>
			    		消费日期
			    		&nbsp;&nbsp;
			    		<span class="text-bold"><?= $start?> 至 <?= $end?></span>
			    	</li>	
			    </ul>
			  </div>
			</div>
		</div>
		
		<div class="center clearfix bot">
			<table id="table" data-toggle="table"
			data-pagination="true"
			data-side-pagination="server"
			data-locale="zh-CN"
			data-click-to-select="true"
			data-url="<?= $this->Url->build(['prefix'=>'console','controller'=>'Charge','action'=>'subjectDetailData',$start,$end,$charge_body,$type_id]); ?>"
			data-unique-id="id"
			>
			<thead>
				<tr>
					<th data-field="username">姓名</th>
					<th data-field="department_name" >租户</th>
					<th data-field="basic_name" >消费科目</th>
					<th data-field="username" data-formatter="countMode">计费方式</th>
					<th data-field="start_time" data-formatter="timestrap2date">开始时间</th>
					<th data-field="end_time" data-formatter="timestrap2date">结束时间</th>
					<th data-field="use_time" >使用时间</th>
					<th data-field="cost">消费金额</th>
				</tr>
			</thead>
		</table>
		</div>
	</div>
	</div>

</div>	
<?= $this->Html->script('bootstrap-datetimepicker.js'); ?>
<script type="text/javascript">
	function timestrap2date(value) {
      var now = new Date(parseInt(value) * 1000);
      return now.toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ");
    }
    function countMode(value){
    	var q ='按分钟计费';
    	return q;
    }
</script>