<style>
    .point-host-startup{
        margin-right: 150px;
    }
</style>
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="role-form"
          action="<?php echo $this->Url->build(array('controller' => 'Role','action'=>'postedit')); ?>"
          method="post">
        <div>
            <div class="content-operate clearfix">
                <div class="pull-left">
                    <span style="font-size: 20px;">修改角色</span>
                </div>

            </div>
            <hr>
            <input type="hidden" value="<?=$data['id'];?>" name="id">
            <!--添加内容-->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="home">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">角色名:</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="name" id="name" value="<?=$data['name'];?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">备注</label>
                        <div class="col-sm-6">
                            <textarea type="text" class="form-control" rows="6" name="note" id="note"><?=$data['note'];?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" id="submit_add" class="btn btn-primary">保存</button>
                            <!--<a type="button" href="<?php echo $this->Url->build(array('controller' => 'Role','action'=>'index')); ?>" class="btn btn-danger">返回</a>-->
                            <a type="button" onclick="window.history.go(-1)" class="btn btn-danger">返回</a>
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
    //提交
    $('#role-form').bootstrapValidator({
        submitButtons: 'button[type="submit"]',
        submitHandler: function(validator, form, submitButton){
            $.post(form.attr('action'), form.serialize(), function(data){
                var data = eval('(' + data + ')');
                if (data.code == 0) {
                    tentionHide(data.msg,0);
                    setTimeout(function () {
//                        location.href = "<?php echo $this->Url->build(array('controller'=>'Role','action'=>'index'));?>";
                        window.location.reload();
                    }, 500);

                } else {
                    tentionHide(data.msg,1);
                }
            });
        },
        fields : {
            name: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '角色名不能为空'
                    },
                    stringLength: {
                        min: 2,
                        max: 16,
                        message: '请保持在2-16位'
                    },
                    regexp: {
                        regexp: /^\S*$/,
                        message: '角色名称不能有空格'
                    }

                }
            }

        }
    });

</script>
<?= $this->end() ?>