<?= $this->element('network/lists/left', ['active_action' => 'subnet']); ?>
<?= $this->Html->script(['controller/controller.js']); ?>
<div class="wrap-nav-right" ng-app>
    <div class="container-wrap  wrap-buy">
        <a href="<?= $this->Url->build(['controller'=>'network','action'=>'lists','subnet']); ?>" class="btn btn-addition">返回子网列表</a>
<!--        <div class="y-tab">
            <ul>
                <li class="y-first y-current">
                    <a href="##" class="y-item">包年包月</a>
                </li>
                <li class="">
                    <a href="##" class="y-item">按量付费</a>
                </li>
            </ul>
        </div>-->
        <input type="hidden" value="<?= $goods_id ?>" id="txtgoods_id"/>
        <div class="clearfix buy-theme ng-scope" ng-controller="subnetListService">
            <div class="pull-left theme-left">
                <table>
                    <tbody>
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
                                <div class="clearfix location-relative " >
                                    <label for="" class="pull-left">虚拟化技术:</label>
                                    <div class="bk-form-row-cell" >
                                        <div >
                                            <ul class="clearfix city" >
                                                <li ng-repeat="virtual in virtuals"  ng-class="{active:$first}" ng-click="changeVirtual(virtual)">
                                                    {{virtual.para_note}}
                                                </li>
                                            </ul>
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

                                    <label for="" class="pull-left ">子网名：</label>
                                    <div class="bk-form-row-cell">
                                        <input type="text" ng-model="subnet" />
                                        <span class="text-danger sub-net"></span>

                                    </div>
                                </div>
                            <?php if ($deparment_type == 'platform') :?>
                                <div class="clearfix">

                                    <label for="" class="pull-left ">vlanID：</label>
                                    <div class="bk-form-row-cell">
                                        <input type="text" id="textVID"/>
                                        <span class="text-danger sub-vid"></span>
                                    </div>
                                </div>
                            <?php endif ?>
                                <div class="clearfix">
                                    <label for="" class="pull-left ">路由器：</label>
                                    <div class="bk-form-row-cell">
                                        <div class="bk-select-group" >
                                            <select ng-options="router.name for router in routers" ng-model="router" ng-change="changeRouter(router)"></select>
                                            <span class="text-danger rou-ter"></span>
                                        </div>
                                    </div>
                                    <div class="bk-form-row-cell">
                                    <p><i class="icon-info-sign"></i>&nbsp;请选择要连接到子网的路由器
                                    </p>
                                    </div>
                                </div>
                                <div class="clearfix">
                                    <label for="" class="pull-left">网络地址：</label>

                                    <div class="bk-form-row-cell bk-form-row-small" ng-init="ipnum1=192;ipnum2=168;ipnum3=1;ipnum4=102;ipnum5=1">
                                       <input type="text" disabled="disabled" ng-model="ip0" />
                                       &nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;
                                       <input type="text" disabled="disabled" ng-model="ip1" />
                                       &nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;
                                       <input type="text" id="ipip" ng-model="ip2" />
                                       &nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;
                                       <input type="text" disabled="disabled"  ng-model="ip3" />
                                       &nbsp;&nbsp;/ 24
                                        <span class="text-danger i-p"></span>
                                    </div>
                                    <div class="bk-form-row-cell">
                                        <p><i class="icon-info-sign"></i>&nbsp;请为您的子网指定一个网络地址。</p>
                                    </div>
                                </div>

                                <div class="clearfix">
                                    <label for="" class="pull-left">默认网关：</label>

                                    <div class="bk-form-row-cell bk-form-row-small">
                                       <input type="text" disabled="disabled" value="{{ip0}}" />
                                       &nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;
                                       <input type="text" disabled="disabled" value="{{ip1}}" />
                                       &nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;
                                       <input type="text" disabled="disabled" value="{{ip2}}" />
                                       &nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;
                                       <input type="text" disabled="disabled" value="1" />
                                    </div>
                                    <div class="bk-form-row-cell">
                                        <p><i class="icon-info-sign"></i>&nbsp;请为您的子网指定一个默认网关。</p>
                                    </div>
                                </div>

                               <!--  <div class="clearfix">
                                    <label for="" class="pull-left "></label>

                                    <div class="bk-form-row-cell bk-form-row-small">
                                       <ul class="clearfix city" style="margin-bottom:10px;" id="dhcp-controller">
                                         <li class="active">自定义分配</li>
                                         <li>随机分配</li>
                                       </ul>
                                       <div id="dhcp" ng-init="dhcp1=192;dhcp2=168;dhcp3=1;">
                                           <input type="text" ng-model="dhcp1" />
                                           &nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;
                                           <input type="text" ng-model="dhcp2" />
                                           &nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;
                                           <input type="text" ng-model="dhcp3" />
                                           &nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;
                                           <input type="text" ng-model="dhcp4" />
                                           &nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;
                                           <input type="text" ng-model="dhcp5" />
                                           <p><i class="icon-info-sign"></i>&nbsp;请为DHCP服务指定动态地址范围，例如：192.168. * . <起始地址>.<结束地址></p>
                                       </div>
                                    </div>
                                </div> -->

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
                                        <input type="number" min="0" max="250" ng-model="month" /> 月
                                    </div>
                                </div>
                                <div class="clearfix">
                                    <label for="" class="pull-left ">数量:</label>
                                    <div class="bk-form-row-cell">
                                        <input type="number" min="0" max="250" ng-model="num" /> 台
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
                            <input type="hidden" value="{{currentCompany.name}}"  code="{{currentCompany.currentCompanyCode}}">

                        </li>
                        <li>
                            <span class="deleft">地域：</span>
                            <span class="deright">{{currentArea.name}}</span>
                            <input type="hidden" value="{{currentArea.name}}" id="area" code="{{currentAreaCode}}">

                        </li>
                        <li>
                            <span class="deleft">虚拟化技术：</span>
                            <span class="deright">{{currentVirtual.para_note}}</span>
                            <input type="hidden" value="{{currentVirtual.para_value}}" id="fusionType" code="{{currentVirtual.para_value}}">

                        </li>
                        <li>
                            <span class="deleft">子网名：</span>
                            <span class="deright">{{subnet}}</span>
                            <input type="hidden" id="name" value="{{subnet}}">
                        </li>
                        <li>
                            <span class="deleft">路由器：</span>
                            <span class="deright">{{routerName}}</span>
                            <input type="hidden" id="router" value="{{routerId}}" code="{{routerName}}">
                        </li>
                        <li>
                            <span class="deleft">网络地址：</span>
                            <span class="deright">&nbsp;&nbsp;{{ip0}}&nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;{{ip1}}&nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;{{ip2}}&nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;{{ip3}}&nbsp;&nbsp;/ 24</span>
                            <input type="hidden" id="subnetIp" value="{{ip0}}.{{ip1}}.{{ip2}}.0/24">
                        </li>
                      <!--  <li>
                            <span class="deleft">数量：</span>
                            <span class="deright">{{month}}个月 × {{num}}台</span>
                            <input type="hidden" id="month" value="{{month}}">
                            <input type="hidden" id="num" value="{{num}}">
                        </li>
                        <li id="dhcp-form">
                            <span class="deleft">自定义：</span>
                            <span class="deright">{{dhcp1}} <strong>·</strong> {{dhcp2}} <strong>·</strong> {{dhcp3}} <strong>·</strong> {{dhcp4}} <strong>·</strong> {{dhcp5}}</span>
                            <input type="hidden" id="dhcp_ip" value="{{dhcp1}}.{{dhcp2}}.{{dhcp3}}.{{dhcp4}}.{{dhcp5}}">
                        </li>
                        <li>
                            <span class="deleft">配置费用：</span><br>
                            <span class="price ng-binding">￥1200</span>

                        </li>-->
                        <input type="hidden" id="virtual-value" />
                    </ul>
                    <button class="btn btn-oriange" id="addShoppingCar">确认创建</button>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/jQuery-2.1.3.min.js"></script>
