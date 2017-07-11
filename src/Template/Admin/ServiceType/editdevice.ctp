<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
	<div id="maindiv-alert"   class="content-list-page"></div>
	<form class="form-horizontal">
		<div class="dropdown pull-left" id="selects">租户
           
			<a href="javascript:;"  class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <span class="pull-left" id="dept" val="0">全部</span>
                    <span class="caret"></span>
                </a>
			
			<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
				<li><a href="javascript:;" onclick="dept(0,'全部')">全部</a></li>
                    <?php foreach ($dept_grout as $key => $value){?>
                        <li><a href="javascript:;"
					onclick="dept(<?= $value['id']?>,'<?php echo $value['name'];?>')">
                                <?php echo $value['name'];?>
                            </a></li>
                    <?php }?>
                </ul>
		</div>

		  <div class="dropdown pull-left" style="margin-left: 30px;">设备类型
           
			<a href="javascript:;"  class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <span class="pull-left" id="type" val="total">全部</span>
                    <span class="caret"></span>
                </a>
			<ul class="dropdown-menu">
				<li><a href="javascript:;" onclick="types('total','全部')">全部</a></li>
                  <li><a href="javascript:;" onclick="types('hosts','主机')">主机</a></li>
                  <li><a href="javascript:;" onclick="types('desktop','云桌面')">云桌面</a></li>
                </ul>
        </div> 

		
		<div class="dropdown pull-left" style="margin-left: 30px;"> 厂商
		<a href="javascript:;"  class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <span class="pull-left" id="agent" val="total">全部</span>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="javascript:;" onclick="local('total','total','全部')">全部</a></li>
                    <?php if(isset($agent)){
                        foreach($agent as $value) {
                            if ($value['parentid'] == 0) {
                    ?>
                         <li><a href="javascript:;" onclick="local(<?php echo $value['id'] ?>,'<?php echo $value['class_code'] ?>','<?php echo $value['agent_name'] ?>')"><?php echo $value['agent_name'] ?></a></li>
                    <?php }}} ?>
                </ul>
      
            </div>
            <div class="dropdown pull-left" style="margin-left: 15px;">地域
                <a  href="javascript:;" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <span class="pull-left" id="agent_t" val="total">全部</span>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu" id="agent_two"></ul>
          
            </div>
		
		
		<div class="input-group content-search pull-right"
			style="margin-bottom: 10px;">
			<input type="text" class="form-control" name="search" id="search"
				placeholder="搜索设备名称或主机名称或code..."> <span class="input-group-btn">
				<button class="btn btn-primary" id="search-button" type="button">搜索</button>
			</span>
		</div>
		<table class="table table-striped">
			<input name="checkHost" type="hidden" id="checkHost">
			<input name="server_id" type="hidden"
				value="<?php if(isset($server_id)){ echo $server_id;} ?>">
			<thead>
				<tr>
					<th><input id="all-host" type="checkbox" /></th>
					<th>id</th>
					<th>code</th>
					<th>设备名称</th>
					<th>区域</th>
					<th>租户</th>
					<th>主机名称</th>
					<th>主机ip</th>
				</tr>
			</thead>
			<tbody id="host-content">
                <?php if(isset($query)){
                    foreach($query['hosts']['data'] as $value){
                        ?>
                        <tr>
					<td><input name="host" onclick='check(this)'
						value="<?php if(isset($value['basic_id'])){ echo $value['basic_id'];} ?>"
						type="checkbox" /></td>
					<td><?php if(isset($value['basic_id'])){ echo $value['basic_id'];} ?></td>
					<td><?php if(isset($value['code'])){ echo $value['code'];} ?></td>
					<td><?php if(isset($value['devicename'])){ echo $value['devicename'];} ?></td>
					<td><?php if(isset($value['location_name'])){ echo $value['location_name'];} ?></td>
					<td><?php if(isset($value['dept_name'])){ echo $value['dept_name'];} ?></td>
					<td><?php if(isset($value['hostname'])){ echo $value['hostname'];} ?></td>
					<td><?php if(isset($value['ip'])){ echo $value['ip'];} ?></td>
				</tr>
                    <?php
                    }
                } ?>
                </tbody>
		</table>
		<div id='pages' class="content-pagination clearfix">
			<nav class="pull-right">
				<ul id='example' attrs="example">
				</ul>
			</nav>
		</div>

</div>
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
		<a type="submit" id="ds" class="btn btn-primary">保存</a> <a
			type="submit"
			href="<?php echo $this->Url->build(array('controller' => 'ServiceType','action'=>'index')); ?>"
			class="btn btn-danger">返回</a>
	</div>
