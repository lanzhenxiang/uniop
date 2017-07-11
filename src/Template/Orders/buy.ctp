<?= $this->Html->css(['common.css']); ?>
<?php if(!empty($_goodsCookie)){ ?>
<div class="container-wrap top-title">
	<h2>我的购物车</h2>
</div>
<div class="container-wrap">
	<div class="orders-list mt20 pb10">
		<table class="uc-table">
			<tbody>
				<tr>
					<th>
						<input checked="checked" type="checkbox" class="all-check"></th>
					<th>商品图标</th>
					<th>商品规格</th>
					<th>计费方式</th>
					<th>单台原价</th>
					<th>购买数量</th>

					<th>操作</th>
				</tr>
				<?php foreach ($_goodsCookie as $index=>$item) { ?>
				<?php $cookieAttr=json_decode($item['attr'],true); ?>
				<tr>
					<td>
						<input checked="checked" index="<?= $index ?>" type="checkbox" class="single-check">
					</td>
						<!-- <td><?= $item['info']['goods'][0]['name'] ?></td> -->
					<td>
					<?= $this->Html->link(
				                $this->Html->image($item['info']['goods'][0]['mini_icon'], ["alt" => "","width"=>"60","height"=>"60"]),
				                "/home/products/".$item['info']['goods'][0]['id'],
				                ['escape' => false,'target' => '_self']
				    ); ?>
				    </td>
				    <td>
				    	<!-- 规格 -->
						<?php if($item['fixed']==0){?>
						<!-- 判断新商品 -->
							<?php if(isset($item['good_type']) && !in_array($item['good_type'], $no_new_goods) && $item['good_type'] != "ecs"){ ?>
								名称：<?= $item['info']['goods'][0]['name']?></br>
								版本: <?= $item['version']["name"] ?><br>
								<?php
								switch ($item['good_type']) {
									case 'citrix_public':
									case 'citrix': ?>
										部署区位: <?= $item['version']["region"]["name"] ?><br>
										所在VPC: <?= $item['version']["vpc"]["name"] ?><br>
										所在子网: <?= $item['version']["subnet"]["name"] ?><br>
										<?php if (!empty($item['version']["subnet2"]["name"])) {echo '扩展网络：' . $item['version']["subnet2"]["name"].'<br>' ;} ?>
										CPU: <?= $item['version']["spec"]["instancetype"]["cpu"] ?>核<br>
										内存: <?= $item['version']["spec"]["instancetype"]["memory"] ?>GB<br>
										GPU: <?= $item['version']["spec"]["instancetype"]["gpu"] ?>MB<br>
									<?php
										break;
									case 'bs': ?>
										购买时长: <?= $item['version']['price_info']['duration'] ?>月<br>
										服务配置：   厂商自定义配置<br>
									<?php
										break;
									case 'mpaas': ?>
										服务配置：   厂商自定义配置<br>
									<?php	break;
                                    case 'eip':
                                        echo '部署区位: '.$item['version']['dyName'].'<br>';
                                        echo '所在VPC: '.$item['version']['vpcName'].'<br>';
                                        echo '带宽大小: '.$item['version']['bandwidth'].'M<br>';
                                        break;
                                    case 'vfw':
                                    case 'waf': 
                                        echo '部署区位: '.$item['version']['dyName'].'<br>';
                                        echo '所在VPC: '.$item['version']['vpcName'].'<br>';
                                        break;
                                    case 'vpc':
                                        echo '部署区位: '.$item['version']['dyName'].'<br>';
                                        echo '路由器名: '.$item['version']['routerName'].'<br>';
                                        echo 'VPC名: '.$item['version']['vpcName'].'<br>';
                                        break;
                                    case 'elb':
                                        echo '部署区位: '.$item['version']['dyName'].'<br>';
                                        echo '名称: '.$item['version']['lbsName'].'<br>';
                                        echo '所在VPC: '.$item['version']['vpcName'].'<br>';
                                        echo '所在子网: '.$item['version']['netName'].'<br>';
                                        break;
                                    case 'disks':
                                        echo '部署区位: '.$item['version']['dyName'].'<br>';
                                        echo '名称: '.$item['version']['disksName'].'<br>';
                                        echo '容量: '.$item['version']['size'].'GB<br>';
                                        echo '所在VPC: '.$item['version']['vpcName'].'<br>';
                                        echo '所在子网: '.$item['version']['netName'].'<br>';
                                        break;
									} ?>
							<?php } elseif (isset($item['good_type']) && $item['good_type'] == 'ecs') { $item['version'] = (array)$item['version']; //解析json ?>
								名称：<?= $item['info']['goods'][0]['name']?></br>
								版本：自定义配置</br>
								部署区位: <?= $item['version']["dyName"] ?><br>
								所在VPC: <?= $item['version']["vpcName"] ?><br>
								所在子网: <?= $item['version']["netName"] ?><br>
								<?php if (!empty($item['version']["subnetName2"])) {echo '扩展网络：' . $item['version']["subnetName2"].'<br>' ;} ?>
								CPU: <?= $item['version']["cpu"] ?>核<br>
								内存: <?= $item['version']["rom"] ?>GB<br>
								系统镜像：<?= $item['version']["OsName"] ?><br>
								<!-- GPU: 0MB<br> -->
							<?php } else {?>
							<?php if(!empty($item['info']['goods'][0]['goods_vpc'])){ ?>
								<?php $goods = new App\Controller\Admin\GoodsVpcController(); ?>
									<?php  $vpcInfo   = $goods->findVpcEcsConfigure($item['info']['goods'][0]['goods_vpc']);  ?>
									<?= "VPC商品:".$item['info']['goods'][0]["name"] ?><br/>
									<?php foreach ($vpcInfo as $spec){ ?>
										<?php switch ($spec["type"]) {
											case 'ecs':
												# code...
												echo "云主机:".$spec['tagname']; ?><br/><?php
												break;
											case 'desktop':
												echo "云桌面:".$spec['tagname']; ?><br/><?php
												break;
											case 'subnet':
												echo "子网:".$spec['tagname']; ?><br/><?php
												break;
											case 'firewall':
												echo "防火墙:".$spec['tagname']; ?><br/><?php
												break;
											case 'router':
												echo "路由器:".$spec['tagname']; ?><br/><?php
												break;
											case 'elb':
												echo "负载均衡:".$spec['tagname']; ?><br/><?php
												break;
											case 'oceanstor9k':
												echo "华为9000:".$spec['tagname']; ?><br/><?php
												break;
											case 'fics':
												echo "FICS:".$spec['tagname']; ?><br/><?php
												break;
											default:
												# code...
												break;
										 } ?>
									<?php } ?>
							<?php }elseif(!empty($item['version'])){ ?>
								版本: <?= $item['version']->attribute_name; ?><br/>
								基础设置: <?= $item['version']->config["config"]; ?><br/>
								镜像: <?= $item['version']->image["name"]; ?><br/>
								<?php if($item['version']->goods_attribute_detail["haveEip"]=="1"){ ?>
								带宽: <?= $item['version']->goods_attribute_detail["bandWidth"]; ?><br/>
								<?php } ?>
								<?php if($item['version']->goods_attribute_detail["haveMemory"]=="1"){ ?>
								块存储: <?= $item['version']->goods_attribute_detail["size"]; ?><br/>
								<?php } ?>
							<?php }else{ ?>
								<?php foreach ($item['info']['goods'][0]['goods_spec'] as $spec){ ?>
										<?php if($spec['is_display']==1){  ?>
											<?= $spec['spec_name'].":".$spec['spec_value'] ?><br/>
										<?php }?>
								<?php } ?>
							<?php }?>
							<?php }?>
						<?php } elseif ($item['fixed']==3) { ?>
							名称:<?= $cookieAttr['volumeName'] ?><br>
							地域<?= $cookieAttr['area'] ?><br>
							容量：<?= $cookieAttr['size'] ?><br>
						<?php }elseif ($item['fixed']==5) { ?>
							CPU核数:<?= $cookieAttr['cpu'] ?><br>
							内存大小:<?= $cookieAttr['rom'] ?><br>
							操作系统:<?= $cookieAttr['imageName'] ?><br>
							镜像文件名称:<?= $cookieAttr['imageName'] ?><br>
							实例名称：<?= $cookieAttr['name'].'-'.$cookieAttr['nameNum1'];if($cookieAttr['nameNum2']){echo '至'.$cookieAttr['nameNum2'];} ?><br>
							应用软件:<?= $cookieAttr['netName'] ?><br>
							云服务提供商:<?= $cookieAttr['csName'] ?><br>
							机房位置:<?= $cookieAttr['dyName'] ?><br>
							默认子网:<?= $cookieAttr['cpu'] ?><br>
							vpc标识:<?= $cookieAttr['cpu'] ?><br>
							提供商区域代码:<?= $cookieAttr['csCode'] ?><br>
						<?php }elseif ($item['fixed']==4) { ?>
							厂商：<?= $cookieAttr['csName'] ?><br>
							地域：<?= $cookieAttr['dyName'] ?><br>
							路由器名称：<?= $cookieAttr['routerName'] ?><br>
							网络地址 ：<?= $cookieAttr['cidr'] ?><br>
						<?php }elseif ($item['fixed']==6) { ?>
							地域：<?= $cookieAttr['area'] ?><br>
							子网名称：<?= $cookieAttr['subnetName'] ?><br>
							路由器：<?= $cookieAttr['routerName'] ?><br>
							网络地址：<?= $cookieAttr['cidr'] ?><br>
						<?php }else { ?>
							厂商：<?= $cookieAttr['csName'] ?><br>
							地域：<?= $cookieAttr['dyName'] ?><br>
							CPU：<?= $cookieAttr['cpu'] ?><br>
							内存：<?= $cookieAttr['rom'] ?><br>
							镜像：<?= $cookieAttr['imageName'] ?><br>
							子网：<?= $cookieAttr['netName'] ?><br>
						<?php } ?></td>
						<!-- 价格 -->
					<?php if($item['fixed']==0 && isset($item['good_type'])):?>
						<?php switch ($item['good_type']):
						    case 'bs':?>
						        <td>
        							<?= $item['version']['price_info']['name']?>
        						</td>
        						<td>
        							<?= $item['version']['price_info']['price']?>元/<?= $item['version']['price_info']['duration'] ?>个月
        						</td>
        						<td>1 </td>
						    <?php break;
						    case 'mpaas': ?>
    						    <td>
        							<?= $item['version']['price_info']['name']?>
        						</td>
        						<td>
        							<?= $item['version']['price_info']['price']?><?= $item['version']['price_info']['unit']; ?>
        						</td>
        						<td>1 </td>	
						    <?php break;
						    case 'citrix':
						    case 'citrix_public':?>
    						    <td>
        							<?= $item['version']['price_info']['interval']?>
        						</td>
        						<td>
        							<?= $item['version']['price_info']['price']?><?= $item['version']['price_info']['unit']?>
        						</td>
        						<td>
        							<input type="number" value="<?= $item['num'] ?>" class="changenum" data-id="<?= $index ?>" min="1">
        						</td>
						    <?php break;
						    case 'ecs':?>
    						    <td>
        							<?= $item['version']['billCycleName']?>
        						</td>
        						<td>
        							<?= $item['version']['imagePay']+$item['version']['instancePay']?>/<?= $item['version']['unit']?>
        						</td>
        						<td>
        							<input type="number" value="<?= $item['num'] ?>" class="changenum" data-id="<?= $index ?>" min="1">
        						</td>
						    <?php break;
						    case 'eip':?>
						    <td>
    							<?= $item['version']['billCycleName']?>
    						</td>
    						<td>
    							<?= $item['version']['price']*$item['version']['bandwidth']?>/<?= $item['version']['unit']?>
    						</td>
    						<td>
    							<input type="number" value="<?= $item['num'] ?>" class="changenum" data-id="<?= $index ?>" min="1">
    						</td>
						    <?php break;
						    case 'vfw':
						    case "waf":
						    case 'vpc':
						    case 'elb':
						    case 'disks':?>
    						    <td>
        							<?= $item['version']['billCycleName']?>
        						</td>
        						<td>
        							<!--<?= $item['version']['price']?><?= $item['version']['unit']?>-->
									<?php if(isset($item['version']['totalPrice'])&&!empty($item['version']['totalPrice'])){echo $item['version']['totalPrice'];}else{echo $item['version']['price'];}?><?= $item['version']['unit']; ?>
        						</td>
        						<td>
        							<input type="number" value="<?= $item['num'] ?>" class="changenum" data-id="<?= $index ?>" min="1">
        						</td>	
						     <?php break;
						     default:?>
    						     <td>
        							月底结算
        						</td>
        						<td>
        							0
        						</td>
        						<td>
        							<input type="number" value="<?= $item['num'] ?>" class="changenum" data-id="<?= $index ?>" min="1">
        						</td>
						     <?php break;
						endswitch;?>
					<?php endif;?>
					<!-- 操作 -->
					<td>
					<?php if(isset($item['good_type']) && !empty($item['good_type'])){
						switch ($item['good_type']) {
							case 'bs':
								break;
							case 'mpaas':
								break;
							case 'citrix':
								break;
							case 'citrix_public': ?>
							<button class="btn btn-primary" onclick="changeConfigCitrix(<?= $index?>)" >修改配置</button>
							<?php 
								break;
							case 'ecs':?>
							<button class="btn btn-primary" onclick="changeConfigEcs(<?= $index?>)" >修改配置</button>
							<?php	break;
							default:
							break;
						}
						}?>
						<a href="javascript:;" data-id="<?= $index ?>" data-name="<?= $item['info']['goods'][0]['name'] ?>" data-toggle="modal" data-target="#del-modal">删除</a>
					</td>
				</tr>
				<?php } ?></tbody>
		</table>
	</div>
	<div class="clearfix order-total">
		<div class="pull-left">
			<a href="/">继续购物</a>
			&nbsp;|&nbsp;
			共
			<span class="text-primary">
				<?= count($_goodsCookie) ?></span>
			件商品，已选择
			<span class="text-primary" id="order-checked">
				<?= count($_goodsCookie) ?></span>
			件
		</div>
		<div class="pull-right">
			<a id="btnbuys" class="btn btn-primary">提交订单</a>
		</div>
	</div>
</div>
<?php }else{ ?>
<div class="no-shopping clearfix">
	<div class="pull-left">
		<img src="/images/cart.png"></div>
	<div class="pull-left" style="margin-top:3px;margin-left:10px;">
		<h5>购物车空空的哦~，去看看心仪的商品吧~</h5>
		<p>
			<a href="/">去购物></a>
		</p>
	</div>
</div>
<?php } ?>

