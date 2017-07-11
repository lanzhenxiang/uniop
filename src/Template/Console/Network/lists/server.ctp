<!--  主机  列表 -->

<style>
    .graph-display{
        padding:0 20px;
    }
    .graph-display-block{
        width:250px;
        margin-top: 25px;
        margin-right:180px;
        margin-bottom:25px;
        float:left;
    }
    .graph-display-block h4{
        width:140px;
        padding:12px 0;
        text-align:center;
    }
    .graph-display-canvas{
        position:relative;
        width:140px;
        height:140px;
        border-radius:50%;
        background:#d2d2d2;
    }
    .graph-display-account{
        position:absolute;
        top:50%;
        left:0;
        right:0;
        margin:0 auto;
        margin-top: -35px;
        width:70px;
        height:70px;
        line-height:70px;
        text-align: center;
        border-radius:50%;
        background:#fff;
    }
    .graph-display-info li{
        height:35px;
    }
    .color-block{
        display:inline-block;
        width:14px;
        height:8px;
    }
    .sign-danger{
        background:#f64649;
        color:#f64649;
    }
    .sign-out{
        background:#d5ccc5;
        color:#d5ccc5;
    }
    .sign-busy{
        background:#4c5260;
        color:#4c5260;
    }
    .sign-free{
        background:#949fb1;
        color:#949fb1;
    }
