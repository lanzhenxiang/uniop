<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="goods-spec-define-form" action="<?php echo $this->Url->build(array('controller' => 'GoodsSpecDefine','action'=>'addspec')); ?>" method="post">
        <?php if(isset($data)){ ?>
        <input type="hidden" value="<?php echo $data['group_id']; ?>"  name="group_id"  id="group_id" >
        <?php } ?>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">规格分组</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="group_name" value="<?php if(isset($data)){ echo $data['group_name'];} ?>"  id="group_name" placeholder="规格分组" >
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" id="ds" class="btn btn-primary">保存</button>
                <a type="button" href="<?php echo $this->Url->build(array('controller' => 'GoodsSpecDefine','action'=>'index')); echo '/index/-1';?>" class="btn btn-danger">返回</a>
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
                        location.href = '<?php echo $this->Url->build(array('controller'=>'GoodsSpecDefine','action'=>'index'));?>'+'/index/-1';
                    } else {
                        tentionHide(data.msg,1);
                    }
            });
        },
        fields : {
            group_name: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '规格分组名称不能为空'
                    },
                    stringLength: {
                        min: 1,
                        max: 16,
                        message: '请保持在1-16位'
                    },
                    regexp: {
                        regexp: /^\S*$/,
                        message: '规格分组不能有空格'
                    }
                }
            },
        }
    }); 
</script>
<?= $this->end() ?>

