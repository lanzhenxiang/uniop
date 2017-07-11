<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="aduser-form" action="<?php echo $this->Url->build(array('controller' => 'AdUser','action'=>'addedit')); ?>" method="post">
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">登录账号</label>
            <div class="col-sm-6">
                <input type="text" class="form-control"  disabled="disabled" value="<?php if(isset($data['loginName'])){ echo $data['loginName'];}  ?>">
                <?php if(isset($data)){ ?>
                <input type="hidden" class="form-control" name="loginName"  value="<?php echo $data['loginName'];  ?>">
                <input type="hidden" class="form-control" name="vpcCode"  value="<?php echo $data['vpcCode'];  ?>">
                <input type="hidden" class="form-control" name="id"  value="<?php echo $data['id'];  ?>">
                <input type="hidden" class="form-control" name="uid"  value="<?php echo $data['uid']; ?>">
                <?php } ?>
            </div>
        </div>

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">密码</label>
            <div class="col-sm-6">
                <input type="password" class="form-control" name="loginPassword" id="password" placeholder="留空即保持原密码">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">vpcCode</label>
            <div class="col-sm-6">
                <input type="text" class="form-control"  disabled="disabled" value="<?php if(isset($data['vpcCode'])){ echo $data['vpcCode'];}  ?>">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" id="ds" class="btn btn-primary">保存</button>
                <!--<a type="button" href="<?php echo $this->Url->build(array('controller' => 'AdUser','action'=>'index')); ?>" class="btn btn-danger">返回</a>-->
                <a type="button" id="back" class="btn btn-danger" onclick="window.history.go(-1)">返回</a>
            </div>
        </div>
    </form>
</div>

<?=$this->Html->script(['adminjs.js','validator.bootstrap.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">

    $(function(){
        $('#ds').attr('disabled',true);
        $('#password').on('blur',function(){
            if($('#password').val()==''){
                $('#ds').attr('disabled','true');
            }else{
                $('#ds').removeAttr('disabled');
            }
        })
    })
    $('#aduser-form').bootstrapValidator({
        submitButtons: 'button[type="submit"]',
        submitHandler: function(validator, form, submitButton){
            $.post(form.attr('action'), form.serialize(), function(data){
                var data = eval('(' + data + ')');
                if (data.code == 0) {
                    tentionHide(data.msg, 0);
                    location.href = '<?php echo $this->Url->build(array('controller'=>'AdUser','action'=>'index'));?>';
                } else {
                    tentionHide(data.msg, 1);
                }
            });
        },
        fields : {
            loginPassword: {
                group: '.col-sm-6',
                validators: {
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
        }
    });
</script>
<?= $this->end() ?>

