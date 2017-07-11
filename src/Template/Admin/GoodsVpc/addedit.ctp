<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="aduser-form" action="<?php echo $this->Url->build(array('controller' => 'GoodsVpc','action'=>'addedit')); ?>" method="post">

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">vpc配置单名</label>
            <div class="col-sm-6">
                <?php if(isset($data)){ ?>
                    <input type="hidden" value="<?php echo  $data['vpc_id']; ?>" name="vpc_id" id="vpc_id" >
                <?php } ?>
                <input type="text" class="form-control" value="<?php if(isset($data)){ echo $data['vpc_name'];} ?>" name="vpc_name" id="vpc_name" >
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">部署区域</label>
            <div class="col-sm-6">
                <select class="form-control" id="region_code"  name="region_code">
                    <option value="">请选择</option>
                    <?php foreach ($agent as $key => $value) {   ?>
                        <option <?php if(isset($data)){ if($value->region_code == $data['region_code']){echo 'selected';}} ?> value ="<?php echo $value->region_code?>" >
                            <?php echo $value->display_name ;?>
                        </option>
                    <?php }   ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">VPC CIDR</label>
            <div class="col-sm-6">
                <input type="text" class="form-control"  disabled="disabled" value="172.16.0.0/20">
                <input type="hidden" name="vpc_cidr" value="172.16.0.0/20">
                <input type="hidden"   name="regin_name" value="">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" id="ds" class="btn btn-primary">保存</button>
                <a type="button" href="<?php echo $this->Url->build(array('controller' => 'GoodsVpc','action'=>'index')); ?>" class="btn btn-danger">返回</a>
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
                    location.href = '<?php echo $this->Url->build(array('controller'=>'GoodsVpc','action'=>'index'));?>';
                } else {
                    tentionHide(data.msg, 1);
                }
            });
        },
        fields : {
            vpc_name: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '配置单名称不能为空'
                    },
                    regexp: {
                        regexp: /^\S*$/,
                        message: '配置单名称不能有空格'
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
            }
        }
    });
</script>
<?= $this->end() ?>

