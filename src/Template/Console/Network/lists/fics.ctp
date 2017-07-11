<!--  主机  列表 -->


<div class="wrap-nav-right">
   <div class="wrap-manage">
		 <!--[if IE]>
		<object id="vmrc" classid="CLSID:4AEA1010-0A0C-405E-9B74-767FC8A998CB"
		style="width: 100%; height: 100%;"></object>
		<![endif] -->
		<!--[if !IE]><!-->

		<!--<![endif]-->
		<div class="top">
			<span class="title">共享存储卷列表</span>
			<div class="callback-info pull-right text-success"><i class="icon-ok"></i>&nbsp;操作成功</div>
			<div id="maindiv-alert"></div>
		</div>
		<div class="center clearfix">
		<button class="btn btn-addition" onclick="refreshTable();">
		  <i class="icon-refresh"></i>&nbsp;&nbsp;刷新
		</button>
			<?php if (in_array('ccf_fics_new', $this->Session->read('Auth.User.popedomname'))) { ?>
		<a class="btn btn-addition" href="<?= $this -> Url -> build(['controller' => 'network', 'action' => 'add', 'fics']) ?>">
		<i class="icon-plus"></i>&nbsp;&nbsp;新建</a>
			<?php } ?>
			<?php if (in_array('ccm_ps_fics_settinglist', $this->Session->read('Auth.User.popedomname'))) { ?>
			<!-- <button class="btn btn-default" disabled id="bntSetting">
				访问设置
			</button> -->
			<?php } ?>
			<?php if (in_array('ccm_ps_fics_settinglist', $this->Session->read('Auth.User.popedomname'))) { ?>
			<button class="btn btn-default" disabled id="bntRelation">
				关联设备
			</button>
			<?php } ?>
			<?php if (in_array('ccf_host_delete', $this->Session->read('Auth.User.popedomname'))): ?>
            <button class="btn btn-default" id="btnDel" disabled>
              <i class="icon-remove "></i>&nbsp;&nbsp;删除
            </button>
            <?php endif; ?>
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
			<div class="dropdown">
			 品牌:
				<a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button">
                    <span class="pull-left" id="dl_store_type" val="">全部</span>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                	  <li><a href="javascript:;" onclick="storeType('','全部')">全部</a></li>	
                      <?php if(isset($_store_types)&&!empty($_store_types)){foreach($_store_types as $index=>$value) { ?>
                         <li><a href="#" onclick="storeType('<?php echo $index ?>','<?php echo $value ?>')"><?php echo $value ?></a></li>
                      <?php }}?>
                </ul>
			</div>
			<span class="search">
			<input type="text" id="txtsearch" name="search" placeholder="搜索">
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
	 data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'fics', 'lists','?'=>['department_id'=>$_default['id']]]); ?>"
	 data-unique-id="vol_id">
	 <thead>
	   <tr>
<!-- 		<th data-radio="true"></th> -->
		<th data-checkbox="true"></th>
		<th data-field="agent_name">部署区位</th>
		<th data-field="vol_type" data-formatter="formatter_type">品牌</th>
		<th data-field="store_name">存储名</th>
		<th data-field="vol_name">卷名</th>
		<th data-field="total_cap" data-formatter="formatter_tot">容量（GB）</th>
		 <th data-field="name">租户</th>
		 <th data-field="create_time" data-formatter=timestrap2date>创建时间</th>
	  </tr>
	</thead>
  </table>
</div>
</div>
</div>

<!-- 右键弹框 -->
<div class="context-menu" id="context-menu">
	 <ul>
		<?php if (in_array('ccf_host_change', $this->Session->read('Auth.User.popedomname'))): ?>
	       <li id="modify"><a href="javascript:void(0);"><i class="icon-pencil"></i> 修改</a></li>
		<?php endif; ?>
		<?php if (in_array('ccf_host_delete', $this->Session->read('Auth.User.popedomname'))): ?>
	  <li id="del"><a href="javascript:void(0);"><i class="icon-trash"></i> 删除</a></li>
		<?php endif; ?>
	</ul>
  </div>

