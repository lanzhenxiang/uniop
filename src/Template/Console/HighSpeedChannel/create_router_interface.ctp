<!-- 主机 新增 -->

<?= $this->Html->script(['controller/HSCcontroller.js']); ?>

<div class="wrap-nav-right wrap-nav-right-left" ng-app>
    <div class="container-wrap  wrap-buy">
        <a onclick="window.history.go(-1)" class="btn btn-addition">返回路由器接口列表</a>
        <div class="clearfix buy-theme" ng-controller="HSCrouterInterfaceService">
            <div class="pull-left theme-left">
                <div class="">
                    <table>
                        <input type="hidden" value="<?= $goods_id ?>" id="txtgoods_id"></input>

                        <tr >
                            <td class="row-message-left">计费方式</td>
                            <td class="row-message-right" colspan="3">
                                <div class="ng-binding">
                                    <div class="clearfix">
                                        <label for="" class="pull-left ">计费方式:</label>
                                        <div class="bk-form-row-cell">
                                            <ul class="clearfix city">
                                                <li ng-repeat="chargeType in routerList.priceList" ng-class="{active:$first}" ng-click="changeChargeType(chargeType)">
                                                    {{chargeType.label}}
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="clearfix">
                                        <label for="" class="pull-left ">计费周期:</label>
                                        <div class="bk-form-row-cell">
                                            <ul class="clearfix city">
                                                <li ng-repeat="cycle in currentChargeType.interval" ng-class="{active:$first}" ng-click="changeCycle(cycle)">
                                                    {{cycle.label}}
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr >
                            <td class="row-message-left">边界路由器</td>
                            <td class="row-message-right" colspan="3">
                                <div class="ng-binding">
                                    <div class="clearfix location-relative">
                                        <label for="" class="pull-left">接入场景:</label>
                                        <div class="bk-form-row-cell">
                                            <ul class="clearfix city">
                                                <li ng-class="{active:true}">
                                                    专线接入阿里云
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="clearfix location-relative">
                                        <label for="" class="pull-left">路由器:</label>
                                        <div class="bk-form-row-cell" >
                                            <ul class="clearfix city" >
                                                <li ng-class="{active:true}">
                                                    <?=$vbr_data['name']?>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="clearfix location-relative">
                                        <label for="" class="pull-left">部署区位:</label>
                                        <div class="bk-form-row-cell" >
                                            <ul class="clearfix city" >
                                                <li ng-class="{active:true}">
                                                    <?=$vbr_data['location_name']?>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="row-message-left">边界路由器接口</td>
                            <td class="row-message-right" colspan="3">

                                <div class="ng-binding">
                                    <div class="clearfix">
                                        <label for="" class="pull-left ">路由器接口名：</label>
                                        <div class="bk-form-row-cell">
                                            <input id="txtname" type="text" ng-change="changeRouterInterfaceName(val)"  ng-model="val" name="textname" maxlength="15"/>
                                            <span id="customName-warning" class="text-danger txtname"></span>
                                        </div>
                                    </div>
                                    <div class="clearfix">
                                        <label style="width:20%" for="" class="pull-left ">规格:</label>
                                        <div style="width:80%" class="bk-form-row-cell">
                                            <div>
                                                <select ng-options="spec.label for spec in routerList.spec" ng-model="currentSpec" ng-init="currentSpec = routerList.spec[0]" ng-change="changeSpec(currentSpec)" id="spec"></select>
                                                <span class="text-danger" id="spec-warning"></span>
                                                <p><i class="icon-info-sign"></i>&nbsp;
                                                    提示：新建成功后，底层会自动返回一对收和发的接口
                                                </p>
                                            </div>
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
                                        <label for="" class="pull-left">VPC名称:</label>
                                        <div class="bk-form-row-cell">
                                           <?php echo $vpc->name; ?> 
                                        </div>
                                    </div>
                                    <div class="clearfix location-relative">
                                        <label for="" class="pull-left">VPCCode:</label>
                                        <div class="bk-form-row-cell">
                                           <?php echo $vpc->code; ?> 
                                        
                                        </div>
                                    </div>
                                </div>
                            </td>
                            
                        </tr>
                        
