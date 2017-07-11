<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" action="<?php echo $this->Url->build(array('controller' => 'SetSoftware','action'=>'addedit')); ?>" method="post">
        <div class="form-group">
            <label for="set_name" class="col-sm-2 control-label">非编规格</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="set_name"  id="set_name" placeholder="非编规格" value="<?php if(isset($department_data['set_name'])){ echo $department_data['set_name'];}  ?>">
                <?php if(isset($department_data)){ ?>
                <input type="hidden" class="form-control" name="set_id"  id="set_id" value="<?php if(isset($department_data)){ echo $department_data['set_id']; } ?>">
                <?php } ?>
            </div>
        </div>
        <div class="form-group">
            <label for="set_type_code" class="col-sm-2 control-label">非编代码</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="set_code"  id="set_code" placeholder="非编代码"  value="<?php if(isset($department_data['set_code'])){ echo $department_data['set_code'];}  ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label for="cpu_number" class="col-sm-2 control-label">硬件套餐名称</label>
            <div class="col-sm-6">
                <select class="form-control" id="hardware_set" name="hardware_set">
                    <?php foreach ($query['hardware'] as $key => $value) {   ?>
                    <option value="<?php echo $value->set_code;?>" <?php if(isset($department_data) && $department_data['hardware_set']==$value->set_code){ echo 'selected';}  ?>>
                        <span><?php echo $value->set_name;?></span>
                    </option>
                    <?php }   ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="memory_gb" class="col-sm-2 control-label">镜像名称</label>
            <div class="col-sm-6">
                <select class="form-control" id="image_code" name="image_code">
                    <?php foreach ($query['image'] as $key => $value) {   ?>
                    <option value="<?php echo $value->image_code;?>" <?php if(isset($department_data) && $department_data['image_code']==$value->image_code){ echo 'selected';}  ?>>
                        <span><?php echo $value->image_name;?></span>
                    </option>
                    <?php }   ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="provider" class="col-sm-2 control-label">非编品牌</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="provider"  id="provider" placeholder="非编品牌"  value="<?php if(isset($department_data['provider'])){ echo $department_data['provider'];}  ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="set_note" class="col-sm-2 control-label">非编说明</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="set_note"  id="set_note" placeholder="非编说明"  value="<?php if(isset($department_data['set_note'])){ echo $department_data['set_note'];}  ?>">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" id="ds" class="btn btn-primary">保存</button>
                <a type="submit" href="<?php echo $this->Url->build(array('controller' => 'SetSoftware','action'=>'index')); ?>" class="btn btn-danger">返回</a>
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
                if(data.code==0){
                    tentionHide(data.msg,0);
                    location.href='<?php echo $this->Url->build(array('controller'=>'SetSoftware','action'=>'index'));?>';
                }else{
                    tentionHide(data.msg,1);
                }
            });
        },
        fields : {
            set_name : {
                group: '.col-sm-6',
                validators : {
                    notEmpty: {
                        message: '非编规格不能为空'
                    },
                    stringLength: {
                        min: 2,
                        max: 16,
                        message: '请保持在2-16位'
                    },
                    regexp: {
                        regexp: /^\S*$/,
                        message: '非编规格中不能有空格'
                    }
                }
            },
            provider : {
                group: '.col-sm-6',
                validators : {
                    notEmpty: {
                        message: '非编品牌不能为空'
                    },
                    stringLength: {
                        min: 2,
                        max: 16,
                        message: '请保持在2-16位'
                    },
                    regexp: {
                        regexp: /^\S*$/,
                        message: '非编品牌中不能有空格'
                    }
                }
            }
        }
    });
</script>
<?= $this->end() ?>