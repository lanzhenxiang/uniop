<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="service-form" action="<?php echo $this->Url->build(array('controller'=>'ServiceType','action'=>'addedit'));?>" method="post">
        <div class="form-group">
            <label for="service_name" class="col-sm-2 control-label">媒体服务名称</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="service_name"  id="service_name" placeholder="媒体服务名称" value="<?php if(isset($data['service_name'])){ echo $data['service_name'];}  ?>">
                <?php if(isset($data)){ ?>
                <input type="hidden" class="form-control" name="type_id"  id="type_id" value="<?php if(isset($data)){ echo $data['type_id']; } ?>">
                <?php } ?>
                <input type="hidden" class="form-control" name="source"  value="<?php if(isset($source)){ echo $source; } ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="service_code" class="col-sm-2 control-label">媒体服务代码</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="service_code"  id="service_code" placeholder="媒体服务代码"  value="<?php if(isset($data['service_code'])){ echo $data['service_code'];}  ?>">
            </div>
         </div>
        <div class="form-group">
            <label for="department" class="col-sm-2 control-label">租户</label>
            <div class="col-sm-6">
                <select id="department" name="department_id" class="form-control">
                    <?php if(!in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname')) && in_array('cmop_global_tenant_admin',$this->request->session()->read('Auth.User.popedomname'))){
                        foreach ($dept as $key => $value) {
                            if($value['id'] == $this->request->session()->read('Auth.User.department_id')){
                            ?>
                        <option value="<?php echo $value['id'];?>" <?php if(isset($data['department_id']) && $data['department_id']==$value['id']){ echo 'selected';}elseif($value['id']==$this->request->session()->read('Auth.User.department_id')){ echo 'selected';}  ?> >
                            <span><?php echo $value['name'];?></span>
                        </option>
                       <?php }} }elseif(in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))){
                        foreach ($dept as $key => $value) {
                        ?>
                        <option value="<?php echo $value['id'];?>" <?php if(isset($data['department_id']) && $data['department_id']==$value['id']){ echo 'selected';}  ?> >
                            <span><?php echo $value['name'];?></span>
                        </option>
                    <?php } }  ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="template_id" class="col-sm-2 control-label">计费模板</label>
            <div class="col-sm-6">
                <select id="template_id" name="template_id" class="form-control">
                    <?php foreach ($template as $key => $value) {   ?>
                        <option value="<?php echo $value['id'];?>" <?php if(isset($data['template_id']) && $data['template_id']==$value['id']){ echo 'selected';}  ?> >
                            <span><?php echo $value['template_name'];?></span>
                        </option>
                    <?php }   ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="template_id" class="col-sm-2 control-label">计费方式</label>
            <div class="col-sm-6">
                <select id="template_id" name="charge_type" class="form-control">
                    <option value="1" <?php if(isset($data['charge_type']) && $data['charge_type']==1){ echo 'selected';}  ?> >
                        <span>按天计费</span>
                    </option>
                    <option value="2" <?php if(isset($data['charge_type']) && $data['charge_type']==2){ echo 'selected';}  ?> >
                        <span>按月计费</span>
                    </option>
                    <option value="3" <?php if(isset($data['charge_type']) && $data['charge_type']==3){ echo 'selected';}  ?> >
                        <span>按分钟计费</span>
                    </option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="min_instance" class="col-sm-2 control-label">最小实例数量</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="min_instance"  id="min_instance" placeholder="最小实例数量"  value="<?php if(isset($data['min_instance'])){ echo $data['min_instance'];}  ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="max_instance" class="col-sm-2 control-label">最大实例数量</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="max_instance"  id="max_instance" placeholder="最大实例数量"  value="<?php if(isset($data['max_instance'])){ echo $data['max_instance'];}  ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="job_type" class="col-sm-2 control-label">job类型</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="job_type"  id="job_type" placeholder="job类型(对应MPC)"  value="<?php if(isset($data['job_type'])){ echo $data['job_type'];}  ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="extend_type" class="col-sm-2 control-label">弹性扩展类型</label>
            <div class="col-sm-6">
                <input type="radio" name="extend_type" <?php if(isset($data)){if($data['extend_type'] == 0){ echo 'checked="true"';}}else{ echo 'checked="true"'; } ?> value="0" /> 手动
                <input type="radio" name="extend_type" <?php if(isset($data)){if($data['extend_type'] == 1){ echo 'checked="true"';}} ?> value="1" /> 自动
            </div>
        </div>
        <div class="form-group">
            <label for="process_efficiency" class="col-sm-2 control-label">处理效率</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="process_efficiency"  id="process_efficiency" placeholder="处理效率"  value="<?php if(isset($data['process_efficiency'])){ echo $data['process_efficiency'];}  ?>">
            </div>
        </div>
       <!-- <div class="form-group">
            <label for="service_exe" class="col-sm-2 control-label">程序名称</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="service_exe"  id="service_exe" placeholder="程序名称"  value="<?php /*if(isset($data['service_exe'])){ echo $data['service_exe'];}  */?>">
            </div>
        </div>-->
        <div class="form-group">
            <label for="service_note" class="col-sm-2 control-label">服务说明</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="service_note"  id="service_note" placeholder="服务说明"  value="<?php if(isset($data['service_note'])){ echo $data['service_note'];}  ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="sort_order" class="col-sm-2 control-label">排序</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="sort_order"  id="sort_order" placeholder="排序"  value="<?php if(isset($data['sort_order'])){ echo $data['sort_order'];}  ?>">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" id="service_submit" class="btn btn-primary">保存</button>
                <a type="button" href="<?php echo $this->Url->build(array('controller' => 'ServiceType','action'=>'index')); ?>" class="btn btn-danger">返回</a>
            </div>
        </div>

    </form>
