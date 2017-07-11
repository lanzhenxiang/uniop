<!-- 公网ip -->
<?= $this->element('network/lists/left',['active_action'=>'eip']); ?>
<div class="wrap-nav-right">

 <div class="wrap-manage">
  <div class="top">
	<span class="title">公网IP</span>

	<div id="maindiv-alert"></div>
  </div>
  <div class="center clearfix">

   <button class="btn btn-addition" onclick="refreshTable();">
	<i class="icon-refresh"></i>&nbsp;&nbsp;刷新
  </button>
<!-- 	  <?php if (in_array('ccf_eip_new', $this->Session->read('Auth.User.popedomname'))) { ?>
  <a class="btn btn-addition"
  href="<?= $this -> Url -> build(['controller' => 'network', 'action' => 'add', 'eip']) ?>"><i
  class="icon-plus"></i>&nbsp;&nbsp;新建</a>
	  <?php } ?> -->
	  <!-- 跨租户新建EIP -->
          <?=$this->element('switchDepartment',['callback_url' => $this->Url->build(['controller' => 'network', 'action' => 'add', 'eip']),'typeName'=>'EIP'])?>
      <!-- 跨租户新建EIP -->
	  <?php if (in_array('ccf_eip_delete', $this->Session->read('Auth.User.popedomname'))) { ?>
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
	 data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'eip', 'lists','?'=>['department_id'=>$_default['id']]]); ?>"
	 data-unique-id="id">
	 <thead>
	   <tr>
		<th data-checkbox="true"></th>
		<th data-field="id" data-sortable="true">Id</th>
		<th data-field="code" data-sortable="true">公网IP_Code</th>
		<th data-field="eip">公网IP</th>
		<th data-field="name">公网IP名称</th>
		<th data-field="status" data-formatter="formatter_state">状态</th>
		<th data-field="bindcode" >使用状态</th>
		<th data-field="bindcode" >占用设备</th>
		<th data-field="location_name">部署区位</th>
<!-- 		<th data-field="bandwidth">带宽上限(Mbps)</th> -->
		<th data-field="create_time" data-formatter=timestrap2date>创建时间</th>
		<th data-field="description">备注</th>
	  </tr>
	</thead>
  </table>
</div>
</div>
</div>

