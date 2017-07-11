<!-- 镜像 -->
<?= $this->element('network/lists/left',['active_action'=>'images']); ?>
<div class="wrap-nav-right">

	<div class="wrap-manage">
		<div class="top">
			<span class="title">镜像</span>

			<div id="maindiv-alert"></div>
		</div>
        <div class="mirror-head-box">
            <div class="service-content-navi">
                <ul class="clearfix text-center">
                    <li class="active"><a href="##">系统镜像</a></li>
                    <li class=""><a href="##">私有镜像</a></li>
                    <li class=""><a href="##">公共镜像</a></li>
                </ul>
            </div>
            <div class="center clearfix">
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
	                <?php }?>
                    <div class="dropdown">
                        厂商:
                        <a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <span class="pull-left" id="agent" val="">全部</span>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="javascript:;" onclick="local(0,'','全部')">全部</a></li>
                            <?php
						if(isset($agent)){
							foreach($agent as $value) {
								if ($value['parentid'] == 0) {?>
                            <li><a href="#" onclick="local(<?php echo $value['id'] ?>,'<?php echo $value['class_code'] ?>','<?php echo $value['agent_name'] ?>')"><?php echo $value['agent_name'] ?></a></li>
                            <?php }
							}
						} ?>
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
        </div>

		<div class="images-checkbox">
			<div class="bot ">
				<button class="btn btn-addition" onclick="refreshTable(1);">
					<i class="icon-refresh"></i>&nbsp;&nbsp;刷新
				</button>
				<table id="table01" data-toggle="table"
				data-pagination="true" 
				data-side-pagination="server"
				data-locale="zh-CN"
				data-click-to-select="true"
				data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'Imagelist', 'lists']); ?>?source=1&is_private=0"
				data-unique-id="id">
				<thead>
					<tr>
						<th data-checkbox="true"></th>
						<th data-field="id" >Id</th>
				<th data-field="image_code">镜像Code</th>
						<th data-field="image_name">镜像名称</th>
						<th data-field="os_family" >操作系统</th>
						<!-- <th data-field="location_name">厂商</th> -->
						<th data-field="agents" data-formatter="formatter_local">部署区位</th>
						<th data-field="smallest_space">空间要求</th>
					</tr>
				</thead>
			</table>
		</div>
		<div class="bot" style="display:none;">
			<button class="btn btn-addition" onclick="refreshTable(2);">
				<i class="icon-refresh"></i>&nbsp;&nbsp;刷新
			</button>
			<table id="table02" data-toggle="table"
			data-pagination="true"
			data-side-pagination="server"
			data-locale="zh-CN"
			data-click-to-select="true"
			data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'Imagelist', 'lists']); ?>?source=2&is_private=1"
			data-unique-id="id">
			<thead>
				<tr>
					<th data-checkbox="true"></th>
					<th data-field="id" >Id</th>
				<th data-field="image_code" >镜像Code</th>
					<th data-field="image_name">镜像名称</th>
					<th data-field="os_family" >操作系统</th>
					<!-- <th data-field="location_name">厂商</th> -->
					<th data-field="agents" data-formatter="formatter_local">部署区位</th>
					<th data-field="smallest_space">空间要求</th>
				</tr>
			</thead>
		</table>
	</div>
	<div class="bot" style="display:none;">
		<button class="btn btn-addition" onclick="refreshTable(3);">
			<i class="icon-refresh"></i>&nbsp;&nbsp;刷新
		</button>
		<table id="table03" data-toggle="table"
		data-pagination="true"
		data-side-pagination="server"
		data-locale="zh-CN"
		data-click-to-select="true"
		data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'Imagelist', 'lists']); ?>?source=2&is_private=0"
		data-unique-id="id">
		<thead>
			<tr>
				<th data-checkbox="true"></th>
				<th data-field="id" >Id</th>
				<th data-field="image_code" >镜像Code</th>
				<th data-field="image_name">镜像名称</th>
				<th data-field="os_family" >操作系统</th>
				<!-- <th data-field="location_name">厂商</th> -->
				<th data-field="agents" data-formatter="formatter_local">部署区位</th>
				<th data-field="smallest_space">空间要求</th>
			</tr>
		</thead>
	</table>