<div class="modal fade" id="submit-modal" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="myModalLabel">提示</h4>
			</div>
			<div class="modal-body">请至少选中一件商品</div>
		</div>
	</div>
</div>

<div class="modal fade" id="del-modal" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">删除商品</h4>
			</div>
			<div class="modal-body">删除商品？</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-negative" data-dismiss="modal">取消</button>
				<button type="button"  class="btn btn-danger" data-id="" data-name="" id="del-modal-btn">确认</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="callback-modal" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">提示</h4>
			</div>
			<div class="modal-body clearfix" style="line-height:34px;">
				订单提交成功 !
				<a class="btn btn-primary pull-right" href="/">继续购物</a>
			</div>
		</div>
	</div>
</div>
<?php  $this->
start('script_last'); ?>
<script>
	$(function(){
		var priceTotal = 0;
		// $("tr .s-price .cny").each(function(){
		// 	var price =  Number($(this).html());
		// 	priceTotal += price;
		// });
		// $(".total-price-my span:eq(1)").html(priceTotal.toFixed(2));
		$("#btnbuys").click(function(){
			if($('.single-check').filter(':checked').length==0){
				$('#submit-modal').modal('show');
				return false;
			}else{
				var indexs = "";
				$(".single-check:checkbox").each(function(){
					if ($(this)[0].checked == true) {
						var index = $(this).attr("index");
						indexs += index+",";
					}
				});

				var url = '/orders/begainBuy/car';

				$.ajax({
					type:"post",
					url:url,
					data:{"indexs":indexs},
					success: function (data) {
						data= $.parseJSON(data);
						if(data.Code=="0"){
							$('#callback-modal').modal('show');
							$('#callback-modal').on('hidden.bs.modal', function(){
								window.location.reload();
							});
						}else if(data.Code=="400"){
							window.location.href=data.url;
						}else{
							alert(data.Message);
						}
					}
				});
			}
		});
		var $table = $('table');
		var $allCheckbox = $table.find('.all-check');
		var $singleCheckbox = $table.find('.single-check');
		$allCheckbox.on('click', function(){
			if(!$(this).prop('checked')){
				$singleCheckbox.prop('checked', false);
				$('#order-checked').text(0);
			}else{
				$singleCheckbox.prop('checked', true);
				$('#order-checked').text($singleCheckbox.length);
			}
		});
		$singleCheckbox.on('click', function(){
			if(!$(this).prop('checked')){
				$allCheckbox.prop('checked', false);
			}else{
				if($singleCheckbox.filter(':checked').length == $singleCheckbox.length) $allCheckbox.prop('checked', true);
			}
			$('#order-checked').text($singleCheckbox.filter(':checked').length);
		});
	});

	$('#del-modal').on('show.bs.modal',function(event){
		$('#del-modal-btn').attr('data-id',$(event.relatedTarget).attr('data-id'));
		$('#del-modal-btn').attr('data-name',$(event.relatedTarget).attr('data-name'));
	});

	$("#del-modal-btn").on("click",function(){
		var index = $("#del-modal-btn").attr("data-id");
		$.ajax({
			type:"post",
			url:"/orders/del/"+index,
			async:true,
			data:{totle:$(".total-price-my span:eq(1)").html()},
			success: function (data) {
				// data= $.parseJSON(data);
				if(data=="ok"){
					window.location.reload();
				}
			}
		});
	});

	$(".changenum").change(function(){
		var index = $(this).attr("data-id");
		var val = $(this).val();
		$.ajax({
			type:"post",
			url:"/orders/changenum",
			async:false,
			data:{"index":index,"num":val},
			success: function (data) {
				if(data=="ok"){
				}
			}
		});
	});
	//修改ecs配置
