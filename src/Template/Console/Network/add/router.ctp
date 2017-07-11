<?= $this->element('network/lists/left',['active_action'=>'router']); ?>
<?= $this->Html->css(['common.css']); ?>
<?= $this->Html->script(['controller/controller.js']); ?>
<div class="wrap-nav-right " ng-app>
    <div class="container-wrap  wrap-buy">
        <a href="<?= $this->Url->build(['controller'=>'network','action'=>'lists','router']); ?>" class="btn btn-addition">返回路由器列表</a>
        <!-- <div class="y-tab">
            <ul>
                <li class="y-first y-current">
                    <a href="##" class="y-item">包年包月</a>
                </li>
                <li class="">
                    <a href="##" class="y-item">按量付费</a>
                </li>
            </ul>
        </div> -->
        <input type="hidden" value="<?= $goods_id ?>" id="txtgoods_id"/>
        <div class="clearfix buy-theme ng-scope" ng-controller="routerListService">
            <div class="pull-left theme-left">
                <table>
                    <tbody>
                        <tr >
                            <td class="row-message-left">计费周期</td>
                            <td class="row-message-right" colspan="3">
                            <div class="ng-binding">
                                <div class="clearfix location-relative">
                                    <label for="" class="pull-left">计费周期:</label>
                                    <div class="bk-form-row-cell">
                                            <ul class="clearfix city">
                                            <li ng-repeat="price in priceList" ng-class="{active:currentPriceId== price.id}" ng-init="price = currentPrice" ng-click="changePrice(price)">
                                                {{price.name}}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </tr>
                        <tr>
                            <td class="row-message-left">部署区位</td>
                            <td class="row-message-right">
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
                                            <ul class="clearfix city" ng-repeat="host in hostList | filter: { company.name:currentCompany.name}">
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
                        <tr>
                            <td class="row-message-left">基本设置</td>
                            <td class="row-message-right">
                                <div class="ng-binding">
                                    <div class="clearfix">
                                        <label for="" class="pull-left ">路由器名：</label>
                                        <div class="bk-form-row-cell">
                                            <input type="text" id="router-name" name="txtname" ng-model="router" ng-init="router=''">
                                            <span class="text-danger router-name"></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <tr>
                        <td class="row-message-left">VPC</td>
                        <td class="row-message-right">
                            <div class="ng-binding">
                                <div class="clearfix">
                                    <label for="" class="pull-left ">VPC名:</label>
                                    <div class="bk-form-row-cell">
                                        <input type="text" id="router-vpc-name" name="vpc_name" ng-model="vpc" ng-init="vpc=''">
                                        <span class="text-danger vpc-name"></span>
                                        <p><i class="fa icon-exclamation-sign"></i>用来说明路由器在网络规划中的用途，比如路由器是用在办公网，还是用在新闻制作网</p>
                                    </div>
                                </div>
                                <div class="clearfix">
                                    <label for="" class="pull-left ">VPC网络地址:</label>
                                    <div class="bk-form-row-cell bk-form-row-small" ng-init="vpcip1=172;vpcip2=16;vpcip3=0;vpcip4=0;vpcip5=20">
                                        <input  type="text" id="vpcip1" ng-model="vpcip1" />
                                        &nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;
                                        <input  type="text" id="vpcip2" ng-model="vpcip2" />
                                        &nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;
                                        <input type="text" id="vpcip3" ng-model="vpcip3" />
                                        &nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;
                                        <input type="text" disabled="disabled" ng-model="vpcip4" />
                                        &nbsp;&nbsp;/&nbsp;&nbsp;<input type="text" id="vpcip5" ng-model="vpcip5" />
                                        <span class="text-danger vpc-ip"></span>
                                    </div>
                                    <div class="bk-form-row-cell">
                                        <p><i class="icon-info-sign"></i>&nbsp;一个VPC包含一个路由器，一个或多个子网。
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                        <!-- <tr>
                            <td class="row-message-left">购买量</td>
                            <td class="row-message-right">
                                <div class="ng-binding">
                                    <div class="clearfix">
                                        <label for="" class="pull-left ">购买时长:</label>
                                        <div class="bk-form-row-cell">
                                            <input type="number" id="router-time" name="time" value="1" min="1" max="12" ng-model="month" class="ng-pristine ng-valid ng-valid-number ng-valid-max ng-valid-min"> 月
                                        </div>
                                    </div>
                                    <div class="clearfix">
                                        <label for="" class="pull-left ">数量:</label>
                                        <div class="bk-form-row-cell">
                                            <input type="number" id="router-number" name="number" min="1" ng-init="num=1" ng-model="num" class="ng-pristine ng-valid ng-valid-number ng-valid-max ng-valid-min"> 台
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr> -->
                    </tbody>
                </table>
            </div>
            <div class="pull-right theme-right">
                <div class="theme-right-mian ">
                    <p class="theme-buy">当前配置 </p>
                    <ul class="goods-detail">
                        <li>
                            <span class="deleft">厂商：</span>
                            <span class="deright">{{currentCompany.name}}</span>
                            <input id="txtcs" type="hidden" value="{{currentCompany.name}}" code="{{currentCompanyCode}}" />
                        </li>
                        <li>
                            <span class="deleft">地域：</span>
                            <span class="deright">{{currentArea.name}}</span>
                            <input id="txtdy" type="hidden" value="{{currentArea.name}}" code="{{currentAreaCode}}" />
                        </li>
                        <li>
                            <span class="deleft">路由器：</span>
                            <span class="deright" id="names">{{router}}</span>
                            <input type="hidden" id="name" value="{{router}}">
                        </li>
                        <li>
                            <span class="deleft">VPC名：</span>
                            <span class="deright" id="vpc_names">{{vpc}}</span>
                            <input type="hidden" id="vpc_name" value="{{vpc}}">
                        </li>
                        <li>
                            <span class="deleft">VPC地址：</span>
                            <span class="deright">{{vpcip1}} <strong>·</strong> {{vpcip2}} <strong>·</strong> {{vpcip3}} <strong>·</strong> {{vpcip4}} / {{vpcip5}}</span>
                            <input type="hidden" id="vpc_url" value="{{vpcip1}}.{{vpcip2}}.{{vpcip3}}.{{vpcip4}}/{{vpcip5}}">
                        </li>
                        <li>
                            <span class="deleft">计费周期：</span>
                            <span class="deright">{{currentPrice.name}}</span>
                            <input id="txtpricename" type="hidden" value="{{currentPrice.name}}"/>
                            <input id="txtpriceId" type="hidden" value="{{currentPrice.id}}"/>
                        </li>
                        <li>
                            <span class="deleft">单台原价：</span>
                            <span class="deright" >{{currentPrice.price}}{{currentPrice.unit}}</span>
                            <input id="txtprice" type="hidden" value="{{currentPrice.price}}"/>
                            <input id="txtunit" type="hidden" value="{{currentPrice.unit}}"/>
                        </li>
                    </ul>
                    <button class="btn btn-oriange" id="nowBuy">确认创建</button>
                    <!-- <a class="btn btn-sleepred" id="addShoppingCar" href="#">加入清单</a> -->
                    <!-- <p class="pan">实际扣费以账单为准<a href="##">购买和计费说明&gt;&gt;</a></p> -->
                </div>
            </div>
        </div>
    </div>
