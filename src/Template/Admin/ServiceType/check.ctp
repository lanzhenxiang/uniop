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
                <label for="service_name" class="col-sm-2 control-label">媒体服务名称</label>
                <div class="col-sm-6 check-msg">
                   <?php if(isset($data['service_name'])){ echo $data['service_name'];}  ?>
                    <?php if(isset($data)){ ?>
                        <input type="hidden" class="form-control" name="type_id"  id="type_id" value="<?php if(isset($data)){ echo $data['type_id']; } ?>">
                    <?php } ?>
                </div>
            </div>
            <div class="form-group">
                <label for="service_code" class="col-sm-2 control-label">媒体服务代码</label>
                <div class="col-sm-6 check-msg">
                    <?php if(isset($data['service_code'])){ echo $data['service_code'];}  ?>
                </div>
            </div>
            <div class="form-group">
                <label for="service_code" class="col-sm-2 control-label">租户</label>
                <div class="col-sm-6 check-msg">
                    <?php if(isset($data['department']['name'])){ echo $data['department']['name'];}  ?>
                </div>
            </div>
            <div class="form-group">
                <label for="service_code" class="col-sm-2 control-label">计费模板</label>
                <div class="col-sm-6 check-msg">
                    <?php if(isset($data['charge_template']['template_name'])){ echo $data['charge_template']['template_name'];}  ?>
                </div>
            </div>
            <div class="form-group">
                <label for="service_code" class="col-sm-2 control-label">忙碌实例数量</label>
                <div class="col-sm-6 check-msg">
                    <?php if(isset($data['busy_instance'])){ echo $data['busy_instance'];}  ?>
                </div>
            </div>
            <div class="form-group">
                <label for="service_code" class="col-sm-2 control-label">空闲实例数量</label>
                <div class="col-sm-6 check-msg">
                    <?php if(isset($data['free_instance'])){ echo $data['free_instance'];}  ?>
                </div>
            </div>
            <div class="form-group">
                <label for="service_code" class="col-sm-2 control-label">最后更新时间</label>
                <div class="col-sm-6 check-msg">
                    <?php if(isset($data['check_time'])){ echo date('Y-m-d H:i:s',$data['check_time']);}  ?>
                </div>
            </div>
            <div class="form-group">
                <label for="service_code" class="col-sm-2 control-label">排队任务数</label>
                <div class="col-sm-6 check-msg">
                    <?php if(isset($data['wait_job'])){ echo $data['wait_job'];}  ?>
                </div>
            </div>
            <div class="form-group">
                <label for="service_code" class="col-sm-2 control-label">最小实例数量</label>
                <div class="col-sm-6 check-msg">
                    <?php if(isset($data['min_instance'])){ echo $data['min_instance'];}  ?>
                </div>
            </div>
            <div class="form-group">
                <label for="service_code" class="col-sm-2 control-label">最大实例数量</label>
                <div class="col-sm-6 check-msg">
                    <?php if(isset($data['max_instance'])){ echo $data['max_instance'];}  ?>
                </div>
            </div>
            <div class="form-group">
                <label for="service_code" class="col-sm-2 control-label">job类型</label>
                <div class="col-sm-6 check-msg">
                    <?php if(isset($data['job_type'])){ echo $data['job_type'];}  ?>
                </div>
            </div>
            <div class="form-group">
                <label for="service_code" class="col-sm-2 control-label">弹性扩展类型</label>
                <div class="col-sm-6 check-msg">
                    <?php if(isset($data['extend_type']))if($data['extend_type']==0){ echo '手动';}elseif($data['extend_type']==1){ echo '自动';}  ?>
                </div>
            </div>
            <div class="form-group">
                <label for="service_code" class="col-sm-2 control-label">处理效率</label>
                <div class="col-sm-6 check-msg">
                    <?php if(isset($data['process_efficiency'])){ echo $data['process_efficiency'];}  ?>
                </div>
            </div>
            <div class="form-group">
                <label for="service_code" class="col-sm-2 control-label">当前实例数量</label>
                <div class="col-sm-6 check-msg">
                    <?php if(isset($data['current_instance'])){ echo $data['current_instance'];}  ?>
                </div>
            </div>
           <!-- <div class="form-group">
                <label for="service_code" class="col-sm-2 control-label">程序名称</label>
                <div class="col-sm-6 check-msg">
                    <?php /*if(isset($data['service_exe'])){ echo $data['service_exe'];}  */?>
                </div>
            </div>
            <div class="form-group">
                <label for="service_code" class="col-sm-2 control-label">服务说明</label>
                <div class="col-sm-6 check-msg">
                    <?php /*if(isset($data['service_note'])){ echo $data['service_note'];}  */?>
                </div>
            </div>-->
            <div class="form-group">
                <label for="sort_order" class="col-sm-2 control-label">排序</label>
                <div class="col-sm-6 check-msg">
                   <?php if(isset($data['sort_order'])){ echo $data['sort_order'];}  ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <a type="submit"  href="<?php echo $this->Url->build(array('controller' => 'ServiceType','action'=>'addedit')); ?>/<?php if(isset($data['type_id'])){ echo $data['type_id'];} ?>/check"  id="ds" class="btn btn-primary">修改</a>
                    <a type="submit" href="<?php echo $this->Url->build(array('controller' => 'ServiceType','action'=>'index')); ?>" class="btn btn-danger">返回</a>
                </div>
            </div>
           </div>
    </form>
</div>