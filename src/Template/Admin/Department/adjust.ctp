<style>
    .point-host-startup{
        margin-right: 150px;
    }
</style>
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="department-form"
          action="<?php echo $this->Url->build(array('controller' => 'Department','action'=>'postadjust')); ?>"
          method="post">
        <div>
            <div class="content-operate clearfix">
                <div class="pull-left">
                    <span style="font-size: 20px;">调整资源配额</span>
                </div>

            </div>
            <!--添加内容-->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="home">

                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">VPC/路由器</label>
                        <div class="col-sm-6">
                            <input type="hidden" name="id" value="<?=$id;?>">
                            <input type="text" class="form-control" name="router" id="router" value="<?=$router?>">个
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">CPU</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="cpu" id="cpu" value="<?=$cpu?>">核
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">内存</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="memory" id="memory" value="<?=$memory;?>">GB
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">GPU</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="gpu" id="gpu" value="<?=$gpu;?>">MB
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">块存储个数</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="disk" id="disk" value="<?=$disk?>">个
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">单个块存储容量</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="disk_cap" id="disk_cap" value="<?=$disk_cap;?>">GB
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">FICS存储卷</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="fics" id="fics" value="<?=$fics;?>">个
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">总FICS卷容量上限</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="fics_cap" id="fics_cap" value="<?=$fics_cap?>">GB
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">H9000存储卷</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="h9000" id="h9000" value="<?=$h9000;?>">个
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">总H9000卷容量上限</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="h9000_cap" id="h9000_cap" value="<?=$h9000_cap?>">GB
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">桌面基础套件</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="basic" id="basic" value="<?=$basic?>">套
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">防火墙</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="fire" id="fire" value="<?=$fire;?>">个
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">负载均衡</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="elb" id="elb" value="<?=$elb;?>">个
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">EIP</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="eip" id="eip" value="<?=$eip;?>">个
                        </div>
                    </div>
                    <div class="from-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" id="submit_add" class="btn btn-primary">保存</button>
                            <a type="button"
                               href="<?php echo $this->Url->build(array('controller' => 'Department','action'=>'management')); ?>?id=<?=$id;?>" class="btn btn-danger">返回</a>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>
</div>
<?=$this->Html->script(['adminjs.js','validator.bootstrap.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
    //    提交表单
    $('#department-form').bootstrapValidator({
        submitButtons: 'button[type="submit"]',
        submitHandler: function(validator, form, submitButton){
            $.post(form.attr('action'), form.serialize(), function (data) {
                var data = eval('(' + data + ')');
                if (data.code == 0) {
                    tentionHide(data.msg, 0);
//                    location.href = "<?php echo $this->Url->build(array('controller'=>'Department','action'=>'index'));?>";
                    window.location.reload();
                } else {
                    tentionHide(data.msg, 1);
                }
            });
        },
        fields: {
            router:{
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: 'vpc配额不能为空'
                    },
                    regexp: {
                        regexp: /^([1-9]\d*|0)$/,
                        message: '请输入整数'
                    }
                }
            },
            cpu:{
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: 'cpu不能为空'
                    },
                    regexp: {
                        regexp: /^([1-9]\d*|0)$/,
                        message: '请输入整数'
                    }
                }
            },
            memory:{
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '内存配额不能为空'
                    },
                    regexp: {
                        regexp: /^([1-9]\d*|0)$/,
                        message: '请输入整数'
                    }
                }
            },
            gpu:{
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: 'gpu配额不能为空'
                    },
                    regexp: {
                        regexp: /^([1-9]\d*|0)$/,
                        message: '请输入整数'
                    }
                }
            },
            disk:{
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '块存储个数不能为空'
                    },
                    regexp: {
                        regexp: /^([1-9]\d*|0)$/,
                        message: '请输入整数'
                    }
                }
            },
            disk_cap:{
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '单个块存储容量不能为空'
                    },
                    regexp: {
                        regexp: /^([1-9]\d*|0)$/,
                        message: '请输入整数'
                    }
                }
            },
            fics:{
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: 'fics存储卷个数不能为空'
                    },
                    regexp: {
                        regexp: /^([1-9]\d*|0)$/,
                        message: '请输入整数'
                    }
                }
            },
            fics_cap:{
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '单个fics卷容量不能为空'
                    },
                    regexp: {
                        regexp: /^([1-9]\d*|0)$/,
                        message: '请输入整数'
                    }
                }
            },
            h9000:{
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: 'h9000存储卷个数不能为空'
                    },
                    regexp: {
                        regexp: /^([1-9]\d*|0)$/,
                        message: '请输入整数'
                    }
                }
            },
            h9000_cap:{
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '单个h9000卷容量不能为空'
                    },
                    regexp: {
                        regexp: /^([1-9]\d*|0)$/,
                        message: '请输入整数'
                    }
                }
            },
            basic:{
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '桌面基础套件配额不能为空'
                    },
                    regexp: {
                        regexp: /^([1-9]\d*|0)$/,
                        message: '请输入整数'
                    }
                }
            },
            fire:{
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '防火墙配额不能为空'
                    },
                    regexp: {
                        regexp: /^([1-9]\d*|0)$/,
                        message: '请输入整数'
                    }
                }
            },
            elb:{
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '负载均衡配额不能为空'
                    },
                    regexp: {
                        regexp: /^([1-9]\d*|0)$/,
                        message: '请输入整数'
                    }
                }
            },
            eip:{
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '公网IP配额不能为空'
                    },
                    regexp: {
                        regexp: /^([1-9]\d*|0)$/,
                        message: '请输入整数'
                    }
                }
            }

        }
    });


</script>
<?= $this->end() ?>