
<?= $this->Html->css(['common.css']); ?>
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
                                        <label for="" class="pull-left ">路由器名：</label>
                                        <div class="bk-form-row-cell">
                                            <input type="text" id="router-name" name="txtname" ng-model="router" ng-init="router='<?= isset($config['routerName']) ? $config['routerName'] : '';?>'">
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
                                        <input type="text" id="router-vpc-name" name="vpc_name" ng-model="vpc" ng-init="vpc='<?= isset($config['vpcName']) ? $config['vpcName'] : '';?>'">
                                        <span class="text-danger vpc-name"></span>
                                        <p><i class="fa icon-exclamation-sign"></i>用来说明路由器在网络规划中的用途，比如路由器是用在办公网，还是用在新闻制作网</p>
                                    </div>
                                </div>
                                <div class="clearfix">
                                    <label for="" class="pull-left ">VPC网络地址:</label>
                                    <div class="bk-form-row-cell bk-form-row-small" ng-init="vpcip1=<?= isset($config['vpcip1']) ? $config['vpcip1'] : '172';?>;vpcip2=<?= isset($config['vpcip2']) ? $config['vpcip2'] : '16';?>;vpcip3=0;vpcip4=0;vpcip5=<?= isset($config['vpcip5']) ? $config['vpcip5'] : '20';?>">
                                        <input  type="text" id="vpcip1" ng-model="vpcip1" />
                                        &nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;
                                        <input  type="text" id="vpcip2" ng-model="vpcip2" />
                                        &nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;
                                        <input type="text" disabled="disabled" ng-model="vpcip3" />
                                        &nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;
                                        <input type="text" disabled="disabled" ng-model="vpcip4" />
                                        &nbsp;&nbsp;/&nbsp;&nbsp;<input type="text" id="vpcip5" ng-model="vpcip5" />
                                        <span class="text-danger vpc-ip"></span>
                                    </div>
                                    <div class="bk-form-row-cell">
                                        <p><i class="icon-info-sign"></i>&nbsp;一个VPC包含1个路由器，可以创建252个IP</p>
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
                            <input id="txtcs" type="hidden" value="{{currentCompany.name}}" code="{{currentCompanyCode}}" />
                            <input id="txtdy" type="hidden" value="{{currentArea.name}}" code="{{currentAreaCode}}" />
                            <input id="txtcsid" type="hidden" value="{{currentId}}"/>
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
                            <span class="deleft">版本：</span>
                            <span class="deright"><?= $version['name'] ?></span>
                            <input type="hidden" id="txtVersionName" value="<?= $version['name'] ?>">
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
                </div>
            </div>
        </div>
    </div>
</div>

</div>
<?php
$this->start('script_last');
echo $this->Html->script(['jquery-ui-1.10.0.custom.min.js', 'goods/common.js']);
?>
<script>


function routerListService($scope, $http) {
	$http.get("/console/ajax/network/router/createRouterArray.json").success(
		function(data) {
			$scope.hostList = data;

			$scope.currentId = <?php if(isset($config['csid'])) {echo "'".$config['csid']."'";} else { ?> data[0].id <?php }?>;//赋值厂商
			$scope.currentCompanyCode =  <?php if (isset($config['csCode']) && !empty($config['csCode'])){ echo "'".$config['csCode']."'";} else { ?>data[0].company.companyCode<?php }?>;
			$scope.priceList =  data[0].company.price;
			$scope.currentPrice = data[0].company.price[0];
			$scope.currentAreaCode = <?php if (isset($config['regionCode']) && !empty($config['regionCode'])){ echo "'".$config['regionCode']."'";} else { ?>data[0].area[0].areaCode<?php }?>;
			$scope.currentPriceId = <?php if (isset($config['priceId']) && !empty($config['priceId'])){ echo "'".$config['priceId']."'";} else {echo 1;}?>;;
			

			
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
		}
	);

	$scope.changeHost = function(obj) {
		$scope.currentId = obj.id;
		$scope.currentCompany = obj.company;
		$scope.currentCompanyCode = obj.company.companyCode;

		$scope.currentArea = obj.area[0];
		$scope.currentAreaCode = obj.area[0].areaCode;

		$scope.priceList = obj.company.price;
		$scope.currentPrice = obj.company.price[0];

	}

	$scope.changeArea = function(obj) {
		$scope.currentArea = obj;
		$scope.currentAreaCode = obj.areaCode;
	}

	$scope.changeFirewallTemplateInfo = function(obj) {
		$scope.currentTemplateInfo = obj;
	}

	$scope.changePrice = function(obj) {
		$scope.currentPrice=obj;
	}

}
	

    $('#router-name').blur(function(){
        var data = $('#router-name').val();
        $('#names').html(data);
        $('#name').val(data);
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
         addCar('');
    });


function addCar(data){
    var ip1 = $('#vpcip1').val();
    var ip2 = $('#vpcip2').val();
    var ip5 = $('#vpcip5').val();
    var area = $('#area').val();
    var name = $('#name').val();
    // var firewall_name = $('#firewall_name').val();
    var number = $('#number').val();
    var vpc_name = $('#vpc_name').val();
    var vpc_url = $('#vpc_url').val();
    var price_per = $('#price_per').val();
    var validate = true;
    if(data==''){
        var type=0;
    }else{
        var type=1;
    }
    
    if (!/^[1-9]+\d*$/i.test(ip1) || ip1==null || ip1 > 256 || ip1 < 0 || !/^[1-9]+\d*$/i.test(ip2) || ip2==null || ip2 > 256 || ip2 < 0) {
        $(".vpc-ip").html('请输入大于0小于256的整数');
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
   
    if(vpc_name==''){
        $(".vpc-name").html('请输入VPC名');
        validate =false;
    }else{
        $(".vpc-name").html('');
    }
    if(validate){
    	var parames = new Array();

    	parames['csid'] = $('#txtcsid').val();
    	parames['routerName'] = $('#router-name').val();
    	parames['regionCode'] = $("#txtdy").attr("code");
    	parames['csName'] = $("#txtcs").val();
    	parames['csCode'] = $("#txtcs").attr("code");
    	parames['dyCode'] = $("#txtdy").attr("code");
    	parames['dyName'] = $("#txtdy").val();
    	parames['cidr'] = $("#vpc_url").val();
    	parames['vpcName'] = $("#vpc_name").val(); // 展示用

    	parames['billCycleName'] 	= $("#txtpricename").val(); // 计费周期 展示用
       	parames['priceId'] 			= $("#txtpriceId").val(); // id 筛选用
       	parames['price'] 			= $("#txtprice").val(); // 价格  展示用
       	parames['unit'] 			= $("#txtunit").val(); // 价格单位  展示用

    	parames['version'] 			= <?= $version['id'] ?>;
       	parames['version_name'] 	= $("#txtVersionName").val();
       	
       	parames['vpcip1'] 			= $("#vpcip1").val();
       	parames['vpcip2'] 			= $("#vpcip2").val();
       	parames['vpcip5'] 			= $("#vpcip5").val();

       	<?php if(isset($config['order_good_id'])): ?>
        parames['order_good_id']           = '<?=$config['order_good_id']?>';
        <?php endif;?>
    	var url;
       	url = '<?= $url?>';
        $.StandardPost(url,parames);
    }else{
        $('#nowBuy').prop('disabled',false);
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
    });

</script>
<?php
$this->end();
?>