<!-- 云桌面 新增 -->
<?= $this->Html->script(['controller/controller.js']); ?>

<div class="wrap-nav-right " ng-app>
    <div class="container-wrap  wrap-buy">
        <a href="<?= $this->Url->build(['controller'=>'SecurityGroup','action'=>'index']); ?>" class="btn btn-addition">返回安全组列表</a>


        <div class="clearfix buy-theme" ng-controller="securityGroupService">
            <div class="pull-left theme-left">
                <div class="">
                    <table>
                        <input type="hidden" value='<?= $goods_id ?>' id="txtgoods_id">
                        <!--<input type="hidden" value='<?= $instanceTypeCode ?>' id="txtinstanceTypeCode"></input>-->
                       <!-- <input type="hidden" value='<?= $imageCode ?>' id="txtimageCode"></input>-->

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
                                            <ul class="clearfix city" ng-repeat="host in hostList | filter: { company.name:currentCompany.name} :true">
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
                            <td class="row-message-left">安全组信息</td>
                            <td class="row-message-right">
                                <div class="ng-binding">
                                    <div class="clearfix">
                                        <label for="" class="pull-left ">安全组名：</label>
                                        <input type="text" name="securitygroup_name" ng-model="securitygroup" ng-init="securitygroup=''">
                                        <span class="text-danger wall-name"></span>
                                    </div>

                                    <div class="clearfix">
                                        <label for="" class="pull-left ">选择VPC:</label>
                                        <div class="bk-form-row-cell">
                                            <div ng-repeat="host in hostList | filter: { company.name : currentCompany.name} :true">
                                                <div ng-repeat="area in host.area | filter: {areaCode : currentArea.areaCode, name : currentArea.name}">
                                                    <select ng-options="vpc.name for vpc in area.vpc" ng-model="vpc" ng-init="vpc = area.vpc[0]" ng-change="changeVpc(vpc)"></select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td></td>
                        </tr>
                        <tr >
                            <td class="row-message-left">备注信息</td>
                            <td class="row-message-right">
                                <div class="ng-binding">
                                    <label for="" class="pull-left ">备注：</label>
                                    <textarea name="note" id="note" cols="50" rows="10"></textarea>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
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
                            <span class="deleft">虚拟化技术：</span>
                            <span class="deright">{{currentVirtual.para_note}}</span>
                            <input type="hidden" value="{{currentVirtual.para_value}}" id="fusionType" code="{{currentVirtual.para_value}}">

                        </li>
                        <li>
                            <span class="deleft">安全组：</span>
                            <span class="deright" id="firewall_names">{{securitygroup}}</span>
                            <input type="hidden" id="securitygroup_name" value="{{securitygroup}}">
                        </li>
                        <li>
                            <span class="deleft">VPC：</span>
                            <span class="deright">{{currentVpc.name}}</span>
                            <input id="txtvpc" type="hidden" value="{{currentVpc.name}}" code="{{currentVpc.vpCode}}"/>
                        </li>

                    </ul>
                    <button id="btnBuy"  class="btn btn-oriange">确认创建</button>
                </div>
            </div>
        </div>
    </div>



    <?php  $this->start('script_last'); ?>
    <?=$this->Html->script(['angular.min.js','controller/controller.js','jquery-ui-1.10.0.custom.min.js','all-page.js']); ?>
    <script type="text/javascript">
        $('#btnBuy').on('click',function(){
            $(this).prop('disabled',true);
            /*$.get("/console/ajax/Security/firewall/getFirewallByVpc?vpc="+$("#txtvpc").attr("code"), function(data){
                console.log(data);
                if(data=="1"){
                    alert("当前VPC已经存在防火墙，不能多次创建");
                }else{
                    $.getJSON("/console/home/getUserLimit", function(data){
                        if(data.fire_used + 1 > data.data.fire_budget ){
                            alert("配额不足 \r\n 防火墙 数量配额："+ data.data.fire_budget+" 已使用："+data.fire_used);
                            $("#btnBuy").prop('disabled',false);
                        }else{
                            $.getJSON("/console/ajax/network/subnet/getVpsSubnetsCountByVpc?vpc="+$("#txtvpc").attr("code"), function(data){
                                if(data >14){
                                    alert("当前VPC下已创建了15个子网")
                                    $("#btnBuy").prop('disabled',false);
                                }else{
                                    addCar(false);
                                }
                            });
                        }
                    });
                }
            });*/
            addCar(false);
        });







        $('#btnAddcar').on('click',function(){
            addCar(true);
        });


        //添加清单
        function addCar(type){
            var goods_id = $("#txtgoods_id").val();
            $.ajax({
                type:"post",
                url:"/orders/addShoppingCar",
                async:true,
                data:{
                    is_console:1,
                    goods_id:goods_id,
                    attr:{
                        "agentCode":$('#txtcs').val(),
                        "regionCode":$("#txtdy").attr("code"),
                        "fusionType":$('#fusionType').val(),
                        "securitygroupName":$("#securitygroup_name").val(),
                        "vpcCode":$("#txtvpc").attr("code"),
                        "note":$('#note').val(),
                        "token":"<?= $token?>"
                    },
                    type:type
                },
                success: function (data) {
                    data= $.parseJSON(data);
                    if(type==true){
                        $("#number").html(data.number);
                    }else{
                        setTimeout(function() {
                            window.location.href=data.url;
                        }, 1000)
                    }
                }
            });
        }

        $('.bk-form-row-cell').on('blur','input',function(){
            if($(this).val()!=''){
                $(this).next().html('');
            }
        });



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

    <?php $this->end(); ?>