<!-- 修改 -->
<div class="modal fade" id="modal-modify" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title">修改</h5>
            </div>
            <form id="modal-modify-form" action="" method="post">
                <div class="modal-body">
                    <div class="modal-form-group">
                        <label>卷名:</label>
                        <div>
                            <span id="modal-modify-name"></span>
                        </div>
    	            </div>
                    <div class="modal-form-group">
                        <label>容量:</label>
                        <div>
                            <input id="modal-modify-cap" name="total_cap" type="text" maxlength="15" />
                        </div>
                    </div>
                    <input id="modal-modify-id" name="vol_id" type="hidden" />
    	        </div>
                <div class="modal-footer">
    	           <button id="sumbiter" type="button" class="btn btn-primary">确认</button>
    	           <button id="reseter" type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div id="maindiv"></div>
<?php $this -> start('script_last'); ?>
<script type="text/javascript">					
$("#bntSetting").on('click', function() {
	var rows = $('#table').bootstrapTable('getSelections');
	window.location = "/console/network/lists/settinglist?f="+rows[0].vol_id;
});
$("#bntRelation").on('click', function() {
	var rows = $('#table').bootstrapTable('getSelections');
	window.location = "/console/network/lists/ficsHosts?vol_id="+rows[0].vol_id;
});

//input 存在一个被选中状态
$("#table").on('all.bs.table', function(e, row, $element) {
	if ($("tbody input:checked").length > 1) {
		$("#bntRelation").attr('disabled', true);
		$("#btnDel").attr('disabled', false);
	} else if($("tbody input:checked").length == 1) {
		$("#bntRelation").attr('disabled', false);
		$("#btnDel").attr('disabled', false);
	} else {
		$("#bntRelation").attr('disabled', true);
		$("#btnDel").attr('disabled', true);
	}
})
//动态创建modal

function showModal(title, icon, content, content1, method, type, warningInfo = '') {
	$("#maindiv").empty();
	html = "";
	html += '<div class="modal fade" id="modal" tabindex="-1" role="dialog">';
	html += '<div class="modal-dialog" role="document">';
	html += '<div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
	html += '<h5 class="modal-title">' + title + '</h5>';
	html += '</div><div class="modal-body"><i class="'+icon+' icon-color-gray text-primary"></i>&nbsp;&nbsp;' + content + '<span class="text-primary">' + content1 + '</span>';
	html += '<i class="icon-exclamation-sign icon-color-gray text-primary"></i>&nbsp;&nbsp;<span>删出共享存储之前，必须清空共享存储内所有内容（包括空文件夹）</span>';
	// 提示
	html += warningInfo + '</div>';
	html += '<div class="modal-footer"><button id="btnModel_ok" onclick="' + method + '" type="button" class="btn btn-primary">确认</button><button type="button" class="btn btn-danger" id="btnEsc" data-dismiss="modal">取消</button></div></div></div></div>';
	$("#maindiv").append(html);
	if (type == 0) {
		$("#btnModel_ok").remove();
	}
	$('#modal').modal("show");
}

