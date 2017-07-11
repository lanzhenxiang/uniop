<!-- author by lixin -->
<!-- TODO 添加消费列表在此 -->
<?= $this->Html->css(['bootstrap-datetimepicker.min.css','styleBack.css','bootstrap-table.css']); ?>

<div class="wrap-nav-right" style="margin: 0;">
	<div class="wrap-manage">
		<div class="top">
			<span class="title">消费总览</span>
		</div>
		 <div class="center clearfix">
			<div class="pull-left" style="margin-top:6px;">
				<?php if (in_array('cmop_global_sys_admin', $this->Session->read('Auth.User.popedomname'))) { ?>
				租户
				<select style="margin-left:8px;margin-right:20px;" onchange="clickDepartment()" id="select-depart">
					
					<?php if(!empty($departments_data)){?>
					<?php foreach($departments_data as $department){?>
					<option value="<?= $department['id']?>" <?php if($department_id == $department['id']){echo "selected";}?>><?= $department['name']?></option>
					<?php }?>
					<?php }?>
				</select>
				<?php }else{?>
				
					<?php if(!empty($department_data['name'])){?>
					租户：<?= $department_data['name']?>
					<?php }?>
					<?php }?>
				<div class="pull-right order-number input-append date" id="datetimepicker-end" data-date-format="yyyy-mm-dd" style="height:24px;line-height:24px;">
	                <input size="16" type="text" name="time" id="end-time" value="<?php if($end !=0 ) {echo $end;}?>" readonly style="height:24px;line-height:24px;margin-left:5px;">
	                <span class="add-on"><i class="icon-th"></i></span>
	            </div>
	            <div class="pull-right order-number input-append date" id="datetimepicker-start"  data-date-format="yyyy-mm-dd" style="height:24px;line-height:24px;">
	                <input size="16" type="text" name="time" id="start-time" value="<?php if($start !=0 ) {echo $start;}?>" readonly style="height:24px;line-height:24px;margin-left:5px;">
	                <span class="add-on"><i class="icon-th"></i></span>
	            </div>
			</div>
			<div class="pull-right">
			<?php if (in_array('ccm_user_charge_detail', $this->Session->read('Auth.User.popedomname'))) { ?>
				<button class="btn btn-addition" onclick="location.href='<?= $this ->Url ->build(['prefix' =>'admin', 'controller' =>'Bill', 'action' =>'detail',$department_id,'citrix',$start,$end]);?>'">消费明细</button>
				<?php }?>
			</div>
		</div>
	</div>
	<div class="clearfix" style="padding:0 20px;">
		<div class="wrap-manage pull-left" style="width:45%;margin-left:0;margin-right:0">
			<div>
				<div class="top">
					<span><?= $start?>到<?= $end?>消费总览</span>
				</div>
				<div class="center">
					<div class="graph-display-block">
		                <div class="graph-display-content clearfix">
		                    <div class="graph-display-canvas pull-left">
		                        <canvas id="canvas" width="200" height="200"></canvas>
		                        <div class="graph-display-account"></div>
		                        <div class="text-center" style="margin-top:15px;"><?= $start?>到<?= $end?>消费共计: <span class="text-primary">￥<?php if(!empty($sum_cost)){ echo $sum_cost;}else{echo '0';}?></span></div>
		                    </div>
		                    <div class="graph-display-info pull-right">
		                        <ul>
		                        <?php $color = ['#f64649','#e2ebea','#d5ccc5','#949fb1','#45d2e4','#F38630'];?>
		                        <?php $_i = 0; foreach($query as $_q_v){?>
		                        	<li>
		                                <span class="color-block" style="background-color: <?= $color[$_i]?>"></span>
		                                <?php if(!empty($_q_v['name'])){ echo $_q_v['name'];}?>：￥<?php if(!empty($_q_v)){ echo (float)$_q_v['cost'];}else{ echo 0;}?>
		                            </li>
		                        <?php $_i++; }?>
		                        </ul>
		                    </div>
		                </div>
		            </div>
				</div>
			</div>
		</div>
		<div class="wrap-manage pull-right" style="width:53%;margin-left:0;margin-right:0">
			<div>
				<div class="top">
					<span>近期消费走势</span>
				</div>
				<div class="center">
					<canvas id="history-canvas"></canvas>
				</div>
			</div>
		</div>
	</div>
	<div class="wrap-manage">
		<div class="center bot">
			<table id="table" data-toggle="table"
			data-pagination="true"
			data-side-pagination="server"
			data-locale="zh-CN"
			data-click-to-select="true"
			data-url="<?= $this->Url->build(['prefix'=>'admin','controller'=>'Bill','action'=>'subjectData',$department_id,$start,$end]); ?>"
			data-unique-id="id"
			>
			<thead>
				<tr>
					<th data-field="name">资源类型</th>
					<th data-field="charge_type" data-formatter="countMode">计费方式</th>
					<th data-field="cost">消费金额</th>
					<th data-field="resource_type" data-formatter="handle">操作</th>
				</tr>
			</thead>
		</table>

	</div>
