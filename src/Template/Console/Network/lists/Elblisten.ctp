<!--  配置监听器  列表 -->
<?= $this->element('network/lists/left',['active_action'=>'elb']); ?>
<div class="wrap-nav-right">
	<div class="wrap-manage">
		<div class="top">
			<span class="title">分配到监听器</span>
		</div>
		<div class="center clearfix">
		   <a href="<?= $this->Url->build(['controller'=>'network','action'=>'lists','elb']); ?>" class="btn btn-addition">返回负载均衡列表</a>
		</div>
		<div class="center clearfix">
		   <div class="dropdown">
			 ELB名称:<input type="text" value="<?= $_Lib->name ?>" disabled="disabled">
			</div>
			<div class="dropdown">
			 ELB Code:<input style="width:160px;" type="text" value="<?= $_Lib->code ?>" disabled="disabled">
			</div>
			<div class="dropdown">
			 所属子网:<span><?= $_Subnet->name ?></span>
			</div>
			<div class="dropdown">
			 所属子网Code:<input style="width:150px;" type="text" value="<?= $_Subnet->code ?>" disabled="disabled">
			</div>
		</div>
				<div class="bot margint20">
                    <!--tab-->
                    <div class="modal-title-list">
                        <ul class="clearfix">
                            <li class="active">监听器</li>
                            <li>后端实例</li>
                        </ul>
                    </div>
<!--button-->
				<div class="margintb20 clearfix ">
				   <a href="javascript:refreshTable();" id="btnRefresh" class="btn btn-addition"><i class="icon-refresh"></i>&nbsp;&nbsp;刷新</a>
                    <a class="btn btn-addition" id="addListen" href="javascript:addListen()"><i class="icon-plus"></i>&emsp;<span>新建</span></a>
				   <button class="btn btn-default" onclick="" id="btnDel" disabled="disabled">
				  <i class="icon-remove"></i>&emsp;<span>删除</span>
				</button>
				</div>
                    <div class="table-body espe-tab-box">
                        <!--监听器-->
                        <table id="elb_listen" class="table_listen" data-toggle="table"
                               data-pagination="true"
                               data-side-pagination="server"
                               data-locale="zh-CN"
                               data-click-to-select="true"
                               data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'elb','list_listen','?'=>['ELB'=>$_Lib->id]]);?>"
                               data-unique-id="id">
                            <thead>
                            <tr>
                                <th data-checkbox="true"></th>
                                <th data-field="id">Id</th>
                                <th data-field="name">监听器名</th>
                                <th data-field="elbRuleCode">监听器CODE</th>
                                <th data-field="lbMethod" >策略</th>
                                <th data-field="serviceType">协议</th>
                                <th data-field="lbPort">端口</th>
                            </tr>
                            </thead>
                        </table>
                        <!--后端实例-->
                        <table id="elb_netCard" class="table_T" data-toggle="table"
                               data-pagination="true"
                               data-side-pagination="server"
                               data-locale="zh-CN"
                               data-click-to-select="true"
                               data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'elb','list_ElbHost','?'=>['ELB'=>$_Lib->id]]);?>"
                               data-unique-id="id">
                            <thead>
                            <tr>
                                <th data-checkbox="true"></th>
                                <th data-field="id" >Id</th>
                                <th data-field="code">ECS名</th>
                                <th data-field="name" >ECSCode</th>
                                <th data-field="network_code" >绑定网卡</th>
                                <th data-field="is_default" data-formatter="formatter_isdefault">网卡类型</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
		</div>
	</div>