</style>
<div class="wrap-nav-right" style="margin:0 0 0 200px;">


   <div class="wrap-manage">

        <div class="top">
            <span class="title"><?= $_Type->service_name ?>-服务管理</span>
            <div class="callback-info pull-right text-success"><i class="icon-ok"></i>&nbsp;操作成功</div>
            <div id="maindiv-alert"></div>
        </div>

        <div class="graph-display clearfix">
            <?php if($this->request->query['t']==2 || $this->request->query['t']==3 || $this->request->query['t']==5 || $this->request->query['t']==7){ ?>
            <div class="graph-display-block">
                <h4>ACTOR状态统计</h4>
                <div class="graph-display-content clearfix">
                    <div class="graph-display-canvas pull-left">
                        <canvas id="status-canvas" width="140" height="140"></canvas>
                        <div class="graph-display-account"><?php if(isset($actorarray)){ echo $actorarray['total'];}?>台 </div>
                    </div>
                    <div class="graph-display-info pull-right">
                        <ul>
                            <li>
                                <span class="color-block sign-danger"></span>
                                异常：<?php if(isset($actorarray[3])){ echo $actorarray[3];}else{ echo 0;}?>台
                            </li>
                            <li>
                                <span class="color-block sign-out"></span>
                                离线：<?php if(isset($actorarray[0])){ echo $actorarray[0];}else{ echo 0;}?>台
                            </li>
                            <li>
                                <span class="color-block sign-busy"></span>
                                忙碌：<?php if(isset($actorarray[2])){ echo $actorarray[2];}else{ echo 0;}?>台
                            </li>
                            <li>
                                <span class="color-block sign-free"></span>
                                空闲：<?php if(isset($actorarray[1])){ echo $actorarray[1];}else{ echo 0;}?>台
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <?php } ?>
            <?php if($this->request->query['t']==2 || $this->request->query['t']==3 || $this->request->query['t']==5){ ?>
            <div class="graph-display-block">
                <h4>任务数统计</h4>
                <div class="graph-display-content clearfix">
                    <div class="graph-display-canvas pull-left">
                        <canvas id="task-canvas" width="140" height="140"></canvas>
                        <div class="graph-display-account"><?php if(!empty($taskarray)){ echo $taskarray['total'];}else{ echo 0;}?>台</div>
                    </div>
                    <div class="graph-display-info pull-right">
                        <ul>
                            <li>
                                <span class="color-block sign-danger"></span>
                                排队中：<?php if(!empty($taskarray)){ echo $taskarray['wait_job'];}else{ echo 0;}?>台
                            </li>
                            <li>
                                <span class="color-block sign-out"></span>
                                已完成：<?php if(!empty($taskarray)){ echo $taskarray['exec_job'];}else{ echo 0;}?>台
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>

        <div class="center clearfix">
        <button class="btn btn-addition" onclick="refreshTable();">
          <i class="icon-refresh"></i>&nbsp;&nbsp;刷新
        </button>

            <?php if (in_array('ccf_host_startup', $this->Session->read('Auth.User.popedomname'))) { ?>
        <button class="btn btn-default" id="btnStart" disabled>
          <i class="icon-play "></i>&nbsp;&nbsp;启动
        </button>
            <?php } ?>
            <?php if (in_array('ccf_host_shutdonw', $this->Session->read('Auth.User.popedomname'))) { ?>
        <button class="btn btn-default" id="btnStop" disabled>
          <i class="icon-off "></i>&nbsp;&nbsp;关机
        </button>
            <?php } ?>

            <?php if($this->request->query['t']==2 || $this->request->query['t']==3 || $this->request->query['t']==5){ ?>
        <a class="btn btn-primary" type="button" target="_blank" href="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'history', 'action' => 'historycount','?'=>['t'=>$_Type->type_id]]); ?>">历史统计</a>
        <?php } ?>
            <div class="pull-right">
            <div class="dropdown">
                        租户
                        <a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button">
                                <input type="hidden" id="txtdeparmetId" value="<?= $_default["id"] ?>" />
                                <span class="pull-left" id="deparmets" val="<?= $_default["id"] ?>"><?= $_default["name"] ?></span>
                                <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <?php foreach($_deparments as $value) { ?>
                                 <li><a href="#" onclick="departmentlist(<?php echo $value['id'] ?>,'<?php echo $value['name'] ?>')"><?php echo $value['name'] ?></a></li>
                            <?php }?>
                        </ul>
                </div>
         <div class="dropdown">
          厂商
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
            </div>
        </div>
        <div class="bot ">
            <table id="table" data-toggle="table"
     data-pagination="true"
     data-side-pagination="server"
     data-locale="zh-CN"
     data-click-to-select="true"
     data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'server', 'lists','?'=>['t'=>$_Type->type_id,'department_id'=>$_default['id']]]); ?>"
     data-unique-id="id">
     <thead>
       <tr>
        <th data-checkbox="true"></th>
        <th data-field="id" >Id</th>
        <th data-field="name">工作站名称</th>
        <th data-field="code" data-formatter="formatter_code">登录</th>
	<th data-field="host_extend" data-formatter="fomatter_vnc_password">初始密码</th>
        <th data-field="status" data-formatter=formatter_state>工作站状态</th>
        <th data-field="service_list" data-formatter=formatter_state_server>合成服务状态</th>
        <th data-field="service_list" data-formatter=formatter_task_name>正在执行任务名</th>
        <th data-field="service_list" data-formatter=formatter_start_time>任务开始时间</th>
        <th data-field="location_name">部署区位</th>
        <th data-field="host_extend" data-formatter=formatter_ip>主机IP</th>
        <th data-field="host_extend" data-formatter=formatter_config>硬件配置</th>
      </tr>
    </thead>
  </table>
</div>



<!-- 右键弹框 -->
<div class="context-menu" id="context-menu" style="display:none;">
     <ul>
        <?php if (in_array('ccf_host_startup', $this->Session->read('Auth.User.popedomname'))) { ?>
    <li id="start"><a href="javascript:void(0);"><i class="icon-off"></i> 启动</a></li>
        <?php } ?>
        <?php if (in_array('ccf_host_reboot', $this->Session->read('Auth.User.popedomname'))) { ?>
    <li id="close"><a href="javascript:void(0);"><i class="icon-off"></i> 关机</a></li>
        <?php } ?>
        <?php if (in_array('ccf_host_shutdonw', $this->Session->read('Auth.User.popedomname'))) { ?>
    <li id="restart"><a href="javascript:void(0);"><i class="icon-refresh"></i> 重启</a></li>
        <?php } ?>
        <?php if (in_array('ccf_host_change', $this->Session->read('Auth.User.popedomname'))) { ?>
    <li id="modify"><a href="javascript:void(0);"><i class="icon-pencil"></i> 启动服务</a></li>
        <?php } ?>
        <?php if (in_array('ccf_host_change', $this->Session->read('Auth.User.popedomname'))) { ?>
    <li id="modify"><a href="javascript:void(0);"><i class="icon-pencil"></i> 关闭服务</a></li>
    <?php } ?>
        <?php if (in_array('ccf_host_add_disk', $this->Session->read('Auth.User.popedomname'))) { ?>
    <!-- <li id="adddisks"><a href="javascript:void(0);"> <i class="icon-inbox"></i> 删除工作站</a></li> -->
        <?php } ?>
    </ul>
