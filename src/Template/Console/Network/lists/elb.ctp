<!-- 负载均衡 -->
<?= $this->element('network/lists/left',['active_action'=>'elb']); ?>
<div class="wrap-nav-right">

 <div class="wrap-manage">
  <div class="top">
	<span class="title">负载均衡</span>

	<div id="maindiv-alert"></div>
  </div>
  <div class="center clearfix">
   <button class="btn btn-addition" onclick="refreshTable(0);">
	<i class="icon-refresh marginR1"></i>刷新
  </button>
<!-- 	  <?php if (in_array('ccf_load_banlance_new', $this->Session->read('Auth.User.popedomname'))) { ?>
  <a class="btn btn-addition" href="<?= $this -> Url -> build(['controller' => 'network', 'action' => 'add', 'elb']) ?>"><i class="icon-plus"></i>&nbsp;&nbsp;新建</a>
	  <?php } ?> -->

	  <!-- 跨租户新建负载均衡 -->
          <?=$this->element('switchDepartment',['callback_url' => $this->Url->build(['controller' => 'network', 'action' => 'add', 'elb']),'typeName'=>'负载均衡'])?>
      <!-- 跨租户新建负载均衡 -->
	  <?php if (in_array('ccf_load_banlance_delete', $this->Session->read('Auth.User.popedomname'))) { ?>
	<button class="btn btn-default" id="btnDel" disabled="disabled">
	<i class="icon-remove "></i>&nbsp;&nbsp;删除
  </button>
	  <?php } ?>
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
	  </div>
	</div>
	<div class="bot ">
	 <table id="table" data-toggle="table"
	 data-pagination="true" 
	 data-side-pagination="server"
	 data-locale="zh-CN"
	 data-click-to-select="true"
	 data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'elb', 'lists','?'=>['department_id'=>$_default['id']]]); ?>"
	 data-unique-id="A_ID">
	 <thead>
	   <tr>
		<th data-checkbox="true"></th>
        <th data-field="A_ID" >Id</th>
		<th data-field="A_Code" >ELB_Code</th>
		<th data-field="A_Name" data-formatter="formatter_name">ELB名称</th>
		<th data-field="A_Status" data-formatter="formatter_state">状态</th>
		<th data-field="E_DisplayName">部署区位</th>
		<th data-field="C_ip">IP</th>
		<th data-field="B_Name">所属子网</th>
		<th data-field="EIP">公网IP</th>
		<th data-field="H_time" data-formatter=timestrap2date>创建时间</th>
		<!-- <th data-field="create_time" data-formatter=timestrap2date>最大连接数</th> -->
	  </tr>
	</thead>
  </table>
</div>
</div>

 </div>

 <!-- 修改 -->
  <div class="modal fade" id="modal-modify" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	<div class="modal-content">
	 <div class="modal-header">
	  <button type="button" class="close" data-dismiss="modal"
	  aria-label="Close">
	  <span aria-hidden="true">&times;</span>
	</button>
	<h5 class="modal-title">修改</h5>
  </div>
  <form id="modal-modify-form" action="" method="post">
	<div class="modal-body">
	  <div class="modal-form-group">
		<label>ELB名称:</label>
		<div>
		  <input id="modal-modify-name" name="name" type="text" />
		</div>
	  </div>
<!--       <div class="modal-form-group">
		<label>描述:</label>
		<div>
		  <textarea id="modal-modify-description" name="description" rows="5"></textarea>
		</div>
	  </div> -->
	  <input id="modal-modify-id" name="id" type="hidden" />
	</div>
	<div class="modal-footer">
	 <button id="yes_edit" type="button" class="btn btn-primary">确认</button>
	 <button id="reseter" type="button" class="btn btn-danger"
	 data-dismiss="modal">取消</button>
   </div>
 </form>
</div>
</div>
</div>
 <!-- 右键弹框 -->
