<!--  fics 关联  列表 -->


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
			<button class="btn btn-addition" onclick="back();" style="margin-left: 80%">
			  	<i class="icon-reply"></i>&nbsp;&nbsp;返回
			</button>
			<div class="callback-info pull-right text-success"><i class="icon-ok"></i>&nbsp;操作成功</div>
			<div id="maindiv-alert"></div>
		</div>
		<div class="center clearfix">
			<div class="marginb20">
				<span class="marginr20">部署区位： <?= $data['display_name']?></span>
				<span class="marginr20">品牌： <?php switch($store_data['store_type']){
					case "oceanstor9k": 
						echo "H9000"; 
					break;
					default:
						echo "fics";
					break;
					}?></span>
				<span class="marginr20">存储卷：  <?= $data['vol_name']?></span>
			</div>
			<button class="btn btn-addition" onclick="refreshTable();">
			  	<i class="icon-refresh"></i>&nbsp;&nbsp;刷新
			</button>
			<button class="btn btn-addition" onclick="Authorization(1);">
			  	<i class=""></i>批量授权
			</button>
			<button class="btn btn-addition" onclick="Authorization(2);">
			  	<i class=""></i>取消授权
			</button>
			<div class="pull-right">
				<input type="hidden" id="txtdeparmetId" value="<?= $_default["id"] ?>" />
              	<?php if (in_array('ccf_all_select_department', $this->Session->read('Auth.User.popedomname'))) { ?>
              	<div class="dropdown">
		            租户
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
			  		设备类型
					<a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
						<span class="pull-left" id="deviceType" val="">全部</span>
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						<li><a href="javascript:;" onclick="selcetDeviceType('','全部')">全部</a></li>
						<li><a href="javascript:;" onclick="selcetDeviceType('hosts','云主机')">云主机</a></li>
						<li><a href="javascript:;" onclick="selcetDeviceType('desktop','云桌面')">云桌面</a></li>
					</ul>
				</div>
				<div class="dropdown">
			  		OS平台
					<a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
						<span class="pull-left" id="osType" val="">全部</span>
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						<li><a href="javascript:;" onclick="selcetOsType('','全部')">全部</a></li>
						<li><a href="javascript:;" onclick="selcetOsType('Windows','Windows平台')">Windows平台</a></li>
						<li><a href="javascript:;" onclick="selcetOsType('linux','Linux平台')">Linux平台</a></li>
					</ul>
				</div>
				<div class="dropdown">
			  		卷访问权限
					<a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
						<span class="pull-left" id="auth" val="">全部</span>
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						<li><a href="javascript:;" onclick="selectAuth('','全部')">全部</a></li>
						<li><a href="javascript:;" onclick="selectAuth('4','完全控制')">完全控制</a></li>
						<!--<li><a href="javascript:;" onclick="selectAuth('0','只读访问')">只读访问</a></li>-->
						<li><a href="javascript:;" onclick="selectAuth('2','不访问')">不访问</a></li>
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
				data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'fics', 'relevanceDeviceList','?'=>['department_id'=>$_default['id'],'vol_id'=>$vol_id]]); ?>"

				data-unique-id="A_ID">
	 			<thead>
				    <tr>
						<th data-checkbox="true"></th>
						<th data-field="name">名称</th>
						<th data-field="code">CODE</th>
						<th data-field="os">OS平台</th>
						<th data-field="authority" data-formatter="authority">卷访问方式</th>
						<th data-field="type">挂载方式</th>
					 	<th data-field="param">挂载命令</th>
				    </tr>
				</thead>
  			</table>
		</div>
	</div>
</div>
<!-- 修改 -->
<div class="modal fade" id="modal-authorization" tabindex="-1" role="dialog">
   	<div class="modal-dialog" role="document">
		<div class="modal-content">
	 		<div class="modal-header">
	  			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	  				<span aria-hidden="true">&times;</span>
				</button>
				<h5 class="modal-title">批量授权</h5>
  			</div>
  			<form id="modal-modify-form" action="" method="post">
				<div class="modal-body">

	  				<input type="hidden" name="type" value="1">
	  				<div id="tps" class="modal-form-group" style="display:none;">
						确认要撤销本机器的授权
	  				</div>
	  				<div id="mount-type" class="modal-form-group" style="display:inline;">
						<label>挂载方式:</label>
						<div>
						  	<select id="mount-type-select" name="mount-type">
						  		<!-- <option value="">请选择</option> -->
						  		<option value="net use">net use</option>