</div>

<div id="maindiv"></div>
<div class="modal fade" id="history-count" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" ><span aria-hidden="true">&times;</span></button>
        历史统计
      </div>
      <div class="modal-body">
        <canvas width="560" height="300" id="history-canvas" style="margin:30px 0 10px"></canvas>
      </div>
      <div class="modal-footer" style="text-align:center">
        <button type="button" class="btn btn-danger" data-dismiss="modal">关 闭</button>
      </div>
    </div>
  </div>
</div>
<?php $this -> start('script_last'); ?>
<script type="text/javascript">

$('#history-count').one('shown.bs.modal',function(){
    var historyCanvas = $('#history-canvas').get(0).getContext("2d");
    var data = {
        'labels':['总数','合成','转码','迁移','技审'],
        'datasets':[{
            'fillColor': '#44D2E4',
            'data':[55,35,40,40,35]
        }]
    };
    var options = {
            scaleFontColor: "#333",
            scaleLabel: "<%=value%>次",
            scaleShowVerticalLines: false,
            barShowStroke : false,
            barValueSpacing : 32
    };
    var chart = new Chart(historyCanvas).Bar(data,options);
    $('#history-canvas').on("click",
        function(evt){
            var bar = chart.getBarsAtEvent(evt);
            console.log(bar[0].value);
        }
    );
});

var options = {
    animationSteps:40,
    animationEasing:"linear"

};

if($('#status-canvas').get(0)){
    var statusCanvas = $('#status-canvas').get(0).getContext('2d');
    var actordata =  <?php if(isset($actor)){ if(strlen($actor) == 0){echo '[]';}else{ echo $actor;} }else{ echo '[]';} ?>;
    var data = actordata;
    new Chart(statusCanvas).Doughnut(data,options);
}

if($('#task-canvas').get(0)){
    var taskCanvas = $('#task-canvas').get(0).getContext('2d');
    var task = <?php if(isset($taskdata)){ if(strlen($taskdata) == 0){echo '[]';}else{ echo $taskdata;} }else{ echo '[]';} ?>;
    var taskData =  task;
    new Chart(taskCanvas).Doughnut(taskData,options);
}





//地域查询
function local(id, class_code, agent_name) {
    if (agent_name) {
        $('#agent_t').html('全部');
        $('#agent').html(agent_name);
        $('#agent').attr('val', class_code);
        var search = $("#txtsearch").val();
        $('#table').bootstrapTable('refresh', {
            url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','server','lists','?'=>['t'=>$_Type->type_id]]); ?>",
            query: {
                class_code: class_code,
                search: search,
                department_id:$("#txtdeparmetId").val()
            }
        });
        var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','server','lists','?'=>['t'=>$_Type->type_id]]); ?>&class_code=" + class_code + "&search=" + search+'&department_id='+$("#txtdeparmetId").val();
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
            $('#agent_two').html(data);
        }
    }
}

function local_two(class_code2,agent_name,class_code){
    var search= $("#txtsearch").val();
    $('#agent_t').html(agent_name);
    $('#agent_t').attr('val',class_code2);
    $('#table').bootstrapTable('refresh', {
        url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','server','lists','?'=>['t'=>$_Type->type_id]]); ?>",
        query: {class_code2: class_code2,class_code:class_code,search: search,department_id:$("#txtdeparmetId").val()}
    });
    var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','server','lists','?'=>['t'=>$_Type->type_id]]); ?>&class_code=" + class_code + "&class_code2=" + class_code2 + "&search=" + search+'&department_id='+$("#txtdeparmetId").val();
    $('#table').attr('data-url',url);
}

