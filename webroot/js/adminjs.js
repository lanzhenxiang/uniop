/**
 * Created by zhaodanru on 2015/11/26.
 */

function tentionHide(content, state) {
    $("#maindiv-alert").empty();
    var html = "";
    if (state == 0) {
        html += '<div class="point-host-startup "><i></i>' + content + '</div>';
        $("#maindiv-alert").append(html);
        $(".point-host-startup ").slideUp(3000);
    } else {
        html += '<div class="point-host-startup point-host-startdown"><i></i>' + content + '</div>';
        $("#maindiv-alert").append(html);
        $(".point-host-startdown").slideUp(5000);
    }
}

//侧边导航
$(".nav-total-open").on('click', function(){
    $(".main").toggleClass("wrap-nav-left-click");
    if($.cookie("sidebar")=="open"){
        $.cookie("sidebar","close");
    }else{
        $.cookie("sidebar","open");
    }
});