<!-- 右键弹框 -->
<div class="context-menu" id="context-menu">
	<ul>
		<?php if (in_array('ccf_eip_alloc_hosts', $this->Session->read('Auth.User.popedomname'))) { ?>
	<li id="bindEip"><a href="javascript:void(0);"><i class=" icon-desktop"></i> 分配到主机</a></li>
		<?php } ?>
		<?php if (in_array('ccf_eip_alloc_banlance', $this->Session->read('Auth.User.popedomname'))) { ?>
	<li id="bindNlb"><a href="javascript:void(0);"><i class="icon-random"></i> 分配到负载均衡器</a></li>
		<?php } ?>
		<?php if (in_array('ccf_eip_change', $this->Session->read('Auth.User.popedomname'))) { ?>
	<li id="modify"><a href="javascript:void(0);"><i class="icon-pencil"></i> 修改</a></li>
		<?php } ?>
		<?php if (in_array('ccf_eip_adjust_bandwidth', $this->Session->read('Auth.User.popedomname'))) { ?>
	<li id="modify-dk"><a href="javascript:void(0);"><i class="icon-signal"></i> 调整宽带上限</a></li>
	<!-- <li ><a href="javascript:void(0);"><i class="icon-pencil"></i> 修改计费模式</a></li> -->
		<?php } ?>
		<?php if (in_array('ccf_eip_delete', $this->Session->read('Auth.User.popedomname'))) { ?>

	<li id="del"><a href="javascript:void(0);"><i class="icon-remove"></i> 删除</a></li>
		<?php } ?>
		<?php if (in_array('ccf_eip_unallloc', $this->Session->read('Auth.User.popedomname'))) { ?>
	<li ><a href="javascript:void(0);" id="unwrap"><i class="icon-minus"></i> 解绑</a></li>
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
	<button type="button" class="close" data-dismiss="modal"
	aria-label="Close">
	<span aria-hidden="true">&times;</span>
  </button>
  <h5 class="modal-title">修改</h5>
</div>
<form id="modal-modify-form" action="" method="post">
  <div class="modal-body">
	<div class="modal-form-group">
	  <label>名称:</label>
	  <div>
		<input id="modal-modify-name" name="name" type="text" />
	  </div>
	</div>
	<div class="modal-form-group">
	  <label>备注:</label>
	  <div>
		<textarea id="modal-modify-description" name="description" rows="5"></textarea>
	  </div>
	</div>
	<input id="modal-modify-id" name="id" type="hidden" />
  </div>
  <div class="modal-footer">
   <button id="sumbiter" type="button" class="btn btn-primary">确认</button>
   <button id="reseter" type="button" class="btn btn-danger"
   data-dismiss="modal">取消</button>
 </div>
</form>
</div>
</div>
</div>

<div class="modal fade" id="modal-modify-dk" tabindex="-1" role="dialog">
 <div class="modal-dialog" role="document">
  <div class="modal-content">
   <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
	aria-label="Close">
	<span aria-hidden="true">&times;</span>
  </button>
  <h5 class="modal-title">调整带宽上限</h5>
</div>
<form id="modal-modify-form-dk" action="" method="post">
  <div class="modal-body">
	<div class="modal-form-group">
	  <label>带宽上限:</label>
	  <div>
		<input id="modal-modify-bandwidth" class="pull-left" name="bandwidth" type="text" />
		<label>&nbsp;&nbsp;Mbps</label>
		<label id="bandwidth-warning" style="color:#fd6c6d"></label>
	  </div>
	</div>
	<input id="modal-modify-eipId" name="eipId" type="hidden" value="" />
	<input id="modal-modify-eipCode" name="eipCode" type="hidden" />
	<input id="modal-modify-method" name="method" value="eip_attribute" type="hidden" />
	<input id="modal-modify-isEach" name="isEach" value="false" type="hidden" />
  </div>
  <div class="modal-footer">
   <button id="btnBandwidth" type="button" class="btn btn-primary">确认</button>
   <button id="reseter" type="button" class="btn btn-danger"
   data-dismiss="modal">取消</button>
 </div>
</form>
</div>
</div>
</div>

<div id="maindiv"></div>
<?php $this -> start('script_last'); ?>
<script type="text/javascript">
$(function(){
	$('#table').contextMenu('context-menu', {
	bindings: {
		'bindEip': function(event) {
			var uniqueId = $(event).attr('data-uniqueid');
			var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
			if(row.status=="运行中"){
				// if(row.agent_code!="aliyun"&&row.agent_code!="aws"){
				// 	var firewall = eipHaveFireWallEcs(row.id);
				// 	if(firewall==""){
				// 		window.location.href = "/console/network/lists/EipbHosts?e=" + uniqueId+'&department_id='+$("#txtdeparmetId").val();
				// 	}else{
				// 		showModal('提示', 'icon-exclamation-sign', firewall, '', '', 0);
				// 		$("#btnEsc").html("关闭");
				// 	}
				// }else{
					window.location.href = "/console/network/lists/EipbHosts?e=" + uniqueId+'&department_id='+$("#txtdeparmetId").val();
				// }
			}else{
				showModal('提示', 'icon-exclamation-sign', '该EIP状态不可使用', '', '', 0);
				$("#btnEsc").html("关闭");
			}

		},
		'bindNlb': function(event) {
			var uniqueId = $(event).attr('data-uniqueid');
			var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
			if(row.status=="运行中"){
				// var firewall = eipHaveFireWallEcs(row.id);
				// if(firewall==""){
					window.location.href = "/console/network/lists/EipbElb?e=" + uniqueId+'&department_id='+$("#txtdeparmetId").val();
				// }else{
				// 	showModal('提示', 'icon-exclamation-sign', firewall, '', '', 0);
				// 	$("#btnEsc").html("关闭");
				// }

			}else{
				showModal('提示', 'icon-exclamation-sign', '该EIP状态不可使用', '', '', 0);
				$("#btnEsc").html("关闭");
			}
		},
		'modify': function(event) {
			//获取数据
			index=$(event).attr('data-index');
			var uniqueId = $(event).attr('data-uniqueid');
			var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
			var name = row.location_name.split('-');

				//console.log(row);
				//填充数据
				//TODO 根据bootstrap方法
				$('#modal-modify-name').val(row.name);
				$('#modal-modify-description').val(row.description);
				$('#modal-modify-id').val(row.id);
				$('#modal-modify').one('show.bs.modal',
				function() {
					$('#sumbiter').one('click',
					function() {
						console.log('ff');
						//ajax提交页面
						$.ajax({
							url: "<?= $this -> Url -> build(['controller' => 'ajax', 'action' => 'network']); ?>/<?php echo "eip " ?>/<?php echo "edit " ?>",
							async: false,
							data: $('#modal-modify-form').serialize(),
							method: 'post',
							dataType: 'json',
							success: function(e) {
								//操作成功
								if (e.code == '0000') {
									$('#modal-modify').modal("hide");
									// tentionHide('修改成功',0);
									$('#table').bootstrapTable('updateRow', {
										index: index,
										row: e.data
									});
								} else {
									//操作失败
									// tentionHide('修改失败',1);
								}

							}
						});
						return false;
					});
				});

				$('#modal-modify').modal("show");


		},
		'del': function(event) {
			//获取数据
			var uniqueId = $(event).attr('data-uniqueid');
			var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
			if(row.bindcode!=''&&row.bindcode!=null){
				showModal('提示', 'icon-exclamation-sign', '该Eip已使用，请先解绑',row.bindcode, '', 0);
				$("#btnEsc").html("关闭");
			}else{
				showModal('提示', 'icon-question-sign', '确认要删除EIP', row.name, 'ajaxFun(\'' + row.code + '\',\'eip_del\',\'' + row.id + '\')');
			}

		},
		'unwrap': function(event) {
			var uniqueId = $(event).attr('data-uniqueid');
			var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
			var content = getEipByDesc(row);
			if (content != "") {
				showModal('解绑', 'icon-question-sign', '是否解绑EIP-' + row.name, content, 'ajaxFun(\'' + row.code + '\',\'eip_unbind\',\'' + row.id + '\')');
			} else {
				showModal('提示', 'icon-exclamation-sign', '该EIP没有绑定任何资源', '', '', 0);
				$("#btnEsc").html("关闭");
			}
		},
		'modify-dk': function(event) {
			//获取数据
			dkIndex=$(event).attr('data-index');
			var uniqueId = $(event).attr('data-uniqueid');
			var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
			$("#modal-modify-bandwidth").val(row.bandwidth);
			if (name[0] == "亚马逊") {
				if (row.status == "运行中") {
				$('#modal-modify-eipCode').val(row.code);
				$('#modal-modify-eipId').val(row.id);
				$('#modal-modify-dk').one('show.bs.modal',
				function() {
					$('#btnBandwidth').on('click',
					function() {
						var verify = /(^[1-9]([0-9]*)$|^[0-9]$)/;
						var value = $('#modal-modify-bandwidth').val();
						var warning = $('#bandwidth-warning');
						if(!verify.test(value)){
							warning.html('请输入正整数');
							return false;
						}
						$.ajax({
							url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'eip', 'ajaxEip']); ?>",
							async: false,
							data: $('#modal-modify-form-dk').serialize(),
							method: 'post',
							dataType: 'json',
							success: function(e) {
								$('#modal-modify-dk').modal("hide");
								//操作成功
								if (e.code == '0000') {
									// tentionHide('修改成功',0);
									$('#table').bootstrapTable('updateRow', {
										index: dkIndex,
										row: e.data
									});
								} else {
									//操作失败
									// tentionHide('修改失败',1);
								}
							}
						});
					});
					$('#modal-modify-bandwidth').on('blur',function(){
						$('#bandwidth-warning').html('');
					});
				});
				$('#modal-modify-dk').modal("show");
			} else {
				showModal('提示', 'icon-exclamation-sign', '当前EIP无法调整带宽上限', '', '', 0);
				$("#btnEsc").html("关闭");
			}
			}else{
				showModal('提示', 'icon-exclamation-sign', '该厂商EIP,不提供修改带宽方法', '', '', 0);
				$("#btnEsc").html("关闭");
			}

		},
		//异常
        'excp':function(event){
        	var uniqueId = $(event).attr('data-uniqueid');
        	var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
        	var department_id = row.department_id;
        	window.location.href = "/console/excp/lists/excp/eip/"+department_id+'/all/0/0/'+row.id;
        },
        //正常
        'normal':function(event){
        	var uniqueId = $(event).attr('data-uniqueid');
        	var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
        	var department_id = row.department_id;
        	window.location.href = "/console/excp/lists/normal/eip/"+department_id+'/all/0/0/'+row.id;
        }

	}
});
});

