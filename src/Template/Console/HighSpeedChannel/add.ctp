<!-- 主机 新增 -->

<?= $this->Html->script(['controller/HSCcontroller.js']); ?>

<div class="wrap-nav-right wrap-nav-right-left" ng-app>
    <div class="container-wrap  wrap-buy">
        <a href="<?= $this->Url->build(['controller'=>'BoundaryRouterList','action'=>'vbr']); ?>" class="btn btn-addition">返回边界路由器列表</a>
        <div class="clearfix buy-theme" ng-controller="HSCrouterListService">
            <div class="pull-left theme-left">
                <div class="">
                    <table>
                        <input type="hidden" value="<?= $goods_id ?>" id="txtgoods_id"></input>

                        <tr >
                            <td class="row-message-left">连接场景</td>
                            <td class="row-message-right" colspan="3">
                                <div class="ng-binding">
                                    <div class="clearfix">
                                        <label for="" class="pull-left ">连接场景:</label>
                                        <div class="bk-form-row-cell">
                                            <ul class="clearfix city">
                                                <li ng-repeat="router in routerList" ng-class="{active:$first}" ng-click="changeConnectScene(router)">
                                                    {{router.connectscene.label}}
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="row-message-left">本端配置</td>
                            <td class="row-message-right" colspan="3">
                                <div class="ng-binding">
                                    <div class="clearfix location-relative">
                                        <label for="" class="pull-left">厂商:</label>
                                        <div class="bk-form-row-cell">
                                            <ul class="clearfix city" ng-repeat="router in routerList ">
                                                <li ng-repeat="company in router.localconfig" ng-class="{active:$first}" ng-click="changeCompany(company)">
                                                    {{company.company.name}}
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="clearfix location-relative">
                                        <label for="" class="pull-left">地域:</label>
                                        <div class="bk-form-row-cell" >
                                            <ul class="clearfix city" >
                                                <li ng-repeat="area in currentCompany.area" ng-class="{active:$first}" ng-click="changeArea(area)">
                                                    {{area.name}}
                                                </li>
                                            </ul>
                                            <p><i class="icon-info-sign"></i>&nbsp;不同厂商之间的产品内网不互通；订购后不支持更换服务，请谨慎选择</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="ng-binding">
                                    <div class="clearfix">
                                        <label style="width:20%" for="" class="pull-left ">选择VPC:</label>
                                        <div style="width:80%" class="bk-form-row-cell">
                                            <div>
                                                <div>
                                                    <select ng-options="vpc.name for vpc in currentArea.vpc" ng-model="currentVpc" ng-init="vpc = currentArea.vpc[0]" ng-change="changeVpc(currentVpc)" id="vpc"></select>
                                                    <span class="text-danger" id="vpc-warning"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix">
                                        <label style="width:20%" for="" class="pull-left ">选择网络:</label>
                                        <div style="width:80%" class="bk-form-row-cell">
                                            <div>
                                                <select ng-options="subnet.name for subnet in currentVpc.subnet" ng-model="currentSubnet" ng-init="subnet = currentVpc.subnet[0]" ng-change="changeSubnetNet(currentSubnet)" id="subnet"></select>
                                                <span class="text-danger" id="net-warning"></span>
                                                <!--<p ng-repeat="net in vpc.net | filter: net.netCode : currentNet.netCode"><i class="icon-info-sign"></i>&nbsp;该子网使用{{net.isFusion}}技术</p>-->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix">
                                        <label for="" class="pull-left ">名称：</label>
                                        <div class="bk-form-row-cell">
                                        <input id="txtname" type="text" ng-change="changeRouterName(val)"  ng-model="val" name="textname" maxlength="15"/>
                                        <span id="txtRouterName" class="text-danger txtname"></span>
                                        </div>
                                    </div>
                                    <div class="clearfix">
                                        <label for="" class="pull-left ">备用路由器：</label>
                                        <div class="bk-form-row-cell">
                                            <input id="backup1" type="radio" ng-change="changeRouterStatus(routerStatus)"  ng-model="routerStatus" checked="checked" name="backup" value="1"/><label for="backup1">启用</label>
                                            <input id="backup2" type="radio" ng-change="changeRouterStatus(routerStatus)"  ng-model="routerStatus"  name="backup" value="0"/><label for="backup2">不启用</label>
                                            <span id="txtisRedundancy" class="text-danger "></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="row-message-left">对端配置</td>
                            <td class="row-message-right" colspan="3">
                                <div class="ng-binding">
                                    <div class="clearfix location-relative">
                                        <label for="" class="pull-left">厂商:</label>
                                        <div class="bk-form-row-cell">
                                            <ul class="clearfix city" ng-repeat="router in routerList ">
                                                <li ng-repeat="company in router.remoteconfig" ng-class="{active:$first}" ng-click="changeRemoteCompany(company)">
                                                    {{company.company.name}}
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="clearfix location-relative">
                                        <label for="" class="pull-left">地域:</label>
                                        <div class="bk-form-row-cell" >
                                            <ul class="clearfix city" >
                                                <li ng-repeat="area in currentRemoteCompany.area" ng-class="{active:$first}" ng-click="changeRemoteArea(area)">
                                                    {{area.name}}
                                                </li>
                                            </ul>
                                            <p><i class="icon-info-sign"></i>&nbsp;不同厂商之间的产品内网不互通；订购后不支持更换服务，请谨慎选择</p>
                                        </div>
                                    </div>
                                    <div class="clearfix">
                                        <label style="width:20%" for="" class="pull-left ">选择VPC:</label>
                                        <div style="width:80%" class="bk-form-row-cell">
                                            <div>
                                                <div>
                                                    <select ng-options="vpc.name for vpc in currentRemoteArea.vpc" ng-model="currentRemoteVpc" ng-init="vpc = currentArea.vpc[0]" ng-change="changeRemoteVpc(currentRemoteVpc)" id="vpc"></select>
                                                    <span class="text-danger" id="alivpc-warning"></span>
                                                </div>
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
                    <ul class="goods-detail" style="margin: 0">
                        <li>
                            <span class="deleft">连接场景：</span>
                            <span class="deright" >{{currentConnectScene.label}}</span>
                            <input id="connectScene" type="hidden" value="{{currentConnectScene.code}}"/>
                        </li>
                    </ul>
                    <p class="theme-buy">本端配置 </p>
                    <ul class="goods-detail">
                        <li>
                            <span class="deleft">厂商：</span>
                            <span class="deright">{{currentCompany.company.name}}</span>
                            <input id="txtcs" type="hidden" value="{{currentCompany.company.code}}" code="{{currentCompany.company.code}}" />
                        </li>
                        <li>
                            <span class="deleft">地域：</span>
                            <span class="deright">{{currentArea.name}}</span>
                            <input id="txtdy" type="hidden" value="{{currentArea.code}}" code="{{currentArea.code}}" />
                        </li>
                        <li>
                            <span class="deleft">网络：</span>
                            <span class="deright">{{currentSubnet.name}}</span>
                            <input id="subnetCode" type="hidden" value="{{currentSubnet.code}}" code="{{currentSubnet.code}}" />
                        </li>
                        <li>
                            <span class="deleft">名称：</span>
                            <span class="deright">{{routerName}}</span>
                            <input id="routerName" type="hidden"  value="{{routerName}}" code="{{routerName}}" />
                        </li>
                        <li>
                            <span class="deleft">备用状态：</span>
                            <span class="deright">{{routerStatusLabel}}</span>
                            <input id="isRedundancy" type="hidden" value="{{routerStatus}}" code="{{routerStatus}}" />
                        </li>
                    </ul>
                    <p class="theme-buy">对端配置 </p>
                    <ul class="goods-detail">
                        <li>
                            <span class="deleft">厂商：</span>
                            <span class="deright">{{currentRemoteCompany.company.name}}</span>
                            <input id="txtalics" type="hidden" value="{{currentRemoteCompany.company.code}}" code="{{currentRemoteCompany.company.code}}" />
                        </li>
                        <li>
                            <span class="deleft">地域：</span>
                            <span class="deright">{{currentRemoteArea.name}}</span>
                            <input id="txtalidy" type="hidden" value="{{currentRemoteArea.code}}" code="{{currentRemoteArea.code}}" />
                        </li>
                        <li>
                            <span class="deleft">VPC：</span>
                            <span class="deright">{{currentRemoteVpc.name}}</span>
                            <input id="alivpc" type="hidden" value="{{currentRemoteVpc.code}}" code="{{currentRemoteVpc.code}}" />
                        </li>
                    </ul>
                    <button id="btnBuy" class="btn btn-oriange">确认创建</button>
                </div>
            </div>
        </div>
    </div>



    <?php  $this->start('script_last'); ?>

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

        //        网络设置tab
        $(".network-tab").on('click', 'li', function() {
            $(this).addClass("active");
            $(this).siblings().removeClass("active");
            var $tabIndex = $(this).index();
            var $table = $(".network-box>div").eq($tabIndex);
            $table.show();
            $table.siblings().hide();
        });
        function loadSubnetPublic(vpc,host){
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
        });
        $('#btnBuy').on('click',function(){
            $(this).prop('disabled',true);
//            $.getJSON("/console/home/getUserLimit", function(data){
//                if(Number($("#txtcpu").val())*Number($('#txtnumber').val())+data.cpu_used > data.data.cpu_bugedt || (Number($("#txtrom").val())*Number($('#txtnumber').val())+data.memory_used) > data.data.memory_buget){
//                    alert("配额不足 \r\n cpu 配额："+ data.data.cpu_bugedt+" 已使用："+data.cpu_used+" \r\n 内存 配额：" + data.data.memory_buget+" 已使用："+data.memory_used);
//                    $(this).prop('disabled',false);
//                }else{
//
//                }
//            });
            addCar(false);
        });


        //添加清单
        function addCar(type){
            var goods_id= $("#txtgoods_id").val(); //id
            //router Name
            var routerName = $('#routerName').val();
            var isRedundancy = $('#isRedundancy').val();
            var alivpc = $('#alivpc').val();
            var subnet = $('#subnet').val();
            var re = /^\d+.*/gi;
            var validate = true;

            //获取商品配置信息
            if(type==true){
                type=1;
            }else{
                type=0;
            }

            if(subnet=='?'||subnet==undefined){
                $('#net-warning').html('请先去添加网络');
                validate =false;
            }else{
                $('#net-warning').html('');
            }
            if(routerName==''){
                $("#txtRouterName").html('请输入边界路由器名称');
                validate =false;
            }else if(re.test(routerName)){
                $("#txtRouterName").html('边界路由器名称不能以数字开头');
                validate =false;
            }else{
                $("#txtRouterName").html('');
            }

            if(isRedundancy==''){
                $("#txtisRedundancy").html('请选择边界路由器是否冗余');
                validate =false;
            }else{
                $("#txtisRedundancy").html('');
            }

//            if(alivpc==''){
//                $("#alivpc-warning").html('请选择对端vpc');
//                validate =false;
//            }else{
//                $("#alivpc-warning").html('');
//            }


            if(validate){
                $.ajax({
                    type:"post",
                    url:"/orders/addShoppingCar",
                    async:true,
                    data:{
                        is_console:1,
                        goods_id:goods_id,
                        attr:{
                            "name":$('#routerName').val(),
                            "isVbrRedundancy":$('#isRedundancy').val(),
                            "vpcCode":$('#alivpc').val(),
                            "subnetCode":$('#subnetCode').val(),
                            "regionCode":$("#txtdy").val(),
                            "token":"<?= $token?>"
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
    <?=$this->Html->script(['angular.min.js','jquery-ui-1.10.0.custom.min.js']); ?>
    <?php $this->end(); ?>