</div>
</div>
</div>
</div>
<div id="maindiv"></div>
<!-- 共享镜像 -->
<!-- <div class="modal fade" id="modal-share" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
				aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
			<h5 class="modal-title">公共镜像</h5>
		</div>
		<form id="check-agent-form" method="post">
			<div class="modal-body">
				<div class="modal-form-group">
					<label>镜像名称:</label>
					<div>
						<input id="share-image-name" name="image_name" type="text" placeholder="abcdefga"/>
						<input id="share-image-id" name="id" type="hidden"/>
						<input id="checked-agent-id" name="agentId" type="hidden"/>
					</div>
				</div>

				<div class="modal-form-group">
					<label>共享区位:</label>
					<div>
						<ul id="treeDemo" class="ztree"></ul>
					</div>
				</div>
				<div class="modal-form-group">
					<p>温馨提示:</p>
					<p>1.dadada</p>
					<p>2.asa1</p>
				</div>


			</div>
			<div class="modal-footer">
				<button id="edit-submit" type="button" class="btn btn-primary">确认</button>
				<button id="" type="button" class="btn btn-danger"
				data-dismiss="modal">取消</button>
			</div>
		</form>
	</div>
</div>
</div> -->

<!-- 右键弹框 -->
<div class="context-menu" id="context-menu">
	<ul>
		<!-- <li id="share"><a href="javascript:void(0);"><i class="icon-share"></i> 公共镜像</a></li> -->
		<?php if(in_array('ccf_image_del',$this->Session->read('Auth.User.popedomname'))): ?>
		<li id="pr_image_del"><a href="javascript:void(0);"><i class="icon-trash"></i>删除私有镜像</a></li>
		<?php endif;?>
	</a>
</ul>
</div>

<!-- 右键弹框 -->
<div class="context-menu" id="context-menu3">
	<ul>
		<!-- <li id="share3"><a href="javascript:void(0);"><i class="icon-pencil"></i>编辑公共镜像</a></li> -->
		<?php if(in_array('ccf_image_del',$this->Session->read('Auth.User.popedomname'))): ?>
		<li id="pb_image_del"><a href="javascript:void(0);"><i class="icon-trash"></i>删除公共镜像</a></li>
	    <?php endif;?>
	</a>
</ul>
</div>
<script src="/js/jQuery-2.1.3.min.js"></script>
<?= $this->Html->css(['zTreeStyle.css']) ?>
<?= $this->Html->script(['jquery.ztree.core-3.5.js']); ?>
<?= $this->Html->script(['jquery.ztree.excheck-3.5.js']); ?>
<?php
$this->start('script_last');
?>
<script>

// 树形结构
var setting = {
	check: {
		enable: true,
		chkStyle: "checkbox",
		chkboxType: { "Y": "ps", "N": "ps" }
	},
	data: {
		key:{
			name: 'agent_name'
		},
		simpleData: {
			enable: true,
			pIdKey:'parentid'
		}
	}
};


var data ='<?php echo $data; ?>';
data  = eval('(' + data + ')');
var zNodes = data;

$.fn.zTree.init($("#treeDemo"), setting, zNodes);