$("#btnStart").on('click',
function() {
    var names = getRowsID('name');
    if (names != "") {
        showModal('提示', 'icon-question-sign', '确认要启动机器', names, 'ajaxFun(getRowsID(),\'ecs_start\')');
    } else {
        showModal('提示', 'icon-exclamation-sign', '请选中一台主机', '', '', 0);
    }
});
$("#btnStop").on('click',
function() {
    var names = getRowsID('name');
    if (names != "") {
        showModal('提示', 'icon-question-sign', '确认要停止机器', names, 'ajaxFun(getRowsID(),\'ecs_stop\')');
    } else {
        showModal('提示', 'icon-exclamation-sign', '请选中一台主机', '', '', 0);
    }
});
//重启
$("#btnreboot").on('click',
function() {
    var names = getRowsID('name');
    if (names != "") {
        showModal('提示', 'icon-question-sign', '确认要重启机器', names, 'ajaxFun(getRowsID(),\'ecs_reboot\')');
    } else {
        showModal('提示', 'icon-exclamation-sign', '请选中一台主机', '', '', 0);
    }
});

//input 存在一个被选中状态
$("#table").on('all.bs.table', function (e, row, $element) {
    if ($("tbody input:checked").length >= 1) {
        $(".center .btn-default").attr('disabled', false);
    } else {
        $(".center .btn-default").attr('disabled', true);
    }
});

//动态创建modal
function showModal(title, icon, content, content1, method, type) {
    $("#maindiv").empty();
    html = "";
    html += '<div class="modal fade" id="modal" tabindex="-1" role="dialog">';
    html += '<div class="modal-dialog" role="document">';
    html += '<div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
    html += '<h5 class="modal-title">' + title + '</h5>';
    html += '</div><div class="modal-body"><i class="'+icon+' icon-color-gray text-primary"></i>&nbsp;&nbsp;' + content + '<span class="text-primary">' + content1 + '</span></div>';
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
            showModal('提示', 'icon-question-sign', '确认要启动机器', row.name, 'ajaxFun(\'' + row.code + '\',\'ecs_start\',\'' + row.id + '\')');
        },
        'close': function(event) {
            //获取数据
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
            showModal('提示', 'icon-question-sign', '确认要停止机器', row.name, 'ajaxFun(\'' + row.code + '\',\'ecs_stop\',\'' + row.id + '\')');

        },
        'restart': function(event) {
            //获取数据
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
            showModal('提示', 'icon-question-sign', '确认要启动机器', row.name, 'ajaxFun(\'' + row.code + '\',\'ecs_reboot\',\'' + row.id + '\')');
        },
        'del':function(event){
            //获取数据
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
            showModal('提示', 'icon-question-sign', '确认要删除机器', row.name, 'ajaxFun(\'' + row.code + '\',\'ecs_delete\',\'' + row.id + '\')');
        }
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
            url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','server', 'lists','?'=>['t'=>$_Type->type_id]]); ?>?search=" + search+'&department_id='+$("#txtdeparmetId").val()
        });
    },
    1000);
});

