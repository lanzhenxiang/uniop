$(function(){
	
//区域类别变色
$('.bk-form-row-cell').on('click',"li",function(){
	$(this).parent().children().removeClass('active');
	$(this).addClass('active');
});
//点击其他地方当前显示原始隐藏
 $('.buy-theme').on("click",function(e){
	var target = $(e.target);
	if(target.closest(".con-help").length == 0 && target.closest(".con-help-need i").length == 0){
	    $(".con-help-need i").hide();
	}
	if(target.closest(".nomal-disk .assign .auto").length == 0 ){
	    $(".nomal-disk .assign ul").hide();
	}
	if(target.closest(".bk-select-group .selected").length == 0 ){
	    $(".bk-select-group ul").hide();
	}
});
 
//弹出框疑问点击
$('.con-help').on('click', function(){

	if($(this).next().css('display') == 'none'){
		$('.con-help').next().hide();
		$(this).next().show();
	}else{
		
		$(this).next().hide();
	}

});
//数据盘
$('.bk-form-row-cell').on('click','.auto',function(){
	$('.nomal-disk .assign ul').hide();
	$(this).next().show();
	$(this).next().children().on('click',function(){
		$(this).parent().hide();
		$(this).parent().prev().html($(this).html());
	});
});
//镜像选择版本
$('.bk-select-group .selected').on('click', function(){
	if($(this).children('ul').css('display') == 'block'){
		$(this).children('ul').hide();
	}else{
		$('.bk-select-group .selected').children('ul').hide();
		$(this).children('ul').show();
	}
});
$('.bk-select-group ul li').on('click', function(){
	$(this).parent().prev().prev().html($(this).html());
});
//搜缩框
$('.wrap-nav-center .title').on('click',
function() {
    $(this).children("i").toggleClass("icon-angle-down");
    $(this).children("i").toggleClass("icon-angle-up");
    $(this).next("ul").slideToggle(300);
});
//侧边栏
 $(".main-left").on("click", "p", function(){

  	    $(this).next("ul").slideToggle();
  	    $(this).parent('.main-left').siblings().children("ul").slideUp();
  	     $(this).parent('.main-left').siblings().children("p").find('.icon-angle-up').removeClass("icon-angle-down");
        $(this).children("i").toggleClass("icon-angle-down");
        if( $(this).children().hasClass('icon-angle-down')){
        	var index = $(this).parent().index();
			$.cookie("cookieName",index, { path: "/"}); 
        }else{
        	$.cookie("cookieName",10, { path: "/"});
        }
  })
	  	$(".main-left").eq($.cookie("cookieName")).children('ul').show();
	  	$(".main-left").eq($.cookie("cookieName")).find('.icon-angle-up').addClass("icon-angle-down");


$(".wrap-nav .total li").on('click',
function() {
    $(this).parent().children().removeClass('active');
    $(this).addClass('active');
});
 //侧边栏
function init(){
        if($.cookie("sidebar")==null){
            $.cookie("sidebar","open",{path:'/'});
        }
        switch($.cookie("sidebar")){
            case "close":{
                $(".wrap-nav").addClass("wrap-nav-left-click");
                break;
            }
            default:{}
        }

        if(typeof($.cookie("hostsSidebar")) == "undefined" || $.cookie("hostsSidebar") == null){
            $.cookie("hostsSidebar","open",{path:'/'});
        }else if($.cookie("hostsSidebar")=="close"){
            $(".wrap-nav-center").addClass("wrap-nav-center-left");
            $(".wrap-nav-right").addClass("wrap-nav-right-left");
        }
    }

    init();
    //侧边栏
    $(".nav-total-open").on('click', function(){
    	  $(".wrap-nav").toggleClass("wrap-nav-left-click");
          if($.cookie("sidebar")=="open"){
            $.cookie("sidebar","close");
          }else{
            $.cookie("sidebar","open"); 
          }
    });
    

    $(".iconpic-spread").on('click', function(){
    	if($.cookie("hostsSidebar")=="open"){
            $.cookie("hostsSidebar","close",{path:'/'});
          }else{
            $.cookie("hostsSidebar","open",{path:'/'}); 
          }
        $(".wrap-nav-center").toggleClass("wrap-nav-center-left");
        $(".wrap-nav-right").toggleClass("wrap-nav-right-left");
    });
    
})

//公共收缩导航效果 parent:.shrink-ud-box  click:.shrink-down-up
$(".shrink-down-up").on('click',function(){
	var prev=$(this).next();
	var othBox=$(this).parent(".shrink-ud-box").siblings(".shrink-ud-box").find(".shrink-down-up");
	console.log(prev)
	if(prev.css("display")=="none"){
		$(this).find("i").attr("class","icon-angle-down");
		prev.slideDown();
		othBox.next().slideUp();
		othBox.find("i").attr("class","icon-angle-up");
	}else {
		prev.slideUp();
		$(this).find("i").attr("class","icon-angle-up")
	}
})