</div>
<!-- 新建 -->
<div class="modal fade" id="listen-add" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
				<h5 class="modal-title">添加监听器</h5>
			</div>

			<div class="modal-body clearfix">
				<div class="pull-left fire-left">
					<form action="" method="post" id="addfrom_listen" class="form-horizontal">
						<input name="elb_id" type="hidden" value="<?= $_Lib->id ?>" />
						<input name="vpc_id" type="hidden" value="<?= $_Vpc->id ?>" />
						<div class="modal-form-group">
							<label>名称:</label>
							<div style="line-height:28px;">
							  <input name="elbCode" id="elbCode" type="hidden" value="<?= $_Lib->code ?>">
							  <input name="name" type="text" id="name">
							  <span class="text-danger" id="name-warning" style="font-size:12px;"></span>
							</div>
						</div>
						<div class="modal-form-group">
							<label>监听协议</label>
							<div class="bk-select-group">
								<select name="protocol" id="protocol">
									<option value="HTTP">HTTP</option>
									<option value="TCP">TCP</option>
								</select>
							</div>
						</div>
						<div class="modal-form-group">
							<label>端口:</label>
							<div style="line-height:28px;">
							  <input name="port" type="text" id="port">
							  <span class="text-danger" id="port-warning" style="font-size:12px;"></span>
							</div>
						</div>
						<div class="modal-form-group">
							<label>负载方式</label>
							<div class="bk-select-group">
								<select name="lbMethod" id="lbMethod">
									<option value="ROUNDROBIN">轮询</option>
                                    <option value="LEASTCONNECTION">最小连接数</option>
								</select>
							</div>
						</div>
					</form>
				</div>
				<div class="fire-right">
					<p>快捷方式</p>
					<ul>
						<li class="active" data-port="80">http</li>
						<li data-port="443">https</li>
					</ul>
				</div>

			</div>
			<div class="modal-form-point margin20">
				<p>添加监听器后请检查负载均衡器的防火墙规则，确保该端口流量可以通过，否则从外网无法访问你的服务</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary">确认</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
			</div>
			</div>
	</div>
</div>
<!-- 添加后端 -->
<div class="modal fade" id="addEnd" tabindex="-1" role="dialog">
	<div class="modal-dialog add-subnet-madel" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
				<h5 class="modal-title">添加后端</h5>
			</div>
            <!--完成状态-->
            <div class="add-tit-flow">
                <ul class="steps_2 anchor">
                    <li><a class="selected back-step1 add-step" href="javascript:void(0)">
                        <span class="stepNumber"></span>
                        <span class="stepDesc">选择主机</span>
                    </a></li>
                    <li>
                        <a class="disabled add-step" href="javascript:void(0)">
                            <span class="stepNumber"></span>
                            <span class="stepDesc">绑定网卡</span>
                        </a>
                    </li>
                </ul>
            </div>
            <!--add  content-->
			<div class="modal-body clearfix">
                <!--选择esc-->
                <div class="esc-section">
                    <ol class="clearfix end-lineheight">
                        <li class="esc-tit-list">
                            VPC：<input class="marginr20" value="<?= $_Vpc->name ?>" disabled="disabled"/>
                        </li>
                        <li class="esc-tit-list">
                            子网：
                            <div class="dropdown">
                                <a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    <span class="pull-left" id="spanSubnet" val="">全部</span>
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" id="drSubnet">
                                </ul>
                            </div>
                        </select>
                        </li>
                        <li class="esc-tit-list">
                            <div class="search">
                                <input type="text" id="txtsearch" name="search" placeholder="搜索"/>
                                <i class="icon-search"></i>
                            </div>
                        </li>
                    </ol>
                    <div class="modal-disk-content">
                        <table id="hosts_table" data-toggle="table"
                               data-pagination="true"
                               data-side-pagination="server"
                               data-locale="zh-CN"
                               data-click-to-select="true"
                               data-single-select="true"
                               data-unique-id="id">
                            <thead>
                            <tr>
                                <th data-field="id">ID</th>
                                <th data-field="code" ="true">主机CODE</th>
                                <th data-field="name">主机名称</th>
                                <th data-field="S_Name">所属子网</th>
                                <th data-formatter="esc_control">操作</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <!--subnet-->
                <div class="subnet-section"style="display: none;">
                    <div class="clearfix end-lineheight">
                        所属子网：<span id="txtSubnetName" class="marginr20"></span>
                        所属子网Code：<span id="txtsubnetCode" class="marginr20"></span>
                        待绑定ELBCode：<span><?= $_Lib->code ?></span>
                        <input type="hidden" name="txtselectEcs" id="txtselectEcs"/>
                    </div>
                    <div class="modal-disk-content">
                        <table id="netCard_table" data-toggle="table"
                               data-pagination="true"
                               data-side-pagination="server"
                               data-locale="zh-CN"
                               data-click-to-select="true"
                               data-single-select="true"
                               data-unique-id="id">
                            <thead>
                            <tr>
                                <!-- <th data-checkbox="true"></th> -->
                                <th data-field="id">ID</th>
                                <th data-field="code">主机CODE</th>
                                <th data-field="name">主机名称</th>
                                <th data-field="network_code">网卡CODE</th>
                                <th data-field="is_default" data-formatter="formatter_isdefault">网卡类型</th>
                                <th data-field="E_Code">已绑定负载CODE</th>
                                <th data-formatter="subnet_control">操作</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
			</div>
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
		<label>名称:</label>
		<div>
		  <input id="modal-modify-name" name="name" type="text" />
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
<div id="maindiv"></div>
<!-- 删除 -->

