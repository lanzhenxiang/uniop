<?= $this->element('content_header'); ?>
<?= $this->Html->script(['controller/controller.js']); ?>
<div class="wrap-nav-right content-body" ng-app>
    <div class="container-wrap  wrap-buy">
       <a href="<?= $this->Url->build(['controller'=>'store','action'=>'index',$_id]); ?>" class="btn btn-addition">返回媒体存储列表</a>
       <div class="clearfix buy-theme ng-scope" ng-controller="storeListService">
            <div class="pull-left theme-left">
                <table>
                    <tbody>
                    <tr>
                        <td class="row-message-left">部署区位</td>
                        <td class="row-message-right">
                            <div class="ng-binding">
                                <div class="clearfix location-relative">
                                    <label for="" class="pull-left">厂商:</label>
                                    <div class="bk-form-row-cell">
                                        <ul class="clearfix city">
                                            <li ng-repeat="host in hostList" ng-class="{active:$first}" ng-click="changeHost(host)">
                                                {{host.company.name}}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="clearfix location-relative">
                                    <label for="" class="pull-left">地域:</label>
                                    <div class="bk-form-row-cell">
                                        <ul class="clearfix city" ng-repeat="host in hostList | filter: { company.name:currentCompany.name}">
                                            <li ng-repeat="area in host.area" ng-class="{active:$first}" ng-click="changeArea(area)">
                                                {{area.name}}
                                            </li>
                                        </ul>
                                        <p><i class="icon-info-sign"></i>&nbsp;不同厂商之间的产品内网不互通；订购后不支持更换服务，请谨慎选择</p>
                                   </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="row-message-left">存储产品</td>
                        <td class="row-message-right">
                            <div class="ng-binding">
                                <div class="clearfix location-relative">
                                    <label for="" class="pull-left">品牌:</label>
                                    <div class="bk-form-row-cell">
                                        <ul class="clearfix city">
                                            <li ng-repeat="s in storeType" ng-class="{active:$first}" ng-click="changeStoreType(s)">
                                                {{s.name}}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="clearfix location-relative">
                                    <label for="" class="pull-left">存储名:</label>
                                    <div class="bk-form-row-cell">
                                        <ul class="clearfix city">
                                            <li ng-repeat="ss in store" ng-class="{active:$first}" ng-click="changeStore(ss)">
                                                {{ss.store_name}}
                                            </li>
                                        </ul>
                                   </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                   <tr>
                        <td class="row-message-left">基本设置</td>
                        <td class="row-message-right">
                            <div class="ng-binding">

                                <div class="clearfix">
                                    <label for="" class="pull-left ">卷名: </label>
                                    <div class="bk-form-row-cell">
                                        <input type="text" ng-model="dname" id="txtDName"/>
                                        <span class="text-danger display-name"></span>
                                    </div>
                                </div>
                                <div class="clearfix">
                                    <label for="" class="pull-left ">卷名: </label>
                                    <div class="bk-form-row-cell">
                                        <input type="text" ng-model="ficsName" id="fics-name"/>
                                        <span class="text-danger fics-name"></span>
                                    </div>
                                </div>
                                <div class="clearfix">
                                    <label class="pull-left ">总容量（GB）:</label>
                                    <div class="bk-form-row-cell">
                                        <input type="text" ng-model="ficsGB" id="fics-gb"/>
                                        <span class="text-danger fics-gb"></span>
                                    </div>
                                </div>

                                <div class="clearfix">
                                    <label class="pull-left ">告警水位（%）:</label>
                                    <div class="bk-form-row-cell">
                                        <input type="text" ng-model="ficsWarn" ng-init="ficsWarn=80" id="fics-warn" />
                                        <span class="text-light">%</span>
                                        <span class="text-danger fics-warn"></span>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>


            <div class="pull-right theme-right">
                <div class="theme-right-mian ">
                    <p class="theme-buy">当前配置 </p>
                    <ul class="goods-detail">
                        <li>
                            <span class="deleft">厂商：</span>
                            <span class="deright">{{currentCompany.name}}</span>
                            <input type="hidden" value="{{currentCompany.name}}"  code="{{currentCompany.currentCompanyCode}}">

                        </li>
                        <li>
                            <span class="deleft">地域：</span>
                            <span class="deright">{{currentArea.name}}</span>
                            <input type="hidden" value="{{currentArea.name}}" id="area" code="{{currentAreaCode}}">
                            <input type="hidden" id="txtRegionCode" value="{{currentAreaCode}}">
                        </li>
                        <li>
                            <span class="deleft">品牌：</span>
                            <span class="deright">{{currentStoreType.name}}</span>
                            <input type="hidden" id="txtstoreType" value="{{currentStoreType.name}}" code="{{currentStoreType.type}}">
                        </li>
                        <li>
                            <span class="deleft">存储：</span>
                            <span class="deright">{{currentStore.store_name}}</span>
                            <input type="hidden" id="txtStroe" value="{{currentStore.store_name}}"  code="{{currentStore.store_code}}">
                        </li>
                        <li>
                            <span class="deleft">存储名：</span>
                            <span class="deright">{{dname}}</span>
                            <input type="hidden" id="txtFicsName" value="{{ficsName}}">

                        </li>
                        <li>
                            <span class="deleft">卷名：</span>
                            <span class="deright">{{ficsName}}</span>
                            <input type="hidden" id="txtFicsName" value="{{ficsName}}">

                        </li>
                        <li>
                            <span class="deleft">容量: </span>
                            <span class="deright">{{ficsGB}}</span>
                            <input type="hidden" id="txtFicsGB" value="{{ficsGB}}">
                        </li>
                        <li>
                            <span class="deleft">告警水位：</span>
                            <span class="deright">{{ficsWarn}}</span>
                            <input type="hidden" id="txtFicsWarn" value="{{ficsWarn}}">
                            <span class="text-light">%</span>
                        </li>
                    </ul>
                    <button class="btn btn-oriange" id="btnBuy">确认创建</button>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/jQuery-2.1.3.min.js"></script>
