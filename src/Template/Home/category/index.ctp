<div class="index-service">
	<div class="container-wrap">
		<?php foreach ($good_category['category_data'] as $_k => $_category_v){?>
		<?php if(!empty($good_category['goods_count'][$_category_v['id']])){?>
		<?php if($good_category['goods_count'][$_category_v['id']] > 0){?>
		<div class="product-kinds">
			<div class="product-title clearfix">
				<h2 class="pull-left">
					<a href="javascript:;" class="product-kinds-btn"><img src="/images/goods-list1.png" /></a>
					<?= $_category_v['name']?>
				</h2>
				<?php if($good_category['goods_count'][$_category_v['id']] > 8){?><!-- 一个分类最多显示8个商品 --> 
				<div class="pull-right" style="line-height:32px;margin-right:5px;">
					<a href="<?php echo $this->Url->build(array('controller' => 'Home','action'=>'goods',$_category_v['id'])); ?>">更多</a>
				</div>
				<?php }?>
			</div>
			<div class="product-list text-center">
				<?php $goods_array_data = array_chunk($_category_v['good'],4); $num_ul = count($goods_array_data); ?>
				<?php if($num_ul > 0){?>
				<div class="product-list-show">
					<ul class="clearfix">
					<?php foreach($goods_array_data[0] as $_good){?>
						<li>

							<a href="<?= $this->Url->build('/home/products/'.$_good['id']) ?>" >
								<div class="product-thumb">
								<?php if(!empty($_good['icon'])){
                                  echo '<img src="/images/'.$_good['icon'].'" alt="">';            
                                }?>
								</div>
								<h3 style="margin:10px 0 0 0"><?= $_good['name']?></h3>
								<div class="product-rank">
									<ul class="clearfix">
									<?php for($_i = 0;$_i<5;$_i++ ){
										if($_i<$_good['star']){?>
										<li style="margin:5px 0 10px 0"><i class="icon-star"></i></li>
										<?php }else{?>
										<li style="margin:5px 0 10px 0"><i class="icon-star-empty"></i></li>
									<?php }}?>
									</ul>
								</div>
							</a>
						</li>
					<?php }?>
					</ul>
				</div>
				<?php } if($num_ul >1){?>
				<div class="product-list-more">
					<ul class="clearfix">
						<?php foreach($goods_array_data[1] as $_good){?>
						<li>
							<a href="<?= $this->Url->build('/home/products/'.$_good['id']) ?>" >
								<div class="product-thumb">
								<?php if(!empty($_good['icon'])){
                                  echo '<img src="/images/'.$_good['icon'].'" alt="">';            
                                }?>
								</div>
								<h3 style="margin:10px 0 0 0"><?= $_good['name']?></h3>
								<div class="product-rank">
									<ul class="clearfix">
									<?php for($_i = 0;$_i<5;$_i++ ){
										if($_i<$_good['star']){?>
										<li style="margin:5px 0 10px 0"><i class="icon-star"></i></li>
										<?php }else{?>
										<li style="margin:5px 0 10px 0"><i class="icon-star-empty"></i></li>
									<?php }}?>
									</ul>
								</div>
							</a>
						</li>
					<?php }?>
					</ul>
				</div>
				<div class="text-center">
					<a href="javascript:;" class="product-list-btn "><img src="/images/goods-list2.png" /></a>
				</div>	
				<?php }?>
			</div>
		</div>
		<?php } } } ?>	

	</div>
</div>
<script>
	$('.product-list-btn').on('click',function(){
		var $this = $(this);
		if($this.parent().prev().css('display')=='none'){
			$this.parent().prev().slideDown(function(){
				$this.find('img').attr('src','/images/goods-list3.png');
			});
		}else{
			$this.parent().prev().slideUp(function(){
				$this.find('img').attr('src','/images/goods-list2.png');
			});
		}
	});

	$('.product-kinds-btn').on('click',function(){
		var $this = $(this);
		if($this.parents('.product-title').next().css('display')=='none'){
			$this.parents('.product-title').next().slideDown();
		}else{
			$this.parents('.product-title').next().slideUp();
		}
	});

</script>