<div class="context-menu" id="context-menu">
	<ul>
		<?php if (in_array('ccf_load_banlance_change', $this->Session->read('Auth.User.popedomname'))) { ?>
		<li id="modify"><a href="javascript:void(0);"><i class="icon-pencil"></i> 修改</a></li>
		<?php } ?>
		<?php if (in_array('ccf_load_banlance_delete', $this->Session->read('Auth.User.popedomname'))) { ?>
		<li id="del"><a href="javascript:void(0);"><i class="icon-trash"></i> 删除</a></li>
		<?php } ?>
		<?php if (in_array('ccf_load_banlance_configure', $this->Session->read('Auth.User.popedomname'))) { ?>
		<li id="shareHost"><a href="javascript:void(0);"> <i class="icon-paste"></i> 分配监听器</a></li>
		<?php } ?>
		<?php if (in_array('ccm_ps_interface_logs', $this->Session->read('Auth.User.popedomname'))) { ?>
		<li id="excp"><a  href="javascript:;"><i class="icon-book"></i> 异常日志</a></li>
		<?php } ?>
		<?php if (in_array('ccm_ps_interface_logs', $this->Session->read('Auth.User.popedomname'))) { ?>
        <li id="normal"><a  href="javascript:;"><i class="icon-book"></i> 正常日志</a></li>
        <?php } ?>
	</ul>
  </div>
<div id="maindiv"></div>
<?php $this -> start('script_last'); ?>
<script>
//input 存在一个被选中状态
$("#table").on('all.bs.table.table', function(e, row, $element) {
  if ($("tbody input:checked").length >= 1) {
	$(".center .btn-default").attr('disabled', false);
  } else {
	$(".center .btn-default").attr('disabled', true);
  }
});
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

function refreshTable(type) {
  var silent = false;
  var search = $("#txtsearch").val();
  var class_code = $("#agent").attr('val');
  var class_code2 = $("#agent_t").attr('val');
  if (type == 1) {
	$("#table").bootstrapTable("refresh", {
	  url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'elb', 'lists']); ?>",
	  silent: true,
	  query: {
		class_code2: class_code2,
		class_code: class_code,
		search: search,
		department_id:$("#txtdeparmetId").val()
	  }
	});
  } else {
	$("#table").bootstrapTable("refresh", {
	  url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'elb', 'lists']); ?>",
	  silent: false,
	  query: {
		class_code2: class_code2,
		class_code: class_code,
		search: search,
		department_id:$("#txtdeparmetId").val()
	  }
	});
  }
}

function formatter_ip(value, row, index) {
  if (value != "" && value != null) {
	return value;
  } else {
	return "-";
  }
}

function formatter_name(value, row, index) {
  if (value != "" && value != null) {
  	var department_id = $("#txtdeparmetId").val();
	var url = "/console/network/lists/Elblisten?e=" + row.A_ID+'&department_id'+department_id;
	return '<a onclick="isGoto(' + row.A_ID + '); return false" href="' + url + '">' + value + '</a>';
  }
}

$("#btnDel").on('click', function() {
	var o = true;
	var rows = $('#table').bootstrapTable('getSelections');
		rows.forEach(function(e) {
			if (e.Listen != null && e.Listen != "") {
				showModal('提示', 'icon-exclamation-sign', '该ELB下存在监听器', '请先删除', '', 0);
				$("#btnEsc").html("关闭");
				o = false;
				return false;
		  	}
		});
	if(o==true){
		showModal("提示", 'icon-question-sign', "确认要删除选中的ELB吗？", "", "ajaxFun('',\'lbs_del\')");
	}

});

//动态创建modal

function showModal(title, icon, content, content1, method, type) {
  $("#maindiv").empty();
  html = "";
  html += '<div class="modal fade" id="modal" tabindex="-1" role="dialog">';
  html += '<div class="modal-dialog" role="document">';
  html += '<div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
  html += '<h5 class="modal-title">' + title + '</h5>';
  html += '</div><div class="modal-body"><i class="' + icon + ' icon-color-gray text-primary"></i>&nbsp;&nbsp;' + content + '<span class="text-primary">' + content1 + '</span></div>';
  html += '<div class="modal-footer"><button id="btnMk" onclick="' + method + '" type="button" class="btn btn-primary">确认</button><button id="btnEsc" type="button" class="btn btn-danger" data-dismiss="modal">取消</button></div></div></div></div>';
  $("#maindiv").append(html);
  if (type == 0) {
	$("#btnMk").remove();
  }
  $('#modal').modal("show");
}

