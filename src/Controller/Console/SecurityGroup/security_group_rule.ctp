<?= $this->element('security/left',['active_action'=>'security_group']); ?>
<style>
    .info{
        margin-right: 40px;
    }
    hr{
        border:1px #ddd solid;
    }
</style>
<div class="wrap-nav-right">
    <div class="wrap-manage">
        <div class="top">
            <span class="title pull-left">安全组规则管理</span>
            <a href="<?= $this->Url->build(['controller'=>'SecurityGroup','action'=>'index']); ?>" class="btn btn-addition pull-right">返回</a>
            <div style="clear: both"></div>
            <div id="maindiv-alert"></div>
        </div>

        <div class="center clearfix">
            <span class="info">安全组名:<?=$data['name'];?></span>
            <span class="info">安全组CODE:<?=$data['code'];?></span>
            <span class="info">所在VPC名:<?=$data['vpcName'];?></span>
            <span class="info">所在VPC CODE:<?=$data['vpc'];?></span>
            <span class="info">部署区位:<?=$data['location_name'];?></span>
            <div style="clear: both;"></div>
        </div>
        <br>
        <div class="center clearfix">
            <span>安全组规则列表</span>
            <hr>
        </div>

        <div class="clearfix center">
            <div class="pull-left">
            <a href="javascript:refreshTable();" id="btnRefresh" class="btn btn-addition">
                <i class="icon-refresh"></i>&nbsp;&nbsp;刷新
            </a>
            <a class="btn btn-addition" id="fire-built-btn" href="javascript:">
                <i class="icon-plus"></i>&emsp;<span>新建</span>
            </a>
            <button class="btn btn-default" onclick="" id="btnDel" disabled="disabled">
                <i class="icon-remove"></i>&emsp;<span>删除</span>
            </button>
            </div>
            <!--搜索框-->
            <div class="pull-right">
                 <span class="search pull-right">
                <input type="text" id="txtsearch" name="search" placeholder="搜索">
                  <i class="icon-search"></i>
              </span>
            </div>
            <div style="clear: both"></div>
        </div>
        <!--表格-->
        <div class="bot ">
            <p>下行规则</p>
            <table id="table_1" class="table_0">
                <thead>
                <tr>
                    <th data-checkbox="true"></th>
                    <th data-field="id">Id</th>
                    <th data-field="name">规则名称</th>
                    <th data-field="protocol">协议</th>
                    <th data-field="actionType">授权类型</th>
                    <th data-field="actionObject">操作对象</th>
                    <th data-field="portRange" data-formatter=formatter_startport>起始端口</th>
                    <th data-field="portRange" data-formatter=formatter_endport>结束端口</th>
                    <th data-field="direction" data-formatter=formatter_direction>通信方向</th>
                    <th data-field="action">规则操作</th>
                    <th data-field="status">状态</th>
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
                    <th data-field="id">Id</th>
                    <th data-field="name">规则名称</th>
                    <th data-field="protocol">协议</th>
                    <th data-field="actionType">授权类型</th>
                    <th data-field="actionObject">操作对象</th>

                    <th data-field="portRange" data-formatter=formatter_startport>起始端口</th>
                    <th data-field="portRange" data-formatter=formatter_endport>结束端口</th>
                    <th data-field="direction" data-formatter=formatter_direction>通信方向</th>
                    <th data-field="action">规则操作</th>
                    <th data-field="status">状态</th>
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

        <?php if (in_array('ccm_ps_interface_logs', $this->Session->read('Auth.User.popedomname'))) { ?>
        <li id="excp"><a  href="javascript:;"><i class="icon-book"></i> 异常日志</a></li>
        <?php } ?>
        <?php if (in_array('ccm_ps_interface_logs', $this->Session->read('Auth.User.popedomname'))) { ?>
        <li id="normal"><a  href="javascript:;"><i class="icon-book"></i> 正常日志</a></li>
        <?php } ?>
    </ul>
