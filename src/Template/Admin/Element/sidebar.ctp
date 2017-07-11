<div class="sidebar pull-left text-center">
    <div class="nav-total-open"><i class="icon-align-justify"></i></div>



    <?php if($this->request->session()->read("Auth.User.popedomname")): ?>
        <?php $popedomname = $this->request->session()->read("Auth.User.popedomname");?>
        <ul>
        <?php foreach($_admin_menu as $rootMenu):?>
            <?php if(in_array($rootMenu['popedom_code'],$popedomname)): ?>
            <li class="sidebar-major">
                <h5>
                    <i class="<?=$rootMenu['icon']?>"></i><span><?=$rootMenu['label']?></span>
                    <i class="icon-angle-up"></i>
                </h5>
                <ul class="sidebar-secondary" style="display: none;">
                    <?php foreach($rootMenu['children'] as $subMenu):?>
                        <?php if(in_array($rootMenu['popedom_code'],$popedomname)): ?>
                            <!--<li class="<?php echo  strtolower($_request_params['action'])==$subMenu['name']?'active':'' ?>" >-->
<?php $menu_arr=explode('/',$subMenu['url']); $request=$this->request->params;?>
                    <!--一般情况-->
                    <li class="<?php if(!in_array($request['controller'],array('Goods'))){
                        if($menu_arr[2]==$request['controller']){
                            if(isset($menu_arr[3])){
                                if($menu_arr[3]==$request['action']){
                                    echo 'active';
                                }
                            }else{
                                echo 'active';
                            }
                        }
                    //特殊情况
                    }else{
                        if($menu_arr[2]=='Goods'&&$menu_arr[3]=='index'&&in_array($request['action'],array('index','add','upexcel','edit'))&&$request['controller']=='Goods'){
                            echo 'active';
                        }else if($menu_arr[2]=='Goods'&&$menu_arr[3]=='index-new'&&in_array($request['action'],array('indexNew','addGoods','editDatail','selectVersion'))&&$request['controller']=='Goods'){
                            echo 'active';
                        }else if($menu_arr[2]=='Goods'&&$menu_arr[3]=='version'&&in_array($request['action'],array('version','price','addVersion'))&&$request['controller']=='Goods'){
                            echo 'active';
                        }
                    }
                    ?>" >
                            <a href="<?=$subMenu['url'] ?>">
                                <i class="<?=$subMenu['icon']?>"></i>&nbsp;&nbsp;<?=$subMenu['label']?>
                            </a>
                            </li>
                        <?php endif;?>
                    <?php endforeach;?>
                </ul>
            </li>
            <?php endif;?>
        <?php endforeach;?>
        </ul>
    <?php endif;?>
    <!--新旧版分界线---->
</div>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>
<script>

$(function(){

//侧边栏
 $(".sidebar-major").on("click", "h5", function(){

        $(this).next("ul").slideToggle();
        $(this).parent('li').siblings().children("ul").slideUp();
        $(this).parent('li').siblings().children("h5").find('.icon-angle-up').removeClass("icon-angle-down");
        $(this).children("i").eq(1).toggleClass("icon-angle-down");
        var height = $(this).parent().height()
        if( height<40){
            var index = $(this).parent().index();
            $.cookie("cookieName",index, { path: "/"});
        }else{
            $.cookie("cookieName",10, { path: "/"});
        }
  })
        $(".sidebar-major").eq($.cookie("cookieName")).children('ul').show();
        $(".sidebar-major").eq($.cookie("cookieName")).find('.icon-angle-up').addClass("icon-angle-down");
})

</script>

