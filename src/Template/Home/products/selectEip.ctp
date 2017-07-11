<!-- <?= $this->Html->script(['controller/controller.js']); ?> -->
<div class="index-breadcrumb">
    <ol class="breadcrumb">
        <li><a href="<?= $this->Url->build(['controller'=>'home','action'=>'category'])?>">商品列表</a></li>
        <li><a href="<?= $this->Url->build(['controller'=>'home','action'=>'goods'])?><?php if(isset($this_good_cate)){echo '/',$this_good_cate['id'];}?>"><?php if(isset($this_good_cate)){echo $this_good_cate['name'];}?></a></li>
        <li><a href="<?= $this->Url->build(['controller'=>'home','action'=>'products'])?><?php if(isset($this_good_info)){echo '/',$this_good_info['id'];}?>"><?php if(isset($this_good_info)){echo $this_good_info['name'];}?></a></li>
        <li class="active">商品配置</li>
    </ol>
</div>
<div class="wrap-nav-right" ng-app>
    <div class="container-wrap  wrap-buy">
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
                                            <li ng-repeat="host in hostList" ng-class="{active: currentCompanyCode == host.company.companyCode}" ng-option="host.company.regionCode" ng-click="changeHost(host)">
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
                        <td class="row-message-left">网络</td>
                        <td class="row-message-right">
                            <div class="ng-binding">
                                <div class="clearfix">
                                    <label for="" class="pull-left ">选择VPC:</label>
                                    <div class="bk-form-row-cell">
                                        <div ng-repeat="host in hostList | filter: { company.name : currentCompany.name} :true">
                                            <div ng-repeat="area in host.area | filter: {areaCode : currentArea.areaCode, name : currentArea.name}">
                                                <select ng-options="vpc.name for vpc in area.vpc" ng-model="vpc" ng-init="vpc = currentVpc" ng-change="changeVpc(vpc)" id="vpc"></select>
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
                                        <input type="text" ng-model="subnet" ng-init="subnet='<?php if (isset($config['eipName']) && !empty($config['eipName'])){ echo $config['eipName'];}?>'" id="sub-net"/>
                                        <span class="text-danger sub-net"></span>
                                    </div>
                                </div>
                                <div class="clearfix">
                                  <label class="pull-left ">带宽上限:</label>
                                  <div class="slider-area bk-form-row-cell">
                                    <div id="slider"></div>
                                  </div>
                                  <div class="amount pull-left">
                                    <input type="text" id="amount" placeholder="5"> Mbps
                                    <!-- <span class="warm">1Mbps-300Mbps</span> -->
                                  </div>
                                </div>
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
                            <span class="deleft">部署区位：</span>
                            <span class="deright">{{currentCompany.name}}-{{currentArea.name}}</span>
                            <input type="hidden" value="{{currentCompany.name}}-{{currentArea.name}}" id="area" code="{{currentAreaCode}}">
                            <input type="hidden" id="txtRegionCode" value="{{currentAreaCode}}">
                            <input type="hidden" id="csCode" value="{{currentCompanyCode}}">
                        </li>
                        <li>
                            <span class="deleft">所在VPC：</span>
                            <span class="deright">{{currentVpc.name}} VPC</span>
                            <input id="txtVpcode" type="hidden" value="{{currentVpc.vpCode}}"  />
                            <input id="txtvpcName" type="hidden" value="{{currentVpc.name}}">
                        </li>
                        <li>
                            <span class="deleft">名称：</span>
                            <span class="deright">{{subnet}}</span>
                            <input type="hidden" id="txtEipName" value="{{subnet}}">
                        </li>
                        <li>
                            <span class="deleft">版本：</span>
                            <span class="deright"><?= $version['name'] ?></span>
                            <input type="hidden" id="txtVersionName" value="<?= $version['name'] ?>">
                        </li>
                        <li>
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
<script src="/js/bootstrap.min.js"></script>
<script src="/js/jquery-ui-1.10.0.custom.min.js" type="text/javascript"></script>

<script>



