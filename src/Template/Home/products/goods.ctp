<?= $this->Html->css(['common.css']); ?>
<style type="text/css">
	p input {padding: 0 ;margin: 0 10px;}
	.service-promote-info label{
		width:64px;
		height: 28px;
		margin-right:10px;
		text-align: right;
		font-weight: normal;
		line-height:28px;
	}
	.bk-form-row-cell {
	    float: left;
	    width: 400px;
	    line-height: 28px;
	}
	.clearfix .location-relative{
		margin-top: 10px;
	}
	.service-promote p{
		margin-bottom:0px;
	}
	.btn-primary{
		margin-top:20px;
	}
</style>
<input id="goodsurl" type="hidden" value="<?=$this->url->build(['controller' => 'Orders', 'action' => 'buy'])?>" />
<input id="user" type="hidden" value="<?= $user ?>">
<div class="service-main clearfix" ng-controller="myGoods">
	<div class="service-alert alert alert-success fade in" role="alert">
		<div class="container">
			<div class="pull-left">
				<i class="icon-ok"></i>
				该商品已成功加入购物车！
			</div>
			<button type="button" class="close" style="margin-top:6px;margin-left:10px;" id="service-alert-close"><span aria-hidden="true">&times;</span></button>
			<div class="pull-right">
				<button class="btn btn-addition" id="btnShopping">继续购物</button>
				<button class="btn btn-danger" id="btnCheckout">结算</button>
			</div>
		</div>
	</div>
	<div class="service-content">
		<div class="service-promote clearfix">
			<div class="service-promote-image pull-left">
                <!-- <?php if($goodsInfo['goods'][0]['icon']){?>
                <?=$this->Html->image($goodsInfo['goods'][0]['icon'], ['alt' => '', 'width' => '258', 'height' => '318']);?>
                111
                <?php }else{ ?>
                <img src="/images/nophoto.jpg" alt="产品" style="width: 258px;height: 318px"/>
                <?php } ?> -->
				<?= $this->Html->image($goodsInfo['goods'][0]['icon'], ['alt' => '','width'=>'258','height'=>'318']); ?>
			</div>
			<div class="service-promote-info pull-left">
				<h1> <?= $goodsInfo['goods'][0]['name'] ?> </h1>
				<h5>

				</h5>
				<h5 style="margin-bottom:25px;"><?= empty($charge)==false ? $charge[0]["charge_template"]["charge_note"] : "" ?></h5>
				<div class="line"></div>
				<div class="clearfix location-relative">
					<label for="" class="pull-left">版本:</label>
					<div class="bk-form-row-cell">
						<ul class="clearfix city">
							<li ng-repeat="attirbute in attributeList" ng-class="{active:$first}" ng-click="changeAttribute(attirbute)">{{ attirbute.attribute_name }}
							</li>
						</ul>
						<input id="txtversion" type="hidden" value="{{ currentId }}"/>
					</div>
				</div>
				<div class="clearfix location-relative">
					<label for="" class="pull-left">计费方式:</label>
					<div class="bk-form-row-cell">
						<ul class="clearfix city">
							<li ng-repeat="charge in chargeList" ng-class="{active: currentChargeId == charge.id }"" ng-click="changeCharge(charge)">{{ charge.name }}
							</li>
						</ul>
						<input id="txtcharge" type="hidden" value="{{ currentChargeId }}"/>
					</div>
				</div>
				<div class="clearfix location-relative">
					<label for="" class="pull-left">基本设置:</label>
					<div class="bk-form-row-cell">
						<p>{{ config }}</p>
					</div>
				</div>
				<div class="clearfix location-relative">
					<label for="" class="pull-left">镜像:</label>
					<div class="bk-form-row-cell">
						<p>{{ image }}</p>
					</div>
				</div>
				<!--<div id="p_bandwidth" class="clearfix location-relative">-->
					<!--<label for="" class="pull-left">带宽:</label>-->
					<!--<div class="bk-form-row-cell">-->
						<!--<p>{{ bandwidth }} Mbps</p>-->
					<!--</div>-->
				<!--</div>-->
				<div id="p_memory" class="clearfix location-relative">
					<label for="" class="pull-left">块存储:</label>
					<div class="bk-form-row-cell">
						<p>{{ memory }} GB</p>
					</div>
				</div>
				<p style="margin-top:10px;">数量 : <input id="txtnum" type="number" value="1" min="1"> 台</p>
				<?php if($goodsInfo['goods'][0]['sn']=="Fimas_Serail"){ ?>
					<button class="btn btn-primary" id="te  st">申请使用</button>
				<?php	}else{ ?>
					<button class="btn btn-primary" id="btn-cart">加入购物车</button>
				<?php	} ?>

			</div>
		</div>
		<div style="width:1200px;margin:0 auto 30px;">
			<p><?php echo $goodsInfo['goods'][0]['description']; ?></p>
		</div>
		<div style="width:1200px;margin:0 auto 50px;">
			<div class="service-content-navi">
				<ul class="clearfix text-center">
					<li class="active"><a href="##">产品详情</a></li>
					<li><a href="##">产品规格</a></li>
				</ul>
			</div>
			<div class="product-approve">
				<ul>
					<li id="Approve">
						<div class="product-title clearfix">
							<h2>产品详情</h2>
						</div>
						<div class="index-more" id="currentDetail">

						</div>
					</li>
					<li id="Approve1" style="display:none;">
 						<div class="product-title clearfix">
							<h2>产品规格</h2>
						</div>
						<div class="index-more" id="currentSpec">

						</div>
						<!--<table class="table table-bordered">
							<thead>
								<tr>
									<th colspan="2" class="text-center">产品参数</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($goodsInfo['goods'][0]['goods_spec'] as $attr){ ?>
									<?php if($attr['is_display']==1){ ?>
									<tr>
										<td style="width:25%;background:#F4F4F4;"><?php echo $attr['spec_name'] ?></td>
										<td style="width:75%;"><?php echo $attr['spec_value'] ?></td>
									</tr>
									<?php } ?>
								<?php } ?>
							</tbody>
						</table> -->
					</div>
				</li>
			</ul>
		</div>
	</div>
