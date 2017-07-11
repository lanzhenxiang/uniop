<?= $this->element('network/lists/left',['active_action'=>'vpx']); ?>
<div class="wrap-nav-right">
    <div class="wrap-manage">
        <div class="top">
            <span class="title"><a href="initCopy">VPX列表</a></span>

            <div id="maindiv-alert"></div>
        </div>

        <!--bnt box-->
        <div class="center clearfix">
           
            <div class="pull-right">
                <div class="dropdown">
                    VPC:
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
           <span class="search"><input type="text" id="txtsearch" name="search" placeholder="搜索">
            <input type="hidden" name="txtdeparmetId" id="txtdeparmetId" value="<?=$_default['id']?>">
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
                   data-url="<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','init','lists','?'=>['type'=>'vpx','department_id'=>$_default['id']]]); ?>"
                   data-unique-id="id">
                <thead>
                <tr>
                    <th data-checkbox="true"></th>
                    <th data-field="id">Id</th>
                    <th data-field="code" data-formatter="formatter_main">CODE</th>
                    <th data-field="type" >类型</th>
                    <th data-field="code" data-formatter="formatter_code">登录</th>
                    <th data-field="name">名称</th>
                    <th data-field="status" data-formatter="formatter_state">状态</th>
                    <th data-field="I_Ip">IP</th>
                    <th data-field="" data-formatter="formatter_config">配置</th>
                    <th data-field="plat_form" data-formatter="formatter_operateSystem">操作系统</th>
                    <th data-field="location_name">部署区位</th>
                    <th data-field="vpcName" data-formatter="formatter_operateSystem">VPC</th>
                    <th data-field="I_SubnetCode">子网</th>
                    <!-- <th data-field="-">警告状态</th> -->
                    <!-- <th data-field="modify_time" data-formatter=timestrap2date>备份于</th> -->
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
        <?php if (in_array('ccf_desktop_startup', $this->Session->read('Auth.User.popedomname'))) { ?>
<!--         <li><a id="start" href="#"><i class="icon-play"></i> 启动</a></li> -->
        <?php } ?>
        <?php if (in_array('ccf_desktop_shutdonw', $this->Session->read('Auth.User.popedomname'))) { ?>
<!--         <li><a id="close" href="#"><i class="icon-off"></i> 关机</a></li> -->
        <?php } ?>
        <?php if (in_array('ccf_desktop_reboot', $this->Session->read('Auth.User.popedomname'))) { ?>
<!--         <li><a id="restart" href="#"><i class="icon-refresh"></i> 重启</a></li> -->
        <?php } ?>
        <?php if (in_array('ccm_ps_interface_logs', $this->Session->read('Auth.User.popedomname'))) { ?>
        <li id="excp"><a  href="javascript:;"><i class="icon-book"></i> 异常信息</a></li>
        <?php } ?>
        <?php if (in_array('ccm_ps_interface_logs', $this->Session->read('Auth.User.popedomname'))) { ?>
        <li id="normal"><a  href="javascript:;"><i class="icon-book"></i> 正常信息</a></li>
        <?php } ?>
    </ul>
</div>
<div id="maindiv"></div>
<div style="width:0;height:0">
    <iframe id="lauchFrame" name="lauchFrame" src="" width=0  height=0 >
    </iframe>
