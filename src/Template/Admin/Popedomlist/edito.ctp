<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="popedomlist-form" action="<?php echo $this->Url->build(array('controller'=>'Popedomlist','action'=>'addedit'));?>" method="post">
        <div class="form-group">
            <?php if(isset($data)){ ?>
            <input type="hidden" value="<?php if(isset($data)){echo $data['popedomid'];}?>" name="popedomid" id="popedomid">
            <?php } ?>
            <label for="popedomname" class="col-sm-2 control-label">权限名称</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="popedomname" id="popedomname" value="<?php if(isset($data)){echo $data['popedomname'];}?>" placeholder="权限名称">
            </div>
        </div>
        <div class="form-group">
            <label for="popedomnote" class="col-sm-2 control-label">权限说明</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="popedomnote" id="popedomnote" value="<?php if(isset($data)){echo $data['popedomnote'];}?>" placeholder="权限说明">
            </div>
        </div>
        <div class="form-group">
            <label for="parent_id" class="col-sm-2 control-label">上级菜单</label>
            <div class="col-sm-6">
                <select class="form-control" id="parent_id" name="parent_id">
                    <option value="<?= $_workFlow->popedomid ;?>" 'selected' >
                        <span><?= $_workFlow->popedomnote ;?></span>
                    </option>
                </select>
            </div>
            <label class="control-label text-danger"><i id="shangji"></i></label>
        </div>
        <div class="form-group">
            <label for="popedomsubtype" class="col-sm-2 control-label">权限所属类型</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="popedomsubtype" id="popedomsubtype" value="<?php if(isset($data)){echo $data['popedomsubtype'];}?>" placeholder="权限所属类型">
            </div>
        </div>
        <div class="form-group">
            <label for="serinalno" class="col-sm-2 control-label">排序</label>
            <div class="col-sm-6">
                <input type="text" name="serinalno"  class="form-control" id="serinalno" value="<?php if(isset($data)){echo $data['serinalno'];}?>" placeholder="排序">
            </div>
            <label class="control-label text-danger"><i id="sn"></i></label>
        </div>
        <div class="form-group">
            <label for="popedomtype" class="col-sm-2 control-label">所属系统</label>
            <div class="col-sm-6">
                <select id="popedomtype" name="popedomtype" class="form-control">
                    <option value="cmop_console" <?php if(isset($data['popedomtype']) && $data['popedomtype']=='cmop_console'){ echo 'selected';}  ?> >
                        cmop_console
                    </option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" id="account_submit" class="btn btn-primary">保存</button>
                <a type="button" href="<?php echo $this->Url->build(array('controller' => 'Popedomlist','action'=>'index')); ?>" class="btn btn-danger">返回</a>
            </div>
        </div>
    </form>
</div>

<?=$this->Html->script(['adminjs.js','validator.bootstrap.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">

$('#popedomlist-form').bootstrapValidator({
    submitButtons: 'button[type="submit"]',
    submitHandler: function(validator, form, submitButton){
        $.post(form.attr('action'), form.serialize(), function(data){
            var data = eval('(' + data + ')');
            if(data.code==0){
                tentionHide(data.msg,0);
                location.href='<?php echo $this->Url->build(array('controller'=>'Popedomlist','action'=>'order'));?>';
            }else{
                tentionHide(data.msg,1);
            }
        });
    },
    fields : {
        popedomname: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '权限名称不能为空'
                },
                stringLength: {
                    min: 1,
                    max: 30,
                    message: '请保持在1-30位'
                },
                regexp: {
                    regexp: /^[^\u4e00-\u9fa5\s]{0,}$/,
                    message: '权限名称不能有空格和中文'
                }
            }
        },
        popedomnote: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '权限说明不能为空'
                },
                stringLength: {
                    min: 1,
                    max: 30,
                    message: '请保持在1-30位'
                },
            }
        },
        popedomsubtype: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '权限所属类型不能为空'
                },
                stringLength: {
                    min: 1,
                    max: 30,
                    message: '请保持在1-30位'
                },
                regexp: {
                    regexp: /^\S*$/,
                    message: '权限所属类型不能有空格'
                }
            }
        },
        serinalno: {
                group: '.col-sm-6',
                validators: {
                    between: {
                        min: 0,
                        max: 1000,
                        message: '排序只能在0-1000之间'
                    },
                    regexp: {
                        regexp: /^([1-9]\d*|0)$/,
                        message: '请输入整数'
                    }
                }
            },
    }
});
</script>
<?= $this->end() ?>