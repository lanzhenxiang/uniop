<!-- 主机 新增 -->

<?= $this->Html->script(['controller/controller.js']); ?>

<div class="wrap-nav-right wrap-nav-right-left" ng-app>
    <div class="container-wrap  wrap-buy">
    <a href="<?= $this->Url->build(['controller'=>'business','action'=>'lists','hosts']); ?>" class="btn btn-addition">返回业务系统</a>
        <div class="clearfix buy-theme" ng-controller="hostListService">
            <div class="pull-left theme-left">
                <div class="">
                <table>
                <input type="hidden" value="<?= $goods_id ?>" id="txtgoods_id"></input>

                    <tr >
                            <td class="row-message-left">计费周期</td>
                            <td class="row-message-right" colspan="3">
                                <div class="ng-binding">
                                    <div class="clearfix">
                                        <label for="" class="pull-left ">计费周期:</label>
                                        <div class="bk-form-row-cell">
                                            <ul class="clearfix city" id="selbillCycle" name="selbillCycle" >
                                                <?php foreach ($chargeList as $key => $value) { ?>
                                                    <?php if(!empty($value)){ ?>
                                                        <?php if($key == 1){ ?>
                                                            <li class="active" data-val="<?= $key ?>"><?= $value ?></li>
                                                        <?php }else{ ?>
                                                            <li class="" data-val="<?= $key ?>"><?= $value ?></li>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </td>
                    </tr>
                    <tr>
                        <td class="row-message-left">部署区位</td>
                        <td class="row-message-right" colspan="3">
                            <div class="ng-binding">
                                <div class="clearfix location-relative">
                                    <label for="" class="pull-left">厂商:</label>
                                    <div class="bk-form-row-cell">
                                        <ul class="clearfix city">
                                            <li ng-repeat="host in hostList" ng-class="{active:$first}" ng-click="changeHost(host)">
                                                {{host.company.name}}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="clearfix location-relative">
                                    <label for="" class="pull-left">地域:</label>
                                    <div class="bk-form-row-cell">
                                        <ul class="clearfix city" ng-repeat="host in hostList | filter: { company.name:currentCompany.name} :true">
                                            <li ng-repeat="area in host.area" ng-class="{active:$first}" ng-click="changeArea(area)">
                                                {{area.name}}
                                            </li>
                                        </ul>
                                        <p><i class="icon-info-sign"></i>&nbsp;不同厂商之间的产品内网不互通；订购后不支持更换服务，请谨慎选择</p>
                                   </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr >
                            <td class="row-message-left">网络设置</td>
                            <td class="row-message-right">
                                <div class="ng-binding">

                                    <div class="clearfix network-tab">
                                        <label for="" class="pull-left"></label>
                                        <div class="bk-form-row-cell">
                                            <ul class="clearfix city">
                                                <li id="subnet_default" class="active">默认网络 </li>
                                                <li id="subnet_extend_menu" class="hide">扩展网络</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="network-box">
                                        <!--默认-->
                                        <div class="">
                                            <div class="clearfix">
                                                <label style="width:20%" for="" class="pull-left ">VPC:</label>
                                                <div style="width:80%" class="bk-form-row-cell">
                                                    <div ng-repeat="host in hostList | filter: { company.name : currentCompany.name} :true">
                                                        <div ng-repeat="area in host.area | filter: {areaCode : currentArea.areaCode, name : currentArea.name}">
                                                            <select class="select-style" ng-options="vpc.name for vpc in area.vpc" ng-model="vpc" ng-init="vpc = area.vpc[0]" ng-change="changeVpc(vpc)" id="vpc"></select>
                                                            <span class="text-danger" id="vpc-warning"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="clearfix">
                                                <label style="width:20%" for="" class="pull-left ">子网:</label>
                                                <div style="width:80%" class="bk-form-row-cell">
                                                    <div ng-repeat="host in hostList | filter: { company.name : currentCompany.name} :true">
                                                        <div ng-repeat="area in host.area | filter: {areaCode : currentArea.areaCode, name : currentArea.name}">
                                                            <div ng-repeat="vpc in area.vpc | filter: {vpCode : currentVpc.vpCode}">
                                                                <select class="select-style" ng-options="net.name for net in vpc.net" ng-model="net" ng-init="net = vpc.net[0]" ng-change="changeNet(net)" id="net"></select>
                                                                <span class="text-danger" id="net-warning"></span>
                                                                <p ng-repeat="net in vpc.net | filter: net.netCode : currentNet.netCode"><i class="icon-info-sign"></i>&nbsp;该子网使用{{net.isFusion}}技术</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--扩展-->
                                        <div class="bk-form-row-cell " >
                                            <table id="subnet-extend-table" data-toggle="table"
         data-pagination="true"
         data-side-pagination="server"
         data-locale="zh-CN"
         data-click-to-select="true"
         data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'hosts', 'getPublicSubnetExtend']); ?>"
         data-unique-id="id"  class="network-table">
                                                <thead>
                                                <tr>
                                                    <th data-checkbox="true"></th>
                                                    <th data-field="vpc_name">VPC</th>
                                                    <th data-field="subnet_name">子网</th>
                                                </tr>
                                                </thead>
                                            </table>
                                            <p><i class="icon-info-sign"></i>&nbsp;通过勾选设置主机要连接的网络。</p>
                                        </div>
                                    </div>
                                </div>
                            </td>
                    </tr>
                    <tr >
                        <td class="row-message-left">业务模板</td>
                        <td class="row-message-right" colspan="3">
                            <div class="ng-binding">
                                <div class="clearfix">
                                    <label for="" class="pull-left ">模板:</label>
                                      <div class="bk-form-row-cell">
                                        <select class="select-style" id="business-template">
                                            <option value="0">未选择</option>
                                            <?php foreach($templates as $value): ?>
                                                <?php if($value['business_template_detail'] != null): ?>
                                                <option value="<?=$value['biz_tid']?>" ><?=$value['biz_temp_name']?></option>
                                                <?php endif;?>
                                            <?php endforeach;?>
                                        </select>
                                        <span class="text-danger" id="template-warning"></span>
                                      </div>
                                </div>
                                <div id="template-detail" class="hide">
                                    <div class="clearfix">
                                        <label for="" class="pull-left ">版本:</label>
                                          <div class="bk-form-row-cell">
                                            <span id="version-txt"></span>
                                            <span class="text-danger txtnum"></span>
                                          </div>
                                    </div>
                                    <div class="clearfix">
                                        <label for="" class="pull-left ">系统规模:</label>
                                          <div class="bk-form-row-cell">
                                            <span id="level-txt"></span>
                                            <span class="text-danger txtnum"></span>
                                          </div>
                                    </div>
                                    <div class="clearfix">
                                        <label for="" class="pull-left ">配置清单:</label>
                                        <div class="bk-form-row-cell " >
                                                <table id="biz-template-detail-table" data-toggle="table"
                                                     data-pagination="true"
                                                     data-side-pagination="server"
                                                     data-locale="zh-CN"
                                                     data-click-to-select="true"
                                                     data-url=""
                                                     data-unique-id="id"  class="network-table">
                                                    <thead>
                                                    <tr>
                                                        <th data-field="tagname">配置项</th>
                                                        <th data-field="instance_name">计算性能</th>
                                                        <th data-field="image_name">OS版本</th>
                                                        <th data-field="number">数量</th>
                                                    </tr>
                                                    </thead>
                                                </table>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        </td>
                    </tr>
                </table>
                </div>
            </div>


            <div class="pull-right theme-right">
                <div class="theme-right-mian">
                    <p class="theme-buy">当前配置 </p>
                    <ul class="goods-detail">
                        <li>
                            <span class="deleft">计费周期：</span>
                            <span class="deright" id="span_billCycle">按天计费</span>
                            <input id="txtbillCycle" type="hidden" value="1"/>
                        </li>
                        <li>
                            <span class="deleft">厂商：</span>
                            <span class="deright">{{currentCompany.name}}</span>
                            <input id="txtcs" type="hidden" value="{{currentCompany.name}}" code="{{currentCode}}" />
                        </li>
                        <li>
                            <span class="deleft">地域：</span>
                            <span class="deright">{{currentArea.name}}</span>
                            <input id="txtdy" type="hidden" value="{{currentArea.name}}" code="{{currentAreaCode}}" />
                        </li>
                        <li>
                            <span class="deleft">网络：</span>
                            <span class="deright">{{currentNet.name}}</span>
                            <input id="txtnet" type="hidden" value="{{currentNet.name}}" code="{{currentNet.netCode}}" />
                        </li>
                        <li>
                            <span class="deleft">添加网络：</span>
                            <span id="txtnetn22" class="deright" ></span>
                            <input id="txtnet22" type="hidden" value="" code="" />
                        </li>
                        <li>
                            <span class="deleft">业务模板：</span>
                            <span id="template-txt" class="deright" ></span>
                            <input id="biz_tid" type="hidden" value="" code="" />
                        </li>
                    </ul>
                    <input id="txtcpu" type="hidden" value="" code="" />
                    <input id="txtrom" type="hidden" value="" code="" />
                    <button id="btnBuy" class="btn btn-oriange">确认创建</button>
                </div>
            </div>
        </div>
    </div>



<?php  $this->start('script_last'); ?>
<?=$this->Html->script(['angular.min.js','controller/controller.js','jquery-ui-1.10.0.custom.min.js']); ?>
<script type="text/javascript">
function setSubnet(){
    $("#txtnet2").html($("#net2").find("option:selected").text());
    $("#txtnet22").val($("#net2").val());
    $("#txtnetn22").html($("#net2").val());
}
$("#subnet-extend-table").on('all.bs.table', function(e, row, $element) {
    var subnet_name = getRowId('name');
    var subnet_code = getRowId('code');
    $("#txtnet22").val(subnet_code);
    $("#txtnetn22").html(subnet_name);
})

function getRowId(type){
    var idlist = '';
    $("input[name='btSelectItem']:checkbox").each(function() {
        if ($(this)[0].checked == true) {
            //alert($(this).val());
            var id = $(this).parent().parent().attr('data-uniqueid');
            var row = $('#subnet-extend-table').bootstrapTable('getRowByUniqueId', id);
            var delimiter = idlist =="" ? '':',';
            if (type == 'code') {
                idlist += delimiter + row.subnet_code;
            } else if (type == "name") {
                idlist += delimiter + row.subnet_name;
            } else {
                idlist += delimiter + row.id;
            }
        }
    });
    return idlist;
}

$('#business-template').on('change',function(){
    var biz_tid = $(this).find("option:selected").val();
    if(biz_tid == 0){
        $("#template-detail").addClass('hide');
        $('#template-txt').html('');
        $('#template').val('');
        return;
    }
    $.ajax({
        type:"post",
        url:"/console/ajax/business/hosts/getBusinessTemplateDetail",
        async:true,
        data:{
            biz_tid:biz_tid,
        },
        success: function (data) {
            data= $.parseJSON(data);
            $('#version-txt').html(data.version);
            $('#level-txt').html(data.system_level);
            $('#template-txt').html(data.biz_temp_name);
            $('#biz_tid').val(data.biz_tid);
            refreshTable(biz_tid);
            $("#template-detail").removeClass('hide');
        }
    });
});
//刷新模板资源清单列表
function refreshTable(biz_tid){
    $('#biz-template-detail-table').bootstrapTable('refresh', {
            url :　"<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'business','hosts','getTemplateDetail']); ?>?biz_tid=" + biz_tid
    });
}
function initSubnetExtends(obj){
    var subnet_code = obj.netCode;
    $.ajax({
        type:"post",
        url:"/console/ajax/network/hosts/ajaxExtendNetCardAllow",
        async:true,
        data:{
            subnet_code:subnet_code,
        },
        success: function (data) {
            data= $.parseJSON(data);
            $("input[name='btSelectItem']:checkbox").each(function() {
                if ($(this)[0].checked == true) {
                    $(this)[0].checked = false;
                }
            });
            $("input[name='btSelectAll']:checkbox")[0].checked = false;
            $('#subnet_default').click();
            $("#txtnet22").val('');
            $("#txtnetn22").html('');
            if(data.allow == true){
                $('#subnet_extend_menu').removeClass('hide');
            }else{
                $('#subnet_extend_menu').addClass('hide');
            }
        }
    });
}

//网络设置tab
$(".network-tab").on('click', 'li', function() {
    $(this).addClass("active");
    $(this).siblings().removeClass("active");
    var $tabIndex = $(this).index();
    var $table = $(".network-box>div").eq($tabIndex);
    $table.show();
    $table.siblings().hide();
});
function loadSubnetPublic(vpc,host =""){
    var h="";

    var h2="";
    vpc_txt = $("#vpc2  option:selected").text();
    if(vpc==''||vpc==null){
        vpc='';
    }
    //$("#vpc2").html("<option value=''>不扩展</option>");
    // var date = new Date();
    if(host == 'aliyun'){
        //initVpc2();
    } else {
        $.ajax({
            type: "post",
            url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'hosts', 'loadSubnetPublic']); ?>" + "?d=" + Date.parse(new Date()),
            data: {
                vpc: vpc
            },
            async: false,
            cache: false,
            success: function(data) {
                data = $.parseJSON(data);

                // console.log(data.vpc);

                h += "<option value=''>不扩展</option>";
                $.each(data.vpc, function(i, n) {
                    if (i == 0) {
                        //<option value="4">$vpc$_822</option><option value="5">vpc[822]_fox</option><
                        h += "<option value='" + n.code + "' selected=\"selected\">" + n.name + "</option>";
                    } else {
                        h += "<option value='" + n.code + "'>" + n.name + "</option>";
                    }
                });
                if (vpc != "") {
                    $("#vpc2").html(h);
                }

                vpc = $("#vpc2").val();
                if (vpc_txt != "不扩展") {
                    $.each(data.vpc, function(i, n) {
                        if (n.code == vpc) {
                            $.each(n.subnet, function(x, y) {
                                if (i == x) {
                                    h2 += "<option value='" + y.code + "' selected=\"selected\">" + y.name + "</option>";
                                } else {
                                    h2 += "<option value='" + y.code + "'>" + y.name + "</option>";
                                }
                            })
                        };
                    });

                    $("#net2").html(h2);
                } else {
                    h2 += "<option value=''></option>";
                    $("#net2").html(h2);
                }
                // alert(data)
            }
        });
    }
    $("#txtnet2").html($("#net2").find("option:selected").text());
    $("#txtnet22").val($("#net2").val());
}