$(function(){
	// 镜像切换
	$(".service-content-navi li").on('click',function(){
		var index=$(this).index();
		$(".service-content-navi li").removeClass('active')
		$(this).addClass('active');
		$(".images-checkbox .bot").hide();
		$(".images-checkbox .bot").eq(index).show();
	});

	$('#table02').contextMenu('context-menu', {
		bindings:{
			// 'share': function(event) {
			// 	var treeObj = $.fn.zTree.getZTreeObj("treeDemo");
			// 	var nodes = treeObj.getNodes();
			// 	treeObj.expandAll(true);
			// 	treeObj.checkAllNodes(false);
			// 	var uniqueId = $(event).attr('data-uniqueid');
			// 	var row = $('#table02').bootstrapTable('getRowByUniqueId', uniqueId);
			// 	$("#share-image-name").val(row.image_name);
			// 	$("#share-image-id").val(row.id);
			// 	console.log(row);
			// 	for (var i=0, l=nodes.length; i < l; i++) {
			// 		for (var j=0, ll=nodes[i]['children'].length; j < ll; j++) {
			// 			for (var m=0, rowl=row.agents.length; m < rowl; m++) {
			// 				if (row['agents'][m]['id'] == nodes[i]['children'][j]['id']) {
			// 					treeObj.checkNode(nodes[i]['children'][j], true, true);
			// 				}
			// 			}
			// 		}
			// 	}
			// 	$('#modal-share').modal("show");
			// },
			'pr_image_del':function(event){
				var image_id = $(event).attr('data-uniqueid');
			    if (image_id != undefined) {
			        $.ajax({
			            type: "post",
			            url: '/console/ajax/network/hosts/ajaxImageDel',
			            async: true,
			            timeout: 9999,
			            data: {
			                method: 'image_del',
			                basic_id: image_id,
			            },
			            //dataType:'json',
			            success: function(data) {
			                
			                data = $.parseJSON(data);
			                if (data.Code != "0") {
			                      showModal('提示', 'icon-exclamation-sign', data.Message, '', '', 0);
			                      //$("#btnEsc").html("关闭");
			                } 
			            }
			        });
			    }
			}
		}
	})


	$('#table03').contextMenu('context-menu3', {
		bindings:{
			// 'share3': function(event) {
			// 	var treeObj = $.fn.zTree.getZTreeObj("treeDemo");
			// 	var nodes = treeObj.getNodes();
			// 	treeObj.expandAll(true);
			// 	treeObj.checkAllNodes(false);
			// 	var uniqueId = $(event).attr('data-uniqueid');
			// 	var row = $('#table03').bootstrapTable('getRowByUniqueId', uniqueId);
			// 	$("#share-image-name").val(row.image_name);
			// 	$("#share-image-id").val(row.id);

			// 	for (var i=0, l=nodes.length; i < l; i++) {
			// 		for (var j=0, ll=nodes[i]['children'].length; j < ll; j++) {
			// 			for (var m=0, rowl=row.agents.length; m < rowl; m++) {
			// 				if (row['agents'][m]['id'] == nodes[i]['children'][j]['id']) {
			// 					treeObj.checkNode(nodes[i]['children'][j], true, true);
			// 				}
			// 			}
			// 		}
			// 	}
			// 	$('#modal-share').modal("show");
			// },
			'pb_image_del':function(event){
				var image_id = $(event).attr('data-uniqueid');
			    if (image_id != undefined) {
			        $.ajax({
			            type: "post",
			            url: '/console/ajax/network/hosts/ajaxImageDel',
			            async: true,
			            timeout: 9999,
			            data: {
			                method: 'image_del',
			                basic_id: image_id,
			            },
			            //dataType:'json',
			            success: function(data) {
			                
			                data = $.parseJSON(data);
			                console.log(data.Message);
			                if (data.Code != "0") {
			                      showModal('提示', 'icon-exclamation-sign', data.Message, '', '', 0);
			                      //$("#btnEsc").html("关闭");
			                } 
			            }
			        });
			    }
			}
		}
	})
})

$("#edit-submit").on('click',function(){
	var treeObj = $.fn.zTree.getZTreeObj("treeDemo");
	var nodes = treeObj.getCheckedNodes(true);
	var checkAgentId="";
	for (var i=0, l=nodes.length; i < l; i++) {
		if (nodes[i]['parentid'] !=0 || nodes[i]['parentid'] !=null) {
			checkAgentId += nodes[i]['id']+',';
		};
	}
	$("#checked-agent-id").val(checkAgentId);
	$('#modal-share').modal("hide");
    //ajax提交页面
    $.ajax({
    	url:"<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','Imagelist','edit']); ?>",
    	data:$('#check-agent-form').serialize(),
    	method:'post',
    	dataType:'json',
    	success:function(e){
            //操作成功
            if(e.code == '0000'){
            	refreshTable(1);
            	refreshTable(2);
            	refreshTable(3);
            }else{
            	alert(e.msg);
            }
        }
    });
    return false;
})

