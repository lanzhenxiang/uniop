<!-- 子网 -->
<?= $this->element('network/lists/left',['active_action'=>'subnet']); ?>

<div class="wrap-nav-right">

	<div class="wrap-manage">
        <div class="top">
            <span class="title">子网列表</span>

            <div id="maindiv-alert"></div>
        </div>
		<div class="center clearfix">
            <button class="btn btn-addition" id="btn-refresh"><i class="icon-refresh"></i>&nbsp;&nbsp;刷新</button>
            <!-- <?php if (in_array('ccf_subnet_new', $this->Session->read('Auth.User.popedomname'))) { ?>
			<a class="btn btn-addition" href="<?= $this->Url->build(['controller'=>'network','action'=>'add','subnet']) ?>"><i class="icon-plus"></i>&nbsp;&nbsp;新建</a>
            <?php } ?> -->
            <!-- 跨租户新建子网 -->
              <?=$this->element('switchDepartment',['callback_url' => $this->Url->build(['controller' => 'network', 'action' => 'add', 'subnet']),'typeName'=>'子网'])?>
            <!-- 跨租户新建子网 -->
            <button class="btn btn-default" id="batchdelete" disabled = "disabled">
                <i class="icon-remove"></i>&nbsp;&nbsp;删除
            </button>
            <div class="pull-right">
            <input type="hidden" id="txtdeparmetId" value="<?= $_default["id"] ?>" />
              <?php if (in_array('ccf_all_select_department', $this->Session->read('Auth.User.popedomname'))) { ?>
              <div class="dropdown">
                租户:
                <a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button">
                    <span class="pull-left" id="deparmets" val="<?= $_default["id"] ?>"><?= $_default["name"] ?></span>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                      <?php foreach($_deparments as $value) { ?>
                         <li><a href="#" onclick="departmentlist(<?php echo $value['id'] ?>,'<?php echo $value['name'] ?>')"><?php echo $value['name'] ?></a></li>
                      <?php }?>
                </ul>
              </div>
              <?php }?>
                <div class="dropdown">
                厂商:
                <a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <span class="pull-left" id="agent" val="">全部</span>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="javascript:;" onclick="local(0,'','全部')">全部</a></li>
                    <?php if(isset($agent)){
                        foreach($agent as $value) {
                            if ($value['parentid'] == 0) {
                    ?>
                         <li><a href="#" onclick="local(<?php echo $value['id'] ?>,'<?php echo $value['class_code'] ?>','<?php echo $value['agent_name'] ?>')"><?php echo $value['agent_name'] ?></a></li>
                    <?php }}} ?>
                </ul>
            </div>
            <div class="dropdown">
                地域:
                <a  href="javascript:;" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <span class="pull-left" id="agent_t" val="">全部</span>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu" id="agent_two"></ul>
            </div>
      			<span class="search"><input type="text" id="txtsearch" name="search" placeholder="搜索">
                  <i class="icon-search"></i>
                </span>
                <!--<button class="btn btn-addition"><i class="icon-tag"></i>&nbsp;&nbsp;标签</button>-->
            </div>
		</div>
		<div class="bot ">

            <table id="table" data-toggle="table" data-pagination="true"
                   data-side-pagination="server" 
                   data-locale="zh-CN" data-click-to-select="true" data-url="<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','subnet','lists','?'=>['department_id'=>$_default['id']]]); ?>" data-pagination="true"   data-unique-id="id">
                <thead>
                <tr>
                    <th data-checkbox="true"></th>
                   <!-- <th data-field="id" >id</th>-->
                   <th data-field="id" >Id</th>
                    <th data-field="code" >子网Code</th>
                    <th data-field="name" id="name">子网名称</th>
                    <th data-field="routername">所属路由器</th>
                    <th data-field="cidr">网段</th>
                    <th data-field="cidr" data-formatter="formatter_cidr">默认网关</th>
                    <th data-field="status" data-formatter="formatter_state">状态</th>
                    <th data-field="location_name">部署区位</th>
                    <th data-field="isFusion" data-formatter="isfuion">虚拟化技术</th>
                    <th data-field="create_time" data-formatter=timestrap2date >创建时间</th>
                </tr>
                </thead>
            </table>

		</div>

	</div>