</div>
</div>		

<?php
$this->start('script_last');
?>
<?= $this->Html->script(['bootstrap-datetimepicker.js','Chart.js']); ?>
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
	            endDate:time,

	        }
	        ).on('changeDate', function(){
	            var end = $('#end-time').val();
	            location.href="<?= $this ->Url ->build(['prefix' =>'admin', 'controller' =>'Bill', 'action' =>'subject',$department_id,$start]);?>/"+end;
	        });
	        $('#datetimepicker-start').datetimepicker({
	            autoclose:true,
	            minView:2,
	            endDate:'<?= $end?>',
	        }
	        ).on('changeDate', function(){
	            var start = $('#start-time').val();
	            location.href="<?= $this ->Url ->build(['prefix' =>'admin', 'controller' =>'Bill', 'action' =>'subject',$department_id
	             ]);?>/"+start+'/<?php echo $end?>';
	        });
    	}
    );

	<?php $time = ''; $data = '';foreach ($line_query as $_l_v) {
		$mon ='"'.$_l_v['y'].'-'.$_l_v['m'].'",';
		$time = $mon.$time;
		$sum =$_l_v['cost'].',';
		$data = $sum.$data;
	}?>

	var data = {
	    labels: [<?= $time?>],
	    datasets: [
	        {
	            label: "My First dataset",
	            fillColor: "rgba(220,220,220,0.2)",
	            strokeColor: "rgba(220,220,220,1)",
	            pointColor: "rgba(220,220,220,1)",
	            pointStrokeColor: "#fff",
	            pointHighlightFill: "#fff",
	            pointHighlightStroke: "rgba(220,220,220,1)",
	            data: [<?= $data?>]
	        }
	    ]
	};


	var options = {};
	var historyCanvas = $('#history-canvas').get(0).getContext('2d');
	new Chart(historyCanvas).Line(data,options);


	var binOptions = {
	    animationSteps:40,
	    animationEasing:"linear",
	    showTooltips:false
	};

	
    var canvas = $('#canvas').get(0).getContext('2d');
    
    <?php $i =0;$binData=''; foreach ($query as $_v){
    	$binData .= '{value:'.$_v["cost"].',color:"'.$color[$i].'"},';
    	$i++;
    }?>
    var binData = [
		<?= $binData?>	
	]
    new Chart(canvas).Doughnut(binData,binOptions);

    // function subject(y,m){
    // 	location.href="<?= $this ->Url ->build(['prefix' =>'admin', 'controller' =>'charge', 'action' =>'subject']);?>/"+y+'/'+m;
    // } 

    function countMode(value, row, index) {
		switch(value){
			case 1:{
				return "按天计费";
				break;
			}
			case 2:{
				return "按月计费";
				break;
			}
			case 4:{
				return "按年计费";
				break;
			}
			case 5:{
				return "按时长计费";
				break;
			}
			case 6:{
				return "包月";
				break;
			}
			default:{
				return "默认计费";
			}
		}
	} 

	function handle(value,row,index){
		var param = "";
		if(value == 'ecs' || value == 'citrix'){
			param = '?charge_type='+row.charge_type;
		}
		url = "<?= $this ->Url ->build(['prefix' =>'admin', 'controller' =>'Bill', 'action' =>'detail',$department_id]);?>/"+value+"/<?= $start?>/<?= $end?>";
		if(param != ""){
			url += param;
		}
		return '<a href="'+url +'">查看明细</a>' ;
	}

	function timestrap2date(value) {
      var now = new Date(parseInt(value) * 1000);
      return now.toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ");
    }

    function clickDepartment(){
    	department_id = $('#select-depart').val();
    	location.href = "/admin/Bill/subject/"+department_id+"/<?=$start?>/<?=$end?>";
    }

</script>
<?php
$this->end();
?>