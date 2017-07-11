<div class="header-back clearfix">
	<a href="<?= $this->Url->build('/') ?>"><img src="/images/logo-console.png" class="logo-back"></a>

	<ul class="header-nav pull-right">
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
              <span><?php echo $this->Session->read('Auth.User.username');  ?></span></a></span>
              <?php }else{ ?>
              <a href="/accounts/login">请登录</a> <!-- | <a href="">注 册</a> -->
              <?php } ?>
          </div>
		<li>
	        <h2 title="注销"><a href="/accounts/loginout"><i class="icon-off"></i></a></h2>
	    </li>
	</ul>   
</div>