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
						<ul class="clearfix city" ng-repeat="attirbute in attributeList | filter: attirbuteFilter">
							<li ng-repeat="style in attirbute.pricelist" ng-class="{active:$first}" ng-click="changeStyle(style)">{{ style.name }}
							</li>
						</ul>
					</div>
				</div>
				<div class="clearfix location-relative">
					<label for="" class="pull-left">计费周期:</label>
					<div class="bk-form-row-cell"  >
					 	<div ng-repeat="attirbute in attributeList | filter: attirbuteFilter">
							<ul class="clearfix city" ng-repeat="style in attirbute.pricelist | filter: {name : currentStyle.name }" >
								<li ng-repeat="interval in style.list "  ng-class="{active:$first}" ng-click="changeInterval(interval)">{{ interval.interval }}
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
					<label for="" class="pull-left">VPC:</label>
					<div class="bk-form-row-cell">
						<p>{{ currentVersion.vpc.name }}</p>
					</div>
				</div>
				<div class="clearfix location-relative">
					<label for="" class="pull-left">子网:</label>
					<div class="bk-form-row-cell">
						<p>{{ currentVersion.subnet.name }}</p>
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
					<input id="txtcharge" type="hidden" value=""/>
				</div>
				
				<p style="margin-top:10px;">数量 : <input id="txtnum" type="number" value="1" min="1"> 台</p>
				<?php if($goodsInfo['goods'][0]['sn']=="Fimas_Serail"){ ?>
					<button class="btn btn-primary" id="te  st">申请使用</button>
				<?php	}else{ ?>
					<button class="btn btn-primary" id="btn-cart" ng-init="canBuy=true" ng-disabled="canBuy">加入购物车</button>
				<?php	} ?>

				<script type="text/javascript">
					// var app = angular.module('myApp', []);
					app.controller('myGoods', function($scope,$http) {
						$http.get("/home/getGoodsJson/"+<?= $goodsInfo['goods'][0]['id'] ?>).success(
							function(data){
								$scope.attributeList = data;
								$scope.currentVersion 	= data[0];
								$scope.pricelist 		= data[0].pricelist;
								$scope.currentId		= data[0].id;
								$scope.currentStyle 	= data[0].pricelist[0];
								$scope.currentInterval 	= data[0].pricelist[0].list[0]; 
								$scope.canBuy = false; //购买图标
								$scope.changeAttribute = function(obj){
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
								
								$scope.attirbuteFilter = function(obj){
									return obj.name === $scope.currentVersion.name;
								}
							}
						);
					});
				</script>