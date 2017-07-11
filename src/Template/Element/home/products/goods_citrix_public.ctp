                <h1> <?= $goodsInfo['goods'][0]['name'] ?> </h1>
				<h5>

				</h5>
				<div class="line"></div>
				<div class="clearfix location-relative">
					<label for="" class="pull-left">版本:</label>
					<div class="bk-form-row-cell">
						<ul class="clearfix city">
							<li ng-repeat="attirbute in attributeList" ng-class="{active:$first}" ng-click="changeAttribute(attirbute)">{{ attirbute.name }}
							</li>
						</ul>
						<input id="txtversion" type="hidden" value="{{ currentId }}"/>
					</div>
				</div>
				<div class="clearfix location-relative">
					<label for="" class="pull-left">计费方式:</label>
					<div class="bk-form-row-cell">
						<ul class="clearfix city" ng-repeat="attirbute in attributeList | filter: {name : currentVersion.name }">
							<li ng-repeat="style in attirbute.pricelist" ng-class="{active: currentStyle == style }" ng-click="changeStyle(style)">{{ style.name }}
							</li>
						</ul>
					</div>
				</div>
				<div class="clearfix location-relative">
					<label for="" class="pull-left">计费周期:</label>
					<div class="bk-form-row-cell"  >
					 	<div ng-repeat="attirbute in attributeList | filter: {name : currentVersion.name }">
							<ul class="clearfix city" ng-repeat="style in attirbute.pricelist | filter: {name : currentStyle.name }" >
								<li ng-repeat="interval in style.list "  ng-class="{active:currentPriceId == interval.id}" ng-click="changeInterval(interval)">{{ interval.interval }}
								</li>
							</ul>
						</div>
						<input id="price_id" type="hidden" value="{{ currentInterval.id }}"/>
					</div>
				</div>
				<div class="clearfix location-relative">
					<label for="" class="pull-left">部署区位:</label>
					<div class="bk-form-row-cell">
						<p>{{ currentVersion.region.name }}</p>
					</div>
				</div>

				<div class="clearfix location-relative">
					<label for="" class="pull-left">CPU:</label>
					<div class="bk-form-row-cell">
						<p>{{ currentVersion.spec.instancetype.cpu }}核</p>
					</div>
				</div>
				<div class="clearfix location-relative">
					<label for="" class="pull-left">内存:</label>
					<div class="bk-form-row-cell">
						<p>{{ currentVersion.spec.instancetype.memory }}GB</p>
					</div>
				</div>
				<div class="clearfix location-relative">
					<label for="" class="pull-left">GPU:</label>
					<div class="bk-form-row-cell">
						<p>{{ currentVersion.spec.instancetype.gpu }}MB</p>
					</div>
				</div>
				<div class="clearfix location-relative">
					<label for="" class="pull-left">价格:</label>
					<div class="bk-form-row-cell">
						<p>{{ currentInterval.price }}{{ currentInterval.unit }}</p>
					</div>
					<input id="txtcharge" type="hidden" value="{{currentInterval.id}}"/>
				</div>
				<div class="clearfix location-relative">
					<label for="" class="pull-left">配置:</label>
					<div class="bk-form-row-cell">
<!-- 						<ul class="clearfix city" name="selbillCycle" > 
                            <li class="active" data-val="1231" onclick="gotoSelectVpc()" ng-init="canBuy=true" ng-disabled="canBuy">请指定</li>
                        </ul> -->
                        <button class="btn btn-primary" style="margin: 0px;" onclick="gotoSelectVpc()" ng-init="canBuy=true" ng-disabled="canBuy">请指定</button>
					</div>
				</div>	

				
				<p style="margin-top:10px;">数量 : <input id="txtnum" type="number" value="1" min="1"> 台</p>
				<?php if($goodsInfo['goods'][0]['sn']=="Fimas_Serail"){ ?>
					<button class="btn btn-primary" id="te  st">申请使用</button>
				<?php	}else{ ?>
					<button class="btn btn-primary" id="btn-cart" ng-init="canBuy=true" ng-disabled="canBuy">加入购物车</button>
				<?php	} ?>

				<script type="text/javascript">
					app.controller('myGoods', function($scope,$http) {
						$http.get("/home/getGoodsJson/"+<?= $goodsInfo['goods'][0]['id'] ?>).success(
							function(data){
								$scope.attributeList 	= data;

								$scope.currentId		= <?php if(isset($config['version'])) {echo "'".$config['version']."'";} else { ?> data[0].id <?php }?>;
								$scope.currentPriceId		= <?php if(isset($config['price_id'])) {echo "'".$config['price_id']."'";} else { ?> data[0].pricelist[0].list[0].id <?php }?>;
								$scope.currentVersion 	= data[0];
								$scope.pricelist 		= data[0].pricelist;
								$scope.currentStyle 	= data[0].pricelist[0];
								$scope.currentInterval 	= data[0].pricelist[0].list[0];
								$scope.canBuy = false; //购买图标
								for (var v in data) {
									if (data[v].id == $scope.currentId) {
										$scope.currentVersion = data[v];
										$scope.pricelist 		= data[v].pricelist;
										$scope.currentStyle 	= data[v].pricelist[0];
										$scope.currentInterval 	= data[v].pricelist[0].list[l];
										for (var p in data[v].pricelist) {
											for (var l in data[v].pricelist[p].list) {
												if(data[v].pricelist[p].list[l].id == $scope.currentPriceId) {
													$scope.currentStyle 	= data[v].pricelist[p];
													$scope.currentInterval 	= data[v].pricelist[p].list[l];
												}
											}
										}
									}
								}

								$scope.changeAttribute 	= function(obj){
									$scope.currentVersion 	= obj;
									$scope.pricelist 		= obj.pricelist;
									$scope.currentId		= obj.id;
									$scope.currentStyle 	= obj.pricelist[0];
									$scope.currentInterval 	= obj.pricelist[0].list[0];
								}
								
								$scope.changeStyle = function(obj){
									$scope.currentStyle = obj;
									$scope.currentInterval = obj.list[0];
								}
								$scope.changeInterval = function(obj){
									$scope.currentInterval = obj;
								}
							}
						);
					});

					function gotoSelectVpc() {
						var i = new Array();
						<?php if (!empty($config)) { ?>
						<?php foreach ($config as $key => $value) {?>
							i['<?= $key ?>'] = '<?= $value ?>';
						<?php } 
						}?>

						i['version'] = $("#txtversion").val();
						i['price_id'] = $('#price_id').val();
						// console.log(i)
						var url = '/home/selectCitrixVpc/'+<?= $goodsInfo['goods'][0]['id'] ?>+'/'+$('#txtversion').val()+'/'+$('#txtcharge').val();
						$.StandardPost(url,i);
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
				</script>