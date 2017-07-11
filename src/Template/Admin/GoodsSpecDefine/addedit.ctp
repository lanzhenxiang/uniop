<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="goods-spec-define-form" action="<?php echo $this->Url->build(array('controller' => 'GoodsSpecDefine','action'=>'addedit')); ?>" method="post">
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">规格分组</label>
            <div class="col-sm-6">
                <select class="form-control" name="group_id" id="group_id">
                    <option value="">请选择</option>
                    <?php foreach ($group as $key => $value) {   ?>
                    <option value="<?php echo $value['group_id'];?>" <?php if(isset($data['group_id']) && $data['group_id']==$value['group_id']){ echo 'selected';}  ?>>
                        <span name="group_name"><?php echo $value['group_name'];?></span>
                    </option>
                    <?php }   ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">规格名称</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="spec_name"  id="spec_name" placeholder="规格名称" value="<?php if(isset($data['spec_name'])){ echo $data['spec_name'];}  ?>">
                <?php if(isset($data)){ ?>
                <input type="hidden" class="form-control" name="id"  id="id" value="<?php if(isset($data)){ echo $data['id']; } ?>">
                <?php } ?>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">规格代码</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="spec_code"  id="spec_code" placeholder="规格代码" value="<?php if(isset($data['spec_code'])){ echo $data['spec_code'];}  ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">规格描述</label>
            <div class="col-sm-6">
                <textarea class="form-control" name="spec_value" id="spec_value" rows="4"><?php if(isset($data['spec_value'])){ echo $data['spec_value'];}  ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">是否展示</label>
            <div class="col-sm-6">
                <label class="radio-inline">
                    <input type="radio" name="is_display" value="1" <?php if(isset($data['is_display'])){ if($data['is_display']==1){echo 'checked';}}?> > 是
                </label>
                <label class="radio-inline">
                    <input type="radio" name="is_display" value="0" <?php if(isset($data['is_display'])){ if($data['is_display']==0){echo 'checked';}}?> > 否
                </label>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">是否是接口参数</label>
            <div class="col-sm-6">
                <label class="radio-inline">
                    <input type="radio" name="is_need" value="1" <?php if(isset($data['is_need'])){ if($data['is_need']==1){echo 'checked';}}?> > 是
                </label>
                <label class="radio-inline">
                    <input type="radio" name="is_need" value="0" <?php if(isset($data['is_need'])){ if($data['is_need']==0){echo 'checked';}}?> > 否
                </label>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">排序</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="sort_order"  id="sort_order" value="<?php if(isset($data['sort_order'])){ echo $data['sort_order'];}  ?>">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" id="ds" class="btn btn-primary">保存</button>
                <a type="button" href="<?php echo $this->Url->build(array('controller' => 'GoodsSpecDefine','action'=>'index')); ?>" class="btn btn-danger">返回</a>
            </div>
        </div>
    </form>
</div>

<?=$this->Html->script(['adminjs.js','validator.bootstrap.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
    $('#goods-spec-define-form').bootstrapValidator({
        submitButtons: 'button[type="submit"]',
        submitHandler: function(validator, form, submitButton){
            $.post(form.attr('action'), form.serialize(), function(data){
                var data = eval('(' + data + ')');
                if (data.code == 0) {
                    tentionHide(data.msg,0);
                    location.href = '<?php echo $this->Url->build(array('controller'=>'GoodsSpecDefine','action'=>'index'));?>';
                } else {
                    tentionHide(data.msg,1);
                }
            });
        },
        fields : {
            group_id: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '请选择规格分组'
                    },
                }
            },
            spec_name: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '规格名称不能为空'
                    },
                    stringLength: {
                        min: 1,
                        max: 16,
                        message: '请保持在1-16位'
                    },
                    regexp: {
                        regexp: /^\S*$/,
                        message: '规格名称不能有空格'
                    }
                }
            },
            spec_code: {
                group: '.col-sm-6',
                validators: {
                    stringLength: {
                        min: 1,
                        max: 16,
                        message: '请保持在1-16位'
                    },
                    regexp: {
                        regexp: /^[^\u4e00-\u9fa5\s]{0,}$/,
                        message: '规格代码不能有空格和中文'
                    }
                }
            },
            is_display: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '请选择是否展示'
                    }
                }
            },
            is_need: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '请选择是否是接口参数'
                    }
                }
            },
            sort_order: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '排序不能为空'
                    },
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

