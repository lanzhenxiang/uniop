<!--  安全  列表 -->
<?= $this->element('security/left',['active_action'=>$_select]); ?>
<div class="wrap-nav-right">
    <div class="wrap-manage">
            <div class="top">
                <div class="pull-left">
            <span class="title"><?= $_name ?></span>
                </div>
                <div class="pull-right">
                    <a href="/console/security/lists/firewall">
                    <button class="btn btn-addition">
                        <i class="icon-reply"></i>&nbsp;&nbsp;返回防火墙列表
                    </button>
                </a>
                </div>
                <div style="clear:both"></div>
            <div id="maindiv-alert"></div>
        </div>
                    <div class="center clearfix">
                       <button class="btn btn-addition" onclick="refreshTable();">
                          <i class="icon-refresh"></i>&nbsp;&nbsp;刷新
                      </button>
        <a class="btn btn-addition" data-toggle="modal" id="fire-built-btn" href="#"><i class="icon-plus"></i>&nbsp;&nbsp;新建</a>
        <button class="btn btn-default" id="btnDel" disabled="disabled">
          <i class="icon-remove"></i>&nbsp;&nbsp;删除
        </button>
            <div class="pull-right">
                <span class="search"><input type="text" id="txtsearch" name="search" placeholder="搜索">
                  <i class="icon-search"></i>
              </span>
          </div>
      </div>

        <div class="bot ">
            <p>下行规则</p>
            <table id="table_1" class="table_0">
                <thead>
                    <tr>
                    <th data-checkbox="true"></th>
                    <th data-field="id" ="true">Id</th>
                        <th data-field="rule_name">规则名称</th>
                        <th data-field="protocol">协议</th>
                        <th data-field="source_ip">源IP</th>
                        <th data-field="target_ip">目标IP</th>
                        <th data-field="portRange" data-formatter=formatter_startport>起始端口</th>
                        <th data-field="portRange" data-formatter=formatter_endport>结束端口</th>
                        <th data-field="direction" data-formatter=formatter_direction>通信方向</th>
                        <th data-field="status" data-formatter=formatter_status>状态</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="bot ">
            <p>上行规则</p>
            <table id="table_2" class="table_0">
                <thead>
                    <tr>
                    <th data-checkbox="true"></th>
                    <th data-field="id" ="true">Id</th>
                        <th data-field="rule_name">规则名称</th>
                        <th data-field="protocol">协议</th>
                        <th data-field="source_ip">源IP</th>
                        <th data-field="target_ip">目标IP</th>
                        <th data-field="portRange" data-formatter=formatter_startport>起始端口</th>
                        <th data-field="portRange" data-formatter=formatter_endport>结束端口</th>
                        <th data-field="direction" data-formatter=formatter_direction>通信方向</th>
                        <th data-field="status" data-formatter=formatter_status>状态</th>
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
        <!-- <li id="modify"><a href="javascript:;"><i class="icon-pencil"></i> 修改</a></li> -->
        <li id="modify_policy"><a href="javascript:;"><i class="icon-pencil"></i> 修改规则</a></li>
        <li><a id="dele" href="javascript:;"><i class="icon-trash"></i> 删除</a></li>

        <?php if (in_array('ccf_excp_list', $this->Session->read('Auth.User.popedomname'))) { ?>
        <li id="excp"><a  href="javascript:;"><i class="icon-book"></i> 异常日志</a></li>
        <?php } ?>
        <?php if (in_array('ccf_normal_list', $this->Session->read('Auth.User.popedomname'))) { ?>
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
        <input id="modal-modify-id" name="id" type="hidden" />
            <div class="modal-body">
                <p class="name">
                    <span>名称</span><input id="modal-modify-name" name="rule_name"
                    type="text">
                </p>
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
          <input type="hidden" value="" id="modal-dele-code" name="codes">
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

<!-- 新建 -->
<div class="modal fade" id="fire-built" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h5 id="firewallPolicy-modal-title" class="modal-title">新建规则</h5>
            </div>

            <div class="modal-body clearfix">
                <form method="post" id="addfrom" class="form-horizontal">
                <div class="pull-left fire-left">
                    <input type="hidden" id="firewall_policy_method" name="method" value="firewall_add_policy" />
                        <input type="hidden" name="template_id" id="templateId"/>
                        <input type="hidden" name="id" id="id"/>
                        <input type="hidden" name="txt_id" id="edit_txt_id"/>
                        <input type="hidden" name="firewallRuleCode" id="firewallRuleCode">
                        <div class="modal-form-group">
                            <label>通信方向</label>
                            <div class="form-group bk-select-group">
                                <select name="direction" id="direction">
                                    <option value="Ingress" selected="selected">下行规则</option>
                                    <option value="Egress">上行规则</option>
                                    <!-- <option value="Bothway">双向通信</option> -->
                                </select>
                            </div>
                        </div>
                        <div class="modal-form-group">
                            <label>名称:</label>
                            <div class="form-group">
                              <input name="rule_name" type="text" id="rule-name">
                              <span class="text-danger rule-name"></span>
                            </div>
                        </div>
                        <div class="modal-form-group">
                            <div class="clearfix">
                                <label>源IP:</label>
                            <div class="form-group">
                              <input name="source_ip" type="text" id="rule-ip" placeholder="0.0.0.0/0"><span class="text-danger rule-ip"></span>
                            </div>
                            </div>
                            <p class="content contentlist">源IP：默认值为0.0.0.0/0，表示对所有源IP开放，一般不作改动
                            </p>
                        </div>
                        <div class="modal-form-group">
                            <div class="clearfix">
                                 <label>目标IP:</label>
                            <div class="form-group">
                              <input name="target_ip" type="text" id="target_ip"  placeholder="0.0.0.0/0">
                              <span id="select-btn-icon" class="caret target-list"></span>
                              <span class="text-danger target_ip"></span>
                            </div>
                            </div>
                            <p class="content contentlist">目标IP：默认值为0.0.0.0/0（表示不受限制），其他支持的格式如10.159.6.0/24或10.159.6.186-10.159.6.196。仅支持IPV4。通常指定具体的IP，定位到具体ECS或ELB上，需要重点强调的是：对于ECS填其默网卡的IP，对于ELB填私有IP；不要去填ECS或ELB绑的EIP
                            </p>
                        </div>
                        <div class="modal-form-group" id="sourceAddressNat-div" >
                            <div class="clearfix">
                                <label>是否源地址转换:</label>
                                <div class="form-group">
                                    <input type="radio" name="sourceAddressNat" value="enable">是
                                    <input type="radio" name="sourceAddressNat" value="disable"  checked="checked" >否
                                </div>
                            </div>
                        </div>
                        <div class="modal-form-group" id="poolIP-div" style="display: none;" disabled>
                            <div class="clearfix">
                                <label>源地址转换IP:</label>
                            <div class="form-group">
                              <input name="poolIP" type="text" id="poolIP" placeholder="0.0.0.0" readonly style="color: #000"><span class="text-danger poolIP"></span>
                           
                            </div>
                            </div>
                            <p class="content contentlist">
                            </p>
                        </div>
                        <div class="modal-form-group">
                            <label>协议</label>
                            <div class="bk-select-group">
                                <select name="protocol" id="agreement">
                                    <!-- <option value="all">ALL</option> -->
                                    <option value="TCP">TCP</option>
                                    <option value="UDP">UDP</option>
                                    <option value="ICMP">ICMP</option>
                                </select>
                            </div>
                        </div>
                        <div id="port-group">
                            <div class="modal-form-group">
                                <label>起始端口:</label>
                                <div class="form-group">
                                  <input name="startPort"  id="startPort" type="text">
                                </div>
                            </div>
                            <div class="modal-form-group">
                                <label>结束端口:</label>
                                <div class="form-group">
                                    <input name="stopPort" id="stopPort" type="text">
                                </div>
                            </div>
                        </div>
                        
                        <input type="hidden" value="Accept"  name="action"/>
                        <!-- <div class="modal-form-group">
                            <label>允许/拒绝</label>
                            <div class="bk-select-group">
                                <select name="action" id="selaction">
                                    <option value="Accept">接受</option>
                                    <option value="Deny">拒绝</option>
                                </select>
                            </div>
                        </div> -->

                </div>
                <div class="fire-right">
                    <p>快捷方式</p>
                    <ul>
                        <li class="active">ssh</li>
                        <li>http</li>
                        <li >https</li>
                        <li>ftp</li>
                        <li>openvpn</li>
                        <li>remote</li>
                        <li>pptp</li>
                    </ul>
                </div>
                <div style="clear:both;text-align:center">
                    <button type="submit" class="btn btn-primary">确认</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                </div>
                </form>
                <div id="selectItem" class="selectItemhidden bootstrap-table targetIpTable" data-pagination="false" style=""> 
                    <table id="ecsTable" class="table fselect-table" data-pagination="false" data-classes="table-hover" data-show-header="false" data-show-pagination="false" data-page-size="15" data-smart-display="true" data-show-pagination-switch="false">
                                <thead>
                                    <tr>
                                    <th data-field="name">主机名称</th>
                                    </tr>
                                </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div style="display: none;">
    <iframe id="lauchFrame" name="lauchFrame" src=""></iframe>
</div>
<?php
$this->start('script_last');
?>
<?=$this->Html->script(['validator.bootstrap.js']); ?>
<script type="text/javascript">
$('#fire-built-btn').on('click',function(){
    var $modal = $('#fire-built');
    $modal.find('input[type="text"]').val("");
    $modal.find('#agreement > option').eq(0).prop("selected","true");
    $modal.find('#direction > option').eq(0).prop("selected","true");
    $modal.find('#selaction > option').eq(0).prop("selected","true");
    initIpSelectList();
    $modal.modal('show');
    $("#startPort").val(22);
    $("#agreement").val('TCP');
});

function setFirewallpolicyIp(direction) {
    var id=getUrlParam('id');
    $.ajax({
        url: url,
        async: false,
        data: {
            'table_1': table_1,
            'table_2': table_2,
            isEach: true,
            type: type,
            basic_id: basic_id
        },
        method: 'post',
        dataType: 'json',
        success: function(e) {
            e = $.parseJSON(e);
            //操作成功
            if (e.Code != "0") {
                alert(e.Message);
            } else {}
            refreshTable();
            $("#fire-built").modal("hide");
        }
    });
}

$("input[name=stopPort]").on('blur',function(){
     var startPort = parseInt($(this).parents(".modal-form-group").prev().find("input[name=startPort]").val());
    if($(this).val()<startPort && startPort < 65536){
        $(this).val(startPort);
    }
})
$("input[name=startPort]").on('blur',function(){
    var stopPort = parseInt($(this).parents(".modal-form-group").next().find("input[name=stopPort]").val());
     if($(this).val()>stopPort && $(this).val() < 65536 ){
        $(this).parents(".modal-form-group").next().find("input[name=stopPort]").val($(this).val());
    }
})
$(document).ready(function(){
    $("#agreement").change(function(){
        if($(this).val()=="all"){
            $('#port-group').css('display','none');
        }else{
            $('#port-group').css('display','block');
        }
    });
    //绑定正向-上行规则信息
    var templateId=getUrlParam('templateId');
    if(templateId!=null){
        $("#templateId").val(templateId);
        $("#edit_id").val(templateId);
        $('#table_1').bootstrapTable({
            url: "/console/ajax/security/FirewallPolicy/lists?d=1&templateId=" + templateId,
            pagination:true,
            sidePagination:"server",
            locale: "zh-CN",
            clickToSelect: "true",
            uniqueId: "id",
            clickToSelect: "true"
        });
        $('#table_2').bootstrapTable({
            url: "/console/ajax/security/FirewallPolicy/lists?d=2&templateId=" + templateId,
            pagination:true,
            sidePagination:"server",
            locale: "zh-CN",
            clickToSelect: "true",
            uniqueId: "id",
            clickToSelect: "true"
        });
    }else{
        var id=getUrlParam('id');
        $("#id").val(id);
        $("#edit_id").val(id);
        $('#table_1').bootstrapTable({
            url: "/console/ajax/security/FirewallPolicy/lists?d=1&id=" + id,
            pagination:true,
            sidePagination:"server",
            locale: "zh-CN",
            clickToSelect: "true",
            uniqueId: "id",
            clickToSelect: "true"
        });
        $('#table_2').bootstrapTable({
            url: "/console/ajax/security/FirewallPolicy/lists?d=2&id=" + id,
            pagination:true,
            sidePagination:"server",
            locale: "zh-CN",
            clickToSelect: "true",
            uniqueId: "id",
            clickToSelect: "true"
        });
        $('#ecsTable').bootstrapTable({
            url: "/console/ajax/security/Firewall/getEcsListInFirewallVpc?d=2&direction=Ingress&firewall_id=" + id,
            pagination:true,
            sidePagination:"server",
            locale: "zh-CN",
            clickToSelect: "true",
            uniqueId: "id",
            clickToSelect: "true",
            onClickRow:function(row,element){
                if($("#selectItem").hasClass("targetIpTable")){
                    $("#target_ip").val(row.ip);
                    $("#selectItem").hide();
                }
                if($("#selectItem").hasClass("sourceIpTable")){
                    $("#rule-ip").val(row.ip);
					if (row.eip == '' || row.eip == null) {
						$("input[name='sourceAddressNat']:eq(1)").prop("checked",'checked');
						$("#poolIP-div").css('display','none');
						$("#poolIP").val(row.eip);
					} else {
						$("#poolIP").val(row.eip);
					}
                    $("#selectItem").hide();
                }
            }
        });
    }
});

$("#btnDel").on('click',function(){
            $('#modal-info').html('确认要删除选中防火墙规则');
            $('#modal-dele').modal("show");
            $('#sumbiter-dele').attr('id', 'sumbiter-dele');
            $('#sumbiter-dele').one('click',function(){
                $('#modal-dele').modal("hide");
                var table_1,table_2;
                var type="<?= $_select ?>";
                table_1=$("#table_1").bootstrapTable('getSelections');
                table_2=$("#table_2").bootstrapTable('getSelections');
                // alert(table_1);
                // console.log(table_1.serialize());
                url="<?= $this -> Url -> build(['controller' => 'ajax', 'action' => 'security', 'FirewallPolicy', 'delFirewall_policy']); ?>";
                var iid=getUrlParam('id');
                var tid=getUrlParam('templateId');
                var basic_id;
                if(iid!=null&&iid!=""){
                    basic_id=iid;
                }else{
                    basic_id=tid;
                }
                    $.ajax({
                        url: url,
                        async: false,
                        data: {'table_1':table_1,'table_2':table_2,isEach:true,type:type,basic_id:basic_id},
                        method: 'post',
                        dataType: 'json',
                        success: function(e) {
                            e= $.parseJSON(e);
                            //操作成功
                            if(e.Code!="0"){
                                alert(e.Message);
                            }else{
                            }
                            refreshTable();
                            $("#fire-built").modal("hide");
                        }
                });
            });
});

$('#addfrom').bootstrapValidator({
    submitButtons: 'button[type="submit"]',
    submitHandler: function(validator, form, submitButton){

        var targetIp = $('#target_ip').val();
        var sourceIp = $('#rule-ip').val();
        var startPort = $('#startPort').val();
        var stopPort = $('#stopPort').val();
        var direction= $("#direction").val();
        var writtenIp = '0.0.0.0/0';
        var url,port;
        if (direction == "Ingress") {
            var sourceAddressNat = 'disable';
            var poolIP = '';
        } else {
            var sourceAddressNat = $("input[name='sourceAddressNat']:checked").val();
            var poolIP = $('#poolIP').val();
        }
        if(targetIp == ''){
            $('#target_ip').val(writtenIp);
        }
        if(sourceIp == ''){
            $('#rule-ip').val(writtenIp);
        }
        if(stopPort == ''){
            $('#stopPort').val(startPort);
            stopPort = $('#stopPort').val();
        }
        if(stopPort!=""||startPort!=null){
            port=startPort+"-"+stopPort;
        }else{
            port=startPort;
        }
        var templateId=getUrlParam('templateId');
        var id=getUrlParam('id');
        if(id!=null){
           $.post("/console/ajax/security/FirewallPolicy/isRepeatPolicyI",{
            "firewall_id":id,
            "protocol":$("#agreement").val(),
            "source_ip":sourceIp,
            "target_ip":targetIp,
            "portRange":port,
            "direction":$("#direction").val(),
            "sourceAddressNat" : sourceAddressNat,
            "poolIP" : poolIP
           },function(result){
            if(result=="true"){
                alert('规则已存在');
            }else{
                var type = "<?= $_select ?>";
                if(type =="firewall_template"){
                    url = "<?= $this -> Url -> build(['controller' => 'ajax', 'action' => 'security', 'FirewallPolicy', 'addTemplate_policy']); ?>";
                }else if(type =="firewall"){
                    url = "<?= $this -> Url -> build(['controller' => 'ajax', 'action' => 'security', 'FirewallPolicy', 'addFilewall_policy']); ?>";
                }
                $("#direction").removeAttr("disabled");
                    $.post(url, form.serialize(), function(e){
                        var e = $.parseJSON(e);
                        e = $.parseJSON(e);
                        if(e.Code!="0"){
                            alert(e.Message);
                        }else{
                            $("#fire-built").modal("hide");
                        }
                    });
                }
            });
        }else{
            $.post("/console/ajax/security/FirewallPolicy/isRepeatPolicyT",{
            "template_id":templateId,
            "protocol":$("#agreement").val(),
            "source_ip":sourceIp,
            "target_ip":targetIp,
            "portRange":port,
            "direction":$("#direction").val(),
            "sourceAddressNat" : sourceAddressNat,
            "poolIP" : poolIP
           },function(result){
            if(result=="true"){
                alert('规则已存在');
            }else{
                var type = "<?= $_select ?>";
                if(type =="firewall_template"){
                    url = "<?= $this -> Url -> build(['controller' => 'ajax', 'action' => 'security', 'FirewallPolicy', 'addTemplate_policy']); ?>";
                }else if(type =="firewall"){
                    url = "<?= $this -> Url -> build(['controller' => 'ajax', 'action' => 'security', 'FirewallPolicy', 'addFilewall_policy']); ?>";
                }
                $("#direction").removeAttr("disabled");
                    $.post(url, form.serialize(), function(e){
                        var e = $.parseJSON(e);
                        e = $.parseJSON(e);
                        if(e.Code!="0"){
                            alert(e.Message);
                        }else{
                            $("#fire-built").modal("hide");
                        }
                    });
                }
            });
        }
    },
    fields : {
        rule_name : {
            group: '.modal-form-group',
            validators : {
                notEmpty: {
                    message: '名称不能为空'
                }
            }
        },
        startPort : {
            group: '.modal-form-group',
            validators : {
                regexp : {
                    regexp : /^\+?[1-9][0-9]*$/,
                    message : '请填写正整数'
                },
                lessThan :{
                    value: 65536,
                    message : '端口值不能大于65535'
                }
            }
        },
        stopPort : {
            group: '.modal-form-group',
            validators : {
                regexp : {
                    regexp : /^\+?[1-9][0-9]*$/,
                    message : '请填写正整数'
                },
                lessThan :{
                    value: 65536,
                    message : '端口值不能大于65535'
                }
            }
        },
        source_ip : {
            group: '.modal-form-group',
            validators : {
                regexp: {
                    regexp: /((([1-9]?|1\d)\d|2([0-4]\d|5[0-5]))\.){3}(([1-9]?|1\d)\d|2([0-4]\d|5[0-5]))/,
                    message: '请填写正确的ip'
                }
            }
        },
        target_ip : {
            group: '.modal-form-group',
            validators : {
                regexp: {
                    regexp: /((([1-9]?|1\d)\d|2([0-4]\d|5[0-5]))\.){3}(([1-9]?|1\d)\d|2([0-4]\d|5[0-5]))/,
                    message: '请填写正确的ip'
                }
            }
        },
        poolIP : {
            group: '.modal-form-group',
            validators : {
                regexp: {
                    regexp: /((([1-9]?|1\d)\d|2([0-4]\d|5[0-5]))\.){3}(([1-9]?|1\d)\d|2([0-4]\d|5[0-5]))/,
                    message: '请填写正确的ip'
                }
            }
        }
    }
});


    //input 存在一个被选中状态

$(".table_0").on('all.bs.table.table', function (e, row, $element) {
    if ($("tbody input:checked").length >= 1) {
        $(".center .btn-default").attr('disabled', false);
    } else {
        $(".center .btn-default").attr('disabled', true);
    }
})


    function getUrlParam(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]); return null;
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
    var url="test/"+row.id;
    return '<a href="'+url+'">'+value+'</a>';
}