function eipHaveFireWallEcs(id) {
	// body...
	var str="";
	$.ajax({
		url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'eip', 'eipHaveFireWallEcs']); ?>",
		async: false,
		data: {
			id: id
		},
		method: 'post',
		dataType: 'json',
		success: function(e) {
			if(e[0].agent_code !="aliyun"){//阿里云没有防火墙实例
				if(e[0].firewallstatus==""||e[0].firewallstatus==null){
					str= "当前EIP下没有防火墙实例，请先创建防火墙实例";
				}else if(e[0].firewallstatus!="运行中"){
					str= "当前防火墙实例状态不可用";
				}
			}
		}
	});
	return str;
}

function getEipByDesc(row) {
	var id = row.id;
	var str = "";
	$.ajax({
		url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'eip', 'getEipByDesc']); ?>",
		async: false,
		data: {
			id: id
		},
		method: 'post',
		dataType: 'json',
		success: function(e) {
			if (e != null) {
				e = $.parseJSON(e);
				str = "关联设备名称:"+ e.name +",Code:"+ e.code
			}
		}
	});
	return str;
}

$("#btnDel").on('click',
function() {
	showModal("提示", 'icon-question-sign', "确认要删除选中EIP", "", "ajaxFun('',\'eip_del\')");

});
//input 存在一个被选中状态
$("#table").on('all.bs.table.table',
function(e, row, $element) {
	if ($("tbody input:checked").length >= 1) {
		$(".center .btn-default").attr('disabled', false);
	} else {
		$(".center .btn-default").attr('disabled', true);
	}
});
//动态创建modal
function showModal(title, icon, content, content1, method, type) {
	$("#maindiv").empty();
	var html = "";
	html += '<div class="modal fade" id="modal" tabindex="-1" role="dialog">';
	html += '<div class="modal-dialog" role="document">';
	html += '<div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
	html += '<h5 class="modal-title">' + title + '</h5>';
	html += '</div><div class="modal-body"><i class="'+icon+' text-primary"></i>&nbsp;' + content + '<p class="text-primary">&emsp;' + content1 + '</p></div>';
	html += '<div class="modal-footer"><button id="btnMk" onclick="' + method + '" type="button" class="btn btn-primary">确认</button><button id="btnEsc" type="button" class="btn btn-danger" data-dismiss="modal">取消</button></div></div></div></div>';
	$("#maindiv").append(html);
	if (type == 0) {
		$("#btnMk").remove();
	}
	$('#modal').modal("show");
}

