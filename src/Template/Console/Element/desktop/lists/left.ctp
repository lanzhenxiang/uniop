<div class="wrap-nav-center">
    <div class="desk-left">
        <div class="title"><span>云桌面</span><i class="icon-angle-down"></i></div>
        <ul class="">
<!--第一级-->
            <?php if(isset($first)){?>
            <?php foreach($first as $key => $value){?>
            <?php if (in_array($value['popedom_code'], $this->Session->read('Auth.User.popedomname'))) { ?>

            <li class="shrink-ud-box" <?php if(isset($active_group)&&($active_group==$value['name'])){echo 'class="active"';} ?>>
            <a href="javascript:void(0)" class="shrink-down-up"><span><?php echo $value['label'];?></span><i class="icon-angle-up"></i></a>
            <ol class="desk-sec-menu">
                <!--第二级-->
                <?php if(isset($second)){?>
                <?php foreach($second as $s_key => $s_value){?>
                <?php if($s_value['parent_id']==$value['id']){?>
                <?php if (in_array($s_value['popedom_code'], $this->Session->read('Auth.User.popedomname'))) {?>
                <li class="<?php if(isset($active_action)&&($active_action==$s_value['name'])){echo 'active';}?>"><a  href="/<?php echo $s_value['url']; ?>"><?php echo $s_value['label'];?></a></li>
                <?php }?>
                <?php }?>
                <?php }?>
                <?php }?>

            </ol>
            </li>
            <?php }?>
            <?php }?>
            <?php }?>
        </ul>
        <span class="iconpic iconpic-spread"></span>
    </div>
</div>
<script type="text/javascript">
$(function(){
    $("li.active").parent(".desk-sec-menu").prev("a").find("i").attr("class","icon-angle-down");
    $("li.active").parent(".desk-sec-menu").slideDown();
         var desktop = document.getElementById('desktop');
        desktop.className='active';
        var excp = document.getElementById('excp');
        $("#network").removeClass('active')
})

</script>
