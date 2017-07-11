<?= $this->element('network/lists/left', ['active_action' => 'eip']); ?>
<?= $this->Html->script(['controller/controller.js']); ?>

<div class="wrap-nav-right" ng-app>
    <div class="container-wrap  wrap-buy">
        <a href="<?= $this->Url->build(['controller'=>'network','action'=>'lists','eip']); ?>" class="btn btn-addition">返回EIP列表</a>
       <div class="clearfix buy-theme ng-scope" ng-controller="eipListService">
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
                        <input type="hidden" value="<?= $goods_id ?>" id="txtgoods_id"/>
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
                        <td class="row-message-left">网络</td>
                        <td class="row-message-right">
                            <div class="ng-binding">
                                <div class="clearfix">
                                    <label for="" class="pull-left ">选择VPC:</label>
                                    <div class="bk-form-row-cell">
                                        <div ng-repeat="host in hostList | filter: { company.name : currentCompany.name} :true">
                                            <div ng-repeat="area in host.area | filter: {areaCode : currentArea.areaCode, name : currentArea.name}">
                                                <select ng-options="vpc.name for vpc in area.vpc" ng-model="vpc" ng-init="vpc = area.vpc[0]" ng-change="changeVpc(vpc)" id="vpc"></select>
                                                <span class="text-danger" id="vpc-warning"></span>
                                            </div>
                                        </div>
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

                                    <label for="" class="pull-left ">名称：</label>
                                    <div class="bk-form-row-cell">
                                        <input type="text" ng-model="subnet" id="sub-net"/>
                                        <span class="text-danger sub-net"></span>

                                    </div>
                                </div>
<!--                                 <div class="clearfix">
                                    <label for="" class="pull-left ">计费模式：</label>
                                    <div class="bk-form-row-cell">
                                        <div class="bk-select-group" >
                                            <select ng-options="router.name for router in routers" ng-model="router" ng-change="changeRouter(router)"></select>
                                            <span class="text-danger rou-ter"></span>
                                        </div>
                                    </div>
                                    </div> -->
                                     <div class="clearfix">

                                  <label class="pull-left ">带宽上限:</label>
                                  <div class="slider-area bk-form-row-cell">
                                    <div id="slider"></div>
                                  </div>
                                  <div class="amount pull-left">
                                    <input type="text" id="amount" placeholder="1"> Mbps
                                    <!-- <span class="warm">1Mbps-300Mbps</span> -->
                                  </div>
                                </div>
<!--                                 <div class="clearfix">
                                    <label for="" class="pull-left ">带宽上限：</label>
                                    <div class="bk-form-row-cell">
                                        <div class="bk-select-group" >

                                        </div>
                                    </div>

                                </div> -->
                                <div class="clearfix">
                                    <label for="" class="pull-left ">备注：</label>
                                    <div class="bk-form-row-cell">
                                        <div class="bk-select-group" >
                                            <textarea name="" id="txtDescription" cols="50" rows="5"></textarea>
                                            <span class="text-danger rou-ter"></span>
                                        </div>
                                  </div>
                                </div>
                            </div>
                        </td>
                    </tr>
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
                            <input type="hidden" value="{{currentCompany.name}}"  code="{{currentCompany.currentCompanyCode}}">

                        </li>
                        <li>
                            <span class="deleft">地域：</span>
                            <span class="deright">{{currentArea.name}}</span>
                            <input type="hidden" value="{{currentArea.name}}" id="area" code="{{currentAreaCode}}">
                            <input type="hidden" id="txtRegionCode" value="{{currentAreaCode}}">
                        </li>
                        <li>
                            <span class="deleft">VPC：</span>
                            <span class="deright">{{currentVpc.name}}</span>
                            <input id="txtVpcode" type="hidden" value="{{currentVpc.vpCode}}"  />
                        </li>
                        <li>
                            <span class="deleft">名称：</span>
                            <span class="deright">{{subnet}}</span>
                            <input type="hidden" id="txtEipName" value="{{subnet}}">

                        </li>


<!--                         <li>
                            <span class="deleft">计费方式：</span>
                            <span class="deright">{{routerName}}</span>
                            <input type="hidden" id="router" value="{{routerId}}" code="{{routerName}}">
                        </li> -->
                        <li">
                            <span class="deleft">带宽上限: </span>
                            <span class="deright broadband" id="txtBandwidth">1</span>Mbps

                        </li>
                        <li>
                            <span class="deleft">计费周期：</span>
                            <span class="deright">{{currentPrice.name}}</span>
                            <input id="txtpricename" type="hidden" value="{{currentPrice.name}}"/>
                            <input id="txtpriceId" type="hidden" value="{{currentPrice.id}}"/>
                        </li>
                        <li>
                            <span class="deleft">单M原价：</span>
                            <span class="deright" >{{currentPrice.price}}{{currentPrice.unit}}.M</span>
                            <input id="txtprice" type="hidden" value="{{currentPrice.price}}"/>
                            <input id="txtunit" type="hidden" value="{{currentPrice.unit}}"/>
                        </li>
                        <li>
                            <span class="deleft">单个原价：</span>
                            <span class="deright" ><span id="totalPrice">{{currentTotalPrice}}</span>{{currentPrice.unit}}</span>
                            <input id="txtprice_total" type="hidden" value="{{currentTotalPrice}}"/>
                            <input id="txtunit_total" type="hidden" value="{{currentPrice.unit}}"/>
                        </li>
                    </ul>
                    <button class="btn btn-oriange" id="btnBuy">确认创建</button>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/jQuery-2.1.3.min.js"></script>
