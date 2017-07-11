<?= $this->Html->script(['jQuery-2.1.3.min.js','bootstrap-datetimepicker.js','jquery.infinitescroll.min.js']); ?>
<?= $this->Html->css(['bootstrap-datetimepicker.min.css']); ?>
<div class="wrap-nav-right wrap-index-page">
    <div class="order-main section">
        <div class="section-header clearfix order-manage">
            <h5 class="pull-left">
                我的订单
            </h5>
            <div class="pull-right order-number">
                订单号<span class="search"><input type="text" id="txtsearch" value="<?= $search?>" name="search" placeholder="搜索">
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
        <!-- <div class="dropdown pull-right">
            订单状态
            <a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                <span class="pull-left" id="agent" val="">全部</span>
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                <li><a href="#" onclick="status(0)">全部</a></li>
                <li><a href="#" onclick="status(1)">待处理</a></li>
                <li><a href="#" onclick="status(4)">处理中</a></li>
                <li><a href="#" onclick="status(5)">已完成</a></li>
            </ul>
        </div> -->
        <div class="dropdown pull-right">
            订单状态
            <a  href="javascript:;" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                <span class="pull-left" id="agent_t" val=""><?php if(isset($step)){echo $step['step_name'] ;}else{echo '全部';} ?></span>
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu" id="agent_two">

                <?php 
                if(isset($detail)){?>
                <li><a href="<?= $this->Url->build(['prefix' => 'console', 'controller' => 'myorder', 'action' => 'index','0',$flow['flow_id'],$start,$end,$search]); ?>">全部</a></li>
                <?php 
                foreach($detail as $value) {
                    ?>
                    <li><a href="<?= $this->Url->build(['prefix' => 'console', 'controller' => 'myorder', 'action' => 'index',$value['lft'],$flow['flow_id'],$start,$end,$search]); ?>"><?php echo $value['step_name'] ?></a></li>
                    <?php 
                }
            } ?>
        </ul>
    </div>

    <div class=" dropdown pull-right">
        订单分类
        <a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            <span class="pull-left" id="agent" val=""><?php if(isset($flow)){echo $flow['flow_name'] ;}else{echo '全部';} ?></span>
            <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
            <li><a href="<?= $this->Url->build(['prefix' => 'console', 'controller' => 'myorder', 'action' => 'index','0','0',$start,$end,$search]); ?>">全部</a></li>
            <?php 
            if(isset($template)){
                foreach($template as $value) {
                    ?>
                    <li><a href="<?= $this->Url->build(['prefix' => 'console', 'controller' => 'myorder', 'action' => 'index','0',$value['flow_id'],$start,$end,$search]); ?>"><?php echo $value['flow_name'] ?></a></li>
                    <?php 
                }
            } ?>
        </ul>
    </div>
        <!-- <div class="dropdown pull-right">
            订单分类
            <a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                <span class="pull-left" id="agent" val="">全部</span>
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                <li><a href="javascript:;" onclick="local(0,'','全部')">全部</a></li>
            </ul>
        </div> -->
            <!-- <div class="pull-right">
                订单状态
                &nbsp;&nbsp;
                <select id="order-filter">
                    <option value="0" <?php if($status == 0){echo "selected";}?>>全部</option>
                    <option value="1" <?php if($status == 1){echo "selected";}?>>提交</option>
                    <option value="2" <?php if($status == 2){echo "selected";}?>>待付款</option>
                    <option value="3" <?php if($status == 3){echo "selected";}?>>付款完成</option>
                    <option value="4" <?php if($status == 4){echo "selected";}?>>处理中</option>
                    <option value="5" <?php if($status == 5){echo "selected";}?>>已完成</option>
                </select>
            </div> -->
            
        </div>
        <div class="section-body" id="order-panel">

            <?php 
            if (isset($info['orderinfo'])) {
                foreach ($info['orderinfo'] as $key => $value) { ?>
                <div class="order-content">
                    <!-- <div class="order-button">
                </div> -->
                <p class="order-tips">
                    <?= date('Y年m月d日 H:i',$value['create_time']) ?>  | 
                    <?= $value['account']['username'] ?> |  
                    订单号：<?= $value['number']?> |
                    订单状态：<span class="text-danger"><?= $value['workflow_detail']['step_name']?></span>
                    <!-- <?php switch ($value['status']) {
                        case 1:
                        echo '<span class="text-danger">待处理</span>';
                        break;
                        case 4:
                        echo '<span class="text-warning">处理中</span>';
                        break;
                        case 5:
                        echo '<span class="text-primary">处理完成</span>';
                        break;
                        default:
                        echo '<span class="text-danger">未知状态</span>';
                        break;
                    }?> -->
                    <span class="pull-right order-click">收起<i class="icon-angle-down"></i></span></p>
                    <?php if(isset($value['goodinfo'])){?>
                    <?php foreach ($value['goodinfo'] as $list) {?>
                    <div class="clearfix order-detail">
                        <div class="order-photo pull-left">
                            <?php if(isset($list['mini_icon'])){ ?>
                            <?= $this->Html->image($list['mini_icon']); ?>
                            <?php }?>
                        </div>
                        <div class="order-name pull-left">
                            <h3><?= $list['name']?></h3>
                        </div>
                        <div class="order-info pull-left">
                            <p><?php 
                                if(isset($list['cpu'])){
                                    echo 'Cpu : '.$list['cpu'];
                                } 
                                if(isset($list['gpu'])){
                                    echo '内存 : '.$list['gpu'];
                                }
                                if(isset($list['rom'])){
                                    echo '显存 : '.$list['cpu'];
                                }?>
                            </p>
                            <?php if (isset($list['OS'])){?>
                            <p>操作系统 <?= $list['OS']?></p>
                            <?php }?>
                            <?php if (isset($list['activision'])){?>
                            <p>软件厂商 <?= $list['activision']?></p>
                            <?php }?>
                            <?php if (isset($list['version'])){?>
                            <p>软件版本 <?= $list['version']?></p>
                            <?php }?>
                            <?php if (isset($list['labs'])){?>
                            <p>机房位置 <?= $list['labs']?></p>
                            <?php }?>
                        </div>
                    </div>

                    <?php }?>
                    <?php }?>
                </div>


                <?php }
            }?>
            <div id="order-pagination">
            </div>
        </div>
    </div>
    <div id="navigation">
    <a href="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'myorder', 'action' => 'getOrderInfo','10','2',$status,$flow_id,$start,$end,$search]); ?>"></a>
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
            endDate:time
        }
        ).on('changeDate', function(){
            var end = $('#end-time').val()
            // alert(end);
            location.href="<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'myorder', 'action' =>'index',$status,$flow_id,$start]);?>/"+end+'/<?php echo $search?>';
        });
        $('#datetimepicker-start').datetimepicker({
            autoclose:true,
            minView:2,
            endDate:time
        }
        ).on('changeDate', function(){
            var start = $('#start-time').val()
            // alert(end);
            location.href="<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'myorder', 'action' =>'index',$status,$flow_id]);?>/"+start+'/<?php echo $end?>/<?php echo $search?>';
        });

    var _renderItem = function(data) {
        html = '<div class="order-content"><p class="order-tips">' + timestrap2date(data.create_time) + '  | ' + data.account.username + ' |  订单号：' + data.number + ' | 订单状态：'+'<span class="text-danger">'+data.workflow_detail.step_name+'</span>';
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
                location.href="<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'myorder', 'action' =>'index',$status,$flow_id,$start,$end]);?>/"+search;

            },
            1000);
        });
</script>