function formatter_direction(value,row,index){
    if(value=='Ingress'){
        return "下行规则";
    }else if(value=='Egress'){
        return "上行规则";
    }
}

// <th data-field="portRange" data-formatter=formatter_startport>起始端口</th>
//                         <th data-field="portRange" data-formatter=formatter_endport>结束端口</th>

function formatter_startport(value,row,index){
    if(value!=""&&value!=undefined){
        return value.split('-')[0];
    }else{
        return "-";
    }
}

function formatter_endport(value,row,index){
    if(value!=""&&value!=undefined){
        if(value.split('-')[1]!=undefined){
            return value.split('-')[1];
        }else{
            return value.split('-')[0];
        }
    }else{
        return "-";
    }
}
function formatter_status(value,row,index){
        if(value =="0"){
            return "正常"
        }else if (value =="1"){
            return "已失效"
        }else{
            return "未知"
        }
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

$('#table_1').contextMenu('context-menu', {
    bindings: {
        'modify': function(event) {
            //获取数据
            index=$(event).attr('data-index');
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table_1').bootstrapTable('getRowByUniqueId', uniqueId);
            //填充数据
            //TODO 根据bootstrap方法
            $('#modal-modify-name').val(row.rule_name);
            // $('#modal-modify-description').val(row.description);
            $('#modal-modify-id').val(row.id);
            $('#modal-modify').modal("show");

            //填充数据
            $('#sumbiter').one('click',
                function() {
                    $.ajax({
                        url: '<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'security','FirewallPolicy','edit']); ?>',
                        data: $('#modal-modify-form').serialize(),
                        method: 'post',
                        dataType: 'json',
                        success: function(e) {
                        //操作成功
                        if (e.code == '0000') {
                            $('#table_1').bootstrapTable('updateRow', {
                                index: index,
                                row: e.data
                            });
                            $('#modal-modify').modal("hide");
                            tentionHide('修改成功',0);
                        }else{
                            tentionHide('修改失败',1);
                        }
                    }
                });
            });
        },
        'modify_policy':function(event){
            index=$(event).attr('data-index');
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table_1').bootstrapTable('getRowByUniqueId', uniqueId);

            $("#rule-name").val(row.rule_name);
            $("#rule-ip").val(row.source_ip);
            $("#target_ip").val(row.target_ip);
            $("#protocol").val(row.protocol);
            $("#startPort").val(row.portRange.split('-')[0]);
            $("#stopPort").val(row.portRange.split('-')[1]);
            $("#direction").val(row.direction);
            $("#direction").attr("disabled","disabled");
            $("#action").val(row.action);
            $("#firewallRuleCode").val(row.polic_code);
            $("#txt_id").val(row.id);
            $("#firewall_policy_method").val("firewall_update_policy");
            $("#firewallPolicy-modal-title").html("修改规则");
            initIpSelectList();
            $("#fire-built").modal('show');
        },
        'dele': function(event) {
            var type="<?= $_select ?>";
            //获取数据
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table_1').bootstrapTable('getRowByUniqueId', uniqueId);
            $('#modal-info').html('确认要删除防火墙模板规则');
            $('#modal-dele-name').html(row.role_name);
            if(type=="firewall_template"){
                $('#modal-dele-code').val('');
            }else if(type=="firewall"){
                $('#modal-dele-code').val(row.polic_code);
            }
            $('#modal-dele').modal("show");
            $('#sumbiter-dele').attr('id', 'sumbiter-dele');
            $('#sumbiter-dele').one('click',
                function() {
                    var iid=getUrlParam('id');
                    var tid=getUrlParam('templateId');
                    var basic_id;
                    if(iid!=null&&iid!=""){
                        basic_id=iid;
                    }else{
                        basic_id=tid;
                    }
                //ajax提交页面
                $.ajax({
                    url: '<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'security','FirewallPolicy','delFirewall_policy']); ?>',
                    data:{
                        id:row.id,
                        basic_id:basic_id,
                        firewallRuleCode:$("#modal-dele-code").val(),
                        isEach:false,
                        type:type
                    },
                    method: 'post',
                    dataType: 'json',
                    success: function(e) {
                        $('#modal-dele').modal("hide");
                        e=$.parseJSON(e);
                        if(e.Code=="0"){
                            tentionHide('删除成功',0);
                        }else{
                            tentionHide('删除失败',1);
                        }
                        refreshTable();
                    }
                });
            });
        },



        //异常日志
        'excp':function(event){

            var uniqueId=$(event).attr('data-uniqueid');
            var row=$('#table_1').bootstrapTable('getRowByUniqueId',uniqueId);
            var department_id = 0;
            window.location.href = "/console/excp/lists/excp/firewall/"+department_id+'/all/0/0/'+row.firewall_id;

        },
        //正常日志
        'normal':function(event){
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table_1').bootstrapTable('getRowByUniqueId', uniqueId);
            var department_id = 0;
            window.location.href = "/console/excp/lists/normal/firewall/"+department_id+'/all/0/0/'+row.firewall_id;
        }
    }
});

$('#table_2').contextMenu('context-menu', {
    bindings: {
        'modify': function(event) {
            //获取数据
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table_2').bootstrapTable('getRowByUniqueId', uniqueId);
            //填充数据
            //TODO 根据bootstrap方法
            $('#modal-modify-name').val(row.rule_name);
            // $('#modal-modify-description').val(row.description);
            $('#modal-modify-id').val(row.id);
            $('#modal-modify').modal("show");

            //填充数据
            $('#sumbiter').one('click',
                function() {
                    $.ajax({
                        url: '<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'security','FirewallPolicy','edit']); ?>',
                        data: $('#modal-modify-form').serialize(),
                        method: 'post',
                        dataType: 'json',
                        success: function(e) {
                        //操作成功
                        if (e.code == '0000') {
                            $('#table_2').bootstrapTable('updateRow', {
                                index: $(event).data("index"),
                                row: e.data
                            });
                            $('#modal-modify').modal("hide");
                            tentionHide('修改成功',0);
                        }else{
                            tentionHide('修改失败',1);
                        }
                    }
                });
            });
        },
        'modify_policy':function(event){
            index=$(event).attr('data-index');
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table_2').bootstrapTable('getRowByUniqueId', uniqueId);

            $("#rule-name").val(row.rule_name);
            $("#rule-ip").val(row.source_ip);
            $("#target_ip").val(row.target_ip);
            $("#protocol").val(row.protocol);
            $("#startPort").val(row.portRange.split('-')[0]);
            $("#stopPort").val(row.portRange.split('-')[1]);
            $("#direction").val(row.direction);

            $("#direction").attr("disabled","disabled");
            $("#action").val(row.action);
            $("#firewallRuleCode").val(row.polic_code);
            $("#txt_id").val(row.id);
            $("#firewall_policy_method").val("firewall_update_policy");
            initIpSelectList();
            $("#firewallPolicy-modal-title").html("修改规则");
            $("#fire-built").modal('show');
        },
        'dele': function(event) {
            var type="<?= $_select ?>";
            //获取数据
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table_2').bootstrapTable('getRowByUniqueId', uniqueId);
            $('#modal-info').html('确认要删除防火墙模板规则');
            $('#modal-dele-name').html(row.role_name);
            if(type=="firewall_template"){
                $('#modal-dele-code').val('');
            }else if(type=="firewall"){
                $('#modal-dele-code').val(row.polic_code);
            }
            $('#modal-dele').modal("show");
            $('#sumbiter-dele').attr('id', 'sumbiter-dele');
            $('#sumbiter-dele').one('click',
                function() {
                    var iid=getUrlParam('id');
                    var tid=getUrlParam('templateId');
                    var basic_id;
                    if(iid!=null&&iid!=""){
                        basic_id=iid;
                    }else{
                        basic_id=tid;
                    }
                //ajax提交页面
                $.ajax({
                    url: '<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'security','FirewallPolicy','delFirewall_policy']); ?>',
                    data:{
                        id:row.id,
                        basic_id:basic_id,
                        firewallRuleCode:$("#modal-dele-code").val(),
                        isEach:false,
                        type:type
                    },
                    method: 'post',
                    dataType: 'json',
                    success: function(e) {
                        $('#modal-dele').modal("hide");
                        e=$.parseJSON(e);
                        if(e.Code=="0"){
                            tentionHide('删除成功',0);
                        }else{
                            tentionHide('删除失败',1);
                        }
                        refreshTable();
                    }
                });
            });
        },

        //异常日志
        'excp':function(event){
            var uniqueId=$(event).attr('data-uniqueid');
            var row=$('#table_2').bootstrapTable('getRowByUniqueId',uniqueId);
            var department_id = 0;
            window.location.href = "/console/excp/lists/excp/firewall/"+department_id+'/all/0/0/'+row.firewall_id;
        },
        //正常日志
        'normal':function(event){
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table_2').bootstrapTable('getRowByUniqueId', uniqueId);
            var department_id = 0;
            window.location.href = "/console/excp/lists/normal/firewall/"+department_id+'/all/0/0/'+row.firewall_id;
        }
    }
});

$("#fire-built").on('hide.bs.modal',function(){
    $("#direction").removeAttr("disabled");
});

function refreshTable() {
    var templateId=getUrlParam('templateId');
    if(templateId!=null){
        url= "/console/ajax/security/FirewallPolicy/lists?d=1&templateId=" + templateId;
        url1= "/console/ajax/security/FirewallPolicy/lists?d=2&templateId=" + templateId;
        $('#table_1').bootstrapTable('refresh',{url: url});
        $('#table_2').bootstrapTable('refresh',{url: url1});
    }else{
        var id=getUrlParam('id');
        url= "/console/ajax/security/FirewallPolicy/lists?d=1&id=" + id;
        url1= "/console/ajax/security/FirewallPolicy/lists?d=2&id=" + id;
        $('#table_1').bootstrapTable('refresh',{url: url});
        $('#table_2').bootstrapTable('refresh',{url: url1});
    }
    //$('#table').bootstrapTable('showLoading');
    //$('#table').bootstrapTable('hideLoading');
}

function notifyCallBack(value) {
    //console.log(value);
   var templateId=getUrlParam('templateId');
    if(templateId!=null){
        url= "/console/ajax/security/FirewallPolicy/lists?d=1&templateId=" + templateId;
        url1= "/console/ajax/security/FirewallPolicy/lists?d=2&templateId=" + templateId;
    }else{
        var id=getUrlParam('id');
        url= "/console/ajax/security/FirewallPolicy/lists?d=1&id=" + id;
        url1= "/console/ajax/security/FirewallPolicy/lists?d=2&id=" + id;
    }

    if (value.MsgType == "success" || value.MsgType == "error") {
        if (value.Data.method == "firewall_del_policy"||value.Data.method == "firewall_add_policy"||value.Data.method == "firewall_update_policy") {
        setTimeout(function() {
            $('#table_1').bootstrapTable('refresh',{url: url,silent: true});
            $('#table_2').bootstrapTable('refresh',{url: url1,silent: true});
            }, 3000);
        }
    }
}

//搜索绑定
$("#txtsearch").on('keyup',
function() {
    if (timer != null) {
        clearTimeout(timer);
    }
    var timer = setTimeout(function() {
        var search = $("#txtsearch").val();
            var templateId=getUrlParam('templateId');
            if(templateId!=null){
                $("#templateId").val(templateId);
                $("#edit_id").val(templateId);
                $('#table_1').bootstrapTable('refresh',{
                    url: "/console/ajax/security/FirewallPolicy/lists?d=1&templateId=" + templateId,
                    query: {
                    search: search
                    }
                });
                $('#table_2').bootstrapTable('refresh',{
                    url: "/console/ajax/security/FirewallPolicy/lists?d=2&templateId=" + templateId,
                    query: {
                    search: search
                    }
                });
            }else{
                var id=getUrlParam('id');
                $("#id").val(id);
                $("#edit_id").val(id);
                $('#table_1').bootstrapTable('refresh',{
                    url: "/console/ajax/security/FirewallPolicy/lists?d=1&id=" + id,
                    query: {
                    search: search
                    }
                });
                $('#table_2').bootstrapTable('refresh',{
                    url: "/console/ajax/security/FirewallPolicy/lists?d=2&id=" + id,
                    query: {
                    search: search
                    }
                });
            }
    },
    1000);
});

function ajaxFun(id, method) {
    heartbeat(0);
    $('#modal').modal("hide");
    if (method == "desktop_start") {
        tentionHide('启动云桌面', 0);
        state = 0;
    } else if (method == "desktop_stop") {
        tentionHide('停止云桌面', 1);
        state = 1;
    } else if (method == "desktop_reboot") {
        tentionHide('重启云桌面', 2);
        state = 2;
    }

    $.ajax({
        type: "post",
        url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','desktop','stopDesktop']); ?>",
        async: true,
        timeout: 9999999,
        data: {
            status: state,
            //启动or关闭
            type: method,
            ids: id
        },
        //dataType:'json',
        success: function(e) {
            e = $.parseJSON(e);
            //操作成功
            if (e.code == '0000') {
                $('#table').bootstrapTable('removeByUniqueId', $(event).data("uniqueid"));
                $('#modal-dele').modal("hide");
            } else {
                //操作失败
                alert(e.msg);
            }
            refreshTable();
        }
    });
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
        $('#agent_t').html('全部');
        $('#agent').html(agent_name);
        $('#agent').attr('val', class_code);
        var search= $("#txtsearch").val();
        $('#table').bootstrapTable('refresh', {
            url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'security','firewall','lists']); ?>",
            query: {class_code: class_code,search: search}
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
        query: {class_code2: class_code2,class_code:class_code,search: search}
    });
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


