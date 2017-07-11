<!-- author by lixin -->
<!-- TODO 添加反向引入列表在此 -->
<?= $this -> element('import/lists/left', ['active_action' => 'vpc']); ?>
<div class="wrap-nav-right">
	<div class="wrap-manage">
		<div class="top">
			<span class="title">VPC</span>
		</div>
		<div class="center clearfix">
			<button class="btn btn-addition" onclick="refreshTable()">
				<i class="icon-refresh">&nbsp;&nbsp;刷新</i>
			</button>
			<button class="btn btn-addition" onclick="reverseImport()">
				<i class="icon-signin">&nbsp;&nbsp;引入</i>
			</button>
			<!-- <button class="btn btn-addition">
				<i class="icon-inbox">&nbsp;&nbsp;引入硬盘</i>
			</button> -->
			<div class="pull-right">
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
	            <?php } ?>
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
			</div>
		</div>
        <input type="hidden" id="txtdeparmetId" value="<?= $_default["id"] ?>" />
		<div class="bot">
		<!-- TODO 主机列表table -->
		<table id="table" data-toggle="table"
    	 data-pagination="true" ="false"
    	 data-side-pagination="server"
    	 data-locale="zh-CN"
    	 data-click-to-select="true"
    	 data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'Import', 'hosts', 'vpclists','?'=>['department_id'=>$_default["id"]]]); ?>"
    	 data-unique-id="Identifier">
    	 <thead>
    	   <tr>
    		<th data-checkbox="true"></th>
	        <th data-field="id" ="true">Id</th>
	        <th data-field="code">VPCCode</th>
	        <th data-field="name">VPC名称</th>
	        <th data-field="display_name">部署区位</th>
    	  </tr>
    	</thead>
      </table>
		</div>
	</div>
</div>
<div id="maindiv"></div>
<?php $this -> start('script_last'); ?>
<script type="text/javascript">
function formatter_main(value, row, index) {
    if(value!=null&&value!=""){
        return '<a href="/console/network/data/hosts/' + row.H_ID + '">' + row.H_Code + '</a>';
    }else{
        return "-";
    }
}
function formatter_state(value, row, index) {
	switch (value) {
	case "创建中":
		{
			return '<span id="imgState' + row.H_ID + '" class="circle circle-create"></span><span id="txtState' + row.H_ID + '">创建中</span>';
			break;
		}
	case "运行中":
		{
			return '<span id="imgState' + row.H_ID + '" class="circle circle-run"></span><span id="txtState' + row.H_ID + '">运行中</span>';
			break;
		}
	case "已停止":
		{
			return '<span id="imgState' + row.H_ID + '" class="circle circle-stopped"></span><span id="txtState' + row.H_ID + '">已停止</span>';
			break;
		}
	case "创建失败":
		{
			return '<span id="imgState' + row.H_ID + '" class="circle circle-stopped"></span><span id="txtState' + row.H_ID + '">创建失败</span>';
			break;
		}
	case "销毁中":
		{
			return '<span id="imgState' + row.H_ID + '" class="circle circle-create"></span><span id="txtState' + row.H_ID + '">销毁中</span>';
			break;
		}
	case "销毁失败":
		{
			return '<span id="imgState' + row.H_ID + '" class="circle circle-stopped"></span><span id="txtState' + row.H_ID + '">销毁失败</span>';
			break;
		}
	default:
		{
			return '<span id="imgState' + row.H_ID + '" class="circle circle-create"></span>-';
		}
	}
}

function formatter_operateSystem(value, row, index) {
	if (value != null) {
		return value;
	} else {
		return "-";
	}
}
function formatter_vxnets(value, row, index) {
	var val;
	if (value != null) {
		return value;
	} else {
		val = "-";
	}
	return val;
}
function formatter_ip(value, row, index) {
	if (value != null) {
		return value;
	} else {
		return "-";
	}
}
function formatter_config(value, row, index) {
	if (row.D_Cpu != 0) {
		return row.D_Cpu + "核*" + row.D_Memory + "GB*" + row.D_Gpu + "MB";
	} else {
		return "-";
	}
}
function formatter_eip(value, row, index) {
	var val;
	if (value != null) {
		return value;
	} else {
		val = "-";
	}
	return val;
}
function timestrap2date(value) {
	var now = new Date(parseInt(value) * 1000);
	return now.toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ");
}

