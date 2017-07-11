<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal">
        <div>
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">详细信息</a></li>
            </ul>
            <div class="tab-content">
            <div class="form-group">
                <label for="task_name" class="col-sm-2 control-label">任务名称</label>
                <div class="col-sm-6 check-msg">
                   <?php if(isset($data['task_name'])){ echo $data['task_name'];}  ?>
                </div>
            </div>
            <div class="form-group">
                <label for="task_url" class="col-sm-2 control-label">任务URL</label>
                <div class="col-sm-6 check-msg">
                    <?php if(isset($data['task_url'])){ echo $data['task_url'];}  ?>
                </div>
            </div>
            <div class="form-group">
                <label for="task_type" class="col-sm-2 control-label">任务类型</label>
                <div class="col-sm-6 check-msg">
                    <?php if(isset($data['task_type'])){
                        switch($data['task_type']){
                            case 1:
                                echo '一次性任务';break;
                            case 2:
                                echo '每天';break;
                            case 3:
                                echo '每周';break;
                            case 4:
                                echo '每月';break;
                        }
                    } ?>
                </div>
            </div>
            <div class="form-group">
                <label for="planed_day" class="col-sm-2 control-label">计划执行日</label>
                <div class="col-sm-6 check-msg">
                    <?php if(isset($data['planed_day'])){  if($data['planed_day']==0){ echo '每天';}else{ echo $data['planed_day'].'号';}}  ?>
                </div>
            </div>
            <div class="form-group">
                <label for="begin_time" class="col-sm-2 control-label">开始日期</label>
                <div class="col-sm-6 check-msg">
                    <?php if(isset($data['begin_time'])){ echo date('Y-m-d H:i:s',$data['begin_time']);}  ?>
                </div>
            </div>
            <div class="form-group">
                <label for="planed_day" class="col-sm-2 control-label">结束日期</label>
                <div class="col-sm-6 check-msg">
                    <?php if(isset($data['end_time'])){ echo date('Y-m-d H:i:s',$data['end_time']);}  ?>
                </div>
            </div>
            <div class="form-group">
                <label for="next_begin_time" class="col-sm-2 control-label">下次开始时间</label>
                <div class="col-sm-6 check-msg">
                    <?php if(isset($data['next_begin_time'])){ echo date('Y-m-d H:i:s',$data['next_begin_time']);}  ?>
                </div>
            </div>
            <div class="form-group">
                <label for="dura_time" class="col-sm-2 control-label">持续执行时长</label>
                <div class="col-sm-6 check-msg">
                    <?php if(isset($data['dura_time'])){ if($data['dura_time']==0){ echo '长久';}else{ echo $data['dura_time']/60 .'小时';}}  ?>
                </div>
            </div>
            <div class="form-group">
                <label for="task_interval" class="col-sm-2 control-label">间隔时间</label>
                <div class="col-sm-6 check-msg">
                    <?php if(isset($data['task_interval'])){ echo $data['task_interval'].'秒';}  ?>
                </div>
            </div>
            <div class="form-group">
                <label for="last_finish_time" class="col-sm-2 control-label">最后完成时间</label>
                <div class="col-sm-6 check-msg">
                    <?php if(isset($data['last_finish_time'])){ echo date('Y-m-d H:i:s',$data['last_finish_time']);}  ?>
                </div>
            </div>
            <div class="form-group">
                <label for="exec_status" class="col-sm-2 control-label">当前执行状态</label>
                <div class="col-sm-6 check-msg">
                    <?php if(isset($data['exec_status'])){ if($data['exec_status'] ==0){ echo '空闲'; }elseif($data['exec_status']==1){ echo '执行中';}}  ?>
                </div>
            </div>
            <div class="form-group">
                <label for="exec_begin_time" class="col-sm-2 control-label">本次开始时间</label>
                <div class="col-sm-6 check-msg">
                    <?php if(isset($data['exec_begin_time'])){ echo date('Y-m-d H:i:s',$data['exec_begin_time']);}  ?>
                </div>
            </div>
            <div class="form-group">
                <label for="task_status" class="col-sm-2 control-label">任务状态</label>
                <div class="col-sm-6 check-msg">
                    <?php if(isset($data['task_status'])){ if($data['task_status'] ==1){ echo '启用'; }elseif($data['task_status']==0){ echo '挂起';}}  ?>
                </div>
            </div>
                <div class="form-group">
                    <label for="task_para" class="col-sm-2 control-label">执行参数</label>
                    <div class="col-sm-6 check-msg">
                        <?php if(isset($data['task_para'])){ echo $data['task_para'];}  ?>
                    </div>
                </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <a type="submit"  href="<?php echo $this->Url->build(array('controller' => 'AutoTask','action'=>'addedit')); ?>/<?php if(isset($data['id'])){ echo $data['id'];} ?>/check"  id="ds" class="btn btn-primary">修改</a>
                    <!--<a type="submit" href="<?php echo $this->Url->build(array('controller' => 'AutoTask','action'=>'index')); ?>" class="btn btn-danger">返回</a>-->
                    <a type="button" id="back" class="btn btn-danger" onclick="window.history.go(-1)">返回</a>
                </div>
            </div>
           </div>
    </form>
</div>