$('#table').contextMenu('context-menu', {
	bindings: {
		'modify': function(event) {
			//获取数据
    			index = $(event).attr('data-index');
    			var uniqueId = $(event).attr('data-uniqueid');
    			var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
				$('#modal-modify-name').html(row.vol_name);
				$('#modal-modify-cap').val(row.total_cap);
				$('#modal-modify-id').val(row.vol_id);
				var type=row.vol_type;
				var origin_cap=Number(row.total_cap);
				$('#modal-modify').one('show.bs.modal', function() {
					$('#sumbiter').one('click', function() {
						//容量是否在配额范围内
						var ok=1;
						var new_cap=$('#modal-modify-cap').val();
						$.getJSON("/console/home/getUserLimit", function(data){
							if(type=='oceanstor9k'){
								if((Number(new_cap-origin_cap)+data.oceanstor9k_cap_used)>data.data.oceanstor9k_cap_bugedt){
									ok=0;
									alert("配额不足 \r\n H9000 总容量配额："+ data.data.oceanstor9k_cap_bugedt+" 已使用："+data.oceanstor9k_cap_used);
									$('#modal-modify').modal("hide");
								}
							}else if(type=='fics'){
								if((Number(new_cap-origin_cap)+data.fics_cap_used)>data.data.fics_cap_bugedt){
									ok=0;
									alert("配额不足 \r\n FICS 总容量配额："+ data.data.fics_cap_bugedt+" 已使用："+data.fics_cap_used);
									$('#modal-modify').modal("hide");
								}
							}

							if(ok==1) {
								//ajax提交页面
								$.ajax({
									url: "<?= $this -> Url -> build(['controller' => 'ajax', 'action' => 'network', 'fics', 'edit']); ?>",
									async: false,
									data: $('#modal-modify-form').serialize(),
									method: 'post',
									dataType: 'json',
									success: function (e) {
										//操作成功
										if (e.code == '0000') {
											$('#modal-modify').modal("hide");
										}
										refreshTable();
									}
								});
							}
						});

						return false;
					});
				});
				$('#modal-modify').modal("show");
		},
		'del': function(event) {
			var uniqueId = $(event).attr('data-uniqueid');
			var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
			//获取数据
			showModal('提示', 'icon-question-sign', '确认删除所选存储卷吗？', row.vol_name, 'ajaxFun(\'' + row.vol_id + '\')');
		}
	}

});
//删除
$("#btnDel").on('click', function() {
    var names = "";
    var id = "";
	var rows = $('#table').bootstrapTable('getSelections');

    var firewall = false;

    $.each(rows, function(i, e) {
    	if (id != "") {
			id = id + ",";
			names = names + ",";
		}
		id = id + e.vol_id;
		names = names + e.vol_name;
    });
    
    if (id != "") {
    	showModal('提示', 'icon-question-sign', '确认要删除存储卷', names, 'ajaxFun(\'' + id + '\')');   
    } else {
    	showModal('提示', 'icon-exclamation-sign', '请选中一条存储卷', '', '', 0);
        $("#btnEsc").html("关闭");
   }
	
});

//搜索绑定
$("#txtsearch").on('keyup', function() {
	if (timer != null) {
		clearTimeout(timer);
	}
	var class_code = $("#agent").attr('val');
	var type = $("#dl_store_type").attr("val");
	var class_code2 = $("#agent_t").attr('val');
	var timer = setTimeout(function() {
		var search = $("#txtsearch").val();
		$('#table').bootstrapTable('refresh', {
			url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','fics','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val()+'&type='+type
		});
	}, 1000);
});



//TYPE

function formatter_type(value, row, index) {
	switch (value) {
    	case "oceanstor9k":
    		{
    			return '华为9000';
    			break;
    		}
    	case "fics":
    		{
    			return 'FICS共享存储';
    		}
    	}
}

function formatter_cap(value,row,index){
	if(value!=""&&value!="0"&&value!=undefined){
		return value+"%";
	}
	return "-";
}

function formatter_tot(value,row,index){
	if(value!=""&&value!="0"&&value!=undefined){
		return value+"GB";
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

function ajaxFun(id) {
	$('#modal').modal("hide");
	$.ajax({
		type: "post",
		url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'fics', 'deleteId']); ?>",
		async: true,
		data: {
			id:id
		},
		success: function(data) {
			data = $.parseJSON(data);
			if (data.code != "0000" && data.Code!="0") {
				alert(data.Message);
			}
			refreshTable();
		}
	});
}