function changebillCycle(){
    var id = $("#selbillCycle").val();
    var val = $("#selbillCycle").find("option:selected").text();
    $("#txtbillCycle").val(id);
    $("#span_billCycle").html(val);
}
$("#selbillCycle").on('click','li',function(){
            var id = $(this).attr("data-val");
            var val = $(this).text();
            $("#txtbillCycle").val(id);
            $("#span_billCycle").html(val);
        })
$('#btnBuy').on('click',function(){
    $(this).prop('disabled',true);
    $.getJSON("/console/home/getUserLimit", function(data){
        //计算选择的模板的所使用的cpu数量和内存大小
        var rows = $('#biz-template-detail-table').bootstrapTable('getData');
        var cpu_total = 0;
        var rom_total = 0;
        rows.forEach(function(e){
            cpu_total += Number(e.cpu_sub_total);
            rom_total += Number(e.rom_sub_total);
        })

        if(cpu_total+data.cpu_used > data.data.cpu_bugedt || rom_total+data.memory_used > data.data.emory_buget){
            alert("配额不足 \r\n cpu 配额："+ data.data.cpu_bugedt+" 已使用："+data.cpu_used+" \r\n 内存 配额：" + data.data.memory_buget+" 已使用："+data.memory_used);
            $(this).prop('disabled',false);
        }else{
            addCar(false);
        }
    });
});


