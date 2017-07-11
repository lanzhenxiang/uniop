<!-- 硬盘 -->
<?= $this->element('network/lists/left',['active_action'=>'disks']); ?>
<div class="wrap-nav-right">
    <div class="wrap-manage">
        <div class="top">
            <span class="title">硬盘列表</span>
        <div id="maindiv-alert"></div>
    </div>
        <div class="center clearfix">
            <button class="btn btn-addition" id="btn-refresh">
                <i class="icon-refresh"></i>&nbsp;&nbsp;刷新
            </button>
            <button class="btn btn-default" id="batchdelete" disabled = "disabled">
                <i class="icon-remove"></i>&nbsp;&nbsp;删除
            </button>
        
            <div role="presentation" class="dropdown">
           
            </div>
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
            <ul class="dropdown-menu" id="agent_two">
            </ul>
        </div>
                <span class="search"><input type="text" id="txtsearch" name="search" placeholder="搜索">
                    <i class="icon-search"></i>
                </span>
                <!--<button class="btn btn-addition"><i class="icon-tag"></i>&nbsp;&nbsp;标签</button>-->
            </div>
        </div>
        <div class="bot ">
            <table id="table" data-toggle="table" data-pagination="true"
                   data-side-pagination="server" 
                   data-locale="zh-CN" data-click-to-select="true" data-url="<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','disks','lists','?'=>['department_id'=>$_default["id"]]]); ?>" data-pagination="true"   data-unique-id="id">
            <thead>
                <tr>
                    <th data-checkbox="true"></th>
                    <th data-field="id" >Id</th>
                    <th data-field="code" >硬盘Code</th>
                    <th data-field="name" id="name">硬盘名称</th>
                 <!--   <th data-field="" >资源/盘符</th>-->
                    <th data-field="capacity" data-formatter=getCapacity>容量（GB）</th>
                    <th data-field="location_name">部署区位</th>
                    <th data-field="status" data-formatter="formatter_state">状态</th>
                    <th data-field="H_Name" data-formatter=formatter_hosts>挂载主机</th>
                    <th data-field="H_Code" data-formatter=formatter_ip>主机Code</th>
                    <!--                    <th>备份于</th>-->
                    <th data-field="create_time" data-formatter=timestrap2date>创建时间</th>
                </tr>
                </thead>
            </table>

        </div>

    </div>
</div>

<div class="context-menu" id="context-menu">
    <ul>
        <?php if (in_array('ccf_disk_change', $this->Session->read('Auth.User.popedomname'))) { ?>
        <li id="modify"><a href="#"><i class="icon-pencil"></i> 修改</a></li>
        <?php } ?>
        <?php if (in_array('ccf_disk_delete', $this->Session->read('Auth.User.popedomname'))) { ?>
        <li id="delete"><a  href="javascript:;"><i class="icon-trash"></i> 删除</a></li>
        <?php } ?>
        <?php if (in_array('ccf_disk_extend', $this->Session->read('Auth.User.popedomname'))) { ?>
        <li id="expansion"><a href="javascript:void(0);"> <i class="icon-inbox"></i> 扩容</a></li>
        <?php } ?>
        <?php if (in_array('ccm_ps_interface_logs', $this->Session->read('Auth.User.popedomname'))) { ?>
        <li id="excp"><a  href="javascript:;"><i class="icon-book"></i> 异常日志</a></li>
        <?php } ?>
         <?php if (in_array('ccm_ps_interface_logs', $this->Session->read('Auth.User.popedomname'))) { ?>
        <li id="normal"><a  href="javascript:;"><i class="icon-book"></i> 正常日志</a></li>
        <?php } ?>
    </ul>
</div>

<!-- 删除 -->
<div class="modal fade" id="modal-delete" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">提示</h5>
            </div>
            <div class="modal-body">
                <i class="icon-question-sign icon-color-gray text-primary"></i>&nbsp;&nbsp;确认要删除硬盘<span class="text-primary" id="sure"></span>？
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="yes">确认</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>

<!-- 修改 -->
<div class="modal fade" id="modal-modify" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">修改</h5>
            </div>
            <form id="modal-modify-form" action="" method="post">
                <div class="modal-body">
                    <p class="name">
                        <span >硬盘名称</span>
                        <input id="modal-modify-name" name="name" type="text">
                        <span class="text-danger" id="modal-modify-warning" style="float:none;width:auto"></span>
                    </p>
                  <!--  <p class="name"><span>描述</span><textarea id="modal-modify-description" name="description" ></textarea>-->
                        <input id="modal-modify-id" name="id" type="hidden" />
                    </p>
                </div>
                <div class="modal-footer">
                    <button id="sumbiter" type="button" class="btn btn-primary" disabled="true">确认</button>
                    <button id="reseter" type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                </div>
            </form>

        </div>
    </div>