<!-- 						  		<option value="unc">unc</option> -->
						  		<option value="mount">mount</option>
						  	</select>
						</div>
	  				</div>
	  				<div id="drive-letter" class="modal-form-group" style="display:inline;">
						<label>目标盘符:</label>
						<div>
						  	<select id="drive-letter-select" name="drive-letter">
						  		<?php for($i = 'D'; $i < 'Z';$i++) { ?>
						  			<option value="<?= $i?>"><?= $i?>:</option>
						  		<?php } ?>
						  		<option value="Z">Z:</option>
						  	</select>
						</div>
	  				</div>
	  				<div id="access_type_div" class="modal-form-group" style="display:none">
						<label>授权方式:</label>
						<div>
						  	<select id="access_type" name="access_type">
						  		<option value="nfs">nfs</option>
						  		<option value="cifs">cifs</option>
						  	</select>
						</div>
	  				</div>
	  				<div id="target-path" class="modal-form-group" style="display:none">
						<label>目标路径:</label>
						<div>
						  	<textarea id="target-path-textarea" name="target-path" rows="5"></textarea>
						</div>
	  				</div>
	  				<input id="modal-modify-id" name="id" type="hidden" />
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
//input 存在一个被选中状态
$("#table").on('all.bs.table', function(e, row, $element) {
	if ($("tbody input:checked").length >= 1) {
		$(".center .btn-default").attr('disabled', false);
	} else {
		$(".center .btn-default").attr('disabled', true);
	}
})

function back(){
	window.location = "/console/network/lists/fics";
}
//动态创建modal

function showModal(title, icon, content, content1, method, type, info) {
	$("#maindiv").empty();
	html = "";
	html += '<div class="modal fade" id="modal" tabindex="-1" role="dialog">';
	html += '<div class="modal-dialog" role="document">';
	html += '<div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
	html += '<h5 class="modal-title">' + title + '</h5>';
	html += '</div><div class="modal-body"><i class="'+icon+' icon-color-gray text-primary"></i>&nbsp;&nbsp;' + content + '<span class="text-primary">' + content1 + '</span></div>';
	html += '<div class="modal-footer"><button id="btnModel_ok" onclick="' + method + '" type="button" class="btn btn-primary">确认</button><button type="button" class="btn btn-danger" id="btnEsc" data-dismiss="modal">'+info+'</button></div></div></div></div>';
	$("#maindiv").append(html);
	if (type == 0) {
		$("#btnModel_ok").remove();
	}
	$('#modal').modal("show");
}

function authority(value){
	switch(value){
		case '1':
			return "完全控制";
			break;
		case '0':
		 	return 	"只读访问";
		 	break;
		case '2':
			return '禁止访问';
			break;
		case '4':
			return '读写访问';
			break;	 
		default :
			return "";
			break ;		
	}
	return '';
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
			if (data.code != "0000") {
				alert(data.msg);
			}
			refreshTable();
		}
	});
}
//选择租户
function departmentlist(id,name){
	$("#txtdeparmetId").val(id);
	$("#deparmets").html(name);
	refreshTable()
}
//选择设备类型
function selcetDeviceType(id,name){
	$("#deviceType").attr('val',id);
	$("#deviceType").html(name);
	refreshTable()
}
//选择OS
function selcetOsType(id,name){
	$("#osType").attr('val',id);
	$("#osType").html(name);
	refreshTable()
}
//选择权限
function selectAuth(id,name){
	$("#auth").attr('val',id);
	$("#auth").html(name);
	refreshTable()
}
//搜索
$("#txtsearch").on('keyup', function() {
	if (timer != null) {
		clearTimeout(timer);
	}
	var timer = setTimeout(function() {
		refreshTable()
	}, 1000);
});

//刷新
function refreshTable() {
	var search = $("#txtsearch").val();
	var deviceType = $("#deviceType").attr('val');
	var osType = $("#osType").attr('val');
	var auth = $("#auth").attr('val');
	var departmentId = $("#txtdeparmetId").val();
	var vol_id = <?= $vol_id ?>;
	$('#table').bootstrapTable('refresh', {
		url: "<?= $this->Url->build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'fics', 'relevanceDeviceList']); ?>?vol_id="+vol_id+"&type="+deviceType+"&plat_form="+osType+"&auth="+auth+"&search="+search+"&department_id="+departmentId,
	});
}

