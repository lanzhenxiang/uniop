<?= $this->Html->script(['bootstrap-datetimepicker.js','jquery.infinitescroll.min.js']); ?>
<?= $this->Html->css(['bootstrap-datetimepicker.min.css','adminlog.css']); ?>
<div class="wrap-nav-right wrap-index-page">
    <div class="order-main section">
        <div class="section-header clearfix order-manage" >
            <h5 class="pull-left" style="font-size: 24px;font-weight: bold;">
                运营管理中心日志
            </h5>
        </div>
        <div class="section-header clearfix order-manage">
            <div class="pull-left">
             <a  href="javascript:void(0);"  onclick="window.location.reload()" class="btn btn-addition"><i
        class="icon-refresh"></i>&nbsp;&nbsp;刷新</a>
            </div>
            <div class="pull-right order-number">
                <span class="search">
                    <input type="text" id="searchtext" name="search" placeholder="搜索登陆名">
                    <i id="search" class="icon-search"></i>
                </span>
            </div>
            <div class="pull-right order-number input-append date" id="datetimepicker-end" data-date-format="yyyy-mm-dd">
                结束时间
                <input size="16" type="text" name="time" id="end-time" value="<?php if($end !='end' ) {echo $end;}?>" readonly>
                <span class="add-on"><i class="icon-th"></i></span>
            </div>
            <div class="pull-right order-number input-append date" id="datetimepicker-start"  data-date-format="yyyy-mm-dd">
                开始时间
                <input size="16" type="text" name="time" id="start-time" value="<?php if($start !='start'){ echo $start;} ?>" readonly>
                <span class="add-on"><i class="icon-th"></i></span>
            </div>
            <div class="pull-right order-number">
                <div class="dropdown">
                   租户
                  <a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <span class="pull-left" id="department" val="<?=$department['id']?>"> <?= $department['name']?> </span>
                    <span class="caret"></span>
                  </a>
                  <ul class="dropdown-menu">
                    <li><a href="<?= $this->Url->build(['prefix' => 'admin', 'controller' => 'log', 'action' => 'adminlog',-1,$start,$end,$loginname]);?>" >全部</a></li>
                    <?php
                    if(isset($departments)){
                      foreach($departments as $value) { ?>
                        <li><a href="<?= $this->Url->build(['prefix' => 'admin', 'controller' => 'log', 'action' => 'adminlog',$value['id'],$start,$end,$loginname]);?>" ><?= $value['name'] ?></a></li>
                        <?php }
                    } ?>
                  </ul>
                </div>
            </div>
        </div>
        <div class="section-body" id="order-panel">
            <?php if(!empty($data) && $data->count() > 0){?>
                <?php foreach($data as $_log_key => $_log_data){?>
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
                        <?php if(!empty($_log_data['ui_name'])){?>
                            <p>
                                操作界面 ：<span class="text-primary"><?= $_log_data['ui_name']?></span>
                            </p>
                            <?php }?>
                            <?php if(!empty($_log_data['user_event'])){?>
                            <p style="margin-bottom:15px;">
                                操作日志 : <span class="text-primary"><?= $_log_data['user_event']?></span>
                            </p> 
                            <?php }?>
                            <p>
                                <?= $_log_data['device_event']?>
                            </p>
                        </div>
                    </div>  
                <?php }?>
            <?php }else{?>
            <div class="operation-note clearfix">
                没有记录
            </div>
            <?php }?>
            <div id="order-pagination" >
            </div>
        </div>
    </div>
    <div class="content-pagination clearfix">
        <nav class="pull-right">
            <ul class="pagination">
                <?php echo $this->Paginator->first('<<');?>
                <?php echo $this->Paginator->numbers();?>
                <?php echo $this->Paginator->last('>>');?>
            </ul>
        </nav>
    </div>
</div>

</div>
<?=$this->Html->script(['adminjs.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
    var myDate = new Date();
        var year = myDate.getFullYear();
        var month =myDate.getMonth()+1;
        var day =  myDate.getDate();
        var time =year+'-'+month+'-'+day;
        $('#datetimepicker-end').datetimepicker({
            autoclose:true,
            minView:2,
            startDate:'<?= $start?>',
            endDate:time
        }
        ).on('changeDate', function(){
            var end = $('#end-time').val()
            // alert(end);
            location.href="<?= $this ->Url ->build(['prefix' =>'admin', 'controller' =>'log', 'action' =>'adminlog',$department['id'],$start]);?>/"+end+"/<?=$loginname?>";
        });
        $('#datetimepicker-start').datetimepicker({
            autoclose:true,
            minView:2,
            endDate:'<?= $end?>',
        }
        ).on('changeDate', function(){
            var start = $('#start-time').val()
            // alert(end);
            location.href="<?= $this ->Url ->build(['prefix' =>'admin', 'controller' =>'log', 'action' =>'adminlog',$department['id']]);?>/"+start+"/<?php echo $end;?>/<?=$loginname?>";
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

    $('#search').on('click',function(){
        var loginname = $('#searchtext').val();
        location.href = "<?php echo $this->Url->build(array('controller'=>'Log','action'=>'adminlog',$department['id'],$start,$end));?>/"+loginname;
    })
    $(function() {
        var loginname ="<?php echo $loginname ?>";
        $('#searchtext').val(loginname);
    })

    function deletes(id) {
        $('#modal-delete').modal("show");
        $('#yes').one('click',function() {
            $.ajax({
                type: 'post',
                url: '<?php echo $this->Url->build(array('controller' => 'Orders','action'=>'delete')); ?>',
                data: {id: id},
                success: function (data) {
                    var data = eval('(' + data + ')');
                    if (data.code == 0) {
                        tentionHide(data.msg, 0);
                        location.href = '<?php echo $this->Url->build(array('controller'=>'Orders','action'=>'index'));?>';
                    } else {
                        $('#modal-delete').modal("hide");
                        tentionHide(data.msg, 1);
                    }
                }
            });
        })
    }

    $('.auto-proce').bind('click',function(){
        alert("进入下一阶段");
    })
</script>
<?= $this->end() ?>
