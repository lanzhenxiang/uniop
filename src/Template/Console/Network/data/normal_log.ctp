<?= $this->Html->css(['network/hosts']); ?>
<?php if($type == "desktop"){ ?>
    <?= $this -> element('desktop/lists/left', ['active_action' => 'desktop']); ?>
<?php }else{?>
<?= $this -> element('network/lists/left', ['active_action' => 'hosts']); ?>
<?php }?>
<?php if(isset($action)){
	if($action != 'desktop'){
		if($action == 'excp'){ ?>
<!--<?= $this->element('excp/left',['active_action'=>$action]); ?>-->
<?php	}else if($action == 'firewall'){ ?>
<!--<?= $this->element('security/left',['active_action'=>'firewall']); ?>-->
<?php }else{ ?>
<!--<?= $this->element('network/lists/left',['active_action'=>$action]); ?>-->
<?php }}} ?>

<?= $this->Html->css('jsonFormater.css'); ?>
<?= $this->Html->css('bootstrap-datetimepicker.min.css'); ?>

<div class="wrap-nav-right hosts-content">
    <?= $this -> element('network/lists/left_nav', ['active_action' => 'normal_log','id'=>$_data['0']['H_ID']]); ?>
        <div class="normal-log hosts-right clearfix">
            <div class=" <?php if(isset($action)){ if($action == 'desktop' ){ echo 'wrap-nav-right-left';}} ?>">
                <div class="">
                    <div class="host-static" style="margin:0">
                        <h5 class="title">  
                            <?= $this -> element('network/data/breadcrumb', ['breadcrumbTitle' => '正常日志','biz_tid' => $_data['0']['Biz_tid']]); ?>
                        </h5>
                    </div>
                    <div class="center hosts-table" id='task_panel'>
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
                            <div class="panel-body" style="color:#333;padding-left:10px;font-size:16px;background:#DFF0D8;display: inline-block;width: 100%">
                                暂无正常日志
                            </div>
                        </div>
                        <?php }?>
                        <div id="expc-navigation">

                        </div>
                    </div>
                    <div id="navigation">
                        <a href="<?= $this -> Url -> build(['controller' => 'network', 'action' => 'getTask',$_data['0']['H_ID'],'normal','10','2']); ?>"></a>
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
        </div>

</div>
</div>

<?= $this->Html->script(['jsonFormater.js','jquery.infinitescroll.min.js','bootstrap-datetimepicker.js']); ?>
<script>
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
        });


   </script>
