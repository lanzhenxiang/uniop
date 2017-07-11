<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="orders-form" action="<?php echo $this->Url->build(array('controller' => 'Orders','action'=>'addedit')); ?>" method="post">
        <div class="form-group">
            <label for="number" class="col-sm-2 control-label">订单号</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="number"  id="number" placeholder="分类名称" value="<?php if(isset($department_data['number'])){ echo $department_data['number'];}  ?>" disabled>
                <?php if(isset($department_data)){ ?>
                <input type="hidden" class="form-control" name="id"  id="id" value="<?php if(isset($department_data)){ echo $department_data['id']; } ?>">
                <?php } ?>
            </div>
        </div>
        <div class="form-group">
            <label for="department_id" class="col-sm-2 control-label">购买租户</label>
            <div class="col-sm-6">
                <select class="form-control" name="department_id" disabled>
                    <?php foreach ($query as $key => $value) {   ?>
                    <option value="" ></option>
                    <option value="<?php echo $key;?>" <?php if(isset($department_data['department_id']) && $department_data['department_id']==$key){ echo 'selected';}?> >
                        <span><?php echo $value;?></span>
                    </option>
                    <?php }   ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="status" class="col-sm-2 control-label">订单状态</label>
            <div class="col-sm-6">
            <input type="text" class="form-control" name="status"  id="status" placeholder="订单状态" value="<?php if(isset($department_data['workflow_detail']['step_name'])){ echo $department_data['workflow_detail']['step_name'];}else{echo '未知';}  ?>" disabled>
            </div>
        </div>
        <div class="form-group">
            <label for="price_total" class="col-sm-2 control-label">总价</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="price_total"  id="price_total" placeholder="总价"  value="<?php if(isset($department_data['price_total'])){ echo $department_data['price_total'];}  ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="create_by" class="col-sm-2 control-label">下单人</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="create_by"  id="create_by" placeholder="下单人"  value="<?php if(isset($department_data['account'])){ echo $department_data['account']['username'];}  ?>" disabled>
            </div>
        </div>
        <div class="form-group">
            <label for="create_time" class="col-sm-2 control-label">下单时间</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="create_time"  id="create_time" placeholder="下单时间"  value="<?php if(isset($department_data['create_time'])){ echo date('Y-m-d H:i:s',$department_data['create_time']);}  ?>" disabled>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" id="ds" class="btn btn-primary">保存</button>
                <a type="button" href="<?php echo $this->Url->build(array('controller' => 'Orders','action'=>'index')); ?>" class="btn btn-danger">返回</a>

            </div>
        </div>
    </form>
</div>
<?=$this->Html->script(['adminjs.js','validator.bootstrap.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">

    $('#orders-form').bootstrapValidator({
        submitButtons: 'button[type="submit"]',
        submitHandler: function(validator, form, submitButton){
            $.post(form.attr('action'), form.serialize(), function(data){
                var data = eval('(' + data + ')');
                if(data.code==0){
                    tentionHide(data.msg,0);
                    location.href='<?php echo $this->Url->build(array('controller'=>'Orders','action'=>'index'));?>';
                }else{
                    tentionHide(data.msg,1);
                }
            });
        },
        fields : {
            price_total: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '总价不能为空'
                    },
                    regexp: {
                        regexp: /^\d+(\.\d*[1-9]{1})?$/,
                        message: '请填写正确的总价'
                    }
                }
            },
        }
    }); 
</script>
<?= $this->end() ?>