</div>

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
<!-- 修改 -->
<div class="modal fade" id="modal-modify" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title">修改安全组规则</h5>
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

                        <input type="hidden" name="id" id="id"/>

                        <div class="modal-form-group">
                            <label>通信方向</label>
                            <div class="form-group bk-select-group">
                                <select name="direction" id="direction">
                                    <option value="ingress" selected="selected">下行规则</option>
                                    <option value="egress">上行规则</option>
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
                            <label>授权类型:</label>
                            <div class="form-group">
                                <select name="action-type" id="action-type" onchange="changeActionType()">
                                    <option value="securitygroup" selected="selected">securitygroup</option>
                                    <option value="cidr">cidr</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-form-group" id="object_div" style="display:none">
                            <div class="clearfix">
                                <label>操作对象:</label>
                                <div class="form-group">
                                    <input name="source_ip" type="text" id="rule-ip" placeholder="0.0.0.0/0"><span class="text-danger rule-ip"></span>
                                </div>
                            </div>
                            <p class="content contentlist">源IP：默认值为0.0.0.0/0，表示对所有源IP开放，一般不作改动
                            </p>
                        </div>
                        <div class="modal-form-group">
                            <label>规则操作:</label>
                            <div class="form-group">
                                <select name="action" id="action" >
                                    <option value="accept" selected="selected">accept</option>
                                    <option value="decline">decline</option>
                                </select>
                            </div>
                        </div>

                        <!--<div class="modal-form-group" id="sourceAddressNat-div" >-->
                            <!--<div class="clearfix">-->
                                <!--<label>是否源地址转换:</label>-->
                                <!--<div class="form-group">-->
                                    <!--<input type="radio" name="sourceAddressNat" value="enable">是-->
                                    <!--<input type="radio" name="sourceAddressNat" value="disable"  checked="checked" >否-->
                                <!--</div>-->
                            <!--</div>-->
                        <!--</div>-->
                        <!--<div class="modal-form-group" id="poolIP-div" style="display: none;" disabled>-->
                            <!--<div class="clearfix">-->
                                <!--<label>源地址转换IP:</label>-->
                                <!--<div class="form-group">-->
                                    <!--<input name="poolIP" type="text" id="poolIP" placeholder="0.0.0.0" disabled style="color: #000"><span class="text-danger poolIP"></span>-->
                                <!--</div>-->
                            <!--</div>-->
                            <!--<p class="content contentlist">-->
                            <!--</p>-->
                        <!--</div>-->

                        <div class="modal-form-group">
                            <label>协议</label>
                            <div class="bk-select-group">
                                <select name="protocol" id="agreement">
                                     <option value="all">ALL</option>
                                    <option value="TCP">TCP</option>
                                    <option value="UDP">UDP</option>
                                    <option value="ICMP">ICMP</option>
                                    <option value="GRE">GRE</option>
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
<div class="modal fade" id="modal-msg" tabindex="-1" role="dialog"></div>
<?=$this->Html->script(['adminjs.js','validator.bootstrap.js','bootstrap-datetimepicker.js','jquery.cookie.js','bootstrap-paginator.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
//新建
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

//    function setFirewallpolicyIp(direction) {
//        var id=getUrlParam('id');
//        $.ajax({
//            url: url,
//            async: false,
//            data: {
//                'table_1': table_1,
//                'table_2': table_2,
//                isEach: true,
//                type: type,
//                basic_id: basic_id
//            },
//            method: 'post',
//            dataType: 'json',
//            success: function(e) {
//                e = $.parseJSON(e);
//                //操作成功
//                if (e.Code != "0") {
//                    alert(e.Message);
//                } else {}
//                refreshTable();
//                $("#fire-built").modal("hide");
//            }
//        });
//    }

    $("input[name=stopPort]").on('blur',function(){
        var startPort = parseInt($(this).parents(".modal-form-group").prev().find("input[name=startPort]").val());
        if($(this).val()<startPort && startPort < 65536){
            $(this).val(startPort);
        }
    });
    $("input[name=startPort]").on('blur',function(){
        var stopPort = parseInt($(this).parents(".modal-form-group").next().find("input[name=stopPort]").val());
        if($(this).val()>stopPort && $(this).val() < 65536 ){
            $(this).parents(".modal-form-group").next().find("input[name=stopPort]").val($(this).val());
        }
    });
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
                url: "/console/SecurityGroup/ruleLists?d=1&templateId=" + templateId,
                pagination:true,
                sidePagination:"server",
                locale: "zh-CN",
                uniqueId: "id",
                clickToSelect: "true"
            });
            $('#table_2').bootstrapTable({
                url: "/console/SecurityGroup/ruleLists?d=2&templateId=" + templateId,
                pagination:true,
                sidePagination:"server",
                locale: "zh-CN",
                uniqueId: "id",
                clickToSelect: "true"
            });
        }else{
            var id=getUrlParam('id');
            $("#id").val(id);
            $("#edit_id").val(id);
            $('#table_1').bootstrapTable({
                url: "/console/SecurityGroup/ruleLists?d=1&id=" + id,
                pagination:true,
                sidePagination:"server",
                locale: "zh-CN",
                uniqueId: "id",
                clickToSelect: "true"
            });
            $('#table_2').bootstrapTable({
                url: "/console/SecurityGroup/ruleLists?d=2&id=" + id,
                pagination:true,
                sidePagination:"server",
                locale: "zh-CN",
                uniqueId: "id",
                clickToSelect: "true"
            });
            $('#ecsTable').bootstrapTable({
                url: "/console/ajax/security/Firewall/getEcsListInFirewallVpc?d=2&direction=Ingress&firewall_id=" + id,
                pagination:true,
                sidePagination:"server",
                locale: "zh-CN",
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
        $('#modal-info').html('确认要删除选中安全组规则');
        $('#modal-dele').modal("show");
        $('#sumbiter-dele').attr('id', 'sumbiter-dele');
        $('#sumbiter-dele').one('click',function(){
            $('#modal-dele').modal("hide");
            var table_1,table_2;
            table_1=$("#table_1").bootstrapTable('getSelections');
            table_2=$("#table_2").bootstrapTable('getSelections');

            url="<?= $this -> Url -> build(['controller' => 'SecurityGroup', 'action' => 'delRules']); ?>";
            var iid=getUrlParam('id');
            var basic_id;
            if(iid!=null&&iid!=""){
                basic_id=iid;
            }
            $.ajax({
                url: url,
                async: false,
                data: {'table_1':table_1,'table_2':table_2,isEach:true,basic_id:basic_id},
                method: 'post',
                dataType: 'json',
                success: function(e) {
                    e= $.parseJSON(e);
                    //操作成功
                    if(e.Code!="0"){
                        made_modal('删除规则', e.msg);
                    }else{
                        made_modal('删除规则', e.msg);
                    }
                    refreshTable();
                }
            });
        });
    });

function changeActionType(){
    var type=$('#action-type').val();
    if(type=='cidr'){
        $('#rule-ip').val('');
        $('#object_div').css({display:'block'});
    }else{
        $('#rule-ip').val('');
        $('#object_div').css({display:'none'});
    }
}
    $('#addfrom').bootstrapValidator({
        submitButtons: 'button[type="submit"]',
        submitHandler: function(validator, form, submitButton){

            var sourceIp = $('#rule-ip').val();
            var startPort = $('#startPort').val();
            var stopPort = $('#stopPort').val();
            var direction= $("#direction").val();
            var writtenIp = '0.0.0.0/0';
            var url,port;

            if(sourceIp == ''){
                $('#rule-ip').val(writtenIp);
            }
            if(stopPort == ''){
                $('#stopPort').val(startPort);
                stopPort = $('#stopPort').val();
            }
            if(stopPort!=""||startPort!=null){
                port=startPort+"/"+stopPort;
            }else{
                port=startPort+"/"+startPort;
            }

            var id=getUrlParam('id');
            if(id!=null){
                $.post("/console/SecurityGroup/isRepeatRule",{
                    "basic_id":id,
                    "protocol":$("#agreement").val(),
                    "action_type":$('#action-type').val(),
                    "source_ip":sourceIp,
                    "portRange":port,
                    "direction":$("#direction").val(),

                },function(result){
                    if(result.code==1){
                        alert('规则已存在');
                    }else{
                            url = "<?= $this -> Url -> build(['controller' => 'SecurityGroup', 'action' => 'addRule']); ?>";

                        $.post(url, form.serialize(), function(e){
                            var e = $.parseJSON(e);
                            e = $.parseJSON(e);
                            refreshTable();
                            if(e.Code!="0"){
                                alert(e.Message);
                            }else{
                                $("#fire-built").modal("hide");
                                made_modal('新建规则','新建规则成功');
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

    function formatter_direction(value,row,index){
        if(value=='ingress'){
            return "下行规则";
        }else if(value=='egress'){
            return "上行规则";
        }
    }


    function formatter_startport(value,row,index){
        if(value!=""&&value!=undefined){
            return value.split('/')[0];
        }else{
            return "-";
        }
    }

    function formatter_endport(value,row,index){
        if(value!=""&&value!=undefined){
            if(value.split('/')[1]!=undefined){
                return value.split('/')[1];
            }else{
                return value.split('/')[0];
            }
        }else{
            return "-";
        }
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
                $('#modal-modify-name').val(row.name);
                // $('#modal-modify-description').val(row.description);
                $('#modal-modify-id').val(row.id);
                $('#modal-modify').modal("show");

                //填充数据
                $('#sumbiter').one('click',
                        function() {
                            $.ajax({
                                url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'SecurityGroup','action'=>'editRule']); ?>",
                                data: $('#modal-modify-form').serialize(),
                                method: 'post',
                                dataType: 'json',
                                success: function(e) {
                                    $('#modal-modify').modal("hide");
                                    //操作成功
                                    if (e.code == '0') {
                                        made_modal('修改安全组规则', e.msg);
                                    }else{
                                        made_modal('修改安全组规则', e.msg);
                                    }
                                }
                            });
                        });
            },
            'dele': function(event) {
                //获取数据
                var uniqueId = $(event).attr('data-uniqueid');
                var row = $('#table_1').bootstrapTable('getRowByUniqueId', uniqueId);
                $('#modal-info').html('确认要删除选中安全组规则');
                $('#modal-dele-name').html(row.role_name);

                $('#modal-dele').modal("show");
                $('#sumbiter-dele').attr('id', 'sumbiter-dele');
                $('#sumbiter-dele').one('click',
                        function() {
                            var iid=getUrlParam('id');
                            var basic_id;
                            if(iid!=null&&iid!=""){
                                basic_id=iid;
                            }
                            //ajax提交页面
                            $.ajax({
                                url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'SecurityGroup','action'=>'delRules']); ?>",
                                data:{
                                    id:row.id,
                                    basic_id:basic_id,
                                    isEach:false
                                },
                                method: 'post',
                                dataType: 'json',
                                success: function(e) {
                                    $('#modal-dele').modal("hide");
                                    e=$.parseJSON(e);
                                    if(e.Code=="0"){
                                        made_modal('删除规则', e.msg);
                                    }else{
                                        made_modal('删除规则', e.msg);
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
                window.location.href = "/console/excp/lists/excp/securityGroup/"+department_id+'/all/0/0/'+row.basic_id;

            },
            //正常日志
            'normal':function(event){
                var uniqueId = $(event).attr('data-uniqueid');
                var row = $('#table_1').bootstrapTable('getRowByUniqueId', uniqueId);
                var department_id = 0;
                window.location.href = "/console/excp/lists/normal/securityGroup/"+department_id+'/all/0/0/'+row.basic_id;
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
                $('#modal-modify-name').val(row.name);
                // $('#modal-modify-description').val(row.description);
                $('#modal-modify-id').val(row.id);
                $('#modal-modify').modal("show");

                //填充数据
                $('#sumbiter').one('click',
                        function() {
                            $.ajax({
                                url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'SecurityGroup','action'=>'editRule']); ?>",
                                data: $('#modal-modify-form').serialize(),
                                method: 'post',
                                dataType: 'json',
                                success: function(e) {
                                    //操作成功
                                    $('#modal-modify').modal("hide");
                                    refreshTable();
                                    if (e.code == '0') {
                                        made_modal('修改安全组规则', e.msg);
                                    }else{
                                        made_modal('修改安全组规则', e.msg);
                                    }
                                }
                            });
                        });
            },
            'dele': function(event) {
                //获取数据
                var uniqueId = $(event).attr('data-uniqueid');
                var row = $('#table_2').bootstrapTable('getRowByUniqueId', uniqueId);
                $('#modal-info').html('确认要删除选中安全组规则');
                $('#modal-dele').modal("show");
                $('#sumbiter-dele').attr('id', 'sumbiter-dele');
                $('#sumbiter-dele').one('click',
                        function() {
                            var iid=getUrlParam('id');
                            var basic_id;
                            if(iid!=null&&iid!=""){
                                basic_id=iid;
                            }
                            //ajax提交页面
                            $.ajax({
                                url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'SecurityGroup','action'=>'delRules']); ?>",
                                data:{
                                    id:row.id,
                                    basic_id:basic_id,
                                    isEach:false
                                },
                                method: 'post',
                                dataType: 'json',
                                success: function(e) {
                                    $('#modal-dele').modal("hide");
                                    e=$.parseJSON(e);
                                    if(e.Code=="0"){
                                        made_modal('删除规则', e.msg);
                                    }else{
                                        made_modal('删除规则', e.msg);
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
                window.location.href = "/console/excp/lists/excp/securityGroup/"+department_id+'/all/0/0/'+row.basic_id;
            },
            //正常日志
            'normal':function(event){
                var uniqueId = $(event).attr('data-uniqueid');
                var row = $('#table_2').bootstrapTable('getRowByUniqueId', uniqueId);
                var department_id = 0;
                window.location.href = "/console/excp/lists/normal/securityGroup/"+department_id+'/all/0/0/'+row.basic_id;
            }
        }
    });

    $("#fire-built").on('hide.bs.modal',function(){
        $("#direction").removeAttr("disabled");
    });

    function refreshTable() {
        var search = $("#txtsearch").val();
        var templateId=getUrlParam('templateId');
        if(templateId!=null){
           var url= "/console/SecurityGroup/ruleLists?d=1&templateId=" + templateId;
            var url1= "/console/SecurityGroup/ruleLists?d=2&templateId=" + templateId;
            $('#table_1').bootstrapTable('refresh',{url: url,query:{
              search:search
            }});
            $('#table_2').bootstrapTable('refresh',{url: url1,query:{
                search:search
            }});
        }else{
            var id=getUrlParam('id');
           var url= "/console/SecurityGroup/ruleLists?d=1&id=" + id;
           var url1= "/console/SecurityGroup/ruleLists?d=2&id=" + id;
            $('#table_1').bootstrapTable('refresh',{url: url,query:{
                search:search
            }});
            $('#table_2').bootstrapTable('refresh',{url: url1,query:{
                search:search
            }});
        }

    }


    //搜索绑定
    $("#txtsearch").on('keyup',
            function() {
                if (timer != null) {
                    clearTimeout(timer);
                }
                var timer = setTimeout(function() {
                            refreshTable();
                        },
                        1000);
            });


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

            if(_seft.attr('id') == "rule-ip" && targetId.hasClass("sourceIpTable")){
                targetId.show();
            }

            if(_seft.attr('id') == "target_ip" && targetId.hasClass("targetIpTable")){
                targetId.show();
            }
        });



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
    //弹框
    function made_modal(name, msg) {
        $("#modal-msg").empty();
        var html = '<div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header">' +
                '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                '<h5 class="modal-title">' + name + '</h5></div><div class="modal-body">' +
                '<i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;' + msg + '<span class="text-primary" ></span>' +
                '</div><div class="modal-footer"><button type="button" class="btn btn-danger" data-dismiss="modal">确认</button></div> </div> </div>';

        $('#modal-msg').append(html);
        $('#modal-msg').modal('show');
    }
</script>
<?= $this->end() ?>