<?php
$this->start('script_last');
?>
<script type="text/javascript">
    $(function(){
        $(".table-body .bootstrap-table").eq(1).css("display","none");
        $(".modal-title-list li").eq(0).click()
    })
//动态创建modal
function showModal(title, icon, content, content1, method) {
	$("#maindiv").empty();
	var html = "";
	html += '<div class="modal fade" id="modal" tabindex="-1" role="dialog">';
	html += '<div class="modal-dialog" role="document">';
	html += '<div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
	html += '<h5 class="modal-title">' + title + '</h5>';
	html += '</div><div class="modal-body"><i class="' + icon + ' icon-color-gray text-primary"></i>&nbsp;&nbsp;' + content + '<span class="text-primary">' + content1 + '</span></div>';
	html += '<div class="modal-footer"><button id="btnMk" onclick="' + method + '" type="button" class="btn btn-primary">确认</button><button id="btnEsc" type="button" class="btn btn-danger" data-dismiss="modal">取消</button></div></div></div></div>';
	$("#maindiv").append(html);

	$('#modal').modal("show");
}
//tab切换
    control();
    function control(){
        var $tabIndex;
        var inputCheck=function(table){
            $(table).find("input[type='checkbox']").attr("checked",false);
        };

        $(".modal-title-list").on("click", "li", function(){
            $(this).addClass("active");
            $(this).siblings().removeClass("active");
            $tabIndex=$(this).index();
            var $table=$(".table-body .bootstrap-table").eq($tabIndex);
            inputCheck(".table-body .bootstrap-table");
            if ($("tbody input:checked").length >= 1) {
                $(this).parents(".bot").find(".btn-default").attr('disabled', false);
            } else {
                $(this).parents(".bot").find(".btn-default").attr('disabled', true);
            }
            if($tabIndex==0) {
                $("#addListen").find("span").html("新建");
                $("#btnDel").find("span").html("删除");
                $("#addListen").attr("href","javascript:addListen()");
            }else{
                $("#addListen").find("span").html("添加");
                $("#btnDel").find("span").html("解绑");
                $("#addListen").attr("href","javascript:addHosts()");
            }
            $table.show();
            $table.siblings().hide();
        });
        $("#btnDel").on('click',function(){
            if($tabIndex==0){
                var tot = $('#elb_listen').bootstrapTable('getSelections');
                var netCard_tot = $('#elb_netCard').bootstrapTable('getData');
                var isgo = true;
                $.each(netCard_tot, function(i,val){
                    isgo=false;
                    return;
                });

                if(isgo){
                    showModal("提示", 'icon-question-sign', "确认要删除选中的监听器？","", "");
                    $("#btnMk").one('click', function() {
                        $.each(tot, function(i,val){
                            del_listen(val.id);
                        });
                    });
                }else{
                    showModal("提示", 'icon-question-sign', "负载均衡中存在绑定的网卡设备请解绑","", "");
                    $("#btnEsc").html("关闭");
                    $("#btnMk").remove();
                }

            }else{
                var tot = $('#elb_netCard').bootstrapTable('getSelections');
                showModal("提示", 'icon-question-sign', "确认要解绑选中的主机网卡？","", "");
                $("#btnMk").one('click', function() {
                    $.each(tot, function(i,val){
                        ajax_elb_hosts("lbs_unbind",val.network_code);
                    });
                });
            }

        })

    }

    $("table").on('all.bs.table.table', function(e, row, $element) {
        if ($("tbody input:checked").length >= 1) {
            $(this).parents(".bot").find(".btn-default").attr('disabled', false);
        } else {
            $(this).parents(".bot").find(".btn-default").attr('disabled', true);
        }
    });