function showModal(title, icon, content, content1, method, type, delete_info) {
	var info = '取消';
	if (type == 0){
		info = '关闭';
	}
    $("#maindiv").empty();
    html = "";
    html += '<div class="modal fade" id="modal" tabindex="-1" role="dialog">';
    html += '<div class="modal-dialog" role="document">';
    html += '<div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
    html += '<h5 class="modal-title">' + title + '</h5>';
    html += '</div><div class="modal-body"><i class="'+icon+' icon-color-gray text-primary"></i>&nbsp;&nbsp;' + content + '<span class="text-primary">' + content1 + '</span>';
    if(delete_info == 1){
        html +='<br /><i class="icon-exclamation-sign icon-color-gray text-primary"></i>&nbsp;&nbsp;<span > 可在回收站找回已删除的主机</span><span class=" text-primary" id="modal-dele-name"></span>';
    }
    html +='</div>';
    html += '<div class="modal-footer"><button id="btnModel_ok" onclick="' + method + '" type="button" class="btn btn-primary">确认</button><button type="button" class="btn btn-danger" id="btnEsc" data-dismiss="modal">' + info + '</button></div></div></div></div>';
    $("#maindiv").append(html);
    if (type == 0) {
        $("#btnModel_ok").remove();
    }
    $('#modal').modal("show");
}


//搜索绑定
$("#txtsearch").on('keyup', function() {
	if (timer != null) {
		clearTimeout(timer);
	}
	var class_code = $("#agent").attr('val');

	var class_code2 = $("#agent_t").attr('val');
	var timer = setTimeout(function() {
		var search = $("#txtsearch").val();
		$('#table').bootstrapTable('refresh', {
			url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'Import', 'hosts', 'vpclists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val()
		});
	}, 500);
});

//引入
function reverseImport(){
	var info = $('#table').bootstrapTable('getSelections');
	if(info == ''){
		showModal('提示', 'icon-exclamation-sign', '请选择一条记录', '', '', 0);
	} else {
		showModal('提示', 'icon-question-sign', '确认要引入VPC', '', 'doReverseImport()');
	}
}

function doReverseImport(){
	$('#modal').modal("hide");
	var info = $('#table').bootstrapTable('getSelections');
	var id=[];
	$.each(info,function(i,n){
		id[i] = n.code;
	});
	$.ajax({
		"type":"post",
		"url" : "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'Import', 'hosts', 'addInstance']); ?>",
		"data" : {"id":id},
		"success":function(data) {
			data = $.parseJSON(data);
	        if (timer != null) {
				clearTimeout(timer);
			}
	        var timer = setTimeout(function() {
	        	showModal('提示', 'icon-exclamation-sign', data.msg, '', '', 0);
	        }, 500);
	        refreshTable();
		}
	});
}

//筛选租户
function departmentlist(id,name){
    $("#txtdeparmetId").val(id);
	$("#deparmets").html(name);
	var search = $("#txtsearch").val();
	var department_id = id;
	var class_code = $("#agent").attr('val');
	var class_code2 = $("#agent_t").attr('val');
	$("#table").bootstrapTable('refresh', {
	    url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'Import', 'hosts', 'vpclists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val()
	});
}
//刷新
function refreshTable() {
    var search = $("#txtsearch").val();
    var class_code = $("#agent").attr('val');
    var class_code2 = $("#agent_t").attr('val');
    $('#table').bootstrapTable('refresh', {
        url :　"<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'Import', 'hosts', 'vpclists']); ?>?department_id="+$("#txtdeparmetId").val(),
        query: {
    		class_code: class_code,
    		class_code2: class_code2,
    		search: search,
    		department_id:$("#txtdeparmetId").val()
    	}
    });
}
//地域查询
function local(id, class_code, agent_name) {
    if (agent_name) {
    	$('#agent_t').html('全部');
    	$('#agent').html(agent_name);
    	$('#agent').attr('val', class_code);
    	var search = $("#txtsearch").val();
    	$('#agent_t').attr('val', '');
    	refreshTable();

    	var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'Import','hosts','vpclists']); ?>?search=" + search + '&class_code=' + class_code +'&department_id='+$("#txtdeparmetId").val();
    	$('#table').attr('data-url', url);
    	var jsondata = <?php echo json_encode($agent); ?>;

    	if (id != 0) {
    		var data = '<li><a href="javascript:;" onclick="local_two(\'\',\'全部\',\'' + class_code + '\')">全部</a></li>';
    		$.each(jsondata, function(i, n) {
    			if (n.parentid == id) {
    				data += '<li><a href="#" onclick="local_two(\'' + n.class_code + '\',\'' + n.agent_name + '\',\'' + class_code + '\')">' + n.agent_name + '</a></li>';
    			}
    		});
    		$('#agent_two').html(data);
    	} else {
    		data = '';
            $('#agent_t').attr('val', data);
    		$('#agent_two').html(data);
    	}
    }
}

function local_two(class_code2, agent_name, class_code) {
    var search = $("#txtsearch").val();
    $('#agent_t').html(agent_name);
    $('#agent_t').attr('val', class_code2);
    $('#table').bootstrapTable('refresh', {
    	url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'Import','hosts','vpclists']); ?>",
    	query: {
    		class_code2: class_code2,
    		class_code: class_code,
    		search: search,
    		department_id:$("#txtdeparmetId").val()
    	}
    });
    var url = "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'Import', 'hosts', 'vpclists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val();
    $('#table').attr('data-url', url);
}
</script>
<?php $this -> end(); ?>
