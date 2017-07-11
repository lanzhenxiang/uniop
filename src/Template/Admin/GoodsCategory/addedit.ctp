<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="goods-category-form" action="<?php echo $this->Url->build(array('controller' => 'GoodsCategory','action'=>'addedit')); ?>" method="post">
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">分类名称（用于展示）</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="name"  id="name" placeholder="分类名称" value="<?php if(isset($department_data['name'])){ echo $department_data['name'];}  ?>">
                <?php if(isset($department_data)){ ?>
                <input type="hidden" class="form-control" name="id"  id="id" value="<?php if(isset($department_data)){ echo $department_data['id']; } ?>">
                <?php } ?>
            </div>
        </div>
        <div class="form-group">
            <label for="parent_id" class="col-sm-2 control-label">上级分类</label>
            <div class="col-sm-6">
                <select class="form-control" name="parent_id">
                    <option value="0">顶级分类</option>
                    <?php foreach ($query as $key => $value) {   ?>
                    <option value="<?php echo $key;?>" <?php if(isset($department_data['parent_id']) && $department_data['parent_id']==$key){ echo 'selected';}  ?>>
                        <span><?php echo $value;?></span>
                    </option>
                    <?php }   ?>
                </select>
            </div>
        </div>
        <!--<div class="form-group">-->
            <!--<label for="is_hot" class="col-sm-2 control-label">热销</label>-->
            <!--<div class="col-sm-6">-->
                <!--<label class="radio-inline">-->
                    <!--<input type="radio" name="is_hot" <?php if(isset($department_data['is_hot'])){if($department_data['is_hot']==1){echo 'checked';} } ?> value="1"> 热销-->
                <!--</label>-->
                <!--<label class="radio-inline">-->
                    <!--<input type="radio" name="is_hot" <?php if(isset($department_data['is_hot'])){if($department_data['is_hot']==0){echo 'checked';}} ?> value="0"> 不热销-->
                <!--</label>-->
            <!--</div>-->
        <!--</div>-->
        <div class="form-group">
            <label for="icon" class="col-sm-2 control-label">分类图标</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="icon"  id="icon" placeholder="分类图标"  value="<?php if(isset($department_data['icon'])){ echo $department_data['icon'];}  ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="description" class="col-sm-2 control-label">分类描述</label>
            <div class="col-sm-6">
                <textarea type="text" class="form-control" name="description"  id="description" placeholder="分类描述"><?php if(isset($department_data['description'])){ echo $department_data['description'];}  ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label for="sort_order" class="col-sm-2 control-label">排序</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="sort_order"  id="sort_order" placeholder="排序"  value="<?php if(isset($department_data['sort_order'])){ echo $department_data['sort_order'];}  ?>">
            </div>
            <!-- <label class="control-label text-danger"><i class="icon-asterisk"></i></label> -->
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" id="ds" class="btn btn-primary">保存</button>
                <a type="button" href="<?php echo $this->Url->build(array('controller' => 'GoodsCategory','action'=>'index')); ?>" class="btn btn-danger">返回</a>
            </div>
        </div>
    </form>
</div>

<?=$this->Html->script(['adminjs.js','validator.bootstrap.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">

    $('#goods-category-form').bootstrapValidator({
        submitButtons: 'button[type="submit"]',
        submitHandler: function(validator, form, submitButton){
            $.post(form.attr('action'), form.serialize(), function(data){
                var data = eval('(' + data + ')');
                if(data.code==0){
                    tentionHide(data.msg,0);
                    location.href='<?php echo $this->Url->build(array('controller'=>'GoodsCategory','action'=>'index'));?>';
                }else{
                    tentionHide(data.msg,1);
                }
            });
        },
        fields : {
            name: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '商品分类名称不能为空'
                    },
                    stringLength: {
                        min: 1,
                        max: 16,
                        message: '请保持在1-16位'
                    },
                    regexp: {
                        regexp: /^\S*$/,
                        message: '商品分类名称不能有空格'
                    }
                }
            },
            label: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '分类标签不能为空'
                    },
                    stringLength: {
                        min: 1,
                        max: 30,
                        message: '请保持在1-30位'
                    },
                    regexp: {
                        regexp: /^\S*$/,
                        message: '分类标签不能有空格'
                    }
                }
            },
            is_hot: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '请选择是否热销'
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
                        regexp: /^[0-9]\d*$/i,
                        message: '请输入正整数'
                    }
                }
            },
        }
    }); 
</script>
<?= $this->end() ?>

