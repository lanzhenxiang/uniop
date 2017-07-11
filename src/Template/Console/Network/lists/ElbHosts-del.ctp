<!-- 硬盘 -->
<?= $this->element('network/lists/left',['active_action'=>'elb']); ?>

<div class="wrap-nav-right">
	<div class="wrap-manage">
        <div class="top">
            <span class="title">分配ELB到主机</span><a href="<?= $this->Url->build(['controller'=>'network','action'=>'lists','eip']); ?>" class="btn btn-addition">返回ELB列表</a>
            <div id="maindiv-alert"></div>
        </div>
        <div class="center clearfix">
            <button class="btn btn-default" id="btnBind" disabled>
                <i class="icon-play "></i>&nbsp;&nbsp;绑定ELB
            </button><input type="text" style="width:250px;" disabled="disabled" value="<?php echo $_EipId->code  ?>" id="eipId"/>
            <div role="presentation" class="dropdown">
            </div>
            <div class="pull-right">
                <div class="dropdown">
                    厂商
                    <a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <span class="pull-left" id="agent">全部</span>
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
                                    <span class="pull-left" id="agent_t">全部</span>
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" id="agent_two">
                                </ul>
                            </div>
                            <div class="dropdown">
                                是否已绑定ELB
                                <a  href="javascript:;" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    <span class="pull-left" id="is_bindEip_0">全部</span>
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu" id="is_bindEip" state="">
                                    <li><a href="javascript:;" onclick="selectChange('0','全部')">全部</a></li>
                                    <li><a href="javascript:;" onclick="selectChange('1','未绑定')">未绑定</a></li>
                                    <li><a href="javascript:;" onclick="selectChange('2','已绑定')">已绑定</a></li>
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
                      data-locale="zh-CN" data-click-to-select="true" data-url="<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action' => 'network', 'elb', 'bindElbHostsList']); ?>" data-pagination="true"   data-unique-id="A_Code">
                      <thead>
                       <tr>
                        <!-- <th data-checkbox="true"></th> -->
                        <th data-field="A_Code" >主机Code</th>
                        <th data-field="A_Name">主机名称</th>
                        <th data-field="A_Status" data-formatter="formatter_state">状态</th>
                        <th data-field="B_Code" data-formatter="formatter_isBindElb">是否已绑定Elb</th>
                        <th data-field="E_DisplayName">部署区位</th>
                    </tr>
                </thead>
            </table>

        </div>

    </div>
</div>
 <!-- 右键弹框 -->
<div class="context-menu" id="context-menu">
    <ul>
        <li id="unwrap"><a href="javascript:void(0);"><i class="icon-pencil"></i>解绑</a></li>
    </ul>
</div>

<div id="maindiv"></div>

<?php
$this->start('script_last');
?>
<script type="text/javascript">
$(function(){
$('#table').contextMenu('context-menu', {
    bindings: {
        'unwrap': function(event) {
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
            if(row.B_Code!=null&&row.B_Code!=""){
                showModal('解绑', 'icon-question-sign', '是否解绑ELB',row.A_Name);
                $("#btnMk").bind('click',function(){
                    $("#modal-confirm").modal("hide");
                    $.ajax({
                        type: "post",
                        url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'elb', 'ajaxElb']); ?>",
                        async: true,
                        timeout: 9999,
                        data: {
                            method: 'lbs_unbind',
                            instanceCode:row.A_Code,
                            loadbalanceCode:row.B_Code
                        },
                        success: function(data) {
                            data = $.parseJSON(data);
                            if (data.Code != "0") {
                                alert(data.Message);
                            }
                            refreshTable('false');
                        }
                    });
                });
            }else{
                showModal('提示', 'icon-exclamation-sign', '该主机没有绑定ELB','');
                $("#btnMk").remove();
            }
      },
    }
});
});

    function selectChange(state,name){
        $("#is_bindEip").attr("state",state);
        $("#is_bindEip_0").html(name);
        refreshTable('true');
    }

    $('#table').on('click','tr',function(){
        $('#table tr').removeClass('active');
        $(this).addClass('active');
        if($('#btnBind').prop('disabled')){
         $('#btnBind').prop('disabled',false);
     }
 });

    $("#btnBind").on('click',function(){
        var _id = $('#table tr').filter('.active').data('uniqueid');
        var info = $('#table').bootstrapTable('getRowByUniqueId',_id);
        var content="确定将"+$("#eipId").val()+"-绑定到-";
        var content1=info.A_Name;
        showModal('提示', 'icon-question-sign','' + content + '','' + content1 + '');
        $('#btnMk').bind('click',function(){
            $("#modal-confirm").modal("hide");
            $.ajax({
                type: "post",
                url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'elb', 'ajaxElb']); ?>",
                async: true,
                timeout: 9999,
                data: {
                    method: 'lbs_bind',
                    instanceCode:info.A_Code,
                    loadbalanceCode:$("#eipId").val()
                },
                success: function(data) {
                    data = $.parseJSON(data);
                    if (data.Code != "0") {
                        alert(data.Message);
                    }
                    refreshTable('false');
                }
            });
        });
    });

