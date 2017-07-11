<style>
    .task-target{
        display:none;
    }
</style>
<?= $this->element('content_header'); ?>
<?= $this->Html->css(['bootstrap-datetimepicker.min.css']); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="service-form" action="<?php echo $this->Url->build(array('controller'=>'AutoTask','action'=>'addedit'));?>" method="post">
        <div class="form-group">
            <label for="task_name" class="col-sm-2 control-label">任务名称</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="task_name"  id="task_name" placeholder="任务名称" value="<?php if(isset($data['task_name'])){ echo $data['task_name'];}  ?>">
                <?php if(isset($data)){ ?>
                <input type="hidden" class="form-control" name="id"  id="id" value="<?php if(isset($data)){ echo $data['id']; } ?>">
                <?php } ?>
                <input type="hidden" class="form-control" name="source"  value="<?php if(isset($source)){ echo $source; } ?>">            </div>
        </div>
        <div class="form-group">
            <label for="task_url" class="col-sm-2 control-label">任务URL</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="task_url"  id="task_url" placeholder="任务URL"  value="<?php if(isset($data['task_url'])){ echo $data['task_url'];}  ?>">
            </div>
         </div>
        <div class="form-group" id="date-mode" style="padding-top:5px;">
            <label for="expire" class="col-sm-2 control-label">开始时间</label>
            <div class="col-sm-6">
                <div class="input-append date" id="begin_time"  data-date-format="yyyy-mm-dd">
                    <input size="20" type="text" name="next_begin_time" id="next_begin_time" value="<?php  if(isset($data)){ echo date("Y-m-d H:i:s",$data['next_begin_time']);} ?>">
                    <span class="add-on"><i class="icon-th"></i></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="task_type" class="col-sm-2 control-label">任务类型</label>
            <div class="col-sm-6">
                <input type="radio" name="task_type" <?php if(isset($data)){if($data['task_type'] == 1){ echo 'checked="true"';}}else{ echo 'checked="true"'; } ?> value="1" /> 一次性任务
                <input type="radio" name="task_type" <?php if(isset($data)){if($data['task_type'] == 2){ echo 'checked="true"';}} ?> value="2" /> 每天
                <input type="radio" name="task_type" <?php if(isset($data)){if($data['task_type'] == 4){ echo 'checked="true"';}} ?> value="4" /> 每月
            </div>
        </div>
        <div class="form-group task-target">
            <label for="planed_day" class="col-sm-2 control-label">计划执行日</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="planed_day"  id="planed_day" placeholder="计划执行日"  value="<?php if(isset($data['planed_day'])){ echo $data['planed_day'];}  ?>">
            </div>
        </div>
        <div class="form-group task-target">
            <label for="dura_time" class="col-sm-2 control-label">持续时长</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="dura_time"  id="dura_time" placeholder="持续时长"  value="<?php if(isset($data['dura_time'])){ echo $data['dura_time']/60;}  ?>">&nbsp;&nbsp;小时
            </div>
        </div>
        <div class="form-group task-target">
            <label for="task_interval" class="col-sm-2 control-label">间隔时间</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="task_interval"  id="task_interval" placeholder="间隔时间"  value="<?php if(isset($data['task_interval'])){ echo $data['task_interval'];}  ?>">&nbsp;&nbsp;秒
            </div>
        </div>
        <div class="form-group" id="date-mode" style="padding-top:5px;">
            <label for="expire" class="col-sm-2 control-label">结束时间</label>
            <div class="col-sm-6">
                <div class="input-append date" id="end_time"  data-date-format="yyyy-mm-dd">
                    <input size="20" type="text" name="end_time" value="<?php  if(isset($data)){ echo date("Y-m-d H:i:s",$data['end_time']);}else{ echo '2037-12-31 23:59:59';} ?>">
                    <span class="add-on"><i class="icon-th"></i></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="task_status" class="col-sm-2 control-label">任务状态</label>
            <div class="col-sm-6">
                <input type="radio" name="task_status" <?php if(isset($data)){if($data['task_status'] == 1){ echo 'checked="true"';}}else{ echo 'checked="true"'; } ?> value="1" /> 启用
                <input type="radio" name="task_status" <?php if(isset($data)){if($data['task_status'] == 0){ echo 'checked="true"';}} ?> value="0" /> 挂起
            </div>
        </div>
        <div class="form-group">
            <label for="task_para" class="col-sm-2 control-label">任务参数</label>
            <div class="col-sm-6">
                <textarea type="text" class="form-control" rows="6" name="task_para" id="task_para" placeholder="任务参数" ><?php if(isset($data['task_para'])){ echo $data['task_para'];}  ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label for="task_note" class="col-sm-2 control-label">任务说明</label>
            <div class="col-sm-6">
                <textarea type="text" class="form-control" rows="6" name="task_note" id="task_note" placeholder="任务说明"><?php if(isset($data['task_note'])){ echo $data['task_note'];}  ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" id="service_submit" class="btn btn-primary">保存</button>
                <!--<a type="button" href="<?php echo $this->Url->build(array('controller' => 'AutoTask','action'=>'index')); ?>" class="btn btn-danger">返回</a>-->
                <a type="button" id="back" class="btn btn-danger" onclick="window.history.go(-1)">返回</a>
            </div>
        </div>

    </form>
