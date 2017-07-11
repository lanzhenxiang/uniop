<!-- author by lixin -->
<!-- TODO 添加反向引入列表在此 -->
<?= $this -> element('import/lists/left', ['active_action' => 'disks']); ?>
<div class="wrap-nav-right">
	<div class="wrap-manage">
		<div class="top">
			<span class="title">反向引入主机</span>
		</div>
		<div class="center clearfix">
			<button class="btn btn-addition">
				<i class="icon-refresh">&nbsp;&nbsp;刷新</i>
			</button>
			<button class="btn btn-addition">
				<i class="icon-signin">&nbsp;&nbsp;引入</i>
			</button>
			<button class="btn btn-addition">
				<i class="icon-inbox">&nbsp;&nbsp;引入硬盘</i>
			</button>
			<div class="pull-right">
				<div class="dropdown">
					VPC:
					<a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button">
						<span class="pull-left">全部</span>
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
                        <li><a href="javascript:;" onclick="local(0,'','全部')">全部</a></li>
                        <?php if(isset($_vpc)){
                            foreach($_vpc as $value) {?>
                                <li><a href="#" onclick="local(<?php echo $value['id'] ?>,'<?php echo $value['class_code'] ?>','<?php echo $value['agent_name'] ?>')">
                                 <?php echo $value['name'] ?></a>
                                 </li>
                            <?php } ?>
                        <?php } ?>
                    </ul>
				</div>
				<div class="dropdown">
					子网:
					<a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button">
						<span class="pull-left">全部</span>
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						<li><a href="">全部</a></li>
					</ul>
				</div>
				<div class="dropdown">
					主机:
					<a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button">
						<span class="pull-left">全部</span>
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						<li><a href="">全部</a></li>
					</ul>
				</div>
			</div>
		</div>
        <input type="hidden" id="txtdeparmetId" value="<?= $_default["id"] ?>" />
		<div class="bot">
		<!-- TODO 主机列表table -->
		<table id="table" data-toggle="table"
    	 data-pagination="true" 
    	 data-side-pagination="server"
    	 data-locale="zh-CN"
    	 data-click-to-select="true"
    	 data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'Import', 'hosts', 'MergeJson','?'=>['department_id'=>$_default["id"]]]); ?>"
    	 data-unique-id="Identifier">
    	 <thead>
    	   <tr>
    		<th data-checkbox="true"></th>
    		<!-- <th data-field="H_ID" >Id</th> -->
    		<th data-field="Identifier" data-formatter="formatter_main">主机Code</th>
    		<!-- <th data-field="H_Code" data-formatter="formatter_code">登录</th> -->
    		<!-- <th data-field="H_Name">主机名称</th> -->
    		<th data-field="status" data-formatter="formatter_state">状态</th>
    		<th data-field="D_Os_Form" data-formatter="formatter_operateSystem">操作系统</th>
    		<th data-field="E_Name">部署区位</th>
    		<th data-field="G_Name" data-formatter="formatter_vxnets">所属子网</th>
    		<th data-field="D_Ip" data-formatter="formatter_ip">主机IP</th>
    		<th data-field="" data-formatter="formatter_config">配置</th>
    		<th data-field="E_Ip" data-formatter="formatter_eip">公网IP</th>
    		<th data-field="H_time" data-formatter=timestrap2date>创建时间</th>
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
//showModal('提示', 'icon-question-sign', '确认要启动机器', row.H_Name, 'ajaxFun(\'' + row.H_Code + '\',\'ecs_start\',\'' + row.H_ID + '\')');
//showModal('提示', 'icon-exclamation-sign', '当前设备状态无法进行操作', '', '', 0);
//$("#btnEsc").html("关闭");
function showModal(title, icon, content, content1, method, type, delete_info) {
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
    html += '<div class="modal-footer"><button id="btnModel_ok" onclick="' + method + '" type="button" class="btn btn-primary">确认</button><button type="button" class="btn btn-danger" id="btnEsc" data-dismiss="modal">取消</button></div></div></div></div>';
    $("#maindiv").append(html);
    if (type == 0) {
        $("#btnModel_ok").remove();
    }
    $('#modal').modal("show");
}
</script>
<?php $this -> end(); ?>