<!--                         <tr> -->
<!--                             <td class="row-message-left">对端配置</td> -->
<!--                             <td class="row-message-right" colspan="3"> -->
<!--                                 <div class="ng-binding"> -->
<!--                                     <div class="clearfix location-relative"> -->
<!--                                         <label for="" class="pull-left">厂商:</label> -->
<!--                                         <div class="bk-form-row-cell"> -->
<!--                                             <ul class="clearfix city"> -->
<!--                                                 <li ng-repeat="company in routerList.remoteconfig" ng-class="{active:$first}" ng-click="changeRemoteCompany(company)"> -->
<!--                                                     {{company.company.name}} -->
<!--                                                 </li> -->
<!--                                             </ul> -->
<!--                                         </div> -->
<!--                                     </div> -->
<!--                                     <div class="clearfix location-relative"> -->
<!--                                         <label for="" class="pull-left">地域:</label> -->
<!--                                         <div class="bk-form-row-cell" > -->
<!--                                             <ul class="clearfix city" > -->
<!--                                                 <li ng-repeat="area in currentRemoteCompany.area" ng-class="{active:$first}" ng-click="changeRemoteArea(area)"> -->
<!--                                                     {{area.name}} -->
<!--                                                 </li> -->
<!--                                             </ul> -->
<!--                                             <p><i class="icon-info-sign"></i>&nbsp;不同厂商之间的产品内网不互通；订购后不支持更换服务，请谨慎选择</p> -->
<!--                                         </div> -->
<!--                                     </div> -->
<!--                                     <div class="clearfix"> -->
                                        <label style="width:20%" for="" class="pull-left ">选择VPC:</label>
                                        <div style="width:80%" class="bk-form-row-cell">