<script src="/js/angular.min.js"></script>
<script src="/js/controller/controller.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/jquery-ui-1.10.0.custom.min.js" type="text/javascript"></script>

<script>
function changebillCycle(){
    var id = $("#selbillCycle").val();
    var val = $("#selbillCycle").find("option:selected").text();
    $("#txtbillCycle").val(id);
    $("#span_billCycle").html(val);
}
$(function() {

	bandwidth = 1;
	$('#amount').val(bandwidth);
	$('#txtBandwidth').html(bandwidth);
    $("#amount").keyup(function() {
        var a = /(^[1-9]([0-9]*)$|^[0-9]$)/;
        var amountVal = $(this).val();
        $(this).blur(function() {
            if (a.test(amountVal) == false || amountVal < 1) {
                $(".amount").addClass('red');
            } else if (amountVal > 500) {
                $(".amount").addClass('red');
                $(".broadband").html(500);
                $("#amount").val(500)
            } else {
                $("#amount").val(amountVal)
                $(".amount").removeClass('red');
            }
            var totalPrice = $("#amount").val() * $("#txtprice").val();
         	$('#totalPrice').html(totalPrice);
         	$('#txtprice_total').val(totalPrice);
        })
        $(".broadband").html(amountVal);
        $("#slider").slider({
            value: amountVal
        });
    });
    $("#slider").slider({
        value: $("#amount").val(),
        min: 1,
        max: 500,
        step: 1,
        orientation: "horizontal",
        range: "min",
        animate: true,
        slide: function(event, ui) {6
            var totalPrice = ui.value * $("#txtprice").val();
         	$('#totalPrice').html(totalPrice);
         	$('#txtprice_total').val(totalPrice);
            $("#amount").val(ui.value);
            $(".broadband").html(ui.value);
        }
    });
    //新建 右边固定框
    var offsetTop = $(".theme-right").offset().top;
    var width = $(".buy-theme").width() * 0.24;
    $(window).scroll(

    function() {
        if ($(document).scrollTop() > offsetTop - 60) {
            var offsetLeft = $(".theme-right").offset().left;
            $(".theme-right").css({
                position: "fixed",
                top: "60px",
                left: offsetLeft,
                width: width
            });
        } else {
            $(".theme-right").css("position", "static");
        }
    });

    $('#btnBuy').on('click', function() {
        // $.getJSON("/console/home/getUserLimit", function(data){
        //             if(Number($("#txtcpu").val())+data.cpu_used > data.cpu_bugedt || (Number($("#txtrom").val())+data.memory_used) > data.memory_buget){
        //                 alert("配额不足 \r\n cpu 配额："+ data.cpu_bugedt+" 已使用："+data.cpu_used+" \r\n 内存 配额：" + data.memory_buget+" 已使用："+data.memory_used)
        //             }else{
        //                 addCar(false);
        //             }
        //         });
        $(this).prop('disabled',true);
        $.getJSON("/console/home/getUserLimit", function(data) {
        if (data.eip_used >= data.data.eip_budget) {
            alert('配额不足 \r\n EIP 配额：' + data.data.eip_budget);
            $(this).prop('disabled',false);
        }else{
            addCar(false);
        }
        });
//        $(this).prop('disabled',true);
//        addCar(false);
    });

    function addCar(type) {
        var goods_id = $("#txtgoods_id").val(); //id
        //获取商品配置信息
        if (type == true) {
            type = 1;
        } else {
            type = 0;
        }
        var name = $('#sub-net').val();
        var validate = true;
        if (name == '') {
            $(".sub-net").html('请输入名称');
            validate = false;
        } else {
            $(".sub-net").html('');
        }
        if (validate) {
            $.ajax({
            type:"post",
            url:"/orders/addShoppingCar",
            async:true,
            data:{
                is_console:1,
                goods_id:goods_id,
                attr:{
                        "regionCode":$("#txtRegionCode").val(),
                        "eipName":$("#txtEipName").val(),
                        "description":$("#txtDescription").val(),
                        "bandwidth":$("#txtBandwidth").html(),
                        "vpcCode":$("#txtVpcode").val(),
                        "number":1,
                        "billCycle":$("#txtbillCycle").val(),
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
})
</script>
</body>
</html>