<!-- 云桌面 新增 -->

<?= $this->Html->script(['controller/controller.js']); ?>

<div class="wrap-nav-right " ng-app>
	<div class="container-wrap  wrap-buy">
		<a href="<?= $this->Url->build(['controller'=>'desktop','action'=>'lists']); ?>" class="btn btn-addition">返回云桌面列表</a>
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
		<div class="clearfix buy-theme" ng-controller="desktopListService">
			<div class="pull-left theme-left">
				<div class="">
					<table>
						<input type="hidden" value='<?= $goods_id ?>' id="txtgoods_id"></input>
						<tr>
						  <td class="row-message-left">计费</td>
						  <td class="row-message-right" colspan="3">
						      <div class="ng-binding">
									<div class="clearfix location-relative">
										<label for="" class="pull-left">计费方式:</label>
										<div class="bk-form-row-cell">
											<ul class="clearfix city" ng-repeat="host in hostList | filter: { company.name:currentCompany.name} :true">
												<li ng-repeat="style in currentSoftwareInfo.pricelist" ng-class="{active:$first}" ng-click="changeStyle(style)">{{ style.name }}
							                    </li>
											</ul>
										</div>
									</div>
									<div class="clearfix location-relative">
										<label for="" class="pull-left">计费周期:</label>
										<div class="bk-form-row-cell">
							                 <ul class="clearfix city" ng-repeat="host in hostList | filter: { company.name:currentCompany.name} :true">
								                    <li ng-repeat="interval in currentStyle.list "  ng-class="{active:$first}" ng-click="changeInterval(interval)">{{ interval.interval }}
								                    </li>
							                 </ul>
						                    <input id="price_id" type="hidden" value="{{ currentInterval.id }}"/>
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
							<td></td>
						</tr>
						<tr>
							<td class="row-message-left">基本设置</td>
							<td class="row-message-right" colspan="3">
								<div class="ng-binding">
									<div class="clearfix">
										<label for="" class="pull-left ">实例名称：</label>
										<div class="bk-form-row-cell">
											<input ng-model="firstName" type="text" ng-init="firstName=''" id="name" maxlength="15" />
											<span class="text-danger txtname"></span>
											<p><i class="icon-info-sign"></i>&nbsp;实例名称由主名、短横线、数字序号构成，其中，主名只能由8个英文字母构成，序号范围从1到999;例：abcdefgh-12</p>
										</div>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<td class="row-message-left">非编型号</td>
							<td class="row-message-right" colspan="3">
								<div class="ng-binding">
									<div class="clearfix">
										<label for="" class="pull-left">品牌:</label>
										<div class="bk-form-row-cell">
											<ul class="clearfix city" ng-repeat="host in hostList | filter: {company.companyCode : currentCompany.companyCode}">
												<li ng-repeat="software in currentArea.setsoftwareInfo" ng-class="{active:$first}" ng-click="changeSoftware(software)">
													{{software.name}}
												</li>
											</ul>
										</div>
									</div>
									<div class="clearfix">
										<label for="" class="pull-left">规格:</label>
										<div class="bk-form-row-cell" ng-repeat="host in hostList | filter: {company.companyCode : currentCompany.companyCode}">
											<div ng-repeat="software in currentArea.setsoftwareInfo | filter: {name : currentSoftware.name}">
												<select ng-options="info.name for info in software.softwareInfo" ng-click="changeSoftwareInfo(info)" ng-model="info" ng-init="info = software.softwareInfo[0]">
												</select>
												<a href="#" data-toggle="modal" data-target="#tips" /><i class="icon-exclamation-sign" style="margin-left:5px;"></i></a>
												<!-- Modal -->
												<div class="modal fade" id="tips" role="dialog">
													<div class="modal-dialog" role="document">
														<div class="modal-content">
															<div class="modal-header">
																<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
																<h4 class="modal-title" id="myModalLabel">提示</h4>
															</div>
															<div class="modal-body">
																<div class="modal-form-group">
																	<label>产品:</label>
																	<div>
																		{{info.name}}
																	</div>
																</div>
																<div class="modal-form-group">
																	<label>操作系统:</label>
																	<div>
																		{{info.imageName}}
																	</div>
																</div>
																<div class="modal-form-group">
																	<label>CPU:</label>
																	<div>
																		{{info.hardwareCpu}}核
																	</div>
																</div>
																<div class="modal-form-group">
																	<label>内存:</label>
																	<div>
																		{{info.hardwareMemory}}GB
																	</div>
																</div>
																<div class="modal-form-group">
																	<label>GPU:</label>
																	<div>
																		{{info.hardwareGpu}}MB
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
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
									<div class="clearfix">
										<label for="" class="pull-left ">IP分配方式:</label>
										<div class="bk-form-row-cell bk-form-row-small">
											<ul class="clearfix city" style="margin-bottom:10px;" id="netIp">
												<li class="active">自动分配</li>
												<li>自定义分配</li>
											</ul>
											<div id="netIpInfo" style="display: none;">
												<input type="text" ng-model="ip0" disabled="disabled" />
												&nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;
												<input type="text" ng-model="ip1" disabled="disabled" />
												&nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;
												<input type="text" ng-model="ip2" disabled="disabled" />
												&nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;
												<input type="text" ng-model="ip3" disabled="true" id="ip3" />
											</div>
										</div>
									</div>
								</div>
							</td>
						<td class="row-message-left">添加网络</td>
                        <td class="row-message-right">
                            <div class="ng-binding">
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
											<!--
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
                                            </div>-->
                                            <div class="clearfix" style="display: none;">
												<label for="" class="pull-left ">IP分配方式:</label>
												<div class="bk-form-row-cell bk-form-row-small">
													<ul class="clearfix city" style="margin-bottom:10px;" id="netIp">
														<li class="active">自动分配</li>
														<li>自定义分配</li>
													</ul>
													<div id="netIpInfo" style="display: none;">
														<input type="text" ng-model="ip0" disabled="disabled" />
														&nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;
														<input type="text" ng-model="ip1" disabled="disabled" />
														&nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;
														<input type="text" ng-model="ip2" disabled="disabled" />
														&nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;
														<input type="text" ng-model="ip3" disabled="true" id="ip3" />
													</div>
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
						<tr style="display:none">
							<td class="row-message-left">密码</td>
							<td class="row-message-right">
								<div class="ng-binding">
									<div class="clearfix">
										<label for="" class="pull-left ">账号类型:</label>
										<div class="bk-form-row-cell">
											<ul class="pwd-tab clearfix city">
												<li class="active">创建账号(AD)</li>
												<li>选择账号</li>
												<!-- <li>稍后创建</li> -->
											</ul>
											<input type="hidden" name="ad" id="ad" value="1"/>
										</div>
									</div>
									<div class="pwd-content" style="display:block">
										<div class="clearfix">
											<label for="" class="pull-left ">登录账号:</label>
											<div class="bk-form-row-cell">
												<input style="display:none">
												<input type="text" id="txtaduser">
												<span class="text-danger txtaduser"></span>
											</div>
										</div>
										<div class="clearfix">
											<label for="" class="pull-left ">登录密码:</label>
											<div class="bk-form-row-cell">
												<input style="display:none">
												<input id="txtpwd1" type="password" value="">
												<span class="text-danger txtpwd1"></span>
											</div>
										</div>
										<div class="clearfix">
											<label for="" class="pull-left ">确认密码:</label>
											<div class="bk-form-row-cell">
												<input id="txtpwd2"  type="password"/>
												<span class="text-danger txtpwd2"></span>
											</div>
										</div>
									</div>
									<div class="pwd-content">
										<div class="clearfix">
											<label for="" class="pull-left ">登录帐号:</label>
											<div class="bk-form-row-cell">
												<div ng-repeat="host in hostList | filter: {company.companyCode : currentCompany.companyCode}">
													<div ng-repeat="area in host.area | filter: {areaCode : currentArea.areaCode}">
														<div ng-repeat="vpc in area.vpc | filter: {vpcCode : currentVpc.vpcCode}">
															<select ng-options=" aduser.loginName for aduser in vpc.aduser" ng-model="aduser" ng-init="aduser = vpc.aduser[0]" ng-change="changeAduser(aduser)"></select>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<!-- <div class="pwd-content">
								</div> -->

							</div>
						</td>
					</tr>
					<tr >
						<td class="row-message-left">购买量</td>
						<td class="row-message-right" colspan="3">
							<div class="ng-binding">
									<!-- <div class="clearfix">
										<label for="" class="pull-left ">购买时长:</label>
										<div class="bk-form-row-cell">
											<input type="number" min="0" max="12" ng-model="month" ng-init="month=1" /> 月
										</div>
									</div> -->
									<div class="clearfix">
										<label for="" class="pull-left ">数量:</label>

										<div class="bk-form-row-cell">
											<input type="number" min="1" max="250" ng-model="num" ng-init="num=1" id="txtnum"/> 台
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
							<span class="deleft">实例名称：</span>
							<span class="deright">{{firstName}}</span>
							<input id="txtname" type="hidden" value="{{firstName}}"/>
						</li>
						<li>
							<span class="deleft">品牌：</span>
							<span class="deright">{{currentSoftware.name}}</span>
						</li>
						<li>
							<span class="deleft">规格：</span>
							<span class="deright">{{currentSoftwareInfo.name}}</span>
							<input id="txtgename" type="hidden" value="{{currentSoftwareInfo.name}}"/>
							<input id="txtimage" type="hidden" value="{{currentSoftwareInfo.imageCode}}"/>
							<input id="txtimagename" type="hidden" value="{{currentSoftwareInfo.imageName}}"/>
							<input id="txthardware" type="hidden" value="{{currentSoftwareInfo.hardwareCode}}"/>
							<input id="txtcpu" type="hidden" value="{{currentSoftwareInfo.hardwareCpu}}"/>
							<input id="txtrom" type="hidden" value="{{currentSoftwareInfo.hardwareMemory}}"/>
							<input id="txtgpu" type="hidden" value="{{currentSoftwareInfo.hardwareGpu}}"/>
						</li>
						<li>
							<span class="deleft">网络：</span>
							<span class="deright">{{currentNet.name}}</span>
							<input id="txtnet" type="hidden" value="{{currentNet.name}}" code="{{currentNet.netCode}}" netid="{{currentNet.netId}}" />
							<input id="txtvpc" type="hidden" value="{{currentVpc.name}}" code="{{currentVpc.vpcCode}}"/>
						</li>
						<li>
                            <span class="deleft">添加网络：</span>
                            <span id="txtnetn22" class="deright" >{{currentNet2.name}}</span>
                            <input id="txtnet22" type="hidden" value="{{currentNet2.name}}" code="{{currentNet2.netCode}}" />
                        </li>
						<li>
							<span class="deleft">IP分配：</span>
							<span class="deright">
								{{ip0}}&nbsp;
								<strong>·</strong>&nbsp;
								{{ip1}}&nbsp;
								<strong>·</strong>&nbsp;
								{{ip2}}&nbsp;
								<strong>·</strong>&nbsp;
								{{ip3}}
							</span>
						</li>
						<li>
							<span class="deleft">购买量：</span>
							<span class="deright">{{num}} 台</span>
							<input id="txtmonth" type="hidden" value="{{month}}"  />
							<input id="txtnumber" type="hidden" value="{{num}}"  />
							<input id="aduser" type="hidden" value="{{currentAduser.loginName}}" />
						</li>
						<li>
                            <span class="deleft">单台原价：</span>
                            <span class="deright">{{ currentInterval.price }}{{ currentInterval.unit }}</span>
                        </li>
                        <li>
                            <span class="deright"></span>
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
function setSubnet(){
    $("#txtnet2").html($("#net2").find("option:selected").text());
    $("#txtnet22").val($("#net2").val());
}
function initVpc2(){
    $('#vpc2').html("<option value=''>不扩展</option>");
    $('#net2').html("");
}
function loadSubnetPublic(vpc){
    var h="";
    var h2="";
	vpc_txt = $("#vpc2  option:selected").text();
    //console.log(vpc_txt);
    if(vpc==''||vpc==null){
        vpc='';
    }
    // var date = new Date();
    $.ajax({
        type:"post",
        url:"<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'hosts', 'loadSubnetPublic']); ?>"+"?d="+Date.parse(new Date()),
        data:{vpc:vpc},
        async:false,
        cache: false,
        success:function(data){
            data= $.parseJSON(data);
             h += "<option value=''  >不扩展</option>";
             $.each(data.vpc,function(i,n){
                if(i==0 && vpc !=''){
                    //<option value="4">$vpc$_822</option><option value="5">vpc[822]_fox</option><
                    h += "<option value='"+n.code+"' selected=\"selected\">"+n.name+"</option>";
                }else{
                    h += "<option value='"+n.code+"'>"+n.name+"</option>";
                }
             });

             $("#vpc2").html(h);
             vpc = $("#vpc2").val();
             if(vpc_txt!="不扩展"){
                $.each(data.vpc,function(i,n){
                if(n.code==vpc){
                        $.each(n.subnet,function(x,y){
                            if(i==x){
                                h2 += "<option value='"+y.code+"' selected=\"selected\">"+y.name+"</option>";
                            }else{
                                h2 += "<option value='"+y.code+"'>"+y.name+"</option>";
                            }
                        })
                    };
                 });

				$("#net2").html(h2);
             }else{
				h2 += "<option value=''></option>";
				$("#net2").html(h2);
			 }
            // alert(data)
        }
    });
    //$("#txtnet2").html($("#net2").find("option:selected").text());
    //$("#txtnet22").val($("#net2").val());
}
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
			$(this).prop('disabled',true);
			$.getJSON("/console/home/getUserLimit", function(data){
				var all=$("#txtnumber").val();
				if(all<1){
					alert('商品个数出错');
					$('#btnBuy').prop('disabled',false);
					return false;
				}
				if(($("#txtcpu").val()*all+data.cpu_used) > data.data.cpu_bugedt){
					alert('CPU配额不足');
					$('#btnBuy').prop('disabled',false);
					return false;
				}
				if(($("#txtrom").val()*all+data.memory_used) > data.data.memory_buget){
					alert('内存配额不足');
					$('#btnBuy').prop('disabled',false);
					return false;
				}
				if(($("#txtgpu").val()*all+data.gpu_used) > data.data.gpu_bugedt){
					alert('GPU配额不足');
					$('#btnBuy').prop('disabled',false);
					return false;
				}
				addCar(false);
			});

		});


