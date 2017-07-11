<!-- 主机 新增 -->
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
		<div class="clearfix buy-theme" ng-controller="elbService">
			<div class="pull-left theme-left">
				<div class="">
				<table>
				<input type="hidden" value="<?= $goods_id ?>" id="txtgoods_id"></input>
				<input type="hidden" value='<?= $instanceTypeCode ?>' id="txtinstanceTypeCode"></input>
                <input type="hidden" value='<?= $imageCode ?>' id="txtimageCode"></input>
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
                            <td class="row-message-right" colspan="3">
                                <div class="ng-binding">
                                    <div class="clearfix location-relative">
                                        <label for="" class="pull-left">厂商:</label>
                                        <div class="bk-form-row-cell">
                                            <ul class="clearfix city">
                                                <li ng-repeat="host in hostList" ng-class="{active:currentId == host.id}" ng-option="host.company.regionCode" ng-click="changeHost(host)">
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
						<td class="row-message-right">
							<div class="ng-binding">
							    <div class="clearfix">
							    	<label for="" class="pull-left ">名称：</label>
							    	<div class="bk-form-row-cell">
							    		<input id="txtname" ng-model="elbname" type="text"  name="textname" ng-init="elbname = '<?= isset($config['lbsName']) ? $config['lbsName'] : '';?>'"/>
							    		<span class="text-danger txtname"></span>
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
								    	<div ng-repeat="host in hostList | filter: { company.name : currentCompany.name}">
								    		<div ng-repeat="area in host.area | filter: {areaCode : currentArea.areaCode, name : currentArea.name}">
										    	<select ng-options="vpc.name for vpc in area.vpc" ng-model="vpc" ng-init="vpc = currentVpc" ng-change="changeVpc(vpc)" id="vpc"></select>
										    	<span class="text-danger" id="vpc-warning"></span>
										    </div>
								    	</div>
									</div>
								</div>
								<div class="clearfix">
									<label for="" class="pull-left ">选择网络:</label>
								    <div class="bk-form-row-cell">
								    	<div ng-repeat="host in hostList | filter: { company.name : currentCompany.name}">
								    		<div ng-repeat="area in host.area | filter: {areaCode : currentArea.areaCode, name : currentArea.name}">
								    			<div ng-repeat="vpc in area.vpc | filter: {vpCode : currentVpc.vpCode}">
											    	<select ng-options="net.name for net in vpc.net" ng-model="net" ng-init="net = currentNet" ng-change="changeNet(net)" id="net"></select>
											    	<span class="text-danger" id="net-warning"></span>
											    </div>
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
					<ul class="goods-detail">
						<li>
                            <span class="deleft">部署区位：</span>
                            <span class="deright">{{currentCompany.name}}-{{currentArea.name}}</span>
                            <input type="hidden" value="{{currentCompany.name}}-{{currentArea.name}}" id="area" code="{{currentAreaCode}}">
                            <input type="hidden" id="txtRegionCode" value="{{currentAreaCode}}">
                            <input type="hidden" id="csCode" value="{{currentCompanyCode}}">
                            <input id="txtcs" type="hidden" value="{{currentCompany.name}}" code="{{currentCompanyCode}}" />
                            <input id="txtdy" type="hidden" value="{{currentArea.name}}" code="{{currentAreaCode}}" />
                            <input id="txtcsid" type="hidden" value="{{currentId}}"/>
                        </li>
                        <li>
                            <span class="deleft">名称：</span>
                            <span class="deright">{{elbname}}</span>
                            <input type="hidden" id="txtElbName" value="{{elbname}}">
                        </li>
                        <li>
                            <span class="deleft">版本：</span>
                            <span class="deright"><?= $version['name'] ?></span>
                            <input type="hidden" id="txtVersionName" value="<?= $version['name'] ?>">
                        </li>
						<li>
                            <span class="deleft">所在VPC：</span>
                            <span class="deright">{{currentVpc.name}}</span>
                            <input id="txtvpc" type="hidden" value="{{currentVpc.name}}" code="{{currentVpc.vpCode}}" />
                        </li>
                        <li>
                            <span class="deleft">所在子网：</span>
                            <span class="deright">{{currentNet.name}}</span>
                            <input id="txtnet" type="hidden" value="{{currentNet.name}}" code="{{currentNet.netCode}}" />
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
					<button id="btnBuy" class="btn btn-oriange">确认创建</button>
				</div>
			</div>
		</div>
	</div>



<?php
$this->start('script_last');
echo $this->Html->script(['jquery-ui-1.10.0.custom.min.js', 'goods/common.js']);
?>
<script type="text/javascript">