</div>

<div class="context-menu" id="context-menu">
    <ul>
        <?php if (in_array('ccf_subnet_change', $this->Session->read('Auth.User.popedomname'))) { ?>
        <li id="modify"><a href="javascript:;"><i class="icon-pencil"></i> 修改</a></li>
        <?php } ?>
        <?php if (in_array('ccf_subnet_delete', $this->Session->read('Auth.User.popedomname'))) { ?>
        <li id="delete"><a  href="javascript:;"><i class="icon-trash"></i> 删除</a></li>
        <?php } ?>
        <?php if (in_array('ccm_ps_interface_logs', $this->Session->read('Auth.User.popedomname'))) { ?>
        <li id="excp"><a  href="javascript:;"><i class="icon-book"></i> 异常日志</a></li>
        <?php } ?>
         <?php if (in_array('ccm_ps_interface_logs', $this->Session->read('Auth.User.popedomname'))) { ?>
        <li id="normal"><a  href="javascript:;"><i class="icon-book"></i> 正常日志</a></li>
        <?php } ?>
    </ul>
</div>


<!-- 修改 -->
<div class="modal fade" id="modal-modify" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">修改</h5>
            </div>
            <form id="modal-modify-form" action="" method="post">
                <div class="modal-body">
                    <p class="name"><span >子网名称</span><input id="modal-modify-name" name="name" type="text"></p>
                    <!--  <p class="name"><span>描述</span><textarea id="modal-modify-description" name="description" ></textarea>-->
                    <input id="modal-modify-id" name="id" type="hidden" />
                    </p>
                </div>
                <div class="modal-footer">
                    <button id="sumbiter" type="button" class="btn btn-primary" disabled="true">确认</button>
                    <button id="reseter" type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- 绑定路由 -->
<div class="modal fade" id="modal-modify" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">绑定理由</h5>
            </div>
            <form id="modal-modify-form" action="" method="post">
                <div class="modal-body">
                    <p class="name"><span >名称</span><input id="modal-modify-name" name="name" type="text"></p>
                    <!--  <p class="name"><span>描述</span><textarea id="modal-modify-description" name="description" ></textarea>-->
                    <input id="modal-modify-id" name="id" type="hidden" />
                    </p>
                </div>
                <div class="modal-footer">
                    <button id="sumbiter" type="button" class="btn btn-primary" disabled="true">确认</button>
                    <button id="reseter" type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                </div>
            </form>

        </div>
    </div>
</div>
<div id="maindiv"></div>
<?php
$this->start('script_last');
?>
<script type="text/javascript">

//input 存在一个被选中状态
$("#table").on('all.bs.table', function(e, row, $element) {
	if ($("tbody input:checked").length >= 1) {
		$(".center .btn-default").attr('disabled', false);
	} else {
		$(".center .btn-default").attr('disabled', true);
	}
})


$('#batchdelete').on('click', function() {
    var ids = '';
    var names = '';
    var rows = $('#table').bootstrapTable('getSelections');
    $.each(rows,function(e, i) {
        if (ids.length > 1) {
        	ids +=  ',';
        	names += ','
        }
    	ids += i.id;
    	names += i.name;
    })

    if (ids.length > 1) {
    	showModal('提示', 'icon-question-sign', '确认要删除子网', names, 'ajaxFun(\''+ids+'\',\'subnet_del\')');
    } else {
    	showModal('提示', 'icon-exclamation-sign', '请选中一个子网', '', '', 0, '我知道了');
    }
});

