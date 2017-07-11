<!--  安全  列表 -->
<?= $this->element('security/left',['active_action'=>'firewall']); ?>
<div class="wrap-nav-right">
    <div class="wrap-manage">
        <div class="top">
            <span class="title">防火墙列表</span>

            <div id="maindiv-alert"></div>
        </div>
        <div class="center clearfix">
            <button class="btn btn-addition" onclick="refreshTable();">
                <i class="icon-refresh"></i>&nbsp;&nbsp;刷新
            </button>
            <a class="btn btn-addition" href="<?= $this->Url->build(['controller' => 'Security', 'action' => 'add', 'firewall']) ?>"><i class="icon-plus"></i>&nbsp;&nbsp;新建</a>
            <button class="btn btn-default" id="btnEdit" disabled>
              <i class="icon-pencil"></i>&nbsp;&nbsp;编辑策略
            </button> 
            <button class="btn btn-default" id="btnDel" disabled>
              <i class="icon-remove "></i>&nbsp;&nbsp;删除
            </button>   
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
                <input type="hidden" id="txtdeparmetId" value="<?= $_default["id"] ?>" />
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
        data-pagination="true" ="false"
        data-side-pagination="server"
        data-locale="zh-CN"
        data-click-to-select="true"
        data-url="<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'security','firewall','lists','?'=>['department_id'=>$_default['id']]]); ?>"
        data-unique-id="id">
        <thead>
            <tr>
                <th data-checkbox="true"></th>
                <th data-field="id" ="true">Id</th>
                <th data-field="code" ="true">防火墙Code</th>
                <th data-field="name" data-formatter=formatter_name>防火墙名称</th>
                <th data-field="status" data-formatter=formatter_state>火墙状态</th>
                <th data-field="location_name">部署区位</th>
                <th data-field="vpcName" data-formatter=formatter_vpc>所属VPC名</th>
                <th data-field="vpc" data-formatter=formatter_vpc>所属VPC CODE</th>
                <th data-field="firewallecsCode" data-formatter=formatter_vpc>实例CODE</th>
                <th data-field="firewallecsCode" data-formatter=formatter_code>登录实例</th>
                <th data-field="firewallecsStatus" data-formatter=formatter_state>实例状态</th>
                <th data-field="I_Ip">实例私有IP</th>
                <th data-field="firewallecsEIP" data-formatter=formatter_vpc>实例公网IP</th>
                <!--                        <th data-field="modify_time" data-formatter=timestrap2date>备份于</th> -->
                <th data-field="create_time" data-formatter=timestrap2date>创建时间</th>
            </tr>
        </thead>
    </table>
</div>
</div>

</div>
</div>

<!-- 右键弹框 -->
<div class="context-menu" id="context-menu">
    <ul>
        <!-- <li><a id="close" href="javascript:;"><i class="icon-off"></i> 关机</a></li> -->
        <!-- <li><a href="javascript:;"><i class="icon-refresh"></i> 重启</a></li> -->
        <li id="modify"><a href="javascript:;"><i class="icon-pencil"></i> 修改</a></li>
        <!-- <li><a href="javascript:;"><i class="icon-tag"></i> 标签</a></li>
        <li><a href="javascript:;" class="context-primary"> <i class="icon-inbox"></i> 硬盘</a>
        <ul class="context-secondary">
            <li><a href="javascript:;"><i class="icon-plus"></i> 加载</a></li>
            <li><a href="javascript:;"><i class="icon-minus"></i> 卸载</a></li>
        </ul>
        </li>
        <li><a href="javascript:;"><i class="icon-key"></i> SSH密钥</a></li>
        <li><a href="javascript:;"><i class="icon-exchange"></i> 网络</a></li>
        <li><a href="javascript:;"><i class="icon-globe"></i> 公网IP</a></li>
        <li><a href="javascript:;"><i class="icon-tags"></i> 内网域名别名</a></li>
        <li><a href="javascript:;"><i class="icon-paste"></i> 制作成新映象</a></li>
        <li><a href="javascript:;"><i class="icon-camera"></i> 创建备份</a></li>
        <li><a href="javascript:;" class="context-primary"> <i class="icon-bell"></i>
            告警策略
        </a>
        <ul class="context-secondary">
            <li><a href="javascript:;"><i class="icon-plus"></i> 绑定</a></li>
            <li><a href="javascript:;"><i class="icon-minus"></i> 解绑</a></li>
        </ul></li>
        <li><a href="javascript:;"><i class="icon-th-list"></i> 更多操作 </a></li>-->
        <li><a id="dele" href="javascript:;"><i class="icon-trash"></i> 删除</a></li>
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
                        <label>描述:</label>
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

