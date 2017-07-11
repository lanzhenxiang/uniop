<!--  云桌面  列表 -->
<?= $this->element('desktop/lists/left',['active_action'=>'init']); ?>
<div class="wrap-nav-right desktop-supp">

    <div class="wrap-manage">
        <div class="top">
            <span class="title">桌面支撑服务管理</span>

            <div id="maindiv-alert"></div>
        </div>
        <div class="modal-title-list margin20">
            <ul class="clearfix">
                <li class="active margin">vpc1</li>
                <li>vpc2</li>
            </ul>
        </div>
        <!--bnt box-->
        <div class="center clearfix">
            <button class="btn btn-addition" onclick="refreshTable();">
                <i class="icon-refresh"></i>&nbsp;&nbsp;刷新
            </button>
            <?php if (in_array('ccf_host_new', $this->Session->read('Auth.User.popedomname'))) { ?>
            <button class="btn btn-addition add-desktop">
                <i class="icon-plus"></i>&nbsp;&nbsp;新建</button>
            <?php } ?>
            <?php if (in_array('ccf_host_startup', $this->Session->read('Auth.User.popedomname'))) { ?>
            <button class="btn btn-default" id="btnStart" disabled>
                <i class="icon-play "></i>&nbsp;&nbsp;启动
            </button>
            <?php } ?>
            <?php if (in_array('ccf_host_shutdonw', $this->Session->read('Auth.User.popedomname'))) { ?>
            <button class="btn btn-default" id="btnStop" disabled>
                <i class="icon-off "></i>&nbsp;&nbsp;关机
            </button>
            <button class="btn btn-default" onclick="" id="btnDel" disabled="disabled">
            <i class="icon-trash"></i>&emsp;<span>删除</span>
        </button>
            <?php } ?>

          <!--  <div class="pull-right">
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
                    厂商
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
                    地域
                    <a  href="javascript:;" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <span class="pull-left" id="agent_t" val="">全部</span>
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" id="agent_two"></ul>
                </div>
                <span class="search"><input type="text" id="txtsearch" name="search" placeholder="搜索">
                  <i class="icon-search"></i>
                </span>
                &lt;!&ndash; <button class="btn btn-addition dropdown" role="presentation">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-tag"></i>&nbsp;&nbsp;标签</a>
                    <ul class="dropdown-menu">
                        <li><a href="#">测试环境</a></li>
                        <li><a href="#">只是环境</a></li>
                    </ul>
                  </button> &ndash;&gt;
            </div>-->
        </div>
        <!--信息-->
        <div class="center">
            <span class="marginr20">部署区位：<span>萧山云-杭州</span></span>
            &emsp;<span class="marginr20">VPC名：<span>VPC1</span></span>
            &emsp;<span>子网：<span>后台</span></span>
        </div>
        <div class="table-body bot">
            <table data-toggle="table">
                <thead>
                <tr>
                    <th data-checkbox="true"></th>
                    <th data-field="id" >Id</th>
                    <th data-field="code" data-formatter="formatter_main">CODE</th>
                    <th data-field="type" >类型</th>
                    <th data-field="code" data-formatter="formatter_code">登录</th>
                    <th data-field="name">桌面名称</th>
                    <th data-field="status" data-formatter="formatter_state">状态</th>
                    <th data-field="plat_form" data-formatter="formatter_operateSystem">操作系统</th>
                    <!--<th data-field="location_name">部署区位</th>-->
                    <!--<th data-field="vpcName" data-formatter="formatter_operateSystem">VPC名</th>-->
                    <!--<th data-field="I_SubnetCode">子网</th>-->
                    <th data-field="I_Ip">内网IP</th>
                    <th data-field="" data-formatter="formatter_config">配置</th>
                    <!-- <th data-field="-">警告状态</th> -->
                    <!-- <th data-field="modify_time" data-formatter=timestrap2date>备份于</th> -->
                    <th data-field="create_time" data-formatter=timestrap2date>创建时间</th>
                </tr>
                </thead>
            </table>
            <table id="table" data-toggle="table"
                   data-pagination="true"
                   data-side-pagination="server"
                   data-locale="zh-CN"
                   data-click-to-select="true"
                   data-url="<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','init','lists','?'=>['department_id'=>$_default['id']]]); ?>"
                   data-unique-id="id">
                <thead>
                <tr>
                    <th data-checkbox="true"></th>
                    <th data-field="id" >Id</th>
                    <th data-field="code" data-formatter="formatter_main">CODE</th>
                    <th data-field="type" >类型</th>
                    <th data-field="code" data-formatter="formatter_code">登录</th>
                    <th data-field="name">桌面名称</th>
                    <th data-field="status" data-formatter="formatter_state">状态</th>
                    <th data-field="plat_form" data-formatter="formatter_operateSystem">操作系统</th>
                    <!--<th data-field="location_name">部署区位</th>-->
                    <!--<th data-field="vpcName" data-formatter="formatter_operateSystem">VPC名</th>-->
                    <!--<th data-field="I_SubnetCode">子网</th>-->
                    <th data-field="I_Ip">内网IP</th>
                    <th data-field="" data-formatter="formatter_config">配置</th>
                    <!-- <th data-field="-">警告状态</th> -->
                    <!-- <th data-field="modify_time" data-formatter=timestrap2date>备份于</th> -->
                    <th data-field="create_time" data-formatter=timestrap2date>创建时间</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<!--新建桌面支撑-->
