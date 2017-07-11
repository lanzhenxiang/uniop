<!-- 主页模板 -->
<?= $this->element('swiper'); ?>
<div class="index-service">
	<div class="container-wrap">
		<?php if (isset($_view_vars['hot'])&&(!empty($_view_vars['hot']))){
		      foreach ($_view_vars['hot'] as $_key => $_value){
		          //顶级分类
		          if (($_value['parent_id'] == 0)&&(!empty($_value['good']))){ ?>
		<div class="product-title clearfix"><h2><?= $_value['name'] ?></h2></div>
		<div class="product-list text-center">
			<ul class="clearfix">
				<?php foreach ($_value['good'] as $_kk => $_vv){ ?>
					<li>
						<a href="<?= $this->Url->build('/home/products/'.$_vv['id']) ?>" >
    						<div class="product-thumb">
                                <?php if($_vv['icon']){ ?>
                                <?=$this->Html->image($_vv['icon'], ['alt' => '产品']);?>
                                <?php }else{ ?>
                                <img src="/images/nophoto.jpg" alt="暂无产品图片" width="100%"/>
                                <?php } ?>
    						</div>
    						<h3 style="margin:10px 0 0 0 "><?= $_vv['name'] ?></h3>
    						<div class="product-rank">
								<ul class="clearfix">
									<?php for($_i = 0;$_i<5;$_i++ ){ ?>
									<li style="margin:5px 0 10px 0"><i class="icon-star"></i></li>
									
									<?php }?>
								</ul>
							</div>
						</a>
					</li>

		<?php               
		          }
		
		?>
		 		</ul>
		 	</div>
		 <?php 
		          }
		          
		      }
		  }
		?>
	</div>
</div>
<?php
$this->start('script_last');
echo $this->Html->script('swiper.jquery.min.js');
?>
<script type="text/javascript">
	//首页banner
	$('.carousel').carousel({
		interval: false
	});

</script>
<?php 
$this->end();
?>