<div id="maindiv"></div>
<!-- 删除 -->
<div class="modal fade" id="modal-dele" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h5 class="modal-title">提示</h5>
    </div>
    <form id="modal-dele-form" action="" method="post">
        <div class="modal-body">
          <i class="icon-question-sign icon-color-gray text-primary"></i>&nbsp;&nbsp;<span id="modal-info"></span><span class=" text-primary" id="modal-dele-name"></span><span id="word1">？</span></br>
          <i class="icon-exclamation-sign icon-color-gray text-primary"></i>&nbsp;&nbsp;<span id="word2">删除防火墙,会同时删除防火墙绑定的规则！</span>
          <input type="hidden" value="" id="modal-dele-id" name="ids">
          <input type="hidden" value="" id="modal-status" name="status">
      </div>
      <div class="modal-footer">
          <button type="button" id="sumbiter-dele" class="btn btn-primary">确认</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
      </div>
  </form>
</div>
</div>
</div>
<div style="display: none;">
    <iframe id="lauchFrame" name="lauchFrame" src=""></iframe>
</div>
<?php
$this->start('script_last');
?>
<script type="text/javascript">
//input 存在一个被选中状态
$("#table").on('all.bs.table', function(e, row, $element) {
	
	if ($("tbody input:checked").length > 1) {
		$("#btnEdit").attr('disabled', true);
		$("#btnDel").attr('disabled', false);
	} else if($("tbody input:checked").length == 1){
		$("#btnEdit").attr('disabled', false);
		$("#btnDel").attr('disabled', false);
	} else {
		$(".center .btn-default").attr('disabled', true);
	}
})

$('#btnEdit').on('click', function() {
	var row = $('#table').bootstrapTable('getSelections');
	var url = '/console/security/lists/firewallpolicy?id='+row[0].id
	self.location=url;
})

$('#btnDel').on('click', function() {
	var row = $('#table').bootstrapTable('getSelections');
	var ids = '';
	var haveFalse = 0;
	 $.each(row,function(e, i) {
		 if(ids.length > 0) {
			 ids += ',';
		 }
		 ids += i.id;
		 if (i.delete_info != 0){
			 $('.icon-question-sign').hide();
             $('#modal-info').hide();
             $('#modal-dele-name').hide();
             $('#word1').hide();
    	     switch (i.delete_info) {
    	     case '1':
                 $('#word2').html(i.vpc + ' 下有ECS在使用EIP，不能删除防火墙!');
                 break;
    	     case '2':
    	    	 $('#word2').html(i.vpc + ' 下存在VPX，不能删除防火墙!');
    	    	 break;
    	     case '3':
                 $('#word2').html(i.vpc + ' 下存在DDC，不能删除防火墙!');
                 break;
    	     case '4':
                 $('#word2').html(i.vpc + ' 下存在AD，不能删除防火墙!');
                 break;
    	     case '5':
                 $('#word2').html(i.vpc + ' 下存在WI，不能删除防火墙!');
                 break;
    	     }
    	     $('#sumbiter-dele').hide().next().html("关闭");
             $('#modal-dele').modal("show");
             haveFalse = 1;
             return false;
		 }
	 })
	if(haveFalse == 0) {
	 $('#modal-info').show();
     $('.icon-question-sign').show();
     $('#word1').show();
     $('#modal-info').html('确认要删除防火墙');
     $('#modal-dele-name').html(row.name);
     $('#modal-dele-id').val(row.code);
     $('#word2').html( '删除防火墙,会同时删除防火墙绑定的规则！');
     $('#sumbiter-dele').show();
     $('#modal-dele').modal("show");
     $('#sumbiter-dele').attr('id', 'sumbiter-dele');
     $('#sumbiter-dele').one('click',
     function () {
     	//ajax提交页面
		$.ajax({
        	url: '<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'security','firewall','delFirewallAll']); ?>',
            data: {
            	ids: ids,
        	},
            method: 'post',
            dataType: 'json',
            success: function (e) {
            	$('#modal-dele').modal("hide");
            	if (e.code == "0000") {
                	refreshTable();
                } else {
                	alert(e.msg);
                }
             }
         });
    });
	}
})


    function formatter_code(value, row, index) {
        var html = "";
        var code = row.firewallecsCode;
        var fusionType = row.fusionType;
        var os = row.firewallecsPFM;
        if (fusionType == "vmware") {
            var url = "/console/network/webConsole/" + code;
            if (row.firewallecsStatus == "运行中") {
                html += "<a href=" + url + " target='_blank'><i class='icon-laptop'></i></a>";
                    return html;
            } else {
                return "-";
            }
        }
        return "-";
    }

    function formatter_vpc(value,row,index){
        if(value!=null&&value!=""){
            return value;
        }else{
            return "-";
        }
    }

    function queryParams() {
        var params = {};
        $("input[name='search']").each(function() {
            params[$(this).attr('name')] = $(this).val();
        });
        params['order'] = 'asc';
        params['limit'] = '10';
        params['offset'] = '0';
        return params;
    }