<script src="/js/angular.min.js"></script>
<script src="/js/controller/controller.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/jquery-ui-1.10.0.custom.min.js" type="text/javascript"></script>

<script>
//区域类别变色
$('.bk-form-row-cell').on('click',"li",function(){
	$(this).parent().children().removeClass('active');
	$(this).addClass('active');
});
$(function() {

    $('#btnBuy').on('click',function(){
        $(this).prop('disabled',true);
       addCar(false);
    });


    function addCar() {
        //获取商品配置信息
        var volName = $('#fics-name').val();
        var gb = $('#fics-gb').val();
        var warn = $('#fics-warn').val();
        var validate = true;
        if (volName == '') {
            $(".fics-name").html('请输入卷名');
            validate = false;
        }else if(!/^[A-Za-z0-9_]+$/i.test(volName)){
            $(".fics-name").html('只能输入字母、数字和下划线');
            validate = false;
        }else{
            $(".fics-name").html('');
        }
        if (!/^[1-9]+\d*$/i.test(gb)) {
            $(".fics-gb").html('请输入正整数');
            validate = false;
        } else if(gb == '') {
            $(".fics-gb").html('请输入总容量');
            validate = false;
        } else {
            $(".fics-gb").html('');
        }
        if (!/^[1-9]+\d*$/i.test(warn)) {
            $(".fics-warn").html('请输入正整数');
            validate = false;
        }else if(warn<1 || warn>100){
            $(".fics-warn").html('请输入1-100以内的正整数');
            validate = false;
        }else if (warn == '') {
            $(".fics-warn").html('请输入名称');
            validate = false;
        } else {
            $(".fics-warn").html('');
        }
        warn = gb*warn/100;
        if (validate) {
            $.ajax({
            type:"post",
            url:"<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'vpcStore', 'addstore','?'=>['id'=>$_id]]); ?>",
            async:true,
            data:{
                "store_code":$("#txtStroe").attr("code"),
                "region_code":$("#txtRegionCode").val(),
                'vol_type':$("#txtstoreType").attr("code"),
                'vol_name':$("#txtFicsName").val(),
                "total_cap":$("#txtFicsGB").val(),
                "warn_cap":$("#txtFicsWarn").val(),
                "vpcId":"<?= $_id ?>",
                "display_name":$("#txtDName").val()
            },
            success: function (data) {
                data= $.parseJSON(data);
                if(data.code=="0000"){
                    setTimeout(function() {
                            window.location.href="<?= $this->Url->build(['controller'=>'store','action'=>'index',$_id]); ?>";
                        }, 1000);
                }else{
                    alert(data.Message);
                }
            }
            });
        }else{
            $('#btnBuy').prop('disabled',false);
        }
    }
})


$(".wrap-nav-right").addClass('wrap-nav-right-left');
//新建 右边固定框
var offsetTop = $(".theme-right").offset().top;
var width = $(".buy-theme").width() * 0.24;
$(window).scroll(
    function(){
        if($(document).scrollTop() > offsetTop - 60){
            var offsetLeft = $(".theme-right").offset().left;
            $(".theme-right").css({position:"fixed",top:"60px",left:offsetLeft,width:width});
        }else{
            $(".theme-right").css("position","static");
        }
    }
    );
</script>
</body>
</html>