function ajaxFun(id, method) {
	$('#modal').modal("hide");
	var url = '';
	switch (method){
	case 'subnet_del':
		url = "/console/ajax/network/subnet/delSubnetAll";
		break;
	}

	$.ajax({
        url:url,
        data:{id:id},
        type: "post",
        success:function(data){
            data = $.parseJSON(data);
            if(data.Code== 0){
                var search = $("#txtsearch").val();
                var class_code = $("#agent").attr('val');
                var class_code2 = $("#agent_t").attr('val');
                $('#modal').modal("hide");
                $('#modal').on('hidden.bs.modal', function (e) {
                  $('#table').bootstrapTable('refresh', {
                        url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','subnet','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val()
                    });
                });
                layer.msg(data.Message);
                refreshTable();
            }else{
                $('#modal').modal("hide");
                layer.alert(data.Message);
                refreshTable();
//                tentionHide(data.Message,1);
            }
        }
    })
}


    //搜索绑定
    $("#txtsearch").on('keyup',
        function() {
            if(timer!=null){
                clearTimeout(timer);
            }
            var search= $("#txtsearch").val();
            var class_code = $("#agent").attr('val');

            var class_code2 = $("#agent_t").attr('val');
            var search= $("#txtsearch").val();
            var timer = setTimeout(function(){
                var agent = $("#agent").attr('val');
                var agent_t =$('#agent_t').attr('val');
                $('#table').bootstrapTable('refresh', {
                    url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','subnet','lists']); ?>?search=" + search+'&class_code='+class_code+'&class_code2='+class_code2+'&department_id='+$("#txtdeparmetId").val()
                });
            },1000);
        });
    function departmentlist(depart_id,depart_name){
        var search= $("#txtsearch").val();
        var class_code = $("#agent").attr('val');
        var class_code2 = $("#agent_t").attr('val');
        $('#txtdeparmetId').val(depart_id);
            $('#table').bootstrapTable('refresh', {
                url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','subnet','lists']); ?>?search=" + search+'&class_code='+class_code+'&class_code2='+class_code2+'&department_id='+$("#txtdeparmetId").val()
            });


    }

    function showModal(title, icon, content, content1, method) {
        $("#maindiv").empty();
        html = "";
        html += '<div class="modal fade" id="modal" tabindex="-1" role="dialog">';
        html += '<div class="modal-dialog" role="document">';
        html += '<div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
        html += '<h5 class="modal-title">' + title + '</h5>';
        html += '</div><div class="modal-body"><i class="'+icon+' icon-color-gray text-primary"></i>&nbsp;&nbsp;' + content + '<span class="text-primary">' + content1 + '</span></div>';
        html += '<div class="modal-footer"><button id="yes"  type="button" onclick="' + method + '" class="btn btn-primary">确认</button><button type="button" class="btn btn-danger" id="btnEsc" data-dismiss="modal">取消</button></div></div></div></div>';
        $("#maindiv").append(html);
        $('#modal').modal("show");
    }

    function local(id,class_code,agent_name) {
        if (agent_name) {
            $('#agent_t').html('全部');
            $('#agent').html(agent_name);
            $('#agent').attr('val', class_code);
            var search= $("#txtsearch").val();
            $('#table').bootstrapTable('refresh', {
                url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','subnet','lists']); ?>",
                query: {class_code: class_code,search: search,department_id:$("#txtdeparmetId").val()}
            });
            var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','subnet','lists']); ?>?class_code=" + class_code + "&search=" + search+'&department_id='+$("#txtdeparmetId").val();
            $('#table').attr('data-url',url);
            var jsondata = <?php echo json_encode($agent); ?>;
            if(id!=0){
                var data='<li><a href="javascript:;" onclick="local_two(\'\',\'全部\',\'' + class_code + '\')">全部</a></li>';
                $.each(jsondata, function (i, n) {
                    if(n.parentid == id){
                        data += '<li><a href="#" onclick="local_two(\'' + n.class_code + '\',\'' + n.agent_name + '\',\'' + class_code + '\')">' + n.agent_name + '</a></li>';
                    }
                })
            $('#agent_two').html(data);
             }else {
                data = '';
                $('#agent_t').attr('val',data);
                $('#agent_two').html(data);
            }
        }
    }

    function local_two(class_code2,agent_name,class_code){
        var search= $("#txtsearch").val();
        $('#agent_t').html(agent_name);
        $('#agent_t').attr('val',class_code2);
        $('#table').bootstrapTable('refresh', {
            url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','subnet','lists']); ?>",
            query: {class_code2: class_code2,class_code:class_code,search: search,department_id:$("#txtdeparmetId").val()}
        });
        var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','subnet','lists']); ?>?class_code=" + class_code + "&class_code2=" + class_code2 + "&search=" + search+'&department_id='+$("#txtdeparmetId").val();
        $('#table').attr('data-url',url);
    }

    //右键操作--------------------------------------------------