//添加清单
function addCar(type){
    var goods_id= $("#txtgoods_id").val(); //id
    var txtpwd1 = $('#txtpwd1').val();
    var txtpwd2 = $('#txtpwd2').val();
    var txtname = $('#txtname').val();
    var biz_tid = $('#biz_tid').val();
    var vpc = $('#vpc').val();
    var net = $('#net').val();
    var net2 = $('#net2').val();
    var validate = true;

    //获取商品配置信息
    if(type==true){
        type=1;
    }else{
        type=0;
    }

    if(vpc=='?'||vpc==undefined){
        $('#vpc-warning').html('请先去添加VPC');
        validate =false;
    }else{
        $('#vpc-warning').html('');
    }
    if(net=='?'||net==undefined){
        $('#net-warning').html('请先去添加网络');
        validate =false;
    }else{
        $('#net-warning').html('');
    }
    if(txtname==''){
        $(".txtname").html('请输入名称');
        validate =false;
    }else{
        $(".txtname").html('');
    }
    if(biz_tid==''){
        $('#template-warning').html('请选择一个业务模板');
        validate = false;
    }else{
        $('#template-warning').html('');
    }

    if(validate){
        $.ajax({
        type:"post",
        url:"/orders/addTemplateShoppingCar",
        async:true,
        data:{
            is_console:1,
            goods_id:goods_id,
            biz_tid:biz_tid,
            attr:{
                    "ecsName":$("#txtname").val(),
                    "imageCode":$("#txtimage").attr("code"),
                    "bandwidth":0,
                    "vpcCode":'',
                    "subnetCode":$("#txtnet").attr("code"),
                    "instanceTypeCode":$("#txtypecode").val(),
                    "regionCode":$("#txtdy").attr("code"),
                    "csName":$("#txtcs").val(),
                    "csCode":$("#txtcs").attr("code"),
                    "dyName":$("#txtdy").val(),
                    "dyCode":$("#txtdy").attr("code"),
                    "cpu":$("#txtcpu").val(),
                    "rom":$("#txtrom").val(),
                    "netName":$("#txtnet").val(),
                    "netCode":$("#txtnet").attr("code"),
                    "imageName":$("#txtimage").val(),
                    "number":$("#txtnumber").val(),
                    "month":$("#txtmonth").val(),
                    "billCycle":$("#txtbillCycle").val(),
                    "subnetCode2":$("#txtnet22").val()
                },
            type:type
        },
        success: function (data) {
            data= $.parseJSON(data);
            if(type==true){
                $("#number").html(data.number);
            }else{
                if(data.Code=="0"){
                    setTimeout(function() {
                            window.location.href=data.url;
                        }, 1000);
                }else{
                    alert(data.Message);
                }
            }

        }
        });
    }else{
        $('#btnBuy').prop('disabled',false);
    }
}
//新建 右边固定框
var offsetTop = $(".theme-right").offset().top;
var width = $(".buy-theme").width() * 0.24;
$(window).scroll(
    function(){
        if($(document).scrollTop() > offsetTop - 60){
            var offsetLeft = $(".theme-right").offset().left;
            $(".theme-right").css({position:"fixed",top:"60px",left:offsetLeft,width:width});
        }else{
            $(".theme-right").css("position","static");
        }
    }
);
</script>

<?php $this->end(); ?>