<!--                                             <div> -->
<!--                                                 <div> -->
<!--                                                     <select ng-options="vpc.name for vpc in currentRemoteArea.vpc" ng-model="currentRemoteVpc" ng-init="vpc = currentArea.vpc[0]" ng-change="changeRemoteVpc(currentRemoteVpc)" id="vpc"></select> -->
<!--                                                     <span class="text-danger" id="vpc-warning"></span> -->
<!--                                                 </div> -->
<!--                                             </div> -->
<!--                                         </div> -->
<!--                                     </div> -->
<!--                                 </div> -->
<!--                             </td> -->
<!--                         </tr> -->
                    </table>
                </div>
            </div>


            <div class="pull-right theme-right">
                <div class="theme-right-mian">
                    <p class="theme-buy">当前配置 </p>
                    <ul class="goods-detail" >
                        <li>
                            <span class="deleft">接入场景：</span>
                            <span class="deright" >专线接入阿里云</span>
                            <input id="connectScene" type="hidden" value="专线接入阿里云"/>
                        </li>
                        <li>
                            <span class="deleft">路由器：</span>
                            <span class="deright"><?=$vbr_data['name']?></span>
                            <input id="routerCode" type="hidden" value="<?=$vbr_data['code']?>" code="<?=$vbr_data['code']?>" />
                            <input id="basic_id" type="hidden" value="<?=$vbr_data['id']?>" code="<?=$vbr_data['id']?>" />
                        </li>
                        <li>
                            <span class="deleft">地域：</span>
                            <span class="deright"><?=$vbr_data['location_name']?></span>
                            <input id="txtdy" type="hidden" value="<?=$vbr_data['Agent']['region_code']?>" code="<?=$vbr_data['Agent']['region_code']?>" />
                        </li>
                        <li>
                            <span class="deleft">接口名称：</span>
                            <span class="deright">{{routerInterfaceName}}</span>
                            <input id="customName" type="hidden"  value="{{routerInterfaceName}}" code="{{routerInterfaceName}}" />
                        </li>
                        <li>
                            <span class="deleft">规格：</span>
                            <span class="deright">{{currentSpec.label}}</span>
                            <input id="specCode" type="hidden" value="{{currentSpec.code}}" code="{{currentSpec.code}}" />
                        </li>
                        <p class="theme-buy" style="font-size: 12px;">对端配置 </p>
                        <li>
                            <span class="deleft">厂商：</span>
                            <span class="deright">{{currentRemoteCompany.company.name}}</span>
                            <input id="txtcs" type="hidden" value="{{currentRemoteCompany.company.name}}" code="{{currentRemoteCompany.company.code}}" />
                        </li>
                        <li>
                            <span class="deleft">地域：</span>
                            <span class="deright">{{currentRemoteArea.name}}</span>
                            <input id="txtdy" type="hidden" value="{{currentRemoteArea.name}}" code="{{currentRemoteArea.code}}" />
                        </li>
                        <li>
                            <span class="deleft">VPC：</span>
                            <span class="deright"><?php echo $vpc->name; ?></span>
                        </li>
                        <li>
                            <span class="deleft">配置价格：</span>
                            <span id="textCurrentPrice" class="deright">￥0.0</span>
                            <input id="currentPrice" type="hidden" value="0" code="0" />
                            <input id="chargeMode" type="hidden" value="{{currentChargeType.code}}" code="{{currentChargeType.code}}" />
                            <input id="chargeInterval" type="hidden" value="{{currentChargeInterval.code}}" code="{{currentChargeInterval.code}}" />
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

        function getRouterInterfacePrice(chargeType,spec){
            layer.load(0, {shade: false}); //0代表加载的风格，支持0-2
            $.ajax({
                type:"post",
                url:"/console/HighSpeedChannel/getRouterInterfacePrice",
                async:true,
                data:{
                    regionCode:"<?=$vbr_data['Agent']['region_code']?>",
                    chargeType:chargeType,
                    spec:spec
                },
                dataType:'json',
                success: function (response) {
                    if(response.code != 0){
                        layer.msg(response.msg);
                    }else{
                        price = response.data.totalPrice;
                        priceTxt = "￥"+ price + response.data.priceTxt;
                        $("#textCurrentPrice").html(priceTxt);
                        $("#currentPrice").val(price);
                    }
                    layer.closeAll('loading');
                }
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
            var customName = $('#customName').val();
            var spec = $('#specCode').val();
            var routerCode = $('#routerCode').val();
            var oppositeVpcCode = "<?php echo $vpc->code;?>";

            var validate = true;

            //获取商品配置信息
            if(type==true){
                type=1;
            }else{
                type=0;
            }
            console.log(oppositeVpcCode);
            if(oppositeVpcCode==''||oppositeVpcCode==undefined){
                $('#vpc-warning').html('阿里云vpc不存在');
                validate =false;
            }else{
                $('#vpc-warning').html('');
            }
            if(customName==''||customName==undefined){
                $('#customName-warning').html('请填写边界路由器接口名称');
                validate =false;
            }else{
                $('#customName-warning').html('');
            }
            if(spec=='' ||spec==undefined){
                $("#spec-warning").html('请输入选择规格');
                validate =false;
            }else{
                $("#spec-warning").html('');
            }

            if(validate){
                $.ajax({
                    type:"post",
                    url:"/orders/addShoppingCar",
                    async:true,
                    data:{
                        is_console:1,
                        goods_id:goods_id,
                        attr:{
                            "customName":$("#customName").val(),
                            "spec":$("#specCode").val(),
                            "routerCode":$("#routerCode").val(),
                            "oppositeVpcCode": oppositeVpcCode,
                            "basicId":$("#basic_id").val(),
                            "charge_mode":$("#chargeMode").val(),
                            "interval":$("#chargeInterval").val(),
                            "price":$("#currentPrice").val(),
                            "real_price":$("#currentPrice").val(),
                            "regionCode":"<?=$vbr_data['Agent']['region_code']?>",
                            "vbr_id" : "<?= $vbr_id?>",
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