</div>
</form>
</div>

<?=$this->Html->script(['adminjs.js','jquery.cookie.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
        //分页
       	$(function() {
	        var name ="<?php if(isset($name)){ echo $name;} ?>";
	        $('#searchtext').val(name);
	        var depart_name ="<?php echo $department_name; ?>";
	        var depart_id ="<?php echo $department_id; ?>";
	        $('#dept').html(depart_name); 
	        $('#dept').attr('val',depart_id);
       
    	})
        function paging(page){
            var search = $("#search").val();
            var department_id =$('#dept').attr('val');
            var type =$('#type').attr('val');
            var class_code = $('#agent').attr('val');
        	var class_code2 = $('#agent_t').attr('val');
           
            $.ajax({
                type: "GET",
                url: "<?php echo $this->Url->build(array('controller' => 'ServiceType','action'=>'checkhost')); ?>/"+page+"/"+department_id+"/"+type+"/"+class_code+"/"+class_code2+"/"+search,
                dataType:"json",
                success: function(msg){
                    if(msg.data){
                        var type = '';
                        $.each(msg.data, function(i, n){
                            if (n.basic_id) {
                                type+='<tr><td><input name="host" onclick="check(this)" value="'+n.basic_id+'"  type="checkbox"/></td><td>'+n.basic_id+'</td><td>'+n.code+'</td><td>'+n.devicename+'</td><td>'+n.location_name+'</td><td>'+n.dept_name+'</td><td>'+n.hostname+'</td><td>'+n.ip+'</td></tr>';
                            }
                        });
                        $("#host-content").html(type);
                        checkCheck();//已选中的打钩
                    }
                }
            });
        }
        //分页
        var options = {
            alignment:'right',
            bootstrapMajorVersion:10,
            currentPage: <?= $page?>,
            numberOfPages: 8,
            totalPages:<?= $query['hosts']['total']?>,
            itemTexts: function (type, page, current) {
                switch (type) {
                    case "first":
                        return "<<";
                    case "prev":
                        return "<";
                    case "next":
                        return ">";
                    case "last":
                        return ">>";
                    case "page":
                        return page;
                }
            }
        }

        //更新分页
        function pageing(datas){
            var element = $('#example');//对应下面ul的ID
            var options = {
                alignment:'right',
                bootstrapMajorVersion:10,
                currentPage: datas.page,
                numberOfPages: 8,
                totalPages:datas.total,
                itemTexts: function (type, page, current) {
                    switch (type) {
                        case "first":
                            return "<<";
                        case "prev":
                            return "<";
                        case "next":
                            return ">";
                        case "last":
                            return ">>";
                        case "page":
                            return page;
                    }
                }
            }
            element.bootstrapPaginator(options);
        }

        $('#example').bootstrapPaginator(options);//填充分页

        //添加cookie
        $(function(){
            var hostId = "<?php echo $basic_id;?>";
            $.cookie("checkHost",hostId);
            str = $.cookie("checkHost");
            checkCheck();
        })

        //点击桌面是修改cookie
        function check(obj){
            str = $.cookie("checkHost");
            strs=str.split(",");
            if(obj.checked){
                strs[strs.length]=obj.value;
            }else{
                for(var i=0;i<strs.length;i++){
                    if(strs[i]===obj.value){
                        strs.splice(i,1);
                        i--;
                    }
                }
            }
            $.cookie("checkHost",strs);
            str = $.cookie("checkHost");
            $("#checkHost").val(str);

            checkAll();
        }

        //检查是否全选
        function checkAll(){
            var imagelen = $("input:checkbox[name='host']:checked").length;
            var imagelens =$("input:checkbox[name='host']").length;
            if(imagelen == imagelens){
                $('#all-host').prop('checked','true');
            }else{
                $('#all-host').prop('checked','');
            }
        }

        //更具cookie的值添加check
        function checkCheck(){
            str = $.cookie("checkHost");
            strs=str.split(",");
            $("input:checkbox[name='host']").each(function(){
                for(var i=0;i<strs.length;i++){
                    if(strs[i]===$(this).val()){
                        $(this).prop('checked','true')
                    }
                }
            });
            checkAll();
            $("#checkHost").val(strs);
        }

        $('#all-host').on('click',function(){
            str = $.cookie("checkHost");
            var checked=true;
            strs=str.split(",");
            if($('#all-host').is(":checked")){
                $("input:checkbox[name='host']").prop('checked','true');
            }else{
                $("input:checkbox[name='host']").prop('checked','');
            }
            $("input:checkbox[name='host']").each(function(){
                if($(this).prop("checked")==true){
                    checked = jQuery.inArray($(this).val(), strs);
                    if(checked<0){
                        strs[strs.length]=$(this).val();
                    }
                }else{
                    for(var i=0;i<strs.length;i++){
                        if(strs[i]==$(this).val()){
                            strs.splice(i,1);
                            i--;
                        }
                    }
                }
            });
            $.cookie("checkHost",strs);
            str = $.cookie("checkHost");
            $("#checkHost").val(str);
        })

        $(function(){
            var imagelen = $("input:checkbox[name='host']:checked").length;
            var imagelens =$("input:checkbox[name='host']").length;
            if(imagelen == imagelens){
                $('#all-host').prop('checked','true');
            }else{
                $('#all-host').prop('checked','');
            }
        })


        $("#ds").click(function(){
                var validate=true;
                if(validate){
                    $.ajax({
                        type: 'post',
                        url: '<?php echo $this->Url->build(array('controller' => 'ServiceType','action'=>'posthost')); ?>',
                        data: $("form").serialize(),
                        success: function(data) {
                            var data = eval('(' + data + ')');
                            if(data.code==0){
                                $.cookie("checkHost",'');
                                tentionHide(data.message,0);
                                location.href='<?php echo $this->Url->build(array('controller'=>'ServiceType','action'=>'index'));?>';
                            }else{
                                tentionHide(data.message,1);
                            }
                        }
                    });
                }
            }
        );

        //搜索按钮
        $("#search-button").on('click',function(){
            var search = $("#search").val();
            var department_id =$('#dept').attr('val');
            var type =$('#type').attr('val');
            var class_code = $('#agent').attr('val');
        	var class_code2 = $('#agent_t').attr('val');
            $.ajax({
                url: '<?php echo $this->Url->build(array('controller' => 'ServiceType','action'=>'checkhost')); ?>/1/'+department_id+'/'+type+'/'+class_code+'/'+class_code2+'/'+search,
                success: function(data) {
                    datas = $.parseJSON(data);
                    if(datas){
                        var type = '';
                        $.each(datas.data, function(i, n){
                            if (n.basic_id) {
                                 type+='<tr><td><input name="host" onclick="check(this)" value="'+n.basic_id+'"  type="checkbox"/></td><td>'+n.basic_id+'</td><td>'+n.code+'</td><td>'+n.devicename+'</td><td>'+n.location_name+'</td><td>'+n.dept_name+'</td><td>'+n.hostname+'</td><td>'+n.ip+'</td></tr>';
                            }
                        });
                    }
                    $("#host-content").html(type);
                    checkCheck();//已选中的打钩   
                    if(datas.data.length!=0){
                    	pageing(datas);
                    	 $('#pages').show(); 
                    }else{
                       $('#pages').hide(); 
                    }
                }
            });
        })
        
        //租户
        function dept(id,dept_name){
        	var name = $('#search').val();
        	$('#dept').html(dept_name);
        	if(!name){
            	name='';
        	}
        	var class_code = $('#agent').attr('val');
        	var class_code2 = $('#agent_t').attr('val');
        	var type =$('#type').attr('val');
        	$('#dept').attr('val',id)
        	$.ajax({
                url: '<?php echo $this->Url->build(array('controller' => 'ServiceType','action'=>'checkhost')); ?>/1/'+id+'/'+type+'/'+class_code+'/'+class_code2+'/'+name,
                success: function(data) {
                    datas = $.parseJSON(data);
                    if(datas){
                        var type = '';
                        $.each(datas.data, function(i, n){
                            if (n.basic_id) {
                                 type+='<tr><td><input name="host" onclick="check(this)" value="'+n.basic_id+'"  type="checkbox"/></td><td>'+n.basic_id+'</td><td>'+n.code+'</td><td>'+n.devicename+'</td><td>'+n.location_name+'</td><td>'+n.dept_name+'</td><td>'+n.hostname+'</td><td>'+n.ip+'</td></tr>';
                            }
                        });
                    }
                    $("#host-content").html(type);
                    checkCheck();//已选中的打钩
                    if(datas.data.length!=0){
                    	pageing(datas);
                    	 $('#pages').show(); 
                    }else{
                       $('#pages').hide(); 
                    }
               
                }
            });    	
            }

        //设备类型
        function types(type,hosts_desktop){
        	var name = $('#search').val();
        	$('#type').html(hosts_desktop);
        	$('#type').attr('val',type);
        	var class_code = $('#agent').attr('val');
        	var class_code2 = $('#agent_t').attr('val');
        	if(!name){
            	name='';
        	}
        	var department_id =$('#dept').attr('val');
        	$.ajax({
                url: '<?php echo $this->Url->build(array('controller' => 'ServiceType','action'=>'checkhost')); ?>/1/'+department_id+'/'+type+'/'+class_code+'/'+class_code2+'/'+name,
                success: function(data) {
                    datas = $.parseJSON(data);
                    if(datas){
                        var type = '';
                        $.each(datas.data, function(i, n){
                            if (n.basic_id) {
                                 type+='<tr><td><input name="host" onclick="check(this)" value="'+n.basic_id+'"  type="checkbox"/></td><td>'+n.basic_id+'</td><td>'+n.code+'</td><td>'+n.devicename+'</td><td>'+n.location_name+'</td><td>'+n.dept_name+'</td><td>'+n.hostname+'</td><td>'+n.ip+'</td></tr>';
                            }
                        });
                    }
                    $("#host-content").html(type);
                    checkCheck();//已选中的打钩
                    if(datas.data.length!=0){
                    	pageing(datas);
                    	 $('#pages').show(); 
                    }else{
                       $('#pages').hide(); 
                    }
               
                }
            });    	
            }

        //区域选择
        function local(id,class_code,agent_name) {
            if (agent_name) {
                $('#agent_t').html('全部');
                $('#agent_t').attr('val','total');
                $('#agent').html(agent_name);
                $('#agent').attr('val', class_code);
                var search= $("#search").val();
                console.log(search);
                var department_id =$('#dept').attr('val');
                var type =$('#type').attr('val');
                var jsondata = <?php echo json_encode($agent); ?>;
                $.ajax({
                    url: '<?php echo $this->Url->build(array('controller' => 'ServiceType','action'=>'checkhost')); ?>/1/'+department_id+'/'+type+'/'+class_code+'/'+class_code+'/'+search,
                    success: function(data) {
                        datas = $.parseJSON(data);
                        if(datas){
                            var type = '';
                            $.each(datas.data, function(i, n){
                                if (n.basic_id) {
                                     type+='<tr><td><input name="host" onclick="check(this)" value="'+n.basic_id+'"  type="checkbox"/></td><td>'+n.basic_id+'</td><td>'+n.code+'</td><td>'+n.devicename+'</td><td>'+n.location_name+'</td><td>'+n.dept_name+'</td><td>'+n.hostname+'</td><td>'+n.ip+'</td></tr>';
                                }
                            });
                        }
                        $("#host-content").html(type);
                        checkCheck();//已选中的打钩
                        if(datas.data.length!=0){
                        	pageing(datas);
                        	 $('#pages').show(); 
                        }else{
                           $('#pages').hide(); 
                        }
                   
                    }
                });    	
                if(id!=0){
                    var data='<li><a href="javascript:;" onclick="local_two(\'total\',\'全部\',\'' + class_code + '\')">全部</a></li>';
                    $.each(jsondata, function (i, n) {
                        if(n.parentid == id){
                            data += '<li><a href="javascript:;" onclick="local_two(\'' + n.class_code + '\',\'' + n.agent_name + '\',\'' + class_code + '\')">' + n.agent_name + '</a></li>';
                        }
                    })
                $('#agent_two').html(data);
                 }else {
                    data = '';
                    $('#agent_two').html(data);
                }
            }
        }

        function local_two(class_code2,agent_name,class_code){
        	 var search= $("#search").val();
        	 var department_id =$('#dept').attr('val');
             var type =$('#type').attr('val');
            $('#agent_t').html(agent_name);
            $('#agent_t').attr('val',class_code2);
            $.ajax({
                url: '<?php echo $this->Url->build(array('controller' => 'ServiceType','action'=>'checkhost')); ?>/1/'+department_id+'/'+type+'/'+class_code+'/'+class_code2+'/'+search,
                success: function(data) {
                    datas = $.parseJSON(data);
                    if(datas){
                        var type = '';
                        $.each(datas.data, function(i, n){
                            if (n.basic_id) {
                                 type+='<tr><td><input name="host" onclick="check(this)" value="'+n.basic_id+'"  type="checkbox"/></td><td>'+n.basic_id+'</td><td>'+n.code+'</td><td>'+n.devicename+'</td><td>'+n.location_name+'</td><td>'+n.dept_name+'</td><td>'+n.hostname+'</td><td>'+n.ip+'</td></tr>';
                            }
                        });
                    }
                    $("#host-content").html(type);
                    checkCheck();//已选中的打钩
                    if(datas.data.length!=0){
                    	pageing(datas);
                    	 $('#pages').show(); 
                    }else{
                       $('#pages').hide(); 
                    }
               
                }
            });    	
        }
    </script>
<?= $this->end() ?>