</div>

</div>
<?php
$this->start('script_last');
echo $this->Html->script(['angular.min.js','controller/controller.js','jquery-ui-1.10.0.custom.min.js']);
?>
<script>

    $('#router-name').blur(function(){
        var data = $('#router-name').val();
        $('#names').html(data);
        $('#name').val(data);
    });
    $('#router-number').blur(function(){
        var data = $('#router-number').val();
        $('#numbers').html(data+'台');
        $('#number').val(data);
    });
    $('#router-time').blur(function(){
        var data = $('#router-time').val();
        $('#times').html(data);
        $('#time').val(data);
    });
    $('#router-vpc-name').blur(function(){
        var data = $('#router-vpc-name').val();
        $('#vpc_names').html(data);
        $('#vpc_name').val(data);
    });

    $('.bk-form-row-cell').on('click',"li",function(){
        $(this).parent().children().removeClass('active');
        $(this).addClass('active');
    });
    $( "#slider" ).slider({
        value: 1,
        min:0,
        max:500,
        step:1,
        orientation: "horizontal",
        range: "min",
        animate: true,
        slide:function(event, ui){
            $( "#amount" ).html(ui.value );
            $("#bandwidth").html(ui.value);
        }
    });
    $("#amount").html($("#slider").slider("value"));
    $("#bandwidth").html($("#slider").slider("value"));


    $('#nowBuy').on('click',function(){
        $(this).prop('disabled',true);
        $.getJSON("/console/home/getUserLimit", function(data){
            if(data.router_used+1 > data.data.router_bugedt){
                alert("配额不足 \r\n 路由 配额："+ data.data.router_bugedt+" 已使用："+data.router_used)
            }else{
                addCar('');
            }
        });
    });

    $('#addShoppingCar').on('click',function(){
        $.getJSON("/console/home/getUserLimit", function(data){
            if(data.router_used+1 > data.data.router_bugedt){
                alert("配额不足 \r\n 路由 配额："+ data.data.router_bugedt+" 已使用："+data.router_used)
            }else{
                addCar('list');
            }
        });
    });

