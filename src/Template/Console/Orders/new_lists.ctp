<?= $this->Html->script(['jQuery-2.1.3.min.js','bootstrap-datetimepicker.js','jquery.infinitescroll.min.js']); ?>
<?= $this->Html->css(['bootstrap-datetimepicker.min.css']); ?>
<div class="wrap-nav-right wrap-index-page">
    <div class="order-main section">
        <div class="section-header clearfix order-manage">
            <h5 class="pull-left">
                待审核订单
            </h5>
            <div class="pull-right order-number">
                订单号<span class="search"><input type="text" id="txtsearchpeople" value="<?= $search_people?>" name="search" placeholder="搜索">
                    <i class="icon-search"></i>
                </span>
            </div>
            <div class="pull-right order-number">
                订单号<span class="search"><input type="text" id="txtsearchnumber" value="<?= $search_number?>" name="search" placeholder="搜索">
                    <i class="icon-search"></i>
                </span>
            </div>
            <div class="pull-right order-number input-append date" id="datetimepicker-end" data-date-format="yyyy-mm-dd">
                结束时间
                <input size="16" type="text" name="time" id="end-time" value="<?php if($end !=0 ) {echo $end;}?>" readonly>
                <span class="add-on"><i class="icon-th"></i></span>
            </div>
            <div class="pull-right order-number input-append date" id="datetimepicker-start"  data-date-format="yyyy-mm-dd">
                开始时间
                <input size="16" type="text" name="time" id="start-time" value="<?php if($start !=0){ echo $start;} ?>" readonly>
                <span class="add-on"><i class="icon-th"></i></span>
            </div>
            
        </div>
        <div class="section-body" id="order-panel">
            <table>

                <tr>
                    <th></th>
                    <th>订单号</th>
                    <th>商品规格配置</th>
                    <th>计费方式</th>
                    <th>购买量</th>
                    <th>单台原价</th>
                    <th>成交单价</th>
                    <th>操作</th>
                    <th>申请人</th>
                    <th>租户</th>
                    <th>提交时间</th>

                </tr>
                <?php foreach($info as $k => $v){?>
                <tr>
                    <td></td>
                    <td><?php $v['number']?></td>
                    <td>  <?php switch (variable) {
                        case 'value':
                            # code...
                            break;
                        
                        default:
                            # code...
                            break;
                    }


                     $v['orders_goods'][0]['instance_conf']?></td>
                    <td><?php debug($v)?></td>
                </tr>
                <?php }?>
                
            </table>
                
            
            <div id="order-pagination">
            </div>
        </div>
    </div>
    <div id="navigation">
    <a href="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'Orders', 'action' => 'getOrderInfo','10','2',$start,$end,$search_people,$search_number]); ?>"></a>
    </div>
</div>

</div>

<script>

    var myDate = new Date();
        var year = myDate.getFullYear();
        var month =myDate.getMonth()+1;
        var day =  myDate.getDate();
        var time =year+'-'+month+'-'+day;
        $('#datetimepicker-end').datetimepicker({
            autoclose:true,
            minView:2,
            <?php if(isset($start) && !empty($start)){?>
            startDate:'<?= $start?>',
            <?php }?>
            endDate:time
        }
        ).on('changeDate', function(){
            var end = $('#end-time').val()
            // alert(end);
            location.href="<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'Orders', 'action' =>'lists',$start]);?>/"+end+'/<?php echo $search?>';
        });
        $('#datetimepicker-start').datetimepicker({
            autoclose:true,
            minView:2,
            <?php if(isset($end) && !empty($end)){?>
            endDate:'<?= $end?>',
            <?php }else{?>
            endDate:time
            <?php }?>
        }
        ).on('changeDate', function(){
            var start = $('#start-time').val()
            // alert(end);
            location.href="<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'Orders', 'action' =>'lists']);?>/"+start+'/<?php echo $end?>/<?php echo $search?>';
        });

    var _renderItem = function(data) {
        html = '<div class="order-content"><div class="order-button"><a class="btn btn-warning change-images" href="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'Orders', 'action' => 'detail',]);?>/'+data.id+'">开始审核</a></div>';
        html += '<p class="order-tips">' + timestrap2date(data.create_time) + '  | ' + data.account.username + ' |  订单号：' + data.number;
        $.each(data.goodinfo,function(i,n){
            html += ' <div class="clearfix order-detail"><div class="order-photo pull-left"><img style="width:125px" src="/images/' + n.mini_icon + '"); ?></div><div class="order-name pull-left"> <h3>' + n.name + '</h3></div><div class="order-info pull-left">';
        if(n.cpu){
            html += '<p>Cpu :' + n.cpu + '内存 :' + n.rom + '显存 :' + n.gpu + '</p><p>操作系统' + n.OS + '</p><p>软件厂商' + n.activision + '</p><p>软件版本' + n.version + '</p><p>机房位置' + n.labs + '</p>';
        }
        html += '</div></div>';
    })
        html +='</div>';
        return html;
    }
    $("#order-panel").infinitescroll({
        navSelector : "#navigation",
        nextSelector : "#navigation a",
        itemSelector : "#order-pagination",
        debug : true,
        dataType : "json",
        appendCallback  : false
    },function(response){
        var data = response.orderinfo;
        $content = $('#order-pagination');

        $.each(data,function(i,n){
            var item = $(_renderItem(n));
            $content.append(item);
        });
    });

    function timestrap2date(value){
        var now = new Date(parseInt(value) * 1000);
        return now.toLocaleString();
    }
    $(".section-body").on('click','.order-click',function(){
            if($(this).children().hasClass("icon-angle-down")){
                $(this).parent(".order-tips").nextAll().slideUp();
                $(this).html('展开<i class="icon-angle-up"></i>');
            }else{
                $(this).parent(".order-tips").nextAll().slideDown();
                $(this).html('收起<i class="icon-angle-down"></i>');
            }

        })
    //搜索绑定
    $("#txtsearch").on('keyup',
        function() {
            if (timer != null) {
                clearTimeout(timer);
            }
            var timer = setTimeout(function() {
                var search = $('#txtsearch').val();
                location.href="<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'Orders', 'action' =>'lists',$start,$end]);?>/"+search;
            },
            1000);
        });
</script>