<div class="modal fade" id="desktop-add" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h5 class="modal-title">新建桌面支撑服务</h5>
            </div>

            <div class="modal-body clearfix">
                <div class="pull-left fire-left">
                    <form action="" method="post" id="addfrom_listen" class="form-horizontal">
                        <div class="modal-form-group">
                            <label>部署区位:</label>
                            <span>萧山云-杭州</span>
                        </div>
                        <div class="modal-form-group">
                            <label>VPC名:</label>
                           <span>VPC1</span>
                        </div>

                        <div class="modal-form-group">
                            <label>选择子网:</label>
                            <div class="bk-select-group">
                                <select class="select-style">
                                    <option value="">办公</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">确认创建</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>
<!-- 右键弹框 -->
<div class="context-menu" id="context-menu">
    <ul>
        <?php if (in_array('ccf_desktop_startup', $this->Session->read('Auth.User.popedomname'))) { ?>
        <li><a id="start" href="#"><i class="icon-play"></i> 启动</a></li>
        <?php } ?>
        <?php if (in_array('ccf_desktop_shutdonw', $this->Session->read('Auth.User.popedomname'))) { ?>
        <li><a id="close" href="#"><i class="icon-off"></i> 关机</a></li>
        <?php } ?>
        <?php if (in_array('ccf_desktop_reboot', $this->Session->read('Auth.User.popedomname'))) { ?>
        <li><a id="restart" href="#"><i class="icon-refresh"></i> 重启</a></li>
        <?php } ?>
        <?php if (in_array('ccf_excp_list', $this->Session->read('Auth.User.popedomname'))) { ?>
        <li id="excp"><a  href="javascript:;"><i class="icon-book"></i> 异常信息</a></li>
        <?php } ?>
        <?php if (in_array('ccf_normal_list', $this->Session->read('Auth.User.popedomname'))) { ?>
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

    control();
    function control(){
        var $tabIndex;
        var inputCheck=function(table){
            $(table).find("input[type='checkbox']").attr("checked",false);
        };
        $(".modal-title-list").on("click", "li", function(){
            $(this).addClass("active");
            $(this).siblings().removeClass("active");
            inputCheck(".table-body .bootstrap-table");
            if ($("tbody input:checked").length >= 1) {
                $(".center .btn-default").attr('disabled', false);
            } else {
                $(".center .btn-default").attr('disabled', true);
            }
            $tabIndex=$(this).index();
            var $table=$(".table-body .bootstrap-table").eq($tabIndex);
            $table.show();
            $table.siblings().hide();
        });
    }
//新建modal
    $(".btn.add-desktop").on('click',function(){
        $("#desktop-add").modal("show");
    });
//    删除
$("#btnDel").on('click',function(){
    showModal('提示','icon-exclamation-sign', '确定删除此子网？','', '');
})
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
                    showModal('提示', 'icon-exclamation-sign', '请选中一台云桌面', '', '', 0,'关闭');
                }
                //ajaxFun(getRowsID(),'desktop_start');
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
                    showModal('提示', 'icon-exclamation-sign', '请选中一台云桌面', '', '', 0,'关闭');
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
                    class_code2: class_code2,
                    class_code: class_code,
                    search: search,
                    department_id:$("#txtdeparmetId").val()
                }
            });
        }, 500);

        var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','desktop','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val();
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

    //时间戳转换日期格式
    function timestrap2date(value) {
        var now = new Date(parseInt(value) * 1000);
        return now.toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ");
    }
    //格式化配置
    function formatter_config(value, row, index) {
        if (row.cpu != 0) {
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
            $.each(tot, function(i,val){
                $('#table').bootstrapTable('removeByUniqueId',val.H_ID);
            });
        }
        if (id != undefined) {
            $.ajax({
                type: "post",
                url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'desktop','init', 'ajaxHosts']); ?>",
                async: true,
                timeout: 9999,
                data: {
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
            query: {class_code2:class_code2,class_code: class_code,search: search,department_id:$("#txtdeparmetId").val()}
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
                url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','desktop','lists']); ?>",
                query: {class_code: class_code,search: search,department_id:$("#txtdeparmetId").val()}
            });
            var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','desktop','lists']); ?>?class_code=" + class_code + "&search=" + search+'&department_id='+$("#txtdeparmetId").val();
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
            query: {class_code2: class_code2,class_code:class_code,search: search,department_id:$("#txtdeparmetId").val()}
        });
        var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','init','lists']); ?>?class_code=" + class_code + "&class_code2=" + class_code2 + "&search=" + search+'&department_id='+$("#txtdeparmetId").val();
        $('#table').attr('data-url',url);
    }
    //重启
    $("#btnreboot").on('click',
            function(){
                var names = getRowsID('name');
                if (names != "") {
                    showModal('提示', 'icon-question-sign', '确认要重启云桌面', names, 'ajaxFun(getRowsID(),\'ecs_reboot\')',1,'取消');
                } else {
                    showModal('提示', 'icon-exclamation-sign', '请选中一台云桌面', '', '', 0,'关闭');
                }
            });


    function btnaddDisks(idList) {//挂载硬盘
        var id = $("#hostsId").val();
        var instanceCode = $("#hostsCode").val();
        var name = $("#txtdisks_name").val();
        var size = $("#amount").val();
        var vpcCode = $("#txtvpcCode").val();
        var class_code=$("#txtclass_code").val();
        var volumeCode, method;
        var number = 1;
        $.ajaxSettings.async = false;
        istrue = true

        var a = /^[1-9]\d*0$/;
        if (a.test(size) == false || size < 10 || size > 1000) {
            $("#amount").val(10);
        }
        if (name == "") {
            $('#name-warning').html('硬盘名称不能为空');
            $obj.prop('disabled', false);
            return false;
        }

        $.get("/console/ajax/network/hosts/getDisksCount/"+instanceCode,function(data){
            $.getJSON("/console/ajax/network/hosts/getDisksLimit",function(data2){
                if(Number(data) >= data2){
                    alert('主机最多挂载'+data2+'个硬盘')
                    istrue = false
                }
            })
        })


        $.getJSON("/console/home/getUserLimit", function(data){
            if(Number(size)+data.disks_used > data.disks_bugedt ){
                alert("配额不足 \r\n 磁盘 配额："+ data.disks_bugedt+" 已使用："+data.disks_used)
                istrue = false
            }else{

            }
        });
        if (!istrue) {
            $obj.prop('disabled', false);
            return false
        }
        $.ajaxSettings.async = true;
        if (idList != null && idList != '') {
            method = "volume_attach"; //挂载
            volumeCode=idList;
        } else {
            method = "volume_add";
        }
        $('#disk-manage').modal("hide");
        $.ajax({
            type: "post",
            url: "<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'ajax', 'action' =>'network', 'disks', 'ajaxDisks']); ?>",
            async: true,
            timeout: 9999999,
            data: {
                method: method,
                id: id,
                name: name,
                size: size,
                instanceCode: instanceCode,
                volumeCode: volumeCode,
                vpcCode: vpcCode,
                class_code:class_code
            },
            success: function(data) {
                data = $.parseJSON(data);
                if (data.Code != 0) {
                    alert(data.Message);
                }else{
                    if (idList != null && idList != '') {
                        //showModal('提示', 'icon-exclamation-sign', '添加硬盘成功','', 'hidenModal()');
                    } else {
                        //showModal('提示', 'icon-exclamation-sign', '挂载硬盘成功','', 'hidenModal()');
                    }

                }
            }
        });
    }

    function notifyCallBack(value){
        //console.log(value);

        var search = $("#txtsearch").val();
        var department_id = $("#txtdeparmetId").val();
        var class_code = $("#agent").attr('val');
        var class_code2 = $("#agent_t").attr('val');
        var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','init','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+department_id;

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
                class_code2: class_code2,
                class_code: class_code,
                search: search,
                department_id:$("#txtdeparmetId").val()
            }
        });
        var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','init','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val();
        $('#table').attr('data-url', url);
    }
</script>
<?php
  $this->end();
?>