//提示框消失

function tentionHide(content, state) {
  $("#maindiv-alert").empty();
  var html = "";
  if (state == 0) {
	html += '<div class="point-host-startup "><i></i>' + content + '</div>';
	$("#maindiv-alert").append(html);
	$(".point-host-startup ").slideUp(3000);
  } else {
	html += '<div class="point-host-startup point-host-startdown"><i></i>' + content + '</div>';
	$("#maindiv-alert").append(html);
	$(".point-host-startdown").slideUp(3000);
  }
}

$(function() {
  $('#table').contextMenu('context-menu', {
	bindings: {
	  'shareHost': function(event) {
		var uniqueId = $(event).attr('data-uniqueid');
		isGoto(uniqueId);
	  },
	  'modify': function(event) {
		$('#modal-modify').modal("show");
		var uniqueId = $(event).attr('data-uniqueid');
		var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
		$('#modal-modify-name').val(row.A_Name);
		$('#modal-modify-id').val(row.A_ID);

		var name = $('#modal-modify-name').val();
		$('#modal-modify-name').blur(

		function() {
		  var name_ = $('#modal-modify-name').val();
		  if (name != name_) {
			$('#sumbiter').prop('disabled', false);
		  } else {
			$('#sumbiter').prop('disabled', true);
		  }
		})
		$('#yes_edit').one('click', function() {
		  $.ajax({
			method: 'post',
			url: '<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax ','action'=>'network']); ?>/<?php echo "hosts" ?>/<?php echo "edit" ?>',
//			data: $("#modal-modify-form").serialize(),
			  data:{id:$('#modal-modify-id').val(),name:$('#modal-modify-name').val()},
			success: function(data) {
			  data = $.parseJSON(data);
			  //console.debug(data);
			  if (data.code == '0000') {
				refreshTable(1);
				$('#modal-modify').modal("hide");
				tentionHide('修改成功', 0);
			  } else {
				tentionHide('修改失败', 1);
			  }
			}
		  })
		})
	  },
	  'del': function(event) {
		//获取数据
		var uniqueId = $(event).attr('data-uniqueid');
		var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
// 		if(row.A_Status=="创建中"){
// 		  showModal('提示', 'icon-exclamation-sign', '当前设备状态无法进行操作', '', '', 0);
// 		  $("#btnEsc").html("关闭");
// 		  return false;
// 		}
		if (row.EIP != null && row.EIP != "") {
		  showModal('提示', 'icon-exclamation-sign', '该ELB下已经绑定EIP', '请先解绑', '', 0);
		  $("#btnEsc").html("关闭");
		} else {
            if(row.A_Code==null){
            row.A_Code="";
            }
            if (row.Listen != null && row.Listen != "") {
            showModal('提示', 'icon-exclamation-sign', '该ELB下存在监听器', '请先删除', '', 0);
            $("#btnEsc").html("关闭");
            } else {

            showModal('提示', 'icon-question-sign', '确认要删除选中的ELB吗？', row.A_Name, 'ajaxFun(\'' + row.A_Code + '\',\'lbs_del\',\'' + row.A_ID + '\')');
            }
		}
	  },
      'excp':function(event){
      	var uniqueId = $(event).attr('data-uniqueid');
      	var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
      	//console.log(row);
      	var department_id = row.A_department;
      	window.location.href = "/console/excp/lists/excp/elb/"+department_id+'/all/0/0/'+row.A_ID;
      },
      //正常
      'normal':function(event){
      	var uniqueId = $(event).attr('data-uniqueid');
      	var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
      	var department_id = row.A_department;
      	window.location.href = "/console/excp/lists/normal/elb/"+department_id+'/all/0/0/'+row.A_ID;
      }

	}
  });
});

