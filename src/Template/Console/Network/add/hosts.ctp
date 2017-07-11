<!-- 主机 新增 -->

<?= $this->element('network/lists/left',['active_action'=>'hosts']); ?>
<?= $this->Html->script(['controller/controller.js']); ?>

<div class="wrap-nav-right " ng-app>
    <div class="container-wrap  wrap-buy">
        <a href="<?= $this->Url->build(['controller'=>'network','action'=>'lists','hosts']); ?>" class="btn btn-addition">返回主机列表</a>
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
                                                <li class="active" ng-init="buyTyle=<?= $key ?>" ng-click="changeCharging(<?= $key ?>)" data-val="<?= $key ?>"><?= $value ?></li>
                                                <?php }else{ ?>
                                                <li class="" ng-click="changeCharging(<?= $key ?>)" data-val="<?= $key ?>"><?= $value ?></li>
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
                        <tr>
                            <td class="row-message-left">基本设置</td>
                            <td class="row-message-right" colspan="3">
                                <div class="ng-binding">
                                    <div class="clearfix">
                                        <label for="" class="pull-left">CPU:</label>
                                        <div class="bk-form-row-cell">
                                            <div ng-repeat="host in hostList | filter: {company.name : currentCompany.name }">
                                                <ul class="clearfix city" ng-repeat="area in host.area | filter: {areaCode : currentArea.areaCode , name : currentArea.name} :true">
                                                    <li ng-repeat="set in area.set" ng-class="{active:$first}" ng-click="changeSet(set)">
                                                        {{set.cpu}}核
                                                    </li>
                                                </ul>
                                            </div>
                                            <span id="set-warning"></span>
                                        </div>
                                    </div>
                                    <div class="clearfix">
                                        <label for="" class="pull-left">内存:</label>
                                        <div class="bk-form-row-cell">
                                            <div ng-repeat="host in hostList | filter: { company.name : currentCompany.name} :true">
                                                <div ng-repeat="area in host.area | filter: {areaCode : currentArea.areaCode , name : currentArea.name}">
                                                    <ul class="clearfix city" ng-repeat="set in area.set | filter: cpuFilter">
                                                        <li  ng-class="{active:$first}"  ng-repeat="rom in set.rom" ng-click="changeRom(rom)">
                                                            {{rom.num}} GB
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix">
                                        <label for="" class="pull-left ">实例名称：</label>
                                        <div class="bk-form-row-cell">
                                            <input id="txtname" type="text"  name="textname" maxlength="15"/>
                                            <span class="text-danger txtname"></span>
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
                                            <div class="clearfix">
                                                <label for="" class="pull-left ">EIP名称:</label>
                                                <div class="bk-form-row-cell">
                                                    <div id="eip-wrapper">

                                                    </div>
                                                    <div style="margin-top:15px;">
                                                        <div >
                                                            <a id="add-eip" class="host-add-button active" href="javascript:void(0)">
                                                                <i class="icon-plus"></i>添加EIP</a>
                                                            <a id="cancel-eip" class="host-add-button hide " href="javascript:void(0)">
                                                                <i class="icon-remove"></i>取消EIP</a>
                                                        </div>
                                                        <input type="hidden" id="new_num" value="">

                                                    </div>
                                                    <p><i class="icon-info-sign"></i>&nbsp;创建EIP，需要满足VPC存在防火墙。</p>
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
                        <!--
                        <tr >
                            <td class="row-message-left">网络</td>
                            <td class="row-message-right">
                                <div class="ng-binding">
                                    <div class="clearfix">
                                        <label style="width:20%" for="" class="pull-left ">选择VPC:</label>
                                        <div style="width:80%" class="bk-form-row-cell">
                                            <div ng-repeat="host in hostList | filter: { company.name : currentCompany.name} :true">
                                                <div ng-repeat="area in host.area | filter: {areaCode : currentArea.areaCode, name : currentArea.name}">
                                                    <select ng-options="vpc.name for vpc in area.vpc" ng-model="vpc" ng-init="vpc = area.vpc[0]" ng-change="changeVpc(vpc)" id="vpc"></select>
                                                    <span class="text-danger" id="vpc-warning"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix">
                                        <label style="width:20%" for="" class="pull-left ">选择网络:</label>
                                        <div style="width:80%" class="bk-form-row-cell">
                                            <div ng-repeat="host in hostList | filter: { company.name : currentCompany.name} :true">
                                                <div ng-repeat="area in host.area | filter: {areaCode : currentArea.areaCode, name : currentArea.name}">
                                                    <div ng-repeat="vpc in area.vpc | filter: {vpCode : currentVpc.vpCode}">
                                                        <select ng-options="net.name for net in vpc.net" ng-model="net" ng-init="net = vpc.net[0]" ng-change="changeNet(net)" id="net"></select>
                                                        <span class="text-danger" id="net-warning"></span>
                                                        <p ng-repeat="net in vpc.net | filter: net.netCode : currentNet.netCode"><i class="icon-info-sign"></i>&nbsp;该子网使用{{net.isFusion}}技术</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="row-message-left">添加网络</td>
                            <td class="row-message-right">
                                <div class="ng-binding">
                                    <!-- <div class="clearfix">
                                        <label style="width:20%" for="" class="pull-left ">选择VPC:</label>
                                        <div style="width:80%" class="bk-form-row-cell">
                                            <div ng-repeat="host in hostList | filter: { company.name : currentCompany.name} :true">
                                                <div ng-repeat="area in host.area | filter: {areaCode : currentArea.areaCode, name : currentArea.name}">
                                                    <select ng-options="vpc.name for vpc in area.vpc" ng-model="vpc" ng-init="vpc = area.vpc[0]" ng-change="changeVpc2(vpc)" id="vpc"></select>
                                                    <span class="text-danger" id="vpc-warning"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix">
                                        <label style="width:20%" for="" class="pull-left ">选择网络:</label>
                                        <div style="width:80%" class="bk-form-row-cell">
                                            <div ng-repeat="host in hostList | filter: { company.name : currentCompany.name} :true">
                                                <div ng-repeat="area in host.area | filter: {areaCode : currentArea.areaCode, name : currentArea.name}">
                                                    <div ng-repeat="vpc in area.vpc | filter: {vpCode : currentVpc2.vpCode}">
                                                        <select ng-options="net.name for net in vpc.net" ng-model="net" ng-init="net = vpc.net[0]" ng-change="changeNet2(net)" id="net"></select>
                                                        <span class="text-danger" id="net-warning"></span>
                                                        <p ng-repeat="net in vpc.net | filter: net.netCode : currentNet2.netCode"><i class="icon-info-sign"></i>&nbsp;该子网使用{{net.isFusion}}技术</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="clearfix">
                                        <label style="width:20%" for="" class="pull-left ">选择VPC:</label>
                                        <div style="width:80%" class="bk-form-row-cell">
                                            <select id="vpc2" onchange="loadSubnetPublic($(this).val())"></select>
                                        </div>
                                    </div>
                                    <div class="clearfix">
                                        <label style="width:20%" for="" class="pull-left ">选择网络:</label>
                                        <div style="width:80%" class="bk-form-row-cell">
                                            <select id="net2" onchange="setSubnet()"></select>
                                        </div>
                                    </div>

                                </div>
                            </td>
                        </tr>-->
                        <tr >
                            <td class="row-message-left">镜像</td>
                            <td class="row-message-right" colspan="3">
                                <div class="ng-binding">
                                    <div class="clearfix">
                                        <label for="" class="pull-left ">镜像类型:</label>
                                        <div class="bk-form-row-cell">
                                            <div ng-repeat="host in hostList | filter: { company.name : currentCompany.name} :true" style="margin-bottom:15px;">
                                                <div ng-repeat="area in host.area | filter: {areaCode : currentArea.areaCode, name : currentArea.name} :true">
                                                    <ul class="clearfix city">
                                                        <li ng-repeat="imagetype in area.imageType" ng-click="changeImageType(imagetype)" ng-class="{active:$first}">{{imagetype.name}}</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <p><i class="icon-info-sign"></i>&nbsp;系统镜像即基础操作系统。自定义镜像则在基础操作系统上，集成了运行环境和各类软件。</p>
                                        </div>
                                    </div>
                                    <div class="clearfix">
                                        <label for="" class="pull-left ">业务分类:</label>
                                        <div class="bk-form-row-cell">

                                            <div ng-repeat="host in hostList | filter: { company.name : currentCompany.name} :true" style="margin-bottom:15px;">
                                                <div ng-repeat="area in host.area | filter: {areaCode : currentArea.areaCode, name : currentArea.name} :true">
                                                    <ul class="clearfix city" ng-repeat="imagetype in area.imageType | filter: {name : currentImageType.name}">
                                                        <li ng-repeat="os in imagetype.Os"  ng-click="changeOs(os)" ng-class="{active:$first}">{{os.name}}</li>
                                                    </ul>
                                                </div>
                                            </div>

                                        </div>
                                        <label for="" class="pull-left ">镜像名:</label>
                                        <div class="bk-form-row-cell">
                                            <div ng-repeat="host in hostList | filter: { company.name : currentCompany.name} :true">
                                                <div ng-repeat="area in host.area | filter: {areaCode : currentArea.areaCode, name : currentArea.name} :true">
                                                    <div ng-repeat="imagetype in area.imageType | filter: {name : currentImageType.name}">
                                                        <div ng-repeat="os in imagetype.Os | filter: {name : currentOs.name}">
                                                            <select ng-options="type.name for type in os.types" ng-model="type" ng-change="changeOsTypes(type)" ng-init="type = os.types[0]"></select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr >
                            <td class="row-message-left">存储</td>
                            <td class="row-message-right" colspan="3">
                                <div class="ng-binding">
                                    <!--<div class="clearfix">-->
                                        <!--<label for="" class="pull-left ">系统盘:</label>-->
                                        <!--<div class="bk-form-row-cell">-->
                                            <!--<div class="modal-form-group">-->
                                                <!--<label style="font-size:12px;">容量大小:</label>-->
                                                <!--<div class="slider-area">-->
                                                    <!--<div id="slidersys"></div>-->
                                                <!--</div>-->
                                                <!--<div class="amount pull-left">-->
                                                    <!--<input type="text" id="sysdisk" style="width:80px;margin-right: 10px;" placeholder="40" disabled="disabled"> GB-->
                                                <!--</div>-->
                                            <!--</div>-->
                                        <!--</div>-->
                                    <!--</div>-->
                                    <div class="clearfix">
                                        <label for="" class="pull-left ">数据盘:</label>
                                        <div class="bk-form-row-cell">
                                            <div id="disks-wrapper">

                                            </div>
                                            <div style="margin-bottom:15px;">
                                                <div >
                                                    <a id="add-disks" class="host-add-button active" href="javascript:void(0)">
                                                        <i class="icon-plus"></i>添加硬盘</a>
                                                </div>
                                                <p><i class="icon-info-sign"></i>&nbsp;您还可以选择<span id="disksOtherNums">8</span>块。自动分配设备名</p>
                                                <input type="hidden" id="new_num" value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <!-- <tr >

                            <td class="row-message-left">模式</td>
                            <td class="row-message-right">
                                <div class="ng-binding">
                                    <div class="clearfix">
                                        <label for="" class="pull-left ">计费方式:</label>
                                          <div class="bk-form-row-cell">
                                            <ul class="clearfix city">
                                                <li class="active">按量计费</li>
                                                <li>包年计费</li>
                                            </ul>
                                          </div>
                                    </div>
                                </div>
                            </td>
                        </tr> -->
                        <tr >
                            <td class="row-message-left">购买量</td>
                            <td class="row-message-right" colspan="3">
                                <div class="ng-binding">
                                    <div class="clearfix">
                                        <label for="" class="pull-left ">数量:</label>

                                        <div class="bk-form-row-cell">
                                            <input type="number" id="txtnum" min="1" max="999" ng-model="num" name="txtnum" ng-init="num=1" /> 台
                                            <span class="text-danger txtnum"></span>
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
                    <ul class="goods-detail" id="goods-detail">
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
                            <span class="deleft">配置：</span>
                            <span class="deright">{{currentSet.cpu}}核 & {{currentRom.num}}GB</span>
                            <input id="txtcpu" type="hidden" value="{{currentSet.cpu}}"  />
                            <input id="txtrom" type="hidden" value="{{currentRom.num}}"  />
                            <input id="txtypecode" type="hidden" value="{{currentSetCode}}"  />
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
                            <span class="deleft">镜像：</span>
                            <span class="deright">{{currentOsTypes.name}}</span>
                            <input id="txtimage" type="hidden" value="{{currentOsTypes.name}}" code="{{currentOsTypes.typeCode}}" />
                        </li>
                        <li>
                            <span class="deleft">购买量：</span>
                            <span class="deright"> {{num}} 台</span>
                            <input id="txtnumber" type="hidden" value="{{num}}"  />
                        </li>
                        <li>
                            <span class="deleft">单台原价：</span>
                            <span class="deright">{{instancePrice+imagePrice}}{{unit}}</span>
                        </li>
                        <li>
                            <span class="deleft">单台原价明细：</span>
                            <span class="deright">----------------------</span>
                        </li>
                        <li>
                            <span class="deleft">计算能力：</span>
                            <span class="deright" id="instancePay">{{instancePrice}}{{unit}}</span>
                            <input id="txtinstancePay" type="hidden" value="{{instancePrice}}" code="" />
                        </li>
                        <li>
                            <span class="deleft">系统镜像：</span>
                            <span class="deright" id="imagePay">{{imagePrice}}{{unit}}</span>
                            <input id="txtimagePay" type="hidden" value="{{imagePrice}}" code="" />
                            <input id="txtvpc" type="hidden" value="{{currentVpc.name}}" code="{{currentVpc.vpCode}}" />
                            <input id="txtos" type="hidden" value="{{currentOs.name}}" />
                            <input id="txtimageType" type="hidden" value="{{currentImageType.name}}" />
                        </li>
                        <li>
                            <span class="deleft">系统盘：</span>
                            <span class="deright" id="sysdisknum">40GB</span>
                            <input id="txtsysdisk" type="hidden" value="40" code="" />
                        </li>

                        <li>
                            <span class="deleft">存储单价：</span>
                            <span class="deright" id="instancePay">{{currentDisksPrice}}{{unit}}</span>
                            <input id="txtdisksPrice" type="hidden" value="{{currentDisksPrice}}" code="" />
                        </li>
                    </ul>
                    <button id="btnBuy" class="btn btn-oriange">确认创建</button>
                </div>
            </div>
        </div>
    </div>



    <?php  $this->start('script_last'); ?>
    <?=$this->Html->script(['angular.min.js','controller/controller.js','jquery-ui-1.10.0.custom.min.js']); ?>

    <script type="text/javascript">
        //单个存储最大容量
        var max_cup=1000;
        $.getJSON("/console/home/getUserLimit", function(data){
            if(data.data.disks_cap_bugedt) {
                max_cup = data.data.disks_cap_bugedt;
            }

        });
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
        var disksMaxNums = 8;
        $("#add-disks").click(function(){
            var currentNums = $("#disks-wrapper").children().length;
            var currentNums_disk=currentNums;
            var min=0;
            var del= $('#new_num').val();
            var arr=del.split(',');
            if(del!='') {
                $.each(arr, function (i, n) {
                    if(n<min || min==0){
                        min=n;
                    }

                });

            }
            if(min>0){
                del=del.replace(','+min,'');
                $('#new_num').val(del);
                currentNums_disk=min;
            }

            var element = "<div class=\"modal-form-group\"><label class=\"pull-left\" style=\"font-size:12px;\">容量大小:</label><div class=\"slider-area\"><div id=\"slider\" class=\"slider\" num=\""+(currentNums_disk+1)+"\"></div>                                            </div><div class=\"amount pull-left\"><input type=\"text\" name=\"amount\" style=\"width:80px;margin-right: 10px;\" id=\"amount\" readonly=\"true\" placeholder=\"10\" >GB</div><button  class=\"btn cancel-disks-btn\"><i class=\"icon-remove\"></i></button></div>";



            if(currentNums < disksMaxNums){
                $("#add-disks").addClass('active');
                $("#disks-wrapper").append(element);
                var nums = disksMaxNums - currentNums - 1;
                $("#disksOtherNums").html(nums);
                $('#goods-detail').append(" <li id=\"li"+(currentNums_disk+1)+"\"><span class=\"deleft\">存储：</span> <span class=\"deright\" id=\"disk"+(currentNums_disk+1)+"\">10GB</span> </li>");
            }
            if(currentNums + 1 >= disksMaxNums){
                $("#add-disks").removeClass('active');
            }
            initAddDisksElement();
        });

        $('#add-eip').click(function () {
            var element = '<input id="txteipname" type="text"  name="texteipname" maxlength="15"/><span class="text-danger" id="eip-warning"></span>'
			element = element + '<div class="clearfix"><label class="pull-left ">带宽上限:</label><div class="slider-area bk-form-row-cell">';
            element = element + '<div id="slider-eip"></div></div><div class="amount pull-left"><input type="text" id="amount-eip" placeholder="1" style="width:80px;margin-right: 10px;">  Mbps';
            element = element + '</div></div>';

            bandwidth = 1;
        	$('#amount-eip').val(bandwidth);
        	$('#txtBandwidth').html(bandwidth);
       	 
            $.ajax({
                type:"post",
                url:"/console/ajax/network/hosts/hasFirewallEcs",
                async:true,
                data:{
                    subnetCode:$("#txtnet").attr("code"),
                    regionCode:$("#txtdy").val()
                },
                success: function (data) {
                    data= $.parseJSON(data);
                    if(data.code == "1"){
                        layer.msg(data.msg);
                    }else{
                        if($('#add-eip').hasClass("active")){
                            $("#eip-wrapper").html(element);
                            $("#add-eip").addClass('hide');
                            $("#cancel-eip").removeClass("hide");

                            $('#goods-detail').append("<li id=\"li-eip\"><span class=\"deleft\">公网IP：</span><span class=\"deright\" id=\"eip-size\">1Mbps</span> </li>");

                            $("#slider-eip").slider({
                                min: 1,
                                max: 500,
                                step: 1,
                                orientation: "horizontal",
                                range: "min",
                                animate: true,
                                slide: function(event, ui) {
                                    $("#amount-eip").val(ui.value);
                                    $('#eip-size').html(ui.value+"Mbps");
                                }
                            });

                            $("#amount-eip").keyup(function() {
                                var a = /(^[1-9]([0-9]*)$|^[0-9]$)/;
                                var amountVal = $(this).val();
                                $(this).blur(function() {
                                    if (a.test(amountVal) == false || amountVal < 1) {
                                        $("#amount-eip").addClass('red');
                                    } else if (amountVal > 500) {
                                        $("#amount-eip").addClass('red');
                                        $("#eip-size").html(500+"Mbps");
                                        $("#amount-eip").val(500)
                                    } else {
                                        $("#amount-eip").val(amountVal)
                                        $("amount-eip").removeClass('red');
                                    }
                                })
                                $("#eip-size").html(amountVal+"Mbps");
                                $("#slider-eip").slider({
                                    value: amountVal
                                });
                            });
                            
                        }
                    }
                }
            });
        });
        
        $("#cancel-eip").click(function () {
            $("#cancel-eip").addClass('hide');
            $("#add-eip").removeClass("hide");
            clearEipInput();
        })

        function clearEipInput()
        {
            $("#eip-wrapper").html("");
            $('#add-eip').addClass('active');
            $("#txteipname").val("");
            $("#cancel-eip").addClass('hide');
            $("#add-eip").removeClass("hide");
            $("#li-eip").remove();
        }

        $("#slidersys").slider({
            //      value: $("#amount").val(),
            min: 40,
            max: 500,
//        max:1000,
            step: 1,
            orientation: "horizontal",
            range: "min",
            animate: true,
            slide: function(event, ui) {
                var val=ui.value-ui.value%10;
                $("#sysdisk").val(val);
                $("#txtsysdisk").val(val);

                $("#sysdisknum").html(val+'GB');
            }
        });

        function initAddDisksElement(){
            $(".cancel-disks-btn").click(function(){
                $('#btnBuy').removeAttr('disabled');
                var currentNums = $("#disks-wrapper").children().length;
                if(currentNums <= disksMaxNums){
                    $("#add-disks").addClass('active');
                }else{
                    $("#add-disks").removeClass('active');
                }
                var nums = disksMaxNums - currentNums + 1;
                $(this).parent().remove();

                var disk_num=$(this).parent().children('div').children('#slider').attr('num');

                if($('#new_num').val().indexOf(disk_num)==-1) {
                    var new_num=$('#new_num').val()+','+disk_num;
                    $('#new_num').val(new_num);
                }
                var disk_id='#li'+disk_num;
                $(disk_id).remove();
                $("#disksOtherNums").html(nums);

            });

            $(".slider").slider({
                //      value: $("#amount").val(),
                min: 10,
                max: max_cup,
//        max:1000,
                step: 1,
                orientation: "horizontal",
                range: "min",
                animate: true,
                slide: function(event, ui) {
                    var val=ui.value-ui.value%10;
                    $(this).parent().next().children('input').val(val);

                    var num=$(this).attr('num');
                    var id='#disk'+num;
                    $(id).html(val+'GB');
                }
            });
        }

        //initAddDisksElement();


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
            var currentNums = $("#disks-wrapper").children().length;//存储个数
            var eipname = $("#txteipname").val();//eip名称
            $.getJSON("/console/home/getUserLimit", function(data){
                if(Number($("#txtcpu").val())*Number($('#txtnumber').val())+data.cpu_used > data.data.cpu_bugedt || (Number($("#txtrom").val())*Number($('#txtnumber').val())+data.memory_used) > data.data.memory_buget){
                    alert("配额不足 \r\n cpu 配额："+ data.data.cpu_bugedt+" 已使用："+data.cpu_used+" \r\n 内存 配额：" + data.data.memory_buget+" 已使用："+data.memory_used);
                    $(this).prop('disabled',false);
                }else if(((currentNums)*Number($('#txtnumber').val())+data.disks_used)>data.data.disks_bugedt){
                    alert("配额不足 \r\n 磁盘配额: "+ data.data.disks_bugedt+"个 已使用: "+data.disks_used);
                    $(this).prop('disabled',false);
                }else if(eipname !=undefined && (Number($('#txtnumber').val())+data.eip_used)>data.data.eip_budget){
                    alert("配额不足 \r\n EIP配额: "+ data.data.eip_budget+"个 已使用: "+data.eip_used);
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
            var txtnum  = $('#txtnum').val();
            var txtypecode = $('#txtypecode').val();
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
            var eipname = $("#txteipname").val();
            console.log(eipname);
            if(eipname != undefined && eipname == ""){
                $('#eip-warning').html('请输入EIP名称');
                validate =false;
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
            if(txtnum==''){
                $(".txtnum").html('请输入数量');
                validate =false;
            }else{
                $(".txtnum").html('');
            }
            if(txtypecode==''){
                $('#set-warning').html('<i class="icon-info-sign"></i>&nbsp;套餐信息不存在，请联系管理员');
                validate = false;
            }else{
                $('#set-warning').html('');
            }
            var disks = "";
            $('input[name="amount"]').each(function(index,element){
                var size = $(element).val();
                if(size =="" || size < 10 || size > 1000){
                    size = 10;
                }
                disks += size + ",";
            })

            if(validate){
                $.ajax({
                    type:"post",
                    url:"/orders/addShoppingCar",
                    async:true,
                    data:{
                        is_console:1,
                        goods_id:goods_id,
                        attr:{
                            "ecsName":$("#txtname").val(),
                            "imageCode":$("#txtimage").attr("code"),
                            "bandwidth":0,
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
                            "subnetCode2":$("#txtnet22").val(),
                            "imagePay":$("#txtimagePay").val(),
                            "instancePay":$("#txtinstancePay").val(),
                            "instanceName":$("#instanceTypeCode").val(),
                            "vpcCode":$("#txtvpc").attr("code"),
                            "vpcName":$("#txtvpc").val(),
                            "billCycleName":$("#span_billCycle").html(),
                            "OsName":$("txtos").val(),
                            "imagetypeName":$("#txtimageType").val(),
                            "totalPay":parseFloat($("#txtinstancePay").val())+parseFloat($("#txtimagePay").val()),
                            "disks":disks,
                            "disksPrice":$("#txtdisksPrice").val(),
//                            "sysdisk":$("#txtsysdisk").val(),
                            "eipname":$("#txteipname").val(),
                            "bandwidth" : $("#amount-eip").val(),
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
    <?php $this->end(); ?>