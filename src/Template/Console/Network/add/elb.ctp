<!-- 主机 新增 -->

<?= $this->element('network/lists/left',['active_action'=>'elb']); ?>
<?= $this->Html->script(['controller/controller.js']); ?>

<div class="wrap-nav-right " ng-app>
	<div class="container-wrap  wrap-buy">
	<a href="<?= $this->Url->build(['controller'=>'network','action'=>'lists','elb']); ?>" class="btn btn-addition">返回ELB列表</a>
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
						<td class="row-message-left">基本设置</td>
						<td class="row-message-right">
							<div class="ng-binding">
							    <div class="clearfix">
							    	<label for="" class="pull-left ">名称：</label>
							    	<div class="bk-form-row-cell">
							    		<input id="txtname" ng-model="elbname" type="text"  name="textname"/>
							    		<span class="text-danger txtname"></span>
							    	</div>
							    </div>
							</div>
							<!-- <div class="ng-binding">
							    <div class="clearfix">
							    	<label for="" class="pull-left ">协议：</label>
							    	<div class="bk-form-row-cell">
										<select name="protocol" style="width:200px;" ng-model="protocol" id="protocol">
											<option value="TCP">TCP</option>
											<option value="UDP">UDP</option>
										</select>
							    	</div>
							    </div>
							</div>
							<div class="ng-binding">
							    <div class="clearfix">
							    	<label for="" class="pull-left ">端口：</label>
							    	<div class="bk-form-row-cell">
							    		<input id="txtport" ng-model="txtport" type="text" name="txtport"/>
							    		<span class="text-danger txtname"></span>
							    	</div>
							    </div>
							</div> -->
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
										    	<select ng-options="vpc.name for vpc in area.vpc" ng-model="vpc" ng-init="vpc = area.vpc[0]" ng-change="changeVpc(vpc)" id="vpc"></select>
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
											    	<select ng-options="net.name for net in vpc.net" ng-model="net" ng-init="net = vpc.net[0]" ng-change="changeNet(net)" id="net"></select>
											    	<span class="text-danger" id="net-warning"></span>
											    </div>
										    </div>
								    	</div>
									</div>
								</div>
					   		</div>
						</td>
					</tr>
					<!-- <tr>
						<td class="row-message-left">EIP设置</td>
						<td class="row-message-right">
							<div class="ng-binding">
								<div class="clearfix">
									<label for="" class="pull-left ">公网IP:</label>
								    <div class="bk-form-row-cell">
								    	<div ng-repeat="host in hostList | filter: { company.name : currentCompany.name}">
								    		<div ng-repeat="area in host.area | filter: {areaCode : currentArea.areaCode, name : currentArea.name}">
										    	<select ng-options="eip.name for eip in area.eip" ng-model="eip" ng-change="changeEip(eip)" ng-init="eip = area.eip[0]">
										    		<option value=""></option>
										    	</select>
										    </div>
								    	</div>
								    	<p><i class="icon-info-sign"></i>&nbsp;不选公网EIP，表示该ELB只对内提供服务</p>
									</div>
								</div>
					   		</div>
						</td>
					</tr> -->


					<!-- <tr >
						<td class="row-message-left">购买量</td>
						<td class="row-message-right">
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
					</tr> -->
				</table>
				</div>
			</div>


			<div class="pull-right theme-right">
				<div class="theme-right-mian">
					<p class="theme-buy">当前配置 </p>
					<ul class="goods-detail">
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
                            <span class="deleft">名称：</span>
                            <span class="deright">{{elbname}}</span>
                            <input type="hidden" id="txtElbName" value="{{elbname}}">
                        </li>
						<li style="display: none;">
							<span class="deleft">网络：</span>
							<span class="deright">{{currentNet.name}}</span>
                            <input id="txtnet" type="hidden" value="{{currentNet.name}}" code="{{currentNet.netCode}}" />
                            <input id="txtvpc" type="hidden" value="{{currentVpc.name}}" code="{{currentVpc.vpCode}}" />
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



<?php  $this->start('script_last'); ?>
<?=$this->Html->script(['angular.min.js','controller/controller.js','jquery-ui-1.10.0.custom.min.js']); ?>
<script type="text/javascript">

$('#btnAddcar').on('click',function(){
	addCar(true);
});
$('#btnBuy').on('click',function(){
	// $.getJSON("/console/home/getUserLimit", function(data){
	// 		 	if(Number($("#txtcpu").val())+data.cpu_used > data.cpu_bugedt || (Number($("#txtrom").val())+data.memory_used) > data.memory_buget){
	// 		 		alert("配额不足 \r\n cpu 配额："+ data.cpu_bugedt+" 已使用："+data.cpu_used+" \r\n 内存 配额：" + data.memory_buget+" 已使用："+data.memory_used)
	// 		 	}else{

	// 		 	}
 //  			});
	$(this).attr('disabled',"disabled");
	$.getJSON("/console/home/getUserLimit", function(data) {
		if (data.elb_used >= data.data.elb_budget) {
			alert('配额不足 \r\n ELB 配额：' + data.data.elb_budget);
			$(this).prop('disabled',true);
		}else{
			addCar(false);
		}

	});

//
//	$(this).prop('disabled',true);
//	addCar(false);
});


//添加清单
function addCar(type){

	var txtname = $('#txtname').val();
	var txtnum  = $('#txtnum').val();
	var validate = true;
	var goods_id = $("#txtgoods_id").val(); //id
	var vpc = $('#vpc').val();
	var net = $('#net').val();
	//获取商品配置信息

	console.log(vpc);
	console.log(net);
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
		$.ajax({
			type:"post",
			url:"/orders/addShoppingCar",
			async:true,
			data:{
                is_console:1,
                goods_id:goods_id,
                attr:{
                    "ecsName":"负载均衡实例",
                    "lbsName":$("#txtname").val(),
                    "vpcCode":$("#txtvpc").attr("code"),
                    "subnetCode":$("#txtnet").attr("code"),
                    "regionCode":$("#txtdy").attr("code"),
                    "imageCode":$("#txtimageCode").val(),
                    "instanceTypeCode":$("#txtinstanceTypeCode").val(),
                    "token":"<?= $token?>",

                    "billCycleName" : $("#txtpricename").val(), // 计费周期 展示用
                    "priceId"       : $("#txtpriceId").val(), // id 筛选用
                    "price"         : $("#txtprice").val(), // 价格  展示用
                    'real_price'    : $("#txtprice").val(),
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