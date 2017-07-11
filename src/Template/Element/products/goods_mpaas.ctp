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
					<label for="" class="pull-left">价格:</label>
					<div class="bk-form-row-cell">
						<p>{{ price }}{{ unit }}</p>
					</div>
					<input id="txtcharge" type="hidden" value="{{ price }}{{ unit }}"/>
				</div>

				<input id="price_id" type="hidden" value="{{ chargeId }}"/>
				<input id="txtnum" type="hidden" value="1" min="1">
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
								$scope.price = data[0].pricelist[0].list[0].price
								$scope.unit = data[0].pricelist[0].list[0].unit
								$scope.currentId=data[0].id;
								$scope.chargeId = data[0].pricelist[0].list[0].id;

								$scope.canBuy = false; //购买图标
								
								$scope.changeAttribute = function(obj){
									$scope.currentId=obj.id;
									$scope.price = obj.pricelist[0].list[0].price;
									$scope.unit = obj.pricelist[0].list[0].unit;
									$scope.chargeId = obj.pricelist[0].list[0].id;
								}
							}
						);
					});
				</script>