<div class="index-breadcrumb">
    <ol class="breadcrumb">
        <li><a href="<?= $this->Url->build(['controller'=>'home','action'=>'category'])?>">商品列表</a></li>
        <li><a href="<?= $this->Url->build(['controller'=>'home','action'=>'goods'])?><?php if(isset($this_good_cate)){echo '/',$this_good_cate['id'];}?>"><?php if(isset($this_good_cate)){echo $this_good_cate['name'];}?></a></li>
        <li><a href="<?= $this->Url->build(['controller'=>'home','action'=>'products'])?><?php if(isset($this_good_info)){echo '/',$this_good_info['id'];}?>"><?php if(isset($this_good_info)){echo $this_good_info['name'];}?></a></li>
        <li class="active">商品配置</li>
    </ol>
</div>
<!-- vfw 新增 -->
<div class="wrap-nav-right " ng-app>
	<div class="container-wrap  wrap-buy">
		<div class="clearfix buy-theme" ng-controller="FirewallListService">
			<div class="pull-left theme-left">
				<div class="">
					<table>
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
                            <td class="row-message-left">基本设置</td>
                            <td class="row-message-right">
                                <div class="ng-binding">
                                    <div class="clearfix">
                                        <label for="" class="pull-left ">防火墙名：</label>
                                        <input type="text" name="firewall_name" ng-model="firewall" ng-init="firewall=''">
                                            <span class="text-danger wall-name"></span>
                                    </div>
                                </div>
                            </td>
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
							<td></td>
						</tr>
						<tr >
							<td class="row-message-left">网络</td>
							<td class="row-message-right">
								<div class="ng-binding">
									<div class="clearfix">
										<label for="" class="pull-left ">选择VPC:</label>
										<div class="bk-form-row-cell">
											<div ng-repeat="host in hostList | filter: { company.name : currentCompany.name} :true">
												<div ng-repeat="area in host.area | filter: {areaCode : currentArea.areaCode, name : currentArea.name}">
													<select ng-options="vpc.name for vpc in area.vpc" ng-model="vpc" ng-init="vpc = currentVpc" ng-change="changeVpc(vpc)"></select>
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
				<div class="theme-right-mian ">
					<p class="theme-buy">当前配置 </p>
					<ul class="goods-detail">
					    <li>
                            <span class="deleft">部署区位：</span>
                            <span class="deright">{{currentCompany.name}}-{{currentArea.name}}</span>
                            <input id="txtcs" type="hidden" value="{{currentCompany.name}}" code="{{currentCompanyCode}}" />
                            <input id="txtdy" type="hidden" value="{{currentArea.name}}" code="{{currentAreaCode}}" />
                        </li>
                        <li>
                            <span class="deleft">所在VPC：</span>
                            <span class="deright">{{currentVpc.name}} VPC</span>
                            <input id="txtvpc" type="hidden" value="{{currentVpc.name}}" code="{{currentVpc.vpCode}}"/>
                        </li>
						<li>
                            <span class="deleft">名称：</span>
                            <span class="deright">{{firewall}}</span>
                            <input type="hidden" id="firewall_name" value="{{firewall}}">
                        </li>
                        <li>
                            <span class="deleft">版本：</span>
                            <span class="deright"><?= $version['name'] ?></span>
                            <input type="hidden" id="txtEipName" value="<?= $version['name'] ?>">
                        </li>
						<li>
                            <span class="deleft">计费方式：</span>
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
					<button id="btnBuy"  class="btn btn-oriange">确认创建</button>
				</div>
			</div>
		</div>
	</div>



	<?php  $this->start('script_last'); ?>
	<?=$this->Html->script(['controller/controller.js','jquery-ui-1.10.0.custom.min.js','all-page.js', 'jquery.cookie.js']); ?>
	
	<script>
	function FirewallListService($scope, $http) {
		$http.get("/console/ajax/network/hosts/createFirewallArray.json?dept_id=<?php if(isset($config['dept_id'])){echo $config['dept_id'];}?>").success(
			function(data) {
				$scope.hostList = data;
				$scope.currentCompanyCode =  <?php if (isset($config['csCode']) && !empty($config['csCode'])){ echo "'".$config['csCode']."'";} else { ?>data[0].company.companyCode<?php }?>;
				$scope.priceList =  data[0].company.price;
				$scope.currentPrice = data[0].company.price[0];
				$scope.currentAreaCode = <?php if (isset($config['regionCode']) && !empty($config['regionCode'])){ echo "'".$config['regionCode']."'";} else { ?>data[0].area[0].areaCode<?php }?>;
				$scope.currentVpcCode = <?php if (isset($config['vpcCode']) && !empty($config['vpcCode'])){ echo "'".$config['vpcCode']."'";} else { ?>data[0].area[0].areaCode<?php }?>;
				$scope.currentPriceId = <?php if (isset($config['priceId']) && !empty($config['priceId'])){ echo "'".$config['priceId']."'";} else {echo 1;}?>;;
				$scope.currentVpc = data[0].area[0].vpc[0];
				$scope.firewall = "<?php if(isset($config['firewallName']) && !empty($config['firewallName'])){echo $config['firewallName'];}else{echo "";}?>";
				
				for (var h in data) {
	                if (data[h].company.companyCode == $scope.currentCompanyCode) {
	                    $scope.currentCompany = data[h].company;
	                    $scope.currentCompanyCode = data[h].company.companyCode;
	                    $scope.priceList =  data[h].company.price;
	                    for (var p in data[h].company.price) {
							if (data[h].company.price[p].id == $scope.currentPriceId) {
								$scope.currentPrice = data[h].company.price[h];
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
		}
	}

	
		$('#btnBuy').on('click',function(){
			$.cookie('the_cookie', 'the_value'); 
			$(this).prop('disabled',true);
			$.get("/console/ajax/Security/firewall/getFirewallByVpc?vpc="+$("#txtvpc").attr("code"), function(data){
				console.log(data);
				if(data=="1"){
					$("#btnBuy").prop('disabled',false);
					alert("当前VPC已经存在防火墙，不能多次创建");
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
		});
//添加清单
function addCar(type){
	var goods_id = $("#txtgoods_id").val();
	var parames = new Array();
			parames['is_console'] 		= 1;
           	parames['goods_id'] 		= goods_id;
           	parames['regionCode'] 		= $("#txtdy").attr('code');
           	parames['csCode']       	= $("#txtcs").attr('code');
           	parames['csName'] 			= $('#txtcs').val();
           	parames['dyName'] 			= $('#txtdy').val();
           	parames['firewallName'] 	= $("#firewall_name").val();
           	parames['vpcCode'] 			= $("#txtvpc").attr('code');
           	parames['vpcName'] 			= $("#txtvpc").val();
           	parames['priceId'] 			= $("#txtpriceId").val();
           	parames['price'] 			= $("#txtprice").val();
           	parames['unit'] 			= $("#txtunit").val();
           	parames['version'] 			= <?= $version['id'] ?>;
           	parames['billCycleName'] 	= $("#txtpricename").val();
           	parames['version_name'] 		= $("#txtVersionName").val();

           	<?php if(isset($config['order_good_id'])): ?>
            parames['order_good_id']           = '<?=$config['order_good_id']?>';
            <?php endif;?>
			var url;
           	url = '<?= $url?>';
            $.StandardPost(url,parames);
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

</script>

<?php $this->end(); ?>