</div>
<?php
$this->start('script_last');
?>
<script type="text/javascript">
    function fromatter_Capacity(value, row, index) {
        return value + "GB";
    }

    /* 渲染页面 */
    function operateFormatter(value, row, index) {
        return '<a href="javascript:;" data-id="' + row.id + '" data-row="' + row.code + '" class="del-disk"><i class="icon-remove"></i></a>';
    }

    function formatter_main(value, row, index) {
        if(value!=null&&value!=""){
            return value;
        }else{
            return "-";
        }
    }

    $("#btnStart").on('click',
            function() {
                var id = getRowsID('name');
                var codes = getRowsID('code');
                var names = getRowsID('name');
                codes = codes.substring(0,codes.length-1);
                code = codes.split(",");
                names = names.substring(0,names.length-1);
                f = names.split(",");
                for(var i=0;i<code.length;i++){
                    if(code[i] == 'null'){
                        showModal('提示','icon-exclamation-sign', '当前设备状态无法进行操作', f[i], '', 0);
                        $("#btnDesktop").html("关闭");
                        return false;
                    }
                }
                if (id != "") {
                    showModal('提示', 'icon-question-sign', '确认要启动机器', id, 'ajaxFun(getRowsID(),\'ecs_start\')');
                } else {
                    showModal('提示', 'icon-exclamation-sign', '请选中一台VPX实例', '', '', 0,'关闭');
                }
                //ajaxFun(getRowsID(),'desktop_start');
            });
    $("#btnDel").on('click', function() {
        var id = getRowsID('name');
        var codes = getRowsID('code');
        var names = getRowsID('name');
        codes = codes.substring(0, codes.length - 1);
        code = codes.split(",");
        names = names.substring(0, names.length - 1);
        f = names.split(",");
        for (var i = 0; i < code.length; i++) {
            if (code[i] == 'null') {
                showModal('提示', 'icon-exclamation-sign', '当前设备状态无法进行操作', f[i], '', 0);
                $("#btnDesktop").html("关闭");
                return false;
            }
        }
        if (id != "") {
            showModal('提示', 'icon-question-sign', '确认要删除', id, 'ajaxFun(getRowsID(),\'ecs_delete\')');
        } else {
            showModal('提示', 'icon-exclamation-sign', '请选中一台VPX实例', '', '', 0, '关闭');
        }
    });

    $("#btnStop").on('click',
            function() {

                var id = getRowsID('name');
                var codes = getRowsID('code');
                var names = getRowsID('name');
                codes = codes.substring(0,codes.length-1);
                code = codes.split(",");
                names = names.substring(0,names.length-1);
                f = names.split(",");
                for(var i=0;i<code.length;i++){

                    if(code[i] == 'null'){
                        showModal('提示','icon-exclamation-sign', '当前设备状态无法进行操作', f[i], '', 0);
                        $("#btnDesktop").html("关闭");
                        return false;
                    }
                }
                if (id != "") {
                    showModal('提示', 'icon-question-sign', '确认要停止机器', id, 'ajaxFun(getRowsID(),\'ecs_stop\')');
                } else {
                    showModal('提示', 'icon-exclamation-sign', '请选中一台VPX实例', '', '', 0,'关闭');
                }
            });

    //input 存在一个被选中状态
    $("table input").on('click',
            function() {
                if ($("tbody input:checked").length >= 1) {
                    $(".center .btn-shutdown").attr('disabled', false);
                } else {
                    $(".center .btn-shutdown").attr('disabled', true);
                }
            })

    //动态创建modal
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

    $('#table').contextMenu('context-menu', {
        bindings: {
            'start': function(event) {
                var uniqueId = $(event).attr('data-uniqueid');
                var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
                if (row.status != '创建中' && row.status != '创建失败') {
                    showModal('提示', 'icon-question-sign', '确认要启动机器', row.name, 'ajaxFun(\'' + row.code + '\',\'ecs_start\',\'' + row.id + '\')');
                } else {
                    showModal('提示', 'icon-exclamation-sign', '当前设备状态无法进行操作', '', '', 0);
                    $("#btnEsc").html("关闭");
                }
            },
            'close': function(event) {
                //获取数据
                var uniqueId = $(event).attr('data-uniqueid');
                var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
                if (row.status != '创建中' && row.status != '创建失败') {
                    showModal('提示', 'icon-question-sign', '确认要停止机器', row.name, 'ajaxFun(\'' + row.code + '\',\'ecs_stop\',\'' + row.id + '\')');
                } else {
                    showModal('提示', 'icon-exclamation-sign', '当前设备状态无法进行操作', '', '', 0);
                    $("#btnEsc").html("关闭");
                }
            },
            'restart': function(event) {
                //获取数据
                var uniqueId = $(event).attr('data-uniqueid');
                var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
                if (row.status != '创建中' && row.status != '创建失败' && row.status != '已停止') {
                    showModal('提示', 'icon-question-sign', '确认要启动机器', row.name, 'ajaxFun(\'' + row.code + '\',\'ecs_reboot\',\'' + row.id + '\')');
                } else {
                    showModal('提示', 'icon-exclamation-sign', '当前设备状态无法进行操作', '', '', 0);
                    $("#btnEsc").html("关闭");
                }
            },
            'defined': function(event) {
                //获取数据
                $('#modal-defined').modal("show");
                var uniqueId = $(event).attr('data-uniqueid');
                var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
            },
            'excp':function(event){
                var uniqueId = $(event).attr('data-uniqueid');
                var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);

                var department_id = row.department_id;
                window.location.href = "/console/excp/lists/excp/hosts/"+department_id+'/all/0/0/'+row.id;
            },
            //正常
            'normal':function(event){
                var uniqueId = $(event).attr('data-uniqueid');
                var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
                var department_id = row.department_id;
                window.location.href = "/console/excp/lists/normal/hosts/"+department_id+'/all/0/0/'+row.id;
            }
        }
    });

    //搜索绑定
    $("#txtsearch").on('keyup', function() {
        if (timer != null) {
            clearTimeout(timer);
        }
        var class_code = $("#agent").attr('val');

        var class_code2 = $("#agent_t").attr('val');
        var timer = setTimeout(function() {
            var search = $("#txtsearch").val();
            $('#table').bootstrapTable('refresh', {
                url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'desktop', 'init', 'lists']); ?>",
                query: {
                    type:'vpx',
                    class_code2: class_code2,
                    class_code: class_code,
                    search: search,
                    department_id:$("#txtdeparmetId").val()
                }
            });
        }, 500);

        var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','desktop','lists']); ?>?type='vpx'&search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val();
        $('#table').attr('data-url', url);
    });

    //input 存在一个被选中状态
    $("#table").on('all.bs.table.table', function (e, row, $element) {
        if ($("tbody input:checked").length >= 1) {
            $(".center .btn-default").attr('disabled', false);
        } else {
            $(".center .btn-default").attr('disabled', true);
        }
    })
    //格式化配置
    function formatter_config(value, row, index) {
        if (row.cpu != null && row.cpu != 0) {
            return row.cpu + "核*" + row.memory + "GB*" + row.gpu + "MB";
        } else {
            return "-";
        }
    }
    //格式化code
    function formatter_code(value, row, index) {
        var html = "";
        var code = row.code;
        var name = row.location_name.split('-');
        if (name[0] == "亚马逊") {
            return "-";
        } else if (name[0] == "阿里云") {
            var os = row.host_extend.plat_form;
            if (os != null) {
                var url = "/console/network/webConsole/" + code;
                if (row.status == "运行中") {
                    if (os == "Linux") {
                        html += "<a href='#' onclick='is_login(\"" + row.id + "\")'><i class='icon-laptop'></i></a>";
                        return html;
                    } else {
                        html += "<a href='#' onclick='is_login(\"" + row.id + "\")'><i class='icon-desktop'></i></a>";
                        return html;
                    }
                } else {
                    return "-";
                }
            }
        } else if (name[0] == "索贝") {
            var url = "/console/network/webConsole/" + code;
            if (row.status == "运行中") {
                if (os == "Linux") {
                    html += "<a href=" + url + " target='_blank'><i class='icon-laptop'></i></a>";
                    return html;
                } else {
                    html += "<a href=" + url + " target='_blank'><i class='icon-desktop'></i></a>";
                    return html;
                }
            } else {
                return "-";
            }
        }
        return "-";
    }


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
        return '<span id="imgState' + row.id + '" class="circle circle-create"></span>-';
    }




    //返回操作系统
    function formatter_operateSystem(value, row, index) {
        if (value != null) {
            return value;
        } else {
            return "-";
        }
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
            } else if(type==2){
                $("#txtState" + id).html('正在重启...');
            }else{
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
                } else if(type==2){
                    $("#txtState" + e).html('正在重启...');
                }else{
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
        $("#disk-manage").modal("hide");
        var tot = $('#table').bootstrapTable('getSelections');
        if (method == "ecs_start") {
            heartbeat(0, id);
        } else if (method == "ecs_stop") {
            heartbeat(1, id);
        } else if (method == "ecs_reboot") {
            heartbeat(2, id);
        } else if (method == "ecs_delete") {
            console.log(1);
            // $.each(tot, function(i,val){
            //     $('#table').bootstrapTable('removeByUniqueId',val.H_ID);
            // });
        }
        if (id != undefined) {
            $.ajax({
                type: "post",
                url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'desktop','init', 'ajaxHosts']); ?>",
                async: true,
                timeout: 9999,
                data: {
                    type:'vpx',
                    method: method,
                    instanceCode: code,
                    basicId: id,
                    isEach: "false"
                },
                //dataType:'json',
                success: function(data) {
                    console.log(data);
                    data = $.parseJSON(data);
                    if (data.Code != "0") {
                        alert(data.Message);
                    }
                    refreshTable();
                }
            });
        } else {
            $.ajax({
                type: "post",
                url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'desktop','init', 'ajaxHosts']); ?>",
                async: true,
                timeout: 9999,
                data: {
                    type:'vpx',
                    method: method,
                    table: tot,
                    isEach: "true"
                },
                //dataType:'json',
                success: function(data) {
                    data = $.parseJSON(data);
                    if (data.Code != "0") {
                        alert(data.Message);
                    }
                    refreshTable();
                }
            });
        }
    }

    function refreshTable() {
        var search= $("#txtsearch").val();
        //$('#table').bootstrapTable('showLoading');
        console.log($("#agent").attr('val'))
        var class_code = $("#agent").attr('val');
        var class_code2 =$("#agent_t").attr('val');
        $('#table').bootstrapTable('refresh', {
            url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','init','lists']); ?>",
            query: {class_code2:class_code2,type:'vpx',class_code: class_code,search: search,department_id:$("#txtdeparmetId").val()}
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
    function local(id,class_code,agent_name) {
        if (agent_name) {
            var search= $("#txtsearch").val();
            $('#agent_t').html('全部');
            $('#agent').html(agent_name);
            $('#agent').attr('val', class_code);
            $('#table').bootstrapTable('refresh', {
                url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','init','lists']); ?>",
                query: {type:'vpx',class_code: class_code,search: search,department_id:$("#txtdeparmetId").val()}
            });
            var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','init','lists']); ?>?type='vpx'&class_code=" + class_code + "&search=" + search+'&department_id='+$("#txtdeparmetId").val();
            $('#table').attr('data-url',url);
            var jsondata = <?php echo json_encode($agent); ?>;
            if(id!=0){
                var data='';
                var data='<li><a href="javascript:;" onclick="local_two(\'\',\'全部\',\'' + class_code + '\')">全部</a></li>';
                $.each(jsondata, function (i, n) {
                    if(n.parentid == id){
                        data += '<li><a href="#" onclick="local_two(\'' + n.class_code + '\',\'' + n.agent_name + '\')">' + n.agent_name + '</a></li>';
                    }
                })
                $('#agent_two').html(data);
            }else{
                data='';
                $('#agent_t').attr('val', data);
                $('#agent_two').html(data);
            }

        }
    }
    function local_two(class_code2,agent_name,class_code){
        var search= $("#txtsearch").val();
        $('#agent_t').html(agent_name);
        $('#agent_t').attr('val',class_code2);
        $('#table').bootstrapTable('refresh', {
            url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','init','lists']); ?>",
            query: {type:'vpx',class_code2: class_code2,class_code:class_code,search: search,department_id:$("#txtdeparmetId").val()}
        });
        var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','init','lists']); ?>?type='vpx'&class_code=" + class_code + "&class_code2=" + class_code2 + "&search=" + search+'&department_id='+$("#txtdeparmetId").val();
        $('#table').attr('data-url',url);
    }
    //重启
    $("#btnreboot").on('click',
            function(){
                var names = getRowsID('name');
                if (names != "") {
                    showModal('提示', 'icon-question-sign', '确认要重启VPX实例', names, 'ajaxFun(getRowsID(),\'ecs_reboot\')',1,'取消');
                } else {
                    showModal('提示', 'icon-exclamation-sign', '请选中一台VPX实例', '', '', 0,'关闭');
                }
            });

    function notifyCallBack(value){
        //console.log(value);

        var search = $("#txtsearch").val();
        var department_id = $("#txtdeparmetId").val();
        var class_code = $("#agent").attr('val');
        var class_code2 = $("#agent_t").attr('val');
        var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','init','lists']); ?>?type='vpx'&search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+department_id;

        if(value.MsgType=="success"||value.MsgType=="error"){
            if(value.Data.method=="ecs_stop"||value.Data.method=="ecs_start"||value.Data.method=="ecs_start"||value.Data.method=="desktop_init"){
                $('#table').bootstrapTable('refresh', {
                    url: url,
                    silent: true
                });
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
            url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'desktop', 'init', 'lists']); ?>",
            query: {
                type:'vpx',
                class_code2: class_code2,
                class_code: class_code,
                search: search,
                department_id:$("#txtdeparmetId").val()
            }
        });
        var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','init','lists']); ?>?type='vpx'&search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val();
        $('#table').attr('data-url', url);
    }
</script>
<?php
  $this->end();
?>