$(function(){
    /* 新建规则点击事件 */
    $(".modal-body .fire-right li").on('click',function(){
        var index = $(this).index();
        $(this).addClass('active').siblings('li').removeClass('active');
        var rule = [22,80,443,21,1194,3389,1723];
        var selected = ['TCP','TCP','TCP','TCP','UDP','TCP','TCP'];
        $("#startPort").val(rule[index]);
        $("#stopPort").val('');
        $("#agreement").val(selected[index]);
    })
});

$("target_ip").focus(function(){
    $(".selectWrapper").show();
});


jQuery.fn.selectEcs = function(targetId) {
    var _seft = this;
    var targetId = $(targetId);

    this.click(function(){
        // var A_top = $(this).offset().top + $(this).outerHeight(true);  //  1
        // var A_left =  $(this).offset().left;
        //targetId.bgiframe();
        if(_seft.attr('id') == "rule-ip" && targetId.hasClass("sourceIpTable")){
            targetId.show();
        }

        if(_seft.attr('id') == "target_ip" && targetId.hasClass("targetIpTable")){
            targetId.show();
        }
    });

    // targetId.find("#ecsTable :radio").click(function(){     
       
    //    console.log(row);
    //    // _seft.val( $(this).val() );
    //     //targetId.hide();
    // });

    $(document).click(function(event){
        if(!(event.target.id =='rule-ip' || event.target.id =='target_ip')){
              targetId.hide();  
        }
    });

    targetId.click(function(e){
        e.stopPropagation(); //  2
    });

    return this;
}
$("#target_ip").selectEcs("#selectItem");
$("#rule-ip").selectEcs("#selectItem");