function addCar(data){
    var ip1 = $('#vpcip1').val();
    var ip2 = $('#vpcip2').val();
    var ip3 = $('#vpcip3').val();
    var ip5 = $('#vpcip5').val();
    var area = $('#area').val();
    var name = $('#name').val();
    // var firewall_name = $('#firewall_name').val();
    var number = $('#number').val();
    var vpc_name = $('#vpc_name').val();
    var vpc_url = $('#vpc_url').val();
    var firewall = $('#firewall').val();
    var price_per = $('#price_per').val();
    var firewall_template_id = $('#firewallTemplateId').val()
    var validate = true;
    if(data==''){
        var type=0;
    }else{
        var type=1;
    }
    if(firewall_template_id == ''){
        $(".firewall-template").html('请选择防火墙模板');
        validate =false;
    }
    if (!/^[1-9]+\d*$/i.test(ip1) || ip1==null || ip1 > 255 || ip1 < 1 || !/^[1-9]+\d*$/i.test(ip2) || ip2==null || ip2 > 255 || ip2 < 0 || !/^[0-9]+\d*$/i.test(ip3) || ip3==null || ip3 > 255 || ip3 < 0) {
        $(".vpc-ip").html('请输入大于0小于256的整数'+ip3);
        validate =false;
    }else{
        $(".vpc-ip").html('');
    	if (!/^[1-9]+\d*$/i.test(ip5) || ip5==null || ip5 > 21 || ip5 < 15) {
            $(".vpc-ip").html('请输入大于15小于21的整数掩码');
            validate =false;
        }else{
            $(".vpc-ip").html('');
        }
    }
    if(name==''){
        $(".router-name").html('请输入路由名');
        validate =false;
    }else{
        $(".router-name").html('');
    }
    // if(firewall_name==''){
    //     $(".wall-name").html('请输入防火墙名');
    //     validate =false;
    // }else{
    //     $(".wall-name").html('');
    // }
    if(vpc_name==''){
        $(".vpc-name").html('请输入VPC名');
        validate =false;
    }else{
        $(".vpc-name").html('');
    }
    if(validate){
        $.ajax({
            type:"post",
            url:"/orders/addShoppingCar",
            async:true,
            data:{
                is_console:1,
                goods_id:$("#txtgoods_id").val(),
                attr:{
                    "firewallTemplateId":$('#firewallTemplateId').val(),
                    "routerName":$('#router-name').val(),
                    "ecsName":$("#txtname").val(),
                    "regionCode":$("#txtdy").attr("code"),
                    "csName":$("#txtcs").val(),
                    "csCode":$("#txtcs").attr("code"),
                    "dyName":$("#txtdy").val(),
                    "dyCode":$("#txtdy").attr("code"),
                    "cidr":$("#vpc_url").val(),
                    "number":$("#router-number").val(),
                    "vpcName":$("#vpc_name").val(),
                    "month":$("#router-time").val(),
                    "token":"<?= $token?>",

                    "billCycleName" : $("#txtpricename").val(), // 计费周期 展示用
                    "priceId"       : $("#txtpriceId").val(), // id 筛选用
                    "price"         : $("#txtprice").val(), // 价格  展示用
                    'real_price'    : $("#txtprice").val(),
                    "unit"          : $("#txtunit").val(), // 价格单位  展示用
                },
                type:type
            },
            success: function (data) {
                data= $.parseJSON(data);
                if(type==true){
                    alert(data.Message);
                }else{
                    setTimeout(function() {
                            window.location.href=data.url;
                        }, 1000);
                }

            }
        });
    }else{
        $('#nowBuy').prop('disabled',false);
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
    });

</script>
<?php
$this->end();
?>