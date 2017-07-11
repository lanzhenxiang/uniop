<!-- 主机 新增 -->

<?= $this->Html->script(['controller/controller.js']); ?>
<div class="index-breadcrumb">
    <ol class="breadcrumb">
        <li><a href="<?= $this->Url->build(['controller'=>'home','action'=>'category'])?>">商品列表</a></li>
        <li><a href="<?= $this->Url->build(['controller'=>'home','action'=>'goods'])?><?php if(isset($this_good_cate)){echo '/',$this_good_cate['id'];}?>"><?php if(isset($this_good_cate)){echo $this_good_cate['name'];}?></a></li>
        <li><a href="<?= $this->Url->build(['controller'=>'home','action'=>'products'])?><?php if(isset($this_good_info)){echo '/',$this_good_info['id'];}?>"><?php if(isset($this_good_info)){echo $this_good_info['name'];}?></a></li>
        <li class="active">商品配置</li>
    </ol>
</div>
<div class="wrap-nav-right " ng-app>
    <div class="container-wrap  wrap-buy">
        <div class="clearfix buy-theme" ng-controller="hostListService">
            <div class="pull-left theme-left">
                <div class="">
                <table>

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
                                                        <?php
                                                            //初始化计费方式
                                                         $billCycle = isset($config['billCycle']) ? $config['billCycle'] : 1;
                                                         if($key == $billCycle){ ?>
                                                            <li class="active" data-val="<?= $key ?>" onclick="getPrice()" ><?= $value ?></li>
                                                        <?php }else{ ?>
                                                            <li class="" data-val="<?= $key ?>"  onclick="getPrice()" ><?= $value ?></li>
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
                                            <li ng-repeat="host in hostList" ng-class="{active: currentId == host.id}" ng-option="host.company.regionCode" ng-click="changeHost(host)">
                                                {{host.company.name}}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="clearfix location-relative">
                                    <label for="" class="pull-left">地域:</label>
                                    <div class="bk-form-row-cell">
                                        <ul class="clearfix city" ng-repeat="host in hostList | filter: { company.name:currentCompany.name} :true">
                                            <li ng-repeat="area in host.area" ng-class="{active: currentAreaCode == area.areaCode}" ng-click="changeArea(area)">
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
                                                <li ng-repeat="set in area.set" ng-class="{active: currentCpu == set.cpu}" ng-click="changeSet(set)">
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
                                        <div ng-repeat="host in hostList | filter: {company.name : currentCompany.name }">
                                            <div ng-repeat="area in host.area | filter: {areaCode : currentArea.areaCode , name : currentArea.name}">
                                                <ul class="clearfix city" ng-repeat="set in area.set | filter: cpuFilter">
                                                    <li  ng-class="{active: currentRom == rom}"  ng-repeat="rom in set.rom" ng-click="changeRom(rom)">
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
                                        <input id="txtname" type="text"  name="textname" ng-modal="computerName" ng-init="computerName=''" maxlength="15"/>
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
                                                            <select class="select-style" ng-options="vpc.name for vpc in area.vpc" ng-model="vpc" ng-init="vpc = currentVpc" ng-change="changeVpc(vpc)" id="vpc"></select>
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
                                                                <select class="select-style" ng-options="net.name for net in vpc.net" ng-model="net" ng-init="net = currentNet" ng-change="changeNet(net)" id="net"></select>
                                                                <span class="text-danger" id="net-warning"></span>
                                                                <p ng-repeat="net in vpc.net | filter: net.netCode : currentNet.netCode"><i class="icon-info-sign"></i>&nbsp;该子网使用{{net.isFusion}}技术</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--扩展-->
                                        <div class="bk-form-row-cell tabBox" >
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
                        <td class="row-message-left">镜像</td>
                        <td class="row-message-right" colspan="3">
                            <div class="ng-binding">
                                <div class="clearfix">
                                    <label for="" class="pull-left ">镜像类型:</label>
                                    <div class="bk-form-row-cell">
                                        <div ng-repeat="host in hostList | filter: { company.name : currentCompany.name} :true" style="margin-bottom:15px;">
                                            <div ng-repeat="area in host.area | filter: {areaCode : currentArea.areaCode, name : currentArea.name} :true">
                                                <ul class="clearfix city">
                                                    <li ng-repeat="imagetype in area.imageType" ng-click="changeImageType(imagetype)" ng-class="{active: currentImagetypeName == imagetype.name}">{{imagetype.name}}</li>
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
                                                <ul class="clearfix city" ng-repeat="imagetype in area.imageType | filter: {name : currentImageType.name} :true">
                                                    <li ng-repeat="os in imagetype.Os | filter:{} :true"  ng-click="changeOs(os)" ng-class="{active: currentOsName == os.name}">{{os.name}}</li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div ng-repeat="host in hostList | filter: { company.name : currentCompany.name} :true">
                                            <div ng-repeat="area in host.area | filter: {areaCode : currentArea.areaCode, name : currentArea.name} :true">
                                                <div ng-repeat="imagetype in area.imageType | filter: {name : currentImageType.name} :true">
                                                    <div ng-repeat="os in imagetype.Os | filter: {name : currentOs.name} :true">
                                                        <select ng-options="type.name for type in os.types" ng-model="type" ng-change="changeOsTypes(type)" ng-init="type = currentOsTypes" class="change-image"></select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr style="display: none">
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
                    <ul class="goods-detail">
                       <!--  <li>
                            <span class="deleft">厂商：</span>
                            <span class="deright">{{currentCompany.name}}</span>
                            <input id="txtcs" type="hidden" value="{{currentCompany.name}}" code="{{currentCode}}" />
                        </li> -->
                        <li>
                            <span class="deleft">部署区位：</span>
                            <span class="deright">{{currentArea.displayName}}</span>
                            <input id="txtdy" type="hidden" value="{{currentArea.displayName}}" code="{{currentAreaCode}}" />
                            <input id="txtcs" type="hidden" value="{{currentCompany.name}}" code="{{currentCode}}" />
                            <input id="txtcsid" type="hidden" value="{{currentId}}"/>
                        </li>
                        <li>
                            <span class="deleft">所在VPC：</span>
                            <span class="deright">{{vpc.name}}</span>
                            <input id="txtvpc" type="hidden" value="{{vpc.name}}" code="{{vpc.vpCode}}" />
                        </li>
                        <li>
                            <span class="deleft">所在子网：</span>
                            <span class="deright">{{currentNet.name}}</span>
                            <input id="txtnet" type="hidden" value="{{currentNet.name}}" code="{{currentNet.netCode}}" />
                        </li>
                        <li>
                            <span class="deleft">主机名称：</span>
                            <span class="deright" id = "computerName">{{computerName}}</span>
                        </li>
                        <li>
                            <span class="deleft">计算能力：</span>
                            <span class="deright">----------------------</span>
                            <input id="txtcpu" type="hidden" value="{{currentSet.cpu}}"  />
                            <input id="txtrom" type="hidden" value="{{currentRom.num}}"  />
                            <input id="txtypecode" type="hidden" value="{{currentSetCode}}"  />
                            <input id="instanceDay" type="hidden" value="{{instanceDay}}"  />
                            <input id="instanceMonth" type="hidden" value="{{instanceMonth}}"  />
                            <input id="instanceYear" type="hidden" value="{{instanceYear}}"  />
                        </li>
                        <li>
                            <span class="deleft">CPU：</span>
                            <span class="deright">{{currentSet.cpu}}核</span>
                        </li>
                        <li>
                            <span class="deleft">内存：</span>
                            <span class="deright">{{currentRom.num}}GB</span>
                        </li>
                        <li>
                            <span class="deleft">系统镜像：</span>
                            <span class="deright">{{currentOsTypes.name}}</span>
                            <input id="txtimage" type="hidden" value="{{currentOsTypes.name}}" code="{{currentOsTypes.typeCode}}" />
                            <input id="imageDay" type="hidden" value="{{currentOsTypes.priceDay}}"  />
                            <input id="imageMonth" type="hidden" value="{{currentOsTypes.priceMonth}}"  />
                            <input id="imageYear" type="hidden" value="{{currentOsTypes.priceYear}}"  />
                            <input type="hidden" id="imagetypeName" value="{{currentImagetypeName}}">
                            <input type="hidden" id="OsName" value="{{currentOsName}}">
                        </li>
                        <li>
                            <span class="deleft">添加网络：</span>
                            <span id="txtnetn22" class="deright" ></span>
                            <input id="txtnet22" type="hidden" value="" code="" />
                        </li>

                        <li style="display: none">
                            <span class="deleft">购买量：</span>
                            <span class="deright"> {{num}} 台</span>
                            <input id="txtnumber" type="hidden" value="{{num}}"  />
                        </li>
                        <li>
                            <span class="deleft">计费方式：</span>
                            <span class="deright" id="span_billCycle">按天计费</span>
                            <input id="txtbillCycle" type="hidden" value="1"/>
                        </li>
                        <li>
                            <span class="deleft">单台原价：</span>
                            <span class="deright" id="totalPay"></span>
                            <input id="txttotalPay" type="hidden" value="" code="" />
                        </li>
                        <li>
                            <span class="deleft">单台原价明细：</span>
                            <span class="deright">----------------------</span>
                            <input id="txtunit" type="hidden" value="">
                        </li>
                        <li>
                            <span class="deleft">计算能力：</span>
                            <span class="deright" id="instancePay"></span>
                            <input id="txtinstancePay" type="hidden" value="" code="" />
                        </li>
                        <li>
                            <span class="deleft">系统镜像：</span>
                            <span class="deright" id="imagePay"></span>
                            <input id="txtimagePay" type="hidden" value="" code="" />
                        </li>
                    </ul>
                    <button id="btnBuy" class="btn btn-oriange">确认</button>
                </div>
            </div>
        </div>
    </div>
<?= $this->Html->css(['bootstrap-table.css']); ?>
<?php  $this->start('script_last'); ?>

<?=$this->Html->script(['bootstrap.js' , 'bootstrap-table.js', 'jquery-ui-1.10.0.custom.min.js']); ?>
<script type="text/javascript">

$('#txtname').blur(function(){
    var data = $('#txtname').val();
    $('#computerName').html(data);
});

function hostListService($scope, $http) {
    $http.get("/console/ajax/network/hosts/createHostsArray.json?dept_id=<?php if(isset($config['dept_id'])){echo $config['dept_id'];}?>").success(
        // $http.get("/json/data-new.json").success(
        function(data) {
            $scope.hostList = data;

                $scope.currentId = <?php if(isset($config['csid'])) {echo "'".$config['csid']."'";} else { ?> data[0].id <?php }?>;//赋值厂商
                $scope.currentAreaCode = <?php if(isset($config['regionCode'])) {echo "'".$config['regionCode']."'";} else { ?> data[0].area[0].areaCode <?php }?>;//赋值区域
                $scope.currentCpu = <?php if(isset($config['cpu'])) {echo "'".$config['cpu']."'";} else { ?> data[0].area[0].set[0].cpu <?php }?>;//cpu
                $scope.currentRomNum = <?php if(isset($config['rom'])) {echo "'".$config['rom']."'";} else { ?> data[0].area[0].set[0].rom[0].num <?php }?>;//内存
                computerName = <?php if(isset($config['ecsName'])) {echo "'".$config['ecsName']."'";} else { ?> '' <?php }?>;//主机名
                $scope.currentVpCode = <?php if(isset($config['vpcCode'])) {echo "'".$config['vpcCode']."'";} else { ?> data[0].area[0].vpc[0].vpCode <?php }?>;//vpcCode
                $scope.currentSubnetCode = <?php if(isset($config['subnetCode'])) {echo "'".$config['subnetCode']."'";} else { ?> data[0].area[0].vpc[0].net[0].netCode <?php }?>;//subnetCode
                $scope.currentImagetypeName = <?php if(isset($config['imagetypeName'])) {echo "'".$config['imagetypeName']."'";} else { ?> data[0].area[0].imageType[0].name <?php }?>;//镜像来源名称
                $scope.currentOsName = <?php if(isset($config['OsName'])) {echo "'".$config['OsName']."'";} else { ?> data[0].area[0].imageType[0].Os[0].name <?php }?>; //镜像类型
                $scope.currentImageCode = <?php if(isset($config['imageCode'])) {echo "'".$config['imageCode']."'";} else { ?> data[0].area[0].imageType[0].Os[0].types[0].typeCode <?php }?>; //镜像类型
                $('#txtnum').val(<?php if(isset($config['number'])) {echo $config['number'];} else { ?> 1 <?php }?>);//数量
                $scope.num = <?php if(isset($config['number'])) {echo $config['number'];} else { ?> 1 <?php }?>;//数量
                $("#txtbillCycle").val(<?php if(isset($config['number'])) {echo $config['number'];} else { ?> 1 <?php }?>)//计费模式

                $('#txtname').val(computerName)
                $('#computerName').html(computerName)

                for (var h in data) {
                    if (data[h].id == $scope.currentId) {
                        $scope.currentCompany = data[h].company;
                        $scope.currentCompanyCode = data[h].company.companyCode;
                        area = data[h].area;
                    }
                }

                for (var a in area) {
                    if (area[a].areaCode == $scope.currentAreaCode) {
                        $scope.currentArea = area[a];
                        $scope.currentAreaCode = area[a].areaCode;
                        set = area[a].set;
                        areaOne = area[a];
                    }
                }

                if (set.length != 0) {
                    angular.element('#set-warning').html('');
                    for (var s in set) {
                        if (set[s].cpu == $scope.currentCpu) {
                            $scope.currentSet = set[s];
                            if (set[s].rom.length != 0) {
                                rom = set[s].rom;
                                for (var r in rom) {
                                    if (rom[r].num == $scope.currentRomNum) {
                                        $scope.currentRom = rom[r];
                                        $scope.currentSetCode = rom[r].setCode;
                                        $scope.instanceDay = rom[r].priceDay;
                                        $scope.instanceMonth = rom[r].priceMonth;
                                        $scope.instanceYear = rom[r].priceYear;
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $scope.currentSet = "";
                    $scope.currentRom = "";
                    $scope.currentSetCode = "";
                    angular.element('#set-warning').html('<i class="icon-info-sign"></i>&nbsp;套餐信息不存在，请联系管理员');
                }

                if (areaOne.vpc.length != 0) {
                    for (var v in areaOne.vpc) {
                        // console.log(v)
                        if (areaOne.vpc[v].vpCode == $scope.currentVpCode) {
                            $scope.currentVpc = areaOne.vpc[v];
                            for (var n in areaOne.vpc[v].net) {
                                if (areaOne.vpc[v].net[n].netCode == $scope.currentSubnetCode) {
                                    initSubnetExtends(areaOne.vpc[v].net[n]);
                                    $scope.vpc = areaOne.vpc[v];
                                    $scope.currentNet = areaOne.vpc[v].net[n];
                                }
                            }
                        }
                    }
                }

                if (areaOne.imageType.length != 0) {
                    for (var it in areaOne.imageType) {
                        if (areaOne.imageType[it].name == $scope.currentImagetypeName) {
                            $scope.currentImageType = areaOne.imageType[it];
                            for (var i in areaOne.imageType[it].Os) {
                                if (areaOne.imageType[it].Os[i].name == $scope.currentOsName) {
                                    $scope.currentOs = areaOne.imageType[it].Os[i];
                                    for (var t in areaOne.imageType[it].Os[i].types) {
                                        if (areaOne.imageType[it].Os[i].types[t].typeCode == $scope.currentImageCode){
                                            $scope.currentOsTypes = areaOne.imageType[it].Os[i].types[t];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                //计费
                getPrice();
        }
    );


    $scope.changeHost = function(obj) {
        $scope.currentId = obj.id;
        $scope.currentCompany = obj.company;
        $scope.currentCode = obj.company.companyCode;

        $scope.currentArea = obj.area[0];
        $scope.currentAreaCode = obj.area[0].areaCode;

        if (obj.area[0].set.length != 0) {
            angular.element('#set-warning').html('');
            $scope.currentSet = obj.area[0].set[0];
            if (obj.area[0].set[0].rom.length != 0) {
                $scope.currentCpu = obj.area[0].set[0].cpu;
                $scope.currentRom = obj.area[0].set[0].rom[0];
                $scope.currentSetCode = obj.area[0].set[0].rom[0].setCode;
                $scope.instanceDay = obj.area[0].set[0].rom[0].priceDay;
                $scope.instanceMonth = obj.area[0].set[0].rom[0].priceMonth;
                $scope.instanceYear = obj.area[0].set[0].rom[0].priceYear;
            }
        } else {
            $scope.currentSet = "";
            $scope.currentRom = "";
            $scope.currentSetCode = "";
            angular.element('#set-warning').html('<i class="icon-info-sign"></i>&nbsp;套餐信息不存在，请联系管理员');
        }


        if (obj.area[0].vpc.length != 0) {
            $scope.currentVpc = obj.area[0].vpc[0];
            if (obj.area[0].vpc[0].net.length != 0) {
                initSubnetExtends(obj.area[0].vpc[0].net[0]);
                $scope.vpc = obj.area[0].vpc[0];
                $scope.currentNet = obj.area[0].vpc[0].net[0];
            }
        }

        if (obj.area[0].imageType[0].Os.length != 0) {
            $scope.currentOs = obj.area[0].imageType[0].Os[0];
            $scope.currentImagetypeName = obj.area[0].imageType[0].name;
            $scope.currentOsName = obj.area[0].imageType[0].Os[0].name;
            if (obj.area[0].imageType[0].Os[0].types.length != 0) {
                $scope.currentOsTypes = obj.area[0].imageType[0].Os[0].types[0];
            }
        }
        //console.log(obj.area[0].vpc[0]);
        //loadSubnetPublic(obj.area[0].vpc[0].vpCode,$scope.currentCode);
    }

    $scope.changeArea = function(obj) {

        $scope.currentArea = obj;
        $scope.currentAreaCode = obj.areaCode;

        if (obj.set.length != 0) {
            angular.element('#set-warning').html('');
            $scope.currentSet = obj.set[0];
            if (obj.set[0].rom.length != 0) {
                $scope.currentCpu = obj.set[0].cpu;
                $scope.currentRom = obj.set[0].rom[0];
                $scope.currentSetCode = obj.set[0].rom[0].setCode;
                $scope.instanceDay = obj.set[0].rom[0].priceDay;
                $scope.instanceMonth = obj.set[0].rom[0].priceMonth;
                $scope.instanceYear = obj.set[0].rom[0].priceYear;
            }
        } else {
            $scope.currentSet = "";
            $scope.currentRom = "";
            $scope.currentSetCode = "";
            angular.element('#set-warning').html('<i class="icon-info-sign"></i>&nbsp;套餐信息不存在，请联系管理员');
        }

        if (obj.vpc.length != 0) {
            $scope.currentVpc = obj.vpc[0];
            if (obj.vpc[0].net.length != 0) {
                initSubnetExtends(obj.vpc[0].net[0]);
                $scope.vpc = obj.vpc[0];
                $scope.currentNet = obj.vpc[0].net[0];
            }
        }

        if (obj.imageType[0].Os.length != 0) {
            $scope.currentOs = obj.imageType[0].Os[0];
            $scope.currentImagetypeName = obj.imageType[0].name;
            $scope.currentOsName = obj.imageType[0].Os[0].name;
            if (obj.imageType[0].Os[0].types.length != 0) {
                $scope.currentOsTypes = obj.imageType[0].Os[0].types[0];
            }
        }
    }

    $scope.changeSet = function(obj) {

        $scope.currentSet = obj;

        if (obj.rom.length != 0) {
            $scope.currentCpu = obj.cpu;
            $scope.currentRom = obj.rom[0];
            $scope.currentSetCode = obj.rom[0].setCode;
            $scope.instanceDay = obj.rom[0].priceDay;
            $scope.instanceMonth = obj.rom[0].priceMonth;
            $scope.instanceYear = obj.rom[0].priceYear;
        }
    }

    $scope.changeRom = function(obj) {

        $scope.currentRom = obj;
        $scope.currentSetCode = obj.setCode;
        $scope.instanceDay = obj.priceDay;
        $scope.instanceMonth = obj.priceMonth;
        $scope.instanceYear = obj.priceYear;

    }

    $scope.changeVpc = function(obj) {

        $scope.currentVpc = obj;
        if (obj.net.length != 0) {
            initSubnetExtends(obj.net[0]);
            $scope.vpc = obj;
            $scope.currentNet = obj.net[0];
        }
        // console.log(obj);
        //loadSubnetPublic(obj.vpCode);
    }

    $scope.changeNet = function(obj) {
        initSubnetExtends(obj);
        $scope.currentNet = obj;
    }

    $scope.changeImageType = function(obj){
        $scope.currentImageType = obj;
        $scope.currentImagetypeName = obj.name;
        $scope.currentOsName = obj.Os[0].name;
        if(obj.Os.length != 0){
            $scope.currentOs = obj.Os[0];
            if (obj.Os[0].types.length != 0) {
                $scope.currentOsTypes = obj.Os[0].types[0];
            }
        }
    }

    $scope.changeOs = function(obj) {
        $scope.currentOs = obj;
        $scope.currentOsName = obj.name;
        if (obj.types.length != 0) {
            $scope.currentOsTypes = obj.types[0];
        }
    }
    $scope.changeOsTypes = function(obj) {

        $scope.currentOsTypes = obj;
    }

    $scope.cpuFilter = function (item) {
      return item.cpu === $scope.currentSet.cpu;
    };
}

$(document).on('click', '.clearfix.city,.clearfix.city.ng-scope','li', function(){
        getPrice();
});

$(document).on('change', '.change-image', function(){
        getPrice();
});



function getPrice(){
	var unit;
    switch ($('#txtbillCycle').val()) {
        case '1':
            unit = '元/天';
            $('#instancePay').html($('#instanceDay').val() + '元/天');
            $('#imagePay').html($('#imageDay').val() + '元/天');
            $('#txtinstancePay').val($('#instanceDay').val());
            $('#txtimagePay').val($('#imageDay').val());
        break;
        case '2':
        	unit = '元/月';
            $('#instancePay').html($('#instanceMonth').val() + '元/月');
            $('#imagePay').html($('#imageMonth').val() + '元/月');
            $('#txtinstancePay').val($('#instanceMonth').val());
            $('#txtimagePay').val($('#imageMonth').val());
        break;
        case '4':
        	unit = '元/年';
            $('#instancePay').html($('#instanceYear').val() + '元/年');
            $('#imagePay').html($('#imageYear').val() + '元/年');
            $('#txtinstancePay').val($('#instanceYear').val());
            $('#txtimagePay').val($('#imageYear').val());
        break;
    }
    $('#txtunit').val(unit);
	$('#totalPay').html(parseFloat($('#txtimagePay').val())+parseFloat($('#txtinstancePay').val())+unit);
    $('#txttotalPay').val(parseFloat($('#txtimagePay').val())+parseFloat($('#txtinstancePay').val()));
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

            <?php if (!empty($config)) {?>
            //设置初始化选中
            $("#subnet-extend-table").bootstrapTable("checkBy", {field:"subnet_code", values:["<?php echo $config['subnetCode2'];?>"]})
            <?php }?>
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
            console.log(id);
        })
$('#btnBuy').on('click',function(){
    addCar(false);
});


//添加清单
function addCar(type){
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

    if(validate){

        var parames = new Array();
        parames['csid']             = $("#txtcsid").val();
        parames['ecsName']          = $("#txtname").val();
        parames['imageCode']        = $("#txtimage").attr("code");
        parames['vpcCode']          = $("#txtvpc").attr("code");
        parames['vpcName']          = $("#txtvpc").val();
        parames['subnetCode']       = $("#txtnet").attr("code");
        parames['instanceTypeCode'] = $("#txtypecode").val();
        parames['regionCode']       = $("#txtdy").attr("code");
        parames['csName']           = $("#txtcs").val();
        parames['dyName']           = $("#txtdy").val();
        parames['cpu']              = $("#txtcpu").val();
        parames['rom']              = $("#txtrom").val();
        parames['netName']          = $("#txtnet").val()
        parames['imageName']        = $("#txtimage").val();
        parames['number']           = $("#txtnumber").val();
        parames['billCycle']        = $("#txtbillCycle").val();
        parames['subnetCode2']      = $("#txtnet22").val();
        parames['subnetName2']		= $('#txtnetn22').html();
        parames['imagePay']         = $("#txtimagePay").val();
        parames['instancePay']      = $("#txtinstancePay").val();
        parames['imagetypeName']    = $('#imagetypeName').val();
        parames['OsName']           = $('#OsName').val();
        parames['billCycleName']    = $('#span_billCycle').html();
        parames['totalPay']         = parseFloat(parames['imagePay'])+parseFloat(parames['instancePay']);
		parames['instance_price'] 	= $('#instancePay').html();
		parames['image_price']		= $('#imagePay').html();
		parames['unit'] 			= $('#txtunit').val();
        
        <?php if(isset($config['order_id'])): ?>
        parames['order_id']         = '<?=$config['order_id'] ?>';
        <?php endif;?>
        <?php if(isset($config['uid'])): ?>
        parames['uid']              = '<?=$config['uid'] ?>';
        <?php endif;?>
        <?php if(isset($config['method'])): ?>
        parames['method']           = '<?=$config['method']?>';
        <?php endif;?>
        <?php if(isset($config['order_good_id'])): ?>
        parames['order_good_id']           = '<?=$config['order_good_id']?>';
        <?php endif;?>
        var url;
        url = '<?= $url?>';
        $.StandardPost(url,parames);
    }else{
        $('#btnBuy').prop('disabled',false);
    }
}

//post 表单
$.extend({
    StandardPost:function(url,args){
        var body = $(document.body),
            form = $("<form method='post'></form>"),
            input;
        form.attr({"action":url});
        for (arg in args)
        {
            input = $("<input type='hidden'>");
            input.attr({"name":arg});
            input.val(args[arg]);
            form.append(input);
        };

        form.appendTo(document.body);
        form.submit();
        document.body.removeChild(form[0]);
    }
});


$('.bk-form-row-cell').on('click',"li",function(){
    $(this).parent().children().removeClass('active');
    $(this).addClass('active');
});

$(function(){
   $('#span_billCycle').html($('#selbillCycle').children('li.active').html());
})
</script>

<?php $this->end(); ?>