</div>
<?=$this->Html->script(['adminjs.js','bootstrap-datetimepicker.js','jquery.cookie.js','validator.bootstrap.js','jquery.uploadify.min.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
    var myDate = new Date();
    var year = myDate.getFullYear();
    var month =myDate.getMonth()+1;
    var day =  myDate.getDate();
    var time =year+'-'+month+'-'+day;
    $('#begin_time').datetimepicker({
            format: "yyyy-mm-dd hh:ii",
            autoclose:true,
            maxView:4,
            startDate:time
        }
    );
    $('#end_time').datetimepicker({
            format: "yyyy-mm-dd hh:ii",
            autoclose:true,
            maxView:4,
            startDate:time
        }
    );


    $('#service-form').bootstrapValidator({
    submitButtons: 'button[type="submit"]',
    submitHandler: function(validator, form, submitButton){

        $.post(form.attr('action'), form.serialize(), function(data){
            var data = eval('(' + data + ')');
            if (data.code == 0) {
                tentionHide(data.msg,0);
                if(data.source==1){
                    location.href = '<?php echo $this->Url->build(array('controller'=>'AutoTask','action'=>'check'));?>/'+data.id;
                }else {
                    location.href = '<?php echo $this->Url->build(array('controller'=>'AutoTask','action'=>'index'));?>/index';
                }
            } else {
                tentionHide(data.msg,1);
            }
        });
    },
    fields : {
        task_name: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '任务名称不能为空'
                },
                stringLength: {
                    min: 2,
                    max: 30,
                    message: '请保持在2-32位'
                },
                regexp: {
                    regexp: /^\S*$/,
                    message: '任务名称中不能有空格'
                }
            }
        },
        task_url: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '任务url不能为空'
                },
                regexp: { 
                    regexp: /^((http|ftp|https):\/\/)?[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;:/~\+#]*[\w\-\@?^=%&amp;/~\+#])?$/,
                    message: '请输入正确的任务url'
                }
            }
        },
        begin_time: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '开始时间不能为空'
                }
            }
        },
        end_time: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '结束时间不能为空'
                }
            }
        },
        planed_day: {
            group: '.col-sm-6',
            validators: {
                between: {
                    min: 1,
                    max: 31,
                    message: '数值在1-31之间'
                }
            }
        },
        dura_time: {
            group: '.col-sm-6',
            validators: {
                between: {
                    min: 0,
                    max: 24,
                    message: '数值在0-24之间'
                }
            }
        }
    }


});



$('input[name="task_type"]').change(
    function(){
        switch($(this).val()){
            case "1":{
                $('.task-target').css('display','none');
                break;
            }
            case "2":{
                $('.task-target').css('display','none');
                $('.task-target').not(':first').css('display','block');
                break;
            }
            default:{
                $('.task-target').css('display','none');
                $('.task-target').filter(':first').css('display','block');
            }
        }
    }
);

$(document).ready(
    function(){
        switch($('input[name="task_type"]:checked').val()){
            case "1":{
                break;
            }
            case "2":{
                $('.task-target').not(':first').css('display','block');
                break;
            }
            default:{
                $('.task-target').filter(':first').css('display','block');
            }
        }
    }
);
</script>
<?= $this->end() ?>