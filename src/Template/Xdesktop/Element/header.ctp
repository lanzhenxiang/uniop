<!-- header -->
<div class="header">
    <div class="header-relate ">
        <div class="container-wrap clearfix">
                <a href="<?= $this->Url->build('/') ?>"><?= $this->Html->image('logo.png',['class'=>'pull-left nav-logo']); ?></a>
            <div class="pull-right header-tel">
                <a class="header-controll" href="<?= $this->Url->build('/xdesktop') ?>"><i class="icon-desktop"></i>工具中心</a>
                <?php 
                if ($this->request->session()->read("Auth.User.popedomname")) {
                  $popedomname = $this->request->session()->read("Auth.User.popedomname");
                  if(in_array('cpb_home_console', $popedomname)){
                    echo '<a class="header-controll" href="'. $this->Url->build('/console') .'"><i class="icon-dashboard"></i>资源中心</a>';
                  }
                    if(in_array('cmop_global_sys_admin', $popedomname) || in_array('cmop_global_tenant_admin', $popedomname)){
                    echo '<a class="header-controll" href="'. $this->Url->build('/admin') .'"><i class="icon-wrench"></i>运营管理中心</a>';
                  }
                }
                ?>
            </div>
        </div>
    </div>
    <div class="header-content container-wrap clearfix">
        
        <div class="header-navi pull-left">
            <ul class="text-center clearfix">
                <li class="header-navi-service">
                    <a href="">产品与服务</a>
                    <div class="header-navi-detail">
                      <ul class="text-left">
                      <?php if(!empty($good_category['category_data'])){?>
                      <?php foreach($good_category['category_data'] as $category){?>
                        <?php if(!empty($good_category['goods_count'][$category['id']])) {if($good_category['goods_count'][$category['id']] > 0){?>
                        <li onclick="redirect('<?= $this->Url->build('/home/goods/'.$category['id']) ?>')">
                          <?= $category['name']?>
                          <i class="icon-angle-right pull-right"></i>

                          <?php if(!empty($category['good'])){?>
                          <?php $goods_array_data = array_chunk($category['good'],5); $num_ul = count($goods_array_data) <= 3 ? count($goods_array_data):3;?>
                          <div class="header-navi-detail-info-<?= $num_ul?> header-navi-detail-info clearfix">

                          <?php foreach ($goods_array_data as $_g_k => $goods_data) { if($_g_k>2){break;}?>
                            <ul class="pull-left">
                            <?php foreach($goods_data as $good){?>

                              <li>
                                <a href="<?= $this->Url->build('/home/products/'.$good['id']) ?>">
                              <?php 
                                if(!empty($good['picture'])){
                                  echo '<img src="/images/'.$good['picture'].'" width="70px" alt="">';            
                                }
                                ?>
                                <?= $good['name']?></a>
                              </li>
                            <?php }?>
                            </ul>
                                
                          <?php }?>
                            
                          </div>
                          <?php }?>
                        </li>

                        <?php } } } }?>
                      </ul>
                    </div>
                </li>
                <!-- <li class="header-navi-service">
                    <a href="">解决方案</a>
                    
                </li>
                <li class="header-navi-service"><a href="">文 档</a></li>
                <li class="header-navi-service"><a href="">关于我们</a></li> -->
            </ul>
        </div>
        <div class="pull-right">
          <div class="header-info pull-left">
           
              <?php if($this->Session->read('Auth.User.id')){ ?>
                <span class="user-login">
                <!-- <img src="/images/user-header.png" alt=""> -->
                <a href="/console/accountinfo/index" class="header-change">
                <?php $image = $this->request->session()->read('Auth.User.image');
                if(!empty($image)){
                  $image = explode("/",$image);
                  $images = $image[1]."/".$image[2];
                  echo '<img src="/images/'.$images.'" alt="">';            
                }else{
                  echo '<img src="/images/user-photo.png" alt="">';
                }
                ?>
              <span><?php echo $this->Session->read('Auth.User.username');  ?></span></a>|<a href="/accounts/loginout" title="">退出</a></span>
              <?php }else{ ?>
              <a href="<?= $this->Url->build('/accounts/login'); ?>">请登录</a> <!-- | <a href="">注 册</a> -->
              <?php } ?>
          </div>
          <div class="header-cart pull-right">
              <img src="/images/nav-cart.png" alt="">
              <span class="num"><?= isset($_number)?$_number:0 ?></span>
          </div>
        </div>
        
    </div>
</div>
<!-- header end -->


<?= $this->Html->script(['jQuery-2.1.3.min.js','angular.min.js']); ?>
<script>
    $('#controll').on('click',function(){
        var username = "<?php echo $this->Session->read('Auth.User.id') ?>";
        if(username){
            var url = "<?php echo $this->Url->build('/console'); ?>";
            $('#controll').attr('href',url);
        }else{
            alert('您还没有登陆，请先登录');
        }
    });
    $(".header-navi-service").hover(function(){
        $(this).children(".header-navi-mask").show();
    },function(){
         $(this).children(".header-navi-mask").hide();
    })

</script>