//删除
function del_listen(id) {
	$.ajax({
		method: 'post',
		url: '<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network']); ?>/<?php echo "Elb" ?>/<?php echo "del_Listen" ?>',
		data: {
			id: id
		},
		success: function(data) {
			$('#modal').modal("hide");
			data = $.parseJSON(data);
			if (data.code == '0000') {
				window.location.reload(); //刷新当前页面.
			} else {
				// tentionHide('操作失败',1);
			}
		}
	});
}

function edit_listen(id) {
	$.ajax({
		method: 'post',
		url: '<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network']); ?>/<?php echo "Elb" ?>/<?php echo "edit_Listen" ?>',
		data: $("#modal-modify-form").serialize(),
		success: function(data) {
			$('#modal').modal("hide");
			data = $.parseJSON(data);
			if (data.code == '0000') {
				window.location.reload(); //刷新当前页面.
			} else {
				// tentionHide('操作失败',1);
			}
		}
	});
}

function unbind_hosts(id, protocol, port, listen) {
	var tables = $("#" + id).bootstrapTable('getSelections');
	if (tables.length != 0) {
		showModal("提示", 'icon-question-sign', "是否解绑选中的主机", "", "");
		$("#btnMk").unbind("click");
		$("#btnMk").one('click', function() {
			$("#modal").modal("hide");
			tables.forEach(function(e) {
				ajax_elb_hosts("lbs_unbind", e['code']);
			});
		});
	} else {
		showModal("提示", 'icon-exclamation-sign', "没有选中主机", "", "");
		$("#btnEsc").html("关闭");
		$("#btnMk").remove();
	}
}

//添加网卡操作
function esc_control(value, row, index){
    var bnt="<button class='btn btn-primary elb-bind' data-mark='0' data-ecs='"+row.code+"' data-subnet='"+row.subnet+"' data-subnetName='"+row.S_Name+"'>选择主机</button>"
    return bnt;
}
//绑定ELB按钮
$(".esc-section").delegate('.elb-bind','click',function(){
    $(".add-step").eq(1).addClass("selected").removeClass("disabled");
    $(".add-step").eq(0).addClass("done").removeClass("selected");
    $(this).parents(".esc-section").hide();
    var ecs_code = $(this).attr("data-ecs");
//    console.log(ecs_code);
    var subnet_code = $(this).attr("data-subnet");
    var subnet_name = $(this).attr("data-subnetName");
    $('#netCard_table').bootstrapTable('refresh', {
        url: "/console/ajax/network/elb/getNetCardlistByEcsCode?code=" + ecs_code
    });
    $("#txtsubnetCode").html(subnet_code);
    $("#txtSubnetName").html(subnet_name);
    $(".subnet-section").show();
});
//网卡操作
function subnet_control(value, row, index){
    var val = 0;
    if(row.E_Code==null){
        text="绑定" ;
    }else{
        text="解绑";
        val =1;
    }
    varl = 0;
    var text;
    var bnt="<button class='btn btn-primary elb-unbind' data-mark='"+val+"' data-id='"+row.id+"' data-code='"+row.network_code+"'>"+text+"网卡</button>"
    return bnt;
}
//解绑网卡
$(".subnet-section").delegate('.elb-unbind','click',function(){
    var val=$(this).data('mark');
    var id=$(this).data('id');
    var code=$(this).data('code');
    if(val!=0){
        showModal("提示", 'icon-question-sign', "是否解绑ELB？", "绑定网卡 "+code,'ajax_elb_hosts(\'lbs_unbind\',\''+code+'\')');
        // $("#btnMk").one('click', function() {
        //     $('#modal').modal("hide");
        //     ajax_elb_hosts(table_id, "lbs_unbind", e['code'], protocol, port, id);
        //     setTimeout(function() {
        //         del_listen(id);
        //     }, 6000);
        // });
    }else{
        showModal("提示", 'icon-question-sign', "是否绑定ELB？", "绑定网卡 "+code,'ajax_elb_hosts(\'lbs_bind\',\''+code+'\')');
    }
});
//添加modal还原
$(".back-step1").click(function(){
    $(".add-step").eq(1).addClass("disabled").removeClass("selected");
    $(".add-step").eq(0).addClass("selected").removeClass("done");
    $(".esc-section").show();
    $(".subnet-section").hide();
})

