<div class="index-breadcrumb">
	<ol class="breadcrumb">
	  <li><a href="<?= $this->Url->build(['controller'=>'home','action'=>'category'])?>">商品列表</a></li>
	  <li class="active"><?= $goods_category_data['name']?></li>
	</ol>
</div>
<div class="index-service">
	<div class="container-wrap">
		<div class="product-title">
			<h2><?= $goods_category_data['name']?></h2>
		</div>
		<div class="product-list text-center">
			<ul class="clearfix">
			<?php foreach($goods_data as $_good){?>
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
		<div class="product-pagination">
		<ul class="pagination">
		    <?php echo $this->Paginator->first('<<');?>
            <?php echo $this->Paginator->prev(' < '); ?>
            <?php echo $this->Paginator->numbers();?>
            <?php echo $this->Paginator->next(' > '); ?>
            <?php echo $this->Paginator->last('>>');?>
            </ul>
		</div>	
	</div>
</div>		