</div>
<?=$this->Html->script(['adminjs.js','jquery.cookie.js','validator.bootstrap.js','jquery.uploadify.min.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
$('#service-form').bootstrapValidator({
    submitButtons: 'button[type="submit"]',
    submitHandler: function(validator, form, submitButton){

        $.post(form.attr('action'), form.serialize(), function(data){
            var data = eval('(' + data + ')');
            if (data.code == 0) {
                tentionHide(data.msg,0);
                if(data.source==1){
                    location.href = '<?php echo $this->Url->build(array('controller'=>'ServiceType','action'=>'check'));?>/'+data.id;
                }else {
                    location.href = '<?php echo $this->Url->build(array('controller'=>'ServiceType','action'=>'index'));?>/index';
                }
            } else {
                tentionHide(data.msg,1);
            }
        });
    },
    fields : {
        service_name: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '服务名称不能为空'
                },
                stringLength: {
                    min: 2,
                    max: 16,
                    message: '请保持在2-32位'
                },
                regexp: {
                    regexp: /^\S*$/,
                    message: '服务名称中不能有空格'
                }
            }
        },
        service_code: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '服务代码不能为空'
                },
                stringLength: {
                    min: 2,
                    max: 16,
                    message: '请保持在2-16位'
                },
                regexp: {
                    regexp: /^\S*$/,
                    message: '服务代码中不能有空格'
                }
            }
        },
        min_instance: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '最小实例数不能为空'
                },
                between: {
                    min: 0,
                    max: 1000,
                    message: '数值在0-1000之间'
                },
                regexp: {
                    regexp: /^([1-9]\d*|0)$/,
                    message: '请输入整数'
                }
            }
        },
        max_instance: {
            trigger: 'submit',
            validators: {
                notEmpty: {
                    message: '最大实例数不能为空'
                },
                between: {
                    min: 1,
                    max: 1000,
                    message: '数值在1-1000之间'
                },
                regexp: {
                    regexp: /^([1-9]\d*|0)$/,
                    message: '请输入整数'
                }
            }
        },
        /*job_type: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: 'job类型不能为空'
                }
            }
        },*/
        process_efficiency: {
            group: '.col-sm-6',
            validators: {
               /* notEmpty: {
                    message: '处理效率不能为空'
                },*/
                numeric: {
                    message: '请输入正确的数值'
                }
            }
        }
    }
});
</script>
<?= $this->end() ?>