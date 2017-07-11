<style>
    .point-host-startup{
        margin-right: 150px;
    }
</style>
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="pwd-form"
          action="<?php echo $this->Url->build(array('controller' => 'Account','action'=>'postpassword')); ?>"
          method="post">
        <div>
            <div class="content-operate clearfix">
                <div class="pull-left">
                    <span style="font-size: 20px;">修改密码</span>
                </div>

            </div>
            <hr>
            <!--添加内容-->
            <input type="hidden" value="<?=$data['id'];?>" name="id">
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="home">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">登录名:</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="loginname" id="loginname" value="<?=$data['loginname'];?>" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">姓名:</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="username" id="username" value="<?=$data['username'];?>" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">密码:</label>
                        <div class="col-sm-6">
                            <input type="password" class="form-control" name="password" id="password" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">确认密码:</label>
                        <div class="col-sm-6">
                            <input type="password" class="form-control" name="repassword" id="repassword" value="">
                        </div>
                    </div>
                    <div class="from-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" id="submit_add" class="btn btn-primary">保存</button>
                            <!--<a type="button" href="<?php echo $this->Url->build(array('controller' => 'Account','action'=>'index')); ?>" class="btn btn-danger">返回</a>-->
                            <a type="button" id="back" class="btn btn-danger" onclick="window.history.go(-1)">返回</a>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </form>
</div>
<?=$this->Html->script(['adminjs.js','bootstrap-datetimepicker.js','validator.bootstrap.js','jquery.cookie.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">


    //提交
    $('#pwd-form').bootstrapValidator({
        submitButtons: 'button[type="submit"]',
        submitHandler: function(validator, form, submitButton){
            $.post(form.attr('action'), form.serialize(), function(data){
                var data = eval('(' + data + ')');
                if (data.code == 0) {
                    tentionHide(data.msg,0);
                    setTimeout(function () {
//                        location.href = "<?php echo $this->Url->build(array('controller'=>'Account','action'=>'index'));?>";
                        window.location.reload();
                    }, 500);

                } else {
                    tentionHide(data.msg,1);
                }
            });
        },
        fields : {
            password: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '密码不能为空'
                    },
                    stringLength: {
                        min: 6,
                        max: 16,
                        message: '请保持在6-16位'
                    },
                    regexp: {
                        regexp: /^\S*$/,
                        message: '密码不能有空格'
                    }
                }
            },
            repassword: {
                trigger: 'submit',
                validators: {
                    notEmpty: {
                        message: '确认密码不能为空'
                    },
                    identical: {
                        field: 'password',
                        message: '请输入相同的密码'
                    }
                }
            }

        }
    });

</script>
<?= $this->end() ?>