function notifyCallBack(value) {
	var search = $("#txtsearch").val();
	var department_id = $("#txtdeparmetId").val();
	var class_code = $("#agent").attr('val');
	var class_code2 = $("#agent_t").attr('val');
	var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','fics','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+department_id;
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

//授权
function Authorization(type){
	var selectHosts = $("#table").bootstrapTable('getSelections');
	isWin = 0;
	isLinux = 0;
	$.each(selectHosts,function(i,n){
		switch(n.os){
			case "Windows":
			case "云主机":
			case "Adobe":
				isWin = 1;
			break;
			case "CentOS":
			case "Linux":
				isLinux = 1;
			break;	


		}
	});

	if (isWin == 1 && isLinux == 1){
		showModal('提示', 'icon-exclamation-sign', '请选择相同的OS平台的机器', '', '','0','关闭');
	} else {
		if(selectHosts.length == 0){
			showModal('提示', 'icon-exclamation-sign', '请选择一条记录', '', '','0','关闭');
		} else{
			$("input[name='type']").val(type)
			<?php if($store_data['store_type'] == "fics"){ ?>
				$("#mount-type").css('display','none')
			<?php } else { ?>
				$("#mount-type").css('display','inline')
			<?php } ?>	

			
			$('#mount-type-select').val('net use');
			$("#drive-letter").css('display','inline')
			$("#target-path").css('display','none')
			$("#access_type_div").css('display','none')
			
			$('#drive-letter-select').val('D');
			$('#target-path-textarea').html('');
			checkType(type)
			$("#modal-authorization").modal('show');
		}
	}
}

//选择挂载方式
function checkType(val){
	if(val == 2){
		$("#modal-authorization .modal-title").html("取消授权")
		$("#tps").show();
		$("#mount-type").css('display','none')
		$("#drive-letter").css('display','none')
		$("#target-path").css('display','none')
		$("#access_type_div").css('display','none')
		$("#mount-type").css('display','none')
	} else {
		$("#modal-authorization .modal-title").html("批量授权")
		$("#tps").hide();
		mountTypeSelect()
		<?php if($store_data['store_type'] == "fics"){ ?>
			$("#mount-type").css('display','none')
		<?php } else { ?>
			$("#mount-type").css('display','inline')
		<?php } ?>
	}
}

$('#mount-type-select').on('click',function(){
	mountTypeSelect();
});

function mountTypeSelect(){
	var type;
	type = $('#mount-type-select').val();
	switch(type){
		case "net use":
			$("#drive-letter").css('display','inline')
			$("#target-path").css('display','none')
			$("#access_type_div").css('display','none')
		break;
		case "mount":
			$("#drive-letter").css('display','none')
			$("#target-path").css('display','inline')
			$("#access_type_div").css('display','inline')
		break;
		default:
			$("#drive-letter").css('display','none')
			$("#target-path").css('display','none')
			$("#access_type_div").css('display','none')
		break;
	}
}

// $('#sumbiter').on('click',function(){
// 	var type = $('input[name="type"]:checked').val();
// 	var mount = '';
// 	var drive = '';
// 	var path = '';
// 	if(type == '4'){
// 		mount = $('#mount-type-select').val()
// 		switch(mount){
// 			case 'net use':
// 				drive = $('#drive-letter-select').val()
// 			break;
// 			case 'unc':
// 			break;
// 			case 'mount':
// 				path = $('#target-path-textarea').val()
// 			break;
// 		}
		
// 	}
// });

$('#sumbiter').on('click',function(){
	set()
})

//发送请求
function set(){
	var authority = $('input[name="type"]').val()
	var type = $('#mount-type-select').val()
	var drive = $('#drive-letter-select').val()
	var path = $('#target-path-textarea').val()
	var access_type =  $('#access_type').val()

	var selectHosts = $("#table").bootstrapTable('getSelections');
	var id = '';
	$.each(selectHosts,function(i,n){
		if(id != ""){
			id += ",";
		}
		id += n.id
	})
	
    $("#modal-authorization").modal('hide');
	$.ajax({
    		type: "post",
    		url: "<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'ajax', 'action' =>'network', 'fics', 'setFicsRelationHosts']); ?>",
    		async: true,
    		timeout: 9999999,
    		data: {
    			basic_id: id,
    			authority: authority,
    			type: type,
    			access_type:access_type,
    			drive: drive,
    			path: path,
    			id: <?= $vol_id?>
    		},
    		success: function(data) {
    			data = $.parseJSON(data);
    			if (data.code != 0) {
    				showModal('提示', 'icon-exclamation-sign', data.msg, '', '','0','关闭');
    			} else {
    				showModal('提示', 'icon-exclamation-sign', data.msg, '', '','0','关闭');
    				refreshTable()
    			}
    			// $obj.prop('disabled', false);
    		}
    	});
}

</script>
<?php
$this -> end();
?>