//搜索绑定
$("#txtsearch").on('keyup',
function() {
	if (timer != null) {
		clearTimeout(timer);
	}
	var class_code = $("#agent").attr('val');

	var class_code2 = $("#agent_t").attr('val');
	var search = $("#txtsearch").val();
	var timer = setTimeout(function() {
		$('#table').bootstrapTable('refresh', {
			url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','eip','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val()
		});
	},
	1000);

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
function formatter_usestate(value, row, index){
	if(value!=null && value !=""){
		if(value!=""&&value!=null){
			return "已使用";
		}
		else{
			return "未使用";
		}
	}
	return "-";
}

function formatter_eip(value, row, index){
	if(value!=null && value !=""){
		if(value.ip!=""&&value.ip!=null){
			return value.ip;
		}
		else{
			return "-";
		}
	}
	return "-";
}

function formatter_use_code(value, row, index){
	if(value!=null && value !=""){
		if(value.bindcode!=""&&value.bindcode!=null){
			return value.bindcode;
		}
		else{
			return "-";
		}
	}
	return "-";
}

function formatter_bandwidth(value, row, index){
	if(value!=null && value !=""){
		if(value.bandwidth!=""&&value.bandwidth!=null){
			return value.bandwidth;
		}
		else{
			return "-";
		}
	}
	return "-";
}

//心跳
function heartbeat(type, id) {
	if (id != undefined && id != "") {
		$("#imgState" + id).removeClass('circle-stopped');
		$("#imgState" + id).removeClass('circle-run');
		$("#imgState" + id).addClass('circle-create'); //添加样式，样式名为className
		if (type == 0) {
			$("#txtState" + id).html('正在启动...');
		} else if (type == 1) {
			$("#txtState" + id).html('正在停止...');
		} else if (type == 2) {
			$("#txtState" + id).html('正在重启...');
		} else {
			$("#txtState" + id).html('正在销毁...');
		}
	} else {
		var ids = getRowsID('id');
		var idList = ids.split(',');
		idList.forEach(function(e) {
			$("#imgState" + e).removeClass('circle-stopped');
			$("#imgState" + e).removeClass('circle-run');
			$("#imgState" + e).addClass('circle-create'); //添加样式，样式名为className
			if (type == 0) {
				$("#txtState" + e).html('正在启动...');
			} else if (type == 1) {
				$("#txtState" + e).html('正在停止...');
			} else if (type == 2) {
				$("#txtState" + e).html('正在重启...');
			} else {
				$("#txtState" + e).html('正在销毁...');
			}
		});
	}
}
//获取选中行参数
function getRowsID(type) {
	var idlist = '';
	$("input[name='btSelectItem']:checkbox").each(function() {
		if ($(this)[0].checked == true) {
			//alert($(this).val());
			var id = $(this).parent().parent().attr('data-uniqueid');
			var row = $('#table').bootstrapTable('getRowByUniqueId', id);
			if (row.status != '') {
				if (type == 'name') {
					idlist += row.name + ',';
				} else if (type == "id") {
					idlist += row.id + ',';
				} else {
					idlist += row.code + ',';
				}
			}
		}
	});
	return idlist;
}

function ajaxFun(code, method, id) {
	$('#modal').modal("hide");
	if (method == "ecs_start") {
		heartbeat(0, id);
	} else if (method == "ecs_stop") {
		heartbeat(1, id);
	} else if (method == "ecs_reboot") {
		heartbeat(2, id);
	} else if (method == "eip_del") {
		heartbeat(3, id);
	}
	if (id != undefined) {
		$.ajax({
			type: "post",
			url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'eip', 'ajaxEip']); ?>",
			async: true,
			timeout: 9999,
			data: {
				method: method,
				eipCode: code,
				basicId: id,
				isEach: false
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
		$.ajax({
			type: "post",
			url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'eip', 'ajaxEip']); ?>",
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
function refreshTable(type) {
	var silent = false;
	var search = $("#txtsearch").val();
	var class_code = $("#agent").attr('val');
	var class_code2 = $("#agent_t").attr('val');
	if (type == 1) {
		$("#table").bootstrapTable("refresh", {
			url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'eip', 'lists']); ?>",
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
			url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'eip', 'lists']); ?>",
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

//地域查询
function local(id, class_code, agent_name) {
	if (agent_name) {
		$('#agent_t').html('全部');
		$('#agent').html(agent_name);
		$('#agent').attr('val', class_code);
		var search = $("#txtsearch").val();
		$('#table').bootstrapTable('refresh', {
			url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','eip','lists']); ?>",
			query: {
				class_code: class_code,
				search: search,
				department_id:$("#txtdeparmetId").val()
			}
		});
		var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','eip','lists']); ?>?class_code=" + class_code + "&search=" + search+'&department_id='+$("#txtdeparmetId").val();
		$('#table').attr('data-url', url);
		var jsondata = <?php echo json_encode($agent); ?>;
		if (id != 0) {
			var data = '<li><a href="javascript:;" onclick="local_two(\'\',\'全部\',\'' + class_code + '\')">全部</a></li>';
			$.each(jsondata,
			function(i, n) {
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
		url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','eip','lists']); ?>",
		query: {
			class_code2: class_code2,
			class_code: class_code,
			search: search,
			department_id:$("#txtdeparmetId").val()
		}
	});
	var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','eip','lists']); ?>?class_code=" + class_code + "&search=" + search+'&department_id='+$("#txtdeparmetId").val();
	$('#table').attr('data-url', url);
}
function notifyCallBack(value) {
  //console.log(value);

	var search = $("#txtsearch").val();
	var department_id = $("#txtdeparmetId").val();
	var class_code = $("#agent").attr('val');
	var class_code2 = $("#agent_t").attr('val');
	var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','eip','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+department_id;

	if (value.MsgType == "success" || value.MsgType == "error") {
		if (value.Data.method == "eip_del" || value.Data.method == "eip_add" || value.Data.method == "eip_attribute" ||value.Data.method=="eip_bind"||value.Data.method=="eip_unbind") {
			$('#table').bootstrapTable('refresh', {
				url: url,
				silent: true
			});
		}
	}
}
// function departmentlist(id,name){
//     $("#txtdeparmetId").val(id);
// 	$("#deparmets").html(name);
// 	var search = $("#txtsearch").val();
// 	var department_id = id;
// 	var url;
// 	var class_code = $("#agent").attr('val');
// 	var class_code2 = $("#agent_t").attr('val');
// 	$("#table").bootstrapTable('refresh', {
// 		url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','eip','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val()
// 	});
// }
</script>
<?php $this -> end(); ?>