//返回用户名对应的URL
function formatter_name(value, row, index){
    var url="/console/security/lists/firewallpolicy?id="+row.id;
    return '<a href="'+url+'">'+value+'</a>';
}

//动态创建modal
function showModal(title, icon, content, content1, method, type) {
    $("#maindiv").empty();
    html = "";
    html += '<div class="modal fade" id="modal" tabindex="-1" role="dialog">';
    html += '<div class="modal-dialog" role="document">';
    html += '<div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
    html += '<h5 class="modal-title">' + title + '</h5>';
    html += '</div><div class="modal-body"><i class="'+icon+' icon-color-gray text-primary"></i>&nbsp;&nbsp;' + content + '<span class="text-primary">' + content1 + '</span></div>';
    html += '<div class="modal-footer"><button id="btnMk" onclick="' + method + '" type="button" class="btn btn-primary">确认</button><button type="button" class="btn btn-danger" data-dismiss="modal">取消</button></div></div></div></div>';
    $("#maindiv").append(html);
    if (type == 0) {
        $("#btnMk").remove();
    }
    $('#modal').modal("show");
}

$('#table').contextMenu('context-menu', {

    bindings: {
        'modify': function(event) {
            //获取数据
            index=$(event).attr('data-index');
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
            //填充数据
            //TODO 根据bootstrap方法
            $('#modal-modify-name').val(row.name);
            $('#modal-modify-description').val(row.description);
            $('#modal-modify-id').val(row.id);
            $('#modal-modify').modal("show");

            //填充数据
            $('#sumbiter').one('click',
                function() {
                    $.ajax({
                        url: '<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','desktop','edit']); ?>',
                        data: $('#modal-modify-form').serialize(),
                        method: 'post',
                        dataType: 'json',
                        success: function(e) {
                        //操作成功
                        if (e.code == '0000') {
                            $('#table').bootstrapTable('updateRow', {
                                index: index,
                                row: e.data
                            });
                            $('#modal-modify').modal("hide");
                            // tentionHide('修改成功',0);
                        }else{
                            // tentionHide('修改失败',1);
                        }
                    }
                });
                });
        },



        'dele': function (event) {
            //获取数据
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
//            alert(row.delete_info);

//          不满足删除条件
            if (row.delete_info == '1') {
                $('.icon-question-sign').hide();
                $('#modal-info').hide();
                $('#modal-dele-name').hide();
                $('#word1').hide();
                $('#word2').html(row.vpc + ' 下有ECS在使用EIP，不能删除防火墙!');
                $('#sumbiter-dele').hide().next().html("关闭");
                $('#modal-dele').modal("show");
            } else if (row.delete_info == '2') {
                $('.icon-question-sign').hide();
                $('#modal-info').hide();
                $('#modal-dele-name').hide();
                $('#word1').hide();
                $('#word2').html(row.vpc + ' 下存在VPX，不能删除防火墙!');
                $('#sumbiter-dele').hide().next().html("关闭");
                $('#modal-dele').modal("show");
            } else if (row.delete_info == '3') {
                $('.icon-question-sign').hide();
                $('#modal-info').hide();
                $('#modal-dele-name').hide();
                $('#word1').hide();
                $('#word2').html(row.vpc + ' 下存在DDC，不能删除防火墙!');
                $('#sumbiter-dele').hide().next().html("关闭");
                $('#modal-dele').modal("show");
            } else if (row.delete_info == '4') {
                $('.icon-question-sign').hide();
                $('#modal-info').hide();
                $('#modal-dele-name').hide();
                $('#word1').hide();
                $('#word2').html(row.vpc + ' 下存在AD，不能删除防火墙!');
                $('#sumbiter-dele').hide().next().html("关闭");
                $('#modal-dele').modal("show");
            } else if (row.delete_info == '5') {
                $('.icon-question-sign').hide();
                $('#modal-info').hide();
                $('#modal-dele-name').hide();
                $('#word1').hide();
                $('#word2').html(row.vpc + ' 下存在WI，不能删除防火墙!');
                $('#sumbiter-dele').hide().next().html("关闭");
                $('#modal-dele').modal("show");
            } else if (row.delete_info == '0') {// 满足删除条件
                $('#modal-info').show();
                $('.icon-question-sign').show();
                $('#word1').show();
                $('#modal-info').html('确认要删除防火墙');
                $('#modal-dele-name').html(row.name);
                $('#modal-dele-id').val(row.code);
                $('#word2').html( '删除防火墙,会同时删除防火墙绑定的规则！');
                $('#sumbiter-dele').show();
                $('#modal-dele').modal("show");
                $('#sumbiter-dele').attr('id', 'sumbiter-dele');
                $('#sumbiter-dele').one('click',
                        function () {
                            //ajax提交页面

                            $.ajax({
                                url: '<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'security','firewall','ajaxFun']); ?>',
                                data: {
                                    method: 'firewall_del',
                                    basicId: row.id,
                                    firewallCode: row.code
                                },
                                method: 'post',
                                dataType: 'json',
                                success: function (e) {
                                    $('#modal-dele').modal("hide");
                                    if (e.code == "0000") {
                                        // tentionHide('删除成功',0);
                                        refreshTable();
                                    } else {
                                        alert(e.msg);
                                        // tentionHide('删除失败',1);
                                    }

                                }
                            });
                        });
            }
        },



        'excp':function(event){
        	var uniqueId = $(event).attr('data-uniqueid');
        	var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);

        	var department_id = row.department_id;
        	window.location.href = "/console/excp/lists/excp/firewall/"+department_id+'/all/0/0/'+row.id;
        },
        //正常
        'normal':function(event){
        	var uniqueId = $(event).attr('data-uniqueid');
        	var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
        	var department_id = row.department_id;
        	window.location.href = "/console/excp/lists/normal/firewall/"+department_id+'/all/0/0/'+row.id;
        }
    }

});