</div>
<!-- 批量删除 -->
<div class="modal fade" id="modal-deleteAll" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">提示</h5>
            </div>
            <div class="modal-body">
                <i class="icon-exclamation-sign icon-color-gray text-primary"></i>&nbsp;&nbsp;确认要删除这些硬盘么？
                <input type="hidden" id="ids">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="yesAll">确认</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="disk-manage" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="slide-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title">主机硬盘管理</h5>
            </div>
            <div class="modal-body">
                    <div class="modal-form-group">
                        <label>容量大小:</label>
                        <div class="slider-area">
                            <div id="slider"></div>
                        </div>
                        <div class="amount pull-left">
                            <input type="text" id="amount" placeholder="10" disabled="disabled"> GB
                        </div>
                    </div>
                    <div class="modal-form-group">
                        <label></label>
                        <div>
                            <h6 class="warm">10GB-1000GB</h6>
                            <input type="hidden" name="volumeCode" id="volumeCode">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button onclick="capacity()" type="button" id="voadd" class="btn btn-primary">确认</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                    </div>
            </div>
        </div>
    </div>
</div>

<div id="maindiv"></div>

<?php
$this->start('script_last');
?>
<script type ="text/javascript">
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
            url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','disks','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val()
        });
    },
    1000);

});

function local(id, class_code, agent_name) {
    if (agent_name) {
        var search = $("#txtsearch").val();
        $('#agent_t').html('全部');
        $('#agent').html(agent_name);
        $('#agent').attr('val', class_code);
        $('#table').bootstrapTable('refresh', {
            url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','disks','lists']); ?>",
            query: {
                class_code: class_code,
                search: search,
                department_id:$("#txtdeparmetId").val()
            }
        });
        var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','disks','lists']); ?>?search=" + search + '&class_code=' + class_code +'&department_id='+$("#txtdeparmetId").val();
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
        url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','disks','lists']); ?>",
        query: {
            class_code2: class_code2,
            class_code: class_code,
            search: search,
            department_id:$("#txtdeparmetId").val()
        }
    });

    var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','disks','lists']); ?>?class_code=" + class_code + "&search=" + search;
    $('#table').attr('data-url', url);
}