//修改
$(".edit_listen").on("click", function() {
	var name = $(this).attr("attr_name");
	var id = $(this).attr("attr_id");
	$("#modal-modify-name").val(name);
	$("#modal-modify-id").val(id);
	$('#modal-modify').modal("show");

	$("#sumbiter").one('click', function() {
		edit_listen(id);
	});
})
//    espe-bnt-box
function addListen () {
    $("#listen-add").modal("show");
    $("#listen-add").on('shown.bs.modal', function() {
        $('.fire-right').on('click', 'li', function() {
            $('.fire-right').find('li').removeClass('active');
            $(this).addClass('active');
            $('#port').val($(this).attr('data-port'));
        });
    });
    $('#name').on('blur', function() {
        if ($(this).val() != '') {
            $('#name-warning').html('');
        }
    });
    $('#port').on('blur', function() {
        if ($(this).val() != '') {
            $('#port-warning').html('');
        }
        if (!$(this).val().match(/^([1-9]\d*|0)$/)) {
            $('#port-warning').html('只能输入数字');
        }
    });
    $('#port').on('focus', function() {
        $('#port-warning').html('');
    });
    $('#listen-add').on('hidden.bs.modal', function() {
        $('.fire-right').off('click', 'li');
        $('#name').off('blur');
        $('#port').off('blur');
    });
}

$("#addEnd").on('hidden.bs.modal', function() {
	$("#bindHosts").unbind("click");
});



$("#listen-add .btn-primary").on("click", function() { //添加新监听器
	if ($('#name').val() == '' || $('#name').val() == undefined) {
		$('#name-warning').html('名称不能为空');
		return;
	}
	if ($('#port').val() == '' || $('#port').val() == undefined) {
		$('#port-warning').html('端口不能为空');
		return;
	}
	if (!$('#port').val().match(/^([1-9]\d*|0)$/)) {
		$('#port-warning').html('只能输入数字');
		return;
	}
	var isgo = getisRepeatListen($('#protocol').val(), $('#port').val());
	if (isgo == "false") {
		$.ajax({
			method: 'post',
			url: '<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network']); ?>/<?php echo "Elb" ?>/<?php echo "addElb_Listen" ?>',
			data: $("#addfrom_listen").serialize(),
			success: function(data) {
				$('#listen-add').modal("hide");
				data = $.parseJSON(data);
				if (data.code == '0000') {
					window.location.reload(); //刷新当前页面.
					//refreshTable(1);
					//tentionHide('操作成功',0);
				} else {
					//tentionHide('操作失败',1);
				}
			}
		});
	}
});

function addHosts() {
	$('#hosts_table').bootstrapTable('refresh', {
		url: "/console/ajax/network/elb/unuse_hosts?vpc=" + "<?= $_Vpc->code ?>"
	});
	$("#addEnd").modal("show");
    $(".back-step1").click();
    bindSubnet("<?= $_Vpc->code ?>");
}

