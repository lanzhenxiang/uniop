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
					<label for="" class="pull-left">配置:</label>
					<div class="bk-form-row-cell">
						<ul class="clearfix city" id="selbillCycle" name="selbillCycle" >
                            <li class="active" data-val="1231" onclick="gotoSelectVfw()" >自定义配置</li>
                        </ul>
					</div>
				</div>
				<p style="margin-top:10px;margin-left: 38px; ">数量 : 
				<input type="number" id="txtnum" min="1" max="999" ng-model="num" name="txtnum" ng-init="num=<?php echo isset($config['number']) ? $config['number'] : 1; ?>" /> 台
				<input id="txtcharge" type="hidden" value="<?php echo isset($config['totalPay']) ? $config['totalPay'] : 0; ?>"/>
				<input id="price_id" type="hidden" value="0"/>
				</p>

				<!-- <input id="txtnum" type="number" value="" min="1"> 台</p> -->
				<?php if($goodsInfo['goods'][0]['sn']=="Fimas_Serail"){ ?>
					<button class="btn btn-primary" id="te  st">申请使用</button>
				<?php	}else{ ?>
					<button class="btn btn-primary" id="btn-cart" style="margin-left: 38px; " ng-init="canBuy=true" ng-disabled="canBuy">加入购物车</button>
				<?php	} ?>

				<script type="text/javascript">

					// $('#txtnum').on('click',function(){});

					app.controller('myGoods', function($scope,$http) {
						$http.get("/home/getGoodsJson/"+<?= $goodsInfo['goods'][0]['id'] ?>).success(
							function(data){
								$scope.attributeList = data;
								$scope.currentId=data[0].id;
								$scope.canBuy = false; //购买图标
								$scope.changeAttribute = function(obj){
									$scope.currentId=obj.id;
								}
							}
						);
					});
					function gotoSelectVfw() {
						var i = new Array();
						<?php if (!empty($config)) { ?>
						<?php foreach ($config as $key => $value) {?>
							i['<?= $key ?>'] = '<?= $value ?>';
						<?php } 
						}?>

						var url = '/home/selectVfw/'+<?= $goodsInfo['goods'][0]['id'] ?>+'/'+$('#txtversion').val();
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