function fomatter_vnc_password(value, row, index){
	return value.vnc_password
}
//格式化配置
function formatter_config(value, row, index) {
    if (value != null) {
        if (value.cpu != 0) {
            return value.cpu + "核*" + value.memory + "GB";
        } else {
            return "-";
        }
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
        if (row.host_extend != null) {
            var os = row.host_extend.plat_form;
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
        var os = row.host_extend.plat_form;
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

//返回ip
function formatter_ip(value, row, index) {
    if (value.ip != null) {
        return value.ip;
    } else {
        return "-";
    }
}

function formatter_start_time(value, row, index){
    if(value!=null){
      if(value.task_stime!=null&&value.task_stime!=''){
        return timestrap2date(value.service_list.task_stime)
      }else{
        return "-";
      }
    }else{
      return "-";
    }

}

function formatter_task_name(value,row,index){
  if(value != null){
    if(value.task_name!=null&&value.task_name!=''){
      return value.service_list.task_name;
    }else
    {
      return "-";
    }
  }else{
    return "-";
  }
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
}


function formatter_state_server(value,row,index){
  var val = value;
  if(val!=null){
    if(val.service_status!=null && val.service_status!=''){
      switch(val.service_status){
        case 1:
        {
          return '<span id="imgState_server' + row.id + '" class="circle circle-run"></span><span id="txtState_server' + row.id + '">运行中</span>';
        }
        case 0:
        {
          return '<span id="imgState_server' + row.id + '" class="circle circle-stopped"></span><span id="txtState_server' + row.id + '">关闭</span>';
        }
        case 3:
        {
          return '<span id="imgState_server' + row.id + '" class="circle circle-stopped"></span><span id="txtState_server' + row.id + '">异常</span>';
        }
      }
    }else{
      return '<span id="imgState_server' + row.id + '" class="circle circle-run"></span><span id="txtState_server' + row.id + '">运行中</span>';
    }
  }else{
    return '<span id="imgState_server' + row.id + '" class="circle circle-run"></span><span id="txtState_server' + row.id + '">运行中</span>';
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
    if (method == "ecs_start") {
        heartbeat(0, id,'');
    } else if (method == "ecs_stop") {
        heartbeat(1, id,'');
    } else if (method == "ecs_reboot") {
        heartbeat(2, id,'');
    }else if(method == "ecs_delete") {
        heartbeat(3, id,'');
    }
    if(id!=undefined){
      $.ajax({
        type: "post",
        url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'server', 'ajaxHosts']); ?>",
        async: true,
        timeout: 9999,
        data: {
          method: method,
          instanceCode:code,
          basicId:id,
          isEach:"false"
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
    }else{
      $.ajax({
        type: "post",
        url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'server', 'ajaxHosts']); ?>",
        async: true,
        timeout: 9999,
        data: {
          method: method,
          instanceCode:code,
          basicId:id,
          isEach:"true"
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
    var class_code = $("#agent").attr('val');
    var class_code2 =$("#agent_t").attr('val');
    $('#table').bootstrapTable('refresh', {
                url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','server', 'lists','?'=>['t'=>$_Type->type_id]]); ?>",
                query: {class_code2:class_code2,class_code: class_code,search: search,department_id:$("#txtdeparmetId").val()}
            });
}

function notifyCallBack(value){
  //console.log(value);
  if(value.MsgType=="success"||value.MsgType=="error"){
    if(value.Data.method=="ecs_del"||value.Data.method=="ecs_add"||value.Data.method=="ecs"){
      $('#table').bootstrapTable('refresh', {
        url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'server', 'lists','?'=>['t'=>$_Type->type_id,'department_id'=>$_default['id']]]); ?>",
        silent: true
      });
    }
  }
}

//心跳
function heartbeat(type,id,name) {
    if (id != undefined && id != "") {
        var id = name+id;
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
            var e = name+e;
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

function webadmin(id){
  var row = $('#table').bootstrapTable('getRowByUniqueId', id);
  $("#modal").modal("hide");
      $.ajax({
        type: "post",
        url: "<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'ajax', 'action' =>'network', 'hosts', 'webadmin']); ?>",
        async: true,
        data: {
            method: "ecs_up_vnc_password",
            instanceCode: row.code
        },
        success: function(data) {
            data = $.parseJSON(data);
            if (data.Code != 0) {
                alert(data.Message);
            }else{
              showModal('提示', 'icon-question-sign', '初始化已经完成,是否立即重启','重启后生效', 'ajaxFun(getRowsID(),\'ecs_reboot\')');
              $("#btnModel_ok").html("立即重启");
              $("#btnEsc").html("稍后重启");
            }
            // $('#disk-manage').modal("hide");
        }
    });
}

function is_login(id){
  var url;
  var row = $('#table').bootstrapTable('getRowByUniqueId', id);
  if(row.host_extend.vnc_password!=null && row.host_extend.vnc_password!=""){
    url = "/console/network/webConsole/" + row.code;
    window.open(url);
  }else{
    showModal('提示', 'icon-question-sign', '当前是第一次操作,是否进行初始化操作','', 'webadmin(\'' + id + '\')');
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
        url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','server','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val()
    });
}
</script>
<?php
$this -> end();
?>