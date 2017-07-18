<?= $this->Html->css('bootstrap-datetimepicker.min.css'); ?>
<div class="wrap-nav-right wrap-index-page page-wrapper">
    <div class="row section-body">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h3>编辑硬件更换记录
                    </h3>
                    <div class="panel-body">
                        <form id="hardware-instead-form" action="<?php echo $this->Url->build(array('controller' => 'HardwareInstead','action'=>'edit')); ?>" method="post">
                            <div class="">
                                <div class="modal-body">

                                    <div class="modal-disk-contentsys" style="display:block;">

                                        <div class="modal-form-group form-group">
                                            <label for="instead_date">更换日期:</label>
                                            <div class="amount pull-left">
                                                <div class="order-number input-append date" id="instead_date_datetimepicker" data-date-format="yyyy-mm-dd" style="height:28px;margin:0;line-height:28px;">
                                                    <input  type="text" name="instead_date" id="instead_date" value="<?=$instead['instead_date']?>" readonly style="height:28px;margin:0;line-height:28px;">
                                                    <span class="add-on"><i class="icon-th"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-form-group form-group">
                                            <label for="instead_content">更换内容:</label>
                                            <div class="amount pull-left">
                                                <textarea id="instead_content" name="instead_content" rows="5" placeholder="更换内容"><?=$instead['instead_content']?></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-form-group form-group">
                                            <label for="instead_reason">更换原因:</label>
                                            <div class="amount pull-left">
                                                <textarea id="instead_reason" name="instead_reason" rows="5" placeholder="更换原因"><?=$instead['instead_reason']?></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <input type="hidden" name="id" id="id" value="<?=$instead['id']?>">
                                            <button type="submit" id="addHardwareInstead" class="btn btn-primary">确认</button>
                                            <a href="<?php echo $this->Url->build(array('controller' => 'ecs','action'=>'detail')); ?>/<?=$instead['hardware_asset']['assets_no']?>" class="btn btn-danger" data-dismiss="modal">取消</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <!-- /.table-responsive -->
                    </div>
                </div>
            </div>
        </div>
        <!-- /.col-lg-12 -->
    </div>

</div>
<?= $this->Html->script(['bootstrap-datetimepicker.js','validator.bootstrap.js']); ?>
<script type="text/javascript">

    $(document).ready(
        function(){

            var myDate = new Date();
            var year = myDate.getFullYear();
            var month =myDate.getMonth()+1;
            var day =  myDate.getDate();
            var time =year+'-'+month+'-'+day;
            $('#instead_date_datetimepicker').datetimepicker({
                    autoclose:true,
                    minView:2,
                    startDate:'',
                    endDate:time
                }
            );
        });
    $('#hardware-instead-form').bootstrapValidator({
        submitButtons: 'button[type="submit"]',
        submitHandler: function(validator, form, submitButton){
            $.post(form.attr('action'), form.serialize(), function(data){
                if(data.code== 0){
                    layer.alert(data.msg);
                    window.location.href ="<?php echo $this->Url->build(array('controller' => 'ecs','action'=>'detail')); ?>/<?=$instead['hardware_asset']['assets_no']?>";
                }else{
                    layer.alert(data.msg);
                }
            });
        },
        fields : {
            instead_date: {
                // group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '更换日期不能为空'
                    }
                }
            },
            instead_content: {
                // group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '更换内容不能为空'
                    }
                }
            },
            instead_reason: {
                // group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '更换原因不能为空'
                    }
                }
            },
        }
    });
</script>