function elbService($scope, $http) {
	$http.get("/console/ajax/network/hosts/createHostsArray.json").success(
		// $http.get("/json/data-new.json").success(
		function(data) {
			$scope.hostList = data;


			$scope.currentId = <?php if(isset($config['csid'])) {echo "'".$config['csid']."'";} else { ?> data[0].id <?php }?>;//赋值厂商
			$scope.currentCompanyCode =  <?php if (isset($config['csCode']) && !empty($config['csCode'])){ echo "'".$config['csCode']."'";} else { ?>data[0].company.companyCode<?php }?>;
			$scope.priceList =  data[0].company.price;
			$scope.currentPrice = data[0].company.price[0];
			$scope.currentAreaCode = <?php if (isset($config['regionCode']) && !empty($config['regionCode'])){ echo "'".$config['regionCode']."'";} else { ?>data[0].area[0].areaCode<?php }?>;
			$scope.currentPriceId = <?php if (isset($config['priceId']) && !empty($config['priceId'])){ echo "'".$config['priceId']."'";} else {echo 1;}?>;


			$scope.currentVpCode = <?php if(isset($config['vpcCode'])) {echo "'".$config['vpcCode']."'";} else { ?> data[0].area[0].vpc[0].vpCode <?php }?>;//vpcCode
            $scope.currentSubnetCode = <?php if(isset($config['subnetCode'])) {echo "'".$config['subnetCode']."'";} else { ?> data[0].area[0].vpc[0].net[0].netCode <?php }?>;//subnetCode
            

			for (var h in data) {
                if (data[h].company.companyCode == $scope.currentCompanyCode) {
                    $scope.currentCompany = data[h].company;
                    $scope.currentCompanyCode = data[h].company.companyCode;
                    $scope.priceList =  data[h].company.price;
                    for (var p in data[h].company.price) {
						if (data[h].company.price[p].id == $scope.currentPriceId) {
							$scope.currentPrice = data[h].company.price[p];
						}
					}
                    area = data[h].area;
                }
            }
			for (var a in area) {
                if (area[a].areaCode == $scope.currentAreaCode) {
                    $scope.currentArea = area[a];
                    $scope.currentAreaCode = area[a].areaCode;
                    areaOne = area[a];
                }
            }

			if (areaOne.vpc.length != 0) {
                for (var v in areaOne.vpc) {
                    if (areaOne.vpc[v].vpCode == $scope.currentVpCode) {
                        $scope.currentVpc = areaOne.vpc[v];
                        for (var n in areaOne.vpc[v].net) {
                            if (areaOne.vpc[v].net[n].netCode == $scope.currentSubnetCode) {
                                $scope.currentNet = areaOne.vpc[v].net[n];
                            }
                        }
                    }
                }
            }

		}
	);

	$scope.changeHost = function(obj) {
		$scope.currentId = obj.id;
		$scope.currentCompany = obj.company;
		$scope.currentCode = obj.company.companyCode;

		$scope.currentArea = obj.area[0];
		$scope.currentAreaCode = obj.area[0].areaCode;

		$scope.currentVpc = obj.area[0].vpc[0];
		$scope.currentNet = obj.area[0].vpc[0].net[0];

		$scope.priceList = obj.company.price;
		$scope.currentPrice = obj.company.price[0];
	}

	$scope.changeArea = function(obj) {
		$scope.currentArea = obj;
		$scope.currentAreaCode = obj.areaCode;
		$scope.currentVpc = obj.vpc[0];
		$scope.currentNet = obj.vpc[0].net[0];
	}

	$scope.changeVpc = function(obj) {

		$scope.currentVpc = obj;
		$scope.currentNet = obj.net[0];

	}

	$scope.changeNet = function(obj) {

		$scope.currentNet = obj;
	}

	$scope.changePrice = function(obj) {
		$scope.currentPrice=obj;
	}
}

$('#btnBuy').on('click',function(){
	addCar();
});


//添加清单
function addCar(){

	var txtname = $('#txtname').val();
	var validate = true;
	var goods_id = $("#txtgoods_id").val(); //id
	var vpc = $('#vpc').val();
	var net = $('#net').val();
	//获取商品配置信息

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
	if(validate){
		var parames = new Array();

    	parames['csid'] = $('#txtcsid').val();
    	parames['ecsName'] = "负载均衡实例";
    	parames['regionCode'] = $("#txtdy").attr("code");
    	parames['csName'] = $("#txtcs").val();
    	parames['csCode'] = $("#txtcs").attr("code");
    	parames['dyCode'] = $("#txtdy").attr("code");
    	parames['dyName'] = $("#txtdy").val();
    	parames['lbsName'] = $("#txtname").val(); // 展示用

    	parames['vpcCode']          = $("#txtvpc").attr("code");
        parames['vpcName']          = $("#txtvpc").val();
        parames['subnetCode']       = $("#txtnet").attr("code");
        parames['netName']          = $("#txtnet").val();

    	parames['billCycleName'] 	= $("#txtpricename").val(); // 计费周期 展示用
       	parames['priceId'] 			= $("#txtpriceId").val(); // id 筛选用
       	parames['price'] 			= $("#txtprice").val(); // 价格  展示用
       	parames['unit'] 			= $("#txtunit").val(); // 价格单位  展示用

    	parames['version'] 			= <?= $version['id'] ?>;
       	parames['version_name'] 	= $("#txtVersionName").val();
       	
       	parames['imageCode'] 			= $("#txtimageCode").val();
       	parames['instanceTypeCode'] 	= $("#txtinstanceTypeCode").val();

		
    	<?php if(isset($config['order_good_id'])): ?>
        parames['order_good_id']           = '<?=$config['order_good_id']?>';
        <?php endif;?>
//         console.log(parames);
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