function bindSubnet(vpc){
    var html="";
    $.get("<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','hosts','getAllsubNet']); ?>?vpCode="+vpc+"",function(data){
        data = $.parseJSON(data);
        $.each(data, function(i, n) {
            // console.log(n);
            html += '<li><a href="#" onclick="selectSubnet(this)" data-id="'+n.netCode+'" data-name="'+n.name+'">' + n.name + '</a></li>';
        });
        $("#drSubnet").html(html);
    });
}

function selectSubnet(tmp){
//     console.log($(tmp).attr("data-id"));
    var listen = $("#hosts_table").attr("listen");
    $("#spanSubnet").val($(tmp).attr("data-id"));
    $("#spanSubnet").html($(tmp).attr("data-name"));
    var search = $("#txtsearch").val();
    $('#hosts_table').bootstrapTable('refresh', {
        url: "/console/ajax/network/elb/unuse_hosts?vpc=" + "<?= $_Vpc->code ?>&subnet="+$(tmp).attr("data-id")+"&search="+search
    });
}

$("#txtsearch").on('keyup', function() {
        if (timer != null) {
            clearTimeout(timer);
        }
        var listen = $("#hosts_table").attr("listen");
        var timer = setTimeout(function() {
            var search = $("#txtsearch").val();
            var subnet = $("#spanSubnet").val();
            $('#hosts_table').bootstrapTable('refresh', {
                url: "/console/ajax/network/elb/unuse_hosts?vpc=" + "<?= $_Vpc->code ?>&subnet="+subnet+"&search="+search
            });
        }, 500);
});

function refreshTable() {
    $('#elb_netCard').bootstrapTable('refresh', {
    url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'elb','list_ElbHost','?'=>['ELB'=>$_Lib->id]]);?>"
    });
    $('#elb_listen').bootstrapTable('refresh', {
    url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'elb','list_listen','?'=>['ELB'=>$_Lib->id]]);?>"
    });
}

$("#hosts_table").on('check.bs.table', function(e, row, $element) {
	$("#hosts_name").html(row.name);
	$("#hosts_protocol").html($(this).attr("protocol"));
	$("#hosts_port").html($(this).attr("port"));
});

$("#hosts_table").on('uncheck.bs.table', function(e, row, $element) {
	$("#hosts_name").html("");
	$("#hosts_protocol").html("");
	$("#hosts_port").html("");
});

function ajax_elb_hosts(method,netCode) {
	$("#addEnd").modal("hide");
    $('#modal').modal("hide");
    $(".subnet-section").hide();
	$.ajax({
		type: "post",
		url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'elb', 'ajaxElb']); ?>",
		async: true,
		data: {
			method: method,
			netCode: netCode,
			loadbalanceCode: '<?= $_Lib->code ?>',
            basicId:'<?= $_Lib->id ?>'
		}
	})
}

function getisRepeatListen(protocol, port) {
	var result = "";
	$.ajax({
		type: "post",
		url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'elb', 'isRepeatListen']); ?>",
		async: false,
		data: {
			protocol: protocol,
			port: port,
			basicId: "<?= $_Lib->id ?>"
		},
		success: function(data) {
			if (data != "null") {
				$('#port-warning').html("监听器重复");
				result = "true";
			} else {
				result = "false";
			}
		}
	});
	return result;
}


    function notifyCallBack(value) {
    	if (value.MsgType == "success") {
    		if (value.Data.method == "lbs_bind" || value.Data.method == "lbs_unbind"||value.Data.method == "lbs_add_listener" || value.Data.method == "lbs_del_listener") {
                $('#elb_netCard').bootstrapTable('refresh', {
                    url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'elb','list_ElbHost','?'=>['ELB'=>$_Lib->id]]);?>"
                });
                $('#elb_listen').bootstrapTable('refresh', {
                    url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'elb','list_listen','?'=>['ELB'=>$_Lib->id]]);?>"
                });
    		}
    	}
    }

    function formatter_isdefault(value, row, index) {
        if(value=="1"){
            return "默认网卡";
        }else{
            return "扩展网卡";
        }
    }
</script>
<?php
$this->end();
?>

