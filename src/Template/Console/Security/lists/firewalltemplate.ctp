<!--  安全  列表 -->
<?= $this->element('security/left',['active_action'=>'firewall_template']); ?>
<div class="wrap-nav-right">
    <div class="wrap-manage">
        <div class="top">
            <span class="title">防火墙模板列表</span>
            <div id="maindiv-alert"></div>

        </div>

        <div class="center clearfix">
            <button class="btn btn-addition" onclick="refreshTable();">
                <i class="icon-refresh"></i>&nbsp;&nbsp;刷新
            </button>
            <a class="btn btn-addition" id="newTemplate" href="javascript:;"><i class="icon-plus"></i>&nbsp;&nbsp;新建</a>
            <div class="pull-right">
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
            data-url="<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'security','firewallTemplate','lists']); ?>"
            data-unique-id="id">
            <thead>
                <tr>
                    <!-- <th data-checkbox="true"></th> -->
                    <th data-field="id" data-width="300" ="true">Id</th>
                    <th data-field="template_name" data-formatter=formatter_name>防火墙模板名称</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
</div>

<!-- 右键弹框 -->
<div class="context-menu" id="context-menu">
    <ul>
        <li id="modify"><a href="javascript:;"><i class="icon-pencil"></i> 修改</a></li>
        <li><a id="dele" href="javascript:;"><i class="icon-trash"></i> 删除</a></li>
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
            <h5 class="modal-title" id="modify-title">修改</h5>
        </div>
        <form id="modal-modify-form" action="" method="post">
            <div class="modal-body">
                <p class="name">
                    <span>名称</span><input id="modal-modify-name" name="name"
                    type="text">
                </p>
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
          <i class="icon-question-sign icon-color-gray text-primary"></i>&nbsp;&nbsp;<span id="modal-info"></span><span class=" text-primary" id="modal-dele-name"></span>？
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
$('.wrap-nav .title').on('click',
function() {
    $(this).children("i").toggleClass("icon-angle-down");
    $(this).children("i").toggleClass("icon-angle-up");
    $(this).next("ul").slideToggle(300)
});
$(".wrap-nav .total li").on('click',
function() {
    $(this).parent().children().removeClass('active');
    $(this).addClass('active')
});
$("#btnStart").on('click',
function() {
    var id = getRowsID('name');
    if (id != "") {
        showModal('提示', 'icon-question-sign', '确认要启动机器', id, 'ajaxFun(getRowsID(),\'desktop_start\')')
    } else {
        showModal('提示', 'icon-exclamation-sign', '请选中一台主机', '', '', 0)
    }
});
$("#btnStop").on('click',
function() {
    var id = getRowsID('name');
    if (id != "") {
        showModal('提示', 'icon-question-sign', '确认要停止机器', id, 'ajaxFun(getRowsID(),\'desktop_stop\')')
    } else {
        showModal('提示', 'icon-exclamation-sign', '请选中一台主机', '', '', 0)
    }
});

$("table input").on('click',
function() {
    if ($("tbody input:checked").length >= 1) {
        $(".center .btn-shutdown").attr('disabled', false)
    } else {
        $(".center .btn-shutdown").attr('disabled', true)
    }
});
$('#table').contextMenu('context-menu', {
    bindings: {
        'modify': function(event) {
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
            $('#modal-modify-name').val(row.template_name);
            // $('#modal-modify-description').val(row.description);
            $('#modal-modify-id').val(row.id);
            $('#modal-modify').modal("show");
            $('#sumbiter').one('click',
            function() {
                $.ajax({
                    url: '<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'security','FirewallTemplate','edit']); ?>',
                    data: $('#modal-modify-form').serialize(),
                    method: 'post',
                    dataType: 'json',
                    success: function(e) {
                        $('#modal-modify').modal("hide");
                        if (e.code == '0000') {
                            tentionHide(e.msg,0);
                        } else {
                            tentionHide(e.msg,1);
                        }
                        refreshTable();
                    }
                });
            })
        },
        'dele': function(event) {
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
            $('#modal-info').html('确认要删除防火墙模板');
            $('#modal-dele-name').html(row.template_name);
            $('#modal-dele-id').val(row.id);
            $('#modal-dele').modal("show");
            $('#sumbiter-dele').attr('id', 'sumbiter-dele');
            $('#sumbiter-dele').one('click',
            function() {
                $.ajax({
                    url: '<?= $this->Url->build(['controller'=>'Ajax','action'=>'security','FirewallTemplate','del']); ?>',
                    data: $('#modal-dele-form').serialize(),
                    method: 'post',
                    dataType: 'json',
                    success: function(e) {
                        e= $.parseJSON(e);
                        if (e.Code == '0') {
                            $('#table').bootstrapTable('removeByUniqueId', $(event).data("uniqueid"));
                            $('#modal-dele').modal("hide")
                        } else {
                            alert(e.Message)
                        }
                    }
                });
                return false
            })
        },
    }
});
//搜索绑定
$("#txtsearch").on('keyup',
function() {
    if (timer != null) {
        clearTimeout(timer);
    }
    var timer = setTimeout(function() {
        var search = $("#txtsearch").val();
        $('#table').bootstrapTable('refresh', {
            url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'security', 'firewallTemplate', 'lists']); ?>",
            query: {
                search: search
            }
        });
    },
    1000);
});
$("#btnreboot").on('click', function() {
     var names = getRowsID('name');
    if (names != "") {
        showModal('提示', 'icon-question-sign', '确认要重启云桌面', names, 'ajaxFun(getRowsID(),\'desktop_reboot\')')
    } else {
        showModal('提示', 'icon-exclamation-sign', '请选中一台云桌面', '', '', 0)
    }
});
$("#newTemplate").on('click',function(){
    $("#modal-modify-name").val("");
    $("#modal-modify").modal("show");
    $("#modify-title").html("新增");
    $("#sumbiter").unbind('click');
    $('#sumbiter').on('click',
    function() {
        $.ajax({
            url: "<?= $this->Url->build(['controller'=>'ajax','action'=>'security','FirewallTemplate','add']); ?>",
            data: $('#modal-modify-form').serialize(),
            method: 'post',
            dataType: 'json',
            success: function(e) {
                $('#modal-modify').modal("hide");
                if (e.code == '0') {
                    tentionHide(e.msg,0);
                } else {
                    tentionHide(e.msg,1);
                }
                refreshTable();
            }
        });
    })
});
/**---------方法------------*/
//返回用户名对应的URL
function formatter_name(value, row, index){
    var url="/console/security/lists/firewallpolicy?templateId="+row.id;
    return '<a href="'+url+'">'+value+'</a>';
}
function queryParams() {
    var params = {};
    $("input[name='search']").each(function() {
        params[$(this).attr('name')] = $(this).val()
    });
    params['order'] = 'asc';
    params['limit'] = '10';
    params['offset'] = '0';
    return params
}
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
        $("#btnMk").remove()
    }
    $('#modal').modal("show")
}

