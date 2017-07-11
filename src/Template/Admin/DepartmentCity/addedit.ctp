<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" enctype="multipart/form-data" id="console-categroy-form" action="<?php echo $this->Url->build(array('controller' => 'DepartmentCity','action'=>'addedit')); ?>" method="post">
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">租户</label>
            <div class="col-sm-6">
                <?php if(isset($department_data)){ ?>
                <input type="hidden" class="form-control" name="id"  id="id" value="<?php if(isset($department_data)){ echo $department_data['id']; } ?>">
                <?php } ?>
                <select class="form-control" id="department_id" name="department_id">
                    <?php foreach ($query['departments'] as $key => $value) {   ?>
                    <option value="<?php echo $value->id;?>" <?php if(isset($department_data) && $department_data['department_id']==$value->id){ echo 'selected';}  ?>>
                        <span><?php echo $value->name;?></span>
                    </option>
                    <?php }   ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="city_name" class="col-sm-2 control-label">地点</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="city_name"  id="city_name" placeholder="地点"  value="<?php if(isset($department_data['city_name'])){ echo $department_data['city_name'];}  ?>">
            </div>
            
        </div>
        <div class="form-group">
            <label for="is_center" class="col-sm-2 control-label">是否中心点</label>
            <div class="col-sm-6">
                <label class="radio-inline">
                    <input type="radio" name="is_center" value="1" <?php if(isset($department_data['is_center']) && $department_data['is_center'] == 1){ echo "checked";}  ?>>
                    是
                </label>
                <label class="radio-inline">
                    <input type="radio" name="is_center" value="0" <?php if(isset($department_data['is_center']) && $department_data['is_center'] == 0){ echo "checked";}  ?>>
                    否
                </label>
            </div>
            
        </div>
        <div class="form-group">
            <label for="icon" class="col-sm-2 control-label">描述</label>
            <div class="col-sm-6">
                <textarea type="text" class="form-control" rows="4" name="info" id="info" placeholder="描述 "><?php if(isset($department_data['info'])){ echo $department_data['info'];}  ?></textarea>
            </div>
        </div>
       
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" id="ds" class="btn btn-primary">保存</button>
                <a type="button" href="<?php echo $this->Url->build(array('controller' => 'DepartmentCity','action'=>'index')); ?>" class="btn btn-danger">返回</a>
            </div>
        </div>
    </form>
</div>
<?=$this->Html->script(['adminjs.js','validator.bootstrap.js']); ?>
<?= $this->start('script_last'); ?>
<?php echo $this->Html->script('jquery.uploadify.min.js'); ?>
<script type="text/javascript">
    $('#console-categroy-form').bootstrapValidator({
        submitButtons: 'button[type="submit"]',
        submitHandler: function(validator, form, submitButton){
            $.post(form.attr('action'), form.serialize(), function(data){
                var data = eval('(' + data + ')');
                if(data.code==0){
                    tentionHide(data.msg,0);
                    location.href="<?php echo $this->Url->build(array('controller'=>'DepartmentCity','action'=>'index'));?>";
                }else{
                    tentionHide(data.msg,1);
                }
            });
        },
        fields : {
            department_id: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '请选择租户'
                    }
                }
            },
            city_name: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '请选择城市'
                    }
                }
            },
            is_center: {
                group: '.col-sm-6',
                validators: {
                     notEmpty: {
                        message: '请选择是否是中心点'
                    }
                }
            }
        }
    }); 
</script>
<?= $this->end() ?>