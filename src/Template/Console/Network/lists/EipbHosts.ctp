<!-- 硬盘 -->
<?= $this->element('network/lists/left',['active_action'=>'eip']); ?>

<div class="wrap-nav-right">
    <div class="wrap-manage">
        <div class="top">
            <span class="title">分配EIP到主机</span><a href="<?= $this->Url->build(['controller'=>'network','action'=>'lists','eip']); ?>" class="btn btn-addition">返回EIP列表</a>
        <div id="maindiv-alert"></div>
    </div>
        <div class="center clearfix">
            <button class="btn btn-default" id="btnBind" disabled>
            <i class="icon-play "></i>&nbsp;&nbsp;绑定EIP
            </button><input type="text" disabled="disabled" value="<?php echo $_EipId->code  ?>" id="eipId"/>
            <div role="presentation" class="dropdown">
            </div>
            <div class="pull-right">
                <div class="dropdown">
                <input type="hidden" id="txtdeparmetId" value="<?= $department_id ?>" />
                                是否已绑定EIP
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
            <table id="table" data-toggle="table" 
                   data-side-pagination="server"
                   data-locale="zh-CN" data-click-to-select="true" data-url="<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action' => 'network', 'eip', 'bindEipHostsList','?'=>['class_code2'=>$_EipId->location_code,'department_id'=>$department_id,'eipCode'=>$_EipId->code]]); ?>" data-pagination="true"   data-unique-id="A_Code">
            <thead>
             <tr>
                <!-- <th data-checkbox="true"></th> -->
                <th data-field="A_ID" >Id</th>
                <th data-field="A_Code" >主机Code</th>
                <th data-field="A_Name">主机名称</th>
                <th data-field="A_Status" data-formatter="formatter_state">状态</th>
                <th data-field="E_Code" data-formatter="formatter_isBindEip">是否已绑定EIP</th>
                <th data-field="E_DisplayName">部署区位</th>
            </tr>
                </thead>
            </table>

        </div>

    </div>
</div>

<div id="maindiv"></div>

<?php
$this->start('script_last');
?>
<script type="text/javascript">

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
    var eip = isUserEip(info.E_Code,$("#eipId").val());
    if(eip){
        var content="确定将"+$("#eipId").val()+"-绑定到-";
        var content1=info.A_Name;
        showModal('提示', 'icon-question-sign','' + content + '','' + content1 + '');
         $('#btnMk').bind('click',function(){
            $("#modal-confirm").modal("hide");
                $.ajax({
                type: "post",
                url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'eip', 'ajaxEip']); ?>",
                async: true,
                timeout: 9999,
                data: {
                    method: 'eip_bind',
                    iaasCode:info.A_Code,
                    eipCode:$("#eipId").val(),
                    isForward:true,
                    isEach:false,
                    isEcs:true
                },
                //dataType:'json',
                success: function(data) {
                    data = $.parseJSON(data);
                    if (data.Code != "0") {
                        showModal('提示', 'icon-exclamation-sign', data.Message,'');
                        $("#btnMk").remove();
                    }
                    refreshTable('false');
                }
            });
         });
    }
});

function isUserEip(code,eipCode){
    var name,result;
    if(code!=""&&code!=null&&code!=undefined){
        showModal('提示', 'icon-exclamation-sign','该设备已经绑定EIP','不能重复绑定');
        $("#btnMk").remove();
        return false;
    }
    else{
        result = true;
    }
    $.ajax({
        type:"post",
        url:"<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'eip', 'isUserEip']); ?>",
        data:{code:eipCode},
        success:function(data){
            data = $.parseJSON(data);
            if(data.length==0){
                name = "";
            }else{
                name = data[0]['name'];
            }
        }
    });
    if(name!=""){
        result = true;
    }else{
        showModal('提示', 'icon-exclamation-sign','该EIP已经绑定设备',name);
        $("#btnMk").remove();
        return false;
    }
    return result;
}


function refreshTable(type) {
    var state = $("#is_bindEip").attr("state");
    $('#table').bootstrapTable('refresh', {
        url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','eip','bindEipHostsList','?'=>['class_code2'=>$_EipId->location_code]]); ?>",
        query: {staute: state,department_id:$("#txtdeparmetId").val(),eipCode:$("#eipId").val()},
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
                    url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','eip','bindEipHostsList','?'=>['class_code2'=>$_EipId->location_code]]); ?>",
                    query: {search: search,department_id:$("#txtdeparmetId").val(),eipCode:$("#eipId").val()}
                });
            },1000);
        });
    function notifyCallBack(value){
        //console.log(value);
        if(value.MsgType=="success"||value.MsgType=="error"||value.MsgType=="info"){
            if(value.Data.method=="eip_bind"){
                refreshTable('true');
            }

        }
    }

//动态创建modal
function showModal(title, icon, content, content1) {
    $("#maindiv").empty();
    html = "";
    html += '<div class="modal fade" id="modal-confirm" tabindex="-1" role="dialog">';
    html += '<div class="modal-dialog" role="document">';
    html += '<div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
    html += '<h5 class="modal-title">' + title + '</h5>';
    html += '</div><div class="modal-body"><i class="'+icon+' icon-color-gray text-primary"></i>&nbsp;&nbsp;' + content + '<span class="text-primary">' + content1 + '</span></div>';
    html += '<div class="modal-footer"><button id="btnMk" type="button" class="btn btn-primary">确认</button><button type="button" class="btn btn-danger" data-dismiss="modal">取消</button></div></div></div></div>';
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

    function formatter_isBindEip(value,row,index){
        if(value!=null&&value!=""){
            return "已绑定EIP";
        }else{
            return "未绑定EIP";
        }
    }

</script>
<?php
$this->end();
?>
