<div class="wrap-nav-left">
    <div class="header-pic clearfix">
        <div class="pull-left">
            <?php $image = $this->request->session()->read('Auth.User.image');
            if(!empty($image)){
                $image = explode("/",$image);
                $images = $image[1]."/".$image[2];
                echo $this->Html->image($images);
            }else{
                echo $this->Html->image('user-photo.png');
            }
            ?>
        </div>
        <div class="header-info pull-left">
            <?php if($this->Session->read('Auth.User.id')){ ?>
            <span class="wel">欢迎</span>
            <br>
            <span><?php echo $this->Session->read('Auth.User.username');  ?></span>
            <br/>
            <span><?php echo $this->Session->read('Auth.User.department_name');  ?></span>
            <?php }else{ ?>
                <span>请登录</span>
            <?php } ?>
		</div>
	</div>
    <div class="nav-total-open">
        <i class="icon-align-justify"></i>
    </div>
    <div class="wrap-nav-detail">
        <?php
            if (isset($_console_category) && !empty($_console_category)){

                foreach ($_console_category as $key => $value) {
                    if (in_array($value['popedom_code'], $this->Session->read('Auth.User.popedomname'))) {
                        ?>
                        <div class="main-left">
                        <p class="title"><span
                                class="none"><?= $value['label'] ?></span><?php if (!empty($value['children'])) { ?><i
                                class="icon-angle-up"></i><?php } ?></p>
                        <?php
                        //子菜单
                        if (!empty($value['children'])) {
                            ?>
                            <ul class="total">
                                <?php
                                foreach ($value['children'] as $kk => $vv) {

                                    if (in_array($vv['popedom_code'], $this->Session->read('Auth.User.popedomname'))) {
                                       
                                        ?>
                                        <a href="<?= $this->Url->build($vv['url']) ?>">
                                            <li id="<?php echo $vv['name'];?>" <?php
                       
                                                $_fun = explode('/',str_replace("_","",$vv['url']));
                                                //debug($_fun);
                                                //exit;
                                                if( strtolower($_fun[2]) == strtolower($_request_params['controller'])){

                                                    if( isset($_fun[4]) && strtolower($_fun[4]) == 'fics' && ($this->request->url=="console/network/lists/fics"||$this->request->url=="console/network/add/fics"||$this->request->url=="console/network/lists/ficsHosts")){
                                                        echo 'class="active"';
                                                    }else if(strtolower($_fun[2]) == "orders" && strtolower($_fun[3]) == strtolower($_request_params['action'])){
                                                        echo 'class="active"';
                                                    }else if($vv['url'] != "/console/network/lists/fics" && ($this->request->url != "console/network/lists/fics"&&$this->request->url!="console/network/add/fics" && $this->request->url!="console/network/lists/ficsHosts") && strtolower($_fun[2]) != "orders"
                                                    ){
                                                        echo 'class="active"';
                                                    }
                                                }

                                                ?>>

                                            
                                                <i class="icon-<?= empty($vv['icon']) ? '' : $vv['icon']; ?>"></i>&nbsp;&nbsp;<?= $vv['label'] ?>
                                            </li>
                                        </a>
                                    <?php
                                    }
                                }
                                ?>
                            </ul>
                            </div>
                        <?php
                        }

                    }
                }
            }
        ?>
    </div>
</div>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>
<script>
    var pass ="<?php  if(!empty($_request_params['pass'] && count($_request_params['pass'])>1)){ echo $_request_params['pass'][1];}else{ echo '';} ?>";
     if(pass){
     	var excp = document.getElementById('excp');
        if(pass == 'desktop'){
        	var desktop = document.getElementById('desktop');
        	desktop.className='active';
        	removeClass(excp,"active");
        }else if(pass == 'hosts' || pass == 'disks' || pass == 'router' || pass == 'subnet' || pass == 'elb' || pass == 'eip' || pass == 'vpc'){
        	var network = document.getElementById('network');

        	network.className='active';
        	removeClass(excp,"active");

        }
    }

    function hasClass(obj, cls) {
        return obj.className.match(new RegExp('(\\s|^)' + cls + '(\\s|$)'));
    }

    function removeClass(obj, cls) {
        if (hasClass(obj, cls)) {
            var reg = new RegExp('(\\s|^)' + cls + '(\\s|$)');
            obj.className = obj.className.replace(reg, ' ');
        }
    }
</script>