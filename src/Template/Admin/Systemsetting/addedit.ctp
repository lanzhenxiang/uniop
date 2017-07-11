<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" action="<?php echo $this->Url->build(array('controller' => 'Systemsetting','action'=>'addedit')); ?>">
        <!-- <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">参数类型</label>
            <div class="col-sm-6">
                <select class="form-control" name="para_type" id="para_type">
                    <option value="">请选择</option>
                    <option value="0" <?php if(isset($data['para_type'])){if($data['para_type']==0){echo "selected";}}?>>系统参数</option>
                    <option value="1" <?php if(isset($data['para_type'])){if($data['para_type']==1){echo "selected";}}?>>租户专用</option>
                    <option value="2" <?php if(isset($data['para_type'])){if($data['para_type']==2){echo "selected";}}?>>账户专用</option>
                </select>
            </div>
        </div> -->
        <input type="hidden" name="para_type" value="0" />
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">数据代码</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="para_code"  id="para_code" placeholder="数据代码" value="<?php if(isset($data['para_code'])){ echo $data['para_code'];}  ?>">
                <?php if(isset($data)){ ?>
                <input type="hidden" class="form-control" name="id"  id="id" value="<?php if(isset($data['id'])){ echo $data['id']; } ?>">
                <?php } ?>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">数据值</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="para_value"  id="para_value" placeholder="数据值" value="<?php if(isset($data['para_value'])){ echo $data['para_value'];}  ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">数据说明</label>
            <div class="col-sm-6">
                <textarea class="form-control" name="para_note" rows="4"><?php if(isset($data['para_note'])){ echo $data['para_note'];}  ?></textarea>
            </div>
        </div>
        <!-- <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">排序</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="sort_order"  id="sort_order" value="<?php if(isset($data['sort_order'])){ echo $data['sort_order'];}  ?>">
            </div>
            <label class="control-label text-danger"id="order"> <i class="icon-asterisk" ></i> </label>
        </div> -->
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">保存</button>
                <!--<a type="submit" href="<?php echo $this->Url->build(array('controller' => 'Systemsetting','action'=>'index')); ?>" class="btn btn-danger">返回</a>-->
                <a type="button" id="back" class="btn btn-danger" onclick="window.history.go(-1)">返回</a>
            </div>
        </div>
    </form>
</div>

<?=$this->Html->script(['adminjs.js','validator.bootstrap.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
    $('form').bootstrapValidator({
        submitButtons: 'button[type="submit"]',
        submitHandler: function(validator, form, submitButton){
            $.post(form.attr('action'), form.serialize(), function(data){
                var data = eval('(' + data + ')');
                if (data.code == 0) {
                    tentionHide(data.msg,0);
                    location.href = '<?php echo $this->Url->build(array('controller'=>'Systemsetting','action'=>'index'));?>';
                } else {
                    tentionHide(data.msg,1);
                }
            });
        },
        fields : {
            para_code : {
                group: '.col-sm-6',
                validators : {
                    notEmpty: {
                        message: '数据代码不能为空'
                    },
                    stringLength: {
                        min: 1,
                        max: 30,
                        message: '请保持在1-30位'
                    },
                    regexp: {
                        regexp: /^[^\u4e00-\u9fa5\s]{0,}$/,
                        message: '数据代码不能有空格和中文'
                    }
                }
            },
            para_value : {
                group: '.col-sm-6',
                validators : {
                    notEmpty: {
                        message: '数据值不能为空'
                    },
                    regexp: {
                        regexp: /^[^\u4e00-\u9fa5\s]{0,}$/,
                        message: '数据值不能有空格和中文'
                    }
                }
            }
        }
    });
</script>
<?= $this->end() ?>

