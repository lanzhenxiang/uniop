<?php if(isset($action)){
	if($action != 'desktop'){
		if($action == 'excp'){ ?>


			<?= $this->element('excp/left',['active_action'=>$action]); ?>
	<?php	}else if($action == 'firewall'){ ?>
	<?= $this->element('security/left',['active_action'=>'firewall']); ?>
	<?php }else if($action == 'securityGroup'){ ?>
<?= $this->element('security/left',['active_action'=>'security_group']); ?>
<?php }else if($action == 'recycle'){ ?>
	<?php }else{ ?>
	<?= $this->element('network/lists/left',['active_action'=>$action]); ?>
<?php }}} ?>

<?= $this->Html->css('jsonFormater.css'); ?>
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
	.json-formater{
		padding:10px 0;
	}
</style>
<div class="wrap-nav-right <?php if(isset($action)){ if($action == 'desktop' || $action == 'recycle' ){ echo 'wrap-nav-right-left';}} ?>">
	<div class="wrap-manage">
		<div class="top">
		<?php if($interface =='excp'){ ?>
			<span class="title">异常日志</span>
		<?php }else if($interface =='normal'){ ?>
			<span class="title">正常日志</span>
			<?php }else if($interface=='executing'){ ?>
			<span class="title">执行中日志</span>
			<?php }?>
		</div>
		<div class="center" id='task_panel'>
		<?php if($action == 'excp'){ ?>
			<div class="expc-navi">
				<span class="text-light">租户:</span>&nbsp;&nbsp;
				<select onchange="jump()" id='select_jump'>
				<option <?php if(isset($department_id)){ if($department_id==0){ echo 'selected';}} ?> value="0">全部</option>
				<?php if(isset($dept_grout)){ if(!empty($dept_grout)){ foreach ($dept_grout as $value){ ?>
					<option  <?php if(isset($department_id)){ if($department_id==$value['id']){ echo 'selected';}} ?> value="<?php echo $value['id'] ?>"><?php echo $value['name'];?></option>

					<?php }}else{ ?>
						<option <?php if($department_id==$this->request->session()->read('Auth.User.department_id')){ echo 'selected';} ?> value="<?php echo $this->request->session()->read('Auth.User.department_id') ?>"><?php echo $this->request->session()->read('Auth.User.department_name');?></option>
					<?php } }?>
				</select>

				<span class="text-light">&#160;操作类型:</span>&nbsp;&nbsp;
				<select onchange="jump_operation()" id='select_operation'>
				<option <?php if(isset($operation_code)){ if($operation_code=='all'){ echo 'selected';}} ?> value="all">全部</option>
				<?php if(isset($operation_data)){ if(!empty($operation_data)){ foreach ($operation_data as $key=> $value){ ?>
					<option  <?php if(isset($operation_code)){ if($operation_code==$key){ echo 'selected';}} ?> value="<?php echo $key ?>"><?php echo explode('_',$key)[0].'_'.$value['name'];?></option>

					<?php }}}?>
				</select>


				<div class="pull-right order-number input-append date" id="datetimepicker-end" data-date-format="yyyy-mm-dd" style="height:24px;line-height:24px;">
					<input size="16" type="text" name="time" id="end-time" value="<?php if($end !=0 ) {echo $end;}?>" readonly style="height:24px;width:140px;line-height:24px;margin-left:5px;">
					<span class="add-on"><i class="icon-th"></i></span>
				</div>
				<div class="pull-right order-number input-append date" id="datetimepicker-start"  data-date-format="yyyy-mm-dd" style="height:24px;line-height:24px;">
					<input size="16" type="text" name="time" id="start-time" value="<?php if($start !=0 ) {echo $start;}?>" readonly style="height:24px;width:140px;line-height:24px;margin-left:5px;">
					<span class="add-on"><i class="icon-th"></i></span>
				</div>
			</div>
		<?php } ?>
			<?php $pass='';
			if(!empty($_request_params['pass']) && count($_request_params['pass'])>1){
				$pass = $_request_params['pass'][1];
			}else{
				$pass = '';
			}
			if($pass == 'hosts' || $pass == 'disks' || $pass == 'router' || $pass == 'subnet' || $pass == 'elb' || $pass == 'eip' || $pass == 'vpc' || $pass=='desktop' || $pass=='firewall' || $pass == 'recycle'){

				if($pass=='firewall'){ ?>
				<div class="text-right" style="padding:10px 0">
					<a href="<?= $this->Url->build(['controller'=>'security','action'=>'lists',$pass]); ?>" class="btn btn-addition">返回列表</a>
				</div>
				<?php }else if($pass=='desktop'){ ?>
					<div class="text-right" style="padding:10px 0">
					<a href="<?= $this->Url->build(['controller'=>'desktop','action'=>'lists']); ?>" class="btn btn-addition">返回列表</a>
				</div>
				<?php }else if($pass=='recycle'){ ?>
					<div class="text-right" style="padding:10px 0">
					<a href="<?= $this->Url->build(['controller'=>'recycled','action'=>'index']); ?>" class="btn btn-addition">返回列表</a>
				</div>
				<?php }else{ ?>

			<div class="text-right" style="padding:10px 0">
				<a href="<?= $this->Url->build(['controller'=>'network','action'=>'lists',$pass]); ?>" class="btn btn-addition">返回列表</a>
			</div>
			<?php }} ?>
			<div id="expc-message">
		<?php if(!empty($task_data)){
			foreach ($task_data as $value){
		?>
			<div class="expc-content">
				<div class="expc-header">
					<img src="/images/expc-host.png" class="expc-header-pin" />
					<h5>
						<?php echo $value['name']; ?>
					</h5>
					<p><?php echo date('Y-m-d',$value['create_time']); ?></p>
					<p><?php echo date('H:i',$value['create_time']); ?></p>
				</div>
				<div class="panel panel-default">
					<div class="panel-body">
						<ul class="clearfix">
							<li>
								设备名称
								&nbsp;&nbsp;
								<span><?php if(!empty($value['instance_basic'])){
									echo $value['instance_basic']['name'];
								}else{
									echo '';
								} ?></span>
							</li>
							<li>
								任务ID
								&nbsp;&nbsp;
								<span><?php echo $value['task_id'] ?></span>
							</li>
							<li>
								上级任务ID
								&nbsp;&nbsp;
								<span><?php echo $value['parent_id'] ?></span>
							</li>
							<li>
								根任务ID
								&nbsp;&nbsp;
								<span><?php echo $value['root_id'] ?></span>
							</li>
						</ul>
					</div>
				</div>
				<div class="expc-body">
					<h5>请求数据</h5>
					<div class="http-request" style="display:none;">
						<?php echo $value['request_data'] ?>
					</div>
					<div class="json-formater"></div>
					<h5>返回数据</h5>
					<div class="http-response" vas="<?php echo $value['task_id'] ?>" style="display:none;"><?php

					if(strlen($value['response_asyn_data']) !=0){
                        json_decode($value['response_asyn_data']);
                        if((json_last_error() == JSON_ERROR_NONE)){
                           echo $value['response_asyn_data'];
                        }else{
                            echo "底层服务器错误或参数有误";
                        }

					}
					 ?></div>
					<div class="json-formater" id='json-formater<?php echo $value['task_id'] ?>'></div>
					<a href='javascript:;' onclick="message(<?php echo $value['task_id']; ?>)" data-toggle="modal" data-target="#modal">查看详细信息</a>
				</div>
                </div>
                 <?php } }else{ ?>
                 		<div class="panel panel-default">
						  <div class="panel-body" style="color:#333;padding-left:10px;font-size:16px;background:#DFF0D8">
						    	<?php if($interface =='excp'){ ?>
									恭喜您，没有异常信息
								<?php }else if($interface =='normal'){ ?>
									暂无正常日志
								<?php }else if($interface=='executing'){ ?>
							  暂无执行中日志
							  <?php }?>
						  </div>
						</div>
                 <?php }?>
				<div id="expc-navigation">

							</div>
				        </div>
				        <div id="navigation">
			            	<a href="<?= $this -> Url -> build(['controller' => 'excp', 'action' => 'gettask',$interface,$action,'10','2',$department_id,$operation_code,$start,$end,$id]); ?>"></a>
			        	</div>

				    </div>
				</div>
			</div>
			<div class="modal fade" id="modal" role="dialog" style="height: 900px">
			  <div class="modal-dialog" role="document">
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
			        <h4 class="modal-title">返回数据详情</h4>
			      </div>
			      <div class="modal-body" >
