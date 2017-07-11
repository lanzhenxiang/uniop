<style>
    .point-host-startup{
        margin-right: 150px;
    }
</style>
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="department-form"
          action="<?php echo $this->Url->build(array('controller' => 'BudgetTemplate','action'=>'postadjust')); ?>?type=<?php if(isset($type)){echo $type;}?>"
          method="post">
        <div>
            <div class="content-operate clearfix">
                <div class="pull-left">
                    <span style="font-size: 20px;">调整资源配额</span>
                </div>
                <div class="pull-right">
                    <button type="submit" id="submit" class="btn btn-primary">保存</button>
                    <a type="button" href="<?php echo $this->Url->build(array('controller' => 'BudgetTemplate','action'=>'index')); ?>?type=<?php if(isset($type)){echo $type;}?>" class="btn btn-danger">取消</a>
                </div>
            </div>
            <!--添加内容-->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="home">

                    <?php if(isset($data)){?>
                    <?php foreach($data as $key => $value){?>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label"><?=$value['title_f'];?></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="<?=$value['para_code'];?>" id="<?=$value['para_code'];?>" value="<?=$value['para_value'];?>">&#160;&#160;<?=$value['title_b'];?>
                        </div>
                    </div>
                    <?php }?>
                    <?php }?>


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
                    setTimeout(function () {
                    window.location.reload()},500);
                } else {
                    tentionHide(data.msg, 1);
                    setTimeout(function () {
                        window.location.reload()},500);
                }
            });
        },
        fields: {
            router_bugedt:{
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
            cpu_bugedt:{
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
            memory_buget:{
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
            gpu_bugedt:{
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
            disks_bugedt:{
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
            disks_cap_bugedt:{
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
            fics_num_bugedt:{
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
            fics_cap_bugedt:{
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
            oceanstor9k_num_bugedt:{
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
            oceanstor9k_cap_bugedt:{
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
            basic_budget:{
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
            fire_budget:{
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
            elb_budget:{
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
            eip_budget:{
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