function changeConfigEcs(obj){
	var i = new Array();
	<?php foreach ($_goodsCookie as $key => $value) { $value['version'] = isset($value['version']) ? $value['version']: []; ?>
		i[<?= $key?>] = '<?= json_encode($value["version"])?>';
		goods_id = '<?= $value["goods_id"]?>';
	<?php } ?>
	i[obj] = $.parseJSON(i[obj]);
	// console.log(i)
//
	var url = '/home/selectEcs/'+goods_id;
	i[obj].url = "/orders/changeCookie/"+obj+"/";

    $.StandardPost(url,i[obj]);

}
	
function changeConfigCitrix(obj) {
	var i = new Array();
	<?php foreach ($_goodsCookie as $key => $value) { 
	    if (isset($value['good_type']) && !empty($value['good_type']) && $value['good_type'] == "citrix_public") {
	    $value['config'] = isset($value['config']) ? $value['config']: []; ?>
		i[<?= $key?>] = <?= json_encode($value["config"])?>;
		goods_id = '<?= $value["goods_id"]?>';
		price_id = '<?= $value["price_id"]?>';
		version_id = '<?= $value["version"]["id"]?>';
	<?php } } ?>
	i[obj] = $.parseJSON(i[obj]);
	console.log(i[obj])

	var url = '/home/selectCitrixVpc/'+goods_id+'/'+version_id+'/'+price_id;
	i[obj].url = "/orders/changeCookie/"+obj+"/";

    $.StandardPost(url,i[obj]);
}

//post 表单 来源json
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
<?php $this->
end(); ?>