function refreshTable(type) {
    var class_code = $("#agent").attr('val');
    var class_code2 =$("#agent_t").attr('val');
    var state = $("#is_bindEip").attr("state");
    $('#table').bootstrapTable('refresh', {
        url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','elb','bindElbHostsList']); ?>",
        query: {class_code2:class_code2,class_code: class_code,staute: state},
        silent: type
    });
}

    //搜索绑定
    $("#txtsearch").on('keyup',
        function() {
            if(timer!=null){
                clearTimeout(timer);
            }
            var timer = setTimeout(function(){
                var search= $("#txtsearch").val();
                $('#table').bootstrapTable('refresh', {
                    url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','elb','bindElbHostsList']); ?>",
                    query: {search: search}
                });
            },1000);
        });

    function local(id,class_code,agent_name) {
        if (agent_name) {
            $('#agent_t').html('全部');
            $('#agent').html(agent_name);
            $('#agent').attr('val', class_code);
            $('#table').bootstrapTable('refresh', {
                url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','elb','bindElbHostsList']); ?>",
                query: {class_code: class_code}
            });
            var jsondata = <?php echo json_encode($agent); ?>;
            if(id!=0) {
                var data = '<li><a href="javascript:;" onclick="local_two(\'\',\'全部\',\'' + class_code + '\')">全部</a></li>';
                $.each(jsondata, function (i, n) {
                    if (n.parentid == id) {
                        data += '<li><a href="#" onclick="local_two(\'' + n.class_code + '\',\'' + n.agent_name + '\',\'' + class_code + '\')">' + n.agent_name + '</a></li>';
                    }
                })
                $('#agent_two').html(data);
            }else{
                data = '';
                $('#agent_two').html(data);
            }
        }
    }

    function local_two(class_code2,agent_name,class_code){
        $('#agent_t').html(agent_name);
        $('#agent_t').attr('val',class_code2);
        $('#table').bootstrapTable('refresh', {
            url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','elb','bindElbHostsList']); ?>",
            query: {class_code2: class_code2,class_code:class_code}
        });
    }

//动态创建modal
function showModal(title, icon, content, content1) {
    $("#maindiv").empty();
    html = "";
    html += '<div class="modal fade" id="modal-confirm" tabindex="-1" role="dialog">';
    html += '<div class="modal-dialog" role="document">';
    html += '<div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
    html += '<h5 class="modal-title">' + title + '</h5>';
    html += '</div><div class="modal-body"><i class="'+icon' icon-color-gray text-primary"></i>&nbsp;&nbsp;' + content + '<span class="text-primary">' + content1 + '</span></div>';
    html += '<div class="modal-footer"><button id="btnMk" type="button" class="btn btn-primary">确认</button><button type="button" class="btn btn-danger" data-dismiss="modal">关闭</button></div></div></div></div>';
    $("#maindiv").append(html);
    $('#modal-confirm').modal("show");
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

function formatter_isBindElb(value,row,index){
    if(value!=null&&value!=""){
        return "已绑定ELB";
    }else{
        return "未绑定ELB";
    }
}

function notifyCallBack(value){
    //console.log(value);
  if(value.MsgType=="success"||value.MsgType=="error"){
    if(value.Data.method=="lbs_bind"||value.Data.method=="lbs_unbind"){
      refreshTable('true');
  }

}
}

</script>
<?php
$this->end();
?>
