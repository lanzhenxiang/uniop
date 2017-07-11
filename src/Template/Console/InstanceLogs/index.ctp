<?= $this->Html->script(['jQuery-2.1.3.min.js','bootstrap-datetimepicker.js','jquery.infinitescroll.min.js']); ?>
<?= $this->Html->css(['bootstrap-datetimepicker.min.css']); ?>
<div class="wrap-nav-right wrap-index-page">
    <div class="order-main section">
        <div class="section-header clearfix order-manage">
            <h5 class="pull-left">
                操作记录
            </h5>
            
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
            <div class="pull-right order-number">
                <div class="dropdown">
                  人员
                  <a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <span class="pull-left" id="agent" val=""><?= $user_name?></span>
                    <span class="caret"></span>
                  </a>
                  <ul class="dropdown-menu">
                    <li><a href="<?= $this->Url->build(['prefix' => 'console', 'controller' => 'instance_logs', 'action' => 'index','0',$start,$end]);?>" >全部</a></li>
                    <?php
                    if(isset($accounts_data)){
                      foreach($accounts_data as $value) { ?>
                        <li><a href="<?= $this->Url->build(['prefix' => 'console', 'controller' => 'instance_logs', 'action' => 'index',$value['id'],$start,$end]);?>" ><?= $value['username'] ?></a></li>
                        <?php }
                    } ?>
                  </ul>
                </div>
            </div>
        </div>
        <div class="section-body" id="order-panel">
            <?php if(!empty($instance_logs_data)){?>
                <?php foreach($instance_logs_data as $_log_key => $_log_data){?>
                    <div class="operation-note clearfix">
                        <div class="operation-user pull-left">
                            <div class="operation-photo pull-left">
                                <?php if(!empty($_log_data['account']['image'])){ $image = $_log_data['account']['image'];}else{$image = '';}
                                if(!empty($image)){
                                  // echo $this->html->image($image,['width'=>'50px']);
                                  echo '<img src="/'.$image.'" alt="" width="50px;">';            
                                }else{
                                    // echo $this->html->image('user-photo.png',['width'=>'50px']);
                                  echo '<img src="/images/user-photo.png" alt="" width="50px;">';
                                }
                                ?>
                            </div>
                            <div class="operation-info pull-left">
                                <h5><?= $_log_data['account']['username']?></h5>
                                <h6 class="text-light"><?= date('Y年m月d日 H:i',$_log_data['create_time']) ?></h6>
                            </div>
                        </div>
                        <div class="operation-text pull-left">
                        <?php if(!empty($_log_data['device_code'])){?>
                            <p>
                                设备CODE ：<span class="text-primary"><?= $_log_data['device_code']?></span>
                            </p>
                            <?php }?>
                            <?php if(!empty($_log_data['device_name'])){?>
                            <p style="margin-bottom:15px;">
                                设备名 : <span class="text-primary"><?= $_log_data['device_name']?></span>
                            </p> 
                            <?php }?>
                            <p>
                                <?= $_log_data['device_event']?>
                            </p>
                        </div>
                    </div>  
                <?php }?>
            <?php }?>
            <div id="order-pagination" >
            </div>
        </div>
    </div>
    <div id="navigation">
        <a href="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'instance_logs', 'action' => 'logsData','10','2',$uid,$start,$end]); ?>"></a>
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
            location.href="<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'instance_logs', 'action' =>'index',$uid,$start]);?>/"+end;
        });
        $('#datetimepicker-start').datetimepicker({
            autoclose:true,
            minView:2,
            endDate:time
        }
        ).on('changeDate', function(){
            var start = $('#start-time').val()
            // alert(end);
            location.href="<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'instance_logs', 'action' =>'index',$uid]);?>/"+start+"/<?php echo $end;?>";
        });
    

    var _renderItem = function(data) {
        
        html = ' <div class="operation-note clearfix"> <div class="operation-user pull-left"> <div class="operation-photo pull-left"> ';
        if(data.account.image == '' ||data.account.image == null){
            html += '<img src="/images/user-photo.png" alt="" width="50px;">'
        }else{
            html += '<img src="/'+data.account.image+'" alt="" width="50px;">'
        }
        html += ' </div>';
        html +=' <div class="operation-info pull-left"><h5>'+data.account.username+'</h5><h6 class="text-light">'+timestrap2date(data.create_time)+'</h6></div></div>';
        html +=' <div class="operation-text pull-left"><p>设备CODE ：<span class="text-primary">'+data.device_code+'</span></p>';
        html +=' <p style="margin-bottom:15px;">设备名 : <span class="text-primary">'+data.device_name+'</span></p><p>'+data.device_event+'</p></div></div>';
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
        var data = response;
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

    
   
</script>