function getRowsID(type) {
    var idlist = '';
    $("input[name='btSelectItem']:checkbox").each(function() {
        if ($(this)[0].checked == true) {
            var id = $(this).parent().parent().attr('data-uniqueid');
            var row = $('#table').bootstrapTable('getRowByUniqueId', id);
            if (row.status != '') {
                if (type == 'name') {
                    idlist += row.name + ','
                } else if (type == "id") {
                    idlist += row.id + ','
                } else {
                    idlist += row.code + ','
                }
            }
        }
    });
    return idlist
}
function ajaxFun(id, method) {
    heartbeat(0);
    $('#modal').modal("hide");
    if (method == "desktop_start") {
        tentionHide('启动云桌面', 0);
        state = 0
    } else if (method == "desktop_stop") {
        tentionHide('停止云桌面', 1);
        state = 1
    } else if (method == "desktop_reboot") {
        tentionHide('重启云桌面', 2);
        state = 2
    }
    $.ajax({
        type: "post",
        url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','desktop','stopDesktop']); ?>",
        async: true,
        timeout: 9999999,
        data: {
            status: state,
            type: method,
            ids: id
        },
        success: function(e) {
            e = $.parseJSON(e);
            if (e.code == '0000') {
                $('#table').bootstrapTable('removeByUniqueId', $(event).data("uniqueid"));
                $('#modal-dele').modal("hide")
            } else {
                alert(e.msg)
            }
            refreshTable()
        }
    })
}
function refreshTable() {
    $('#table').bootstrapTable('refresh', {
        url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'security','FirewallTemplate','lists']); ?>"
    })
}
function tentionHide(content, state) {
    $("#maindiv-alert").empty();
    var html = "";
    if (state == 0) {
        html += '<div class="point-host-startup "><i></i>' + content + '</div>';
        $("#maindiv-alert").append(html);
        $(".point-host-startup ").slideUp(3000)
    } else {
        html += '<div class="point-host-startup point-host-startdown"><i></i>' + content + '</div>';
        $("#maindiv-alert").append(html);
        $(".point-host-startdown").slideUp(3000)
    }
}
function local(id, class_code, agent_name) {
    if (agent_name) {
        $('#agent_t').html('请选择');
        $('#agent').html(agent_name);
        $('#agent').attr('val', class_code);
        $('#table').bootstrapTable('refresh', {
            url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','desktop','lists']); ?>",
            query: {
                class_code: class_code
            }
        });
        var jsondata = <?php echo json_encode($agent); ?>;
        console.log(jsondata);
        var data = '';
        $.each(jsondata, function(i, n) {
            if (n.parentid == id) {
                data += '<li><a href="#" onclick="local_two(\'' + n.class_code + '\',\'' + n.agent_name + '\')">' + n.agent_name + '</a></li>'
            }
        });
        $('#agent_two').html(data);
    }
}
function local_two(class_code, agent_name) {
    $('#agent_t').html(agent_name);
    $('#agent_t').attr('val', class_code);
    $('#table').bootstrapTable('refresh', {
        url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','desktop','lists']); ?>",
        query: {
            class_code: class_code
        }
    })
}
</script>
<?php
$this->end();
?>
