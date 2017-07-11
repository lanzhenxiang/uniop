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
<div class="index-breadcrumb">
	<ol class="breadcrumb">
		<li><a href="<?= $this->Url->build(['controller'=>'home','action'=>'category'])?>">商品列表</a></li>
		<li><a href="<?= $this->Url->build(['controller'=>'home','action'=>'goods'])?><?php if(isset($this_good_cate)){echo '/',$this_good_cate['id'];}?>"><?php if(isset($this_good_cate)){echo $this_good_cate['name'];}?></a></li>
		<li class="active"><?php if(isset($this_good_info)){echo $this_good_info['name'];}?> 商品购买</li>
	</ol>
</div>
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
				<!--<button class="btn btn-addition" id="btnShopping">继续购物</button>-->
				<button class="btn btn-danger" id="btnCheckout">结算</button>
			</div>
		</div>
	</div>
	<div class="service-content">
        <div class="clearfix service-promote buy-theme product-con">
            <div class="theme-left pull-left">
                <div class="clearfix">
                    <div class="service-promote-image pull-left">
                        <?= $this->Html->image($goodsInfo['goods'][0]['picture'], ['alt' => '','width'=>'258','height'=>'318']); ?>
                    </div>
                    <div class="service-promote-info pull-left">
                        <?= $this -> element('home/products/goods_' . $html_type); ?>
                    </div>
                </div>
                <div class="margintb20">
                    <p><?php echo $goodsInfo['goods'][0]['description']; ?></p>
                </div>
                <div style="margin:0 auto 50px;">
                    <div class="service-content-navi">
                        <ul class="clearfix text-center">
                            <li class="active"><a href="##">产品详情</a></li>
                        </ul>
                    </div>
                    <div class="product-approve">
                        <ul>
                            <li id="Approve">
                                <div class="product-title clearfix">
                                    <h2>产品详情</h2>
                                </div>
                                <div class="index-more" id="currentDetail">
                                    <?= $goodsInfo["goods"][0]["detail"]?>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!--right-->
            <input type="hidden" value ="<?= $good_type?>" id="goodType">
            <?php if(in_array($good_type, $need_config_goods)): ?>
            <div class="theme-right pull-right">
                <div class="theme-right-mian">
                    <p class="theme-buy">当前配置 </p>
                    <ul class="goods-detail">
                    	<?php if (!empty($config)): ?>
                    	   <?= $this->element('home/config/config_' . $good_type)?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
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
		
		var num = $("#txtnum").val();
		if(user=="true"){
			<?php if((in_array($good_type, $need_config_goods)) && empty($config)){ ?>
				alert('请先选择配置');
			<?php } else {?>
			if($('.service-alert').css('display')=='none'){
				$('.service-alert').fadeIn();
			}
			$.ajax({
				type: "post",
				url: "<?= $this->Url->build(['controller' =>'orders', 'action' =>'car']); ?>",
				data:
				{
					goods_id: "<?= $goodsInfo['goods'][0]['id'] ?>",
					n: num,
					number: num,
					isCustom:"0",
                    charge:5,
					version:$("#txtversion").val(),
					goodType:'<?= $good_type?>',
					bscharge:$("#txtcharge").val(),
					price_id:$('#price_id').val(),
					config:'<?php echo isset($config) ? json_encode($config):"" ?>'
				},
				success: function(data) {
					$("#span_num").html(data.number);
				}
			});
			<?php } ?>

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

<?php $this->end(); ?>