//右键操作--------------------------------------------------
$('#table').not('tr:first').contextMenu('context-menu', {
    bindings: {
        //修改
        'modify': function(event) {
            $('#modal-modify').modal("show");
            $('#modal-modify-warning').text("");
            index = $(event).attr('data-index');
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
            $('#modal-modify-name').val(row.name);
            $('#modal-modify-id').val(row.id);

            var name = $('#modal-modify-name').val();
            $('#modal-modify-name').on("input",
            function() {
                var name_ = $('#modal-modify-name').val();
                if (name != name_) {
                    $('#sumbiter').prop('disabled', false);
                } else {
                    $('#sumbiter').prop('disabled', true);
                }
            });
            $('#sumbiter').on('click',function() {
                var name = $('#modal-modify-name').val();
                if(name==""){
                    $('#modal-modify-warning').text('请填写名称');
                }else{
                    $.ajax({
                        method: 'post',
                        url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network']); ?>/<?php echo 'Disks' ?>/<?php echo 'updateDisks' ?>",
                        data: $("#modal-modify-form").serialize(),
                        success: function(data) {
                            data = $.parseJSON(data);
                            //console.debug(data);
                            if (data.code == '0000') {
                                $('#table').bootstrapTable('updateRow', {
                                    index: index,
                                    row: data.data
                                });
                                $('#modal-modify').modal("hide");
                                tentionHide('修改成功', 0);
                            } else {
                                tentionHide('修改失败', 1);
                            }
                        }
                    });
                }
            })
        },
        'bound': function() {
            $('#modal-bound').modal("show");
            console.log(event.id);
        },
        'built': function() {
            $('#modal-built').modal("show");
            console.log(event.id);
        },
        'expansion': function(event) {
            $('#disk-manage').modal("show");

            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
            var capacity = parseInt(row.capacity);
            $("#amount").val(capacity);
            $(".modal-form-group .warm").html('请输入范围' + capacity + 'GB-1000GB,并且是10的倍数');
            $(".modal-body").removeClass('red');
            $("#volumeCode").val(row.code);
            $("#amount").keyup(function() {
                var a = /(^[1-9]([0-9]*)$|^[0-9]$)/;
                var amountVal = $(this).val();
                $(this).blur(function() {
                    if (a.test(amountVal) == false || amountVal < capacity || amountVal > 1000) {
                        $("#amount").val(capacity);
                        $(".modal-body").addClass('red')

                    } else if(parseInt(amountVal)% 10 != 0){
                        $("#amount").val(capacity);
                        $(".modal-body").addClass('red')
                    }
                    else {
                        $("#amount").val(amountVal);
                        $(".modal-body").removeClass('red')
                    }
                    $("#slider").slider({
                        value: $("#amount").val()
                    });
                    $("#voadd").prop('disabled', false);
                })

                if (a.test(amountVal) == false || amountVal < capacity || amountVal > 1000) {
                    $("#voadd").prop('disabled', true);

                }else if(parseInt(amountVal)% 10 != 0){
                    $("#voadd").prop('disabled', true);
                }else {
                    $("#voadd").prop('disabled', false);
                }
                $("#slider").slider({
                    value: $("#amount").val()
                })
            });

            $("#slider").slider({
                value: $("#amount").val(),
                min: 10,
                max: 1000,
                step: 1,
                orientation: "horizontal",
                range: "min",
                animate: true,
                slide: function(event, ui) {
                    var val=ui.value-ui.value%10;
                    $("#amount").val(val);
//                    $("#amount").val(ui.value);
                    if ($("#amount").val() < capacity) {
                        $("#voadd").prop('disabled', true);
                    } else {
                        $("#voadd").prop('disabled', false);
                    }
                    $("#bandwidth").html(ui.value);
                }
            });
            $("#slide-content").on('click',
            function() {
                if ($("#amount").val() < capacity) {
                    $("#amount").val(capacity);
                    $("#slider").slider({
                        value: $("#amount").val()
                    })
                }
                $("#voadd").prop('disabled', false);
            })
        },
        //删除
        'delete': function(event) {
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
            $('#modal-delete').modal("show");
            $('#sure').html($(event).children("td:eq(2)").html());
            $('#yes').one('click',
            function() {
                var id = $('#id').val();
                $('#modal-delete').modal("hide");
                $.ajax({
                    method: 'post',
                    url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'disks', 'deleteDisks']); ?>",
                    data: {
                        volumeCode: row.code,
                        basicId: row.id,
                        host: row.H_Name
                    },
                    success: function(data) {
                        data = $.parseJSON(data);
                        if (data.Code == '0') {
                            $('#table').bootstrapTable('removeByUniqueId', $(event).data("uniqueid"));

//                            tentionHide('删除成功', 0);
                            layer.msg("删除成功");
                        } else {
                            layer.alert(data.Message);
//                            tentionHide(data.Message, 1);
                        }
                    }
                })
            });
        },
        //异常
        'excp':function(event){
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
            var department_id = row.department_id;
            window.location.href = "/console/excp/lists/excp/disks/"+department_id+'/all/0/0/'+row.id;
        },
        //正常
        'normal':function(event){
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
            var department_id = row.department_id;
            window.location.href = "/console/excp/lists/normal/disks/"+department_id+'/all/0/0/'+row.id;
        }
    }

});

function notifyCallBack(value) {
    //console.log(value);
    if (value.MsgType == "success" || value.MsgType == "error" || value.MsgType == "info") {
        if (value.Data.method == "volume_detach") {
            $('#modal-delete').modal("hide");
        }else if(value.Data.method == "volume_resize" || value.Data.method == "volume_del"){
            var search = $("#txtsearch").val();
            var class_code = $("#agent").attr('val');
            var class_code2 = $("#agent_t").attr('val');
            $('#table').bootstrapTable('refresh', {
                url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','disks','lists']); ?>",
                query: {
                    class_code2: class_code2,
                    class_code: class_code,
                    search: search,
                    department_id:$("#txtdeparmetId").val()
                }
            });
        }
    }
}

//动态创建modal
function showModal(title, icon, content, content1, method, type, info) {
    $("#maindiv").empty();
    html = "";
    html += '<div class="modal fade" id="modal-confirm" tabindex="-1" role="dialog">';
    html += '<div class="modal-dialog" role="document">';
    html += '<div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
    html += '<h5 class="modal-title">' + title + '</h5>';
    html += '</div><div class="modal-body"><i class="'+icon+' icon-color-gray text-primary"></i>&nbsp;&nbsp;' + content + '<span class="text-primary">' + content1 + '</span></div>';
    html += '<div class="modal-footer"><button id="btnMk" onclick="' + method + '" type="button" class="btn btn-primary">确认</button><button type="button" class="btn btn-danger" data-dismiss="modal">' + info + '</button></div></div></div></div>';
    $("#maindiv").append(html);
    if (type == 0) {
        $("#btnMk").remove();
    }
    $('#modal-confirm').modal("show");
}