$("#direction").change(function(){
    $("#rule-ip").val("");
    $("#target_ip").val("");
    initIpSelectList();
});

function initIpSelectList()
{
    var direction =  $("#direction").val();
    var id=getUrlParam('id');
    //下行规则--目标ip可选
    if(direction == "Ingress"){
        $('#ecsTable').bootstrapTable('refresh', {
            url: "/console/ajax/security/Firewall/getEcsListInFirewallVpc?d=2&direction=Ingress&firewall_id=" + id,
        });
        $("#selectItem").removeClass("sourceIpTable");
        $("#selectItem").addClass("targetIpTable");
        $("#select-btn-icon").removeClass("source-list");
        $("#select-btn-icon").addClass("target-list");

        $('#sourceAddressNat-div').css('display', 'none');
    //上行规则--源ip可选
    }else if(direction == "Egress"){
        $('#ecsTable').bootstrapTable('refresh', {
            url: "/console/ajax/security/Firewall/getEcsListInFirewallVpc?d=2&direction=Egress&firewall_id=" + id,
        });
        $("#selectItem").removeClass("targetIpTable");
        $("#selectItem").addClass("sourceIpTable");
        $("#select-btn-icon").removeClass("target-list");
        $("#select-btn-icon").addClass("source-list");

        $('#sourceAddressNat-div').css('display', 'block');
        
    }
    $("#poolIP-div").css('display', 'none');
    $("input[name='sourceAddressNat']:eq(1)").prop("checked",'checked');
    $("#poolIP").val('');
}


$("input[name=sourceAddressNat]").click(function(){
    isSourceAddressNat = $(this).val()
    if (isSourceAddressNat == 'enable') { //是

		if ($("#poolIP").val() == '' || $("#poolIP").val() == null) {
			$("input[name='sourceAddressNat']:eq(1)").prop("checked",'checked');
			$("#poolIP-div").css('display','none');
			alert("该机器没有绑定eip，不能源地址转换");
		} else {
	        $("#poolIP-div").css('display','block');
		}
        
    } else { // 否
        $("#poolIP-div").css('display','none');
    }
});


</script>
<?php
$this->end();
?>