//地域查询
function local(id,class_code,agent_name) {
	if (agent_name) {
		var search= $("#txtsearch").val();
		$('#agent_t').html('全部');
		$('#agent').html(agent_name);
		$('#agent').attr('val', id);
		$('#agent_t').attr('val', '');
		refreshTable(1);
		refreshTable(2);
		refreshTable(3);
		var jsondata = <?php echo json_encode($agent); ?>;
		if(id!=0){
			var data='';
			var data='<li><a href="javascript:;" onclick="local_two(\'\',\'全部\',\'' + class_code + '\',\'' + '' + '\')">全部</a></li>';
			$.each(jsondata, function (i, n) {
				if(n.parentid == id){
					data += '<li><a href="#" onclick="local_two(\'' + n.class_code + '\',\'' + n.agent_name + '\',\'' + class_code + '\',\'' + n.id + '\')">' + n.agent_name + '</a></li>';
				}
			})
			$('#agent_two').html(data);
		}else{
			data='';
			$('#agent_two').html(data);
		}

	}
}
function local_two(class_code2,agent_name,class_code,id){
	var search= $("#txtsearch").val();
	$('#agent_t').html(agent_name);
	$('#agent_t').attr('val',id);
	refreshTable(1);
	refreshTable(2);
	refreshTable(3);
}

//搜索绑定
$("#txtsearch").on('keyup', function() {
	if(timer!=null){
		clearTimeout(timer);
	}
	var search= $("#txtsearch").val();
	var class_code = $("#agent").attr('val')

	var class_code2 = $("#agent_t").attr('val')
	var search= $("#txtsearch").val();
	var timer = setTimeout(function(){
		refreshTable(1);
		refreshTable(2);
		refreshTable(3);
	},1000);
});
function departmentlist(id,name){
    $("#deparmets").attr("val",id);
    $("#deparmets").html(name);
    var timer = setTimeout(function(){
		refreshTable(1);
		refreshTable(2);
		refreshTable(3);
	},1000);
}
function refreshTable(data) {
	var search= $("#txtsearch").val();
    //$('#table').bootstrapTable('showLoading');
    console.log($("#agent").attr('val'))
    var class_code = $("#agent").attr('val');
    var class_code2 =$("#agent_t").attr('val');
    var department_id =$("#deparmets").attr('val');
    switch (data) {
    	case 1:
    	source=1;
    	share=0;
    	break;
    	case 2:
    	source=2;
    	share=1;
    	break;
    	case 3:
    	source=2;
    	share=0;
    	break;
    }
    $('#table0'+data).bootstrapTable('refresh', {
    	url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'Imagelist', 'lists']); ?>?source="+source+"&is_private="+share,
    	query: {department_id:department_id,class_code2:class_code2,class_code: class_code,search: search}
    });
    //$('#table').bootstrapTable('hideLoading');
}

//返回网络

function formatter_local(value, row, index) {
	if (value != '') {
		var local='';
   		 $.each(value, function(i, n){
   		 	if(n.parentid !=0){
   		 	local +=n.display_name+'<br/>';

   		 	}
   		 });
   		return local;
   	} else {
   		return "-";
   	}
   }
   
function showModal(title, icon, content, content1, method, type, delete_info) {
    	$("#maindiv").empty();
    	var html = "";
    	html += '<div class="modal fade" id="modal" tabindex="-1" role="dialog">';
    	html += '<div class="modal-dialog" role="document">';
    	html += '<div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
    	html += '<h5 class="modal-title">' + title + '</h5>';
    	html += '</div><div class="modal-body"><i class="'+icon+' icon-color-gray text-primary"></i>&nbsp;&nbsp;' + content + '<span class="text-primary batch-warning">' + content1 + '</span>';
        if(delete_info == 1){
            html +='<br /><i class="icon-exclamation-sign icon-color-gray text-primary"></i>&nbsp;&nbsp;<span > 可在回收站找回已删除的主机</span><span class=" text-primary batch-warning" id="modal-dele-name"></span>';
        }

        html +='</div>';
    	html += '<div class="modal-footer"><button id="btnModel_ok" onclick="' + method + '" type="button" class="btn btn-primary">确认</button><button type="button" class="btn btn-danger" id="btnEsc" data-dismiss="modal">取消</button></div></div></div></div>';
    	$("#maindiv").append(html);
    	if (type == 0) {
    		$("#btnModel_ok").remove();
    	}
    	$('#modal').modal("show");
    }
</script>
<?php
$this->end();
?>