function notifyCallBack(value) {
    //console.log(value);
    var search = $("#txtsearch").val();
    var department_id = $("#txtdeparmetId").val();
    var class_code = $("#agent").attr('val');
    var class_code2 = $("#agent_t").attr('val');
    var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'security','firewall','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+department_id;
    if (value.MsgType == "success" || value.MsgType == "error") {
        if (value.Data.method == "router_del"||value.Data.method=="firewall"||value.Data.method=="firewall_del"||value.Data.method=="firewall_add") {
        setTimeout(function() {
            $('#table').bootstrapTable('refresh', {
                url: url,
                    silent: true
                });
            }, 3000);
        }
    }
}

function refreshTable() {
    var class_code = $("#agent").attr('val');
    var class_code2 = $("#agent_t").attr('val');
    var search = $("#txtsearch").val();
    $('#table').bootstrapTable('refresh', {
        url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'security','firewall','lists']); ?>",
        query: {
                class_code2: class_code2,
                class_code: class_code,
                search: search,
                department_id:$("#txtdeparmetId").val()
            }
    });
}

//搜索绑定
$("#txtsearch").on('keyup',
function() {
    if (timer != null) {
        clearTimeout(timer);
    }
    var timer = setTimeout(function() {
        var class_code = $("#agent").attr('val');
        var class_code2 = $("#agent_t").attr('val');
        var search = $("#txtsearch").val();
        $('#table').bootstrapTable('refresh', {
            url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'security', 'firewall', 'lists']); ?>",
            query: {
                class_code2: class_code2,
                class_code: class_code,
                search: search,
                department_id:$("#txtdeparmetId").val()
            }
        });
    },
    1000);
});

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
function local(id,class_code,agent_name) {
    if (agent_name) {
        $('#agent_t').html('全部');
        $('#agent').html(agent_name);
        $('#agent').attr('val', class_code);
        var search= $("#txtsearch").val();
        $('#table').bootstrapTable('refresh', {
            url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'security','firewall','lists']); ?>",
            query: {class_code: class_code,search: search,department_id:$("#txtdeparmetId").val()}
        });
        var jsondata = <?php echo json_encode($agent); ?>;
        if(id!=0){
            var data='<li><a href="javascript:;" onclick="local_two(\'\',\'全部\',\'' + class_code + '\')">全部</a></li>';
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
    var search= $("#txtsearch").val();
    $('#agent_t').html(agent_name);
    $('#agent_t').attr('val',class_code2);
    $('#table').bootstrapTable('refresh', {
        url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'security','firewall','lists']); ?>",
        query: {class_code2: class_code2,class_code:class_code,search: search,department_id:$("#txtdeparmetId").val()}
    });
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
        url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'security', 'firewall', 'lists']); ?>",
        query: {
            class_code2: class_code2,
            class_code: class_code,
            search: search,
            department_id:$("#txtdeparmetId").val()
        }
    });
    var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'security','firewall','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val();
    $('#table').attr('data-url', url);
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
                return '<span id="imgState' + row.H_ID + '" ></span>-';
            }
        }
    }

</script>
<?php
$this->end();
?>
