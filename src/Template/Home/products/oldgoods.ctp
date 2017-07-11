<?= $this->Html->css(['common.css']); ?>
<input id="goodsurl" type="hidden" value="<?=$this->url->build(['controller' => 'Orders', 'action' => 'buy'])?>" />
<input id="user" type="hidden" value="<?= $user ?>">
<div class="service-main clearfix">
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
                <?php if($goodsInfo['goods'][0]['icon']){?>
                <?=$this->Html->image($goodsInfo['goods'][0]['icon'], ['alt' => '', 'width' => '258', 'height' => '318']);?>
                <?php }else{ ?>
                <img src="/images/nophoto.jpg" alt="产品" style="width: 258px;height: 318px"/>
                <?php } ?>
				<!-- <img src="/images/product1.png" width="300" height="350"> -->
				<!--<?= $this->Html->image($goodsInfo['goods'][0]['icon'], ['alt' => '','width'=>'258','height'=>'318']); ?>-->
			</div>
			<div class="service-promote-info pull-left">
				<h1> <?= $goodsInfo['goods'][0]['name'] ?> </h1>
				<h5>
					<?php $str=""; ?>
					<?php foreach ($goodsInfo['goods'][0]['goods_spec'] as $key => $value) { ?>
						<?php if($value["spec_name"]=="CPU核数"){ ?>
							<?php $str .= $value["spec_value"]."CPU," ?>
						<?php } ?>
						<?php if($value["spec_name"]=="内存大小"){ ?>
							<?php $str .= $value["spec_value"]."内存," ?>
						<?php } ?>
						<?php if($value["spec_name"]=="显存大小"){ ?>
							<?php $str .= $value["spec_value"]."显存," ?>
						<?php } ?>
					<?php } ?>
					<?php $str = substr($str, 0, -1) ?>
					<?php echo $str; ?>
				</h5>
				<h5 style="margin-bottom:25px;"><?= empty($charge)==false ? $charge[0]["charge_template"]["charge_note"] : "" ?></h5>
				<div class="line"></div>
				<p style="margin-top:80px;">数量 : <input id="txtnum" type="number" value="1" min="1"> 台</p>
				<?php if($goodsInfo['goods'][0]['sn']=="Fimas_Serail"){ ?>
					<button class="btn btn-primary" id="test">申请使用</button>
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
						<div class="index-more">
							<?= $goodsInfo['goods'][0]['detail'] ?>
						</div>
					</li>
					<li id="Approve1" style="display:none;">
						<div class="product-title clearfix">
							<h2>产品规格</h2>
						</div>
						<table class="table table-bordered">
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
						</table>
					</div>
				</li>
			</ul>
		</div>
	</div>
</div>
</div>

<?php  $this->start('script_last'); ?>
<script type="text/javascript">
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
					},
					success: function(data) {

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

	// var user=$("#user").val();
	// $("#buy").click(function(){
	// 	var url;
	// 	if(user=="true"){
	// 	//获取url参数
	// 	url= $("#goodsurl").val();
	// 	}else{
	// 	url= "<?=$this->Url->build(['controller' => 'Accounts', 'action' => 'login'])?>"
	// 	}
	// 	window.location.href=url;
	// });
	// $("#test").click(function(){
	// 	if(user=="true"){
	// 			$.ajax({
	// 				type: "post",
	// 				url: "<?= $this->Url->build(['prefix' =>'console', 'controller' =>'ajax', 'action' =>'network', 'hosts', 'desktop_add']); ?>",
	// 				async: true,
	// 				timeout: 9999999,
	// 				data:
	// 				{
	// 					method: 'fimas_add',
	// 					regionCode: 'Region-ZFfTd7BG',
	// 				},
	// 				success: function(data) {
	// 					data = $.parseJSON(data);
	// 					if (data.Code != 0) {
	// 						alert(data.Message);
	// 					}else{

	// 					}
	// 					$('#disk-manage').modal("hide");
	// 				}
	// 			});
	// 		}else{
	// 		var url= "<?=$this->Url->build(['controller' => 'Accounts', 'action' => 'login'])?>"
	// 		window.location.href=url;
	// 		}
	// 	});
</script>
<?php $this->end(); ?>