function isGoto(uniqueId) {
  var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
  if (row.A_Status == "运行中") {
	window.location.href = "/console/network/lists/Elblisten?e=" + uniqueId;
  } else {
	showModal('提示', 'icon-exclamation-sign', '该负载均衡正在创建中或创建失败，不能正常使用', '', '', 0);
	$("#btnEsc").html("关闭");
  }
}

function ajaxFun(code, method, id) {
  $("#modal").modal("hide");
  if (id != undefined) {
	$.ajax({
	  type: "post",
	  url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'elb', 'ajaxElb']); ?>",
	  async: true,
	  timeout: 9999,
	  data: {
		method: method,
		loadBalancerCode: code,
		basicId: id,
		isEach: "false"
	  },
	  //dataType:'json',
	  success: function(data) {
		data = $.parseJSON(data);
		if (data.Code != "0") {
		  alert(data.Message);
		}
		refreshTable(1);
	  }
	});
  } else {
	var rows = $('#table').bootstrapTable('getSelections');
	var o = true;
	if(o==true){
	  $.ajax({
	  type: "post",
	  url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'elb', 'ajaxElb']); ?>",
	  async: true,
	  timeout: 9999,
	  data: {
		method: method,
		table: rows,
		isEach: true
	  },
	  //dataType:'json',
	  success: function(data) {
		data = $.parseJSON(data);
		if (data.Code != "0") {
		  alert(data.Message);
		}
		refreshTable(1);
	  }
	});
	}
  }
}
//地域查询

function local(id, class_code, agent_name) {
  if (agent_name) {
	$('#agent_t').html('全部');
	$('#agent').html(agent_name);
	$('#agent').attr('val', class_code);
	var search = $("#txtsearch").val();
	$('#table').bootstrapTable('refresh', {
	  url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','elb','lists']); ?>",
	  query: {
		class_code: class_code,
		search: search,
		department_id:$("#txtdeparmetId").val()
	  }
	});
	var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','elb','lists']); ?>?class_code=" + class_code + "&search=" + search+'&department_id='+$("#txtdeparmetId").val();
	$('#table').attr('data-url', url);
	var jsondata = <?php echo json_encode($agent); ?> ;
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

//搜索绑定
$("#txtsearch").on('keyup', function() {
  if (timer != null) {
	clearTimeout(timer);
  }
  var class_code = $("#agent").attr('val');
  var class_code2 = $("#agent_t").attr('val');
  var search = $("#txtsearch").val();
  var timer = setTimeout(function() {
	$('#table').bootstrapTable('refresh', {
	  url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','elb','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val()
	});
  }, 1000);

});

function local_two(class_code2, agent_name, class_code) {
  var search = $("#txtsearch").val();
  $('#agent_t').html(agent_name);
  $('#agent_t').attr('val', class_code2);
  $('#table').bootstrapTable('refresh', {
	url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','elb','lists']); ?>",
	query: {
	  class_code2: class_code2,
	  class_code: class_code,
	  search: search,
	  department_id:$("#txtdeparmetId").val()
	}
  });
  var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','elb','lists']); ?>?class_code=" + class_code + "&search=" + search+'&department_id='+$("#txtdeparmetId").val();
  $('#table').attr('data-url', url);
}

function notifyCallBack(value) {
  //console.log(value);
  if (value.MsgType == "success" || value.MsgType == "error") {
	if (value.Data.method == "lbs_del" || value.Data.method == "lbs_add" || value.Data.method == "lbs") {
	  refreshTable(1);
	}
  }
}
function departmentlist(id,name){
    $("#txtdeparmetId").val(id);
	$("#deparmets").html(name);
	var search = $("#txtsearch").val();
	var department_id = id;
	var url;
	var class_code = $("#agent").attr('val');
	var class_code2 = $("#agent_t").attr('val');
	$("#table").bootstrapTable('refresh', {
		url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','elb','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val()
	});
}
</script>
<?php
$this -> end();
?>