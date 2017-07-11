<!-- 云桌面 新增 -->
<?= $this->Html->script(['controller/controller.js']); ?>

<div class="wrap-nav-right " ng-app>
	<div class="container-wrap  wrap-buy">
		<a href="<?= $this->Url->build(['controller'=>'Security','action'=>'lists','firewall']); ?>" class="btn btn-addition">返回防火墙列表</a>
		<!-- <div class="y-tab">
			<ul>
				<li class="y-first y-current">
					<a href="##" class="y-item">包年包月</a>
				</li>
				<li class="">
					<a href="##" class="y-item">按量付费</a>
				</li>
			</ul>
		</div> -->

		<div class="clearfix buy-theme" ng-controller="firewallListService">
			<div class="pull-left theme-left">
				<div class="">
					<table>
						<input type="hidden" value='<?= $goods_id ?>' id="txtgoods_id"></input>
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
													<select ng-options="vpc.name for vpc in area.vpc" ng-model="vpc" ng-init="vpc = area.vpc[0]" ng-change="changeVpc(vpc)"></select>
													<p><i class="icon-info-sign"></i>&nbsp;一个VPC只能添加一个防火墙</p>
												</div>
											</div>
										</div>
									</div>
									<!-- <div class="clearfix">
										<label for="" class="pull-left ">选择网络:</label>
										<div class="bk-form-row-cell">
											<div ng-repeat="host in hostList | filter: { company.name : currentCompany.name} :true">
												<div ng-repeat="area in host.area | filter: {areaCode : currentArea.areaCode, name : currentArea.name}">
								    				<div ng-repeat="vpc in area.vpc | filter: {vpcCode : currentVpc.vpcCode}">
														<select ng-options="net.name for net in vpc.net" ng-model="net" ng-init="net = vpc.net[0]" ng-change="changeNet(net)"></select>
														<p ng-repeat="net in vpc.net | filter: net.netCode : currentNet.netCode"><i class="icon-info-sign"></i>&nbsp;该子网使用{{net.isFusion}}技术</p>
													</div>
												</div>
											</div>
										</div>
									</div> -->
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
                            <span class="deleft">防火墙：</span>
                            <span class="deright" id="firewall_names">{{firewall}}</span>
                            <input type="hidden" id="firewall_name" value="{{firewall}}">
                        </li>
						<li>
							<span class="deleft">VPC：</span>
							<span class="deright">{{currentVpc.name}}</span>
							<input id="txtvpc" type="hidden" value="{{currentVpc.name}}" code="{{currentVpc.vpCode}}"/>
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
			$.get("/console/ajax/Security/firewall/getFirewallByVpc?vpc="+$("#txtvpc").attr("code"), function(data){
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
			});
		});
		$(".pwd-tab").on("click","li",function(){
			$(".pwd-content").css("display","none");
			$(".pwd-content").eq($(this).index()).css("display","block");
			switch($(this).index()){
				case 0:{
					$("#ad").val(1);
					break;
				}
				case 1:{
					$("#ad").val(2);
					break;
				}
				default:{
					$("#ad").val("");
				}
			}
		});

		$("#netIp").on("click","li",function(){
			var state = !$("#ip3").prop("disabled");
			if(state){
				$("#netIpInfo").css("display","none");
			}else{
				$("#netIpInfo").css("display","block");
			}
			$("#ip3").prop("disabled",state);
		});


		$('.ng-tie').on('click','li',function(){
			if($(this).siblings().hasClass('active')){
				$(this).siblings().removeClass('active');
			}
			$(this).addClass('active');
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
				"ecsName":"防火墙实例",
				"firewallName":$("#firewall_name").val(),
				"imageCode":$("#txtimageCode").val(),
				"instanceTypeCode":$("#txtinstanceTypeCode").val(),
				"regionCode":$("#txtdy").attr("code"),
				"vpcCode":$("#txtvpc").attr("code"),
				"token":"<?= $token?>",

				"billCycleName" : $("#txtpricename").val(), // 计费周期 展示用
                "priceId"       : $("#txtpriceId").val(), // id 筛选用
                "price"         : $("#txtprice").val(), // 价格  展示用
                'real_price'    : $("#txtprice").val(),
                "unit"          : $("#txtunit").val(), // 价格单位  展示用
				//"subnetCode":$("#txtnet").attr("code")
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