<textarea id="body-m" style="height: 400px;width:560px"></textarea>
			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>

			      </div>
			    </div>
			  </div>
			</div>


<?= $this->Html->script(['jsonFormater.js','jquery.infinitescroll.min.js','bootstrap-datetimepicker.js']); ?>
 <script>

	$(document).ready(
			function(){
				var myDate = new Date();
				var year = myDate.getFullYear();
				var month =myDate.getMonth()+1;
				var day =  myDate.getDate()+1;
				var time =year+'-'+month+'-'+day;
				$('#datetimepicker-end').datetimepicker({
					format: "yyyy-mm-dd hh:ii:ss",
					autoclose:true,
					maxView:4,
					startDate:'<?= $start?>',
					endDate:time
				}
				).on('changeDate', function(){
					var depart_id = $('#select_jump').val();
				    var operation_code = $('#select_operation').val();
					var end = $('#end-time').val();
					location.href='<?= $this -> Url -> build(['controller' => 'excp', 'action' => 'lists',$interface,'excp']); ?>/'+depart_id+'/'+operation_code+'/<?php echo $start; ?>'+'/'+end;
				});
				$('#datetimepicker-start').datetimepicker({
					format: "yyyy-mm-dd hh:ii:ss",
					autoclose:true,
					maxView:4,
					endDate:'<?= $end?>',
				}
				).on('changeDate', function(){
					var depart_id = $('#select_jump').val();
				    var operation_code = $('#select_operation').val();

					var start = $('#start-time').val();
					location.href='<?= $this -> Url -> build(['controller' => 'excp', 'action' => 'lists',$interface,'excp']); ?>/'+depart_id+'/'+operation_code+'/'+start+'/<?php echo $end; ?>';
				});
			}
			);


		function jump(){
			var depart_id = $('#select_jump').val();
			var operation_code = $('#select_operation').val();

			location.href='<?= $this -> Url -> build(['controller' => 'excp', 'action' => 'lists',$interface,'excp']); ?>/'+depart_id+'/'+operation_code+'/<?php echo $start; ?>/<?php echo $end; ?>';
		}

		function jump_operation(){
			var depart_id = $('#select_jump').val();
		    var operation_code = $('#select_operation').val();

			location.href='<?= $this -> Url -> build(['controller' => 'excp', 'action' => 'lists',$interface,'excp']); ?>/'+depart_id+'/'+operation_code+'/<?php echo $start; ?>/<?php echo $end; ?>';

		}


 		function message(task_id){
            $.ajax({
                type: "POST",
                url: '<?php echo $this->Url->build(array('controller'=>'excp','action'=>'messagedata'));?>',
                data: {task_id: task_id},
                success: function (data) {
                	data = $.parseJSON(data);

                    $('#body-m').val(data.response_asyn_data);
                }
            });
		}



    	$('.http-response').each(
    		function(){

        	    if($(this).html().length !=0){

        	    	 var str = $(this).html().substr(0, 1)
        	    	if(str =='{'){
        	    		var jsonDom = $(this).next();
            			var jsonFormater = new JsonFormater({dom:jsonDom});
            			jsonFormater.doFormat($(this).html());
            	    }else{
                	    data = "<span style='color: #ff0000;'>"+$(this).html()+"</span>";
            	    	$('#json-formater'+$(this).attr('vas')).html(data);
                	}

            	}else{
        			$('#json-formater'+$(this).attr('vas')).html('');
            	}

    		}
    	);

    	$('.http-request').each(
    		function(){
    			var jsonDom = $(this).next();
    			var jsonFormater = new JsonFormater({dom:jsonDom});
    			jsonFormater.doFormat($(this).html());
    		}
    	);

    	var _renderItem = function(data) {
    		var time = data.create_time;
    		var date = new Date(parseInt(time) * 1000);
    		var month = date.getMonth()+1;
    		if(month<10){
    			month = '0'+month;
        	}
    		var day =  date.getDate();
    		if(day<10){
    			day = '0'+day;
        	}
    		var times = [date.getFullYear(), month, day].join('-');

    		var hour = date.getHours();
    		if(hour<10){
    			hour = '0'+hour;
        	}

    		minute =  date.getMinutes();
    		if(minute<10){
    			minute = '0'+minute;
        	}

    		var time_hour = [hour, minute].join(':');

    		var response='';
    		if(data.response_asyn_data.length !=0){
    			var str = data.response_asyn_data.substr(0, 1);
    			//console.log(str);
    			if(str =='{'){
    				response =data.response_asyn_data;
        		}else{
        			response ='底层服务器错误或参数有误';
            	}
        	}else{
        		response='';
            }

    		 if(data.instance_basic!=null){

    			var dom = '<div class="expc-content"><div class="expc-header"><img src="/images/expc-host.png" class="expc-header-pin" />' +
				'<h5>' + data.name + '</h5><p>'+times+'</p><p>'+time_hour+'</p></div><div class="panel panel-default"><div class="panel-body"><ul class="clearfix">' +
				'<li>设备名称&nbsp;&nbsp;<span>' + data.instance_basic.name + '</span></li><li>任务ID&nbsp;&nbsp;<span>' + data.task_id + '</span>' +
				'</li><li>上级任务ID&nbsp;&nbsp;<span>' + data.parent_id + '</span></li><li>根任务ID&nbsp;&nbsp;<span>' + data.root_id + '</span></li></ul></div></div><div class="expc-body"><h5>请求数据</h5><div class="http-request" style="display:none;">' +
				data.request_data + '</div><div class="json-formater"></div><h5>返回数据</h5><div class="http-response" vas="'+data.task_id +'" style="display:none;">' + response + '</div><div class="json-formater"  id="json-formater'+data.task_id +'"></div><a href="javascript:;" onclick="message('+data.task_id+')" data-toggle="modal" data-target="#modal">查看详细信息</a></div></div>';
        	}else{
        		var dom = '<div class="expc-content"><div class="expc-header"><img src="/images/expc-host.png" class="expc-header-pin" />' +
				'<h5>' + data.name + '</h5><p>'+times+'</p><p>'+time_hour+'</p></div><div class="panel panel-default"><div class="panel-body"><ul class="clearfix">' +
				'<li>设备名称&nbsp;&nbsp;<span></span></li><li>任务ID&nbsp;&nbsp;<span>' + data.task_id + '</span>' +
				'</li><li>上级任务ID&nbsp;&nbsp;<span>' + data.parent_id + '</span></li><li>根任务ID&nbsp;&nbsp;<span>' + data.root_id + '</span></li></ul></div></div><div class="expc-body"><h5>请求数据</h5><div class="http-request" style="display:none;">' +
				data.request_data + '</div><div class="json-formater"></div><h5>返回数据</h5><div class="http-response" vas="'+data.task_id +'" style="display:none;">' + response + '</div><div class="json-formater" id="json-formater'+data.task_id +'"></div><a href="javascript:;" onclick="message('+data.task_id+')" data-toggle="modal" data-target="#modal">查看详细信息</a></div></div>';
            }

            return dom;
        }

    	$("#expc-message").infinitescroll({
            navSelector : "#navigation",
            nextSelector : "#navigation a",
            itemSelector : "#expc-navigation",
            debug : true,
            dataType : "json",
            appendCallback  : false
        },function(response){

            $content = $('#expc-navigation');
            $.each(response,function(i,n){
                var item = $(_renderItem(n));
                $content.append(item);
            });
            $('.http-response').each(
	    		function(){

	        	    if($(this).html().length !=0){

	        	    	 var str = $(this).html().substr(0, 1)
	        	    	if(str =='{'){
	        	    		var jsonDom = $(this).next();
	        	    		aStr = $(this).html();
	            			var jsonFormater = new JsonFormater({dom:jsonDom});
							var str=aStr.replace(/[\r\n]/g,"");//去掉json字符串中的回车换行符
	            			jsonFormater.doFormat(str);
	            	    }else{
	                	    data = "<span style='color: #ff0000;'>"+$(this).html()+"</span>";
	            	    	$('#json-formater'+$(this).attr('vas')).html(data);
	                	}

	            	}else{
	        			$('#json-formater'+$(this).attr('vas')).html('');
	            	}
	    		}
	    	);

	    	$('.http-request').each(
	    		function(){
	    			var jsonDom = $(this).next();
	    			var jsonFormater = new JsonFormater({dom:jsonDom});
	    			jsonFormater.doFormat($(this).html());
	    		}
	    	);
        });


   </script>