function refreshTable() {
	var search = $("#txtsearch").val();
	//$('#table').bootstrapTable('showLoading');
	var class_code = $("#agent").attr('val');
	var class_code2 = $("#agent_t").attr('val');
	$('#table').bootstrapTable('refresh', {
		url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'fics', 'lists']); ?>",
		query: {
			class_code2: class_code2,
			class_code: class_code,
			search: search,
			department_id:$("#txtdeparmetId").val(),
			type:$("dl_store_type").attr('val')
		}
	});
	//$('#table').bootstrapTable('hideLoading');
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
			url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','fics','lists']); ?>",
			query: {
				class_code: class_code,
				search: search,
				department_id:$("#txtdeparmetId").val(),
				type:$("#dl_store_type").attr('val')
			}
		});
		var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','fics','lists']); ?>?class_code=" + class_code + "&search=" + search+'&department_id='+$("#txtdeparmetId").val();
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
			$('#agent_two').html(data);
		}
	}
}

function local_two(class_code2, agent_name, class_code) {
	var search = $("#txtsearch").val();
	$('#agent_t').html(agent_name);
	$('#agent_t').attr('val', class_code2);
	$('#table').bootstrapTable('refresh', {
		url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','fics','lists']); ?>",
		query: {
			class_code2: class_code2,
			class_code: class_code,
			search: search,
			department_id:$("#txtdeparmetId").val(),
			type:$("#dl_store_type").attr('val')
		}
	});
	var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','fics','lists']); ?>?class_code=" + class_code + "&class_code2=" + class_code2 + "&search=" + search+'&department_id='+$("#txtdeparmetId").val();
	$('#table').attr('data-url', url);
}


function notifyCallBack(value) {
	var search = $("#txtsearch").val();
	var department_id = $("#txtdeparmetId").val();
	var class_code = $("#agent").attr('val');
	var class_code2 = $("#agent_t").attr('val');
	var type = $("#dl_store_type").attr("val");
	var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','fics','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+department_id+'$type='+type;
	//console.log(value);
	if (value.MsgType == "success" || value.MsgType == "error") {
		if (value.Data.method == "ecs_del" || value.Data.method == "ecs_add" || value.Data.method == "ecs_start" || value.Data.method == "ecs_stop" || value.Data.method == "ecs") {
			$('#table').bootstrapTable('refresh', {
				url: url,
				silent: true
			});
		}
	}
}
$(".wrap-nav-right").addClass('wrap-nav-right-left');
//新建 右边固定框
// var offsetTop = $(".theme-right").offset().top;
var width = $(".buy-theme").width() * 0.24;
$(window).scroll(
function(){
    if($(document).scrollTop() > offsetTop - 60){
        var offsetLeft = $(".theme-right").offset().left;
        $(".theme-right").css({position:"fixed",top:"60px",left:offsetLeft,width:width});
    }else{
        $(".theme-right").css("position","static");
    }
}
);

function departmentlist(id,name){
      $("#txtdeparmetId").val(id);
      $("#deparmets").html(name);
      var search = $("#txtsearch").val();
      var department_id = id;
      var url;
      var class_code = $("#agent").attr('val');
      var class_code2 = $("#agent_t").attr('val');
      var type = $("#dl_store_type").attr("val");
      $("#table").bootstrapTable('refresh', {
        url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','fics','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val()+'&type='+type
      });
 }

 function storeType(val,name){
 	  $("#dl_store_type").attr('val',val);
 	  $("#dl_store_type").html(name);
	  var search = $("#txtsearch").val();
	  var department_id = $("#txtdeparmetId").val();
	  var url;
	  var class_code = $("#agent").attr('val');
	  var class_code2 = $("#agent_t").attr('val');
	  var type = $("#dl_store_type").attr("val");
	  $("#table").bootstrapTable('refresh', {
	    url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','fics','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val()+'&type='+type
	  });
 }

</script>
<?php
$this -> end();
?>