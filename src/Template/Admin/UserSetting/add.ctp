<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal">
        <div class="form-group">
            <label for="owner_type" class="col-sm-2 control-label">参数类型</label>
            <div class="col-sm-6">
                <select class="form-control" name="owner_type" id="owner_type" onchange="onChangeType(this.value)">
                    <option value="">请选择</option>
                    <option value="1">用户参数</option>
                    <option value="2">租户/部门参数</option>
                    <option value="3">机房/区域参数</option>
                </select>
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="type"></i></label>
        </div>
        <div class="form-group">
            <label for="owner_id" class="col-sm-2 control-label">用户名称</label>
            <div class="col-sm-6">
                <select class="form-control" name="owner_id" id="owner_id">
                    <option value="">请选择</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">参数代码</label>
            <div class="col-sm-6" id="select-para">
                <input type="text" class="form-control" name="para_code"  id="para_code" placeholder="参数代码" value="">

            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="code"></i></label>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">参数值</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="para_value"  id="para_value" placeholder="参数值" value="">
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="value"></i></label>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">参数说明</label>
            <div class="col-sm-6">
                <textarea class="form-control" name="para_note" rows="4"></textarea>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <a type="submit" id="ds" class="btn btn-primary">保存</a>
                <a type="submit" href="<?php echo $this->Url->build(array('controller' => 'UserSetting','action'=>'index')); ?>" class="btn btn-danger">返回</a>
            </div>
        </div>
    </form>
</div>
<?=$this->Html->script(['adminjs.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">

    $("#ds").click(function() {
        var validate = true;
        var type_id = $('#owner_id').val();
        var code = $('#para_code').val();
        var value = $('#para_value').val();
        if(type_id==''){
            $('#type').html('请选择参数类型');
        }else{
            $('#type').html('');
        }
        if(code) {
            // if (code.length < 1 || code.length > 16) {
            //     $('#code').html('数据代码应在1到16位之间');
            //     return false;
            // } else {
                $('#code').html('');
            // }
        } else {
            $('#code').html('请输入参数代码');
            return false;
        }
        if(value) {
            // if (value.length < 1 || value.length > 16) {
            //     $('#value').html('参数值应在1到16位之间');
            //     return false;
            // } else {
                $('#value').html('');
            // }
        } else {
            $('#value').html('请输入参数值');
            return false;
        }
        $.ajax({
            type: 'post',
            url: '<?php echo $this->Url->build(array('controller' => 'UserSetting','action'=>'addedit')); ?>',
            data: $("form").serialize(),
            success: function (data) {
                var data = eval('(' + data + ')');
                if (data.code == 0) {
                    tentionHide(data.msg, 0);
                    location.href = '<?php echo $this->Url->build(array('controller'=>'UserSetting','action'=>'index'));?>';
                } else {
                    tentionHide(data.msg, 1);
                }
            }
        });
    });

function onChangeType(id){
    $.ajax({
        url: '<?php echo $this->Url->build(array('controller' => 'UserSetting','action'=>'check')); ?>/'+id,
        success: function (data) {
            datas = $.parseJSON(data);
            if(datas){
                var type = '';
                var i = '';
                if(datas.type==1){
                    $.each( datas.row, function(i, n){
                        type +='<option value="'+n.id+'">'+n.username+'</option>';
                    });
                    i += '<input type="text" class="form-control" name="para_code"  id="para_code" placeholder="参数代码" value="">';
                    $("#select-para").html(i);
                }else if(datas.type==2){
                    $.each( datas.row, function(i, n){
                        type +='<option value="'+n.id+'">'+n.name+'</option>';
                    });
                    i += '<select class="form-control" name="para_code" id="para_code"><option value="cpu_bugedt">cpu_bugedt</option><option value="memory_buget">memory_buget</option><option value="gpu_bugedt">gpu_bugedt</option><option value="subnet_bugedt">subnet_bugedt</option><option value="router_bugedt">router_bugedt</option><option value="disks_bugedt">disks_bugedt</option><option value="host_max_disk">host_max_disk</option></select>';
                    $("#select-para").html(i);
                }else if(datas.type==3){
                    $.each( datas.row, function(i, n){
                        type +='<option value="'+n.id+'">'+n.display_name+'</option>';
                    });
                    i += '<input type="text" class="form-control" name="para_code"  id="para_code" placeholder="参数代码" value="">';
                    $("#select-para").html(i);
                }else{
                    type +='<option value="">请选择</option>';
                    i += '<input type="text" class="form-control" name="para_code"  id="para_code" placeholder="参数代码" value="">';
                    $("#select-para").html(i);
                }
                $("#owner_id").html(type);
            }
        }
    });
}

</script>
<?= $this->end() ?>

