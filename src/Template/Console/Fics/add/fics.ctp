<?= $this->Html->script(['controller/controller.js']); ?>

<div class="wrap-nav-right" ng-app>
    <div class="container-wrap  wrap-buy">
       <a href="<?= $this->Url->build(['controller'=>'fics','action'=>'lists','fics']); ?>" class="btn btn-addition">返回媒体存储列表</a>
       <div class="clearfix buy-theme ng-scope" ng-controller="storeListService">
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
                        <td class="row-message-left">存储产品</td>
                        <td class="row-message-right">
                            <div class="ng-binding">
                                <div class="clearfix location-relative">
                                    <label for="" class="pull-left">品牌:</label>
                                    <div class="bk-form-row-cell">
                                        <ul class="clearfix city">
                                            <li ng-repeat="s in storeType" ng-class="{active:$first}" ng-click="changeStoreType(s)">
                                                {{s.name}}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="clearfix location-relative">
                                    <label for="" class="pull-left">存储名:</label>
                                    <div class="bk-form-row-cell">
                                        <ul class="clearfix city">
                                            <li ng-repeat="ss in store" ng-class="{active:$first}" ng-click="changeStore(ss)">
                                                {{ss.store_name}}
                                            </li>
                                        </ul>
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
                                    <label for="" class="pull-left ">卷名: </label>
                                    <div class="bk-form-row-cell">
                                        <input type="text" ng-model="ficsName" id="fics-name"/>
                                        <span class="text-danger fics-name"></span>
                                    </div>
                                </div>
                                <div class="clearfix">
                                    <label class="pull-left ">总容量（GB）:</label>
                                    <div class="bk-form-row-cell">
                                        <input type="text" ng-model="ficsGB" ng-init="ficsGB=1" id="fics-gb"/>
                                        <span class="text-danger fics-gb"></span>
                                    </div>
                                </div>

                                <div class="clearfix" style="display: none;">
                                    <label class="pull-left ">告警水位（%）:</label>
                                    <div class="bk-form-row-cell">
                                        <input type="text" ng-model="ficsWarn" ng-init="ficsWarn=80" id="fics-warn" />
                                        <span class="text-light">%</span>
                                        <span class="text-danger fics-warn"></span>
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
                            <input id="txtcs" type="hidden" value="{{currentCompany.name}}"  code="{{currentCompany.currentCompanyCode}}">
                        </li>
                        <li>
                            <span class="deleft">地域：</span>
                            <span class="deright">{{currentArea.name}}</span>
                            <input type="hidden" id="txtRegionCode" value="{{currentAreaCode}}">
                            <input id="txtdy" type="hidden" value="{{currentArea.name}}" code="{{currentAreaCode}}" />
                        </li>
                        <li>
                            <span class="deleft">品牌：</span>
                            <span class="deright">{{currentStoreType.name}}</span>
                            <input type="hidden" id="txtstoreType" value="{{currentStoreType.name}}" code="{{currentStoreType.type}}">
                        </li>
                        <li>
                            <span class="deleft">存储：</span>
                            <span class="deright">{{currentStore.store_name}}</span>
                            <input type="hidden" id="txtStroe" value="{{currentStore.store_name}}"  code="{{currentStore.store_code}}">
                        </li>
                        <li>
                            <span class="deleft">卷名：</span>
                            <span class="deright">{{ficsName}}</span>
                            <input type="hidden" id="txtFicsName" value="{{ficsName}}">

                        </li>
                        <li>
                            <span class="deleft">容量: </span>
                            <span class="deright">{{ficsGB}}</span>
                            <input type="hidden" id="txtFicsGB" value="{{ficsGB}}">
                        </li>
                        <li style="display: none;">
                            <span class="deleft">告警水位：</span>
                            <span class="deright">{{ficsWarn}}</span>
                            <input type="hidden" id="txtFicsWarn" value="{{ficsWarn}}">
                            <span class="text-light">%</span>
                        </li>
                        <li>
                            <span class="deleft">计费周期：</span>
                            <span class="deright">{{currentPrice.name}}</span>
                            <input id="txtpricename" type="hidden" value="{{currentPrice.name}}"/>
                            <input id="txtpriceId" type="hidden" value="{{currentPrice.id}}"/>
                        </li>
                        <li>
                            <span class="deleft">1GB原价：</span>
                            <span class="deright" >{{currentPrice.price}}{{currentPrice.unit}}</span>
                            <input id="txtprice" type="hidden" value="{{currentPrice.price}}"/>
                            <input id="txtunit" type="hidden" value="{{currentPrice.unit}}"/>
                        </li>
                        <li>
                            <span class="deleft">总价：</span>
                            <span class="deright" >{{currentPrice.price * ficsGB}}{{currentPrice.unit}}</span>
                            <input id="txtpricetotail" type="hidden" value="{{currentPrice.price * ficsGB}}"/>
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