<script src="/js/angular.min.js"></script>
<script src="/js/controller/controller.js"></script>
<script src="/js/bootstrap.min.js"></script>

<script>
    $('#diskname').blur(function(){
        var data = $('#diskname').val();
        $('#names').html(data);
        $('#name').val(data);

    });

    $('#memory_m').blur(function(){
        var data = $('#memory_m').val();
        $('#capacity').html(data);
        $('#memory').val(data);
    });

    $('#ipip').blur(function(){
        $.ajax({
            type:"post",
            url:"<?php echo $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','subnet','cidr']); ?>",
            async:true,
            data:{cidr:$('#subnetIp').val(),routerid:$("#router").val()},
            success: function (data) {
                data= $.parseJSON(data);
                if(data.code==1){
                    $(".i-p").html(data.msg);
                }else{
                    $(".i-p").html(data.msg);
                }
            }
        });
    });




    $('#addShoppingCar').on('click',function(){

        $(this).prop('disabled',true);

        $.ajaxSettings.async = false;
        istrue = false

        $.getJSON("/console/home/getUserLimit", function(data){
            if(data.subnet_used+1 > data.data.subnet_bugedt ){
                alert("配额不足 \r\n 子网 配额："+ data.data.subnet_bugedt+" 已使用："+data.subnet_used)
                istrue = false
            }else{
                istrue = true
            }
        });

        if(istrue){
            $.getJSON("/console/ajax/network/subnet/getVpsSubnetsCount?vpc="+$("#router").val(), function(data){
                if(data >14){
                    alert("一个vpc下最多建立15个子网")
                    istrue = false
                }else{
                    istrue = true
                }
            });
        }

        if(!istrue){
            $('#addShoppingCar').prop('disabled',false);
            return false;
        }

        $.ajaxSettings.async = true;

        // if($('#virtual').css('display')=='block'){
        //     $('#virtual-value').val($('#virtual li').filter('.active').html());
        // }
        // if (Trim($('#virtual-value').val()) == "OpenStack") {
        //     isFusion=true;
        // }else{
        //     isFusion=false;
        // }
        var fusionType = $('#fusionType').val();
        var name = $('#name').val();
        var router = $('#router').val();
        var validate = true;
        var ip = $('#ipip').val();
        if(/^([1-9]\d*)$/.test(ip)){
            if(parseInt(ip)<= 255 && 0<= parseInt(ip)){
                $('.i-p').html('');
            }else{
                $(".i-p").html('请输入正确的网络地址');
                validate =false;
            }
        }else{
            $(".i-p").html('请输入正确的数字');
            validate =false;
        }

        if(name==''){
            $(".sub-net").html('请输入子网名');
            validate =false;
        }else{
            $(".sub-net").html('');
        }
        if(router==''){
            $(".rou-ter").html('请选择路由器');
            validate =false;
        }else{
            $(".rou-ter").html('');
        }
        <?php if ($deparment_type == 'platform') :?>
        vid = $('#textVID').val();
        if(/^[1-9]*[1-9][0-9]*$/.test(vid)){
            $('.sub-vid').html('');
        }else{
            $(".sub-vid").html('请输入正整数');
            validate =false;
        }
        <?php endif ?>


        if(validate){
            $.ajax({
                type:"post",
                url:"<?php echo $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','subnet','cidr']); ?>",
                async:true,
                data:{cidr:$('#subnetIp').val(),routerid:$("#router").val()},
                success: function (data) {
                    data= $.parseJSON(data);
                    if(data.code==1){
                        $(".i-p").html(data.msg);
                        $('#addShoppingCar').prop('disabled',false);
                    }else{
                        $(".i-p").html(data.msg);
                        $.ajax({
                            type:"post",
                            url:"/orders/addShoppingCar",
                            async:true,
                            data:{
                                is_console:1,
                                goods_id:$("#txtgoods_id").val(),
                                attr:{
                                    "subnetName":$("#name").val(),
                                    "routerid":$("#router").val(),
                                    "routerName":$("#router").attr("code"),
                                    "area":$('#area').val(),
                                    "vpcCode":'',
                                    "cidr":$('#subnetIp').val(),
                                    "regionCode":$("#area").attr("code"),
                                    "fusionType":fusionType,
                                    <?php if ($deparment_type == 'platform') :?>
                                    "vlanId":$('#textVID').val(),
                                    <?php endif ?>
                                    "token":"<?= $token?>"
                                },
                                type:1
                            },
                            success: function (data) {
                                data= $.parseJSON(data);
                                if(data.Code==0){
                                    setTimeout(function() {
                                        window.location.href=data.url;
                                }, 1000);
                                }else{
                                    alert(data.Message);
                                }
                            }
                        });
                    }
                }
            });
        }else{
            $('#addShoppingCar').prop('disabled',false);
        }

    })

    function Trim(str){
        return str.replace(/(^\s*)|(\s*$)/g, "");
    }

    $(function () {
       $('#dhcp-controller').on('click', 'li', function () {
            if ($('#dhcp').css('display') == 'block') {
                $('#dhcp').hide();
                $('#dhcp-form').hide();
                $('#dhcp_ip').val('');
            } else {
                $('#dhcp').show();
                $('#dhcp-form').show();
            }
        });
    })

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