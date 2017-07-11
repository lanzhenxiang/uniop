<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="instance-basic-form" action="<?php echo $this->Url->build(array('controller' => 'InstanceBasic','action'=>'editip')); ?>" method="post">
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">机器名称</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="name" disabled  id="name" placeholder="机器名称" value="<?php if(isset($department_data['name'])){ echo $department_data['name'];}  ?>">
                <?php if(isset($department_data)){ ?>
                <input type="hidden" class="form-control" name="id"  id="id" value="<?php if(isset($department_data['host_extend'])){ echo $department_data['host_extend']['id']; } ?>">
                <input type="hidden" class="form-control" name="basic_id"  id="basic_id" value="<?php if(isset($department_data['id'])){ echo $department_data['id']; } ?>">
                <?php } ?>
            </div>
        </div>
        <div class="form-group">
            <label for="cpu" class="col-sm-2 control-label">CPU(核)</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="cpu"  id="cpu" placeholder="CPU(核)"  value="<?php if(isset($department_data['host_extend']['cpu'])){ echo $department_data['host_extend']['cpu'];}?>">
            </div>

            <!-- <label class="control-label text-danger"><i class="icon-asterisk" id="cpu"></i></label> -->
        </div> 
        <div class="form-group">
            <label for="memory" class="col-sm-2 control-label">内存大小(GB)</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="memory"  id="memory" placeholder="内存大小"  value="<?php if(isset($department_data['host_extend']['memory'])){ echo $department_data['host_extend']['memory'];}?>">
            </div>

            <!-- <label class="control-label text-danger"><i class="icon-asterisk" id="memory"></i></label> -->
        </div> 
        <div class="form-group">
            <label for="gpu" class="col-sm-2 control-label">GPU(MB)</label>
            <div class="col-sm-6">
                <select id="gpu" name="gpu" class="form-control">
                    <option value="0" <?php if(isset($department_data['host_extend']['gpu']) && $department_data['host_extend']['gpu']==0){ echo 'selected';}  ?> >
                        <span>0</span>
                    </option>
                    <option value="512" <?php if(isset($department_data['host_extend']['gpu']) && $department_data['host_extend']['gpu']==512){ echo 'selected';}  ?> >
                        <span>512</span>
                    </option>
                    <option value="1024" <?php if(isset($department_data['host_extend']['gpu']) && $department_data['host_extend']['gpu']==1024){ echo 'selected';}  ?> >
                        <span>1024</span>
                    </option>
                    <option value="2048" <?php if(isset($department_data['host_extend']['gpu']) && $department_data['host_extend']['gpu']==2048){ echo 'selected';}  ?> >
                        <span>2048</span>
                    </option>
                    <option value="4096" <?php if(isset($department_data['host_extend']['gpu']) && $department_data['host_extend']['gpu']==4096){ echo 'selected';}  ?> >
                        <span>4096</span>
                    </option>
                </select>
            </div>

            <!-- <label class="control-label text-danger"><i class="icon-asterisk" id="daima"></i></label> -->
        </div>
        <div class="form-group">
            <label for="plat_form" class="col-sm-2 control-label">操作系统平台</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="plat_form"  id="plat_form" placeholder="操作系统平台"  value="<?php if(isset($department_data['host_extend']['plat_form'])){ echo $department_data['host_extend']['plat_form'];}?>">
            </div>

            <!-- <label class="control-label text-danger"><i class="icon-asterisk" id="daima"></i></label> -->
        </div>
        <div class="form-group">
            <label for="os_family" class="col-sm-2 control-label">操作系统</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="os_family"  id="os_family" placeholder="操作系统"  value="<?php if(isset($department_data['host_extend']['os_family'])){ echo $department_data['host_extend']['os_family'];}?>">
            </div>

            <!-- <label class="control-label text-danger"><i class="icon-asterisk" id="daima"></i></label> -->
        </div>
        <div class="form-group">
            <label for="ip" class="col-sm-2 control-label">IP地址</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="ip"  id="ip" placeholder="IP地址"  value="<?php if(isset($department_data['host_extend']['ip'])){ echo $department_data['host_extend']['ip'];}?>">
            </div>

        </div>
        <div class="form-group">
            <input type="hidden" value="" name="location_code" id="location_code">
            <label for="location_name" class="col-sm-2 control-label">所属区域</label>
            <div class="col-sm-6">

                <select name="location_name" id="location_name" class="form-control">
                    <?php foreach ($data['agent_info'] as $agent) {?>
                    <option code="<?= $agent['class_code']?>" value="<?= $agent['display_name']?>" <?php if(isset($department_data['location_code'])){if ($agent['display_name']==$department_data['location_name']){echo 'selected';}} ?>
                        ><?= $agent['display_name']?></option>
                        <?php }?>
                    </select>
                </div>
                <!-- <label class="control-label text-danger"><i class="icon-asterisk" id="daima"></i></label> -->
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" id="ds" class="btn btn-primary">保存</button>
                    <a type="button" href="<?php echo $this->Url->build(array('controller' => 'InstanceBasic','action'=>'index')); ?>" class="btn btn-danger">返回</a>
                </div>
            </div>
        </form>
    </div>


    <?=$this->Html->script(['adminjs.js','validator.bootstrap.js']); ?>
    <?= $this->start('script_last'); ?>
    <script type="text/javascript">

        $('#instance-basic-form').bootstrapValidator({
            submitButtons: 'button[type="submit"]',
            submitHandler: function(validator, form, submitButton){
                $('#location_code').val($('#location_name option:selected' ).attr('code'));
                $.post(form.attr('action'), form.serialize(), function(data){
                    var data = eval('(' + data + ')');
                    if(data.code==0){
                        tentionHide(data.msg,0);
                        location.href='<?php echo $this->Url->build(array('controller'=>'InstanceBasic','action'=>'index'));?>';
                    }else{
                        tentionHide(data.msg,1);
                    }
                });
            },
            fields : {
                cpu: {
                    group: '.col-sm-6',
                    validators: {
                        notEmpty: {
                            message: 'CPU不能为空'
                        },
                        between: {
                            min: 1,
                            max: 32,
                            message: 'CPU只能是1或2-32之间的偶数'
                        },
                        regexp: {
                            regexp: /^(([1-9][0-9]*)?[02468]|1)$/,
                            message: 'CPU只能是1或2-32之间的偶数'
                        }
                    }
                },
                memory: {
                    group: '.col-sm-6',
                    validators: {
                        notEmpty: {
                            message: '内存不能为空'
                        },
                        between: {
                            min: 1,
                            max: 32,
                            message: '内存只能是1或2-32之间的偶数'
                        },
                        regexp: {
                            regexp: /^(([1-9][0-9]*)?[02468]|1)$/,
                            message: '内存只能是1或2-32之间的偶数'
                        }
                    }
                },
                plat_form: {
                    group: '.col-sm-6',
                    validators: {
                        stringLength: {
                            min: 0,
                            max: 16,
                            message: '请保持在1-16位'
                        },
                    }
                },
                os_family: {
                    group: '.col-sm-6',
                    validators: {
                        stringLength: {
                            min: 0,
                            max: 16,
                            message: '请保持在1-16位'
                        },
                    }
                },
                ip: {
                    group: '.col-sm-6',
                    validators: {
                        ip: {
                            message: '请输入正确的ip地址'
                        },
                    }
                }
            }
        }); 
</script>
<?= $this->end() ?>