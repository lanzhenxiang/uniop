<!-- <div class="wrap-nav-right" style="padding:0 10px; min-width:1100px;margin: 0 0 0 200px;"> -->
<?= $this->Html->script(['jQuery-2.1.3.min.js','bootstrap-datetimepicker.js','jquery.infinitescroll.min.js']); ?>
<?= $this->Html->css(['bootstrap-datetimepicker.min.css']); ?>
<div class="wrap-nav-right wrap-index-page">

    <!-- <div class="index-total section">
        <div class="section-header">
            <h5>
                资源配额使用情况
                <div class="pull-right">
                    <i class="icon-refresh"></i> 
                    <i class="icon-chevron-down"></i>
                </div>
            </h5>
        </div>
        <div class="total-chart section-body">
            <ul class="clearfix">
                <li>
                    <div class="total-chart-sharp">
                        <div class="total-chart-rate"><?php 
                            if(!empty($query['cpu_bugedt'])&&!empty($data['cpu_used']))
                            { 
                                $i=$data['cpu_used']/$query['cpu_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?>
                        </div>
                        <canvas id="cpu"></canvas>
                    </div>
                    <h1>Cpu</h1>
                    <p>可用<?php if(!empty($query['cpu_bugedt'])&&!empty($data['cpu_used']))
                        { 
                            echo $query['cpu_bugedt']-$data['cpu_used'];
                        }else if(!empty($query['cpu_bugedt'])&&empty($data['cpu_used']))
                        {
                            echo $query['cpu_bugedt'];
                        }else{
                            echo 0;
                        } ?>核，已使用<?php if(!empty($data['cpu_used']))
                        { 
                            echo $data['cpu_used'];
                        }else{
                            echo 0;
                        }?>核
                    </p>
                </li>
                <li>
                    <div class="total-chart-sharp">
                        <div class="total-chart-rate"><?php if(!empty($query['memory_buget'])&&!empty($data['memory_used']))
                            { 
                                $i=$data['memory_used']/$query['memory_buget']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?>
                        </div>
                        <canvas id="rom"></canvas>
                    </div>
                    <h1>内存</h1>
                    <p>可用<?php if(!empty($query['memory_buget'])&&!empty($data['memory_used']))
                        { 
                            echo $query['memory_buget']-$data['memory_used'];
                        }elseif (!empty($query['memory_buget'])&&empty($data['memory_used'])) {
                            echo $query['memory_buget'];
                        }else{
                            echo "0";
                        }?>GB，已使用<?php if(!empty($data['memory_used']))
                        { 
                            echo $data['memory_used'];
                        }else{
                            echo "0";
                        }?>GB
                    </p>
                </li>
                <li>
                    <div class="total-chart-sharp">
                        <div class="total-chart-rate"><?php if(!empty($query['gpu_bugedt'])&&!empty($data['gpu_used']))
                            { 
                                $i=$data['gpu_used']/$query['gpu_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?>
                        </div>
                        <canvas id="gpu"></canvas>
                    </div>
                    <h1>Gpu</h1>
                    <p>可用<?php if(!empty($query['gpu_bugedt'])&&!empty($data['gpu_used']))
                        { 
                            echo $query['gpu_bugedt']-$data['gpu_used'];
                        }elseif (!empty($query['gpu_bugedt'])&&empty($data['gpu_used'])) {
                            echo $query['gpu_bugedt'];
                        }else{
                            echo "0";
                        }?>MB，已使用<?php if(!empty($data['gpu_used']))
                        { 
                            echo $data['gpu_used'];
                        }else{
                            echo "0";
                        }?>MB
                    </p>
                </li>
                <li>
                    <div class="total-chart-sharp">
                        <div class="total-chart-rate"><?php if(!empty($query['disks_bugedt'])&&!empty($data['disks_used']))
                            { 
                                $i=$data['disks_used']/$query['disks_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?>
                        </div>
                        <canvas id="disk"></canvas>
                    </div>
                    <h1>硬盘</h1>
                    <p>可用<?php if(!empty($query['disks_bugedt'])&&!empty($data['disks_used']))
                        { 
                            echo $query['disks_bugedt']-$data['disks_used'];
                        }elseif (!empty($query['disks_bugedt'])&&empty($data['disks_used'])) {
                            echo $query['disks_bugedt'];
                        }else{
                            echo "0";
                        }?>GB，已使用<?php if(!empty($data['disks_used']))
                        { 
                            echo $data['disks_used'];
                        }else{
                            echo "0";
                        }?>GB
                    </p>
                </li>
            </ul>
        </div>
    </div> -->
    <div class="order-main section">
        <div class="section-header clearfix order-manage">
            <h5 class="pull-left">
                订单管理
                <!-- <div class="pull-right">
                    <i class="icon-refresh"></i> 
                    <i class="icon-chevron-down"></i>
                </div> -->
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
        <div class="dropdown pull-right">
            订单状态
            <a  href="javascript:;" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                <span class="pull-left" id="agent_t" val=""><?php if(isset($step)){echo $step['step_name'] ;}else{echo '全部';} ?></span>
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu" id="agent_two">
            
            <?php 
                if(isset($detail)){?>
                    <li><a href="<?= $this->Url->build(['prefix' => 'console', 'controller' => 'Home', 'action' => 'order','0',$flow['flow_id'],$start,$end,$search]); ?>">全部</a></li>
                    <?php 
                    foreach($detail as $value) {
                        ?>
                        <li><a href="<?= $this->Url->build(['prefix' => 'console', 'controller' => 'Home', 'action' => 'order',$value['lft'],$flow['flow_id'],$start,$end,$search]); ?>"><?php echo $value['step_name'] ?></a></li>
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
                <li><a href="<?= $this->Url->build(['prefix' => 'console', 'controller' => 'Home', 'action' => 'order','0','0',$start,$end,$search]); ?>">全部</a></li>
                <?php 
                if(isset($template)){
                    foreach($template as $value) {
                        ?>
                        <li><a href="<?= $this->Url->build(['prefix' => 'console', 'controller' => 'Home', 'action' => 'order','0',$value['flow_id'],$start,$end,$search]); ?>"><?php echo $value['flow_name'] ?></a></li>
                        <?php 
                    }
                } ?>
            </ul>
        </div>
            
        </div>
        <div class="section-body" id="order-panel">

            <?php 
            if (isset($info['orderinfo'])) {
                foreach ($info['orderinfo'] as $key => $value) {?>
                <div class="order-content">
                    <div class="order-button">
                        <a class="btn btn-warning change-images" href="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'Orders', 'action' => 'detail',$value['id']]); ?>">查看详情</a>
                    </div>
                    <?php if (in_array('cmop_global_sys_admin', $this->Session->read('Auth.User.popedomname'))||in_array('cmop_global_tenant_admin', $this->Session->read('Auth.User.popedomname'))) {?>
                    <?php } ?>
                    <p class="order-tips">
                        <?= date('Y年m月d日 H:i',$value['create_time']) ?>  | 
                        <?= $value['account']['username'] ?> |  
                        订单号：<?= $value['number']?> |
                        订单状态：<span class="text-danger"><?=$value['status']!=-1?$value['workflow_detail']['step_name']:'结束'?></span>
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
            <a href="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'Home', 'action' => 'getOrderInfo','10','2',$status,$flow_id,$start,$end,$search]); ?>"></a>
        </div>
    </div>
    <script>
        // var cpuData = [
        // {
        //     value: <?php if(!empty($data['cpu_used']))
        //     { 
        //         $i=$data['cpu_used'];
        //         echo $i;
        //     }else{
        //         echo "0";
        //     }?>,
        //     color:"#f1b048"
        // },
        // {
        //     value: <?php 
        //     if(!empty($query['cpu_bugedt']) && !empty($data['cpu_used']))
        //     { 
        //         $i=$query['cpu_bugedt']-$data['cpu_used'];
        //         echo $i;
        //     }else{
        //         echo 0;
        //     }?>,
        //     color: "transparent"
        // }
        // ];

        // var romData = [
        // {
        //     value: <?php 
        //     if(!empty($data['memory_used']))
        //     { 
        //         $i=$data['memory_used'];
        //         echo $i;
        //     }else{
        //         echo "0";
        //     }?>,
        //     color:"#2e8bcc"
        // },
        // {
        //     value: <?php 
        //     if(!empty($query['memory_buget'])&&!empty($data['memory_used']))
        //     { 
        //         $i=$query['memory_buget']-$data['memory_used'];
        //         echo $i;
        //     }else{
        //         echo "0";
        //     }?>,
        //     color: "transparent"
        // }
        // ];

        // var gpuData = [
        // {
        //     value: <?php 
        //     if(!empty($data['gpu_used']))
        //     { 
        //         $i=$data['gpu_used'];
        //         echo $i;
        //     }else{
        //         echo "0";
        //     }?>,
        //     color:"#a4ca57"
        // },
        // {
        //     value: <?php 
        //     if(!empty($query['gpu_bugedt'])&&!empty($data['gpu_used']))
        //     { 
        //         $i=$query['gpu_bugedt']-$data['gpu_used'];
        //         echo $i;
        //     }else{
        //         echo "0";
        //     }?>,
        //     color: "transparent"
        // }
        // ];

        // var diskData = [
        // {
        //     value: <?php 
        //     if(!empty($data['disks_used']))
        //     { 
        //         $i=$data['disks_used'];
        //         echo $i;
        //     }else{
        //         echo "0";
        //     }?>,
        //     color:"#23bab5"
        // },
        // {
        //     value: <?php 
        //     if(!empty($query['disks_bugedt'])&&!empty($data['disks_used']))
        //     { 
        //         $i=$query['disks_bugedt']-$data['disks_used'];
        //         echo $i;
        //     }else{
        //         echo "0";
        //     }?>,
        //     color: "transparent"
        // }
        // ];

        // window.onload = function(){
        //     var cpu = document.getElementById("cpu").getContext("2d");
        //     var rom = document.getElementById("rom").getContext("2d");
        //     var gpu = document.getElementById("gpu").getContext("2d");
        //     var disk = document.getElementById("disk").getContext("2d");
        //     new Chart(cpu).Doughnut(cpuData, 
        //     {
        //         percentageInnerCutout : 80,
        //         animationEasing: "easeInOutQuad",
        //         animationSteps : 50,
        //         segmentShowStroke : false,
        //         showTooltips: false
        //     });
        //     new Chart(rom).Doughnut(romData, 
        //     {
        //         percentageInnerCutout : 80,
        //         animationEasing: "easeInOutQuad",
        //         animationSteps : 50,
        //         segmentShowStroke : false,
        //         showTooltips: false
        //     });
        //     new Chart(gpu).Doughnut(gpuData, 
        //     {
        //         percentageInnerCutout : 80,
        //         animationEasing: "easeInOutQuad",
        //         animationSteps : 50,
        //         segmentShowStroke : false,
        //         showTooltips: false
        //     });
        //     new Chart(disk).Doughnut(diskData, 
        //     {
        //         percentageInnerCutout : 80,
        //         animationEasing: "easeInOutQuad",
        //         animationSteps : 50,
        //         segmentShowStroke : false,
        //         showTooltips: false
        //     });
        // };


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
            location.href="<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'Home', 'action' =>'order',$status,$flow_id,$start]);?>/"+end+'/<?php echo $search?>';
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
            location.href="<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'Home', 'action' =>'order',$status,$flow_id]);?>/"+start+'/<?php echo $end?>/<?php echo $search?>';
        });

        var _renderItem = function(data) {
            html = '<div class="order-content">';
            html += '<div class="order-button"><a class="btn btn-warning change-images" href="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'Orders', 'action' => 'detail']);?>/'+data.id+'">查看详情</a></div>'
            html +='<p class="order-tips">' + timestrap2date(data.create_time) + '  | ' + data.account.username + ' |  订单号：' + data.number + ' | 订单状态：'+'<span class="text-danger">';
            if(data.workflow_detail){
                html += data.workflow_detail.step_name;
            }
            html += '</span>';
            $.each(data.goodinfo,function(i,n){
                html += ' <div class="clearfix order-detail"><div class="order-photo pull-left">';
                if(n.mini_icon){
                    html+='<img style="width:125px" src="/images/' + n.mini_icon + '"); ?>'
                }
                html += '</div><div class="order-name pull-left"> <h3>' + n.name + '</h3></div><div class="order-info pull-left">';
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
        function delivery(status,id){
            var search = $('#txtsearch').val();
            $.ajax({
                type: "post",
                url: "<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'Home', 'action' =>'delivery']);?>/"+id+"/"+status,
                success: function(data) {
                    data = $.parseJSON(data);
                    // console.log(data);
                    if (data.code != 0) {
                        alert(data.msg);
                    }else{
                        alert(data.msg);
                        location.href='<?php echo $this->Url->build(array('controller'=>'Home','action'=>'order'));?>/'+search;
                    }
                }
            })
        }

        //搜索绑定
        $("#txtsearch").on('keyup',
            function() {
                if (timer != null) {
                    clearTimeout(timer);
                }
                var timer = setTimeout(function() {
                    var search = $('#txtsearch').val();
                    location.href="<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'Home', 'action' =>'order',$status,$flow_id,$start,$end]);?>/"+search;

                },
                1000);
            });
    </script>