$('#table').not('tr:first').contextMenu('context-menu', {

    bindings: {
        //修改
        'modify':function(event){
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
            if(row){
                $('#modal-modify').modal("show");
                $('#modal-modify-name').val(row.name);
                $('#modal-modify-id').val(row.id);
            }

            index=$(event).attr('data-index');
            var name = $('#modal-modify-name').val();
            $('#modal-modify-name').on("input",
                function(){
                var name_ = $('#modal-modify-name').val();
                if(name != name_){
                    $('#sumbiter').prop('disabled',false);
                }else{
                    $('#sumbiter').prop('disabled',true);
                }
            })
            $('#sumbiter').one('click',function(){

                $.ajax({
                    method:'post',
                    url:'<?= $this->Url->build(['controller'=>'ajax','action'=>'network']); ?>/<?php echo "Subnet" ?>/<?php echo "updateSubnet" ?>',
                    data:$("#modal-modify-form").serialize(),
                    success:function(data){
                        data = $.parseJSON(data);
                        //console.debug(data);
                        if(data.code=='0000'){
                            $('#table').bootstrapTable('updateRow', {index: index, row: data.data});
                            $('#modal-modify').modal("hide");
                        }else{

                        }
                    }
                })
            })

        },
        /*'unbing':function(event){

            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
            console.log(row);
            if(row.router ==null){
                tentionHide('此子网还没有绑定路由',1);
            }else{
                showModal('提示', '确认要离开此路由？', row.router);
                $('#yes').one('click',function(){
                    $.ajax({
                        url:'<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','subnet','unbing']); ?>',
                        data:{routerCode:row.router,subnetCodes:row.code},
                        success:function(data){
                            data = $.parseJSON(data);
                            if(data.code=='0000'){
                                $('#table').bootstrapTable('refresh', {
                                    url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','subnet','lists']); ?>"
                                });
                                $('#modal').modal("hide");
                                tentionHide(data.msg,0);
                            }else{
                                $('#modal').modal("hide");
                                tentionHide(data.msg,1);
                            }
                        }
                    })
                })
            }

        },*/
        'bound':function(){
            $('#modal-bound').modal("show");
            console.log(event.id);
        },
        'built':function(){
            $('#modal-built').modal("show");
            console.log(event.id);
        },
        //删除
        'delete':function(event){

            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
            if(row.status!="创建中"){
                if(row){
                    showModal('提示', 'icon-question-sign', '确认要删除该子网', row.name);
                }
                $('#yes').one('click',function(){
                    $.ajax({
                        url:'<?= $this->Url->build(['controller'=>'ajax','action'=>'network']); ?>/<?php echo "Subnet" ?>/<?php echo "deleteSubnet" ?>',
                        data:{id:row.id,subnetCodes:row.code,routerCode:row.router},
                        success:function(data){
                            data = $.parseJSON(data);
                            if(data.code=='0000'){
                                var search = $("#txtsearch").val();
                                var class_code = $("#agent").attr('val');
                                var class_code2 = $("#agent_t").attr('val');
                                $('#modal').modal("hide");
                                /*tentionHide(data.msg,0);*/
                                $('#modal').on('hidden.bs.modal', function (e) {
                                  $('#table').bootstrapTable('refresh', {
                                        url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','subnet','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val()
                                    });
                                })
                            }else{
                                $('#modal').modal("hide");
                                layer.msg(data.msg);
//                                tentionHide(data.msg,1);
                            }
                        }
                    })
                })
            }else{
                showModal('提示','icon-exclamation-sign', '当前设备状态无法进行操作','');
                $("#btnEsc").html("关闭");
                $("#yes").remove();
                return false;
            }
        },
        'excp':function(event){
        	var uniqueId = $(event).attr('data-uniqueid');
        	var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
        	console.log(row);
        	var department_id = row.department_id;
        	window.location.href = "/console/excp/lists/excp/subnet/"+department_id+'/all/0/0/'+row.id;
        },
        //正常
        'normal':function(event){
        	var uniqueId = $(event).attr('data-uniqueid');
        	var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
        	var department_id = row.department_id;
        	window.location.href = "/console/excp/lists/normal/subnet/"+department_id+'/all/0/0/'+row.id;
        }

    }

});
//--------------------------------------------------------
//------------------------------------------

    $("#btn-refresh").on(
        "click",function(){
            var search= $("#txtsearch").val();
            var class_code = $("#agent").attr('val');
            var class_code2 =$("#agent_t").attr('val');
            $('#table').bootstrapTable('refresh', {
                url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','subnet','lists']); ?>",
                query: {class_code2:class_code2,class_code: class_code,search: search,department_id:$("#txtdeparmetId").val()}
            });
        }
    );

    //返回状态
    function formatter_state(value, row, index) {
        switch (value) {
            case "创建中":
            {
                return '<span id="imgState' + row.id + '" class="circle circle-create"></span><span id="txtState' + row.id + '">创建中</span>';
                break;
            }
            case "运行中":
            {
                return '<span id="imgState' + row.id + '" class="circle circle-run"></span><span id="txtState' + row.id + '">运行中</span>';
                break;
            }
            case "已停止":
            {
                return '<span id="imgState' + row.id + '" class="circle circle-stopped"></span><span id="txtState' + row.id + '">已停止</span>';
                break;
            }
            case "创建失败":
            {
                return '<span id="imgState' + row.id + '" class="circle circle-stopped"></span><span id="txtState' + row.id + '">创建失败</span>';
                break;
            }
            case "销毁中":
            {
                return '<span id="imgState' + row.id + '" class="circle circle-create"></span><span id="txtState' + row.id + '">销毁中</span>';
                break;
            }
            case "销毁失败":
            {
                return '<span id="imgState' + row.id + '" class="circle circle-stopped"></span><span id="txtState' + row.id + '">销毁失败</span>';
                break;
            }
            default:
            {
                return '<span id="imgState' + row.id + '" class="circle circle-create"></span>-';
            }
        }
    }


    function notifyCallBack(value){
        var search = $("#txtsearch").val();
        var department_id = $("#txtdeparmetId").val();
        var class_code = $("#agent").attr('val');
        var class_code2 = $("#agent_t").attr('val');
        var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','subnet','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+department_id;
        //console.log(value);
          if(value.MsgType=="success"||value.MsgType=="error"){
             if(value.Data.method=="subnet_del"||value.Data.method=="router_bind"||value.Data.method=="subnet"||value.Data.method=="subnet_add"){
                 $('#table').bootstrapTable('refresh', {
                     url: url,
                     silent: true
                 });
             }

         }
    }
    function formatter_cidr(value,row,index){
        if(value==null){
            return '-';
        }else{
            var arr=value.split('/')[0];
            var str =arr.substring(0,arr.length-1)+'1';
            return str;
        }
    }

    function isfuion(value){
        if(value==null){
            return '-';
        }else if(value == 'false'){
            return 'VMware';
        }else if(value == 'true'){
            return 'OpenStack';
        }
    }

    function tentionHide(content, state) {
        $("#maindiv-alert").empty();
        var html = "";
        if (state == 0) {
            html += '<div class="point-host-startup "><i></i>' + content + '</div>';
            $("#maindiv-alert").append(html);
            $(".point-host-startup ").slideUp(5000);
        } else {
            html += '<div class="point-host-startup point-host-startdown"><i></i>' + content + '</div>';
            $("#maindiv-alert").append(html);
            $(".point-host-startdown").slideUp(5000);
        }
    }
    function refreshTable(){
      var search = $("#txtsearch").val();
      var department_id = $("#txtdeparmetId").val();;
      var class_code = $("#agent").attr('val');
      var class_code2 = $("#agent_t").attr('val');
      $("#table").bootstrapTable('refresh', {
        url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','subnet','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val()
        });
    }
    
</script>
<?php
$this->end();
?>