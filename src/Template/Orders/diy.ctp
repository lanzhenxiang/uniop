<?= $this->Html->css(['common.css']); ?>
<?= $this->Html->script(['controller/controller.js']); ?>
    <div class="container-wrap top-title">
		<h2>云服务器ECS</h2>
	</div>
	
	<div class="container-wrap">
		<div class="y-tab">
			<ul>
				<li class="y-first y-current">
					<a href="##" class="y-item">包年包月</a>
				</li>
				<li class="">
					<a href="##" class="y-item">按量付费</a>
				</li>
			</ul>
		</div>

		<div class="clearfix buy-theme" ng-controller="hostListService">
			<div class="pull-left theme-left">
				<div class="">
				<table>
				<input type="hidden" value="<?= $goods_id ?>" id="txtgoods_id"></input>
					<tr>
						<td class="row-message-left">部署区位</td>
						<td class="row-message-right">
							<div class="ng-binding">
								<div class="clearfix location-relative">
									<label for="" class="pull-left">厂商:</label>
								    <div class="bk-form-row-cell">
								    	<ul class="clearfix city">
											<li ng-repeat="host in hosts" ng-class="{active:$first}" ng-click="changeHost(host)" data-company="{{host.company.companyCode}}">
												{{host.company.name}}	
												<input type="hidden" value="{{host.company.companyCode}}" >
											</li>
										</ul>
								    </div>
								</div>
								<div class="clearfix location-relative">
									<label for="" class="pull-left">地域:</label>
								    <div class="bk-form-row-cell">
										<ul class="clearfix city" ng-repeat="host in hosts | filter:current">
											<li ng-repeat="area in host.area" ng-class="{active:$first}" ng-click="changeArea(area)" data-area="{{host.area.areaCode}}">
												{{area.name}}
												<input type="hidden" value="{{area.areaCode}}" >
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
									<label for="" class="pull-left">CPU:</label>
								   <div class="bk-form-row-cell">
									<ul class="clearfix city" ng-repeat="host in hosts | filter:current">
										<li ng-repeat="set in host.set" ng-class="{active:$first}" ng-click="changeCpu(set)">
											{{set.cpu}}核 
										</li>		
									</ul>
								   </div>
								</div>
								<div class="ng-tie">
								<div class="clearfix" ng-repeat="host in hosts | filter:current">
								    <label for="" class="pull-left">内存:</label>
								   <div class="bk-form-row-cell" ng-repeat="set in host.set | filter: {cpu:currentSet}">
									<ul class="clearfix city" ng-repeat="rom in set">
										<li  ng-class="{active:$first}"  ng-repeat="type in rom" data-set="{{type.setCode}}" ng-click="changeRom(type)">
											{{type.num}} GB
											<input type="hidden" id="txtypeCode" value="{{type.setCode}}">
										</li>
									</ul>
									</div>
							    </div>
								</div>
							</div>
						</td>
					</tr>
					<tr >
						<td class="row-message-left">网络</td>
						<td class="row-message-right">
							<div class="ng-binding">
								<div class="clearfix">
									<label for="" class="pull-left ">选择网络:</label>
								    <div class="bk-form-row-cell">
								    	<ul class="list-card list-card-primary" ng-repeat="host in hosts | filter:current">
								    		<li ng-repeat="net in host.net" ng-class="{active:$first}" ng-click="changeNet(net)">
											<i class="icon-ok"></i> {{net.name}}
											</li>
								    	</ul>
								    	<button class="btn btn-warning" disabled="disabled">添加网络</button>
									</div>
								</div>
					   		</div>
						</td>
					</tr>
					<tr >
						<td class="row-message-left">镜像</td>
						<td class="row-message-right">
							<div class="ng-binding">
								<div class="clearfix">
									<label for="" class="pull-left ">镜像类型:</label>
								    <div class="bk-form-row-cell">
										<ul class="clearfix city">
											<li class="active">系统镜像</li>
											<li>自定义镜像</li>
										</ul>
										<p><i class="icon-info-sign"></i>&nbsp;系统镜像即基础操作系统。自定义镜像则在基础操作系统上，集成了运行环境和各类软件。</p>
								   </div>
								</div>
								<div class="clearfix">
									<label for="" class="pull-left ">操作系统:</label>
								    <div class="bk-form-row-cell">
								    	<ul class="clearfix city" style="margin-bottom:4px" ng-repeat="host in hosts | filter:current">
											<li ng-repeat="os in host.Os" ng-class="{active:$first}" ng-click="changeOs(os)">
												{{os.name}}
											</li>
										</ul>
										<div ng-repeat="host in hosts | filter:current">
											<ul class="list-card list-card-primary" ng-repeat="os in host.Os | filter:currentOs">
												<li ng-repeat="type in os.types" ng-class="{active:$first}" ng-click="changeOsTypes(type)">
													<i class="icon-ok"></i> {{type.name}}
												</li>
									    	</ul>
								    	</div>
									</div>
								</div>
							</div>	
						</td>
					</tr>

					<tr >
						<td class="row-message-left">密码</td>
						<td class="row-message-right">
							<div class="ng-binding">
								<div class="clearfix">
									<label for="" class="pull-left ">设置密码:</label>
								   <div class="bk-form-row-cell">
									<ul class="clearfix city">
										<li class="active">立即设置</li>
										<li>创建后设置</li>
									</ul>
								   </div>
								</div>
								<div class="clearfix">
									<label for="" class="pull-left ">登录密码:</label>
								   <div class="bk-form-row-cell">
										<input type="text">
								   </div>
								</div>
								<div class="clearfix">
									<label for="" class="pull-left ">确认密码:</label>
								   <div class="bk-form-row-cell">
										<input id="txtpwd"  type="text"/>
								   </div>
								</div>
								<div class="clearfix">
									<label for="" class="pull-left ">实例名称：</label>
								   <div class="bk-form-row-cell">
										<input id="txtname" type="text"/>
								   </div>
								</div>
					    </div>
						</td>
					</tr>
					<tr >
						<td class="row-message-left">购买量</td>
						<td class="row-message-right">
							<div class="ng-binding">
								<div class="clearfix">
									<label for="" class="pull-left ">购买时长:</label>
								   <div class="bk-form-row-cell">
										<input type="number" min="0" max="12" ng-model="month" /> 月
								   </div>
								</div>
								<div class="clearfix">
									<label for="" class="pull-left ">数量:</label>
								   
									  <div class="bk-form-row-cell">
									  	<input type="number" min="0" max="999" ng-model="num" /> 台
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
							<span class="deright">{{current}}</span>
							<input id="txtcs" type="hidden" value="{{current}}" code="{{currentCode}}" />
						</li>
						<li>
							<span class="deleft">地域：</span>
							<span class="deright">{{currentArea}}</span>
                            <input id="txtdy" type="hidden" value="{{currentArea}}" code="{{currentAreaCode}}" />
						</li>
						<li>
							<span class="deleft">配置：</span>
							<span class="deright">{{currentSet}}核 & {{currentRom}}GB</span>
							<input id="txtcpu" type="hidden" value="{{currentSet}}"  />
							<input id="txtrom" type="hidden" value="{{currentRom}}"  />
							<input id="txtypecode" type="hidden" value="{{currentSetCode}}"  />
						</li>

						<li>
							<span class="deleft">网络：</span>
							<span class="deright">{{currentNet.name}}</span>
                            <input id="txtnet" type="hidden" value="{{currentNet.name}}" code="{{currentNet.netCode}}" />
						</li>
						<li>
							<span class="deleft">镜像：</span>
							<span class="deright">{{currentOsTypes.name}}</span>
							<input id="txtimage" type="hidden" value="{{currentOsTypes.name}}" code="{{currentOsTypes.typeCode}}" />
						</li>
						<li>
							<span class="deleft">购买量：</span>
							<span class="deright"> {{month}} 个月 X  {{num}} 台</span>
                            <input id="txtmonth" type="hidden" value="{{month}}"  />
                            <input id="txtnumber" type="hidden" value="{{num}}"  />
						</li>
					</ul>
					<a id="btnBuy"  class="btn btn-oriange" href="javascript:void(0)">点击确认</a>
				</div>
			</div>
		</div>
	</div>



<?php  $this->start('script_last'); ?>
<script>
		
		$('.bk-form-row-cell').on('click','li',function(){
			if($(this).siblings().hasClass('active')){
				$(this).siblings().removeClass('active');
			}
			$(this).addClass('active');
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
			addCar(false);
		});


		//添加清单
		function addCar(type){
			var goods_id=$("#txtgoods_id").val(); //id
			//获取商品配置信息
			if(type==true){
				type=1;
			}else{
				type=0;
			}
            $.ajax({
                type:"post",
                url:"/orders/addShoppingCar",
                async:true,
                data:{
                	goods_id:goods_id,
                	attr:{
                    		"ecsName":$("#txtname").val(),
                    		"imageCode":$("#txtimage").attr("code"),
                    		"bandwidth":0,
                    		"vpcCode":'',
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
                    		"imageCode":$("#txtimage").attr("code"),
                    		"number":$("#txtnumber").val(),
                    		"month":$("#txtmonth").val()
                    	},
                	type:type
                },
                success: function (data) {
                	data= $.parseJSON(data);
                	if(type==true){
                		$("#number").html(data.number);
                    }else{
                    	window.location.href=data.url;
                    }
                }
            });
		}
	</script>

<?php $this->end(); ?>