</div>
</div>

<?php  $this->start('script_last'); ?>
<script type="text/javascript">
	$('.bk-form-row-cell').on('click','li',function(){
		if($(this).siblings().hasClass('active')){
			$(this).siblings().removeClass('active');
		}
		$(this).addClass('active');
	});
	$(function(){
		$("#txtnum").val("1");
	})
	var user=$("#user").val();
	$(".service-content-navi li").each(
		function(i){
			$(this).bind('click', function(){
				$(".service-content-navi li").removeClass("active");
				$(this).addClass("active");
				$('.product-approve li').css("display","none");
				$('.product-approve li').eq(i).css("display","block");
				updateSidebar();
			})
		});

	function updateSidebar(){
		$(".service-sidebar").height($(".service-content").height()+70);
	}

	$(window).on('resize',function(){
		updateSidebar();
	});

	$('#btn-cart').on('click',function(){
		if($('.service-alert').css('display')=='none'){
			$('.service-alert').fadeIn();
		}
		var num = $("#txtnum").val();
		if(user=="true"){
				$.ajax({
					type: "post",
					url: "<?= $this->Url->build(['controller' =>'orders', 'action' =>'car']); ?>",
					data:
					{
						goods_id: "<?= $goodsInfo['goods'][0]['id'] ?>",
						n: num,
						isCustom:"0",
						version:$("#txtversion").val(),
						charge:$("#txtcharge").val()
					},
					success: function(data) {
						$("#span_num").html(data.number);
					}
				});
			}else{
				var url= "<?=$this->Url->build(['controller' => 'Accounts', 'action' => 'login'])?>"
				window.location.href=url;
			}
	});

	$('#service-alert-close').on('click',function(){
		$('.service-alert').fadeOut();
	});

	$("#btnShopping").bind("click",function(){
		window.location.href="/";
	});

	$("#btnCheckout").bind("click",function(){
		var url;
		if(user=="true"){
		//获取url参数
		url= $("#goodsurl").val();
		}else{
		url= "<?=$this->Url->build(['controller' => 'Accounts', 'action' => 'login'])?>"
		}
		var num = $("#txtnum").val();
		window.location.href=url;
	})
</script>

<script type="text/javascript">
	// var app = angular.module('myApp', []);
	app.controller('myGoods', function($scope,$http) {
		$http.get("/home/getGoodsJson/"+<?= $goodsInfo['goods'][0]['id'] ?>).success(
			function(data){
				$scope.attributeList = data;
				$scope.chargeList=data["chargeList"];
				$scope.config=data[0].config.config;
				$scope.image=data[0].image.name;
				$scope.currentId=data[0].id;
				$scope.currentChargeId=data["current"].id;
				// $scope.currentDetail = $.parseHTML(data[0].goods_attribute_detail.attribute_detail);
				// $scope.currentSpec = $.parseHTML(data[0].goods_attribute_detail.attribute_spec);
				$("#currentDetail").html(data[0].goods_attribute_detail.attribute_detail);
				$("#currentSpec").html(data[0].goods_attribute_detail.attribute_spec);
				if(data[0].goods_attribute_detail.haveEip=="1"){
					$scope.bandwidth=data[0].goods_attribute_detail.bandWidth;
					$("#p_bandwidth").css("display","block");
				}else{
					$("#p_bandwidth").css("display","none");
				}
				if(data[0].goods_attribute_detail.haveMemory=="1"){
					$scope.memory=data[0].goods_attribute_detail.size;
					$("#p_memory").css("display","block");
				}else{
					$("#p_memory").css("display","none");
				}
				$scope.changeAttribute = function(obj){
					$scope.currentId=obj.id;
					$scope.config=obj.config.config;
					$scope.image=obj.image.name;
					// $scope.currentDetail = $.parseHTML(obj.goods_attribute_detail.attribute_detail);
					// $scope.currentSpec = $.parseHTML(obj.goods_attribute_detail.attribute_spec);
					$("#currentDetail").html(obj.goods_attribute_detail.attribute_detail);
				$("#currentSpec").html(obj.goods_attribute_detail.attribute_spec);
					if(obj.goods_attribute_detail.haveEip=="1"){
						$("#p_bandwidth").css("display","block");
						$scope.bandwidth=obj.goods_attribute_detail.bandWidth;
					}else{
						$("#p_bandwidth").css("display","none");
					}
					if(data[0].goods_attribute_detail.haveMemory=="1"){
						$scope.memory=data[0].goods_attribute_detail.size;
						$("#p_memory").css("display","block");
					}else{
						$("#p_memory").css("display","none");
					}
				}
				$scope.changeCharge = function(obj){
					$scope.currentChargeId=obj.id;
				}
			}
		);
	});
</script>
<?php $this->end(); ?>