<?= $this->Html->css('bootstrap-datetimepicker.min.css'); ?>
<div class="wrap-nav-right wrap-index-page page-wrapper">
    <div class="row section-body">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h3>编辑硬件维修记录
                    </h3>
                    <div class="panel-body">
                        <form id="hardware-repair-form" action="<?php echo $this->Url->build(array('controller' => 'HardwareRepair','action'=>'edit')); ?>" method="post">
                            <div class="">
                                <div class="modal-body">

                                    <div class="modal-disk-contentsys" style="display:block;">

                                        <div class="modal-form-group form-group">
                                            <label for="repair_date">日期:</label>
                                            <div class="amount pull-left">
                                                <div class="order-number input-append date" id="repair_date_datetimepicker" data-date-format="yyyy-mm-dd" style="height:28px;margin:0;line-height:28px;">
                                                    <input  type="text" name="repair_date" id="repair_date" value="<?=$repair['repair_date']?>" readonly style="height:28px;margin:0;line-height:28px;">
                                                    <span class="add-on"><i class="icon-th"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-form-group form-group">
                                            <label for="repair_by">服务人员:</label>
                                            <div class="amount pull-left">
                                                <input type="text" id="repair_by" name="repair_by" value="<?=$repair['repair_by']?>" rows="5" placeholder="更换内容">
                                            </div>
                                        </div>
                                        <div class="modal-form-group form-group">
                                            <label for="repair_reason">故障现象:</label>
                                            <div class="amount pull-left">
                                                <textarea id="repair_reason" name="repair_reason" rows="5" placeholder="故障现象"><?=$repair['repair_reason']?></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-form-group form-group">
                                            <label for="repair_way">处理方法:</label>
                                            <div class="amount pull-left">
                                                <textarea id="repair_way" name="repair_way" rows="5" placeholder="处理方法"><?=$repair['repair_way']?></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-form-group form-group">
                                            <label for="repair_note">备注:</label>
                                            <div class="amount pull-left">
                                                <textarea id="repair_note" name="repair_note" rows="5" placeholder="备注"><?=$repair['repair_way']?></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <input type="hidden" name="id" id="id" value="<?=$repair['id']?>">
                                            <button type="submit" id="addHardwareRepair" class="btn btn-primary">确认</button>
                                            <a href="<?php echo $this->Url->build(array('controller' => 'ecs','action'=>'detail')); ?>/<?=$repair['hardware_asset']['assets_no']?>" class="btn btn-danger" data-dismiss="modal">取消</a>
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
            $('#repair_date_datetimepicker').datetimepicker({
                    autoclose:true,
                    minView:2,
                    startDate:'',
                    endDate:time
                }
            );
        });
    $('#hardware-repair-form').bootstrapValidator({
        submitButtons: 'button[type="submit"]',
        submitHandler: function(validator, form, submitButton){
            $.post(form.attr('action'), form.serialize(), function(data){
                if(data.code== 0){
                    layer.alert(data.msg);
                    window.location.href ="<?php echo $this->Url->build(array('controller' => 'ecs','action'=>'detail')); ?>/<?=$repair['hardware_asset']['assets_no']?>";
                }else{
                    layer.alert(data.msg);
                }
            });
        },
        fields : {
            repair_date: {
                // group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '维修日期不能为空'
                    }
                }
            },
            repair_by: {
                // group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '服务人员不能为空'
                    }
                }
            },
            repair_reason: {
                // group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '故障现象不能为空'
                    }
                }
            },
            repair_way: {
                // group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '处理方法不能为空'
                    }
                }
            }
        }
    });
</script>