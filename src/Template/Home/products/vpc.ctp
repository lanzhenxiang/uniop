<?=$this->Html->css(['common.css']);?>
<input id="goodsurl" type="hidden" value="<?=$this->url->build(['controller' => 'Orders', 'action' => 'buy'])?>" />
<input id="user" type="hidden" value="<?=$user?>">
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
                <?=$this->Html->image($goodsInfo['goods'][0]['icon'], ['alt' => '', 'width' => '258', 'height' => '318']);?>
			</div>
			<div class="service-promote-info pull-left" style="margin-left:50px;">
				<h1> <?=$goodsInfo['goods'][0]['name']?> </h1>
				<p style="width:800px;">
					<?php echo $goodsInfo['goods'][0]['description']; ?>
				</p>

				<!-- <h5 style="margin-bottom:25px;"><?=empty($charge) == false ? $charge[0]["charge_template"]["charge_note"] : ""?></h5> -->
				<div class="line"></div>
				<p style="margin-top:80px;"><input id="txtnum" type="hidden" value="1" min="1"> </p>
				<?php if ($goodsInfo['goods'][0]['sn'] == "Fimas_Serail") {?>
					<button class="btn btn-primary" id="test">申请使用</button>
				<?php	} else {?>
					<button class="btn btn-primary" id="btn-cart">加入购物车</button>
				<?php	}?>

			</div>
		</div>
		<div style="width:1200px;margin:0 auto 30px;">
			<p></p>
		</div>
		<div style="width:1200px;margin:0 auto 50px;">
			<div class="service-content-navi">
				<ul class="clearfix text-center">
					<li class="active"><a href="##">配置清单</a></li>
				</ul>
			</div>
			<div class="product-approve">
				<ul>
					<li id="Approve">
						<!-- div class="product-title clearfix">
							<h2>配置清单</h2>
						</div> -->
						<p style="margin-top:15px;"></p>
						<table class="table table-bordered">
				        <thead>
				        <tr>
				        	<th>配置项类型</th>
				            <th>配置项名称</th>
				            <th>计算性能</th>
				            <th>OS版本</th>
				            <th>数量（台）</th>
				            <th>所在子网</th>
				        </tr>
				        </thead>
				      <tbody>
				        <?php if(isset($_vpcInfo)){
				            foreach($_vpcInfo as $value){
				            ?>
				        <tr>
				        	<td><?php if(isset($value['type'])){ echo $value['type'];}else{echo '-';} ?></td>
				            <td><?php if(isset($value['tagname'])){ echo $value['tagname'];}else{echo '系统预留';} ?></td>
				            <td><?php if(isset($value['cpu_number'])){ echo $value['cpu_number'].'核'.$value['memory_gb'].'G';}else{echo '-';} ?></td>
				            <td><?php switch ($value['type']) {
				            	case 'fics':
				            		echo "容量".$value['total_cap'].",告警".$value["warn_cap"]."%";
				            		break;
				            	case 'oceanstor9k':
				            		echo "容量".$value['total_cap'].",告警".$value["warn_cap"]."%";
				            		break;
				            	default:
				            		if(isset($value['image_name'])){ echo $value['image_name'];}else{echo '-';}
				            		break;
				            } ?></td>
				            <td><?php if(isset($value['number'])){ echo $value['number'];}else{echo '系统预留';} ?></td>
				            <td><?php if(!empty($value['subnetName'])){ echo $value['subnetName'];}elseif($value['type']=='router' || $value['type']=='firewall'||$value['type']=='fics'||$value['type']=='oceanstor9k'||$value['type']=='subnet'){ echo '-'; } ?></td>
				        </tr>
				        <?php }} ?>
        			</tbody>
				    </table>
					</li>
				</li>
			</ul>
		</div>
	</div>
</div>
</div>

<?php $this->start('script_last');?>
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
					url: "<?=$this->Url->build(['controller' => 'orders', 'action' => 'car']);?>",
					data:
					{
						goods_id: "<?=$goodsInfo['goods'][0]['id']?>",
						n: num,
						isCustom:"1",
						charge:"1"
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
</script>
<?php $this->end();?>