//批量删除----------------------------------
$('#batchdelete').on('click', function() {
    var ids = new Array();
    var selected = $("input:checkbox[name='btSelectItem']:checked");
    var selected_all = selected.parent().parent();
    if (selected_all.length >= 1) {
        selected_all.each(function() {
            ids.push($(this).data("uniqueid"));
        });
        $('#modal-deleteAll').modal('show');
        $('#ids').val(ids);
    } else {
        showModal('提示', 'icon-exclamation-sign', '请选中一个硬盘', '', '', 0, '我知道了');
    }

});

$('#yesAll').on('click',
function(event) {
        var ids = $('#ids').val();
        $('#modal-deleteAll').modal("hide");
        $.ajax({
            method: 'post',
            url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'disks', 'deleteDisksAll']); ?>",
            data: {
                ids: ids
            },
            success: function(data) {
                data = $.parseJSON(data);
                if (data.Code == '0') {
                    $('#table').bootstrapTable('refresh', {
                        url: "<?php echo $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','disks','lists']); ?>"
                    });
//                    tentionHide(data.Message, 0);
                    layer.msg(data.Message);
                } else {
                    layer.alert(data.Message);
                    //                    tentionHide(data.Message, 1);
                }
            }
        })

});

//input 存在一个被选中状态
$("#table").on('all.bs.table', function(e, row, $element) {
	if ($("tbody input:checked").length >= 1) {
		$(".center .btn-default").attr('disabled', false);
	} else {
		$(".center .btn-default").attr('disabled', true);
	}
})
//------------------------------------------
$("#btn-refresh").on("click",function(){
    refreshTable()
});


function refreshTable() {
    //$('#table').bootstrapTable('showLoading');
    // console.log($("#agent").attr('val'));
    var search = $("#txtsearch").val();
    var class_code = $("#agent").attr('val');
    var class_code2 = $("#agent_t").attr('val');
    $('#table').bootstrapTable('refresh', {
        url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','disks','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val()
    });
}

function getCapacity(value, row, index) {
    var capacity = '-';
    if (value != null) {
        capacity = parseInt(value);
    }
    return capacity;
}

function formatter_hosts(value, row, index) {
    //console.log(value.attachhostid);
    if (value != null) {
        if (value == 0) {
            return "-";
        } else {
            return value;
        }
    } else {
        return "-";
    }
}

//返回ip
function formatter_ip(value, row, index) {
    if (value != null) {
        return value;
    } else {
        return "-";
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

function capacity() {
    var size = $('#amount').val();
    var volumeCode = $('#volumeCode').val();
    var search = $("#txtsearch").val();
    var department_id = $("#txtdeparmetId").val();
    var class_code = $("#agent").attr('val');
    var class_code2 = $("#agent_t").attr('val');
    var istrue = true;
    $.ajaxSettings.async = false;
    $.getJSON("/console/home/getUserLimit", function(data) {
        if (Number(size) > data.data.disks_cap_bugedt) {
            alert("配额不足 \r\n 单个磁盘容量配额：" + data.data.disks_cap_bugedt+'GB');
            istrue = false;
        }
    });
    $.ajaxSettings.async = true;
    if (!istrue) {
        //$("#voadd").attr('disabled', true);
        return false
    }

    $.ajax({
        method: 'post',
        url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network']); ?>/<?php echo 'Disks' ?>/<?php echo 'addvolume' ?>",
        data: {
            size: size,
            volumeCode: volumeCode
        },
        success: function(data) {
            data = $.parseJSON(data);
            if (data.code == '0000') {

                $('#table').bootstrapTable('refresh', {
                    url: "<?php echo $this->Url->build(['prefix '=>'console','controller'=>'ajax','action'=>'network','disks','lists']); ?>",
                    query: {
                        class_code2: class_code2,
                        class_code: class_code,
                        search: search,
                        department_id:$("#txtdeparmetId").val()
                    }
                });
                $('#disk-manage').modal("hide");
                tentionHide(data.msg, 0);
            } else {
                $('#disk-manage').modal("hide");
                tentionHide(data.msg, 1);
            }
        }
    })
}

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

function departmentlist(id,name){
    $("#txtdeparmetId").val(id);
    $("#deparmets").html(name);
    var search = $("#txtsearch").val();
    var department_id = id;
    var url;
    var class_code = $("#agent").attr('val');
    var class_code2 = $("#agent_t").attr('val');
    $("#table").bootstrapTable('refresh', {
        url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','disks','lists']); ?>",
        query: {
            class_code2: class_code2,
            class_code: class_code,
            search: search,
            department_id:$("#txtdeparmetId").val()
        }
    });
    var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','disks','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val();
    $('#table').attr('data-url', url);
}

</script>
<?php
$this->end();
?>