$(function() {
    $('#btnBuy').on('click',function(){
        $(this).prop('disabled',true);
        var go = true;
        $.ajax({
            type:"post",
            url:"<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'fics', 'fics', 'checkVolName']) ?>",
            async:false,
            data:{
                vol_name:$('#fics-name').val()
            },
            success: function (data) {
                if(data == 1){
                    $(".fics-name").html('卷名已被使用');
                    go = false;
                }
            }
        });
        if(go == true){
//            $.getJSON("/console/home/getUserLimit", function(data){
//                if(data.oceanstor9k_num_used + 1 > data.data.oceanstor9k_num_bugedt || (Number($("#fics-gb").val())+data.oceanstor9k_cap_used) > data.data.oceanstor9k_cap_bugedt){
//                    alert("配额不足 \r\n fics 数量配额："+ data.data.oceanstor9k_num_bugedt+" 已使用："+data.oceanstor9k_num_used+" \r\n 大小配额：" + data.data.oceanstor9k_cap_bugedt+"GB 已使用："+data.oceanstor9k_cap_used+"GB");
//                    $("#btnBuy").prop('disabled',false);
//                }else{
//                    addCar(false);
//                }
//            });

            if($('#txtstoreType').attr('code')=='oceanstor9k') {
                $.getJSON("/console/home/getUserLimit", function (data) {
                    if (data.oceanstor9k_num_used + 1 > data.data.oceanstor9k_num_bugedt || (Number($("#fics-gb").val())+data.oceanstor9k_cap_used) > data.data.oceanstor9k_cap_bugedt) {
                        alert("配额不足 \r\n H9000 数量配额：" + data.data.oceanstor9k_num_bugedt + " 已使用：" + data.oceanstor9k_num_used + " \r\n 总容量配额：" + data.data.oceanstor9k_cap_bugedt + "GB,已使用:"+data.oceanstor9k_cap_used+"GB");
                        $("#btnBuy").prop('disabled', false);
                    } else {
                        addCar(false);
                    }
                });
            }else if($('#txtstoreType').attr('code')=='fics'){
                $.getJSON("/console/home/getUserLimit", function (data) {
                    if (data.fics_num_used + 1 > data.data.fics_num_bugedt || (Number($("#fics-gb").val())+data.fics_cap_used) > data.data.fics_cap_bugedt) {
                        alert("配额不足 \r\n FICS 数量配额：" + data.data.fics_num_bugedt + " 已使用：" + data.fics_num_used + " \r\n 总容量配额：" + data.data.fics_cap_bugedt + "GB,已使用:"+data.fics_cap_used+"GB");
                        $("#btnBuy").prop('disabled', false);
                    } else {
                        addCar(false);
                    }
                });
            }

        } else {
            $("#btnBuy").prop('disabled',false);
        }
    });


    function addCar(type) {
        var goods_id = $("#txtgoods_id").val(); //id
        //获取商品配置信息
        if (type == true) {
            type = 1;
        } else {
            type = 0;
        }
        var volName = $('#fics-name').val();
        var gb = $('#fics-gb').val();
        var warn = $('#fics-warn').val();
        var validate = true;
        if (volName == '') {
            $(".fics-name").html('请输入卷名');
            validate = false;
        }else if(!/^[A-Za-z0-9_]+$/i.test(volName)){
            $(".fics-name").html('只能输入字母、数字和下划线');
            validate = false;
        }else if(volName.length < 8 || volName.length >32){
            $(".fics-name").html('请输入8-32位长度的卷名');
            validate = false;
        }else{
            $(".fics-name").html('');
        }
        if (!/^[1-9]+\d*$/i.test(gb)) {
            $(".fics-gb").html('请输入正整数');
            validate = false;
        } else if(gb == '') {
            $(".fics-gb").html('请输入总容量');
            validate = false;
        } else {
            $(".fics-gb").html('');
        }
        if (!/^[1-9]+\d*$/i.test(warn)) {
            $(".fics-warn").html('请输入正整数');
            validate = false;
        }else if(warn<1 || warn>100){
            $(".fics-warn").html('请输入1-100以内的正整数');
            validate = false;
        }else if (warn == '') {
            $(".fics-warn").html('请输入名称');
            validate = false;
        } else {
            $(".fics-warn").html('');
        }

        warn = gb*warn/100;
        if (validate) {
            $.ajax({
            type:"post",
            url:"/orders/addShoppingCar",
            async:true,
            data:{
                is_console:1,
                goods_id:goods_id,
                attr:{
                        "storeCode":$("#txtStroe").attr("code"),
                        "regionCode":$("#txtRegionCode").val(),
                        'storeType':$("#txtstoreType").attr("code"),
                        'storeName':$("#txtstoreType").val(),
                        'volume_name':$("#txtFicsName").val(),
                        "total_cap":$("#txtFicsGB").val(),
                        "warn_level":$("#txtFicsWarn").val(),
                        "token":"<?= $token?>",

                        "csName":$("#txtcs").val(),
                        "csCode":$("#txtcs").attr("code"),
                        "dyName":$("#txtdy").val(),
                        "dyCode":$("#txtdy").attr("code"),

                        "billCycleName" : $("#txtpricename").val(), // 计费周期 展示用
                        "priceId"       : $("#txtpriceId").val(), // id 筛选用
                        "price"         : $("#txtpricetotail").val(), // 价格  展示用
                        'real_price'    : $("#txtpricetotail").val(),
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


$(".wrap-nav-right").addClass('wrap-nav-right-left');
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
</body>
</html>