//添加清单
function addCar(type){
	var value1 = true;

	if($('#name').val()==''||$('#name').val()==null){
		$(".txtname").html('请输入名称');
		$('#btnBuy').prop('disabled',false);
		return false;
	}

	$.ajax({
		type:"post",
		url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'Ajax','action'=>'desktop','desktop','checkDesktopName']); ?>",
		async:true,
		data:{
			name:$('#txtname').val(),
			num:$('#txtnumber').val(),
		},
		async:false,
	}).done(function(data){
		result = $.parseJSON(data);
		if(result==1){
			$(".txtname").html('已存在的桌面名称，请重新输入');
			$('#btnBuy').prop('disabled',false);
			value1 = false;
			return false;
		}
	});
	var goods_id=$("#txtgoods_id").val(); //id


	if(type){
		type=1;
	}else{
		type=0;
	}

	if(value1){
		$.ajax({
			type:"post",
			url:"/orders/addShoppingCar",
			async:true,
			data:{
				is_console:1,
				goods_id:8,
				attr:{
					"name":$("#txtname").val(),
					"bandwidth":0,
					"vpcCode":$("#txtvpc").attr("code"),
					"vpcName":$("#txtvpc").val(),
					"subnetCode":$("#txtnet").attr("code"),
					"subnetId":$("#txtnet").attr("netid"),
					"regionCode":$("#txtdy").attr("code"),
					"csName":$("#txtcs").val(),
					"csCode":$("#txtcs").attr("code"),
					"dyName":$("#txtdy").val(),
					"dyCode":$("#txtdy").attr("code"),
					"cpu":$("#txtcpu").val(),
					"rom":$("#txtrom").val(),
					"gpu":$("#txtgpu").val(),
					"netName":$("#txtnet").val(),
					"netCode":$("#txtnet").attr("code"),
					"imageCode":$("#txtimage").val(),
					"instanceTypeCode":$("#txthardware").val(),
					"number":$("#txtnumber").val(),
					"month":$("#txtmonth").val(),
					"ad":$("#ad").val(),
					"aduser":$('#txtaduser').val(),
					"txtaduser":$('#aduser').val(),
					"adpass":$('#txtpwd2').val(),
					"subnetCode2":$("#txtnet22").val(),
					"price_id":$("#price_id").val(),
					"spec_name":$("#txtgename").val(),
					"image_name":$("#txtimagename").val(),
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
	}else{
		$('#btnBuy').prop('disabled',false);
	}

}

$('.bk-form-row-cell').on('blur','input',function(){
	if($(this).val()!=''){
		$(this).next().html('');
	}
})

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
//        网络设置tab
$(".network-tab").on('click', 'li', function() {
    $(this).addClass("active");
    $(this).siblings().removeClass("active");
    var $tabIndex = $(this).index();
    var $table = $(".network-box>div").eq($tabIndex);
    $table.show();
    $table.siblings().hide();
});

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