<div class="service-sidebar pull-left">
	<div class="service-sidebar-content text-left">
		<h4>产品与服务</h4>
		<ul>
            <?php if (isset($_menuCategory)&&!empty($_menuCategory)){ ?>
            
            <?php foreach ($_menuCategory as $category){ ?>
            <?php if(count($category['goods'])!=0){ ?>
            <li class="service-sidebar-major">
                <h5><?= $category['name'] ?><i class="icon-angle-down pull-right"></i></h5>
                <ul class="service-sidebar-secondary">
                   <?php foreach ($category['goods'] as $goods){ ?>
                   <?php  $url= $goods['id']; ?> 
                   <li><a href="/home/products/<?= $url ?>">
                    <?= $goods['name']?>
                </a></li>
                <?php } ?>
            </li>
            <?php } ?>
        </ul>
    </li>
    <?php } ?>
    <?php } ?>
</ul>
</div>
</div>

<script>
    
//侧边下拉
    $(".service-sidebar-major").on("click", "h5", function(){
        $(this).next("ul").slideToggle(300);
        $(this).children("i").toggleClass("icon-angle-down");
        $(this).children("i").toggleClass("icon-angle-up");
    })
    
    function resetSidebar(){
        var resetHeight = $(document).height()-$(".header").height()-$(".footer").height();
        $(".service-sidebar").height(resetHeight);
    }

    $(function(){
        resetSidebar();
    });

</script>