<?= $this -> element('charge/left', ['active_action' => 'personDetail']); ?>
<?= $this->Html->css('bootstrap-datetimepicker.min.css'); ?>
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
			<span class="title">人员消费详情</span>
			<!-- <button class="btn btn-default pull-right">返回</button> -->
		</div>
		<div class="center clearfix">
		<div class="pull-left">
			<?php if (in_array('cmop_global_sys_admin', $this->Session->read('Auth.User.popedomname'))) { ?>
			租户&nbsp; 
			<select onchange="clickDepartment()" id="select-depart">
				<option value="0" <?php if($department_id == 0){echo "selected";}?>>全部</option>
				<?php if(isset($departments_data)){?>
				<?php foreach($departments_data as $department){?>
				<option value="<?= $department['id']?>" <?php if($department_id == $department['id']){echo "selected";}?>><?= $department['name']?></option>
				<?php }?>
				<?php }?>
			</select>
			<?php }?>

			<?php if (in_array('cmop_global_sys_admin', $this->Session->read('Auth.User.popedomname')) || in_array(' 	cmop_global_tenant_admin', $this->Session->read('Auth.User.popedomname'))) { ?>
			姓名&nbsp;  
			<select onchange="clickAccount()" id="select-account">
				<option value="0" <?php if($account_id == 0){echo "selected";}?>>全部</option>
				<?php foreach($accounts_data as $account){?>
					<option value="<?= $account['id']?>" <?php if($account_id == $account['id']){echo "selected";}?>><?= $account['username']?></option>
				<?php }?>
			</select>
			<?php }?>
			服务类型&nbsp;  
			<select onchange="clickService()" id="select-service">
				<option value="0" <?php if($type_id == 0){echo "selected";$type_name = '全部';}?>>全部
				</option>
				<?php foreach($service_type_data as $service){?>
					<option value="<?= $service['type_id']?>" <?php if($type_id == $service['type_id']){echo "selected";$type_name = $service['service_name'];}?>><?= $service['service_name']?>
					</option>
				<?php }?>
			</select>
			<!-- 消费科目 
			<select>
				<option onclick="location.href='<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'charge', 'action' =>'personDetail',$department_id,$account_id,$type_id,0,$start,$end]);?>'" <?php if($instance_id == 0){echo "selected";$instance_name = '全部';}?>>全部</option>
				<?php foreach($instance_basic_data as $instance_basic){?>
					<option onclick="location.href='<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'charge', 'action' =>'personDetail',$department_id,$account_id,$type_id,$instance_basic['id'],$start,$end]);?>'" <?php if($instance_id == $instance_basic['id']){echo "selected";$instance_name = $instance_basic['name'];}?>><?= $instance_basic['name']?></option>
				<?php }?>
			</select> -->
			统计时间
			<div class="pull-right order-number input-append date" id="datetimepicker-end" data-date-format="yyyy-mm-dd" style="height:24px;line-height:24px;">
	            <input size="16" type="text" name="time" id="end-time" value="<?php if($end !=0 ) {echo $end;}?>" readonly style="height:24px;line-height:24px;margin-left:5px;">
	            <span class="add-on"><i class="icon-th"></i></span>
	        </div>
	        <div class="pull-right order-number input-append date" id="datetimepicker-start"  data-date-format="yyyy-mm-dd" style="height:24px;line-height:24px;">
	            <input size="16" type="text" name="time" id="start-time" value="<?php if($start !=0 ) {echo $start;}?>" readonly style="height:24px;line-height:24px;margin-left:5px;">
	            <span class="add-on"><i class="icon-th"></i></span>
	        </div>
			</div>	 		 
					 	 	 
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
			        	姓名
			        	&nbsp;&nbsp;
			        	<span class="text-bold"><?= $account_name['username']?></span>
			        </li>
			        <li>
			        	租户
			        	&nbsp;&nbsp;
			        	<span class="text-bold"><?= $department_name['name']?></span>
			        </li>
			    	<li>
			    		服务类型
			    		&nbsp;&nbsp;
			        	<span class="text-bold"><?= $type_name?></span>
			    	</li>
			    	<!-- <li>
			    		消费科目
			    		&nbsp;&nbsp;
			        	<span class="text-bold"><?= $instance_name?></span>
			    	</li> -->
			    	<li>
			    		消费日期
			    		&nbsp;&nbsp;
			    		<span class="text-bold"><?= $start?> 至 <?= $end?></span>
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
				data-url="<?= $this->Url->build(['prefix'=>'console','controller'=>'Charge','action'=>'personDetailData',$department_id,$account_id,$type_id,$instance_id,$start,$end]); ?>"
				data-unique-id="id"
				>
				<thead>
					<tr>
						<th data-field="username" >姓名</th>
						<th data-field="department_name" >租户</th>
						<th data-field="instance_name">消费科目</th>
						<!-- <th data-field="instance_name">计费方式</th> -->
						<th data-field="start_time" data-formatter="timestrap2date">开始时间</th>
						<th data-field="end_time" data-formatter="timestrap2date">结束时间</th>
						<th data-field="use_time">使用时常(秒)</th>
						<th data-field="cost">金额</th>
					</tr>
				</thead>
			</table>
		</div>
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
	            location.href="<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'Charge', 'action' =>'personDetail',$department_id,$account_id,$type_id,$instance_id,$start]);?>/"+end;
	        });
	        $('#datetimepicker-start').datetimepicker({
	            autoclose:true,
	            minView:2,
	            endDate:'<?= $end?>',
	        }
	        ).on('changeDate', function(){
	            var start = $('#start-time').val();
	            location.href="<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'Charge', 'action' =>'personDetail',$department_id,$account_id,$type_id,$instance_id]);?>/"+start+'/<?php echo $end?>';
	        });
    	}
    );

	//时间戳转换日期格式
    function timestrap2date(value) {
      var now = new Date(parseInt(value) * 1000);
      return now.toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ");
    }



	function clickDepartment(){
		department_id = $('#select-depart').val();
    	location.href = "<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'charge', 'action' =>'personDetail']);?>/"+department_id+"/0/0/0/<?= $start?>/<?= $end?>";
    }

    function clickAccount(){
    	account = $('#select-account').val();
    	location.href = "<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'charge', 'action' =>'personDetail',$department_id])?>/"+account+"/<?= $type_id?>/<?= $instance_id?>/<?= $start?>/<?= $end?>";
    }

    function clickService(){
    	type = $('#select-service').val();
    	location.href = "<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'charge', 'action' =>'personDetail',$department_id,$account_id])?>/"+type+"/0/<?= $start?>/<?= $end?>";
    }

</script>