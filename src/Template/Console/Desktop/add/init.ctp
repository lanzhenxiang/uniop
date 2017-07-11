<!-- 云桌面 新增 -->
<?= $this->Html->script(['controller/controller.js']); ?>

<div class="wrap-nav-right " ng-app>
	<div class="container-wrap  wrap-buy">
		<a href="<?= $this->Url->build(['controller'=>'desktop','action'=>'lists','init']); ?>" class="btn btn-addition">返回管理服务列表</a>
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

		<div class="clearfix buy-theme" ng-controller="desktopInitListService">
			<div class="pull-left theme-left">
				<div class="">
					<table>
						<input type="hidden" value='<?= $goods_id ?>' id="txtgoods_id"></input>
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
												</div>
											</div>
										</div>
									</div>
									<div class="clearfix">
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
							<span class="deleft">VPC：</span>
							<span class="deright">{{currentVpc.name}}</span>
							<input id="txtvpc" type="hidden" value="{{currentVpc.name}}" code="{{currentVpc.vpcCode}}"/>
						</li>
						<li>
							<span class="deleft">网络：</span>
							<span class="deright">{{currentNet.name}}</span>
							<input id="txtnet" type="hidden" value="{{currentNet.name}}" code="{{currentNet.netCode}}" netid="{{currentNet.netId}}" />
						</li>
					</ul>
					<button id="btnBuy"  class="btn btn-oriange">确认创建</button>
				</div>
			</div>
		</div>
	</div>
	<div id="maindiv"></div>


	<?php  $this->start('script_last'); ?>
	<?=$this->Html->script(['angular.min.js','controller/controller.js','jquery-ui-1.10.0.custom.min.js','all-page.js']); ?>
	<script type="text/javascript">
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
		$('#btnBuy').on('click',function(){
			$.getJSON("/console/home/getUserLimit", function(data){
				if(data.basic_used + 1 > data.data.basic_budget ){
					alert("配额不足 \r\n 桌面基础套件 数量配额："+ data.data.basic_budget+" 已使用："+data.basic_used);
					$("#btnBuy").prop('disabled',false);
				}else{
					validateAddCar();
				}
			});


		});
//验证创建桌面套件，是否重复
function validateAddCar(){
	$.ajax({
		type:"post",
		url:"<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'desktop', 'init', 'hasDesktopExternal']); ?>",
		async:true,
		data:{
			vpcCode:$("#txtvpc").attr("code")
		},
		success: function (data) {
			data= $.parseJSON(data);
			if(data.allow == false){
				showModal('提示', 'icon-exclamation-sign', '同一VPC下已创建成功一套桌面套件,不能重复创建!', '', '', 0);
			}else{
				$(this).prop('disabled',true);
				addCar(false);
			}
		}
	});
}

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
				"vpcCode":$("#txtvpc").attr("code"),
				"subnetCode":$("#txtnet").attr("code"),
				"token":"<?= $token?>"
			},
			type:type
		},
		success: function (data) {
			data= $.parseJSON(data);
			if(data.allow == false){
				showModal('提示', 'icon-exclamation-sign', data.msg, '', '', 0);
				$('#btnBuy').removeAttr('disabled');
			}else{
				if(type==true){
				$("#number").html(data.number);
				}else{
					setTimeout(function() {
	                        window.location.href=data.url;
	                }, 10000)
				}
			}
		}
	});
}

//动态创建modal
function showModal(title, icon, content, content1, method, type, delete_info) {
      $("#maindiv").empty();
      html = "";
      html += '<div class="modal fade" id="modal" tabindex="-1" role="dialog">';
      html += '<div class="modal-dialog" role="document">';
      html += '<div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
      html += '<h5 class="modal-title">' + title + '</h5>';
      html += '</div><div class="modal-body"><i class="'+icon+' icon-color-gray text-primary"></i>&nbsp;&nbsp;' + content + '<span class="text-primary batch-warning">' + content1 + '</span>';
        if(delete_info == 1){
            html +='<br /><i class="icon-exclamation-sign icon-color-gray text-primary"></i>&nbsp;&nbsp;<span > 可在回收站找回已删除的主机</span><span class=" text-primary batch-warning" id="modal-dele-name"></span>';
        }

        html +='</div>';
      html += '<div class="modal-footer"><button id="btnModel_ok" onclick="' + method + '" type="button" class="btn btn-primary">确认</button><button type="button" class="btn btn-danger" id="btnEsc" data-dismiss="modal">取消</button></div></div></div></div>';
      $("#maindiv").append(html);
      if (type == 0) {
        $("#btnModel_ok").remove();
      }
      $('#modal').modal("show");
    }


$('.bk-form-row-cell').on('blur','input',function(){
	if($(this).val()!=''){
		$(this).next().html('');
	}
})



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