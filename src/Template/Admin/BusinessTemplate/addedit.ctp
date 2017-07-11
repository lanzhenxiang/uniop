<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="aduser-form" action="<?php echo $this->Url->build(array('controller' => 'BusinessTemplate','action'=>'addedit')); ?>" method="post">
 
        <div class="form-group">
            <label for="region_code" class="col-sm-2 control-label">部署区域</label>
            <div class="col-sm-6">
                <select class="form-control" id="region_code"  name="region_code">
                    <option value="">请选择</option>
                    <?php foreach ($agent as $key => $value) {   ?>
                        <option <?php if(isset($data)){ if($value->region_code == $data['region_code']){echo 'selected';}} ?> value ="<?php echo $value->region_code?>" >
                            <?php echo $value->display_name;?>
                        </option>
                    <?php }   ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="biz_temp_name" class="col-sm-2 control-label">业务模板名称</label>
            <div class="col-sm-6">
                <?php if(isset($data)){ ?>
                    <input type="hidden" value="<?php echo  $data['biz_tid']; ?>" name="biz_tid" id="biz_tid" >
                <?php } ?>
                <input type="text" class="form-control" value="<?php if(isset($data)){ echo $data['biz_temp_name'];} ?>" name="biz_temp_name" id="biz_temp_name" >
            </div>
        </div>
        <div class="form-group">
            <label for="version" class="col-sm-2 control-label">版本</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" value="<?php if(isset($data)){ echo $data['version'];} ?>" name="version" id="version" >
            </div>
        </div>
        <div class="form-group">
            <label for="system_level" class="col-sm-2 control-label">系统规模</label>
            <div class="col-sm-6">
                <label class="radio-inline">
                    <input type="radio" name="system_level" value="高配" data-bv-field="system_level" 
                    <?php if(isset($data) && $data['system_level'] == '高配'): ?>checked="true" <?php endif;?>
                    >高配
                </label>
                <label class="radio-inline">
                    <input type="radio" name="system_level" value="标配" data-bv-field="system_level" 
                    <?php if(isset($data) && $data['system_level'] == '标配'): ?>checked="true" <?php endif;?>
                    >标配
                </label>
                <label class="radio-inline">
                    <input type="radio" name="system_level" value="低配" data-bv-field="system_level" 
                    <?php if(isset($data) && $data['system_level'] == '低配'): ?>checked="true" <?php endif;?>
                    >低配
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" id="ds" class="btn btn-primary">保存</button>
                <a type="button" href="<?php echo $this->Url->build(array('controller' => 'BusinessTemplate','action'=>'index')); ?>" class="btn btn-danger">返回</a>
            </div>
        </div>
    </form>
</div>

<?=$this->Html->script(['adminjs.js','validator.bootstrap.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">

    $('#aduser-form').bootstrapValidator({
        submitButtons: 'button[type="submit"]',
        submitHandler: function(validator, form, submitButton){
            $.post(form.attr('action'), form.serialize(), function(data){
                var data = eval('(' + data + ')');
                if (data.code == 0) {
                    tentionHide(data.msg, 0);
                    location.href = '<?php echo $this->Url->build(array('controller'=>'BusinessTemplate','action'=>'index'));?>';
                } else {
                    tentionHide(data.msg, 1);
                }
            });
        },
        fields : {
            biz_temp_name: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '业务模板名称不能为空'
                    },
                    regexp: {
                        regexp: /^\S*$/,
                        message: '业务模板名称不能有空格'
                    }
                }
            },
            region_code: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '请选择一个区域'
                    }
                }
            },
            version: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '业务模板版本不能为空'
                    }
                }
            },
            system_level: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '请选择一个系统规模'
                    }
                }
            }

        }
    });
</script>
<?= $this->end() ?>