function eipListService($scope, $http) {
	$http.get("/console/ajax/network/eip/createEIPArray.json?dept_id=<?php if(isset($config['dept_id'])){echo $config['dept_id'];}?>").success(
		function(data) {
			$scope.hostList = data;
			
			$scope.currentCompanyCode =  <?php if (isset($config['csCode']) && !empty($config['csCode'])){ echo "'".$config['csCode']."'";} else { ?>data[0].company.companyCode<?php }?>;
			$scope.priceList =  data[0].company.price;
			$scope.currentPrice = data[0].company.price[1];
			$scope.currentAreaCode = <?php if (isset($config['regionCode']) && !empty($config['regionCode'])){ echo "'".$config['regionCode']."'";} else { ?>data[0].area[0].areaCode<?php }?>;
			$scope.currentVpcCode = <?php if (isset($config['vpcCode']) && !empty($config['vpcCode'])){ echo "'".$config['vpcCode']."'";} else { ?>data[0].area[0].areaCode<?php }?>;
			$scope.currentPriceId = <?php if (isset($config['priceId']) && !empty($config['priceId'])){ echo "'".$config['priceId']."'";} else {echo 1;}?>;
			$scope.currentVpc = data[0].area[0].vpc[0];

			for (var h in data) {
                if (data[h].company.companyCode == $scope.currentCompanyCode) {
                    $scope.currentCompany = data[h].company;
                    $scope.currentCompanyCode = data[h].company.companyCode;
                    $scope.priceList =  data[h].company.price;
                    
                    for (var p in data[h].company.price) {
						if (data[h].company.price[p].id == $scope.currentPriceId) {
							$scope.currentPrice = data[h].company.price[p];
                            var bandwidth = <?php if (isset($config['bandwidth']) && !empty($config['bandwidth'])){ echo $config['bandwidth'];} else {echo 1;}?>;
                            $scope.currentTotalPrice = $scope.currentPrice.price * bandwidth;
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
                    if (areaOne.vpc[v].vpCode == $scope.currentVpcCode) {
                        $scope.currentVpc = areaOne.vpc[v];
                    }
                }
            }
		}
	);
	$scope.changeHost = function(obj) {
		$scope.currentCompany = obj.company;
		$scope.currentCode = obj.company.companyCode;
		$scope.currentArea = obj.area[0];
		$scope.currentAreaCode = obj.area[0].areaCode;
		$scope.priceList = obj.company.price;
		$scope.currentPrice = obj.company.price[0];
		if (obj.area[0].vpc.length != 0) {
			$scope.currentVpc = obj.area[0].vpc[0];
		}
	}

	$scope.changeArea = function(obj) {

		$scope.currentArea = obj;
		$scope.currentAreaCode = obj.areaCode;

		if (obj.vpc.length != 0) {
			$scope.currentVpc = obj.vpc[0];
		}
	}

	$scope.changeVpc = function(obj) {
		$scope.currentVpc = obj;
	}
	
	$scope.changePrice = function(obj) {
		$scope.currentPrice=obj;
        var size = $("#amount").val();
	    var totalPrice = $scope.currentPrice.price * size;
        $('#totalPrice').html(totalPrice);
        $('#txtprice_total').val(totalPrice);
	}

}


$('.bk-form-row-cell').on('click',"li",function(){
    $(this).parent().children().removeClass('active');
    $(this).addClass('active');
});
$(function() {
	bandwidth = <?php if (isset($config['bandwidth']) && !empty($config['bandwidth'])){ echo "'".$config['bandwidth']."'";} else {echo 1;}?>;
	$('#amount').val(bandwidth);
	$('#txtBandwidth').html(bandwidth);
	$('#txtDescription').val('<?php if (isset($config['description']) && !empty($config['description'])){ echo $config['description'];}?>');
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
        value: $("#txtBandwidth").html(),
        min: 1,
        max: 500,
        step: 1,
        value: bandwidth,
        orientation: "horizontal",
        range: "min",
        animate: true,
        slide: function(event, ui) {
       	 	var totalPrice = ui.value * $("#txtprice").val();
         	$('#totalPrice').html(totalPrice);
         	$('#txtprice_total').val(totalPrice);
            $("#amount").val(ui.value);
            $(".broadband").html(ui.value);
        }
    });
   
    $('#btnBuy').on('click', function() {
        addCar(false);
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
           	var parames = new Array();
           	parames['is_console'] 		= 1;
           	parames['goods_id'] 		= goods_id;
           	parames['regionCode'] 		= $("#txtRegionCode").val();
           	parames['csCode']       	= $("#csCode").val();
           	parames['dyName'] 			= $('#area').val();
           	parames['eipName'] 			= $("#txtEipName").val();
           	parames['description'] 		= $("#txtDescription").val();
           	parames['bandwidth'] 		= $("#txtBandwidth").html();
           	parames['vpcCode'] 			= $("#txtVpcode").val();
           	parames['vpcName'] 			= $("#txtvpcName").val();
           	parames['billCycleName'] 	= $("#txtpricename").val();
           	parames['priceId'] 			= $("#txtpriceId").val();
           	parames['price'] 			= $("#txtprice").val();
           	parames['price_total'] 		= $("#txtprice_total").val();
           	parames['unit'] 			= $("#txtunit").val();
           	parames['version'] 			= <?= $version['id'] ?>;
           	parames['version_name'] 	= $("#txtVersionName").val();
           
           	<?php if(isset($config['order_good_id'])): ?>
            parames['order_good_id'] 	= '<?=$config['order_good_id']?>';
            <?php endif;?>
        	var url;
           	url = '<?= $url?>';
            $.StandardPost(url,parames);
        }else{
            $('#btnBuy').prop('disabled',false);
        }
    }
})

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
</script>
</body>
</html>